<?php

class RecentController extends CommonController{
	//静态 处理后的数据
	static private $treeList = array();
	//树层级 从1开始
	static private $treekey = 0;
	//缓存每次剩余下数据
	static private $data = array();

	public function index(){
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		$keywords = !empty($_REQUEST['keywords'])?htmlspecialchars($_REQUEST['keywords']):'';
		$Btime = empty($_REQUEST['Btime']) ? '' : $_REQUEST['Btime'];
		$Etime = empty($_REQUEST['Etime']) ? '' : $_REQUEST['Etime'];
		$timeStyle = empty($_REQUEST['timeStyle']) ?'0':$_REQUEST['timeStyle'];
		$url = '/index.php/Admin/Recent/index/';
		$Btimestr = strtotime($Btime);
		$Etimestr = strtotime($Etime);
	
	
		if(!empty($keywords)){
			$url.='keywords/'.$keywords.'/';
		}
		if(!empty($page)){
			$url.=C('VAR_PAGE').'/'.$page.'/';
		}
		if(!empty($num)){
			$url.='numPerPage/'.$num;
		}
		if(!empty($timeStyle)){
			$url.='timeStyle/'.$timeStyle;
		}
		
		$db = M('AnswerUserRecent');
		$map = array('status'=>array('neq','-1'));
		$mapdata =array('a.status'=>array('neq','-1'));
		$mapPage = array();

		if(!empty($Btime)&&empty($Etime)){
			$map['addtime'] = array('gt',$Btimestr);
			$mapdata['a.addtime'] = array('gt',$Btimestr);
			$mapPage['Btime'] = $Btime;
		}elseif (empty($Btime)&&!empty($Etime)){
			$map['addtime'] = array('lt',$Etimestr);
			$mapdata['a.addtime'] = array('lt',$Etimestr);
			$mapPage['Etime'] = $Etime;
		}elseif(!empty($Btime)&&!empty($Etime)){
			if($Btimestr>$Etimestr){
				$this->error('起始时间大于截止时间');
			}
			$map['addtime'] = array( array('gt',$Btimestr),array('lt',$Etimestr));
			$mapdata['a.addtime'] = array( array('gt',$Btimestr),array('lt',$Etimestr));
			$mapPage['Etime'] = $Etime;
			$mapPage['Btime'] = $Btime;
		}
		if(!empty($keywords)){
			$map['content'] = array('like',"%$keywords%");
			$mapdata['a.content'] = array('like',"%$keywords%");
			$mapPage['keywords'] = $keywords;
		}
		
		if($timeStyle == 1){
			//lt
			$map['addtime'] = array('lt',time());
			$mapdata['a.addtime'] = array('lt',time());
			$mapPage['timeStyle'] = '1';
		}elseif($timeStyle == 2){
			//gt
			$map['addtime'] = array('gt',time());
			$mapdata['a.addtime'] = array('gt',time());
			$mapPage['timeStyle'] = '2';
		}

		
		$count = $db ->where($map)->count();
		$this->_page($count,$mapPage,$page,$num);
		
		$data = $db->table(C("DB_PREFIX")."answer_user_recent as a ")->field("a.id,b.name,a.content,a.system_user_id,a.addtime,a.longitude,a.latitude,a.comment_count")
		->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id")
		->where($mapdata)->limit($num)->page($page)->order("a.addtime desc")->select();
		$dbReply = M('AnswerUserRecentReply');
// 		dump($data);
		foreach ($data as $key =>$row){
			$data[$key]['reply_count'] = $dbReply->where(array('recent_id'=>$row['id'],'status'=>0))->count();
		}
// 		dump($data);
		$this->assign('btime',$Btime);
		$this->assign('etime',$Etime);
		$this->assign('timeStyle',$timeStyle);
		$this->assign('list', $data);
		$this->assign('furl', base64_encode(urlencode($url)));
		$this->assign('keywords',$keywords);
		$this->display();
	}
	public function add(){
		$model = M('city');
		
		$province = $model->where('pid=0')->getField('id,name');
		$this->assign("province", $province);
		
		$currentUser = $this->getCurrentUser();
	
		$_SESSION['pics_recent'] =array();
		$this->display();
	}
	function insert() {
		$longitude = empty($_REQUEST['longitude']) ?'':$_REQUEST['longitude'];
		$latitude = empty($_REQUEST['latitude']) ?'':$_REQUEST['latitude'];
		$title = empty($_REQUEST['title']) ?'':$_REQUEST['title'];
		$time = empty($_REQUEST['time']) ?'':$_REQUEST['time'];
		$systemid= $this->getCurrentUser();
		if(empty($longitude)){
			$this->error('选择精度');
		}
		if(empty($latitude)){
			$this->error('选择维度');
		}
		if(empty($title)){
			$this->error('填写问题');
		}
	
		if(!empty($time)){
			$time = strtotime($time);
		}else{
			$time = time();
		}
		$model = M('AnswerUserRecent');
		$arr = array(
				'longitude'=>$longitude,
				'latitude'=>$latitude,
				'addtime'=>$time,
				'system_user_id'=>$systemid,
				'content'=>$title,
	
		);
		$pics = $_SESSION['pics_recent'];
		if(empty($pics)){
			$arr['pics']='[]';
		}else{
				
			$arr['pics'] =json_encode($pics);
		}
		$list = $model->add($arr);
		if ($list !== false) { //保存成功
			$this->success('新增成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	public function del(){
		$this->_delete('AnswerUserRecent');
	}
	public function pdel(){
		$ids = !empty($_REQUEST['ids'])?$_REQUEST['ids']:'';
		if(empty($ids)){
			$this->error('ids为空');
		}
		$ids = explode(',', $ids);
		$db = M('AnswerUserRecent');
		$data = $db ->where(array('id'=>array('in',$ids)))->setField('status',-1);
		if($data === false){
			$this->error('批量删除失败');
		}else{
			$this->success('批量删除成功');
		}
	}
	public function detail(){
			//	dump($_GET);
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		$furlen = isset ( $_GET ['furl'] ) ?   $_GET ['furl']  : '';
		$furl=urldecode (base64_decode($furlen));
//  	dump($furl);
		$currentUser = $this->getCurrentUser();
		$db = M('AnswerUserRecent');
		$recent= $db->table(C('DB_PREFIX')."answer_user_recent as a ")->field('a.id,a.system_user_id,a.content,a.pics,a.addtime,b.header,b.name,a.latitude,a.longitude')
		->join(C('DB_PREFIX')."system_user as b on b.id = a.system_user_id",'LEFT')
		->where(array('a.id'=>$id))->find();
			
		$ulr="http://api.map.baidu.com/geocoder/v2/?ak=4TGAqmofi6LcGeNYVFlOTOQG&output=json&pois=0&location=".$recent['latitude'].','.$recent['longitude'];
		$adrressJson = file_get_contents($ulr);
		$adrress = json_decode($adrressJson,true);
// 		dump($adrress);
		$recent['baidumap'] = $adrress['result']['formatted_address'];
		if(empty($recent)){
			$this->jsonUtils->echo_json_msg(4, '动态不存在');exit();
		}
		$recent ['header'] = imgUrl($recent['header']);
		$recent ['addtime'] = date('Y-m-d H:i:s',$recent['addtime']);
		$recent ['pics'] = imgUrl(json_decode($recent['pics'],true));
		
		$this->data = array();
		$data = $db ->table (C('DB_PREFIX')."answer_user_recent_reply as a")->field('a.id,b.name as reply_name,a.reply_id as system_userid,ifnull(c.name,"") as pidname,a.reply_content,a.addtime,a.pid')
		->join(C('DB_PREFIX')."system_user as b on b.id = a.reply_id",'LEFT')
		->join(C('DB_PREFIX')."system_user as c on c.id = a.pid_id",'LEFT')
		->where(array('a.recent_id'=>$recent['id'],'a.status'=>0))->order('a.addtime asc')->select();
		if(!empty($data)){
			foreach ($data as $key=>$row){
				$data[$key]['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
			}
			$recent['count'] = count($data);
			$this->data = $data;
			$data = $this->dealRecentArray();
		}else{
			$data= array();
			$recent['count'] = 0;
		}
// 		dump($recent);
		$recent['child'] = $data;
		$this->assign('furlen',$furlen);
		$this->assign('furl',$furl);
		$this->assign('data',$recent);
// 		dump($recent);
		$this->display();
	}
	
	function changetext(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$text = isset ( $_POST ['text'] ) ?   $_POST ['text']  : '';
		$imgChange = isset ( $_POST ['imgChange'] ) ?   $_POST ['imgChange']  : '';
		$imgarr = isset ( $_POST ['imgarr'] ) ?   $_POST ['imgarr']  : '';
		if(empty($id)||empty($text)){
			$this->ajaxReturn(array('code'=>1,'msg'=>'参数不全'));
		}
		if($imgChange){
			$imgarr  = array_filter($imgarr);
			foreach ($imgarr as $row){
				unset($row['hbsize']);
				unset($row['hdsize']);
				unset($row['hssize']);
				$row['hs'] = str_replace(C('ROOT_UPLOADS'), '', $row['hs']);
				$row['hd'] = str_replace(C('ROOT_UPLOADS'), '', $row['hd']);
				$row['hb'] = str_replace(C('ROOT_UPLOADS'), '', $row['hb']);
				$img[] = $row;
			}
			$save ['pics'] = json_encode($img);
		}
		$save['content'] = $text;
		$db = M('AnswerUserRecent');
		$data = $db ->where(array('id'=>$id))->save($save);
		if($data ===false){
			$this->ajaxReturn(array('code'=>1,'msg'=>'修改失败'));
		}else{
			$this->ajaxReturn(array('code'=>0,'msg'=>'ok','data'=>''));
		}
		
		
		
		
	
	}
	function changeReply(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$text = isset ( $_POST ['text'] ) ?   $_POST ['text']  : '';
		if(empty($id)||empty($text)){
			$this->ajaxReturn(array('code'=>1,'msg'=>'参数不全'));
		}
		$db = M('AnswerUserRecentReply');
		$data = $db ->where(array('id'=>$id))->save(array('reply_content'=>$text));
		if($data){
			$this->ajaxReturn(array('code'=>0,'msg'=>'ok','data'=>$text));
		}else{
			$this->ajaxReturn(array('code'=>1,'msg'=>'修改失败'));
		}
	
	}
	public function delete_reply(){
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if(empty($id)){
			$this->ajaxReturn(array('code'=>1,'msg'=>'参数不全'));
		}
		$db = M('AnswerUserRecentReply');
		$data = $db ->where(array('id'=>$id))->save(array('status'=>-1));
		if($data ===false){
			$this->ajaxReturn(array('code'=>1,'msg'=>'删除失败'));
				
		}else{
			
			$recent_id = $db ->where(array('id'=>$id))->getField('recent_id');
			M('AnswerUserRecent')->where(array('id'=>$recent_id))->setDec('comment_count');
			$this->ajaxReturn(array('code'=>0,'msg'=>'删除成功',));
		}
	
	}
	/**
	 * 编辑回答
	 */
	public function replyToRecent(){
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		$pid = isset ( $_GET ['pid'] ) ? htmlspecialchars ( $_GET ['pid'] ) : '';
		$submiter = $_REQUEST['submiter'];
		$systemid = $this->getCurrentUser();
	
		if($systemid ==$submiter){
			$note = true;
		}else{
			$note = false;
		}
		$db = M('AnswerUserRecent');
		$data = $db ->where(array('id'=>$id))->find();
		if($data){
			$this->assign('note',$note);
			$this->assign('id',$id);
			$this->assign('pid',$pid);
		}else{
			$this->error('改动态不存在');exit();
		}
		$this->display();
	}
	public function replyToAction(){
		
		$systemid = $this->getCurrentUser();
		$pid = isset ( $_POST ['pid'] ) ? htmlspecialchars ( $_POST ['pid'] ) : '';
		$recent_id = isset ( $_POST ['recent_id'] ) ? htmlspecialchars ( $_POST ['recent_id'] ) : '';
		$reply_context = isset ( $_POST ['reply_content'] ) ? htmlspecialchars ( $_POST ['reply_content'] ) : '';
		$time = empty($_REQUEST['time']) ?'':$_REQUEST['time'];
		if(!empty($time)){
			$time = strtotime($time);
		}else{
			$time = time();
		}
		if($time >time()){
			$status = 1;//定时任务
		}else{
			$status = 0;
		}
		if(empty($recent_id)){
			$this->error( '动态id为空');
			exit();
		}
		
		if(empty($reply_context)){
			$this->error( '回答内容为空');
			exit();
		}
		$auth = M('AnswerUserRecent')->where(array('id'=>$recent_id))->find();
		if($auth){
			if(empty($pid) ){
				//顶层回复
				$data ['recent_id'] = $recent_id;
				$data ['reply_id'] = $systemid;
				$data ['reply_content'] = $reply_context;
				$data ['addtime'] = $time;
				$data ['pid'] = 0;
				$data ['pid_id'] =0;
				$data ['status'] = $status;	
				$db = M('AnswerUserRecentReply');
				$data = $db ->add($data);
				if($data){
					
					if ($status == 0){
						M('AnswerUserRecent')->where(array('id'=>$recent_id))->setInc('comment_count');
						$arr['belong'] = $auth['system_user_id'];//消息所有者
						$arr['recent_id'] = $recent_id;//动态id
						$arr['recent'] = $auth['content'];//动态内容
						$arr['send_id'] = $systemid;//消息发送者
						$arr['receive_id'] = $auth['system_user_id'];//消息接受者
						$arr['msg'] = $reply_context;//消息主体
						$arr['reply_id'] = $data;//对其快速回复的pid
						$arr['addtime'] = $time;
						//动态所有者 收到消息提示
						$this->addRecentMessage($arr);
						

					}
					$this->success( '回复成功');exit();
				}else{
					$this->error( '回复失败');exit();
				}
			}else{
		
				$db = M('AnswerUserRecentReply');
				//验证上级回复是否存在
				$pidData =$db ->where(array('id'=>$pid))->field('recent_id,reply_id')->find();
				if($pidData ['recent_id'] != $recent_id || empty($pidData)){
					$this->jsonUtils->error( '回复参数有错');exit();
				}
				$data ['recent_id'] = $recent_id;
				$data ['reply_id'] = $systemid;
				$data ['reply_content'] = $reply_context;
				$data ['addtime'] = $time;
				$data ['pid'] = $pid;
				$data ['pid_id'] = $pidData['reply_id'];
				$data ['status'] = $status;
				$data = $db ->add($data);
				if($data){
					
					if ($status == 0){
						M('AnswerUserRecent')->where(array('id'=>$recent_id))->setInc('comment_count');
						$arr['belong'] = $auth['system_user_id'];//消息所有者
						$arr['recent_id'] = $recent_id;//动态id
						$arr['recent'] = $auth['content'];//动态内容
						$arr['send_id'] = $systemid;//消息发送者
						$arr['receive_id'] = $pidData['reply_id'];//消息接受者
						$arr['msg'] = $reply_context;//消息主体
						$arr['reply_id'] = $data;//对其快速回复的pid
						$arr['addtime'] = $time;
						
						
						//问题所有者 收到评论消息提示
						$this->addRecentMessage($arr);
						//答案者 收到评价的消息提示
						if($pidData['reply_id'] != $auth['system_user_id']){
							$arr['belong'] = $pidData['reply_id'];//消息所有者
							$this->addRecentMessage($arr);
						}
					}
					$this->success( '回复成功');exit();
				}else{
					$this->error( '回复失败');exit();
				}
		
		
			}
		}else{
			$this->error( '动态不存在');exit();
		}
		
	
		
	}
	/**
	 * 添加动态的消息通知
	 */
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
	 * 处理动态返回排序
	 * @param int $pid
	 * @param int $count
	 */
	protected function dealRecentArray($pid = '0',$count = 1){
		
		$data =$this->data;
		foreach ($data as $key => $value){
			if($value['pid']==$pid){
				$value['count'] = $count;
				$treekey = $this->treekey;
				if($pid ==0){
					// 					$treekey = $treekey +1;
					// 					$this->treekey = $treekey;
					self::$treeList []=$value;
					//self::$treeList [$treekey]['child']=array();
				}else{
					self::$treeList []=$value;
					//self::$treeList [$treekey]['child'][]=$value;
				}
				unset($data[$key]);
				$this->data = $data;
				self::dealRecentArray($value['id'],$count+1);
			}
		}
		return self::$treeList ;
	}

	public function test(){
		//商户给用户报价 jpush
// 		$jid = 12;
// 				//云推送
// 			$jpush = new \App\Model\JpushModel();
// 			$jpush->user = 0;
// 			$jpush ->push(2, array($jid), $data);
// 			//聊天内推送
// 			$xmpp = new \App\Model\XmppApiModel();
// 			$xmpp ->requestPush(2, array($jid), $data);

		//商户	//xmpp
		$jid = array(18);
			//用户发需求
			$jpush = D('Jpush');
			$jpush->user = 2;
			$jpush ->push(1, $jid, $data);
			$xmpp = new \App\Model\XmppApiModel();
			$xmpp ->requestPush(1, $jid, $data);
			//云推送
	}
    
    
}


