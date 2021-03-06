<?php
namespace Drupal\mall\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mall\Item;
use Drupal\mall\x;
use Drupal\library\Member;

use Drupal\library\Library;

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
		$data['myUid'] = x::myUid();
		
        return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
    }
	
	public function members(){
		$data = [];		
		$member = Member::load( Library::myUid() );
		if( $member->roles->target_id == 'administrator' ){
			$input = x::input();
			
			if( empty( $input['page'] ) ) $input['page'] = 1;		
			if( empty( $input['limit'] ) ) $input['limit'] = 10;
			if( empty( $input['by'] ) ) $input['by'] = 'user_id';		
			if( empty( $input['order'] ) ) $input['order'] = 'DESC';
			
			$page_num = $input['page'];
			$limit = $input['limit'];	
			$by = $input['by'];
			$order = $input['order'];	
			
			if( $page_num <= 1 ) $from = 0;
			else $from = $limit * $page_num - $limit;		
			
			$result = db_query("SELECT count( DISTINCT( user_id ) ) FROM mall_item");
			$total_items = array_values( $result->fetchAssoc() )[0];
			
			$result = db_query("SELECT DISTINCT(user_id) FROM mall_item ORDER BY $by $order LIMIT $from, $limit");
			$rows = $result->fetchAllAssoc('user_id',\PDO::FETCH_ASSOC);
			
			$members = [];
			foreach( $rows as $row ){
				$members[ $row['user_id'] ] = Member::load( $row['user_id'] );
			}
			
			$data['members'] = $members;		
			$data['items_per_page'] = $limit;
			$data['input'] = $input;
			$data['total_items'] = $total_items;
		}else{
			$data['error'] = Library::error('Admin Error.', "Only administrators can view this page");
		}
		
        return [
            '#theme' => x::getThemeName(),
            '#data' => $data,
        ];
	}

}
