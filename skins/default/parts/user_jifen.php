<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user = $db->get_row("select id,jifen from users where id=$userId");
$if_qiandao1 = $db->get_var("select days from user_qiandao where userId=$userId and comId=$comId and dtTime='".date("Y-m-d")."' limit 1");
//$jifen_yue = $db->get_var("select jifen_yue from user_shezhi where comId=$comId");
?>
<link href="/skins/default/styles/jifen.css" rel="stylesheet" type="text/css">
<div class="wodejifen">
	<div class="wodejifen_1">
    	<div class="wodejifen_1_01">
        	<div class="wodejifen_1_01_up">
            	我的积分
                <div class="wodejifen_1_01_up_left" onclick="go_prev_page();">
                	<img src="/skins/default/images/a925_1.png" />
                </div>
                <div class="wodejifen_1_01_up_right" onclick="location.href='/index.php?p=8&a=jfjl'">
                	积分明细
                </div>
            </div>
        	<div class="wodejifen_1_01_down" style="display:none;">
            	我的积分
                <div class="wodejifen_1_01_down_left">
                	<img src="/skins/default/images/a923_14.png" />
                </div>
                <div class="wodejifen_1_01_down_right">
                	积分明细
                </div>
            </div>
        </div>
    	<div class="wodejifen_1_02">
        	我的<b id="my_jifen"><?=$user->jifen?></b>积分 <img src="/skins/default/images/a925_11.png" />
        </div>
    	<div class="wodejifen_1_03" onclick="qiandao();" <? if($if_qiandao1>0){?>style="display:none"<? }?>>
        	签到
        </div>
        <div class="wodejifen_1_04" onclick="location.href='/index.php?p=8&a=qdjf';" <? if($if_qiandao1==0){?>style="display:none;"<? }?>>
        	已连续签到<br><span id="my_days"><?=$if_qiandao1?></span>天
        </div>
    </div>
	<div class="wodejifen_2">
    	<div class="wodejifen_2_left">
        	<ul>
        		<li>
                	<a href="/index.php?p=8&a=qdjf">
                    	<img src="/skins/default/images/a925_13.png" /><br>签到领分
                    </a>
                </li>
                <li>
                	<a href="/index.php?p=8&a=jifen_rule">
                    	<img src="/skins/default/images/a925_14.png" /><br>分享领分
                    </a>
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    	<div class="wodejifen_2_right">
        	<ul>
        		<li>
                	<a href="javascript:" onclick="layer.open({content:'功能开发中~~',skin: 'msg',time: 2});">
                    	<img src="/skins/default/images/a925_15.png" /><br>优惠购
                    </a>
                </li>
                <li>
                	<a href="/index.php?p=4&a=jifen">
                    	<img src="/skins/default/images/a925_16.png" /><br>积分兑换
                    </a>
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="shoucangshangpin_2">
	    <img src="/skins/erp_zong/images/shoucangshangpin_12.gif" />
	</div>
	<div class="shoucangshangpin_3">
	    <ul id="tuijian_list">
	      <div class="clearBoth"></div>
	    </ul>
	</div>
</div>
<script type="text/javascript" src="/skins/default/scripts/user/jifen.js"></script>