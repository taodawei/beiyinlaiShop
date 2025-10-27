<body style="background:url(<?=empty($_SESSION['demo_com_back'])?'/skins/default/images/bj.gif':$_SESSION['demo_com_back']?>) center top no-repeat #f6f6f6;background-size:100%;">
  <div id="shouye">
    <div class="shouye_1">
      <div class="shouye_1_left">
        <div class="shouye_1_left_01">
          <img src="/skins/default/images/sou_1.png"/>
        </div>
        <div class="shouye_1_left_02">
          <form action="/index.php" method="get">
            <input type="hidden" name="p" value="4">
            <input type="search" name="keyword" placeholder="搜索关键词"/>
          </form>
        </div>
        <div class="clearBoth"></div>
      </div>
      <div class="shouye_1_right">
        <ul>
          <li>
            <a href="?p=4&a=channels"><img src="/skins/default/images/biao_1.png"/><br>分类</a>
          </li>
          <li>
            <a href="javascript:" onclick="$('#fenxiang_tc').show();"><img src="/skins/default/images/biao_11.png"/><br>分享</a>
          </li>
          <div class="clearBoth"></div>
        </ul>
      </div>
      <div class="clearBoth"></div>
    </div>
    <div class="shouye_2">
      <div class="shouye_2_left" onclick="location.href='/index.php?p=1&a=shop';">
        <img src="<?=$_SESSION['demo_com_logo']?>"/>
      </div>
      <div class="shouye_2_right" onclick="location.href='/index.php?p=1&a=shop';">
        <h2><?=$_SESSION['demo_com_title']?> <img src="/skins/default/images/biao_12.png"/></h2>
        <?=$_SESSION['demo_com_remark']?>
      </div>
      <div class="dianpu_3_2" style="float:right;margin-right:.5rem;margin-top:.4rem;" onclick="guanzhu();">
        <? 
        $comId = (int)$_SESSION['demo_comId'];
        $ifguanzhu = (int)$db->get_var("select userId from user_shop_collect where userId=".(int)$_SESSION['demo_zhishangId']." and shopId=$comId");?>
        <img src="/skins/muying/images/dianpu_guanzhu<? if($ifguanzhu>1){echo '1';}?>.png" style="width:3rem">
      </div>
      <div class="clearBoth"></div>
    </div>
    <div class="shouye_3">
      <ul>
        <li>
          <a href="/" <? if(empty($request['p'])){?>class="shouye_3_on"<? }?>>首页</a>
        </li>
        <li>
          <a href="/index.php?p=4" <? if($request['p']==4 && $request['a']!='shangxin' && $request['a']!='huodong'){?>class="shouye_3_on"<? }?>>商品</a>
        </li>
        <li>
          <a href="/index.php?p=4&a=huodong" <? if($request['a']=='huodong'){?>class="shouye_3_on"<? }?>>活动</a>
        </li>
        <li>
          <a href="/index.php?p=4&a=shangxin" <? if($request['a']=='shangxin'){?>class="shouye_3_on"<? }?>>上新</a>
        </li>
        <div class="clearBoth"></div>
      </ul>
    </div>
    <script type="text/javascript">var ifguanzhu = <?=$ifguanzhu>0?1:0?>;</script>