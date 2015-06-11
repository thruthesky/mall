<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\x;

class ItemController extends ControllerBase {
    public function edit() {
        $data = [];
        return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
    }

}
