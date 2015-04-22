<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/RunLog" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/RunLog" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                    <li>
                        <label>status：</label>
                        <select name="status" >
                        	<option >请选择</option>
                        	<?php if($_REQUEST['status'] == 1): ?><option value="0" selected>成功</option>
                        	<?php else: ?>
                        		<option value="0">成功</option><?php endif; ?>
                        	<?php if($_REQUEST['status'] == -1): ?><option value="1" selected>失败</option>
                        	<?php else: ?>
                        		<option value="1">失败</option><?php endif; ?>
                        </select>
                    </li>
                     <li>
                        <label>position：</label>
                        <select name="position" >
                        	<option >请选择</option>
                        	<?php if($_REQUEST['position'] == 'cronAnswer'): ?><option value="cronAnswer" selected>cronAnswer</option>
                        	<?php else: ?>
                        		<option value="cronAnswer">cronAnswer</option><?php endif; ?>
                        	<?php if($_REQUEST['position'] == 'cronRecent'): ?><option value="cronRecent" selected>cronRecent</option>
                        	<?php else: ?>
                        		<option value="cronRecent">cronRecent</option><?php endif; ?>
                        	<?php if($_REQUEST['position'] == 'cronPush'): ?><option value="cronPush" selected>cronPush</option>
                        	<?php else: ?>
                        		<option value="cronPush">cronPush</option><?php endif; ?>
                        	<?php if($_REQUEST['position'] == 'Jpush'): ?><option value="Jpush" selected>Jpush</option>
                        	<?php else: ?>
                        		<option value="Jpush">Jpush</option><?php endif; ?>
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
            	<li><a class="add" href="/index.php/Admin/RunLog/add" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>新增</span></a></li>
               <li><a class="edit" href="/index.php/Admin/RunLog/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>查看</span></a></li> 
            	<li><a class="delete" href="/index.php/Admin/RunLog/foreverdelete/id/{sid_node}/navTabId/RunLog.index" target="ajaxTodo"  title="你确定要删除吗？" warn="请选择节点" ><span>删除</span></a></li> 
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                	 <th width="40">ID</th>
                    <th width="65">异常点</th>
                    <th width="100">msg</th>
                    <td width="100">时间</td>
                      <td width="20">status</td>
                </tr>
            </thead>
            <tbody>


            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
               		 <td><?php echo ($vo['id']); ?></td>
                    <td><?php echo ($vo['position']); ?></td>
                    <td><?php echo ($vo['msg']); ?></td>
                    <td><?php echo ($vo['addtime']); ?></td>
                    <td><?php echo ($vo['status']); ?></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>

            </tbody>
        </table>
        <div class="panelBar">
            <div class="pages">
                <span>显示</span>
                <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
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