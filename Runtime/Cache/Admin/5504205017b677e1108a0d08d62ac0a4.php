<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/AuthRule/update/navTabId/AuthRule.index/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>唯一标识：</label>
                    <input type="text" class="required" value="<?php echo ($vo["name"]); ?>"  name="name" id="name">
                </div>
         
                <div class="unit">
                    <label>权限：</label>
                    <input type="text" class="required" value="<?php echo ($vo["title"]); ?>"  name="title" id="title">
                </div>
                   <div class="unit">
                    <label>权限组：</label>
                    <select name="category">
                    	<?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i; if($vo["category"] == $vi['id']): ?><option value="<?php echo ($vi["id"]); ?>" selected="selected"><?php echo ($vi["auth_rule_group"]); ?></option>
                    		<?php else: ?>
                    		<option value="<?php echo ($vi["id"]); ?>"><?php echo ($vi["auth_rule_group"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                   
                </div>
                <div class="unit">
                    <label>type：</label>
                    <input type="text" class="required" value="<?php echo ($vo["type"]); ?>"  name="type" id="type">
                </div>
                  <div class="unit">
                    <label>condition：</label>
                      <input type="text" class="" value="<?php echo ($vo["condition"]); ?>"  name="condition" id="condition">
                </div>
                <div class="unit">
                    <label>状态：</label>
                   	<select name="status">
                   		<?php if($vo["status"] == 0): ?><option value="0" selected>禁用</option>
                   		<?php else: ?>
                   		<option value ="0">禁用</option><?php endif; ?>
                   		<?php if($vo["status"] == 1): ?><option value="1" selected>正常</option>
                   		<?php else: ?>
                   		<option value="1">正常</option><?php endif; ?>
                   	</select>
					
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