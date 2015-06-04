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

    $parent_id  = x::in('parent_id');
    Category::add($parent_id, x::in('name', ''));
	
	if( $parent_id == 0 ) $redirect_url = '/mall/admin/category';
	else {
      $group = Category::groupRoot($parent_id);
      $redirect_url = '/mall/admin/category/group/list?parent_id=' . $group->id();
    }
    return new RedirectResponse( $redirect_url );
  }
  
  public static function del() {
	$id =  \Drupal::request()->get('id', 0);
    
	if( $id == 0 ) $redirect_url = '/mall/admin/category';
	else{
		$group = Category::groupRoot($id);
		$redirect_url = '/mall/admin/category/group/list?parent_id=' . $group->id();
	}
	Category::del( $id );
    return new RedirectResponse( $redirect_url );//
  }
  
  public static function update() {	
	$id =  \Drupal::request()->get('id');
	$name =  \Drupal::request()->get('name');
   
	
	if( $id == 0 ) $redirect_url = '/mall/admin/category';
	else{
		$group = Category::groupRoot($id);
		$redirect_url = '/mall/admin/category/group/list?parent_id=' . $group->id();
	}
	Category::update( $id, $name );
    return new RedirectResponse( $redirect_url );//
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
	
	//$group_id = x::in('no');
	//$group = \Drupal::entityManager()->getStorage('mall_category')->load( $group_id );

	//$root = [ 'id'=>$group_id, 'name'=> $group->label() ];

    $data = [];

    if ( $id = x::in('parent_id') ) {
      $data['group'] = Category::load(x::in('parent_id'));
      $data['children'] = Category::loadChildren( $id );
    }
    return [
      '#theme' => x::getThemeName(),
      '#data' => $data,
    ];
  }
}
