<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Demand" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
     <input type="hidden" name="hidden_id" value="<?php echo ($hidden_id); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
	<div class="pageHeader">
		<form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Demand/urge_merchant" method="post" id="autosubmit">
		<div class="searchBar">
			<ul class="searchContent">
			<!-- 
				<li>
					<select name="type">
					
						<option value="1">从未报价的商家</option>
						<option value="2">未连续报价的商家</option>
					</select>
										
				</li>
				 -->
			</ul>
			<div class="subBar">
				
				<ul>
					
					<li><div class="buttonActive"><div class="buttonContent"><button type="submit" >查询</button></div></div></li>
					
				</ul>
			</div>
		</div>
		</form>
	</div>

	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<!-- <li><a class="add" href="/index.php/Admin/Demand/add" target="dialog" mask="true" width="700" height="400"><span>新增</span></a></li>
				<li><a class="delete" href="/index.php/Admin/Demand/delete_recent/id/{sid_node}/navTabId/Demand.index" target="ajaxTodo"  title="你确定要删除吗？" warn="请选择节点"><span>删除</span></a></li>
				<li><a class="edit" href="/index.php/Admin/Demand/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
				 -->
			</ul>
		</div>
		<table class="list" width="100%" layoutH="116">
			<thead>
			<tr>
				<th width="60">商户id</th>
				<th width="80" >连续不报价个数</th>
                <th width="100">名称</th>
				 <th width="60">地址</th>
				  <th width="60">负责人</th>
				
			</tr>
			</thead>
			<tbody>
			<tr><td><h2>以下为从未报价</h2></td><td></td><td></td><td></td><td></td></tr>
			<?php if(is_array($nodata)): $i = 0; $__LIST__ = $nodata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($vo['merchant_id']); ?></td>
					<td>从未</td>
					<td><?php echo ($vo['merchant_name']); ?></td>
					<td><?php echo ($vo['name']); ?>，<?php echo ($vo['address']); ?></td>
					<td><?php echo ($vo['manager']); ?></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<tr><td><h2>以下为连续未报价</h2></td><td></td><td></td><td></td><td></td></tr>
			<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr >
					<td><?php echo ($vo['merchant_id']); ?></td>
					<td><?php echo ($vo['num']); ?></td>
					<td><?php echo ($vo['merchant_name']); ?></td>
					<td><?php echo ($vo['name']); ?>，<?php echo ($vo['address']); ?></td>
					<td><?php echo ($vo['manager']); ?></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			
			</tbody>
		</table>
		<div class="panelBar">
			<div class="pages">
				<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
			
				<option value="<?php echo ($numPerPage); ?>" selected=selected><?php echo ($numPerPage); ?></option>
			
			</select>
				<span>共<?php echo ($totalCount); ?>条</span>

			</div>
			<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
		</div>
		
	</div>
</div>

<!--

//-->
</script>