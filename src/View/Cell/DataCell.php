<?php 

namespace App\View\Cell;

use Cake\View\Cell;

class DataCell extends Cell{

	public $developer;

	public function initialize(): void{
		$this->developer = array(
			'Developer1',
			'Developer2',
			'Developer3',
			'Developer4',
			'Developer5',
		);
	}

	public function display(){

		$this->set('dev_count', count($this->developer));
	}

	public function sayMessage($message, $developers){

		$msg = "Welcome to " . $message;
		print_r($developers);
		$this->set('welcome_msg', $msg);
	}
}


 ?>