<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"title"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'title',title:'".$kehu_title."名称',width:220}"),
				"content"=>array("title"=>"反馈内容","rowCode"=>"{field:'content',title:'反馈内容',width:400}"),
				"dtTime"=>array("title"=>"反馈时间","rowCode"=>"{field:'dtTime',title:'反馈时间',width:130,sort:true}")
			);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$isnew = (int)$request['isnew'];
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['fankuiPageNum'])?10:$_COOKIE['fankuiPageNum'];
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$weidu = 0;
$yidu = 0;
$fankuis = $db->get_results("select count(*) as num,isnew from demo_kehu_fankui where comId=$comId group by(isnew)");
if(!empty($fankuis)){
	foreach ($fankuis as $f) {
		if($f->isnew==0){
			$yidu = $f->num;
		}else{
			$weidu = $f->num;
		}
	}
}
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
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> <?=$kehu_title?>反馈
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部(<?=$weidu+$yidu?>)</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectLevel(0,'全部(<?=$weidu+$yidu?>)');" class="splist_up_01_left_02_down_on">全部(<?=$weidu+$yidu?>)</a>
									</li>
									<li class="allsort_01">
										<a href="javascript:" onclick="selectLevel(2,'已回复(<?=$yidu?>)');" class="allsort_01_tlte">已回复(<?=$yidu?>)</a>
									</li>
									<li class="allsort_01">
										<a href="javascript:" onclick="selectLevel(1,'未回复(<?=$weidu?>)');" class="allsort_01_tlte">未回复(<?=$weidu?>)</a>
									</li>
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
					<div class="splist_up_01_right" style="float:left;margin-left:20px;">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
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
						<li class="operate1">
							<a href="javascript:huifu();"><img src="images/biao_100.png"> 立即回复</a>
						</li>
						<li class="operate2">
							<a href="javascript:huifu();"><img src="images/biao_30.png"> 查看详情</a>
						</li>
						<li>
							<a href="javascript:z_confirm('确定要删除该反馈吗？',del_fankui,'');"><img src="images/biao_32.png"> 删除</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="khfk_ljhuifu" id="khfk_ljhuifu">
    	<div class="khfk_ljhuifu1">
        	<div class="khfk_ljhuifu_01">
            	回复
            </div>
        	<div class="khfk_ljhuifu_03" id="khfk_ljhuifu_03">
            	<div class="khfk_ljhuifu_03_up">
                	<textarea id="khfk_content" cols="30" rows="10" placeholder="请输入回复内容……"></textarea>
                </div>
            	<div class="khfk_ljhuifu_03_down" id="khfk_upload">
                	<img src="images/biao_104.png"> 添加附件 <span>（附件最大1M，支持格式：JPG、PNG）</span>
                </div>
                <div id="khfk_imglist">
                	
                </div>
                <input type="hidden" id="khfk_imgs" value="">
                <input type="hidden" id="khfk_id" value="0">
            </div>
        	<div class="khfk_ljhuifu_04">
            	<a href="javascript:" onclick="khfk_send();" class="khfk_ljhuifu_04_1">发送</a><a href="javascript:khfk_cancel();" class="khfk_ljhuifu_04_2">取消</a>
            </div>
        </div>
    </div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="isnew" value="<?=$isnew?>">
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
		  upload.render({
		    elem: '#khfk_upload'
		    ,url: '?m=system&s=upload&a=upload'
		    ,before:function(){
		    	layer.load();
		    }
		    ,done: function(res){
		      layer.closeAll('loading');
		      if(res.code > 0){
		      	return layer.msg(res.msg);
		      }else{
		      	$('#khfk_imglist').append('<a href="'+res.url+'" target="_blank"><img src="'+res.url+'?x-oss-process=image/resize,w_54" width="54" height="54"></a>');
		      	var originalPic = $("#khfk_imgs").val();
		      	if(originalPic==''){
		      		originalPic = res.url;
		      	}else{
		      		originalPic = originalPic+'|'+res.url;
		      	}
		      	$("#khfk_imgs").val(originalPic);
		      }
		  	}
		  	,error: function(){
		  		layer.msg('上传失败，请重试', {icon: 5});
		  	}
		});
		  $(".laydate-btns-confirm").click(function(){
		  	$("#riqilan").slideUp(200);
		  	reloadTable(0);
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=kehu&a=getFankuis'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	isnew:<?=$isnew?>,
		    	keyword:'<?=$keyword?>',
		    	startTime:'<?=$startTime?>',
		    	endTime:'<?=$endTime?>'
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			    $("th[data-field='id']").hide();
			    $("th[data-field='status']").hide();
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var isnew = $("#isnew").val();
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
			      ,isnew:isnew
			      ,keyword:keyword
			      ,startTime:startTime
			      ,endTime:endTime
			    },page: {
					curr: 1
				},done:function(){
					$("th[data-field='id']").hide();
			    	$("th[data-field='status']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		});
	</script>
	<script type="text/javascript" src="js/kehu_fankui.js"></script>
	<? require('views/help.html');?>
</body>
</html>