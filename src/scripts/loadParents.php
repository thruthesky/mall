<?php
use Drupal\mall\Entity\Category;
$entities = Category::loadParents(73);
foreach ( $entities as $category ) {
  echo $category->id() . ' : ' .  $category->get('name')->value . "\n";
}

/*
foreach ( $entities as $id => $category ) {
  echo "$id : $category[name]";
  echo $category->get('name')->value;
  echo "\n";
}
*/

