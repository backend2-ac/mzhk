<?php

namespace App\Controller\Admin;

use App\Controller\AppController; 

class AdminController extends AppController{

	public function initialize(): void{
		parent::initialize();

		$this->loadModel('Admins');
	}


	public function index(){


		// $session = $this->request->getSession();
		// $userName = $session->read('Auth.User.username');

		// debug($session);
		// debug($userName);
		// debug(!empty($userName));
	}

	public function login(){
		if( $this->request->is('post') ){
			$userData = $this->Auth->identify();

			if( $userData ){
				$this->Auth->setUser($userData);
				return $this->redirect($this->Auth->redirectUrl());
			} else{
				$this->Flash->error('Invalid login credentials');
			}
		}
	}

	public function logout(){
		return $this->redirect($this->Auth->logout());
	}

	public function all(){
		$admins = $this->Admins->find('all')->toList();

		$this->set(compact('admins'));
	}

	public function add(){
		if( $this->request->is('post') ){
			$data = $this->request->getData();

			$data['role'] = 'user';

			$passwd = $data['password'];
			$new_passwd = password_hash($passwd, PASSWORD_BCRYPT);
			$data['password'] = $new_passwd;

			$admins = $this->getTableLocator()->get('Admins');
			$entity = $admins->newEntity($data);

			if( $this->Admins->save($entity) ){
				$this->Flash->success(__('Данные успешно сохранены'));
				return $this->redirect( $this->referer() );
			} else{
				$this->Flash->error(__('Ошибка сохранения данных'));
			}
		}
	}


	public function edit($item_id = null){

		$data = $this->Admins->get($item_id);

	    if ($this->request->is(['post', 'put'])) {

	    	$data1 = $this->request->getData();

	    	$passwd = $data1['password'];
			$new_passwd = password_hash($passwd, PASSWORD_BCRYPT);
			$data1['password'] = $new_passwd;

			$admins = $this->getTableLocator()->get('Admins');
			$entity = $admins->newEntity($data1);

			$new_data = $entity->toArray();

	        $this->Admins->patchEntity($data, $new_data);

	        if ($this->Admins->save($data)) {
	            $this->Flash->success(__('Изменения сохранены'));
	            return $this->redirect( $this->referer() );
	        }
	        $this->Flash->error(__('Ошибка сохранения'));
	    }

	    $this->set( compact('data') );
	}

	public function delete($item_id = null){
		$this->request->allowMethod(['post', 'delete']);

	    $data = $this->Admins->get($item_id);

	    if ($this->Admins->delete($data)) {
	        $this->Flash->success(__('Админ был удален'));
	        return $this->redirect( $this->referer() );
	    } else{
	    	$this->Flash->error(__('Ошибка удаления'));
	    }
	}
}


 ?>