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
    self::emptyEntity('mall_member');
    //self::emptyEntity('mall_order');
    //self::emptyEntity('mall_order_item');
    self::emptyEntity('mall_item');
	self::emptyEntity('library_category');	
  }

  public static function Login($username) {
    $user = user_load_by_name($username);
    user_login_finalize( $user );
  }
  
	public function categoryList( &$variables ) {		
		return self::get_category_children( 0 );		
	}


	public static function get_category_children( $parent_id, $depth = 0 ){
		
		$result = db_select('mall_category', 'mc')
		->fields('mc')
		->orderBy('name', 'ASC')
		->condition( 'parent_id', $parent_id )
		->execute();
		
		if( $result ){
			$rows = [];
			while ( $row = $result->fetchAssoc() ) {				
				$rows[ $row['id'] ]['id'] = $row['id'];
				$rows[ $row['id'] ]['name'] = $row['name'];
				$rows[ $row['id'] ]['depth'] = $depth;
				
				$in_depth = self::get_category_children( $row['id'], $depth + 1 );
				if( !empty( $in_depth ) ){
					$rows2 = $in_depth;
					$rows = array_merge( $rows, $rows2 );
				}
			}
						
			if( $rows ) return $rows;
			else return null;
		}
	}
	
	public static function isFrontPage()
    {
        return \Drupal::service('path.matcher')->isFrontPage();        
    }
}