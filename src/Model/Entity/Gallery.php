<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Gallery extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'galleries';

    protected $_accessible = [
		'title' => true,
		'body' => true,
        'link' => true,
		'img' => true,
        'item_order' => true,
	];
}
