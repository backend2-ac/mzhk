<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Service extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'services';

    protected $_accessible = [
		'name' => true,
		'subtitle' => true,
		'tags' => true,
		'unit' => true,
		'motif' => true,
		'topic' => true,
	'title' => true,
	'body' => true,
	'img' => true,
	'images' => true,
	'color' => true,
	'number' => true,
        'item_order' => true,
	];
}
