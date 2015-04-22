<?php

class MerchantController extends CommonController {

    function _initialize() {
    	parent::_initialize();
        $model = M('city');
        $province = $model->where('pid=0')->getField('id,name');
        $this->assign("province", $province);
    }
	public function index(){
		$status =isset($_REQUEST['is_check'])?(int) $_REQUEST['is_check']:''; //2 未激活 1激活
		$keywords = empty($_REQUEST['keywords']) ?'':$_REQUEST['keywords'];
		$mobile = empty($_REQUEST['mobile']) ?'':$_REQUEST['mobile'];
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		$mstatus = empty($_REQUEST['status']) ?'' : $_REQUEST['status'];
		$db = M('Merchant');
		$map = array();
		$mapdata =array();
		
		if(!empty($status)){
			if($status==2){
				$statused = 0;
			}else{
				$statused = $status;
			}
			$map['is_check'] = $statused;
			$mapdata['a.is_check']= $statused;
		}
		if(!empty($mstatus)){
			if($mstatus==2){
				$mstatused = 0;
			}else{
				$mstatused = $mstatus;
			}
			$map['status'] = $mstatused;
			$mapdata['a.status']= $mstatused;
		}
		
		
		$mapPage = $map;
		if(!empty($keywords)){
			$map['merchant_name'] = array('like',"%$keywords%");
			$mapdata['a.merchant_name']= array('like',"%$keywords%");
			$mapPage['keywords']= $keywords;
		}
		if(!empty($mobile)){
			$map['mobile'] = array('like',"%$mobile%");
			$mapdata['a.mobile']= array('like',"%$mobile%");
			$mapPage['mobile']= $mobile;
		}
		
		$count = $db ->where($map)->count();
		$this->_page($count,$mapPage,$page,$num);
		
		$data = $db->table(C('DB_PREFIX')."merchant as a ")->join(C('DB_PREFIX')."merchant as b on a.check_by = b.id",'LEFT')
		->field('a.id,a.merchant_name,a.manager,a.tel,a.province_id,a.city_id,a.area_id,a.address,a.intro,a.mobile,a.status,a.is_check,a.addtime,ifnull(b.mobile,"管理员")  as salename')	
		->where($mapdata)->limit($num)->page($page)->order('addtime desc')->select();
		//dump($data);
		$this->assign('mstatus', $mstatus);
		$this->assign('keywords', $keywords);
		$this->assign('mobile', $mobile);
		$this->assign('status', $status);
		$this->assign('list', $data);
        $this->display();
	}
	public function setAcount(){
		$id = !empty($_REQUEST['id'])?htmlspecialchars($_REQUEST['id']):'';
		$status = !empty($_REQUEST['status'])?htmlspecialchars($_REQUEST['status']):'0';
		if(empty($id)){
			$this->error('id为空');
		}
		if($status != 1 &&$status != -1  &&$status != 0){
			//1为测试账号 0为正常 -1 为删除
			$this->error('status不正确');
		}
		$db = M('Merchant');
		$data = $db ->where(array('id'=>$id))->save(array('status'=>$status));
		if($data === false){
			$this->ajaxReturn(array('code'=>4,'msg'=>'操作失败'));exit();
		}else{
			if($status == -1){
				$session = M('MemberSession');
				$data = $session->where(array('userid'=>$id,'type'=>2))->delete();
				if($data === false){
					$this->error('用户未被T下线');
				}
			}
			$this->success('操作成功');
			
		}
	}
    public function add() {
        $model = M('Category');
        $category = $model->where('pid=0')->getField('id,name');
        $this->assign("category", $category);
        $this->display();
    }

    function insert() {
        $model = M("Merchant");
        $data['merchant_name'] = $_REQUEST['merchant_name'];
        $data['header'] = $_REQUEST['avatar'];
        $data['contact'] = $_REQUEST['contact'];
        $data['tel'] = $_REQUEST['tel'];
        $data['province_id'] = $_REQUEST['province_id'];
        $data['city_id'] = $_REQUEST['city_id'];
        $data['area_id'] = $_REQUEST['area_id'];
        $data['address'] = $_REQUEST['address'];
        $data['longitude'] = $_REQUEST['longitude'];
        $data['latitude'] = $_REQUEST['latitude'];
        $data['pcat_id'] = $_REQUEST['pcat_id'];
        $data['intro'] = $_REQUEST['intro'];
        $data['status'] = $_REQUEST['status'];
        $data['addtime'] = time();
        $data['pcat_id'] = json_encode($data['pcat_id']);
        //保存当前数据对象
        $list = $model->add($data);
        if ($list !== false) { //保存成功
            $this->success('新增成功!', cookie('_currentUrl_'));
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }

    public function edit() {
        $model = M("Merchant");
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $vo['header'] = imgUrl($vo['header']);
        $model = M('city');
        $cityArr = $model->where('pid=' . $vo['province_id'])->getField('id,name');
        $areaArr = $model->where('pid=' . $vo['city_id'])->getField('id,name');
        $city = "<option>请选择城市</option>";
        $area ="<option>请选择地区</option>";
        foreach ($cityArr as $key => $val) {
            if ($key == $vo['city_id']) {
                $city.="<option selected value='$key'>$val</option>";
            } else {
                $city.="<option value='$key'>$val</option>";
            }
        }
        foreach ($areaArr as $k => $v) {
            if ($k == $vo['area_id']) {
                $area.="<option selected value='$k'>$v</option>";
            } else {
                $area.="<option value='$k'>$v</option>";
            }
        }
        $model = M('Category');
        $cate = $model->where('pid=0')->getField('id,name');
        $pcatIdArr = json_decode($vo['pcat_id'], true);
        $category='';
        foreach ($cate as $key1 => $val1) {
            if (in_array($key1, $pcatIdArr)) {
                $category.="<input checked type='checkbox' name='pcat_id[]' value='' />$val1 ";
            } else {
                $category.="<input type='checkbox' name='pcat_id[]' value='' />$val1 ";
            }
        }
        $this->assign("category", $category);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('vo', $vo);
        $this->display();
    }

    function update() {
        $model = M("Merchant");
        $model->create();
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->success('编辑成功!', cookie('_currentUrl_'));
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }

    public function getcity() {
        $id = $_REQUEST['id'];
        $model = M('city');
        $city = $model->where('pid=' . $id)->getField('id,name');
        $this->ajaxReturn($city, "JSON");
    }

}
