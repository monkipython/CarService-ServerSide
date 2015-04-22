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
		<form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Demand" method="post" id="autosubmit">
		<div class="searchBar">
			<ul class="searchContent">
				<li>
					<label>限定时间</label>
					<input name="min" value="<?php echo ($min); ?>" type="text"/>分
				</li>
				<li>
					<label>低于个数</label>
					<input name="biddingLim" value="<?php echo ($biddingLim); ?>" type="text"/>
				</li>
				
			</ul>
			<div class="subBar">
				
				<ul>
					<?php if($autoreload == 1): ?><li id="auto"><p style="color:green;padding: 5px;" >已启用自动刷新</p></li>
					<?php else: ?>
					<li id="auto"><p style="color:red; padding: 5px;" >已停止停止刷新</p></li><?php endif; ?>
					<li><div class="buttonActive"><div class="buttonContent"><button type="submit" id="start">查询</button></div></div></li>
					<li><div class="buttonActive"><div class="buttonContent"><button type="button" id="stop" onclick="clearTimeout(setid);">停止</button></div></div></li>
					
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
				<th width="60">ID</th>
				<th width="80" orderField="title">发布人</th>
				<th width="100">手机号</th>
                <th width="100">需求</th>
				 <th width="60">报价量</th>
				 <th width="60">添加时间</th>
				
			</tr>
			</thead>
			<tbody>
	
			
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
					<td><?php echo ($vo['id']); ?></td>
					<td><?php echo ($vo['nick_name']); ?></td>
					<td><?php echo ($vo['mobile']); ?></td>
					<td><a href="/index.php/Admin/Demand/detail/id/<?php echo ($vo['id']); ?>/furl/<?php echo ($furl); ?>" target="navTab" rel="Demand.detail"><?php echo ($vo['title']); ?></a></td>
					<td><?php echo ($vo['is_bidding']); ?></td>
					<td><?php echo (date('Y-m-d H:i:s',$vo['addtime'])); ?></td>
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
<script type="text/javascript">
	$().ready(function(){
		var min = $('input[name=min]').val();
		var lim = $('input[name=biddingLim]').val();
		if(min == '' || min == undefined ){
			if( lim == '' || lim == undefined){
				return false;
			}
		}
		 setid = setTimeout(function(){
			 $('.searchContent').append('<input name="autoreload" value="1" type="hidden"/>');
			$("#autosubmit",navTab.getCurrentPanel()).submit();
			//console.log('update');
		 },5000); 
		
	 $('#stop').click(function(){
		 $('#auto').html('<p style="color:red;padding: 5px;">已停止停止刷新</p>');
	 })
	
	});
</script>
<!--

//-->
</script>