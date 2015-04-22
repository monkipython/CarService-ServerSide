<?php
namespace Home\Controller;
use Think\Log;

use Think\Controller;

class AnswerController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->session = $_SESSION ['user'];
// 		dump($_SESSION['user']);
// 		if (empty ( $this->session ['session_id'] )) {
			
// 			$this->error ( '请登录！', "/", 2 );
// 		}

		
	
	}
	public function index(){
		
		$pid = isset($_GET['id'])?(int)htmlspecialchars($_GET['id']):0;
		$page = isset ( $_GET ['p'] ) ? (int)htmlspecialchars ( $_GET ['p'] ) : 1;
		$num = isset ( $_GET ['num'] ) ? (int)htmlspecialchars ( $_GET ['num'] ) : 10;
		
		$url = C ( 'CURL_POST_URL' ) . "Answer/getAnswerCategory";
		$answerList = $this->curl($url, array());
		$this->assign('category',$answerList['data']);
		
		$url = C ( 'CURL_POST_URL' ) . "Answer/getAnswerList";
		$data = array(
				'pid'=>$pid,
				'page'=>$page,
				'num'=>$num,
				);
		$answer = $this->curl($url, $data);
		$Page = new \Think\Page ( $answer ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num,
				'id' =>$pid
		) ); // 实例化分页类
		// 传入总记录数和每页显示的记录数
// 		dump($answer);
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'pages', $paginate );
		$this->assign('answer',$answer['data']['list']);
		$this->display();
	}

	
	/**
	 * 添加问题
	 */
	public function addQuestion(){
		$this->jsonUtils=new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
		$session_id = $this->session['session_id'];
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$call="App\\Controller\\CommonController";
		$systemid = call_user_func_array(array($call, 'getSystemUserid'), array($member_id['id'],$member_id['type']));
		$pid = isset ( $_POST ['pid'] ) ? htmlspecialchars ( $_POST ['pid'] ) : '';
		$title = isset ( $_POST ['title'] ) ? htmlspecialchars ( $_POST ['title'] ) : '';
		$pics = isset ( $_POST ['pics'] ) ?   $_POST ['pics'] : '';
		if(empty($pid)){
			$this->jsonUtils->echo_json_msg(4, '分类为空');
			exit();
		}
		if(empty($title)){
			$this->jsonUtils->echo_json_msg(4, '问题为空');
			exit();
		}
	
		if (!empty($pics)) {
			$arr = json_decode($pics,true);
			foreach ($arr as $key =>$row){
				$newarr[] = json_decode($row,true);
			}
		
			$data ['pics'] =json_encode( $newarr);
		}else{
			$data ['pics']="[]";
		}
		
		$data ['system_user_id'] = $systemid;
		$data ['title'] = $title;
		$data ['pid'] = $pid;
		$data ['addtime'] = time();
		$db = M('AnswerProblem');
		$data =$db ->add($data);
		if($data){
			$this->jsonUtils->echo_json_msg(0, 'ok');exit();
		}else{
			$this->jsonUtils->echo_json_msg(4, '失败');exit();
		}
	}
	public function uploadQuestionPic(){
		$verifyToken = md5('seeyoulater' . $_POST['timestamp']);
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			$arr = mul_upload ( '/Answer/' ,1);
			if ($arr) {
// 				 $arr = imgUrl($arr);
				die(json_encode(array("code"=>0,'msg'=>'ok','data'=>$arr[0])));exit();
			}
		
		}else{
			die(json_encode(array("code"=>4,'msg'=>'请选择上传图片')));	exit();	
		}
		
	}
	public function search(){
		
		$keywords = isset($_GET['keyword'])?htmlspecialchars($_GET['keyword']):'';
		$page = isset ( $_GET ['p'] ) ? (int)htmlspecialchars ( $_GET ['p'] ) : 1;
		$num = isset ( $_GET ['num'] ) ? (int)htmlspecialchars ( $_GET ['num'] ) : 10;
		$url = C ( 'CURL_POST_URL' ) . "Answer/getAnswerCategory";
		$answerList = $this->curl($url, array());
		$this->assign('category',$answerList['data']);
		
		$url = C ( 'CURL_POST_URL' ) . "Answer/search";
		$data = array(
				'keyword'=>$keywords,
				'page'=>$page,
				'num'=>$num,
		);
		$answer = $this->curl($url, $data);
// 		dump($answer);
		$Page = new \Think\Page ( $answer ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num,
				'keyword'=>$keywords
		) ); // 实例化分页类
		// 传入总记录数和每页显示的记录数
		// 		dump($answer);
		$paginate = $Page->show (); // 分页显示输出
		$this->assign('count',$answer ['data'] ['count']);
		$this->assign ( 'pages', $paginate );
		$this->assign ( 'keyword', $keywords );
		$this->assign('answer',$answer['data']['list']);
		$this->display();
		
		
		
	}
	public function detail(){
		$id = isset($_GET['id']) ?htmlspecialchars($_GET['id']):'';
		$url = C ( 'CURL_POST_URL' ) . "Answer/getAnswerCategory";
		$answerList = $this->curl($url, array());
		$this->assign('category',$answerList['data']);
		if(!empty($this->session['session_id'])){
			$url = C ( 'CURL_POST_URL' ) . "Answer/getQuestionDetail";
			$data = array(
					'id'=>$id,
					'session_id'=>$this->session['session_id'],
			);
			$answer = $this->curl($url, $data);
			if($answer['data']['is_answered'] ==1 && $answer['data']['edit_answer'] !=0){
				$data = array(
						'id'=>$answer['data']['edit_answer'],
						'session_id'=>$this->session['session_id'],
				);
				$url = C ( 'CURL_POST_URL' ) . "Answer/getQuestionReply";
				$context = $this->curl($url, $data);
				$answer['data']['answer_context'] = $context['data'];
			}
		}else{
			$url = C ( 'CURL_POST_URL' ) . "Answer/getQuestionDetailNoStatus";
			$data = array(
					'id'=>$id,
			);
			$answer = $this->curl($url, $data);
		}
		$this->assign('data',$answer['data']);
		$this->display();
	}
	public function clickLuad(){
		$id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
		$url = C ( 'CURL_POST_URL' ) . "Answer/clickLuad";
		$data = array(
				'id'=>$id,
				'session_id'=>$this->session['session_id'],
		);
		$rel = $this->curl($url, $data);
		$this->ajaxReturn($rel,'json');
		
	}
	public function collect(){
		$id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
		$url = C ( 'CURL_POST_URL' ) . "Home/collect";
		$data = array(
				'obj_id'=>$id,
				'session_id'=>$this->session['session_id'],
				'type'=>4,
		);
		$rel = $this->curl($url, $data);
		$this->ajaxReturn($rel,'json');
	
	}
	public function attend(){
		$id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'';
		$url = C ( 'CURL_POST_URL' ) . "Answer/attentionQuestion";
		$data = array(
				'id'=>$id,
				'session_id'=>$this->session['session_id'],
		);
		$rel = $this->curl($url, $data);
		$this->ajaxReturn($rel,'json');
	
	}
	
	public function reply(){
		$pid = isset($_POST['pid'])?htmlspecialchars($_POST['pid']):'0';
		$issue_id = isset($_POST['issue_id'])?htmlspecialchars($_POST['issue_id']):'';
		$context = isset($_POST['context'])?htmlspecialchars($_POST['context']):'';
		$url = C ( 'CURL_POST_URL' ) . "Answer/replyToSomeone";
		$data = array(
				'issue_id'=>$issue_id,
				'session_id'=>$this->session['session_id'],
				'reply_content'=>$context,
				'pid'=>$pid
		);
		$rel = $this->curl($url, $data);
		$this->ajaxReturn($rel,'json');
	} 
	public function editreply(){
		$id = isset($_POST['id'])?htmlspecialchars($_POST['id']):'0';
		$context = isset($_POST['context'])?htmlspecialchars($_POST['context']):'';
		$url = C ( 'CURL_POST_URL' ) . "Answer/editQuestionReply";
		$data = array(
				'id'=>$id,
				'session_id'=>$this->session['session_id'],
				'reply_content'=>$context,
		);
		$rel = $this->curl($url, $data);
		$this->ajaxReturn($rel,'json');
	}
	
	
}