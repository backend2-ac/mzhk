<?php
declare(strict_types=1);

namespace App\Error;

use Cake\Error\ExceptionRenderer;
use Cake\View\View;
use Psr\Http\Message\ResponseInterface;

/** Render a public 404 without loading content, authentication or database helpers. */
class SiteExceptionRenderer extends ExceptionRenderer
{
    public function render(): ResponseInterface
    {
        if ($this->getHttpCode($this->error) !== 404) {
            return parent::render();
        }

        $this->clearOutput();
        $request = $this->controller->getRequest();
        $response = $this->controller->getResponse()
            ->withStatus(404)
            ->withHeader('X-Robots-Tag', 'noindex, follow');

        if ($request->accepts('application/json') && !$request->accepts('text/html')) {
            return $response->withType('json')->withStringBody(json_encode([
                'message' => 'Страница не найдена',
                'code' => 404,
            ], JSON_UNESCAPED_UNICODE));
        }

        // A plain Cake view keeps this page available even without a database.
        $view = new View($request);
        $view->setTemplatePath('Error');

        return $response->withType('html')->withStringBody(
            $view->render('not_found', 'site_error')
        );
    }
}
