<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
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
    <link href="styles/dianzimiandan.css" rel="stylesheet" type="text/css">
    <link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
<? require("views/system/pdts_fahuo/header.php");?>
<div id="content">
    <div class="content1">
        <div class="content_1">
            电子面单发货确认
        </div>
        <div class="content_2">
            <div class="fhqr_dianzimiandan">
                <div class="fhqr_dianzimiandan_1">
                    注:　1 . 如果手动输入快递单号，完成后需按回车键;<br> 2 . 在输入过程中请不要加入空格或其他符号
                </div>
                <div class="fhqr_dianzimiandan_2">
                    <div class="fhqr_dianzimiandan_2_left">
                        <div class="fhqr_dianzimiandan_2_left_01">
                            请输入快递单号：
                        </div>
                        <div class="fhqr_dianzimiandan_2_left_02">
                            <textarea id="kuaidi_cont" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="fhqr_dianzimiandan_2_right">
                        <div class="fhqr_dianzimiandan_2_right_01">
                            结果反馈：
                        </div>
                        <div class="fhqr_dianzimiandan_2_right_02">
                            <ul id="result_ul">
                                
                            </ul>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="fhqr_dianzimiandan_3">
                    <a href="javascript:" onclick="quren();">确认发货</a> 本批次还可以输入<b id="shengyu">50</b>个发货单
                </div>
            </div>
        </div>
    </div>
</div>
<div id="bg" onclick="hideRowset();"></div>
<script type="text/javascript">
    $(function(){
        $('#kuaidi_cont').on('input propertychange',function(){
            var str = $(this).val();
            var num = parseInt(str.split("\n").length);
            if(num>50){
                layer.msg('每次最多确认50个发货单',{icon: 5});
            }else{
                $("#shengyu").text(50-num);
            }
        });
    });
    function quren(){
        var str = $('#kuaidi_cont').val();
        if(str==''){
            layer.msg('请输入或扫描快递单号',{icon: 5});
            return false;
        }
        layer.load();
        $.ajax({
            type:"POST",
            url:"?m=system&s=pdts_fahuo&a=queren_miandan",
            data:"content="+str,
            timeout:"30000",
            dataType:"text",
            success: function(resdata){
                layer.closeAll();
                $("#result_ul").html(resdata);
            },
            error:function(){
                layer.closeAll();
                alert("超时，请刷新后重试");
            }
        });
    }
</script>
</body>
</html>