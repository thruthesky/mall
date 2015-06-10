<?php
namespace Drupal\mall\Entity;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\mall\MemberInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\mall\x;
use Drupal\user\UserInterface;


/**
 * Defines the CategoryLog entity.
 *
 *
 * @ContentEntityType(
 *   id = "mall_member",
 *   label = @Translation("Mall Member entity"),
 *   base_table = "mall_member",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "first_name",
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

        $fields['first_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('First Name'))
            ->setDescription(t('First Name of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));
        $fields['middle_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Middle Name'))
            ->setDescription(t('Middle Name of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));
        $fields['last_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Last Name'))
            ->setDescription(t('Last Name of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));


        return $fields;
    }
}
