<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "id"=>array("title"=>"会员id","rowCode"=>"{field:'id',title:'会员id',width:100}"),
    "nickname"=>array("title"=>"姓名","rowCode"=>"{field:'nickname',title:'姓名',width:150}"),
    "username"=>array("title"=>"手机号","rowCode"=>"{field:'username',title:'手机号',width:180}"),
    "level"=>array("title"=>"会员等级","rowCode"=>"{field:'level',title:'会员等级',width:100,sort:true}"),
    "money"=>array("title"=>"余额","rowCode"=>"{field:'money',title:'余额',width:150,sort:true}"),
    "yongjin"=>array("title"=>"佣金","rowCode"=>"{field:'yongjin',title:'佣金',width:150,sort:true}"),
    "jifen"=>array("title"=>"积分","rowCode"=>"{field:'jifen',title:'积分',width:150,sort:true}"),
    "fans_num"=>array("title"=>"下级数量","rowCode"=>"{field:'fans_num',title:'下级数量',width:100}"),
    // "dabiao"=>array("title"=>"达标状态","rowCode"=>"{field:'dabiao',title:'达标状态',width:150}"),
    "cost"=>array("title"=>"累计消费","rowCode"=>"{field:'cost',title:'累计消费',width:200,sort:true}"),
    // "lastLogin"=>array("title"=>"最后登录时间","rowCode"=>"{field:'lastLogin',title:'最后登录时间',width:180}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$user = $db->get_row("select nickname,shangji,tuan_id from users where id=$id and comId=$comId");
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
if(!empty($user->shangji)){
    $shangji = $db->get_row("select username,nickname from users where id=$user->shangji");
}
if(!empty($user->tuan_id)){
    $tuanzhang = $db->get_row("select username,nickname from users where id=$user->tuan_id");
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:70px}
        .layui-table-view{margin:10px;}
        td[data-field="image"] div{height:50px;}
        td[data-field="image"] img{border:#abd3e7 1px solid;width:50px;}
        td[data-field="title"] div,td[data-field="key_vals"] div,td[data-field="remark"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 会员详情
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <? require('views/system/users/head.php')?>
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_jifenmingxi">
                        <div class="hyxx_jifenmingxi_up">
                            <div class="hyxx_jifenmingxi_up_left">                              
                                <div class="hyxx_jifenmingxi_up_left_02">
                                    <div class="hyxx_jifenmingxi_up_left_01_left">
                                        搜索：
                                    </div>
                                    <div class="hyxx_jifenmingxi_up_left_02_right">
                                       <input type="text" id="keyword" placeholder="手机号/姓名" class="hyxx_xiaofeimingxi_up_left_01_right">
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="hyxx_jifenmingxi_up_left_04">
                                    <a href="javascript:reloadTable(0);" class="hyxx_yxmx_yuemingxi_up_right_a">筛选</a>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                            <div style="float:right;line-height:50px;">上级：<?=empty($shangji)?'无':$shangji->nickname.'('.$shangji->username.')' ?>&nbsp;&nbsp;<? if(!empty($tuanzhang)){?>团长：<? echo $tuanzhang->nickname; }?>
                                <? if(empty($shangji)){?><a href="javascript:" onclick="xiugai(<?=$id?>);" style="color:#f60">绑定上级</a><? }?>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="hyxx_jifenmingxi_down">
                            <table id="product_list" lay-filter="product_list"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="selectedIds" value="">
    <script type="text/javascript">
        var productListTalbe;
        var productListForm;
        var ifxiugai = 0;
        layui.use(['laydate', 'laypage','table','form'], function(){
          var laydate = layui.laydate
          ,laypage = layui.laypage
          ,table = layui.table
          ,form = layui.form
          ,load = layer.load()
          productListForm = form;
          laydate.render({
            elem: '#riqi1'
            ,show: true
            ,position: 'static'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($startTime)?'':",value:'$startTime'"?>
            ,btns: []
            ,done: function(value, date, endDate){
                $("#s_time1").html(value);
                $("#startTime").val(value);
            }
          });
          laydate.render({
            elem: '#riqi2'
            ,show: true
            ,position: 'static'
            <?=empty($endTime)?'':",value:'$endTime'"?>
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            ,btns: ['confirm']
            ,done: function(value, date, endDate){
                $("#s_time2").html(value);
                $("#endTime").val(value);
            }
          });
          $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
            reloadTable(0);
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-215"
            ,url: '?m=system&s=users&a=getList&shangji=<?=$id?>&type=1&order1=is_dabiao&order2=desc'
            ,page: {curr:<?=$page?>}
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
        });
        function xiugai(id){
            layer.open({
                type: 1
                ,title: false
                ,closeBtn: false
                ,area: '530px;'
                ,shade: 0.3
                ,id: 'LAY_layuipro'
                ,btn: ['提交', '取消']
                ,yes: function(index, layero){
                    // if(ifxiugai==0){
                    //     return false;
                    // }
                    var username = $("#shangji_input").val();
                    $.ajax({
                        type: "POST",
                        url: "?s=users&a=edit_shangji&id="+id,
                        data: "username="+username,
                        dataType:'json',timeout : 8000,
                        success: function(resdata){
                            if(resdata.code==0){
                                layer.msg(resdata.message);
                                return false;
                            }else{
                                location.reload();
                            }
                        }
                    });
                }
                ,btnAlign: 'r'
                ,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
                    '<div class="spxx_shanchu_tanchu_01">'+
                        '<div class="spxx_shanchu_tanchu_01_left">选择上级</div>'+
                        '<div class="spxx_shanchu_tanchu_01_right">'+
                            '<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
                    '</div>'+
                    '<div class="spxx_shanchu_tanchu_02" style="height:48px;padding:0px;margin-top:10px;">'+
                        '<input type="number" id="shangji_input" placeholder="输入上级会员的id号或者手机号" onchange="get_user_name();" class="layui-input" style="width:250px;display:inline-block;"> <span id="shangji_name"></span>'+
                    '</div>'+
                '</div>'
            });
        }
        
        function get_user_name(){
            ifxiugai = 0;
            var id = $("#shangji_input").val();
            if(!isNaN(id)){
                $.ajax({
                    type: "POST",
                    url: "?s=users&a=getuserbyid&id="+id,
                    data: "",
                    dataType:'json',timeout : 8000,
                    success: function(resdata){
                        if(resdata.code==0){
                            layer.msg(resdata.message);
                            return false;
                        }else{
                            ifxiugai = 1;
                            $("#shangji_name").text(resdata.nickname+'('+resdata.username+')');
                        }
                    }
                });
            }
        }
    </script>
    <script type="text/javascript" src="js/users/gift.js"></script>
    <? require('views/help.html');?>
</body>
</html>