<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/Answer/replyToEditAction/navTabId/container.index/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                	<input type="hidden" name="id" value="<?php echo ($data['id']); ?>"/>
                 	<textarea class="form-control" rows="4" name="reply_content" placeholder="请填写回答" style="width:99%; height:180px;"><?php echo ($data['reply_content']); ?></textarea>
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