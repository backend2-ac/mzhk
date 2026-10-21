<?php 

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Page extends Entity{

	protected $_accessible = [
		'title' => true, 
		'alias' => true, 
		'meta_title' => true, 
		'meta_description' => true,
		'meta_keywords' => true,
	];
}

 ?>