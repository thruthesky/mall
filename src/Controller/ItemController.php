<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\mall\x;

use Drupal\library\Library;
use Drupal\library\Language;
use Drupal\library\Entity\Category;
use Drupal\mall\Entity\Item;
use Drupal\mall\Entity\Member;

class ItemController extends ControllerBase {
    public function edit() {
		if( !x::myUid() ) return new RedirectResponse( "/mall?" . x::error(x::ERROR_PLEASE_LOGIN_FIRST) );
		
		$data = [];
		$data = x::getDefaultInformationByUid( x::myUid() );
		$data['status'] = x::$item_status;
		$data['category'][0]['entity'] = x::getCategoryChildren( 0 );
		$data['provinces'] = x::$provinces;
		
		if( $item_id = x::in('item_id') ){
			$data['item'] = Item::load( $item_id ); 
			if( $data['item']->user_id->target_id == x::myUid() || x::isAdmin() ){
				$category_parents = array_reverse( x::getCategoryParents( $data['item']->category_id->target_id ) );
				$count = 0;
				foreach( $category_parents as $c ){
					$data['category'][ $count ]['selected'] = $c->id();					
					$count++;
					$data['category_last_selected'] = $count;
					if( x::getCategoryChildren( $c->id() ) ){
						$data['category'][ $count ]['entity'] = x::getCategoryChildren( $c->id() );
					}
				}
				$files = Item::getFilesByType( $item_id );
				//di( $files );
				
				$file_urls = [];
				foreach( $files as $key => $values ){					
					foreach( $values as $k => $v ){
						$file_urls[ $key ][ $k ] = Item::getFileUrl( $v );
					}
				}
				if( $file_urls ) $data['files'] = $file_urls;
				$data['cities'] = x::$cities[ $data['item']->province->value ];				
			}
			else{
				return new RedirectResponse( "/mall/admin/item/list?" . x::error(x::ERROR_NOT_YOUR_POST) );
			}			
		}		
		
        return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
    }
	
    public function editSubmit() {
		if( !x::myUid() ) return new RedirectResponse( "/mall?" . x::error(x::ERROR_PLEASE_LOGIN_FIRST) );
		
		$input = x::input();	
		//$item = Item::load( $input['item_id'] );//move or fix this later, becomes repeatitive inside Item::del
		//di( $input );exit;
		//if( $item->user_id->target_id == x::myUid() || x::isAdmin() ){
        $re = Item::Update( $input );
        return new RedirectResponse( "/mall/item/add?item_id=".$re );
		/*}
		else{
			return new RedirectResponse( "/mall/admin/item/list?" . x::error(x::ERROR_NOT_YOUR_POST) );
		}*/
    }
	
	public function collection(){
		$data['status'] = x::$item_status;
		$data['items'] = Item::getItems();		
		$data['total'] = count( $data['items'] );
		if( x::in( 'keyword' ) ){
			$data['keyword'] = x::in( 'keyword' );		
		}
		
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
	
	public function collectionWithImages(){
		$data['status'] = x::$item_status;
		$data['items'] = Item::getItems();		
		$data['total'] = count( $data['items'] );
		if( x::in( 'keyword' ) ){
			$data['keyword'] = x::in( 'keyword' );		
		}
		
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
	
	public function del(){
		$item_id = x::in( 'item_id' );
		
		$item = Item::load( $item_id );//move or fix this later, becomes repeatitive inside Item::del
		
		if( $item->user_id->target_id == x::myUid() || x::isAdmin() ){			
			Item::del( $item_id );
			return new RedirectResponse( "/mall/admin/item/list" );
		}
		else{
			return new RedirectResponse( "/mall/admin/item/list?" . x::error(x::ERROR_NOT_YOUR_POST) );
		}
	}
	
	public function view(){
		if( $item_id = x::in('item_id') ){
			$data['item'] = Item::getItemsWithImages( [ 'id' => $item_id ] )['items'][0];
			$data['status'] = x::$item_status;
		}
//di( $data['item']['images'] );exit;
		return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
	}
	
	public function search(){
		$category_text = '';
		$conds = [];
		$input = x::input();
		
		if( $category_id = $input['category'] ){
			if( is_numeric( $category_id ) ){
				/*conds*/
				$conds['category_id'] = $category_id;
				/*eo conds*/
				$category = x::getCategoryEntity( $category_id );
				$category_text = " with the category of [ ".$category->name->value." ]";
			}
			else{
				$data['error'] = "ERROR";
			}
		}
		
		if( empty( $data['error'] ) ){
			/*conds*/
			if( $input['order'] ){
				$order_by = explode( '_', $input['order'] );
				
				$conds['by'] = $order_by[0];
				if( $order_by[1] == 'low' ) $conds['order'] = 'ASC';
				else if( $order_by[1] == 'high' ) $conds['order'] = 'DESC';
			}
			else{
				$input['order'] = 'created_low';
				$conds['by'] = 'created';
				$conds['order'] = 'ASC';
			}
			
			//ONLY make use of page when there is limit...
			if( $input['limit'] ){
				$conds['limit'] = $input['limit'];
				if( $input['page'] ) $conds['page'] = $input['page'];
			}
			/*if changed, also edit on Mall.module*/
			else $conds['limit'] = 15;
			
			if( $input['user_id'] ){
				$conds['user_id'] = $input['user_id'];								
				$member_id = \Drupal::entityQuery('mall_member')->condition('user_id',$conds['user_id'])->execute();
				$member = Member::loadMultiple( $member_id );
				if( $member ){
					$data['user_entity'] = reset( $member );
				}
				else{					
					$data['error'] = Library::error('User does not exist.', Language::string('library', 'user_id_does_not_exist'));					
				}
			}
			if( empty( $data['error'] ) ){
				if( $input['price_from'] ) $conds['price_from'] = $input['price_from'];
				if( $input['price_to'] ) $conds['price_to'] = $input['price_to'];
				if( $input['province'] ) $conds['province'] = $input['province'];//
				if( $input['time'] ) $conds['time'] = $input['time'];
				if( $input['status'] ) $conds['status'] = $input['status'];//
				
				/*eo conds*/	
				$data['default_search_sort'] = x::$default_search_sort;
				$data['default_item_per_page'] = x::$default_item_per_page;	
				
				$data['category_entity_list'] = Category::loadAllCategories();
				$data['status'] = x::$item_status;
				$data['provinces'] = x::$provinces;		
				$data['time'] = x::$time;				
				$data['prices'] = x::$price;
				
				$data['items'] = Item::getItemsWithImages( $conds );
				
				unset( $conds['limit'] );
				unset( $conds['page'] );
				//Fix this. What I just did here was do the query again without the limit and page conditions
				$data['total_items'] = count( Item::getItemsWithImages( $conds )['items'] );
				
				if( x::$input['keyword'] ){				
					$keyword = "[ ".$input['keyword']." ]";
				}
				else{
					$keyword = '[ anything ]';
				}
			}
		}

		return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
	}
}
