<?php 

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class PreferencesTable extends Table{

	public function initialize(array $config): void{
		$this->setTable('preferences');
	}
}


 ?>