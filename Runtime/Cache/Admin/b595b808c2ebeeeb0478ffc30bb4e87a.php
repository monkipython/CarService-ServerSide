<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo (C("sitename")); ?></title>

<link href="/Public/dwz/themes/default/style.css" rel="stylesheet" type="text/css" />
<link href="/Public/dwz/themes/css/core.css" rel="stylesheet" type="text/css" />
<link href="/Public/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="/Public/dwz/themes/default.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="/Public/dwz/themes/css/ieHack.css" rel="stylesheet" type="text/css" />
<![endif]-->
<style type="text/css">
	#header{height:85px}
	#leftside, #container, #splitBar, #splitBarProxy{top:90px}
</style>
<script>
/*ThinkPHP常量*/
var _APP_="/index.php";
var _PUBLIC_="/Public";
var _ROOT_="";
/*本地域名正则表达式*/
//var localTest=/^http?:\/\/<?php echo str_replace(".","\.",$_SERVER['HTTP_HOST']) ?>\//i;
var localTest=/^http?:\/\/[^\/]*?(sinaapp\.com)\//i;
</script>
<script src="/Public/dwz/js/speedup.js" type="text/javascript"></script>
<script src="/Public/dwz/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="/Public/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="/Public/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="/Public/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="/Public/xheditor/xheditor-1.2.1.min.js" type="text/javascript"></script>
<script src="/Public/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script src="/Public/dwz/js/dwz.min.js" type="text/javascript"></script>
<script src="/Public/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>
<script src="/Public/uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>
<script type="text/javascript">
function fleshVerify(){
	//重载验证码
	$('#verifyImg').attr("src", '/index.php/Admin/Public/verify/'+new Date().getTime());
}
function dialogAjaxMenu(json){
	dialogAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
			//扩展
			var menuTag=$("#navMenu .selected").attr('menu');
			$("#sidebar").loadUrl("/index.php/Admin/Public/menu/menu/"+menuTag);
	}
}

function navTabAjaxMenu(json){
	navTabAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
		//扩展
		var menuTag=$("#navMenu .selected").attr('menu');
		$("#sidebar").loadUrl("/index.php/Admin/Public/menu/menu/"+menuTag);
	}
}


function navTabAjaxGroupMenu(json){
	navTabAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
		//扩展
		var menuTag=$("#navMenu .selected").attr('menu');
		$("#sidebar").loadUrl("/index.php/Admin/Public/menu/menu/"+menuTag);
	}
}


/*function navTabAjax(json){
	navTabAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
		$("#navMenu").loadUrl("/index.php/Admin/Public/nav");
	}
}
*/
$(function(){
	DWZ.init("/Public/dwz/dwz.frag.xml", {
		loginUrl:"/index.php/Admin/Public/login_dialog", loginTitle:"登录",	// 弹出登录对话框
		statusCode:{ok:1,error:0},
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"_order", orderDirection:"_sort"}, //【可选】
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"/Public/dwz/themes"});
		}
	});
});
</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo" href="/index.php/Admin" style="margin-left:35px;">Logo</a>
				<ul class="nav">
					<li><a href="/index.php/Admin/Public/changeUser" target="dialog"  width="500" height="500" rel="changeUser"  mask="true">切换模拟用户</a></li>
					<li><a href="/index.php/Admin/Public/main" target="dialog" width="580" height="360" rel="sysInfo">系统消息</a></li>
					<li><a href="/index.php/Admin/Public/password/" target="dialog" mask="true">修改密码</a></li>
					<li><a href="/index.php/Admin/Public/profile/" target="dialog" mask="true">修改资料</a></li>
					<li><a href="/index.php/Admin/Public/logout/">退出</a></li>
				</ul>
				<ul class="themeList" id="themeList">
					<li theme="default"><div class="selected">蓝色</div></li>
					<li theme="green"><div>绿色</div></li>
					<li theme="purple"><div>紫色</div></li>
					<li theme="silver"><div>银色</div></li>
					<li theme="azure"><div>天蓝</div></li>
				</ul>
			</div>
			<div id="navMenu">
				
			</div>
		</div>
		
		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			
			<div id="sidebar">
					
<div class="accordion" fillSpace="sideBar">

    <div class="accordionHeader">
        <h2><span>Folder</span>运营管理</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">	
       		<li><a href="/index.php/Admin/Answer/index" target="navTab" rel="Answer.index">问答列表</a></li>
       		<li><a href="/index.php/Admin/Recent/index" target="navTab" rel="Recent.index">遇见列表</a></li>
       		<li><a href="/index.php/Admin/Demand/index" target="navTab" rel="Demand.index">需求列表</a></li>
            <li><a href="/index.php/Admin/Demand/urge_merchant" target="navTab" rel="Demand.urge_merchant">跟进商家</a></li>	
          <!--  <li><a href="/index.php/Admin/Complain/index" target="navTab" rel="Complain.index">投诉列表</a></li> --> 	
        </ul>		
    </div>
    
     <div class="accordionHeader">
        <h2><span>Folder</span>审核管理</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">	
            <li><a href="/index.php/Admin/Auth/index" target="navTab" rel="Auth.index">商户信息审核</a></li>
            <li><a href="/index.php/Admin/AuthService/index" target="navTab" rel="AuthService.index">商户项目审核</a></li>	
            <li><a href="/index.php/Admin/AuthService/indexHistory" target="navTab" rel="AuthService.indexHistory">商户项目审核历史</a></li>	
             <li><a href="/index.php/Admin/Merchant/index" target="navTab" rel="Merchant.index">商家管理</a></li>	
        </ul>		
    </div>
      <div class="accordionHeader">
        <h2><span>Folder</span>商家管理</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">	
            <li><a href="/index.php/Admin/Merchant/index" target="navTab" rel="Merchant.index">商家管理</a></li>	
          <!-- <li><a href="/index.php/Admin/Service/index" target="navTab" rel="Service.index">发布的项目</a></li>
            <li><a href="/index.php/Admin/MerchantBidding/index" target="navTab" rel="Binding.index">竞价列表</a></li>
             -->  
        </ul>		
    </div>
     <div class="accordionHeader">
        <h2><span>Folder</span>会员管理</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">
            <li><a href="/index.php/Admin/Member/index" target="navTab" rel="Member.index">会员管理</a></li>	
            <li><a href="/index.php/Admin/Cart/index" target="navTab" rel="Car.index">车辆列表</a></li>				
            <li><a href="/index.php/Admin/MemberDemand/index" target="navTab" rel="Binding.index">需求列表</a></li>			
        </ul>		
    </div>
  <!--  <div class="accordionHeader">
        <h2><span>Folder</span>活动管理</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">	
            <li><a href="/index.php/Admin/Activity/index" target="navTab" rel="Activity.index">限时活动</a></li>		
        </ul>		
    </div> --> 
    <div class="accordionHeader">
        <h2><span>Folder</span>订单管理</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">	
            <li><a href="/index.php/Admin/Order/index" target="navTab" rel="Order.index">订单列表</a></li>		
        </ul>		
    </div>
  


    <div class="accordionHeader">
        <h2><span>Folder</span>系统设置</h2>
    </div>
    <div class="accordionContent">
        <ul class="tree treeFolder">	
        	<li><a href="/index.php/Admin/Category/index" target="navTab" rel="Category.index">汽车项目分类</a></li>
            <li><a href="/index.php/Admin/City/index" target="navTab" rel="City.index">城市管理</a></li>
          <!--   <li><a href="/index.php/Admin/Config/index" target="navTab" rel="Config.index">系统配置</a></li> -->
            <li><a href="/index.php/Admin/User/index" target="navTab" rel="User.index">用户管理</a></li>	
           <!--  <li><a href="/index.php/Admin/Log/index" target="navTab" rel="Log.index">登录日志</a></li> -->	
            <li><a href="/index.php/Admin/AuthRule/index" target="navTab" rel="AuthRule.index">系统权限</a></li>		
            <li><a href="/index.php/Admin/AuthGroup/index" target="navTab" rel="AuthGroup.index">系统组</a></li>	
            <li><a href="/index.php/Admin/Config/index" target="navTab" rel="Config.index">AppConf配置</a></li>	
       		  <li><a href="/index.php/Admin/RunLog/index" target="navTab" rel="RunLog.index">App运行日志</a></li>	
        </ul>		
    </div>

</div>



			</div>
		</div>

		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:void(0)"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:void(0)">我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo">
							
							<div class="right">
								<p><?php echo (date('Y-m-d g:i a',time())); ?></p>
							</div>
							<p><span><?php echo (C("sitename")); ?></span></p>
							<p >Welcome, <label style="color:green"><?php echo (session('loginUserName')); ?></label>，您上次登录的时间为： <label style="color:green"><?php echo (date('Y-m-d H:i:s',session('lastLoginTime'))); ?></label></p>
                                                        
						</div>
						
				

						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
	
	<div id="footer">Copyright &copy; 2014 <a href="" target="_blank">养车宝宝</a></div>


</body>
</html>