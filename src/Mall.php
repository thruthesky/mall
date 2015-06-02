<?php
namespace Drupal\mall;
/**
 *
 * Class Mall
 * @package Drupal\mall
 *
 * @description This is the default library class for mall module.
 */
class Mall {



  public static function emptyEntity($entity_type)
  {
    $entities = \Drupal::entityManager()->getStorage($entity_type)->loadMultiple();
    if ( $entities ) {
      foreach ( $entities as $entitiy ) {
        $entitiy->delete();
      }
    }
  }

  public static function emptyData() {
    self::emptyEntity('mall_category');
    self::emptyEntity('mall_member');
    self::emptyEntity('mall_order');
    self::emptyEntity('mall_order_item');
    self::emptyEntity('mall_item');
  }

  public static function Login($username) {
    $user = user_load_by_name($username);
    user_login_finalize( $user );
  }
}