<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageHeader">

	</div>

	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<li><a class="reload" href="/index.php/Admin/Demand/detail/id/<?php echo ($data['id']); ?>/furl/<?php echo ($furlen); ?>"  target="navTab"  rel="Demand.detail"><span>刷新</span></a></li>
				<li><a class="icon" href="/index.php/Admin/Demand/cancelDemand/id/<?php echo ($data['id']); ?>/navTabId/Demand.index" target="ajaxTodo"  title="你确定要取消需求吗？" ><span>取消需求</span></a></li>
			</ul>
		</div>
	
		<div class="row" layoutH="15">
			<div class="col-md-12" style="margin-top:10px;padding:10px;">
			<!-- Button trigger modal -->
			
				<div class="col-md-8">

					<div class="col-md-12" style="border:1px solid #d9d9d9;">
						<div class="col-md-12 text-left " style="border-bottom:1px dotted #DBDBDB;  padding:15px;  " >
									<div class="col-md-2" style="float:left;" >
										<h4>需求详情:</h4>
										<div class="col-md-2"  style="float:left;padding-top:15px;">
											<img src="<?php echo ((isset($data["header"]) && ($data["header"] !== ""))?($data["header"]):'/Public/img/default_user.jpg'); ?>" width ="80" height="80"/>
										</div>
									</div>
									<div class="col-md-10" style="padding:10px 15px 10px 15px; float:left;">
										<div class="col-md-12">
											<span class="theme-h6"><?php echo ($data['nick_name']); ?></span>
										</div>
										<div class="col-md-12">
											<span class="theme  answer-title">报价项目：<?php echo ($data['title']); ?> -	用户电话：<?php echo ($data['mobile']); ?></span>
										</div>
										
										<div class="col-md-12" style="">
										<p  class=" answer-title">
											用户留言：<?php echo ($data['description']); ?></br>
										
											</p>
										</div>
										<div class="col-md-12" style="padding-top:5px;">
											<?php if(is_array($data["pics"])): $i = 0; $__LIST__ = $data["pics"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><div class="col-md-4" style="float:left;padding:5px;">
												<img alt="" src="<?php echo ($img["hs"]); ?>" class="img-responsive">
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
											<div class="clear"></div>
										</div>
										<div class="col-md-12  font-grep">
											
										
											<div class="col-md-4" style="float:left">
												报价数（<?php echo ($data['is_bidding']); ?>）
											</div>
											<div class="col-md-4" style="float:left">
											 报价时间：<?php echo (date("Y-m-d H:i:s",$data['addtime'])); ?>&nbsp;&nbsp;
											</div>
											<div class="col-md-4" style="float:left">
											到店时间：<?php echo (date("Y-m-d H:i:s",$data['reach_time'])); ?>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div class="clear"></div>
								</div>
								<h4>商家报价</h4>
								<div style="min-height:80px;">
										<?php if(is_array($bidding)): $i = 0; $__LIST__ = $bidding;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ch): $mod = ($i % 2 );++$i;?><div class="col-md-12" style="padding-top:25px;">
													<div class="col-md-2"  style="float:left">
														<img src="<?php echo ((isset($ch["header"]) && ($ch["header"] !== ""))?($ch["header"]):'/Public/img/default_user.jpg'); ?>" width ="80" height="80"/>
													</div>
													<div class="col-md-10" style="padding:15px 15px 10px 15px; float:left">
														<div class="col-md-12">
															<span class="theme-h6"><?php echo ($ch['merchant_name']); ?>:</span>
														</div>
														
														<div class="col-md-9" style="">
														<p class=" answer-title">
															总价格<?php echo ($ch['total_price']); ?>元，总时间<?php echo ($ch['total_time']); ?>分，距离<?php echo ($ch['distance']); ?>公里
															</p>
															<p class=" answer-title">
																电话号码:<?php echo ($ch['mobile']); ?><br/>
																座机号：<?php echo ((isset($ch['tel']) && ($ch['tel'] !== ""))?($ch['tel']):"无"); ?><br/>
																留言：<?php echo ((isset($ch['remark']) && ($ch['remark'] !== ""))?($ch['remark']):"无"); ?>
															</p>
														</div>
														
														
												<div class="clear"></div>
													</div>
						
											<div class="clear"></div>
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>	
											<h4>附近商家</h4>
											<?php if(is_array($merchant)): $i = 0; $__LIST__ = $merchant;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ch): $mod = ($i % 2 );++$i;?><div class="col-md-12" style="padding-top:25px;">
													<div class="col-md-2"  style="float:left">
														<img src="<?php echo ((isset($ch["header"]) && ($ch["header"] !== ""))?($ch["header"]):'/Public/img/default_user.jpg'); ?>" width ="80" height="80"/>
													</div>
													<div class="col-md-10" style="padding:15px 15px 10px 15px; float:left">
														<div class="col-md-12">
															<span class="theme-h6">ID:<?php echo ($ch['id']); ?> 商家名:<?php echo ($ch['merchant_name']); ?>:</span>
														</div>
														
														<div class="col-md-9" style="">
														<p class=" answer-title">
															座机号：<?php echo ((isset($ch['tel']) && ($ch['tel'] !== ""))?($ch['tel']):"无"); ?>，手机号:<?php echo ((isset($ch['mobile']) && ($ch['mobile'] !== ""))?($ch['mobile']):"无"); ?>，地址:<?php echo ($ch['province']); echo ($ch['city']); echo ($ch['area']); echo ($ch['address']); ?>，距离:<?php echo ($ch['distance']); ?>公里
															</p>
														</div>
														
														
														<div class="clear"></div>
													</div>
						
											<div class="clear"></div>
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
									<div style="height:50px"></div>
							<div class="clear"></div>
								</div>
					
							</volist>		
								
							<div class="clear"></div>
						
			
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

</script>