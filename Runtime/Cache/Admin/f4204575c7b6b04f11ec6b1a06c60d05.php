<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/MemberDemand" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/MemberDemand" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                    <li>
                        <label>需求ID：</label>
                        <input type="text" name="id" value="<?php echo ($_REQUEST["id"]); ?>" class="medium" >
                    </li>
                      <li>
                        <label>状态:：</label>
                        <select name="status">
                        	<option value="0">未完成需求</option>
                        	<option value="1">已生成订单需求</option>
                        	<option value="2">已取消需求</option>
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
               <!--  <li><a class="edit" href="/index.php/Admin/MemberDemand/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点" width="700" height="400"><span>查看</span></a></li> -->
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                	 <th width="40">ID</th>
                    <th width="65">用户</th>
                    <th width="100">需求标题</th>
                    <th width="150">简介</th>
                    <th width="60">到店时间</th>
                    <th width="60">车ID</th>
                    <th width="50">报价个数</th>
                     <th width="50">已推送公里数(km)</th>
                    <th width="60">选定商家id</th>
                    <th width="100">需求过期时间</th>
                    <th width="50">需求状态</th>
                </tr>
            </thead>
            <tbody>


            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
               		 <td><?php echo ($vo['id']); ?></td>
                    <td><?php echo (getmembername($vo['member_id'])); ?></td>
                    <td><a href="/Admin/Demand/detail/id/<?php echo ($vo['id']); ?>/furl/<?php echo ($furl); ?>" target="navTab" rel="Demand.detail"><?php echo ($vo['title']); ?></a></td>
                    <td><?php echo ($vo['description']); ?></td>
                    <td><?php echo (todate($vo['reach_time'],"Y-m-d")); ?></td>
                    <td><?php echo ($vo['cart_id']); ?></td>
                     
                    <td><?php echo ($vo['is_bidding']); ?></td>
                      <td><?php echo ($vo['range_km']); ?></td>
                    <td><?php echo (getmerchantname($vo['merchant_id'])); ?></td>
                    <td><?php echo (todate($vo['expire_time'])); ?></td>
                    <?php if($vo["status"] == 0): ?><td>未完成需求</td>
                    <?php elseif($vo["status"] == 1): ?>
                    <td>已生成订单需求</td>
                    <?php elseif($vo["status"] == 2): ?>
                    <td>取消需求</td>
                    <?php else: ?>
                    <td>未知</td><?php endif; ?>
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