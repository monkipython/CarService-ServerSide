<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;


/**
 * 采集人员（未启用）
 */
class CollecterController extends Controller{
      


      private $jsonUtils;
      private $dao;
      private $merchant_dao;
      private $session_handle;//session 处理类
      private $session_dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->session_handle=new \Org\Util\SessionHandle;
          $this->dao=M('user');
          $this->merchant_dao=M('merchant');
          $this->session_dao=M('member_session');
      }


      /**
       * 采集人员登录
       * @return [type] [description]
       */
      public function login(){
           $username=$_POST['username'];
           $password=$_POST['password'];
           if(empty($username)||empty($password)){
           	   $this->jsonUtils->echo_json_msg(4,'用户或者密码为空...');exit();
           }
           $arr=$this->dao->where("account='$username'")->select();
           if(!$arr){
           	  $this->jsonUtils->echo_json_msg(1,'该用户不存在');exit();
           }
           $password=md5($password);
           $arr=$this->dao->field('id')->where("account='$username' and password='$password'")->select();
          
           if($arr){
           	 $data['last_login_time']=time();
             $data['last_login_ip']=get_client_ip();
             $data['login_count']= array('exp','login_count+1' );
             $id=$arr[0][id];//采集人员iD
             $s_arr=$this->session_dao->where("type=1 and userid=$id")->select();
             if($s_arr){//是否已经登录过
                   $data1['collecter_id']=$s_arr[0]['userid'];
                   $data1['col_session_id']=$s_arr[0]['sessionid'];
             }else{
                  session_start();
                  $session_id=session_id();
                  $this->session_handle->write($id,$session_id,'',1);//session保存数据库
                  $data1['collecter_id']=$id;
                  $data1['col_session_id']=$session_id;
          
             }
          
             $this->dao->where("id=$id")->save($data);
           
             $this->jsonUtils->echo_json_data(0,'登录成功！',$data1);
           }else{
           	  $this->jsonUtils->echo_json_msg(3,'用户名或者密码错误!');exit();
           }
          


      }

    
      /**
       * 注册
       * @return [type] [description]
       */
      public function register(){
              $account=$_POST['username'];
              $password=$_POST['password'];
              if(empty($account)||empty($password)){
              	 $this->jsonUtils->echo_json_msg(4,"用户和密码不能为空！");exit();
              }
              $arr=$this->dao->where("account='$account'")->select();
              if($arr){
              	$this->jsonUtils->echo_json_msg(1,'该用户名已经被注册过！');exit();
              }

              $data['account']=$account;
              $data['password']=md5($password);
              $data['status']=1;
              $data['create_time']=time();
              $data['update_time']=time();
              $data['type_id']=1;//1 为采集人员
              $result=$this->dao->add($data);
              if($result){
                  $this->jsonUtils->echo_json_msg(0,'注册成功！');exit();
              }else{
                  $this->jsonUtils->echo_json_msg(1,'注册失败！');exit();
              }

      }
      
      /**
       * 采集人员退出
       * @return [type] [description]
       */
      public function loginout(){
        $session_id=$_POST['col_session_id'];
        if(empty($session_id)){
          $this->jsonUtils->echo_json_msg(4,'采集人员会话iD为空');exit();
        }
          $result=$this->session_handle->destroy($session_id);
          if($result){
            $this->jsonUtils->echo_json_msg(0,'退出成功!');exit();
          }else{
            $this->jsonUtils->echo_json_msg(1,'退出失败！');exit();
          }

      }

      /**
       * 采集商家
       * @return [type] [description]
       */
      public function collect(){
          $session_id=$_POST['col_session_id'];

          $merchant_name=$_POST['merchant_name'];
          $address=$_POST['address'];
          $latitude=$_POST['latitude'];
          $longitude=$_POST['longitude'];
          $collect_status=$_POST['collect_status'];
          $collecter_id=$_POST['collecter_id'];
          if(empty($session_id)){
             $this->jsonUtils->echo_json_msg(4,'采集人员会话iD为空');exit();
          }
          $s_arr=$this->session_dao->where("sessionid='$session_id'")->select();
          if(!$s_arr){
             $this->jsonUtils->echo_json_msg(2,'登录超时,请重新登录');exit();
          } 
          if(empty($merchant_name)){
          	$this->jsonUtils->echo_json_msg(4,"商家名称为空！");exit();
          }
          $m_arr=$this->merchant_dao->where("merchant_name='$merchant_name'")->select();
          if($m_arr){
             $this->jsonUtils->echo_json_msg(1,'此商家已经存在...');exit();
          }

          if(empty($longitude)||empty($latitude)){
          	$this->jsonUtils->echo_json_msg(4,'经纬度不能为空！');exit();
          }
          if($collect_status==null||$collect_status==''){
          	$this->jsonUtils->echo_json_msg(4,'洽谈状态为空');exit();
          }
           
        

          $data['merchant_name']=$merchant_name;
          $data['address']=$address;
          $data['longitude']=$longitude;
          $data['latitude']=$latitude;
          $data['collect_status']=$collect_status;
          $data['collecter']=$s_arr[0]['userid'];//采集人员iD
          $data['pwd']=md5('123456');
          $data['addtime']=time();
          $data['modtime']=time();
        
          $result=$this->merchant_dao->add($data);
         
          if($result){
               $this->jsonUtils->echo_json_msg(0,'添加成功！');exit();
          }else{
               $this->jsonUtils->echo_json_msg(1,'添加失败！');exit();
          }

      }

      /**
       * 采集商家列表
       * @return [type] [description]
       */
      public function collect_list(){
           $session_id=$_POST['col_session_id'];
          if(empty($session_id)){
             $this->jsonUtils->echo_json_msg(4,'采集人员会话iD为空');exit();
          }
           $s_arr=$this->session_dao->where("sessionid='$session_id'")->select();
           if(!$s_arr){
             $this->jsonUtils->echo_json_msg(2,'登录超时,请重新登录');exit();
           } 
           if(empty($pagenum)){
              $pagenum=1;
            }
            $pagenum=($pagenum-1)*20;

            $collecter_id=$s_arr[0]['userid'];
            $arr=$this->merchant_dao->query("select id as merchant_id,merchant_name,address,collect_status,longitude,latitude from ".C('DB_PREFIX')."merchant limit $pagenum,20 ");
            if($arr){
                $data['list']=$arr;
                $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
            }else{
               $this->jsonUtils->echo_json_msg(1,'暂无您的采集商家....');exit();
            }  

      }

      /**
       * 修改洽谈状态
       * @return [type] [description]
       */
      public function mod_collect_status(){
             $merchant_id=$_POST['merchant_id'];
             if(empty($merchant_id)){
             	$this->jsonUtils->echo_json_msg(4,'商家ID为空！');exit();
             }
             if(isset($_POST['collect_status'])){
             	 $data['collect_status']=$_POST['collect_status'];
             }
             if(isset($_POST['fail_desc'])){
              $data['collect_fail_desc']=$_POST['fail_desc'];//洽谈失败原因
             }
             
            
             $this->merchant_dao->where("id=$merchant_id")->save($data);
             $this->jsonUtils->echo_json_msg(0,'修改成功！');exit();


      }
      

      /**
       * 采集人员删除商家
       * @return [type] [description]
       */
      public function del_merchant(){
             $merchant_id=$_POST['merchant_id'];
             if(empty($merchant_id)){
              $this->jsonUtils->echo_json_msg(4,'商家ID为空！');exit();
             }

             $result=$this->merchant_dao->where("id=$merchant_id")->delete();
             if($result){
                   $this->jsonUtils->echo_json_msg(0,'删除成功！');exit();
             }else{
                 $this->jsonUtils->echo_json_msg(1,'删除失败！');exit();
             }

      }



      public function test(){
          /*  $collect_status=$_GET['collect_status'];
           if($collect_status==null||$collect_status==''){
            $this->jsonUtils->echo_json_msg(4,'洽谈状态为空');exit();
          }
        */
         $arr = array('img1' ,'img2','img3','img4' );
         $json_obj=json_encode($arr);
        echo $json_obj;
        $arr1=json_decode($json_obj);
        print_r($arr1);
        foreach ($arr1 as $key => $value) {
          echo $value."<br/>";
          echo substr($value, 3,4)."<br/>";
        }

       //$arr2=[];
        $arr2[1]='2';
        print_r($arr2);
      

      }

}

?>