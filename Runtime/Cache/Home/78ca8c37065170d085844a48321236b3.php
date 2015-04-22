<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>驾客－首页</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="Shortcut icon" href="/Public/img/favicon.ico" >
        <link href="/Public/static/bootstrap/css/front-end.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/pagination.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/style.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
        <script src="/Public/static/jquery-2.0.3.min.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap.js" type="text/javascript"></script>
		
<!-- 		<script src="/Public/static/bootstrap/js/run_prettify.js" type="text/javascript"></script> -->
		<link rel="stylesheet" type="text/css" href="/Public/uploadify3.2/uploadify.css">
		<script src="/Public/uploadify3.2/jquery.uploadify.js" type="text/javascript"></script>
		<!-- 自定义css -->
		<link href="/Public/css/index.css" rel="stylesheet" type="text/css">


<style>
.input-style{
	text-align:right;
}

</style>
    </head>
    <body>
    
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
		
			<ol class="breadcrumb">
			  	你所在的位置：
			  <li><a href="/Plateform/project">项目</a></li>
			  <li class="active">添加项目</li>
			</ol>
		</div>
			<div class="col-md-12" style=" margin-top:10px;border:1px solid #ccc;">
				<form class="form-horizontal">
				  <div class="form-group" style="margin-top:10px;">
				    <label for="inputService" class="col-sm-2 control-label">选择项目</label>
				    <div class="col-sm-10">
	     				
						<button type="button" id ="inputService" class="btn btn-primary btn-lg" style="background:rgb(248,125,34);" data-toggle="modal" data-target="#myModalSelect" data-backdrop= 'static'>
							 选择项目名
						</button>
							<input type="hidden" name="sub_id"/>
				    </div>
				  </div>
				  <div class="form-group">
				    <label for="inputPassword3" class="col-sm-2 control-label">服务时长</label>
				    <div class="col-sm-10 ">
				      <input
						type="number" name="day" value="0" class="time-format input-style"  
						 /> 天
						 <input
						type="number" name="hour" value="0" class="time-format input-style"
						 /> 时
						 <input
						type="number" name="min" value="0" class="time-format input-style"
						 /> 分
				    </div>
				  </div>
				   <div class="form-group">
				    <label for="inputPassword3" class="col-sm-2 control-label">报价</label>
				    <div class="col-sm-10 ">
				      <input
						type="number" name="price" value="0" class="time-format input-style"  
						 /> 元
						
				    </div>
				  </div>
				    <div class="form-group">
				    <label for="inputPassword3" class="col-sm-2 control-label" >项目描述</label>
				    <div class="col-sm-10 ">
				      <textarea class="form-control" 
							style="width: 100%; height: 200px; resize: none; color: black; border: 1px solid #dcdcdc;"
							placeholder="对该项目进行描述" name="dec"></textarea>
						
				    </div>
				  </div>
				 <div class="form-group">
					   <label for="file_upload" class="col-xs-4 col-sm-3 control-label">上传图片：</label>
					    <div class="col-xs-12 col-sm-8">
							<div id="queue"></div>
							<input id="file_upload" name="file_upload" type="file" multiple="true">
							<div class="col-xs-12" id ="uploadThumb">
							</div>
							
						</div>
					 </div>
				  <div class="form-group">
				    <div class="col-sm-offset-2 col-sm-10">
				     <a tabindex="0" class="btn btn-primary" role="button" id="addService"
									data-toggle="popover" data-trigger="focus" data-placement="right"
									title=""  data-container="body"
									data-content="">提交</a>
				    </div>
				  </div>
				</form>
			</div>
		</div>
		<div class="modal fade " id="myModalSelect" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <div class="modal-title" id="myModalLabel1">
		 			选择项目名
		        </div>
		      </div>
		      <div class="modal-body">
		        	 <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><h6 class=""> <?php echo ($vo['name']); ?></h6>
		        	 	<div>
		        
		        	 		<?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ch): $mod = ($i % 2 );++$i; if($ch['own'] == 0): ?><button type="button" class="btn btn-default selectService "  value="<?php echo ($ch["id"]); ?>"><?php echo ($ch["name"]); ?></button>
		        	 			 
		        	 			<?php else: ?>
		        	 			<button type="button" class="btn btn-default selectAlert disabled" value="<?php echo ($ch["id"]); ?>" data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-content="已添加过该项目"><?php echo ($ch["name"]); ?></button><?php endif; endforeach; endif; else: echo "" ;endif; ?>
		        			 </div><?php endforeach; endif; else: echo "" ;endif; ?>
		      </div>

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
		window.onload=function(){  
			
			var imgarr = new Array() ;
			var i = 0;
			<?php $timestamp = time();?>
			
			$('#file_upload').uploadify({
				
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('seeyoulater' . $timestamp);?>'
				},
				'buttonText': '<div>选择文件</div>',
				'removeCompleted':true,
				'swf'      : '/Public/uploadify3.2/uploadify.swf',
				'uploader' : '/Plateform/uploadProjectPic',
				
			     onQueueComplete:function(queueData){
	                // console.log(queueData.uploadsSuccessful+'\n'+queueData.uploadsErrored)
	             },
	             onUploadSuccess: function(file,data,respone){
	            	// console.log(data);
	            	 var obj =eval('('+data+')');
	            	 var str = JSON.stringify(obj.data)
	            	imgarr[i] =  str;
	            	 i++;
	            	 console.log(imgarr);
	            	 $('#uploadThumb').append('<div class="col-xs-4"><img src="http://121.40.92.53/ycbb/Uploads'+obj['data']['hs']+'" class="img-responsive" /></div>');
	             },
	             onUploadError: function(file,errorCode,errorMsg,errorString){
	            	 console.log(file);
	            	 console.log(errorCode);
	            	 console.log(errorMsg);
	            	 console.log(errorString);
	           		 alert(errorMsg);
	             },
			});
			$('#addService').click(function(){
				var sub_id = $('input[name=sub_id]').val();
				var day = $('input[name=day]').val();
				var hour = $('input[name=hour]').val();
				var min = $('input[name=min]').val();
				var price = $('input[name=price]').val();
				var dec = $('textarea[name=dec]').val();
				var img = JSON.stringify(imgarr)
				var timeout = day*24*60+hour*60+min*1;
				if(sub_id == '' || sub_id == undefined){
					$('#addService').attr('data-content','请选择项目').popover('show');
					return false;
				}
				if(day == '' || day == undefined){
					$('#addService').attr('data-content','请填写天数').popover('show');
					return false;
				}
				if(hour == '' || hour == undefined){
					$('#addService').attr('data-content','请填写小时').popover('show');
					return false;
				}
				if(min == '' || min == undefined){
					$('#addService').attr('data-content','请填写分钟').popover('show');
					return false;
				}
				if(price == '' || price == undefined){
					$('#addService').attr('data-content','请填写价格').popover('show');
					return false;
				}

				$.ajax({
					url:'/Plateform/doProject',
					type:"POST",
					data:{'sub_id':sub_id,'intro':dec,'price':price,'timeout':timeout,'pics':img},
					success: function(data){
						
						var d = data;
						var msg = d.msg, code = d.code;
						
						if(code == 0){
							$('#addService').attr('data-content','添加成功').popover('show');
							  setTimeout(function () { 
								//window.location.reload() ;
								location.href = "/Plateform/project";
							    }, 1000);
						}else{
							$('#addService').attr('data-content',msg).popover('show');
						}
					},
					  dataType: 'json'
				});
				
				
				
			});
			
		}
			$().ready(function(){
				$('.selectAlert').popover();
				$('.selectService').click(function(){
					$('#inputService').text($(this).text());
					$('input[name=sub_id]').val($(this).val());
					$('.modal-backdrop').hide();
					$('#myModalSelect').modal('hide');
					
				});
			})
	
		</script>
		
    </body>
</html>