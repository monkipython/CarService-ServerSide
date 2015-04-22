<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
	function _initialize(){
		parent::_initialize();
		$this->session = $_SESSION ['user'];
// 		if (empty ( $this->session ['session_id'] )) {
				
// 			$this->error ( '请登录！', "/", 2 );
// 		}
	}
    public function detail(){
    	$id = isset($_GET['id'])?htmlspecialchars($_GET['id']):'';
    	$page = isset ( $_GET ['p'] ) ? (int)htmlspecialchars ( $_GET ['p'] ) : 1;
    	$pagea = isset ( $_GET ['pa'] ) ? (int)htmlspecialchars ( $_GET ['pa'] ) : 1;
    	$pager = isset ( $_GET ['pr'] ) ? (int)htmlspecialchars ( $_GET ['pr'] ) : 1;
    	$num = isset ( $_GET ['num'] ) ? (int)htmlspecialchars ( $_GET ['num'] ) : 6;
    	$numa = isset ( $_GET ['numa'] ) ? (int)htmlspecialchars ( $_GET ['numa'] ) : 6;
    	$numr = isset ( $_GET ['numr'] ) ? (int)htmlspecialchars ( $_GET ['numr'] ) : 5;
    	$type = isset ( $_GET ['type'] ) ? (int)htmlspecialchars ( $_GET ['type'] ) : 1;
    
    	if(empty($id)){
    		$this->error('id为空');
    	}
    	$url = C ( 'CURL_POST_URL' ) . "Answer/getAnswerCategory";
    	$answerList = $this->curl($url, array());
    	$this->assign('category',$answerList['data']);
    	
    	$url = C ( 'CURL_POST_URL' ) . "Answer/getUserInfo";
    	$data = array(
    			'system_user_id'=>$id,
    			'session_id'=>$this->session['session_id'],
    	);
    	$info = $this->curl($url, $data);

		//获取动态
   		$url = C ( 'CURL_POST_URL' ) . "Answer/getSomeoneRecent";
		$data = array(
				'session_id'=>$this->session['session_id'],
				'system_user_id'=>$id,
				'page'=>$page,
				'num'=>$num,
				);
		$recent = $this->curl($url, $data);
		$Page = new \Think\Page ( $recent ['data'] ['count'], $num, array (
				'id'=>$id,
				'p' => $page,
				'num' => $num,
				'pa' => $pagea,
				'numa' => $numa,
				'pr'=>$pager,
				'numr'=>$numr,
				'type'=>1
		) ); // 实例化分页类
		// 传入总记录数和每页显示的记录数
		
		$recentPage = $Page->show (); // 分页显示输出
		//获取提问
		$url = C ( 'CURL_POST_URL' ) . "Answer/getSomeoneAnswer";
		$data = array(
				'session_id'=>$this->session['session_id'],
				'system_user_id'=>$id,
				'page'=>$pagea,
				'num'=>$numa,
				'type'=>1,
		);
		$answer = $this->curl($url, $data);
		$p1 = new \Think\Page ( $answer ['data'] ['count'], $numa, array (
				'id'=>$id,
				'p' => $page,
				'num' => $num,
				'pa' => $pagea,
				'numa' => $numa,
				'pr'=>$pager,
				'numr'=>$numr,
				'type'=>2
		) ,'pa'); // 实例化分页类
		// 传入总记录数和每页显示的记录数
		// 		dump($recent);
		$answerPage = $p1->show (); // 分页显示输出
    	
		//获取回答
		$url = C ( 'CURL_POST_URL' ) . "Answer/getSomeoneAnswer";
		$data = array(
				'session_id'=>$this->session['session_id'],
				'system_user_id'=>$id,
				'page'=>$pager,
				'num'=>$numr,
				'type'=>2,
		);
		$reply = $this->curl($url, $data);
		$p2 = new \Think\Page ( $reply ['data'] ['count'], $numr, array (
				'id'=>$id,
				'p' => $page,
				'num' => $num,
				'pa' => $pagea,
				'numa' => $numa,
				'pr'=>$pager,
				'numr'=>$numr,
				'type'=>3
		),'pr'); // 实例化分页类
		// 传入总记录数和每页显示的记录数
		$replyPage = $p2->show (); // 分页显示输出
		$this->assign('type',$type);
		$this->assign('reply',$reply['data']);
		$this->assign('reply_page',$replyPage);
		$this->assign('answer',$answer['data']);
		$this->assign('answer_page',$answerPage);
		$this->assign('recent',$recent['data']);
		$this->assign('recent_page',$recentPage);
    	
    	$this->assign('info',$info['data']);
        $this->display();
    }
    public function userAttention(){
    	$id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
    	if(empty($id)){
    		$this->ajaxReturn(array(code=>'1',msg=>'id为空')) ;exit();
    	}
    	$url = C ( 'CURL_POST_URL' ) . "Answer/userAttention";
    	$arr = array(
    			'session_id'=>$this->session['session_id'],
    			'system_user_id'=>$id,
    	);
    	$data = $this->curl($url, $arr);
    	$this->ajaxReturn($data);exit();
    }
  
    
}