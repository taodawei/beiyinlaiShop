<?
global $db,$request;
$news = $db->get_row("select * from demo_list where id=".(int)$request['id']);
?>
<link href="/skins/default/styles/wode.css" rel="stylesheet" type="text/css">
<div class="sousuo">
    <div class="wode_1">
        <?=$news->title?>
        <div class="wode_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/sousuo_1.png" />
        </div>
    </div>
    <div class="xinwenxiangqing">
        <div class="xinwenxiangqing_3">
            <?=$news->content?>
        </div>
    </div>
</div>