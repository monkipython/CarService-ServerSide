<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="驾遇-遇见最好的汽车生活服务平台">
<meta name="author" content="">
<title>驾遇 －下载用户端</title>
<script>
function checkSYSdown(){
	  var ua = window.navigator.userAgent; 
	  var max=ua.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
	 if (!!ua.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)|| ua.indexOf('iPhone') > -1 || ua.indexOf('iPad') > -1 || ua.indexOf('iPod') > -1){
		 
		 window.location.href="https://itunes.apple.com/cn/app/jia-yu/id967208797?l=en&mt=8";
	 }else{
		 if(ua.indexOf('Android') > -1){
			window.location.href="/down.php?File=JiaYuClient.apk";
		 }else{
			alert('无法识别您的手机系统!');
		 }
		 
	 }
	  
}

</script>
</head>
<body onload="checkSYSdown()">
</body>
</html>