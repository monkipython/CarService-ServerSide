<?php
namespace App\Controller;
use Think\Think;
use Think\Controller;


/**
 * 推送模块（包括 问答 动态）--待开发
 */
class DeviceController extends Controller{
	private $jsonUtils;
	private $session_handle; // session 处理类
	public function __construct(){
			
		$this->jsonUtils=new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
	}
	
	public function register_device(){
		$device = isset ( $_POST ['device'] ) ? (int)htmlspecialchars ( $_POST ['device'] ) :'';//1 android  2 ios
		$dev_id = isset ( $_POST ['dev_id'] ) ? htmlspecialchars ( $_POST ['dev_id'] ) : '';
		$session_id = $_POST ['session_id'] ;
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		if(empty($device)||$device != '1'||$device != '2'){
			$this->jsonUtils->echo_json_msg(4, '设备不正确');exit();
		}
		if(empty($dev_id)){
			$this->jsonUtils->echo_json_msg(4, '设备号为空');exit();
		}
		$db = M('Device');
		//判断
		$data = $db ->where(array('dev_id'=>$dev_id,'device'=>$device))->getField('id');
		if($data){
			
		}
	}
}
?>