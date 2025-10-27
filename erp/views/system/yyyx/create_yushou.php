<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "image"=>array("title"=>"商品图片","rowCode"=>"{field:'image',title:'商品图片',width:87,unresize:true}"),
    "sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200}"),
    "title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
    "key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
    "price"=>array("title"=>"价格","rowCode"=>"{field:'price',title:'价格',width:150}"),
    "operate"=>array("title"=>"操作","rowCode"=>"{field:'operate',title:'操作',width:150}")
);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:73px}
        .layui-table-view{margin:10px;}
        td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
        td[data-field="image"] div{height:auto;text-align:center;}
        td[data-field="image"] img{border:#abd3e7 1px solid}
        td[data-field="operate"] a{display: inline-block;width: 86px;height: 28px;background-color: #106c99;border-radius: 5px;text-align: center;line-height: 28px;font-size: 13px;color: #ffffff;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <img src="images/yushou_1.png" /> 创建预售 <span>按需求检索商品，选择要预售的商品，设置预售规则创建预售</span> 
        </div>
        <div class="mendianguanli_down">
            <div class="creatyushou">   
                <div class="creatyushou_1">
                    <div class="creatyushou_1_up">
                        <form id="searchForm" method="post" class="layui-form">
                            <ul>
                                <li>
                                    <div class="creatyushou_1_title">
                                        商品名称
                                    </div>
                                    <div class="creatyushou_1_tt">
                                        <input type="text" id="s_title" class="creatyushou_1_tt_input"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <li>
                                    <div class="creatyushou_1_title">
                                        商品分类
                                    </div>
                                    <div class="creatyushou_1_tt">
                                        <div class="layui-form-select">
                                            <div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="" class="layui-input"><i class="layui-edge"></i></div>
                                            <dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
                                        </div>
                                        <input type="hidden" id="super_channel">
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>
                                <li>
                                    <div class="creatyushou_1_title">
                                        商品编码
                                    </div>
                                    <div class="creatyushou_1_tt">
                                        <input type="text" id="s_sn" class="creatyushou_1_tt_input"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <li>
                                    <div class="creatyushou_1_title">
                                        商品状态
                                    </div>
                                    <div class="creatyushou_1_tt">
                                        <select id="s_status" class="creatyushou_1_tt_select">
                                            <option value="">全部</option>
                                            <option value="1">上架</option>
                                            <option value="-1">下架</option>
                                        </select>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>
                            </ul>
                        </form>
                    </div>
                    <div class="creatyushou_1_down">
                        <a href="javascript:" onclick="search_pdt();" class="creatyushou_1_down_01">查 询</a>
                    </div>
                </div>
                <div class="creatyushou_2">
                    <div class="creatyushou_2_up">
                        选择要预售的商品
                    </div>
                    <div class="creatyushou_2_down">
                        <table id="product_list" lay-filter="product_list"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--弹出设置-一口价-填定金-->
    <div class="shezhi_ykjdingjin_tc" id="shezhi_ykjdingjin_tc" style="display:none;">
        <div class="bj">
        </div>
        <div class="shezhi_ykjdingjin">
            <form action="" method="post" id="submit_form" class="layui-form">
                <input type="hidden" id="a_pdtId" name="pdtId">
                <div class="shezhi_ykjdingjin_1">
                    <div class="shezhi_ykjdingjin_1_left">
                        设置规则
                    </div>
                    <div class="shezhi_ykjdingjin_1_right" onclick="$('#shezhi_ykjdingjin_tc').hide();">
                        <img src="images/yingxiao_33.png" />
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="shezhi_ykjdingjin_2">
                    <div class="shezhi_ykjdingjin_2_up">
                        商品编码：<span id="a_sn"></span>     商品价格：<span id="a_price"></span>元
                   </div>
                   <div class="shezhi_ykjdingjin_2_down">
                    <ul>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span> 预售模式：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <input type="radio" name="type" value="1" checked title="一口价" lay-filter="type1">
                                <input type="radio" name="type" value="2" title="阶梯价" lay-filter="type2">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span> 预售价格：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt" id="rows_div" rows="1">
                                <input type="text" name="yushou_price" id="yushou_price" lay-verify="required|number" class="shezhi_ykjdingjin_2_down_tt_input"/> 元&nbsp;&nbsp;<a href="javascript:" style="color:#369dd0;display:none" id="addBtn" onclick="add_qujian();">+添加区间</a>
                                <div id="rows_1" class="add_qujian_div" style="margin-top:5px;display:none">
                                    满 <input type="number" name="man_1" placeholder="0" class="shezhi_ykjdingjin_2_down_tt_input1"/> 份，预售价：<input type="number" name="price_1" placeholder="0.00" class="shezhi_ykjdingjin_2_down_tt_input1"/> 元
                                    <input type="hidden" name="rows[]" value="1">
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span> 付款方式：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <input type="radio" name="paytype" value="1" checked title="全款" lay-filter="paytype1">
                                <input type="radio" name="paytype" value="2" title="定金+尾款" lay-filter="paytype2">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li id="dingjin_li" style="display:none;">
                            <div class="shezhi_ykjdingjin_2_down_title">
                               <span>*</span> 定金：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <input type="text" lay-verify="number" name="dingjin" id="dingjin" class="shezhi_ykjdingjin_2_down_tt_input"/>
                                &nbsp;&nbsp;尾款：<span id="weikuan"></span>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span>  预售数量：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <input type="text" name="num" lay-verify="required|number" class="shezhi_ykjdingjin_2_down_tt_input"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span>  每人限购：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <input type="text" name="num_limit" lay-verify="required|number" class="shezhi_ykjdingjin_2_down_tt_input"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span>  <font id="dingjin_title">活动时间</font>：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <div class="shezhi_ykjdingjin_2_down_tt_shijian">
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_01">
                                        <input type="text" name="startTime" id="startTime" lay-verify="required" readonly="true">
                                        <div class="clearBoth"></div>
                                    </div>
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_02">
                                        至
                                    </div>
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_01">
                                        <input type="text" name="endTime" id="endTime" lay-verify="required" readonly="true">
                                        <div class="clearBoth"></div>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li id="weikuan_div" style="display:none;">
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span>  预付尾款时间：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <div class="shezhi_ykjdingjin_2_down_tt_shijian">
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_01">
                                        <input type="text" name="startTime1" id="startTime1" readonly="true">
                                        <div class="clearBoth"></div>
                                    </div>
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_02">
                                        至
                                    </div>
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_01">
                                        <input type="text" name="endTime1" id="endTime1" readonly="true">
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shezhi_ykjdingjin_2_down_title">
                                <span>*</span>  发货时间：
                            </div>
                            <div class="shezhi_ykjdingjin_2_down_tt">
                                <div class="shezhi_ykjdingjin_2_down_tt_shijian">
                                    <div class="shezhi_ykjdingjin_2_down_tt_shijian_01">
                                        <input type="text" name="fahuoTime" id="fahuoTime" lay-verify="required" readonly="true">
                                        <div class="clearBoth"></div>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="shezhi_ykjdingjin_3">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
            </div>
        </form>
    </div>
</div>
<!--结束-->
</div>
<input type="hidden" id="departs" value="" />
<input type="hidden" id="users" value="" />
<input type="hidden" id="departNames" value=""/>
<input type="hidden" id="userNames" value="" />
<input type="hidden" id="editId" value="0">
<div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<script type="text/javascript">
    var productListTalbe;
    var productListForm;
    layui.use(['laydate', 'laypage','table','form','upload'], function(){
        var laydate = layui.laydate
        ,laypage = layui.laypage
        ,table = layui.table
        ,form = layui.form
        ,upload = layui.upload;
        productListForm = form;
        laydate.render({
            elem: '#startTime'
            ,min:'<?=date("Y-m-d H:i:s")?>'
            ,type: 'datetime'
            ,btns: ['confirm']
            ,format: 'yyyy-MM-dd HH:mm'
        });
        laydate.render({
            elem: '#endTime'
            ,min:'<?=date("Y-m-d H:i:s",strtotime('+1 days'))?>'
            ,type: 'datetime'
            ,btns: ['confirm']
            ,format: 'yyyy-MM-dd HH:mm'
        });
        laydate.render({
            elem: '#startTime1'
            ,min:'<?=date("Y-m-d H:i:s")?>'
            ,type: 'datetime'
            ,btns: ['confirm']
            ,format: 'yyyy-MM-dd HH:mm'
        });
        laydate.render({
            elem: '#endTime1'
            ,min:'<?=date("Y-m-d H:i:s",strtotime('+1 days'))?>'
            ,type: 'datetime'
            ,btns: ['confirm']
            ,format: 'yyyy-MM-dd HH:mm'
        });
        laydate.render({
            elem: '#fahuoTime'
            ,min:'<?=date("Y-m-d H:i:s",strtotime('+1 days'))?>'
            ,type: 'datetime'
            ,btns: ['confirm']
            ,format: 'yyyy-MM-dd HH:mm'
        });
        productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-350"
            ,url: '?m=system&s=yyyx&a=getpdts'
            ,page: true
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("th[data-field='id']").hide();
                $("#page").val(curr);
                layer.closeAll('loading');
            }
        });
        productListForm.on('radio(type1)',function(){
            $("#addBtn").hide();
            $(".add_qujian_div").hide();
        });
        productListForm.on('radio(type2)',function(){
            $("#addBtn").show();
            $(".add_qujian_div").show();
        });
        productListForm.on('radio(paytype1)',function(){
            $("#dingjin_li").hide();
            $("#weikuan_div").hide();
            $("#dingjin_title").html('活动时间');
        });
        productListForm.on('radio(paytype2)',function(){
            $("#dingjin_li").show();
            $("#weikuan_div").show();
            $("#dingjin_title").html('预付定金时间');
        });
        productListForm.on('submit(tijiao)', function(data){
            layer.load();
            var reqData = $("#submit_form").serialize();
            $.ajax({
                type: "POST",
                url: "?s=yyyx&a=create_yushou&submit=1",
                data: reqData,
                dataType:'json',timeout:30000,
                success: function(resdata){
                    layer.closeAll();
                    if(resdata.code==0){
                        layer.msg(resdata.message,{icon:5});
                        return false;
                    }else{
                        location.href='?s=yyyx&a=yushou';
                    }
                }
            });
        });
        $.ajax({
            type: "POST",
            url: "/erp_service.php?action=get_product_channels1",
            data: "",
            dataType:"text",timeout : 20000,
            success: function(resdata){
                $("#selectChannels").append(resdata);
            },
            error: function() {
                layer.msg('数据请求失败', {icon: 5});
            }
        });
        $("#selectChannel").click(function(){
            $(this).parent().toggleClass('layui-form-selected');
        });
    });
</script>
<script type="text/javascript" src="js/yyyx/create_yushou.js"></script>
<? require('views/help.html');?>
</body>
</html>