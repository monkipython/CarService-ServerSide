<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Auth" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
	<div class="pageHeader">
		<form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Auth" method="post">
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
				<!-- <li><a class="add" href="/index.php/Admin/Auth/add" target="dialog" mask="true" width="700" height="400"><span>新增</span></a></li>
				<li><a class="delete" href="/index.php/Admin/Auth/foreverdelete/id/{sid_node}/navTabId/Auth.index" target="ajaxTodo"  title="你确定要删除吗？" warn="请选择节点"><span>删除</span></a></li>
				<li><a class="edit" href="/index.php/Admin/Auth/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
			 -->
			<li><a class="icon" href="/index.php/App/Auth/execAction/id/{sid_node}/navTabId/Auth.index" target="ajaxTodo"  title="你确定要通过？" warn="请选择节点"><span>通过</span></a></li>
			</ul>
		</div>
		<table class="list" width="100%" layoutH="116">
			<thead>
			<tr>
				<th width="40">ID</th>
                <th width="40">obj_id</th>
                <th width="40">商家名称</th>
                 <th width="40">经纬度</th>
                <th width="40">更新数据库no</th>
                <th width="40">对应操作</th>
                <th width="40">业务员id</th>
                <th width="40">提交审核时间</th>
				<th width="80" orderField="title">状态</th>
				<th width="80" >操作</th>
			</tr>
			</thead>
			<tbody>
	
			
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
					<td><?php echo ($vo['id']); ?></td>
					<td><?php echo ($vo['mark_id']); ?></td>
					<td><?php echo ($vo['check_data']['merchant_name']); ?></td>
					<td><?php echo ($vo['check_data']['longitude']); ?>,<?php echo ($vo['check_data']['latitude']); ?></td>
					<td><?php echo ($vo['db_no']); ?></td>
					<td><?php echo ($vo['check_action']); ?></td>
					<td><?php echo (getmerchantname($vo['data_org']['salesman'])); ?></td>
					<td><?php echo (date('Y-m-d H:i:d',$vo['addtime'])); ?></td>
					<td><?php echo ($vo['status']); ?></td>
					<td> <a class="check_by" href="#" onclick="checkby(<?php echo ($vo['id']); ?>)">通过</a> 
					<a href="/index.php/Admin/Auth/edit/id/<?php echo ($vo['id']); ?>/" target="navTab" rel="Auth.detail">查看详细</a>
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
<script>
				function checkby(id){
						$.ajax({
						     type: 'POST',
						     url:'/index.php/App/Auth/execAction' ,
						    data: {'id':id} ,
						    success: function(json){
						    	alert(json.msg);
						    } ,
						    dataType: 'json'
						});
				}
			</script>