<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\x;

class ItemController extends ControllerBase {
	public function add() {	
		$data = [];
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}

}
