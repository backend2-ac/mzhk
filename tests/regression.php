<?php
/** Isolated regression checks: php tests/regression.php (GD, intl, SQLite required).
 * Uses only an in-memory database and a newly created temporary image directory.
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
$vendor = getenv('MZHK_QA_VENDOR') ?: dirname(__DIR__) . '/vendor';
$loader = require $vendor . '/autoload.php';
$loader->addPsr4('App\\', dirname(__DIR__) . '/src/', true);
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('APP', ROOT . '/src/');
define('CONFIG', ROOT . '/config/');
define('TMP', sys_get_temp_dir() . '/mzhk-regression-' . bin2hex(random_bytes(8)) . '/');
define('WWW_ROOT', TMP . 'webroot/');
define('CORE_PATH', $vendor . '/cakephp/cakephp/');
define('CAKE', CORE_PATH . 'src/');
define('CACHE', TMP . 'cache/');
define('LOGS', TMP . 'logs/');
define('RESOURCES', ROOT . '/resources/');
mkdir(WWW_ROOT, 0755, true);
require CORE_PATH . 'config/bootstrap.php';

use App\Service\ImageStorage;
use App\Service\InstagramPost;
use App\Service\PaymentPlans;
use Cake\Core\Configure;
use Cake\Cache\Cache;
use Cake\Datasource\ConnectionManager;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Laminas\Diactoros\UploadedFile;

Configure::write('App.namespace', 'App');
Configure::write('App.encoding', 'UTF-8');
Configure::write('App.paths.templates', [ROOT . '/templates/']);
foreach (['long', '_cake_core_', '_cake_model_'] as $cache) {
    Cache::setConfig($cache, ['className' => 'Array']);
}
ConnectionManager::setConfig('default', ['className' => 'Cake\\Database\\Connection', 'driver' => 'Cake\\Database\\Driver\\Sqlite', 'database' => ':memory:']);
$db = ConnectionManager::get('default');
$sections = ['Advans' => 'advans', 'Characteristics' => 'characteristics', 'Comps' => 'comps', 'Galleries' => 'galleries', 'Houses' => 'houses', 'Infrastructures' => 'infrastructures', 'Preferences' => 'preferences', 'Services' => 'services', 'Slides' => 'slides'];
foreach ($sections as $tableName) {
    $db->execute("CREATE TABLE $tableName (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, name TEXT, body TEXT, img TEXT, images TEXT, alias TEXT, item_order INTEGER, room INTEGER, icon TEXT)");
}
$db->execute('CREATE TABLE i18n (id INTEGER PRIMARY KEY AUTOINCREMENT, locale TEXT, model TEXT, foreign_key INTEGER, field TEXT, content TEXT)');
$checks = 0;
$uploadStreams = [];
function check($condition, string $message): void {
    global $checks;
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    $checks++;
}
function upload(string $format = 'png', int $size = 400): UploadedFile {
    global $uploadStreams;
    $path = tempnam(TMP, 'input-');
    $image = imagecreatetruecolor($size, $size);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
    imagefilledrectangle($image, 20, 20, 60, 60, imagecolorallocate($image, 245, 128, 32));
    $writer = ['jpg' => 'imagejpeg', 'png' => 'imagepng', 'gif' => 'imagegif'][$format];
    $writer($image, $path);
    imagedestroy($image);
    $file = new UploadedFile($path, filesize($path), UPLOAD_ERR_OK, 'PLAN.' . strtoupper($format), 'application/octet-stream');
    $uploadStreams[] = $file->getStream();
    return $file;
}
function controller(string $section, array $data, string $method = 'POST') {
    $class = 'App\\Controller\\Admin\\' . $section . 'Controller';
    $request = new ServerRequest(['url' => '/admin/' . strtolower($section), 'environment' => ['REQUEST_METHOD' => $method, 'HTTP_REFERER' => '/admin/' . strtolower($section)]]);
    return new $class($request->withParsedBody($data));
}
try {
    foreach (['jpg', 'png', 'gif'] as $type) {
        $file = upload($type);
        $hash = hash('sha256', (string)$file->getStream());
        $name = ImageStorage::upload($file, 'formats');
        check(substr($name, -strlen($type)) === $type, 'uppercase filename / actual MIME ' . $type);
        check(hash_file('sha256', WWW_ROOT . 'img/formats/' . $name) === $hash, '400px original preserved ' . $type);
        check(getimagesize(WWW_ROOT . 'img/formats/thumbs/' . $name)[0] === 300, 'thumbnail resized ' . $type);
        check(count(glob(WWW_ROOT . 'img/formats/original/*')) === 0, 'staging cleaned ' . $type);
    }
    $png = ImageStorage::upload(upload('png', 1600), 'formats');
    $decoded = imagecreatefrompng(WWW_ROOT . 'img/formats/' . $png);
    check(getimagesize(WWW_ROOT . 'img/formats/' . $png)[0] === 1500, 'large image resized');
    check((imagecolorat($decoded, 0, 0) >> 24) === 127, 'PNG transparency preserved');
    imagedestroy($decoded);

    // Exercise the real admin add, edit and delete actions for every image section.
    foreach ($sections as $section => $directory) {
        $model = $section === 'Characteristics' ? 'Сharacteristics' : $section;
        $table = TableRegistry::getTableLocator()->get($model);
        $data = ['title' => 'QA', 'name' => 'QA', 'alias' => 'qa_fixture', 'body' => 'Original', 'img' => upload()];
        if (in_array($section, ['Slides', 'Services'])) $data['images'] = upload('gif');
        controller($section, $data)->add();
        $record = $table->find()->firstOrFail();
        $id = $record->id;
        $old = $record->img;
        $secondary = $record->get('images');
        check(is_file(WWW_ROOT . "img/$directory/$old"), "$section add file + DB record");
        controller($section, ['title' => 'Edited', 'body' => 'Changed'])->edit($id);
        check($table->get($id)->img === $old, "$section edit without upload retains image");
        controller($section, ['title' => 'Edited', 'img' => upload('jpg')])->edit($id);
        $new = $table->get($id)->img;
        check($new !== $old && is_file(WWW_ROOT . "img/$directory/$new"), "$section replace image");
        check(!file_exists(WWW_ROOT . "img/$directory/$old") && !file_exists(WWW_ROOT . "img/$directory/thumbs/$old"), "$section old image and thumb removed after save");
        controller($section, [])->delete($id);
        check($table->find()->count() === 0 && !file_exists(WWW_ROOT . "img/$directory/$new"), "$section delete record and image");
        if ($secondary) check(!file_exists(WWW_ROOT . "img/$directory/$secondary") && !file_exists(WWW_ROOT . "img/$directory/thumbs/$secondary"), "$section secondary image cleaned");
    }

    $houses = TableRegistry::getTableLocator()->get('Houses');
    $shared = ImageStorage::upload(upload(), 'houses');
    $first = $houses->saveOrFail($houses->newEntity(['title' => 'Keep', 'img' => $shared]));
    ImageStorage::deleteUnused($houses, 'houses', [$shared]);
    check(is_file(WWW_ROOT . 'img/houses/' . $shared), 'shared image is retained');
    $houses->deleteOrFail($first);
    ImageStorage::deleteUnused($houses, 'houses', [$shared]);
    check(!is_file(WWW_ROOT . 'img/houses/thumbs/' . $shared), 'unreferenced image is removed');

    $invalid = tempnam(TMP, 'invalid-'); file_put_contents($invalid, '<script>alert(1)</script>');
    $bad = new UploadedFile($invalid, filesize($invalid), 0, 'fake.JPG', 'image/jpeg');
    $uploadStreams[] = $bad->getStream();
    controller('Slides', ['title' => 'Bad', 'img' => upload(), 'images' => $bad])->add();
    check(TableRegistry::getTableLocator()->get('Slides')->find()->count() === 0, 'invalid image prevents save');
    check(count(glob(WWW_ROOT . 'img/slides/*.*')) === 0 && count(glob(WWW_ROOT . 'img/slides/thumbs/*')) === 0, 'failed second upload cleans first upload');

    $url = 'https://www.instagram.com/reel/Dewohcrq2qE/';
    check(InstagramPost::url('https://www.instagram.com/mzhk.astana/reel/Dewohcrq2qE/?igsh=abc') === $url, 'profile-qualified Instagram link');
    check(InstagramPost::url('<blockquote data-instgrm-permalink="' . $url . '?utm_source=ig_web_copy_link"></blockquote><script src="evil"></script>') === $url, 'embed code extracts only trusted URL');
    foreach (['javascript:alert(1)', 'https://instagram.com.evil/reel/abc/', 'https://evil@instagram.com/reel/abc/', 'https://www.instagram.com/mzhk.astana/', '<iframe src="https://evil"></iframe>'] as $badUrl) {
        try { InstagramPost::url($badUrl); check(false, 'unsafe URL accepted'); } catch (InvalidArgumentException $expected) { $checks++; }
    }
    check(InstagramPost::forSlot('social_post_1', 'Old caption')['url'] === $url, 'initial company reel selection');
    check(InstagramPost::forSlot('social_post_1', InstagramPost::write('', 'Photo'))['url'] === '', 'admin can clear reel and restore photo');
    check(InstagramPost::read('{"_type":"instagram-post","url":"invalid","caption":"Text"}')['url'] === '', 'malformed stored link cannot crash page');
    $comps = TableRegistry::getTableLocator()->get('Comps');
    $record = $comps->saveOrFail($comps->newEntity(['alias' => 'social_post_1', 'title' => 'Instagram', 'body' => 'Old caption', 'img' => 'keep.jpg']));
    controller('Comps', ['instagram_embed' => $url, 'social_caption' => 'New caption'])->edit($record->id);
    check(InstagramPost::read($comps->get($record->id)->body) === ['url' => $url, 'caption' => 'New caption'], 'admin saves reel and caption');
    check($comps->get($record->id)->img === 'keep.jpg', 'Instagram save preserves previous photo');
    controller('Comps', ['instagram_embed' => 'https://evil.example/', 'social_caption' => 'Invalid'])->edit($record->id);
    check(InstagramPost::read($comps->get($record->id)->body)['url'] === $url, 'invalid reel leaves database unchanged');

    $plans = [['name' => 'Полная 100% оплата'], ['name' => 'Рассрочка 30/70'], ['name' => 'Удобная 50/50']];
    check(PaymentPlans::names($plans) === PaymentPlans::names(array_reverse($plans)), 'reordering services does not change calculator mapping');
    check(PaymentPlans::names($plans)['5050'] === 'Удобная 50/50', 'calculator term matches correct label');

    $view = new \App\View\AppView(new ServerRequest(['url' => '/admin/comps/edit/1']));
    $view->set('data', $comps->get($record->id));
    $html = $view->render('Admin/Comps/edit', false);
    check(strpos($html, 'name="instagram_embed"') !== false && strpos($html, $url) !== false, 'Instagram URL is editable in actual admin form');
    check(strpos($html, 'name="social_caption"') !== false && strpos($html, 'name="img"') !== false, 'caption and photo controls coexist');
    $view = new \App\View\AppView(new ServerRequest(['url' => '/admin/comps/edit/1']));
    $view->set('data', $comps->newEntity(['id' => 100, 'alias' => 'other', 'title' => 'Photo', 'body' => 'Editable description', 'img' => 'keep.jpg']));
    $html = $view->render('Admin/Comps/edit', false);
    check(strpos($html, 'name="body"') !== false && strpos($html, 'name="img"') !== false, 'ordinary photo component keeps editable description');

    $request = (new ServerRequest(['url' => '/admin/requests']))->withAttribute('paging', ['Requests' => ['page' => 1, 'current' => 1, 'count' => 1, 'perPage' => 20, 'pageCount' => 1, 'start' => 1, 'end' => 1, 'prevPage' => false, 'nextPage' => false, 'sort' => null, 'direction' => null]]);
    $view = new \App\View\AppView($request);
    $payload = '<img src=x onerror=alert(1)>';
    $view->set(['data' => [['id' => 1, 'name' => $payload, 'phone' => $payload, 'message' => "План 50/50\n" . $payload, 'date' => new \Cake\I18n\FrozenTime('2026-10-22 10:00:00')]], 'l' => 'ru']);
    $html = $view->render('Admin/Requests/index', false);
    check(strpos($html, $payload) === false && substr_count($html, '&lt;img src=x onerror=alert(1)&gt;') === 3, 'name, phone and message are escaped in admin HTML');
    check(strpos($html, 'План 50/50') !== false, 'request details visible in admin table');

    $view = new \App\View\AppView();
    $view->set(['services' => $plans]);
    $html = $view->render('Pages/home', false);
    check(substr_count($html, 'data-instgrm-permalink=') === 4 && substr_count($html, 'https://www.instagram.com/embed.js') === 1, 'four posts with one official embed script');
    check(strpos($html, 'href="https://astanacreative.kz/"') !== false, 'footer attribution link rendered');

    // Cards without a video URL stay photographs, including whitespace-only values.
    $gallery = [
        ['title' => 'Знакомство с Pangaea', 'link' => 'https://youtu.be/abcdefghijk', 'img' => 'first.jpg'],
        ['title' => 'Пространство для жизни', 'link' => '', 'img' => 'second.jpg'],
        ['title' => 'Место для отдыха', 'link' => '   ', 'img' => 'third.jpg'],
    ];
    $view = new \App\View\AppView();
    $view->set('galleries', $gallery);
    $html = $view->render('Pages/home', false);
    check(substr_count($html, 'class="gallery-play"') === 1, 'only linked gallery card has Play');
    check(substr_count($html, 'class="gallery-preview gallery-photo"') === 2, 'empty and whitespace URLs render non-interactive photos');
    check(strpos($html, 'data-gallery-video') === false && strpos($html, 'gallery-status') === false, 'unconnected video demo controls are absent');
    foreach (['first.jpg', 'second.jpg', 'third.jpg'] as $filename) {
        check(strpos($html, '/img/galleries/' . $filename) !== false, 'gallery photo retained: ' . $filename);
    }
    $gallery[1]['link'] = 'https://youtu.be/12345678901';
    $view = new \App\View\AppView();
    $view->set('galleries', $gallery);
    $html = $view->render('Pages/home', false);
    check(substr_count($html, 'class="gallery-play"') === 2 && strpos($html, 'data-video-src="https://youtu.be/12345678901"') !== false, 'adding a URL automatically enables the second player');

    // Render actual list/edit templates with optional SVG, raster and missing media.
    foreach (['Advans', 'Characteristics', 'Preferences', 'Services'] as $section) {
        $directory = strtolower($section);
        $filename = ImageStorage::upload(upload('jpg'), $directory);
        $rows = [];
        foreach ([
            ['icon' => '<svg viewBox="0 0 32 32"><path d="M4 4h24v24H4z"/></svg>', 'img' => ''],
            ['icon' => '', 'img' => $filename],
            ['icon' => '', 'img' => ''],
            ['icon' => '', 'img' => 'missing.jpg'],
        ] as $i => $media) {
            $rows[] = $media + ['id' => 100 + $i, 'title' => 'QA media', 'body' => 'Description', 'item_order' => 0];
        }
        $view = new \App\View\AppView(new ServerRequest(['url' => '/admin/' . $directory]));
        $view->set('data', $rows);
        $html = $view->render('Admin/' . $section . '/index', false);
        check(substr_count($html, 'class="admin-media-preview"') === 2 && substr_count($html, 'Нет изображения') === 2, "$section list handles SVG, raster, empty and missing files");
        check(strpos($html, '/img/' . $directory . '/thumbs/' . $filename) !== false, "$section existing thumbnail is displayed");
        check(strpos($html, 'data:image/svg+xml;base64,') !== false, "$section SVG is displayed as an image document");
        $model = $section === 'Characteristics' ? 'Сharacteristics' : $section;
        $view = new \App\View\AppView(new ServerRequest(['url' => '/admin/' . $directory . '/edit/100']));
        $editRow = $section === 'Services' ? $rows[1] : $rows[0];
        $view->set('data', TableRegistry::getTableLocator()->get($model)->newEntity($editRow));
        $html = $view->render('Admin/' . $section . '/edit', false);
        $expectedMedia = $section === 'Services' ? '/img/services/thumbs/' . $filename : 'data:image/svg+xml;base64,';
        check(strpos($html, $expectedMedia) !== false && strpos($html, 'name="img"') !== false, "$section edit displays existing media and retains upload control");
        unlink(WWW_ROOT . "img/$directory/thumbs/$filename");
        $view = new \App\View\AppView();
        $html = $view->element('admin/media_preview', ['item' => $rows[1], 'directory' => $directory]);
        check(strpos($html, '/img/' . $directory . '/' . $filename) !== false, "$section missing thumbnail falls back to existing original");
    }
    $view = new \App\View\AppView();
    $html = $view->element('admin/media_preview', ['item' => ['img' => '../other.jpg'], 'directory' => 'advans']);
    check(strpos($html, '<img') === false, 'preview never emits a directory traversal URL');

    $galleryTable = TableRegistry::getTableLocator()->get('Galleries');
    foreach (['add', 'edit'] as $action) {
        $view = new \App\View\AppView(new ServerRequest(['url' => '/admin/galleries/' . $action]));
        $view->set('data', $galleryTable->newEntity(['id' => 99, 'title' => 'Photo', 'link' => '', 'img' => '']));
        $html = $view->render('Admin/Galleries/' . $action, false);
        preg_match('/<input\b[^>]*name="link"[^>]*>/', $html, $input);
        check(!empty($input) && strpos($input[0], 'required') === false && strpos($input[0], 'id="inputLink"') !== false, "gallery $action permits photos without a link and has a unique link field ID");
    }

    $privacy = new \App\Controller\PagesController(new ServerRequest(['url' => '/privacy-policy']));
    $privacy->privacyPolicy();
    $meta = $privacy->viewBuilder()->getVars()['meta'];
    check($meta['title'] === 'Политика конфиденциальности — Pangaea', 'privacy route sets a descriptive tab title');
    $view = new \App\View\AppView();
    $header = $view->element('header');
    $home = $view->render('Pages/home', false);
    foreach (['about', 'advantages', 'payment', 'specifications'] as $anchor) {
        check(strpos($header, 'href="/#' . $anchor . '"') !== false && strpos($home, 'id="' . $anchor . '"') !== false, 'privacy navigation targets an existing home section: ' . $anchor);
    }
    $view = new \App\View\AppView();
    $policyComps = array_fill(1, 11, ['body' => '']);
    $view->set(['meta' => $meta, 'comps' => $policyComps]);
    $html = $view->renderLayout('Policy', 'index');
    check(strpos($html, '<title>Политика конфиденциальности — Pangaea</title>') !== false, 'privacy title is rendered without the stray less-than character');
    $view = new \App\View\AppView();
    $view->set('comps', $policyComps);
    $html = $view->renderLayout('Policy', 'index');
    check(strpos($html, '<title>Pangaea — МЖК «Астана»</title>') !== false, 'secondary page fallback title is well formed');
    echo "PASS: $checks regression checks; isolated DB and files only.\n";
} finally {
    foreach ($uploadStreams as $stream) $stream->close();
    // Remove only the randomly generated test directory owned by this process.
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(TMP, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
    unset($files, $file);
    rmdir(TMP);
}
