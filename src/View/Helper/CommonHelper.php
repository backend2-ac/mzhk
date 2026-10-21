<?php 

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;


class CommonHelper extends Helper{

	public function initialize(array $config): void{
        $db = ConnectionManager::get('default');
    }

	public function get_element($id) {
		$model = TableRegistry::getTableLocator()->get('Comps');
		// $l = Configure::read('Config.lang');
		// $model->setLocale($l);
		$data = $model->get($id);

		if($data){
			if($data['body']){
				return $data['body'];
			} else{
				// $model->setLocale('ru');
				$data = $model->get($id);
				if( $data['body'] ){
					return $data['body'];
				}
			}
		}
    }

    public function get_element_img($id){
    	$model = TableRegistry::getTableLocator()->get('Comps');
    	// $model->setLocale('ru');
		$data = $model->get($id);

    	if( $data ){
    		if( $data['img'] ){
    			return '/img/comps/' . $data['img'];
    		}
    	}
    }
}

 ?>