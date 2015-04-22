<?php

return array(
    //'配置项'=>'配置值'
    'MODULE_ALLOW_LIST' => array('Admin','App','Home'),
	'ROOT_UPLOADS' => 'http://www.caryu.net/Uploads',
    // 设置禁止访问的模块列表
    //'MODULE_DENY_LIST' => array('Common', 'Runtime', 'Api'),
    'DEFAULT_MODULE' => 'Home',
	
    'DB_TYPE' => 'mysqli',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'ycbb_db',
//     'DB_USER' => 'root',
//     'DB_PWD' => '',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'ycbb_',
	'DB_USER' => 'debian-sys-maint',
	'DB_PWD' => 'nnDekZYaqo5kEJPD',
	'EXPIRE_TIME'=> 24*3600*30,	
	'VER_ARRAY' => array('V2'),
	//推送
	'PUSH_RANGE_KM'=>3, //初始距离
	'PUSH_MAX_BINDING_NUM'=>10, //最大商家报价
	'PUSH_MAX_RANGE_KM'=>13, //最大推送范围
	'PUSH_PER_KM'=>2, //每次推送增加公里数
	//短信过期时间间隔
	'MESSAGE_EXPIRE_TIME' =>60*30,
	//xmpp 配置
	'XMPP_SERVER_RESOURCE'=>"Smack",
	"XMPP_SERVER_DOMAIN"=>"caryu.net",
	//微信支付
	'WEIXIN_APPID'=>'wxf155543339aa80cc',
	'WEIXIN_MCH_ID'=>'1238176202',
	'WEIXIN_APP_KEY'=>'EvGe6AeZ9dTDekZYaqo5kEJPDZYaqo5k',
	'WEIXIN_APPSECRET'=>'dee3d062ad8982c1be773b2013b67776',
	'WEIXIN_NOTIFY_URL'=>'http://www.caryu.net/index.php/App/WeixinPay/notifyUrl',

);
