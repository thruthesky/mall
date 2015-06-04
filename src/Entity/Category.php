<?php
namespace Drupal\mall\Entity;
use Drupal\mall\CategoryInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the CategoryLog entity.
 *
 *
 * @ContentEntityType(
 *   id = "mall_category",
 *   label = @Translation("Mall Category entity"),
 *   base_table = "mall_category",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Category extends ContentEntityBase implements CategoryInterface {

  public static function add($parent_id, $name) {
	if( empty( $name ) ) {
      // @todo find a better and unified way to handle error.
		return "Name is empty!";
	}
    $category = Category::create();
    $category->set('user_id', \Drupal::currentUser()->getAccount()->id());
    $category->set('name', $name);
    $category->set('parent_id', $parent_id);
    $category->save();
    return $category->id();
  }

  public static function update($id, $name) {
    $category = Category::load($id);
    if ( $category ) {
      $category->set('name', $name)->save();
      return 0;
    }
    else {
      return -1;
    }
  }

  public static function del($id) {
    $category = Category::load($id);
    if ( $category ) {
		self::deleteChildren( $id, 0, true );
		$category->delete();
	}
  }

  public static function loadChildren($no, $depth = 0) {//$delete temporary

    /*
    $categories = \Drupal::entityManager()->getStorage('mall_category')->loadByProperties(['parent_id'=>$no]);
    if ( empty($categories) ) return [];
    foreach($categories as $category) {
      $returns = self::loadChildren($category->id());
      $categories += $returns;
    }
    return $categories;
    */	
	$categories = \Drupal::entityManager()->getStorage('mall_category')->loadByProperties(['parent_id'=>$no]);
	
	$rows = [];
	foreach( $categories as $c ){
		$id = $c->id();
	
		$rows[ $id ]['id'] = $c->id();
        $rows[ $id ]['name'] = $c->label();
        $rows[ $id ]['depth'] = $depth;
		$rows[ $id ]['user_id'] = $c->user_id->target_id;
		$rows[ $id ]['user_name'] = $c->user_id->entity->name->value;		
		$returns = self::loadChildren( $id, $depth + 1 );		
        if( $returns ) $rows = array_merge( $rows, $returns );
	}	
	return $rows;
	/*
    $rows = [];
    if( $result ){
      while ( $row = $result->fetchAssoc() ) {

  
        $rows[ $row['id'] ]['id'] = $row['id'];
        $rows[ $row['id'] ]['name'] = $row['name'];
        $rows[ $row['id'] ]['depth'] = $depth;

        //
        $returns = self::loadChildren( $row['id'], $depth + 1 );
        if( $returns ) $rows = array_merge( $rows, $returns );
      }

      if( $rows ) return $rows;
      else return [];
    }

    return $rows;
	*/
  }

  /*
  *also deletes the children of a category when deleted
  */
  public static function deleteChildren( $id ){
	$categories = \Drupal::entityManager()->getStorage('mall_category')->loadByProperties(['parent_id'=>$id]);
	foreach( $categories as $c ){
		self::deleteChildren( $c->id() );
		$c->delete();
	}
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
      ->setDescription(t('The ID of the Category entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Category entity.'))
      ->setReadOnly(TRUE);



    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Drupal User ID'))
      ->setDescription(t('The Drupal User ID who created the entity.'))
      ->setSetting('target_type', 'user');



    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));
	
	$fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('Name of Category.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 64,
      ));


    $fields['parent_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent ID'))
      ->setDescription(t('The parent category entity id of the Entity'))
      ->setSetting('target_type', 'mall_category');
	

    return $fields;
  }


}
