<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageContent">
	
	<form method="post" action="/index.php/Admin/Public/changePwd/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>">
		<div class="pageFormContent" layoutH="58">
			
			<div class="unit">
				<label>Old Password</label>
				<input type="password" class="required" name="oldpassword">
			</div>
			
			<div class="unit">
				<label>New Password</label>
				<input type="password" class="required" name="password">
			</div>
			
			<div class="unit">
				<label>Confim Password</label>
				<input type="password" class="required" name="repassword">
			</div>
		
			<div class="unit">
				<label>Identifying code：</label>
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
</div>