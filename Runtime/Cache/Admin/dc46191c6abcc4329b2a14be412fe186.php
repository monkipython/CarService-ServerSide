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

        <form method="post" action="/index.php/Admin/Answer/insert/navTabId/Answer.index/callbackType/closeCurrent" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <div class="pageFormContent" layoutH="48">
                <div class="unit">
                    <label>请选择问题分类：</label>
                    <select name="pid" id="pid" class="combox required">
                        <option value="">请选择</option>
                        <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vocate): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vocate['id']); ?>"><?php echo ($vocate['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div> 
                <div class="unit">
                    <label>请输入问题：</label>
                   
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
                      formData:{PHPSESSID:'xxx', ajax:1,type:1},
                      buttonText:'上传图片',
                      fileSizeLimit:'1000KB',
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
			  <div class="unit">
                    <label>添加时间：</label>
                    <input type="text" class="date "  name="time" dateFmt="yyyy-MM-dd HH:mm:ss" />（置空为当前时间）
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
	 
 })

</script>