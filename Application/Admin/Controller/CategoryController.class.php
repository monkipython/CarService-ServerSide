<?php


class CategoryController extends CommonController{
    
    function _initialize() {
    	parent::_initialize();
        $cate = M('Category')->where(array('status'=>1,'pid'=>0))->select();
        $this->assign('cate',$cate);  
    }

	public function index(){
		
        $category = M('Category')->where(array('status'=>1))->select();
        $tmp = array();
        foreach($category as $val){
            $tmp[$val['id']]= $val['name'];   
        }
        
        $this->category=$tmp;
		parent::index();
	}
    
}


