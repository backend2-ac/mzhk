<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;

use Cake\Cache\Cache;

use Cake\ORM\Query;



/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */

    public function initialize(): void{
        parent::initialize();
    
        $this->loadModel('Pages');
        $this->loadModel('Slides');
        $this->loadModel('Houses');
        $this->loadModel('Advans');
        $this->loadModel('Galleries');
        $this->loadModel('Infrastructures');
        $this->loadModel('Сharacteristics');
        $this->loadModel('Preferences');
        $this->loadModel('Services');
        
        
        $this->loadComponent('Paginator');
    }

    public function home(): void{
        $this->viewBuilder()->setLayout('site');
        $cur_lang = Configure::read('Config.lang');

        $slides = Cache::read('slides', 'long');
        if( !$slides ){
            $all_slides = $this->Slides->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('slides', $all_slides, 'long');
            $slides = Cache::read('slides', 'long');
        }

        $houses = Cache::read('houses', 'long');
        if( !$houses ){
            $all_houses = $this->Houses->find('all')
                ->where(['room' => 1])
                ->orderDesc('item_order')
                ->toList();

            Cache::write('houses', $all_houses, 'long');
            $houses = Cache::read('houses', 'long');
        }

        $houses_2 = Cache::read('houses_2', 'long');
        if( !$houses_2 ){
            $all_houses_2 = $this->Houses->find('all')
                ->where(['room' => 2])
                ->orderDesc('item_order')
                ->toList();

            Cache::write('houses_2', $all_houses_2, 'long');
            $houses_2 = Cache::read('houses_2', 'long');
        }

        $advans = Cache::read('advans', 'long');
        if( !$advans ){
            $all_advans = $this->Advans->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('advans', $all_advans, 'long');
            $advans = Cache::read('advans', 'long');
        }

        $infrastructures = Cache::read('infrastructures', 'long');
        if( !$infrastructures ){
            $all_infrastructures = $this->Infrastructures->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('infrastructures', $all_infrastructures, 'long');
            $infrastructures = Cache::read('infrastructures', 'long');
        }
        
        $galleries = Cache::read('galleries', 'long');
        if( !$galleries ){
            $all_galleries = $this->Galleries->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('galleries', $all_galleries, 'long');
            $galleries = Cache::read('galleries', 'long');
        }

        $characteristics = Cache::read('characteristics', 'long');
        if( !$characteristics ){
            $all_characteristics = $this->Сharacteristics->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('characteristics', $all_characteristics, 'long');
            $characteristics = Cache::read('characteristics', 'long');
        }

        $preferences = Cache::read('preferences', 'long');
        if( !$preferences ){
            $all_preferences = $this->Preferences->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('preferences', $all_preferences, 'long');
            $preferences = Cache::read('preferences', 'long');
        }
        
        $services = Cache::read('services', 'long');
        if( !$services ){
            $all_services = $this->Services->find('all')
                ->orderDesc('item_order')
                ->toList();

            Cache::write('services', $all_services, 'long');
            $services = Cache::read('services', 'long');
        }
        
        $page = $this->Pages->get(1);
        if( $page ){
            $meta['title'] = $page['meta_title'];
            if( !$meta['title'] ){
                $meta['title'] = $page['title'];
            }
            $meta['desc'] = $page['meta_description'];
            $meta['keys'] = $page['meta_keywords'];
        }

        $this->set( compact('meta','slides','houses','houses_2','advans','infrastructures','galleries','characteristics','preferences','services') );
    }

    public function privacyPolicy(){
        
    }

}
