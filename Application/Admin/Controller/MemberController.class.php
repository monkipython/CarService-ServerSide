<?php

class MemberController extends CommonController {

	public function index(){
		$mobile = empty($_REQUEST['mobile']) ?'':$_REQUEST['mobile'];
		$keywords = empty($_REQUEST['keywords']) ?'':$_REQUEST['keywords'];
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		
		if($mobile){
			$map['mobile']=$mobile;
			$mapPage['mobile'] = $mobile;
			$this->assign('mobile',$mobile);
		}
		$mapPage = $map;
		if($keywords){
			$map['nick_name'] = array('like',"%$keywords%");
			$mapPage['keywords'] = $keywords;
			$this->assign('keywords',$keywords);
		}
		
		$db = M('Member');
		$count = $db->where($map) ->count();
		$data = $db->where($map)->order('id desc')->limit($num)->page($page)->select();
		foreach ($data as $key=>$row){
			$data[$key]['header'] = imgUrl($row['header']);
		}
	
	
		$this->_page($count,$mapPage,$page,$num);
		$this->assign('list',$data);
		$this->display();
	}

}
