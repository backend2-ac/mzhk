<?php 

namespace App\View\Helper;

use Cake\View\Helper;

class StringHelper extends Helper{

	public $helpers = ['Html'];

	public function createLinkWithUpperCase($string){
		return $this->Html->link(
			strtoupper($string),
			[
				'controller' => 'Home',
				'action' => 'sayWelcome'
			]
		);
	}

	public function upperCase($string){
		return strtoupper($string);
	}

	public function lowerCase($string){
		return strtolower($string);
	}
}

 ?>