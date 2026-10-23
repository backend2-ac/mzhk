<?php
/** Standalone HTTP error checks. Does not load app config or configure a database. */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
$vendor = getenv('MZHK_QA_VENDOR') ?: dirname(__DIR__) . '/vendor';
$loader = require $vendor . '/autoload.php';
$loader->addPsr4('App\\', dirname(__DIR__) . '/src/', true);
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('APP', ROOT . '/src/');
define('CONFIG', ROOT . '/config/');
define('TMP', sys_get_temp_dir() . '/');
define('WWW_ROOT', ROOT . '/webroot/');
define('CORE_PATH', $vendor . '/cakephp/cakephp/');
define('CAKE', CORE_PATH . 'src/');
define('RESOURCES', ROOT . '/resources/');
require CORE_PATH . 'config/bootstrap.php';

use App\Error\SiteExceptionRenderer;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\ServerRequest;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\MissingControllerException;
use Cake\Controller\Exception\MissingActionException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Routing\Exception\MissingRouteException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

Configure::write('App.namespace', 'App');
Configure::write('App.encoding', 'UTF-8');
Configure::write('App.paths.templates', [ROOT . '/templates/']);
foreach (['long', '_cake_core_', '_cake_model_'] as $cache) Cache::setConfig($cache, ['className' => 'Array']);
$checks = 0;
function checkErrorPage(bool $condition, string $label): void {
    global $checks;
    if (!$condition) throw new RuntimeException('FAIL: ' . $label);
    $checks++;
}
$private = 'PRIVATE_INTERNAL_PATH_AND_MESSAGE';
$request = (new ServerRequest(['url' => '/qa-not-found-20261023', 'environment' => ['REQUEST_METHOD' => 'GET', 'HTTP_ACCEPT' => 'text/html']]))
    ->withAttribute('params', ['controller' => 'QAMissing', 'action' => 'index', 'prefix' => null, 'plugin' => null]);
$errors = [
    new NotFoundException($private),
    new MissingControllerException($private),
    new MissingActionException($private),
    new RecordNotFoundException($private),
    new MissingRouteException($private),
];
foreach ([false, true] as $debug) {
    Configure::write('debug', $debug);
    foreach ($errors as $error) {
        $response = (new SiteExceptionRenderer($error, $request))->render();
        $body = (string)$response->getBody();
        checkErrorPage($response->getStatusCode() === 404, get_class($error) . ' retains HTTP 404 with debug=' . (int)$debug);
        checkErrorPage(strpos($response->getHeaderLine('Content-Type'), 'text/html') === 0, 'HTML content type');
        checkErrorPage(strpos($body, 'Страница<br>не найдена') !== false && strpos($body, 'href="/"') !== false && strpos($body, 'href="/#layouts"') !== false, '404 has title and recovery links');
        checkErrorPage(strpos($body, $private) === false && strpos($body, '/qa-not-found-20261023') === false && strpos($body, 'Stack Trace') === false, 'internal details and request path are not exposed');
        checkErrorPage($response->getHeaderLine('X-Robots-Tag') === 'noindex, follow', '404 is excluded from indexing');
    }
    $json = (new SiteExceptionRenderer(new NotFoundException($private), $request->withHeader('Accept', 'application/json')))->render();
    checkErrorPage($json->getStatusCode() === 404 && strpos($json->getHeaderLine('Content-Type'), 'application/json') === 0, 'JSON request retains JSON response');
    checkErrorPage(json_decode((string)$json->getBody(), true) === ['message' => 'Страница не найдена', 'code' => 404], 'JSON 404 has no internal details');

    $middleware = new ErrorHandlerMiddleware(['exceptionRenderer' => SiteExceptionRenderer::class, 'log' => false]);
    $handler = new class implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface {
            throw new MissingControllerException('PRIVATE_INTERNAL_PATH_AND_MESSAGE');
        }
    };
    $response = $middleware->process($request, $handler);
    checkErrorPage($response->getStatusCode() === 404 && strpos((string)$response->getBody(), 'not-found-main') !== false, 'HTTP middleware serves the designed 404');
}
checkErrorPage(ConnectionManager::configured() === [], '404 renders without any configured database');
echo "PASS: $checks HTTP 404 checks; no database configured.\n";
