<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\mall\x;

use Drupal\mall\Entity\Item;

class ItemController extends ControllerBase {
    public function edit() {
		if( !x::myUid() ) return new RedirectResponse( "/mall?" . x::error(x::ERROR_PLEASE_LOGIN_FIRST) );
		
		$data = [];
		$data = x::getDefaultInformationByUid( x::myUid() );
		$data['status'] = x::$item_status;
		$data['category'][0]['entity'] = x::getCategoryChildren( 0 );
		
		if( $item_id = x::in('item_id') ){			
			$data['item'] = Item::load( $item_id ); 
			if( $data['item']->user_id->target_id == x::myUid() || x::isAdmin() ){
				$category_parents = array_reverse( x::getCategoryParents( $data['item']->category_id->target_id ) );
				$count = 0;
				foreach( $category_parents as $c ){
					$data['category'][ $count ]['selected'] = $c->id();
					$count++;
					if( x::getCategoryChildren( $c->id() ) ){
						$data['category'][ $count ]['entity'] = x::getCategoryChildren( $c->id() );
					}
				}
				
				$files = Item::getFiles( $item_id );
				$file_urls = [];
				foreach( $files as $file ){
					$file_urls[ $file->id() ] = Item::getFileUrl( $file );
				}
				if( $file_urls ) $data['files'] = $file_urls;
			}
			else{
				return new RedirectResponse( "/mall?" . x::error(x::ERROR_NOT_YOUR_POST) );
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
		$item = Item::load( $input['item_id'] );
		if( $item->user_id->target_id == x::myUid() || x::isAdmin() ){
			if( !is_numeric($input['price'] ) ) return new RedirectResponse( "/mall/item/add?" . x::error(x::ERROR_MUST_BE_AN_INTEGER , [ 'field' => 'Price' ] ) );
			if( !is_numeric($input['model_year'] ) ) return new RedirectResponse( "/mall/item/add?" . x::error(x::ERROR_MUST_BE_AN_INTEGER ,[ 'field'=> 'Model year' ]) );
			
			$re = Item::Update( $input );
			
			return new RedirectResponse( "/mall/item/add?item_id=".$re );
		}
		else{
			return new RedirectResponse( "/mall?" . x::error(x::ERROR_NOT_YOUR_POST) );
		}
    }
	
	public function collection(){
		$data['status'] = x::$item_status;
		$data['items'] = Item::getItemList();		
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
		Item::del( $item_id );
		
		return new RedirectResponse( "/mall/admin/item/list" );
	}
}
