<?php
namespace App\Controller;
use Think\Log;
use Think\Controller;
use Think\Model;

/**
 * service
 */
class MerServiceController extends Controller {
	
	private $jsonUtils;
	private $dao; // 主表
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
		$this->dao = M ( 'service' );
		parent::__construct();
	
	}
	/**
	 * 添加项目--选择项目
	 * 标识出已添加过的项目和正在审核的项目
	 */
	public function merServiceSelectList() {
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		
		$data = $this->dao->table ( C ( 'DB_PREFIX' ) . 'category as ca' )->field ( 'ca.id,ca.name,ca.pid,ifnull(se.id,0) as own' )->join ( C ( 'DB_PREFIX' ) . "service as se on (ca.id = se.cat_id) and se.merchant_id = $merchant_id ", 'LEFT' )->where ( "ca.status = 1 " )->order ( 'ca.id asc' )->select ();
		
		$arr = array ();
		// id 提至 key
		foreach ( $data as $temp => $rel ) {
			$arr [$rel ['id']] = $rel;
		}
		// 结果 排序
		foreach ( $arr as $key => $row ) {
			if ($row ['pid'] == 0) {
				unset ( $row ['own'] );
				unset ( $row ['id'] );
				$redata [$key] = $row;
			} else {
				if ($row ['own'] > 0) {
					$row ['own'] = '1';
				}
				$redata [$row ['pid']] ['child'] [] = $row;
			}
		
		}
		
	
	 	$redata = array_values ( $redata );
		
		$this->jsonUtils->echo_json_data ( 0, 'ok', $redata );
	
	}
	
	/**
	 * 添加服务项目
	 */
	public function add_service() {
		
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( $_POST ['intro'] ) : '';
		$price = isset ( $_POST ['price'] ) ? htmlspecialchars ( $_POST ['price'] ) : '';
		$sub_id = isset ( $_POST ['sub_id'] ) ? htmlspecialchars ( $_POST ['sub_id'] ) : '';
		$timeout = isset ( $_POST ['timeout'] ) ? htmlspecialchars ( $_POST ['timeout'] ) : '';
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( $_POST ['mer_session_id'] ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		
		if ($price == null || $price == '' || ! is_numeric ( $price )) {
			$this->jsonUtils->echo_json_msg ( 4, "项目价格不符合格式！" );
			exit ();
		}
		if ($this->dao->where ( array (
				'cat_id' => $sub_id,
				'merchant_id' => $merchant_id 
		) )->field ( 'id' )->find ()) {
			$this->jsonUtils->echo_json_msg ( 1, '已添加过该服务！' );
			exit ();
		}
		
		$cate = CategoryController::getCategoryById ( $sub_id );
		$postion = CommonController::getMerchantPosition ( $merchant_id );
		
		$data ['province_id'] = $postion ['province_id'];
		$data ['city_id'] = $postion ['city_id'];
		$data ['area_id'] = $postion ['area_id'];
		$data ['name'] = $cate ['name'];
		$data ['merchant_id'] = $merchant_id;
		$data ['intro'] = $intro;
		$data ['price'] = $price;
		$data ['pcat_id'] = $cate ['pid'];
		$data ['cat_id'] = $sub_id;
		$data ['timeout'] = $timeout;
		$data ['addtime'] = time ();
		$data ['pics'] = "[]";
		$result = $this->dao->add ( $data );
		
		if ($_FILES) {
			
			$arr = mul_upload ( '/Service/',1 );
			if ($arr) {
				
				$data1 ['pics'] = json_encode ( $arr ); // 把多张图片数组格式转json保存数据库
				$this->dao->where ( "id=$result" )->save ( $data1 );
			}
		}
		if ($result) {
			
			$this->jsonUtils->echo_json_msg ( 0, '添加成功！' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '添加失败！' );
			exit ();
		}
	
	}
	/**
	 * 获取服务项目详情
	 * 
	 * @return [type] [description]
	 */
	public function get_service() {
		$service_id = isset ( $_POST ['service_id'] ) ? htmlspecialchars ( trim ( $_POST ['service_id'] ) ) : '';
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		if (empty ( $service_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '服务项目ID为空！' );
			exit ();
		}
		
		$arr = $this->dao->query ( "select a.id as service_id,b.name as service_pname,a.timeout as time,a.name as service_name,a.price,a.intro,a.pics as img,a.effect from " . C ( 'DB_PREFIX' ) . "service as a join " . C ( 'DB_PREFIX' ) . "category as b on a.pcat_id = b.id  where a.id=$service_id and a.merchant_id=$merchant_id " );
		
		if ($arr) {
			
			if (! empty ( $arr [0] ['img'] )) {
				
				$arr [0] ['img'] = imgUrl ( json_decode ( $arr [0] ['img'] ,true) );
			}
			
			
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '获取该项目信息错误！' );
			exit ();
		}
	
	}
	
	/**
	 * 修改项目
	 * 
	 * @return [type] [description]
	 */
	public function mod_service() {
		$service_id = isset ( $_POST ['service_id'] ) ? htmlspecialchars ( $_POST ['service_id'] ) : '';
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( $_POST ['intro'] ) : '';
		$price = isset ( $_POST ['price'] ) ? htmlspecialchars ( $_POST ['price'] ) : '';
		// $sub_id=isset($_POST['sub_id']) ?
		// htmlspecialchars($_POST['sub_id']):'';
		$timeout = isset ( $_POST ['timeout'] ) ? htmlspecialchars ( $_POST ['timeout'] ) : '';
		$mod_img = isset ( $_POST ['mod_img'] ) ? htmlspecialchars ( $_POST ['mod_img'] ) : '0';
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		if(!empty($price)){
			if (! is_numeric ( $price )) {
				$this->jsonUtils->echo_json_msg ( 4, "项目价格不符合格式！" );
				exit ();
			}else{
				$data ['price'] = $price;
			}
		}
		
		// $cate = CategoryController::getCategoryById($sub_id);
		// if($cate){
		// $data['name']=$cate['name'];
		// }
		
		if ($intro) {
			$data ['intro'] = $intro;
		}
		if ($timeout) {
			$data ['timeout'] = $timeout;
		}
		// $data['pcat_id']=$cate['pid'];
		// $data['cat_id']=$sub_id;
		$data ['effect'] = 0;
// 		if($mod_img){
			if ($_FILES) {
				$f_arr = mul_upload ( '/Service/',1 );
				if ($f_arr) {
					$data ['pics'] = json_encode ( $f_arr );
				}
			}
// else{
// 				$data ['pics'] = '[]';
// 			}
// 		}
		$result = $this->dao->where ( "id=$service_id and merchant_id = $merchant_id" )->save ( $data ); // 保存商家信息
		
		if ($result===false) {
			$this->jsonUtils->echo_json_msg ( 4, '修改失败！' );
			exit ();
		}
	
		
		$this->jsonUtils->echo_json_msg ( 0, '修改成功！' );
		exit ();
	
	}
	
	/**
	 * 商家服务项目列表
	 * 
	 * @return [type] [description]
	 *         不传classid 获取该商户所有下的所有项目
	 */
	public function service_list() {
		
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		$pagenum = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		$classid = isset ( $_POST ['classid'] ) ? htmlspecialchars ( $_POST ['classid'] ) : '';
		$pagenum = ($pagenum - 1) * $num;
		
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$map = "merchant_id=$merchant_id ";
		$sql = "select se.name as service_name,se.id as service_id,ca.icon,
      	se.pcat_id as classid,se.cat_id as sub_id ,se.price,se.sell_num,se.effect, se.timeout 
      	from  " . C ( 'DB_PREFIX' ) . "service as se 
      	join " . C ( 'DB_PREFIX' ) . "category as ca on se.cat_id = ca.id where  merchant_id=$merchant_id ";
		if (! empty ( $classid )) {
			$sql = $sql . " and pcat_id=$classid";
			$map =$map." and pcat_id =$classid ";
		}
		$sql = $sql . "  group by cat_id order by se.addtime desc limit $pagenum,$num ";
		
		$arr = $this->dao->query ( $sql );
		$count = $this->dao->where($map)->count();
		if ($arr) {
			foreach ( $arr as $key => $row ) {
				$arr [$key] ['icon'] = imgUrl ( $row ['icon'] );
			}

		} else {
			$arr = array();
		}
		$data ['list'] = $arr;
		$data ['count'] = $count;
 		$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
	
	}
	
	/**
	 * 删除服务项目
	 * 
	 * @return [type] [description]
	 */
	public function del_service() {
		$service_id = $_POST ['service_id'];
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		if (empty ( $service_id )) {
			$this->jsonUtils->echo_json_msg ( 4, "服务项目ID为空！" );
			exit ();
		} else {
			$result = $this->dao->where ( "id=$service_id and merchant_id =$merchant_id " )->delete (); // 标识删除
			
			if ($result) {
				$this->jsonUtils->echo_json_msg ( 0, '删除成功！' );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, "删除失败！没权限" );
				exit ();
			}
		}
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*******************************************************************************************
	 * Ver V2
	* @第二版本接口
	******************************************************************************************/
	
	
	
	
	
	/**
	 * 修改项目
	 *
	 * @return [type] [description]
	 */
	public function mod_serviceV2() {
		$service_id = isset ( $_POST ['service_id'] ) ? htmlspecialchars ( $_POST ['service_id'] ) : '';
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( $_POST ['intro'] ) : '';
		$price = isset ( $_POST ['price'] ) ? htmlspecialchars ( $_POST ['price'] ) : '';
		// $sub_id=isset($_POST['sub_id']) ?
		// htmlspecialchars($_POST['sub_id']):'';
		$timeout = isset ( $_POST ['timeout'] ) ? htmlspecialchars ( $_POST ['timeout'] ) : '';
		$mer_session_id = $_POST ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$pics = isset ( $_POST ['pics'] ) ?  ( trim ( $_POST ['pics'] ) ) : '';
		if(!empty($price)){
			if (! is_numeric ( $price )) {
				$this->jsonUtils->echo_json_msg ( 4, "项目价格不符合格式！" );
				exit ();
			}else{
				$data ['price'] = $price;
			}
		}
		if ($intro) {
			$data ['intro'] = $intro;
		}
		if ($timeout) {
			$data ['timeout'] = $timeout;
		}
		$data ['effect'] = 0;
		if(!empty($pics)){
			$arr_decode = json_decode($pics,true);
			if(!empty($arr_decode)){
				foreach ($arr_decode as $key =>$row){
					if(!empty($row)){
						$temp[$key]['hs'] = str_replace(C('ROOT_UPLOADS'), '', $row['hs']);
						$temp[$key]['hb'] = str_replace(C('ROOT_UPLOADS'), '', $row['hb']);
					}
				}
				foreach ($temp as $row){
					$data['pics'][] = $row;
				}
			}else{
				$data['pics'] = array();
			}
		}else{
			$data['pics'] = array();
		}
		if ($_FILES) {
			$f_arr = mul_upload ( '/Merchant/',1 );
			if ($f_arr) {
				$data ['pics'] =array_merge($data['pics'],$f_arr); // 把多张图片数组格式转json保存数据库
			}
	
		}
		$data['pics'] = json_encode($data['pics']);
		$result = $this->dao->where ( "id=$service_id and merchant_id = $merchant_id" )->save ( $data ); // 保存商家信息
	
		if ($result===false) {
			$this->jsonUtils->echo_json_msg ( 4, '修改失败！' );
			exit ();
		}
	
	
		$this->jsonUtils->echo_json_msg ( 0, '修改成功！' );
		exit ();
	
	}
	
	

}
?>