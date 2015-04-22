<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
/**
 *
 * @author zhangzhi <583471388@qq.com>
 *        
 */
class ChatController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	public function _initialize() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->chat = new \Org\Util\Easemob ( C ( 'EASEMOB_OPTION' ) );
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( '' );
	
	}
	/**
	 * 获取用户聊天list
	 * @param  session_id 
	 */
	public function UserMsgList_get_json(){
		$session_id = $_POST ['session_id'];
		$uid = $this->session_handle->getsession_userid ( $session_id ,1);
		
		$db = M('');
		$data = $db ->table(C('DB_PREFIX')."chat_user_msg_list as a ")->field('b.chat_name,a.send_id,b.chat_header,a.last_msg,a.addtime,a.msg_type,c.group_name,a.group_id')
				->join(C('DB_PREFIX')."chat_user as b on a.send_id = b.id",'left')
				->join(C('DB_PREFIX')."chat_group as c on a.group_id = c.id",'left')
				->where("a.receive_id = $uid")->order('a.addtime desc')->select();
	
		//dump($data);
		if($data){
			foreach ($data as $key =>$row){
				$data[$key]['chat_header'] = imgUrl($row['chat_header']);
				$data[$key]['addtime']  = date('Y-m-d H:i:s',$row['addtime']);
				$data[$key]['unread_num'] = $this->getUnreadNum($uid,$row['send_id'],$row['msg_type']);
				if($row['msg_type'] ==1){
					unset($data[$key]['group_name']);
				}
			}
		}else{
			$data = array();
		}
		$this->jsonUtils->echo_json_data(0,	'ok', $data);
	}
	/**
	 * 获取未读消息
	 * @param int $uid
	 * @param int $send_id
	 * @param var（500） $msg_type
	 */
	public function getUnreadNum($uid,$send_id,$msg_type){
		$db = M('ChatUserMsg');
		$data = $db ->where(array('receive_id'=>$uid,'send_id'=>$send_id,'msg_type'=>$msg_type,'is_read'=>0))->count();

		return $data;
		
	}
	
	/**
	 * 获取单对单 和群组的 聊天信息
	 */
	public function messageWindow_get_json(){
		$session_id = $_POST ['session_id'];
		$uid = $this->session_handle->getsession_userid ( $session_id ,1);
		$send_id = isset ( $_POST ['send_id'] ) ? htmlspecialchars ( $_POST ['send_id'] ) : '';
		$msg_type = isset ( $_POST ['msg_type'] ) ? htmlspecialchars ( $_POST ['msg_type'] ) : '';
		$group_id = isset ( $_POST ['group_id'] ) ? htmlspecialchars ( $_POST ['group_id'] ) : '';
		$db = M ('ChatUserMsg');
// 		if($msg_type == 1 && $group_id ==0){
// 			//单对单聊天消息
// 			$condition = " send_id = $send_id and reveive_id = $uid ";
// 		}
// 		$data = $db ->where("(send_id=$send_id and reveive_id = $uid and msg_type = $msg_type and group_id = $group_id) or(send_id=$uid and reveive_id = $send_id and msg_type = $msg_type and group_id = $group_id) ")
		
	}
	
	
// 	function test(){
// 		$return = $this->chat->yy_hxSend('xuwei',array('15980919369','123'), 'rihoukeji');
// 		dump($return);die();
// 		$this->getUserMsgList();
// 	}
	/**
	 * chat聊天上图pic
	 */
	public function uploadPic(){
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		if ($_FILES) {
			$f_arr = mul_upload ( '/ChatPic/',3 );
			if ($f_arr) {
				$f_arr[0] = imgUrl($f_arr[0]);
				$this->jsonUtils->echo_json_data(0, 'ok', $f_arr[0]);exit();
			}
		}else{
			$this->jsonUtils->echo_json_msg(404, '未上传图片');exit();
		}
	}
	public function jpush(){
		$jid = isset ( $_REQUEST ['jid'] ) ? htmlspecialchars ( $_REQUEST ['jid'] ) : '';
		$content = isset ( $_POST ['content'] ) ? htmlspecialchars ( $_POST ['content'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$user = M('SystemUser')->where(array('id'=>$jid))->field('type,name')->find();
		//jpush
		$jpush = new \App\Model\JpushModel();
		$jpush->user = $user['type'];
		$string = $content;
		$jpush->push(5, array($jid),array('content'=>$string,'jid'=>$systemid,'title'=>$user['name']));
		$this->jsonUtils->echo_json_msg(0,	'ok');exit();
	}
	//未使用在找解决方法
	public function voiceToFile(){
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		date_default_timezone_set("Asia/Shanghai");
		$time_str = date("Y-m-d"); 
		$randomID = uniqid();
		$chatVoicePath = "./Uploads/ChatVoiceMessage/".$time_str."/".$randomID.".amr";
		$originPAth = "http://121.40.92.53/ycbb/Uploads/ChatVoiceMessage/".$time_str."/".$randomID.".amr";
		
		$voice_encode = isset ( $_POST ['voice'] ) ? htmlspecialchars ( $_POST ['voice'] ) : '';
		
		if($voice_encode == ""){
			$this->jsonUtils->echo_json_data(404, '语音格式有误');exit();
		}
		
		$tmp = base64_decode(urlencode($voice_encode));
		file_put_contents($chatVoicePath, $tmp);
		
		if(file_exists($chatVoicePath)){
			$this->jsonUtils->echo_json_data(0, 'ok', $originPAth);exit();
		}else{
			$this->jsonUtils->echo_json_data(404, '语音格式有误');exit();
		}



	}
	
	/**
	 * 搜索好友 通过手机号
	 */
	
	public function searchFriend(){
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		
		$phone = isset ( $_POST ['phone'] ) ? htmlspecialchars ( $_POST ['phone'] ) : '';
		if(strlen($phone) != 11 || !is_numeric($phone)){
			$this->jsonUtils->echo_json_msg(4, '手机号不符合格式');exit();
		}
		$db = M('SystemUser');
		$data = $db ->where(array('phone'=>$phone))->field('id,name,header,phone')->select();
		if($data){
		foreach ($data as $key =>$row){
			unset($data[$key]['id']);
			$data[$key]['header'] = imgUrl($row['header']);
			$data[$key]['jid'] = $row['id'].'@'.C('XMPP_SERVER_DOMAIN')."/".C('XMPP_SERVER_RESOURCE');
		}
		}else{
			$data =array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	}
	
	
	/**
	 * 根据jid 获取用户信息
	 */
	public function getChatUserData(){
		//跨域解决方法: 指定域名
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		
		$jid = isset ( $_POST ['jid'] ) ? htmlspecialchars ( $_POST ['jid'] ) : '';
		$db = M('SystemUser');
		$data = $db ->where(array('id'=>$jid))->field('name,header')->find();
		if($data === false){
			$this->jsonUtils->echo_json_msg(4, '查询失败');exit();
		}else{
			$data['header'] = imgUrl($data['header']);
			$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
		}
	}
	
	public function chatLog() {
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		$startAt = $_POST ['startAt'];
		$endAt = $_POST ['endAt'];
		$user = $_POST ['me'];
		$roster = $_POST ['roster'];
		$link = mysqli_connect ( "localhost", "debian-sys-maint", "nnDekZYaqo5kEJPD","chatDB" );
		if (mysqli_connect_errno ()) {
			die ( "{code:1, msg:'连接数据库有误！'}" );
		}
		mysqli_set_charset($link, "utf8");
		$result = mysqli_query ( $link, "SELECT * FROM (SELECT * FROM ofMessageArchive WHERE (fromJID = '$user'" . "AND toJID = '$roster') OR (fromJID = '$roster' AND toJID = '$user') " . "ORDER BY sentDate DESC LIMIT $startAt, $endAt) tmp order by tmp.sentDate asc;" );
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			$arr [] = array (
					'fromJID' => $row ['fromJID'],
					'toJID' => $row ['toJID'],
					'sentDate' => date('Y-m-d H:i:s',$row ['sentDate']/1000),
					'body' => $row ['body'] 
			);
		}
		echo json_encode ( $arr );
		mysqli_free_result ( $result );
		mysql_close($link);
	}
	
	public function addUserToRoster(){
		header(	'Access-Control-Allow-Origin:http://www.caryu.net' );
		header( 'Access-Control-Allow-Credentials:true' );
		$username = isset ( $_POST ['username'] ) ? htmlspecialchars ( $_POST ['username'] ) : '';
		$user_jid = isset ( $_POST ['user_jid'] ) ? htmlspecialchars ( $_POST ['user_jid'] ) : '';
		$nick = isset ( $_POST ['nick'] ) ? htmlspecialchars ( $_POST ['nick'] ) : '';
		$link = mysqli_connect ( "localhost", "debian-sys-maint", "nnDekZYaqo5kEJPD" );
		if (mysqli_connect_errno ()) {
			die ( "{code:1, msg:'连接数据库有误！'}" );
		}
		mysql_select_db("chatDB", $link);
		mysqli_query ( $link, "INSERT INTO ofRoster (username, jid, sub, ask, recv, nick) VALUES ('"+$username+"','"+$user_jid+"',3,-1,-1,'"+$nick+"');" );
		mysql_close($link);
		echo json_encode ( "{code:0, msg:'ok'}" );
	}
}