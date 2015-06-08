<?php
namespace Drupal\mall;
use Drupal\mall\HTML;

class Member {
  const TABLE = 'mall_member';

  /**
   * @param array $info
   * @code
   *
  Member::add([
  'uid' => 1,                 // drupal id
  'first_name' => 'JaeHo',    // first name
  'last_name' => 'Song',
  'middle_name' => 'Does not look working',
  'email' => 'admin@drupal.com',
  'mobile' => '0917-467-8603',
  ]);
   * @endcode
   *
   * @param - $info must have $info['uid'] attribute.
   * @note This method should be used for the first insert(member add), it will save 'uid' and the update will not touch 'uid' again.
   */
  public static function add(array $info)
  {
    $uid = $info['uid'];
    self::update_member_attribute($uid, $info);
  }

  /**
   *
   *
   *
   * @code
   * Member::update(['uid'=>2, 'email'=>'2 new email']);
   * @endcode
   */
  public static function update($info)
  {
    $uid = $info['uid'];
    unset($info['uid']);
    self::update_member_attribute($uid, $info);
  }

  private static function update_member_attribute($uid, $info) {
    $insert_uid = self::get($uid, 'uid');

    foreach( $info as $code => $value ) {
	  /*
	  *I need to do this query because of the table structure
	  */
      $res = db_select(self::TABLE, 't')
		  ->fields('t', [ 'value' ])
		  ->condition('uid', $uid)
		  ->condition('code', $code)
		  ->execute();
	  $row = $res->fetchAssoc(\PDO::FETCH_ASSOC);	
	  
      if ( $row ) {
        // update		
        db_update(self::TABLE)
          ->fields(['value' => $value])
          ->condition('uid', $uid)
          ->condition('code', $code)
          ->execute();
      }
      else {		
        // insert
        db_insert(self::TABLE)
          ->fields(['uid'=>$uid, 'code'=>$code, 'value'=>$value])
          ->execute();
      }
    }
  }

  /**
   *
   * Returns the value of the field 'value' on the input condition.
   *
   * @param $uid - drupal user id
   * @param $code - code of the member attribute
   * @return mixed
   *    - null if there is no record
   *    - else the value of the member attribute. it can be empty, null, mixed.
   *
   * @code
   *  $uid = self::get($uid, 'uid');
   * @endcode
   */
  public static function get($uid, $code) {
    $res = db_select(self::TABLE, 't')
      ->fields('t', ['value'])
      ->condition('uid', $uid)
      ->condition('code', $code)
      ->execute();
    $row = $res->fetchAssoc(\PDO::FETCH_ASSOC);
    if ( $row ) return $row['value'];
    else return null;
  }

  public static function set($uid, $code, $value) {
    self::update_member_attribute($uid, [$code=>$value]);
  }

  /**
   *
   * Returns member information in associative array.
   *
   * @param $uid
   * @return array
   *
   */
  public static function gets($uid) {
      $res = db_select(self::TABLE, 't')
        ->fields('t')
        ->condition('uid', $uid)
        ->execute();
    $info = [];
    while( $row = $res->fetchAssoc(\PDO::FETCH_ASSOC) ) {
      $info[$row['code']] = $row['value'];
    }
    return $info;
  }
}