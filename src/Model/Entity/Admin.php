<?php 

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Admin extends Entity{

	protected $_accessible = [
		'username' => true,
		'password' => true,
		'role' => true,
		'created' => true,
		'modified' => true,
	];

	// public function _getNameEmail(){
	// 	return $this->name . "_" . $this->email;
	// }

	// public function _getEmail($value){
	// 	return strtoupper($value);
	// }

	// public function _setEmail($value){
	// 	return strtoupper($value);
	// }

	// public function _getName($value){
	// 	return $value . '_get';
	// }

	// public function _setName($value){
	// 	return $value . '_set';
	// }
}


 ?>