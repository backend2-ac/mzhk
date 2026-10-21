<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;


/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */
/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

// Route static method
Router::connect('/second-route', [
    'controller' => 'First', 
    'action' => 'secondRoute',
]);


// PREFIX Routing

Router::prefix('Admin', function(RouteBuilder $builder){

    $builder->connect('/logout', ['controller' => 'Admin', 'action' => 'logout']);
    $builder->connect('/add', ['controller' => 'Admin', 'action' => 'add']);
    $builder->connect('/all', ['controller' => 'Admin', 'action' => 'all']);
    $builder->connect('/edit/*', ['controller' => 'Admin', 'action' => 'edit']);
    $builder->connect('/delete/*', ['controller' => 'Admin', 'action' => 'delete']);

    $builder->connect('/', ['controller' => 'Admin', 'action' => 'index']);

    $builder->connect('/pages', ['controller' => 'Pages', 'action' => 'index']);
    $builder->connect('/pages/:action/*', ['controller' => 'Pages']);

    $builder->connect('/comps', ['controller' => 'Comps', 'action' => 'index']);
    $builder->connect('/comps/:action/*', ['controller' => 'Comps']);

    $builder->connect('/slides', ['controller' => 'Slides', 'action' => 'index']);
    $builder->connect('/slides/:action/*', ['controller' => 'Slides']);

    $builder->connect('/houses', ['controller' => 'Houses', 'action' => 'index']);
    $builder->connect('/houses/:action/*', ['controller' => 'Houses']);

    $builder->connect('/advans', ['controller' => 'Advans', 'action' => 'index']);
    $builder->connect('/advans/:action/*', ['controller' => 'Advans']);
    
    $builder->connect('/infrastructures', ['controller' => 'Infrastructures', 'action' => 'index']);
    $builder->connect('/infrastructures/:action/*', ['controller' => 'Infrastructures']);

    $builder->connect('/galleries', ['controller' => 'Galleries', 'action' => 'index']);
    $builder->connect('/galleries/:action/*', ['controller' => 'Galleries']);

    $builder->connect('/characteristics', ['controller' => 'Characteristics', 'action' => 'index']);
    $builder->connect('/characteristics/:action/*', ['controller' => 'Characteristics']);

    $builder->connect('/preferences', ['controller' => 'Preferences', 'action' => 'index']);
    $builder->connect('/preferences/:action/*', ['controller' => 'Preferences']);
    
    $builder->connect('/services', ['controller' => 'Services', 'action' => 'index']);
    $builder->connect('/services/:action/*', ['controller' => 'Services']);

   
    $builder->connect('/requests', ['controller' => 'Requests', 'action' => 'index']);
    $builder->connect('/requests/:action/*', ['controller' => 'Requests']);
    
 
    $builder->fallbacks();
});

// $routes->scope('/{lang}', function (RouteBuilder $builder) {

//     $builder->connect('/', ['controller' => 'Pages', 'action' => 'home'])
//         ->setPass(['lang']) // УБИРАТЬ ЕСЛИ ЕСТЬ ALLIAS
//         ->setPatterns(['lang' => 'ru|kz|en'])
//         ->setPersist(['lang']);
    
//     $builder->fallbacks();
// });

$routes->scope('/', function (RouteBuilder $builder) {

    $builder->connect('/', ['controller' => 'Pages', 'action' => 'home']);
    $builder->connect('/privacy-policy', ['controller' => 'Pages', 'action' => 'privacyPolicy']);
  

    $builder->fallbacks();
});


/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *     
 *     // Parse specified extensions from URLs
 *     // $builder->setExtensions(['json', 'xml']);
 *     
 *     // Connect API actions here.
 * });
 * ```
 */
