<?php

namespace App\Controller\Admin;

use App\Controller\AppController; 
use Cake\Validation\Validator;
use Cake\Utility\Text;

class RequestsController extends AppController{

    public function initialize(): void{
        parent::initialize();
        $this->loadModel('Requests');
       
        $this->loadComponent('Paginator');
    }

    public function index(){
        $model = 'Requests';

        $type = 'request';
        if( isset($_GET['type']) && $_GET['type'] && ($_GET['type'] == 'request' || $_GET['type'] == 'reviews') ){
            $type = $_GET['type'];
        }

        $cur_page = 1;
        if( isset($_GET['page']) && is_int(intval($_GET['page'])) ){
            $cur_page = $_GET['page'];
        }
        $per_page = 20;
        $offset = ($cur_page * $per_page) - $per_page;
        $pag_settings = [
            'limit' => $per_page,
        ];

        $data = $this->$model->find()
            
            ->order(['date' => 'DESC'])->limit($per_page)->offset($offset)
            ->toList();

        $this->set( compact('data') );

        $this->set('pagination', $this->paginate(
            $this->$model->find('all')
            
            ->limit($per_page)->order(['date' => 'DESC']), 
            $pag_settings
        ));
    }

    public function delete($item_id = null){
        $model = 'Requests';
        
        $this->request->allowMethod(['post', 'delete']);

        $data = $this->$model->get($item_id);

        if ($this->$model->delete($data)) {
            $this->Flash->success(__('Элемент успешно удален'));
            return $this->redirect( $this->referer() );
        } else{
            $this->Flash->error(__('Ошибка удаления'));
        }
    }
}


 ?>