<?php
use Drupal\mall\Entity\Category;
use Drupal\mall\Entity\Item;
use Drupal\mall\Mall;
use Drupal\mall\Member;
use Drupal\mall\x;
Mall::emptyData();
//echo getcwd();


set_default_category();

function set_default_category() {
	Mall::Login('admin');

	$root_id_default = Category::add(0, 'Default');
	$id_car = Category::add($root_id_default, 'Car');
    $id_h = Category::add($id_car, 'Hyundai');
        $id_s = Category::add($id_h, 'Starex 2007');
          Category::add($id_s, 'Grand Starex No. 2');
      Category::add($id_car, 'Honda');
      Category::add($id_car, 'Samsung');

	$id_starex = Category::add($id_car, 'StareX');
	$id_starexchild = Category::add($id_starex, 'StareXchild');
	$id_bicycle = Category::add($id_car, 'Bycle');
	$root_id_discount = Category::add(0, 'Discount');
	$id_computer = Category::add($root_id_discount, 'Computer');
	$id_monitor = Category::add($id_computer, 'monitor');
	$id_keyboard = Category::add($id_computer, 'keyboard');
	$id_razer = Category::add($id_computer, 'Razer');
	$id_laptop = Category::add($root_id_discount, 'Laptops');
	Category::add($id_computer, 'lenovo');
	Category::add($id_computer, 'Intel');
	Category::add($id_computer, 'Windows');
	Category::add($id_computer, 'Wilkins');
	

	
	$items = 	[
					'Beats',
					'Bench',
					'Benz',
					'Crocs',
					'Dlink',
					'Eguitar',
					'Galaxy4',
					'i7',
					'Iphone6',
					'Keyboard',
					'Leche Flan',
					'Mouse',
					'Nike',
					'Piano',
					'PS4',
					'Skullcandy',
					'Supernatural',
					'Tux',
					'WD',
					'Yukata'
				];
	$count = 0;
	
	foreach( $items as $item ){
		$sale = [];
		$sale['title'] = $item." for sale";
		$sale['name'] = $item;
		$sale['category_id'] = $count;
		$sale['brand'] = $item." Brand";
		$sale['model'] = $item." Model";
		$sale['model_year'] = "199".$count;
		$sale['price'] = "10".$count."00";
		$sale['mobile'] = "090671042".$count."0";
		$sale['status'] = "B";
		$sale['content'] = "Hi, I want to sell my ".$item." with the price of ".$sale['price'].". Please send me a message with my number ".$sale['mobile'];
		
		//print_r( $sale );
		
		$sale_id = Item::update( $sale );
		
		$image = defaultFileUpload( $sale_id, $item );
		//exit;
		$count++;
	}
}




function defaultFileUpload( $id, $name ){
	
	$repo = 'public://mall/';
	file_prepare_directory($repo, FILE_CREATE_DIRECTORY);
	
	$file_location = getcwd()."/modules/mall/src/scripts/default-images/".strtolower( $name ).".jpg";	
	$type = "item_image_thumbnail";

	$f = array();
	$file = file_save_data( file_get_contents( $file_location ), $repo . $name );	
	x::LinkFileToEntity( $id, $file->id(), $type );
	x::LinkFileToEntity( $id, $file->id(), 'item_image1' );
	
}
