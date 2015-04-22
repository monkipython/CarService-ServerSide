<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 囚鸟先生
// +----------------------------------------------------------------------
// $Id: ArticleController.class.php 2014-05-17 23:58:02 $
use Think\Controller;
use Org\Util\Rbac;

class CommonController extends Controller {

    function _initialize() {
        // 用户权限检查
       if(!empty($_SESSION[C('USER_AUTH_KEY')])){
	       	//权限验证 基于Auth 过滤admin 超级唯一最高权限用户
	       	if($_SESSION['administrator']){
	       		return ;
	       	}else{
	       		//首页框架无需验证
	       			$name = ACTION_NAME;
	       			switch ($name){
	       				case 'insert':
	       					$name = 'add';
	       					break;
	       				case 'update':
	       					$name = 'edit';
	       				default:
	       					break;
	       					
	       			}
	       			$currentDetail = CONTROLLER_NAME.'_all';
	       			$current = CONTROLLER_NAME.'_'.$name;
	       			$noAuth = array('Index_index','Merchant_getcity');
	       			if(!in_array($current, $noAuth)){
		       			$auth=new \Think\Auth();
		       			$arr = $auth->check($current.','.$currentDetail,$_SESSION[C('USER_AUTH_KEY')]);
		       			if(!$arr){
		       				$this->error('你无改权限操作该界面');exit();
		       			} else{
// 		       				$session_auth [] = $current;
		       			}
	       			}
	       		}
	      
	       	
       }else{
//        		$this->error('登录过期',__ROOT__.C('USER_AUTH_GATEWAY'));
       		$this->redirect(__ROOT__.C('USER_AUTH_GATEWAY'));
       }
	
		
    	
    }

    /**
     * Dwz系统用到参数  ajax赋值扩展
     * @access protected
     * @param array $result  引用传值
     * @author 囚鸟先生
     * @return void
     */
    protected function ajaxAssign(&$result) {
        $result['statusCode'] = $result['status'];
        $result['navTabId'] = $_REQUEST['navTabId'];
        $result['message'] = $result['info'];
        $result['callbackType'] = isset($_REQUEST['callbackType'])?$_REQUEST['callbackType']:'';
    }

    /**
     * Dwz系统用到参数  ajax赋值扩展 ,继承Controller的ajaxReturn
     * @access protected
     * @param array $data,$type  引用传值
     * @author 东升
     * @return void
     */
    protected function ajaxReturn($data, $type = '') {
        //囚鸟先生
        if (method_exists($this, "ajaxAssign"))
            $this->ajaxAssign($data); //ajax赋值扩展
        parent::ajaxReturn($data, $type);//调用Thinkphp的ajaxReturn
    }

    /**
      +----------------------------------------------------------
     * Index页显示
     * 
     */
    public function index($dwz_db_name='') {
        //列表过滤器，生成查询Map对象
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
//        echo $dwz_db_name."\n";
        //   echo '执行'.CONTROLLER_NAME.'控制器的'.ACTION_NAME.'操作';
        $model = CM($dwz_db_name);
//         dump($model);
        $map = $this->_search($dwz_db_name);
        $this->assign("map", $map);
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        
        $this->display();
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $dwz_db_name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($dwz_db_name = '') {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        //生成查询条件
        $model = CM($dwz_db_name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
      
        return $map;
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _list($model, $map, $sortBy = '', $asc = false) {

        if(!empty($sortBy)){
        	$order = $sortBy;
        	if($asc){
        		$order .=' asc';
        	}else{
        		$order .= ' desc';
        	}
        }else{
        	//排序字段 默认为主键名
        	$order = $model->getPk() . ' desc';
        }
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            //创建分页对象  ,默认10条记录
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '10';
            }
            $p = new Think\Page($count, $listRows);
            $pageNum = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
            //分页查询数据                  
            $voList = $model->where($map)->order($order)->limit($pageNum)->page($_REQUEST[C('VAR_PAGE')])->select();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
//            dump($voList);
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        //囚鸟先生
        $this->assign('totalCount', $count);
        $pageNum = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
        $this->assign('numPerPage', $pageNum); //每页显示多少条
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
        cookie('_currentUrl_', __SELF__);
        return $voList;
    }

    function insert($dwz_db_name='') {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $model = CM($dwz_db_name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
   
        if ($list === false) { 
        	//失败提示
        	$this->error('新增失败!');
       
        } else {
        	//保存成功
        	$this->success('新增成功!');
        }
    }

    function add() {
        $this->display();
    }

    function edit($dwz_db_name='') {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $model = M($dwz_db_name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
//         dump($vo);
        $this->assign('vo', $vo);
        $this->display();
    }

    function update($dwz_db_name='') {
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
        	$this->success('编辑成功!', cookie('_currentUrl_'));
            
        }
    }

    /**
      +----------------------------------------------------------
     * 默认删除操作
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public function _delete($dwz_db_name) {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        //删除指定记录         
        $model = M($dwz_db_name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->setField('status', -1);
                if ($list !== false) {
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    public function foreverdelete() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        //删除指定记录         
        $model = CM($dwz_db_name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete()) {
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }

    /**
      +----------------------------------------------------------
     * 添加删除操作  (多个删除)
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public function delAll() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $model = CM($name);
        $pk = $model->getPk();
        $data[$pk] = array('in', $_POST['ids']);
        $model->where($data)->delete();
        $this->success('批量删除成功');
    }

    public function _clear() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        //删除指定记录         
        $model = CM($dwz_db_name);
        if (!empty($model)) {
            if (false !== $model->where('status=1')->delete()) {
                $this->success(L('_DELETE_SUCCESS_'), cookie('_currentUrl_'));
            } else {
                $this->error(L('_DELETE_FAIL_'));
            }
        }
        $this->forward();
    }

    /**
      +----------------------------------------------------------
     * 默认禁用操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    public function forbid() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $model = CM($dwz_db_name);
        $pk = $model->getPk();
        $id = $_REQUEST [$pk];
        $condition = array($pk => array('in', $id));
        $list = $model->forbid($condition);
        if ($list !== false) {
            $this->success('状态禁用成功', cookie('_currentUrl_'));
        } else {
            $this->error('状态禁用失败！');
        }
    }

    public function checkPass() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $model = CM($dwz_db_name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->checkPass($condition)) {
            $this->success('状态批准成功！', cookie('_currentUrl_'));
        } else {
            $this->error('状态批准失败！');
        }
    }

    public function recycle() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $model = CM($dwz_db_name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->recycle($condition)) {
            $this->success('状态还原成功！', cookie('_currentUrl_'));
        } else {
            $this->error('状态还原失败！');
        }
    }

    public function _recycleBin() {
    	$dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        $map = $this->_search();
        $map ['status'] = - 1;

        $model = CM($dwz_db_name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    /**
      +----------------------------------------------------------
     * 默认恢复操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    function resume() {
        $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
        //恢复指定记录         
        $model = CM($dwz_db_name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition)) {
            $this->success('状态恢复成功！', cookie('_currentUrl_'));
        } else {
            $this->error('状态恢复失败！');
        }
    }

    function _saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty($seqNoList)) {
            $dwz_db_name = $dwz_db_name ? $dwz_db_name : strtolower(CONTROLLER_NAME);
            //更新数据对象             
            $model = CM($dwz_db_name);
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                if (!$result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if ($result !== false) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }
    /**
     * 分页——
     */
	function _page($count,$map=array(),$page,$num){
		if($count>0){
			$this->assign('map', $map);
			$map[C('VAR_PAGE')]=$page;
			$map['numPerPage']=$num;
			$Page = new \Think\Page ( $count, $num,$map); // 实例化分页类
			$p = $Page->show (); // 分页显示输出
			$this->assign("page", $p);
			$this->assign('totalCount', $count);
			$this->assign('numPerPage', $num); //每页显示多少条
			$this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
		//	cookie('_currentUrl_', __SELF__);
		}else{
			return;
		}
		
		
		
	}
	/**
	 * curl
	 */
	public function curl($url,$data){
	
		$ch = curl_init ();
			
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt(  $ch, CURLOPT_TIMEOUT, 5);
			
		curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ( "Expect: ") );
		curl_setopt ( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		$ret = curl_exec ( $ch );
		$curl_errno = curl_errno($ch);
		$info = curl_getinfo($ch);
		curl_close ( $ch );
			
		if($curl_errno > 0){
			$error =  'errorno '.$curl_errno;
			die(json_encode(array('code'=>404,'msg'=>$error)));
		}
		// 		 dump($ret);
		$data = json_decode($ret,true);
		if($data['code'] == 2){
			header("Content-type:text/html;charset=utf-8");
			//清楚模拟用户的session
			$_SESSION['currentUser'] = '';
			$this->error('用户登录过期');
		}else{
			return $data;
		}
			
	
	}
	/**
	 * getCurrentUser 获取当前session 的模拟用户
	 */
	public function getCurrentUser(){
		$user = $_SESSION['currentUser']['id'];
		if(empty($user)){
			$this->error('请选择模拟用户');
		}else{
			return $user;
		}
	}
}
