<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 业务端相关接口
 */
class SalesmanController extends Controller {
	
	private $jsonUtils;
	private $merchant;
	private $session_handle; // session 处理类
	
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->merchant = M ( 'Merchant' );
	
	}
	/**
	 * 搜索商家 by mobile
	 * return header,check_by,id,mobile,longitude,latitude
	 */
	function searchMerchantByPhone() {
		$salesman_session_id = isset ( $_POST ['salesman_session_id'] ) ? htmlspecialchars ( $_POST ['salesman_session_id'] ) : '';
		$mobile = isset ( $_POST ['mobile'] ) ? htmlspecialchars ( $_POST ['mobile'] ) : '';
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['pege'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$str = ($page - 1) * $num . ',' . $num;
		$salesman_id = $this->session_handle->getsession_userid ( $salesman_session_id );
		if (empty ( $mobile )){
			$this->jsonUtils->echo_json_msg ( 4, '请输入搜索的手机号' );
		}
		$map = array (
				'mobile' => $mobile,
				'is_salesman' => 0,
				'check_by' => array (
						array (
								'eq',
								0 
						),
						array (
								'eq',
								$salesman_id 
						),
						'or' 
				) 
		);
		
		$data = $this->merchant->where ( $map )->field ( 'header,is_check,id,mobile,longitude,latitude' )->order ( 'id desc' )->limit ( $str )->select ();
		
		if ($data === false) {
			$this->jsonUtils->echo_json_msg ( 4, '查询失败' );
			exit ();
		} else {
			
			if (! empty ( $data )) {
				foreach ( $data as $key => $row ) {
					$data [$key] ['header'] = imgUrl ( $row ['header'] );
				}
			} else {
				$data = array ();
			}
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
		}
	}
	/**
	 * 审核通过
	 * is_check
	 */
	function submitCheckBy() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$salesman_session_id = isset ( $_POST ['salesman_session_id'] ) ? htmlspecialchars ( $_POST ['salesman_session_id'] ) : '';
		$salesman_id = $this->session_handle->getsession_userid ( $salesman_session_id );
		$status = $this->merchant->where ( array (
				'id' => $id 
		) )->getField ( 'is_check' );
		// Log::write($this->merchant->getLastSql(),'dede');
		if ($status) {
			$this->jsonUtils->echo_json_msg ( 4, '已通过审核' );
			exit ();
		}
		$data = $this->merchant->where ( array (
				'id' => $id 
		) )->save ( array (
				'is_check' => 1,
				'check_by' => $salesman_id 
		) );
		if ($data === false) {
			$this->jsonUtils->echo_json_msg ( 4, '异常操作' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 0, 'ok' );
			exit ();
		}
	}
	
	/**
	 * 商家退出
	 * 
	 * @return [type] [description]
	 *        
	 */
	public function loginout() {
		$session_id = isset ( $_POST ['salesman_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['salesman_session_id'] ) ) : '';
		
		// dump($session_id);exit();
		if (empty ( $session_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家会话iD为空' );
			exit ();
		}
		$result = $this->session_handle->destroy ( $session_id );
		if ($result) {
			$this->jsonUtils->echo_json_msg ( 0, '退出成功!' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '退出失败！' );
			exit ();
		}
	
	}
	/**
	 * 提交商家详情
	 */
	
	function editMerchantInfo() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : ''; // 商家id
		$salesman_session_id = isset ( $_POST ['salesman_session_id'] ) ? htmlspecialchars ( $_POST ['salesman_session_id'] ) : '';
		$salesman_id = $this->session_handle->getsession_userid ( $salesman_session_id );
		// dump($salesman_id);
		if (empty ( $salesman_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '会话id不能为空' );
			exit ();
		}
		$merchant_name = isset ( $_POST ['merchant_name'] ) ? htmlspecialchars ( $_POST ['merchant_name'] ) : '';
		$area_id = isset ( $_POST ['area_id'] ) ? htmlspecialchars ( $_POST ['area_id'] ) : '';
		$address = isset ( $_POST ['address'] ) ? htmlspecialchars ( $_POST ['address'] ) : '';
		$manager = isset ( $_POST ['manager'] ) ? htmlspecialchars ( $_POST ['manager'] ) : '';
		$tel = isset ( $_POST ['tel'] ) ? htmlspecialchars ( $_POST ['tel'] ) : '';
		$business_time = isset ( $_POST ['business_time'] ) ? htmlspecialchars ( $_POST ['business_time'] ) : '';
		$longitude = isset ( $_POST ['longitude'] ) ? htmlspecialchars ( $_POST ['longitude'] ) : '';
		$latitude = isset ( $_POST ['latitude'] ) ? htmlspecialchars ( $_POST ['latitude'] ) : '';
		
		// $auth =
		// $this->merchant->where(array('id'=>$id))->getField('check_by');
		// if($auth==0) {
		// $this->jsonUtils->echo_json_msg(4, '数据异常');exit();
		// }
		// if($auth==$salesman_id){
		if(empty($id)){
			$this->jsonUtils->echo_json_msg ( 4, '商家id不能为空' );
			exit ();
		}
		if (empty ( $merchant_name )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家名不能为空' );
			exit ();
		}
		if (empty ( $address )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家地址不能为空' );
			exit ();
		}
		if (empty ( $manager )) {
			$this->jsonUtils->echo_json_msg ( 4, '负责人名字不能为空' );
			exit ();
		}
	
		if (empty ( $business_time )) {
			$this->jsonUtils->echo_json_msg ( 4, '营业时间为空' );
			exit ();
		}
		if (empty ( $longitude )) {
			$this->jsonUtils->echo_json_msg ( 4, '精度不能为空' );
			exit ();
		}
		if (empty ( $latitude )) {
			$this->jsonUtils->echo_json_msg ( 4, '维度不能空' );
			exit ();
		}
		if (empty ( $area_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '区域id不能空' );
			exit ();
		}
		if ($_FILES) {
			$result = mul_upload ( '/Header/',3 );
				
			if ($result) {
				$save ['header'] = $result [0];
			}
		}
		$area = CityController::getAreaIdPreId ( $area_id );
		$save ['province_id'] = $area ['province'];
		$save ['city_id'] = $area ['city'];
		$save ['area_id'] = $area_id;
		$save ['business_time'] = $business_time;
		$save ['merchant_name'] = $merchant_name;
		$save ['address'] = $address;
		$save ['manager'] = $manager;
		$save ['tel'] = $tel;
		$save ['longitude'] = $longitude;
		$save ['latitude'] = $latitude;
		
	
		
		$check = AuthController::addData ( $save, 0, 'save', array (
				'salesman'=>$salesman_id 
		), $id );
		
		if ($check) {
			
			$this->jsonUtils->echo_json_msg ( 0, '已提交审核' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '修改失败' );
			exit ();
		}
		// }else{
		// $this->jsonUtils->echo_json_msg(4, '无权限操作');exit();
		// }
	
	}
	/**
	 * 获取商家编辑信息
	 */
	function getMerchantInfo() {
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : ''; // 商家id
		$salesman_session_id = isset ( $_POST ['salesman_session_id'] ) ? htmlspecialchars ( $_POST ['salesman_session_id'] ) : '';
		$salesman_id = $this->session_handle->getsession_userid ( $salesman_session_id );
		if (empty ( $salesman_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '会话id不能为空' );
			exit ();
		}
		$db = M('Auth');
		$auth = $db ->where(array('mark_id'=>$id,'status'=>0))->find();
		if($auth == false){
			$data = $this->merchant->where ( array (
					'id' => $id 
			) )->field ( 'province_id,city_id,area_id,business_time,merchant_name,address,manager,tel,longitude,latitude,header' )->find ();
			
			$data ['status'] = '0';
		}else{
			$data = json_decode($auth['check_data'],true);
			$data ['status'] = '1';
		}
		if ($data) {
			$data ['header'] = imgUrl ( $data ['header'] );
			$data ['province_name'] = CityController::getName ( $data ['province_id'] );
			$data ['city_name'] = CityController::getName ( $data ['city_id'] );
			$data ['area_name'] = CityController::getName ( $data ['area_id'] );
			$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
			exit ();
				
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '该商家不存在' );
			exit ();
		}
		
		
	}

}

?>