<?php
/** Isolated regression checks: php tests/regression.php (GD, intl, SQLite required).
 * Uses only an in-memory database and a newly created temporary image directory.
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require dirname(__DIR__) . '/vendor/autoload.php';
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('APP', ROOT . '/src/');
define('CONFIG', ROOT . '/config/');
define('TMP', sys_get_temp_dir() . '/mzhk-regression-' . bin2hex(random_bytes(8)) . '/');
define('WWW_ROOT', TMP . 'webroot/');
define('CORE_PATH', ROOT . '/vendor/cakephp/cakephp/');
define('CAKE', CORE_PATH . 'src/');
define('CACHE', TMP . 'cache/');
define('LOGS', TMP . 'logs/');
define('RESOURCES', ROOT . '/resources/');
mkdir(WWW_ROOT, 0755, true);
require ROOT . '/vendor/cakephp/cakephp/config/bootstrap.php';

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
function check($condition, string $message): void {
    global $checks;
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    $checks++;
}
function upload(string $format = 'png', int $size = 400): UploadedFile {
    $path = tempnam(TMP, 'input-');
    $image = imagecreatetruecolor($size, $size);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
    imagefilledrectangle($image, 20, 20, 60, 60, imagecolorallocate($image, 245, 128, 32));
    $writer = ['jpg' => 'imagejpeg', 'png' => 'imagepng', 'gif' => 'imagegif'][$format];
    $writer($image, $path);
    imagedestroy($image);
    return new UploadedFile($path, filesize($path), UPLOAD_ERR_OK, 'PLAN.' . strtoupper($format), 'application/octet-stream');
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
    echo "PASS: $checks regression checks; isolated DB and files only.\n";
} finally {
    // Remove only the randomly generated test directory owned by this process.
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(TMP, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
    rmdir(TMP);
}
