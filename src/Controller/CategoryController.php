<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\x;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Drupal\mall\Entity\Category;

class CategoryController extends ControllerBase {
	public static function collection() {
      $categories = \Drupal::entityManager()->getStorage('mall_category')->loadByProperties(['parent_id'=>0]);
      //$ids = \Drupal::entityQuery('mall_category')->condition('parent_id',0)->execute();

      $data = [
        'groups' => $categories
      ];
      return [
        '#theme' => x::getThemeName(),
        '#data' => $data,
      ];
	}
  public static function add() {
    Category::add(0, \Drupal::request()->get('name', ''));
    return new RedirectResponse('/mall/admin/category');
  }

  public static function groupCollection()
  {
    //$categories = \Drupal::entityManager()->getStorage('mall_category')->loadByProperties(['parent_id'=>0]);
    //$ids = \Drupal::entityQuery('mall_category')->condition('parent_id',0)->execute();


    /*$cats = Category::loadChildren(\Drupal::request()->get('no'));

    foreach( $cats as $cat ) {
      di( $cat->get('name')->value );
    }
    */

    $cats = Category::loadChildren(\Drupal::request()->get('no'));
    di($cats);


    $data = [
      //'category' => Category::loadChildren(\Drupal::request()->get('no'))
    ];
    return [
      '#theme' => x::getThemeName(),
      '#data' => $data,
    ];
  }
}
