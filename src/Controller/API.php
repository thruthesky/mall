<?php
namespace Drupal\mall\Controller;
use Drupal\comment\Entity\Comment;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Drupal\file\Entity\File;

use Drupal\mall\Entity\Category;

class API extends ControllerBase {
	public function DefaultController(){
		$request = \Drupal::request();

		$method = $request->get('call');
		$re = $this->$method($request);

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
	/*
	public static function test(){
		$data = [];
		$data['code'] = 0;
		$data['test'] = "awaw";
		return $data;
	}
	*/
	
	public static function getParentChildren( $request ){
		$pid = $request->get('pid');
		
		if( !$pid ) return null;
		
		$depth = $request->get('depth') + 1;
		
		$markup = '';
		
		$children = Category::loadChildren( $pid );
			if( $children ){
			$markup =	"<select class='category' name='' depth='$depth'>";
			$markup .= 	"<option value=''>Category</option>";
			foreach( $children as $child ){
				$markup .= "<option value='".$child->id()."'>".$child->name->value."</option>";
			}
			$markup .=	"</select>";		
		}
		
		$data['html'] = $markup;
		
		return $data;
	}
}