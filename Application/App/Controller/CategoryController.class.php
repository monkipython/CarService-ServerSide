<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;


/**
 * 项目分类表
 */
class CategoryController extends Controller{
      private $jsonUtils;
      private $dao;
      private $merchant_dao;
      private $session_handle;//session 处理类
      private $session_dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->dao=M('category');
         
      }
          
      /**
       * 项目列表
       * @return json
       */
      public function category_list(){
           $arr= $this->dao->where(array('status'=>1))->field("id,name,pid")->select();
          if($arr){
             $data['list']=$arr;
             $this->jsonUtils->echo_json_data(0,'ok',$data);
           }else{
            $this->jsonUtils->echo_json_data(1,'暂无栏目数据...');
           }

      }
      
    	/**
    	 * 获取pid
    	 * @param  $id
    	 * @return 
    	 */
	 static public function getCategoryPid($id){
      		$db = 	M('category');
      		$data = $db->where(array('id'=>$id))->getField('pid');
      		return $data;

      }
      /**
       * 获取信息
       * @param  $id
       */
      static  public function  getCategoryById($id){
	      	
	      	$db = 	M('category');
	      	$data = $db->where(array('id'=>$id))->find();
	      	return $data;
      }
      
      /**
       * 验证分类合法(未启用)
       * @param int $sub_id
       * @param int $classid
       * @return string category_name
       */
   private  function checkCategory($sub_id,$classid){
      	$db = M ('category');
      	$arr = $db ->where(array('id'=>$sub_id,'status'=>1))->field('pid,name')->find();
      	if($arr){
      
      		if ($arr['pid'] != $classid){
      			$this->jsonUtils->echo_json_msg(4,	'子分类不属于父分类');exit();
      		}
      	}else{
      		$this->jsonUtils->echo_json_msg(4, '该分类不存在');exit();
      	}
      	return $arr['name'];
      }
      /**
       * 获取项目名 返回字符串
       * @param string $string
       * return string 
       */
      static function getCategoryNames($string){
      	$name= M('category')->where(array('id'=>array('in',$string)))->field('name')->select();
      	$cate='';
      	foreach ($name as $row){
      	
      		$cate[]= $row['name'];
      	}
      	return implode('、',$cate);
      }
   
    

}

?>