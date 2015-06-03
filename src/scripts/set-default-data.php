<?php
use Drupal\mall\Entity\Category;
use Drupal\mall\Entity\Item;
use Drupal\mall\Mall;
Mall::emptyData();

set_default_data();

function set_default_data() {

	Mall::Login('admin');

	$root_id_default = Category::add(0, 'Default');
	$id_car = Category::add($root_id_default, 'Car');
	$id_starex = Category::add($id_car, 'StareX');
	$id_starexchild = Category::add($id_starex, 'StareXchild');
	$id_bicycle = Category::add($id_car, 'Bycle');
	$root_id_discount = Category::add(0, 'Discount');
	$id_computer = Category::add($root_id_discount, 'Computer');
	$id_monitor = Category::add($id_computer, 'monitor');
	$id_keyboard = Category::add($id_computer, 'keyboard');
	$id_laptop = Category::add($root_id_discount, 'Laptops');	

	Category::update($id_starex, 'Van');
	Category::del($id_bicycle);




	/*for item*/
	//Mall::Login('admin');
	$status = ['','S','B','U','S','B'];
	$item_ids = [];
	for( $i = 1; $i <=5; $i ++ ){
		$data = [];
		$data['name'] = "Printer"."_".$i;
		$data['price'] = "1500".$i;
		$data['status'] = $status[$i];/*S = second hand | B = Brand new | U = Slightly used*/
		$data['content'] = "I want to sell my old printer"."_".$i;
		$data['brand'] = "Adidas"."_".$i;
		$data['model'] = "SCX-3200"."_".$i;
		
		$item_ids[] = Item::Add( $data );
	}
	Item::update($item_ids[0], 'Printer - Edited Item');
	Item::del($item_ids[2]);
}
