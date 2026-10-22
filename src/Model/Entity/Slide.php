<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Slide extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'slides';

    protected $_accessible = [
		'title' => true,
		'body' => true,
		'img' => true,
        'images' => true,
        'item_order' => true,
	];
}
