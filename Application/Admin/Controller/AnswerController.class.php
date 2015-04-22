<?php


class AnswerController extends CommonController{
	//静态 处理后的数据
	static private $treeList = array();
	//树层级 从1开始
	static private $treekey = 0;
	//缓存每次剩余下数据
	static private $data = array();
	function _initialize() {
		parent::_initialize();
		$cate = M('AnswerCategory')->where(array('status'=>1,'pid'=>0))->select();
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->assign('cate',$cate);
	}

	public function index(){
		$pid = empty($_REQUEST['pid']) ?'':$_REQUEST['pid'];
		$keywords = empty($_REQUEST['keywords']) ?'':$_REQUEST['keywords'];
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		$timeStyle = empty($_REQUEST['timeStyle']) ?'0':$_REQUEST['timeStyle'];
		$url = '/index.php/Admin/Answer/index/';

		if(!empty($pid)){
			$url.='pid/'.$pid.'/';
		}
		if(!empty($page)){
			$url.=C('VAR_PAGE').'/'.$page.'/';
		}
		if(!empty($num)){
			$url.='numPerPage/'.$num.'/';
		}
		if(!empty($keywords)){
			$url.='keywords/'.$keywords.'/';
		}
		if(!empty($timeStyle)){
			$url.='timeStyle/'.$timeStyle;
		}
	
		$db = M('AnswerProblem');
		$map = array();
		$mapdata =array();
		$map['status'] = 0;
		$mapdata['a.status']=0;
		
		if($pid){
			$map['pid']=$pid;
			$mapdata['a.pid']=$pid;
			$this->assign('pid',$pid);
		}
		$mapPage = $map;
		if($keywords){
			$map['title'] = array('like',"%$keywords%");
			$mapdata['a.title'] = array('like',"%$keywords%");
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
		
		$data = $db->table(C("DB_PREFIX")."answer_problem as a ")->field("a.id,a.title,c.name as pidname,a.system_user_id,b.name,a.attention,a.answer_num,a.addtime")
		->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id")->join(C('DB_PREFIX')."answer_category as c on a.pid = c.id")
		->where($mapdata)->limit($num)->page($page)->order('a.addtime desc')->select();
// 		dump($data);
		$this->assign('timeStyle',$timeStyle);
		$this->assign('list', $data);
		$this->assign('furl', base64_encode(urlencode($url)));

	
		$this->display();
	}
	public function add(){
		
		$currentUser = $this->getCurrentUser();
		
		$_SESSION['pics_answer'] =array();
		$this->display();
	}
	function insert() {
		$pid = empty($_REQUEST['pid']) ?'':$_REQUEST['pid'];
		$title = empty($_REQUEST['title']) ?'':$_REQUEST['title'];
		$time = empty($_REQUEST['time']) ?'':$_REQUEST['time'];
		$systemid= $this->getCurrentUser();
		if(!empty($time)){
			$time = strtotime($time);
		}else{
			$time = time();
		}
		if(empty($pid)){
			$this->error('选择分类');
		}
		if(empty($title)){
			$this->error('填写问题');
		}
		$model = M('AnswerProblem');
		$arr = array(
				'pid'=>$pid,
				'title'=>$title,
				'addtime'=>$time,
				'system_user_id'=>$systemid,
				
				);
		$pics = $_SESSION['pics_answer'];
		if(empty($pics)){
			$arr['pics']='[]';
		}else{
			
			$arr['pics'] =json_encode($pics);
		}
		$list = $model->add($arr);
		if ($list !== false) { //保存成功
			$this->success('新增成功!', cookie('_currentUrl_'));
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	public function del(){
		$this->_delete('AnswerProblem');
	}
	function changetext(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$text = isset ( $_POST ['text'] ) ?   $_POST ['text']  : '';
		$imgChange = isset ( $_POST ['imgChange'] ) ?   $_POST ['imgChange']  : '';
		$imgarr = isset ( $_POST ['imgarr'] ) ?   $_POST ['imgarr']  : '';
		$pid = isset ( $_POST ['pid'] ) ?   $_POST ['pid']  : '';
		if(empty($id)||empty($text)||empty($pid)){
			$this->ajaxReturn(array('code'=>1,'msg'=>'参数不全'));
		}
		if($imgChange){
			$imgarr  = array_filter($imgarr);
			if(!empty($imgarr)){
				foreach ($imgarr as $row){
					unset($row['hbsize']);
					unset($row['hdsize']);
					unset($row['hssize']);
					$row['hs'] = str_replace(C('ROOT_UPLOADS'), '', $row['hs']);
					$row['hd'] = str_replace(C('ROOT_UPLOADS'), '', $row['hd']);
					$row['hb'] = str_replace(C('ROOT_UPLOADS'), '', $row['hb']);
					$img[] = $row;
				}
			}else{
				$img = array();
			}
			$save ['pics'] = json_encode($img);
		}
		$save['pid'] = $pid;
		$save['title'] = $text;
		$db = M('AnswerProblem');
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
		$db = M('AnswerReply');
		$data = $db ->where(array('id'=>$id))->save(array('reply_content'=>$text));
		if($data){
			$this->ajaxReturn(array('code'=>0,'msg'=>'ok','data'=>$text));
		}else{
			$this->ajaxReturn(array('code'=>1,'msg'=>'修改失败'));
		}
	
	}
	public function detail(){
	//	dump($_GET);
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		$furlen = isset ( $_GET ['furl'] ) ?   $_GET ['furl']  : '';
		$furl=urldecode (base64_decode($furlen));
//  	dump($furl);
		$currentUser = $this->getCurrentUser();
		$data  = $this->getQuestionDetail($id);
// 		dump($data);
		$this->assign('furlen',$furlen);
		$this->assign('furl',$furl);
		$this->assign('data',$data);
		$this->display();
	}
	public function delete_reply(){
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if(empty($id)){
			$this->ajaxReturn(array('code'=>1,'msg'=>'参数不全'));
		}
		$db = M('AnswerReply');
		$data = $db ->where(array('id'=>$id))->save(array('status'=>-1));
		if($data ===false){
			$this->ajaxReturn(array('code'=>1,'msg'=>'删除失败'));
			
		}else{
// 			$isuue = $db ->where(array('id'=>$id))->field('issue_id')->find();
// 			M('AnswerProblem')
			$this->ajaxReturn(array('code'=>0,'msg'=>'删除成功',));
		}
		
	}
	/**
	 * 回答问题
	 */
	public function replyToAnswer(){
		$id = $_REQUEST['id'];
		$submiter = $_REQUEST['submiter'];
		$pid = isset($_REQUEST['pid'])?htmlspecialchars($_REQUEST['pid']):0;
		$systemid = $this->getCurrentUser();
		if($systemid ==$submiter){
			$note = true;
		}else{
			$note = false;
		}
		$this->assign('note',$note);
		$this->assign('pid',$pid);
		$this->assign('id',$id);
		$this->display();
	}
	/**
	 * 回答actioon
	 */
	public function replyToAction(){
		$pid = isset($_POST['pid'])?htmlspecialchars($_POST['pid']):'0';
		$issue_id = isset($_POST['issue_id'])?htmlspecialchars($_POST['issue_id']):'';
		$reply_context = isset($_POST['context'])?htmlspecialchars($_POST['context']):'';
		$systemid = $this->getCurrentUser();
		$time = empty($_REQUEST['time']) ?'':$_REQUEST['time'];
		if(!empty($time)){
			$time = strtotime($time);
		}else{
			$time = time();
		}
		if(empty($systemid)){
			$this->error('请选择用户');exit();
		}
		if(empty($issue_id)){
			$this->error('回答id为空');
			exit();
		}
		
		if(empty($reply_context)){
			$this->error('回答内容为空');
			exit();
		}
		
		
		$auth = M('AnswerProblem')->where(array('id'=>$issue_id))->find();
		if($auth){
			$now = time();
			if($time<=$now){
				//找到所有关注
				$atten = M('AnswerAttention');
				$attention = $atten ->where(array('issue_id'=>$issue_id))->field('system_user_id as belong ')->select();
				if(empty($pid) ){
					//顶层回复
					$data ['issue_id'] = $issue_id;
					$data ['reply_id'] = $systemid;
					$data ['reply_content'] = $reply_context;
					$data ['addtime'] = $time;
					$data ['pid'] = 0;
					$data ['pid_id'] = 0;
						
					$db = M('AnswerReply');
					$is = $db ->where(array('issue_id'=>$issue_id,'reply_id'=>$systemid,'pid'=>0,'status'=> array(array('eq',0),array('eq',1), 'or')))->getField('id');
					if(!$is){
						$data = $db ->add($data);
						M('AnswerProblem')->where(array('id'=>$issue_id))->setInc('answer_num');
						if($attention){
							foreach ($attention as $key =>$row){
								$this->addMessage($row['belong'],$issue_id,$auth['title'],3, $systemid, $auth['system_user_id'],$reply_context,$data);
							}
								
						}
						//问题所有者 收到回答消息提示
						$this->addMessage($auth['system_user_id'],$issue_id,$auth['title'],1, $systemid, $auth['system_user_id'],$reply_context,$data);
						$this->success( '回复成功');exit();
					}else{
						$this->success( '你已回复过该消息');exit();
					}
				}else{
			
					$db = M('AnswerReply');
					//验证上级回复是否存在
					$pidData =$db ->where(array('id'=>$pid))->field('issue_id,reply_id')->find();
					if($pidData ['issue_id'] != $issue_id || empty($pidData)){
						$this->error('回复参数有错');exit();
					}
					$data ['issue_id'] = $issue_id;
					$data ['reply_id'] = $systemid;
					$data ['reply_content'] = $reply_context;
					$data ['addtime'] = $time;
					$data ['pid'] = $pid;
					$data ['pid_id'] = $pidData['reply_id'];
					$data = $db ->add($data);
					if($data){
							
						if($attention){
							foreach ($attention as $key =>$row){
								$this->addMessage($row['belong'],$issue_id,$auth['title'],4, $systemid, $pidData['reply_id'],$reply_context,$data);
							}
			
						}
						//问题所有者 收到评论消息提示
						$this->addMessage($auth['system_user_id'],$issue_id,$auth['title'],2, $systemid, $pidData['reply_id'],$reply_context,$data);
						//答案者 收到评价的消息提示
						$this->addMessage($pidData['reply_id'],$issue_id,$auth['title'],2, $systemid, $pidData['reply_id'],$reply_context,$data);
						$this->success( '回复成功');exit();
					}else{
						$this->success( '回复失败');exit();
					}
			
			
				}
			}else{
				//定时发布处理
				if(empty($pid) ){
					//顶层回复
					$data ['issue_id'] = $issue_id;
					$data ['reply_id'] = $systemid;
					$data ['reply_content'] = $reply_context;
					$data ['addtime'] = $time;
					$data ['pid'] = 0;
					$data ['pid_id'] = 0;
					$data ['status'] = 1;
					$db = M('AnswerReply');
					$is = $db ->where(array('issue_id'=>$issue_id,'reply_id'=>$systemid,'pid'=>0,'status'=> array(array('eq',0),array('eq',1), 'or')))->getField('id');
					if(!$is){
						$data = $db ->add($data);
					//	M('AnswerProblem')->where(array('id'=>$issue_id))->setInc('answer_num');
// 						if($attention){
// 							foreach ($attention as $key =>$row){
// 								$this->addMessage($row['belong'],$issue_id,$auth['title'],3, $systemid, $auth['system_user_id'],$reply_context,$data);
// 							}
				
// 						}
						//问题所有者 收到回答消息提示
// 						$this->addMessage($auth['system_user_id'],$issue_id,$auth['title'],1, $systemid, $auth['system_user_id'],$reply_context,$data);
						$this->success( '回复成功');exit();
					}else{
						$this->success( '你已回复过该消息');exit();
					}
				}else{
						
					$db = M('AnswerReply');
					//验证上级回复是否存在
					$pidData =$db ->where(array('id'=>$pid))->field('issue_id,reply_id')->find();
					if($pidData ['issue_id'] != $issue_id || empty($pidData)){
						$this->error('回复参数有错');exit();
					}
					$data ['issue_id'] = $issue_id;
					$data ['reply_id'] = $systemid;
					$data ['reply_content'] = $reply_context;
					$data ['addtime'] = $time;
					$data ['pid'] = $pid;
					$data ['pid_id'] = $pidData['reply_id'];
					$data ['status'] = 1;//定时任务
					$data = $db ->add($data);
					if($data){
							
// 						if($attention){
// 							foreach ($attention as $key =>$row){
// 								$this->addMessage($row['belong'],$issue_id,$auth['title'],4, $systemid, $pidData['reply_id'],$reply_context,$data);
// 							}
								
// 						}
// 						//问题所有者 收到评论消息提示
// 						$this->addMessage($auth['system_user_id'],$issue_id,$auth['title'],2, $systemid, $pidData['reply_id'],$reply_context,$data);
// 						//答案者 收到评价的消息提示
// 						$this->addMessage($pidData['reply_id'],$issue_id,$auth['title'],2, $systemid, $pidData['reply_id'],$reply_context,$data);
						$this->success( '回复成功');exit();
					}else{
						$this->success( '回复失败');exit();
					}
						
						
				}
					
				
				
			}
		}else{
			$this->error( '问题不存在');exit();
		}
		
		
	}
	/**
	 * 编辑回答
	 */
	public function replyToEditAnswer(){
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if(empty($this->getCurrentUser())){
			$this->error('请选择马甲');exit();
		}
		$db = M('AnswerReply');
		$data = $db ->where(array('id'=>$id))->find();
		if($data['reply_id']== $_SESSION['currentUser']['id']){
// 			dump($data);
			$this->assign('data',$data);
		}else{
			$this->error('该回复不属于此时登录用户');exit();
		}
		$this->display();
	}
	/**
	 * 编辑回答action
	 */
	public function replyToEditAction(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$reply_content = isset($_POST['reply_content'])?htmlspecialchars($_POST['reply_content']):'';
		$systemid = $this->getCurrentUser();
		if(empty($systemid)){
			$this->error('选择用户');exit();
		}
		$db= M('AnswerReply');
		$data = $db ->where(array('id'=>$id,'reply_id'=>$systemid))->getField('id');
		if($data){
			$rel = $db ->where(array('id'=>$id))->save(array('reply_content'=>$reply_content));
			if($rel){
// 				$this->ajaxReturn(array('status'=>200,'info'=>'修改成功'),'json');exit();
				$this->success('ok');
			}else{
				$this->error( '修改失败');exit();
			}
		
		}else{
			$this->error( '你无权操作');exit();
		}
		
	}
	
	public function clickLuad(){
	

		$systemid =$this->getCurrentUser();
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';

		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '回答id为空');
			exit();
		}
	
		$db = M('AnswerReply');
		$init = $db ->where(array('id'=>$id))->field('reply_id,pid')->find();
		if($init['pid'] == 0){
			$laudDb = M('AnswerLaud');
			$data = $laudDb->where(array('answer_reply_id'=>$id,'system_user_id'=>$systemid))->find();
			if($data){
				if($data['status'] == 0){
					$rel = $laudDb->where(array('id'=>$data['id']))->save(array('status'=>1));
					if($rel){
						$db->where(array('id'=>$id))->setInc('laud_count');
						//更新个人信息 点赞量
						$dbUser = M('AnswerUser');
						$dbUser->where(array('system_user_id'=>$init['reply_id']))->setInc('laud_count');
						$num = $db->where(array('id'=>$id))->getField('laud_count');
						$this->jsonUtils->echo_json_data(0, '赞成功',array('laud_count'=>$num));exit();
					}else{
						$this->jsonUtils->echo_json_msg(4, '赞失败');exit();
					}
				}else{
					$rel = $laudDb->where(array('id'=>$data['id']))->save(array('status'=>0));
					if($rel){
						$db->where(array('id'=>$id))->setDec('laud_count');
						//更新个人信息 点赞量
						$dbUser = M('AnswerUser');
						$dbUser->where(array('system_user_id'=>$init['reply_id']))->setDec('laud_count');
						$num = $db->where(array('id'=>$id))->getField('laud_count');
						$this->jsonUtils->echo_json_data(0, '取消赞成功',array('laud_count'=>$num));exit();
					}else{
						$this->jsonUtils->echo_json_msg(4, '取消赞失败');exit();
					}
				}
				
			}else{
				$rel = $laudDb->add(array('answer_reply_id'=>$id,'system_user_id'=>$systemid,'status'=>1));
				if($rel){
					$db->where(array('id'=>$id))->setInc('laud_count');
					//更新个人信息 点赞量
					$dbUser = M('AnswerUser');
					$dbUser->where(array('system_user_id'=>$init['reply_id']))->setInc('laud_count');
					$num = $db->where(array('id'=>$id))->getField('laud_count');
					$this->jsonUtils->echo_json_data(0, '赞成功',array('laud_count'=>$num));exit();
						
				}else{
					$this->jsonUtils->echo_json_msg(4, '赞失败');exit();
				}
				
			}
		}else{
			$this->jsonUtils->echo_json_msg(4, '不允许点赞');exit();
		}
	
	}
	public function collect(){
// 		$member_session_id = $_POST ['session_id'];
// 		$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
		$obj_id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$type = isset ( $_POST ['type'] ) ? htmlspecialchars ( $_POST ['type'] ) : '1';
		if (empty ( $obj_id ) || $obj_id == null) {
			$this->jsonUtils->echo_json_msg ( 1, '收藏id为空' );
			exit ();
		}
		if (! in_array ( $type, array (
				1,
				3,4,
		) )) {
			$this->jsonUtils->echo_json_msg ( 1, 'type错误' );
			exit ();
		}
		if($type == 1 ){
// 			$hostid = $member_id['id'];
			$this->error('赞不支持用户收藏商家');
		}elseif($type ==3 || $type ==4){
			$reply_id = M('AnswerReply')->where(array('id'=>$obj_id))->getField('reply_id');
			if(empty($reply_id)){
				$this->jsonUtils->echo_json_msg(4, '收藏的id不存在');exit();
			}
			$systemid = $this->getCurrentUser();
			$hostid = $systemid;
		}else{
			$this->jsonUtils->echo_json_msg ( 1, 'type错误' );
			exit ();
		}
		$data ['member_id'] = $hostid;
		$data ['obj_id'] = $obj_id;
		// $data['type']=$_POST['type'];
		$collect = M ( 'collect' );
		$arr = $collect->where ( "obj_id=$obj_id and member_id = $hostid and type=$type" )->find ();
		if ($arr) {
			$result = $collect->delete($arr['id']);
			if ($type == 1) {
				M ( 'Merchant' )->where ( array (
				'id' => $obj_id
				) )->setDec ( 'collect_count' );
			} elseif ($type == 3 ||$type ==4) {
					
				M ( 'AnswerReply' )->where ( array (
				'id' => $obj_id
				) )->setDec ( 'collect_count' );
				$collect_count = M ( 'AnswerReply' )->where ( array (
				'id' => $obj_id
				) )->getField('collect_count');
				//更新个人信息 收藏量
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$reply_id))->setDec('collect_count');
			}
			
			
			if ($result) {
				$this->jsonUtils->echo_json_data ( 0, '取消收藏成功',array('collect_count'=>$collect_count) );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '取消收藏失败！' );
				exit ();
			}
		}else{
			$data ['type'] = $type;
			$data ['addtime'] = time ();
			$collect = M ( 'collect' );
			$result = $collect->add ( $data );
			if ($type == 1) {
				M ( 'Merchant' )->where ( array (
						'id' => $obj_id 
				) )->setInc ( 'collect_count' );
			} elseif ($type == 3 ||$type ==4) {
			
				M ( 'AnswerReply' )->where ( array (
						'id' => $obj_id 
				) )->setInc ( 'collect_count' );
				//更新个人信息 收藏量
				$collect_count = M ( 'AnswerReply' )->where ( array (
						'id' => $obj_id
				) )->getField('collect_count');
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$reply_id))->setInc('collect_count');
			}
			if ($result) {
				$this->jsonUtils->echo_json_data ( 0, '收藏成功',array('collect_count'=>$collect_count)  );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '收藏失败！' );
				exit ();
			}
			
		}
		
		
		
	
	
	}
	public function attend(){
		$issue_id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
	
		$systemid = $this->getCurrentUser();
		$db  = M('AnswerAttention');
		$add ['system_user_id'] = $systemid;
		$add ['issue_id'] = $issue_id;
		if($del=$db->where($add)->getField('id')){
			$data =$db ->delete($del);
			if($data){
				M('AnswerProblem')->where(array('id'=>$issue_id))->setDec('attention');
				$count = M('AnswerProblem')->where(array('id'=>$issue_id))->getField('attention');
				$this->jsonUtils->echo_json_data(0, '取消关注成功', array('count'=>$count));exit();
				
			}else{
				$this->jsonUtils->echo_json_msg(4, '取消关注失败');exit();
			}
		
		}else{
			$data =$db ->add($add);
			if($data){
				M('AnswerProblem')->where(array('id'=>$issue_id))->setInc('attention');
				$count = M('AnswerProblem')->where(array('id'=>$issue_id))->getField('attention');
				$this->jsonUtils->echo_json_data(0, '关注成功', array('count'=>$count));exit();
			}else{
				$this->jsonUtils->echo_json_msg(4, '关注失败');exit();
			}
		}
		
	}
	
	/**
	 *  提醒消息
	 * @param int $belong  属 于谁的消息
	 * @param int $issue_id  问题
	 * @param string $issue_title 问题名
	 * @param int $type 信息来源的发式  1 回答 2评价 3 回答（关注） 4 评价（关注）
	 * @param int $send_id 发送者
	 * @param int $receive_id 接受者
	 * @param string $msg 消息主体
	 * @param int $reply_id  对其快速回复的pid
	 * @return bool 正确 错误
	 */
	static public function addMessage($belong,$issue_id,$issue_title,$type,$send_id,$receive_id,$msg,$reply_id){
		$db = M('AnswerMessage');
		$add ['msg'] = $msg;
		$add ['send_id'] = $send_id;
		$add ['receive_id'] = $receive_id;
		$add ['type'] = $type;
		$add ['reply_id'] = $reply_id;
		$add ['is_read'] = 0;
		$id = $db ->where(array('belong'=>$belong,'issue_id'=>$issue_id))->field('id')->find();
		if($id){
			$data = $db->where(array('id'=>$id['id'])) ->save($add);
		}else{
				
			$add ['issue_id'] = $issue_id;
			$add ['issue'] = $issue_title;
			$add ['belong'] = $belong;
			$data = $db ->add($add);
		}
	
		if($data === false){
			$this->jsonUtils->echo_json_msg(4, '加入提醒出错');exit();
		}else{
			return $data;
		}
	
	}
	
	
	
	/**
	 * 获取问答详情 读取所有回答数据
	 *
	 */
	public function getQuestionDetail($id){
		
		$systemid =$_SESSION['currentUser']['id'];
		if(empty($id)){
		
			$this->error('问题id为空');exit();
			
		}
		$db = M ('AnswerProblem');
		$problem = $db ->table(C('DB_PREFIX')."answer_problem as a ")
		->field('a.id as issue_id,a.title,a.pics,a.pid,a.addtime,a.attention,a.system_user_id,b.name,b.header,c.name as category_name')
		->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id ")->
		join(C('Db_PREFIX')."answer_category as c on a.pid = c.id")
		->where(array('a.id'=>$id))->find();
		
		$problem ['header'] = imgUrl($problem['header']);
		$problem ['pics'] = imgUrl(json_decode($problem['pics'],true));
		$problem ['addtime'] = date('Y-m-d H:i:s',$problem['addtime']);
	
		$is_attention = M('AnswerAttention') ->where(array('issue_id'=>$id,'system_user_id'=>$systemid))->getField('id');
		$problem ['is_attention'] = empty($is_attention)?'0':'1';
		$problem ['is_answered'] = '0';
		$problem ['edit_answer'] = '';
		$data = $this->getQuestionAnswer($id);
		//检测是否已有回答
		foreach ($data as $key =>$row){
			if($row['system_user_id'] == $systemid){
				$problem ['is_answered'] = '1';
				$problem ['edit_answer'] = $row['id'];
				break;
			}
		}
		$problem['list'] = $data;
		//dump($id);
		//查询该问题下的回答
		if($problem){
			return $problem;
			exit();
		}else{
		
			$this->error('获取失败');
			exit();
		}
	
	
	
	}
	
	/**
	 * 获取所有的回答 并进行排序重置
	 * @param int $issueId
	 * @param int $num
	 * @param int $page
	 */
	protected function getQuestionAnswer($issueId){
		$ansDb = M('');
		$data = $ansDb->table(C('DB_PREFIX')."answer_reply as a")->field("a.laud_count,a.collect_count,a.issue_id,a.id,a.reply_id as system_user_id,b.name,b.header,a.reply_content,a.addtime,a.pid,c.name as pidname")
		->join(C('DB_PREFIX')."system_user as b on a.reply_id = b.id")->join(C('DB_PREFIX')."system_user as c on c.id =a.pid_id",'left')
		->where(array('a.issue_id'=>$issueId,'a.status'=>array(array('eq',0),array('eq',1),'or')))->order('a.id asc')->select();
		if($data){
			foreach ($data as $key =>$row){
				$data[$key]['addtime'] = $this->dealAlltime($row['addtime']);
				$data[$key]['pidname'] = empty($row['pidname'])?'':$row['pidname'];
				$data[$key]['header'] = imgUrl($row['header']);
			}
			$this->data = $data;
			
			$data = $this->dealAnswerArray();
			//排序
// 			dump($data);
			rsort($data);
		}else{
			$data = array();
		}
		return $data;
	}
	/**
	 * 深度优先遍历
	 * @param int $pid
	 * @param int $count
	 */
	protected function dealAnswerArray($pid = '0',$count = 1){
		$data =$this->data;
		foreach ($data as $key => $value){
			if($value['pid']==$pid){
				$value['count'] = $count;
				$treekey = $this->treekey;
				if($pid ==0){
					$treekey = $treekey +1;
					$this->treekey = $treekey;
					self::$treeList [$treekey]=$value;
					self::$treeList [$treekey]['child']=array();
				}else{
					self::$treeList [$treekey]['child'][]=$value;
				}
				unset($data[$key]);
				$this->data = $data;
				self::dealAnswerArray($value['id'],$count+1);
			}
		}
		return self::$treeList ;
	}


	function dealAlltime($Btime){
		$time = time() - $Btime;
		if($time <0){
			$rel = date('Y-m-d H:i:s',$Btime);
		}elseif($time <60){
			$data = '秒前';
			$rel = $time.$data;
		}elseif($time<3600){
			$time = ceil($time/60);
			$data = '分钟前';
			$rel = $time.$data;
		}elseif($time < 86400){
			$time = floor($time/3600);
			$data = '小时前';
			$rel = $time.$data;
		}elseif($time >= 86400){
			$rel = date('Y-m-d',$Btime);
		}
		return $rel;
	}
	
	
	
	
	
    
}


