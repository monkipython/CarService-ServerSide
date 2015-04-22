<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
/**
 * 修改数据审核  
 * （暂用于 业务端提交信息审核）
 * @author zhangzhi
 *
 */
class AuthController extends Controller{

	/**
	 * @param array $data 存储数据 
	 * @param int $db_no 操作数据库no.  ex:0
	 * @param varchar $action 操作方法  ex: save
	 * @param array $org  ex:array('member','id') 数据来源
	 * @param int $mark  标识更新主键  
	 * @return bool
	 */
	
	static function addData( $data,  $db_no,  $action,  $org, $mark){
			
		$db = M('Auth');
		$status = AuthController::checkDbConfig($db_no);
		if($status){
			$save['check_data'] = json_encode($data);
			$save['db_no'] = $db_no;
			$save['check_action'] = $action;
			$save['data_org'] = json_encode($org);
			$save['addtime'] = time();
			$save['status'] = 0;//未审核
			$save['mark_id'] = $mark;
			$auth_id = $db ->where(array('db_no'=>$db_no,'mark_id'=>$mark))->getField('id');
			if($auth_id){
				unset($save['db_no']);
				unset($save['mark_id']);
				$rel = $db ->where(array('id'=>$auth_id))->save($save);
			}else{	
		
				$rel = $db ->add($save);
			}
			return  $rel;
		}else{
			return 0;
		}
	}
	/**
	 * 检测数据库是否存在
	 * @param int $id
	 * @return boolean
	 */
	static function checkDbConfig($id){

		$ids = explode(',', $id);
		$arr = C('AUTH_DB_CONFIG');
		if(is_array($ids)&&count($ids)>=2){
			foreach ($ids as $key=>$row){
				if(!$arr[$row]){
					return false;
				}
			}
		}else{
			if(!$arr[$id]){
				return false;
			}
		}
		
		return true;
	}
	/**
	 * 执行审核通过
	 * @param int $id
	 */
	
	function execAction (){
		$id = $_REQUEST['id'];
		if(empty($id)) return '';
		$arr = C('AUTH_DB_CONFIG');
		$db = M('Auth');
		$data = $db ->where("id =$id")->find();
		
		if($data['status'] ==0){
			$check_data = json_decode($data['check_data'],true);
			$db_no = $data['db_no'];
			$action = $data['check_action'];
			//商家操作 只能支持save 有触发器 关联聊天
			if($db_no==0 ){
				if($action !='save'){
					die('错误操作') ;
				}
			}
			$where = array('id'=>$data['mark_id']);
			$exec_db = M($arr[$db_no]);
		
			switch ($action){
				case 'save':
					$exec = $exec_db->where($where)->save($check_data);
					break;
				case 'add'://未启用
					$exec = $exec_db->add($check_data);
					break;
				case 'del':
					$exec = $exec_db->where($where)->delete();
					break;
				default:
					die('错误数据：action');
					break;
					
			}
			
			if($exec === false){
				$this->ajaxReturn(array('code'=>1,'msg'=>$exec_db->getLastSql()));exit();
				
			}else{
	
				if($db_no == 0){
					//同步更新 商户名称和头像
					if(!empty($check_data["merchant_name"])){
						CommonController::saveName($data['mark_id'], 2, $check_data["merchant_name"]);
					}
					if(!empty($check_data["header"])){
						CommonController::saveHeader($data['mark_id'], 2, $check_data["header"]);
					}
				}
				$db ->where("id =$id")->save(array('status'=>1));//审核通过
				$this->ajaxReturn(array('code'=>0,'msg'=>'审核成功'));exit();
			}
		}else{
			$this->ajaxReturn(array('code'=>1,'msg'=>'已操作过，无需审核'));exit();
		}

	
	
	
	}

	
  
        
      

}
?>