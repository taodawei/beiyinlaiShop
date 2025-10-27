var layer;
var form;
layui.use(['layer','form'],function(){
    layer = layui.layer;
    form = layui.form;
});

layui.form.on('submit(formDemo)', function(data){
    data.field.tuihuan_id = getTuihuanId();
    data.field.msg = '审核已通过,等待用户发货';
    $.post(
        '?s=after_sale&a=addMerchantAddress',
        data.field,
        function(res){
            if (res.code==1){
                layer.msg(res.msg,{icon:1,time:2000});
            }else{
                layer.msg(res.msg,{icon:5});
            }
        },'json'
    );
    return false;
});
/**
 * 审核通过点击事件
 */
function tuihuan_tongguo(){
    //在这里面输入任何合法的js语句
    var tuihuan_id = getTuihuanId();
    var route = window.location.href;
    var type = route.substr(-1,1);
    if (type == 2){
        layer.open({
            type:1
            ,area: ['800px', '500px']
            ,title: '请填写收货信息'
            ,content: "<form class=\"layui-form\" method='post'>\n" +
                "  <div class=\"layui-form-item\">\n" +
                "    <label class=\"layui-form-label\">商家姓名</label>\n" +
                "    <div class=\"layui-input-block\">\n" +
                "      <input type=\"text\" name=\"merchant_name\"   class=\"layui-input\">\n" +
                "    </div>\n" +
                "  </div>\n" +
                "  <div class=\"layui-form-item\">\n" +
                "    <label class=\"layui-form-label\">商家电话</label>\n" +
                "    <div class=\"layui-input-block\">\n" +
                "      <input type=\"text\" name=\"merchant_phone\"    class=\"layui-input\">\n" +
                "    </div>\n" +
                "  </div>\n" +
                "  <div class=\"layui-form-item\">\n" +
                "    <label class=\"layui-form-label\">商家地址</label>\n" +
                "    <div class=\"layui-input-block\">\n" +
                "      <input type=\"text\" name=\"merchant_address\"   class=\"layui-input\">\n" +
                "    </div>\n" +
                "  </div>\n" +
                "  <div class=\"layui-form-item\">\n" +
                "    <label class=\"layui-form-label\">备注</label>\n" +
                "    <div class=\"layui-input-block\">\n" +
                "      <input type=\"text\" name=\"merchant_remark\" autocomplete=\"off\" class=\"layui-input\">\n" +
                "    </div>\n" +
                "  </div>\n" +
                "  <div class=\"layui-form-item\">\n" +
                "    <div class=\"layui-input-block\">\n" +
                "      <button class=\"layui-btn\" lay-submit lay-filter=\"formDemo\">立即提交</button>\n" +
                "    </div>\n" +
                "  </div>\n" +
                "</form>\n"
        });
    }else{
        $.post(
            '?s=after_sale&a=approveSales',
            {
                id:tuihuan_id,
                status:2,
                msg:'申请已通过审核'
            },
            function (res) {
                if (res.code==1){
                    layer.msg(res.msg,{icon:res.code,time:2000});
                }else{
                    layer.msg(res.msg,{icon:5,time:2000});
                }
            },'json'
        );
    }



}

/**
 * 审核驳回点击事件
 */
function tuihuan_bohui(){
    var tuihuan_id = getTuihuanId();
    var reason = $("#bohui_content").val();
    if (reason == ''){
        layer.msg('驳回原因不能为空',{icon:5,time:2000});
        return false;
    }
    //审核通过
    $.post(
        '?s=after_sale&a=approveSales',
        {
            id:tuihuan_id,
            status:-1,
            reason:reason,
            msg:'退货申请被驳回'
        },
        function (res) {
            if (res.code==1){
                layer.msg(res.msg,{icon:res.code});
            }else{
                layer.msg(res.msg,{icon:5});
            }
        },'json'
    );
}

/**
 * 确认收货
 */
function tuihuan_qrsh(){
    var tuihuan_id = getTuihuanId();
    $.post(
        '?s=after_sale&a=confirmReceipt',
        {
            id:tuihuan_id,
            msg:'已确认收到退货'
        },
        function (res) {
            if (res.code==1){
                layer.msg(res.msg,{icon:res.code});
            }else{
                layer.msg(res.msg,{icon:5});
            }
        },'json'
    );
}

/**
 * 立即退款
 */
function tuihuan_ljtk(){
    var tuihuan_id = getTuihuanId();
    var tuihuan_price = $('#tuikuan_price').val();
    var beizhu = $('#beizhu').val();
    // console.log(tuihuan_price);
    $.post(
        '?s=after_sale&a=confirmRefund',
        {
            id:tuihuan_id,
            msg:'已退款',
            tuihuan_price:tuihuan_price,
            beizhu:beizhu
        },
        function (res) {
            if (res.code==1){
                layer.msg(res.msg,{icon:res.code});
            }else{
                layer.msg(res.msg,{icon:5});
            }
        },'json'
    );
}

/**
 * 立即发货
 */
function tuihuan_ljfh(){
        var tuihuan_id = getTuihuanId();
        var wuliubianhao = $("#wuliubianhao").val();
        var wuliugongsi = $("#wuliugongsi").val();
        $.post(
            '?s=after_sale&a=immedilateDelivery',
            {
                tuihuan_id:tuihuan_id,
                wuliubianhao:wuliubianhao,
                wuliugongsi:wuliugongsi,
                msg:'已发货'
            },
            function(res){
               if (res.code==1){
                   layer.msg(res.msg,{icon:res.code});
               }else{
                   layer.msg('错误',{icon:5})
               }
            },'json'
        )
    }

/**
 * 获取当前获取的退换id
 * @returns {jQuery}
 */
function getTuihuanId(){
    var nowIndex = $("#nowIndex").val();
    return $('tr[data-index='+nowIndex+'] td[data-field='+"id"+'] div').html();
}
























