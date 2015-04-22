<?php
namespace app\Model;
use Think\Log;
use Think\Model;
class XmppApiModel extends Model{
	public function __construct($username= "admin",$psd ="klf4life"){
		
		if(empty($username)&&empty($psd)){
			$username = "admin";
			$psd = "klf4life";
		}
		vendor("XMPPHP.XMPP");
		$this->conn = new \XMPPHP_XMPP(XMPP_SERVER_IP, XMPP_SERVER_PORT, $username, $psd, XMPP_SERVER_RESOURCE, XMPP_SERVER_DOMAIN, false, $loglevel=\XMPPHP_Log::LEVEL_ERROR);
		$this->jsonUtils=new \Org\Util\JsonUtils;
	}
	/**
	 * 注册聊天用户
	 * @param unique $username
	 * @param password $psd
	 * @param string $email
	 * @param muti $nick
	 */
	public  function register($username,$psd,$nick=null,$email=null){
		try{
			$this->conn->connect();
			//注册用户
			$this->conn->register($username,$psd,$email,$nick);

			$this->conn->disconnect();
			
			//注册成功 关联生成Vcard 添加vcard nickname avatar
		}catch(\XMPPHP_Exception $e){
			$this->jsonUtils->echo_json_msg(4, $e->getMessage());exit();
		}
	}
	public function updataHeader($header){
		if($header =='') return '';
		try{
			$this->conn->processUntil('session_start');
			$this->conn->presence();
			$vcard['photo'] = $header;
			$this->conn->sendVCard($vcard);
			
			$this->conn->disconnect();
		}catch(\XMPPHP_Exception $e){
			$this->jsonUtils->echo_json_msg(4, $e->getMessage());exit();
		}
	}
	/**
	 * 推送 需求推送
	 * $type = 1 用户发布需求
	 * $type = 2 商家对用户报价
	 */
	public function requestPush($type,array $jid,$data){
		try{
			
			$this->conn->connect();
			$this->conn->processUntil('session_start');
			if($type == 1 && is_array($jid)){
				foreach ($jid as $key =>$row){
					$to = $row.'@'.C('XMPP_SERVER_DOMAIN')."/".C('XMPP_SERVER_RESOURCE');
					$json = array('type'=>1,'demand_id'=>$data['demand_id']);
					$body = "[--request--]".json_encode($json);//用户发需求
					$this->conn->message($to, $body);
				}
			}elseif($type ==2 && is_array($jid)){
				$to = $jid[0].'@'.C('XMPP_SERVER_DOMAIN')."/".C('XMPP_SERVER_RESOURCE');
				$json = array('type'=>2,'demand_id'=>$data['demand_id'],'total_price'=>$data['total_price'],'total_time'=>$data['total_time'],'merchant_id'=>$data['merchant_id']);
				$body="[--request--]".json_encode($json);//商户对用户报价
// 				Log::write($body);
				$this->conn->message($to, $body);
			}elseif($type ==3 && is_array($jid)){
				$to = $jid[0].'@'.C('XMPP_SERVER_DOMAIN')."/".C('XMPP_SERVER_RESOURCE');
				$json = array('type'=>3,'order_no'=>$data['order_no']);
				$body="[--request--]".json_encode($json);//用户选择商户
				$this->conn->message($to, $body);
			}elseif($type ==4 && is_array($jid)){
				$to = $jid[0].'@'.C('XMPP_SERVER_DOMAIN')."/".C('XMPP_SERVER_RESOURCE');
				$json = array('type'=>4);
				$body="[--request--]".json_encode($json);//动态
				$this->conn->message($to, $body);
			}
			$this->conn->disconnect();
		}catch(\XMPPHP_Exception $e){
			$this->jsonUtils->echo_json_msg(4, $e->getMessage());
		}
		
	}
	/**
	 * 互相添加好友
	 * @param unknown_type $send_jid
	 * @param unknown_type $send_name
	 * @param unknown_type $receive_name
	 */
	public function addFriend($REVJID,$send_name="", $receive_name=""){
		try{
			$this->conn->connect();
			$this->conn->processUntil('session_start');
			$this->conn->presence();
			$this->conn->addRosterContact( $REVJID, 'both', $send_name, $receive_name);
			$out =$this->conn->roster->subscribed($this->conn->getFullJID(), $REVJID, $send_name, $receive_name);
            $this->conn->send($out);
			$this->conn->disconnect();
		}catch(\XMPPHP_Exception $e){
			$this->jsonUtils->echo_json_msg(4, $e->getMessage());
		}
	}
	/**
	 * 删减好友
	 * @param unknown_type $REVJID
	 * @param unknown_type $send_name
	 * @param unknown_type $receive_name
	 */
	public function delFriend($REVJID,$send_name="", $receive_name=""){
		try{
			$this->conn->connect();
			$this->conn->processUntil('session_start');
			$this->conn->presence();
			$this->conn->deleteRosterContact( $REVJID, 'both', $send_name, $receive_name);
			$this->conn->disconnect();
		}catch(\XMPPHP_Exception $e){
			$this->jsonUtils->echo_json_msg(4, $e->getMessage());
		}
	}
	
}