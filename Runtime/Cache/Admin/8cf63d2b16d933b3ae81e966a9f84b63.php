<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
	
	<form method="post" action="/index.php/Admin/User/resetPwd" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<input type="hidden" name="id" value="<?php echo ($_GET['id']); ?>">
		<div class="pageFormContent" layoutH="58">
			
			<div class="unit">
				<label>密码</label>
				<input type="password" class="required" name="password">
			</div>
		
			<div class="unit">
				<label>验证码</label>
				<input type="text" class="required" name="verify"> 
				<img src="/index.php/Admin/Public/verify/" BORDER="0" ALT="click" id="verifyImg" onClick="fleshVerify()" style="cursor:pointer" align="absmiddle">
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