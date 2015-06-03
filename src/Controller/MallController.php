<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;

class MallController extends ControllerBase {
	public function admin() {
		$data = [];
		
		return [
			'#theme' => 'mall',
			'#data' => $data,
		];
	}
}
