<?php
use Drupal\mall\Entity\Category;

$entities = Category::loadParents(59);
foreach ( $entities as $category ) {
  echo $category->id() . ' : ' .  $category->get('name')->value . "\n";
}

$category = Category::groupRoot(59);
echo "Group : " . $category->label();

