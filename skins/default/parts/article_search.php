<div class="sousuo">
	<div class="sousuo_1">
    	<div class="sousuo_1_01" onclick="go_prev_page();">
        	<img src="/skins/default/images/sousuo_1.png"/>
        </div>
        <form method="get" action="/index.php" id="search_form">
            <input type="hidden" name="p" value="4">
        	<div class="sousuo_1_02">
            	<div class="sousuo_1_02_left">
                	<img src="/skins/default/images/sou_1.png"/>
                </div>
            	<div class="sousuo_1_02_right">
                	<input type="text" name="keyword" id="keyword" placeholder="搜索商品"/>
                </div>
            	<div class="clearBoth"></div>
            </div>
        	<div class="sousuo_1_03">
            	<a href="javascript:" onclick="$('#search_form').submit();">搜索</a>
            </div>
        </form>
    	<div class="clearBoth"></div>
    </div>
    <?
    global $db;
    $remens = $db->get_var("select remen_sousuo from demo_shezhi order by comId desc limit 1");
    $remen_arr = array();
    if(!empty($remens))$remen_arr = explode(',',$remens);
    $historys = array();
    if(!empty($_COOKIE['search_history'])){
        $historys = json_decode($_COOKIE['search_history'],true);
    }
    ?>
	<div class="sousuo_2">
    	<div class="sousuo_2_up">
        	热门搜索
        </div>
    	<div class="sousuo_2_down">
        	<ul>
                <? if(!empty($remen_arr)){
                    foreach ($remen_arr as $val) {
                        ?><li><a href="/index.php?p=4&keyword=<?=$val?>"><?=$val?></a></li><?
                    }
                }?>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    </div>
	<div class="sousuo_3">
    	<div class="sousuo_3_up">
        	<div class="sousuo_3_up_left">
            	历史搜素
            </div>
        	<div class="sousuo_3_up_right">
            	<img src="/skins/default/images/sousuo_11.png"/> 清空历史记录
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="sousuo_3_down">
        	<ul>
                <? if(!empty($historys)){
                    foreach ($historys as $val){
                        ?><li><a href="/index.php?p=4&keyword=<?=$val?>"><?=$val?></a></li><?
                    }
                }?>
        	</ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("#keyword").focus();
    });
</script>