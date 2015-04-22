<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 其他接口
 * @author Administrator
 *
 */
class HomeController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	
	function _initialize(){
		$ver = !empty($_POST['ver'])?htmlspecialchars($_POST['ver']):'';
		//ver 为空 则不路由，即用原始版本接口
		if(in_array($ver, C('VER_ARRAY'))){
			//在使用版本内 执行路由 反之原始版本接口
			$functionName = ACTION_NAME.$ver;
			if(method_exists($this, $functionName)){
				$this->$functionName();
				die();
			}
				
		}
	}
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( 'merchant' );
		parent::__construct();
	
	}
	
	// 首页地图列表
	public function map_list() {
// 		$longitude = $_POST ['longitude'];
// 		$latitude = $_POST ['latitude'];
// 		$km = $_POST ['km'];
		$city_id = isset ( $_POST ['city_id'] ) ? htmlspecialchars ( $_POST ['city_id'] ) : '1326';
		$category = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : ''; // pcat_id
                                                                       
// 		if (empty ( $category )) {
// 			$arr = $this->dao->table ( C ( 'DB_PREFIX' ) . "merchant as a" )->field ( 'a.id,a.merchant_name,a.header,a.intro,a.tel,a.address,a.mobile,a.longitude,a.latitude,a.service_attitude,a.service_quality,a.merchant_setting,a.comment_count,b.name as province_name,c.name as city_name,d.name as area_name' )
// 					->join(C ( 'DB_PREFIX' ) . "city as b on a.province_id  = b.id",'LEFT')->join(C ( 'DB_PREFIX' ) . "city as c on a.city_id  = c.id",'LEFT')->join(C ( 'DB_PREFIX' ) . "city as d on a.area_id  = d.id",'LEFT')
// 					->where(array('a.is_salesman'=>0))->select ();
		
// 		} else {
// 			$arr = $this->dao->table ( C ( 'DB_PREFIX' ) . "service as a" )->join ( C ( 'DB_PREFIX' ) . 'merchant as b on a.merchant_id = b.id' )->field ( 'b.id,b.merchant_name,b.header,b.intro,b.tel,b.address,b.mobile,b.longitude,b.latitude,b.header,b.comment_count,b.service_attitude,b.service_quality,b.merchant_setting,c.name as province_name,d.name as city_name,e.name as area_name' )
// 			->join(C ( 'DB_PREFIX' ) . "city as c on b.province_id  = c.id",'LEFT')->join(C ( 'DB_PREFIX' ) . "city as d on b.city_id  = d.id",'LEFT')->join(C ( 'DB_PREFIX' ) . "city as e on b.area_id  = e.id",'LEFT')
// 			->where ( "a.pcat_id = $category and b.is_salesman =0" )->group ( 'a.merchant_id' )->select ();
// 		}
		$where = " b.status = 0 ";
		$where = $where . " and b.city_id=$city_id ";
		if (!empty ( $category )) {
			$where = $where . " and a.pcat_id = $category ";
		}
		$arr = $this->dao->table ( C ( 'DB_PREFIX' ) . "service as a" )
		->join ( C ( 'DB_PREFIX' ) . 'merchant as b on a.merchant_id = b.id' )
		->field ( 'b.id,b.merchant_name,b.header,b.intro,b.tel,b.address,b.mobile,b.longitude,b.latitude,b.header,b.comment_count,b.service_attitude,b.service_quality,b.merchant_setting,c.name as province_name,d.name as city_name,e.name as area_name' )
		->join(C ( 'DB_PREFIX' ) . "city as c on b.province_id  = c.id",'LEFT')->join(C ( 'DB_PREFIX' ) . "city as d on b.city_id  = d.id",'LEFT')->join(C ( 'DB_PREFIX' ) . "city as e on b.area_id  = e.id",'LEFT')
		->where ( $where )->group ( 'a.merchant_id' )->select ();
		
		if (! $arr) {
			$arr = array ();
		} else {
			foreach ( $arr as $key => $row ) {
				$arr [$key] ['header'] = imgUrl ( $row ['header'] );
				$arr [$key] ['comment_star'] = number_format ( ($row ['service_attitude'] + $row ['service_quality'] + $row ['merchant_setting']) / 3, 1 );
				unset ( $arr [$key] ['service_attitude'] );
				unset ( $arr [$key] ['service_quality'] );
				unset ( $arr [$key] ['merchant_setting'] );
			}
		}
		if ($arr) {
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '暂无周边商家信息...' );
			exit ();
		}
	
	}
	
	/**
	 * 首页搜素列表
	 * 
	 * @return [type] [description]
	 */
	public function index_list() {
		
		$classid = isset ( $_POST ['cat_id'] ) ? htmlspecialchars ( $_POST ['cat_id'] ) : '0';
		$cityid = isset ( $_POST ['city_id'] ) ? htmlspecialchars ( $_POST ['city_id'] ) : '1326';
		$areaid = isset ( $_POST ['area_id'] ) ? htmlspecialchars ( $_POST ['area_id'] ) : '0';
		$longitude = isset ( $_POST ['longitude'] ) ? htmlspecialchars ( $_POST ['longitude'] ) : '';
		$latitude = isset ( $_POST ['latitude'] ) ? htmlspecialchars ( $_POST ['latitude'] ) : '';
		$sort = isset ( $_POST ['sort'] ) ? htmlspecialchars ( $_POST ['sort'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		
		if (empty ( $longitude ) || empty ( $latitude )) {
			$this->jsonUtils->echo_json_msg ( 4, '经纬度参数不能为空...' );
			exit ();
		}
		if (empty ( $cityid )) {
			$this->jsonUtils->echo_json_msg ( 4, '城市id为空' );
			exit ();
		}
		
		$where = " b.status = 0 ";
		$where = $where . " and b.city_id=$cityid ";
		if (! empty ( $areaid )) {
			$where = $where . " and b.area_id=$areaid";
		}
		if (in_array ( $classid, array (
				'1',
				'2',
				'3',
				'4' 
		) )) {
			$where = $where . " and a.pcat_id = $classid ";
		} else if (! empty ( $classid )) {
			$where = $where . " and a.cat_id = $classid ";
		}
		
		// 缓存 data
		// $dataMark ='Mer/mer_list_'.$cityid.'_'.$areaid.'_'.$classid;
		// $cacheData = F($dataMark);
		// if(empty($cacheData)){
		$arr = $this->dao->table ( C ( 'DB_PREFIX' ) . "service as a" )->join ( C ( 'DB_PREFIX' ) . 'merchant as b on a.merchant_id = b.id','LEFT' )->field ( 'b.id,b.merchant_name,b.header,b.address,b.longitude,b.latitude,b.area_id' )->where ( $where )->group ( 'a.merchant_id' )->select ();
// 		echo $this->dao->getLastSql();
		// F($dataMark,$arr);
		// }else{
		// $arr = $cacheData;
		// }

		if ($arr) {
			foreach ( $arr as $key => $value ) {
				if (! empty ( $arr [$key] ['pics'] )) {
					$json_arr = json_decode ( $arr [$key] ['pics'] );
					$arr [$key] ['pics'] = imgUrl ( $json_arr [0] );
				}
				$arr [$key] ['header'] = imgUrl ( $value ['header'] );
				$arr [$key] ['area_name'] = CityController::getName ( $value ['area_id'] );
				// 根据商家查询所拥有的服务
				$arr [$key] ['service_name'] = CommonController::getMerchantServerListName ( $value ['id'] );
				
				$arr [$key] ['distance'] = getDistance ( $latitude, $longitude, $arr [$key] ['latitude'], $arr [$key] ['longitude'] ); // 计算两点距离
			}
			if ($sort == 1) {
				$arr1 = sort_asc ( $arr );
			} elseif ($sort == 2) {
				$arr1 = sort_desc ( $arr );
			}
// 			dump($arr1);
			$start = ($page - 1) * $num;
			$end = $page * $num - 1;
			if ($start > count ( $arr1 ) - 1) {
				$this->jsonUtils->echo_json_data ( 0, 'ok', array (
						'list' => array()
				) );
				exit ();
			}
			for($i = $start; $i <= $end; $i ++) {
				if (! empty ( $arr1 [$i] )) {
					$dataArr [] = $arr1 [$i];
				}
			
			}
			$data ['list'] = $dataArr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		} else {
			$this->jsonUtils->echo_json_data ( 0, 'ok', array('list'=>array()) );
		}
	
	}
	
	/**
	 * 附近商家（未启用）
	 */ 
	public function around_merchant() {
		
		$longitude = $_POST ['longitude'];
		$latitude = $_POST ['latitude'];
		$classid = isset ( $_POST ['cat_id'] ) ? htmlspecialchars ( $_POST ['cat_id'] ) : '';
		$sort = isset ( $_POST ['sort'] ) ? htmlspecialchars ( $_POST ['sort'] ) : '1';
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		// $km=isset($_POST['km'])?htmlspecialchars($_POST['km']):'3';
		if (empty ( $longitude ) || empty ( $latitude )) {
			$this->jsonUtils->echo_json_msg ( 4, '经纬度参数为空...' );
			exit ();
		}
		
		// $ll_arr=rangekm($km, $longitude,$latitude);//获取最大最小经纬度
		// $maxLng=$ll_arr['maxLng'];
		// $minLng=$ll_arr['minLng'];
		// $maxLat=$ll_arr['maxLat'];
		// $minLat=$ll_arr['minLat'];
		$where = " 1=1 and is_salesman =0 ";
		// $where .= "and b.longitude <$maxLng and b.longitude>$minLng and
		// b.latitude <$maxLat and b.latitude>$minLat";
		// $sql="select
		// a.id,a.longitude,a.latitude,a.intro,a.pics,a.header,a.merchant_name
		// ,a.pcat_id ,b.name as area_name from ". C('DB_PREFIX')."merchant as a
		// left join ".C('DB_PREFIX')."city as b on a.area_id=b.id where
		// a.collecter=0 and a.longitude <$maxLng and a.longitude>$minLng and
		// a.latitude <$maxLat and a.latitude>$minLat ";
		if (! empty ( $classid )) {
			
			$sql = $where . " and a.pcat_id =$classid ";
			$arr = $this->dao->table ( C ( 'DB_PREFIX' ) . "service as a" )->join ( C ( 'DB_PREFIX' ) . 'merchant as b on a.merchant_id = b.id' )->field ( 'b.id,b.merchant_name,b.header,b.intro,b.tel,b.address,b.mobile,b.longitude,b.latitude,b.area_id' )->where ( $where )->group ( 'a.merchant_id' )->page ( $page )->limit ( $num )->select ();
		}else{
			$arr = $this->dao->table ( C ( 'DB_PREFIX' ) . "merchant as b" )->field ( 'b.id,b.merchant_name,b.header,b.intro,b.tel,b.address,b.mobile,b.longitude,b.latitude,b.area_id' )->where ( $where )->page ( $page )->limit ( $num )->select ();
		}
		
		
		// $sql=$sql." limit $pagenum,20";
		// $arr=$this->dao->query($sql);
		
		if ($arr) {
			
			foreach ( $arr as $key => $value ) {
				if ($arr [$key] ['pics']) { // 取json数组图片第一张作为图片
					$json_arr = json_decode ( $arr [$key] ['pics'] );
					$arr [$key] ['pics'] = imgUrl ( $json_arr [0] );
				}
				$arr [$key] ['header'] = imgUrl ( $value ['header'] );
				$arr [$key] ['area_name'] = CityController::getName ( $value ['area_id'] );
				$arr [$key] ['service_name'] = CommonController::getMerchantServerListName ( $value ['id'] );
				$arr [$key] ['distance'] = getDistance ( $latitude, $longitude, $arr [$key] ['latitude'], $arr [$key] ['longitude'] ); // 计算两点距离
			}
			
			if ($sort == 1) { // 按距离升序和降序
				$arr1 = sort_asc ( $arr );
			}
			if ($sort == 2) {
				$arr1 = sort_desc ( $arr );
			}
			
			$data ['list'] = $arr1;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '该附近没有商家信息...' );
		}
	
	}
	
	/**
	 * 用户读取商家信息
	 */
	public function getMerchant() {
		
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $merchant_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家Id为空...' );
			exit ();
		}
		$arr = $this->dao->field ( 'id as merchant_id,merchant_name,header,service_attitude,service_quality,merchant_setting,comment_count,contact,tel,address,pics,longitude,latitude,intro,business_time,wifi_enable' )->where ( "id=$merchant_id" )->select ();
// 		dump($arr);
		if ($arr) {
			$arr [0] ['header'] = imgUrl ( $arr [0] ['header'] );
			if ($arr [0] ['pics']) {
				$json_obj = json_decode ( $arr [0] ['pics'],true );
				$arr [0] ['pics'] = imgUrl ( $json_obj );
			
			}
			$stat = $this->isCollectMer ( $merchant_id, $member_id );
			$arr [0] ['is_collect'] = $stat;
			$service = M ( 'service' );
			$serviceList = $service->field ( "id,name,pcat_id as classid,timeout,price,pcat_id" )->where ( "merchant_id=$merchant_id and effect =1"  )->group ( 'cat_id' )->select ();
			if(empty($serviceList)){
				$serviceList = array();
			}
			$arr [0] ['service_list'] = $serviceList;
// 			$arr [0] ['comment_list'] = $commentList;
			// $arr[0]['service_quality']=empty($c_arr[0]['service_quality'])?0:ceil($c_arr[0]['service_quality']);
			// $arr[0]['service_attitude']=empty($c_arr[0]['service_attitude'])?0:ceil($c_arr[0]['service_attitude']);
			// $arr[0]['merchant_setting']=empty($c_arr[0]['merchant_setting'])?0:ceil($c_arr[0]['merchant_setting']);
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '商家信息为空...' );
			exit ();
		}
	}
	public function getMerchantComment(){
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
	//	$member_session_id = $_POST ['member_session_id'];
	//	$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		
		if (empty ( $merchant_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家Id为空...' );
			exit ();
		}
		$comment = M ( 'Comment' );
		// $c_arr=$comment->query("select avg(service_attitude) as
		// service_attitude,avg(service_quality) as
		// service_quality,avg(merchant_setting) as merchant_setting from
		// ".C('DB_PREFIX')."comment where merchant_id=$merchant_id");
		// 是否收藏过该商家
		$count = $comment->where("type = 0 and merchant_id = $merchant_id")->count();
		$commentList = $comment->table ( C ( 'DB_PREFIX' ) . 'comment as a ' )->field ( 'b.nick_name,b.header,a.service_attitude,a.service_quality,a.merchant_setting,a.addtime,a.pics,a.desc,c.service_name,a.order_no' )
		->join ( C ( 'Db_PREFIX' ) . 'member as b on a.member_id = b.id ' )
		->join ( C ( 'Db_PREFIX' ) . 'order as c on a.order_no = c.order_no' )
		->where ( "a.type = 0 and a.merchant_id = $merchant_id" )->limit ( $num )->page($page)->order ( 'a.addtime desc' )->select ();
		if ($commentList) {
			foreach ( $commentList as $key => $row ) {
				unset ( $commentList [$key] ['service_attitude'] );
				unset ( $commentList [$key] ['service_quality'] );
				unset ( $commentList [$key] ['merchant_setting'] );
				$commentList [$key] ['star'] = number_format ( ($row ['service_attitude'] + $row ['service_quality'] + $row ['merchant_setting']) / 3, 1 );
				$commentList [$key] ['header'] = imgUrl ( $row ['header'] );
				$commentList [$key] ['addtime'] = date ( 'Y-m-d H:i:s', $row ['addtime'] );
				$commentList [$key] ['pics'] = imgUrl ( json_decode ( $row ['pics'], true ) );
			}
		} else {
			$commentList = array ();
		}
		
		$this->jsonUtils->echo_json_data ( 0, 'ok', array('list'=>$commentList,'count'=>$count) );exit();
	}
	/**
	 * 商家详情-简单详情
	 */
	public function merchant_info() {
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id'] ) : '';
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		
		if (empty ( $merchant_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家Id为空...' );
			exit ();
		}
		$arr = $this->dao->field ( "id as merchant_id,merchant_name,service_attitude,service_quality,merchant_setting,comment_count,address,business_time,tel,header,longitude,latitude" )->where ( "id=$merchant_id" )->select ();
		
		if ($arr) {
			$arr [0] ['header'] = imgUrl ( $arr [0] ['header'] );
			$arr [0] ['star'] = number_format ( ($arr [0] ['service_attitude'] + $arr [0] ['service_quality'] + $arr [0] ['merchant_setting']) / 3, 1 );
			// 是否收藏过该商家
			$stat = $this->isCollectMer ( $merchant_id, $member_id );
			$arr [0] ['is_collect'] = $stat;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '没有此商家信息...' );
			exit ();
		}
	
	}
	
	/**
	 *  商家详情项目列表
	 */
	public function merchant_service_list() {
		$classid = isset ( $_POST ['classid'] ) ? htmlspecialchars ( $_POST ['classid'] ) : '';
		$merchant_id = $_POST ['merchant_id'];
		if (! empty ( $classid )) {
			$where = " and  pcat_id=$classid ";
		}
		
		$service = M ( 'service' );
		$arr = $service->field ( "id,name,pcat_id as classid,timeout,price" )->where ( "merchant_id=$merchant_id and effect =1" . $where )->group ( 'cat_id' )->select ();
		if ($arr) {
			$data ["list"] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '该商家暂无服务项目...' );
			exit ();
		}
	}
	
	/**
	 * 是否收藏过该商家
	 */
	public function isCollectMer($merchant_id, $userid) {
		$db = M ( 'Collect' );
		$data = $db->where ( array (
				'obj_id' => $merchant_id,
				'member_id' => $userid,
				'type' => 1 
		) )->find ();
		if ($data) {
			return 1;
		}
		return 0;
	}
	/**
	 * 收藏列表
	 */
	public function collect_list() {
		$member_session_id = $_POST ['session_id'];
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		// 1 收藏商家 3用户收藏问答 4 商家收藏问答
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '1';
		$limit = ($page - 1) * $num . ',' . $num;
		if ($type == 1) {
			$member_id = $this->session_handle->getsession_userid ( $member_session_id );
			$longitude = $_POST ['longitude'];
			$latitude = $_POST ['latitude'];
			if (empty ( $longitude )) {
				$this->jsonUtils->echo_json_msg ( 4, '经度为空...' );
				exit ();
			}
			if (empty ( $latitude )) {
				$this->jsonUtils->echo_json_msg ( 4, '纬度为空...' );
				exit ();
			}
			
			$model = new Model ();
			$sql = "select a.id,b.id as merchant_id ,b.header,b.merchant_name,b.longitude,b.latitude,c.name as area_name
			from " . C ( 'DB_PREFIX' ) . "collect as a  left join
			" . C ( 'DB_PREFIX' ) . "merchant as b on a.obj_id=b.id
			left join " . C ( 'DB_PREFIX' ) . "city as c on c.id=b.id
			where a.member_id=$member_id and a.type = 1 limit $limit";
			$arr = $model->query ( $sql );
			
			if ($arr) {
				foreach ( $arr as $key => $value ) {
					$arr [$key] ['distance'] = getDistance ( $latitude, $longitude, $value ['latitude'], $value ['longitude'] );
					$arr [$key] ['header'] = imgUrl ( $value ['header'] );
					$arr [$key] ['service_name'] = CommonController::getMerchantServerListName ( $value ['merchant_id'] );
					unset ( $arr [$key] ['latitude'] );
					unset ( $arr [$key] ['longitude'] );
				}
				// $arr = sort_asc ( $arr ); // 按距离排序
				$data ['list'] = $arr;
				$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
				exit ();
			} else {
				$data ['list'] = array();
				$this->jsonUtils->echo_json_msg (0, 'ok', $data );
				exit ();
			}
		} elseif($type == 3 || $type ==4) {
			$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
			
			$model = new Model ();
			$sql = "select c.title,c.id as problem_id,d.name as category_name,e.header,b.id as reply_id,b.reply_content,b.laud_count,b.collect_count
				from " . C ( 'DB_PREFIX' ) . "collect as a  left join
				" . C ( 'DB_PREFIX' ) . "answer_reply as b on a.obj_id=b.id
				left join " . C ( 'DB_PREFIX' ) . "system_user as e on e.id=b.reply_id 
				left join " . C ( 'DB_PREFIX' ) . "answer_problem as c on c.id=b.issue_id 
				left join " . C ( 'DB_PREFIX' ) . "answer_category as d on d.id=c.pid
				where a.member_id=$systemid and a.type = $type  order by a.addtime desc limit $limit";
			$arr = $model->query ( $sql );
			if (! $arr){
				$arr = array ();
			}else{
				foreach ($arr as $key =>$row){
					$arr[$key]['header'] = imgUrl($row['header']);
				}
			}
				
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		
		}
	
	}
	
	/**
	 * 收藏  // 1 收藏商家   3用户收藏问答 4 商家收藏问答
	 */
	public function collect() {
		$member_session_id = $_POST ['session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
		$obj_id = isset ( $_POST ['obj_id'] ) ? htmlspecialchars ( $_POST ['obj_id'] ) : '';
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '1';
		if (empty ( $obj_id ) || $obj_id == null) {
			$this->jsonUtils->echo_json_msg ( 1, '收藏id为空' );
			exit ();
		}
		if (! in_array ( $type, array (
				1,
				3,4,
		) )) {
			$this->jsonUtils->echo_json_msg ( 1, 'type错误' );
			exit ();
		}
		if($type == 1 ){
			$hostid = $member_id['id'];
		}elseif($type ==3 || $type ==4){
			$reply_id = M('AnswerReply')->where(array('id'=>$obj_id))->getField('reply_id');
			if(empty($reply_id)){
				$this->jsonUtils->echo_json_msg(4, '收藏的id不存在');exit();
			}
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
			$hostid = $systemid;
		}
		$data ['member_id'] = $hostid;
		$data ['obj_id'] = $obj_id;
		// $data['type']=$_POST['type'];
		$collect = M ( 'collect' );
		$arr = $collect->where ( "obj_id=$obj_id and member_id = $hostid and type=$type" )->find ();
		if ($arr) {
			$result = $collect->delete($arr['id']);
			if ($type == 1) {
				M ( 'Merchant' )->where ( array (
				'id' => $obj_id
				) )->setDec ( 'collect_count' );
			} elseif ($type == 3 ||$type ==4) {
					
				M ( 'AnswerReply' )->where ( array (
				'id' => $obj_id
				) )->setDec ( 'collect_count' );
				$collect_count = M ( 'AnswerReply' )->where ( array (
				'id' => $obj_id
				) )->getField('collect_count');
				//更新个人信息 收藏量
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$reply_id))->setDec('collect_count');
			}
			
			
			if ($result) {
				$this->jsonUtils->echo_json_data ( 0, '取消收藏成功',array('collect_count'=>$collect_count) );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '取消收藏失败！' );
				exit ();
			}
		}else{
			$data ['type'] = $type;
			$data ['addtime'] = time ();
			$collect = M ( 'collect' );
			$result = $collect->add ( $data );
			if ($type == 1) {
				M ( 'Merchant' )->where ( array (
						'id' => $obj_id 
				) )->setInc ( 'collect_count' );
			} elseif ($type == 3 ||$type ==4) {
			
				M ( 'AnswerReply' )->where ( array (
						'id' => $obj_id 
				) )->setInc ( 'collect_count' );
				//更新个人信息 收藏量
				$collect_count = M ( 'AnswerReply' )->where ( array (
						'id' => $obj_id
				) )->getField('collect_count');
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$reply_id))->setInc('collect_count');
			}
			if ($result) {
				$this->jsonUtils->echo_json_data ( 0, '收藏成功',array('collect_count'=>$collect_count)  );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '收藏失败！' );
				exit ();
			}
			
		}
		
		
		
	
	}
	
	/**
	 * 取消收藏
	 */
	public function cancelCollect() {
		$member_session_id = $_POST ['session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
		$obj_id = isset ( $_POST ['obj_id'] ) ? htmlspecialchars ( $_POST ['obj_id'] ) : '';
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '1';
		if (empty ( $obj_id )) {
			$this->jsonUtils->echo_json_msg ( 1, '收藏id为空' );
			exit ();
		}
		if (! in_array ( $type, array (
				1,
				3 ,4
		) )) {
			$this->jsonUtils->echo_json_msg ( 1, 'type错误' );
			exit ();
		}
		if($type == 1 ){
			$hostid = $member_id['id'];
		}elseif($type ==3 || $type ==4){
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
			$hostid = $systemid;
			$reply_id = M('AnswerReply')->where(array('id'=>$obj_id))->getField('reply_id');
			if(empty($reply_id)){
				$this->jsonUtils->echo_json_msg(4, '收藏的id不存在');exit();
			}
		}
		$collect = M ( 'collect' );
		$result = $collect->where ( "obj_id=$obj_id and member_id = $hostid and type=$type" )->delete ();
		if ($result) {
			if ($type == 1) {
				M ( 'Merchant' )->where ( array (
						'id' => $obj_id 
				) )->setDec ( 'collect_count' );
			} elseif ($type == 3 || $type ==4) {
				M ( 'AnswerReply' )->where ( array (
						'id' => $obj_id 
				) )->setDec ( 'collect_count' );
				//更新个人信息 收藏量
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$reply_id))->setDec('collect_count');
			}
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '取消收藏失败...' );
			exit ();
		}
	
	}
	
	/*******************************************************************************************
	 * Ver V2
	* @第二版本接口
	******************************************************************************************/
	
	
	/**
	 * 收藏列表
	 */
	public function collect_listV2() {
		$member_session_id = $_POST ['session_id'];
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		// 1 收藏商家 3用户收藏问答 4 商家收藏问答
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '1';
		$limit = ($page - 1) * $num . ',' . $num;
		if ($type == 1) {
			$member_id = $this->session_handle->getsession_userid ( $member_session_id );
			$model = new Model ();
			$sql = "select b.id as merchant_id ,b.header,b.merchant_name,d.name as city_name,c.name as area_name,b.address
			from " . C ( 'DB_PREFIX' ) . "collect as a  left join
			" . C ( 'DB_PREFIX' ) . "merchant as b on a.obj_id=b.id
			left join " . C ( 'DB_PREFIX' ) . "city as c on c.id=b.area_id
			left join " . C ( 'DB_PREFIX' ) . "city as d on d.id=b.city_id
			where a.member_id=$member_id and a.type = 1 limit $limit";
			$arr = $model->query ( $sql );
			if ($arr) {
				
				foreach ( $arr as $key => $value ) {
					$arr [$key] ['header'] = imgUrl ( $value ['header'] );
				}
				$data ['list'] = $arr;
				$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
				exit ();
			} else {
				$data ['list'] = array();
				$this->jsonUtils->echo_json_msg (0, 'ok', $data );
				exit ();
			}
		} elseif($type == 3 || $type ==4) {
			$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
				
			$model = new Model ();
			$sql = "select c.title,c.id as problem_id,d.name as category_name,e.header,b.id as reply_id,b.reply_content,b.laud_count,b.collect_count
			from " . C ( 'DB_PREFIX' ) . "collect as a  left join
			" . C ( 'DB_PREFIX' ) . "answer_reply as b on a.obj_id=b.id
			left join " . C ( 'DB_PREFIX' ) . "system_user as e on e.id=b.reply_id
			left join " . C ( 'DB_PREFIX' ) . "answer_problem as c on c.id=b.issue_id
			left join " . C ( 'DB_PREFIX' ) . "answer_category as d on d.id=c.pid
			where a.member_id=$systemid and a.type = $type  order by a.addtime desc limit $limit";
			$arr = $model->query ( $sql );
			if (! $arr){
				$arr = array ();
			}else{
				foreach ($arr as $key =>$row){
					$arr[$key]['header'] = imgUrl($row['header']);
				}
			}
	
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
	
		}
	
	}
	
// 	public function test(){
// 		dump(C('PUSH_RANGE_KM'));
// 		$ll_arr=rangekm(C('PUSH_RANGE_KM'), 118.164175,24.487151);//获取最大最小经纬度
// 		dump($ll_arr);
//         $maxLng=$ll_arr['maxLng'];
//         $minLng=$ll_arr['minLng'];
//         $maxLat=$ll_arr['maxLat'];
//         $minLat=$ll_arr['minLat'];
//         $sql="select a.business_time,a.id,b.id as jid from ". C('DB_PREFIX')."merchant as a
//         left join ". C('DB_PREFIX')."system_user as b on (a.id = b.sub_id and b.type =2)
//         where a.longitude <=$maxLng and a.longitude>=$minLng and a.latitude <=$maxLat and a.latitude>=$minLat and a.is_salesman = 0 and  a.status = 0";
//         $ids = M('')->query($sql);
//         dump($ids);
//         $ds = getDistance(24.487151, 118.164175, 24.506724, 118.138093);
//         dump($ds);
// 	}
	
	
// 	public function test() {
// 		$longitude = 118.168177;
// 		$latitude = 24.485699;
// 		$range = 180 / pi () * 20 / 6372.797; // 里面的 1 就代表搜索 1km 之内，单位km
// 		$lngR = $range / cos ( $latitude * pi () / 180 );
// 		$maxLat = $latitude + $range; // 最大纬度
// 		$minLat = $latitude - $range; // 最小纬度
// 		$maxLng = $longitude + $lngR; // 最大经度
// 		$minLng = $longitude - $lngR; // 最小经度
// 		$sql = "select * from ycbb_merchant where longitude<$maxLng and longitude>$minLng and latitude<$maxLat and latitude>$minLat";
// 		$arr = $this->dao->query ( $sql );
// 		echo $this->dao->getLastSql ();
// 		print_r ( $arr );
// 	}

}

?>