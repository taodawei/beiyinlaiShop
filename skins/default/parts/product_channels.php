<?php
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("cache/channels_$comId.php")){
  $cache = 1;
  $content = file_get_contents("cache/channels_$comId.php");
  $channels = json_decode($content);
}
if(empty($channels))$channels = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
?>
<div data-page="about" class="page">
    <div class="fenlei_1">    
        商品分类
        <div class="fenlei_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fenlei_1.png"/>
        </div>
    </div>
  <div class="page-content">
    <div class="fenlei">
      <div class="fenlei_02">
        <a href="/index.php?p=4"><div class="fenlei_02_left">
          全部商品
        </div>
        <div class="fenlei_02_right">
          <img src="/skins/default/images/biao_24.png"/>
        </div>
        </a>
        <div class="clearBoth"></div>
      </div>
      <? if(!empty($channels)){
        foreach ($channels as $c){
          if($cache==1){
            $channels1 = $c->channels;
          }else{
            $channels1 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
          }
        ?>
          <div class="fenlei_03">
            <div class="fenlei_03_up" onclick="$('#channels_<?=$c->id?>').toggle();">
              <div class="fenlei_02_left">
                <?=$c->title?>
              </div>
              <div class="fenlei_02_right">
                <a href="/index.php?p=4&channelId=<?=$c->id?>">查看全部 <img src="/skins/default/images/biao_24.png"/></a>
              </div>
              <div class="clearBoth"></div>
            </div>
            <? if(!empty($channels1)){?>
            <div class="fenlei_03_down hide" id="channels_<?=$c->id?>">
              <ul>
                <? 
                foreach($channels1 as $c1){
                  if($cache==1){
                    $channels2 = $c1->channels;
                  }else{
                    $channels2 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
                  }
                  ?>
                  <li><a <? if(!empty($channels2)){?>href="javascript:showZiChannel(<?=$c1->id?>);"<? }else{?> href="/index.php?p=4&channelId=<?=$c1->id?>"<? }?>><?=$c1->title?></a>
                    <? if(!empty($channels2)){?>
                    <div class="fenlei_04 hide" id="channels_<?=$c1->id?>">
                      <ul>
                        <li>
                          <a href="/index.php?p=4&channelId=<?=$c1->id?>">全部</a>
                        </li>
                        <div class="clearBoth"></div>
                        <? foreach($channels2 as $c2){
                          if($cache==1){
                            $channels3 = $c2->channels;
                          }else{
                            $channels3 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
                          }
                        ?>
                        <li>
                          <a <? if(!empty($channels3)){?>href="javascript:showZiChannel1(<?=$c2->id?>);"<? }else{?> href="/index.php?p=4&channelId=<?=$c2->id?>"<? }?>><?=$c2->title?></a>
                        </li>
                        <div class="clearBoth"></div>
                        <? if(!empty($channels3)){?>
                          <div class="fenlei_05 hide" id="channels_<?=$c2->id?>">
                            <ul>
                              <li>
                                <a href="/index.php?p=4&channelId=<?=$c2->id?>">全部</a>
                              </li>
                              <div class="clearBoth"></div>
                              <? foreach($channels3 as $c3){?>
                              <li>
                                <a href="/index.php?p=4&channelId=<?=$c3->id?>"><?=$c3->title?></a>
                              </li>
                              <div class="clearBoth"></div>
                              <? }?>
                            </ul>
                          </div>
                        <? }}?>
                        <div class="clearBoth"></div>
                      </ul>
                    </div>
                    <? }?>
                  </li>
                <? }?>
                <div class="clearBoth"></div>
              </ul>
            </div>
            <? }?>
          </div>
        <?}
      }?>
    </div>
  </div>
</div>
<?
  require(ABSPATH.'/skins/default/bottom.php');
?>
<script type="text/javascript">
function showZiChannel(channelId){
    if($("#channels_"+channelId).hasClass("hide")){
        $(".fenlei_04").addClass("hide");
        $(".fenlei_05").addClass("hide");
        $("#channels_"+channelId).removeClass("hide");
    }else{
        $(".fenlei_04").addClass("hide");
        $(".fenlei_05").addClass("hide");
    }
}
function showZiChannel1(channelId){
    if($("#channels_"+channelId).hasClass("hide")){
        $(".fenlei_05").addClass("hide");
        $("#channels_"+channelId).removeClass("hide");
    }else{
        $(".fenlei_05").addClass("hide");
    }
}
</script>