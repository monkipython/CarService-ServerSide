<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Answer" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
     
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
	<div class="pageHeader">
		<form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Answer" method="post">
		<div class="searchBar">
			<ul class="searchContent">
				<li>
					<label>分类名称：</label>
					<select name="pid">
					<option value="0">全部</option>
					<?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vol): $mod = ($i % 2 );++$i; if($pid == $vol['id']): ?><option value="<?php echo ($vol['id']); ?>" selected><?php echo ($vol['name']); ?></option>
						<?php else: ?>
						<option value="<?php echo ($vol['id']); ?>"><?php echo ($vol['name']); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</select>
				
				</li>
				<li>
					<label>搜索内容</label>
					<input type="text" name="keywords" value="<?php echo ($map['keywords']); ?>">
				</li>
				<li>
					<select name="timeStyle">
					<option value="0" selected>全部</option>
						<?php if($timeStyle == 1): ?><option value="1" selected>小于当前时间</option>
						<?php else: ?>
							<option value="1">小于当前时间</option><?php endif; ?>
						<?php if($timeStyle == 2): ?><option value="2" selected>大于当前时间</option>
						<?php else: ?>
							<option value="2">大于当前时间</option><?php endif; ?>
					</select>
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
	
	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<li><a class="add" href="/index.php/Admin/Answer/add" target="dialog" mask="true" width="700" height="400"><span>新增问题</span></a></li>
				<li><a class="delete" href="/index.php/Admin/Answer/del/id/{sid_node}/navTabId/Answer.index" target="ajaxTodo"  title="你确定要删除吗？" warn="请选择节点" ><span>删除</span></a></li>
				<!-- <li><a class="edit" href="/index.php/Admin/Answer/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
						 -->
			</ul>
		</div>
		<table class="list" width="100%" layoutH="116">
			<thead>
			<tr>
				<th width="60">ID</th>
				<th width="100">标题</th>
                <th width="100">所属分类</th>
				<th width="80" orderField="title">发布人</th>
				<th width="80">发布时间</th>
				 <th width="60">关注数</th>
				  <th width="60">回答数</th>
				
			</tr>
			</thead>
			<tbody>
	
			
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
					<td><?php echo ($vo['id']); ?></td>
					<td><a href="/index.php/Admin/Answer/detail/id/<?php echo ($vo['id']); ?>/furl/<?php echo ($furl); ?>" target="navTab" rel="container.index"><?php echo ($vo['title']); ?></a></td>
					<td><?php echo ($vo['pidname']); ?></td>
					<?php if(($vo['system_user_id'] > 27 )and($vo['system_user_id'] < 228)): ?><td><p style="color:blue;"><?php echo ($vo['name']); ?></p></td>
					<?php else: ?>
					<td><?php echo ($vo['name']); ?></td><?php endif; ?>
					<td><?php echo (date('Y-m-d H:i:s',$vo['addtime'])); ?></td>
					<td><?php echo ($vo['attention']); ?></td>
					<td><?php echo ($vo['answer_num']); ?></td>
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