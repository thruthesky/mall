<?php
use Drupal\mall\Entity\Category;
use Drupal\mall\Entity\Item;
use Drupal\mall\Mall;
use Drupal\mall\Member;
Mall::emptyData();

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
	$id_laptop = Category::add($root_id_discount, 'Laptops');	
}
