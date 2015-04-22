<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Service" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Service" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                    <li>
                        <label>项目名称：</label>
                        <input type="text" name="name" value="<?php echo ($_REQUEST["name"]); ?>" class="medium" >
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
                <li><a class="add" href="/index.php/Admin/Service/add" target="navTab" mask="true" width="700" height="400"><span>新增</span></a></li>
                <li><a class="edit" href="/index.php/Admin/Service/edit/id/{sid_node}" target="navTab" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li>
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                    <th width="100">项目名称</th>
                    <th width="100">商家名称</th>
                    <th width="100">商家电话</th>
                    <th width="100">地区</th>
                    <th width="100">项目分类</th>
                    <th width="100">简介</th>
                    <th width="100">价格</th>
                    <th width="100">服务时长</th>
                    <th width="100">添加时间</th>
                </tr>
            </thead>
            <tbody>


            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
                    <td><?php echo ($vo['name']); ?></td>
                    <td><?php echo ($vo['merchant_name']); ?></td>
                    <td><?php echo ($vo['merchant_tel']); ?></td>
                    <td><?php echo (getareasname($vo['area_id'])); ?></td>
                    <td><?php echo (getcatename($vo['pcat_id'])); ?>><?php echo (getcatename($vo['cat_id'])); ?></td>
                    <td><?php echo ($vo['intro']); ?></td>
                    <td><?php echo ($vo['price']); ?></td>
                    <td><?php echo ($vo['timeout']); ?>小时</td>
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