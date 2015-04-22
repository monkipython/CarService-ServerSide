<?php

class MerchantBiddingController extends CommonController {

    public function index() {
        $model = CM("merchant_bidding");
        $map = $this->_search("merchant_bidding");
        $merchantName = $_REQUEST['merchant_name'];
        if (!empty($merchantName)) {
            $merchantModel = M("Merchant");
            $merchant = $merchantModel->where("merchant_name='".$merchantName."'")->select();
            if($merchant){
                $map['merchant_id'] = $merchant[0]['id'];
            }else{
                $map['merchant_id'] = 0;
            }
        }
        $this->assign("map", $map);
        
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        $order = ' id desc';
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
            $demandModel = M('member_demand');
            foreach ($voList as &$row) {
                $demand = $demandModel->find($row['demand_id']);
                $row['member_id'] = $demand['member_id'];
                $row['title'] = $demand['title'];
                $row['desc'] = $demand['desc'];
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
        //囚鸟先生
        $this->assign('totalCount', $count);
        $pageNum = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
        $this->assign('numPerPage', $pageNum); //每页显示多少条
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
        cookie('_currentUrl_', __SELF__);
        return;
    }

    function edit() {
        $model = M("Merchant_bidding");
        $id = $_REQUEST ['id'];
        $vo = $model->find($id);
        $demandModel = M('member_demand');
        $demand = $demandModel->find($vo['demand_id']);
        $vo['member_id'] = $demand['member_id'];
        $vo['title'] = $demand['title'];
        $vo['desc'] = $demand['desc'];
        $this->assign('vo', $vo);
        $this->display();
    }

}
