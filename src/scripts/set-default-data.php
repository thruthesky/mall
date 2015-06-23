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
	
	$id_food = Category::add(0, 'Food');
		$id_sweets = Category::add($id_food, 'Sweets');
			Category::add($id_sweets, 'Candies');
			$id_chocolate = Category::add($id_sweets, 'Chocolate');				
				Category::add($id_chocolate, 'Goya');
				Category::add($id_chocolate, 'Hersheys');
		$id_diet = Category::add($id_food, 'Diet');
			$id_low_calories = Category::add($id_diet, 'Low Calories');
	$id_health = Category::add(0, 'Health & Beauty');
		$id_makeup = Category::add($id_health, 'Make up');
			Category::add($id_makeup, 'Avon');
			Category::add($id_makeup, 'Foundation');
		Category::add($id_health, 'Healthy Living');
	$id_home = Category::add(0, 'Home & Lifestyle');
	$id_fashion = Category::add(0, 'Fashion');
		$id_clothing = Category::add($id_fashion, 'Clothing');
		$id_accessories = Category::add($id_fashion, 'Accessories');
			Category::add($id_accessories, 'Necklace');
			Category::add($id_accessories, 'Bracelet');
			Category::add($id_accessories, 'Earrings');
		$id_perfumery = Category::add($id_fashion, 'Perfumery');
			Category::add($id_perfumery, 'Bench');
			Category::add($id_perfumery, 'Avon');
	$id_electronics = Category::add(0, 'Electronics');
		$id_video = Category::add($id_electronics, 'Video Hardware');
			Category::add($id_video, 'Connectors');
			Category::add($id_video, 'Display Devices');
		$id_audio = Category::add($id_electronics, 'Audio Hardware');
			Category::add($id_audio, 'iMax');
	$id_fun = Category::add(0, 'Fun & Travel');
		Category::add($id_fun, 'Games');
		Category::add($id_fun, 'Vacation Spots');
	
	$categories = \Drupal::entityManager()->getStorage('mall_category')->loadByProperties(['parent_id'=>0]);
	
	$clist = [];
	foreach( $categories as $category ){
		$clist[] = $category->id();
		$sub_category = Category::loadAllChildren( $category->id() );		
		foreach( $sub_category as $sc ){
			//di( $sc );exit;
			$clist[] = $sc['entity']->id();
		}
	}
	
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
					'Yukata',
					'FF7 Cloud Figure',
					'Keyblade Keys',
					'DomoKun',
					'Turrones De Casoy',
					'Watermelon',
					'Green Apple',
					'MtDew',
					'PS3 Games',
					'DVI to HDMI',
					'LG 72inches',
					'Oculus Rift',
				];
	$count = 0;
	
	$province = ['Abra','Metro Manila','Pampanga'];
	$city = ['Abra - Bangued','Metro Manila - Makati City','Pampanga - Angeles City'];
	
	foreach( $items as $item ){
		if( $count % 3 == 0 ){
			$prov = $province[2];
			$ct = $city[2];
		}
		else if( $count % 2 == 0 ){
			$prov = $province[1];
			$ct = $city[1];
		}
		else if( $count % 1 == 0 ){
			$prov = $province[0];
			$ct = $city[0];
		}
		$sale = [];
		$sale['title'] = $item." for sale";
		$sale['name'] = $item;
		$sale['category_id'] = $clist[ $count ];
		$sale['brand'] = $item." Brand";
		$sale['model'] = $item." Model";
		$sale['model_year'] = "199".$count;
		$sale['price'] = "10".$count."00";
		$sale['mobile'] = "090671042".$count."0";
		$sale['status'] = "B";
		$sale['province'] = $prov;
		$sale['city'] = $ct;
		$sale['location'] = "Pampanga, Angeles City, San Fernando";
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
