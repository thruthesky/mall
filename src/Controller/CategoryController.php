<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Drupal\mall\Entity\Category;

class CategoryController extends ControllerBase {
	function DefaultController(){
		$request = \Drupal::request();
		
		$method = $request->get('call');
		$parent_id = $request->get('parent_id');
		$name = $request->get('name');
		
		$re = Category::$method( $parent_id, $name );
		
		if ( isset($re['code']) && $re['code'] ) {
            if ( ! isset( $re['message'] ) ) {
                $re['message'] = s::error_message( $re['code'] );
            }
        }
        else $re['code'] = 0;


        $response = new JsonResponse( $re );
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
	}
}
