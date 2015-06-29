<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\x;
use Drupal\mall\Entity\Member;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\library\Library;
use Drupal\library\Language;

class MemberController extends ControllerBase {

  /**
   *
   * It does 'register' and 'edit' by the member and admin.
   *
   * It shows the register & edit form.
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *
   */
	public static function register() {	
		$data = [];
		if( $uid = self::isEdit() ) {            
            if( x::isAdmin() || $uid == x::myUid() ) {
                x::getDefaultInformationByUid( $uid, $data );
            }
            else {
				$data['error'] = Library::error('User ID is not yours.', Language::string('library', 'not_your_account'));
                //return new RedirectResponse( "/mall/member/register/?" . x::error(x::ERROR_NOT_YOUR_ID));
				//di( $data['error'] );
            }
		}
        else{			
			x::getDefaultInformation($data);
		}
		
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
	
	public static function registerSubmit() {		
        if ( self::isEditSubmit() ) { // Edit Submit.		
            $uid = x::in('user_id');
            if ( x::isAdmin() || $uid == x::myUid() ) {
                member::updateMemberFormSubmit($uid);
                return new RedirectResponse( "/mall/member/register?user_id=$uid&mode=edit_success" );
            }
            else return new RedirectResponse( "/mall/member/register/?" . x::error(x::ERROR_NOT_YOUR_ID));
        }
        else { // Register Submit. this is register submit for a new member.			
            $re = x::registerDrupalUser(x::in('username'), x::in('password'), x::in('mail'));
			
            if ( $re == x::ERROR_USER_EXISTS ) {
                return new RedirectResponse( "/mall/member/register?" .  x::error(x::ERROR_USER_EXISTS,[ 'name'=> x::in('username') ]) );
            }
            else {
                x::loginUser(x::in('username'));
                member::updateMemberFormSubmit($re);
                return new RedirectResponse( "/mall/member/register?mode=register_success");
            }
        }
	}
	
	public static function add(){
		 //x::in('parent_id');
	}

    private static function isEdit() {
        if ( $uid = x::in('user_id') ) return $uid;
        else return x::myUid();
    }

    public static function isEditSubmit() {
        return x::in('user_id');
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
	$uid = x::in('user_id');
	
    if( x::isAdmin() || $uid == x::myUid() ) {
		$uid = x::in('user_id');
		Member::del( $uid );
		return new RedirectResponse( '/mall/admin/member/list' );
	}
	else {
		return new RedirectResponse( "/mall/admin/member/list/?" . x::error(x::ERROR_NOT_YOUR_ID));
	}
	/*
	$uid = x::in('uid');
    Member::del( $uid );
    return new RedirectResponse( '/mall/admin/member/list' );
	*/
  }
}
