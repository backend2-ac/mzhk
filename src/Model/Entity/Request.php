<?php 

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Request extends Entity{

	protected $_accessible = [
		'name' => true,
		'phone' => true,
		'sevice_name' => true,
		'date' => true,
		'message' => true,
		'doc' => true,
		'type' => true,
		'service_id' => true,
		
	];

	protected function _customUploadDoc($file = array()){

		if( is_array($file) || is_object($file) ){
			$fileName = $file->getClientFilename();
			$oldPath = WWW_ROOT . 'files'.DS.'docs'.DS. $fileName;

			if(file_exists($oldPath)){
				return $fileName;
			}

			$ext = strtolower(preg_replace("#.+\.([a-z]+)$#", "$1", $fileName));
			$fileName = $this->_genNameFileDoc($ext);
			$path = WWW_ROOT . 'files'.DS.'docs'.DS. $fileName;
			$file_move = $file->moveTo($path);

			return $fileName;

		} elseif( is_string($file) ){
			return $file;
		} else{
			return false;
		}
	}
	protected function _genNameFileDoc($ext){
		$name = md5(microtime()) . ".{$ext}";
		if(is_file(WWW_ROOT . 'files'.DS.'docs'.DS. $name)){
			$name = $this->_genNameFileDoc($ext);
		}
		return $name;
	}

	public function _setDoc($file){
		if( is_string($file) ){
			return $this->_customUploadDoc($file);
		} else{
			return $this->_customUploadDoc($file);
		}
	}
}

 ?>