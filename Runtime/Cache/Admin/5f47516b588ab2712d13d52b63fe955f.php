<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Recent" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
     
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
	<div class="pageHeader">
		<form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Recent" method="post">
		<div class="searchBar">
			<ul class="searchContent">
				<li>
					<label>搜索内容：</label>
					<input name="keywords" value="<?php echo ($keywords); ?>" type="text"/>
				</li>
		<!-- 	<li>
					<select name="timeStyle">
					<option value="0" selected>全部</option>
						<?php if($timeStyle == 1): ?><option value="1" selected>小于当前时间</option>
						<?php else: ?>
							<option value="1">小于当前时间</option><?php endif; ?>
						<?php if($timeStyle == 2): ?><option value="2" selected>大于当前时间</option>
						<?php else: ?>
							<option value="2">大于当前时间</option><?php endif; ?>
					</select>
				</li> -->	
				<li style="width:600px;">
                    <label>时间范围：</label>
                    <input type="text" class="date "  name="Btime" dateFmt="yyyy-MM-dd HH:mm:ss" placeholder="起始时间" value="<?php echo ($btime); ?>" />-<input type="text" class="date "  name="Etime" dateFmt="yyyy-MM-dd HH:mm:ss" placeholder="截止时间" value="<?php echo ($etime); ?>"/></li>
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
				<li><button type="button" class="checkboxCtrl" group="ids" selectType="invert" style="">反选</button></li>
				<li><a class="add" href="/index.php/Admin/Recent/add" target="dialog" mask="true" width="700" height="400"><span>新增</span></a></li>
				<li><a class="delete" href="/index.php/Admin/Recent/del/id/{sid_node}/navTabId/Recent.index" target="ajaxTodo"  title="你确定要删除吗？" warn="请选择节点"><span>删除</span></a></li>
				<!-- <li><a class="edit" href="/index.php/Admin/Recent/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
						 -->
				<li>
					<a title="确实要删除这些记录吗?" target="selectedTodo" rel="ids" posttype="string" href="/index.php/Admin/Recent/pdel/navTabId/Recent.index" class="delete"><span>批量删除</span></a>
				</li>
			</ul>
		</div>
		<table class="list" width="100%" layoutH="116">
			<thead>
			<tr>
				<th width="30">
				<input type="checkbox" class="checkboxCtrl" group="ids" />
				
				</th>
				<th width="60">ID</th>
				<th width="80" orderField="title">发布人</th>
				<th width="100">发布内容</th>
                <th width="100">发布时间</th>
                <th width="50">评论数</th>
				 <th width="60">精度</th>
				  <th width="60">维度</th>
				
			</tr>
			</thead>
			<tbody>
	
			
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
					<td><label><input type="checkbox" name="ids" value="<?php echo ($vo['id']); ?>" /></label></td>
					<td><?php echo ($vo['id']); ?></td>
					<?php if(($vo['system_user_id'] > 27 )and($vo['system_user_id'] < 228)): ?><td><p style="color:blue;"><?php echo ($vo['name']); ?></p></td>
					<?php else: ?>
					<td><?php echo ($vo['name']); ?></td><?php endif; ?>
					<td><a href="/index.php/Admin/Recent/detail/id/<?php echo ($vo['id']); ?>/furl/<?php echo ($furl); ?>" target="navTab" rel="container.index"><?php echo (msubstr($vo['content'],0,60,'utf-8',true)); ?></a></td>
					<td><?php echo (date('Y-m-d H:i:s',$vo['addtime'])); ?></td>
					<td><?php echo ($vo['comment_count']); ?></td>
					<td><?php echo ($vo['longitude']); ?></td>
					<td><?php echo ($vo['latitude']); ?></td>
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