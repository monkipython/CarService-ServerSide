<?php if (!defined('THINK_PATH')) exit();?><style type="text/css" media="screen">
    .my-uploadify-button {
        background:none;
        border: none;
        text-shadow: none;
        border-radius:0;
    }

    .uploadify:hover .my-uploadify-button {
        background:none;
        border: none;
    }

    .fileQueue {
        width: 400px;
        height: 150px;
        overflow: auto;
        border: 1px solid #E5E5E5;
        margin-bottom: 10px;
    }
</style>
<div class="page">
    <div class="pageContent">

        <form method="post" action="/index.php/Admin/Merchant/insert/navTabId/Merchant.index" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>商家昵称：</label>
                    <input type="text" class="required"  name="merchant_name" id="merchant_name">
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
                    <input type="text" class="required"  name="avatar" id="avatar">
                </div>
                <div class="unit">
                    <label>联系人：</label>
                    <input type="text" class="required"  name="contact" id="contact">
                </div>
                <div class="unit">
                    <label>联系电话：</label>
                    <input type="text" class="required"  name="tel" id="tel">
                </div>
                <div class="unit">
                    <label>省份：</label>
                    <select class="" name="province_id" id="province_id" >
                        <option value="">请选择省份</option>
                        <?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" ><?php echo ($vocate); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <select class="" name="city_id" id="city_id" >
                        <option value="">请选择城市</option>
                    </select>
                    <select class="" name="area_id" id="area_id">
                        <option value="">请选择区县</option>
                    </select>
                </div>
                <div class="unit">
                    <label>详细地址：</label>
                    <input type="text" class="required"  name="address" id="address">
                </div>
                <div class="unit">
                    <label>经度：</label>
                    <input type="text" class="required "  name="longitude" id="longitude" /><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>纬度：</label>
                    <input type="text" class="required "  name="latitude" id="latitude" /><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>提供服务：</label>
                    <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="checkbox" name="pcat_id[]" value="<?php echo ($key); ?>"/><?php echo ($vo); endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div class="unit">
                    <label>简介：</label>
                    <textarea name="intro" cols="70" rows="10"></textarea>
                </div>
                <div class="unit">
                    <label>状态：</label>
                    <input type="radio" class="required" checked name="status" value='1'>可用
                    <input type="radio" class="required"  name="status" value='0'>禁用
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