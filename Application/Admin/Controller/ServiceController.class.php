<?php

class ServiceController extends CommonController {

    function _initialize() {
        $cateModel = M('Category');
        $pcat = $cateModel->where('pid=0')->getField('id,name');
        $model = M('city');
        $province = $model->where('pid=0')->getField('id,name');
        $this->assign("province", $province);
        $this->assign("pcat", $pcat);
    }

    public function index() {
        $model = CM("Service");
        $map = $this->_search("Service");
        $this->assign("map", $map);

        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    public function insert() {
        $model = CM("Service");

        $data['name'] = $_REQUEST['name'];
        $data['merchant_id'] = $_REQUEST['merchant_id'];
        $data['province_id'] = $_REQUEST['province_id'];
        $data['city_id'] = $_REQUEST['city_id'];
        $data['area_id'] = $_REQUEST['area_id'];
        $data['pcat_id'] = $_REQUEST['pcat_id'];
        $data['cat_id'] = $_REQUEST['cat_id'];
        $data['intro'] = $_REQUEST['intro'];
        $data['price'] = $_REQUEST['price'];
        $data['timeout'] = $_REQUEST['timeout'];
        $data['pics'] = json_encode($_REQUEST['pics']);
        $data['addtime'] = time();
        $list = $model->add($data);
        if ($list !== false) {
            $this->success('新增成功!', cookie('_currentUrl_'));
        } else {
            $this->error('新增失败!');
        }
    }

    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        $order = $model->getPk() . ' desc';
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
            $merchantModel = M('Merchant');
            foreach ($voList as &$row) {
                $merchant = $merchantModel->find($row['merchant_id']);
                $row['merchant_name'] = $merchant['merchant_name'];
                $row['merchant_tel'] = $merchant['tel'];
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

    public function edit() {
        $model = M("Service");
        $id = $_REQUEST ['id'];
        $vo = $model->getById($id);
        $vo['pics'] = json_decode($vo['pics'], true);
        $model = M('city');
        $cityArr = $model->where('pid=' . $vo['province_id'])->getField('id,name');
        $areaArr = $model->where('pid=' . $vo['city_id'])->getField('id,name');
        $city = $area = $cat = "";
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
        $cateArr = $model->where('pid=' . $vo['pcat_id'])->getField('id,name');
        foreach ($cateArr as $ke => $value) {
            if ($ke == $value['cat_id']) {
                $cate.="<option selected value='$ke'>$value</option>";
            } else {
                $cate.="<option value='$ke'>$value</option>";
            }
        }
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('cate', $cate);
        $this->assign('vo', $vo);
        $this->display();
    }

    function update() {
        $model = CM("Service");
        $data['id'] = $_REQUEST['id'];
        $data['name'] = $_REQUEST['name'];
        $data['merchant_id'] = $_REQUEST['merchant_id'];
        $data['province_id'] = $_REQUEST['province_id'];
        $data['city_id'] = $_REQUEST['city_id'];
        $data['area_id'] = $_REQUEST['area_id'];
        $data['pcat_id'] = $_REQUEST['pcat_id'];
        $data['cat_id'] = $_REQUEST['cat_id'];
        $data['intro'] = $_REQUEST['intro'];
        $data['price'] = $_REQUEST['price'];
        $data['timeout'] = $_REQUEST['timeout'];
        if ($_REQUEST['pics']) {
            $data['pics'] = json_encode($_REQUEST['pics']);
        }

        // 更新数据
        $list = $model->save($data);
        if (false !== $list) {
            //成功提示
            $this->success('编辑成功!', cookie('_currentUrl_'));
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }

    public function getcat() {
        $id = $_REQUEST['id'];
        $model = M('Category');
        $cate = $model->where('pid=' . $id)->getField('id,name');
        $this->ajaxReturn($cate, "JSON");
    }

}
