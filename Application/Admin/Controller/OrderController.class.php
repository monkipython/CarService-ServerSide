<?php

class OrderController extends CommonController {

    public function index() {
        $model = M("Order");
        $map = $this->_search("Order");
        $this->assign("map", $map);
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }



//     function edit() {
//         $model = M("Member_demand");
//         $id = $_REQUEST ["id"];
//         $vo = $model->getById($id);
//         $cartModel = M("Cart");
//         $cart = $cartModel->find($vo['cart_id']);
//         $vo['cart_brand'] = $cart['cart_brand'];
//         $vo['cart_model'] = $cart['cart_model'];
//         $vo['color'] = $cart['color'];
//         $vo['output'] = $cart['output'];
//         $vo["pics"] = json_decode($vo['pics'], true);
//         $this->assign('vo', $vo);
//         $this->display();
//     }

}
