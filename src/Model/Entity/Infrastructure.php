<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Infrastructure extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'infrastructures';

    protected $_accessible = [
		'icon' => true,
		'title' => true,
		'body' => true,
		'img' => true,
        'item_order' => true,
	];
}
