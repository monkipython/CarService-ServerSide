<?php

// 后台用户模块
class UserController extends CommonController {

    
    public function index() {
    	$keywords = empty($_REQUEST['account']) ?'':$_REQUEST['account'];
    	$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
    	$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
    	$map['id'] = array('egt',2);
    	$mapdata['a.id'] = array('egt',2);
    	if(!empty($keywords)){
    		$map['account'] = array('like',"%".$keywords."%");
    		$mapdata['a.account'] = array('like',"%".$keywords."%");
    		$mapPage['account'] = $keywords;
    	}
    	
    	$db = M('User');
 		$data = $db->table(C('DB_PREFIX')."user as a ")->join(C('DB_PREFIX')."auth_group_access as b on a.id = b.uid",'LEFT')
 		->join(C('DB_PREFIX')."auth_group as c on c.id = b.group_id",'LEFT')->
 		where($mapdata)->field('a.*,b.group_id,c.title as group_name')->limit($num)->page($page)->select();
//  		echo $db ->getLastSql();
//  		dump($data);

 		$count = $db ->where($map)->count();
 		$this->_page($count,$mapPage,$page,$num);
    	$this->assign('list',$data);
    	$this->display();
    }
    
    

    // 检查帐号
    public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名第一位必须是字母，且5位以上！');
        }
        $User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
            $this->error('该用户名已经存在！');
        }else {
            $this->success('该用户名可以使用！');
        }
    }

    function add() {
    	$group = M('AuthGroup')->where(array('status'=>1))->select();
    	$this->assign('group',$group);
    	$this->display();
    }
    
    // 插入数据
    public function insert() {
        // 创建数据对象
        $group_id = $_POST['group_id'];
        $User	 =	 D("User");
        if(!$User->create()) {
            $this->error($User->getError());
        }else{
            // 写入帐号数据
            if($result	 =	 $User->add()) {
                $this->addRole($result,$group_id);
                $this->success('用户添加成功！');
            }else{
                $this->error('用户添加失败！');
            }
        }
    }

    protected function addRole($userId,$group_id) {
        $RoleUser = M("AuthGroupAccess");
        $arr['uid']	=	$userId;
        $arr['group_id']	=	$group_id;
        $RoleUser->add($arr);
//         echo $RoleUser->getLastSql();
    }

    //重置密码
    public function resetPwd() {
        $id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
            $this->error('密码不能为空！');
        }
        $User = M('User');
        $User->password	=	md5($password);
        $User->id			=	$id;
        $result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为$password");
        }else {
            $this->error('重置密码失败！');
        }
    }
    public function edit(){
    	$group = M('AuthGroup')->where(array('status'=>1))->select();
    	$db = M('User');
    	$data = $db->table(C('DB_PREFIX')."user as a ")->join(C('DB_PREFIX')."auth_group_access as b on a.id = b.uid",'LEFT')
 		->join(C('DB_PREFIX')."auth_group as c on c.id = b.group_id",'LEFT')->
 		where(array('a.id'=>$_GET['id']))->field('a.*,b.group_id,c.title as group_name')->find();
    	$this->assign('vo', $data);
    	$this->assign('group',$group);
    	$this->display();
    	
    }
    function update($dwz_db_name='') {
    	$group_id = $_POST['group_id'];
    	$uid = $_POST['id'];
    	$dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
    	$model = CM($dwz_db_name);
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	// 更新数据
    	$list = $model->save();
    	if (false === $list) {
    		//错误提示
    		$this->error('编辑失败!');
    
    	} else {
    		//成功提示
    		$this->editRole($uid, $group_id);
    		$this->success('编辑成功!', cookie('_currentUrl_'));
    
    	}
    }
    
    function editRole($uid,$group_id){
    	$RoleUser = M("AuthGroupAccess");
    	$RoleUser->where(array('uid'=>$uid))->save(array('group_id'=>$group_id));
    }
     
    
}