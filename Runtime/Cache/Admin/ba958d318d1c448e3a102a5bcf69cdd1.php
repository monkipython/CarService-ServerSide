<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/AuthGroup/insert/navTabId/AuthGroup.index/" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>权限组名称：</label>
                    <input type="text" class="required" value="<?php echo ($vo["title"]); ?>"  name="title" id="name">
                </div>
                <div class="unit">
                    <label>状态：</label>
                   	<select name="status">
                   		<?php if($vo["status"] == 1): ?><option value="1" selected>正常</option>
                   		<?php else: ?>
                   		<option value="1">正常</option><?php endif; ?>
                   		<?php if($vo["status"] == 0): ?><option value="0" selected>禁用</option>
                   		<?php else: ?>
                   		<option value ="0">禁用</option><?php endif; ?>
                   	
                   	</select>
					
                </div>
                  <div class="unit">
                    <div style="margin:15px;">勾选权限：</div>
                    <?php if(is_array($rules)): $i = 0; $__LIST__ = $rules;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$chd): $mod = ($i % 2 );++$i;?><h2 style="margin:12px;"><?php echo ($chd['auth_rule_group']); ?></h2>
                    	<?php if(is_array($chd["child"])): $i = 0; $__LIST__ = $chd["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cd): $mod = ($i % 2 );++$i;?><span style="padding:5px;"> <input type="checkbox"  name="rules[]" id="rules" value="<?php echo ($cd["id"]); ?>"><?php echo ($cd["title"]); ?></span><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                </div>
               
            </div>

            <div class="formBar">
                <ul>
                    <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                    <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
                </ul>
            </div>
        </form>

    </div>
</div>