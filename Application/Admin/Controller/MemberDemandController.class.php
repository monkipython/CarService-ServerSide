<?php

class MemberDemandController extends CommonController {

    public function index() {
        $model = M("MemberDemand");
        $map = $this->_search("MemberDemand");
        $page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
        $num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
        $url = 'http://caryu.net/index.php/Admin/MemberDemand/index/';
        
     
        if(!empty($page)){
        	$url.=C('VAR_PAGE').'/'.$page.'/';
        }
        if(!empty($num)){
        	$url.='numPerPage/'.$num;
        }
        
        $this->assign('furl', base64_encode(urlencode($url)));
        $this->assign("map", $map);
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    protected function _list($model, $map, $sortBy = '', $asc = false) {
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
            $cartModel = M('Cart');
            foreach ($voList as &$row) {
                $cart = $cartModel->find($row['cart_id']);
                $row['cart_brand'] = $cart['cart_brand'];
                $row['cart_model'] = $cart['cart_model'];
                $row['color'] = $cart['color'];
                $row['output'] = $cart['output'];
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
//             dump($voList);
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
        $model = M("Member_demand");
        $id = $_REQUEST ["id"];
        $vo = $model->getById($id);
        $cartModel = M("Cart");
        $cart = $cartModel->find($vo['cart_id']);
        $vo['cart_brand'] = $cart['cart_brand'];
        $vo['cart_model'] = $cart['cart_model'];
        $vo['color'] = $cart['color'];
        $vo['output'] = $cart['output'];
        $vo["pics"] = json_decode($vo['pics'], true);
        $this->assign('vo', $vo);
        $this->display();
    }

}
