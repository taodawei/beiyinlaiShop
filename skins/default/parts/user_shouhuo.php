<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
if($_SESSION['if_tongbu']==1){
  $comId = 10;
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
}
$keyword = trim($request['keyword']);
?>
<div class="sousuo">
  <div class="sousuo_1" style="background-color:#ffffff;">
      <div class="sousuo_1_01" onclick="location.href='/index.php?p=8'">
          <img src="/skins/default/images/sousuo_1.png"/>
        </div>
      <form  id="sssForm" action="/index.php" method="get">
        <input type="hidden" name="p" value="8">
        <input type="hidden" name="a" value="shouhuo">
        <div class="sousuo_1_02">
          <div class="sousuo_1_02_left">
              <img src="/skins/default/images/sou_1.png"/>
            </div>
          <div class="sousuo_1_02_right">
              <input type="text" name="keyword" placeholder="姓名/电话/地址" value="<?php echo $keyword ;?>" />
            </div>
          <div class="clearBoth"></div>
        </div>
        <div class="sousuo_1_03">
          <a href="javascript:$('#sssForm').submit();">搜索</a>
        </div>
      </form>
      <div class="clearBoth"></div>
    </div>
  <div class="shouhuodizhi">
    <?php  
    $where = "";
    
    if($keyword){
      $where .= " and (name like '%".$keyword."%' or phone like '%".$keyword."%' or address like '%".$keyword."%' or areaName like '%".$keyword."%')";
    }
    $shouhuos = $db->get_results("select * from user_address where userid=$userId and comId=$comId".$where);
    if(!empty($shouhuos)){?>
      <ul>
      <? 
            $i = 0;
            foreach($shouhuos as $shouhuo){
              $i++;
              ?>
              <li class="shouhuodizhi_on">
                <div class="shouhuodizhi_up">
                    <h2><?=$shouhuo->name?> <span><?=$shouhuo->phone?></span></h2>
                      <?=$shouhuo->areaName?><?=$shouhuo->address?>
                  </div>
                  <div class="shouhuodizhi_down">
                      <div class="shouhuodizhi_down_left">
                          <? if($shouhuo->moren==1){?>
                          <img src="/skins/default/images/shenqingshouhou_12.png"/> 默认
                          <? }else{?>
                          <a href="/index.php?p=8&a=shouhuoMoren&id=<?=$shouhuo->id?>">默认</a>
                          <? }?>                          
                      </div>
                      <div class="shouhuodizhi_down_right">
                          <a href="/index.php?p=8&a=shouhuoDel&id=<?=$shouhuo->id?>" onclick="return window.confirm('确定要删除吗')""><img src="/skins/default/images/shouhuodizhi_1.png"/> 删除</a>
                            <a href="/index.php?p=8&a=shouhuoEdit&id=<?=$shouhuo->id?>"><img src="/skins/default/images/shouhuodizhi_11.png"/> 编辑</a>
                      </div>
                    <div class="clearBoth"></div>
                  </div>
              </li>
            <?
            }
          ?>
      </ul>
    <?php }else{?>
        <div class="shouhuodizhi_wu">
          <img src="/skins/default/images/shouhuodizhi_12.png"><br>您还没有收货地址哦，添加一个吧！
        </div>
    <?php }?>
        <div class="shouhuodizhi_xinjian">
          <a href="/index.php?p=8&a=shouhuoEdit"><span>+</span>新建地址</a>
        </div>
    </div>
</div>
