<?php
namespace App\Controller;
use Think\Controller;
use Think\Log;

/**
 * ChatBase 聊天相关基础函授（非接口）--停滞
 * @author 
 *        
 */
class ChatBaseController extends Controller {
	
	public function _initialize() {
// 		$this->jsonUtils = new \Org\Util\JsonUtils ();
	}
	/**
	 * 注册chat 用户唯一标识 绑定在注册用户和注册商户之前
	 * 每秒1/8999 的概率出错，出错后提示重新提交一次即可
	 * @param int $sub_id
	 * @param int $type // 0 普通用户 2商户
	 */
	static public function registerChatUser($sub_id,$name,$header,$type=0){
		$db  = M('ChatUser');
		$userToken = time().rand(1000,9999);
		$add ['type'] = $type;
		$add ['chat_name'] = $name;
		$add ['chat_header'] = $header;
		$add ['sub_id'] = $sub_id;
		$add ['user_token'] = $userToken;
		$data = $db ->add ($add);
		if($data == false){
			return false;
		}else{
			return true;
			
		}
	}
	
	/**
	 * 每次用户发信息 记录每个用户的对话list
	 * @param int(11) $host_user_id 
	 * @param int(11) $receive_id
	 * @param smallint(3) $receive_type 消息类型 1 普通用户 2群组
	 * @param varchat(500) $msg 消息
	 */
	
	static public function addToUserChat($host_user_id,$msg,$receive_id,$send_type='1',$gourp_id ="0",$session_msg_id=""){
		$db = M('ChatUserMsgList');
		if(empty($session_msg_id)){
			//创建会话id 
			
			
			
		}else{
			//已存在会话id
		}
		
		
		
		
		if($check){
			$save = $db ->where(array('id'=>$check['id']))->save(array('last_msg'=>$msg,'addtime'=>time()));
			if($save){
				return 1;
			}else{
				return 0;
			}
			
		}else{
			//获取session_msg_id
			$data = $db ->add(array('send_id'=>$host_user_id,'receive_id'=>$receive_id,'msg_type'=>$send_type,'last_msg'=>$msg,'group_id'=>$gourp_id,'addtime'=>time()));
			echo $db->getLastSql();
			if($data){
				if($send_type==1){
					$data = $db ->add(array('send_id'=>$receive_id,'receive_id'=>$host_user_id,'msg_type'=>$send_type,'last_msg'=>$msg,'group_id'=>$gourp_id,'addtime'=>time()));
					if($data){
						return 1;
					}else{
						return 0;
					}
				}
				return 1;
			}else{
				return 0;
			}
		}
		
	}
	
	public function test(){
// 		$result = $this->registerChatUser(1);
		$result = $this->addToUserChat(1, 'text', 2,2,3003);
		dump($result);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}