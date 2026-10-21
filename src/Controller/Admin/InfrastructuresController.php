<?php

namespace App\Controller\Admin;

use App\Controller\AppController; 
use Cake\I18n\I18n;
use Cake\Validation\Validator;

use Cake\Cache\Cache;

class InfrastructuresController extends AppController{
    
    public function initialize(): void{
        parent::initialize();
        $this->loadModel('Infrastructures');
        $this->loadComponent('Paginator');
    }

    

    public function index(){
        $model = 'Infrastructures';

        $data = $this->$model->find('all')
            ->orderDesc('item_order')
            ->toList();

        $this->set( compact('data') );
    }

    public function add(){
        $model = 'Infrastructures';


        if( $this->request->is('post') ){
            $data = $this->request->getData();

            if( isset($data['img']) && $data['img'] ){
                $imageObject = $data['img'];
                $fileName = $imageObject->getClientFilename();
                unset($data['img']);
            }
            if( isset($data['images']) && $data['images'] ){
                $imagesObject = $data['images'];
                $fileNameImages = $imagesObject->getClientFilename();
                unset($data['images']);
            }
            $entity = $this->$model->newEntity($data);

            if( isset($fileName) && $fileName ){
                $entity->img = $imageObject;
                if( $entity->get('img') == false ){
                    $entity->setError('img', [__('Ошибка валидации изображения')]);
                }
            }
            if( isset($fileNameImages) && $fileNameImages ){
                $entity->images = $imagesObject;
                if( $entity->get('images') == false ){
                    $entity->setError('images', [__('Ошибка валидации изображения')]);
                }
            }

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

        // $this->set( compact('cats_sel') );
    }

    public function edit($item_id = null){
        $model = 'Infrastructures';


        $data = $this->$model->get($item_id);

        if ($this->request->is(['post', 'put'])) {
            $data1 = $this->request->getData();

            if( isset($data1['img']) && $data1['img'] ){
                $imageObject = $data1['img'];
                $fileName = $imageObject->getClientFilename();
                unset($data1['img']);
            }
            if( isset($data1['images']) && $data1['images'] ){
                $imagesObject = $data1['images'];
                $fileNameImages = $imagesObject->getClientFilename();
                unset($data1['images']);
            }
            $entity = $this->$model->newEntity($data1);

            if( isset($fileName) && $fileName ){
                $entity->img = $imageObject;
                if( $entity->get('img') == false ){
                    $entity->setError('img', [__('Ошибка валидации изображения')]);
                }
            }
            if( isset($fileNameImages) && $fileNameImages ){
                $entity->images = $imagesObject;
                if( $entity->get('images') == false ){
                    $entity->setError('images', [__('Ошибка валидации изображения')]);
                }
            }

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
        $model = 'Infrastructures';

        $this->request->allowMethod(['post', 'delete']);
        $data = $this->$model->get($item_id);

        $img_del = [];
        $img_del[] = $data['img'];

        if ($this->$model->delete($data)) {
            $this->Flash->success(__('Элемент успешно удален'));
            $this->_cacheDelete();
            $this->_imgDelete($img_del);
            return $this->redirect( $this->referer() );
        } else{
            $this->Flash->error(__('Ошибка удаления'));
        }
    }

    protected function _cacheDelete(){
        Cache::delete('infrastructures', 'long');

       
    }

    protected function _imgDelete($img_del = null){
        if( $img_del ){
            foreach( $img_del as $img_name ){
                if( $img_name ){
                    $fileName = WWW_ROOT.'img'.DS.'infrastructures'.DS.$img_name;
                    $fileNameThumbs = WWW_ROOT.'img'.DS.'infrastructures'.DS. $img_name;
                    if( file_exists($fileName) ){
                        unlink($fileName);
                    }
                    if( file_exists($fileNameThumbs) ){
                        unlink($fileNameThumbs);
                    }
                    clearstatcache();
                }
            }
        }
    }
}


 ?>