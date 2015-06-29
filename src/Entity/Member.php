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
	const TABLE = 'mall_member';

    public static function loadByUid($uid) {
        $entities = \Drupal::entityManager()->getStorage('mall_member')->loadByProperties(['user_id'=>$uid]);
        return $entities ? reset($entities) : null;
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
			
        $fields['mail'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Email Address'))
            ->setDescription(t('Email Address of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 128,
            ));
			
        $fields['field_mobile'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Mobile'))
            ->setDescription(t('Mobile number of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));
			
        $fields['field_phone'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Phone'))
            ->setDescription(t('Phone number of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));
			
        $fields['field_gender'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Gender'))
            ->setDescription(t('Gender number of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 1,
            ));
			
        $fields['field_first_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('First Name'))
            ->setDescription(t('First Name of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));
			
        $fields['field_middle_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Middle Name'))
            ->setDescription(t('Middle Name of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));
			
        $fields['field_last_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Last Name'))
            ->setDescription(t('Last Name of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 64,
            ));	
			
        $fields['field_birth_day'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Birth Day'))
            ->setDescription(t('Birth Day of the Entity.'))
            ->setSettings(array(
                'default_value' => 0,
				'unsigned', TRUE,
            ));
			
        $fields['field_birth_month'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Birth Month'))
            ->setDescription(t('Birth Month of the Entity.'))
            ->setSettings(array(
                'default_value' => 0,
				'unsigned', TRUE,
            ));
			
        $fields['field_birth_year'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Birth Year'))
            ->setDescription(t('Birth Year of the Entity.'))
            ->setSettings(array(
                'default_value' => 0,
				'unsigned', TRUE,
            ));
			
        $fields['field_location'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Location'))
            ->setDescription(t('Location of the Entity.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 512,
            ));


        return $fields;
    }
	
	/*member functions*/
	 public static function updateMemberFormSubmit($uid) {
		$input = x::input();
		
		$member = self::loadByUid( $uid );

		if( empty( $member ) ) {
			$member = Member::create();
			$member->set( 'user_id', $uid );			
		}
		else{			
		}
		
		$member->set('field_first_name', $input['field_first_name']);
		$member->set('field_last_name', $input['field_last_name']);
		$member->set('field_middle_name', $input['field_middle_name']);
		$member->set('mail', $input['mail']);
		$member->set('field_mobile', $input['field_mobile']);
		$member->set('field_phone', $input['field_phone']);
		$member->set('field_birth_month', $input['field_birth_month']);
		$member->set('field_birth_day', $input['field_birth_day']);
		$member->set('field_birth_year', $input['field_birth_year']);
		$member->set('field_gender', $input['field_gender']);
		$member->set('field_location', $input['field_location']);
		
		$member->save();		
	 }
	 
	 public static function del( $uid ){
		x::deleteUserByUid( $uid );			
	 }
	 
	 public static function getMemberList() {
         $query = \Drupal::entityQuery('mall_member');
         if ( $keyword = x::in('keyword') ) {
             $ors = $query->orConditionGroup();
             $ors->condition( 'field_mobile', '%'.$keyword.'%', 'LIKE' );
             $ors->condition( 'mail', '%'.$keyword.'%', 'LIKE' );
             $ors->condition( 'field_first_name', '%'.$keyword.'%', 'LIKE' );
             $ors->condition( 'field_middle_name', '%'.$keyword.'%', 'LIKE' );
             $ors->condition( 'field_last_name', '%'.$keyword.'%', 'LIKE' );
             $ors->condition( 'field_location', '%'.$keyword.'%', 'LIKE' );
             $query->condition($ors);
         }
         $ids = $query->execute();
         return Member::loadMultiple($ids);


         /*
		$members = [];
			if( $keyword = x::in('keyword') ){
				$res = db_select(self::TABLE, 't')
				->fields('t',['id','user_id']);			
				/*
				*keyword ignores birth_day, birth_month, birth_year, and gender
				*NOTE: is it possible to use ->loadByProperties() with like?
				*/
         /*
				$ors = db_or();			
				$ors->condition( 'field_mobile', '%'.$keyword.'%', 'LIKE' );
				$ors->condition( 'mail', '%'.$keyword.'%', 'LIKE' );
				$ors->condition( 'field_first_name', '%'.$keyword.'%', 'LIKE' );
				$ors->condition( 'field_middle_name', '%'.$keyword.'%', 'LIKE' );
				$ors->condition( 'field_last_name', '%'.$keyword.'%', 'LIKE' );
				$ors->condition( 'field_location', '%'.$keyword.'%', 'LIKE' );
				
				$res = $res->condition( $ors );
				$res = $res->execute();
				
				$member_ids = $res->fetchAllAssoc('id', \PDO::FETCH_ASSOC);
				
				foreach( $member_ids as $member_id ){
					$uid = $member_id['user_id'];		
					$members[ $uid ] = self::loadByUid( $uid );
				}				
				//di( $members );exit;
				return $members;
			}
			else{				
				$members = Member::loadMultiple();				
			}

		return $members;
         */
	 }


}
