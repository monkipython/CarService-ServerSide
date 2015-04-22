<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/Member/update/navTabId/Member.index" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
            <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>用户昵称：</label>
                    <input type="text" class="required" value="<?php echo ($vo["nick_name"]); ?>"  name="nick_name" id="nick_name">
                </div>
                <div class="unit">
                    <label>头像：</label>
                    <div class="pageContent">
                        <input id="testFileInput" type="file" name="image" 
                               uploaderOption="{
                               swf:'/Public/uploadify/scripts/uploadify.swf',
                               uploader:'/index.php/Admin/File/upload/path/Header/',
                               formData:{PHPSESSID:'xxx', ajax:1},
                               buttonText:'上传头像',
                               fileSizeLimit:'500KB',
                               fileTypeDesc:'*.jpg;*.jpeg;*.gif;*.png;',
                               fileTypeExts:'*.jpg;*.jpeg;*.gif;*.png;',
                               auto:true,
                               multi:false,
                               onUploadSuccess:function(file,data,response){
                               var res=eval('(' + data + ')');
                               document.getElementById('avatar').value =res.image.savepath+res.image.savename;
                               }
                               }"
                               />
                    </div>
                </div>
                <div class="unit">
                    <label>头像地址：</label>
                    <input type="text" class="required" value="<?php echo ($vo["header"]); ?>"  name="header" id="avatar">
                </div>
                <div class="unit">
                    <label>联系电话：</label>
                    <input type="text" class="required" value="<?php echo ($vo["mobile"]); ?>"  name="mobile" id="mobile">
                </div>
                <div class="unit">
                    <label>邮箱：</label>
                    <input type="text" class="required"  name="email" id="email" value="<?php echo ($vo["email"]); ?>" >
                </div>
                <div class="unit">
                    <label>余额：</label>
                    <input type="text" class="required " value="<?php echo ($vo["account"]); ?>"  name="account" id="account" />
                </div>
                <div class="unit">
                    <label>积分：</label>
                    <input type="text" class="required " value="<?php echo ($vo["point"]); ?>"  name="point" id="point" />
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
        $(".look_map").click(function() {
            window.open("/index.php/Admin/Public/baidumap");
        });

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