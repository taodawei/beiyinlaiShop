<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
?>
<link href="/skins/default/styles/jifen.css" rel="stylesheet" type="text/css">
<div class="wdjifenmingxi">
  <div class="wdjifenmingxi_1">
      积分规则
        <div class="wdjifenmingxi_1_left" onclick="go_prev_page(0);">
          <img src="/skins/default/images/a923_14.png"/>
        </div>
    </div>
  <div class="wdjifenguize_1" id="ruleMenu">
      <ul>
        <li>
          <a href="javascript:" id="ruleMenu1" onclick="qiehuan('rule',1,'wdjifenguize_1_on');" class="wdjifenguize_1_on">如何使用</a>
        </li>
        <li>
          <a href="javascript:" id="ruleMenu2" onclick="qiehuan('rule',2,'wdjifenguize_1_on');">如何获得</a>
        </li>
        <li>
          <a href="javascript:" id="ruleMenu3" onclick="qiehuan('rule',3,'wdjifenguize_1_on');">扣减规则</a>
        </li>
        <div class="clearBoth"></div>
      </ul>
  </div>
  <div class="wdjifenguize_2 ruleCont" id="ruleCont1">
      <?=dt_article_index(35);?>
  </div>
  <div class="wdjifenguize_2 ruleCont" id="ruleCont2" style="display:none;">
      <?=dt_article_index(36);?>
  </div>
  <div class="wdjifenguize_2 ruleCont" id="ruleCont3" style="display:none;">
      <?=dt_article_index(37);?>
  </div>
</div>