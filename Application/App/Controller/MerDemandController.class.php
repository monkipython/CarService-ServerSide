<?php
namespace App\Controller;
use Think\Log;
use Think\Controller;
use Think\Model;

/**
 * 商家订单
 */
class MerDemandController extends CommonController {
	
	private $jsonUtils;
	private $dao;
	private $bidding_dao;
	private $session_handle; // session 处理类
	private $session_dao;
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( 'member_demand' );
		$this->bidding_dao = M ( 'merchant_bidding' );
		$this->session_dao = M ( 'member_session' );
	
	}
	
	/**
	 * 商户抢单 未报价 已报价
	 * 1 未报价
	 * 2 已报价
	 */
	public function member_demand_list() {
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '1'; // 默认未报价
		$str = ($page - 1) * $num . ',' . $num;
	
		// 商家没有需求项目之一则不显示项目
		if ($type == 1) {
			$sql = "SELECT c.id,c.title as service_name,c.status as demand_status,c.is_bidding as offer_price_num,c.title,from_unixtime(c.addtime,'%Y-%m-%d %H:%i') as addtime,c.longitude,c.latitude,c.expire_time, d.id as member_id,d.nick_name,d.header
				 FROM  `" . C ( 'DB_PREFIX' ) . "demand_merchant_enable` as a 
				 left join `" . C ( 'DB_PREFIX' ) . "member_demand` as c 
				 on a.demand_id = c.id 
				left join `" . C ( 'DB_PREFIX' ) . "member` as d
				on c.member_id = d.id
				where  a.merchant_id = $merchant_id and c.merchant_id = 0 and 
				 c.id not in  (select demand_id from ycbb_merchant_bidding  where merchant_id =$merchant_id  group by demand_id)
				order by c.addtime desc 
		 		 limit $str";
			$sql_count =   "SELECT c.id
				 FROM  `" . C ( 'DB_PREFIX' ) . "demand_merchant_enable` as a 
				 left join `" . C ( 'DB_PREFIX' ) . "member_demand` as c 
				 on a.demand_id = c.id 
				where  a.merchant_id = $merchant_id and c.merchant_id = 0 and 
				 c.id not in  (select demand_id from ycbb_merchant_bidding  where merchant_id =$merchant_id  group by demand_id)
				";
			
		} elseif ($type == 2) {
			$sql = "SELECT c.id,c.title as service_name,c.expire_time,from_unixtime(c.addtime,'%Y-%m-%d %H:%i') as addtime,c.status as demand_status,c.longitude,c.latitude, d.id as member_id,d.nick_name,d.header
	  		  FROM ycbb_merchant_bidding  as b
	  		  left join `" . C ( 'DB_PREFIX' ) . "member_demand` as c
	  		  on b.demand_id = c.id
	  		  left join `" . C ( 'DB_PREFIX' ) . "member` as d
	  		  on c.member_id = d.id
	  		  where  b.merchant_id=$merchant_id  and c.merchant_id != $merchant_id
	  		  group by b.demand_id
	  		  order by b.addtime desc
	  		  limit $str";
			
			$sql_count = "SELECT c.id as count 
			FROM ycbb_merchant_bidding  as b
			left join `" . C ( 'DB_PREFIX' ) . "member_demand` as c
			on b.demand_id = c.id
			where  b.merchant_id=$merchant_id and c.merchant_id != $merchant_id
			group by b.demand_id
			";
			
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '参数错误' );
			exit ();
		}
		$count = $this->dao->query($sql_count);
		$num = count($count);
		$arr = $this->dao->query ( $sql );
	
		if ($arr && $num != 0) {
			
			$model = new Model ();
			$merchant = M ( "merchant" );
			$mer_arr = $merchant->field ( "longitude,latitude" )->where ( "id=$merchant_id" )->select ();
			foreach ( $arr as $key => $value ) {
				$arr [$key] ['header'] = imgUrl ( $value ['header'] );
				$longitude = $arr [$key] ['longitude']; // 用户发布需求的经纬度
				$latitude = $arr [$key] ['latitude'];
				$is_expire= time()-$value['expire_time'] > 0 ? '1':'0';
				if($is_expire){
					if($arr[$key]['demand_status'] == 0){
						$arr[$key]['demand_status'] = '3';//过期
					}
				}
				// 计算商家店铺和用户需求距离
				$arr [$key] ['distance'] = getDistance ( $latitude, $longitude, $mer_arr [0] ['latitude'], $mer_arr [0] ['longitude'] );
				unset ( $arr [$key] ['longitude'] );
				unset ( $arr [$key] ['latitude'] );
				
// 				$bidding_count = $this->bidding_dao->where ( array (
// 						'demand_id' => $value ['id'] 
// 				) )->group ( 'merchant_id' )->count ();
				if ($type == 2) {
					$arr [$key] ['price'] = $this->bidding_dao->where ( array (
							'demand_id' => $value ['id'],
							'merchant_id' => $merchant_id 
					) )->sum ( 'price' );
				}
			//	$arr [$key] ['offer_price_num'] = empty ( $bidding_count ) ? '0' : $bidding_count;
				
			
			
			}
			$data ['list'] = $arr;
			$data['count'] = $num;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_data ( 0, 'ok',array('list'=>array(),'count'=>'0') );
			exit ();
		}
	}
	
	/**
	 * 用户需求详情 type=1 未报价 type=2已报价
	 */
	public function get_member_demand() {
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$id = ( int ) $_POST ['id'];
		$bidd = M ( 'MerchantBidding' )->where(array('demand_id'=>$id,'merchant_id'=>$merchant_id))->find();
		if($bidd){
			$type = 2;
		}else{
			$type = 1 ;
		}
		if (empty ( $id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车主需求ID为空...' );
			exit ();
		}
		
		$arr = $this->dao->query ( "select a.id,a.status as demand_status ,from_unixtime(a.reach_time,'%Y-%m-%d %H:%i') as reach_time ,a.description,a.pics,a.member_id,a.longitude,a.latitude,a.cart_data,b.nick_name,b.header,a.publish,a.expire_time from " . C ( 'DB_PREFIX' ) . "member_demand as a left join " . C ( 'DB_PREFIX' ) . "member as b on a.member_id = b.id   where a.id=$id" );
		
		if ($arr) {
		
			$arr [0] ['header'] = imgUrl ( $arr [0] ['header'] );
			$cart = json_decode($arr[0]['cart_data'],true);
			$arr [0] ['cart_model'] = $cart['cart_model'];
			$model = new Model ();
			$merchant = M ( 'Merchant' );
			$mer_arr = $merchant->field ( "longitude,latitude" )->where ( "id=$merchant_id" )->select ();
			$longitude = $arr [0] ['longitude']; // 用户发布需求的经纬度
			$latitude = $arr [0] ['latitude'];
			$demand_id = $arr [0] ['id'];
			// 计算商家店铺和用户需求距离
			$arr [0] ['distance'] = getDistance ( $latitude, $longitude, $mer_arr [0] ['latitude'], $mer_arr [0] ['longitude'] );
			$is_expire= time()-$arr[0]['expire_time'] > 0 ? '1':'0';
			if($is_expire){
				if($arr[0]['demand_status'] == 0){
					$arr[0]['demand_status'] = '3';//过期
				}
			}
			// 服务项目信息
			if($arr[0]['publish'] == 0){
				$category = "category";
			}elseif($arr[0]['publish'] == 1){
				$category = "car_maintain_category";
			}else{
				$this->jsonUtils->echo_json_msg ( 4, '订单有误' );
				exit ();
			}
			//
			$s_arr = $model->query ( "select b.name,b.id from " . C ( 'DB_PREFIX' ) . "member_demand_subitems as a left join " . C ( 'DB_PREFIX' ) . "$category as b on a.category_id=b.id  where a.demand_id=$demand_id " );
			$perlist = array ();
			if ($s_arr) {
				if ($type == 1) {
					// 未报价 
					$arr [0] ['merchant_remark'] = '';
				} elseif ($type == 2) {
					// 已报价 bidding
					$merchant_remark =  M ( 'MerchantBiddingRemark' )->where ( array (
							'demand_id' => $id ,'merchant_id'=>$merchant_id
					) )->getField ( 'remark' );
					$arr [0] ['merchant_remark'] =!empty($merchant_remark)?$merchant_remark:'';
					$map['demand_id']= $id;
					$map ['merchant_id'] = $merchant_id;
					$alert_price = M ( 'MerchantBidding' )->where ( $map )->field ( 'id as bidding_id,price,sub_id as cat_id,out_time as time' )->select ();
					foreach ( $alert_price as $tem ) {
						$price [$tem ['cat_id']] = $tem;
					}
					
					
				} else {
					$this->jsonUtils->echo_json_msg ( 4, '参数不全.' );
					exit ();
				}
				// 区分是报价为0 还是未报价，-1标识未报价
				foreach ( $s_arr as $key => $row ) {
					$perlist [$key] ['category_id'] = $row ['id'];
					$perlist [$key] ['server_name'] = $row ['name'];
					
						$perlist [$key] ['is_server'] = 1;
						$perlist [$key] ['price'] = !isset ( $price [$row ['id']] ['price'] ) ? '-1': $price [$row ['id']] ['price'];
						$perlist [$key] ['bidding_id'] = !isset  ( $price [$row ['id']] ['bidding_id'] ) ? '-1' : $price [$row ['id']] ['bidding_id'];
						$perlist [$key] ['time'] = !isset  ( $price [$row ['id']] ['time'] ) ? '-1' : $price [$row ['id']] ['time'];
				}
			
			}
			$arr [0] ['list'] = $perlist;
			if ($arr [0] ['pics']) {
				$json_obj = json_decode ( $arr [0] ['pics'],true );
				$arr [0] ['pics'] = imgUrl ( $json_obj );
			} else {
				$arr [0] ['pics'] = '';
			}
			$arr [0] ['cart_model'] == null ? '' : $arr [0] ['cart_model'];
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '没有该用户需求...' );
			exit ();
		}
	
	}
	
	/**
	 * 商家报价 add
	 * 修改报价
	 */
	public function merchant_offer_price() {
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$demand_id = ( int ) $_POST ['id'];
		$bidding_ids = isset ( $_POST ['bidding_ids'] ) ? htmlspecialchars ( $_POST ['bidding_ids'] ) : '';
		$category_ids = isset ( $_POST ['category_ids'] ) ? htmlspecialchars ( $_POST ['category_ids'] ) : '';
		$outtime = isset ( $_POST ['times'] ) ? htmlspecialchars ( $_POST ['times'] ) : '';
		$prices = isset ( $_POST ['prices'] ) ? htmlspecialchars ( $_POST ['prices'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$merchant_remark = isset ( $_POST ['merchant_remark'] ) ? htmlspecialchars ( $_POST ['merchant_remark'] ) : '';
		
		if (empty ( $demand_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车主需求ID为空...' );
			exit ();
		}
		$d_arr = $this->dao->where ( "id=$demand_id" )->find ();
		$jid = $this->getSystemUserid($d_arr['member_id'],0);
		if ($d_arr['merchant_id'] !=0) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求已经选择商家...' );
			exit ();
		}
		if (time() > $d_arr['expire_time']) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求已过期...' );
			exit ();
		}
		if ($d_arr['status'] == 2 ) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求已取消...' );
			exit ();
		}
		if($d_arr['is_bidding'] >= C('PUSH_MAX_BINDING_NUM')){
			$this->jsonUtils->echo_json_msg ( 1, '该需求已超过'.C('PUSH_MAX_BINDING_NUM').'家报价' );
			exit ();
		}
		if (empty ( $bidding_ids )) {
			$this->jsonUtils->echo_json_msg ( 4, '报价id为空...' );
			exit ();
		}
		if (empty ( $category_ids )) {
			$this->jsonUtils->echo_json_msg ( 4, '需求服务项目ID为空...' );
			exit ();
		}
		if (empty ( $outtime )) {
			$this->jsonUtils->echo_json_msg ( 4, '耗时为空...' );
			exit ();
		}
		if (empty ( $prices )) {
			$this->jsonUtils->echo_json_msg ( 4, '报价为空...' );
			exit ();
		}
		//商家是否能对该需求进行报价
		//账号id 等于 0 未最高权限账号 无需权限校对
		if($merchant_id != 0){
			$authBidding = M('DemandMerchantEnable')->where(array('merchant_id'=>$merchant_id,'demand_id'=>$demand_id))->getField('id');
			if(!$authBidding){
				$this->jsonUtils->echo_json_msg ( 4, '你无权限对此进行操作' );
				exit ();
			}
		}
		if(!empty($merchant_remark)){
			// 添加商家备注
			$data_re ['remark'] = $merchant_remark;
			$data_re ['demand_id'] = $demand_id;
			$data_re ['merchant_id'] = $merchant_id;
			$bidding_remark = M ( 'MerchantBiddingRemark' );
			$is_set = $bidding_remark->where(array('demand_id'=>$demand_id,'merchant_id'=>$merchant_id))->getField('id');
			if($is_set){
				M ( 'MerchantBiddingRemark' )->where ( array (
				'id' => $is_set
				) )->save ( array (
				'remark' => $merchant_remark
				) );
			}else{
				$insert = $bidding_remark->add ( $data_re ); // 商家备注
			}
		}
		$bidding_ids = explode ( ',', $bidding_ids );
		$ids_arr = explode ( ",", $category_ids );
		$outtime = explode ( ',', $outtime );
		$price_arr = explode ( ",", $prices );
		foreach ($price_arr as $key =>$row){
			if($row == -1 || $row ==''){
				unset($price_arr[$key]);
			}
		}
		
		$bidding_ids_num = count ( $bidding_ids );
		$ids_arr_num = count ( $ids_arr );
		$outtime_num = count ( $outtime );
		$price_arr_num = count ( $price_arr );
		if(empty($price_arr_num)) {
			$this->jsonUtils->echo_json_msg(4, '请报价');exit();
		}
		if ($bidding_ids_num == $ids_arr_num && $ids_arr_num == $outtime_num && $outtime_num >= $price_arr_num) {
			
			$data ['addtime'] = time ();
			$data ['demand_id'] = $demand_id;
			$data ['merchant_id'] = $merchant_id;
			$total_price = 0;
			$total_time = 0;
			// 查询 商家已有的服务 ，商家未有的服务 不允许报价
		//	$server_ids = CommonController::getServerListByMer ( $merchant_id );
			foreach ( $ids_arr as $key => $value ) {
				//if (in_array ( $value, $server_ids )) {
					$data ['price'] = $price_arr [$key];
// 					if ($data ['price'] == - 1 || $data ['price'] ==='') {
// 						continue;
// 					} else {
						$total_price +=$price_arr[$key];
						$total_time +=$outtime[$key];
						$data ['out_time'] = $outtime [$key];
						$data ['sub_id'] = $value;
						if ($bidding_ids [$key] == '-1') {
							$result = $this->bidding_dao->add ( $data );
						} else {
							$result = $this->bidding_dao->where ( array (
									'id' => $bidding_ids [$key],
									'merchant_id' => $merchant_id 
							) )->save ( array (
									'out_time' => $outtime [$key],
									'price' => $price_arr [$key] 
							) );
						}
						if ($result === false) {
							$data ['bidding_id'] = $bidding_ids [$key]; // 检测错误数据
							$this->jsonUtils->echo_json_msg ( 1, '报价失败！' . json_encode ( $data ) );
							exit ();
						}
					
					//}
				//}
			}
			$data['total_price'] = $total_price;
			$data['total_time'] = $total_time;
			//云推送
			$jpush = new \App\Model\JpushModel();
			$jpush->user = 0;
			$jpush ->push(2, array($jid), $data);
			//聊天内推送
			$xmpp = new \App\Model\XmppApiModel();
			$xmpp ->requestPush(2, array($jid), $data);
	
			
			
// 			if (!empty ( $d_arr ['is_bidding'] )) {
				M ( 'MemberDemand' )->where ( array (
						'id' => $demand_id 
				) )->setInc ( 
						'is_bidding' 
				);
// 			}
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '参数错误！' );
			exit ();
		}
	
	}
	
	/**
	 * 撤销报价
	 * 用户未选择商家前可以撤销报价，如果已经选择了商家，则不允许报价
	 */
	function del_merchant_bidding() {
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$demand_id = ( int ) $_POST ['id'];
		
		if (empty ( $demand_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车主需求ID为空...' );
			exit ();
		}
		$d_arr = $this->dao->where ( "id=$demand_id" )->find ();
		if ($d_arr['merchant_id'] != 0) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求已经选择商家...' );
			exit ();
		}
		if ($d_arr['expire_time'] <time()) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求已经过期...' );
			exit ();
		}
		$data = $this->bidding_dao->where ( array (
				'merchant_id' => $merchant_id,
				'demand_id' => $demand_id 
		) )->delete ();
		$bidding_remark = M ( 'MerchantBiddingRemark' );
		$dat = $bidding_remark->where ( array (
				'merchant_id' => $merchant_id,
				'demand_id' => $demand_id 
		) )->delete ();
		if ($data === false) {
			$this->jsonUtils->echo_json_msg ( 1, '删除失败' );
		} else {
			$this->jsonUtils->echo_json_msg ( 0, '删除成功' );
		}
	
	}
	
	// 商家报价获取某需求的项目列表
	public function demand_service_list() {
		$demand_id = $_POST ['id'];
		if (empty ( $demand_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家需求ID为空...' );
			exit ();
		}
		$model = new Model ();
		$arr = $model->query ( "select a.category_id,b.name from " . C ( 'DB_PREFIX' ) . "member_demand_subitems as a left join " . C ( 'DB_PREFIX' ) . "category as b on a.category_id=b.id where a.demand_id=$demand_id" );
		if ($arr) {
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '该需求没服务项目...' );
			exit ();
		}
	
	}
	
// 	public function test() {
		
// 		$ulr="http://api.map.baidu.com/geocoder/v2/?ak=4TGAqmofi6LcGeNYVFlOTOQG&location=24.514747,118.149879&output=json&pois=0";
// 		$data = file_get_contents($ulr);
// 		$data = json_decode($data,true);
// 		dump($data);
// 	}

	function test (){
// 		die('123');
// 		$jpush = D('Jpush');
		
// 		$jpush ->push(2, array(4), 'data');
		echo 'start:' ;
		dump(date('H:i:s'));
		$jpush = new \App\Model\JpushModel();
		$jpush->user = 0;
		$jpush ->push(2, array(4),array());
		echo 'end:' ;
		dump(date('H:i:s'));
		echo ' 发送xmpp';
		$xmpp = new \App\Model\XmppApiModel();
		$xmpp ->requestPush(2, array(4), $push);
	}
	
	
	public function test1(){

		$push = array('demand_id'=>1,'total_price'=>2,'total_time'=>3,'merchant_id'=>4);
		$jid = '10';
// 		// 		$jpush = D('Jpush');
// 		// 		$jpush->user = 2;
// 		// 		$jpush ->push(1, $jid, $push);
// 	//聊天内推送
		$xmpp = new \App\Model\XmppApiModel();
		$xmpp ->requestPush(2, array($jid), $push);
	
	}


}

?>