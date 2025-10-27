<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
$chukuTypes = str_replace('@_@',',',$kucun_set->chuku_types);
$rukuTypes = str_replace('@_@',',',$kucun_set->ruku_types);
$cangkuSql = "select id,title from demo_kucun_store where comId=$comId and status=1";
if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
    $cangkuSql .= " and id in(".$qx_arry['kucun']['storeIds'].")";
}
$cangkuSql .= " order by id asc";
$cangkus = $db->get_results($cangkuSql);
$storeId = $cangkus[0]->id;


$step = 1;
if($product_set->number_num>0){
	$chushu = pow(10,$product_set->number_num);
	$step = 1/$chushu;
}
$orderId = $kucun_set->ruku_pre.'_'.date("Ymd").'_'.getOrderId($comId,1);
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}

$changeId = (int)$request['changeId'];
$change = $db->get_row("select * from kmd_change where id = $changeId");

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
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="right_up">
    	<a href="javascript:history.go(-1);"><img src="images/biao_63.png"/> <?=$change->title?> 新增兑换商品</a>
    </div>
	<div class="right_down">
    	<div class="sprukuadd">
    		<form id="addForm" action="?m=system&s=change&a=addProduct&tijiao=1" method="post" class="layui-form">
    		      <input type="hidden" name="changeId" value="<?=$change->id?>">
        	<div class="sprukuadd_01">
            	<div class="sprukuadd_01_canku">
                	<div class="sprukuadd_01_canku_left">
                    	<span>*</span>生效日期
                    </div>
                	<div class="sprukuadd_01_canku_right">
                    	<input type="text" name="startTime" value="<?=empty($list->startTime)?'':($list->startTime=='0000-00-00'?'':$list->startTime)?>" id="startTime" placeholder="请选择生效日期" class="addhuiyuan_2_02_input"/>
                    </div>
                	<div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
                <div class="sprukuadd_01_riqi">
                	<div class="sprukuadd_01_riqi_left">
                    	<span>*</span>失效日期
                    </div>
                	<div class="sprukuadd_01_riqi_right">
                    	<input type="text" name="endTime" value="<?=empty($list->endTime)?'':($list->endTime=='0000-00-00'?'':$list->endTime)?>" id="endTime" placeholder="请选择失效日期" class="addhuiyuan_2_02_input"/>
                    </div>
                	<div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
            </div>
            <br>
        	<!--<div class="sprukuadd_02">-->
         <!--   	 说明：如无分库系统自动选择为默认仓，如需要分仓库去仓库<a href="index.php?a=shezhi&url=%3Fm%3Dsystem%26s%3Dstore" target="_blank">设置</a>-->
         <!--   </div>-->
        	<div class="sprukuadd_03" style="padding-top:20px;">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0" id="dataTable" rows="1">
                	<tr height="43">
                    	<td bgcolor="#7bc8ed" width="70" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	
                        </td>
                        <td bgcolor="#7bc8ed" width="118" class="sprukuadd_03_title" align="center" valign="middle"> 
                        </td>
                        <td bgcolor="#7bc8ed" width="166" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	商品编码
                        </td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">                         
                        	商品名称
                        </td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">
                        	规格 
                        </td>

                        <td bgcolor="#7bc8ed" width="100" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	单位
                        </td>
                       
                    </tr>
                    <tr height="48" id="rowTr1">
                    	<td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"> 
                        	1
                        </td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">
                        	<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow(1);"><img src="images/biao_66.png"/></a>  
                        </td>
                        <td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">                         
                        	<div class="sprukuadd_03_tt_addsp">
                            	<div class="sprukuadd_03_tt_addsp_left">
                                	<!--<input type="text" class="layui-input addRowtr" id="searchInput1" row="1" placeholder="输入编码/商品名称" >-->
                                </div>
                            	<div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">
                                	●●●
                                </div>
                            	<div class="clearBoth"></div>
                                <div class="sprukuadd_03_tt_addsp_erji" id="pdtList1">
                                	<ul>
                                		<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
                                	</ul>
                                </div>
                            </div>
                        </td> 
                        <!--<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>-->
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <!--<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>-->
                    </tr>
                </table>
                <script type="text/javascript">
                	var jishiqi;
                    $('#searchInput1').bind('input propertychange', function() {
                        clearTimeout(jishiqi);
                        var row = $(this).attr('row');
                        var val = $(this).val();
                        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
                    });
                	$('#searchInput1').click(function(eve){
                		var nowRow = $(this).attr("row");
                		if($("#pdtList"+nowRow).css("display")=="none"){
                			showpdtList(nowRow,$(this).val());
                		}
                		stopPropagation(eve); 
                	});
                </script>
                <script>
                	layui.use(['laydate','form'], function(){
                		  var laydate = layui.laydate
                		  ,form = layui.form
                		  laydate.render({
                		  	elem: '#startTime'
                		  	,min:'<?=date("Y-m-d H:i:s")?>'
                            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
                            ,type: 'datetime'
                            ,format: 'yyyy-MM-dd HH:mm:ss'
                		  });
                		  
                		  laydate.render({
                		  	elem: '#endTime'
                		  	,min:'<?=date("Y-m-d H:i:s")?>'
                            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
                            ,type: 'datetime'
                            ,format: 'yyyy-MM-dd HH:mm:ss'
                		  });
                		 
                		});
                		
                </script>
            </div>
            <div class="sprukuadd_05">
            	<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
				<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
            </div>
        </form>
        </div>
    </div>
    <div class="sprkadd_xuanzesp">
    	<div class="sprkadd_xuanzesp_01">
        	<div class="sprkadd_xuanzesp_01_1">
            	选择商品
            </div>
        	<div class="sprkadd_xuanzesp_01_2" style="position:relative;">
        		<div class="splist_up_01_left_01_up" style="height:37px;line-height:37px;">
        			<span>全部分类</span> <img src="images/biao_20.png"/>
        		</div>
        		<div class="splist_up_01_left_01_down">
        			<ul style="border-left:0px" id="ziChannels1">
        				<li class="allsort_01">
        					<a href="javascript:selectChannel(0,'全部分类');">全部分类</a>
        				</li>
        				<? if(!empty($channels)){
        					foreach ($channels as $c) {
        						?>
        						<li class="allsort_01">
        							<a href="javascript:" onclick="selectChannel(<?=$c->id?>,'<?=$c->title?>');" onmouseenter="loadZiChannels(<?=$c->id?>,2,<? if(!empty($c->channels)){echo 1;}else{echo 0;}?>);" class="allsort_01_tlte"><?=$c->title?> <? if(!empty($c->channels)){?><span><img src="images/biao_24.png"/></span><? }?></a>
        						</li>
        						<?
        					}
        					?><?
        				}?>
        			</ul>
        		</div>
            </div>
        	<div class="sprkadd_xuanzesp_01_3">
            	<div class="sprkadd_xuanzesp_01_3_left">
                	<input type="text" id="keyword" placeholder="请输入商品名称/编码/规格/关键字">
                </div>
            	<div class="sprkadd_xuanzesp_01_3_right">
                	<a href="javascript:reloadTable(0);"><img src="images/biao_21.gif"></a>
                </div>
            	<div class="clearBoth"></div>
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="sprkadd_xuanzesp_02">
        	<table id="product_list" lay-filter="product_list">
			</table>
        </div>
    	<div class="sprkadd_xuanzesp_03">
        	<a href="javascript:" id="sprkadd_xuanzesp_03_01" class="sprkadd_xuanzesp_03_01">确  认</a><a href="javascript:hideSearch();" class="sprkadd_xuanzesp_03_02">取  消</a>
        </div>
    </div>
    <input type="hidden" id="channelId" value="<?=$channelId?>">
    <script type="text/javascript">
		var step = <?=$step?>;
		var productListTalbe;
		var productListForm;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,active = {
		  	appendCheckData: function(){
		  		var checkStatus = table.checkStatus('product_list')
		  		,data = checkStatus.data;
		  		if(data.length>0){
		  			var num = parseInt($("#dataTable").attr("rows"));
		  			var rownums = $("#dataTable tr").length;
		  			$("#dataTable tr").eq(rownums-1).remove();
			    	for (var i = 0; i < data.length; i++) {
			    		var inventoryId = data[i].id;
			    		var sn = data[i].sn;
			    		var title = data[i].title;
			    		var key_vals = data[i].key_vals;
			    		var shuliang = $("#shuliang_"+inventoryId).val();
                        var max = $("#shuliang_"+inventoryId).attr('max');
			    		var units = data[i].units;
			    		var productId = data[i].productId;
			    		num = num+1;
			    		var str = '<tr height="48" id="rowTr'+num+'"><td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
						'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
							'<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a> '+ 
						'</td>'+
						'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
						'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
						'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
						'<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
				// 			'<input type="text" lay-verify="required|number" name="inventoryNum['+num+']" id="ruku_num_'+inventoryId+'" onfocus="showTips(this,\''+max+'\');" max="'+max+'" value="'+shuliang+'" onchange="zong_chengben('+inventoryId+')" class="sprukuadd_03_tt_input">'+
							'<input type="hidden" name="inventoryId['+num+']" value="'+inventoryId+'">'+
				// 			'<input type="hidden" name="inventorySn['+num+']" value="'+sn+'">'+
				// 			'<input type="hidden" name="inventoryTitle['+num+']" value="'+title+'">'+
				// 			'<input type="hidden" name="inventoryKey_vals['+num+']" value="'+key_vals+'">'+
				// // 			'<input type="hidden" name="inventoryBeizhu['+num+']" id="beizhu'+num+'" value="">'+
				// 			'<input type="hidden" name="inventoryPdtId['+num+']" value="'+productId+'">'+
				// 			'<input type="hidden" name="inventoryUnits['+num+']" value="'+units+'">'+
						'</td>'+
						'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>'+
						'</tr>';
						$("#dataTable").append(str);
			    	}
			    	$("#dataTable").attr("rows",num);
			    	hideSearch();
			    	addRow();
			    }else{
			    	hideSearch();
			    }
		  	}
		  }
		  productListForm = form;
		  laydate.render({
		  	elem: '#dtTime'
		  	,max:'<?=date("Y-m-d H:i:s")?>'
  			,value:'<?=date("Y-m-d H:i")?>'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm'
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-250"
		    ,url: '?m=system&s=product&a=getpdts'
		    ,page: true
            ,where: {
                storeId:<?=$storeId?>
            }
		    ,cols: [[{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:"display:none;"},{field: 'productId', title: 'productId', width:0,style:"display:none;"},{field:'sn',title:'商品编码',width:200},{field:'title',title:'商品名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'shuliang',title:'数量',width:200},{field:'units',title:'单位',width:100}]]
		    ,done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='productId']").hide();
          form.verify({
            kucun:function(value,item){
                if(value<=0){
                    return '入库数量不能小于0';
                }
            }
          });
		  form.on('submit(tijiao)', function(data){
			layer.load();
		  });
		  $("#sprkadd_xuanzesp_03_01").on("click", function(){
		    active['appendCheckData'].call(this);
		  });
          table.on('checkbox(product_list)',function(obj){
            if(typeof(obj.data)=='undefined'){
                if(obj.checked){
                    $("#product_list").next().find(".sprkadd_xuanzesp_02_tt_input").removeClass('disabled').removeAttr('readonly');
                }else{
                    $("#product_list").next().find(".sprkadd_xuanzesp_02_tt_input").addClass('disabled').prop('readonly',true);
                }
            }else{
                var pdtId = obj.data.id;
                if(obj.data.LAY_CHECKED){
                    $("#shuliang_"+pdtId).removeClass('disabled').removeAttr('readonly');
                }else{
                    $("#shuliang_"+pdtId).addClass('disabled').prop('readonly',true);
                }
            }
          });
		});
	</script>
	<script type="text/javascript" src="js/card_product.js?v=1.1"></script>
    <? require('views/help.html');?>
</body>
</html>