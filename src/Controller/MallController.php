<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\Item;
use Drupal\mall\x;

class MallController extends ControllerBase {
	public function admin() {
		$data = [];
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
    public function firstPage() {	
        $data = [];
		
        return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
    }

}
