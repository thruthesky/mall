<?php
namespace Drupal\mall\Entity;
use Drupal\mall\MemberInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the TaskLog entity.
 *
 *
 * @ContentEntityType(
 *   id = "mall_member",
 *   label = @Translation("Mall Member entity"),
 *   base_table = "mall_member",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Member extends ContentEntityBase implements MemberInterface {



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
      ->setDescription(t('The ID of the Task entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Task entity.'))
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
      ->setDescription(t('The person who created this task.'))
      ->setSetting('target_type', 'user');


    $fields['user_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User Type'))
      ->setDescription(t('The user type'))
      ->setSettings(array(
        'default_value' => 'client',
        'max_length' => 16,
      ));

    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User First Name'))
      ->setDescription(t('The user first name'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 32,
      ));
    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User Last Name'))
      ->setDescription(t('The user last name'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 32,
      ));
    $fields['middle_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User Middle Name'))
      ->setDescription(t('The user middle name'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 32,
      ));

    return $fields;
  }


}
