<?php
namespace Drupal\mall;
/**
 * Class X
 * @package Drupal\mall
 * @short Helper library class for mall module.
 * @short Difference from Mall.php is that Mall.php is a library that is only used for mall module. x.php holds more generic functions.
 */

class HTML{
	static $month = [
			'1'=> 'January',
			'2'=> 'February',
			'3'=> 'March',
			'4'=> 'April',
			'5'=> 'May',
			'6'=> 'June',
			'7'=> 'July',
			'8'=> 'August',
			'9'=> 'September',
			'10'=> 'October',
			'11'=> 'November',
			'12'=> 'December',
			];

	public static function htmlOption($option,$default=null)
	{
		$re = '';
		foreach ( $option as $k => $v ) {
			$re .= "<option value='$k'";
			if ( $k == $default ) {
				$re .= " selected=1";
			}
			$re .= ">$v</option>";
		}
		return $re;
	}

	public static function dayOption($my) {
		$kv = [];
		for( $d = 1; $d <= 31; $d++ ) $kv[$d] = $d;
		return self::htmlOption($kv, $my);
	}
	
	public static function monthOption($birth_month) {
		return self::htmlOption(self::$month,$birth_month);
	}
	
	public static function yearOption($my) {
		$kv = [];
		for( $y = 2006; $y >= 1946; $y-- ) $kv[$y] = $y;
		return self::htmlOption($kv, $my);
	}
}