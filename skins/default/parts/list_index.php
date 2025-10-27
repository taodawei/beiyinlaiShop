<?
global $db,$request;
$comId = (int)$_SESSION['demo_comId'];
$channels = $db->get_results("select id,title from demo_list_channel where comId=$comId order by id");
?>
<link href="/skins/default/styles/wode.css" rel="stylesheet" type="text/css">
<style type="text/css">
    .xinwenxiaoxi_down_tt{float:left;padding-top:0px;}
    .xinwenxiaoxi_down_time{float:right;display:inline-block;}
</style>
<div class="wode_1">
 新闻资讯
 <div class="wode_1_left" onclick="go_prev_page();">
     <img src="/skins/default/images/sousuo_1.png" />
 </div>
</div>
<div class="xinwenxiaoxi">
    <? if(!empty($channels)){?>
    <div class="xinwenxiaoxi_up">
        <ul>
            <? foreach($channels as $i=>$c){?>
            <li>
                <a href="javascript:" onclick="qiehuan_type(<?=$i?>,<?=$c->id?>);"><?=$c->title?></a>
            </li>
            <? }?>
        </ul>
    </div>
    <? }?>
    <div class="xinwenxiaoxi_down">
        <ul id="flow_ul"></ul>
    </div>
</div>
<script type="text/javascript">
    var channelId = 5;
    var flow;
    layui.use('flow', function(){
        flow = layui.flow;
        $("#flow_ul").html('');
        reloadTable();
    });
    function reloadTable(){
        flow.load({
            elem: '#flow_ul'
            ,done: function(page, next){
                var lis = [];
                $.ajax({
                    type: "POST",
                    url: "/index.php?p=5&a=get_news_list&pageNum=10&page="+page,
                    data: "channelId="+channelId,
                    dataType:"json",timeout : 20000,
                    success: function(res){
                        $.each(res.data, function(index, item){
                            var str = '<li>'+
                                '<a href="/index.php?p=5&a=view&id='+item.id+'">'+
                                    '<div class="xinwenxiaoxi_down_tt">'+item.title+'</div>'+
                                    '<div class="xinwenxiaoxi_down_time">'+item.dtTime+'</div>'+
                                    '<div class="clearBoth"></div>'+
                                '</a>'+
                            '</li>';
                            lis.push(str);
                        });
                        next(lis.join(''), page < res.pages);
                    },
                    error: function() {
                        layer.closeAll();
                        layer.msg('数据请求失败', {icon: 5});
                    }
                });
            }
        });
    }
    function qiehuan_type(index,id){
        channelId = id;
        $(".xinwenxiaoxi_up .xinwenxiaoxi_up_on").removeClass('xinwenxiaoxi_up_on');
        $(".xinwenxiaoxi_up li").eq(index).find("a").addClass('xinwenxiaoxi_up_on');
        $("#flow_ul").html('');
        reloadTable();
    }
</script>