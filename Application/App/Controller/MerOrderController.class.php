<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 商家订单
 */
class MerOrderController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	private $session_dao;
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( 'order' );
		$this->session_dao = M ( 'member_session' );
	
	}
	
	/**
	 * *商家订单 status = 0 未完成
	 * status = 1 完成
	 * status = 2 失败
	 */
	public function merchant_order_list() {
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		$type = ( int ) $_POST ['type'];
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$map = "a.merchant_del = 0 and a.merchant_id = $merchant_id and ";
		if ($type == 0) {
			$map .='a.status = 0'; 
			$order ="a.addtime desc";
		} elseif ($type == 1) {
			$map .='a.status = 1'; 
			$order ="a.action_time desc";
		} elseif ($type == 2) {
			$map .='(a.status = 2 or a.status =3 )'; 
			$order ="a.action_time desc";
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '参数错误' );exit();
		}
		
		$data = $this->dao->table ( C ( 'DB_PREFIX' ) . 'order as a' )->field ( "me.nick_name,a.member_comment,a.merchant_comment,a.type,a.status,a.total_price,a.id,a.order_no,a.service_name,a.cart_id,from_unixtime(a.addtime,'%Y-%m-%d %H:%i') as addtime,from_unixtime(a.action_time,'%Y-%m-%d') as action_time" )->join ( C ( 'DB_PREFIX' ) . "member as me on a.member_id = me.id" )->where ( $map )->order ( $order)->page ( $page )->limit ( $num )->select ();
		$count = $this->dao->table ( C ( 'DB_PREFIX' ) . 'order as a' )->where($map)->count();
		if ($data) {
			$model = new Model ();
			foreach ( $data as $key => $row ) {
				$data [$key] ['cart_model'] = CartController::getCartModel($row['cart_id']);
				$data [$key] ['member_comment'] = $row ['member_comment'] > 0 ? '1' : '0';
				// 评论星级
				$data [$key] ['member_star'] = MerCommentController::getCommentStar ( $row ['order_no'] );
			}

		} else {
			$data = array();
		}
		$arr ['list'] = $data;
		$arr ['count'] =empty($count)?'0':$count; 
// 		$_SESSION['order_lists'] = $arr;
		$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
		exit ();
	
	}
	
	/**
	 * 订单详情
	 */
	public function get_order() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单ID为空...' );
			exit ();
		}
		
		$model = new Model ();
		$arr = $model->table ( C ( 'DB_PREFIX' ) . "order as a " )->field ( "b.header,b.nick_name,a.member_id,a.service_name,a.id,a.order_no,a.addtime,b.mobile,a.cart_id,a.cart_data,a.reach_time,a.total_price,a.total_time,a.type,a.sub_id,a.sub_data,a.status,a.merchant_comment,a.member_comment,a.merchant_remark,a.member_remark,ifnull(a.fail_content,'') as fail_content,b.mobile" )->join ( C ( 'DB_PREFIX' ) . "member as b on a.member_id = b.id" )->where ( "a.order_no = $order_no and a.merchant_id = $merchant_id and a.merchant_del = 0" )->find ();
		//echo $model->getLastSql();
		if ($arr) {
			//获取jid
			$jid = CommonController::getSystemUserid($arr['member_id'], 0);
			$cart = json_decode($arr['cart_data'],true);
			unset($arr['cart_data']);
			$arr ['system_user_id'] = $jid;
			$arr ['cart_model'] = $cart['cart_model'];
			$arr ['reach_time'] = date('Y-m-d H:i:s',$arr['reach_time']);
			$arr ['addtime'] = date('Y-m-d H:i:s',$arr['addtime']);
			$arr ['header'] = imgUrl ( $arr['header'] );
			// 商家是否评价
			if ($arr ['status'] != 0) { // 未完成订单 没有评价
				if ($arr ['merchant_comment'] > 0) {
					$sql = "select a.desc,c.header,a.service_attitude,c.merchant_name as nick_name,from_unixtime(a.addtime,'%Y-%m-%d %H:%i:%s') as addtime
	        		from " . C ( 'DB_PREFIX' ) . "comment as a
	        		left join " . C ( 'DB_PREFIX' ) . "merchant as c
	        		on a.merchant_id=c.id
	        		where a.order_no={$arr['order_no']} and type=2 limit 1";
					$arro = $this->dao->query ( $sql );
					
					$arro [0] ['header'] = imgUrl ( $arro [0] ['header'] );
					$arr ['merchant_comment_info'] = $arro [0];
				
				} else {
					$arr ['merchant_comment_info'] = '';
					$arr ['merchant_comment'] = '0';
				}
				// 用户是否评论
				
				if ($arr ['member_comment'] > 0) {
					$sql = "select a.desc,c.header,c.nick_name,a.service_attitude,a.service_quality,a.merchant_setting,a.pics,from_unixtime(a.addtime,'%Y-%m-%d %H:%i:%s') as addtime
		        	from " . C ( 'DB_PREFIX' ) . "comment as a  
		        	left join " . C ( 'DB_PREFIX' ) . "member as c 
		        	on a.member_id=c.id 
		        	where a.order_no={$arr['order_no']} and type=0 limit 1";
					$arro = $this->dao->query ( $sql );
					$arro [0] ['header'] = imgUrl ( $arro [0] ['header'] );
					$arro [0] ['pics'] = imgUrl (json_decode( $arro [0] ['pics'],true) );
					$arr ['member_comment_info'] = $arro [0];
				} else {
					$arr ['member_comment_info'] = '';
					$arr ['member_comment'] = '0';
				}
			
			} else {
				$arr ['merchant_comment'] = '0';
				$arr ['member_comment'] = '0';
				$arr ['merchant_comment_info'] = '';
				$arr ['member_comment_info'] = '';
			}
			
			$sub_data = json_decode ( $arr ['sub_data'], true );
			switch ($arr ['type']) { // 0 预约 1需求 2活动
				case '0' :
					$arr ['pics'] = imgUrl($sub_data ['pics']);
					$arr ['distance'] = '';
					$arr ['list'] = $sub_data ['list'];
					
					break;
				
				case '1' or '3' : // 需求
					$arr ['pics'] =  imgUrl($sub_data ['pics']);
					$arr ['distance'] = $sub_data ['distance'];
					$arr ['list'] = $sub_data ['list'];
					break;
				
				case '2' : // 活动
					$arr ['distance'] = '';
					$arr ['list'] = array ();
					break;
				default :
					$this->jsonUtils->echo_json_msg ( 4, '数据异常' );exit();
					break;
			}
			unset ( $arr ['sub_data'] );
		
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '该订单不存在...' );
			exit ();
		}
	}
	
	// 商家确认订单
	public function confirm_user_failed() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$mer_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$content = isset ( $_POST ['content'] ) ? htmlspecialchars ( $_POST ['content'] ) : '';
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单号为空...' );
			exit ();
		}
		if (empty ( $mer_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '请登录' );
			exit ();
		}
		if (empty ( $content )) {
			$this->jsonUtils->echo_json_msg ( 4, '违约内容为空' );
			exit ();
		}
		
		$data = $this->dao->where ( array (
				'order_no' => $order_no,
				'merchant_id' => $mer_id 
		) )->find ();
		if ($data) {
			if ($data ['status'] == 2)
				$this->jsonUtils->echo_json_msg ( 4, '订单已确认违约' );
			if ($data ['type'] == 2) { // 活动 可以随时确定
				$result = $this->dao->where ( array (
						'order_no' => $order_no 
				) )->save ( array (
						'status' => 2,
						'fail_content' => $content ,
						'action_time'=>time()
				) );
			
			} elseif ($data ['type'] == 1 || $data ['type'] == 0) {
				// 到店时间后才能确定违约
				$time = time ();
				if ($time > $data ['reach_time']) {
					$result = $this->dao->where ( array (
							'order_no' => $order_no 
					) )->save ( array (
							'status' => 2,
							'fail_content' => $content ,
							'action_time'=>time()
					) );
				} else {
					$this->jsonUtils->echo_json_msg ( 4, '只有过了预约时间后才能确定违约' );
					exit ();
				}
			
			}
			
			if ($result) {
				
				$this->jsonUtils->echo_json_msg ( 0, 'ok' );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '无权操作...' );
				exit ();
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '你无权限操作该订单' );
			exit ();
		}
		

	
	}
	
	
	public function delOrder() {
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$mer_id = $this->session_handle->getsession_userid ( $mer_session_id );
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单ID为空...' );
			exit ();
		}
		if (empty ( $mer_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '请登录' );
			exit ();
		}
	
		$data = $this->dao->where ( array (
				'order_no' => $order_no,
				'merchant_id' => $mer_id
		) )->find ();
		if ($data) {
			if ($data ['status'] != 0 &&$data['merchant_del'] =  1) {
				$arr ["merchant_del"] = 1;
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

}

?>