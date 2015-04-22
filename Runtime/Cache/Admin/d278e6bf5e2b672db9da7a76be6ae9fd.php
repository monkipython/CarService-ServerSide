<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Merchant" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Merchant" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                  <li style="width:250px;">
                        <label>商家手机号：</label>
                        <input type="text" name="mobile" value="<?php echo ($mobile); ?>" class="medium" >
                    </li>
                    <li style="width:250px;">
                        <label>商家名称：</label>
                        <input type="text" name="keywords" value="<?php echo ($keywords); ?>" class="medium" >
                    </li>
                    <li style="width:250px;">
                        <label>激活状态：</label>
                        <select class="combox" name="is_check">
                            <option value="">请选择</option>
                            <option value="1" <?php if(($status) == "1"): ?>selected=selected<?php endif; ?>>已审核</option>
                            <option value="2" <?php if(($status) == "2"): ?>selected=selected<?php endif; ?>>未审核</option>
                        </select>
                    </li>
                      <li style="width:250px;">
                        <label>状态：</label>
                        <select class="combox" name="status">
                            <option value="">请选择</option>
                             <option value="2" <?php if(($mstatus) == "2"): ?>selected=selected<?php endif; ?>>正常</option>
                            <option value="1" <?php if(($mstatus) == "1"): ?>selected=selected<?php endif; ?>>测试</option>
                            <option value="-1" <?php if(($mstatus) == "-1"): ?>selected=selected<?php endif; ?>>封停</option>
                            
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
              <!--  <li><a class="add" href="/index.php/Admin/Merchant/add" target="navTab" mask="true" width="700" height="400"><span>新增</span></a></li>
                <li><a class="edit" href="/index.php/Admin/Merchant/edit/id/{sid_node}" target="navTab" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
             -->
             <li><a class="delete" href="/index.php/Admin/Merchant/setAcount/id/{sid_node}/status/-1" target="ajaxtodo"  warn="请选择节点" ><span>封停</span></a></li>
            <li><a class="icon" href="/index.php/Admin/Merchant/setAcount/id/{sid_node}/status/1" target="ajaxtodo"  warn="请选择节点" ><span>升为测试账号</span></a></li>
            <li><a class="add" href="/index.php/Admin/Merchant/setAcount/id/{sid_node}/status/0" target="ajaxtodo"  warn="请选择节点" ><span>升为正常账号</span></a></li>
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                     <th width="45">商家ID</th>
                    <th width="100">商家名称</th>
                    <th width="100">负责人</th>
                    <th width="100">座机电话</th>
                    <th width="100">地区</th>
                    <th width="100">联系地址</th>
                    <th width="100">简介</th>
                    <th width="100">联系电话</th>
                    <th width="70">激活</th>
                    <th width='60'>状态</th>
                    <th width="100">注册地址</th>
                     <th width="100">激活人</th>
                    <th width="100">添加时间</th>
                </tr>
            </thead>
            <tbody>


            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
                    <td><?php echo ($vo['id']); ?></td>
                    <td><a href="/index.php/Admin/Merchant/edit/id/<?php echo ($vo['id']); ?>/furl/<?php echo ($furl); ?>" target="navTab" rel="Merchant.detail"><?php echo ($vo['merchant_name']); ?></a></td>
                    <td><?php echo ($vo['manager']); ?></td>
                    <td><?php echo ($vo['tel']); ?></td>
                    <td><?php echo (getareasname($vo['area_id'])); ?></td>
                    <td><?php echo ($vo['address']); ?></td>
                    <td><?php echo ($vo['intro']); ?></td>
                    <td><?php echo ($vo['mobile']); ?></td>
                    <td><?php echo (getstatus($vo['is_check'])); ?></td>
                    <?php if($vo["status"] == 0): ?><td style="color:green">正常</td>
                    <?php elseif($vo["status"] == -1): ?>
                    <td style="color:red">封停</td>
                    <?php elseif($vo["status"] == 1): ?>
                    <td style="color:grep">测试</td>
                     <?php else: ?>
                     <td>未知</td><?php endif; ?>
                    <td><?php echo ($vo['baidumap']); ?></td>
                    <td><?php echo ($vo['salename']); ?></td>
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