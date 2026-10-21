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

use Cake\Controller\Controller;
use Cake\Core\Configure;

use Cake\I18n\I18n;

use Cake\Cache\Cache;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('String');

        $this->loadComponent("Auth", [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'username', 
                        'password' => 'password'
                    ],
                    'userModel' => 'Admins'
                ]
            ],
            'loginAction' => [
                'controller' => 'Admin',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Admin',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Admin', 
                'action' => 'index'
            ],
        ]);

        $this->loadModel('Comps');


        // $this->loadModel('Pages');

        // $this->loadComponent('Authorization.Authorization');

        // $this->loadComponent('Authentication.Authentication');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event){
        parent::beforeFilter($event);


        $params = $this->request->getAttribute('params');
        $admin = (isset($params['prefix']) && $params['prefix'] == 'Admin') ? 'admin/' : false;

        if( isset($params['controller']) && $params['controller'] == 'User' ){
            
        }

        if(!$admin){
            $this->Auth->allow();
        }

        $session = $this->request->getSession();
        $userName = $session->read('Auth.User.username');
        $login = !empty($userName);

        if( $login ){
            $_SESSION['KCFINDER'] = array(
                'disabled' => false,
            );
        } else{
            $_SESSION['KCFINDER'] = array(
                'disabled' => true,
            );
        }

        if( $admin){
            $this->viewBuilder()->setLayout('default');
        } else{
            $this->viewBuilder()->setLayout('index');
        }

        if(isset($params['lang']) && $params['lang'] == 'kz'){
            Configure::write('Config.lang', 'kz');
            $session->write('lang',  'kz');
        }elseif(isset($params['lang']) && $params['lang'] == 'en'){
            Configure::write('Config.lang', 'en');
            $session->write('lang',  'en');
        }else{
            Configure::write('Config.lang', 'ru');
        }

        $l = Configure::read('Config.lang');
        $lang = ( isset($params['lang']) && $params['lang'] ) ? $params['lang'] . '/' : '';

        I18n::setLocale($l);
        
        $request = $params;



        $langs_ids = [1];

        $comps_lang = Cache::read('comps_'.$l, 'long');
        if( !$comps_lang ){
            $this->Comps->setLocale($l);
            $all_comps_lang = $this->Comps->find('all')
                ->where(['Comps.id IN' => $langs_ids])
                ->toList();

            $comps_list_lang = [];
            foreach( $all_comps_lang as $comp_item ){
                $comps_list_lang[$comp_item['id']] = $comp_item;
            }
            Cache::write('comps_'.$l, $comps_list_lang, 'long');
            $comps_lang = Cache::read('comps_'.$l, 'long');
        }

        $comps = Cache::read('comps', 'long');
        if( !$comps ){
            // $this->Comps->setLocale('ru');
            $all_comps = $this->Comps->find('all')
                // ->where(['Comps.id NOT IN' => $langs_ids])
                ->toList();

            $comps_list = [];
            foreach( $all_comps as $comp_item ){
                $comps_list[$comp_item['id']] = $comp_item;
            }
            Cache::write('comps', $comps_list, 'long');
            $comps = Cache::read('comps', 'long');
        }

        $this->set( compact('admin', 'login', 'l', 'lang', 'request', 'comps', 'comps_lang') );
    }
}
