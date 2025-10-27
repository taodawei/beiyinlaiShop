<div class="wode" style="background-color:#f6f6f6;">
	<div class="wode_1">
    	售后
        <div class="wode_1_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
	<div class="shouhouliebiao">
    	<div class="shouhouliebiao_up">
        	<ul>
        		<li class="shouhouliebiao_up_line">
                	<a href="javascript:" onclick="qiehuan_scene(0)" class="shouhouliebiao_up_on">待处理</a>
                </li>
                <li class="shouhouliebiao_up_line">
                	<a href="javascript:" onclick="qiehuan_scene(1)">已完成</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(2)">已驳回</a>
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    	<div class="shouhouliebiao_down">
        	<ul id="flow_ul"></ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var status = 0;
</script>
<script type="text/javascript" src="/skins/default/scripts/shouhou/shouhou.js"></script>