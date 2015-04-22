<?php
return array(
	//'配置项'=>'配置值'
		'APP_USE_NAMESPACE' => true,
		'CURL_POST_URL'=>'http://121.40.92.53/ycbb/index.php/App/',
		'HOME' => 'http://121.40.92.53/ycbb/index.php/Home/',
		'URL_ROUTER_ON'   => true, 
		'URL_ROUTE_RULES'=>array(
				'download'         => 'Index/download', // 静态地址路由
				'AboutUs'         => 'Index/AboutUs', // 静态地址路由
				'ContactUs'         => 'Index/ContactUs', // 静态地址路由
				'Services'  		=>'/Service/serviceProvision'
		),
);