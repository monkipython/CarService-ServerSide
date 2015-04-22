<?php
/**
* 发送短信
*/
namespace Org\Util;
class Sms {


     /**
      * 
      * @param var(11) $mobile
      * @param var(1) $type
      * @param var(1) $ty 0用户端  2商务端
      * @param smallint(1) $ext 扩展
      * @return multitype:NULL string
      */
	   function send_sms($mobile,$type,$ty,$ext=0){
	   	if(!in_array($ty, array('0','2'))){
	   		$return = array('code'=>404,'msg'=>'类型不符合');
	   		return  $return;
	   	}
          Vendor("Sms.sms");
//          $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
		  $target="http://222.73.117.158/msg/HttpSendSM?";
      	  $mobile_code = rand(100000,999999);
//         session_set_cookie_params(1800);
//         session_cache_expire(18000);
//        session_start();
    

	      if(empty($mobile)){
	          exit('手机号码不能为空');
	      }
	      if(empty($type)){
	          exit('短信内容类型不能为空');
	      }
	        switch ($type) {
	          case  1://注册验证码
// 	            $content="亲爱的客官，您刚刚注册驾客网的验证码为【{$mobile_code}】(有效期为30分钟)。如非本人操作请忽略。";
	             $content="因为有你，这个世界变得有一点点不一样。您的注册验证码是".$mobile_code."，以后我们常联系好么。";
	            break;
	          case  2://
// 	            $content="亲爱的客官，您的验证码为【{$mobile_code}】(有效期为30分钟)。如非本人操作请忽略。";
	             $content="因为有你，这个世界变得有一点点不一样。您的验证码是".$mobile_code."，以后我们常联系好么。";
	            break;
	         
	        }
// 	        $post_data = "account=cf_rhkj&password=rihoukeji157&mobile=".$mobile."&content=".rawurlencode($content);
	        $post_data = "account=xmrhwl&pswd=Tch123456&mobile=$mobile&msg=$content&needstatus=true&product=";
	         //密码可以使用明文密码或使用32位MD5加密
// 	        $gets =  xml_to_array(Post($post_data, $target));
	       $gets = file_get_contents($target.$post_data);
	        $ret = str_replace('\n',',',json_encode($gets));
	        $retArr = explode(',',$ret);
	         
	         $session_id='';
// 	          if($gets['SubmitResult']['code']==2){
	          if($retArr[1] == 0){
	         	//发送短信成功
	         	$session_id = md5(uniqid(rand()));
          		  $db = M('Verifycode');
          		  $data ['mobile']= $mobile;
          		  $data ['type'] = $ty;
          		  $data ['session_id'] = $session_id;
          		  $data ['addtime'] = time();
          		  $data ['expire'] = time()+C('MESSAGE_EXPIRE_TIME');
          		  $data ['code'] = $mobile_code;
          		  
          	 	  $data = $db ->add($data);
          	 	  if(!$data){
          	 	  	$session_id = md5(uniqid(rand()));
          	 	  	$data ['session_id'] =$session_id;
          	 	  	$db->add($data);
          	 	  
          	 	  }
          	 	  $code = 2;
// 		          if($ext){
// 		          	$_SESSION['now_mobile'] = $mobile;
// 		          	$_SESSION['now_code_verify']=$mobile_code;
// 		          }else{
// 		          	$_SESSION['mobile'] = $mobile;
// 		          	$_SESSION['code_verify']=$mobile_code;
// 		          	$_SESSION['mobile_code'] = $mobile_code;
// 		          }
		         
	           }else{
	          	 	$code = 1;
	           }
	          
// 	           $return = array('code'=>$gets['SubmitResult']['code'],'msg'=>$gets['SubmitResult']['msg'],'session_id'=>$session_id);
	           $return = array('code'=>$code,'msg'=>$retArr[1],'session_id'=>$session_id);
	        return  $return;

    }
     public function getVerifyCode($session_id,$phone){
     	$db = M('Verifycode');
     	$data = $db ->where(array('session_id'=>$session_id,'mobile'=>$phone))->field('code,expire')->order('addtime desc')->find();
     	if($data['expire'] >= time()){
     		return $data['code'];
     	}else{
     		return '';
     	}
     
     	
     }
   

}

?>