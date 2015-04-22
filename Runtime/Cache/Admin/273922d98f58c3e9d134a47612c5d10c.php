<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageContent pageForm">
		<div class="pageHeader">
		<form onsubmit="return dwzSearch(this, 'dialog')" action="/index.php/Admin/Public/changeUser/dialogID/changeUser" method="post">
		<div class="searchBar">
			<ul class="searchContent">
				<li>
					<label>搜索名称：</label>
					<input name="keywords" type="text" value="<?php echo ($keywords); ?>"/>
				</li>
				
			</ul>
			<div class="subBar">
				<ul>
					<li><div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div></li>
					
				</ul>
			</div>
		</div>
		</form>
	</div>
		<div class="pageFormContent" layoutH="110">
		<table class="list">
		<tr class="row" ><td colspan="3" class="space" id="currentUser">当前用户:<?php echo ((isset($currentUser['id']) && ($currentUser['id'] !== ""))?($currentUser['id']):"无"); ?>,
		<?php if($currentUser["type"] == 2): ?>商户
		<?php elseif($currentUser["type"] == 0): ?>
			用户
		<?php else: ?>
			未知<?php endif; ?>
		 ,<?php echo ((isset($currentUser['name']) && ($currentUser['name'] !== ""))?($currentUser['name']):"无"); ?>,<?php echo ((isset($currentUser['phone']) && ($currentUser['phone'] !== ""))?($currentUser['phone']):"无"); ?>
		</td></tr>
		<tr>
			<th>ID</th>
			<th>类型</th>
			<th>名称</th>
			<th>电话</th>
		</tr>
		<?php if(is_array($user)): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr class="row clickable" id = "<?php echo ($v['id']); ?>" >
		
		<td width="15%"><?php echo ($v['id']); ?></td>
		<td>
		<?php if($v["type"] == 2): ?>商户
		<?php elseif($v["type"] == 0): ?>
			用户
		<?php else: ?>
			未知<?php endif; ?>	
		</td>
	<td><?php echo ($v['name']); ?></td><td><?php echo ($v['phone']); ?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</table>
		</div>
		<div class="formBar">
			<ul>
				
				<li><div class="button"><div class="buttonContent"><button type="button" class="close">关闭</button></div></div></li>
			</ul>
		</div>
	</div>
</div>
<script>
$().ready(function(){
	//定义setTimeout执行方法
	var TimeFn = null;

	$('table .clickable').click(function () {
	    // 取消上次延时未执行的方法
	   // clearTimeout(TimeFn);
	    //执行延时
	  //  TimeFn = setTimeout(function(){
	        //do function在此处写单击事件要执行的代码
	    	$('table .clickable').removeAttr('checked');
			$(this).attr('checked','checked');
	   // },300);
	});

	$('table .clickable').dblclick(function () {
	     // 取消上次延时未执行的方法
	  //  clearTimeout(TimeFn);
	    //双击事件的执行代码
		var id = $(".clickable[checked=checked]").attr('id');
		if( id == ''|| id == undefined){
			alert('请选择用户后在确认');
			return false;
		}

		$.ajax({
			url:'/index.php/Admin/Public/ajaxChangeUser',
			type:"POST",
			data:{'id':id},
			success: function(data){
				var type ;
				if(data.code == 0){
					if(data.data.type == 0){
						type = '用户';
					}else if (data.data.type ==2){
						type ='商户';
					}else{
						type ='未知';
					}
						$('#currentUser').html('当前用户:'+data.data.id+','+type+','+data.data.name+','+data.data.phone);
						$('.pageFormContent').animate({scrollTop: '0px'}, 500);
						$.pdialog.closeCurrent();
						  navTab.reloadFlag('container.index');
						 
				}else{
					alert(data.msg);
				}
			},
			  dataType: 'json'
		});
		
	})

	
})

</script>