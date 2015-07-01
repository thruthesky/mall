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
				if( empty( $data['member'] ) ) $data['error'] = Library::error('Not a mall member.', Language::string('library', 'no_member_account'));
            }
            else {
				$data['error'] = Library::error('User ID is not yours.', Language::string('library', 'not_your_account'));
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
		$theme = 'mall.member.register';
        if ( self::isEditSubmit() ) { // Edit Submit.		
            $uid = x::in('user_id');
            if ( x::isAdmin() || $uid == x::myUid() ) {
                member::updateMemberFormSubmit($uid);					
				$data['notice'] = Library::notice('Successful Account Edit.', Language::string('library', 'succesful_account_edit'));
				x::getDefaultInformationByUid( $uid, $data );				                
            }
            else{
				$data['error'] = Library::error('User Account Not Yours.', Language::string('library', 'user_account_not_yours'));				
			}
        }
        else { // Register Submit. this is register submit for a new member.			
            $re = x::registerDrupalUser(x::in('username'), x::in('password'), x::in('mail'));
			
            if ( $re == x::ERROR_USER_EXISTS ) {                
				$data['error'] = Library::error('User ID exists.', Language::string('library', 'user_name_already_taken', ['user_name'=>x::in('username')]));
            }
            else {
                x::loginUser(x::in('username'));
                member::updateMemberFormSubmit($re);				
				$data['notice'] = Library::notice('Successful Registration.', Language::string('library', 'succesful_registration'));
				x::getDefaultInformationByUid( $re, $data );
            }
        }
		
		return [
			'#theme' => $theme,
			'#data' => $data,
		];
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
