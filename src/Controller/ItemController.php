<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\mall\x;

use Drupal\library\Library;
use Drupal\library\Language;
use Drupal\library\Entity\Category;
use Drupal\mall\Entity\Item;

use Drupal\user\Entity\User;

class ItemController extends ControllerBase {
    public function edit() {
		$data = [];
		$theme = x::getThemeName();//default theme
		
		if( !x::myUid() ){
			$theme = 'mall.mall';
			$data['error'] = Library::error('User not logged in.', Language::string('library', 'not_logged_in'));
		}
				
		$data = x::getDefaultInformationByUid( x::myUid() );
		//$data['status'] = x::$item_status;
		$data['category'][0]['entity'] = x::getCategoryChildren( 0 );
		$data['provinces'] = x::$provinces;
		$data['currency'] = x::$currency;
		
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
				di( $files );exit;
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
				$theme = "mall.admin.item.list";
				$data['error'] = Library::error('Not your post.', Language::string('library', 'not_your_post'));
			}			
		}		
		
        return [
            '#theme' => $theme,
            '#data' => $data,
        ];
    }
	
    public function editSubmit() {
		$theme = "mall.mall";	
		if( !x::myUid() ){			
			$data['error'] = Library::error('User not logged in.', Language::string('library', 'not_logged_in'));
		}
		else{
			$input = x::input();	
			if( empty( $input['price'] ) || $input['price'] <= 0 ){
				$theme = "mall.item.add";
				$data['input'] = $input;
				$data['currency'] = x::$currency;
				if( !empty( $input['province'] ) ){
					$data['provinces'] = x::$provinces;
					$data['cities'] = x::$cities[ $input['province'] ];
				}
				$data['error'] = Library::error('Invalid Price.', "Invalid price value.");
			}
			else{
				$item = Item::load( $input['item_id'] );

				if( empty( $item ) || $item->user_id->target_id == x::myUid() || x::isAdmin() ){
					$re = Item::Update( $input );
					if( empty( $re['error'] ) ){						
						return new RedirectResponse( "/mall/item/add?item_id=".$re );
					}
					else{
						$theme = "mall.item.add";
						$data = $re;
						if( $re['error'] == 'spam' ) $data['error'] = Library::error('Admin Warning', 'Please do not spam item posting.');
						else if( $re['error'] == 'no_file' ) $data['error'] = Library::error('File Error', 'Please Upload atleast one file.');
						$data['category'][0]['entity'] = x::getCategoryChildren( 0 );
						$data['provinces'] = x::$provinces;
						$data['currency'] = x::$currency;
					}
				}
				else{						
					$data['error'] = Library::error('Not your post.', Language::string('library', 'not_your_post'));
				}
			}
		}
		return [
				'#theme' => $theme,
				'#data' => $data,
			];
    }
	
	public function collection(){		
		$user_role = x::loadLibraryMember( Library::myUid() )->roles->target_id;
		if( $user_role == 'administrator' ){
			$input = x::input();
			
			if( !empty( $input['category_id'] ) ) $conds['category_id'] = $input['category_id'];
			
			$data['status'] = x::$item_status;
			$data['all_items'] = Item::getItems( $conds );		
			$data['total_items'] = count( $data['all_items'] );	
			
			if( !empty( $input['page'] ) ) $conds['page'] = $input['page'];
			else {
				$input['page'] = 1;
				$conds['page'] = 1;
			}
			
			if( !empty( $input['limit'] ) ) $conds['limit'] = $input['limit'];
			else $conds['limit'] = 10;						
			
			$data['items_per_page'] = $conds['limit'];
			$data['total_pages'] = ceil( $data['total_items'] / $data['items_per_page'] );
			$data['input'] = $input;		
			$data['items'] = Item::getItems( $conds );
			$data['categories'] = x::getCategoryChildren( 0 );
			
			if( x::in( 'keyword' ) ){
				$data['keyword'] = x::in( 'keyword' );		
			}

			if( $input['page'] > $data['total_pages'] ){
				$data['error'] = Library::error('Page Error','You have exceeded the page limit of '.$data['total_pages']);
			}
		}
		else{
			$data['error'] = Library::error('Administrator error.', 'Only an admin can view this page');
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
			
			//change these later
			if( x::isAdmin() ) return new RedirectResponse( "/mall/admin/item/list" );
			else return new RedirectResponse( "/mall/item/search?user_id=".$item->user_id->target_id );
		}
		else{
			$theme = "mall.admin.item.list";
			$data['error'] = Library::error('Not your post.', Language::string('library', 'not_your_post'));
		}
		
		return [
			'#theme' => $theme,
			'#data' => $data,
		];
	}
	
	public function view(){
		$data = [];
		
		if( $item_id = x::in('item_id') ){			
			$data['item'] = Item::getItemsWithImages( [ 'id' => $item_id ] )['items'][0];
			
			if( !empty( $data['item'] ) ){				
				self::changeCountByField( $data['item']['entity'], 'no_of_view' );
				
				$uid = $data['item']['entity']->user_id->target_id;
				$data['seller'] = x::loadLibraryMember( $uid );
				$data['status'] = x::$item_status;			
				$data['cities'] = x::$cities;	
				$data['currency'] = x::$currency;
				
				self::default_setup( $data );
				$theme = x::getThemeName();
				
			}
			else{
				$data['error'] = Library::error('Ivalid Item ID.', Language::string('library', 'invalid_item_id'));
				$theme = 'mall.mall';
			}
		}

		return [
            '#theme' => $theme,
            '#data' => $data,
        ];
	}
	
	/*
	*@todo need to add something like Increase only per user_id? or IP?
	*changes the count of an entity field ( e.g. for no_of_view )
	*/
	public function changeCountByField( item $entity, $field ){		
		$entity->set($field, ( $entity->$field->value + 1 ) );
		$entity->save();
	}
	
	public function search(){
		$data = [];
		self::default_setup( $data );

		return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
	}
	
	public function default_setup( &$data ){
		$category_text = '';
		$conds = [];		
		$input = x::input();	
        $category_id = isset($input['category_id']) ? $input['category_id'] : 0;
		
		if( $category_id  ) {
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
			if( !empty($input['order_by']) ) {
				/*
				*should be something like
				*created_low -> created ASC ( translated as created from lowest )
				*created_high -> created DESC ( translated as created from highest )
				*/
				$order_by = explode( '_', $input['order_by'] );
				if( count( $order_by ) > 1 ){
					$conds['by'] = $order_by[0];
					if( $order_by[1] == 'low' ) $conds['order'] = 'ASC';
					else if( $order_by[1] == 'high' ) $conds['order'] = 'DESC';
				}
				else{
					if( !empty($input['order'] ) ) $conds['order'] = $input['order'];
					if( !empty($input['by'] ) )  $conds['by'] = $input['by'];
				}
			}
			else{
				$input['order'] = 'created_high';
				$conds['by'] = 'created';
				$conds['order'] = 'DESC';
			}
			
			//ONLY make use of page when there is limit...
			if( !empty( $input['limit'] ) ){
				$conds['limit'] = $input['limit'];				
			}
			/*if changed, also edit on Mall.module*/
			else{
				$conds['limit'] = x::$default_item_per_page[1];				
			}
			
			if( !empty( $input['page'] ) ) $conds['page'] = $input['page'];
			
			if( isset( $input['user_id'] ) ){						
				$conds['user_id'] = $input['user_id'];
				$member = User::load( $conds['user_id'] );	
				
				if( $member ){
					$data['user_entity'] = $member;
				}
				else{
					$data['error'] = Library::error('User does not exist.', Language::string('library', 'user_id_does_not_exist'));
				}
			}
			if( empty( $data['error'] ) ){
				if( !empty( $input['price_from'] ) ) $conds['price_from'] = $input['price_from'];
				if( !empty( $input['price_to'] ) ) $conds['price_to'] = $input['price_to'];
				if( !empty( $input['province'] ) ) $conds['province'] = $input['province'];//
				if( !empty( $input['city'] ) ) $conds['city'] = $input['city'];//
				if( !empty( $input['time'] ) ) $conds['time'] = $input['time'];
				if( !empty( $input['status'] ) ) $conds['status'] = $input['status'];//
	
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
				$all_items = Item::getItemsWithImages( $conds );
				if( isset( $all_items['items'] ) ) $data['total_items'] = count( $all_items['items'] );
				
				if( isset( $input['keyword'] ) ){				
					$keyword = "[ ".$input['keyword']." ]";
				}
				else{
					$keyword = '[ anything ]';
				}
			}
		}
	}
}
