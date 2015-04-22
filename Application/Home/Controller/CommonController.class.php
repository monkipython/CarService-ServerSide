<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function _initialize(){
		
		$cookie = cookie('caryu_session_id');
		if($cookie){
			$_SESSION['user'] = $cookie;
		}else{
			if(!empty($_SESSION['user'])){
				$cookie  = $_SESSION['user'];
			}
		}
// 		dump($cookie);
		$this->assign('cookie',$cookie);
	}
// 	protected  function toDaydiff($num){
		
// 		$day =intval($num /1440);
// 		$left = $num % 1440;
// 		$hour = intval( $left / 60);
// 		$min = $left % 60;
// 		return  (int) $day."天".$hour."时".$min."分";
// 	}
	public function curl($url,$data){
	
		$ch = curl_init ();
		 
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt(  $ch, CURLOPT_TIMEOUT, 5);
		 
		curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ( "Expect: ") );
		curl_setopt ( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		$ret = curl_exec ( $ch );
		$curl_errno = curl_errno($ch);
		$info = curl_getinfo($ch);
		curl_close ( $ch );
		 
		if($curl_errno > 0){
			$error =  'errorno '.$curl_errno;
			die(json_encode(array('code'=>404,'msg'=>$error)));
		}
// 		 dump($ret);
		$data = json_decode($ret,true);
		if($data['code'] == 2){
			header("Content-type:text/html;charset=utf-8");
			$_SESSION['user'] ='';
			cookie('caryu_session_id',null);
			$this->redirect('/','','2','登录已过期，请重新登录,2秒后自动跳转');
		}else{
			return $data;
		}
		 
	
	}
	
	
}