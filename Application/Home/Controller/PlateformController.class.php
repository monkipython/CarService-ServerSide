<?php
namespace Home\Controller;
use Think\Controller;
class PlateformController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->session = $_SESSION ['user'];
		
		if (empty ( $this->session ['mer_session_id'] )) {
			
			$this->error ( '请登录！', "/", 2 );
		}
	
	}
	public function unbid() {
		$page = isset ( $_GET ['p'] ) ? htmlspecialchars ( $_GET ['p'] ) : '1';
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$url = C ( 'CURL_POST_URL' ) . "MerDemand/member_demand_list";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'page' => $page,
				'num' => $num,
				'type' => 1 
		);
		$rel = $this->curl ( $url, $param );

		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num 
		) ); // 实例化分页类
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'pages', $paginate );
		$this->assign ( 'data', $rel ['data'] ['list'] );
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->display ( "BidHistory/unbid" );
	}
	public function bid() {
		$page = isset ( $_GET ['p'] ) ? htmlspecialchars ( $_GET ['p'] ) : '1';
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$url = C ( 'CURL_POST_URL' ) . "MerDemand/member_demand_list";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'page' => $page,
				'num' => $num,
				'type' => 2 
		);
		$rel = $this->curl ( $url, $param );
		// dump($rel);
		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num 
		) ); // 实例化分页类
		                                                                                   // 传入总记录数和每页显示的记录数
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'data', $rel ['data'] ['list'] );
		$this->assign ( 'pages', $paginate );
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->display ( "BidHistory/bid" );
	
	}
	public function updateBid() {
		$id = isset ( $_GET ['demand'] ) ? htmlspecialchars ( $_GET ['demand'] ) : '';
		$type =isset ( $_GET ['type'] ) ? htmlspecialchars ( $_GET ['type'] ) : '';
		if (! $id||!$type) {
			$this->error ( '参数错误' );
			return '';
		}
		
		$url = C ( 'CURL_POST_URL' ) . "MerDemand/get_member_demand";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'id' => $id 
		)
		;
		$rel = $this->curl ( $url, $param );
// 		$rel ['data'] ['pics'] = empty ( $rel ['data'] ['pics'] ) ? array () : explode ( ',', $rel ['data'] ['pics'] );
		$total_price = 0;
		$total_time = 0;
		foreach ( $rel ['data'] ['list'] as $key => $row ) {
			$time = $row ['time'];
			if ($time == 0) {
				$rel ['data'] ['list'] [$key] ['day'] = 0;
				$rel ['data'] ['list'] [$key] ['hour'] = 0;
				$rel ['data'] ['list'] [$key] ['min'] = 0;
			
			} elseif ($time > 0) {
				$left = 0;
				$day = $time / 1440;
				$left =  $time % (1440);
				$hour =  $left /60;
				$left =  $left % 60;
				$min =   $left ;
				$rel ['data'] ['list'] [$key] ['day'] = ( int )$day;
				$rel ['data'] ['list'] [$key] ['hour'] = ( int )$hour;
				$rel ['data'] ['list'] [$key] ['min'] =( int ) $min;
			} else {
				
			}
			if($time>0){
				$total_time += $time;
			}
			if($row['price']>0){
				$total_price += $row['price'];
			}
		}
		//总价 总时间 转化时间格式
		$rel ['data']['total_price'] = $total_price;
		$rel ['data']['total_time'] =toDaydiff($total_time) ;
// 	dump($rel);
		$this->assign('type',$type);
		$this->assign ( 'data', $rel ['data'] );
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->display ( "BidHistory/updateBid" );
	}
	
	public function offer_price() {
// 		dump ( $_REQUEST );
		$demand_id = ( int ) $_GET ['id'];
		$bidding_ids = $_POST ['bidding_ids'];
		$category_ids = $_POST ['category_ids'];
		$prices = $_POST ['prices'];
		$day = $_POST ['day'];
		$hour = $_POST ['hour'];
		$min = $_POST ['min'];
		$merchant_remark = isset($_POST['merchant_remark']) ? htmlspecialchars($_POST['merchant_remark']):'';
		$times = array ();
		// dump($day);
		for($key = 0; $key < count ( $prices ); $key ++) {
			$times [$key] = (( int ) $day [$key]) * (1440) + (( int ) $hour [$key]) * (60) + (( int ) $min [$key]) ;
		}
		// dump($times);
		$url = C ( 'CURL_POST_URL' ) . "MerDemand/merchant_offer_price";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'id' => $demand_id,
				'bidding_ids' => implode ( ',', $bidding_ids ),
				'category_ids' => implode ( ',', $category_ids ),
				'prices' => implode ( ',', $prices ),
				'times' => implode ( ',', $times ) ,
				'merchant_remark'=>$merchant_remark,
		);
		$rel = $this->curl ( $url, $param );
		if($rel['code']==0){
			$this->success('报价成功','unbid');
		}else{
			$this->error($rel['msg']);
		}
	
	}
	/*
	 * 商家项目列表
	 */
	public function project(){

		$classid = isset ( $_GET ['classid'] ) ? htmlspecialchars ( $_GET ['classid'] ) : '';
		$page = isset ( $_GET ['p'] ) ? htmlspecialchars ( $_GET ['p'] ) : '1';
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
// 		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$url = C ( 'CURL_POST_URL' ) . "MerService/service_list";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'page' => $page,
				'num' => $num,
				'classid'=>$classid
		);

		$rel = $this->curl ( $url, $param );
// 		dump($rel);
		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num
		) ); // 实例化分页类
		// 传入总记录数和每页显示的记录数
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'data', $rel ['data'] ['list'] );
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->assign ( 'pages', $paginate );
		$this->display();
	}
	/**
	 * 添加项目
	 */
	public function addProject(){
// 		dump($this->session);
		$url = C ( 'CURL_POST_URL' ) . "MerService/merServiceSelectList";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
		);
		$rel = $this->curl ( $url, $param );
// 		dump($rel);
		$this->assign ('category',$rel['data']);
// 		dump($rel);
		
		$this->display();
	}
	/**
	 * 上传图片 添加项目
	 * 
	 */
	public function uploadProjectPic(){
		$verifyToken = md5('seeyoulater' . $_POST['timestamp']);
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			$arr = mul_upload ( '/Service/' ,1);
			if ($arr) {
				// $arr = imgUrl($arr);
				die(json_encode(array("code"=>0,'msg'=>'ok','data'=>$arr[0])));exit();
			}
	
		}else{
			die(json_encode(array("code"=>4,'msg'=>'请选择上传图片')));	exit();
		}
	
	}
	public function doProject(){
		$this->jsonUtils=new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( $_POST ['intro'] ) : '';
		$price = isset ( $_POST ['price'] ) ? htmlspecialchars ( $_POST ['price'] ) : '';
		$sub_id = isset ( $_POST ['sub_id'] ) ? htmlspecialchars ( $_POST ['sub_id'] ) : '';
		$timeout = isset ( $_POST ['timeout'] ) ? htmlspecialchars ( $_POST ['timeout'] ) : '';
		$pics = isset ( $_POST ['pics'] ) ?   $_POST ['pics'] : '';
		$mer_session_id = $this->session ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		
		if ($price == null || $price == '' || ! is_numeric ( $price )) {
			$this->jsonUtils->echo_json_msg ( 4, "项目价格不符合格式！" );
			exit ();
		}
		if (M ( 'service' )->where ( array (
				'cat_id' => $sub_id,
				'merchant_id' => $merchant_id
		) )->field ( 'id' )->find ()) {
			$this->jsonUtils->echo_json_msg ( 1, '已添加过该服务！' );
			exit ();
		}
		$call="App\\Controller\\CategoryController";
		$cate = call_user_func_array(array($call, 'getCategoryById'), array($sub_id));
		
		
		$call1="App\\Controller\\CommonController";
		$postion = call_user_func_array(array($call1, 'getMerchantPosition'), array($merchant_id));
		
		$data ['province_id'] = $postion ['province_id'];
		$data ['city_id'] = $postion ['city_id'];
		$data ['area_id'] = $postion ['area_id'];
		$data ['name'] = $cate ['name'];
		$data ['merchant_id'] = $merchant_id;
		$data ['intro'] = $intro;
		$data ['price'] = $price;
		$data ['pcat_id'] = $cate ['pid'];
		$data ['cat_id'] = $sub_id;
		$data ['timeout'] = $timeout;
		$data ['addtime'] = time ();
		if (!empty($pics)) {
			$newarr =array();
			$arr = json_decode($pics,true);
			foreach ($arr as $key =>$row){
				$newarr[] = json_decode($row,true);
			}
		
			$data ['pics'] =json_encode( $newarr);
		}else{
			$data ['pics']="[]";
		}
		$result = M ( 'service' )->add ( $data );
		

		if ($result) {
				
			$this->jsonUtils->echo_json_msg ( 0, '添加成功！' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '添加失败！' );
			exit ();
		}
		
	}
	
	public function modProject() {
		$this->jsonUtils=new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
		$service_id = isset ( $_POST ['service_id'] ) ? htmlspecialchars ( $_POST ['service_id'] ) : '';
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( $_POST ['intro'] ) : '';
		$price = isset ( $_POST ['price'] ) ? htmlspecialchars ( $_POST ['price'] ) : '';
		$timeout = isset ( $_POST ['timeout'] ) ? htmlspecialchars ( $_POST ['timeout'] ) : '';
		$pics = $_POST ['pics'];
		$mer_session_id = $this->session['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		if(!empty($price)){
			if (! is_numeric ( $price )) {
				$this->jsonUtils->echo_json_msg ( 4, "项目价格不符合格式！" );
				exit ();
			}else{
				$data ['price'] = $price;
			}
		}
		if ($intro) {
			$data ['intro'] = $intro;
		}
		if ($timeout) {
			$data ['timeout'] = $timeout;
		}
		$data ['effect'] = 0;
		if (!empty($pics)) {
			$newarr =array();
			$arr = json_decode($pics,true);
			foreach ($arr as $key =>$row){
				$newarr[] = json_decode($row,true);
			}
		
			$data ['pics'] =json_encode( $newarr);
		}else{
			$data ['pics']="[]";
		}
		$result = M ( 'service' )->where ( "id=$service_id and merchant_id = $merchant_id" )->save ( $data ); // 保存商家信息
		
		if ($result===false) {
			$this->jsonUtils->echo_json_msg ( 4, '修改失败！' );
			exit ();
		}
	
		
		$this->jsonUtils->echo_json_msg ( 0, '修改成功！' );
		exit ();
	
	}
	/*
	 * 商家项目detail
	*/
	public function projectdetail(){
	
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if(!$id){
			$this->error('id');exit();
		}
		$url = C ( 'CURL_POST_URL' ) . "MerService/get_service";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'service_id' => $id,
			
		);
	
		$rel = $this->curl ( $url, $param );
		$time = $rel['data']['time'];
		$left = 0;
		$day = $time / 1440;
		$left =  $time % (1440);
		$hour =  $left /60;
		$left =  $left % 60;
		$min =   $left ;
		
		$rel['data']['day'] = (int)$day;
		$rel['data']['hour'] = (int)$hour;
		$rel['data']['min'] = (int)$min;

		$this->assign ( 'data', $rel ['data']  );
		$this->display();
	}
	
	public function delProject(){

		$service_id = isset ( $_POST ['service_id'] ) ? htmlspecialchars ( $_POST ['service_id'] ) : '';
		$url = C ( 'CURL_POST_URL' ) . "MerService/del_service";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'service_id' => $service_id,
					
		);
		
		$rel = $this->curl ( $url, $param );
		$this->ajaxReturn($rel);
	}
	public function orderIncomplete(){
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$page = isset ( $_GET ['page'] ) ? htmlspecialchars ( $_GET ['page'] ) : '1';
		$url = C ( 'CURL_POST_URL' ) . "MerOrder/merchant_order_list";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'type' => 0,
				'num'=>$num,
				'page'=>$page
		);
		
		$rel = $this->curl ( $url, $param );
// 		dump($rel);
		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num
		) ); 
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->assign ( 'pages', $paginate );
		$this->assign ( 'data', $rel ['data']['list']  );
		$this->display();
	}
	public function ordercomplete(){
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$page = isset ( $_GET ['page'] ) ? htmlspecialchars ( $_GET ['page'] ) : '1';
		$url = C ( 'CURL_POST_URL' ) . "MerOrder/merchant_order_list";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'type' => 1,
				'num'=>$num,
				'page'=>$page
		);
	
		$rel = $this->curl ( $url, $param );
// 		dump($rel);
		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num
		) );
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->assign ( 'pages', $paginate );
		$this->assign ( 'data', $rel ['data']['list']  );
		$this->display();
	}
	
	public function orderfailed(){
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$page = isset ( $_GET ['page'] ) ? htmlspecialchars ( $_GET ['page'] ) : '1';
		$url = C ( 'CURL_POST_URL' ) . "MerOrder/merchant_order_list";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'type' => 2,
				'num'=>$num,
				'page'=>$page
		);
	
		$rel = $this->curl ( $url, $param );
// 		dump($rel);
		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num
		) );
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->assign ( 'pages', $paginate );
		$this->assign ( 'data', $rel ['data']['list']  );
		$this->display();
	}
	
	
	public function orderdetail(){
		$order_no = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if(!$order_no){
			$this->error('order_no不存在');
		}
		$url = C ( 'CURL_POST_URL' ) . "MerOrder/get_order";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'order_no' => $order_no,
				
		);
		
		$rel = $this->curl ( $url, $param );
		$timetotal = $this->counttime($rel['data']['total_time']) ;
		$rel['data']['total_time'] = $timetotal[0]."天".$timetotal[1]."时".$timetotal[2]."分";
		foreach($rel['data']['list'] as $key =>$row){
			$time = $this->counttime($row['time']);
			$rel['data']['list'][$key]['day'] = $time[0];
			$rel['data']['list'][$key]['hour'] = $time[1];
			$rel['data']['list'][$key]['min'] = $time[2];
		}
// 		dump($rel);
		$this->assign ( 'data', $rel ['data'] );
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->display();
	}
	public function merComment(){
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$content = isset ( $_POST ['content'] ) ? htmlspecialchars ( $_POST ['content'] ) : '';
		
		$url = C ( 'CURL_POST_URL' ) . "MerComment/Comment";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'order_no' => $order_no,
				'content'=>$content
		
		);
		
		$rel = $this->curl ( $url, $param );
		die(json_encode($rel));
	}
	public function confirmfail(){
		$order_no = isset ( $_POST ['order_no'] ) ? htmlspecialchars ( $_POST ['order_no'] ) : '';
		$content = isset ( $_POST ['content'] ) ? htmlspecialchars ( $_POST ['content'] ) : '';
	
		$url = C ( 'CURL_POST_URL' ) . "MerOrder/confirm_user_failed";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
				'order_no' => $order_no,
				'content'=>$content
	
		);
	
		$rel = $this->curl ( $url, $param );
		die(json_encode($rel));
	}
	public function userCenter(){
		
		$url = C ( 'CURL_POST_URL' ) . "Merchant/getMerchant";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
// 				'ver'=>'V2',
		
		);
		
		$rel = $this->curl ( $url, $param );
// 		dump($rel['data']);
		$buss = explode('-',$rel['data']['business_time']);
		$rel['data']['bussstart'] = explode(':',$buss[0]);
		$rel['data']['bussend'] = explode(':',$buss[1]);
		$rel['data']['addr'] = $this->getAddrName($rel['data']['area_id']);
		
		$this->assign('data',$rel['data']);
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->display();
	}
	public function commentCenter(){
		$num = isset ( $_GET ['num'] ) ? htmlspecialchars ( $_GET ['num'] ) : '10';
		$page = isset ( $_GET ['page'] ) ? htmlspecialchars ( $_GET ['page'] ) : '1';
		//getMerchant,getMerchantComment
		$url = C ( 'CURL_POST_URL' ) . "Merchant/getMerchant";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
					
		
		);
		$star = $this->curl ( $url, $param );
		$url = C ( 'CURL_POST_URL' ) . "Home/getMerchantComment";
		$param = array (
				'merchant_id' => $this->session ['merchant_id'],
				'page'=>$page,
				'num'=>$num
		
		);
		$rel = $this->curl ( $url, $param );
		$Page = new \Think\Page ( $rel ['data'] ['count'], $num, array (
				'p' => $page,
				'num' => $num
		) );
		$paginate = $Page->show (); // 分页显示输出
		$this->assign ( 'pages', $paginate );
		$this->assign('data',$rel['data']);
		$this->assign('star',$star['data']);
		$this->assign ( 'sessionJID', $this->session['jid']);
		$this->display();
	}
	protected function getAddrName($id){
		$db = M('City');
		$data = $db->table(C('DB_PREFIX')."city as a ")->join(C("DB_PREFIX")."city as b on b.id = a.city")
		->join(C("DB_PREFIX")."city as c on c.id = a.province")->field("a.name as area,b.name as city,c.name as province")->
		where(array('a.id'=>$id))->find();
		return $data;
		
	}
	public function uploadMerHeader(){
		$url = C ( 'CURL_POST_URL' ) . "Merchant/merchantHeader";
		$param = array (
				'mer_session_id' => $this->session ['mer_session_id'],
		);
		$rel = $this->curl ( $url, $param );
		$this->ajaxReturn($rel,'json');
	}
	public function uploadMerIntro(){
		
	
		$verifyToken = md5('seeyoulater' . $_POST['timestamp']);
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			$arr = mul_upload ( '/Merchant/' ,1);
			if ($arr) {
				// 				 $arr = imgUrl($arr);
				die(json_encode(array("code"=>0,'msg'=>'ok','data'=>$arr[0])));exit();
			}
		
		}else{
			die(json_encode(array("code"=>4,'msg'=>'请选择上传图片')));	exit();
		}
		
		
	}
	
	public function modMerchant() {
		$this->jsonUtils=new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
		$mer_session_id = $this->session ['mer_session_id'];
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$wifi_enable = isset ( $_POST ['wifi_enable'] ) ? htmlspecialchars ( trim ( $_POST ['wifi_enable'] ) ) : '';
		$tel = isset ( $_POST ['tel'] ) ? htmlspecialchars ( trim ( $_POST ['tel'] ) ) : '';
	//	$area_id = isset ( $_POST ['area_id'] ) ? htmlspecialchars ( trim ( $_POST ['area_id'] ) ) : '';
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( trim ( $_POST ['intro'] ) ) : '';
		$address = isset ( $_POST ['address'] ) ? htmlspecialchars ( trim ( $_POST ['address'] ) ) : '';
		$business_time = isset ( $_POST ['business_time'] ) ? htmlspecialchars ( trim ( $_POST ['business_time'] ) ) : '';
		$imgchange = isset ( $_POST ['imgchange'] ) ? htmlspecialchars ( trim ( $_POST ['imgchange'] ) ) : '0';
		$pics = $_POST['pics'];
		if (! empty ( $tel )) {
			$data ['tel'] = $tel;
		}
		if (! empty ( $intro )) {
			$data ['intro'] = $intro;
		}
		if (! empty ( $address )) {
			$data ['address'] = $address;
		}
		if (! empty ( $business_time )) {
			$data ['business_time'] = $business_time;
		}
		if (isset ( $wifi_enable )) {
			$data ['wifi_enable'] = $wifi_enable;
		}
		
		if(!empty($pics)){
			$arr_decode = json_decode($pics,true);
			if(!empty($arr_decode)){
				foreach ($arr_decode as $key =>$row){
					if(!empty($row)){
						$temp[$key]['hs'] = str_replace(C('ROOT_UPLOADS'), '', $row['hs']);
						$temp[$key]['hd'] = str_replace(C('ROOT_UPLOADS'), '', $row['hb']);
					}
				}
				foreach ($temp as $row){
					$data['pics'][] = $row;
				}
			}else{
				$data['pics'] = array();
			}
		}else{
			$data['pics'] = array();
		}
		
		$data['pics'] = json_encode($data['pics']);
		$result = M('Merchant')->where ( "id=$merchant_id" )->save ( $data );
		if($result === false){
			$this->jsonUtils->echo_json_msg ( 4, '修改失败！' );
		}else{
			
			$this->jsonUtils->echo_json_msg ( 0, '修改成功！' );
		}
			
	}
	
	protected function counttime($time){
		
		$left = 0;
		$day = $time / 1440;
		$left =  $time % (1440);
		$hour =  $left /60;
		$left =  $left % 60;
		$min =   $left ;
		return array((int)$day,(int)$hour,(int)$min);
	}
	
	public function uploadPic(){
		if ($_FILES) {
			$f_arr = mul_upload ( '/ChatPic/',3 );
			if ($f_arr) {
				$f_arr[0] = imgUrl($f_arr[0]);
				$this->jsonUtils->echo_json_data(0, 'ok', $f_arr[0]);exit();
			}
		}else{
			$this->jsonUtils->echo_json_msg(404, '未上传图片');exit();
		}
	}
}