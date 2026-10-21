<?php 

namespace App\Model\Table;

use Cake\ORM\Table;

class CompsTable extends Table{

	public function initialize(array $config): void{
		$this->setTable('comps');

		$this->addBehavior('Translate', [
			'fields' => [
				'body', 
			],
			'allowEmptyTranslations' => false
		]);
	}
	
}


 ?>