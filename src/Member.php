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
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  public static function mallMemberForm()
	{

		$form = null;
		
		$myUid = x::myUid();		

		//$user = User::load($myUid);
		$username = self::getMallDefaultValues( $myUid, 'username');
		//exit;
		$first_name = self::getMallDefaultValues( $myUid, 'first_name');
		$last_name = self::getMallDefaultValues( $myUid, 'last_name');
		$middle_name = self::getMallDefaultValues( $myUid, 'middle_name');
		$mail = self::getMallDefaultValues( $myUid, 'mail');
		
		$mobile = self::getMallDefaultValues( $myUid, 'mobile');		
		
		
		/** Gender checking... */
		$gender = self::getMallDefaultValues( $myUid, 'gender');		
		$$gender = "selected";

		/** Birth day */
		$years = html::yearOption(self::getMallDefaultValues( $myUid, 'birth_year'));
		$months = html::monthOption(self::getMallDefaultValues( $myUid, 'birth_month'));
		$days = html::dayOption(self::getMallDefaultValues( $myUid, 'birth_day'));
		
		$gender = self::getMallDefaultValues( $myUid, 'gender');		
		$$gender = "selected";
			

		$location = self::getMallDefaultValues( $myUid, 'location');
		
		if ( x::login() ) {
			//$myUsername = x::my('name');
			$text_submit = "UPDATE";
			$title = "Update Mall User Account";
		}
		else {
			$text_submit = "REGISTER";
			$title = "Register as a Mall User";			
		}




		$form .=<<<EOH
		<div class='title'>$title</div>
		<div class='description'>This form is for Market Only.</div>
        <form class='account' type='POST' action="/mall/member/register/submit">
        <fieldset>            
			<div class='group one'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr valign='top' class='row'>
					<td width='153'>
						<span class='caption'>First Name</span>
					</td>
					<td>
						<span class='value'><input type="text" name="first_name" required placeholder="First name" value="$first_name"></span>
					</td>
				</tr>
				<tr valign='top' class='row'>
					<td>
						<span class='caption'>Last Name</span>
					</td>
					<td>
						<span class='value'><input type="text" name="last_name" required placeholder="Last name"  value="$last_name"></span>
					</td>
				</tr>
				<tr valign='top' class='row'>
					<td>
						<span class='caption'>Middle Name</span>
					</td>
					<td>
						<span class='value'><input type="text" name="middle_name" placeholder="Middle name"  value="$middle_name"></span>
					</td>
				</tr>				
			</table>
			</div><!--/group one-->
			<div class='group two'>
				<table cellpadding=0 cellspacing=0 width='100%'>
					<tr valign='top' class='row'>
						<td width='153'>
							<span class='caption'>Email</span>
						</td>
						<td>
							<span class='value'><span class='element'><input type="email" name="mail" required placeholder="Email" value="$mail"></span>
						</td>
					</tr>
					<tr valign='top' class='row'>
						<td width='153'>
							<span class='caption'>Mobile Number</span>
						</td>
						<td>
							<input type="text" name="mobile" required placeholder="mobile" value="$mobile">
						</td>
					</tr>
				</table>
			</div><!--/group two-->
			
			<div class='group three'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr valign='top' class='row'>
					<td width='153'>
						<span class='caption'>Birth day</span>
					</td>
					<td>
						<span class='value clearfix'>
							<select name="birth_month" required>
								<option value="">Month</option>
								$months
							</select>
							<select name="birth_day" required>
								<option value="">Day</option>
								$days
							</select>
							<select name="birth_year" required>
								<option value="">Year</option>
								$years
							</select>
						</span>
					</td>
				</tr>
				<tr valign='top' class='row'>
					<td>
						<span class='caption'>Gender</span>
					</td>
					<td>
						<span class='value'>
							<select name='gender'>
								<option>Gender</option>
								<option value="M" required $M>Male</option>
								<option value="F" required $F>Female</option>
							</select>							
						</span>
					</td>	
				</tr>
				<tr valign='top' class='row'>
					<td>
						<span class='caption'>Location</span>
					</td>
					<td>
						<input type="text" name="location" required placeholder="Location" value="$location">
					</td>
				</tr>
			</table>
			</div><!--/group three-->
EOH;

		$form .=<<<EOH
        <div class="buttons">
            <input class="form-button" type="submit" value="$text_submit">
            <span class="cancel form-button"><a href='/'>CANCEL</a></span>
        </div>

		</fieldset>
    </form>
EOH;
		return $form;
	}
	
	
	private static function getMallDefaultValues( $uid, $string ) {		
		$res = db_select(self::TABLE, 't')
		  ->fields('t', [ 'value' ])
		  ->condition('uid', $uid)
		  ->condition('code', $string)
		  ->execute();
		$row = $res->fetchAssoc(\PDO::FETCH_ASSOC);
		return $row['value'];
	}
}