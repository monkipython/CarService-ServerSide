<?php
namespace App\Controller;
use Think\Think;
use Think\Controller;


/**
 * 微信支付模块
 */
class WeixinPayController extends Controller{
	private $jsonUtils;
	private $session_handle; // session 处理类
	public function __construct(){
			
		$this->jsonUtils=new \Org\Util\JsonUtils;
		$this->session_handle = new \Org\Util\SessionHandle ();
	}
	/**
	 * 商户后台收到用户支付单，调用微信支付统一下单
	 */
	public function unifiedOrder(){
		

		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//* 公众账号ID
		$data['appid'] = C('WEIXIN_APPID');
		//* 商户号
		$data['mch_id'] = C('WEIXIN_MCH_ID');
		// 设备号
		$data['device_info'] = '';
		//* 随机字符串 
		$data['nonce_str'] = $this->randChar();
		//* 商品描述
		$data['body'] = "商品描述";
		// 商品详情
		$data['detail'] = "商品详情";
		// 附加数据
		$data['attach'] = '附加数据';
		//* 商户订单号
		$data['out_trade_no']='123123';
		// 货币类型
		$data['fee_type'] = 'CNY';
		//* 总金额
		$data['total_fee'] = (int) '1';
		//* 终端IP
		$data['spbill_create_ip'] = get_client_ip();
		// 交易开始时间
		$data['time_start'] = '';
		// 交易结束时间
		$data['time_expire'] = '';
		// 商品标记
		$data['goods_tag'] = '';
		//* 通知地址
		$data['notify_url'] = C('WEIXIN_NOTIFY_URL');
		//* 交易类型
		$data['trade_type'] = 'APP';
		// 商品id
		$data['product_id'] = '';
		// 用户标识
		$data['openid'] = '';
		//* 签名
		
		$data['sign'] = $this->creatSign($data);
		dump($data);
		$rel = $this->postXmlCurl($url, $data);
		$reldata = $this->xml_to_array($rel);
		$arr = $this->decodeWx($reldata);
		dump($reldata);
		
		
	}
	/**
	 * 以xml的方式，调用微信支付
	 */
	protected  function postXmlCurl($url,$arr){
		$arr_xml ['xml'] = $arr;
		$xml .= data_to_xml($arr_xml);
// 	dump($xml);die();
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, '10');
		
		//如果有配置代理这里就设置代理
// 		if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
// 				&& WxPayConfig::CURL_PROXY_PORT != 0){
// 			curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
// 			curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
// 		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
// 		if($useCert == true){
// 			//设置证书
// 			//使用证书：cert 与 key 分别属于两个.pem文件
// 			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
// 			curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
// 			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
// 			curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
// 		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			$this->jsonUtils->echo_json_msg(4, $error);exit();
		}
		
	}
	/**
	 * 生成随机数，转化成字符串
	 */
	protected function randChar(){
		return (string)md5(uniqid());
	}
	
	/**
	 * 生成签名算法
	 */
	protected function creatSign($data){
		unset($data['sign']);
		ksort($data);
		$urlencode = "";
		foreach ($data as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$urlencode .= $k . "=" . $v . "&";
			}
		}
		$urlencode = trim($urlencode, "&");
		
		$str = $urlencode."&key=".C('WEIXIN_APP_KEY');
		$str = strtoupper(md5($str));
		return $str;
		
	}
	/**
	 * 微信回调
	 */
	public function notifyUrl(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		
		//保存支付通知
		echo 'success';
		//告知微信已处理
	}
	
	/**
	 * 提供实时查询支付结果
	 */
	public function checkPay(){
		
	}
	
	
	/**
	 * array to xml
	 */
	function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
				$subxml= $matches[2][$i];
				$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
	function decodeWx($data){
		if(empty($data['xml'])){
			return false;
		}
	}
}
?>