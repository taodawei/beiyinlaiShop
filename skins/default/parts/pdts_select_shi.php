<?
global $db;
$comId = (int)$_SESSION['demo_comId'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$shi_id = empty($request['shi_id'])?(int)$_SESSION['shi_id']:(int)$request['shi_id'];
$channels = array();
if(is_file("cache/pdt_area_$comId.php")){
  $cache = 1;
  $content = file_get_contents("cache/pdt_area_$comId.php");
  $channels = json_decode($content);
}
if(empty($channels)){
    $areas = $db->get_results("select * from demo_pdt_area where comId=$comId order by orders asc");
    if(!empty($areas)){
        $now_orders = '';
        foreach ($areas as $area){
            $channels[$area->orders][] = $area;
        }
    }
}
?>
<link href="/skins/default/styles/bendi.css" rel="stylesheet" type="text/css">
<div class="bendiliebiao">	
	<div class="bendiliebiao_up">
    	<div class="bendiliebiao_up_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/bendi_1.png"/> 直商易购本地
        </div>
    	<div class="clearBoth"></div>
    </div>
    <? if($shi_id>0){
        $shi_title = $db->get_var("select title from demo_area where id=$shi_id");
    ?>
	<div class="bendizhandian_1">
    	<div class="bendizhandian_1_up">
        	当前/已选站点
        </div>
    	<div class="bendizhandian_1_down">
        	<span><?=$shi_title?></span>
        </div>
    </div>
    <? }?>
	<div class="bendizhandian_2">
    	<div class="bendizhandian_1_up">
        	热门站点
        </div>
        <div class="bendizhandian_2_down">
        	<ul>
                <? if(!empty($channels)){
                    foreach ($channels as $channel) {
                        foreach ($channel as $c) {
                            if($c->if_remen==1){
                                ?><li><a href="/index.php?p=22&shi_id=<?=$c->shiId?>"><?=$c->title?></a></li><?
                            }
                        }
                    }
                }?>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    </div>
	<div class="bendizhandian_3">
        <?
        if(!empty($channels)){
            foreach ($channels as $key => $val) {
                ?>
                <div class="bendizhandian_3_up" id="citys_<?=$key?>"><?=$key?></div>
                <div class="bendizhandian_3_down">
                    <ul>
                        <? foreach($val as $c){
                            ?><li><a href="/index.php?p=22&shi_id=<?=$c->shiId?>"><?=$c->title?></a></li><?
                        }?>
                    </ul>
                </div>
                <?
            }
        }
        ?>
    </div>
    <div class="bendizhandian_4">
    	<ul>
            <?
            if(!empty($channels)){
                foreach ($channels as $key => $val) {
                    ?><li><a href="#citys_<?=$key?>"><?=$key?></a></li><?
                }
            }?>
    	</ul>
    </div>
</div>