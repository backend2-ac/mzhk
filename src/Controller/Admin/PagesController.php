<?php

namespace App\Controller\Admin;

use App\Controller\AppController; 
use Cake\I18n\I18n;
use Cake\Validation\Validator;

use Cake\Utility\Text;
use Cake\Cache\Cache;

class PagesController extends AppController{

    public function initialize(): void{
        parent::initialize();

        $this->loadModel('Pages');
       
       
        $this->loadComponent('Paginator');
    }

    public function index(){
        $model = 'Pages';

        $cur_page = 1;
        if( isset($_GET['page']) && is_int(intval($_GET['page'])) ){
            $cur_page = $_GET['page'];
        }
        $per_page = 20;
        $offset = ($cur_page * $per_page) - $per_page;
        $pag_settings = [
            'limit' => $per_page,
        ];

        $data = $this->$model->find('translations', [
            'locales' => ['ru', 'kz', 'en']
        ])
            ->order([$model.'.id' => 'ASC'])
            ->limit($per_page)->offset($offset)
            ->toList();

        $this->set( compact('data') );

        $this->set('pagination', $this->paginate(
            $this->$model->find('all')->order([$model.'.id' => 'ASC'])
            ->limit($per_page), 
            $pag_settings
        ));
    }

    public function add(){
        $model = 'Pages';

        if(isset($_GET['lang']) && $_GET['lang'] == 'kz'){
            $this->$model->setLocale('kz');
        }elseif(isset($_GET['lang']) && $_GET['lang'] == 'en'){
            $this->$model->setLocale('en');
        }else{
            $this->$model->setLocale('ru');
        }

        if( $this->request->is('post') ){
            $data = $this->request->getData();

            if( !isset($data['alias']) || empty($data['alias']) ){
                $data['alias'] = Text::slug($data['title']);
                $data['alias'] = mb_strtolower($data['alias']);

                $created = $this->$model->find()
                    ->where(['alias' => $data['alias']])->first();

                if( $created ){
                    $data['alias'] = $this->_slug_render($data['alias']);

                    $created = $this->$model->find()
                        ->where(['alias' => $data['alias']])->first();

                    if( $created ){
                        $this->Flash->error( __('Запись с таким названием уже существует') );
                        return $this->redirect( $this->referer() );
                    }
                }
            }

            $entity = $this->$model->newEntity($data);

            if( $entity->getErrors() ){
                $errors = $entity->getErrors();
                foreach( $errors as $index => $err ){
                    $this->Flash->error( $err[array_key_first($err)] );
                }
                return $this->redirect( $this->referer() );
            }

            if( $this->$model->save($entity) ){
                $this->Flash->success(__('Данные успешно сохранены'));
                $this->_cacheDelete();
                return $this->redirect( $this->referer() );
            } else{
                $this->Flash->error(__('Ошибка сохранения данных'));
            }
        }
    }

    public function edit($item_id = null){
        $model = 'Pages';

        if(isset($_GET['lang']) && $_GET['lang'] == 'kz'){
            $this->$model->setLocale('kz');
        }elseif(isset($_GET['lang']) && $_GET['lang'] == 'en'){
            $this->$model->setLocale('en');
        }else{
            $this->$model->setLocale('ru');
        }

        $data = $this->$model->get($item_id);

        if ($this->request->is(['post', 'put'])) {
            $data1 = $this->request->getData();

            $entity = $this->$model->newEntity($data1);

            if( $entity->getErrors() ){
                $errors = $entity->getErrors();
                foreach( $errors as $index => $err ){
                    $this->Flash->error( $err[array_key_first($err)] );
                }
                return $this->redirect( $this->referer() );
            }

            $new_data = $entity->toArray();

            $this->$model->patchEntity($data, $new_data);

            if ($this->$model->save($data)) {
                $this->Flash->success(__('Изменения сохранены'));
                $this->_cacheDelete();
                return $this->redirect( $this->referer() );
            }
            $this->Flash->error(__('Ошибка сохранения'));
        }

        

        $this->set( compact('data') );
    }

    public function delete($item_id = null){
        $model = 'Pages';
        
        $this->request->allowMethod(['post', 'delete']);
        $data = $this->$model->get($item_id);

        if ($this->$model->delete($data)) {
            $this->Flash->success(__('Элемент успешно удален'));
            $this->_cacheDelete();
            return $this->redirect( $this->referer() );
        } else{
            $this->Flash->error(__('Ошибка удаления'));
        }
    }

    protected function _cacheDelete(){
        Cache::delete('pages_ru', 'long');
        Cache::delete('pages_kz', 'long');
        Cache::delete('pages_en', 'long');
    }

    protected function _slug_render($slug){
        $slug_date = date('YmdHis');
        $slug = $slug . '_' . $slug_date;
        return $slug;
    }
}


 ?>