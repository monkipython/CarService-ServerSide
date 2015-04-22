<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>页面提示</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='Refresh' content='<?php echo ($waitSecond); ?>;URL=<?php echo ($jumpUrl); ?>'>
<link rel="stylesheet" type="text/css" href="/Public/Css/style.css" />
</head>
<body>
<div class="message">
<table class="message"  cellpadding=0 cellspacing=0 >
	<tr>
		<td height='5'  class="topTd" ></td>
	</tr>
	<tr class="row" >
		<th class="tCenter space"><?php echo ($msgTitle); ?></th>
	</tr>
	<?php if(isset($message)): ?><tr class="row">
		<td style="color:blue"><?php echo ($message); ?></td>
	</tr><?php endif; ?>
	<?php if(isset($error)): ?><tr class="row">
		<td style="color:red"><?php echo ($error); ?></td>
	</tr><?php endif; ?>
	<?php if(isset($closeWin)): ?><tr class="row">
		<td>系统将在 <span style="color:blue;font-weight:bold"><?php echo ($waitSecond); ?></span> 秒后自动关闭，如果不想等待,直接点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 关闭</td>
	</tr><?php endif; ?>
	<?php if(!isset($closeWin)): ?><tr class="row">
		<td>系统将在 <span style="color:blue;font-weight:bold"><?php echo ($waitSecond); ?></span> 秒后自动跳转,如果不想等待,直接点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 跳转</td>
	</tr><?php endif; ?>
	<tr>
		<td height='5' class="bottomTd"></td>
	</tr>
	</table>
</div>
</body>
</html>