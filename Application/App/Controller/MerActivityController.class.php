<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 用户活动
 */
class MerActivityController extends Controller {
	
	private $jsonUtils;
	private $session_handle; // session 处理类
	private $activity_dao;
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->activity_dao = M ( 'activity' );
	}
	
	// 商家添加活动
	public function add_activity() {
		$sessionid = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $sessionid );
		$name = isset ( $_POST ['name'] ) ? htmlspecialchars ( $_POST ['name'] ) : '';
		$categorys = isset ( $_POST ['category_ids'] ) ? htmlspecialchars ( $_POST ['category_ids'] ) : '';
		$cart_model = isset ( $_POST ['cart_model'] ) ? htmlspecialchars ( $_POST ['cart_model'] ) : '';
		$time = isset ( $_POST ['time'] ) ? htmlspecialchars ( $_POST ['time'] ) : '';
		$market_price = isset ( $_POST ['market_price'] ) ? htmlspecialchars ( $_POST ['market_price'] ) : '';
		$second_price = isset ( $_POST ['second_price'] ) ? htmlspecialchars ( $_POST ['second_price'] ) : '';
		$remain = isset ( $_POST ['remain'] ) ? htmlspecialchars ( $_POST ['remain'] ) : '';
		$start_time = isset ( $_POST ['start_time'] ) ? htmlspecialchars ( $_POST ['start_time'] ) : '';
		$end_time = isset ( $_POST ['end_time'] ) ? htmlspecialchars ( $_POST ['end_time'] ) : '';
		$valid_start_time = isset ( $_POST ['valid_start_time'] ) ? htmlspecialchars ( $_POST ['valid_start_time'] ) : '';
		$valid_end_time = isset ( $_POST ['valid_end_time'] ) ? htmlspecialchars ( $_POST ['valid_end_time'] ) : '';
		$date_limit = isset ( $_POST ['date_limit'] ) ? htmlspecialchars ( $_POST ['date_limit'] ) : ''; // 日期限制
		$is_reserve = isset ( $_POST ['reserve'] ) ? htmlspecialchars ( $_POST ['reserve'] ) : ''; // 是否需要预约
		$reserve_timeout = isset ( $_POST ['reserve_timeout'] ) ? htmlspecialchars ( $_POST ['reserve_timeout'] ) : ''; // 逾期是否保留
		$enjoy_other_preferential = isset ( $_POST ['enjoy_other_preferential'] ) ? htmlspecialchars ( $_POST ['enjoy_other_preferential'] ) : ''; // 是否享受其他优惠
		$detail = isset ( $_POST ['detail'] ) ? htmlspecialchars ( $_POST ['detail'] ) : '';
		
		if (empty ( $name )) {
			$this->jsonUtils->echo_json_msg ( 4, '活动名称为空...' );
			exit ();
		}
		if (empty ( $categorys )) {
			$this->jsonUtils->echo_json_msg ( 4, '项目为空...' );
			exit ();
		}
		if (empty ( $cart_model )) {
			$this->jsonUtils->echo_json_msg ( 4, '支持车型为空...' );
			exit ();
		}
		if (empty ( $time )) {
			$this->jsonUtils->echo_json_msg ( 4, '服务用时为空...' );
			exit ();
		}
		if (empty ( $start_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '活动开始时间为空...' );
			exit ();
		}
		if (empty ( $end_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '活动结束时间为空...' );
			exit ();
		}
		if (empty ( $valid_start_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '有效期开始时间为空...' );
			exit ();
		}
		if (empty ( $valid_end_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '有效期结束时间为空...' );
			exit ();
		}
		if (empty ( $date_limit )) {
			$this->jsonUtils->echo_json_msg ( 4, '日期使用限制为空...' );
			exit ();
		}
		if (empty ( $is_reserve )) {
			$this->jsonUtils->echo_json_msg ( 4, '是否需要预约为空...' );
			exit ();
		}
		if (empty ( $reserve_timeout )) {
			$this->jsonUtils->echo_json_msg ( 4, '逾期是否保留为空...' );
			exit ();
		}
		if (empty ( $enjoy_other_preferential )) {
			$this->jsonUtils->echo_json_msg ( 4, '是否共享别的优惠为空...' );
			exit ();
		}
		if (empty ( $remain )) {
			$this->jsonUtils->echo_json_msg ( 4, '数量为空...' );
			exit ();
		}
		if (empty ( $second_price )) {
			$this->jsonUtils->echo_json_msg ( 4, '秒杀价为空...' );
			exit ();
		}
		if (empty ( $market_price )) {
			$this->jsonUtils->echo_json_msg ( 4, '市场价为空...' );
			exit ();
		}
		
		$data ['name'] = $name;
		$data ['category_ids'] = $categorys;
		$data ['cart_model'] = $cart_model;
		$data ['time'] = $time;
		$data ['market_price'] = $market_price;
		$data ['second_price'] = $second_price;
		$data ['remain'] = $remain;
		$data ['start_time'] = strtotime ( $start_time );
		$data ['end_time'] = strtotime ( $end_time );
		$data ['valid_start_time'] = strtotime ( $valid_start_time );
		$data ['valid_end_time'] = strtotime ( $valid_end_time );
		if ($data ['start_time'] >= $data ['end_time']) {
			$this->jsonUtils->echo_json_msg ( 4, '活动开始结束时间无效' );
			exit ();
		}
		if ($data ['valid_start_time'] >= $data ['valid_end_time']) {
			$this->jsonUtils->echo_json_msg ( 4, '活动有效期开始结束时间无效' );
			exit ();
		}
		$data ['date_limit'] = $date_limit;
		$data ['reserve'] = $is_reserve;
		$data ['reserve_timeout'] = $reserve_timeout;
		$data ['enjoy_other_preferential'] = $enjoy_other_preferential;
		$data ['detail'] = $detail;
		$data ['effect'] = 0;
		$data ['merchant_id'] = $merchant_id;
		$data ['addtime'] = time ();
		
		if ($_FILES) {
			
			$arr = mul_upload ( '/Activity/',1 );
			if ($arr) {
				
				$data ['pics'] = json_encode ( $arr ); // 把多张图片数组格式转json保存数据库
			
			}
		}else{
			$data['pics'] = array();
		}
		
		$result = $this->activity_dao->add ( $data );
		
		if ($result) {
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '添加失败！' );
			exit ();
		}
	
	}
	// 商家活动列表
	public function activity_list() {
		$sessionid = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $sessionid );
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$type = isset ( $_POST ['type'] ) ? ( int ) htmlspecialchars ( $_POST ['type'] ) : '2';
		$limit = ($page - 1) * $num . "," . $num;
		$cur_date = time ();
		switch ($type) {
			case 2 : // 进行中
				$where = " and start_time <= $cur_date and end_time >= $cur_date  and   effect =1  order by addtime desc limit $limit";
				break;
			case 3 : // 下架
				$where = " and end_time<$cur_date order by addtime desc limit $limit";
				break;
			case 1 : // 未开始
				$where = " and start_time>$cur_date  and effect =1   order by addtime desc limit $limit";
				break;
			default : // 默认进行中
				$this->jsonUtils->echo_json_msg ( 1, 'type错误' );
				exit ();
				break;
		}
		$sql = "select id,name,effect,second_price,market_price,pics,from_unixtime(start_time,'%Y-%m-%d %H:%i') as start_time,from_unixtime(end_time,'%Y-%m-%d %H:%i') as end_time,remain  from " . C ( 'DB_PREFIX' ) . "activity   where merchant_id=$merchant_id " . $where;
		$arr = $this->activity_dao->query ( $sql );
		
		foreach ( $arr as $key => $value ) {
			if (! empty ( $value ['pics'] )) {
				$pics = json_decode ( $value ['pics'], true );
				$pics = imgUrl ( $pics [0] );
				$arr [$key] ['pics'] = $pics;
			
			}
		
		}
		if ($arr === false) {
			$this->jsonUtils->echo_json_msg ( 1, '数据异常...' );
			exit ();
		} else {
			
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		}
	}
	
	/**
	 * 商家修改活动
	 * 只有活动在进行中不能修改
	 */
	public function mod_activity() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$sessionid = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $sessionid );
		$name = isset ( $_POST ['name'] ) ? htmlspecialchars ( $_POST ['name'] ) : '';
		$categorys = isset ( $_POST ['category_ids'] ) ? htmlspecialchars ( $_POST ['category_ids'] ) : '';
		$cart_model = isset ( $_POST ['cart_model'] ) ? htmlspecialchars ( $_POST ['cart_model'] ) : '';
		$time = isset ( $_POST ['time'] ) ? htmlspecialchars ( $_POST ['time'] ) : '';
		$market_price = isset ( $_POST ['market_price'] ) ? htmlspecialchars ( $_POST ['market_price'] ) : '';
		$second_price = isset ( $_POST ['second_price'] ) ? htmlspecialchars ( $_POST ['second_price'] ) : '';
		$remain = isset ( $_POST ['remain'] ) ? htmlspecialchars ( $_POST ['remain'] ) : '';
		$start_time = isset ( $_POST ['start_time'] ) ? htmlspecialchars ( $_POST ['start_time'] ) : '';
		$end_time = isset ( $_POST ['end_time'] ) ? htmlspecialchars ( $_POST ['end_time'] ) : '';
		$valid_start_time = isset ( $_POST ['valid_start_time'] ) ? htmlspecialchars ( $_POST ['valid_start_time'] ) : '';
		$valid_end_time = isset ( $_POST ['valid_end_time'] ) ? htmlspecialchars ( $_POST ['valid_end_time'] ) : '';
		$date_limit = isset ( $_POST ['date_limit'] ) ? htmlspecialchars ( $_POST ['date_limit'] ) : ''; // 日期限制
		$is_reserve = isset ( $_POST ['reserve'] ) ? htmlspecialchars ( $_POST ['reserve'] ) : ''; // 是否需要预约
		$reserve_timeout = isset ( $_POST ['reserve_timeout'] ) ? htmlspecialchars ( $_POST ['reserve_timeout'] ) : ''; // 逾期是否保留
		$enjoy_other_preferential = isset ( $_POST ['enjoy_other_preferential'] ) ? htmlspecialchars ( $_POST ['enjoy_other_preferential'] ) : ''; // 是否享受其他优惠
		$detail = isset ( $_POST ['detail'] ) ? htmlspecialchars ( $_POST ['detail'] ) : '';
		
		if (empty ( $id )) {
			$this->jsonUtils->echo_json_msg ( 4, '活动id为空...' );
			exit ();
		}
		$rel = $this->activity_dao->where ( array (
				'id' => $id,
				'merchant_id' => $merchant_id 
		) )->getField ( 'id' );
		if (! rel) {
			$this->jsonUtils->echo_json_msg ( 4, '错误操作' );
		}
		if (! empty ( $name )) {
			$data ['name'] = $name;
		}
		if (! empty ( $categorys )) {
			$data ['category_ids'] = $categorys;
		}
		
		if (! empty ( $cart_model )) {
			$data ['cart_model'] = $cart_model;
		}
		
		if (! empty ( $time )) {
			$data ['time'] = $time;
		}
		
		if (! empty ( $market_price )) {
			$data ['market_price'] = $market_price;
		}
		
		if (! empty ( $second_price )) {
			$data ['second_price'] = $second_price;
		}
		
		if (! empty ( $remain )) {
			$data ['remain'] = $remain;
		}
		
		if (! empty ( $start_time )) {
			$data ['start_time'] = strtotime ( $start_time );
		}
		
		if (! empty ( $end_time )) {
			$data ['end_time'] = strtotime ( $end_time );
		}
		
		if (! empty ( $valid_start_time )) {
			$data ['valid_start_time'] = strtotime ( $valid_start_time );
		}
		
		if (! empty ( $valid_end_time )) {
			$data ['valid_end_time'] = strtotime ( $valid_end_time );
		}
		
		if ($data ['start_time'] >= $data ['end_time']) {
			$this->jsonUtils->echo_json_msg ( 4, '活动开始结束时间无效' );
			exit ();
		}
		if ($data ['valid_start_time'] >= $data ['valid_end_time']) {
			$this->jsonUtils->echo_json_msg ( 4, '活动有效期开始结束时间无效' );
			exit ();
		}
		if (! empty ( $date_limit )) {
			$data ['date_limit'] = $date_limit;
		}
		
		if (! empty ( $is_reserve )) {
			$data ['reserve'] = $is_reserve;
		}
		
		if (! empty ( $$reserve_timeout )) {
			$data ['reserve_timeout'] = $reserve_timeout;
		}
		
		if (! empty ( $enjoy_other_preferential )) {
			$data ['enjoy_other_preferential'] = $enjoy_other_preferential;
		}
		
		if (! empty ( $detail )) {
			$data ['detail'] = $detail;
		}
		
		if (! empty ( $valid_end_time )) {
			$data ['valid_end_time'] = strtotime ( $valid_end_time );
		}
		
		if (! empty ( $valid_end_time )) {
			$data ['valid_end_time'] = strtotime ( $valid_end_time );
		}
		
		if (! empty ( $name )) {
			$data ['valid_end_time'] = strtotime ( $valid_end_time );
		}
		
		$data ['effect'] = 0;
		
		if ($_FILES) {
			$arr = mul_upload ( '/Activity/',1 );
			if ($arr) {
				
				$data ['pics'] = json_encode ( $arr ); // 把多张图片数组格式转json保存数据库
					                                 // $this->service_dao->where("id=$id")->save($data1);
			}
		}
		$result = $this->activity_dao->where ( "id=$id" )->save ( $data );
		$this->jsonUtils->echo_json_msg ( 0, 'ok' );
		exit ();
	
	}
	// 商家删除活动
	public function del_activity() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$sessionid = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $sessionid );
		$r = $this->activity_dao->where ( array (
				'id' => $id,
				'merchant_id' => $merchant_id 
		) )->delete ();
		if ($r) {
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '删除失败！' );
			exit ();
		}
	}
	
	/**
	 * 获取活动详情 1未开始 2进行中 3已下架
	 */
	
	public function get_activity() {
		$id = ( int ) $_POST ['id'];
		$sessionid = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
		if (empty ( $merchant_id )) {
			if (! empty ( $sessionid )) {
				$merchant_id = $this->session_handle->getsession_userid ( $sessionid );
			} else {
				$this->jsonUtils->echo_json_msg ( 6, '商家id为空' );
			}
		}
		
		$arr = $this->activity_dao->where ( "id=$id " )->find ();
		if ($arr) {
			if ($arr ['pics']) {
				$json_obj = json_decode ( $arr ['pics'], true );
				$arr ['pics'] = imgUrl ( $json_obj );
			}
			$arr ['start_time'] = date ( 'Y-m-d H:i', $arr ['start_time'] );
			$arr ['end_time'] = date ( 'Y-m-d H:i', $arr ['end_time'] );
			$arr ['valid_start_time'] = date ( 'Y-m-d H:i', $arr ['valid_start_time'] );
			$arr ['valid_end_time'] = date ( 'Y-m-d H:i', $arr ['valid_end_time'] );
			$arr ['category_name'] = CategoryController::getCategoryNames ( $arr ['category_ids'] );
			$arr ['resttime'] = $arr ['end_time'] - time () > 0 ? $arr ['end_time'] - time () : 0;
			if ($arr ['effect'] == 0) { // 审核中 只能下架
				$arr ['instock'] = 0; // 0下架
			} else {
				if ($arr ['end_time'] < time ()) { // 已过期的活动
					$arr ['instock'] = 1; // 1上架
				} else {
					$arr ['instock'] = 0; // 0下架
				}
			}
			
			$arr ['merchant_name'] = MerchantController::getMerName ( $merchant_id );
			$star = MerchantController::getMerCommentStar ( $merchant_id );
			$arr ['service_quality'] = $star ['service_quality'];
			$arr ['service_attitude'] = $star ['service_attitude'];
			$arr ['merchant_setting'] = $star ['merchant_setting'];
			
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '获取失败！' );
			exit ();
		}
	}
	/**
	 * 下架
	 */
	public function remove_activity() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$sessionid = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $sessionid );
		
		$auth = $this->activity_dao->where ( array (
				'id' => $id,
				'merchant_id' => $merchant_id 
		) )->find ();
		if ($auth) {
		
			$time = time ();
			if ($auth ['end_time'] > $time) {
				$data = $this->activity_dao->where ( array (
						'id' => $id 
				) )->save ( array (
						'start_time' => $time,
						'end_time' => $time,
						'effect' => 1 
				) );
				if ($data) {
					$this->jsonUtils->echo_json_msg ( 0, 'ok' );
				} else {
					$this->jsonUtils->echo_json_msg ( 4, '数据异常' );
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 4, '已下架，无需下架' );
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '你无权操作' );
		}
	
	}
	/**
	* 上架
	*/
// 	public function instock_activity(){
// 		$id=isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
// 		$sessionid=isset($_POST['mer_session_id'])?htmlspecialchars($_POST['mer_session_id']):'';
// 		$merchant_id=$this->session_handle->getsession_userid($sessionid);
		
// 		$auth =$this->activity_dao->where(array('id'=>$id,'merchant_id'=>$merchant_id))->find();
// 		if($auth){
		
// 		$time = time();
		
// 		if($auth['end_time']>$time && $auth['end_time']>$auth['start_time']){
// 		$data = $this->activity_dao->where(array('id'=>$id))->save(array('effect'=>0));
// 		if($data){
// 		$this->jsonUtils->echo_json_msg(0, 'ok');
// 		}else{
// 		$this->jsonUtils->echo_json_msg(4, '数据异常');
// 		}
// 		}else{
// 		$this->jsonUtils->echo_json_msg(4, '无法上架 ，错误活动开始结束时间');
// 		}
		
// 		}else{
// 		$this->jsonUtils->echo_json_msg(4, '你无权操作');
// 		}
		
// 	}

}

?>