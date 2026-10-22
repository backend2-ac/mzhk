<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Advan extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'advans';

    protected $_accessible = [
		'icon' => true,
		'title' => true,
		'body' => true,
		'img' => true,
        'item_order' => true,
	];
}
