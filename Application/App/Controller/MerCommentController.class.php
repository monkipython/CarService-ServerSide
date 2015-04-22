<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
use Think\Log;

/**
 * 商家评论
 */
class MerCommentController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	private $session_dao;
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( 'comment' );
		$this->session_dao = M ( 'member_session' );
	
	}
	/**
	 * 商家评论列表
	 * type =0 用户对商家的评价
	 * type= 2 商家对用户的评价
	 */
	
	public function merchant_comment_list() {
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '0';
		$mer_session_id = $_POST ['mer_session_id'];
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$limit = ($page - 1) * $num . ',' . $num;
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		
		if ($type == 0) {
			$sql = "select c.service_name,a.desc,from_unixtime(a.addtime,'%Y-%m-%d') as addtime,a.service_attitude,a.service_quality,a.merchant_setting,a.order_no,b.nick_name,c.status from 
         	" . C ( 'DB_PREFIX' ) . "comment as a 
         	left join " . C ( 'DB_PREFIX' ) . "member as b 
         	on a.member_id=b.id 
         	left join " . C ( 'DB_PREFIX' ) . "order as c 
         	on c.order_no = a.order_no 
         	where a.merchant_id=$merchant_id and a.type = 0 
         	order  by a.addtime desc limit $limit";
			$arr = $this->dao->query ( $sql );
			
			if ($arr === false) {
				
				$this->jsonUtils->echo_json_msg ( 1, '暂无评论记录...' );
				exit ();
			} else {
				if (! $arr) {
					$arr = array ();
				} else {
					foreach ( $arr as $key => $row ) {
						
						$arr [$key] ['service_star'] = (string)ceil( ($row ['service_attitude'] + $row ['service_quality'] + $row ['merchant_setting']) / 3) ;
						
						unset ( $arr [$key] ['service_attitude'] );
						unset ( $arr [$key] ['service_quality'] );
						unset ( $arr [$key] ['merchant_setting'] );
					
					}
				}
				$data ['list'] = $arr;
				$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
				exit ();
			
			}
		} elseif ($type == 2) {
			
			$sql = "select c.service_name,a.desc,from_unixtime(a.addtime,'%Y-%m-%d') as addtime,a.order_no,b.nick_name,a.service_attitude as service_star,c.status from
         	" . C ( 'DB_PREFIX' ) . "comment as a
         	left join " . C ( 'DB_PREFIX' ) . "member as b
         	on a.member_id=b.id 
         	left join " . C ( 'DB_PREFIX' ) . "order as c 
         	on c.order_no = a.order_no  
         	where a.merchant_id=$merchant_id and a.type = 2
         	order by a.addtime desc limit $limit";
			$arr = $this->dao->query ( $sql );
			
			if ($arr === false) {
				$this->jsonUtils->echo_json_msg ( 1, '暂无回复记录...' );
				exit ();
			} else {
				if (! $arr){
					$arr = array ();
				}
					
				
				$data ['list'] = $arr;
				$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
				exit ();
			}
		}
	
	}
	// 商家评论
	public function comment() {
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		//$service_attitude = isset ( $_POST ['service_attitude'] ) ? htmlspecialchars ( $_POST ['service_attitude'] ) : '';
		if (empty ( $merchant_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家ID为空...' );
			exit ();
		}
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$content = isset ( $_POST ['content'] ) ? htmlspecialchars ( $_POST ['content'] ) : '';
		
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单id为空...' );
			exit ();
		}
		if (empty ( $content )) {
			$this->jsonUtils->echo_json_msg ( 4, '评论内容为空...' );
			exit ();
		}
		$db = M ( 'Order' );
		$dat = $db->where ( array (
				'order_no' => $order_no,
				'merchant_id' => $merchant_id 
		) )->find ();
		if ($dat === false) {
			$this->jsonUtils->echo_json_msg ( 4, '无权操作' );
		} else {
			
			if ($dat ['merchant_comment'] == 1) {
				$this->jsonUtils->echo_json_msg ( 4, '你已经评价过该订单' );
			}
		
		}
		
		$data ['service_quality'] = 0;
		$data ['service_attitude'] = 0;
		$data ['merchant_setting'] = 0;
		$data ['merchant_id'] = $merchant_id;
		$data ['member_id'] = $dat ['member_id'];
		$data ['type'] = 2; // 商户评论
		$data ['order_no'] = $order_no;
		$data ['desc'] = $content;
		$data ['addtime'] = time ();
		$data ['pics'] = "[]";
		$result = $this->dao->add ( $data );
		
		if ($result) {
			if ($_FILES) {
				$arr = mul_upload ( '/Comment/',2 );
				if ($arr) {
					
					$data1 ['pics'] = json_encode ( $arr ); // 把多张图片数组格式转json保存数据库
					$this->service_dao->where ( "id=$result" )->save ( $data1 );
				}
			
			}
			$db->where ( array (
					'id' => $dat ['id'] 
			) )->save ( array (
					'merchant_comment' => '1' 
			) );
			
			$this->jsonUtils->echo_json_msg ( 0, '评论成功!' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '评论失败...' );
			exit ();
		}
	
	}
	
	// 商家回复
	public function merchant_reply() {
		
		$mer_session_id = $_POST ['mer_session_id'];
		$id = $_POST ["id"];
		$content = $_POST ["content"];
		$order_no = $_POST ['order_no'];
		
		if (empty ( $content )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家回复内容为空...' );
			exit ();
		}
		if (empty ( $order_no )) {
			$this->jsonUtils->echo_json_msg ( 4, '订单号为空...' );
			exit ();
		}
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$data ['parent_id'] = $id;
		$data ['desc'] = $content;
		$data ['merchant_id'] = $merchant_id;
		$data ['addtime'] = time ();
		$result = $this->dao->add ( $data );
		if ($result) {
			$this->jsonUtils->echo_json_msg ( 0, '回复成功...' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '回复失败,请稍后进行尝试...' );
			exit ();
		}
	
	}
	/**
	 * 获取用户评价的星级
	 * 
	 * @param unknown_type $order_id        	
	 */
	static function getCommentStar($order_id) {
		$db = M ( 'Comment' );
		$data = $db->where ( array (
				'order_no' => $order_id,
				'type' => 0 
		) )->find ();
		$star = floatval ( round ( ($data ['service_attitude'] + $data ['service_quality'] + $data ['merchant_setting']) / 3, 1 ) );
		if (empty ( $star )) {
			$star = 0;
		}
		return $star;
	}

}

?>