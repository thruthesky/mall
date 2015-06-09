<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\x;
use Drupal\mall\Member;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MemberController extends ControllerBase {
	public static function register() {
		$data = [];
		
		if( $uid = x::in('uid') ){

		if( !x::isAdmin() ){
			if( $isMyAccountUrl = x::isMyAccount() ){
				return new RedirectResponse( "/mall/member/register/".$isMyAccountUrl );
			}
		}	
			
			$data = x::getInformationByUid( $uid );
			
		}
		else{	
			//just checks if the user is logged in or not
			if( $uid = x::myUid() ) {
			  x::getDefaultInformation($data);
			}
			else {
			  //$data['error'] = x::error( x::ERROR_PLEASE_LOGIN_FIRST );
			}
		}
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
	
	public static function registerSubmit() {

	  //just checks if the user is logged in or not
	  
		if( !x::isAdmin() ){
			if( $isMyAccountUrl = x::isMyAccount() ){
				return new RedirectResponse( "/mall/member/register/".$isMyAccountUrl );
			}
		}
	  
      $re = x::registerUser(x::in('username'), x::in('password'), x::in('email'));	
  	  
      if ( $re && !x::myUid() ) {		
        $error = x::error(x::ERROR_USER_EXISTS,[ 'name'=> x::in('username') ]);
		return new RedirectResponse( "/mall/member/register?error=".$error[0]."&message=".$error[1] );
      }
	  
	  if( !x::myUid() ){ x::loginUser(x::in('username')); }


			$register_data = [];
			if( x::in('uid') ) $register_data['uid'] = x::in('uid');
			else $register_data['uid'] = x::myUid();
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
			
			if( $register_data['uid'] != x::myUid() ) $redirect_url = "?uid=".x::in('uid');
			else $redirect_url = "";
			
			return new RedirectResponse( "/mall/member/register".$redirect_url );

	}
	
	public static function add(){
		 //x::in('parent_id');
	}

  public function collection()
  {
	$data['members'] = Member::getMemberList();
	$data['total'] = count( $data['members'] );
	if( x::in( 'keyword' ) ){
		$data['keyword'] = x::in( 'keyword' );		
	}
	
	return [
		'#theme' => x::getThemeName(),
		'#data' => $data,
	];
  }
  
   public static function del() {   
	$uid = x::in('uid');
    Member::del( $uid );
    return new RedirectResponse( '/mall/admin/member/list' );
  }
}
