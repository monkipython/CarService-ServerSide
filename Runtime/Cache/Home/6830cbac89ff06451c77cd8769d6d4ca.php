<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>问答个人中心</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/Public/static/bootstrap/css/front-end.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/pagination.css" rel="stylesheet" type="text/css" />
        <link href="/Public/static/bootstrap/css/style.css" rel="stylesheet" type="text/css" />
<!--         <link href="/Public/static/bootstrap/css/prettify.css" rel="stylesheet" type="text/css" /> -->
        <link href="/Public/static/bootstrap/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
        <script src="/Public/static/jquery-2.0.3.min.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/jquery.paginatetable.js" type="text/javascript"></script>
		<script src="/Public/static/bootstrap/js/bootstrap-dialog.min.js" type="text/javascript"></script>
<!-- 		<script src="/Public/static/bootstrap/js/run_prettify.js" type="text/javascript"></script> -->
		<!-- 自定义css -->
		<link href="/Public/css/index.css" rel="stylesheet" type="text/css">
		<!-- 上传图片 -->
		<link rel="stylesheet" type="text/css" href="/Public/uploadify3.2/uploadify.css">
		<script src="/Public/uploadify3.2/jquery.uploadify.js" type="text/javascript"></script>
		<style>
			.margin-auto{
				margin-left:50px;
			}
			.text-inde{
				text-indent:1em;
			}
		</style>
    </head>
    <body>
    
   <div class="col-md-12" style="height:30px; background-color:#ddd; z-index:99;margin-bottom:0px; margin-top:0px;">
	    	<ul class="top-nav pull-right">
	    		<li>欢迎 ，<a href="/Plateform/userCenter"><span class="badge badge-default" id="username_id"><?php echo ($cookie['name']); ?></span></a></li>
		    	<li><a href="/Index/message"><i class="fa fa-envelope"></i> 消息 <span class="label label-default">0</span> </a></li>
		    	<li><a href="/Index/profile"><i class="fa fa-gear"></i> 帐号管理 </a></li>
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
	      	
	      	<li><div class="dropdown" style="margin-top:3px;">
			  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="background:none; border:0;box-shadow:none;font-size: 18px;line-height:23px;
color: #ffffff;">
			    	我要抢单
			    <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="margin-top:10px;">
			    <li role="presentation"><a role="menuitem" tabindex="-1" href="/Plateform/unbid">未报价</a></li>
			    <li role="presentation"><a role="menuitem" tabindex="-1" href="/plateform/bid">已报价</a></li>
			    
			  </ul>
			</div>
			</li>
			<li><a href="/Plateform/orderIncomplete"> 我的订单 </a></li>
	      	<li><a href="/Plateform/project"> 我的项目 </a></li>
	      	<li><a href="/Plateform/userCenter"> 管理中心 </a></li>
	      	<li style="margin-left:650px;"><img src="/Public/img/index_03.png"
						style="margin-left: 35px; margin-top: -5px;z-index:99; position:absolute;" /> <a href="/Answer"
						style="padding-top: 10px;">问答</a></li>
	      </ul>

	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
</nav>
 <script>
 	$().ready(function(){
 		$('#loginOut').click(function(){
 			$.ajax({
 				type: 'POST',
 				url:'/Acount/loginOut',
 				data:'',
 				success:function(json){
 					if(json.code==0){
 						alert(json.msg);
 						location.href = "/";
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
				  <a href="/Plateform/userCenter" class="btn btn-info active">账户中心</a>
				  <a href="/Plateform/commentCenter" class="btn btn-info">评价中心</a>
				  <a href="javascript:;" class="btn btn-info ">消息</a>
				</div>
			</div>
		</div>
			<div class="col-md-12" style=" margin-top:10px; border:1px solid #ccc; padding:20px;">
				<div class="col-md-3">
					<div class="col-md-12 text-center" >
					<img src="<?php echo ((isset($data['header']) && ($data['header'] !== ""))?($data['header']):'/Public/img/default_user.jpg'); ?>" class="img-circle img-reponsive" id="header" width="130" height="130"/>
					<p style="color:#ccc;padding:15px;">支持jpg，png，gif等上传格式</p>
					</div>
					<div class="col-md-12 "  >
						<div id="queue"></div>
						<input id="file_upload" name="file_upload" type="file" multiple="true">
					</div>	
				</div>
				<div class="col-md-9">
					<form class="form-horizontal">
					  <div class="form-group">
					    <label for="mobile" class="col-sm-2 control-label">手机账号</label>
					    <div class="col-sm-6">
					        <input type="text" class="form-control  text-inde" disabled="disabled" value="<?php echo ($data['mobile']); ?>">
					    </div>
					  </div>
					  <div class="form-group">
					    <label for="companyName" class="col-sm-2 control-label">公司名称</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control text-inde" id="companyName" placeholder="请填写公司名称" value="<?php echo ($data['merchant_name']); ?>">
					    </div>
					  </div>
					  
					  <div class="form-group">
					    <label for="charge" class="col-sm-2 control-label">负责人</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control text-inde" id="charge" placeholder="请填写公司负责人" value="<?php echo ($data['manager']); ?>">
					    </div>
					  </div>
					  	<div class="form-group">
					    <label for="tel" class="col-sm-2 control-label">公司座机</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control text-inde" id="tel" placeholder="请填写公司座机号码" value="<?php echo ($data['tel']); ?>">
					    </div>
					  </div>
					  	<div class="form-group">
					    <label  class="col-sm-2 control-label">是否有wifi</label>
					    <div class="col-sm-6">
					    <div class="radio">
					    <?php if($data["wifi_enable"] == 1): ?><label class="radio-inline">
							    <input type="radio" name="wifi_enable" value="1" checked>是
							  </label>
							  <label class="radio-inline">
							    <input type="radio" name="wifi_enable" value="0" >否
							  </label>
							      <?php else: ?>
							    <label class="radio-inline">
							    <input type="radio" name="wifi_enable" value="1" >是
							  </label>
							  <label class="radio-inline">
							    <input type="radio" name="wifi_enable" value="0" checked>否
							  </label><?php endif; ?> 
							</div>
					    </div>
					  </div>
	
					  <div class="form-group">
					   <label for="charge" class="col-sm-2 control-label">营业时间</label>
					     <div class="col-xs-1 input-group pull-left">
					     <input  class="form-control  time-padding "
					     onkeyup="this.value=this.value.replace(/\D/g,'')"  
					     onafterpaste="this.value=this.value.replace(/\D/g,'')" 
					     maxlength="2" name="time1" type="text" value="<?php echo ($data['bussstart'][0]); ?>"/>  
					       <span class="input-group-addon time-padding">时</span>
					     </div>
					         <div class="col-xs-1 input-group pull-left ">
					     <input  class="form-control  time-padding"
					     onkeyup="this.value=this.value.replace(/\D/g,'')"  
					     onafterpaste="this.value=this.value.replace(/\D/g,'')" 
					     maxlength="2" name="time2" type="text" value="<?php echo ($data['bussstart'][1]); ?>"/>  
					       <span class="input-group-addon time-padding">分</span>
					     </div>
					      <div class="col-xs-1 input-group pull-left ">
					   		 <span class="input-group-addon " style="padding:7px 0px;">-</span>
					    </div>
					         <div class="col-xs-1 input-group pull-left">
					     <input  class="form-control  time-padding "
					     onkeyup="this.value=this.value.replace(/\D/g,'')"  
					     onafterpaste="this.value=this.value.replace(/\D/g,'')" 
					     maxlength="2" name="time3" type="text" value="<?php echo ($data['bussend'][0]); ?>"/>  
					       <span class="input-group-addon time-padding">时</span>
					     </div>
					         <div class="col-xs-1 input-group pull-left">
					     <input  class="form-control  time-padding"
					     onkeyup="this.value=this.value.replace(/\D/g,'')"  
					     onafterpaste="this.value=this.value.replace(/\D/g,'')" 
					     maxlength="2" name="time4" type="text" value="<?php echo ($data['bussend'][0]); ?>"/>  
					       <span class="input-group-addon time-padding">分</span>
					  </div>
					  </div>
					  <div class="form-group">
					    <label for="charge" class="col-sm-2 control-label ">公司地址</label>
					    <div class="col-sm-6">
					    <?php echo ($data['addr']['province']); ?>,<?php echo ($data['addr']['city']); ?>,<?php echo ($data['addr']['area']); ?>
					      <input type="text" class="form-control text-inde" id="addr" placeholder="请填写公司地址" value="<?php echo ($data['address']); ?>">
					    </div>
					  </div>
					   <div class="form-group">
					    <label for="charge" class="col-sm-2 control-label ">公司简介</label>
					    <div class="col-sm-6">
					   
					      <textarea type="text" class="form-control text-inde"  rows="5" name="dec"  placeholder="请填写公司简介" ><?php echo ($data['intro']); ?></textarea>
					    </div>
					  </div>
					     <div class="form-group">
					    <label for="" class="col-sm-2 control-label">上传公司简介图</label>
					    <div class="col-sm-6" style="float:left;">
					    <div id="queue1"></div>
						<input id="file_upload1" name="file_upload1" type="file" multiple="true" style="margin:0;">
						<div class="col-xs-12" id ="uploadThumb">
							<?php if(is_array($data["pics"])): $i = 0; $__LIST__ = $data["pics"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$is): $mod = ($i % 2 );++$i;?><div class="col-xs-4 orgnize"><img src="<?php echo ($is['hs']); ?>" class="img-responsive" /></div><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					    </div>
					  </div>
					  <div class="form-group">
					    <div class="col-sm-offset-2 col-sm-10">
					    <button type="button" class="btn btn-default " id="modMer" data-toggle="popover" data-content="">提交</button>
					    
					    </div>
					  </div>
					</form>								
					
				
				</div>
				
				
			</div>
		</div>
		
		
		<!-- footer -->
		
<!-- footer -->
<div class="footer">
	<div class="col-md-12" style="text-align:center; font-size:18px; background-color:#f87d22; color:#fff; padding-top:50px; padding-bottom:40px;">
	<p class="copy-right">版权所有：厦门日厚科技有限公司</p>
	<p>客服电话：0592-5021243 传真：0592-5021243 邮编：361000</p>
	<p>Copyright &copy; caryu.com 2013-2014</p>
	</div>
</div>
		<!-- footer end-->
	<script>
		window.onload=function(){  
			
			var imgarr = new Array() ;
			var i = 0;
			<?php $timestamp = time();?>
			
			$('#file_upload').uploadify({
				'buttonClass':'margin-auto',
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('seeyoulater' . $timestamp);?>',
					'mer_session_id':'<?php echo ($cookie['session_id']); ?>'
				},
				'buttonText': '上传头像',
				'removeCompleted':true,
				'swf'      : '/Public/uploadify3.2/uploadify.swf',
				'uploader' : '/index.php/App/Merchant/merchantHeader',
				
			     onQueueComplete:function(queueData){
	                // console.log(queueData.uploadsSuccessful+'\n'+queueData.uploadsErrored)
	             },
	             onUploadSuccess: function(file,data,respone){
	            	console.log(data);
	            	 var obj =eval('('+data+')');
	            	$('#header').attr("src",obj['data']['header']);
	             },
	             onUploadError: function(file,errorCode,errorMsg,errorString){
	            	 console.log(file);
	            	 console.log(errorCode);
	            	 console.log(errorMsg);
	            	 console.log(errorString);
	           		 alert(errorMsg);
	             },
			});
			
			
			$('#file_upload1').uploadify({
				'queueID'  : 'queue1',
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('seeyoulater' . $timestamp);?>',
					'mer_session_id':'<?php echo ($cookie['session_id']); ?>'
				},
				'buttonText': '上传简介图',
				'removeCompleted':true,
				'swf'      : '/Public/uploadify3.2/uploadify.swf',
				'uploader' : '/Plateform/uploadMerIntro',
				
			     onQueueComplete:function(queueData){
	                // console.log(queueData.uploadsSuccessful+'\n'+queueData.uploadsErrored)
	             },
	             onUploadSuccess: function(file,data,respone){
	            	 var obj =eval('('+data+')');
	            	 var str = JSON.stringify(obj.data)
	            	 imgarr[i] =  str;
	            	 i++;
	            	 console.log(imgarr);
	            	 $('.orgnize').remove();
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
			
			
			$('#modMer').click(function(){
			//	var companyName = $('input[id=companyName]').val();
			//	var charge = $('input[id=charge]').val();
				var tel = $('input[id=tel]').val();
				var wifi_enable = $('input[name=wifi_enable]:checked').val();
				var addr = $('input[id=addr]').val();
				var dec = $('textarea[name=dec]').val();
				var img = JSON.stringify(imgarr);
				var time1 = $('input[name=time1]').val();
				var time2 = $('input[name=time2]').val();
				var time3 = $('input[name=time3]').val();
				var time4 = $('input[name=time4]').val();
				var time = time1+":"+time2+"-"+time3+":"+time4;
				
			//	if(companyName == '' || companyName == undefined){
			//		$('#modMer').attr('data-content','请填写公司名称').popover('show');
			//		return false;
			//	}
			//	if(charge == '' || charge == undefined){
			//		$('#modMer').attr('data-content','请填写复制人').popover('show');
		//		return false;
			//	}
				if(tel == '' || tel == undefined){
					$('#modMer').attr('data-content','请填写公司联系电话').popover('show');
					return false;
				}
				if(wifi_enable == '' || wifi_enable == undefined){
					$('#modMer').attr('data-content','请选择wifi').popover('show');
					return false;
				}
				if(addr == '' || addr == undefined){
					$('#modMer').attr('data-content','请填写公司街道地址').popover('show');
					return false;
				}
				if(time1 == '' || time1 == undefined ||time2 == '' || time2 == undefined ||time3 == '' || time3 == undefined || time4 == '' || time4 == undefined   ){
					$('#modMer').attr('data-content','请填写公司营业时间').popover('show');
					return false;
				}

				$.ajax({
					url:'/Plateform/modMerchant',
					type:"POST",
					data:{'wifi_enable':wifi_enable,'tel':tel,'intro':dec,'address':addr,'pics':img,'business_time':time},
					success: function(data){
						
						var d = data;
						var msg = d.msg, code = d.code;
						
						if(code == 0){
							$('#modMer').attr('data-content','添加成功').popover('show');
							  setTimeout(function () { 
								window.location.reload() ;
								//location.href = "/Plateform/project";
							    }, 1000);
						}else{
							$('#modMer').attr('data-content',msg).popover('show');
						}
					},
					  dataType: 'json'
				});
				
				
				
			});
			
			
		}
		</script>
    </body>
</html>