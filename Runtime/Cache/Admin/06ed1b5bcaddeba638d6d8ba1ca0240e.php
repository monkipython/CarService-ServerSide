<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/RunLog/update/navTabId/RunLog.index/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>key：</label>
                    <input type="text" class="required" value="<?php echo ($vo["key"]); ?>"  name="key" id="key">
                </div>
         
                <div class="unit">
                    <label>value：</label>
                    <input type="text" class="required" value="<?php echo ($vo["value"]); ?>"  name="value" id="value">
                </div>
         
                <div class="unit">
                    <label>说明：</label>
                   <textarea rows="5" cols="75" name="intro" id="intro"><?php echo ($vo["intro"]); ?></textarea>
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