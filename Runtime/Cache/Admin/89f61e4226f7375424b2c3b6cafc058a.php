<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/City/insert/navTabId/City.index" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>名称：</label>
                    <input type="text" class="required"  name="name" id="name">
                </div>
                <div class="unit">
                    <label>上级：</label>
                    <select class="" name="province_id" id="province_id" >
                        <option value="">请选择省份</option>
                        <?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" ><?php echo ($vocate); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <select class="" name="city_id" id="city_id" >
                        <option value="">请选择城市</option>
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
<script>
    $(function() {
        $('#province_id').change(function() {
            var province = $(this).val();
            $.getJSON("/index.php/Admin/Merchant/getcity/id/" + province + '/', function(result) {
                var str = "<option value=''>请选择城市</option>";
                $.each(result, function(index, d) {
                    if (d != null && index != 'callbackType') {
                        str += "<option value='" + index + "'>" + d + "</option>";
                    }
                })
                $("#city_id").html(str);
            });
        });

        $('#city_id').change(function() {
            var province = $(this).val();
            $.getJSON("/index.php/Admin/Merchant/getcity/id/" + province + '/', function(result) {
                var str = "<option value=''>请选择地区</option>";
                $.each(result, function(index, d) {
                    if (d != null && index != 'callbackType') {
                        str += "<option value='" + index + "'>" + d + "</option>";
                    }
                })
                $("#area_id").html(str);
            });
        });
    });

</script>