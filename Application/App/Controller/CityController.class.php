<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;


/**
 * 城市
 */
class CityController extends Controller{
      private $jsonUtils;
      private $dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->dao=M('City');
         
      }
      
      /**
       * 查询pid下的子id
       * @return json
       */
      public function city_list(){
      	  $pid = isset($_POST['pid']) ?(int)htmlspecialchars($_POST['pid']):'0';
          $arr= $this->dao->field("id ,name,pid")->where(array('pid'=>$pid,'status'=>1))->select();
          if($arr){
            $data['list']=$arr;
            $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
          }else{
            $this->jsonUtils->echo_json_msg(1,'暂无城市数据...');exit();
          }


      }


   static public function getAreaIdPreId($areaid){
  	 	$db = M('City');
   		$data = $db->where(array('id'=>$areaid))->find();
   		return $data;
   		
   	}
   	static  public function getName($id){
   		if(empty($id)){
   			return '';
   		}
   		$db = M('City');
   		$data =$db->where(array('id'=>$id))->getField('name');
   		return $data;
   	}

}

?>