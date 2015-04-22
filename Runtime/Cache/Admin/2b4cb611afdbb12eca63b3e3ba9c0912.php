<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Log" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ($_REQUEST['_sort']); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>

<div class="page">
	<div class="pageHeader">
		
	</div>
	<form method="post" action="/index.php/Admin/Log/in" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
	<div class="pageContent">
	


		<table class="list" width="100%" layoutH="40">
			<thead>
			<tr>
				<th width="60">编号</th>
				<th width="100">日志内容</th>
				<th width="100">所属模块</th>
				<th width="100">操作者</th>
                <th width="100">ip</th>
				<th width="100">IP地址</th>
				<th width="100">创建时间</th>
			</tr>
			</thead>
			<tbody>
			
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
					<td><?php echo ($vo['id']); ?></td>
					<td><?php echo ($vo['vc_operation']); ?></td>
					<td><?php echo ($vo['vc_module']); ?></td>
					<td><?php echo ($vo['creator_name']); ?></td>
					<td><?php echo ($vo['vc_ip']); ?></td>
                    <td><?php echo ($vo['ip_attr']); ?></td>
					<td><?php echo (todate($vo['createtime'])); ?></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
		</table>
		<div class="panelBar">
			<div class="pages">
				<span>共<?php echo ($totalCount); ?>条</span>
			</div>
			<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
		</div>
		</form>
	</div>
</div>