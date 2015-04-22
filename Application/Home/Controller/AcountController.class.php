<?php
namespace Home\Controller;
use Think\Controller;
class AcountController extends Controller {
	
	
	// 用户登录
	public function login(){
		$username=isset($_POST['username']) ?htmlspecialchars(trim($_POST['username'])):'';
		$password=isset($_POST['password']) ?htmlspecialchars(trim($_POST['password'])):'';
		$autologin=isset($_POST['autologin']) ?htmlspecialchars(trim($_POST['autologin'])):'';
		$type=isset($_POST['type']) ?htmlspecialchars(trim($_POST['type'])):'-1';
		if($type == 0){
			//商户
			$url = C('CURL_POST_URL')."Merchant/login";
			$data = array(
					'username'=>$username,
					'password'=>$password
			);
			$rel = CurlController::curl($url, $data);
			$rel['data']['name'] = $rel['data']['merchant_name'];
			$rel['data']['session_id'] = $rel['data']['mer_session_id'];
			$res['data']['jid'] = $rel['data']['jid'];
			$rel['data']['type'] = '2';
		}elseif ($type == 1){
			//用户
			$url = C('CURL_POST_URL')."Member/login";
			$data = array(
					'mobile'=>$username,
					'password'=>$password
			);
			$rel = CurlController::curl($url, $data);
// 			$rel['data']['name'] = $rel['data']['merchant_name'];
			$rel['data']['session_id'] = $rel['data']['member_session_id'];
			$res['data']['jid'] = $rel['data']['jid'];
			$rel['data']['type'] = '0';
		}else{
			die(json_encode(array('code'=>1,'msg'=>'登录方式错误'))) ;
		}
		if($rel['code'] == 0){
			if($autologin){
				cookie('caryu_session_id',$rel['data'],C('EXPIRE_TIME'));
			}
		}
		$_SESSION['user'] = $rel['data'];
		echo json_encode($rel);
	}
	

	
   public function register(){
    	if(!empty($_SESSION['uid'])){
    	   $username=isset($_POST['username']) ?htmlspecialchars(trim($_POST['username'])):'';
           $password=isset($_POST['password']) ?htmlspecialchars(trim($_POST['password'])):'';
           $session_id=isset($_SESSION['uid']) ? $_SESSION['uid']:'';
           $code_verify=isset($_POST['code_verify']) ?htmlspecialchars(trim($_POST['code_verify'])):'';
	       $url = C('CURL_POST_URL')."Merchant/register";
	       $data = array(
	       			'username'=>$username,
	       			'password'=>$password,
	       			'session_id'=>$session_id,
	       			'code_verify'=>$code_verify,
	       			'device' => 'web'
	       );
	       $rel = CurlController::curl($url, $data);
		   if($rel['code']==0){
	       	 echo json_encode($rel);
	       }else{
	       	 echo json_encode($rel);
	       }

    	}
    }
    
    public function safeCode(){
		$username=isset($_POST['username']) ?htmlspecialchars(trim($_POST['username'])):'';
		$url = C('CURL_POST_URL')."Merchant/register";
	    $data = array(
	       		'username'=>$username
	    );
	    $rel = CurlController::curl($url, $data);
	    if($rel['code']==0){
// 	      $_SESSION['uid'] = $rel['data'];
	      echo json_encode($rel);
	    }else{
	      echo json_encode($rel);
	    }
    }
    
    public function userAlreadyExists(){
	    $username=isset($_POST['username']) ?htmlspecialchars(trim($_POST['username'])):'';
	    $verifylength = strlen($username);
	    if($verifylength != 11 || is_numeric($username) == false){
		    die(json_encode(array('code'=>202,'msg'=>'手机格式不符合要求')));
	    }
		$url = C('CURL_POST_URL')."Merchant/checkUser";
		$data = array(
	       	'username'=>$username
	    );
	    $rel = CurlController::curl($url, $data);
		die(json_encode($rel));
    }

    
	public function loginOut(){
		$_SESSION['user'] ='';
		cookie('caryu_session_id',null);
		$this->ajaxReturn(array('code'=>0,'msg'=>'登出成功'));
	}
    
    
}