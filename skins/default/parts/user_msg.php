<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$last = $db->get_var("select content from user_msg$fenbiao where userId=$userId order by id desc limit 1");
?>
<div class="zhifu">
	<div class="zhifu_1">
    	消息中心
        <div class="zhifu_1_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/fanhui_1.png"/>
        </div>
    </div>
	<div class="xiaoxizhongxin_02">
    	<ul>
    		<li onclick="location.href='/index.php?p=8&a=msglist';">
            	<div class="xiaoxizhongxin_02_1">
                	<img src="/skins/default/images/biao_107.png"/>
                </div>
            	<div class="xiaoxizhongxin_02_2">
                	<h2>业务消息</h2>
                    <? if(!empty($last)){echo sys_substr($last,30,true);}?>
                </div>
            	<div class="xiaoxizhongxin_02_3">
                	<img src="/skins/default/images/fenlei_11.png"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li onclick="location.href='/index.php?p=4&a=huodong';">
            	<div class="xiaoxizhongxin_02_1">
                	<img src="/skins/default/images/biao_108.png"/>
                </div>
            	<div class="xiaoxizhongxin_02_2">
                	<h2>优惠信息</h2>更多惊喜等着你
                </div>
            	<div class="xiaoxizhongxin_02_3">
                	<img src="/skins/default/images/fenlei_11.png"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
    	</ul>
    </div>
</div>