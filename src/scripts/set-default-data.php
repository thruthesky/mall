<?php

use Drupal\mall\Entity\Category;

set_default_data();


function set_default_data() {
  $category = Category::create();
  $category->set('name', 'Category A');
  $category->set('parent_id', 0);
  $category->save();
}
