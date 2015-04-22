<?php if (!defined('THINK_PATH')) exit();?><div class="page">
	<div class="pageContent">

	<form method="post" action="/index.php/Admin/Merchant/update/navTabId/Merchant.index" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
		<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
		<div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>商家昵称：</label>
                    <?php echo ($vo["merchant_name"]); ?>
                    
                </div>
                <div class="unit">
                    <label>头像：</label>
                    <img src="<?php echo ($vo["header"]); ?>" width="90"/>
                    
                </div>
         
                <div class="unit">
                    <label>负责人：</label>
                    <input type="text" class="required" value="<?php echo ($vo["manager"]); ?>"  name="manager" id="manager">
                </div>
                <div class="unit">
                    <label>联系电话：</label>
                    <input type="text" class="required" value="<?php echo ($vo["tel"]); ?>"  name="tel" id="tel">
                </div>
                 <div class="unit">
                    <label>注册手机号：</label>
                    <input type="text" class="required" value="<?php echo ($vo["mobile"]); ?>"  name="mobile" id="mobile">
                </div>
                 <div class="unit">
                    <label>服务态度：</label>
                    <input type="text" class="required" value="<?php echo ($vo["service_attitude"]); ?>"  name="service_attitude" id="service_attitude">
                </div>
                 <div class="unit">
                    <label>服务质量：</label>
                    <input type="text" class="required" value="<?php echo ($vo["service_quality"]); ?>"  name="service_quality" id="service_quality">
                </div>
                 <div class="unit">
                    <label>休闲设备：</label>
                    <input type="text" class="required" value="<?php echo ($vo["merchant_setting"]); ?>"  name="merchant_setting" id="merchant_setting">
                </div>
                <div class="unit">
                    <label>省份：</label>
                    <select class="" name="province_id" id="province_id" >
                        <option value="">请选择省份</option>
                        <?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate): $mod = ($i % 2 );++$i;?><option <?php if(($vo["province_id"]) == $key): ?>selected=selected<?php endif; ?> value="<?php echo ($key); ?>" ><?php echo ($vocate); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <select class="" name="city_id" id="city_id" >
                        <?php echo ($city); ?>
                    </select>
                    <select class="" name="area_id" id="area_id">
                        <?php echo ($area); ?>
                    </select>
                </div>
                <div class="unit">
                    <label>详细地址：</label>
                    <input type="text" class="required" value="<?php echo ($vo["address"]); ?>"  name="address" id="address">
                </div>
                <div class="unit">
                    <label>经度：</label>
                    <input type="text" class="required " value="<?php echo ($vo["longitude"]); ?>"  name="longitude" id="longitude" /><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>纬度：</label>
                    <input type="text" class="required " value="<?php echo ($vo["latitude"]); ?>"  name="latitude" id="latitude" /><a class="look_map">查看地图</a>
                </div>
                  <div class="unit">
                    <label>是否有wifi：</label>
                    <input type="text" class="required " value="<?php echo ($vo["wifi_enable"]); ?>"  name="wifi_enable" id="wifi_enable" /> 1有 0无
                </div>
                  <div class="unit">
                    <label>营业时间：</label>
                    <input type="text" class="required " value="<?php echo ($vo["business_time"]); ?>"  name="business_time" id="business_time" /> 
                </div>
                <div class="unit">
                    <label>简介：</label>
                    <textarea name="intro" cols="70" rows="10"><?php echo ($vo["intro"]); ?></textarea>
                </div>
             
                <div class="unit">
                    <label>状态：</label>
                     <?php if(($vo["status"]) == "-1"): ?>封停<?php endif; ?>
                  	 <?php if(($vo["status"]) == "1"): ?>测试<?php endif; ?>
                   <?php if(($vo["status"]) == "0"): ?>正常<?php endif; ?>
                   
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
        $(".look_map",navTab.getCurrentPanel()).click(function() {
        	window.open('/index.php/Admin/Public/baidumap','newwindow','height=400,width=550,top=200,left=300,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no');
        });

        $('#province_id',navTab.getCurrentPanel()).change(function() {
            var province = $(this).val();
            $.getJSON("/index.php/Admin/Merchant/getcity/id/" + province + '/', function(result) {
                var str = "<option value=''>请选择城市</option>";
                $.each(result, function(index, d) {
                    if (d != null && index != 'callbackType') {
                        str += "<option value='" + index + "'>" + d + "</option>";
                    }
                })
                $("#city_id",navTab.getCurrentPanel()).html(str);
            });
        });

        $('#city_id',navTab.getCurrentPanel()).change(function() {
            var province = $(this).val();
            $.getJSON("/index.php/Admin/Merchant/getcity/id/" + province + '/', function(result) {
                var str = "<option value=''>请选择地区</option>";
                $.each(result, function(index, d) {
                    if (d != null && index != 'callbackType') {
                        str += "<option value='" + index + "'>" + d + "</option>";
                    }
                })
                $("#area_id",navTab.getCurrentPanel()).html(str);
            });
        });
    });

</script>