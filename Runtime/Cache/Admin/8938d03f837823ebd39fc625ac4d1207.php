<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/AuthService" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
	<div class="pageHeader">
		<form onsubmit="return navTabSearch(this);" action="/index.php/Admin/AuthService" method="post">
		<div class="searchBar">
			<ul class="searchContent">
				<!-- <li>
					<label>分类名称：</label>
					<input type="text" name="name" value="<?php echo ($_REQUEST["name"]); ?>" class="medium" >
				</li>
				 -->
			</ul>
			<div class="subBar">
				<ul>
					<li><div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div></li>
					
				</ul>
			</div>
		</div>
		</form>
	</div>

	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<li><a class="icon" href="/index.php/Admin/AuthService/nopermit/id/{sid_node}/navTabId/AuthService.index" target="ajaxTodo"  title="你确定要不通过吗？" warn="请选择节点"><span>不通过</span></a></li>
				<li><a class="check icon" href="/index.php/Admin/AuthService/execAction/id/{sid_node}/navTabId/AuthService.index" target="ajaxTodo"   warn="请选择节点"><span>通过</span></a></li>
			</ul>
		</div>
		<table class="list" width="100%" layoutH="116">
			<thead>
			<tr>
				<th width="40">ID</th>
                <th width="40">审核项目</th>
                <th width="40">商家名称</th>
                 <th width="40">价格</th>
                <th width="40">耗时（分）</th>
                <th width="40">添加时间</th>
				<th width="80" >简介</th>
			</tr>
			</thead>
			<tbody>
	
			
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
					<td><?php echo ($vo['id']); ?></td>
					<td><?php echo ($vo['name']); ?></td>
					<td><?php echo ($vo['merchant_name']); ?></td>
					<td><?php echo ($vo['price']); ?></td>
					<td><?php echo ($vo['timeout']); ?></td>
					<td><?php echo (date("Y-m-d H:i:s",$vo['addtime'])); ?></td>
			
					<td>
					<!-- <a href="/index.php/Admin/AuthService/edit/id/<?php echo ($vo['id']); ?>/" target="navTab" >查看详细</a> -->
					<?php echo (msubstr($vo['intro'],0,60,'utf-8',true)); ?>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			
			</tbody>
		</table>
		<div class="panelBar">
			<div class="pages">
				<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="5" <?php if(($numPerPage) == "5"): ?>selected=selected<?php endif; ?>>5</option>
				<option value="10" <?php if(($numPerPage) == "10"): ?>selected=selected<?php endif; ?>>10</option>
				<option value="15" <?php if(($numPerPage) == "15"): ?>selected=selected<?php endif; ?>>15</option>
				<option value="20" <?php if(($numPerPage) == "20"): ?>selected=selected<?php endif; ?>>20</option>
			</select>
				<span>共<?php echo ($totalCount); ?>条</span>

			</div>
			<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
		</div>
	</div>
</div>