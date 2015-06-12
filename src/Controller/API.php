<?php
namespace Drupal\mall\Controller;
use Drupal\comment\Entity\Comment;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Drupal\mall\x;
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
	
	
	
	/*fileUpload*/
	public static function fileUpload( $request ){
		//if( x::my('uid') == 0 ) return ['code'=>x::ERROR_PLEASE_LOGIN_FIRST,'message'=>x::error(x::ERROR_PLEASE_LOGIN_FIRST)];
        $repo = 'public://mall/';
        file_prepare_directory($repo, FILE_CREATE_DIRECTORY);

		$image_style = $request->get('image_style');

		$files = [];
		for ( $j = 0; $j < count($_FILES['files']['name']); $j ++ ) {
			$file = array();
			$file['name'] = $_FILES['files']['name'][$j];
			$file['type'] = $_FILES['files']['type'][$j];
			$file['tmp_name'] = $_FILES['files']['tmp_name'][$j];
			$file['error'] = $_FILES['files']['error'][$j];
			$file['size'] = $_FILES['files']['size'][$j];
			$files[] = $file;
		}
		$infos = [];
		if ( $files ) {
			foreach( $files as $f ) {
				$info = [];
				$info['name'] = $f['name'];
				$info['type'] = $f['type'];
				if ( $f['error'] ) {
					$info['error'] = $f['error'];
				}
				else {
					$file = file_save_data(file_get_contents($f['tmp_name']), $repo . $f['name']);
					if ( $image_style ) {
						$info['url'] = entity_load('image_style', $image_style)->buildUrl($file->getFileUri());
					}
					else $info['url'] = $file->url();
					$info['fid'] = $file->id();
				}
				$infos[] = $info;
			}
		}
		$re = [];
		$re['files'] = $infos;
		return $re;
	}
	
	public static function deleteFile( $request ){
		$fid = $request->get('fid');
		$file = \Drupal::entityManager()->getStorage('file')->load($fid);
		$file->delete();
		
		$re['fid'] = $fid;
		
		return $re;
	}
	/*eo fileUpload*/
}