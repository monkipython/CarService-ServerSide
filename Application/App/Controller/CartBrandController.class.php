<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;


/**
 * 车品牌相关操作
 */
class CartBrandController extends Controller{
      
      private $jsonUtils;
      private $dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->dao=M('CarBrand');
         
      }
      
      /**
       * 查询pid下的子id
       * @return json
       */
      public function brand_list(){
      	  $pid = isset($_POST['pid']) ?(int)htmlspecialchars($_POST['pid']):'0';
          $arr= $this->dao->field("id ,name,pid,icon")->where(array('pid'=>$pid,'status'=>1))->select();
          foreach ($arr as $key =>$row){
          	$arr[$key]['icon'] = imgUrl($row['icon']);
          }
          if($arr){
            $data['list']=$arr;
            $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
          }else{
            $this->jsonUtils->echo_json_msg(1,'暂无城市数据...');exit();
          }


      }

	/**
	 * 获取制定id的品牌信息
	 * @param int $id
	 */
   static public function getInfo($id){
  	 	$db = M('CarBrand');
   		$data = $db->where(array('id'=>$id))->find();
   		return $data;
   		
   	}
   	/**
   	 * 获取制定id的品牌名称
   	 * @param int $id
   	 */
   	static  public function getName($id){
   		if(empty($id)){
   			return '';
   		}
   		$db = M('CarBrand');
   		$data = $db->where(array('id'=>$id))->getField('name');
   		return $data;
   	}
   	
   	
   	
   	
   	

}
?>