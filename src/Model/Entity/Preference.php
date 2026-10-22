<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Preference extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'preferences';

    protected $_accessible = [
		'icon' => true,
		'title' => true,
		'body' => true,
		'img' => true,
        'item_order' => true,
	];
}
