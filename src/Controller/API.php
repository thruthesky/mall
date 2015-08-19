<?php
namespace Drupal\mall\Controller;
use Drupal\comment\Entity\Comment;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Drupal\mall\x;
use Drupal\file\Entity\File;
use Drupal\library\Entity\Category;

class API extends ControllerBase {
	public function DefaultController(){
		$request = \Drupal::request();

		$method = $request->get('call');
		$re = $this->$method($request);

		if ( isset($re['code']) && $re['code'] ) {
			/*if ( ! isset( $re['message'] ) ) {
				$re['message'] = s::error_message( $re['code'] );
			}*/
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
	
	public static function getCity( $request ){
		$province = $request->get('province');
		$cities = x::getCitiesOf( $province );
		$markup = '';

		if( $cities ){
			$markup =	"<select class='location' name='city'>";
			$markup .= 	"<option value=''>City</option>";
			foreach( $cities as $k => $v ){
				$markup .= "<option value='".$k."'>".$v."</option>";
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

		$files = [];
		$type = '';
		foreach( $_FILES as $k => $v ){
			$file_usage_type = $k;
			if( strpos( $v['type'], "image/" ) !== false ){
				$f = array();
				$f['name'] = $v['name'];
				$f['type'] = $v['type'];
				$f['tmp_name'] = $v['tmp_name'];
				$f['error'] = $v['error'];
				$f['size'] = $v['size'];
			}			
			else{
				return ['code'=>'-10001','error'=>'Only images less than 16MB are supported'];
			}			
		}

		$info = [];
		if ( $f ) {
			$info = [];
			$info['name'] = $f['name'];
			$info['type'] = $f['type'];
			if ( $f['error'] ) {
				$info['error'] = $f['error'];
			}
			else {
                //@todo escape korean character. Korean file name creates error.
				$file = file_save_data(file_get_contents($f['tmp_name']), $repo . $f['name']);
                //debug_log("file: $file");

				//di( $f['name'] );
				if ( $image_style = $request->get('image_style') ) {
					$info['url'] = entity_load('image_style', $image_style)->buildUrl($file->getFileUri());
				}
				else $info['url'] = $file->url();
				$info['fid'] = $file->id();
			}
			$info['file_usage_type'] = $file_usage_type;
			if( $no = $request->get('no') ) $info['no'] = $no;
		}
		$re = [];
		$re['files'] = $info;
		
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