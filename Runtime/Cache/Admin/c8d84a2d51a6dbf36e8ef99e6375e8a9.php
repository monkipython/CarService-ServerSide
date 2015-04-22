<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Activity" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Activity" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                    <li>
                        <label>活动名称：</label>
                        <input type="text" name="name" value="<?php echo ($_REQUEST["name"]); ?>" class="medium" >
                    </li>
                    <li>
                        <label>状态：</label>
                        <select class="combox" name="status">
                            <option value="">请选择</option>
                            <option value="1" <?php if(($_REQUEST["status"]) == "1"): ?>selected=selected<?php endif; ?>>已审核</option>
                            <option value="0" <?php if(($_REQUEST["status"]) == "0"): ?>selected=selected<?php endif; ?>>未审核</option>
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
                <li><a class="edit" href="/index.php/Admin/Activity/edit/id/{sid_node}" target="navTab" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                    <th width="100">活动名称</th>
                    <th width="100">商家名称</th>
                    <th width="100">秒杀价</th>
                    <th width="100">门市价</th>
                    <th width="100">剩余量</th>
                    <th width="100">开始时间</th>
                    <th width="100">结束时间</th>
                    <th width="100">使用车型</th>
                    <th width="70">是否有效</th>
                    <th width="70">状态</th>
                    <th width="100">详细信息</th>
                    <th width="100">添加时间</th>
                </tr>
            </thead>
            <tbody>


            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
                    <td><?php echo ($vo['name']); ?></td>
                    <td><?php echo ($vo['merchant_name']); ?></td>
                    <td><?php echo ($vo['second_price']); ?></td>
                    <td><?php echo ($vo['market_price']); ?></td>
                    <td><?php echo ($vo['remain']); ?></td>
                    <td><?php echo (todate($vo['start_time'])); ?></td>
                    <td><?php echo (todate($vo['end_time'])); ?></td>
                    <td><?php echo ($vo['cart_model']); ?></td>
                    <td><?php echo (getstatus($vo['effect'])); ?></td>
                    <td><?php echo (getstatus($vo['status'])); ?></td>
                    <td><?php echo ($vo['detail']); ?></td>
                    <td><?php echo (todate($vo['addtime'])); ?></td>
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