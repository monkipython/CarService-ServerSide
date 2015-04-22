<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo ($data['title']); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="/Public/static/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/static/bootstrap/css/pagination.css" rel="stylesheet" type="text/css" />
<link href="/Public/static/bootstrap/css/style.css" rel="stylesheet" type="text/css" />
<link href="/Public/static/bootstrap/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/static/bootstrap/css/front-end.css" rel="stylesheet">
<script src="/Public/static/jquery-2.0.3.min.js" type="text/javascript"></script>
<script src="/Public/static/bootstrap/js/bootstrap.js" type="text/javascript"></script>
<!-- 自定义css -->
<link href="/Public/css/index.css" rel="stylesheet" type="text/css">
<link href="/Public/css/answer.css" rel="stylesheet" type="text/css">
      <link rel="Shortcut icon" href="/Public/img/favicon.ico" >
<!-- 自定义css -->
     <link href="/Public/static/bootstrap/css/chat.css" rel="stylesheet" type="text/css">
     <link href="/Public/static/bootstrap/css/lightbox.css" rel="stylesheet" type="text/css" />
	 <link rel="stylesheet" type="text/css" href="/Public/uploadify3.2/uploadify.css">
<style>
.nav-tabs > li.active > a:focus {
	border:none;
}

</style>

    </head>

<body>
   
<div class="container-fluid">
<div class="row">
<?php if(!empty($cookie)): ?><div class="col-md-12"
	style="height: 30px; background-color: #ddd; z-index: 99; margin-bottom: 0px; margin-top: 0px;">
	<ul class="top-nav pull-right">
		<li>欢迎 ，<a href="/user/detail/id/<?php echo ($cookie['jid']); ?>"> <span
				class="badge badge-default" id="username_id" data="<?php echo ($cookie['jid']); ?>"><?php echo ($cookie['name']); ?></span></a></li>
	
		<li><a href="/Index/message"><i class="fa fa-envelope"></i>
				消息 <span class="label label-default">0</span> </a></li>
		<li><a href="/Index/profile"><i class="fa fa-gear"></i> 帐号管理
		</a></li>
		<li><a href="javascript:;" id="loginOut"> 退出 </a></li>
	</ul>
</div><?php endif; ?>
<div class="col-md-12 col-xs-12 top-bg">
	<div class="col-md-3 col-xs-12" >
		<a href="/" class="pull-left"><img class="top-logo-answer" src="/Public/img/logo.png"></a>
				<?php if($cookie['type'] == 2): ?><a class="pull-right plateform"  href="/Plateform/unbid">
				进入商户后台 </a><?php endif; ?>

	</div>
	<div class="col-md-8 col-xs-12">
		<form class="navbar-form form-margin" role="search"  action="/Answer/search" method="get">
			<div class="form-group col-md-6 col-xs-6 padding0">
				<input type="text" class="form-control top-search-input" placeholder="Search" name="keyword" value="<?php echo ($keyword); ?>">
			</div>
			<div class="col-md-4 col-xs-6 padding3">
				<button type="submit" class="btn btn-default">搜索</button>
				<?php if(empty($cookie) ): ?><a class="add-question" href="#" data-toggle="modal" data-target="#nostatus" >我要提问</a>
				<?php else: ?>
					<a class="add-question" href="#" data-toggle="modal" data-target="#myModaladdQuestion"  data-backdrop= 'static'>我要提问</a><?php endif; ?>
			</div>
		</form>
	</div>

</div>
</div>

<div class="row ">
<div class="col-md-12 padding-none">
<nav class="navbar navbar-default top-nav-answer" role="navigation">
	<div class="container" >
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse "
			id="bs-example-navbar-collapse-1" >
			<ul class="nav navbar-nav navbar-left nav-p nav-a">
				<li><span class="sprite"></span> <a href="/Answer/"
					class="pull-left"> 全部 </a> <span class="sprite"></span></li>
				<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="/Answer/index/id/<?php echo ($vo['id']); ?>"
					class="pull-left"> <?php echo ($vo['name']); ?> </a><span class="sprite"></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>

		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>
</div>
</div>
</div>
<div class="modal fade" id="nostatus" tabindex="-1"
		role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
		data-backdrop="true">
		<div class="modal-dialog ">
			<div class="modal-content" style="border-radius:6px;">
				<div class="modal-header" style="background-color: rgb(226,229,220);border-top-right-radius:6px;border-top-left-radius:6px;">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <div class="modal-title" id="myModalLabel1">
		 			<ul class="nav nav-tabs" id="myTab"> 
				    
				      <li class="active"><a href="#member-login">车主登录</a></li> 
				        <li ><a href="#merchant-login">商家登录</a></li> 
				    </ul> 
		        </div>
		      </div>
				<div class="modal-body text-center" style="padding:0px;">
					<div
						class="  login">
						<div class="login-body" style="border-bottom-right-radius:6px;border-bottom-left-radius:6px;
border-top-right-radius:0;border-top-left-radius:0;
">

						    <div class="tab-content" style="padding:5px 0px 15px 0px;"> 
							     
						
						      <div class="tab-pane" id="member-login">
						      <?php if(empty($cookie)): ?><form role="form" action="Acount/login" method="post" id="login">
									<div class="input-group" style="padding: 5px;">
										<span
											class="input-group-addon glyphicon glyphicon-user span-left-type"></span>
										<input type="text" class="form-control input-type"
											name="username" placeholder="用户名">
									</div>
									<div class="input-group" style="padding: 5px;">
										<span
											class="input-group-addon glyphicon glyphicon-lock span-left-type "></span>
										<input type="password" class="form-control input-type"
											name="password" placeholder="密码">
										<input name="return" type="hidden" value="1">
										<input name="type" type="hidden" class="logintype" value="1">
									</div>
	
									<div class="checkbox black-color" >
										<label style="margin-left: 10px; height:10px;">
										 <input
											type="checkbox" class="checkboxsize" name="checkbox"
											value='ON' checked="checked">自动登录
										</label> 
									
									
									</div>
										<div class="clear"></div>
										<div>
									<a tabindex="0" class="btn  btn-danger login_submit"
										role="button" id="login_btn_mer" data-toggle="popover" 
										data-trigger="focus" data-placement="right" title=""
										data-container="body" data-content="">登录</a>
	
									</div>
								</form>
								<?php else: ?>
								<div class="page-header">
									<a href="javascript:;"> 欢迎 ，<span
										class="badge badge-default" id=""><?php echo ($cookie["name"]); ?>
									</span>回来
									</a>
								</div>
									<?php if($cookie['type'] == 2): ?><a href="/Plateform/unbid" class="btn btn-success"
									style="margin: 20px 0px 60px 0px;">进入后台</a> 
								<?php else: ?>
									<a href="/Answer" class="btn btn-success"
									style="margin: 20px 0px 60px 0px;">进入后台</a><?php endif; endif; ?>
						      
						      
						      
						      </div> 
						    <div class="tab-pane active" id="merchant-login">
							      	<?php if(empty($cookie)): ?><form role="form" action="Acount/login" method="post" id="login">
									<div class="input-group" style="padding: 5px;">
										<span
											class="input-group-addon glyphicon glyphicon-user span-left-type"></span>
										<input type="text" class="form-control input-type"
											name="username" placeholder="用户名">
									</div>
									<div class="input-group" style="padding: 5px;">
										<span
											class="input-group-addon glyphicon glyphicon-lock span-left-type "></span>
										<input type="password" class="form-control input-type"
											name="password" placeholder="密码">
										<input name="return" type="hidden" value="1">
										<input name="type" type="hidden" class="logintype" value="0">
									</div>
	
									<div class="checkbox black-color" >
										<label style="margin-left: 10px; height:10px;">
										 <input
											type="checkbox" class="checkboxsize" name="checkbox"
											value='ON' checked="checked">自动登录
										</label> 
									
									
									</div>
										<div class="clear"></div>
										<div>
									<a tabindex="0" class="btn  btn-danger login_submit"
										role="button" id="login_btn" data-toggle="popover" 
										data-trigger="focus" data-placement="right" title=""
										data-container="body" data-content="">登录</a>
	
									</div>
								</form>
								<?php else: ?>
								<div class="page-header">
									<a href="javascript:;"> 欢迎 ，<span
										class="badge badge-default" id=""><?php echo ($cookie["name"]); ?>
									</span>回来
									</a>
								</div>
								<?php if($cookie['type'] == 2): ?><a href="/Plateform/unbid" class="btn btn-success"
									style="margin: 20px 0px 60px 0px;">进入后台</a> 
								<?php else: ?>
									<a href="/Answer" class="btn btn-success"
									style="margin: 20px 0px 60px 0px;">进入后台</a><?php endif; endif; ?>
						
							      
							</div>
						    </div> 					
													
						  </div> 
						
						
						
					</div>
				</div>
				<div class="clear"></div>
			
			</div>
		</div>
	</div>
<div class="modal fade " id="myModaladdQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <div class="modal-title" id="myModalLabel1">
		 			我要提问
		        </div>
		      </div>
		      <div class="modal-body">
		        	  <form  class="form-horizontal" >
			            <div class="form-group">
					    <label for="registerphone" class="col-xs-4 col-sm-3 control-label">问题：</label>
					    <div class="col-xs-12 col-sm-8">
					      <textarea  class="form-control" name="title" placeholder="" style="height:150px; text-align:left; resize: none;" ></textarea>
					  </div>
					  </div>
			 
					 
					 <div class="form-group">
					    <label for="registerphone" class="col-xs-4 col-sm-3 control-label">问题分类：</label>
					    <div class="col-xs-12 col-sm-8">
							<select name="pid" style="border:1px solid #ccc;border-radius:6px;">
							  <option value ="">请选择</option>
							  <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value ="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
					     
					  </div>
					  </div>
					   <div class="form-group">
					   <label for="registerphone" class="col-xs-4 col-sm-3 control-label">上传图片：</label>
					    <div class="col-xs-12 col-sm-8">
							<div id="queue"></div>
							<input id="file_upload" name="file_upload" type="file" multiple="true">
							<div class="col-xs-12" id ="uploadThumb">
							
								
							</div>
							
						</div>
					   </div>
					  
					  
					  
			        </form>
		      </div>
		      <div class="modal-footer">
		        <a tabindex="0" class="btn btn-primary btn-sm btn-block theme-color-btn" role="button" id="addAnswer"
									data-toggle="popover" data-trigger="focus" data-placement="top"
									title=""  data-container="body"
									data-content="">提问</a>
		      </div>
		    </div>
		  </div>
		</div>
		<audio id="message-alert" hidden="true">
	  	<source src="/Public/static/bootstrap/audio/alert.wav" type="audio/wav">
	  </audio>
	
	<script src="/Public/static/bootstrap/js/script.js"></script>

	<!-- Plugin JavaScript -->
	<script src="/Public/static/bootstrap/js/jquery.easing.min.js"></script>

	
<script>
	$().ready(function() {

		$('#loginOut').click(function() {
			$.ajax({
				type : 'POST',
				url : '/Acount/loginOut',
				data : '',
				success : function(json) {
					if (json.code == 0) {
						alert(json.msg);
						location.href = "/";
					} else {
						alert('登出失败');
					}
				},
				dataType : 'json'
			});
		});
		
		$('#addAnswer').click(function(){
			var name = $('textarea[name=title]').val();
			var pid = $('select[name=pid]').val();
			var img = JSON.stringify(imgarr)
			console.log(img);
			if(name == '' || name == undefined){
				$('#addAnswer').attr('data-content','请填写问题').popover('show');
				return false;
			}
			if(pid == '' || pid == undefined){
				$('#addAnswer').attr('data-content','请填写问题分类').popover('show');
				return false;
			}
			$.ajax({
				url:'/Answer/addQuestion',
				type:"POST",
				data:{'title':name,'pid':pid,'pics':img},
				success: function(data){
					console.log(data);
					var d = data;
					var msg = d.msg, code = d.code;
					if(code == 0){
					$('#addAnswer').attr('data-content','添加成功,1秒后自动关闭').popover('show');
						  setTimeout(function () { 
							//window.location.reload() ;
							location.href = "/Answer/index/id/"+pid;
						    }, 1000);
					}else{
						$('#addAnswer').attr('data-content',msg).popover('show');
					}
				},
				  dataType: 'json'
			});
			
			
			
		});
		
		
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
			'uploader' : '/Answer/uploadQuestionPic',
			
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
		

		
		
	})
</script>
    
    <script> 
      $(function () { 
        $('#myTab a:first').tab('show');//初始化显示哪个tab 
      
        $('#myTab a').click(function (e) { 
          e.preventDefault();//阻止a链接的跳转行为 

          $(this).tab('show');//显示当前选中的链接及关联的content 
        }) 
      }) 
    </script>
   <!-- Modal -->

		<div class="container" >
		<div class="row">
			
		</div>
		<div class="row">
			<div class="col-md-12" style="text-align:center; margin-top:10px;">
			<!-- Button trigger modal -->
			
				<div class="col-md-8">
					<div class="col-md-12" style="padding-left:0px;padding-right:0px;">
						
						<?php if($data['is_answered'] == 1 and edit_answer != ''): ?><div class="modal fade " id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title" id="myModalLabel">编辑回答</h4>
						      </div>
						      	<input name="edit_id" value="<?php echo ($data['edit_answer']); ?>" type="hidden"/>
						      	<input name="answer_edit" value="<?php echo ($data['edit_answer']); ?>" type="hidden"/>
						      <div class="modal-body">
						      	<textarea class="form-control" rows="4"  name="context_pid" placeholder="请填写回答"><?php echo ($data['answer_context']); ?></textarea>
						         </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						        <button type="button" class="btn btn-primary" id="editanswer">确定</button>
						      </div>
						    </div>
						  </div>
						</div>
						<button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#myModaledit">
						编辑回答
						</button>
						<?php else: ?> 
						<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title" id="myModalLabel">回答</h4>
						      </div>
						      	<input name="issue_id" value="<?php echo ($data['issue_id']); ?>" type="hidden"/>
						      <div class="modal-body">
						      	<textarea class="form-control" rows="4"  name="context_pid_0" placeholder="请填写回答"></textarea>
						         </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						        <button type="button" class="btn btn-primary" id="answer">确定</button>
						      </div>
						    </div>
						  </div>
						</div>
						<?php if(empty($cookie) ): ?><button type="button" class="btn btn-info pull-right " data-toggle="modal" data-target="#nostatus">
							回答
							</button>
						<?php else: ?>
						<button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#myModal">
						回答
						</button><?php endif; endif; ?>
						
					</div>
					<div class="col-md-12" style="border:1px solid #d9d9d9;">
						<div class="col-md-12 text-left " style="border-bottom:1px dotted #DBDBDB; margin-right:-15px;" >
									<div class="col-md-2" >
										<a href="/user/detail/id/<?php echo ($data['system_user_id']); ?>">
										<img src="<?php echo ((isset($data['header']) && ($data['header'] !== ""))?($data['header']):'/Public/img/default_user.jpg'); ?>" width="80" height="80" class="  img-circle header-img"/>
										</a>
									</div>
									<div class="col-md-10" style="margin-left:-5px;padding:20px 0px 10px 0px;">
										<div class="col-md-12">
											<span class="theme-h6"><?php echo ($data['name']); ?>:</span>
										</div>
										
										<div class="col-md-12" style="">
										<p  class=" answer-title">
											<?php echo ($data['title']); ?>
											</p>
										</div>
										<div class="col-md-12" style="padding-top:5px;">
											<?php if(is_array($data["pics"])): $i = 0; $__LIST__ = $data["pics"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><div class="col-md-4">
												<img alt="" src="<?php echo ($img["hs"]); ?>" class="img-responsive">
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
										</div>
										<div class="col-md-12  font-grep">
											
											<div class="col-md-3">
												<a href="javascript:att(<?php echo ($data['issue_id']); ?>);" >关注</a>（<span id="att<?php echo ($data['issue_id']); ?>"><?php echo ($data['attention']); ?></span>）
											</div>
											<div class="col-md-4">
												分类（<?php echo ($data['category_name']); ?>）
											</div>
											<div class="col-md-4">
											 <?php echo ($data['addtime']); ?>
											</div>
										</div>
										
										
									</div>
									
								</div>
						<?php if(is_array($data["list"])): $i = 0; $__LIST__ = $data["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 text-left " style="border-bottom:1px dotted #DBDBDB;" >
								
									<div class="col-md-2" >
										<a href="/user/detail/id/<?php echo ($vo['system_user_id']); ?>">
										<img src="<?php echo ((isset($vo['header']) && ($vo['header'] !== ""))?($vo['header']):'/Public/img/default_user.jpg'); ?>" width="80" height="80" class="  img-circle header-img"/>
										</a>
									</div>
									<div class="col-md-10" style="margin-left:-5px;padding:20px 0px 10px 0px;">
										<div class="col-md-12">
											<span class="theme-h6"><?php echo ($vo['name']); ?>:</span>
										</div>
										
										<div class="col-md-12" style="">
										<p class=" answer-title">
											<?php echo ($vo['reply_content']); ?>
											</p>
										</div>
										
										<div class="col-md-12  font-grep">
											
											<div class="col-md-3">
												<a href="javascript:luad(<?php echo ($vo['id']); ?>);" >点赞</a>（<span  id="luad<?php echo ($vo['id']); ?>"><?php echo ($vo['laud_count']); ?></span>）
											</div>
											<div class="col-md-4">
												<a href="javascript:collect(<?php echo ($vo['id']); ?>);" >收藏</a>（<span  id="collect<?php echo ($vo['id']); ?>"><?php echo ($vo['collect_count']); ?></span>）
											</div>
											<div class="col-md-4">
											 <?php echo ($vo['addtime']); ?>
											</div>
										</div>
										<div class="col-md-12">
											<?php if(empty($cookie) ): ?><button type="button" onclick="javascript:;" class="btn btn-info pull-right" data-toggle="modal" data-target="#nostatus">
												评论
												</button>
											<?php else: ?>
												<button type="button" onclick="javascript:changepid(<?php echo ($vo['id']); ?>);" class="btn btn-info pull-right" data-toggle="modal" data-target="#myModalCom">
												评论
												</button><?php endif; ?>
										</div>
										<?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ch): $mod = ($i % 2 );++$i;?><div class="col-md-12">
													<div class="col-md-2" >
														<a href="/user/detail/id/<?php echo ($ch['system_user_id']); ?>">
														<img src="<?php echo ((isset($ch['header']) && ($ch['header'] !== ""))?($ch['header']):'/Public/img/default_user.jpg'); ?>" width="50" height="50" class="  img-circle header-img"/>
														</a>
													</div>
													<div class="col-md-10" style="margin-left:-25px;padding:20px 0px 10px 0px;">
														<div class="col-md-12">
															<span class="theme-h6"><?php echo ($ch['name']); ?> 回复了   <?php echo ($ch['pidname']); ?> :</span>
														</div>
														
														<div class="col-md-9" style="">
														<p class=" answer-title">
															<?php echo ($ch['reply_content']); ?>
															</p>
														</div>
														<div class="col-md-3 font-grep">
														 <?php echo ($ch['addtime']); ?>
														</div>
												
													</div>
												<div class="col-md-12">
												<?php if(empty($cookie) ): ?><button type="button" onclick="javascript:;" class="btn btn-info pull-right" data-toggle="modal" data-target="#nostatus">
													评论
													</button>
												<?php else: ?>
													<button type="button" onclick="javascript:changepid(<?php echo ($ch['id']); ?>);" class="btn btn-info pull-right" data-toggle="modal" data-target="#myModalCom">
												评论
												</button><?php endif; ?>
										</div>
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
								
								</div><?php endforeach; endif; else: echo "" ;endif; ?>		
								
								
								
			
					</div>
				
				</div>
				
				
				
				<div class="col-md-12" id="pagination"><?php echo ($pages); ?></div>
			</div>
			</div>
		</div>
		
		<div class="modal fade " id="myModalCom" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title" id="myModalLabel">评论</h4>
						      </div>
						      	<input name="pid" value="" type="hidden"/>
						      	<input name="issue_id" value="<?php echo ($data['issue_id']); ?>" type="hidden"/>
						      <div class="modal-body">
						      	<textarea class="form-control" rows="4"  name="context_pid_com" placeholder="请填写评论"></textarea>
						         </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						        <button type="button" class="btn btn-primary" id="Com">确定</button>
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
			//$().ready(function(){
				function luad(id){
					$.ajax({
						url:'/Answer/clickLuad',
						type:"POST",
						data:{'id':id},
						success: function(data){
							if(data.code == 0){
									//alert(data.msg);
									$('#luad'+id).text(data.data.laud_count);
							}else{
								alert(data.msg);
							}
						},
						  dataType: 'json'
					});
				};
				function collect(id){
					$.ajax({
						url:'/Answer/collect',
						type:"POST",
						data:{'id':id},
						success: function(data){
							
							if(data.code == 0){
									//alert(data.msg);
									$('#collect'+id).text(data.data.collect_count);
							}else{
								alert(data.msg);
							}
						},
						  dataType: 'json'
					});
				};
				function att(id){
					$.ajax({
						url:'/Answer/attend',
						type:"POST",
						data:{'id':id},
						success: function(data){
							
							if(data.code == 0){
									//alert(data.msg);
									$('#att'+id).text(data.data.count);
							}else{
								alert(data.msg);
							}
						},
						  dataType: 'json'
					});
				};
				function changepid(id){
					$('input[name=pid]').val(id);
				}
			//})
			$().ready(function(){
				$('#answer').click(function(){
					var context = $('textarea[name=context_pid_0]').val();
					var issue = $('input[name=issue_id]').val();
					$.ajax({
						url:'/Answer/reply',
						type:"POST",
						data:{'context':context,'issue_id':issue},
						success: function(data){
							if(data.code == 0){
									window.location.reload();
							}else{
								alert(data.msg);
								$('#myModal').modal('hide');
							}
						},
						  dataType: 'json'
					});
					
				});
				$('#editanswer').click(function(){
					var context = $('textarea[name=context_pid]').val();
					var id = $('input[name=edit_id]').val();
					$.ajax({
						url:'/Answer/editreply',
						type:"POST",
						data:{'context':context,'id':id,'pid':0},
						success: function(data){
							if(data.code == 0){
									window.location.reload();
							}else{
								alert(data.msg);
								$('#editanswer').modal('hide');
							}
						},
						  dataType: 'json'
					});
					
				});
				$('#Com').click(function(){
					var context = $('textarea[name=context_pid_com]').val();
					var issue = $('input[name=issue_id]').val();
					var pid = $('input[name=pid]').val();
					$.ajax({
						url:'/Answer/reply',
						type:"POST",
						data:{'context':context,'issue_id':issue,'pid':pid},
						success: function(data){
							if(data.code == 0){
									window.location.reload();
							}else{
								alert(data.msg);
								$('#myModalCom').modal('hide');
							}
						},
						  dataType: 'json'
					});
					
				});
				
			})
		</script>
    </body>
</html>