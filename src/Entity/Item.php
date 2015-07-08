<?php
namespace Drupal\mall\Entity;
use Drupal\mall\ItemInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

use Drupal\mall\x;

/**
 * Defines the ItemLog entity.
 *
 *
 * @ContentEntityType(
 *   id = "mall_item",
 *   label = @Translation("Mall Item entity"),
 *   base_table = "mall_item",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Item extends ContentEntityBase implements ItemInterface {
  public static function update( $input ) {
		
		if( $input['item_id'] ) $item = Item::load( $input['item_id'] );

		if( empty( $item ) ) {
			$item = Item::create();
			$item->set('user_id', x::myUid() );
		}
		
		if( empty( $input['model_year'] ) ) $input['model_year'] = 0;
		if( empty( $input['model'] ) ) $input['model'] = "NA";
		if( empty( $input['brand'] ) ) $input['brand'] = "NA";
		
		$item->set('title', $input['title']);		
		$item->set('name', $input['name']);	
		$item->set('category_id', $input['category_id']);		
		$item->set('brand', $input['brand']);
		$item->set('model', $input['model']);
		$item->set('model_year', $input['model_year']);
		$item->set('price', $input['price']);
		$item->set('mobile', $input['mobile']);
		$item->set('status', $input['status']);
		$item->set('city', $input['city']);
		$item->set('province', $input['province']);
		$item->set('location', $input['location']);
		$item->set('content', $input['content']);

		$item->save();
		
		$fids = $input['fids'];
		if( $fids ){
			$exploded_fids = explode( "," ,$fids );
			foreach( $exploded_fids as $ef ){
				if ( empty($ef) ) continue;
				$type_and_fid = explode( "-", $ef );
				x::LinkFileToEntity( $item->id(), $type_and_fid[1], $type_and_fid[0] );
			}
		}
		
		return $item->id();
  }

  public static function del($id) {
    $item = Item::load($id);
    if ( $item ) $item->delete();
  }
  /*
  *$conds usage
  *'limit' for max number of rows to get, 'page' for pagination ( MUST HAVE LIMIT )
  *'order' ASC or DESC, 'by' the field to order
  *( and all field condition e.g. $conds['id'] = 10 will become ->conds( 'id'=>10 ) )
  */
  public static function getItems( $conds = array() ) { 
	$query = \Drupal::entityQuery('mall_item');
	
	if( isset($conds['limit']) ){
		if( ! empty($conds['page']) ){
			$from = $conds['limit'] * ( $conds['page'] - 1 );
			unset( $conds['page'] );
		}
		else{
			$from = 0;
		}		

		$query = $query->range($from, $conds['limit']);		
		unset( $conds['limit'] );		
	}
	
	if( isset($conds['by']) ){
		if( ! empty($conds['order']) ){
			$order = $conds['order'];
			unset( $conds['order'] );
		}
		else{
			$order = 'ASC';
		}
		
		$query = $query->sort( $conds['by'], $order );
		unset( $conds['by'] );		
	}
	
	if( isset($conds['category_id']) ){
		$ors = $query->orConditionGroup();
		$ors->condition( "category_id", $conds['category_id'] );
		$children = x::getAllCategoryChildren( $conds['category_id'] );
		
		foreach( $children as $child ){		
			$ors->condition( "category_id", $child['entity']->id() );
		}
		
		$query->condition($ors);
		unset( $conds['category_id'] );
	}
	
	if( ! empty($conds['price_from']) ){
		$query = $query->condition( 'price', $conds['price_from'], '>=' );
		unset( $conds['price_from'] );
	}
	
	if( isset( $conds['price_to']) ){
		$query = $query->condition( 'price', $conds['price_to'], '<=' );
		unset( $conds['price_to'] );
	}
	
	//per hour
	if( isset($conds['time']) ){
		$difference = time() - $conds['time'] * 60 * 60;		
		$query = $query->condition( 'created', $difference, '>=' );
		unset( $conds['time'] );
	}
	
	if( isset($conds) ){
		foreach( $conds as $k => $v ){
			$query = $query->condition( $k, $v );
		}
	}
	
	if ( $keyword = x::in('keyword') ) {
		$ors = $query->orConditionGroup();
		$ors->condition( 'title', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'brand', '%'.$keyword.'%', 'LIKE' );	
		/*$ors->condition( 'name', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'content', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'model', '%'.$keyword.'%', 'LIKE' );
		*/		
		$query->condition($ors);
	}
	$ids = $query->execute();
      if ( $ids ) {
          return Item::loadMultiple($ids);
      }
      else return [];


  }


  /*
  *
  *
  */
  public static function getFilesByType( $item_id, $type='' ) {
	$files = [];

      $db = db_select('file_usage', 's')
          ->fields('s',['fid','type'])
          ->condition('module','mall')
          ->condition('id',$item_id);

      if ( $type ) {
          $db->condition('type', $type);
      }

	$result = $db->execute();
	
	while( $row = $result->fetchAssoc() ) {				
		/*
		*Note that the $row['type'] should ONLY have numbers at the END of the file for this to work...
		*There there are numbers in between, this will create a logic error.
		*/
		$no = preg_replace("/[^0-9]/", "", $row['type']);		
		if( is_numeric( $no ) ){
			$usage_type = substr( $row['type'], 0, strpos( $row['type'], $no ) );			
		}
		else{
			$usage_type = $row['type'];
		}		
		/*
		*sample returning value for item_image2 is $files[ item_image ][ 2 ] = $file_entity;
		*sample returning value for item_image_thumbnail ( meaning $no is empty ) is $files[ item_image_thumbnail ][ 0 ] = $file_entity; -> automatically sets an iterating index for code unification
		*/
		if( $no ) $files[ $usage_type ][ $no ] = \Drupal::entityManager()->getStorage('file')->load( $row['fid'] );
		else $files[ $usage_type ][] = \Drupal::entityManager()->getStorage('file')->load( $row['fid'] );
	}
	return $files;
  }
  
  public static function getFileUrl( $file_entity ) {
	$file_url = [];
	$file_url['fid'] = $file_entity->id();
	$file_url['url_original'] = $file_entity->url();
	$path = $file_entity->getFileUri();
	$file_url['url_thumbnail'] = entity_load('image_style', 'thumbnail')->buildUrl($path);
	$file_url['url_medium'] = entity_load('image_style', 'medium')->buildUrl($path);
	$file_url['url_large'] = entity_load('image_style', 'large')->buildUrl($path);
	return $file_url;
  }
  

  public static function getItemsWithImages( $conds = array()) {    
	$data = [];
	$entity_items = self::getItems( $conds );	
	$images = [];
	$items = [];
	foreach( $entity_items as $k => $v ){
		$items[ $k ]['entity'] = $v;
		$items[ $k ]['rendered_price'] = self::renderPrice( $v->price->value );
		$items[ $k ]['category_root'] = x::getCategoryRoot( $v->category_id->target_id );
		$files = Item::getFilesByType( $v->id() );			
		foreach( $files as $key => $value ){
			foreach( $value as $v ){				
				$items[ $k ]['images'][ $key ][] = self::getFileUrl( $v );
			}
		}		
	}
	$data['items'] = $items;
	$data['items'] = array_values( $items );
	//$data['item_images'] = $images;
	return $data;
  }
  
  public static function renderPrice( $price ){
	//"₱ ".
	return "₱ ".number_format($price);	
  }


  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }


  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Item entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Item entity.'))
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The person who created this Item.'))
      ->setSetting('target_type', 'user');
	 
	 $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('title'))
      ->setDescription(t('Is the title of the item. This will be displayed as the title of item in list page or in view page.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 256,
      ));
	  
	 $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the item. It may confused with model and title.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 256,
      ));
	 
    $fields['price'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Price'))
      ->setDescription(t('Price of Item.'));
	  
    $fields['mobile'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Mobile'))
            ->setDescription(t('Mobile number of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 256,
            ));
			
	  
	  
	$fields['category_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Category ID'))
      ->setDescription(t('Category ID'))
      ->setSetting('target_type', 'library_category');

    $fields['status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Status'))
      ->setDescription(t('the Status of the item. New, Used, Defective, etc....'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 1,
      ));

	$fields['content'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Content'))
      ->setDescription(t('Content of Item.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 8192,
    ));
	  
    $fields['brand'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Brand'))
      ->setDescription(t('Brand of Item.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 64,
      )); 
	  
    $fields['model'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Model'))
      ->setDescription(t('Model of Item.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 64,
      ));
	  
    $fields['model_year'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Model Year'))
      ->setDescription(t('Model Year of Item.'))
      ->setSettings(array(
        'default_value' => 0,
        'max_length' => 4,
      ));
	  
    $fields['province'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Province'))
      ->setDescription(t('Province of seller.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 64,
      ));
	  
    $fields['city'] = BaseFieldDefinition::create('string')
      ->setLabel(t('City'))
      ->setDescription(t('City of seller.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 64,
      ));
	  
    $fields['location'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Location'))
      ->setDescription(t('Location of seller.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 512,
      ));

    return $fields;
  }
}
