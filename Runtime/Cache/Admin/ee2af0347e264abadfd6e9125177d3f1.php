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

        <form method="post" action="/index.php/Admin/Recent/insert/navTabId/Recent.index/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <div class="pageFormContent" layoutH="58">
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
                    <label>经度：</label>
                    <input type="text" class="required "  name="longitude" id="longitude" /><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>纬度：</label>
                    <input type="text" class="required "  name="latitude" id="latitude" /><a class="look_map">查看地图</a>
                </div>
                <div class="unit">
                    <label>添加时间：</label>
                    <input type="text" class="date "  name="time" dateFmt="yyyy-MM-dd HH:mm:ss" />（置空为当前时间）
                </div>
                <div class="unit">
                    <label>请输入动态：</label>
                   
                    <textarea class="form-control required" rows="4" name="title" placeholder="请填写回答" style="width:99%; height:180px;"></textarea>
                </div>
             <!--  <div class="unit">
                    <label>状态：</label>
                    <input type="radio" class="required" checked name="status" value='1'>可用
                    <input type="radio" class="required"  name="status" value='0'>不可用
                </div>
			 -->  
           
			<div class="unit">
				
				<div>
                   <input id="testFileInput" type="file" name="image" 
                      uploaderOption="{
                      swf:'/Public/uploadify/scripts/uploadify.swf',
                      uploader:'/index.php/Admin/Public/uploadPic',
                      formData:{PHPSESSID:'xxx', ajax:1,type:2},
                      buttonText:'上传图片',
                      fileSizeLimit:'5000KB',
                      fileTypeDesc:'*.jpg;*.jpeg;*.gif;*.png;',
                      fileTypeExts:'*.jpg;*.jpeg;*.gif;*.png;',
                      auto:true,
                      multi:true,
                      onUploadSuccess:function(file,data,response){
                           var obj=eval('(' + data + ')');
                           var str = JSON.stringify(obj.data);
                           var pic = 'http://121.40.92.53/ycbb/Uploads'+obj['data']['hs'];
			            	$('#uploadThumb').append('<div ><img src='+pic+' width=200 /></div>');
			             
                      }
                      }"
                      />
                      </div>
                      <div class="col-xs-12" id ="uploadThumb">
							
								
							</div>
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
 $().ready(function(){
	  $(".look_map",$.pdialog.getCurrent()).click(function() {
		  window.open('/index.php/Admin/Public/baidumap?id=1','newwindow','height=400,width=550,top=200,left=300,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no');
      });
      $('#province_id',$.pdialog.getCurrent()).change(function() {
          var province = $(this).val();
          $.getJSON("/index.php/Admin/Merchant/getcity/id/" + province + '/', function(result) {
              var str = "<option value=''>请选择城市</option>";
              $.each(result, function(index, d) {
                  if (d != null && index != 'callbackType') {
                      str += "<option value='" + index + "'>" + d + "</option>";
                  }
              })
              $("#city_id",$.pdialog.getCurrent()).html(str);
          });
      });

      $('#city_id',$.pdialog.getCurrent()).change(function() {
          var province = $(this).val();
          $.getJSON("/index.php/Admin/Merchant/getcity/id/" + province + '/', function(result) {
              var str = "<option value=''>请选择地区</option>";
              $.each(result, function(index, d) {
                  if (d != null && index != 'callbackType') {
                      str += "<option value='" + index + "'>" + d + "</option>";
                  }
              })
              $("#area_id",$.pdialog.getCurrent()).html(str);
          });
      });
 })

</script>