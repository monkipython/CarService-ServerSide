<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageContent pageForm">
		<div class="pageFormContent" layoutH="58">
		<table class="list">
		<tr class="row" ><th colspan="3" class="space">系统信息</th></tr>
		<?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr class="row" ><td width="15%"><?php echo ($key); ?></td><td><?php echo ($v); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</table>
		</div>
		<div class="formBar">
			<ul>
				<li><div class="button"><div class="buttonContent"><button type="button" class="close">关闭</button></div></div></li>
			</ul>
		</div>
	</div>
</div>