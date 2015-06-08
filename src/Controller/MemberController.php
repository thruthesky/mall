<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\x;
use Drupal\mall\Member;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MemberController extends ControllerBase {
	public static function register() {
		$data = [];
		
		//just checks if the user is logged in or not
		if( $uid = x::myUid() ) {
          x::getDefaultInformation($data);
        }
		else {
          //$data['error'] = x::error( x::ERROR_PLEASE_LOGIN_FIRST );
        }
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
	
	public static function registerSubmit() {
	
		//just checks if the user is logged in or not
		if( x::myUid() ) {
			$register_data = [];
			$register_data['uid'] = x::myUid();
			if( x::in('first_name') ) $register_data['first_name'] = x::in('first_name');
			if( x::in('last_name') ) $register_data['last_name'] = x::in('last_name');
			if( x::in('middle_name') ) $register_data['middle_name'] = x::in('middle_name');
			if( x::in('mail') ) $register_data['mail'] = x::in('mail');
			if( x::in('mobile') ) $register_data['mobile'] = x::in('mobile');			
			if( x::in('gender') ) $register_data['gender'] = x::in('gender');
			if( x::in('birth_month') ) $register_data['birth_month'] = x::in('birth_month');
			if( x::in('birth_day') ) $register_data['birth_day'] = x::in('birth_day');
			if( x::in('birth_year') ) $register_data['birth_year'] = x::in('birth_year');
			if( x::in('location') ) $register_data['location'] = x::in('location');

			Member::update($register_data);
			
			return new RedirectResponse( "/mall/member/register/" );
		}
		else{
			return new RedirectResponse( "/mall/member/register/" );
		}
	}
	
	public static function add(){
		 //x::in('parent_id');
	}
}
