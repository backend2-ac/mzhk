<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class House extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'houses';

    protected $_accessible = [
		'title' => true,
		'body' => true,
		'img' => true,
        'room' => true,
        'item_order' => true,
	];
}
