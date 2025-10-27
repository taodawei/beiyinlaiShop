<?
global $db,$request,$adminRole,$db,$qx_arry;

$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];

$allRows = array(
    "image"=>array("title"=>"列表图","rowCode"=>"{field:'image',title:'列表图',width:150}"),
	"title"=>array("title"=>"标题","rowCode"=>"{field:'title',title:'标题',width:150}"),
	"en_title"=>array("title"=>"英文","rowCode"=>"{field:'en_title',title:'英文',width:150}"),

	"statusInfo"=>array("title"=>"展示","rowCode"=>"{field:'statusInfo',title:'展示',width:120}"),
	"ordering"=>array("title"=>"权重","rowCode"=>"{field:'ordering',title:'权重',width:100}"),

	"dtTime"=>array("title"=>"发布时间","rowCode"=>"{field:'dtTime',title:'发布时间',width:200,sort:true}")
);

			
			
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['gonggaoPageNum'])?10:$_COOKIE['gonggaoPageNum'];
$channels = $db->get_results("select * from demo_study_channel where comId=$comId and parentId = 0");
foreach ($channels as &$channel){
    $channel->channels = $db->get_results("select * from demo_study_channel where comId=$comId and parentId = $channel->id");
}

$source = (int)$request['source'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/kehu_fankui.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:80px}
		.layui-table-view{margin:10px;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		td[data-field="title"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_131.png"/> 商品专题列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left" style="display:none;">
						<div class="splist_up_01_left_01">
							<div class="splist_up_01_left_01_up">
								<span>全部分类</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_01_down">
								<ul style="border-left:0px;" id="ziChannels1">
									<li class="allsort_01">
										<a href="javascript:selectLevel(0,'全部分类');">全部分类</a>
									</li>
									<? if(!empty($channels)){
										foreach ($channels as $c) {
											?>
											<li class="allsort_01">
												<a href="javascript:" onclick="selectLevel(<?=$c->id?>,'<?=$c->title?>');" onmouseenter="loadZiChannels(<?=$c->id?>,2,<? if(!empty($c->channels)){echo 1;}else{echo 0;}?>);" class="allsort_01_tlte"><?=$c->title?> <? if(!empty($c->channels)){?><span><img src="images/biao_24.png"/></span><? }?></a>
											</li>
											<?
										}
										?><?
									}?>
								</ul>
							</div>
						</div>
						<div class="sprukulist_01">
                        	<div class="sprukulist_01_left">
                            	<span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                            </div>
                        	<div class="sprukulist_01_right">
                            	<img src="images/biao_76.png"/>
                            </div>
                        	<div class="clearBoth"></div>
                        	<div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;">
                        		<div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                        	</div>
                        </div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入专题关键词"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
						    <? chekurl($arr,'<a href="?m=system&s=topic&a=add" class="splist_add">新 增</a>') ?>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
				<script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
						<li>
							<a href="javascript:editStudy();"><img src="images/biao_136.png"> 修改</a>
						</li>
						<li>
							<a href="javascript:z_confirm('确定要删除该专题吗？',del_study,'');"><img src="images/biao_32.png"> 删除</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="keyword" value="<?=$keyword?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laydate', 'laypage','table','form','upload'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,upload = layui.upload
		  ,load = layer.load()
		  laydate.render({
		  	elem: '#riqi1'
		  	,show: true
		  	,position: 'static'
		  	,min: '2017-12-1'
  			,max: '<?=date("Y-m-d")?>'
  			<?=empty($startTime)?'':",value:'$startTime'"?>
  			,btns: []
  			,done: function(value, date, endDate){
  				$("#s_time1").html(value);
  				$("#startTime").val(value);
  			}
		  });
		  laydate.render({
		  	elem: '#riqi2'
		  	,show: true
		  	,position: 'static'
		  	<?=empty($endTime)?'':",value:'$endTime'"?>
		  	,min: '2017-12-1'
  			,max: '<?=date("Y-m-d")?>'
  			,btns: ['confirm']
  			,done: function(value, date, endDate){
  				$("#s_time2").html(value);
  				$("#endTime").val(value);
  			}
		  });
		  $(".laydate-btns-confirm").click(function(){
		  	$("#riqilan").slideUp(200);
		  	reloadTable(0);
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=topic&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	keyword:'<?=$keyword?>',
		    	startTime:'<?=$startTime?>',
		    	endTime:'<?=$endTime?>'
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			    $("th[data-field='id']").hide();
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var keyword = $("#keyword").val();
		  	var startTime = $("#startTime").val();
		  	var endTime = $("#endTime").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,channelId:channelId
			      ,keyword:keyword
			      ,startTime:startTime
			      ,endTime:endTime
			    },page: {
					curr: 1
				},done:function(){
					$("th[data-field='id']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		});
		
		
		function loadZiChannels(menuId,ceng,hasnext){
        	var channelDiv = $(".splist_up_01_left_01_down");
        	if($("#ziChannels"+ceng).length==0&&hasnext==1){
        		var ulstr = '<ul id="ziChannels'+ceng+'"><div style="text-align:center;"><img src="images/loading.gif"></div></ul>';
        		var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
        		channelDiv.css("width",(nowWidth+200)+"px");
        		channelDiv.append(ulstr);
        	}else{
        		if(ceng<4&&$("#ziChannels4").length>0){
        			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
        			channelDiv.css("width",(nowWidth-200)+"px");
        			$("#ziChannels4").remove();
        		}
        		if(ceng<3&&$("#ziChannels3").length>0){
        			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
        			channelDiv.css("width",(nowWidth-200)+"px");
        			$("#ziChannels3").remove();
        		}
        		if($("#ziChannels"+ceng).length>0&&hasnext==0){
        			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
        			channelDiv.css("width",(nowWidth-200)+"px");
        			$("#ziChannels"+ceng).remove();
        		}else{
        			$("#ziChannels"+ceng).html('<div style="text-align:center;"><img src="images/loading.gif"></div>');
        		}
        	}
        	if(hasnext==1){
        		ajaxpost=$.ajax({
        			type: "POST",
        			url: "/erp_service.php?action=get_study_channels1",
        			data: "&id="+menuId,
        			dataType:"json",timeout : 8000,
        			success: function(resdata){
        				var listr = '';
        				for(var i=0;i<resdata.items.length;i++){
        					if(ceng<4){
        						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectLevel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" onmouseenter="loadZiChannels('+resdata.items[i].id+','+(ceng+1)+','+resdata.items[i].hasNext+');" class="allsort_01_tlte">'+resdata.items[i].title+(resdata.items[i].hasNext==1?' <span><img src="images/biao_24.png"></span>':'')+' </a></li>';
        					}else{
        						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectLevel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" class="allsort_01_tlte">'+resdata.items[i].title+'</a></li>';
        					}
        				}
        				$("#ziChannels"+ceng).html(listr);
        			},
        			error: function() {
        				layer.closeAll();
        				layer.msg('数据请求失败', {icon: 5});
        			}
        		});
        	}
        }
        
        function editStudy(){
        	var channelId = $("#channelId").val();
        	var keyword = $("#keyword").val();
        	var startTime = $("#startTime").val();
        	var endTime = $("#endTime").val();
        	var	page = $("#page").val();
        	var order1 = $("#order1").val();
        	var order2 = $("#order2").val();
        	var url = "?m=system&s=topic&channelId="+channelId+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&page="+page+"&order1="+order1+"&order2="+order2;
        	url = encodeURIComponent(url);
        	location.href="?m=system&s=topic&a=add&id="+getPdtId()+"&url="+url;
        }
        
        
        function del_study(params){
        	var pdtId = getPdtId();
        	layer.load();
        	ajaxpost=$.ajax({
        		type: "POST",
        		url: "?m=system&s=topic&a=del",
        		data: "&ids="+pdtId,
        		dataType:"json",timeout : 20000,
        		success: function(resdata){
        			layer.closeAll();
        			if(resdata.code==0){
        				layer.msg(resdata.message, {icon: 5});
        			}else{
        				reloadTable(1);
        			}
        		},
        		error: function() {
        			layer.closeAll();
        			layer.msg('数据请求失败', {icon: 5});
        		}
        	});
        }
        
        
        
//选择分类
	</script>
	<script type="text/javascript" src="js/shezhi/banner_list.js"></script>
	<? require('views/help.html');?>
</body>
</html>