<?php
namespace Drupal\mall\Controller;
use Drupal\comment\Entity\Comment;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Drupal\file\Entity\File;

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
}