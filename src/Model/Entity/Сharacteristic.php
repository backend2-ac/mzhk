<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Сharacteristic extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'characteristics';

    protected $_accessible = [
		'icon' => true,
		'title' => true,
		'body' => true,
		'img' => true,
        'item_order' => true,
	];
}
