<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>驾客－首页</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/Public/static/bootstrap/css/front-end.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/pagination.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/style.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
        <script src="/Public/static/jquery-2.0.3.min.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/jquery.paginatetable.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap-dialog.min.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/jquery.fileupload.js"></script>
		<style>
			.ycbb-service-items p{
				text-align: left;
				margin-left: 40%;
			}
			.h{
				font-weight: bolder;
			}
			.h-warning{
				color: #f87d22;
			}
			.h-lg{
				font-size: 25px;
			}
			.h-sm{
				font-size: 20px;
			}
			.h-xs{
				font-size: 16px;
			}
		</style>
    </head>
    <body>
    	<div class="col-md-12" style="height:30px; background-color:#ddd; z-index:99;margin-bottom:0px; margin-top:0px;">
	    	<ul class="top-nav pull-right">
	    		<li><a href="#"> 欢迎 ，<span class="badge badge-default" id="username_id">卢先生</span></a></li>
		    	<li><a href="<?php echo ($home); ?>Index/message"><i class="fa fa-envelope"></i> 消息 <span class="label label-default">0</span> </a></li>
		    	<li><a href="<?php echo ($home); ?>Index/profile"><i class="fa fa-gear"></i> 帐号管理 </a></li>
		    	<li><a href="#"> 退出 </a></li>
	    	</ul>
    	</div>
    	<div class="col-md-12" style="height:80px; z-index:99; margin-bottom:0px; background-color:#fff;">
	    	<a href="http://121.40.92.53/ycbb/index.php" class="pull-left" style="margin-top:20px; margin-left:50px;"><img src="/Public/img/logo.png"></a>
    	</div>
        <nav class="navbar navbar-default" role="navigation" style="background-color:#f87d22;">
			  <div class="container-fluid">
			    <div class="navbar-header">
			      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" 
			      data-target="#bs-example-navbar-collapse-1">
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			      </button>
			    </div>
			    <!-- Collect the nav links, forms, and other content for toggling -->
			    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="border-bottom:0px;">
			      <ul class="nav navbar-nav navbar-left nav-p">
			      	<li><a href="<?php echo ($home); ?>Index/orderIncomplete"> 我的订单 </a></li>
			      	<li><a href="<?php echo ($home); ?>Index/unbid"> 我要抢单 </a></li>
			      	<li><a href="<?php echo ($home); ?>Index/event"> 限时活动 </a></li>
			      	<li><a href="<?php echo ($home); ?>Index/comment"> 服务评价 </a></li>
			      	<li><a href="<?php echo ($home); ?>Index/project"> 我的项目 </a></li>
			      </ul>
			    </div><!-- /.navbar-collapse -->
			  </div><!-- /.container-fluid -->
		</nav>
		<div class="container">
			<div class="col-md-12" style="text-align:center; margin-top:10px;">
				<div class="panel panel-default">
				  <div class="panel-header" style="border-bottom: 1px solid #eee;">
				 	 <h3 class="h h-lg" style="color:#666; padding-top:10px;">账号管理</h3>
				  </div>
				  <div class="panel-body">
				  		<div class="col-md-12" style="text-align:left;">
					  		<div class="col-md-12" style="padding-top:25px;">
					  			<div class="col-md-12"><label class="label label-info bs-user" id="mer_name"></label></div>
					    		<div class="col-md-12">
					    			<div class="bs-callout bs-callout-warning" id="mer_intro"></div>
					    		</div>
					    		<div class="col-md-12"></div>
								<div class="col-md-12" style="padding-left:0px;">
						    		 <div class="col-md-2">
					    			  <span class="thumbnail">
								      <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTcxIiBoZWlnaHQ9IjE4MCIgdmlld0JveD0iMCAwIDE3MSAxODAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjxkZWZzLz48cmVjdCB3aWR0aD0iMTcxIiBoZWlnaHQ9IjE4MCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjU4LjU0MTY2NjAzMDg4Mzc5IiB5PSI5MCIgc3R5bGU9ImZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMHB0O2RvbWluYW50LWJhc2VsaW5lOmNlbnRyYWwiPjE3MXgxODA8L3RleHQ+PC9nPjwvc3ZnPg==" id="mer_header"/>
								      <!-- <button class="btn btn-info">删除 <i class="fa fa-remove fa-la"></i></button> -->
									 </span>
									 </div>
								  </div>
								</div>
					  		</div>
				  		<div class="col-md-12" style="text-align:left; border-top:1px solid #eee; padding-top:25px;">
				  			<!-- Table -->
						  <table class="table" id="project-table">
						  	<thead>
							  	<tr>
								  	<th>手机</th>
								  	<th>公司座机</th>
								  	<th>地址</th>
							  	</tr>
						  	</thead>
						  	<tbody>
							  	<tr>
								  	<td class="col-md-3"><p id="mer_mobile"></p></td>
								  	<td class="col-md-4"><p id="mer_tel"></p></td>
								  	<td class="col-md-5"><p id="mer_address"></p></td>
								  	
							  	</tr>
							  	<tr>
							  		<td colspan="4">
							  			<div class="form-group">
						  				<h4 class="h h-sm" style="padding-top:20px; padding-left: 5px;padding-bottom:10px;text-align:left"> 公司简介 </h4>
								    	<div class="well" style="width:100%;height:200px; resize:none; text-align:left" 
								    	name="note" disabled>
									    	<p>介绍：<span id="mer_intro"></span></p>
											<p>WIFI：<span id="mer_wifi"></span></p>
											<p>营业时间：<span id="mer_hours"></span></p>
								    	</div>
								    	</div>
							  		</td>
							  	</tr>
						  	</tbody>
						  </table>
						  <div class="col-xs-offset-9 col-md-3">
						  	<a class="btn btn-info col-md-5" href="<?php echo ($home); ?>Index/updatePassword"> 密码修改 </a>
					  		<a class="btn btn-info col-xs-offset-1 col-md-5" href="<?php echo ($home); ?>Index/updateProfile"> 资料修改 </a>
						  </div>
					</div>
			</div>
		</div>
		</div>
		</div>
		
		<!-- footer -->
		<div class="footer" style="margin-top:30px;">
			<div class="col-md-12" style="text-align:center; font-size:18px; background-color:#f87d22; color:#fff; padding-top:20px;">
			<p class="copy-right">版权所有：厦门日后科技有限公司</p>
			<p>客服电话：000－0000-000 传真：0000-1234567 邮编：361000</p>
			<p>Copyright &copy; 2013-2014</p>
			</div>
		</div>
		<!-- footer end-->
		<script src="/Public/static/bootstrap/js/jquery.session.js"></script>
		<script src="/Public/static/bootstrap/js/script.js"></script>
    </body>
</html>