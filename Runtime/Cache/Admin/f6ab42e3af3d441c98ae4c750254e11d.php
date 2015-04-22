<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">
    <div class="pageFormContent"  layoutH="0" style="">
        <form method="post" action="/index.php/Admin/Auth/update/navTabId/Auth.index" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
            <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
             <div class="unit">
                    <label>店名：</label>
                      <input type="text" class="required" value="<?php echo ($vo["check_data"]["merchant_name"]); ?>"  name="merchant_name">
               		<img src="<?php echo ((isset($vo["check_data"]["header_location"]) && ($vo["check_data"]["header_location"] !== ""))?($vo["check_data"]["header_location"]):'/Public/img/default_user.jpg'); ?>  " width="75" 	height="75"/>
               		<input type="hidden" name="header" value="<?php echo ($vo["check_data"]["header"]); ?>"/>
                </div>
                 <div class="unit">
                    <label>联系电话：</label>
                    <input type="text" class="required " value="<?php echo ($vo["check_data"]["tel"]); ?>"  name="tel"  />
                </div>
                
                <div class="unit">
                    <label>选择地址：</label>
                     <select class="" name="province_id" id="province_id" >
                        <option value="">请选择省份</option>
                        <?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate): $mod = ($i % 2 );++$i; if( $vo['check_data']['province_id'] == $vocate['id']): ?><option selected=selected value="<?php echo ($vocate["id"]); ?>" ><?php echo ($vocate["name"]); ?></option>
                        	<?php else: ?>
                        	 <option value="<?php echo ($vocate["id"]); ?>" ><?php echo ($vocate["name"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <select class="" name="city_id" id="city_id" >
                        <option value="">请选择省份</option>
                        <?php if(is_array($city)): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate1): $mod = ($i % 2 );++$i; if( $vo['check_data']['city_id'] == $vocate1['id']): ?><option selected=selected value="<?php echo ($vocate1["id"]); ?>" ><?php echo ($vocate1["name"]); ?></option>
                        	<?php else: ?>
                        	 <option value="<?php echo ($vocate1["id"]); ?>" ><?php echo ($vocate1["name"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <select class="" name="area_id" id="area_id">
                       <option value="">请选择区域</option>
                        <?php if(is_array($area)): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate2): $mod = ($i % 2 );++$i; if( $vo['check_data']['area_id'] == $vocate2['id']): ?><option selected=selected value="<?php echo ($vocate2["id"]); ?>" ><?php echo ($vocate2["name"]); ?></option>
                        	<?php else: ?>
                        	 <option value="<?php echo ($vocate2["id"]); ?>" ><?php echo ($vocate2["name"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="unit">
                    <label>详情地址：</label>
                    <input type="text" class="required" value="<?php echo ($vo["check_data"]["address"]); ?>"  name="address" >
                </div>
               
                <div class="unit">
                    <label>营业时间：</label>
                    <input type="text" class=" required" value="<?php echo ($vo["check_data"]["business_time"]); ?>"  name="business_time" >
                </div>
               
                <div class="unit">
                    <label>负责人：</label>
                    <input type="text" class="required " value="<?php echo ($vo["check_data"]["manager"]); ?>"  name="manager"  />
                </div>
           
                <div class="unit">
                    <label>经度：</label>
                    <input type="text" class="required "  name="longitude" id="longitude" value="<?php echo ($vo["check_data"]["longitude"]); ?>"/><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>纬度：</label>
                    <input type="text" class="required "  name="latitude" id="latitude"  value="<?php echo ($vo["check_data"]["latitude"]); ?>"/><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>操作：</label>
                    <?php echo ($vo["check_action"]); ?>
                </div>
                <div class="unit">
                    <label>提交人：</label>
                    <?php echo ($vo["data_org"]); ?>
                </div>
                 <div class="unit">
                    <label>提交审核时间：</label>
                   <?php echo (todate($vo["addtime"])); ?>
                </div>
                
                 
             
            <div class="formBar">
                <ul>
                    <li><div class="buttonActive"><div class="buttonContent"><button type="submit" >通过审核</button></div></div></li>
                    <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
                </ul>
            </div>
            </form>
             
            </div>

		<script>
		//onclick="checkby(<?php echo ($vo["id"]); ?>)"
				function checkby(id){
						$.ajax({
						     type: 'POST',
						     url:'/index.php/App/Auth/execAction' ,
						    data: {'id':id} ,
						    success: function(json){
						    	alert(json.msg);
						    } ,
						    dataType: 'json'
						});
				}
			</script>
    </div>
</div>
<script>
    $(function() {
		//console.log(navTab.getCurrentPanel());

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