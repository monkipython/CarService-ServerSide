<?php
use Think\Model;

class JpushModel extends Model{
	/**
	 * type 1 3 发给商户
	 * type 2 发给用户
	 * type 4 both
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
		foreach ($jidRe as $key => $row){
			$jid[] = strval($row);
		}
	
		if($type == 1 && is_array($jid)){
			if($this->user==2){
				//用户发需求
				$content = "您收到一条用户需求";
				$title ="提醒";
				$json = array('type'=>1,'demand_id'=>$data['demand_id']);
			}else{
				return ;
			}
		
		}elseif($type ==2 && is_array($jid)){
			if($this->user==0){
			//商户对用户报价
			$content = "有商家对您的需求报价啦！";
			$title ="提醒";
			$json = array('type'=>2,'demand_id'=>$data['demand_id'],'total_price'=>$data['total_price'],'total_time'=>$data['total_time'],'merchant_id'=>$data['merchant_id']);
			}else{
			
				return ;
			}
		}elseif($type ==3 && is_array($jid)){
			if($this->user==2){
				//用户确认订单
				$content = "您有一条新的订单！";
				$title ="提醒";
				$json = array('type'=>3,'order_no'=>$data['order_no']);
			}else{
				return ;
			}
			
		}elseif ($type == 4 && is_array($jid)){
			
			//遇见推送
			$content = "您有新的动态！";
			$title ="提醒";
			$json = array('type'=>4);
			
		}elseif ($type == 5 && is_array($jid)){
				
			//遇见聊天
			$content = $data['content'];
			$title ="聊天";
			$json = array('type'=>5,'jid'=>$data['jid']);
				
		}
		$br = '<br/>';
		//android('通知内容','标题','SDK内置通知栏样式','data')
		//ios('通知内容','sound','+1','content-available','category','data')
		try {
			$result = $this->client->push()
			->setPlatform(\JPush\Model\Platform('android', 'ios'))
			->setAudience(\JPush\Model\audience(\JPush\Model\alias($jid)/*,M\tag(array('tag1','tag2'))*/))
			/*         ->setAudience(M\all) */
// 			->setNotification(\JPush\Model\notification('驾遇',
// 					\JPush\Model\android($content, $title,1,$json),
// 					\JPush\Model\ios($content,'sound', '+1', true,$json))
// 			)
			->setMessage(\JPush\Model\message($content, $title, null,$json))
			->setOptions(\JPush\Model\options(null, null, null, true, 0))
			->printJSON()
			->send();
			
			echo 'Push Success.' . $br;
			echo 'sendno : ' . $result->sendno . $br;
			echo 'msg_id : ' .$result->msg_id . $br;
			echo 'Response JSON : ' . $result->json . $br;
		} catch (\JPush\Exception\APIRequestException $e) {
			echo 'Push Fail.' . $br;
			echo 'Http Code : ' . $e->httpCode . $br;
			echo 'code : ' . $e->code . $br;
			echo 'Error Message : ' . $e->message . $br;
			echo 'Response JSON : ' . $e->json . $br;
			echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
			echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
			echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
		} catch (\JPush\Exception\APIConnectionException $e) {
			echo 'Push Fail: ' . $br;
			echo 'Error Message: ' . $e->getMessage() . $br; 
			echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
		}
		
		
	}
}