<?php


class AuthRuleController extends CommonController{
	public function index(){
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		$model = M("AuthRule");
		$data = $model->order('category asc')->limit($num)->page($page)->select();
		$count = $model->count();
		$this->_page($count,array(), $page, $num);
		$this->assign('list',$data);
		$this->display();
	}
	public function edit(){
		$group = M('AuthRuleGroup')->select();
		$this->assign('group',$group);
		parent::edit('AuthRule');
	}
	public function add(){
		$group = M('AuthRuleGroup')->select();
		$this->assign('group',$group);
		parent::add();
	}
	public function update(){
		parent::update('AuthRule');
	}
	public function insert(){
		
		parent::insert('AuthRule');
	}
// 	public function test(){
		
// 		$data = getDistance(24.530147, 118.111616, 24.481367, 118.157685);
// 		dump($data);
// 	}
	
	
}


