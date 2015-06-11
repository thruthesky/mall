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
		$item = Item::load( $input['item_id'] );

		if( empty( $item ) ) {
			$item = Item::create();
			$item->set('user_id', x::myUid() );
		}

		$item->set('title', $input['title']);		
		$item->set('name', $input['name']);	
		$item->set('category_id', $input['category_id']);		
		$item->set('brand', $input['brand']);
		$item->set('model', $input['model']);
		$item->set('model_year', $input['model_year']);		
		$item->set('price', $input['price']);		
		$item->set('mobile', $input['mobile']);			
		$item->set('status', $input['status']);		
		$item->set('content', $input['content']);

		$item->save();
		
		return $item->id();
  }

  public static function del($id) {
    $item = Item::load($id);
    if ( $item ) $item->delete();
  }
  
  public static function getItemList() {
	$query = \Drupal::entityQuery('mall_item');
	if ( $keyword = x::in('keyword') ) {
		$ors = $query->orConditionGroup();
		$ors->condition( 'title', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'name', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'content', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'brand', '%'.$keyword.'%', 'LIKE' );
		$ors->condition( 'model', '%'.$keyword.'%', 'LIKE' );		
		$query->condition($ors);
	}
	$ids = $query->execute();
	return Item::loadMultiple($ids);
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
                'max_length' => 64,
            ));
			
	  
	  
	$fields['category_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Category ID'))
      ->setDescription(t('Category ID'))
      ->setSetting('target_type', 'mall_category');

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

    return $fields;
  }
}
