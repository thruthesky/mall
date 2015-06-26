<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\Item;
use Drupal\mall\x;

class MallController extends ControllerBase {
	public function admin() {
		$data = [];
		return [
			'#theme' => x::getThemeName(),
			'#data' => $data,
		];
	}
    public function firstPage() {	
        $data = [];
		$data['mall_top'] = Item::getItemsWithImages( [ 'limit'=>20, 'order'=>'DESC', 'by'=>'id' ] );
		di( $data );exit;
		//$data['mall_top_small'] = Item::getItemsWithImages( [ 'limit'=>8, 'order'=>'DESC', 'by'=>'id' ] );
		//$data['todays_pick'] = Item::getItemsWithImages( [ 'limit'=>9, 'order'=>'DESC', 'by'=>'id' ] );	
		//$data['mall_item_list'] = Item::getItemsWithImages( [ 'limit'=>16, 'order'=>'DESC', 'by'=>'id' ] );
		
        return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
    }

}
