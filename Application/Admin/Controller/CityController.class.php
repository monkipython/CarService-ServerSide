<?php

class CityController extends CommonController {

    function _initialize() {
    	parent::_initialize();
        $model = M('city');
        $province = $model->where('pid=0 and status=>1')->getField('id,name');
        $this->assign("province", $province);
    }

    public function index() {
        $model = CM("City");
        $map = $this->_search("City");
//         $pid = $_REQUEST['pid'] ? $_REQUEST['pid'] : 0;
//         $map['pid'] = $pid;
        $this->assign("map", $map);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    public function changeStatus(){
    	$id = !empty($_REQUEST['id']) ?htmlspecialchars($_REQUEST['id']):'';
    	if(empty($id)){
    		$this->error('id为空');
    	}
    	$db = M('City');
    	$status = $db ->where(array('id'=>$id))->getField('status');
    	if($status == 1){
    		$reset  = 0;
    	}else{
    		$reset = 1;
    	}
    	$data = $db ->where(array('id'=>$id))->save(array('status'=>$reset));
    	if($data === false){
    		$this->error('操作失败');
    	}else{
    		$this->success('操作成功');
    	}
    }
    
    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        $order = 'id asc';
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

            foreach ($voList as &$row) {
                if ($row['province'] > 0) {
                    $province = $model->find($row['province']);
                    $row['province_name'] = $province['name'];
                }
                if ($row['city'] > 0) {
                    $city = $model->find($row['city']);
                    $row['city_name'] = $city['name'];
                }
            }
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign("page", $page);
        }

        $this->assign('totalCount', $count);
        $pageNum = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
        $this->assign('numPerPage', $pageNum); //每页显示多少条
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
        cookie('_currentUrl_', __SELF__);
        return;
    }

    function insert() {
        $model = M("City");
        $data['name'] = $_REQUEST['name'];
        $data['province'] = $_REQUEST['province_id'];
        $data['city'] = $_REQUEST['city_id'];
        $data['pid'] = 0;
        if ($data['city'] > 0) {
            $data['pid'] = $data['city'];
        } elseif ($data['province'] > 0) {
            $data['pid'] = $data['province'];
        }
        $list = $model->add($data);
        if ($list !== false) {
            $this->success('新增成功!', cookie('_currentUrl_'));
        } else {
            $this->error('新增失败!');
        }
    }

    function edit() {
        $model = M("City");
        $id = $_REQUEST ["id"];
        $vo = $model->getById($id);
        $cityArr = $model->where('pid=' . $vo['province'])->getField('id,name');
        $city = "";
        foreach ($cityArr as $key => $val) {
            if ($key == $vo['city']) {
                $city.="<option selected value='$key'>$val</option>";
            } else {
                $city.="<option value='$key'>$val</option>";
            }
        }
        $this->assign('vo', $vo);
        $this->assign('city', $city);
        $this->display();
    }

}
