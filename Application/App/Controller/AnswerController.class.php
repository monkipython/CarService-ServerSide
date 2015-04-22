<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
use Think\Log;

/**
 * 问答模块
 * 动态模块
 * 
 */
class AnswerController extends Controller{
	//静态 处理后的数据
	static private $treeList = array();
	//树层级 从1开始
	static private $treekey = 0;
	//缓存每次剩余下数据
	static private $data = array();
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	
	 function _initialize(){
		$ver = !empty($_POST['ver'])?htmlspecialchars($_POST['ver']):'';
		//ver 为空 则不路由，即用原始版本接口
		if(in_array($ver, C('VER_ARRAY'))){
			//在使用版本内 执行路由 反之原始版本接口
			$functionName = ACTION_NAME.$ver;
			if(method_exists($this, $functionName)){
				$this->$functionName();
				die();
			}
			
		}
	}
	
	public function __construct(){
	
		$this->jsonUtils = new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
		parent::__construct();
	}
	/**
	 * 问答分类列表 一次性返回
	 * @param null
	 * @return JsonFormatter
	 */
	public function getAnswerCategory(){
		$db = M('AnswerCategory');
		$data = $db ->where(array('status'=>1))->field('id,name')->select();
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	}
	/**
	 * 问答列表-获取最新的问题列表 （大于当前时间）
	 * 时间倒序
	 * 分页
	 * @param pid page num
	 * @return JsonFormatter
	 */
	public function getAnswerList(){
		$pid = isset ( $_POST ['pid'] ) ? (int)htmlspecialchars ( $_POST ['pid'] ) : 0; 
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		if(!empty($pid)){
			$map ['a.pid'] = $pid; 
			$map ['a.status'] = 0;
			$map ['a.addtime'] = array('lt',time());
			$where = "status=0 and pid=".$pid." and addtime < ".time() ;
		}else{
			$map = "a.status = 0  and a.addtime < ".time();
			$where = "status = 0  and addtime < ".time();
		}
		$answer = M('');
		$prefix = C('DB_PREFIX');
		$data = $answer->table($prefix."answer_problem as a ")->field('a.id,a.system_user_id,b.header,b.name,a.title,a.pid,a.addtime,a.pics,a.answer_num,c.name as pidname ,b.type as is_mer')
		->join ($prefix."system_user as b on a.system_user_id = b.id")->join($prefix.'answer_category as c on a.pid = c.id')
		->where($map)->order('a.addtime desc')->limit($num)->page($page)->select();
		$count = M('AnswerProblem')->where($where)->count();
		if(!$data){
			$data = array();
		}else{
			foreach ($data as $key =>$row){
				if($row['is_mer'] == 2){
					$data[$key]['is_mer'] = '1';
				}
				$data[$key]['pics'] = imgUrl(json_decode($row['pics'],true));
				$data[$key]['header'] = imgUrl($row['header']);
				$data[$key]['addtime'] = dealtime($row['addtime']);
			//	$data[$key]['content'] = substr($row['content'],0,200);
				unset($data[$key]['pid']); 
			}
		}
		$arr['count'] = $count;
		$arr['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);
		exit();
	} 
	/**
	 * 模糊搜索--搜索问题 %keyword%
	 * 
	 */
	public function search(){
		$keyword = isset ( $_POST ['keyword'] ) ? htmlspecialchars ( $_POST ['keyword'] ) : ''; 
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		if(empty($keyword)||$keyword == 'null' || $keyword == ' '){
			$this->jsonUtils->echo_json_msg(4, '搜索关键词为空');
			exit();
		}
		$arr = array();
		if (stristr($_REQUEST['keyword'], ' AND ') !== false)
		{
			/* 检查关键字中是否有AND，如果存在就是并 */
			$arr        = explode('AND', $_REQUEST['keyword']);
			$operator   = " AND ";
		}
		elseif (stristr($_REQUEST['keyword'], ' OR ') !== false)
		{
			/* 检查关键字中是否有OR，如果存在就是或 */
			$arr        = explode('OR', $_REQUEST['keyword']);
			$operator   = " OR ";
		}
		elseif (stristr($_REQUEST['keyword'], ' + ') !== false)
		{
			/* 检查关键字中是否有加号，如果存在就是或 */
			$arr        = explode('+', $_REQUEST['keyword']);
			$operator   = " OR ";
		}
		else
		{
			/* 检查关键字中是否有空格，如果存在就是并 */
			$arr        = explode(' ', $_REQUEST['keyword']);
			$operator   = " AND ";
		}
		
		$arr = array_filter($arr);
		$keywords = '';
		foreach ($arr as $key => $val)
		{
			$val = trim($val);
			$keywords .="( a.title LIKE '%$val%'  )";
			$keywords_count .="( title LIKE '%$val%'  )";
			if ( $key < count($arr) && count($arr) > 1 && $key !== count($arr)-1)
			{
				$keywords .= $operator;
				$keywords_count .= $operator;
			}
		}
		
		$map = " $keywords and a.addtime <".time();
		$db = M('AnswerProblem');
		$count = $db ->where("$keywords_count and addtime <".time())->count();
		//$data = $db ->where($map)->field('id,title,attention,answer_num')->page($page)->limit($num)->select();
		$data = $db->table(C("DB_PREFIX")."answer_problem as a ") ->field('a.id,a.title,a.attention,a.answer_num,a.addtime,b.header,b.name,c.name as pidname,b.id as system_user_id')
		->join(C("DB_PREFIX")."system_user as b on b.id = a.system_user_id") ->
		join(C("DB_PREFIX")."answer_category as c on c.id = a.pid")
		->where($map)->page($page)->order('a.addtime desc')->limit($num)->select();
		if($data){
			foreach ($data as $key=>$row){
				$data[$key]['last_answer'] = $this->getLastReply($row['id']);
				$data[$key]['header'] = imgUrl($row['header']);
				$data[$key]['addtime'] = dealtime($row['addtime']);
			}
		}else{
			$data = array();
		}
		$array['list'] = $data;
		$array['count'] = !empty($count)?$count:'0';
		$this->jsonUtils->echo_json_data(0, 'ok', $array);
		exit();
	}
	
	/**
	 * 搜索列表里最近一条回答
	 * 获取赞数最多一条，时间排序
	 */
	protected function getLastReply($issue_id){
		$db = M('AnswerReply');
		$data = $db->table(C('DB_PREFIX')."answer_reply as a ")->field('b.name,b.header,a.addtime,a.laud_count,a.reply_content')
		->join(C('DB_PREFIX')."system_user as b on a.reply_id = b.id")
		->where(array('a.issue_id'=>$issue_id,'a.pid'=>0))->order('laud_count desc,addtime desc')->find();
		if(!$data){
			$data =new \stdClass();
		}else{
			$data ['header'] = imgUrl($data['header']);
			$data ['addtime'] = date('Y-m-d',$data['addtime']);
		}
		return $data;
		
	}
	/**
	 * 添加问题
	 */
	public function addQuestion(){
		$session_id = $_POST ['session_id'] ;
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$pid = isset ( $_POST ['pid'] ) ? htmlspecialchars ( $_POST ['pid'] ) : '';
		$title = isset ( $_POST ['title'] ) ? htmlspecialchars ( $_POST ['title'] ) : '';
		$this->staticAddQuestion($session_id, $pid, $title);
	}
	
	/**
	 * 添加问题
	 * action
	 */
	public function staticAddQuestion($session_id,$pid,$title){
		
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$call = "App\\Controller\\CommonController";
		$systemid = call_user_func_array(array($call, 'getSystemUserid'), array($member_id['id'],$member_id['type']));
		if(empty($pid)){
			$this->jsonUtils->echo_json_msg(4, '分类为空');
			exit();
		}
		if(empty($title)){
			$this->jsonUtils->echo_json_msg(4, '问题为空');
			exit();
		}
	
		$data ['system_user_id'] = $systemid;
		$data ['title'] = $title;
		$data ['pid'] = $pid;
		$data ['addtime'] = time();
		if ($_FILES) {
			$arr = mul_upload ( '/Answer/' ,1);
			if ($arr) {
				$data ['pics'] = json_encode ( $arr ); // 把多张图片数组格式转json保存数据库
			}
		}else{
			$data ['pics']="[]";
		}
		$db = M('AnswerProblem');
		$data =$db ->add($data);
		if($data){
			$this->jsonUtils->echo_json_msg(0, 'ok');exit();
		}else{
			$this->jsonUtils->echo_json_msg(4, '失败');exit();
		}
	}
	
	
	/**
	 * 对问题进行关注 or 取消关注
	 * delete add
	 * 对对应问题setDec setInc
	 */
	public function attentionQuestion(){
		$issue_id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
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
	 * 获取回答
	 * 只允许一个用户回答一次问题，再次回答 则编辑回答
	 */
	public function getQuestionReply(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$db= M('AnswerReply');
		$data = $db ->where(array('id'=>$id,'reply_id'=>$systemid))->getField('reply_content');
		if($data){
			$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
		}else{
			$this->jsonUtils->echo_json_msg(4, '你无权操作');exit();
		}
	}
	/**
	 * 编辑回答  提交修改
	 */
	public function editQuestionReply(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$reply_content = isset($_POST['reply_content'])?htmlspecialchars($_POST['reply_content']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$db= M('AnswerReply');
		$data = $db ->where(array('id'=>$id,'reply_id'=>$systemid))->getField('reply_content');
		if($data){
			$rel = $db ->where(array('id'=>$id))->save(array('reply_content'=>$reply_content));
			if($rel){
				$this->jsonUtils->echo_json_data(0, 'ok', array());exit();
			}else{
				$this->jsonUtils->echo_json_msg(4, '修改失败');exit();
			}
		
		}else{
			$this->jsonUtils->echo_json_msg(4, '你无权操作');exit();
		}
	}
	/**
	 * 获取问答详情 读取所有回答数据
	 * 
	 */
	public function getQuestionDetail(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '问答id为空');
			exit();
		}
		$db = M ('AnswerProblem');
		$problem = $db ->table(C('DB_PREFIX')."answer_problem as a ")
		->field('a.id as issue_id,a.title,a.pics,a.addtime,a.attention,a.system_user_id,b.name,b.header,c.name as category_name')
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
			$this->jsonUtils->echo_json_data(0, 'ok', $problem);
			exit();
		}else{
			$this->jsonUtils->echo_json_msg(0, '获取失败');
			exit();
		}
		
		
		
	}
	
	/**
	 * 获取问答详情 读取所有回答数据（无状态）
	 * 不需登录即可使用，没有是否已回答，是否有关注的状态
	 */
	public function getQuestionDetailNoStatus(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';

		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '问答id为空');
			exit();
		}
		$db = M ('AnswerProblem');
		$problem = $db ->table(C('DB_PREFIX')."answer_problem as a ")
		->field('a.id as issue_id,a.title,a.pics,a.addtime,a.attention,a.system_user_id,b.name,b.header,c.name as category_name')
		->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id ")->
		join(C('Db_PREFIX')."answer_category as c on a.pid = c.id")
		->where(array('a.id'=>$id))->find();
		$problem ['header'] = imgUrl($problem['header']);
		$problem ['pics'] = imgUrl(json_decode($problem['pics'],true));
		$problem ['addtime'] = date('Y-m-d H:i:s',$problem['addtime']);
	
		$data = $this->getQuestionAnswer($id);
		$problem['list'] = $data;
		//dump($id);
		//查询该问题下的回答
		if($problem){
			$this->jsonUtils->echo_json_data(0, 'ok', $problem);
			exit();
		}else{
			$this->jsonUtils->echo_json_msg(0, '获取失败');
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
		->where(array('a.issue_id'=>$issueId,'a.status'=>0,'a.addtime'=>array('lt',time())))->order('a.id asc')->select();
		if($data){
			foreach ($data as $key =>$row){
				$data[$key]['addtime'] = dealtime($row['addtime']);
				$data[$key]['pidname'] = empty($row['pidname'])?'':$row['pidname'];
				$data[$key]['header'] = imgUrl($row['header']);
			}
			$this->data = $data;
			$data = $this->dealAnswerArray();
			//排序
			rsort($data);
		}else{
			$data = array();
		}
		return $data;
	}
	/**
	 * 深度优先遍历 进行排序
	 * 	--根据pid 进行确认排序
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

	/**
	 * 获取问答详情信息 一条
	 * 获取单条回答详情
	 */
	public function dealOneAnswerData(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$db = M('AnswerReply');
		$issue= $db ->where(array('id'=>$id))->getField('issue_id');
		$this->data = array();
		$data = $this->getQuestionAnswer($issue);
		foreach ($data as $key=>$row){
			$arr[$row['id']] = $row;
		}
		$rel = $arr[$id];
		$is_laud = M('AnswerLaud')->where(array('system_user_id'=>$systemid,'answer_reply_id'=>$id,'status'=>1))->getField('id');
		if($is_laud){
			$rel['is_laud'] = '1';
		}else{
			$rel['is_laud'] = '0';
		}
		$is_collect = M ('Collect')->where(array('member_id'=>$systemid,'obj_id'=>$id))->getField('id');
		if($is_collect){
			$rel['is_collect'] = '1';
		}else{
			$rel['is_collect'] = '0';
		}
		if($rel){

			$this->jsonUtils->echo_json_data(0, 'ok', $rel);exit();
		}else{
			$this->jsonUtils->echo_json_msg(4, '该回答不存在');exit();
		}
		
	}
	/**
	 * 点赞 or 取消赞
	 */
	public function clickLuad(){
		
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
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
	/**
	 * 回复问题或者回复某人
	 *  pid =0 回答顶层
	 */
	public function replyToSomeone(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$pid = isset ( $_POST ['pid'] ) ? htmlspecialchars ( $_POST ['pid'] ) : '';
		$issue_id = isset ( $_POST ['issue_id'] ) ? htmlspecialchars ( $_POST ['issue_id'] ) : '';
		$reply_context = isset ( $_POST ['reply_content'] ) ? htmlspecialchars ( $_POST ['reply_content'] ) : '';
		
		if(empty($issue_id)){
			$this->jsonUtils->echo_json_msg(4, '回答id为空');
			exit();
		}

		if(empty($reply_context)){
			$this->jsonUtils->echo_json_msg(4, '回答内容为空');
			exit();
		}
		$auth = M('AnswerProblem')->where(array('id'=>$issue_id))->find();
		if($auth){
			//找到所有关注
			$atten = M('AnswerAttention');
			$attention = $atten ->where(array('issue_id'=>$issue_id))->field('system_user_id as belong ')->select();
			if(empty($pid) ){
				//顶层回复
					$data ['issue_id'] = $issue_id;
					$data ['reply_id'] = $systemid;
					$data ['reply_content'] = $reply_context;
					$data ['addtime'] = time();
					$data ['pid'] = 0;
					$data ['pid_id'] = 0;
					
					$db = M('AnswerReply');
					$is = $db ->where(array('issue_id'=>$issue_id,'reply_id'=>$systemid,'pid'=>0,'status'=>0))->getField('id');
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
						$this->jsonUtils->echo_json_data(0, '回复成功',array('id'=>$data));exit();
					}else{
						$this->jsonUtils->echo_json_msg(4, '你已回复过该消息');exit();
					}
			}else{
				
				$db = M('AnswerReply');
				//验证上级回复是否存在
				$pidData =$db ->where(array('id'=>$pid))->field('issue_id,reply_id')->find();
				if($pidData ['issue_id'] != $issue_id || empty($pidData)){
					$this->jsonUtils->echo_json_msg(4, '回复参数有错');exit();
				}
				$data ['issue_id'] = $issue_id;
				$data ['reply_id'] = $systemid;
				$data ['reply_content'] = $reply_context;
				$data ['addtime'] = time();
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
					$this->jsonUtils->echo_json_data(0, '回复成功',array('id'=>$data));exit();
				}else{
					$this->jsonUtils->echo_json_msg(4, '回复失败');exit();
				}
				
				
			}
		}else{
			$this->jsonUtils->echo_json_msg(4, '问题不存在');exit();
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
	protected function addMessage($belong,$issue_id,$issue_title,$type,$send_id,$receive_id,$msg,$reply_id){
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
	 * 消息列表
	 * 1 回答 2评价 3 回答（关注） 4 评价（关注） 
	 */
	public function messageList(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$db = M('');
		
		$data  = $db ->table(C('DB_PREFIX')."answer_message as a ")->field('a.id,a.msg,a.send_id,a.receive_id,a.type,a.issue,a.issue_id,a.reply_id,b.name as send_name,c.name as revice_name,b.header')
			->join(C('DB_PREFIX')."system_user as b on a.send_id = b.id",'LEFT')
			->join(C('DB_PREFIX')."system_user as c on a.receive_id = c.id",'LEFT')
			->where(" a.belong = $systemid and is_read = 0")->select();
		if($data){
			foreach ($data as $key =>$row){
				unset($data[$key]['send_id']);
				unset($data[$key]['receive_id']);
				$data[$key] ['header'] = imgUrl($row['header']);
				if($row['receive_id'] ==$systemid ){
					$data[$key] ['revice_name'] = '你';
				}
			}
			//信息被读取 更新信息表
			M('AnswerMessage')->where(array('belong'=>$systemid,'is_read'=>0))->save(array('is_read'=>1));
		
		}else{
			$data = array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	}
	/**
	 * 获取未读 问题消息个数
	 */
	public function getMessageNum(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		
		$db = M('AnswerMessage');
		$num = $db->where(array('belong'=>$systemid,'is_read'=>0))->count();
		if(empty($num)){
			$num = '0';
		}
		$this->jsonUtils->echo_json_data(0, 'ok', array('num'=>$num));exit();
	}
	
	/**
	 * 个人主页 获取个人信息 
	 * 不传 system_user_id 则获取自己的个人主页
	 */
	public function getUserInfo(){
		
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		if(!empty($session_id)){
			$member_id = $this->session_handle->getsession_userid($session_id,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);//谁查看
		}
		$system_user_id = isset($_POST['system_user_id'])?(int)htmlspecialchars($_POST['system_user_id']):'';//查看谁
		//必须有其一参数不为空
		if(empty($systemid)&&empty($system_user_id)){
			$this->jsonUtils->echo_json_msg(4, '参数不全');exit();
		}
		//当查看为空 本身不为空时，则为查看自己
		if(empty($system_user_id)&&!empty($systemid)){
			$system_user_id =$systemid;
		}
		$db = M('AnswerUser');
		$data = $db ->table(C('DB_PREFIX')."answer_user as a ")
		->field('a.*,b.name,b.header')->join(C('DB_PREFIX')."system_user as b on a.system_user_id=b.id")
		->where(array('a.system_user_id'=>$system_user_id))->find();
		$data ['background'] = imgUrl($data['background']);
		$data ['header'] = imgUrl($data['header']);
		if(!empty($systemid)){
			if($system_user_id == $systemid){
				$data ['is_attention'] = '0';
				$data ['is_chat'] = '0';
			}else{
				$db = M('AnswerUserAttention');
				$da = $db ->where(array('host'=>$systemid,'attented'=>$system_user_id))->find();
				if($da){
					$rel = $db ->where(array('host'=>$system_user_id,'attented'=>$systemid))->find();
					if($rel){
						$data ['is_attention'] = '2';// 0不显示 1已关注 2 互相关注（即好友）3未关注
					}else{
						$data ['is_attention'] = '1';
					}
				}else{
					$data ['is_attention'] = '3';
				}
				$data ['is_chat'] = '1';
				
			}
		}else{
			$data ['is_attention'] = '-1';//不显示
			$data ['is_chat'] = '0';
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);
	}
	
	
	/**
	 * 个人主页 关注功能
	 */
	public function userAttention(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$system_user_id = isset($_POST['system_user_id'])?htmlspecialchars($_POST['system_user_id']):'';
		if($member_id['type'] ==2){
			$this->jsonUtils->echo_json_msg(4, '商户无法关注');exit();
		}
		if(empty($system_user_id)){
			$this->jsonUtils->echo_json_msg(4, 'system_user_id为空');exit();
		}
		if($system_user_id == $systemid){
			$this->jsonUtils->echo_json_msg(4, '无法关注自己');exit();
		}
		$db = M('AnswerUserAttention');
		$data = $db ->where(array('host'=>$systemid,'attented'=>$system_user_id))->find();
		
		if($data){
			$del = $db->delete($data['id']);
			if($del){
				//减少自己关注量 他人粉丝
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$systemid))->setDec('attention_count');
				$dbUser->where(array('system_user_id'=>$system_user_id))->setDec('fans_count');
// 				$map = "(host = $system_user_id and  friends = $systemid) or (host = $systemid and  friends = $system_user_id)";
// 				$friend = M('AnswerUserFriend')->where($map)->delete();
				//聊天删除好友
// 				$xmpp = new \App\Model\XmppApiModel($systemid,$systemid);
// 				$domain =  XMPP_SERVER_DOMAIN;
// 				$resoure =  XMPP_SERVER_RESOURCE;
// 				$rece = $system_user_id."@".$domain."/".$resoure;
// 				$xmpp->delFriend($rece);
				$data ['is_attention'] = '3';// 3未关注 1已关注 2 互相关注（即好友）
				$this->jsonUtils->echo_json_data(0, '取消关注成功',$data);exit();
			}else{
				$this->jsonUtils->echo_json_msg(4, '取消关注失败');exit();
			}
			
		}else{
			$rel = $db ->add(array('host'=>$systemid,'attented'=>$system_user_id));
			if($rel){
				//加载自己关注量 他人粉丝
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$systemid))->setInc('attention_count');
				$dbUser->where(array('system_user_id'=>$system_user_id))->setInc('fans_count');
				//检测是否互相关注 生成好友
				$data = $db ->where(array('host'=>$system_user_id,'attented'=>$systemid))->find();
				if($data){
					//添加好友
// 					$friend = M('AnswerUserFriend');
// 					$add[0] = array('host'=>$system_user_id,'friends'=>$systemid);
// 					$add[1] = array('host'=>$systemid,'friends'=>$system_user_id);
// 					$dat = $friend->addAll($add);
// 					if(!$dat){
// 						$db->delete($rel);
// 						$this->jsonUtils->echo_json_msg(4, '自动添加好友失败');exit();
// 					}
					//聊天添加好友
// 					$xmpp = new \App\Model\XmppApiModel($systemid,$systemid);
// 					$domain =  XMPP_SERVER_DOMAIN;
// 					$resoure =  XMPP_SERVER_RESOURCE;
// 					$rece = $system_user_id."@".$domain."/".$resoure;
// 					$xmpp->addFriend($rece);
					
					$data ['is_attention'] = '2';// 3未关注 1已关注 2 互相关注（即好友）
					$this->jsonUtils->echo_json_data(0, '关注成功',$data);exit();
				}else{
					$data ['is_attention'] = '1';// 3未关注 1已关注 2 互相关注（即好友）
					$this->jsonUtils->echo_json_data(0, '关注成功',$data);exit();
				}
			}else{
				$this->jsonUtils->echo_json_msg(4, '关注失败');exit();
			}
			
		}
		
	}
	/**
	 * 获取某人动态 
	 * 公共查看
	 */
	public function getSomeoneRecent(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		if(!empty($session_id)){
			$member_id = $this->session_handle->getsession_userid($session_id,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		}
		$system_user_id = isset($_POST['system_user_id'])?(int)htmlspecialchars($_POST['system_user_id']):'';
		$longitude = isset($_POST['longitude'])?htmlspecialchars($_POST['longitude']):'';
		$latitude = isset($_POST['latitude'])?htmlspecialchars($_POST['latitude']):'';
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		if(empty($system_user_id)&&empty($systemid)){
			$this->jsonUtils->echo_json_msg(4, '参数不全');exit();
		}
		if(empty($system_user_id)&&!empty($systemid)){
			$system_user_id =$systemid;
		}
		if(!empty($longitude)&&!empty($latitude)){
			$disable=true;
		}else{
			$disable=false;
		}
		$time = time();
		$db = M('AnswerUserRecent');
		$count = $db ->where(array('system_user_id'=>$system_user_id,array('addtime'=>array('lt',$time))))->count();
		$data = $db ->where(array('system_user_id'=>$system_user_id,array('addtime'=>array('lt',$time))))->field('id,content,pics,addtime,longitude,latitude')->page($page)->order('addtime desc')->limit($num)->select();
		if($data){
			if($disable){
				foreach ($data as $key =>$row){
					$data [$key]['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
					$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
					$data [$key]['distance'] = getDistance($longitude,$latitude,$row['longitude'],$row['latitude']);
				}
			}else{
				foreach ($data as $key =>$row){
					$data [$key]['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
					$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
				}
			}
		}else{
			$data = array();
		}
		$arr['count'] = empty($count)?0:$count;
		$arr['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);exit();
	}
	/**
	 * 发动态
	 */
	
	public function addSomeoneRecent(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		if($member_id['type'] ==2){
			$this->jsonUtils->echo_json_msg(4, '商家已不允许发布动态');exit();
		}
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$content = isset($_POST['content'])?htmlspecialchars($_POST['content']):'';
		$longitude = isset($_POST['longitude'])?htmlspecialchars($_POST['longitude']):'';
		$latitude = isset($_POST['latitude'])?htmlspecialchars($_POST['latitude']):'';
		if(empty($content)&& empty($_FILES)){
			$this->jsonUtils->echo_json_msg(4, '内容为空');exit();
		}
		if(empty($longitude)){
			$this->jsonUtils->echo_json_msg(4, ' 精度为空');exit();
		}
		if(empty($latitude)){
			$this->jsonUtils->echo_json_msg(4, '维度为空');exit();
		}
		$db = M('AnswerUserRecent');
		$add ['system_user_id'] = $systemid;
		$add ['content'] = $content;
		$add ['addtime'] = time();
		$add ['longitude'] = $longitude;
		$add ['latitude'] = $latitude;

		if ($_FILES) {
			//Log::wirte(json_encode($_FILES));
			$f_arr = mul_upload ( '/Recent/' ,2);
			if ($f_arr) {
				$add ['pics'] =json_encode($f_arr) ;
			}
		}else{
			$add ['pics'] = "[]";
		}
		$data = $db ->add($add);
		if($data){
			$this->jsonUtils->echo_json_msg(0, 'ok');exit();
		}else{
			$this->jsonUtils->echo_json_msg(4, '发表失败');exit();
		}
	
	}
	
	/**
	 * 获取某人所有的提问和回答
	 */
	public function getSomeoneAnswer(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		if(!empty($session_id)){
			$member_id = $this->session_handle->getsession_userid($session_id,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		}
		$system_user_id = isset($_POST['system_user_id'])?(int)htmlspecialchars($_POST['system_user_id']):'';
		$type = isset($_POST['type'])?htmlspecialchars($_POST['type']):'';
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		if(empty($system_user_id)&&empty($systemid)){
			$this->jsonUtils->echo_json_msg(4, '参数不全');exit();
		}
		if(empty($system_user_id)&&!empty($systemid)){
			$system_user_id =$systemid;
		}
		$time = time();
		if($type ==1 ){
			//获取所有提问
			$db = M('AnswerProblem');
			$count = $db ->where(array('system_user_id'=>$system_user_id,'addtime'=>array('lt',$time)))->count();
			$data = $db ->where(array('system_user_id'=>$system_user_id,'addtime'=>array('lt',$time)))->field('id,title,attention,answer_num,addtime,pics')->order('addtime desc')->page($page)->limit($num)->select();
			if($data){
				foreach ($data as $key =>$row){
					$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
					$data [$key]['addtime'] = dealtime($row['addtime']);
				}
			}else{
				$data =array();
			}
		}elseif($type == 2 ){
			//获取所有回答
			$db = M('AnswerReply');
			$count = $db ->where(array('reply_id'=>$system_user_id,'pid'=>0,'addtime'=>array('lt',$time)))->count();
			$data = $db ->table(C('DB_PREFIX')."answer_reply as a")->field('b.id,b.title,a.id,a.reply_content,a.addtime,a.laud_count,a.collect_count,a.issue_id')
			->join(C('DB_PREFIX')."answer_problem as b on a.issue_id = b.id")
			->where(array('a.reply_id'=>$system_user_id,'a.pid'=>0,'a.addtime'=>array('lt',$time)))
			->order('a.addtime desc')->page($page)->limit($num)->select();
			if($data){
				foreach ($data as $key =>$row){
					$data [$key]['addtime'] = dealtime($row['addtime']);
				}
			}else{
				$data = array();
			}
		}
		
		$arr ['count'] = empty($count)?0:$count;
		$arr ['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);exit();
		
	}
	/**
	 * 关注类表
	 */
	public function userAttentionList(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		$db = M('');
		$data  = $db ->table(C('DB_PREFIX').'answer_user_attention as a ')->field('a.attented as system_user_id,b.name,b.header,ifnull(c.id,0) as is_attention ')
				->join(C('DB_PREFIX')."system_user as b on a.attented = b.id",'LEFT')
				->join(C('DB_PREFIX')."answer_user_friend as c on (c.host = a.host and c.friends = a.attented)",'LEFT')
				->where("a.host = $systemid")
				->page($page)->limit($num)->order('a.id desc')->select();
		if($data){
			foreach ($data as $key =>$row){
				$data[$key]['recent'] = $this->getUserOneRecent($row['system_user_id']);
				if($row['is_attention'] == 0){
					$data[$key]['is_attention'] = '1';//已关注
				}else{
					$data[$key]['is_attention'] = '2';//互相关注
				}
			
			
			}
		}else{
			$data =array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
		
	}
	/**
	 * 获取用户的最新动态 内容限制 1条
	 */
	protected function getUserOneRecent($id){
		$db = M('AnswerUserRecent');
		$data = $db ->where(array('system_user_id'=>$id))->getField('content');
		if(!$data){
			$data = '';
		}
		return $data;
	}
	/**
	 * 粉丝列表 （停用 取消好友概念）
	 */
	public function getUserFans(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		
		$db = M('');
		$data  = $db ->table(C('DB_PREFIX').'answer_user_attention as a ')->field('a.host as system_user_id,b.name,b.header,ifnull(c.id,0) as is_attention ')
		->join(C('DB_PREFIX')."system_user as b on a.attented = b.id",'LEFT')
		->join(C('DB_PREFIX')."answer_user_friend as c on (c.host = a.host and c.friends = a.attented)",'LEFT')
		->where("a.attented = $systemid")
		->page($page)->limit($num)->order('a.id desc')->select();
		if($data){
			foreach ($data as $key =>$row){
				$data[$key]['recent'] = $this->getUserOneRecent($row['system_user_id']);
				if($row['is_attention'] == 0){
					$data[$key]['is_attention'] = '3';//关注
				}else{
					$data[$key]['is_attention'] = '2';//互相关注
				}
			}
		}else{
			$data =array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
		
	}
	/**
	 * 发现 附近的人
	 */
	public function getNearbyRecent(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$longitude = isset($_POST['longitude'])?htmlspecialchars($_POST['longitude']):'';
		$latitude = isset($_POST['latitude'])?htmlspecialchars($_POST['latitude']):'';
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		//type =1 附近的动态 =2 已关注的人的动态
		$type = isset($_POST['type'])?htmlspecialchars($_POST['type']):'1';
		$db = M('');
		if($type == 1){
			if(empty($longitude)){
				$this->jsonUtils->echo_json_msg(4, '精度错误');exit();
			}
			if(empty($latitude)){
				$this->jsonUtils->echo_json_msg(4, '维度错误');exit();
			}
			$ll_arr=rangekm(3, $longitude,$latitude);//获取最大最小经纬度
			$maxLng=$ll_arr['maxLng'];
			$minLng=$ll_arr['minLng'];
			$maxLat=$ll_arr['maxLat'];
			$minLat=$ll_arr['minLat'];
			//$where = " a.longitude <=$maxLng and a.longitude>=$minLng and a.latitude <=$maxLat and a.latitude>=$minLat and a.status=0 and a.addtime <".time();
			$where = "  a.status=0  and a.addtime <".time();
			$data = $db ->table(C("DB_PREFIX")."answer_user_recent as a ") ->field("b.header,b.name,a.id,a.system_user_id,a.content,a.pics,a.addtime,a.longitude,a.latitude,a.comment_count")
			->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id")->where($where)->order("a.addtime desc")->limit($num)
			->page($page)->select();
		}elseif($type == 2 ){
			$data  = $db ->table(C("DB_PREFIX")."answer_user_recent as a" )->field("b.header,b.name,a.id,a.system_user_id,a.content,a.pics,a.addtime,a.longitude,a.latitude,a.comment_count")
			->join(C('DB_PREFIX')."answer_user_attention as c on c.attented = a.system_user_id ")->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id",'LEFT')
			->where("c.host = $systemid and a.status=0 and a.addtime <".time())->order("a.addtime desc")->limit($num)
			->page($page)->select();
// 			echo $db->getLastSql();
		}else{
			$this->jsonUtils->echo_json_msg(4, 'type错误');exit();
		}
		
		if($data){
			foreach ($data as $key =>$row){
				unset($data[$key]['longitude']);
				unset($data[$key]['latitude']);
				$data [$key]['header'] = imgUrl($row['header'],true) ;
				$data [$key]['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
				$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
				$data [$key]['distance'] = getDistance($longitude,$latitude,$row['longitude'],$row['latitude']);
			//	$data [$key]['list'] = $this->getRecentComment($row['id']);
				$data [$key]['comment_count'] = $row['comment_count'];
			}
		}else{
			$data = array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	}
	/**
	 * 获取指定动态评论个数
	 */
	protected function getRecentCommentCount($recent_id){
		$db = M('AnswerUserRecentReply');
		$data = $db ->where(array('recent_id'=>$recent_id,'status'=>0))->count();
		if(!$data){
			$data = '0';
		}
		return $data;
	}
	/**
	 * 获取动态评论
	 * @param int $recent_id
	 */
	protected function getRecentComment($recent_id){
		$db = M('');
		$data = $db ->table(C('DB_PREFIX')."answer_user_recent_reply as a")->field('a.id,a.reply_id,a.reply_content,a.pid,a.pid_id,a.addtime,b.name as replyname,c.name as pidname')
		->join(C('DB_PREFIX')."system_user as b on a.reply_id = b.id","LEFT")
		->join(C('DB_PREFIX')."system_user as c on a.pid_id = c.id","LEFT")
		->where(array('a.recent_id'=>$recent_id,'a.status'=>0))->select();
		if($data){
			foreach ($data as $key =>$row){
				unset($data[$key]['reply_id']);
				unset($data[$key]['pid_id']);
				$data[$key]['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
			}
			$this->data = $data;
			$data = $this->dealRecentArray();
		}else{
			$data = array();
		}
	
		return $data;
		
		
	}
	/**
	 * 回复某人的动态
	 */
	
	public function replyToRecent(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$pid = isset ( $_POST ['pid'] ) ? htmlspecialchars ( $_POST ['pid'] ) : '';
		$recent_id = isset ( $_POST ['recent_id'] ) ? htmlspecialchars ( $_POST ['recent_id'] ) : '';
		$reply_context = isset ( $_POST ['reply_content'] ) ? htmlspecialchars ( $_POST ['reply_content'] ) : '';
		
		if(empty($recent_id)){
			$this->jsonUtils->echo_json_msg(4, '动态id为空');
			exit();
		}
		
		if(empty($reply_context)){
			$this->jsonUtils->echo_json_msg(4, '回答内容为空');
			exit();
		}
		$auth = M('AnswerUserRecent')->where(array('id'=>$recent_id))->find();
		if($auth){
			if(empty($pid) ){
				//顶层回复
				$data ['recent_id'] = $recent_id;
				$data ['reply_id'] = $systemid;
				$data ['reply_content'] = $reply_context;
				$data ['addtime'] = time();
				$data ['pid'] = 0;
				$data ['pid_id'] =0;
					
				$db = M('AnswerUserRecentReply');
				$data = $db ->add($data);
				if($data){
					 M('AnswerUserRecent')->where(array('id'=>$recent_id))->setInc('comment_count');
					$arr['belong'] = $auth['system_user_id'];//消息所有者
					$arr['recent_id'] = $recent_id;//动态id
					$arr['recent'] = $auth['content'];//动态内容
					$arr['send_id'] = $systemid;//消息发送者
					$arr['receive_id'] = $auth['system_user_id'];//消息接受者
					$arr['msg'] = $reply_context;//消息主体
					$arr['reply_id'] = $data;//对其快速回复的pid
					$arr['addtime'] = time();
					//动态所有者 收到消息提示
					$this->addRecentMessage($arr);
					$this->jsonUtils->echo_json_data(0, '回复成功',array('id'=>$data));exit();
				}else{
					$this->jsonUtils->echo_json_msg(4, '回复失败');exit();
				}
			}else{
		
				$db = M('AnswerUserRecentReply');
				//验证上级回复是否存在
				$pidData =$db ->where(array('id'=>$pid))->field('recent_id,reply_id')->find();
				if($pidData ['recent_id'] != $recent_id || empty($pidData)){
					$this->jsonUtils->echo_json_msg(4, '回复参数有错');exit();
				}
				$data ['recent_id'] = $recent_id;
				$data ['reply_id'] = $systemid;
				$data ['reply_content'] = $reply_context;
				$data ['addtime'] = time();
				$data ['pid'] = $pid;
				$data ['pid_id'] = $pidData['reply_id'];
				$data = $db ->add($data);
				if($data){
					M('AnswerUserRecent')->where(array('id'=>$recent_id))->setInc('comment_count');
					$arr['belong'] = $auth['system_user_id'];//消息所有者
					$arr['recent_id'] = $recent_id;//动态id
					$arr['recent'] = $auth['content'];//动态内容
					$arr['send_id'] = $systemid;//消息发送者
					$arr['receive_id'] = $pidData['reply_id'];//消息接受者
					$arr['msg'] = $reply_context;//消息主体
					$arr['reply_id'] = $data;//对其快速回复的pid
					$arr['addtime'] = time();
					
					
					//问题所有者 收到评论消息提示
					$this->addRecentMessage($arr);
					//答案者 收到评价的消息提示
					if($pidData['reply_id'] != $auth['system_user_id']){
						$arr['belong'] = $pidData['reply_id'];//消息所有者
						$this->addRecentMessage($arr);
					}
					
					$this->jsonUtils->echo_json_data(0, '回复成功',array('id'=>$data));exit();
				}else{
					$this->jsonUtils->echo_json_msg(4, '回复失败');exit();
				}
		
		
			}
		}else{
			$this->jsonUtils->echo_json_msg(4, '动态不存在');exit();
		}
		
	}
	/**
	 * 添加动态的消息通知
	 */
	protected  function addRecentMessage($arr){
		$db = M('AnswerUserRecentMessage');
		$data = $db ->add($arr);
		if($data){
			$user = M('SystemUser')->where(array('id'=>$arr['belong']))->getField('type');
			//jpush
			$jpush =new \App\Model\JpushModel();
			$jpush->user = $user;
			$jpush ->push(4, array($arr['belong']),array());
			
			$xmpp = new \App\Model\XmppApiModel();
			$xmpp ->requestPush(4, array($arr['belong']),array());
		}
		return $data;
	}
	/**
	 * 获取动态详情
	 */
	public function getRecentDetail(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$db = M('AnswerUserRecent');
		$recent= $db->table(C('DB_PREFIX')."answer_user_recent as a ")->field('a.id,a.system_user_id,a.content,a.pics,a.addtime,b.header,b.name')
		->join(C('DB_PREFIX')."system_user as b on b.id = a.system_user_id",'LEFT')
		->where(array('a.id'=>$id))->find();
		if(empty($recent)){
			$this->jsonUtils->echo_json_msg(4, '动态不存在');exit();
		}
		$recent ['header'] = imgUrl($recent['header']);
		$recent ['addtime'] = date('Y-m-d H:i:s',$recent['addtime']);
		$recent ['pics'] = imgUrl(json_decode($recent['pics'],true));
		
		$this->data = array();
		$this->treeList = array();
		$this->treekey = 0;
		$data = $db ->table (C('DB_PREFIX')."answer_user_recent_reply as a")->field('a.id,b.name as reply_name,ifnull(c.name,"") as pidname,a.reply_content,a.addtime,a.pid')
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
		$recent['child'] = $data;
		if($recent){
			//信息被读取 更新信息表
		//	M('AnswerUserRecentMessage')->where(array('recent_id'=>$recent['id'],'belong'=>$systemid))->save(array('is_read'=>1));
			$this->jsonUtils->echo_json_data(0, 'ok', $recent);exit();
		}
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
	/**
	 * 动态通知列表
	 */
	public function recentMessageList(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		
		$db = M('');
		$data  = $db ->table(C('DB_PREFIX')."answer_user_recent_message as a ")
		->field('a.id,a.msg,a.send_id,a.receive_id,a.addtime,a.recent,a.recent_id,a.reply_id,b.name as send_name,c.name as revice_name,b.header')
		->join(C('DB_PREFIX')."system_user as b on a.send_id = b.id",'LEFT')
		->join(C('DB_PREFIX')."system_user as c on a.receive_id = c.id",'LEFT')
		->where(" a.belong = $systemid and is_read = 0")->select();
		if($data){
			foreach ($data as $key =>$row){
				$data[$key] ['system_user_id'] = $row['send_id'];
				unset($data[$key]['send_id']);
				unset($data[$key]['receive_id']);
				$data[$key] ['header'] = imgUrl($row['header']);
				$data[$key] ['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
				if($row['receive_id'] ==$systemid ){
					$data[$key] ['revice_name'] = '你';
				}
			}
			M('AnswerUserRecentMessage')->where(array('belong'=>$systemid,'is_read'=>0))->save(array('is_read'=>1));
		
		}else{
			$data = array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
		
	}
	/**
	 * 获取用户是否有最新消息通知 统计个数
	 */
	public function getRecentAlert(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$db = M('AnswerUserRecentMessage');
		$count = $db ->where(array('belong'=>$systemid,'is_read'=>0))->count();
		if(!$count){
			$count= '0';
			$this->jsonUtils->echo_json_data(0, 'ok', array('count'=>$count,'header'=>''));exit();
		}
		$header = $db ->table(C('DB_PREFIX')."answer_user_recent_message as a")->field('b.header')
		->join(C('DB_PREFIX')."system_user as b on b.id= a.send_id")
		->where(array('a.belong'=>$systemid,'a.is_read'=>0))->order('a.addtime desc')->find();
// 		echo $db->getLastSql();
		$header = imgUrl($header['header']);
		$this->jsonUtils->echo_json_data(0, 'ok', array('count'=>$count,'header'=>$header));
	}
	/**
	 * 上传背景图
	 */
	public function uploadbackground(){
		
	}
	
   /*******************************************************************************************
    * Ver V2
    * @第二版本接口
    ******************************************************************************************/
	
	/**
	 * ver V2
	 * 个人主页 获取个人信息
	 * 不传 system_user_id 则获取自己的个人主页
	 */
	public function getUserInfoV2(){

		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		if(!empty($session_id)){
			$member_id = $this->session_handle->getsession_userid($session_id,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);//谁查看
		}
		$system_user_id = isset($_POST['system_user_id'])?(int)htmlspecialchars($_POST['system_user_id']):'';//查看谁
		//必须有其一参数不为空
		if(empty($systemid)&&empty($system_user_id)){
			$this->jsonUtils->echo_json_msg(4, '参数不全');exit();
		}
		//当查看为空 本身不为空时，则为查看自己
		if(empty($system_user_id)&&!empty($systemid)){
			$system_user_id =$systemid;
		}
		$db = M('');
		$data = $db -> table(C('DB_PREFIX')."system_user as a")->join(C('DB_PREFIX')."member as b on (b.id = a.sub_id and a.type = 0)","LEFT")
		->field('a.id as system_user_id,b.id,b.nick_name,b.header,b.gender,b.signature,b.albums,b.driving_exp,b.interests,b.haunts,b.personal_description,a.brand_icon')
		->where(array('a.id'=>$system_user_id))->find();
		if(!$data['id']){
			$this->jsonUtils->echo_json_msg(5, '未找到该用户资料');exit();
		}
		$data ['albums'] = imgUrl(json_decode($data['albums'],true));
		$data ['brand_icon'] = imgUrl($data['brand_icon']);
		$data ['header'] = imgUrl($data['header']);
		//动态
		$recent = $this->getSomeoneShortRecentV2($system_user_id);
		$data['recent_count'] = $recent['count'];
		$data['recent_content'] = $recent['content'];
		$data['recent_pics'] = $recent ['pics'];
		//获取默认车型
		$Cartdb =M('');
		$member = M ('SystemUser')->where(array('id'=>$system_user_id))->getfield('sub_id');
		$cart = $Cartdb->table(C('DB_PREFIX')."cart as a")->join(C('DB_PREFIX')."car_brand as b on a.brand_id = b.id",'LEFT') 
		->join(C('DB_PREFIX')."car_brand as c on a.model_id = c.id",'LEFT')
		->field("c.name as model_name")
		->where(array('a.member_id'=>$member,'a.default_cart'=>1))->find();
// 		$data['brand_icon'] = imgUrl($cart['brand_icon']);
		$data['model_name'] = empty($cart['model_name'])?'':$cart['model_name'];
		if(!empty($systemid)){
			// 只存在关注和已关注状态
			if($system_user_id == $systemid){
				$data ['is_attention'] = '0';
				$data ['is_chat'] = '0';
			}else{
				$db = M('AnswerUserAttention');
				$da = $db ->where(array('host'=>$systemid,'attented'=>$system_user_id))->find();
				if($da){
					// 0不显示 1已关注 2 互相关注（即好友）3未关注
					$data ['is_attention'] = '1';
				}else{
					$data ['is_attention'] = '3';
				}
				$data ['is_chat'] = '1';
	
			}
		}else{
			$data ['is_attention'] = '-1';//不显示
			$data ['is_chat'] = '0';
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data); exit();
	}
	/**
	 * 获取简短个人动态 
	 * 只显示 最近一条动态和动态数
	 */
	public function getSomeoneShortRecentV2($system_user_id){
		$time = time();
		$db = M('AnswerUserRecent');
		$count = $db ->where(array('system_user_id'=>$system_user_id,'status'=>0))->count();
		$data = $db ->where(array('system_user_id'=>$system_user_id,'status'=>0))
		->field('content,pics')->order('addtime desc')->find();
		if($data){
				if($data['pics'] =='[]'){
					$data['pics'] = '';
				}else{
					$pics= imgUrl(json_decode($data['pics'],true));
					$data['pics'] = $pics[0]['hs'];
				}
		}else{
			$data = array('content'=>'','pics'=>'');
		}
		$data['count'] = empty($count)?'0':$count;
		
		return $data;
	}
	
	/**
	 * 修改个人资料
	 */
	
	public function  editUserInfoV2(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		//$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$pics = isset ( $_POST ['pics'] ) ?  ( trim ( $_POST ['pics'] ) ) : '';
		$signature = isset($_POST['signature'])?htmlspecialchars($_POST['signature']):'';
		$nick_name = isset($_POST['nick_name'])?htmlspecialchars($_POST['nick_name']):'';
		$gender = isset($_POST['gender'])?htmlspecialchars($_POST['gender']):'';
		$driving_exp = isset($_POST['driving_exp'])?htmlspecialchars($_POST['driving_exp']):'';
		$haunts = isset($_POST['haunts'])?htmlspecialchars($_POST['haunts']):'';
		$interests = isset($_POST['interests'])?htmlspecialchars($_POST['interests']):'';
		$personal_description = isset($_POST['personal_description'])?htmlspecialchars($_POST['personal_description']):'';
		
		if(empty($nick_name)){
			$this->jsonUtils->echo_json_msg(5, '昵称为空');exit();
		}
		if(empty($gender) &&!in_array($gender, array(1,0))){
			$this->jsonUtils->echo_json_msg(5, '性别为空');exit();
		}
		if(empty($driving_exp)){
			$this->jsonUtils->echo_json_msg(5, '驾龄为空');exit();
		}
		
		
		$db = M('Member');
		Log::write($pics,'ERR');
		if(!empty($pics)){
			$arr_decode = json_decode($pics,true);
			if(!empty($arr_decode)){
				foreach ($arr_decode as $key =>$row){
					if(!empty($row)){
						$temp[$key]['hs'] = str_replace(C('ROOT_UPLOADS'), '', $row['hs']);
						$temp[$key]['hb'] = str_replace(C('ROOT_UPLOADS'), '', $row['hb']);
					}
				}
				foreach ($temp as $row){
					$data['albums'][] = $row;
				}
			}else{
				$data['albums'] = array();
			}
		}else{
			$data['albums'] = array();
		}
		if ($_FILES) {
			$f_arr = mul_upload ( '/Merchant/',1 );
			if ($f_arr) {
				$data ['albums'] =array_merge($data['albums'],$f_arr); // 把多张图片数组格式转json保存数据库
			}
		
		}
		$data['albums'] = json_encode($data['albums']);
		
		$data['nick_name'] = $nick_name;
		CommonController::saveName($member_id['id'], 0, $nick_name);
		$data['gender'] = $gender;
		$data['driving_exp'] = $driving_exp;
		if(!empty($signature)){
			$data['signature'] = $signature;
		}
		if(!empty($interests)){
			$data['interests'] = $interests;
		}
		if(!empty($personal_description)){
			$data['personal_description'] = $personal_description;
		}
		if(!empty($haunts)){
			$data['haunts'] = $haunts;
		}
		$rel = $db ->where(array('id'=>$member_id['id']))->save($data);
		if($rel === false){
			$this->jsonUtils->echo_json_msg(6, '修改失败');exit();
		}else{
			$this->jsonUtils->echo_json_msg(0, 'ok');exit();
		}
		
		
		
	}
	
	
	/**
	 * 我的关注列表
	 */
	public function myAttention(){
		$session_id = !empty($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$system_user_id = !empty($_POST['system_user_id'])?htmlspecialchars($_POST['system_user_id']):'';
		$page = !empty($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = !empty($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		if(!empty($system_user_id)){
			$systemid = $system_user_id;
		}
		$db = M();
		$data = $db -> table(C('DB_PREFIX')."answer_user_attention as a")
		->join(C('DB_PREFIX')."system_user as b on a.attented = b.id",'LEFT')
		->join(C('DB_PREFIX')."member as c on (b.sub_id = c.id and b.type = 0)",'RIGHT')
		->field('b.id as system_user_id,b.brand_icon,c.nick_name,c.gender,c.header')
		->where(array('a.host'=>$systemid))->page($page)->limit($num)->select();

		if(!empty($data)){
			foreach($data as $key =>$row){
				$data[$key]['brand_icon'] = imgUrl($row['brand_icon']);
				$data[$key]['header'] =imgUrl( $row['header']);
			}
		}else{
			$data = array();
		}
		$arr['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);exit();
	}
	/**
	 * 我的粉丝列表
	 */
	public function myFans(){
		$session_id = !empty($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$page = !empty($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = !empty($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		$system_user_id = !empty($_POST['system_user_id'])?htmlspecialchars($_POST['system_user_id']):'';
		if(!empty($system_user_id)){
			$systemid = $system_user_id;
		}
		$db = M();
		$data = $db -> table(C('DB_PREFIX')."answer_user_attention as a")
		->join(C('DB_PREFIX')."system_user as b on a.host = b.id",'LEFT')
		->join(C('DB_PREFIX')."member as c on (b.sub_id = c.id and b.type = 0)",'RIGHT')
		->field('b.id as system_user_id,b.brand_icon,c.nick_name,c.gender,c.header')
		->where(array('a.attented'=>$systemid))->page($page)->limit($num)->select();
// 		dump($data);
		if(!empty($data)){
			foreach($data as $key =>$row){
				$data[$key]['brand_icon'] = imgUrl($row['brand_icon']);
				$data[$key]['header'] =imgUrl( $row['header']);
			}
		}else{
			$data = array();
		}
		$arr['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);exit();
	}
	
	/**
	 * 个人主页 关注功能
	 */
	public function userAttentionV2(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$system_user_id = isset($_POST['system_user_id'])?htmlspecialchars($_POST['system_user_id']):'';
		if($member_id['type'] ==2){
			$this->jsonUtils->echo_json_msg(4, '商户无法关注');exit();
		}
		if(empty($system_user_id)){
			$this->jsonUtils->echo_json_msg(4, 'system_user_id为空');exit();
		}
		if($system_user_id == $systemid){
			$this->jsonUtils->echo_json_msg(4, '无法关注自己');exit();
		}
		$db = M('AnswerUserAttention');
		$data = $db ->where(array('host'=>$systemid,'attented'=>$system_user_id))->find();
	
		if($data){
			$del = $db->delete($data['id']);
			if($del){
				//减少自己关注量 他人粉丝
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$systemid))->setDec('attention_count');
				$dbUser->where(array('system_user_id'=>$system_user_id))->setDec('fans_count');
				// 				$map = "(host = $system_user_id and  friends = $systemid) or (host = $systemid and  friends = $system_user_id)";
				// 				$friend = M('AnswerUserFriend')->where($map)->delete();
				//聊天删除好友
				// 				$xmpp = new \App\Model\XmppApiModel($systemid,$systemid);
				// 				$domain =  XMPP_SERVER_DOMAIN;
				// 				$resoure =  XMPP_SERVER_RESOURCE;
				// 				$rece = $system_user_id."@".$domain."/".$resoure;
				// 				$xmpp->delFriend($rece);
				$data ['is_attention'] = '3';// 3未关注 1已关注 2 互相关注（即好友）
				$this->jsonUtils->echo_json_data(0, '取消关注成功',$data);exit();
			}else{
				$this->jsonUtils->echo_json_msg(4, '取消关注失败');exit();
			}
				
		}else{
			$rel = $db ->add(array('host'=>$systemid,'attented'=>$system_user_id));
			if($rel){
				//加载自己关注量 他人粉丝
				$dbUser = M('AnswerUser');
				$dbUser->where(array('system_user_id'=>$systemid))->setInc('attention_count');
				$dbUser->where(array('system_user_id'=>$system_user_id))->setInc('fans_count');
				//检测是否互相关注 生成好友
				$data ['is_attention'] = '1';// 3未关注 1已关注 2 互相关注（即好友）
				$this->jsonUtils->echo_json_data(0, '关注成功',$data);exit();
			}else{
				$this->jsonUtils->echo_json_msg(4, '关注失败');exit();
			}
				
		}
	
	}
	
	/**
	 * 遇见模块 列表
	 */
	public function getNearbyRecentV2(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$longitude = isset($_POST['longitude'])?htmlspecialchars($_POST['longitude']):'';
		$latitude = isset($_POST['latitude'])?htmlspecialchars($_POST['latitude']):'';
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		//type =1 附近的动态 =2 已关注的人的动态
		$type = isset($_POST['type'])?htmlspecialchars($_POST['type']):'1';
		$db = M('');
		if($type == 1){
			if(empty($longitude)){
				$this->jsonUtils->echo_json_msg(4, '精度错误');exit();
			}
			if(empty($latitude)){
				$this->jsonUtils->echo_json_msg(4, '维度错误');exit();
			}
			$ll_arr=rangekm(3, $longitude,$latitude);//获取最大最小经纬度
			$maxLng=$ll_arr['maxLng'];
			$minLng=$ll_arr['minLng'];
			$maxLat=$ll_arr['maxLat'];
			$minLat=$ll_arr['minLat'];
			//$where = " a.longitude <=$maxLng and a.longitude>=$minLng and a.latitude <=$maxLat and a.latitude>=$minLat and a.status=0 and a.addtime <".time();
			$where = "  a.status=0 ";
			$data = $db ->table(C("DB_PREFIX")."answer_user_recent as a ") ->field("b.header,b.name,a.id,a.system_user_id,a.content,a.pics,a.addtime,a.longitude,a.latitude,a.comment_count,a.laud_count,b.brand_icon")
			->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id")
			->where($where)->order("a.addtime desc")->limit($num)
			->page($page)->select();
		}elseif($type == 2 ){
			$data  = $db ->table(C("DB_PREFIX")."answer_user_recent as a" )->field("b.header,b.name,a.id,a.system_user_id,a.content,a.pics,a.addtime,a.longitude,a.latitude,a.comment_count,a.laud_count,b.brand_icon")
			->join(C('DB_PREFIX')."answer_user_attention as c on c.attented = a.system_user_id ")->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id",'LEFT')
			->where("c.host = $systemid and a.status=0 ")->order("a.addtime desc")->limit($num)
			->page($page)->select();
			// 			echo $db->getLastSql();
		}elseif($type == 3){
			$data  = $db ->table(C("DB_PREFIX")."answer_user_recent as a" )->field("b.header,b.name,a.id,a.system_user_id,a.content,a.pics,a.addtime,a.longitude,a.latitude,a.comment_count,a.laud_count,b.brand_icon")
			->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id",'LEFT')
			->where("a.system_user_id = $systemid and a.status=0 ")->order("a.addtime desc")->limit($num)
			->page($page)->select();
			// 			echo $db->getLastSql();
		}else{
			$this->jsonUtils->echo_json_msg(4, 'type错误');exit();
		}
		if($data){
			foreach ($data as $key =>$row){
				unset($data[$key]['longitude']);
				unset($data[$key]['latitude']);
				$data [$key]['header'] = imgUrl($row['header']) ;
				$data [$key]['brand_icon'] = imgUrl($row['brand_icon']) ;
				$data [$key]['addtime'] = dealtime($row['addtime']);
				$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
				if(!empty($longitude)&&!empty($latitude)){
					$data [$key]['distance'] = getDistance($longitude,$latitude,$row['longitude'],$row['latitude']);
				}
				//	$data [$key]['list'] = $this->getRecentComment($row['id']);
				$data [$key]['comment_count'] = $row['comment_count'];
			}
		}else{
			$data = array();
		}
		$arr['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);exit();
	}
	
	
	/**
	 * 动态点赞
	 */
	public function recnetClickLuad(){
	
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
	
		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '动态为空');
			exit();
		}
	
		$db = M('AnswerUserRecent');
		$init = $db ->where(array('id'=>$id,'status'=>array('egt',0)))->getField('id');
		if($init){
			$laudDb = M('AnswerUserRecentLaud');
			$data = $laudDb->where(array('recent_id'=>$id,'system_user_id'=>$systemid))->find();
			if($data){
				if($data['status'] == 0){
					$rel = $laudDb->where(array('id'=>$data['id']))->save(array('status'=>1));
					if($rel){
						$db->where(array('id'=>$id))->setInc('laud_count');
						//更新个人信息 点赞量
// 						$dbUser = M('AnswerUser');
// 						$dbUser->where(array('system_user_id'=>$init['reply_id']))->setInc('laud_count');
// 						$num = $db->where(array('id'=>$id))->getField('laud_count');
						$this->jsonUtils->echo_json_msg(0, '赞成功');exit();
					}else{
						$this->jsonUtils->echo_json_msg(4, '赞失败');exit();
					}
				}else{
					$rel = $laudDb->where(array('id'=>$data['id']))->save(array('status'=>0));
					if($rel){
						$db->where(array('id'=>$id))->setDec('laud_count');
						//更新个人信息 点赞量
// 						$dbUser = M('AnswerUser');
// 						$dbUser->where(array('system_user_id'=>$init['reply_id']))->setDec('laud_count');
// 						$num = $db->where(array('id'=>$id))->getField('laud_count');
						$this->jsonUtils->echo_json_msg(0, '取消赞成功');exit();
					}else{
						$this->jsonUtils->echo_json_msg(4, '取消赞失败');exit();
					}
				}
	
			}else{
				$rel = $laudDb->add(array('recent_id'=>$id,'system_user_id'=>$systemid,'status'=>1));
				if($rel){
					$db->where(array('id'=>$id))->setInc('laud_count');
					//更新个人信息 点赞量
// 					$dbUser = M('AnswerUser');
// 					$dbUser->where(array('system_user_id'=>$init['reply_id']))->setInc('laud_count');
// 					$num = $db->where(array('id'=>$id))->getField('laud_count');
					$this->jsonUtils->echo_json_msg(0, '赞成功');exit();
	
				}else{
					$this->jsonUtils->echo_json_msg(4, '赞失败');exit();
				}
	
			}
		}else{
			$this->jsonUtils->echo_json_msg(4, '该动态不存在');exit();
		}
	
	}
	/**
	 * 删除我的某条动态
	 */
	public function delOneRecent(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		
		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '动态为空');
			exit();
		}
		$db = M('AnswerUserRecent');
		$init = $db ->where(array('id'=>$id,'status'=>array('egt',0),'system_user_id'=>$systemid))->getField('id');
		if($init){
			
			$data = $db ->where(array('id'=>$id))->save(array('status'=>-1));
			if($data === false){
				$this->jsonUtils->echo_json_msg(4, '删除失败');exit();
			}else{
				$this->jsonUtils->echo_json_msg(0, '删除成功');exit();
			}
		}else{
			$this->jsonUtils->echo_json_msg(4, '该动态不存在');exit();
		}
		
	}
	
	/**
	 * 动态通知列表
	 */
	public function recentMessageListV2(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
	
		$db = M('');
		$data  = $db ->table(C('DB_PREFIX')."answer_user_recent_message as a ")
		->field('a.msg,a.send_id,a.addtime,a.recent_id,b.name as send_name,b.header')
		->join(C('DB_PREFIX')."system_user as b on a.send_id = b.id",'LEFT')
		->where(" a.belong = $systemid and is_read = 0")->select();
		if($data){
			foreach ($data as $key =>$row){
				$data[$key] ['system_user_id'] = $row['send_id'];
				unset($data[$key]['send_id']);
				unset($data[$key]['receive_id']);
				$data[$key] ['header'] = imgUrl($row['header']);
				$data[$key] ['addtime'] = dealtime($row['addtime']);
			
			}
			M('AnswerUserRecentMessage')->where(array('belong'=>$systemid,'is_read'=>0))->save(array('is_read'=>1));
	
		}else{
			$data = array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $data);exit();
	
	}
	
	
	/**
	 * 获取动态详情
	 */
	public function getRecentDetailV2(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$longitude = isset($_POST['longitude'])?htmlspecialchars($_POST['longitude']):'';
		$latitude = isset($_POST['latitude'])?htmlspecialchars($_POST['latitude']):'';
		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '动态不存在');exit();
		}
		if(empty($longitude)){
			$this->jsonUtils->echo_json_msg(4, '精度错误');exit();
		}
		if(empty($latitude)){
			$this->jsonUtils->echo_json_msg(4, '维度错误');exit();
		}
		
		$db = M('AnswerUserRecent');
		$recent= $db->table(C('DB_PREFIX')."answer_user_recent as a ")->field('a.id,a.system_user_id,a.laud_count,a.comment_count,a.content,a.pics,a.addtime,b.header,b.name,c.gender,b.brand_icon,a.longitude,a.latitude')
		->join(C('DB_PREFIX')."system_user as b on b.id = a.system_user_id",'LEFT')
		->join(C('DB_PREFIX')."member as c on (c.id = b.sub_id and b.type = 0)",'LEFT')
		->where(array('a.id'=>$id))->find();
		if(empty($recent)){
			$this->jsonUtils->echo_json_msg(4, '动态不存在');exit();
		}
		$recent ['distance'] = getDistance($longitude,$latitude,$recent['longitude'],$recent['latitude']);
		unset($recent['longitude']);
		unset($recent['latitude']);
		$recent ['header'] = imgUrl($recent['header']);
		$recent ['brand_icon'] = imgUrl($recent['brand_icon']);
		$recent ['addtime'] = dealtime($recent['addtime']);
		$recent ['pics'] = imgUrl(json_decode($recent['pics'],true));
	
		//获取点赞头像和点赞id
		$laud = M('AnswerUserRecentLaud');
		$laudData = $laud->table(C('DB_PREFIX')."answer_user_recent_laud as a")
		->field('a.system_user_id,b.header')
		->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id")
		->where(array('a.recent_id'=>$id,'a.status'=>1))->order('a.id desc')->select();
		if(!empty($laudData)){
			foreach ($laudData as $key =>$row){
				$laudData[$key]['header'] = imgUrl($row['header']);
			}
		}else{
			$laudData = array();
		}
		$recent ['laudArr'] = $laudData;
		$this->data = array();
		$this->treeList = array();
		$this->treekey = 0;
		$data = $db ->table (C('DB_PREFIX')."answer_user_recent_reply as a")->field('a.id,b.name as reply_name,ifnull(c.name,"") as pidname,a.reply_content,a.addtime,a.pid')
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
		$recent['child'] = $data;
		if($recent){
			//信息被读取 更新信息表
			//	M('AnswerUserRecentMessage')->where(array('recent_id'=>$recent['id'],'belong'=>$systemid))->save(array('is_read'=>1));
			$this->jsonUtils->echo_json_data(0, 'ok', $recent);exit();
		}
	}
	
	public function recentLauder(){
		$id = isset ( $_POST ['id'] ) ? htmlspecialchars ( $_POST ['id'] ) : '';
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		if(empty($id)){
			$this->jsonUtils->echo_json_msg(4, '动态不存在');exit();
		}
		$laud = M('AnswerUserRecentLaud');
		$laudData = $laud->table(C('DB_PREFIX')."answer_user_recent_laud as a")
		->field('a.system_user_id,b.name,b.header,c.signature')
		->join(C('DB_PREFIX')."system_user as b on a.system_user_id = b.id")
		->join(C('DB_PREFIX')."member as c on (b.sub_id = c.id and b.type = 0)")
		->where(array('a.recent_id'=>$id,'a.status'=>1))->order('a.id desc')->select();
		if(!empty($laudData)){
			foreach ($laudData as $key =>$row){
				$laudData[$key]['header'] = imgUrl($row['header']);
			}
		}else{
			$laudData = array();
		}
		$this->jsonUtils->echo_json_data(0, 'ok', $laudData);exit();
	}
	
	/**
	 * 获取某人动态
	 * 公共查看
	 */
	public function getSomeoneRecentV2(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		if(!empty($session_id)){
			$member_id = $this->session_handle->getsession_userid($session_id,1);
			$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		}
		$system_user_id = isset($_POST['system_user_id'])?(int)htmlspecialchars($_POST['system_user_id']):'';
		$longitude = isset($_POST['longitude'])?htmlspecialchars($_POST['longitude']):'';
		$latitude = isset($_POST['latitude'])?htmlspecialchars($_POST['latitude']):'';
		$page = isset($_POST['page'])?htmlspecialchars($_POST['page']):'1';
		$num = isset($_POST['num'])?htmlspecialchars($_POST['num']):'6';
		if(empty($system_user_id)&&empty($systemid)){
			$this->jsonUtils->echo_json_msg(4, '参数不全');exit();
		}
		if(empty($system_user_id)&&!empty($systemid)){
			$system_user_id =$systemid;
		}
		if(!empty($longitude)&&!empty($latitude)){
			$disable=true;
		}else{
			$disable=false;
		}
		$time = time();
		$db = M('AnswerUserRecent');
		$count = $db ->where(array('system_user_id'=>$system_user_id,'status'=>0))->count(); 
		$data = $db ->table(C('DB_PREFIX')."answer_user_recent as a")
		->join(C('DB_PREFIX')."system_user as b on b.id = a.system_user_id")
		->join(C('DB_PREFIX')."member as c on (c.id = b.sub_id and b.type = 0)")
		->where(array('a.system_user_id'=>$system_user_id,'a.status'=>0))
		->field('a.id,a.system_user_id,a.content,a.pics,a.addtime,a.laud_count,a.comment_count,a.longitude,a.latitude,b.name,b.header,b.brand_icon,c.gender')->page($page)->order('addtime desc')->limit($num)->select();
		if($data){
			if($disable){
				foreach ($data as $key =>$row){
					$data [$key]['addtime'] = dealtime($row['addtime']);
					$data [$key]['header'] = imgUrl($row['header']);
					$data [$key]['gender'] = !empty($row['gender'])?$row['gender']:'0';
					$data [$key]['brand_icon'] = imgUrl($row['brand_icon']);
					$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
					$data [$key]['distance'] = getDistance($longitude,$latitude,$row['longitude'],$row['latitude']);
					unset($data[$key]['longitude']);
					unset($data[$key]['latitude']);
				}
			}else{
				foreach ($data as $key =>$row){
					$data [$key]['addtime'] = date('Y-m-d H:i:s',$row['addtime']);
					$data [$key]['header'] = imgUrl($row['header']);
					$data [$key]['gender'] = !empty($row['gender'])?$row['gender']:'0';
					$data [$key]['brand_icon'] = imgUrl($row['brand_icon']);
					$data [$key]['pics'] = imgUrl(json_decode($row['pics'],true));
				}
			}
		}else{
			$data = array();
		}
		$arr['count'] = empty($count)?0:$count;
		$arr['list'] = $data;
		$this->jsonUtils->echo_json_data(0, 'ok', $arr);exit();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function test(){
		//商户给用户报价 jpush
				$jid = 12;
						//云推送
					$jpush = new \App\Model\JpushModel();
					$jpush->user = 0;
					$jpush ->push(2, array($jid), $data);
					//聊天内推送
// 					$xmpp = new \App\Model\XmppApiModel();
// 					$xmpp ->requestPush(2, array($jid), $data);
	
// 		//商户	//xmpp
// 		$jid = array(18);
// 		//用户发需求
// 		$jpush = D('Jpush');
// 		$jpush->user = 2;
// 		$jpush ->push(1, $jid, $data);
// 		$xmpp = new \App\Model\XmppApiModel();
// 		$xmpp ->requestPush(1, $jid, $data);
// 		//云推送
	}
	
}
?>