<?php 

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\Utility\Text;

class SlugBehavior extends Behavior{

	protected $_defaultConfig = [
		'field' => 'name',
		'slug' => 'slug',
		'replacement' => '-'
	];

	public function slug(EntityInterface $entity){

		$config = $this->getConfig();
		
		$value = $entity->get($config['field']);

		$entity->set($config['slug'], Text::slug($value, $config['replacement']));

		// return Text::slug($value, $this->_config['replacement']);
	}

	public function beforeSave(EventInterface $eventInterface, EntityInterface $entity, ArrayObject $option){
		return $this->slug($entity);
	}
}

 ?>