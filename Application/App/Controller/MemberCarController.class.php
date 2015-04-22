<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
/**
 * 用户车辆
 */
class MemberCarController extends Controller {

      private $jsonUtils;
      private $dao;
      private $session_handle;//session 处理类
      private $session_dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->session_handle=new \Org\Util\SessionHandle;
          $this->dao=M('cart');
          $this->session_dao=M('member_session');
         
      }

       /**
        * 用户车辆信息列表
        */
      public function my_car(){
        $pagenum=$_POST['pagenum'];
       if(empty($pagenum)){
              $pagenum=1;
         }
       $pagenum=($pagenum-1)*20;
        $session_id=$_POST['memeber_session_id'];
        $member_id=$this->session_handle->getsession_userid($session_id);
        $arr=$this->dao->where("member_id=$member_id")->limit($pagenum,20)->select();
        if($arr){
              $data['list']=$arr;
              $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
        }else{
             $this->jsonUtils->echo_json_msg(1,'暂无车辆信息...');exit();
        }

      }

      public function test(){

        echo "test";
      }

   

}