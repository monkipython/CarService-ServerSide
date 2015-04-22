<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Order" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Order" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                    <li>
                        <label>订单号：</label>
                        <input type="text" name="order_no" value="<?php echo ($_REQUEST["order_no"]); ?>" class="medium" >
                    </li>
                    <li>
                    	<label>订单状态</label>
                    	<select name="status">
                    		<option >请选择</option>
                    		<?php if($_REQUEST["status"] == 0): ?><option value="0" selected>未完成订单</option>
                    		<?php else: ?>
                    		<option value="0">未完成订单</option><?php endif; ?>
                    		<?php if($_REQUEST["status"] == 1): ?><option value="1" selected>完成订单</option>
                    		<?php else: ?>
                    		<option value="1">完成订单</option><?php endif; ?>
                    		<?php if($_REQUEST["status"] == 2): ?><option value="2" selected>违约订单</option>
                    		<?php else: ?>
                    		<option value="2">违约订单</option><?php endif; ?>
                    		<?php if($_REQUEST["status"] == 3): ?><option value="3" selected>用户关闭订单</option>
                    		<?php else: ?>
                    		<option value="3">用户关闭订单</option><?php endif; ?>
                    		
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
               <!--  <li><a class="edit" href="/index.php/Admin/Order/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>查看</span></a></li> -->
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                	<th width="40">订单号</th>
                    <th width="100">服务项目</th>
                    <th width="80">用户</th>
                    <th width="80">商户</th>
                    <th width="40">订单状态</th>
                    <th width="40">总价（元）</th>
                    <th width="60">总时长（分）</th>
                    <th width="60">车ID</th>
                    <th width="100">添加时间</th>
                    <th width="100">到店时间</th>
      
                </tr>
            </thead>
            <tbody>


            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
               		<td><?php echo ($vo['order_no']); ?></td>
               		<td><a href="/Admin/Demand/detail/id/<?php echo ($vo['sub_id']); ?>/furl/<?php echo ($furl); ?>" target="navTab" rel="Demand.detail"><?php echo ($vo['service_name']); ?></a></td>
                    <td><?php echo (getmembername($vo['member_id'])); ?></td>
                    <td><?php echo (getmerchantname($vo['merchant_id'])); ?></td>
                    <?php if($vo["status"] == 0): ?><td>未完成</td>
                    <?php elseif($vo["status"] == 1): ?>
                    <td>完成</td>
                    <?php elseif($vo["status"] == 2): ?>
                    <td>订单违约</td>
                    <?php elseif($vo["status"] == 3): ?>
                    <td>用户关闭订单</td>
                    <?php else: ?>
                    <td>未知错误</td><?php endif; ?>
                    <td><?php echo ($vo['total_price']); ?></td>
                    <td><?php echo ($vo['total_time']); ?></td>
                    <td><?php echo ($vo['cart_id']); ?></td>
                    <td><?php echo (todate($vo['addtime'])); ?></td>
                    <td><?php echo (todate($vo['reach_time'])); ?></td>
                   <!--  <?php if($vo["type"] == 1): ?><td>普通需求</td>
                    <?php elseif($vo["type"] == 3): ?>
                    <td>保养需求</td>
                    <?php else: ?>
                		    未知错误<?php endif; ?>
                     -->
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