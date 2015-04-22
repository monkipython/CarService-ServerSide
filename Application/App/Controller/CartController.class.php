<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 用户车辆 管理接口
 */
class CartController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	private $session_dao;
	
	
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
		$this->dao = M ( 'cart' );
		$this->session_dao = M ( 'member_session' );
		parent::__construct();
	}
	/**
	 * 获取指定id的cart_model
	 * @param unknown_type $id
	 */
	static public function getCartModel($id) {
		$cartModel = M ( 'Cart' )->where ( array (
				'id' => $id
		) )->getField ( 'cart_model' );
		return $cartModel;
	}
	/**
	 * 获取指定id的车辆信息
	 * @param int $id
	 */
	static public function getCartInfo($id) {
		$cart = M ( 'Cart' )->where ( array (
				'id' => $id
		) )->find ();
		return $cart;
	}
	/**
	 * 修改指定id的 行驶公里数 和新车上路时间
	 * @param int $id
	 * @param int $km
	 * @param int $time
	 */
	public function editCartInfo($id, $km, $time) {
		$data = M ( 'Cart' )->where ( array (
				'id' => $id
		) )->save ( array (
				'km' => $km,
				'carttime' => $time
		) );
		return $data;
	}
	
	/**
	 * 获取指定用户的
	 * 车辆列表
	 */
	public function myCar() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$arr = $this->dao->query ( "select a.id as cart_id,b.icon,a.cart_model,a.car_number,a.frame_number,a.default_cart from " . C ( 'DB_PREFIX' ) . "cart as a left join " . C ( 'DB_PREFIX' ) . "car_brand as b on b.id = a.brand_id where a.member_id=$member_id order by a.default_cart desc" );
		if ($arr) {
			foreach ($arr as $key =>$row){
				$brand = explode(',', $row['cart_model']);
				$arr [$key] ['icon'] = imgUrl ( $row['icon'] );
				$arr [$key] ['brand_name'] = $brand[0];
				$arr [$key] ['view_name'] = $brand[1];
			}
		
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '暂无车辆信息...' );
			exit ();
		}
	}
	
	/**
	 * 获取制定用户的指定车辆详情
	 */
	public function getCar() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$car_id = isset ( $_POST ['car_id'] ) ? htmlspecialchars ( $_POST ['car_id'] ) : '';
		if (empty ( $car_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车辆ID为空...' );
			exit ();
		}
		$model = new Model ();
		$arr = $this->dao->query ( "select a.id as car_id,a.cart_model,a.brand_id,b.icon,a.carsview_id,a.model_id,a.car_number,a.engine_number,a.frame_number,a.carttime from " . C ( 'DB_PREFIX' ) . "cart as a left join " . C ( 'DB_PREFIX' ) . "car_brand as b on b.id = a.brand_id where a.id=$car_id and a.member_id =$member_id" );
		$brand = explode(',', $arr[0]['cart_model']);
		$arr [0] ['brand_name'] = $brand[0];
		$arr [0] ['view_name'] = $brand[1];
		$arr [0]['carttime'] = empty(date('Y-m-d',$arr[0]['carttime']))? '' :date('Y-m-d',$arr[0]['carttime']);
		$arr [0]['icon'] =imgUrl($arr[0]['icon']);
		if ($arr) {
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '获取车辆信息失败...' );
			exit ();
		}
	
	}
	/**
	 * 获取指定用户的默认车型
	 */
	public function getDefaultCar(){
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$db =M('Cart');
		$data = $db ->where(array('member_id'=>$member_id,'default_cart'=>1))->find();
		$data ['addtime'] = date('Y-m-d H:i:s',$data['addtime']);
		$data ['carttime'] = !empty($data['carttime'])?date('Y-m-d',$data['carttime']):'';
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	}
	/**
	 * 添加车辆信息
	 */
	public function addCar() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$brand_id = isset ( $_POST ['brand_id'] ) ? htmlspecialchars ( $_POST ['brand_id'] ) : '';
		$carsview_id = isset ( $_POST ['carsview_id'] ) ? htmlspecialchars ( $_POST ['carsview_id'] ) : '';
		$model_id = isset ( $_POST ['model_id'] ) ? htmlspecialchars ( $_POST ['model_id'] ) : '';
		$carttime = isset ( $_POST ['carttime'] ) ? htmlspecialchars ( $_POST ['carttime'] ) : '';
		$carnumber = isset ( $_POST ['car_number'] ) ? htmlspecialchars ( $_POST ['car_number'] ) : '';
		$enginenumber = isset ( $_POST ['engine_number'] ) ? htmlspecialchars ( $_POST ['engine_number'] ) : '';
		$framenumber = isset ( $_POST ['frame_number'] ) ? htmlspecialchars ( $_POST ['frame_number'] ) : '';
		
		if(empty($brand_id)){
			$this->jsonUtils->echo_json_msg ( 4, '车辆品牌为空...' );
			exit ();
		}
		if(empty($carsview_id)){
			$this->jsonUtils->echo_json_msg ( 4, '车系为空...' );
			exit ();
		}
		if(empty($model_id)){
			$this->jsonUtils->echo_json_msg ( 4, '车辆模型为空...' );
			exit ();
		}
		if(!empty($carttime)){
			$data ['carttime'] = strtotime($carttime);
		}
		if(!empty($carnumber)){
			$data ['car_number'] = $carnumber;
		}
		if(!empty($enginenumber)){
			$data ['engine_number'] = $enginenumber;
		}
		if(!empty($framenumber)){
			$data ['frame_number'] = $framenumber;
		}
		$cart_model[] = CartBrandController::getName($brand_id);
		$cart_model[] = CartBrandController::getName($carsview_id);
		$cart_model[] = CartBrandController::getName($model_id);
		$cart_model =implode(',', $cart_model);
		$data ['brand_id'] = $brand_id;
		$data ['carsview_id'] = $carsview_id;
		$data ['model_id'] = $model_id;
		$data ['cart_model'] = $cart_model;
 		$data ['member_id'] = $member_id;
		$data ['addtime'] = time ();
		
		$status = $this->dao->where(array('member_id'=>$member_id,'default_cart'=>1))->getField('id');
		if(!$status){
			$data ['default_cart'] = 1;
		}
		$result = $this->dao->add ( $data );
		if ($result) {
			if($data ['default_cart'] == 1){
				$brand_icon = $this->dao->table(C('DB_PREFIX')."cart as a")->join(C('DB_PREFIX')."car_brand as b on a.brand_id = b.id")
				->where("a.id=$result ")->field('b.icon')->find();
				M('SystemUser')->where(array('sub_id'=>$member_id,'type'=>0))->save(array('brand_icon'=>$brand_icon['icon']));
			}
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '添加车辆信息失败...' );
			exit ();
		}
	
	}
	/**
	 * 修改车辆
	 */
	public function modCar() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$car_id = isset ( $_POST ['car_id'] ) ? htmlspecialchars ( $_POST ['car_id'] ) : '';
		$brand_id = isset ( $_POST ['brand_id'] ) ? htmlspecialchars ( $_POST ['brand_id'] ) : '';
		$carsview_id = isset ( $_POST ['carsview_id'] ) ? htmlspecialchars ( $_POST ['carsview_id'] ) : '';
		$model_id = isset ( $_POST ['model_id'] ) ? htmlspecialchars ( $_POST ['model_id'] ) : '';
		
		$carttime = isset ( $_POST ['carttime'] ) ? htmlspecialchars ( $_POST ['carttime'] ) : '';
		$carnumber = isset ( $_POST ['car_number'] ) ? htmlspecialchars ( $_POST ['car_number'] ) : '';
		$enginenumber = isset ( $_POST ['engine_number'] ) ? htmlspecialchars ( $_POST ['engine_number'] ) : '';
		$framenumber = isset ( $_POST ['frame_number'] ) ? htmlspecialchars ( $_POST ['frame_number'] ) : '';
		
		if(empty($brand_id)){
			$this->jsonUtils->echo_json_msg ( 4, '车辆品牌为空...' );
			exit ();
		}
		if(empty($carsview_id)){
			$this->jsonUtils->echo_json_msg ( 4, '车系为空...' );
			exit ();
		}
		if(empty($model_id)){
			$this->jsonUtils->echo_json_msg ( 4, '车辆模型为空...' );
			exit ();
		}
		if(!empty($carttime)){
			$data ['carttime'] = strtotime($carttime);
		}
		if(!empty($carnumber)){
			$data ['car_number'] = $carnumber;
		}
		if(!empty($enginenumber)){
			$data ['engine_number'] = $enginenumber;
		}
		if(!empty($framenumber)){
			$data ['frame_number'] = $framenumber;
		}
		$cart_model[] = CartBrandController::getName($brand_id);
		$cart_model[] = CartBrandController::getName($carsview_id);
		$cart_model[] = CartBrandController::getName($model_id);
		$cart_model =implode(',', $cart_model);
		$data ['brand_id'] = $brand_id;
		$data ['carsview_id'] = $carsview_id;
		$data ['model_id'] = $model_id;
		$data ['cart_model'] = $cart_model;
		

		$result = $this->dao->where(array('id'=>$car_id,'member_id'=>$member_id))->save ( $data );
		if ($result === false) {
			$this->jsonUtils->echo_json_msg ( 1, '修改车辆信息失败...' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
			
		}
	
	}
	
	/**
	 * 删除车辆
	 */
	public function delCar() {
		$car_id = ( int ) $_POST ['car_id'];
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $car_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车ID为空...' );
			exit ();
		}
		$default = $this->dao->where ( "id=$car_id and member_id = $member_id" )->getField('default_cart');
		
		$result = $this->dao->where ( "id=$car_id and member_id = $member_id" )->delete ();
		if ($result) {
			if($default){
				

				$default_car = $this->dao->where ( "member_id = $member_id" )->order('id asc')->field('id,brand_id')->find();
				if($default_car){
					$this->dao->where(array('id'=>$default_car['id']))->save (array('default_cart'=>1));
					$brand_icon = M('CarBrand')->where("id=".$default_car['brand_id'] )->getField('icon');
					M('SystemUser')->where(array('sub_id'=>$member_id,'type'=>0))->save(array('brand_icon'=>$brand_icon));
				}else{
					M('SystemUser')->where(array('sub_id'=>$member_id,'type'=>0))->save(array('brand_icon'=>''));
				}
			}
		
			$this->jsonUtils->echo_json_msg ( 0, '删除成功...' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '删除错误...' );
			exit ();
		}
	}
	/**
	 * 切换默认车型
	 */
	public function setDefaultCar(){
		$car_id = ( int ) $_POST ['car_id'];
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		if (empty ( $car_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车ID为空...' );
			exit ();
		}
		$res = $this->dao->where ( "member_id = $member_id and default_cart =1 " )->save (array('default_cart'=>0));
		$result = $this->dao->where ( "id=$car_id and member_id = $member_id" )->save (array('default_cart'=>1));
		$brand_icon = $this->dao->table(C('DB_PREFIX')."cart as a")->join(C('DB_PREFIX')."car_brand as b on a.brand_id = b.id")
					->where("a.id=$car_id ")->field('b.icon')->find();
		M('SystemUser')->where(array('sub_id'=>$member_id,'type'=>0))->save(array('brand_icon'=>$brand_icon['icon']));
		if ($result ===false) {
			
			$this->jsonUtils->echo_json_msg ( 1, '错误...' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 0, '成功...' );
			exit ();
		}
	
	}
	
	
	
	/**
	 * 获取指定用户的
	 * 车辆列表
	 */
	public function myCarV2() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$arr = $this->dao->query ( "select a.id as car_id,b.icon,a.cart_model,a.default_cart from " . C ( 'DB_PREFIX' ) . "cart as a left join " . C ( 'DB_PREFIX' ) . "car_brand as b on b.id = a.brand_id where a.member_id=$member_id order by a.default_cart desc" );
		if ($arr) {
			foreach ($arr as $key =>$row){
				$brand = explode(',', $row['cart_model']);
				$arr [$key] ['icon'] = imgUrl ( $row['icon'] );
// 				$arr [$key] ['brand_name'] = $brand[0];
				$arr [$key] ['view_name'] = $brand[1];
				unset($arr[$key]['cart_model']);
			}
	
			$data ['list'] = $arr;
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '暂无车辆信息...' );
			exit ();
		}
	}
	/**
	 * 获取制定用户的指定车辆详情
	 */
	public function getCarV2() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$car_id = isset ( $_POST ['car_id'] ) ? htmlspecialchars ( $_POST ['car_id'] ) : '';
		if (empty ( $car_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '车辆ID为空...' );
			exit ();
		}
		$model = new Model ();
		$arr = $this->dao->query ( "select a.id as car_id,a.cart_model,b.icon,a.brand_id,a.carsview_id,a.model_id,a.car_number,a.frame_number from " . C ( 'DB_PREFIX' ) . "cart as a left join " . C ( 'DB_PREFIX' ) . "car_brand as b on b.id = a.brand_id where a.id=$car_id and a.member_id =$member_id" );
		$brand = explode(',', $arr[0]['cart_model']);
		$arr [0] ['brand_name'] = $brand[0];
		$arr [0] ['view_name'] = $brand[1];
		$arr [0] ['model_name'] = $brand[2];
		$arr [0]['icon'] =imgUrl($arr[0]['icon']);
		if ($arr) {
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '获取车辆信息失败...' );
			exit ();
		}
	
	}
	/**
	 * 获取指定用户的默认车型
	 */
	public function getDefaultCarV2(){
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$db =M('Cart');
		$data = $db->table(C('DB_PREFIX')."cart as a")->field('a.id ,b.icon,a.cart_model,a.car_number,a.frame_number,a.default_cart')
		->join(C('DB_PREFIX')."car_brand as b on a.brand_id = b.id",'LEFT')
		->where(array('a.member_id'=>$member_id,'a.default_cart'=>1))->find();
		// 		echo $db->getLastSql();
		if($data === false){
			$data = array();
		}else{
			$brand = explode(',', $data['cart_model']);
			$data ['brand_name'] = $brand[0];
			$data ['view_name'] = $brand[1];
			$data['icon'] =imgUrl($data['icon']);
			$data ['addtime'] = date('Y-m-d H:i:s',$data['addtime']);
			$data ['carttime'] = !empty($data['carttime'])?date('Y-m-d',$data['carttime']):'';
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	}
	
	

}

?>