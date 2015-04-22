<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;


/**
 * 用户活动(废弃)
 */
class ActivityController extends Controller{
      


      private $jsonUtils;
      private $dao;
	  private $session_handle;//session 处理类
      private $session_dao;
     public function __construct(){
       
          $this->jsonUtils=new \Org\Util\JsonUtils;
          $this->session_handle=new \Org\Util\SessionHandle;
          $this->dao=M('activity');
          $this->session_dao=M('member_session');
         
      }
          
      //活动列表
      public function activity_list(){
           $page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
      	   $num =  isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
           $limit = ($page-1)*$num.','.$num;
           $time=time();
         $arr=$this->dao->query("select a.id,a.name,a.pics,a.merchant_id,b.merchant_name,a.second_price,a.market_price,a.start_time,b.header,a.remain  from ".C('DB_PREFIX')."activity  as a left join .".C('DB_PREFIX')."merchant as b on a.merchant_id=b.id   where a.effect =1 and $time<a.end_time order by a.addtime desc limit $limit");
         
         if($arr){
              foreach ($arr as $key => $value) {
              		if(!empty($value['pics'])){
              			$pics = json_decode($value['pics'],true);
              			$pics = imgUrl($pics[0]);
              			$arr[$key]['icon'] = $pics;
              			
              		}
                  $arr[$key]['header'] = imgUrl($value['header']);
                   if($time>$arr[$key]['start_time']){
                      $arr[$key]['is_start']=1;
                   }else{
                        $arr[$key]['is_start']=0;
                   }
              }
              unset($arr['start_time']);
              unset($arr['end_time']);
              $data['list']=$arr;
              $this->jsonUtils->echo_json_data(0,'ok',$data);exit();
         }else{
              $this->jsonUtils->echo_json_msg(1,'暂无活动信息...');exit();
         }
      }
    
     //获取活动详情
     public function get_activity(){
             
             $id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
             if(empty($id)){
              $this->jsonUtils->echo_json_msg(4,'活动ID为空');exit();
             }

               $arr=$this->dao->query("select a.id,a.category_ids,a.name,a.merchant_id,b.merchant_name,b.address,a.second_price,a.market_price,a.start_time,a.end_time,a.cart_model,a.detail,a.pics,a.remain, a.order_time,a.business_hours,a.other from ".C('DB_PREFIX')."activity  as a left join .".C('DB_PREFIX')."merchant as b on a.merchant_id=b.id   where a.effect =1 and a.id=$id");
            // echo  $this->dao->getLastSql();exit();
              if($arr){
               
                 $merchant_id=$arr[0]['merchant_id'];
                 $start_time=date('Y-m-d',$arr[0]['start_time']);
                 $end_time=date('Y-m-d',$arr[0]['end_time']);
                 $valid_time=$start_time."到".$end_time;//有效时间\
                 $cur_time=date('Y-m-d',time());
                 if(time()<$arr[0]['start_time']){
                      $remain_days=0;
                      $remain_time=0;
                     
                 }else{
                   $remain_days=$this->diff_days($start_time,$cur_time);//获取两日期相差天数
                   $remain_time=$arr[0]['end_time'] -time();
                  
                 }
                 $name= M('category')->where(array('id'=>array('in',$arr[0]['category_ids'])))->field('name')->select();
                 $cate='';
                 foreach ($name as $row){
                 
                 	$cate[]= $row['name'];
                 }
                 $arr[0]['category_name'] =empty($cate)?'': implode('、', $cate);
                 
                 
                  //商家平均服务态度星级，服务质量星级，设备星级
                 $m_arr=$this->dao->query("select avg(service_attitude) as service_attitude ,avg(service_quality) as service_quality,avg(merchant_setting) as merchant_setting from ".C('DB_PREFIX')."comment  where merchant_id=$merchant_id ");
                
                 $arr[0]['valid_time']=$valid_time;//有效时间
                 $arr[0]['remain_days']=$remain_days;//剩余天数
                 $arr[0]['remain_time']=$remain_time;//剩余miaoshu
                // $arr[0]['remain_time']='';
                 unset($arr[0]['start_time']);
                 unset($arr[0]['end_time']);
                    $arr[0]['service_attitude']=$m_arr[0]['service_attitude']==null?'':$m_arr[0]['service_attitude'];
                    $arr[0]['service_quality']=$m_arr[0]['service_attitude']==null?'':$m_arr[0]['service_attitude'];
                    $arr[0]['merchant_setting']=$m_arr[0]['service_attitude']==null?'':$m_arr[0]['service_attitude'];
                
                 if($arr[0]['pics']){
                   $json_obj=json_decode($arr[0]['pics'],true);
                   $arr[0]['pics']=imgUrl($json_obj);
                 }
                $this->jsonUtils->echo_json_data(0,'ok',$arr[0]);
              }else{
                $this->jsonUtils->echo_json_msg(1,'获取失败！');exit();
              }


     }
     //参加活动  
    public function attend_activity(){
          $id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
          $num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'1';
 
      if(empty($id)){
        $this->jsonUtils->echo_json_msg(4,'活动ID为空...');exit();
      }
	  if(empty($num)){
        $this->jsonUtils->echo_json_msg(4,'活动数量为空...');exit();
      }
           $member_session_id=$_POST['member_session_id'];
           $member_id=$this->session_handle->getsession_userid($member_session_id);
      
      $arr=$this->dao->field("start_time,end_time,second_price,merchant_id,remain,name,cart_model")->where("id=$id")->select();
      $cur_time=time();//当前时间
      $start_time=$arr[0]['start_time'];//活动开始时间
      $end_time=$arr[0]['end_time'];//活动结束时间
      
      if($cur_time<$start_time){
        $this->jsonUtils->echo_json_msg(1,'活动还没开始...');exit();
      }elseif($cur_time>$end_time){
        $this->jsonUtils->echo_json_msg(1,'活动已经结束...');exit();
      }
      $left=$arr[0]['remain'] - $num; 
      if($num<=0){
      	$this->jsonUtils->echo_json_msg(4,'活动数量不足');exit();
      }else{
      	$this->dao->where("id=$id")->save(array('remain'=>$left));
      }
      $order=M('order');
      $data['order_no']=time().rand(1000, 9999);
      $data['service_name'] = $arr[0]['name'];
      $data['status'] = 0;
      $data['merchant_id']=$arr[0]['merchant_id'];//商家ID
      $data['member_id']=$member_id;
      $data['type']=2;//活动类型订单
      $data['goods_count']=$num;
      $second_price=$arr[0]['second_price'];
      $data['unit_price']=$second_price;
      $data['total_price']=$second_price*$num;
      $data['sub_id'] = $id;
      $data['sub_data'] = json_encode(array());
      $data['reach_time'] ='';
      $data['cart_model'] =$arr[0]['cart_model']; 
      $data['member_remark']='';
      $data['merchant_remark']='';
   
      $data['addtime']=time();
      
      $result=$order->add($data);
   
      if($result){
           $detail=M('order_detail');
              $d_data['order_no']= $data['order_no'];
              $d_data['obj_id']=$id;
              $d_data['price']=$second_price;;
              $d_data['num']=$num;
              $d_data['obj_type']="activity";
              $detail->add($d_data);
             
            $this->jsonUtils->echo_json_msg(0,'ok');exit();
      }else{
             $this->jsonUtils->echo_json_msg(1,'参加活动错误，请重新尝试...');exit();
      }



    }

      /**
       * 返回两个日期相差的天数
       * @param  [type] $date1 [description]
       * @param  [type] $date2 [description]
       * @return [type]        [description]
       */
       private function diff_days($date1,$date2){
          if(empty($date2)||empty($date1)){
             return 0;
          }
          $Days=round((strtotime($date2)-strtotime($date1))/3600/24);  
          return $Days;
       }

      public function test(){
       //echo $this->diff_days('2014-9-10','2014-9-15');
        echo strtotime("2014-09-24");
        echo  "<br/>11111111111";


      }
        
      

}

?>