<?php


class AuthGroupController extends CommonController{
	public function index(){
		$model = M("AuthGroup");
		$map = $this->_search("AuthGroup");
		$this->assign("map", $map);
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if (!empty($model)) {
			$this->_list($model, $map,'id',true);
		}
		$this->display();
	}
	public function add(){
		$rules = M('AuthRule')->where(array('status'=>1))->select();
		$group = M('AuthRuleGroup')->select();
		foreach ($group as $key =>$row){
			foreach ($rules as $k =>$r){
				if($r['category'] == $row['id']){
					$group [$key]['child'][] = $r;
				}
			}
		}
// 		dump($group);
		$this->assign('rules',$group);
		parent::add('AuthGroup');
	}
	public function edit(){
		$rules = M('AuthRule')->where(array('status'=>1))->select();
		$model = M('AuthGroup');
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$select = explode(',',$vo['rules']);
		$status = 0;
		foreach ($rules as $key =>$row){
			if(in_array($row['id'], $select)){
				$rules[$key]['selected'] = 1;
			}else{
				$rules[$key]['selected'] = 0;
			}
		}
		$group = M('AuthRuleGroup')->select();
		foreach ($group as $key =>$row){
			foreach ($rules as $k =>$r){
				if($r['category'] == $row['id']){
					$group [$key]['child'][] = $r;
				}
			}
		}
		$this->assign('rules',$group);
		$this->assign('vo', $vo);
		$this->display();
	}
	public function update(){
		$_POST['rules'] = implode(',',$_POST['rules']);
// 		dump($_POST);
		parent::update('AuthGroup');
	}
	public function insert(){
		$rule = $_POST['rules'];
		$_POST['rules'] = implode(',', $rule);
	
		parent::insert('AuthGroup');
	}
	
	
}


