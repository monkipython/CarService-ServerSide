<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
use Think\Log;

/**
 *  会员评论
 */
class MemberCommentController extends Controller{
      


      private $jsonUtils;
      private $dao;
      private $session_handle;//session 处理类
      private $session_dao;
      public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->session_handle=new \Org\Util\SessionHandle;
          $this->dao=M('comment');
          $this->session_dao=M('member_session');
         
      }
		/**
		 *  type= 0 用户对商家的评价
		 *	type= 2 商家对用户的评价
		 */
        //用户评论列表
      public function member_comment_list(){
       $type=isset($_POST['type'])?htmlspecialchars($_POST['type']):'0';
         $member_session_id=$_POST['member_session_id'];
         $page=isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
       	 $num = isset($_POST['num']) ?htmlspecialchars($_POST['num']):'6';
          $limit =($page-1)*$num.','.$num;
          $member_id=$this->session_handle->getsession_userid($member_session_id);
   
         if($type==0){
         	$sql="select a.id,a.desc,from_unixtime(a.addtime,'%Y-%m-%d') as addtime,c.id as order_id,a.service_attitude,a.service_quality,a.merchant_setting,a.order_no,b.nick_name,b.header from 
         	".C('DB_PREFIX')."comment as a 
         	left join ".C('DB_PREFIX')."member as b 
         	on a.member_id=b.id 
         	left join ".C('DB_PREFIX')."order as c 
         	on c.order_no = a.order_no 
         	where a.member_id=$member_id and a.type = 0 
         	limit $limit";
             $arr=$this->dao->query($sql);
             if($arr ===false){
             	
                $this->jsonUtils->echo_json_msg(1,'暂无评论记录...');exit();
             }else{
             	foreach ($arr as $key=> $row){
             		$arr[$key]['service_star'] = floor(($row['service_attitude']+$row['service_quality']+$row['merchant_setting'])/3);
             		$arr[$key]['header'] = imgUrl($row['header']);
             		unset($arr[$key]['service_attitude']);
             		unset($arr[$key]['service_quality']);
             		unset($arr[$key]['merchant_setting']);
             		 
             	}
             	
             	$data['list']=$arr;
             	$this->jsonUtils->echo_json_data(0,'ok',$data);exit();
             
             }
         }elseif($type==2){
                
         	$sql="select a.id,a.desc,from_unixtime(a.addtime,'%Y-%m-%d') as addtime,c.id as order_id,a.order_no,b.merchant_name,b.header from
         	".C('DB_PREFIX')."comment as a
         	left join ".C('DB_PREFIX')."merchant as b
         	on a.merchant_id=b.id 
         	 left join ".C('DB_PREFIX')."order as c 
         	on c.order_no = a.order_no  
         	where a.member_id=$member_id and a.type = 2
         	limit $limit";
         	$arr=$this->dao->query($sql);
               
                if($arr === false){
                	$this->jsonUtils->echo_json_msg(1,'暂无回复记录...');exit();
                }else{
                	if(!$arr) $arr = array();
                	foreach ($arr as $key=> $row){
                		
                		$arr[$key]['header'] = imgUrl($row['header']);
                		
                	
                	}
                	
                  
                  $data['list']=$arr;
                  $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
                }
         }
      	

      }

       
	     /**
	      * 用户查看商家评论列表
	      */
       public function comment_list(){
	        $merchant_id= isset($_POST['merchant_id'])?htmlspecialchars($_POST['merchant_id']):'';
	        $page= isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
	        $num= isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
	        
	        $limit = ($page-1)*$num .','.$num;

	     	$sql="select a.id,a.desc,from_unixtime(a.addtime,'%Y-%m-%d') as comment_time,b.header,b.nick_name,a.pics as member_name from ".C('DB_PREFIX')."comment as a left join ".C('DB_PREFIX')."member as b on a.member_id=b.id  where a.merchant_id=$merchant_id limit $limit";
	        $arr=$this->dao->query($sql);

	       if($arr ===false ){
	      	 	$this->jsonUtils->echo_json_msg(1,'暂无评论记录...');exit();
	       }else{
		        foreach ($arr as $key => $value) {
		        	if($arr[$key]['pics']){
		        		$pics_arr= json_decode($arr[$key]['pics']);
		        		$arr[$key]['pics']=imgUrl($pics_arr);
		        	}else{
		        		$arr[$key]['pics']="";
		        	}
		        }
		        $count=$this->dao->where("merchant_id=$merchant_id and parent_id=0")->count();
		        $data["count"]=$count;
		        $data["list"]=$arr;
		        $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
	       }
	

      }

    /**
     * 会员评论
     */
    public function comment(){
      $session_id= isset($_POST['member_session_id'])?htmlspecialchars($_POST['member_session_id']):'';
      $member_id=$this->session_handle->getsession_userid($session_id);
      $order_no= isset($_POST['order_no'])?htmlspecialchars($_POST['order_no']):'';
      $content= isset($_POST['content'])?htmlspecialchars($_POST['content']):'';
// 	Log::write($order_no);
      if(empty($order_no)||$order_no==null){
        $this->jsonUtils->echo_json_msg(4,'订单id为空...');exit();
      }
      if(empty($content)){
        $this->jsonUtils->echo_json_msg(4,'评论内容为空...');exit();
      }

      $db = M('Order');
      $dat =$db ->where(array('order_no'=>$order_no,'member_id'=>$member_id))->find();
      if($dat == false){
      	$this->jsonUtils->echo_json_msg(4, '无权操作');
      }else{
      	
      	if($dat['member_comment']==1){
      		$this->jsonUtils->echo_json_msg(4, '你已经评价过该订单');
      	}
      	 
      }
      $data['service_quality']=(int)$_POST['service_quality'];
      $data['service_attitude']=(int)$_POST['service_attitude'];
      $data['merchant_setting']=(int)$_POST['merchant_setting'];
      $data['merchant_id']=$dat['merchant_id'];
      $data['member_id']=$member_id;
      $data['order_no']=$order_no;
      $data['desc']=$content;
      $data['addtime']=time();
     
   
           if($_FILES){
//            	Log::write(json_encode($_FILES));
	            $arr = mul_upload('/Comment/',2);
	            if($arr){
	                 $data['pics']=json_encode($arr);//把多张图片数组格式转json保存数据库
	            }
          }else{
          	$data['pics'] = "[]";
          }
         $result=$this->dao->add($data);
         if($result){
	        $rel = M('Merchant')->where(array('id'=>$dat['merchant_id']))->find();
	    	$save['service_attitude'] = number_format(($rel['service_attitude']+  $data['service_attitude'])/($rel['comment_count']+1),1);
	    	$save['service_quality'] = number_format(($rel['service_quality']+  $data['service_quality'])/($rel['comment_count']+1),1);
	    	$save['merchant_setting'] = number_format(($rel['merchant_setting']+  $data['merchant_setting'])/($rel['comment_count']+1),1);
	        $save['comment_count'] = $rel['comment_count']+ 1;
	        M('Merchant')->where(array('id'=>$dat['merchant_id']))->save($save);
	        $db ->where(array('id'=>$dat['id']))->save(array('member_comment'=>1));
	        $this->jsonUtils->echo_json_msg(0,'评论成功!');exit();
	      }else{
	        $this->jsonUtils->echo_json_msg(1,'评论失败...');exit();
	      }



    }

      



        
      

}

?>