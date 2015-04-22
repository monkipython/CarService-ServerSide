<?php if (!defined('THINK_PATH')) exit();?>
    
   <!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>驾客－首页</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/Public/static/bootstrap/css/front-end.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/pagination.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/style.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
        <script src="/Public/static/jquery-2.0.3.min.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/jquery.paginatetable.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap-dialog.min.js" type="text/javascript"></script>
        <link rel="Shortcut icon" href="/Public/img/favicon.ico" >
<!-- 自定义css -->
        <link href="/Public/css/index.css" rel="stylesheet" type="text/css">
        <link href="/Public/static/bootstrap/css/chat.css" rel="stylesheet" type="text/css">
        <link href="/Public/static/bootstrap/css/lightbox.css" rel="stylesheet" type="text/css" />
        
       <link href="/Public/bootstrap-star-rating-master/css/star-rating.min.css" media="all" rel="stylesheet" type="text/css" />
		<script src="/Public/bootstrap-star-rating-master/js/star-rating.min.js" type="text/javascript"></script>
        <style>
.ycbb-service-items p {
	text-align: left;
	margin-left: 40%;
}

.h {
	font-weight: bolder;
}

.h-warning {
	color: #f87d22;
}

.h-lg {
	font-size: 25px;
}

.h-sm {
	font-size: 20px;
}

.h-xs {
	font-size: 16px;
}

table .input-border-dc {
	border: 1px solid #dcdcdc;
}

table input{
	text-align:center;
}
.table > thead > tr > th {
	padding:5px;
}
.table .input-style{
	border: 1px solid #ccc;
	border-radius: 4px;
	width: 30%;
	font-size:14px;	
	text-indent:0.5em;
}
.table .time-format{
	width:45px;
}
.table-bordered > tbody > tr > td{
	border-right:none;
	border-left:none;
}
input::-webkit-input-placeholder {
color: #D6D0CA !important; 
}
input:-moz-placeholder {
color: #D6D0CA !important; 
}
input::-moz-placeholder {
color: #D6D0CA !important; 
}
input:-ms-input-placeholder {
color: #D6D0CA !important; 
}
.margin-auto {
	margin-left: 50px;
}

.border-bot {
	padding: 5px;
	border-bottom: 1px solid #ccc;
}

.rating-xs {
	font-size: 16px;
}

.rating-disabled {
	cursor: default;
}
.control-label{
	font-size:16px;
}

</style>
        
        
    </head>
    
<body>
<div class="col-md-12" style="height:30px; background-color:#ddd; z-index:99;margin-bottom:0px; margin-top:0px;">
	    	<ul class="top-nav pull-right">
	    		<li>欢迎 ，<a href="/Plateform/userCenter"><span class="badge badge-default" id="username_id" data="<?php echo ($cookie['jid']); ?>"><?php echo ($cookie['name']); ?></span></a></li>
		    	<li><a href="javascript:;"><i class="fa fa-envelope"></i> 消息 <span id="push-count" class="label label-default">0</span> </a></li>
		    	<li><a href="/Plateform/userCenter"><i class="fa fa-gear"></i> 帐号管理 </a></li>
		    	<li><a href="javascript:;" id="loginOut"> 退出 </a></li>
	    	</ul>
</div>
 <div class="col-md-12" style="height:80px; z-index:99; margin-bottom:0px; background-color:#fff;">
 	<div style="margin-top:20px; margin-left:50px;" >
  	<a href="/"  class="pull-left"><img src="/Public/img/logo.png"></a>
  	<div class="pull-left" style="font-size:19px;">|&nbsp;&nbsp;商户后台管理</div>
  	</div>
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
	      	
	      	<li>

<!-- 
			  </button>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="margin-top:10px;">
			    <li role="presentation"><a role="menuitem" tabindex="-1" href="/Plateform/unbid">未报价</a></li>
			    <li role="presentation"><a role="menuitem" tabindex="-1" href="/plateform/bid">已报价</a></li>
			    
			  </ul>
			</div>
			 -->
			 <a href="/Plateform/unbid">我要抢单 </a>
			</li>
			<li><a class="order-history" href="/Plateform/orderIncomplete"> 我的订单 </a><label id='order-num' class='label label-danger' style="display:none"></label></li>
	      	<li><a href="/Plateform/project"> 我的项目 </a></li>
	      	<li><a href="/Plateform/userCenter"> 管理中心 </a></li>
	      	<li style="margin-left:650px;"><img src="/Public/img/index_03.png"
						style="margin-left: 35px; margin-top: -5px;z-index:99; position:absolute;" /> <a href="/Answer"
						style="padding-top: 10px;">问答</a></li>
	      </ul>

	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	  <audio id="message-alert" hidden="true">
	  	<source src="/Public/static/bootstrap/audio/alert.wav" type="audio/wav">
	  </audio>

</nav>
 <script>
 	$(document).ready(function(){
 		$('#loginOut').click(function(){
 			$.ajax({
 				type: 'POST',
 				url:'/Acount/loginOut',
 				data:'',
 				success:function(json){
 					if(json.code==0){
 						alert(json.msg);
 						location.href = "/";
 						$.session.clear();
 					}else{
 						alert('登出失败');
 					}
 				},
 		 	  dataType: 'json'
 			});
 		})
 	})
 </script>
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-left">
					<div class="btn-group" role="group" >
					  <a href="/Plateform/userCenter" class="btn btn-info ">账户中心</a>
					  <a href="/Plateform/CommentCenter" class="btn btn-info active">评价中心</a>
					  <a href="javascript:;" class="btn btn-info ">消息</a>
					</div>
				</div>
			</div>
				<div class="col-md-12" style=" margin-top:10px; border:1px solid #ccc; padding:5px;">
					<h5 class="border-bot">店铺综合评分</h5>
								<div class="col-md-12">
									 <div class="form-group">
									    <label class="col-xs-2 col-sm-2 control-label">服务态度：</label>
									    <div class="col-xs-12 col-sm-8" style="margin-left:-80px;">
									     <input id="input-id1" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs"  
										 value="<?php echo ($star['service_attitude']); ?>">
									  </div>
									</div>
									</div>
									<div class="col-md-12">
										 <div class="form-group">
									    <label class="col-xs-4 col-sm-2 control-label"> 服务质量：</label>
									    <div class="col-xs-12 col-sm-8" style="margin-left:-80px;">
									     <input id="input-id1" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs"  
										 value="<?php echo ($star['service_quality']); ?>">
									  </div>
									  </div>
									  
									</div>
									<div class="col-md-12">
										 <div class="form-group">
									    <label class="col-xs-4 col-sm-2 control-label">休闲环境：</label>
									    <div class="col-xs-12 col-sm-8" style="margin-left:-80px;">
									     <input id="input-id1" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs"  
										 value="<?php echo ($star['merchant_setting']); ?>">
									  </div>
									</div>
									</div>
				</div>
				<div class="col-md-12" style=" margin-top:10px; border:1px solid #ccc; padding:5px;">
						<h5 class="border-bot">评论（<?php echo ($data['count']); ?>）</h5>
						<div class="col-md-12" style="text-align:center; margin-top:10px;">
				<div class="panel panel-default col-md-12" id="ycbb-project-list-panel">
				  <!-- Table -->
				  <table class="table" id="project-table">
				  	<thead>
					  	<tr>
						  	<th>车主称呼</th>
						  	<th>评论时间</th>
						  	<th>评论内容</th>
						  	<th>订单号</th>
						  	<th>项目名称</th>
						  	<th>评价星级</th>
					  	</tr>
				  	</thead>
				  	<tbody>
					  	
					 <?php if(is_array($data["list"])): $i = 0; $__LIST__ = $data["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						  	<td><?php echo ($vo['nick_name']); ?></td>
						  	<td><?php echo (substr($vo['addtime'],5)); ?></td>
						  	<td><p><?php echo ($vo['desc']); ?></p></td>
						  	<td><?php echo ($vo['order_no']); ?></td>
						  	<td><?php echo ($vo['service_name']); ?></td>
						  	<td>
						  	     <input id="input-id1" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs"  
										 value="<?php echo ($vo['star']); ?>">
						  	</td>
						  

						  	
					  	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				  	</tbody>
				  </table>
				</div>
				<div class="col-md-12" id="pagination"><?php echo ($pages); ?></div>
			</div>
						
						
				</div>
		</div>
		
		<!-- footer -->
		<section class='chat-container'>
        	<header class='top-header'>
              <div class='left-nav-btn left'>
                <span class='top-header-tit glyphicon glyphicon-user'></span>
                <span class="current-chat-user" data="<?php echo ($sessionJID); ?>"></span>
                <span class='msg-count-icon badge badge-danger'>新消息</span>
              </div>
              <div class='right-nav-btn right'>
                <span id='chat-box-minimize'>显示</span>
              </div>
			 </header>
			<div class="contact-list-container" style="display:none">
	        	<div class="setting">
		            <div class='col-md-2 center'>
		              <span class='glyphicon glyphicon-user'></span> 
		            </div>
		            <div class='col-md-8 center'>
					  	<input type="text" class="contact-textfield" placeholder="电话号码"/>
		            </div>
		            <div class="col-md-2 center">
			            <span class='add-user-btn glyphicon glyphicon-search'></span> 
		            </div>
				</div>
	        <div class="contact-list">
		        <ol class='contact-box'>
		            <li class='push-contact'></li>
		          </ol>
	        </div>
        </div>
        <div class="chat-minimize-container" style="display:none">
          <div class="setting">
          </div>
          <ol class='chat-box'>
            <li class='push-msg'></li>
          </ol>
          <div class="emojicon">
	          <div class='emoji-box'></div>
	          <div class='arrow-down'></div>
          </div>
          <div class="tools">
	          <button class="choose-image-click btn btn-default btn-sm">
	          	<form class="post-img-form" enctype="multipart/form-data">
	          		<input id="selectedfile" class="select-image" type="file" name="selectedfile" multiple/>
	          		<input class="sid" type="hidden" value="<?php echo ($mer_session_id); ?>"/>
	          	</form>
	          	<i class="glyphicon glyphicon-picture"></i>
	          </button>
	          <div class="emoji-click">
	          	<img src="http://121.40.92.53/ycbb/Uploads/Emoji/1.png"/>
	          </div>
          </div>
          <div class="send-message-box">
              <textarea id="mytTextField" name="send_message" class="col-md-12" row="5"></textarea>
          </div>
        </div>
        </section>
        <div class="enlarge-img"></div>
<!-- footer -->
<div class="footer">
	
	<div class="col-md-12" style="text-align:center; font-size:15px; background-color:rgb(254,247,250); color:rgb(153,153,153); padding-top:50px; padding-bottom:5px;">
	<div class="bottom_logo"><img src="/Public/index/img/bottom_logo.png" alt="bottom logo" width="105" height="49"></div>
	<p class="copy-right">&copy; 2014 Caryu
版权所有：厦门日厚网络科技有限公司
客服电话：0592-5021243 传真：0592-5021243 </p>
	<div class="menu_bottom">

<a href="javascript:;">Support</a> <div class="sep">|</div>
<a href="/AboutUs" target="_blank">About Us</a> <div class="sep">|</div>
<a href="/ContactUs" target="_blank">Contact Us</a> <div class="sep">|</div>
<a href="/Services" target="_blank">Services</a> <div class="sep">|</div>
<a href="javascript:;">闽ICP备14019281号-1</a>


</div>
	<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254510099'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/z_stat.php%3Fid%3D1254510099' type='text/javascript'%3E%3C/script%3E"));</script>
	</div>
</div>

<!-- footer end-->
	<script type="text/javascript" src="/Public/static/bootstrap/js/jquery.base64.js"></script>
        <script type="text/javascript" src="/Public/static/bootstrap/js/strophe.js"></script>
<!--         <script type="text/javascript" src="/Public/static/bootstrap/js/strophe.register.js"></script> -->
		<script type="text/javascript" src="/Public/static/bootstrap/js/strophe.rsm.js"></script>
		<script type="text/javascript" src="/Public/static/bootstrap/js/iso8601_support.js"></script>
        <script type="text/javascript" src="/Public/static/bootstrap/js/strophe.archive.js"></script>
        <script type="text/javascript" src="/Public/static/bootstrap/js/jquery.cookie.js"></script>
        <script src="/Public/static/bootstrap/js/jquery.session.js"></script>
        <script type="text/javascript" src="/Public/static/bootstrap/js/lightbox.js"></script>
        <script type="text/javascript" src="/Public/static/bootstrap/js/WebSQL.js"></script>
        <script type="text/javascript" src="/Public/static/bootstrap/js/chat.js"></script>
        <script src="/Public/uploadify3.2/jquery.uploadify.js" type="text/javascript"></script>
	<script src="/Public/static/bootstrap/js/script.js"></script>



		<!-- footer end-->
		<script>
		$().ready(function(){
			$(".rating").rating('refresh',{ showClear: false, showCaption: false,readonly:true});
		})
		</script>
    </body>
</html>