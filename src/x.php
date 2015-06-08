<?php
namespace Drupal\mall;
use Drupal\mall\HTML;
use Drupal\user\Entity\User;

/**
 * Class X
 * @package Drupal\mall
 * @short Helper library class for mall module.
 * @short Difference from Mall.php is that Mall.php is a library that is only used for mall module. x.php holds more generic functions.
 */

class x {

  const ERROR_CATEGORY_EXIST = 'ERROR_CATEGORY_EXIST';
  const ERROR_BLANK_CATEGORY_NAME = 'ERROR_BLANK_CATEGORY_NAME';
  
  const ERROR_PLEASE_LOGIN_FIRST = 'ERROR_PLEASE_LOGIN_FIRST';


  static $input = [];

  public static function getThemeName() {
    $uri = \Drupal::request()->getRequestUri();
    if ( $uri == '/mall' or $uri == '/mall/' ) return 'mall.mall'; // this is the entry key of routing.yml
    //$uri = substr($uri, 1);
    list($uri, $trash) = explode('?', $uri, 2);
    $uri = trim($uri, '/ ');
    $uri = str_replace('/', '.', $uri);
    $uri = strtolower($uri);
    return $uri;
  }

  public static function getThemeFileName() {
    return self::getThemeName() . '.html.twig';
  }

  public static function isFromSubmit() {
    return \Drupal::request()->get('mode') == 'submit';
  }



  /**
   * @param $username
   * @return int
   *
   *
   * @code if ( ! x::getUserID(x::in('owner')) ) return x::errorInfoArray(x::error_wrong_owner, $data);
   */
  public static function getUserID($username) {
    if ( $username ) {
      $user = user_load_by_name($username);
      if ( $user ) {
        return $user->id();
      }
    }
    return 0;
  }


  /**
   *
   * It simply returns Username
   *
   * @param $id
   * @return array|mixed|null|string - username
   * @code $task->worker = x::getUsernameByID( $task->get('worker_id')->value );
   */
  public static function getUsernameByID($id) {
    if ( $id ) {
      $user = User::load($id);
      if ( $user ) {
        return $user->getUsername();
      }
    }
    return '';
  }


  public static function myUid() {
    return \Drupal::currentUser()->getAccount()->id();
  }

  public static function login() {
    return self::myUid();
  }
  public static function admin()
  {
    return x::myUid() == 1;
  }



  /**
   * Returns TRUE if the user is accessing mall module.
   *
   * @return bool
   *
   */
  public static function isMallPage() {
    $request = \Drupal::request();
    $uri = $request->getRequestUri();
    if ( strpos( $uri, '/mall') !== FALSE ) {
      return TRUE;
    }
    else return FALSE;
  }




  /**
   * Returns TRUE if the user is accessing office task page.
   *
   * @return bool
   *
   */
  public static function isMallAdminCategoryPage() {
    $request = \Drupal::request();
    $uri = $request->getRequestUri();
    if ( strpos( $uri, '/mall/admin/category') !== FALSE ) {
      return TRUE;
    }
    else return FALSE;
  }





  public static function input() {
    return self::getInput();
  }

  /**
   * This is a wrapper of "\Drupal::request()->get($name, $default);" except that the default value is  zero(0) instead of null.
   * @param $name
   * @param int $default
   * @return mixed
   * @code
   *    $parent_id  = x::in('parent_id');
   *    $parent_id  = x::in('parent_id', null);
   *    $parent_id  = x::in('parent_id', '');
   * @code
   */
  public static function in($name, $default=0) {
    return \Drupal::request()->get($name, $default);
  }


  /**
   *
   * 입력 값을 임의로 지정한다.
   *
   * x::getInput() 과 x::in() 함수는 입력 값을 리턴한다.
   *
   * 하지만 이 함수를 통해서 입력 값을 임의로 지정하여 해당 함수들이 임의로 지정한 값을 사용 하게 할 수 있다.
   *
   * 예를 들면, 쿠키에 마지막 검색(폼 전송) 값을 저장해 놓고 다음에 접속 할 때 마지막에 지정한 검색 옵션을 그대로 적용하는 것이다.
   *
   *
   * @param $array
   */
  public static function setInput($array) {
    self::$input = $array;
  }

  /**
   * self::$input 의 값을 리턴한다.
   *
   * @note 주의 할 점은 이 값은 꼭 HTTP 입력 값이 아닐 수 있다.
   *
   *      기본 적으로 HTTP 입력 값을 리턴하지만,
   *
   *      프로그램 적으로 임의로 이 값을 다르게 지정 할 수도 있다.
   *
   *      이 함수는 x::in() 에 영향을 미친다.
   *
   * @return array
   */
  public static function getInput() {

    if ( empty(self::$input) ) {
      $request = \Drupal::request();
      $get = $request->query->all();
      $post = $request->request->all();
      self::$input = array_merge( $get, $post );
    }
    return self::$input;
  }


  /**
   *
   * @Note returns an entity ID by User ID.
   *
   * Entity can be any type as long as it has user_id field.
   *
   * @param $type
   * @param $uid
   * @return mixed|null
   */
  public static function loadEntityByUserID($type,$uid) {
    $entities = \Drupal::entityManager()->getStorage($type)->loadByProperties(['user_id'=>$uid]);
    if ( $entities ) $entity = reset($entities);
    else $entity = NULL;
    return $entity;
  }


  public static function log ( $str )
  {
    $path_log = "./x8.log";
    $fp_log = fopen($path_log, 'a+');

    if ( ! is_string($str) ) {
      $str = print_r( $str, true );
    }
    self::$count_log ++;
    fwrite($fp_log, self::$count_log . " : $str\n");

    fclose( $fp_log );
  }












  /**
   * @param $k
   * @param $v
   * @refer the definition of user_cookie_save() and you will know.
   */
  public static function set_cookie($k, $v) {
    user_cookie_save([$k=>$v]);
  }
  /**
   * @param $k - is the key of the cookie.
   * @return mixed
   */
  public static function get_cookie($k) {
    return \Drupal::request()->cookies->get("Drupal_visitor_$k");
  }
  /**
   * @param $k
   */
  public static function delete_cookie($k) {
    user_cookie_delete($k);
  }



  public static function error($code, $kvs=[]) {
    $message = self::errorMessage($code);
    foreach( $kvs as $k => $v ) {
      $message = str_replace('#'.$k, $v, $message);
    }
    return [$code, $message];
  }

  private static function errorMessage($code) {
    switch( $code ) {
      case self::ERROR_CATEGORY_EXIST : $msg = "The category '#name' is already exists under '#parent'."; break;
      case self::ERROR_BLANK_CATEGORY_NAME : $msg = "Category name cannot be blank!."; break;
      case self::ERROR_PLEASE_LOGIN_FIRST : $msg = "Please login first!."; break;
      default: $msg = 'Unknown'; break;
    }
    return $msg;
  }

  /**
   * Returns true if the input object indicates Error.
   *
   * @note if $re is minus or $re[0] is minus, then it considers as error.
   *
   * @param $re
   * @return bool
   */
  public static function isError($re) {
    if ( is_numeric($re) && $re < 0 ) return true;
    else if ( is_array($re) && strpos($re[0], 'ERROR') !== false ) return true;
    return false;
  }
  
  
  
  
  
  
  
  /*------------*/
  
  public static function my( $field )
    {
        if ( $field == 'uid' ) return \Drupal::currentUser()->getAccount()->id();
        else if ( $field == 'uid' ) return \Drupal::currentUser()->getAccount()->getUsername();
        else if ( $field == 'name' ) return \Drupal::currentUser()->getAccount()->getUsername();
        else if ( $field == 'mail' ) return \Drupal::currentUser()->getAccount()->getEmail();
        else {
	        $user = User::load( \Drupal::currentUser()->getAccount()->id() );
	        return x::getExtraField($field, $user);
        }
    }

	
	public static function getExtraField($field, User &$user)
    {

        global $_getExtraField;

	    if ( ! isset($_getExtraField) ) $_getExtraField = [];


	    $field = "field_$field";
	    $uid = $user->id();

	    /**
	     *
	     */
	    if ( isset( $_getExtraField[$uid][ $field ] ) ) {
		    return $_getExtraField[$uid][ $field ];
	    }

	    if ( $user->hasField($field) ) {
		    $value = $user->get($field)->value;
		    if ( ! $value ) $value = '';
	    }
	    else $value = '';




	    $_getExtraField[$uid][ $field ] = $value;
	    return $_getExtraField[$uid][ $field ];

	    /*


        if ( $user === null ) {
            $user = User::load(self::myUid());
        }

        $uid = $user->id();


        if ( isset( $_getExtraField[$uid][ $field ] ) ) return $_getExtraField[$uid][ $field ];

        $value = null;
        $record = $user->get('field_'.$field);
        if ( $record ) {			
            $record_value = $record->getValue();
            if ($record_value && isset($record_value[0]) && isset($record_value[0]['value'])) {
				$value = $record_value[0]['value'];				
            }
        }
	    */
    }

  public static function getDefaultInformation(array &$data) {
    $uid = x::myUid();
    $data['member'] = Member::gets($uid);
    //$data['user'] = \Drupal::currentUser()->getAccount();
    $data['user'] = User::load($uid);
  }
}