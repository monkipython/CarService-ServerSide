<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/index.php/Admin/Member" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($_REQUEST['pageNum']) && ($_REQUEST['pageNum'] !== ""))?($_REQUEST['pageNum']):1); ?>"/>
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>"/>
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ((isset($_REQUEST['_sort']) && ($_REQUEST['_sort'] !== ""))?($_REQUEST['_sort']):'1'); ?>"/>
    <input type="hidden" name="listRows" value="<?php echo ($_REQUEST['listRows']); ?>"/>
    <?php if(is_array($map)): $i = 0; $__LIST__ = $map;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><input type="hidden" name="<?php echo ($key); ?>" value="<?php echo ($_REQUEST[$key]); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>


<div class="page">
    <div class="pageHeader">
        <form onsubmit="return navTabSearch(this);" action="/index.php/Admin/Member" method="post">
            <div class="searchBar">
                <ul class="searchContent">
                    <li>
                        <label>用户昵称：</label>
                        <input type="text" name="keywords" value="<?php echo ($keywords); ?>" class="medium" >
                    </li>
                    <li>
                        <label>联系电话：</label>
                        <input type="text" name="mobile" value="<?php echo ($mobile); ?>" class="medium" >
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
              <!--   <li><a class="edit" href="/index.php/Admin/Member/edit/id/{sid_node}" target="navTab" mask="true" warn="请选择节点" width="700" height="400"><span>修改</span></a></li> -->
            </ul>
        </div>
        <table class="list" width="100%" layoutH="116">
            <thead>
                <tr>
                	<th width="40">ID</th>
                    <th width="70">用户昵称</th>
                    <th width="70">注册手机号</th>
                    <th width="40">头像</th>
                    <th width="100">性别</th>
                    <th width="50">驾龄</th>
                    <th width="100">注册时间</th>
                    <th width="150">签名</th>
                </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
                  <td><?php echo ($vo['id']); ?></td>
                    <td><?php echo ($vo['nick_name']); ?></td>
                    <td><?php echo ($vo['mobile']); ?></td>
                    <td><img height="50" width="50"  src="<?php echo ((isset($vo['header']) && ($vo['header'] !== ""))?($vo['header']):'/Public/img/default_user.jpg'); ?>" /></td>
                    <?php if($vo["gender"] == 1): ?><td>男</td>
                    <?php elseif($vo["gender"] == 0): ?>
                    <td>女</td>
                    <?php else: ?>
                    <td>未知</td><?php endif; ?>
                    <td><?php echo ($vo['driving_exp']); ?></td>
                    <td><?php echo (todate($vo['add_time'])); ?></td>
                     <td><?php echo ($vo['signature']); ?></td>
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