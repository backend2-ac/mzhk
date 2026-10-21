<?php 

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class PagesTable extends Table{

	public function initialize(array $config): void{
		$this->setTable('pages');

		$this->hasMany('Documents');

		$this->addBehavior('Translate', [
			'fields' => [
				'title', 
			],
			'allowEmptyTranslations' => false
		]);
	}

	// public function validationDefault(Validator $validator): Validator{
	// 	$validator->allowEmptyString('meta_title');
	// 	return $validator;
	// }
}


 ?>