<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Comp extends Entity
{
    use ImageUploadTrait;

    protected $imageDirectory = 'comps';

    protected $_accessible = [
		'alias' => true,
		'title' => true, 
		'body' => true,
		'img' => true,
	];
}
