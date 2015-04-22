<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageContent">

	<form method="post" action="/index.php/Admin/Recent/update/navTabId/Recent.index" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
		<div class="pageFormContent" layoutH="58">
			 <div class="unit">
				<label>请选择分类：</label>
				<select name="class_id" class="required combox" >
					<option value="">请选择</option>
					<?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vocate['id']); ?>" <?php if(($vo["pid"]) == $vocate['id']): ?>selected=selected<?php endif; ?>><?php echo ($vocate['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
			<div class="unit">
				<label>名称：</label>
				<input type="text" class="required" value="<?php echo ($vo["name"]); ?>"  name="name" id="name">
			</div>
            <div class="unit">
				<label>状态：</label>
				<input type="radio" class="required" checked name="status" value='1'>可用
                <input type="radio" class="required"  name="status" value='0'>不可用
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