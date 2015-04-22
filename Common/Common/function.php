<?php
function imgUrl($url) {
	$root = C('ROOT_UPLOADS');
	if (empty ( $url )) {
		if (is_array ( $url )) {
			return array ();
		} else {
			return '';
		}
	}
	if (is_array ( $url )) {
		foreach ( $url as $key => $row ) {
			if(! empty ( $row ['hs'] )){
				$hsimg = getimagesize('Uploads'.$row['hs']);
				$hs = $root . $row ['hs'];
			}else{
				$hs = '';
			}
// 			if(! empty ( $row ['hd'] )){
// 				$hdimg = getimagesize('Uploads'.$row['hd']);
// 				$hd = $root . $row ['hd'];
// 			}else{
// 				$hd = '';
// 			}
			if(! empty ( $row ['hb'] )){
				$hbimg = getimagesize('Uploads'.$row['hb']);
				$hb = $root . $row ['hb'];
			}else{
				$hb = '';
			}
			$rel [$key] = array (
					'hs' => $hs,
					//'hd' => $hd,
					'hb' => $hb,
					'hssize' => array($hsimg[0],$hsimg[1]),
					//'hdsize'=>array($hdimg[0],$hdimg[1]),
					'hbsize' => array($hbimg[0],$hbimg[1]),
			);
		}
	} else {
		$url = ( string ) $url;
		$rel = $root . $url;
	}
	
	return $rel;
}
/**
 * 多文件上传
 * @param  string $file     [description]
 * @param  string $savaPath [description]
 * @return [type]           [description]
 */
function mul_upload($savaPath='',$sizeType){
	set_time_limit(30);
	//是否删除原图
	$saveResource = 0;
	if($sizeType == 1){
		$size = array(
				'hs'=>array(137,130),
				'hd'=>array(0,0),
				'hb'=>array(0,0),
		);
	}elseif($sizeType == 2){
		$size = array(
				'hs'=>array(160,160),
				'hd'=>array(0,0),
				'hb'=>array(0,0),
		);
	}elseif($sizeType == 3){
		//上传原图
	}else{
		die(json_encode(array('code'=>'4','msg'=>'上传生成缩略图尺寸不存在')));
	}
	$upload = new \Think\Upload();// 实例化上传类
	$upload->maxSize   =     3145728 ;// 设置附件上传大小
	$upload->mimes      =     array('image/jpeg', 'image/png', 'image/gif');// 设置附件上传MIME类型
	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	$upload->saveExt   = 		'jpg';
	//$upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
	$upload->savePath  =      $savaPath; // 设置附件上传（子）目录
	$upload->saveRule = 'uniqid';
	// 上传文件
	$info   =   $upload->upload();
	if(!$info) {// 上传错误提示错误信息
			die(json_encode(array('code'=>'4','msg'=>'上传失败')));exit();
	}else{// 上传成功 获取上传文件信息
		$image = new \Think\Image();
		if($sizeType == 3){
			foreach ( $info as $file ) {
				$source = './Uploads'.$file['savepath'].$file['savename'];
				$image->open($source);
				$image->thumb(0, 0,\Think\Image::IMAGE_THUMB_SCALE)->save($source,'jpg',85);
				$arr [] = $file ['savepath'] . $file ['savename'];
			}
		}else{
			$imgInfo = reset($info);
			if(!file_exists('./Uploads'.$imgInfo['savepath']."hs")){
				mkdir('./Uploads'.$imgInfo['savepath']."hs");
			}
// 			if(!file_exists('./Uploads'.$imgInfo['savepath']."hd")){
// 				mkdir('./Uploads'.$imgInfo['savepath']."hd");
// 			}
			if(!file_exists('./Uploads'.$imgInfo['savepath']."hb")){
				mkdir('./Uploads'.$imgInfo['savepath']."hb");
			}
			foreach($info as $file){
				$source = './Uploads'.$file['savepath'].$file['savename'];
				$hs = $file['savepath']."hs/".$file['savename'];
// 				$hd = $file['savepath']."hd/".$file['savename'];
				$hb = $file['savepath']."hb/".$file['savename'];
				//打开源文件
				$image->open($source);
				// 生成一个缩放缩略图并保存为 采取中心填充方式 保存后删除文件
				$image->thumb($size['hs'][0], $size['hs'][1],\Think\Image::IMAGE_THUMB_CENTER)->save('./Uploads'.$hs,'jpg');
// 				$image->open($source);
// 				$image->thumb($size['hd'][0], $size['hd'][1],\Think\Image::IMAGE_THUMB_SCALE)->save('./Uploads'.$hd,'jpg',50);
				$image->open($source);
				$image->thumb($size['hb'][0], $size['hb'][1],\Think\Image::IMAGE_THUMB_SCALE)->save('./Uploads'.$hb,'jpg',85);
				if(is_file($source)&& $saveResource ==0){
					unlink($source);
					//   echo '删除成功';
				}
				$arr[] = array('hs'=>$hs,/*'hd'=>$hd,*/'hb'=>$hb);

			}
			 
			 
		}
		return $arr;
	}



}


/**
 * 单文件上传
 * @param  [type] $file     [description]
 * @param  string $savaPath [description]
 * @return [type]           [description]
 */
function one_upload($file,$savaPath=''){
	$upload = new \Think\Upload();// 实例化上传类
	$upload->maxSize   =     3145728 ;// 设置附件上传大小
	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	//$upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
	$upload->savePath  =      $savaPath; // 设置附件上传（子）目录
	$upload->saveRule = 'uniqid';
	// 上传单个文件
	$info   =   $upload->uploadOne($_FILES[$file]);
	if(!$info) {// 上传错误提示错误信息
		die(json_encode(array('code'=>'4','msg'=>'上传失败')));exit();
	}else{// 上传成功 获取上传文件信息
		return $info['savepath'].$info['savename'];
	}

}


/**
 * 对象转化数组
 * @param  [type] $obj [description]
 * @return [type]      [description]
 */
function ob2ar($obj) {
	if(is_object($obj)) {
		$obj = (array)$obj;
		$obj = ob2ar($obj);
	} elseif(is_array($obj)) {
		foreach($obj as $key => $value) {
			$obj[$key] = ob2ar($value);
		}
	}
	return $obj;
}


//查询范围内
function  rangekm($km,$longitude,$latitude){
	$range = 180 / pi() * $km / 6372.797;     //里面的 km 就代表搜索 多少km 之内，单位km
	$lngR = $range / cos($latitude * pi() / 180);
	$maxLat = $latitude + $range;//最大纬度
	$minLat = $latitude - $range;//最小纬度
	$maxLng = $longitude + $lngR;//最大经度
	$minLng = $longitude - $lngR;//最小经度
	$arr['maxLat'] = $maxLat;
	$arr['minLat'] = $minLat;
	$arr['maxLng'] = $maxLng;
	$arr['minLng'] = $minLng;
	return $arr;
}


/**
 * 根据两点间的经纬度计算距离
 *
 * @param float $lat
 *          纬度值
 * @param float $lng
 *          经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
	//   $earthRadius = 6367000; // approximate radius of earth in meters
	$earthRadius = 6367; // approximate radius of earth in km
	/*
	 * Convert these degrees to radians to work with the formula
	*/
	$lat1 = ($lat1 * pi ()) / 180;
	$lng1 = ($lng1 * pi ()) / 180;

	$lat2 = ($lat2 * pi ()) / 180;
	$lng2 = ($lng2 * pi ()) / 180;
	/*
	 * Using the Haversine formula http://en.wikipedia.org/wiki/Haversine_formula calculate the distance
	*/
	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow ( sin ( $calcLatitude / 2 ), 2 ) + cos ( $lat1 ) * cos ( $lat2 ) * pow ( sin ( $calcLongitude / 2 ), 2 );
	$stepTwo = 2 * asin ( min ( 1, sqrt ( $stepOne ) ) );
	$calculatedDistance = $earthRadius * $stepTwo;

	return number_format($calculatedDistance,1);
}





function sort_asc($arr) {
	$n=count($arr);
	for($h=0;$h<$n-1;$h++){//外层循环n-1
		for($i=0;$i<$n-$h-1;$i++){
			$compareOne = (int)($arr [$i]['distance']*10);
			$compareTwo = (int)($arr [$i + 1]['distance']*10);
			
			if($compareOne>$compareTwo){//判断数组大小，颠倒位置
				$kong=$arr[$i+1];
				$arr[$i+1]=$arr[$i];
				$arr[$i]=$kong;
			}
		}
	}

	return $arr;
}



		//距离降序
function sort_desc($a) {
	$len=count($a);
	for($i = 0; $i < $len - 1; $i ++) {
	for($j = $len - 1; $j > $i; $j --)
		if (((int)$a [$j]['distance']) > ((int)$a [$j - 1]['distance'])) { //
		$x = $a [$j];
		$a [$j] = $a [$j - 1];
		$a [$j - 1] = $x;
	}
	}

	return $a;
}



//$arr数组 $name字段  数组返回 xx,xx,xx
function  arraytostr($arr,$name){
		if(empty($arr)){
			return '';
		}
		$str='';
		foreach ($arr as $key => $value) {
			$str=$str.",".$arr[$key][$name];
		}
		return  substr($str, 1,strlen($str)-1);

}

function toDaydiff($num){
		
		$day =intval($num /1440);
		$left = $num % 1440;
		$hour = intval( $left / 60);
		$min = $left % 60;
		return  (int) $day."天".$hour."时".$min."分";
}
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
		$strL = strlen($str);
		if(function_exists("mb_substr")){
			if($suffix&& $strL>$length )
				return mb_substr($str, $start, $length, $charset)."...";
			else
				return mb_substr($str, $start, $length, $charset);
		}elseif(function_exists('iconv_substr')) {
			if($suffix&& $strL>$length)
				return iconv_substr($str,$start,$length,$charset)."...";
			else
				return iconv_substr($str,$start,$length,$charset);
		}
		$re['utf-8']   = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
		$re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
		$re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
		$re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		if($suffix&& $strL>$length) return $slice."…";
		return $slice;
}
function timeCompare($bussTime){
	if(empty($bussTime)||!strpos($bussTime,'-')){
		return false;
	}
	$bussTime = str_replace(array(':',' '),array('',''), $bussTime);
	$re = strstr($bussTime,'-');
	if(!empty($re)){
		$buss = explode('-', $bussTime);
		$time = date('Hi');
		$begin = (int)$buss[0];
		$end = (int)$buss[1];
		if($begin<$time &&$end>$time){
			return true;
		}else{
			return false;
		}
	}
	return false;
}
 function dealtime($Btime){
	$time = time() - $Btime;
	if($time <0){
		$data ='';
		$rel = '时间错误';
	}elseif($time <60){
		$data = '秒前';
		$rel = $time.$data;
	}elseif($time<3600){
		$time = ceil($time/60);
		$data = '分钟前';
		$rel = $time.$data;
	}elseif($time < 86400){
		$time = floor($time/3600);
		$data = '小时前';
		$rel = $time.$data;
	}elseif($time >= 86400){
		$rel = date('Y-m-d',$Btime);
	}
	return $rel;
}

		


?>