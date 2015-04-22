<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
use Think\Log;

/**
 * 用户需求
 */
class MemberDemandController extends Controller {
	
	private $jsonUtils;
	private $dao; // 需求表
	private $session_handle; // session 处理类
	private $session_dao;
	private $bidding_dao;
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( 'member_demand' );
		$this->session_dao = M ( 'member_session' );
		$this->bidding_dao = M ( 'merchant_bidding' );
	
	}
	/**
	 *  发布需求选择根据母项目选择子项目
	 */
	public function demand_service() {
		$id = isset ( $_POST ["id"] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		if (empty ( $id )) {
			$this->JsonUtils->echo_json_msg ( 4, '母项目ID为空...' );
			exit ();
		}
		$category = M ( 'category' );
		$arr = $category->field ( "id,name" )->where ( array (
				'id' => $id 
		) )->select ();
		if ($arr) {
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '暂无此项目的子项目信息...' );
			exit ();
		}
	
	}
	/**
	 * 发布需求之前 验证是否符合发布需求
	 * @param int $member_id
	 */
	protected function auth_add_demand($member_id){
		
		//检测需求未完成不能 在发布需求
// 		$findDemand = $this->dao->where(array('member_id'=>$member_id,'status'=>0,'expire_time'=>array('gt',time())))->getField('id');
// 		if($findDemand){
// 			$this->jsonUtils->echo_json_msg(4, '您有一发布需求未完成，不能发布需求');exit();
// 		}
		//72小时内只能取消3次需求
		$limit_time = time()-72*60*60;
		$cancel_demand = $this->dao->where("member_id=$member_id and status=2 and cancel_time>$limit_time and is_bidding > 0")->field('cancel_time,id')->order('cancel_time asc')->select();
		if($cancel_demand === false){
			$this->jsonUtils->echo_json_msg(4, '72小时限制查询出错');exit();
		}else{
			$num = count($cancel_demand);
			if($num >= 3){
				$last_cancel = $cancel_demand[0]['cancel_time'];
				$left_time = $last_cancel-$limit_time;
				if ($left_time>0) {
					$hour = floor($left_time/3600);
					$minute = floor(($left_time-3600 * $hour)/60);
				//	$second = floor((($left_time-3600 * $hour) - 60 * $minute) % 60);
					$result = $hour.'小时'.$minute.'分钟';
				}
				$str = "由于您72小时内已取消3次需求，于".$result."后可以再次发布需求";
				$this->jsonUtils->echo_json_msg(4, $str);exit();
			}
		}
		//订单最多能有5个为完成订单
		$order =MemberOrderController::getOrderWithoutDone($member_id);
		if(!$order){
			$this->jsonUtils->echo_json_msg(4, '您已有5个未完成的订单，请完成后在继续下单');exit();
		}
		return true;
		
		
	
	}
	/**
	 * 验证权限 是否能发布需求
	 */
	public function before_add_demand(){
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$data = $this->auth_add_demand($member_id);
		if($data){
			$this->jsonUtils->echo_json_data(0, 'ok', array());exit();
		}
	}
	/**
	 * 用户发布需求
	 */
	public function add_demand() {
		$publish = isset ( $_POST ['publish'] ) ? (int)htmlspecialchars ( $_POST ['publish'] ) : '0'; // 发布需求方式默认0,0 项目发布需求 1保养发布需求
		$cart_id = isset ( $_POST ['cart_id'] ) ? (int)htmlspecialchars ( $_POST ['cart_id'] ) : '';
		$reach_time = isset ( $_POST ['reach_time'] ) ? htmlspecialchars ( $_POST ['reach_time'] ) : '';
		$desc = isset ( $_POST ['desc'] ) ? htmlspecialchars ( $_POST ['desc'] ) : '';
// 		$range_km = isset ( $_POST ['range_km'] ) ? htmlspecialchars ( $_POST ['range_km'] ) : '';
		$category_ids = isset ( $_POST ['category_ids'] ) ? htmlspecialchars ( $_POST ['category_ids'] ) : '';
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$longitude = $_POST ['longitude'];
		$latitude = $_POST ['latitude'];
		if (empty ( $longitude ) || empty ( $latitude )) {
			$this->jsonUtils->echo_json_msg ( 4, '经度或者纬度为空...' );
			exit ();
		}
// 		Log::write($cart_id,'ERR');
		if (empty ( $cart_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车辆为空...' );
			exit ();
		}
		if (empty ( $reach_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '预约时间为空' );
			exit ();
		}
		$arr_ids = explode ( ",", $category_ids );
		$count = count ( $arr_ids );
		$name = $this->getDemandName ( $arr_ids, $publish );
		if (! $name) {
			$this->jsonUtils->echo_json_msg ( 4, '发布需求的项目存在错误' );
			exit ();
		}
		$reach_time =  strtotime ( $reach_time );
		if ( $reach_time < time()) {
			$this->jsonUtils->echo_json_msg ( 4, '发布到店时间要大于当前时间' );
			exit ();
		}
		$auth = $this->auth_add_demand($member_id);
        $ll_arr=rangekm(C('PUSH_RANGE_KM'), $longitude,$latitude);//获取最大最小经纬度
        $maxLng=$ll_arr['maxLng'];
        $minLng=$ll_arr['minLng'];
        $maxLat=$ll_arr['maxLat'];
        $minLat=$ll_arr['minLat'];
		$data ['publish'] = $publish;
		if ($publish == 1) {
			$km = isset ( $_POST ['km'] ) ? htmlspecialchars ( $_POST ['km'] ) : '';
			if (empty ( $km )) {
				$this->jsonUtils->echo_json_msg ( 4, '用户公里数为空' );
				exit ();
			}
			$param = array (
					'km' => $km,
					'category_ids' => $category_ids 
			);
			$data ['param'] = json_encode ( $param );
		}
		$data ['member_id'] = $member_id;
		$data ['cart_id'] = $cart_id;
		$data ['cart_data'] = $this->getcart ( $cart_id );
		$data ['title'] = $name;
		// $data['city_id']=$area['city'];
		// $data['area_id']=$area_id;
		$data ['reach_time'] =  $reach_time ;
		$data ['description'] = $desc;
		$data ['addtime'] = time ();
		$data ['expire_time'] = time () + 24*60*60;
		$data ['range_km']= C('PUSH_RANGE_KM');
		$data ['longitude'] = $longitude;
		$data ['latitude'] = $latitude;
		$data ['pics'] = json_encode ( array () );
		
		if ($_FILES) {
			$f_arr = mul_upload ( '/Demand/',1 );
			if ($f_arr) {
				$data ['pics'] = json_encode ( $f_arr ); // 把多张图片数组格式转json保存数据库
			}
		}
		$result = $this->dao->add ( $data );
		
		if ($result) {
			$data['demand_id'] =$result;
		
			$subitems = M ( "member_demand_subitems" );
			if (! empty ( $category_ids )) {
				
				$data1 ["member_id"] = $member_id; // 会员ID
				$data1 ["demand_id"] = $result; // 需求id
				foreach ( $arr_ids as $key => $value ) {
					$data1 ['category_id'] = $value; // 子项目ID
					$subitems->add ( $data1 );
				}
				
			}
			//查询出范围内的商家，给其调整需求配置
			$sql="select a.business_time,a.id,b.id as jid from ". C('DB_PREFIX')."merchant as a 
			left join ". C('DB_PREFIX')."system_user as b on (a.id = b.sub_id and b.type =2)
			where a.longitude <=$maxLng and a.longitude>=$minLng and a.latitude <=$maxLat and a.latitude>=$minLat and ( a.status = 0 or a.status = 1)";
			$ids = M('')->query($sql);
			//echo M('')->getLastSql();
			if($ids){
				foreach ($ids as $key =>$row){
					$addAll [$key]['id'] = null;
					$addAll [$key]['merchant_id'] = $row['id'];
					$addAll [$key]['demand_id'] = $result;
					if(timeCompare($row['business_time'])){
						$jid[] = $row['jid'];
					}
				}
				 M('DemandMerchantEnable')->addAll($addAll);
				 $jpush = new \App\Model\JpushModel();
				 $jpush->user = 2;
				 $jpush ->push(1, $jid, $data);
				 $xmpp = new \App\Model\XmppApiModel();
				 $xmpp ->requestPush(1, $jid, $data);
				 //云推送
				 
			}
	
			
			$this->jsonUtils->echo_json_msg ( 0, '发布需求成功!' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, 'failed' );
			exit ();
		}
	
	}
	
	/**
	 * 用户查看需求详情
	 */
	public function get_demand() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
		$member_session_id = isset ( $_POST ['member_session_id'] ) ? htmlspecialchars ( $_POST ['member_session_id'] ) : ''; //
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $id )) {
			$this->jsonUtils->echo_json_msg ( 4, '需求ID为空...' );
			exit ();
		}
		// 判断是否有改需求
		$bidding_arr = $this->dao->where ( "id=$id and member_id = $member_id and member_del = 0" )->find ();
		if ($bidding_arr == false) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求不存在...' );
			exit ();
		}
		
		if ($bidding_arr ['is_bidding']) {
			$longitude = $_POST ['longitude'];
			$latitude = $_POST ['latitude'];
			if (empty ( $longitude ) || empty ( $latitude )) {
				$this->jsonUtils->echo_json_msg ( 4, '经度或者纬度为空...' );
				exit ();
			}
			
			if (empty ( $merchant_id )) {
				$this->jsonUtils->echo_json_msg ( 4, '商家id为空...' );
				exit ();
			}
			
			// 需求项目列表信息
			// 查询某个商家对用户需求的所有报价
			$bidding = $this->getMerchantBiddingBy ( $id, $merchant_id, $bidding_arr ['publish'] );
			$total_price = 0;
			$total_time = 0;
			foreach ( $bidding as $key => $row ) {
				if($row['price']>0){
					$total_price += $row ['price'];
				}
				if($row['time']>0){
					$total_time += $row ['time'];
				}
			}
			$model = new Model ();
			// 需求报价总价格和总服务时间
			$cart = json_decode($bidding_arr['cart_data'],true);
			$data ['cart_model'] = $cart['cart_model'];
			$data ['reach_time'] = date('Y-m-d H:i:s',$bidding_arr['reach_time']);
			$data ['pics'] = imgUrl ( json_decode ( $bidding_arr ['pics'], true ) );
			$data ['description'] = $bidding_arr ['description'];
			$data ['list'] = $bidding;
			$data ["total_price"] =(string) $total_price;
			$data ['total_time'] = (string) $total_time;
			$data ['demand_status'] = $bidding_arr['status'];
			// 1 已过期 0 未过期
			$is_expire= time()-$bidding_arr['expire_time'] > 0 ? '1':'0';
			if($is_expire){
					if($bidding_arr['status'] == 0){
						$data['demand_status'] = '3';//过期
					}
				}
			// 商家备注
			$remark_arr = $model->query ( "select remark from " . C ( 'DB_PREFIX' ) . "merchant_bidding_remark  where demand_id=$id and merchant_id = $merchant_id" );
			$data ['remark'] = $remark_arr [0] ['remark'] == null ? '' : $remark_arr [0] ['remark'];
			
			// 商家信息
			$merchant_arr = $model->query ( "select id,merchant_name,header,intro,longitude,latitude,tel from " . C ( 'DB_PREFIX' ) . "merchant where id=$merchant_id" );
			$merchant_arr [0] ['header'] = imgUrl ( $merchant_arr [0] ['header'] );
			$merchant_arr [0] ['system_user_id']  = CommonController::getSystemUserid($merchant_arr [0] ['id'],2);
			$data ['merchant_info'] = $merchant_arr [0];
			
			$distance = getDistance ( $latitude, $longitude, $merchant_arr [0] ['latitude'], $merchant_arr [0] ['longitude'] );
			$data ['merchant_info'] ['distance'] = $distance;

			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		} else {
			
				if ($bidding_arr ['pics']) {
					$data ['pics'] = imgUrl ( json_decode ($bidding_arr ['pics'], true ) );
				}
				$cart = json_decode ( $bidding_arr ['cart_data'], true );
				$data ['id'] = $bidding_arr ['id'];
				$data ['title'] = $bidding_arr ['title'];
				$data ['reach_time'] = date('Y-m-d H:i:s',$bidding_arr['reach_time']);
				$data ['cart_model'] = $cart ['cart_model'];
				$data ['description'] = $bidding_arr ['description'];
				$data ['province_id'] = CityController::getName ( $bidding_arr ['province_id'] );
				$data ['city_id'] = CityController::getName ( $bidding_arr['city_id'] );
				$data ['area_id'] = CityController::getName ( $bidding_arr['area_id'] );
				$data ['demand_status'] = $bidding_arr['status'];
				// 1 已过期 0 未过期
				$is_expire= time()-$bidding_arr['expire_time'] > 0 ? '1':'0';
				if($is_expire){
						if($bidding_arr['status'] == 0){
							$data['demand_status'] = '3';//过期
						}
					}
				$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
				exit ();
		
		}
	}
	
	public function get_demand_info(){
		
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$member_session_id = isset ( $_POST ['member_session_id'] ) ? htmlspecialchars ( $_POST ['member_session_id'] ) : ''; 
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		
		$bidding_arr = $this->dao->where ( "id=$id and member_id = $member_id and member_del = 0" )->find ();
		if ($bidding_arr === false) {
			$this->jsonUtils->echo_json_msg ( 1, '该需求不存在...' );
			exit ();
		}
		if ($bidding_arr ['pics']) {
			$data ['pics'] = imgUrl ( json_decode ($bidding_arr ['pics'], true ) );
		}
		$cart = json_decode ( $bidding_arr ['cart_data'], true );
		$data ['id'] = $bidding_arr ['id'];
		$data ['title'] = $bidding_arr ['title'];
		$data ['reach_time'] = date('Y-m-d H:i:s',$bidding_arr['reach_time']);
		$data ['cart_model'] = $cart ['cart_model'];
		$data ['description'] = $bidding_arr ['description'];
		$data ['province_id'] = CityController::getName ( $bidding_arr ['province_id'] );
		$data ['city_id'] = CityController::getName ( $bidding_arr['city_id'] );
		$data ['area_id'] = CityController::getName ( $bidding_arr['area_id'] );
		$data ['demand_status'] = $bidding_arr['status'];
		// 1 已过期 0 未过期
		$is_expire= time()-$bidding_arr['expire_time'] > 0 ? '1':'0';
		if($is_expire){
			if($bidding_arr['status'] == 0){
				$data['demand_status'] = '3';//过期
			}
		}
		$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
		exit ();
		
	}
	/**
	 * 需求列表
	 */
	public function demand_list() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
// 		if ($type == 1) { // 已报价
			$longitude = $_POST ['longitude'];
			$latitude = $_POST ['latitude'];
			if (empty ( $longitude ) || empty ( $latitude )) {
				$this->jsonUtils->echo_json_msg ( 4, '经度或者纬度为空...' );
				exit ();
			}
			// 查询已报价的需求
			$db = M ( 'MemberDemand' );
			$data = $db->table ( C ( 'DB_PREFIX' ) . "member_demand as a " )->field ( 'a.addtime,a.title,a.id,a.expire_time,a.status as demand_status,a.is_bidding' )->join ( C ( 'DB_PREFIX' ) . "member_demand_subitems as b on a.id =b.demand_id" )->where ( "a.merchant_id =0 and a.member_id = $member_id and a.member_del = 0" )->group ( 'a.id' )->page ( $page )->limit ( $num )->order ( 'a.addtime desc' )->select ();
			$db2 = M ( 'MerchantBidding' );

			if ($data) {
				foreach ( $data as $key => $row ) {
					$bidding = $db2->query ( "select sum(a.price) as total_price,sum(a.out_time) as total_time,b.merchant_name,b.header ,b.id as merchant_id,b.latitude,b.longitude from " . C ( 'DB_PREFIX' ) . "merchant_bidding as a left join " . C ( 'DB_PREFIX' ) . "merchant as b on a.merchant_id = b.id where demand_id = " . $row ['id'] . "  group by merchant_id order by a.addtime asc" );
					$data [$key]['child_count'] = count($bidding);
					if($data [$key]['child_count']>0){
						foreach ( $bidding as $ke => $ro ) {
							$bidding [$ke] ['header'] = imgUrl ( $ro ['header'] );
							$distance = getDistance ( $latitude, $longitude, $ro ['latitude'], $ro ['longitude'] );
							$bidding [$ke] ['distance'] = $distance;
							unset ( $bidding [$ke] ['latitude'] );
							unset ( $bidding [$ke] ['longitude'] );
						}
					}
					$data [$key] ['addtime'] = date ( 'Y-m-d H:i:s', $row ['addtime'] );
					// 1 已过期 0 未过期
					$is_expire= time()-$row['expire_time'] > 0 ? '1':'0';
					if($is_expire){
						if($row['demand_status'] == 0){
							$data[$key]['demand_status'] = '3';//过期
						}
					}
					$data [$key] ['child'] = $bidding;
				}
				
				$arr ['list'] = $data;
				$this->jsonUtils->echo_json_data ( 0, "ok", $arr );
				exit ();
			} else {
				$arr ['list'] = array();
				$this->jsonUtils->echo_json_data ( 0, "ok", $arr );
				exit ();
			}
// 		} 
// 		elseif ($type == 2) { // 未报价
// 			$limit = ($page-1)*$num.','.$num;
// 			$arr = $this->dao->query ( "select id,title,reach_time,status as demand_status,from_unixtime(addtime,'%Y-%m-%d') as addtime,a.description,a.expire_time from " . C ( 'DB_PREFIX' ) . "member_demand as a where a.is_bidding = 0 and a.member_id = $member_id and a.member_del = 0 order by a.addtime desc limit $limit" );
// 			if ($arr) {
// 				$data ['list'] = $arr;
// 				foreach ( $arr as $key => $value ) {
// 					$arr [$key] ['type'] = 2;
// 					// 1 已过期 0 未过期
// 					$is_expire= time()-$value['expire_time'] > 0 ? '1':'0';
// 					if($is_expire){
// 						if($value['demand_status'] == 0){
// 							$arr[$key]['demand_status'] = '3';//过期
// 						}
// 					}
// 				}
				
				
// 			} else {
// 				$data ['list'] = array();
// 			}
// 			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
// 			exit ();
// 		}else{
// 			$this->jsonUtils->echo_json_msg ( 1, 'type 为空...' );
// 			exit ();
// 		}
	
	}
	/**
	 * 用户确定需求
	 */
	public function confirm_demand() {
		$id = ( int ) $_POST ['id'];
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $id )) {
			$this->jsonUtils->echo_json_msg ( 4, '需求ID为空...' );
			exit ();
		}
		if (empty ( $merchant_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家ID为空...' );
			exit ();
		}
		
		$order = M ( 'order' );
		// 需求是否属于这个用户
		$demand = $this->dao->where ( array (
				'id' => $id,
				'member_id' => $member_id 
		) )->find ();
		
		if (! $demand) {
			
			$this->jsonUtils->echo_json_msg ( 4, '数据异常1' );
			exit ();
		}
		if (time() > $demand ['expire_time']) {
			$this->jsonUtils->echo_json_msg ( 4, '已过期' );
			exit ();
		}
		if (time() > $demand ['reach_time']) {
			$this->jsonUtils->echo_json_msg ( 4, '到店时间已过期' );
			exit ();
		}
		if ($demand ['merchant_id']) {
			$this->jsonUtils->echo_json_msg ( 4, '已选择商户' );
			exit ();
		}
		// 检测商家是否有报价
		$bidding = M ( 'MerchantBidding' )->where ( array (
				'demand_id' => $id,
				'merchant_id' => $merchant_id 
		) )->find ();
		
		if (! $bidding) {
			$this->jsonUtils->echo_json_msg ( 4, '数据异常2' );
			exit ();
		}
		$o_arr = $order->where ( "sub_id=$id and member_id=$member_id" )->select ();
		if ($o_arr) {
			$this->jsonUtils->echo_json_msg ( 1, '此需求你已经提交过订单' );
			exit ();
		}
		
		// 查询需求
		$arr = $this->dao->query ( "select id,reach_time  as reach_time ,cart_data,description,pics,publish,member_id,longitude,latitude,cart_id ,merchant_id as demand_status from " . C ( 'DB_PREFIX' ) . "member_demand  where id=$id" );
		
		if ($arr) {
			$arr [0] ['demand_status'] = $arr [0] ['demand_status'] > 0 ? 1 : 0;
			
			$model = new Model ();
			$merchant = M ( 'merchant' );
			$mer_arr = $merchant->field ( "longitude,latitude" )->where ( "id=$merchant_id" )->select ();
			$longitude = $arr [0] ['longitude']; // 用户发布需求的经纬度
			$latitude = $arr [0] ['latitude'];
			$demand_id = $arr [0] ['id'];
			// 计算商家店铺和用户需求距离
			$arr [0] ['distance'] = getDistance ( $latitude, $longitude, $mer_arr [0] ['latitude'], $mer_arr [0] ['longitude'] );
			// 需求详情项目
			if ($arr [0] ['publish'] == 0) {
				$s_arr = $model->query ( "select b.name,b.id from " . C ( 'DB_PREFIX' ) . "member_demand_subitems as a left join " . C ( 'DB_PREFIX' ) . "category as b on a.category_id=b.id  where a.demand_id=$demand_id " );
			} else {
				$s_arr = $model->query ( "select b.name,b.id from " . C ( 'DB_PREFIX' ) . "member_demand_subitems as a left join " . C ( 'DB_PREFIX' ) . "car_maintain_category as b on a.category_id=b.id  where a.demand_id=$demand_id " );
			}
			$perlist = array ();
			if ($s_arr) {
				// 查询 商家已有的服务 ，商家未有的服务 不允许报价
				// $server_ids = CommonController::getServerListByMer (
				// $merchant_id );
				// $string = implode ( ',', $server_ids );
				// 允许报价 所提示的价格
				// 已报价 bidding
				$arr [0] ['merchant_remark'] = M ( 'MerchantBiddingRemark' )->where ( array (
						'demand_id' => $id 
				) )->getField ( 'remark' );
				$merchant_remark = empty ( $arr [0] ['merchant_remark'] ) ? '' : $arr [0] ['merchant_remark'];
				$map ['demand_id'] = $id;
				// $map ['sub_id'] = array (
				// 'in',
				// $string
				// );
				$map ['merchant_id'] = $merchant_id;
				$alert_price = M ( 'MerchantBidding' )->where ( $map )->field ( 'id as bidding_id,price,sub_id as cat_id,out_time as time' )->select ();
				foreach ( $alert_price as $tem ) {
					$price [$tem ['cat_id']] = $tem;
				}
				
				// 区分是报价为0 还是未报价，-1标识未报价
				// foreach ( $server_ids as $tem => $row ) {
				// if (! isset ( $price [$row] )) {
				// $price [$row] = array (
				// 'cat_id' => $row,
				// 'price' => - 1,
				// 'bidding_id' => '-1'
				// );
				// }
				
				// }
				
				$total_price = 0;
				$total_time = 0;
				$service_name = array ();
				// dump($price);
				foreach ( $s_arr as $key => $row ) {
					$perlist [$key] ['id'] = $row ['id'];
					$perlist [$key] ['name'] = $row ['name'];
					$service_name [] = $row ['name'];
					// if (in_array ( $row ['id'], $server_ids )) {
					$perlist [$key] ['is_server'] = 1;
					$perlist [$key] ['time'] = ! isset ( $price [$row ['id']] ['time'] ) ? '-1' : $price [$row ['id']] ['time'];
					$perlist [$key] ['price'] = ! isset ( $price [$row ['id']] ['price'] ) ?'-1' : $price [$row ['id']] ['price'];
					$perlist [$key] ['bidding_id'] = ! isset ( $price [$row ['id']] ['bidding_id'] ) ?'-1' : $price [$row ['id']] ['bidding_id'];
					
					// } else {
					// $perlist [$key] ['is_server'] = 0;
					// $perlist [$key] ['price'] = 0;
					// }
					if ($perlist [$key] ['price'] > 0) {
						$total_price += $perlist [$key] ['price'];
					}
					if ($perlist [$key] ['price'] > 0) {
						$total_time += $perlist [$key] ['time'];
					}
				}
			
			}
			
			// 拼接过程
			$arr [0] ['list'] = $perlist;
			if ($arr [0] ['pics']) {
				
				$json_obj = json_decode ( $arr [0] ['pics'], true );
			
			} else {
				
				$json_obj = array ();
			
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '没有该用户需求...' );
			exit ();
		}
		$param = json_decode ( $demand ['param'], true );
		$rel ['param'] = count ( $param ) > 0 ? $param : array ();
		$rel ['distance'] = $arr [0] ['distance'];
		$rel ['pics'] = $json_obj;
		$rel ['list'] = $perlist;
		
		$order_no = time () . rand ( 1000, 9999 );
		$data ['order_no'] = $order_no;
		$data ['service_name'] = ! empty ( $service_name ) ? implode ( '、', $service_name ) : '';
		$data ['status'] = 0;
		$data ['merchant_id'] = $merchant_id;
		$data ['member_id'] = $member_id;
		if ($arr [0] ['publish'] == 0) {
			$data ['type'] = 1; // 项目需求订单
		} else {
			$data ['type'] = 3; // 保养需求订单
		}
		$data ['goods_count'] = 1;
		$data ['total_price'] = $total_price;
		$data ['unit_price'] = $total_price;
		$data ['total_time'] = $total_time;
		$data ['sub_id'] = $id;
		$data ['sub_data'] = json_encode ( $rel );
		$data ['reach_time'] = $arr [0] ['reach_time'];
		$data ['cart_id'] = $arr [0] ['cart_id'];
		$data ['cart_data'] = $arr [0] ['cart_data'];
		// dump($arr[0]['cart_data']);
		$data ['merchant_remark'] = $merchant_remark;
		$data ['member_remark'] = $arr [0] ['description'];
		$data ['addtime'] = time ();
		
		$result = $order->add ( $data );
		// echo $order->getLastSql();
		if ($result) {
			
			// 修改需求 确认订单已被商家完成
			CommonController::order_done ( $id, $merchant_id );
			$jid = CommonController::getJid($merchant_id, 2);
			//云推送
			$jpush = new \App\Model\JpushModel();
			$jpush->user = 2;
			$jpush ->push(3, array($jid), array('order_no'=>$order_no));
			$xmpp = new \App\Model\XmppApiModel();
			$xmpp ->requestPush(3, array($jid), array('order_no'=>$order_no));
			
		
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '确认需求错误...' );
			exit ();
		}
	
	}
	
	/**
	 * 删除需求
	 */
	public function del_demand() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$demand_id = isset ( $_POST ['demand_id'] ) ? htmlspecialchars ( $_POST ['demand_id'] ) : '';
		if (empty ( $demand_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '需求ID为空...' );
			exit ();
		}
		$demand = $this->dao->where ( array (
				'id' => $demand_id,
				'member_id' => $member_id 
		) )->find ();
		if (! $demand) {
			$this->jsonUtils->echo_json_msg ( 4, '数据异常' );
		}
		if($demand['status']==2 ||$demand['status']==1 || $demand['expire_time'] < time()){
			//需求完成 需求取消 才能删除
			$save ['member_del'] = 1;
			$result = $this->dao->where ( "id=$demand_id" )->save ($save);
		}else{
			$this->jsonUtils->echo_json_msg ( 1, '不允许删除' );
			exit ();
		}
		if ($result) {
// 			$demand_subitems = M ( 'member_demand_subitems' );
// 			$demand_subitems->where ( "demand_id=$demand_id" )->delete ();
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '删除错误' );
			exit ();
		}
	
	}
	
	/**
	 * 取消需求
	 */
	public function cancel_demand() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$demand_id = isset ( $_POST ['demand_id'] ) ? htmlspecialchars ( $_POST ['demand_id'] ) : '';
		if (empty ( $demand_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '需求ID为空...' );
			exit ();
		}
		$demand = $this->dao->where ( array (
				'id' => $demand_id,
				'member_id' => $member_id
		) )->find ();
		if (! $demand) {
			$this->jsonUtils->echo_json_msg ( 4, '数据异常' );
		}
		if($demand['status']==0 && time() <=$demand['expire_time']){
			//正常需求 才能取消
			$save ['status'] = 2;
			$save ['cancel_time'] = time();
			$save ['expire_time'] = time();
			$result = $this->dao->where ( "id=$demand_id" )->save ($save);
			if ($result) {
				// 			$demand_subitems = M ( 'member_demand_subitems' );
				// 			$demand_subitems->where ( "demand_id=$demand_id" )->delete ();
				$this->jsonUtils->echo_json_msg ( 0, 'ok' );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '取消错误' );
				exit ();
			}
		}else{
			$this->jsonUtils->echo_json_msg ( 1, '不允许取消' );
			exit ();
		}
		
	
	}
	
	
	/**
	 * 获取商家对某个需求的各个项目报价
	 */
	
	public function getMerchantBiddingBy($demand_id, $merchant_id, $publish) {
		$prefix = C ( 'DB_PREFIX' );
		if ($publish == 0) {
			$dbname = 'category';
		} elseif ($publish == 1) {
			$dbname = 'car_maintain_category';
		} else {
			$this->jsonUtils->echo_json_msg ( 4, 'publish方式出错' );
			exit ();
		}
		$db = new Model ();
		$data = $db ->table($prefix . "member_demand_subitems as a")
		->field('b.name,b.id')
		->join($prefix.$dbname . ' as b on a.category_id = b.id')->where ( "a.demand_id = $demand_id" )->select ();
		
		$rel = M ( "MerchantBidding " )->field ( 'sub_id as id,price,out_time as time' )->where ( "demand_id = $demand_id and merchant_id
    			= $merchant_id" )->select ();
		foreach ($rel as $key =>$row){
			$price[$row['id']] = $row;
		}
		foreach ($data as $l =>$r){
			$data[$l]['price'] = !isset ( $price [$r ['id']] ['price'] ) ? '-1': $price [$r ['id']] ['price'];
			$data[$l]['time'] = !isset ( $price [$r ['id']] ['time'] ) ? '-1': $price [$r ['id']] ['time'];
		}
		return $data;
	}
	/**
	 * 检测发布需求的项目是否正确 正确返回项目名
	 *
	 * @param array $arr        	
	 * @param int $publish        	
	 */
	public function getDemandName($arr, $publish) {
		if ($publish == 0) {
			$dbname = 'Category';
		} elseif ($publish == 1) {
			$dbname = 'CarMaintainCategory';
		} else {
			$this->jsonUtils->echo_json_msg ( 4, 'publish方式出错' );
			exit ();
		}
		$db = M ( $dbname );
		foreach ( $arr as $key => $row ) {
			$data = $db->where ( array (
					'id' => $row 
			) )->getField ( 'name' );
			if ($data) {
				$name [] = $data;
			} else {
				return false;
			}
		}
		return implode ( '、', $name );
	
	}
	
	/**
	 * 获取cart——model
	 */
	public function getcart($id) {
		$db = M ( 'Cart' );
		$data = $db->where ( array (
				'id' => $id 
		) )->find ();
		$data = json_encode ( $data );
		return $data;
	}
	
	/**
	 * 获取官方推荐的套餐信息
	 */
	public function getComboList() {
		$db = M ( 'DemandCombo' );
		$data = $db->select ();
		$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
	}
	/**
	 * 获取推荐套餐详情
	 */
	public function getComboInfo() {
		$comboId = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		if (empty ( $comboId )) {
			$this->jsonUtils->echo_json_msg ( 4, 'id不为空' );
		}
		$db = M ( 'DemandCombo' );
		$data = $db->where ( array (
				'id' => $comboId 
		) )->find ();
		
		$name = M ( 'Category' )->where ( "id in (" . $data ['combo'] . ") and status =1 " )->field ( 'id,name' )->select ();
		unset ( $data ['combo'] );
		unset ( $data ['sort'] );
		$data ['name'] = $name;
		$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
		exit ();
	
	}
	
	/**
	 * 需求--选择项目
	 * 标识出已添加过的项目和正在审核的项目
	 */
	public function merServiceSelectList() {
		
		$data = M ( 'Category' )->where ( array (
				'status' => 1 
		) )->field ( 'id,pid,name' )->select ();
		
		$arr = array ();
		// id 提至 key
		foreach ( $data as $temp => $rel ) {
			$arr [$rel ['id']] = $rel;
		}
		// 结果 排序
		foreach ( $arr as $key => $row ) {
			if ($row ['pid'] == 0) {
				unset ( $row ['id'] );
				$redata [$key] = $row;
			} else {
				
				$redata [$row ['pid']] ['child'] [] = $row;
			}
		
		}
		
		$redata = array_values ( $redata );
		
		$this->jsonUtils->echo_json_data ( 0, 'ok', $redata );
	
	}
	/**
	 * 保养手册
	 */
	public function maintain() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$brand_id = isset ( $_POST ['brand_id'] ) ? htmlspecialchars ( $_POST ['brand_id'] ) : '';
		if (empty ( $brand_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '品牌id为空' );
			exit ();
		}
		$maintain = M ( 'CarMaintain' );
		$data = $maintain->where ( array (
				'brand_id' => $brand_id 
		) )->field ( 'category_id,km,month' )->order ( 'sort asc' )->select ();
		if ($data == false) {
			$this->jsonUtils->echo_json_msg ( 4, '不存在改品牌' );
			exit ();
		} else {
			foreach ( $data as $key => $row ) {
				unset ( $data [$key] ['category_id'] );
				$data [$key] ['desc'] = $this->getMaintainDesc ( $row ['category_id'] );
			}
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		}
	
	}
	public function getMaintainDesc($ids) {
		$db = M ( 'CarMaintainCategory' );
		$data = $db->where ( array (
				'id' => array (
						'in',
						$ids 
				) 
		) )->field ( 'id,name' )->select ();
		return $data;
	}
	/*
	 * 保养检测
	 */
	public function maintainSubmit() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$startTime = isset ( $_POST ['start'] ) ? htmlspecialchars ( $_POST ['start'] ) : ''; // 新车上路时间
		$km = isset ( $_POST ['km'] ) ? htmlspecialchars ( $_POST ['km'] ) : '';
		$cart_id = isset ( $_POST ['cart_id'] ) ? (int)htmlspecialchars ( $_POST ['cart_id'] ) : '';
		if (empty ( $startTime )) {
			$this->jsonUtils->echo_json_msg ( 4, '新车上路时间为空' );
			exit ();
		}
		if (empty ( $km )) {
			$this->jsonUtils->echo_json_msg ( 4, '公里数为空' );
			exit ();
		}
		if (empty ( $cart_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车辆id为空' );
			exit ();
		}
		$startTime =  strtotime ( $startTime ) ;
		$now = time () ;
		$month = $now - $startTime;
		if ($month < 0) {
			$this->jsonUtils->echo_json_msg ( 4, '新车上路时间大于当前时间' );
			exit ();
		} else {
			//保存 新车上路时间
			$cart = R ( 'App/Cart/getCartInfo', array ($cart_id) );
			$brand_id = $cart ['brand_id'];
			// 更新车辆行驶公里数 新车上路时间
			$cartUpdate = R ( 'App/Cart/editCartInfo', array (
					$cart_id,
					$km,
					 $startTime
			) );
			$db = M ( 'CarMaintain' );
			$dataKm = $db->where ( "km<$km and brand_id = $brand_id" )->order ( 'sort desc' )->find ();
			$data = $db->where ( array (
					'brand_id' => $brand_id 
			) )->field ( 'id,category_id,km,month' )->order ( 'sort asc' )->select ();
			
			if ($data == false) {
				$dataKm = $db->where ( "km<$km and brand_id = 0" )->order ( 'sort desc' )->find ();
				$data = $db->where ( array (
						'brand_id' => 0 
				) )->field ( 'id,category_id,km,month' )->order ( 'sort asc' )->select ();
			
			}
			$focus = 0;
			foreach ( $data as $key => $row ) {
				unset ( $data [$key] ['category_id'] );
				if ($row ['id'] == $dataKm ['id']) {
					if ($focus) {
						// 错误情况处理
						$data [$key] ['focus'] = '0';
					} else {
						$focus = '1';
					}
					$data [$key] ['focus'] = '1';
				} else {
					$data [$key] ['focus'] = '0';
				}
				$data [$key] ['desc'] = $this->getMaintainDesc ( $row ['category_id'] );
			}
			if (! $focus) {
				// 超出范围 最后一条未focus
				$count = count ( $data ) - 1;
				if($data [$count] ['km'] <$km){
					$data [$count] ['focus'] = '1';
				}
			}
			$arr ['user_km'] = $km;
			$arr ['user_month'] = $month;
			$arr ['list'] = $data;
			
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
			exit ();
		
		}
	
	}
	/**
	 * 保养项目列表
	 */
	public function maintainList() {
		$db = M ( 'CarMaintainCategory' );
		$data = $db->field ( 'id,name' )->select ();
		$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
	}
	/**
	 * 保养订单完成 生成保养记录
	 *
	 * @param unknown_type $km        	
	 * @param unknown_type $category_ids        	
	 */
	static public function addMyMaintain($member_id, $cart_id, $km, $category_ids, $time) {
		$db = M ( 'CartMaintain' );
		$data = $db->add ( array (
				'member_id' => $member_id,
				'cart_id' => $cart_id,
				'km' => $km,
				'category_ids' => $category_ids,
				'addtime' => $time 
		) );
		
		return $data;
	}
	/**
	 * 我的保养记录
	 */
	public function getCarMaintain() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$cart_id = isset ( $_POST ['cart_id'] ) ? htmlspecialchars ( $_POST ['cart_id'] ) : '';
		$db = M ( 'CartMaintain' );
		$data = $db->where ( array (
				'cart_id' => $cart_id,
				'member_id' => $member_id 
		) )->field ( 'id,km,category_ids,addtime' )->order ( 'addtime desc' )->select ();
// 		echo $db->getLastSql();
// 		dump($data);
		if(!$data){
			$data =array();
		}else{
			foreach ( $data as $key => $row ) {
				
				$data [$key] ['addtime'] = date ( 'Y-m-d', $row ['addtime'] );
				$child = M ( 'CarMaintainCategory' )->where ( array (
				'id' => array (
				'in',
				$row ['category_ids']
				)
				) )->field ( 'id,name' )->select ();
				$data [$key] ['child'] = empty($child)?array():$child;
				unset ( $data [$key] ['category_ids'] );
			}
		}
		$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
	}
	/**
	 * 添加我的保养记录
	 */
	public function addMyCarMaintain() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$cart_id = isset ( $_POST ['cart_id'] ) ? htmlspecialchars ( $_POST ['cart_id'] ) : '';
		$km = isset ( $_POST ['km'] ) ? htmlspecialchars ( $_POST ['km'] ) : '';
		$category_ids = isset ( $_POST ['category_ids'] ) ? htmlspecialchars ( $_POST ['category_ids'] ) : '';
		$time = isset ( $_POST ['time'] ) ? htmlspecialchars ( $_POST ['time'] ) : '';
		if(empty($category_ids)) {
			$this->jsonUtils->echo_json_msg ( 4, '请选择项目' );
			exit ();
		}
		$time = strtotime ( $time );
		$data = $this->addMyMaintain ( $member_id, $cart_id, $km, $category_ids, $time );
		if ($data) {
			$this->jsonUtils->echo_json_msg ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '添加错误' );
			exit ();
		}
	}
	/**
	 * 修改我的保养记录
	 */
	public function editMyCarMaintain() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$cart_id = isset ( $_POST ['cart_id'] ) ? htmlspecialchars ( $_POST ['cart_id'] ) : '';
		$km = isset ( $_POST ['km'] ) ? htmlspecialchars ( $_POST ['km'] ) : '';
		$category_ids = isset ( $_POST ['category_ids'] ) ? htmlspecialchars ( $_POST ['category_ids'] ) : '';
		$time = isset ( $_POST ['time'] ) ? htmlspecialchars ( $_POST ['time'] ) : '';
		$time = strtotime ( $time );
		$db = M ( 'CartMaintain' );
		$data = $db->where ( array (
				'id' => $id,
				'member_id' => $member_id 
		) )->getField ( 'id' );
		if ($data) {
			$rel = $db->where ( array (
					'id' => $data 
			) )->save ( array (
					'km' => $km,
					'category_ids' => $category_ids,
					'addtime' => $time 
			) );
			if ($rel ===false) {
				$this->jsonUtils->echo_json_msg ( 4, '修改失败' );
				exit ();
				
			} else {
				$this->jsonUtils->echo_json_msg ( 0, 'ok' );
				exit ();
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '参数错误' );
			exit ();
		}
	}
	/**
	 * 删除保养记录
	 */
	public function delMyCarMaintain() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$db = M ( 'CarMaintain' );
		$data = $db->where ( array (
				'id' => $id,
				'member_id' => $member_id 
		) )->getField ( 'id' );
		if ($data) {
			$del = $db->where ( array (
					'id' => $id 
			) )->delete ();
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '添加错误' );
			exit ();
		}
	}
	public function test(){
		//商户给用户报价 jpush
// 				$jid = 12;
// 						//云推送
// 					$jpush = new \App\Model\JpushModel();
// 					$jpush->user = 0;
// 					$jpush ->push(2, array($jid), $data);
// 					//聊天内推送
// 					$xmpp = new \App\Model\XmppApiModel();
// 					$xmpp ->requestPush(2, array($jid), $data);
	
// 		//商户	//xmpp
// 		$jid = array(18);
// 		//用户发需求
// 		$jpush = D('Jpush');
// 		$jpush->user = 2;
// 		$jpush ->push(1, $jid, $data);
// 		$xmpp = new \App\Model\XmppApiModel();
// 		$xmpp ->requestPush(1, $jid, $data);
// 		//云推送
// 		$a = timeCompare('09:50-22:22');
// 		dump($a);
// 		$jpush = new \App\Model\JpushModel();
// 		$jpush->user = 0;//4
// 		$jpush ->push(2, array('4'), array('demand_id'=>123));
// 		$jpush = new \App\Model\JpushModel();
// 		$jpush->user = 2;//4
// 		$jpush ->push(1, array('440'), array('demand_id'=>123));
	}
}

?>