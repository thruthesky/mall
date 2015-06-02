<?php
use Drupal\mall\Entity\Category;
use Drupal\mall\Mall;
Mall::emptyData();

set_default_data();

function set_default_data() {

  Mall::Login('jaeho');

  $root_id_default = Category::add(0, 'Default');
  $id_car = Category::add($root_id_default, 'Car');
  $id_starex = Category::add($id_car, 'StareX');
  $id_bicycle = Category::add($id_car, 'Bycle');
  $root_id_discount = Category::add(0, 'Discount');
  $id_computer = Category::add($root_id_discount, 'Computer');

  Category::update($id_starex, 'Van');
  Category::del($id_bicycle);
}
