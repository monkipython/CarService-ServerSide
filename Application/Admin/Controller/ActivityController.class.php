<?php

class ActivityController extends CommonController {

    public function index() {
        $model = CM("Activity");
        $map = $this->_search("Activity");
        $this->assign("map", $map);

        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
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
            }
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
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }

        $this->assign('totalCount', $count);
        $pageNum = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
        $this->assign('numPerPage', $pageNum); //每页显示多少条
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
        cookie('_currentUrl_', __SELF__);
        return;
    }

    public function update() {
        $data['id'] = $_REQUEST['id'];
        $data['name'] = $_REQUEST['name'];
        $data['second_price'] = $_REQUEST['second_price'];
        $data['market_price'] = $_REQUEST['market_price'];
        $data['start_time'] = strtotime($_REQUEST['start_time']);
        $data['end_time'] = strtotime($_REQUEST['end_time']);
        $data['remain'] = $_REQUEST['remain'];
        $data['cart_model'] = $_REQUEST['cart_model'];
        $data['detail'] = $_REQUEST['detail'];
        $data['effect'] = $_REQUEST['effect'];
        $data['status'] = $_REQUEST['status'];
        if ($_REQUEST['pics']) {
            $data['pics'] = json_encode($_REQUEST['pics']);
        }
        
        $model = CM("Activity");
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

}
