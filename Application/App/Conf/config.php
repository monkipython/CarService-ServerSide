<?php
return array(
	
//  	'DB_TYPE' => 'mysqli',
//     'DB_HOST' => 'localhost',
//     'DB_NAME' => 'ycbb_db',
//     'DB_USER' => 'debian-sys-maint',
//     'DB_PWD' => 'wY1NtUP7IsNvXGvm',
//     'DB_PORT' => '3306',
//     'DB_PREFIX' => 'ycbb_',
	'APP_USE_NAMESPACE' => true,
	//'SESSION_AUTO_START' =>false
	//权限验证 验证数据库
	'AUTH_DB_CONFIG'=>array('0'=>'Merchant'),
	'EASEMOB_OPTION'=>array('client_id'=>'YXA6GBC5EGsSEeSmsxcgVaxMHA',
			'client_secret'=>'YXA6QWpbnVFc3_iNy-aRfvSjbP4GrXA',
			'app_name'=>'jiake',
			'org_name'=>'rihoukeji',
			),
	//短信过期时间间隔
	'MESSAGE_EXPIRE_TIME' =>60*30,
	//xmpp 配置
	'XMPP_SERVER_RESOURCE'=>"Smack",
	"XMPP_SERVER_DOMAIN"=>"caryu.net",
);
