<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageHeader">

	</div>

	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<li><a class="add" href="/index.php/Admin/Answer/add" target="dialog" mask="true" width="700" height="400"><span>新增问题</span></a></li>
				<li><a class="reload" href="/index.php/Admin/Answer/detail/id/<?php echo ($data['issue_id']); ?>/furl/<?php echo ($furlen); ?>"  target="navTab"  rel="container.index"><span>刷新</span></a></li>
				<li><a class="return" href="<?php echo ($furl); ?>"  target="navTab"  rel="Answer.index"><span>返回上一页</span></a></li>
			</ul>
		</div>
	
		<div class="row" layoutH="15" id="container_sc">
			<div class="col-md-12" style="margin-top:10px;padding:10px;">
			<!-- Button trigger modal -->
			
				<div class="col-md-8">

					<div class="col-md-12" style="border:1px solid #d9d9d9;">
						<div class="col-md-12 text-left parent" style="border-bottom:1px dotted #DBDBDB;  padding:15px;  " >
									<div class="col-md-2" style="float:left;" >
										<a href="/user/detail/id/<?php echo ($data['system_user_id']); ?>">
										<img src="<?php echo ((isset($data['header']) && ($data['header'] !== ""))?($data['header']):'/Public/img/default_user.jpg'); ?>" width="80" height="80" class="  img-circle header-img"/>
										</a>
									</div>
									<div class="col-md-10 answerEdit" style="padding:10px 15px 10px 15px; float:left; max-width:750px;">
										<div class="col-md-12">
										
												<?php if(($data['system_user_id'] > 27 )and($data['system_user_id'] < 228)): ?><span class="theme-h6" style="color:blue;"><?php echo ($data['name']); ?>:</span>
												<?php else: ?>
												<span class="theme-h6"><?php echo ($data['name']); ?>:</span><?php endif; ?>
											
										</div>
										
										<div class="col-md-12 changeinput" >
											<p  class=" answer-title inputedit"><?php echo ($data['title']); ?></p>
											<div class="changetext" style="display:none;">
											<textarea rows="3" cols="40" name="changetext"  data-id="<?php echo ($data['issue_id']); ?>"><?php echo ($data['title']); ?></textarea>
												<div>
												<label>分类名称：</label>
												<select name="pid" id="pid">
												<option value="0">全部</option>
												<?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vol): $mod = ($i % 2 );++$i; if($data['pid'] == $vol['id']): ?><option value="<?php echo ($vol['id']); ?>" selected><?php echo ($vol['name']); ?></option>
													<?php else: ?>
													<option value="<?php echo ($vol['id']); ?>"><?php echo ($vol['name']); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
												</select>
												</div>
											</div>
										</div>
										<div class="col-md-12 imgContainer" style="padding-top:5px;" >
											<?php if(is_array($data["pics"])): $i = 0; $__LIST__ = $data["pics"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><div class="col-md-4 imgCell" style="float:left;padding:5px;">
												<img alt="" src="<?php echo ($img["hs"]); ?>" class="img-responsive" >
												
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
										
						                   
											
											
										</div>
										<div class="clear"></div>
										<div><input id="addPic" type="file" name="image" /></div>
									   <div class="col-xs-12" id ="uploadThumb"></div>
										<div class="clear"></div>
										<div class="col-md-12  font-grep">
											
											<div class="col-md-3" style="float:left">
												<a href="javascript:att(<?php echo ($data['issue_id']); ?>);" >关注</a>（<span id="att<?php echo ($data['issue_id']); ?>"><?php echo ($data['attention']); ?></span>）
											</div>
											<div class="col-md-4" id ='category' style="float:left">
												分类（<?php echo ($data['category_name']); ?>）
											</div>
											<div class="col-md-4" style="float:left">
											 <?php echo ($data['addtime']); ?>
											</div> 
											<div class="clear"></div>
										</div>
											<div class="col-md-12 btnList" style="padding-left:0px;padding-right:0px;">
											
											<?php if($data['is_answered'] == 1 and edit_answer != ''): ?><a class=" btn" href="/index.php/Admin/Answer/replyToEditAnswer/id/<?php echo ($data['edit_answer']); ?>"  target="dialog" mask="true" width="500" height="400"><span >编辑回答</span></a>
										
											<?php else: ?> 
											
											<a class=" btn" href="/index.php/Admin/Answer/replyToAnswer/id/<?php echo ($data['issue_id']); ?>/submiter/<?php echo ($data['system_user_id']); ?>" target="dialog" mask="true" width="500" height="400"><span>回答</span></a><?php endif; ?>
											<a class=" btn edittai" href="javascript:;" style="display:none"><span >修改</span></a>
											<a class="submit hide" href="javascript:;" ><span>确认</span></a>	
											<a class="cancel hide" href="javascript:;" ><span>取消</span></a>	
										</div>
										
									</div>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
						<?php if(is_array($data["list"])): $i = 0; $__LIST__ = $data["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 text-left parent" style="border-bottom:1px dotted #DBDBDB; padding:15px;" >
								
									<div class="col-md-2"  style="float:left;" >
										<a href="/user/detail/id/<?php echo ($vo['system_user_id']); ?>">
										<img src="<?php echo ((isset($vo['header']) && ($vo['header'] !== ""))?($vo['header']):'/Public/img/default_user.jpg'); ?>" width="80" height="80" class="  img-circle header-img"/>
										</a>
									</div>
									<div class="col-md-10 answerEdit" style="padding:20px 15px 10px 15px; float:left;max-width:750px;">
										<div class="col-md-12">
											<?php if(($vo['system_user_id'] > 27 )and($vo['system_user_id'] < 228)): ?><span class="theme-h6" style="color:blue;"><?php echo ($vo['name']); ?>:</span>
												<?php else: ?>
												<span class="theme-h6"><?php echo ($vo['name']); ?>:</span><?php endif; ?>
										</div>
										
										<div class="col-md-12 changeinput" style="">
										<p class=" answer-title inputedit"><?php echo ($vo['reply_content']); ?></p>
											<div class="changetext" style="display:none;">
											<textarea rows="3" cols="40" name="changetext"  data-id="<?php echo ($vo['id']); ?>"><?php echo ($vo['reply_content']); ?></textarea>
											<a class="submitReply" href="javascript:;" ><span>确认</span></a>	
											<a class="cancel" href="javascript:;" ><span>取消</span></a>	
											<a class="delete" href="/index.php/Admin/Answer/delete_reply/id/<?php echo ($vo['id']); ?>/navTabId/Answer.index" target="ajaxTodo"  title="你确定要删除吗？"  ><span>删除</span></a>
											</div>
										</div>
										
										<div class="col-md-12  font-grep">
											
											<div class="col-md-3" style="float:left">
												<a href="javascript:luad(<?php echo ($vo['id']); ?>);" >点赞</a>（<span  id="luad<?php echo ($vo['id']); ?>"><?php echo ($vo['laud_count']); ?></span>）
											</div>
											<div class="col-md-4" style="float:left">
												<a href="javascript:collect(<?php echo ($vo['id']); ?>);" >收藏</a>（<span  id="collect<?php echo ($vo['id']); ?>"><?php echo ($vo['collect_count']); ?></span>）
											</div>
											<div class="col-md-4" style="float:left">
											 <?php echo ($vo['addtime']); ?>
											</div>
											<div class="clear"></div>
										</div>
										<div class="col-md-12 btnList">
												<a class=" btn" href="/index.php/Admin/Answer/replyToAnswer/id/<?php echo ($data['issue_id']); ?>/pid/<?php echo ($vo['id']); ?>" target="dialog" mask="true" width="500" height="400"><span>评论</span></a>
												<a class=" btn edittai" href="javascript:;" style="display:none"><span >修改</span></a>
										</div>
										<?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ch): $mod = ($i % 2 );++$i;?><div class="col-md-12 parent" style="padding-top:25px;">
													<div class="col-md-2"  style="float:left">
														<a href="/user/detail/id/<?php echo ($ch['system_user_id']); ?>">
														<img src="<?php echo ((isset($ch['header']) && ($ch['header'] !== ""))?($ch['header']):'/Public/img/default_user.jpg'); ?>" width="50" height="50" class="  img-circle header-img"/>
														</a>
													</div>
													<div class="col-md-10 answerEdit" style="padding:15px 15px 10px 15px; float:left">
														<div class="col-md-12">
															<?php if(($ch['system_user_id'] > 27 )and($ch['system_user_id'] < 228)): ?><span class="theme-h6"><span style="color:blue;"><?php echo ($ch['name']); ?></span> 回复了   <?php echo ($ch['pidname']); ?> :</span>
															<?php else: ?>
															<span class="theme-h6"><?php echo ($ch['name']); ?> 回复了   <?php echo ($ch['pidname']); ?> :</span><?php endif; ?>	
														
														</div>
														
														<div class="col-md-12 changeinput" style="">
															<p class=" answer-title inputedit"><?php echo ($ch['reply_content']); ?></p>
																<div class="changetext" style="display:none;">
																<textarea rows="3" cols="40" name="changetext"  data-id="<?php echo ($ch['id']); ?>"><?php echo ($ch['reply_content']); ?></textarea>
																<a class="submitReply" href="javascript:;" ><span>确认</span></a>	
																<a class="cancel" href="javascript:;" ><span>取消</span></a>	
																<a class="delete" href="/index.php/Admin/Answer/delete_reply/id/<?php echo ($ch['id']); ?>/navTabId/Answer.index" target="ajaxTodo"  title="你确定要删除吗？"  ><span>删除</span></a>
																</div>
															</div>
														<div class="col-md-3 font-grep" style="">
														 <?php echo ($ch['addtime']); ?>
														</div>
														<div class="col-md-12 btnList" style="margin-top:5px;">
																<a class=" btn scroll" href="/index.php/Admin/Answer/replyToAnswer/id/<?php echo ($data['issue_id']); ?>/pid/<?php echo ($ch['id']); ?>" target="dialog" mask="true" width="500" height="400"><span>评论</span></a>
																<a class=" btn edittai" href="javascript:;" style="display:none"><span >修改</span></a>
														</div>
												<div class="clear"></div>
													</div>
						
											<div class="clear"></div>
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
							<div class="clear"></div>
								</div><?php endforeach; endif; else: echo "" ;endif; ?>		
								
							<div class="clear"></div>
						
			
					</div>
				
				</div>
				
				
				
			</div>
			</div>
		
		
	</div>
</div>
<script>
function luad(id){
	$.ajax({
		url:'/index.php/Admin/Answer/clickLuad',
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
		url:'/index.php/Admin/Answer/collect',
		type:"POST",
		data:{'id':id,'type':<?php $a = $_SESSION['currentUser']['type'];if($a==0){echo 3;}elseif($a==2){echo 4;}else{echo 0;}?>},
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
		url:'/index.php/Admin/Answer/attend',
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
$().ready(function(){
	 imgarr = <?php echo json_encode($data['pics']);?>;
	 imgChange = null;
	//var imgObj=eval("("+imgarr+")");
	//img action
	//console.log(imgarr);
	$('.imgDelete').live('click',function(){
		imgChange = true;
		var index = $(this).closest('.imgCell').index();
		$(this).closest('.imgCell').hide();
		imgarr[index]="";
		//console.log(imgarr);
	});
	$('.answerEdit').bind({'mouseenter':function(){
		$(this).children('.btnList').children('.edittai').show();
	},'mouseleave':function(){
		$(this).children('.btnList').children('.edittai').hide();
	}});

	//$('.edittai').die('click');
	$('.edittai').bind('click',function(){
		$(this).closest('.parent').children('.answerEdit').unbind('mouseenter').unbind('mouseleave');
		var text = $(this).closest('.parent').children('.answerEdit').children('.changeinput');
		text.children('.inputedit').hide();
		text.children('.changetext').show();
		var img = $(this).closest('.parent').children('.answerEdit').children('.imgContainer');
	//	img.children('.imgCell').append('<div class="imgBtn"><span class="imgRepick"><input class="editPic" type="file"  /></span><span class="imgDelete">删除</span></div>');
		img.children('.imgCell').each(function(){
			var len = $(this).index();
			$(this).append('<div class="imgBtn"><span class="imgRepick"><input class="editPic" type="file" id="upload'+len+'" /></span><span class="imgDelete">删除</span></div>');
			//动态绑定
			$("#upload"+len).uploadify({
				
				'formData'     : {ajax:1,type:1},
				'buttonText': '修改',
				'removeCompleted':true,
				'swf'      : '/Public/uploadify/scripts/uploadify.swf',
				'uploader' : '/index.php/Admin/Public/uploadPicByEdit',
				'queueID ':'queue',
				'width': '55',
				'height':'22',
				hideButton :'true',
			     onQueueComplete:function(queueData){
		            // console.log(queueData.uploadsSuccessful+'\n'+queueData.uploadsErrored)
		         },
		         onUploadSuccess: function(file,data,respone){
		        	// console.log(data);
		        	
		  			 var obj=eval('(' + data + ')');
		  			 if(obj.code == 0){
			             var pic = obj['data']['hs'];
			             imgarr[len] = obj.data;
			             imgChange = true;
			          	$('.imgContainer').find('.imgCell').eq(len).find('img').attr('src',pic);
			          	
		  			 }else{
		  				alert(obj.msg); 
		  			 }
		         },
		         onUploadError: function(file,errorCode,errorMsg,errorString){
		        	 console.log(file);
		        	 console.log(errorCode);
		        	 console.log(errorMsg);
		        	 console.log(errorString);
		       		 alert(errorMsg);
		         },
			});
		
		});
		
		
		$('#addPic').show();
		$(this).hide();
		$(this).parent('.btnList').children('.submit').show();
		$(this).parent('.btnList').children('.cancel').show();
		//
	});
	//$('.cancel').die('click');
	$('.cancel').bind('click',function(){
		$(this).closest('.parent').children('.answerEdit').bind({'mouseenter':function(){
			$(this).children('.btnList').children('.edittai').show();
		},'mouseleave':function(){
			$(this).children('.btnList').children('.edittai').hide();
		}});
		var text = $(this).closest('.parent').children('.answerEdit').children('.changeinput');
		text.children('.inputedit').show();
		text.children('.changetext').hide();
		$(this).closest('.parent').children('.answerEdit').children('.imgContainer').find('.imgBtn').remove();
		$('#addPic').hide();
		$(this).parent('.btnList').children('.submit').hide();
		$(this).parent('.btnList').children('.cancel').hide();
		
	})
	$('.submit').click(function(){
		var pos = $(this).closest('.parent').children('.answerEdit').children('.changeinput');
		var text = pos.children('.changetext').children('textarea[name=changetext]').val();
		var id = pos.children('.changetext').children('textarea[name=changetext]').attr('data-id');
		var pid = $('#pid').val();
		var pidtext =$("#pid").find("option:selected").text(); 
		var $this = $(this);
		if( pid == '' || pid ==undefined){
			alert('分类未选择');
			return false;
		}
		//console.log(imgarr);return false;
		$.ajax({
			'url':'/index.php/Admin/Answer/changetext',
			'data':{'id':id,'text':text,'imgChange':imgChange,'imgarr':imgarr,'pid':pid},
			'type':'post',
			'success':function(data){
				console.log(data);
				if(data.code ==0){
					//$this.parent('.changetext').children('textarea[name=changetext]').val(data.data);
					$this.closest('.parent').children('.answerEdit').children('.changeinput').children('.inputedit').html(text);
					$this.closest('.parent').children('.answerEdit').children('.changeinput').children('.changetext').hide();
					$this.closest('.parent').children('.answerEdit').children('.changeinput').children('.inputedit').show();
					$this.closest('.parent').children('.answerEdit').children('.imgContainer').find('.imgBtn').remove();
					$('#addPic').hide();
					$('#category').html('分类（'+pidtext+'）');
					$this.parent('.btnList').children('.submit').hide();
					$this.parent('.btnList').children('.cancel').hide();
					
				}else{
					console.log(data);
					alert(data.msg);
				}
			},
			'dataType':'json'
			
		});
		$(this).closest('.parent').children('.answerEdit').bind({'mouseenter':function(){
			$(this).children('.btnList').children('.edittai').show();
		},'mouseleave':function(){
			$(this).children('.btnList').children('.edittai').hide();
		}});
	});
	$('.submitReply').click(function(){
		var text = $(this).parent('.changetext').children('textarea[name=changetext]').val();
		var id = $(this).parent('.changetext').children('textarea[name=changetext]').attr('data-id');
		var $this = $(this);
		$.ajax({
			'url':'/index.php/Admin/Answer/changeReply',
			'data':{'id':id,'text':text},
			'type':'post',
			'success':function(data){
				console.log(data);
				if(data.code ==0){
					//$this.parent('.changetext').children('textarea[name=changetext]').val(data.data);
					$this.parent('.changetext').parent('.changeinput').children('.inputedit').html(data.data);
					$this.parent('.changetext').hide();
					$this.parent('.changetext').parent('.changeinput').children('.inputedit').show();
				}else{
					alert(data.msg);
				}
			},
			'dataType':'json'
			
		});
		$(this).closest('.parent').children('.answerEdit').bind({'mouseenter':function(){
			$(this).children('.btnList').children('.edittai').show();
		},'mouseleave':function(){
			$(this).children('.btnList').children('.edittai').hide();
		}});
	})
})
</script>
<script>

	
	$('#addPic').uploadify({
		
		'formData'     : {ajax:1,type:1},
		'buttonText': '新增图片',
		'removeCompleted':true,
		'swf'      : '/Public/uploadify/scripts/uploadify.swf',
		'uploader' : '/index.php/Admin/Public/uploadPicByEdit',
		'queueID ':'queue',
	     onQueueComplete:function(queueData){
            // console.log(queueData.uploadsSuccessful+'\n'+queueData.uploadsErrored)
         },
         onUploadSuccess: function(file,data,respone){
        	// console.log(data);
        	
  			 var obj=eval('(' + data + ')');
  			 if(obj.code == 0){
	           //  var str = JSON.stringify(obj.data);
	             var pic = obj['data']['hs'];
	             var len = imgarr.length;
	             imgarr[len] = obj.data;
	             imgChange = true;
	          	$('.imgContainer').append('<div class="col-md-4 imgCell" style="float:left;padding:5px;"><img src="'+pic+'" class="img-responsive"><div class="imgBtn"><span class="imgRepick"><input class="editPic" type="file" id="upload'+len+'" /></span><span class="imgDelete">删除</span></div></div>');
		    		//动态绑定
	
					$("#upload"+len).uploadify({
						
						'formData'     : {ajax:1,type:1},
						'buttonText': '修改',
						'removeCompleted':true,
						'swf'      : '/Public/uploadify/scripts/uploadify.swf',
						'uploader' : '/index.php/Admin/Public/uploadPicByEdit',
						'queueID ':'queue',
						'width': '55',
						'height':'22',
						hideButton :'true',
					     onQueueComplete:function(queueData){
				            // console.log(queueData.uploadsSuccessful+'\n'+queueData.uploadsErrored)
				         },
				         onUploadSuccess: function(file,data,respone){
				        	// console.log(data);
				        	
				  			  var obj=eval('(' + data + ')');
				  			 if(obj.code == 0){
					             var pic = obj['data']['hs'];
					             imgarr[len] = obj.data;
					             imgChange = true;
					          	$('.imgContainer').find('.imgCell').eq(len).find('img').attr('src',pic);
					          	
				  			 }else{
				  				alert(obj.msg); 
				  			 }
				         },
				         onUploadError: function(file,errorCode,errorMsg,errorString){
				        	 console.log(file);
				        	 console.log(errorCode);
				        	 console.log(errorMsg);
				        	 console.log(errorString);
				       		 alert(errorMsg);
				         },
					});
  			 }else{
  				alert(obj.msg); 
  			 }
         },
         onUploadError: function(file,errorCode,errorMsg,errorString){
        	 console.log(file);
        	 console.log(errorCode);
        	 console.log(errorMsg);
        	 console.log(errorString);
       		 alert(errorMsg);
         },
	});


</script>