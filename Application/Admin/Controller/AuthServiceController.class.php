<?php
use Home\Controller\CurlController;
class AuthServiceController extends CommonController{
    

	public function index(){
        $auth = M('')->table(C('DB_PREFIX')."service as a ")
        ->join(C('DB_PREFIX')."merchant as b on b.id = a.merchant_id")
        ->where(array('a.effect'=>0))->field('a.id,a.name,b.merchant_name,a.price,a.timeout,a.addtime,a.intro')->order('a.addtime desc')->select();
        $count = M('Service')->where(array('effect'=>0))->count();
        $this->assign('totalCount',$count);
        $this->assign('list',$auth);
        $this->display();
	}
	public function indexHistory(){
		$type =isset($_REQUEST['type'])?(int) $_REQUEST['type']:'1';
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$id = !empty($_REQUEST['merchant_id'])?(int) $_REQUEST['merchant_id']:'';
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		$map['a.effect']=$type;
		$mapcount['effect'] = $type;
		if(!empty($id)){
			$map['a.merchant_id'] =$id;
			$mapcount ['merchant_id'] = $id;
		}
		$auth = M('')->table(C('DB_PREFIX')."service as a ")
		->join(C('DB_PREFIX')."merchant as b on b.id = a.merchant_id")
		->where($map)->field('a.id,a.name,b.merchant_name,a.price,a.timeout,a.addtime')->order('a.addtime desc')->limit($num)->page($page)->select();
		$count = M('Service')->where($mapcount)->count();
		
		$mapPage['type'] = $type;
		if(!empty($id)){
			$mapPage['merchant_id'] = $id;
		}
		$this->assign('merchant_id',$id);
		$this->_page($count,$mapPage,$page,$num);
		$this->assign('type',$type);
		$this->assign('count',$count);
		$this->assign('list',$auth);
		$this->display();
	}
	function edit(){
		$db = M('Auth');
		$data =$db-> where(array('id'=>$_GET['id']))->find();
		$data['check_data'] = json_decode($data['check_data'],true);
		$data['check_data']['header'] = imgUrl($data['check_data']['header']);
		$url = "http://121.40.92.53/ycbb/index.php/App/City/city_list";
		$province = CurlController::curl($url, array());
		$city = CurlController::curl($url, array('pid'=>$data['check_data']['province_id']));
		$area = CurlController::curl($url, array('pid'=>$data['check_data']['city_id']));
		$this->assign('vo',$data);
		$this->assign('province',$province['data']['list']);
		$this->assign('city',$city['data']['list']);
		$this->assign('area',$area['data']['list']);
		$this->display();
	}
	function execAction(){
		$id = isset($_GET['id'])?htmlspecialchars($_GET['id']):'';
		if(!$id){
			$this->error('id为空');
		}
		$db = M('Service');
		$data = $db->where(array('id'=>$id))->save(array('effect'=>1));
		if($data === false){
			$this->error('审核失败');exit();	
		}else{
			$this->success('审核成功');exit();
		}
	}
	function nopermit(){
		$id = isset($_GET['id'])?htmlspecialchars($_GET['id']):'';
		if(!$id){
			$this->error('id为空');
		}
		$db = M('Service');
		$data = $db->where(array('id'=>$id))->save(array('effect'=>-1));
		if($data === false){
			$this->error('提交失败');exit();
		}else{
			$this->success('提交成功');exit();
		}
	}
	
    
}


