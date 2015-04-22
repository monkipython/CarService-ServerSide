<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;


/**
 * 配置信息
 */
class ConfigController extends Controller{
      

    
      private $jsonUtils;
      private $dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->dao=M('Config');
         
      }

      //服务条款
      public function config(){
        $key=$_POST['key'];
        if(empty($key)){
          $this->jsonUtils->echo_json_msg(4,'key参数为空...');exit();
        }
        $arr=$this->dao->field("value")->where("key='$key'")->select();
        if($arr){
           $this->JsonUtils->echo_json_data(0,'ok',$arr[0]['value']);exit();
        }else{
           $this->jsonUtils->echo_json_data(0,'ok','');
        }

      }

     //安卓用户端版本更新
      public function merchant_version_update(){
        $version_code=(int)$_POST['version_code'];
        if(empty($version_code)){
             $this->jsonUtils->echo_json_msg(4,"版本号为空...");exit();
        }
        $arr=$this->dao->field("value")->where("key='version_code'")->select();
        if($arr){
              $version=(int)$arr[0]['value'];
              if($version>$version_code){
                    $v_arr=$this->dao->field('value')->where("key='android_update_url'")->select();
                    $data['version_code']=$version;
                    $data['android_update_url']=$v_arr[0]['value'];
                    $this->jsonUtils->echo_json_data(0,'有新的版本更新...',$data);exit();
              }else{
                 $this->jsonUtils->echo_json_msg(1,'已是最新版本...');exit();
              }    
        }else{
          $this->jsonUtils->echo_json_msg(1,'已是最新版本...');exit();
        }


      }
   		/**
   		 * type = 1 用户
   		 * type = 2 商户
   		 */
      //安卓用户端版本更新
      public function version_update(){
//         $version_code=(int)$_POST['version_code'];
        $device=$_POST['device'];
        $type=$_POST['type'];
//         if(empty($version_code)){
//              $this->jsonUtils->echo_json_msg(4,"版本号为空...");exit();
//         }
        if(empty($device)){
        	$this->jsonUtils->echo_json_msg(4,"设备为空...");exit();
        }
        if(empty($type)){
        	$this->jsonUtils->echo_json_msg(4,"type为空...");exit();
        }
        switch ($device){
        	case 'android':
        		if($type ==1) {
        			$array = array('android_member_version_code','android_member_update_url') ;
        		}elseif($type ==2){
        			$array = array('android_mer_version_code','android_mer_update_url') ;
        		}
        		break;
        	case 'ios':
        		if($type ==1) {
        			$array = array('ios_member_version_code','ios_member_update_url') ;
        		}elseif($type ==2){
        			$array = array('ios_mer_version_code','ios_mer_update_url') ;
        		}
        		break;
        	default:
        		$this->jsonUtils->echo_json_msg(4,"数据异常...");exit();
        		break;
        		
        }
        
        $code=$this->dao->where(array('key'=>$array[0]))->getField('value');
     //   if($code>$version_code){
	        $url=$this->dao->where(array('key'=>$array[1]))->getField('value');
	        
	        $this->jsonUtils->echo_json_data(0, 'ok', array('ver'=>(int)$code,'url'=>$url));

//         }else{
//         	$this->jsonUtils->echo_json_data(0, '无需更新','');
//         }
      }

	  public function banner(){
	  	$position = isset($_POST['position'])?htmlspecialchars($_POST['position']):'';
	  	$db = M('Banner');
	  	$data = $db->where(array('position'=>$position))->field('url,title')->order('ord asc')->select();
	  	if($data){
		  	foreach ($data as $key =>$row){
		  		$data[$key]['url']= imgUrl($row['url']);
		  	}
	  	}else{
	  		$data = array();
	  	}
	  	$arr ['list'] = $data;
	  	$this->jsonUtils->echo_json_data(0, 'ok', $arr);
	  }
	     


   

}

?>