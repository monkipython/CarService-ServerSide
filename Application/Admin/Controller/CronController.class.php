<?php


use Think\Controller;

class CronController extends Controller{
	
	/**
	 * AnswerReply
	 * 定时执行问答的回复 loop 问题不需要定时任务
	 */
	public function answer_reply(){
		set_time_limit(0);
		$db = M('AnswerReply');
		$log = M('RunLog');
		$problem  = M('AnswerProblem');
		$atten = M('AnswerAttention');
		$data = $db ->where(array('status'=>1,'addtime'=>array('lt',time())))->select();
		$save['status'] = 0;
		if(!empty($data)){
		foreach ($data as $key =>$row){
			
			$attention = $atten ->where(array('issue_id'=>$row['issue_id']))->field('system_user_id as belong ')->select();
			$auth = $problem->where(array('id'=>$row['issue_id']))->find();
			if(empty($row['pid']) ){
				//顶层回复
					$data = $db->where(array('id'=>$row['id'])) ->save($save);
					if($data){
						$problem->where(array('id'=>$row['issue_id']))->setInc('answer_num');
						if($attention){
							foreach ($attention as $k =>$r){
								AnswerController::addMessage($r['belong'],$row['issue_id'],$auth['title'],3, $row['reply_id'], $auth['system_user_id'],$row['reply_content'],$row['id']);
							}

						}
					//问题所有者 收到回答消息提示
						AnswerController::addMessage($auth['system_user_id'],$row['issue_id'],$auth['title'],1, $row['reply_id'], $auth['system_user_id'],$row['reply_content'],$row['id']);
					}else{
						$log->add(array('position'=>'cronAnswer','msg'=>'执行id为'.$row['id'].'的定时任务更新数据库(AnswerReply)失败','addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
					}
			}else{
			
				//验证上级回复是否存在
				$pidData =$db ->where(array('id'=>$row['pid']))->field('issue_id,reply_id')->find();
				if($pidData ['issue_id'] != $row['issue_id'] || empty($pidData)){
					$log->add(array('position'=>'cron','msg'=>'执行id为'.$row['id'].'的定时任务,获取上级回复失败(AnswerReply)','addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
				}
				$data = $db->where(array('id'=>$row['id'])) ->save($save);
				if($data){
						
					if($attention){
						foreach ($attention as $k =>$r){
							AnswerController::addMessage($r['belong'],$row['issue_id'],$auth['title'],4, $row['reply_id'], $pidData['reply_id'],$row['reply_content'],$row['id']);
						}

					}
					//问题所有者 收到评论消息提示
					AnswerController::addMessage($auth['system_user_id'],$row['issue_id'],$auth['title'],2, $row['reply_id'], $pidData['reply_id'],$row['reply_content'],$row['id']);
					//答案者 收到评价的消息提示
					AnswerController::addMessage($pidData['reply_id'],$row['issue_id'],$auth['title'],2, $row['reply_id'], $pidData['reply_id'],$row['reply_content'],$row['id']);
					
				}else{
					$log->add(array('position'=>'cronAnswer','msg'=>'执行id为'.$row['id'].'的定时任务更新数据库(AnswerReply)失败','addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
				}
			
			
			}
				
		}
		$log->add(array('position'=>'cronAnswer','msg'=>'(AnswerReply)执行定时任务成功','addtime'=>date('Y-m-d H:i:s',time()),'status'=>0));
		
	}else{
		$log->add(array('position'=>'cronAnswer','msg'=>'(AnswerReply)执行定时任务成功','addtime'=>date('Y-m-d H:i:s',time()),'status'=>0));
	}
}
/**
 * 重置 回答条数
 */
// public function recentCount(){
// 		set_time_limit(0);
// 		$db = M('AnswerUserRecent');
// 		$data = $db->field('id')->select();
// // 		dump($data);die();
// 		$c = count($data);
// 		$chid = M('AnswerUserRecentReply');
// 		$success = 0; $error = 0;
// 		foreach ($data as $key =>$row){
// 			$count = $chid->where('status = 0 and recent_id ='.$row['id'].' and addtime < '.time())->count();
// // 			dump($count);
// 			if($count>0){
// 				$st = $db->where("id =". $row['id'])->save(array('comment_count'=>$count));
// 				if($st === false ){
// 					$error = $error +1;
// 				}else{
					
// 					$success = $success +1;
// 				}
// 			}
// 		}
// 		$em = $c - $success -$error;
// 		echo "总计$c,成功$success,失败$error,为空$em";
// 	}
// public function change(){
// 	$db = M('AnswerUserRecentReply');
// 	$data = $db->where('addtime >'.time().' and status = 0')->save(array('status'=>1));
	
// }

/**
 * 动态 定时执行
 */
public function recent_reply(){
	set_time_limit(0);
	$db = M('AnswerUserRecentReply');
	$log = M('RunLog');
	$recent  = M('AnswerUserRecent');
	$data = $db ->where(array('status'=>1,'addtime'=>array('lt',time())))->select();
// 	echo $db ->getLastSql();
//  	dump($data);die();
	$save['status'] = 0;
	if(!empty($data)){ 
		foreach ($data as $key =>$row){
			$auth =$recent->where(array('id'=>$row['recent_id']))->find();
			if($row['pid'] == 0){
				//保存成该回复已正常执行
				$dbsave = $db->where(array('id'=>$row['id']))->save($save);
				if ($dbsave === false){
					$log->add(array('position'=>'cronRecent','msg'=>'(AnswerUserRecentReply)执行定时任务失败'.$db->getLastSql(),'addtime'=>date('Y-m-d H:i:s'),'status'=>1));
				
				}else{
					//动态 评价+1
					$recent->where(array('id'=>$row['recent_id']))->setInc('comment_count');
					$arr['belong'] = $auth['system_user_id'];//消息所有者
					$arr['recent_id'] = $row['recent_id'];//动态id
					$arr['recent'] = $auth['content'];//动态内容
					$arr['send_id'] = $row['reply_id'];//消息发送者
					$arr['receive_id'] = $auth['system_user_id'];//消息接受者
					$arr['msg'] = $row['reply_content'];//消息主体
					$arr['reply_id'] = $row['id'];//对其快速回复的pid
					$arr['addtime'] = time();
					//动态所有者 收到消息提示
					$this->addRecentMessage($arr);
				
				}
		
			
			}else{
				//验证上级回复是否存在
				$pidData =$db ->where(array('id'=>$row['pid']))->field('recent_id,reply_id')->find();
				if($pidData ['recent_id'] != $row['recent_id'] || empty($pidData)){
					$log->add(array('position'=>'cronRecent','msg'=>'(AnswerUserRecentReply)执行定时任务失败:上级pid验证出错或者数据源不匹配','addtime'=>date('Y-m-d H:i:s'),'status'=>1));
				}

				$dbsave = $db->where(array('id'=>$row['id']))->save($save);
				if ($dbsave === false){
					$log->add(array('position'=>'cronRecent','msg'=>'(AnswerUserRecentReply)执行定时任务失败'.$db->getLastSql(),'addtime'=>date('Y-m-d H:i:s'),'status'=>1));
				
				}else{
					$recent->where(array('id'=> $row['recent_id']))->setInc('comment_count');
					$arr['belong'] = $auth['system_user_id'];//消息所有者
					$arr['recent_id'] = $row['recent_id'];//动态id
					$arr['recent'] = $auth['content'];//动态内容
					$arr['send_id'] = $row['reply_id'];//消息发送者
					$arr['receive_id'] = $pidData['reply_id'];//消息接受者
					$arr['msg'] = $row['reply_content'];//消息主体
					$arr['reply_id'] = $row['id'];//对其快速回复的pid
					$arr['addtime'] = time();

			
					//问题所有者 收到评论消息提示
					$b=$this->addRecentMessage($arr);
					//答案者 收到评价的消息提示
					if($pidData['reply_id'] != $auth['system_user_id']){
						$arr['belong'] = $pidData['reply_id'];//消息所有者
// 						dump($arr);
						$this->addRecentMessage($arr);
					}
				}
		
			}
			
	
		}
		$log->add(array('position'=>'cronRecent','msg'=>'(AnswerUserRecentReply)执行定时任务成功','addtime'=>date('Y-m-d H:i:s',time()),'status'=>0));

	}else{
		$log->add(array('position'=>'cronRecent','msg'=>'(AnswerUserRecentReply)执行定时任务成功','addtime'=>date('Y-m-d H:i:s',time()),'status'=>0));
	}
}
	public  function addRecentMessage($arr){
		$db = M('AnswerUserRecentMessage');
		$data = $db ->add($arr);
		if($data){
			$user = M('SystemUser')->where(array('id'=>$arr['belong']))->getField('type');
			//jpush
			$jpush = new \App\Model\JpushModel();
			$jpush->user = $user;
			$jpush ->push(4, array($arr['belong']),array());
			
			$xmpp = new \App\Model\XmppApiModel();
			$xmpp ->requestPush(4, array($arr['belong']),array());
			
		}
		return $data;
	}	



	/**
	 * 发布需求 规则：：（以下数值均可调整）
	 * 1 5分钟内商家报价达到10 或者 用户选择商户，停止推送，（达到10家报价 不从未保价消失，只提示已超过10家商户报价）
	 * 2 5分钟后，没有达到10家报价，推送半径增加2km，且剔除已推送过的商家。如范围内没有商家，立即叠加2km （最多5次，即范围13km后不在推送）
	 * 3 5分钟为一次循环点，每次增加2km，直到报价到达10家，或者用户选择商家 或者总半径范围达到13km
	 * 4    报价个数只增不减，即使商家撤销了报价。
	 */
	public function demand_push(){
		set_time_limit(0);
		//初始公里数
		$initKm = C('PUSH_RANGE_KM');
		$initBindingNUm = C('PUSH_MAX_BINDING_NUM');
		$initRangeKm = C('PUSH_MAX_RANGE_KM');
		$initPerKm = C('PUSH_PER_KM');
		$time = time();
		//筛选出哪些是应该推送的 报价小于{}、未选定商家、范围小于{}、未过期需求[未删除]
		// range_km 不等于0 ；等于0为只推送一次，无需再次扩充范围
		$map = array('is_bidding' => array('lt',$initBindingNUm),
					 'merchant_id' => 0,
					 'range_km' =>array( array('lt',$initRangeKm), array('neq',0)),
					 'status' => 0,
					 'expire_time'=>array('gt',$time),
				
				);
		
		$demandDb = M('MemberDemand');
		$data = $demandDb->where($map)->field('id,range_km,longitude,latitude')->select();
// 		echo $demandDb->getlastSql();
// dump($data);die();
		
		$log = M('RunLog');
		$merchantDb = M('Merchant');
		$merchantEnable = M('DemandMerchantEnable');
// 		dump($data);die();
		if(!empty($data)){
		foreach ($data as $key =>$row){
			//再次确认是否需要推送
			$mapCheck = $map;
			$mapCheck['id'] = $row['id'];
			$mapCheck['expire_time'] = array('gt',time());
			$check = $demandDb->where($mapCheck)->find('id');
			if($check !== false){
				$current = $row['range_km'];
				do{
					$loop = false;
					$current += $initPerKm;
// dump($current);
					//范围不超过最大值
					if($current > $initRangeKm){
						break;
					}
					//获取最大最小经纬度
					$ll_arr=rangekm($current, $row['longitude'],$row['latitude']);
					$maxLng=$ll_arr['maxLng'];
					$minLng=$ll_arr['minLng'];
					$maxLat=$ll_arr['maxLat'];
					$minLat=$ll_arr['minLat'];
					//获取范围内商家
					$merchantList = array();
					$sql="select a.id,b.id as jid,a.business_time from ". C('DB_PREFIX')."merchant as a
					left join ". C('DB_PREFIX')."system_user as b on (a.id = b.sub_id and b.type =2)
					where a.longitude <=$maxLng and a.longitude>=$minLng and a.latitude <=$maxLat and a.latitude>=$minLat and (a.status = 0 or a.status =1) ";
					$merchantList = M('')->query($sql);
					$merchantListCount = count($merchantList);
// dump($merchantListCount);
					//剔除已推送的商家
					$merchantEnableList = $merchantEnable->where(array('demand_id'=>$row['id']))->field('merchant_id')->select();
					$merchantEnableListCount = count($merchantEnableList);
// dump($merchantEnableListCount);
					//如果有商家的话
					if($merchantListCount > $merchantEnableListCount){
						$MerchantLeft = array();
						foreach ($merchantList as $ke =>$ro){
							foreach ($merchantEnableList as $k =>$r){
								$enable = 0;
								if($ro['id'] == $r['merchant_id']){
									$enable = 1;
									break;
								}
							}
							if($enable == 0){
								$MerchantLeft[] = $ro;
							}
						
						}	
						
						//对剩余商家进行推送
// $MerchantLeft = array(array('id'=>105,'jid'=>18));
						$jid = array();
						foreach ($MerchantLeft as $mk =>$mr){
							$addAll [$mk]['id'] = null;
							$addAll [$mk]['merchant_id'] = $mr['id'];
							$addAll [$mk]['demand_id'] = $row['id'];
							if(timeCompare($mr['business_time'])){
								$jid[] = $mr['jid'];
							}
						}
						$addMerchant = $merchantEnable->addAll($addAll);
// $log->add(array('position'=>'cronPush','msg'=>json_encode($addAll),'addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
						if(!$addMerchant){
							$log->add(array('position'=>'cronPush','msg'=>'执行id为'.$row['id'].'的定时任务add数据库(DemandMerchantEnable)失败','addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
						}

						if($addMerchant === false){
							$log->add(array('position'=>'cronPush','msg'=>'执行id为'.$row['id'].'的需求时，周围'.$current.'km无商家','addtime'=>date('Y-m-d H:i:s',time()),'status'=>1));
						}
						//云推送
						$push ['demand_id'] = $row['id'];
// dump($push);
						$jpush = new \App\Model\JpushModel();
						 $jpush->user = 2;
						 $jpush ->push(1, $jid, $push);
						 $xmpp = new \App\Model\XmppApiModel();
						 $xmpp ->requestPush(1, $jid, $push);
					
						
					}else{
						$loop = true;
					}
					//更新已推送的公里数
					$saveRange = $demandDb->where(array('id'=>$row['id']))->save(array('range_km'=>$current));
				
				}while($loop);
			
			}
		}
		}
		$log->add(array('position'=>'cronPush','msg'=>'定时任务更新数据库(MemberDemand)成功','addtime'=>date('Y-m-d H:i:s',time()),'status'=>0));
	}

// 	public function test(){
// 	$jid = array(12);
// 		$jpush = new \App\Model\JpushModel();
// 						 $jpush->user = 2;
// 						 $jpush ->push(1, $jid, $data);
// 						 $xmpp = new \App\Model\XmppApiModel();
// 						 $xmpp ->requestPush(1, $jid, $data);
// 	}
	protected  function dealtimeRecent(){
		$db = M('AnswerUserRecent');
		$a = strtotime('2015-04-15 24:00:00');
		$data = $db ->where(array('status'=>array('neq','-1'),'addtime'=>array('gt',time())))->select();
// 		dump($data);die();
		$inc =5*3600;
		$i = 0;
		foreach ($data as $key =>$row){
			$ttime = $row['addtime']-$inc;
// 			dump($ttime);die();
			if( $ttime > time()){
				$save ['status']=1;
			}else{
				$save ['status']=0;
			}
			$save ['addtime'] =$ttime;
			$da = $db->where(array('id'=>$row['id']))->save($save);
			if($da ===false){
				
			}else{
				$i++;
			}
		}
		dump($i);
	}
	

}