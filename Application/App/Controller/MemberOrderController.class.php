<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 会员订单
 */
class MemberOrderController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->dao = M ( 'order' );
		$this->session_handle = new \Org\Util\SessionHandle ();
	
	}
	
	//验证 订单数量
	public function auth_order_num() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$order =MemberOrderController::getOrderWithoutDone($member_id);
		if(!$order){
			$this->jsonUtils->echo_json_msg(4, '您已有5个未完成的订单，请完成后在继续下单');exit();
		}
		$this->jsonUtils->echo_json_msg(0, 'ok');exit();
	}
	
	// 预约项目
	// 提交订单
	public function submit_order() {
		
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
		$order_time = isset ( $_POST ['reach_time'] ) ? htmlspecialchars ( $_POST ['reach_time'] ) : '';
		$cart_id = isset ( $_POST ['cart_id'] ) ? htmlspecialchars ( $_POST ['cart_id'] ) : '';
		$service_ids = isset ( $_POST ['service_ids'] ) ? htmlspecialchars ( $_POST ['service_ids'] ) : '';
		$member_remark = isset ( $_POST ['remark'] ) ? htmlspecialchars ( $_POST ['remark'] ) : '';
		
		if (empty ( $merchant_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家ID为空...' );
			exit ();
		}
		if (empty ( $order_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '到店时间为空...' );
			exit ();
		}
		if (empty ( $cart_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车id为空...' );
			exit ();
		}
		if (empty ( $service_ids )) {
			$this->jsonUtils->echo_json_msg ( 4, '请选择所需服务项目...' );
			exit ();
		}
		$unix_time = strtotime ( $order_time );
		if ($unix_time  < time ()) {
			$this->jsonUtils->echo_json_msg ( 4, '预约时间必须大于当前时间' );
			exit ();
		}
	
		$order =MemberOrderController::getOrderWithoutDone($member_id);
		if(!$order){
			$this->jsonUtils->echo_json_msg(4, '您已有5个未完成的订单，请完成后在继续下单');exit();
		}
		$service = M ( "service" );
		$order_no = time () . rand ( 1000, 9999 ); // 订单号
		$total_price = 0;
		$total_time = 0;
		$service_ids_arr = explode ( ',', $service_ids );
		
		if ($_FILES) {
			$f_arr = mul_upload ( '/UserService/' ,1);
			if ($f_arr) {
				$d_data ['pics'] = $f_arr;
			}
		}else{
			$d_data ['pics'] = array();
		}
		foreach ( $service_ids_arr as $key => $value ) {
			
			$s_arr = $service->field ( "cat_id as id,price,name,timeout as time" )->where ( "id=$value and merchant_id = $merchant_id" )->find ();
			if ($s_arr) {
				$total_price = $total_price + $s_arr ['price'];
				$total_time = $total_time + $s_arr ['time'];
				$d_data ['list'] [$key] = $s_arr;
				$d_data ['list'] [$key] ['is_server'] = 1;
				$service_name [] = $s_arr ['name'];
			} else {
				$this->jsonUtils->echo_json_msg ( 4, '提交的服务项目有误' );
				exit ();
			}
		
		}
		
		$data ['order_no'] = $order_no;
		$data ['service_name'] = implode ( '、', $service_name );
		$data ['status'] = 0;
		$data ['merchant_id'] = $merchant_id;
		$data ['member_id'] = $member_id;
		$data ['type'] = 0; // 服务订单
		$data ['goods_count'] = 1;
		$data ['unit_price'] = $total_price;
		$data ['total_price'] = $total_price;
		$data ['total_time'] = $total_time;
		
		$data ['sub_data'] = json_encode ( $d_data );
		$data ['reach_time'] = $unix_time;
		$data ['cart_id'] = $cart_id;
		$data ['cart_data'] = MemberDemandController::getcart ( $cart_id );
		$data ['addtime'] = time ();
		$data ['action_time'] = time ();
		$data ['member_remark'] = $member_remark;
		$data ['merchant_remark'] = '';
		
		$result = $this->dao->add ( $data );
		
		if ($result) {
			$this->jsonUtils->echo_json_msg ( 0, '提交订单成功...' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '提交订单失败...' );
			exit ();
		}
	
	}
	
	
	// 订单列表
	public function order_list() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '0';
		
		$sql = "select a.order_no,a.status,from_unixtime(a.addtime,'%Y-%m-%d') as addtime,from_unixtime(a.action_time,'%Y-%m-%d') as action_time,
		b.merchant_name,a.service_name, a.total_price,a.type,a.member_comment,b.header 
		from " . C ( 'DB_PREFIX' ) . "order as a 
		left join " . C ( 'DB_PREFIX' ) . "merchant as b on a.merchant_id=b.id 
		where a.member_id=$member_id and a.member_del= 0 ";
		if ($type == 0) { // 未完成订单
			$sql = $sql . " and  a.status=0";
			$order = "a.addtime desc";
		} elseif ($type == 1) { // 已经完成
			$sql = $sql . "  and a.status=1";
			$order = "a.action_time desc";
		} elseif ($type == 2) {
			$sql = $sql . "  and (a.status= 2 or a.status = 3 )"; // 失败
			$order = "a.action_time desc";
		} else {
			$this->jsonUtils->echo_json_msg ( 4, 'type错误' );
			exit ();
		}
		$limit = ($page - 1) * $num . ',' . $num;
		$sql = $sql . " order by $order limit $limit";
		$arr = $this->dao->query ( $sql );
		if ($arr) {
			
			foreach ( $arr as $key => $value ) {
				$arr [$key] ['header'] = imgUrl ( $value ['header'] );
			
			}
			if ($type == 2) {
				foreach ( $arr as $key => $row ) {
					$arr [$key] ['member_comment'] = $row ['member_comment'] > 0 ? '1' : '0';
				}
			}
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		} else {
			$arr = array ();
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		}
	
	}
	
	// 订单详情
	public function get_order() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单号为空...' );
			exit ();
		}
		$arr = $this->dao->table(C('DB_PREFIX')."order as a")
		->field ( "a.order_no,a.addtime,b.id as merchant_id,b.header,b.merchant_name,a.reach_time,a.cart_data,a.member_remark,a.merchant_remark,a.total_price,a.total_time,a.type,a.sub_data,a.member_comment,a.merchant_comment,a.fail_content,a.status,b.tel" )
		->join(C('DB_PREFIX').'merchant as b on a.merchant_id = b.id')
		->where ( "a.order_no=$order_no and a.member_id = $member_id " )->find ();
		if ($arr) {
			$cart = json_decode ( $arr ['cart_data'], true );
			unset ( $arr ['cart_data'] );
			$arr ['system_user_id']  = CommonController::getSystemUserid($arr ['merchant_id'],2);
			$arr ['header'] = imgUrl($arr ['header'] );
			$arr ['cart_model'] = $cart ['cart_model'];
			$arr ['reach_time'] = date ( 'Y-m-d H:i:s', $arr ['reach_time'] );
			$arr ['addtime'] = date ( 'Y-m-d H:i:s', $arr ['addtime'] );
			// $arr ['header'] = imgUrl ( $arr ['header'] );
			$model = new Model ();
			$sub_data = json_decode ( $arr ['sub_data'], true );
			switch ($arr ['type']) { // 0 预约 1需求 2活动
				case '0' :
					$arr ['pics'] = imgUrl (  $sub_data ['pics'] );
				//	$arr ['distance'] = '';
					$arr ['list'] = $sub_data ['list'];
					
					break;
				
				case '1' or '3' : // 需求
					$arr ['pics'] = imgUrl ( $sub_data ['pics'] );
				//	$arr ['distance'] = $sub_data ['distance'];
					$arr ['list'] = $sub_data ['list'];
					break;
				
				case '2' : // 活动
				//	$arr ['distance'] = '';
					$arr ['list'] = array ();
					break;
				default :
					$this->jsonUtils->echo_json_msg ( 4, '数据异常' );
					exit ();
					break;
			}
			unset ( $arr ['sub_data'] );
			if ($arr ['status'] != 0) {
				if ($arr ['member_comment'] > 0) {
					$sql = "select a.desc,c.header,c.nick_name,a.pics,from_unixtime(a.addtime,'%Y-%m-%d %H:%i:%s') as addtime,
					a.service_attitude,a.service_quality,a.merchant_setting
					from " . C ( 'DB_PREFIX' ) . "comment as a  left join
					" . C ( 'DB_PREFIX' ) . "member as c on a.member_id=c.id
					where a.order_no={$arr['order_no']} and type=0 limit 1";
					$arro = $this->dao->query ( $sql );
					$arro [0] ['header'] = imgUrl ( $arro [0] ['header'] );
					$arro [0] ['pics'] = imgUrl ( json_decode($arro [0] ['pics'],true) );
					$arr ['member_comment'] = '1';
					$arr ['member_comment_info'] = empty($arro [0])?'':$arro[0];
				
				} else {
					$arr ['member_comment'] = '0';
					$arr ['member_comment_info'] = '';
				
				}
				if ($arr ['merchant_comment'] > 0) {
					$sql = "select a.desc,c.header,a.pics,c.merchant_name as nick_name,a.service_attitude,from_unixtime(a.addtime,'%Y-%m-%d %H:%i:%s') as addtime 
					from " . C ( 'DB_PREFIX' ) . "comment as a  left join " . C ( 'DB_PREFIX' ) . "merchant as c 
					on a.merchant_id=c.id where a.order_no={$arr['order_no']} and type=2 limit 1";
					$arro = $this->dao->query ( $sql );
					$arro [0] ['header'] = imgUrl ( $arro [0] ['header'] );
					$arro [0] ['pics'] = imgUrl ( json_decode($arro [0] ['pics'],true) );
					$arr ['merchant_comment'] = '1';
					$arr ['merchant_comment_info'] = empty($arro [0])?'':$arro[0];
				} else {
					$arr ['merchant_comment'] = '0';
					$arr ['merchant_comment_info'] = '';
				
				}
			
			} else {
				$arr ['merchant_comment_info'] = '';
				$arr ['member_comment_info'] = '';
			}
			
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '该订单不存在..' );
			exit ();
		}
	
	}
	// 确认订单
	public function confirm_order() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$member_session_id = isset ( $_POST ['member_session_id'] ) ? htmlspecialchars ( $_POST ['member_session_id'] ) : '';
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单ID为空...' );
			exit ();
		}
		if (empty ( $member_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '请登录' );
			exit ();
		}
		
		$data = $this->dao->where ( array (
				'order_no' => $order_no,
				'member_id' => $member_id 
		) )->find ();
		if ($data) {
			if ($data ['status'] == 1) {
				$this->jsonUtils->echo_json_msg ( 1, '此订单已完成...' );
				exit ();
			} else {
				$arr ["status"] = 1;
				$arr ["action_time"] = time();
				$result = $this->dao->where ( array (
						'order_no' => $order_no 
				) )->save ( $arr );
				if ($result) {
					// 订单成功 如果为保养订单 需记录到保养记录表
					if ($data ['type'] == 3) {
						$sub_data = json_decode ( $data ['sub_data'], true );
						$param = $sub_data ['param'];
						
						MemberDemandController::addMyMaintain ( $member_id, $data ['cart_id'], $param ['km'], $param ['category_ids'], time () );
					}
// 					//聊天内推送 done
// 					$jid = CommonController::getJid($data['merchant_id'], 2);
// 					$xmpp = D('XmppApi');
// 					$xmpp ->requestPush(3, array($jid), array('demand_id'=>$data['sub_id']));
					$this->jsonUtils->echo_json_msg ( 0, 'ok' );
					exit ();
				} else {
					$this->jsonUtils->echo_json_msg ( 1, '确认错误...' );
					exit ();
				}
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '你无权限操作该订单' );
			exit ();
		}
	
	}
	/**
	 * 取消订单 用户取消订单 仅限在未完成订单
	 */
	public function cancelOrder() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$cancel_reason = isset ( $_POST ['cancel_reason'] ) ? htmlspecialchars ( $_POST ['cancel_reason'] ) : '';
		$member_session_id = isset ( $_POST ['member_session_id'] ) ? htmlspecialchars ( $_POST ['member_session_id'] ) : '';
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单ID为空...' );
			exit ();
		}
		if (empty ( $cancel_reason )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单失败原因为空...' );
			exit ();
		}
		if (empty ( $member_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '请登录' );
			exit ();
		}
		
		$data = $this->dao->where ( array (
				'order_no' => $order_no,
				'member_id' => $member_id 
		) )->find ();
		if ($data) {
			if ($data ['status'] == 1) {
				$this->jsonUtils->echo_json_msg ( 1, '此订单已完成...' );
				exit ();
			} elseif ($data ['status'] == 2) {
				$this->jsonUtils->echo_json_msg ( 1, '订单已被商家确定违约' );
				exit ();
			} elseif ($data ['status'] == 3) {
				$this->jsonUtils->echo_json_msg ( 1, '订单已取消' );
				exit ();
			} elseif ($data ['status'] == 0) {
				$arr ["status"] = 3;
				$arr ['cancel_reason'] = $cancel_reason;
				$arr ['action_time'] = time();
				$result = $this->dao->where ( array (
						'order_no' => $order_no 
				) )->save ( $arr );
				if ($result) {
					$this->jsonUtils->echo_json_msg ( 0, 'ok' );
					exit ();
				} else {
					$this->jsonUtils->echo_json_msg ( 1, '确认错误...' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '订单错误' );
				exit ();
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '你无权限操作该订单' );
			exit ();
		}
	}
	public function delOrder() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$member_session_id = isset ( $_POST ['member_session_id'] ) ? htmlspecialchars ( $_POST ['member_session_id'] ) : '';
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单ID为空...' );
			exit ();
		}
		if (empty ( $member_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '请登录' );
			exit ();
		}
		
		$data = $this->dao->where ( array (
				'order_no' => $order_no,
				'member_id' => $member_id
		) )->find ();
		if ($data) {
			if ($data ['status'] != 0 &&$data['member_del'] =  1) {
				$arr ["member_del"] = 1;
				$result = $this->dao->where ( array (
						'order_no' => $order_no
				) )->save ( $arr );
				if ($result) {
					$this->jsonUtils->echo_json_msg ( 0, 'ok' );
					exit ();
				} else {
					$this->jsonUtils->echo_json_msg ( 1, '删除错误...' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '无需删除' );
				exit ();
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '你无权限操作该订单' );
			exit ();
		}
		
		
	}
	/**
	 * 查询未完成订单数量 
	 * return bool
	 */
	static public function getOrderWithoutDone($member_id){
		$db = M('Order');
		$data = $db ->where(array('member_id'=>$member_id,'status'=>0))->field('id')->select();
		if($data === false){
			return false;
		}else{
			$count = count($data);
			if($count >= 16){
				return false;
			}else{
				return true;
			}
		}
	}

}

?>