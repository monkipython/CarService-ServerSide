<?php
use Think\Controller;
use Org\Util\Rbac;

class PublicController extends Controller {

    // 检查用户是否登录
    protected function checkUser() {
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->error('没有登录','Public/login/');
        }
    }

    // 顶部页面
    public function top() {
        C('SHOW_RUN_TIME',false);			// 运行时间显示
        C('SHOW_PAGE_TRACE',false);
        $model	=	M("Group");
        $list	=	$model->where('status=1')->getField('id,title');
        $this->assign('nodeGroupList',$list);
        $this->display();
    }

    public function drag(){
        C('SHOW_PAGE_TRACE',false);
        C('SHOW_RUN_TIME',false);			// 运行时间显示
        $this->display();
    }

    // 尾部页面
    public function footer() {
        C('SHOW_RUN_TIME',false);			// 运行时间显示
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    
    // 百度地图
    public function baidumap() {
        $this->display();
    }
    

    // 后台首页 查看系统信息
    public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
        $this->display();
    }

    // 用户登录页面
    public function login() {
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->display();
        }else{
            $this->redirect('Index/index');
        }
    }

    public function index() {
        //如果通过认证跳转到首页
        redirect(__MODULE__);
    }

    // 用户登出
    public function logout() {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
            $this->redirect('Public/login');
        }else {
            $this->error('已经登出！');
        }
    }

    // 登录检测
    public function checkLogin() {
        if(empty($_POST['account'])) {
            $this->error('帐号错误！',__CONTROLLER__.'/login',2);
        }elseif (empty($_POST['password'])){
            $this->error('密码必须！',__CONTROLLER__.'/login',2);
        }elseif (empty($_POST['verify'])){
            $this->error('验证码必须！',__CONTROLLER__.'/login',2);
        }
        //生成认证条件
        $map            =   array();
        // 支持使用绑定帐号登录
        $map['account']	= $_POST['account'];
        $map["status"]	=	array('gt',0); 		

        //3.2.1 的 验证码 检验方法
        $verify = $_POST['verify'] ;
        if(!$this->check_verify($verify)){
            $this->error('验证码输入错误！',__CONTROLLER__.'/login',2);
        }           
 
        $authInfo = Rbac::authenticate($map);
        //使用用户名、密码和状态 的方式进行认证
        if(false === $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != md5($_POST['password'])) {
                $this->error('密码错误！',__CONTROLLER__.'/login',2);
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['loginUserName']		=	empty($authInfo['nickname'])?$authInfo['account']:$authInfo['nickname'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
            $_SESSION['login_count']	=	$authInfo['login_count'];
            if($authInfo['account']=='admin') {
                $_SESSION['administrator']		=	true;
            }


//             $log['vc_operation']="用户登录：登录成功！";
//             $log['vc_module']="系统管理";
//             $log['creator_id']=$authInfo['id'];
//             $log['creator_name']=$authInfo['account'];
//             $log['vc_ip']=get_client_ip();
//             $log['createtime']=time();
//             M("Log")->add($log);


            //保存登录信息
            $User	=	M('User');
            $ip		=	get_client_ip();
            $time	=	time();
            $data = array();
            $data['id']	=	$authInfo['id'];
            $data['last_login_time']	=	$time;
            $data['login_count']	=	array('exp','login_count+1');
            $data['last_login_ip']	=	$ip;
            $User->save($data);

            // 缓存访问权限
            //RBAC::saveAccessList();
            $this->redirect('Admin/Index/index');

        }
    }
    
    // 更换密码
    public function changePwd() {
        $this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
        //3.2.1 的 验证码 检验方法
        $verify = $_POST['verify'] ;
        if(!$this->check_verify($verify)){
            $this->error('验证码输入错误！');
        } 
        $map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
            $User->password	=	pwdHash($_POST['password']);
            $User->save();
            $this->success('密码修改成功！');
         }
    }
    
    // 用户资料
    public function profile() {
        $this->checkUser();
        $User	 =	 M("User");
        $vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
        $this->assign('vo',$vo);
        $this->display();
    } 
	
    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串	  
	public function check_verify($code, $id = ''){
		$verify = new \Think\Verify();
		return $verify->check($code, $id);
	}	
	
	//生成  验证码 图片的方法
	public function verify() {             
        //3.2.1  中的生成 验证码 图片的方法        
        $Verify = new \Think\Verify();
        // 设置验证码字符为纯数字
        $Verify->codeSet = '0123456789'; 
        $Verify->length   = 4;
        $Verify->entry();                      
    }	
	
    // 修改资料
    public function change() {
        $this->checkUser();
        $User	 =	 D("User");
        if(!$User->create()) {
            $this->error($User->getError());
        }
        $result	=	$User->save();
        if(false !== $result) {
            $this->success('资料修改成功！');
        }else{
            $this->error('资料修改失败!');
        }
    }

    public function nav(){
        $volist=M("GroupClass")->where(array('status'=>1))->order("sort desc, id desc")->select();
        $this->volist=$volist;
        $this->display();
    }
    /**
     * 切换模拟用户
     */
    public function changeUser(){
    	$keywords = isset($_POST['keywords'])?htmlspecialchars($_POST['keywords']):'';
    	$map = array();
    	$map['id'] = array(array('gt','27'),array('lt','228'));
    	if(!empty($keywords)){
    		$map['name']=array('like',"%$keywords%");
    		$this->assign('keywords',$keywords);
    	}
    	$db = M('SystemUser');
    	$data = $db ->where($map)->field('id,type,name,phone')->select();
    	$currentUser = $_SESSION['currentUser'];
    	$this->assign('currentUser',$currentUser);
    	$this->assign('user',$data);
    	$this->display();
    }
    /**
     * 模拟用户 生成session
     */
    public function ajaxChangeUser(){
    	$id = isset($_POST['id']) ?htmlspecialchars($_POST['id']):'';
    	if(empty($id)){
    		$this->ajaxReturn(array('code'=>1,'msg'=>'未选择用户'),'json');exit();
    	}else{
    		$db = M('SystemUser');
    		$data = $db ->field('id,type,name,phone')->find($id);
    		if($data){
    			$_SESSION['currentUser'] = $data;
    			$this->ajaxReturn(array('code'=>0,'msg'=>'成功','data'=>$data),'json');exit();
    		}else{
    			$this->ajaxReturn(array('code'=>1,'msg'=>'用户不存在'),'json');exit();
    		}
    	}
    }
    /**
     * 上传图片(使用session暂存图片信息，仅供新增使用)
     */
    public function uploadPic(){
    	$type = $_REQUEST['type'];
    	$config = array(
    			'1'=>array('/Answer/','pics_answer',1),
    			'2'=>array('/Recent/','pics_recent',2)
    	);
    	if(empty($type)||empty($config[$type])){
    		die(json_encode(array("code"=>4,'msg'=>'请联系管理员')));	exit();
    	}
    	
    	if (!empty($_FILES)) {
    		$arr = mul_upload ( $config[$type][0] ,$config[$type][2]);
    		if ($arr) {
    			// 				 $arr = imgUrl($arr);
    			$pic = $config[$type][1];
    			$_SESSION[$pic][]=$arr[0];
    			die(json_encode(array("code"=>0,'msg'=>'ok','data'=>$arr[0])));exit();
    		}
    
    	}else{
    		die(json_encode(array("code"=>4,'msg'=>'请选择上传图片')));	exit();
    	}
    
    }
    /**
     * 上传图片，仅供图片修改删除 新增 使用，（未存储session，由js确定最终数据）
     */
    public function uploadPicByEdit(){
    	$type = $_REQUEST['type'];
    	$config = array(
    			'1'=>array('/Answer/','pics_answer',1),
    			'2'=>array('/Recent/','pics_recent',2)
    	);
    	if(empty($type)||empty($config[$type])){
    		die(json_encode(array("code"=>4,'msg'=>'请联系管理员')));	exit();
    	}
    	 
    	if (!empty($_FILES)) {
    		$arr = mul_upload ( $config[$type][0] ,$config[$type][2]);
    		if ($arr) {
    			$arr = imgUrl($arr);
    			//$pic = $config[$type][1];
    			//$_SESSION[$pic][]=$arr[0];
    			die(json_encode(array("code"=>0,'msg'=>'ok','data'=>$arr[0])));exit();
    		}
    
    	}else{
    		die(json_encode(array("code"=>4,'msg'=>'请选择上传图片')));	exit();
    	}
    
    }
    public function uploadHeader(){
//     	$this->saveName(4,0,'');
    }
    
    /**
     * 修改用户名 密码 更新system——user 表
     *
     */
    static public function saveHeader($sub_id,$type,$header){
    	$db  = M('SystemUser');
    	$id = $db ->where(array('type'=>$type,'sub_id'=>$sub_id))->getField('id');
    	$data = $db ->where(array('id'=>$id))->save(array('header'=>$header));
    	set_time_limit(20);
    	$xmpp = new \App\Model\XmppApiModel($id,$id);
    	$xmpp->updataHeader($header);
    	//     	dump('123');
    	return $data;
    }
    /**
     * 修改header 更新system——user 表
     */
     public function saveName($sub_id,$type,$name){
    	$db  = M('SystemUser');
    	$id = $db ->where(array('type'=>$type,'sub_id'=>$sub_id))->getField('id');
    	$data = $db ->where(array('id'=>$id))->save(array('name'=>$name));
    	$rel = M('');
    	$sql= "update ofUser set name ='$name' where username = $id ";
    	$row = $rel->db(1,"mysql://".C('DB_USER').":".C('DB_PWD')."@localhost:3306/chatDB")->execute($sql);
    	return $data;
    }
    
    
}