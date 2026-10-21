<?php 

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class GalleriesTable extends Table{

	public function initialize(array $config): void{
		$this->setTable('galleries');
	}
}


 ?>