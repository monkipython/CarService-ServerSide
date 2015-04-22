<?php if (!defined('THINK_PATH')) exit();?><div class="page">
    <div class="pageContent">

            <div class="pageFormContent" layoutH="58">
                <div class="unit">
                    <label>用户名称：</label>
                    <?php echo (getmembername($vo["member_id"])); ?>
                </div>
                <div class="unit">
                    <label>标题：</label>
                   <?php echo ($vo["title"]); ?>
                </div>
                <div class="unit">
                    <label>需求详情：</label>
                   <?php echo ($vo["desc"]); ?>
                </div>
                <div class="unit">
                    <label>到店时间：</label>
                   <?php echo ($vo["reach_time"]); ?>
                </div>
                <div class="unit">
                    <label>价格区间：</label>
                    <?php echo ($vo["start_price"]); ?>--<?php echo ($vo["end_price"]); ?>
                </div>
                <div class="unit">
                    <label>地区：</label>
                   <?php echo (getareasname($vo["area_id"])); ?>
                </div>
                <div class="unit">
                    <label>车辆信息：</label>
                   <?php echo ($vo['cart_brand']); ?>,<?php echo ($vo['cart_model']); ?>,<?php echo ($vo['color']); ?>,<?php echo ($vo['output']); ?>
                </div>
                <div class="unit">
                    <label>选择商家：</label>
                   <?php echo (getmerchantname($vo["merchant_id"])); ?>
                </div>
                
                <div class="unit">
                    <label>发布时间：</label>
                   <?php echo (todate($vo["addtime"])); ?>
                </div>
                 <div class="unit">
                    <label>图片信息：</label>
                   <?php if(is_array($vo["pics"])): $i = 0; $__LIST__ = $vo["pics"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pics): $mod = ($i % 2 );++$i;?><img src="__UPLOAD__<?php echo ($pics); ?>" /><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>

    </div>
</div>