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
	  $groups = [];
	  foreach( $categories as $c ){
		$groups[$c->id()]['entity'] = $c;
		$groups[$c->id()]['child_no'] = count( Category::loadChildren( $c->id() ) );
	  }

      $data = [
        'groups' => $groups
      ];
      return [
        '#theme' => x::getThemeName(),
        '#data' => $data,
      ];
	}
  public static function add() {

    $parent_id  = x::in('parent_id');
    $re = Category::add($parent_id, x::in('name', ''));

	if( $parent_id == 0 ) $redirect_url = '/mall/admin/category?';
	else {
      $group = Category::groupRoot($parent_id);
      $redirect_url = '/mall/admin/category/group/list?parent_id=' . $group->id();
    }

    if ( x::isError($re) ) {
      $redirect_url .= "&error=$re[0]&message=$re[1]";
    }



    return new RedirectResponse( $redirect_url );
  }
  
  public static function del() {
	$id = x::in('id');
    //$category = Category::load($id);
    if ( x::in('confirmed', '') != 'yes' && $children = Category::loadChildren($id) ) {
      $redirect_url = "/mall/admin/category/delete/confirm?id=$id";
    }
    else {
      $is_root = Category::isRoot($id);
	  if ( $is_root ) $redirect_url = '/mall/admin/category';
      else $redirect_url = '/mall/admin/category/group/list?parent_id=' . Category::getRootID($id);
      Category::deleteAll($id );      
    }
    return new RedirectResponse( $redirect_url );
    /*

    $group = Category::groupRoot($id);
	if( $group->id() == $id ) $redirect_url =
	else{
		$redirect_url = '/mall/admin/category/group/list?parent_id=' . $group->id();
	}

    return new RedirectResponse( $redirect_url );//
    */
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

  public static function deleteConfirm() {
	$data = [];
	$data['category'] = Category::load(x::in('id'));
	$children = Category::loadChildren( x::in('id') );
	if( $children ) $data['children'] = $children;
	
    return [
      '#theme' => x::getThemeName(),
      '#data' => $data,
    ];
  }
}
