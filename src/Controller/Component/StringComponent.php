<?php 

namespace App\Controller\Component;

use Cake\Controller\Component;

class StringComponent extends Component{

	public function arraySum( $arr = array() ){
		return array_sum($arr);
	}

	public function arrayReverse( $arr = array() ){
		return array_reverse($arr);
	}
}

 ?>