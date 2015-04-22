<?php


use Home\Controller\CurlController;

class AuthController extends CommonController{
  
	public function index(){
        $auth = M('Auth')->where(array('status'=>0))->select();
//         $tmp = array();
//         foreach($category as $val){
//             $tmp[$val['id']]= $val['name'];   
//         }
		foreach($auth as $key =>$row){
			$auth[$key]['check_data'] = json_decode($row['check_data'],true);
			$auth[$key]['data_org'] = json_decode($row['data_org'],true);
			if($row['status']){
				$auth[$key]['status'] = '已审核';
			} else{
				$auth[$key]['status'] = '未审核';
			}
		}
// 		dump($auth);
        $this->assign('list',$auth);
        $this->display();
// 		parent::index();
	}
	function edit(){
		$db = M('Auth');
		$data =$db-> where(array('id'=>$_GET['id']))->find();
		$data['check_data'] = json_decode($data['check_data'],true);
		$data['check_data']['header_location'] = imgUrl($data['check_data']['header']);
		$url = "http://121.40.92.53/ycbb/index.php/App/City/city_list";
		$province = CurlController::curl($url, array());
		$city = CurlController::curl($url, array('pid'=>$data['check_data']['province_id']));
		$area = CurlController::curl($url, array('pid'=>$data['check_data']['city_id']));
		$this->assign('vo',$data);
		$this->assign('province',$province['data']['list']);
		$this->assign('city',$city['data']['list']);
		$this->assign('area',$area['data']['list']);
		$this->display();
	}
	function update(){
		$id =$_POST['id'];
		$post ['merchant_name'] = $_POST['merchant_name'];
		$post ['tel'] = $_POST['tel'];
		$post ['province_id'] = $_POST['province_id'];
		$post ['city_id'] = $_POST['city_id'];
		$post ['area_id'] = $_POST['area_id'];
		$post ['address'] = $_POST['address'];
		$post ['business_time'] = $_POST['business_time'];
		$post ['manager'] = $_POST['manager'];
		$post ['longitude'] = $_POST['longitude'];
		$post ['latitude'] = $_POST['latitude'];
		
		$db = M('Auth');
		$data =$db-> where(array('id'=>$id))->find();
		$save  = array_merge(json_decode($data['check_data'],true),$post);
		$action = $db ->where(array('id'=>$id))->save(array('check_data'=>json_encode($save)));
		
		if($action ===false){
			$this->ajaxReturn(array('code'=>1,'msg'=>'审核失败'),'json');exit();
		}else{
			$url = "http://121.40.92.53/ycbb/index.php/App/Auth/execAction";
			$rel = CurlController::curl($url, array('id'=>$id));
			if($rel['code'] == 0){
				$this->ajaxReturn(array('code'=>0,'msg'=>'审核成功'),'json');exit();
			}else{
				$this->ajaxReturn(array('code'=>1,'msg'=>'审核失败'),'json');exit();
			}
		}
	
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
				echo $exec_db->getLastSql();
				echo 'error';
	
			}else{
	
				if($db_no == 0){
					if(!empty($check_data["merchant_name"])){
						saveName($data['mark_id'], 2, $check_data["merchant_name"]);
					}
					if(!empty($check_data["header"])){
						saveHeader($data['mark_id'], 2, $check_data["header"]);
					}
				}
				$db ->where("id =$id")->save(array('status'=>1));//审核通过
				$this->ajaxReturn(array('code'=>0,'msg'=>'审核成功'));
			}
		}else{
			$this->ajaxReturn(array('code'=>1,'msg'=>'已操作过，无需审核'));
		}
	
	
	
	
	}
	
	
	
	
    
}


