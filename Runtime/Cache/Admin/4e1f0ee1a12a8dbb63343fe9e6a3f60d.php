<?php if (!defined('THINK_PATH')) exit();?>
<script language="JavaScript">
<!--
function checkName(){
	ThinkAjax.send('/index.php/Admin/User/checkAccount/','ajax=1&account='+$F('account'));
}
//-->
</script>


<div class="pageContent">
	
	<form method="post" action="/index.php/Admin/User/insert/navTabId/User.index/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<div class="pageFormContent" layoutH="58">

			<div class="unit">
				<label>Username：</label>
				<input type="text" class="required alphanumeric" size="30" maxlength="20" name="account" value="<?php echo ($vo["account"]); ?>" />
			</div>
			<div class="unit">
				<label>Password：</label>
				<input type="text" name="password" size="30" maxlength="20" class="required alphanumeric"/>
			</div>
			<div class="unit">
				<label>Email：</label>
				<input type="text" name="email" size="30" maxlength="100" class="required email"/>
			</div>
			<div class="unit">
				<label>Status：</label>
				<select name="status">
					<option value="1">启用</option>
					<option value="0">禁用</option>
				</select>
			</div>
			   <div class="unit">
                    <label>权限组：</label>
                    <select name="group_id">
                    	<?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                   
                </div>
			
			<div class="unit">
				<label>Remark：</label>
				<textarea name="remark" rows="3" cols="57"></textarea>
			</div>
			
		</div>
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">Submit</button></div></div></li>
				<li><div class="button"><div class="buttonContent"><button type="button" class="close">Cancel</button></div></div></li>
			</ul>
		</div>
	</form>
	
</div>