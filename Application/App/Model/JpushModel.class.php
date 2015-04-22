<?php
namespace app\Model;
use Think\Model;

class JpushModel extends Model{
	/**
	 * type 1 3 发给商户
	 * type 2 发给用户
	 * type 4 5 both
	 * user = 0 用户 =2 商户
	 */
	public function __construct(){
		vendor("Jpush.autoload");
		\JPush\JPushLog::setLogHandlers(array(new \Monolog\Handler\StreamHandler(RUNTIME_PATH .'/Logs/App/jpush.log', \Monolog\Logger::DEBUG)));
	}
	public function push($type,array $jidRe,$data=array()){
		$user =	$this->user ;
		if($user!=0 &&$user != 2){
			return ;
		}
		
		if($user == 0){
			//用户端
			$master_secret = 'f48628efd987ed7cc1c7fd11';
			$app_key='f4dc7979df38925abffa8783';
			$this->client = new \JPush\JPushClient($app_key, $master_secret);
		}elseif ($user == 2){
			//商户端
			$master_secret = 'df6e87a1fa52b6ff8aa1d816';
			$app_key ='d314b5b569ecb7af0e42011a';
			$this->client = new \JPush\JPushClient($app_key, $master_secret);
		}else{
			return ;
		}
	
		//easy push
		$jid = array();
		if(empty($jidRe)) return false;
		foreach ($jidRe as $key => $row){
			$jid[] = strval($row);
		}
	
		if($type == 1 && is_array($jid)){
			if($this->user==2){
				//用户发需求
				$content = "您收到一条用户需求";
				$title ="提醒";
				$json = array('type'=>1,'demand_id'=>$data['demand_id']);
				$sound = 'demand.mp3';
			}else{
				return ;
			}
		
		}elseif($type ==2 && is_array($jid)){
			if($this->user==0){
			//商户对用户报价
			$content = "有商家对您的需求报价啦！";
			$title ="提醒";
			$json = array('type'=>2,'demand_id'=>$data['demand_id'],'total_price'=>$data['total_price'],'total_time'=>$data['total_time'],'merchant_id'=>$data['merchant_id']);
			$sound = 'prompt.mp3';
			}else{
			
				return ;
			}
		}elseif($type ==3 && is_array($jid)){
			if($this->user==2){
				//用户确认订单
				$content = "您有一条新的订单！";
				$title ="提醒";
				$json = array('type'=>3,'order_no'=>$data['order_no']);
				$sound = 'prompt.mp3';
			}else{
				return ;
			}
			
		}elseif ($type == 4 && is_array($jid)){
			
			//遇见推送
			$content = "您有新的动态！";
			$title ="提醒";
			$json = array('type'=>4);
			$sound = 'prompt.mp3';
			
		}elseif ($type == 5 && is_array($jid)){
				
			//聊天推送
			$content = $data['content'];
			$title =$data['title'];
			$json = array('type'=>5,'jid'=>$data['jid']);
			$sound = 'prompt.mp3';
				
		}
		$br = '<br/>';
		//android('通知内容','标题','SDK内置通知栏样式','data')
		//ios('通知内容','sound','+1','content-available','category','data')
		try {
			$result = $this->client->push()
			->setPlatform(\JPush\Model\Platform('ios'))
			->setAudience(\JPush\Model\audience(\JPush\Model\alias($jid)/*,M\tag(array('tag1','tag2'))*/))
			/*         ->setAudience(M\all) */
			->setNotification(\JPush\Model\notification('驾遇',
					/*	\JPush\Model\android($content, $title,1,$json),*/
						\JPush\Model\ios($content,$sound, '+1', true,$json))
				)
			->setMessage(\JPush\Model\message($content, $title, null,$json))
			->setOptions(\JPush\Model\options(null, null, null, true, 0))
			->printJSON()
			->send();
// 			echo 'Push Success.' . $br;
// 			echo 'sendno : ' . $result->sendno . $br;
// 			echo 'msg_id : ' .$result->msg_id . $br;
// 			echo 'Response JSON : ' . $result->json . $br;
		} catch (\JPush\Exception\APIRequestException $e) {
			$str = 'Push Fail IOS. '.date('Y-m-d H:i:s') .'Http Code : ' . $e->httpCode .'code : ' . $e->code .'Error Message : ' . $e->message .'Response JSON : ' . $e->json .'rateLimitLimit : ' . $e->rateLimitLimit .'rateLimitRemaining : ' . $e->rateLimitRemaining .'rateLimitReset : ' . $e->rateLimitReset ;
			$log = M('RunLog');
			$jsondata = $json;
			$jsondata['user'] = $user;
			$jsondata['jid'] = $jid;
			$log->add(array('position'=>'Jpush','msg'=>"执行为".json_encode($jsondata)."出错:".$str,'addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
		} catch (\JPush\Exception\APIConnectionException $e) {
			$str = 'Push Fail IOS: '.'Error Message: ' . $e->getMessage() .'IsResponseTimeout: ' . $e->isResponseTimeout;
			$log = M('RunLog');
			$jsondata = $json;
			$jsondata['user'] = $user;
			$jsondata['jid'] = $jid;
			$log->add(array('position'=>'Jpush','msg'=>"执行为".json_encode($jsondata)."出错:".$str,'addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
		}
		try {
				
			$result = $this->client->push()
			->setPlatform(\JPush\Model\Platform('android'))
			->setAudience(\JPush\Model\audience(\JPush\Model\alias($jid)/*,M\tag(array('tag1','tag2'))*/))
			/*         ->setAudience(M\all) */
			// 			->setNotification(\JPush\Model\notification('驾遇',
					// 				/*	\JPush\Model\android($content, $title,1,$json),*/
					// 					\JPush\Model\ios($content,'sound', '+1', true,$json))
					// 			)
			->setMessage(\JPush\Model\message($content, $title, null,$json))
// 			->setOptions(\JPush\Model\options(null, null, null, true, 0))
			->printJSON()
			->send();
		
			// 			echo 'Push Success.' . $br;
			// 			echo 'sendno : ' . $result->sendno . $br;
			// 			echo 'msg_id : ' .$result->msg_id . $br;
			// 			echo 'Response JSON : ' . $result->json . $br;
		} catch (\JPush\Exception\APIRequestException $e) {
 			$str = 'Push Fail Android. '.date('Y-m-d H:i:s') .'Http Code : ' . $e->httpCode .'code : ' . $e->code .'Error Message : ' . $e->message .'Response JSON : ' . $e->json .'rateLimitLimit : ' . $e->rateLimitLimit .'rateLimitRemaining : ' . $e->rateLimitRemaining .'rateLimitReset : ' . $e->rateLimitReset ;
			$log = M('RunLog');
			$json['user'] = $user;
			$json['jid'] = $jid;
			$log->add(array('position'=>'Jpush','msg'=>"执行为".json_encode($json)."出错:".$str,'addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
		} catch (\JPush\Exception\APIConnectionException $e) {
			$str = 'Push Fail Android: '.'Error Message: ' . $e->getMessage() .'IsResponseTimeout: ' . $e->isResponseTimeout;
			$log = M('RunLog');
			$json['user'] = $user;
			$json['jid'] = $jid;
			$log->add(array('position'=>'Jpush','msg'=>"执行为".json_encode($json)."出错:".$str,'addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
		}
		
		
	}
}