<?php


class DemandController extends CommonController{


	public function index(){
		$page = empty($_REQUEST[C('VAR_PAGE')])?1:$_REQUEST[C('VAR_PAGE')];
		$num = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		$min = !empty($_REQUEST['min'])?htmlspecialchars($_REQUEST['min']):'';//时间内
		$biddingLim = !empty($_REQUEST['biddingLim'])?htmlspecialchars($_REQUEST['biddingLim']):'';//低于报价数量
		$autoreload = !empty($_REQUEST['autoreload'])?htmlspecialchars($_REQUEST['autoreload']):'0';
		$url = 'http://caryu.net/index.php/Admin/Demand/index/';
		
		if(!empty($min)){
			$url.='min/'.$min.'/';
		}
		if(!empty($page)){
			$url.=C('VAR_PAGE').'/'.$page.'/';
		}
		if(!empty($num)){
			$url.='numPerPage/'.$num;
		}
		
		$db = M('MemberDemand');
		$mappage =  array('status'=>0);
		$map = array('status'=>0,'expire_time'=>array('gt',time()));
		$mapdata =array('a.status'=>0,'a.expire_time'=>array('gt',time()));
		if(!empty($biddingLim)){
			$map['is_bidding']= array('lt',$biddingLim);
			$mapdata['is_bidding']= array('lt',$biddingLim);
			$mappage['biddingLim']= $biddingLim;
		}
		if(!empty($min)){
			$second = $min*60;
			$ltTime = time()-$second;
			$gtTime = time()-86400;
			$mappage['min']= $min;
			$map['addtime'] = array(array('lt',$ltTime),array('gt',$gtTime));
			$mapdata['a.addtime'] = array(array('lt',$ltTime),array('gt',$gtTime));
		}
		$count = $db ->where($map)->count();
		if(!empty($min)||!empty($biddingLim)){
		
			$num = $count;
		}
		$this->_page($count,$mappage,$page,$num);
		
		$data = $db->table(C("DB_PREFIX")."member_demand as a ")->field("a.id,b.nick_name,b.mobile,a.title,a.is_bidding,a.addtime")
		->join(C('DB_PREFIX')."member as b on a.member_id = b.id",'LEFT')
		->where($mapdata)->limit($num)->page($page)->order("a.addtime desc")->select();
		
// 		dump($data);
		$this->assign('autoreload',$autoreload);
		$this->assign('list', $data);
		$this->assign('furl', base64_encode(urlencode($url)));
		$this->assign('min',$min);
		$this->assign('biddingLim',$biddingLim);
		$this->display();
	}

	
	public function detail(){
			//	dump($_GET);
		$id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if(empty($id)){
			$this->error('id为空');
		}
		$furlen = isset ( $_GET ['furl'] ) ?   $_GET ['furl']  : '';
		$furl=urldecode (base64_decode($furlen));
		//已报价商家
		$dis = M('')->table(C('DB_PREFIX')."member_demand as a")
			->field('a.*,b.nick_name,b.mobile,b.header')
			->join(C('DB_PREFIX')."member as b on a.member_id = b.id")
		->where("a.id=$id")->find();
		$dis['header'] = imgUrl($dis['header']);
		$dis['pics'] = imgUrl(json_decode($dis['pics'],true));
		$db = M('');
		//分开显示报价情况
// 		$bidding = $db ->table(C('DB_PREFIX')."merchant_bidding as a ")->field('b.name,a.price,a.out_time,c.merchant_name,c.header,c.mobile,c.tel,a.merchant_id')
// 		->join(C('DB_PREFIX')."category as b on a.sub_id = b.id",'LEFT')
// 		->join(C('DB_PREFIX')."merchant as c on a.merchant_id = c.id",'LEFT')
// 		->where(array('a.demand_id'=>$id))->order('a.addtime asc')->select();
// 		$biddingMerchant = array();
// 		foreach($bidding as $k =>$r){
// 			$bidding_data [$r['merchant_id']]['child'][] = $r;
// 			$bidding_data [$r['merchant_id']]['total_price'] += $r['price'];
// 			$bidding_data [$r['merchant_id']]['out_time'] += $r['price'];
// 			if(!array($r['merchant_id'],$biddingMerchant)){
// 				$biddingMerchant[] = $r['merchant_id'];
// 			}
// 		}
// 		dump($bidding_data);dump($biddingMerchant);die();
		$bidding = $db->query ( "select sum(a.price) as total_price,sum(a.out_time) as total_time,b.merchant_name,b.header,b.mobile,b.tel ,b.id as merchant_id,b.latitude,b.longitude from " . C ( 'DB_PREFIX' ) . "merchant_bidding as a left join " . C ( 'DB_PREFIX' ) . "merchant as b on a.merchant_id = b.id where demand_id = " . $id . "  group by merchant_id order by a.addtime asc" );
		$count = count($bidding);
		foreach ( $bidding as $ke => $ro ) {
			$bidding [$ke] ['header'] = imgUrl ( $ro ['header'] );
			$distance = getDistance ( $dis['latitude'], $dis['longitude'], $ro ['latitude'], $ro ['longitude'] );
			$bidding [$ke] ['distance'] = $distance;
			$bidding [$ke] ['remark'] = $this->getMerRemark($id,$ro['merchant_id']);
			unset ( $bidding [$ke] ['latitude'] );
			unset ( $bidding [$ke] ['longitude'] );
			$biddingMerchant[] = $ro['merchant_id'];
		}
		$db = M('DemandMerchantEnable');
		$enable = $db ->table(C('DB_PREFIX')."demand_merchant_enable as f ")->
		join(C('DB_PREFIX')."merchant as a on f.merchant_id = a.id",'LEFT')->field('a.merchant_name,a.tel,a.id,a.mobile,a.header,a.longitude,a.latitude,a.address,b.name as province,c.name as city,d.name as area')
		->join(C('DB_PREFIX')."city as b on a.province_id = b.id",'LEFT')->join(C('DB_PREFIX')."city as c on a.city_id = c.id",'LEFT')
		->join(C('DB_PREFIX')."city as d on a.area_id = d.id",'LEFT')
		->where(array('f.demand_id'=>$id))->select();
		if($enable === false) {
			$this->error('商户未收到需求');
		}else{
			if(count($biddingMerchant)>0){
				foreach( $enable as $key =>$row){
					if(in_array($row['id'],$biddingMerchant)){
						unset ($enable[$key]);
					}
				}
				sort($enable);
				
			}
		}
		foreach ($enable as $key=>$row){
			$enable [$key]['header'] = imgUrl($row['header']);
			$enable[$key]['distance'] = getDistance($dis['latitude'],$dis['longitude'],$row['latitude'],$row['longitude']);
		}
		$enable=sort_asc($enable);

		$this->assign('merchant',$enable);
		$this->assign('furlen',$furlen);
		$this->assign('furl',$furl);
		$this->assign('data',$dis);
		$this->assign('bidding',$bidding);
		$this->display();
	}
	
	public function getMerRemark($id,$merid){
		$db = M('MerchantBiddingRemark');
		$data = $db ->where(array('demand_id'=>$id,'merchant_id'=>$merid))->getField('remark');
		return $data;
	}
	
	
	/**
	 * 连续{}次接到需求都未报价的
	 */
	public function urge_merchant(){
		set_time_limit(60);
		//查询
	
		$limitNum = !empty($_REQUEST['limitNum'])?htmlspecialchars($_REQUEST['limitNum']):'1';
		if(empty($limitNum)){
			$this->error('商家个数为空');
			
		}
		//已收到的需求
		$db = M ('DemandMerchantEnable');
		$enable = $db ->table(C('DB_PREFIX')."demand_merchant_enable as a " )->join(C('DB_PREFIX')."merchant as b on a.merchant_id = b.id",'LEFT')->field('a.merchant_id,a.demand_id,b.manager,b.merchant_name,b.address,c.name ')
		->join(C('DB_PREFIX')."city as c on b.area_id = c.id")
		->order('a.id asc')->select();
		foreach($enable as$key =>$row){
			$demand[$row['merchant_id']][] = $row;
		}
		//已报价的需求
		$db2 = M ('MerchantBidding');
		$bidding = $db2->select();
		foreach ($bidding as $key =>$row){
			$biddingMer[$row['merchant_id']][] = $row['demand_id'];
			
		}
		foreach ($biddingMer as $key =>$row){
			$rowUn = array_unique($row);
			sort($rowUn);
			$temp[$key] =$rowUn;
		}
// 		dump($demand);
		//计算规则
		foreach ($demand as $key =>$row){
			if(empty($temp[$key])){
				//所有需求商户都未报价
				$nodata[] = $row[0];//商户均未报价
			}else{
				//检测是否连续几次为报价 ture 就写入data
				$initnum = 0;
				$start = 0;
				$lastkey = 0;//上一次key
				$arr = array();//联系多少次未报价数组
				foreach ($row as $k =>$r){
					
					foreach ($temp[$key] as $te =>$tem){
						if($r['demand_id'] == $tem){
							if($k == 0){
								$number = 0;
							}else{
								$number = $k-$lastkey-1;
								$lastkey = $k;
							}
							$arr[] = $number;
						}
					}
					
				}	
			
				if(max($arr) != 0){
					$data[] = array('num'=>max($arr),'merchant_id'=>$row[0]['merchant_id'],'merchant_name'=>$row[0]['merchant_name'],'address'=>$row[0]['address'],'name'=>$row[0]['name'],'manager'=>$row[0]['manager']) ;//商户均未报价最大个数
				}
				
			}
			
			
		}
		rsort($data);
// 		dump($data);die();
		$this->assign('nodata',$nodata);
		$this->assign('data',$data);
		$this->display();
		
	}
	
	
	public function cancelDemand(){
		
		$demand_id = isset ( $_GET ['id'] ) ? htmlspecialchars ( $_GET ['id'] ) : '';
		if (empty ( $demand_id )) {
			$this->error(  '需求ID为空...' );
			exit ();
		}
		$db = M('MemberDemand');
		$demand = $db->where ( array (
				'id' => $demand_id,
		) )->find ();
		if (! $demand) {
			$this->error( '数据异常' );
		}
		if($demand['status']==0 && time() <=$demand['expire_time']){
			//正常需求 才能取消
			$save ['status'] = 2;
			$save ['cancel_time'] = time();
			//$save ['expire_time'] = time();
			$result = $db->where ( "id=$demand_id" )->save ($save);
			if ($result) {
				$this->success('取消成功');
				exit ();
			} else {
				$this->error( '取消错误' );
				exit ();
			}
		}else{
			$this->error ( '不允许取消' );
			exit ();
		}
		
		
	}
    
}