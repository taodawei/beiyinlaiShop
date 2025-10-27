<?
global $db,$request,$adminRole,$db,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"image"=>array("title"=>"商品图片","rowCode"=>"{field:'image',title:'商品图片',width:87,unresize:true}"),
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:150,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"untis"=>array("title"=>"单位","rowCode"=>"{field:'units',title:'单位',width:100}"),
				"price_sale"=>array("title"=>"零售价","rowCode"=>"{field:'price_sale',title:'零售价',width:100,sort:true}"),
				"price_market"=>array("title"=>"市场价","rowCode"=>"{field:'price_market',title:'市场价',width:100,sort:true}"),
				// "price_cost"=>array("title"=>"成本(供货)价","rowCode"=>"{field:'price_cost',title:'成本(供货)价',width:120,sort:true}"),
				// "baozhiqi"=>array("title"=>"保质期(最近)","rowCode"=>"{field:'baozhiqi',title:'保质期(最近)',width:180,sort:true}"),
				"brand"=>array("title"=>"研究领域","rowCode"=>"{field:'brand',title:'研究领域',width:150}"),
				"kucun"=>array("title"=>"库存数量","rowCode"=>"{field:'kucun',title:'库存数量',width:100}"),
				// "kuncun_cost"=>array("title"=>"库存成本","rowCode"=>"{field:'kuncun_cost',title:'库存成本',width:100,sort:true}"),
				// "mendianTitle"=>array("title"=>"供应商","rowCode"=>"{field:'mendianTitle',title:'供应商',width:100}"),
				"channel"=>array("title"=>"所属分类","rowCode"=>"{field:'channel',title:'所属分类',width:150}"),
				"ordering"=>array("title"=>"排序","rowCode"=>"{field:'ordering',title:'排序',width:80,sort:true}"),
				"dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:150,sort:true}"),
				"updateTime"=>array("title"=>"修改时间","rowCode"=>"{field:'updateTime',title:'修改时间',width:150,sort:true}"),
				"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:87}")
			);
// if(is_file("../cache/product_set_$comId.php")){
// 	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
// }else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
// }
if(empty($product_set)){
	$showRowsArry = array("image"=>1,"sn"=>1,"title"=>1,"key_vals"=>1,"untis"=>1,"price_market"=>1,"price_cost"=>1,"baozhiqi"=>1,"brand"=>1,"kucun"=>1,"kuncun_cost"=>1,"status"=>1);
	$showRows = json_encode($showRowsArry,JSON_UNESCAPED_UNICODE);
	$db->query("insert into demo_product_set(comId,if_image,showRows) value($comId,0,'$showRows')");
}else{
	$showRowsArry = json_decode($product_set->showRows,true);
	if(empty($showRowsArry))$showRowsArry = array("image"=>0,"sn"=>1,"title"=>1,"key_vals"=>1,"untis"=>1,"price_market"=>0,"price_cost"=>0,"brand"=>0,"kucun"=>1,"kuncun_cost"=>0,"status"=>1);
}
if($product_set->if_image==0)$showRowsArry['image']=0;
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($showRowsArry as $row=>$isshow){
	if($isshow==1){
		$rowsJS.=','.$allRows[$row]['rowCode'];
	}
}
$rowsJS .=",{fixed:'right',width:49,title:'<img src=\"images/biao_22.png\" onclick=\"showRowset();\">',align:'center', toolbar: '#barDemo'}";
$channelId = !empty($request['channelId'])?$request['channelId']:0;
$brandId = !empty($request['brandId'])?$request['brandId']:0;
$status = !empty($request['status'])?$request['status']:0;
$keyword = !empty($request['keyword'])?$request['keyword']:'';
$tags = !empty($request['tags'])?$request['tags']:'';
$cangkus = !empty($request['cangkus'])?$request['cangkus']:'';
$source = !empty($request['source'])?$request['source']:0;
$cuxiao = !empty($request['cuxiao'])?$request['cuxiao']:0;
$is_jifen = !empty($request['is_jifen'])?$request['is_jifen']: 1;

$order1 = empty($request['order1'])?'ordering':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['pdtPageNum'])?10:$_COOKIE['pdtPageNum'];
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$brands = $db->get_results("select id,title from demo_product_brand where comId=$comId order by ordering desc,id asc");
$tagsarry = array();
if(!empty($product_set->tags)){
	$tagsarry = explode('@_@',$product_set->tags);
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
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:73px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		.yuandian_xx{width:105px;}
		.cangkugl_xiugai_02_left{width:180px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_19.png"/> 商品列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01">
							<div class="splist_up_01_left_01_up">
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
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus(0,'全部状态');" class="splist_up_01_left_02_down_on">全部状态</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus(1,'上架');">上架</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus(-1,'下架');">下架</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_left_kucun layui-form">
                    	<input type="checkbox" name="hebing" lay-filter="hebing" lay-skin="primary" checked="true" title="将商品合并">
                    </div>
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编码/规格/关键字"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_2" style="display:none;">
							<div class="splist_up_01_right_2_up">
								高级搜索
							</div>
							<div class="splist_up_01_right_2_down">
								<div class="splist_up_01_right_2_down1">
									<div class="splist_up_01_right_2_down1_01">
										高级搜索
									</div>
									<div class="splist_up_01_right_2_down1_02">
										<form id="searchForm" class="layui-form">
											<ul>
												<li>
													<div class="gaojisousuo_left">
														关键词
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_keyword" class="gaojisousuo_right_input" placeholder="请输入商品名称/编码/规格/关键字/条形码"/>
													</div>
													<div class="gaojisousuo_left">
														商品分类
													</div>
													<div class="gaojisousuo_right">
														<div class="layui-form-select">
															<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="" class="layui-input"><i class="layui-edge"></i></div>
															<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
														</div>
														<input type="hidden" name="super_channel" id="super_channel">
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品研究领域
													</div>
													<div class="gaojisousuo_right">
														<select name="super_brand" id="super_brand" lay-search>
															<option value="">选择研究领域或输入搜索</option>
															<? if(!empty($brands)){
																foreach ($brands as $b) {
																	?>
																	<option value="<?=$b->id?>"><?=$b->title?></option>
																	<?
																}
															}?>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品标签
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_tags_all" lay-filter="tags" lay-skin="primary" lay-skin="primary" title="全选" checked/>
														<? if(!empty($tagsarry)){
															foreach ($tagsarry as $i=>$t) {
																?>
																<input type="checkbox" name="super_tags_<?=$i?>" pid="tags" lay-filter="notags" lay-skin="primary" lay-skin="primary" title="<?=$t?>" value="<?=$t?>" />
																<?
															}
														}?>
													</div>
													<div class="clearBoth"></div>
												</li>
												<!-- <li>
													<div class="gaojisousuo_left">
														所属仓库
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_cangkus_all" lay-skin="primary" lay-filter="cangkus" title="全选" checked />
														<input type="checkbox" name="super_cangkus" pid="cangkus" lay-skin="primary" lay-filter="nocangkus" value="仓库1" title="仓库1" />
														<input type="checkbox" name="super_cangkus" pid="cangkus" lay-skin="primary" lay-filter="nocangkus" value="仓库2" title="仓库2" />
													</div>
													<div class="clearBoth"></div>
												</li> -->
												<li>
													<div class="gaojisousuo_left">
														促销状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="super_cuxiao" value="0" checked title="全部"/><input type="radio" name="super_cuxiao" value="1" title="促销中" /><input type="radio" name="super_cuxiao" value="-1" title="正常"/> 
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="sunper_status" value="0" title="全部" checked /><input type="radio" name="sunper_status" value="1" title="上架" /><input type="radio" name="sunper_status" value="-1" title="下架"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品来源
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="sunper_source" value="0" title="全部" checked/><input type="radio" name="sunper_source" value="1" title="手动新增"/><input type="radio" name="sunper_source" value="2" title="批量导入"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_tijiao">
														<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search" > 确 定 </button>
														<button type="layui-btn" lay-submit="" class="layui-btn layui-btn-primary" lay-filter="quxiao"> 取 消 </button>
														<button type="reset" class="layui-btn layui-btn-primary"> 重 置 </button>
													</div>
												</li>
											</ul>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="splist_up_01_right_3">
						    <? chekurl($arr,'<a href="?m=system&s=product&a=daochu" id="daochuA" target="_blank" onclick="daochu();" class="splist_daochu">导 出</a>') ?>
						    <? chekurl($arr,'<a href="?m=system&s=product&a=daoru" class="splist_daoru">导 入</a>') ?>
						    
						    <!--<form id="forms" method="post" action="" style="display: inline-block;">-->
          <!--          		    <?if(strstr($arr, '?m=system&s=product&a=daorushuo')){ ?>-->
          <!--              		    <a href="javascript:;" id="uploadFile" class="splist_daoru"> 英文说明书</a>-->
          <!--              		    <a href="javascript:;" id="uploadFile1" class="splist_daoru"> 中文说明书</a>-->
          <!--              		<? } ?>-->
          <!--              	</form>-->
						    <? chekurl($arr,'<a href="?m=system&s=product&a=daorushuo2" class="splist_daoru">英文说明书</a>') ?>
						    <? chekurl($arr,'<a href="?m=system&s=product&a=daorushuo2&type=1" class="splist_daoru">中文说明书</a>') ?>
						    
						    <!--<div class="fhqr_ptkuaididaoru_up_4">-->
                        		<!--<a href="images/help.xls" target="_blank"> 下载示例文件</a>-->
                        		<form id="forms" method="post" action="" style="display: inline-block;">
                        		    <?if(strstr($arr, '?m=system&s=product&a=batchOnline')){ ?>
                            		    <a href="javascript:;" id="uploadFile" class="splist_daoru" > 批量下架</a>
                            		<? } ?>
                            	</form>
                            <!--</div>-->
						    
						    
						    <? chekurl($arr,'<a href="?m=system&s=product&a=create" class="splist_add">新 增</a>') ?>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
				<div class="splist_up_02">
					<div class="splist_up_02_1">
						<img src="images/biao_25.png"/>
					</div>
					<div class="splist_up_02_2">
						已选择 <span id="selectedNum">0</span> 项
					</div>
					<div class="splist_up_02_3">
					    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=shangjia" onclick="shangjia();"><img src="images/biao_26.png"/> 上架</a>') ?>
					    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=xiajia" onclick="xiajia();"><img src="images/biao_27.png"/> 下架</a>') ?>
						<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=delete" onclick="delAll();"><img src="images/biao_28.png"/> 删除</a>') ?>
						<? //chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=setTags" id="setTags" ><img src="images/biao_29.png"/> 设置标签</a>') ?>
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
					    <? //chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=view" onclick="view_product()"><img src="images/biao_30.png"> 详情</a></li>') ?>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=edit" onclick="edit_product()"><img src="images/biao_31.png"> 编辑</a></li>') ?>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=editProduct" onclick="edit_all()"><img src="images/biao_31.png"> 编辑所有规格</a></li>') ?>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=setorders" onclick="edit_orders()"><img src="images/biao_31.png"> 虚拟销量</a></li>') ?>
						
						
						<li><a href="javascript:edit_product_price();"><img src="images/biao_31.png"> 修改库存</a></li>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=setParam" onclick="setParam()"><img src="images/biao_134.png">设置参数</a></li>') ?>
						
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=setBook" onclick="setBook()"><img src="images/biao_129.png">设置说明书</a></li>') ?>
						
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=delPdf" onclick="z_confirm(\'确定要清除该产品的PDF说明书吗？\',del_pdf,\'\');"><img src="images/biao_127.png">清除说明书</a></li>') ?>
						
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=delete" onclick="z_confirm(\'确定要删除该产品吗？\',del_product,\'\');"><img src="images/biao_32.png"> 删除</a></li>') ?>
					</ul>
				</div>
				<div class="xianshiziduan" id="xianshiziduan">
					<div class="xianshiziduan1">                        
						<div class="xianshiziduan_1">
							选择显示字段
						</div>
						<form action="?m=system&s=product&a=rowsSet" method="POST" id="rowsSetForm" class="layui-form">
							<div class="xianshiziduan_2">
								<ul>
									<?
									$i=0;
									foreach ($allRows as $field=>$row) {
										$i++;
										?>
										<li>
											<input type="checkbox" name="rowsSet[<?=$field?>]" lay-skin="primary" title="<?=$row['title']?>" <? if($field=='title'){?>disabled<?}if($showRowsArry[$field]==1){?> checked<? }?> />
											<? if($i>4){?>
											<span class="rowtodown" onclick="rowToDown(this);"><img src="images/biao_34.png"/></span><span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>
											<? }else if($i==4){?>
											<span class="rowtodown" onclick="rowToDown(this);"><img src="images/biao_34.png"/></span>
											<? }?>
										</li>
										<?
									}
									?>
								</ul>
							</div>
							<div class="xianshiziduan_3">
								<a href="javascript:" onclick="$('#rowsSetForm').submit();" class="xianshiziduan_3_01">确定</a><a href="javascript:" onclick="hideRowset();" class="xianshiziduan_3_02">取消</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="cangkugl_xiugai" id="baozhiqi_xiugai">
    	<div class="cangkugl_xiugai_01">
        	保质期修改
        </div>
        <form id="editForm" action="javascript:" method="post" class="layui-form">
        	<input type="hidden" name="id" id="storeId">
        	<div class="cangkugl_xiugai_02">
        		<ul>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 保质期(最早到期时间)
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_baozhiqi" name="baozhiqi" readonly="true" lay-verify="required" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 距到期多少天提醒
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_days" name="baozhiqi_days" lay-verify="required|number" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 应用范围
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<select name="fanwei" id="e_fanwei">
        						<option value="1">只修改该规格</option>
        						<option value="2">修改该商品所有规格</option>
        					</select>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        	</div>
        	<div class="cangkugl_xiugai_03">
        		<button class="layui-btn" lay-submit="" lay-filter="tijiao">提 交</button>
        		<button class="layui-btn layui-btn-primary" onclick="$('#baozhiqi_xiugai').hide();">取 消</button>
        	</div>
        </form>
    </div>
    <div class="cangkugl_xiugai" id="orders_xiugai">
    	<div class="cangkugl_xiugai_01">
        	修改商品销量
        </div>
        <form id="orderForm" action="javascript:" method="post" class="layui-form">
        	<input type="hidden" name="id" id="storeId">
        	<div class="cangkugl_xiugai_02">
        		<ul>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 销量
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_orders" name="orders" lay-verify="required|number" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        		</ul>
        	</div>
        	<div class="cangkugl_xiugai_03">
        		<button class="layui-btn" lay-submit="" lay-filter="tijiao1">提 交</button>
        		<button class="layui-btn layui-btn-primary" onclick="$('#orders_xiugai').hide();">取 消</button>
        	</div>
        </form>
    </div>
    <div class="cangkugl_xiugai" id="orders_xiugai1">
    	<div class="cangkugl_xiugai_01">
        	修改商品库存
        </div>
        <form id="orderForm1" action="javascript:" method="post" class="layui-form">
        	<input type="hidden" name="id" id="storeId">
        	<div class="cangkugl_xiugai_02">
        		<ul>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 库存
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_views" name="views" lay-verify="required|number" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        		</ul>
        	</div>
        	<div class="cangkugl_xiugai_03">
        		<button class="layui-btn" lay-submit="" lay-filter="tijiao2">提 交</button>
        		<button class="layui-btn layui-btn-primary" onclick="$('#orders_xiugai1').hide();">取 消</button>
        	</div>
        </form>
    </div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="brandId" value="<?=$brandId?>">
	<input type="hidden" id="s_status" value="<?=$status?>">
	<input type="hidden" id="tags" value="<?=$tags?>">
	<input type="hidden" id="cangkus" value="<?=$cangkus?>">
	<input type="hidden" id="source" value="<?=$source?>">
	<input type="hidden" id="cuxiao" value="<?=$cuxiao?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="hebing" value="<?=$is_jifen?>">
    <input type="hidden" id="is_jifen" value="1">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laydate', 'laypage','table','form', 'upload'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  ,active = {
		  	setTags:function(){
				layer.open({
					type: 1
					,title: false
					,closeBtn: false
					,area: '530px;'
					,shade: 0.3
					,id: 'LAY_layuipro'
					,btn: ['提交', '取消']
					,yes: function(index, layero){
						return false;
					}
					,btnAlign: 'r'
					,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
					'<form action="#" class="layui-form" method="post" id="setTagsForm"><div class="spxx_shanchu_tanchu_01">'+
					'<div class="spxx_shanchu_tanchu_01_left">'+'设置商品标签'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_01_right">'+
					'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
					'</div>'+
					'<div class="clearBoth"></div>'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_02">'+
					'<div class="jiliang_tanchu">'+
					'<div style="float:left;width:80px;line-height:42px;">设置标签：</div><div style="float:left;width:350px;line-height:40px;">'+
					<? if(!empty($tagsarry)){
						foreach ($tagsarry as $tag){
						echo '\'<input type="checkbox" name="piliang_tags" lay-skin="primary" title="'.$tag.'" value="'.$tag.'" />\'+';	
						}
					}?>
					'</div>'+
					'</div>'+
					'</form></div>'
					,success: function(layero){
						form.render('checkbox');
						var btn = layero.find('.layui-layer-btn');
						btn.find('.layui-layer-btn0').attr({
							href: 'javascript:setAllTags();'
						});
						return false;
					}
				});
			}
		  }
		  laydate.render({elem: '#e_baozhiqi'});
		  form.on('submit(tijiao)',function(data){
		  	var pdtId = getPdtId();
		  	var baozhiqi = $("#e_baozhiqi").val();
		  	var baozhiqi_days = $("#e_days").val();
		  	var fanwei = $("#e_fanwei option:selected").val();
		  	layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=setBaozhiqi",
				data: "&id="+pdtId+"&baozhiqi="+baozhiqi+"&baozhiqi_days="+baozhiqi_days+"&fanwei="+fanwei,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					if(resdata.code==1){
						layer.closeAll('loading');
						$("#baozhiqi_xiugai").hide();
						reloadTable(1);
						layer.msg('操作成功');
					}else{
						layer.closeAll('loading');
						layer.msg(resdata.message, {icon: 5});
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		  	return false;
		  });
		  form.on('submit(tijiao1)',function(data){
		  	var pdtId = getPdtId();
		  	var orders = $("#e_orders").val();
		  	layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=setorders",
				data: "id="+pdtId+"&orders="+orders,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					if(resdata.code==1){
						layer.closeAll('loading');
						$("#orders_xiugai").hide();
						reloadTable(1);
						layer.msg('操作成功');
					}else{
						layer.closeAll('loading');
						layer.msg(resdata.message, {icon: 5});
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		  	return false;
		  });
		  form.on('submit(tijiao2)',function(data){
		  	var pdtId = getPdtId();
		  	var num = $("#e_views").val();
		  	layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=add_kucun",
				data: "id="+pdtId+"&num="+num,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					if(resdata.code==1){
						layer.closeAll('loading');
						$("#orders_xiugai1").hide();
						//reloadTable(1);
						layer.msg('操作成功');
					}else{
						layer.closeAll('loading');
						layer.msg(resdata.message, {icon: 5});
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		  	return false;
		  });
		  form.on('checkbox(hebing)',function(data){
		  	if(data.elem.checked==false){
		  		$("#hebing").val(0);
		  	}else{
		  		$("#hebing").val(1);
		  	}
		  	reloadTable();
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=product&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	brandId:<?=$brandId?>,
		    	status:<?=$status?>,
		    	keyword:'<?=$keyword?>',
		    	tags:'<?=$tags?>',
		    	source:<?=$source?>,
		    	cuxiao:<?=$cuxiao?>,
		    	hebing:1
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  
		  	upload = layui.upload;
        	upload.render({
        	    elem: '#uploadFile'
        	    ,url: '?m=system&s=upload&a=uploadXls'
        	    ,accept: 'file'
            	,exts: 'xls|xlsx'
        	    ,before: function(obj){
        	      layer.load();
        	    }
        	    ,done: function(res){
        	      layer.closeAll('loading');
        	      //导入成功之后
        	      $.ajax({
        			type:"post",
        			url:"?m=system&s=product&a=batchOnline&type=0",
        			data:"filepath="+res.url,
        			timeout:"60000",
        			dataType:"json",
        			async:false,
        			success: function(data){
        				// reloadTable(0);
						layer.msg(data.content);
        				//window.location.reload();
        			},
        			error:function(){
        			    
        	           // alert("超时,请刷新");
        	        }
        
        	    });
        	      //导入成功之后
        	    }
        	    ,error: function(){
        	      layer.closeAll('loading');
        	      layer.msg('上传失败，请重试', {icon: 5});
        	    }
        	});
        	
        	upload.render({
        	    elem: '#uploadFile1'
        	    ,url: '?m=system&s=upload&a=uploadXls'
        	    ,accept: 'file'
            	,exts: 'xls|xlsx'
        	    ,before: function(obj){
        	      layer.load();
        	    }
        	    ,done: function(res){
        	      layer.closeAll('loading');
        	      //导入成功之后
        	      $.ajax({
        			type:"post",
        			url:"?m=system&s=product&a=daorushuo1&type=1",
        			data:"filepath="+res.url,
        			timeout:"60000",
        			dataType:"json",
        			async:false,
        			success: function(data){
        				// reloadTable(0);
						layer.msg(data.content);
        				//window.location.reload();
        			},
        			error:function(){
        			    
        	           // alert("超时,请刷新");
        	        }
        
        	    });
        	      //导入成功之后
        	    }
        	    ,error: function(){
        	      layer.closeAll('loading');
        	      layer.msg('上传失败，请重试', {icon: 5});
        	    }
        	});
		  
		  $("th[data-field='id']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var brandId = $("#brandId").val();
		  	var status = $("#s_status").val();
		  	var keyword = $("#keyword").val();
		  	var tags = $("#tags").val();
		  	var source = $("#source").val();
		  	var cuxiao = $("#cuxiao").val();
		  	var hebing = $("#hebing").val();
		  	var is_jifen = $("#is_jifen").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,channelId:channelId
			      ,brandId:brandId
			      ,status:status
			      ,keyword:keyword
			      ,tags:tags
			      ,source:source
			      ,cuxiao:cuxiao
			      ,hebing:hebing
                  ,is_jifen:is_jifen
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='id']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		  form.on('checkbox(tags)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='tags']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(notags)', function(data){
		  	$("input[name='super_tags_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('checkbox(cangkus)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='cangkus']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(nocangkus)', function(data){
		  	$("input[name='super_cangkus_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('submit(search)', function(data){
		  	$("#keyword").val(data.field.super_keyword);
		  	$("#channelId").val(data.field.super_channel);
		  	$("#brandId").val(data.field.super_brand);
		  	if(data.field.super_tags_all=="on"){
		  		$("#tags").val('');
		  	}else{
		  		var tagstr = '';
		  		if(typeof(data.field.super_tags_0)!='undefined'){
		  			tagstr=','+data.field.super_tags_0;
		  		}
		  		if(typeof(data.field.super_tags_1)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_1;
		  		}
		  		if(typeof(data.field.super_tags_2)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_2;
		  		}
		  		if(typeof(data.field.super_tags_3)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_3;
		  		}
		  		if(typeof(data.field.super_tags_4)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_4;
		  		}
		  		if(tagstr.length>0){
		  			tagstr = tagstr.substring(1);
		  		}
		  		$("#tags").val(tagstr);
		  	}
		  	if(data.field.super_cangkus_all=="on"){
		  		$("#cangkus").val('');
		  	}else{
		  		var cangkustr = '';
		  		$("input:checkbox[name='super_cangkus']:checked").each(function(){
		  			cangkustr = cangkustr+','+$(this).val();
		  		});
		  		if(cangkustr.length>0){
		  			cangkustr = cangkustr.substring(1);
		  		}
		  		$("#cangkus").val(cangkustr);
		  	}
		  	$("#cuxiao").val(data.field.super_cuxiao);
		  	$("#status").val(data.field.sunper_status);
		  	$("#source").val(data.field.sunper_source);
		  	hideSearch();
		  	reloadTable(0);
		  	return false;
		  });
		  form.on('submit(quxiao)', function(){
		  	hideSearch();
		  	return false;
		  });
		  table.on('checkbox(product_list)', function(obj){
			var checkStatus = table.checkStatus('product_list')
		    ,data = checkStatus.data;
		    if(data.length>0){
		    	var ids = '';
		    	for (var i = 0; i < data.length; i++) {
		    		if(i==0){
		    			ids = data[i].id;
		    		}else{
		    			ids = ids+','+data[i].id;
		    		}
		    	}
		    	$("#selectedIds").val(ids);
		    	$(".splist_up_01").hide();
		    	$(".splist_up_02").show().find(".splist_up_02_2 span").html(data.length);
		    }else{
		    	$(".splist_up_02").hide();
		    	$(".splist_up_01").show();
		    }
		  });
		  $("#setTags").click(function(){
		  	active['setTags'].call(this);
		  });
		  ajaxpost=$.ajax({
		  	type: "POST",
		  	url: "/erp_service.php?action=get_product_channels1",
		  	data: "",
		  	dataType:"text",timeout : 20000,
		  	success: function(resdata){
		  		$("#selectChannels").append(resdata);
		  	},
		  	error: function() {
		  		layer.msg('数据请求失败', {icon: 5});
		  	}
		  });
		  $("#selectChannel").click(function(){
		  	$(this).parent().toggleClass('layui-form-selected');
		  });
		});
		
		function setBook(params){
		    if(params>0){
        		pdtId = params;
        	}else{
        		pdtId = getPdtId();
        	}
        	var channelId = $("#channelId").val();
        	var brandId = $("#brandId").val();
        	var status = $("#s_status").val();
        	var keyword = $("#keyword").val();
        	var tags = $("#tags").val();
        	var source = $("#source").val();
        	var cuxiao = $("#cuxiao").val();
        	var page = $("#page").val();
        	var order1 = $("#order1").val();
        	var order2 = $("#order2").val();
        	var url = '?m=system&s=product&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
        	url = encodeURIComponent(url);
        	
        	location.href="?m=system&s=product&a=setBook&id="+pdtId+"&url="+url;
		}
		
	    function setParam(params)
        {
            if(params>0){
        		pdtId = params;
        	}else{
        		pdtId = getPdtId();
        	}
        	var channelId = $("#channelId").val();
        	var brandId = $("#brandId").val();
        	var status = $("#s_status").val();
        	var keyword = $("#keyword").val();
        	var tags = $("#tags").val();
        	var source = $("#source").val();
        	var cuxiao = $("#cuxiao").val();
        	var page = $("#page").val();
        	var order1 = $("#order1").val();
        	var order2 = $("#order2").val();
        	var url = '?m=system&s=product&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
        	url = encodeURIComponent(url);
        	
        	location.href="?m=system&s=product&a=setParam&id="+pdtId+"&url="+url;
        }
        
		function del_pdf(params){
        	var pdtId = getPdtId();
        	layer.load();
        	ajaxpost=$.ajax({
        		type: "POST",
        		url: "?m=system&s=product&a=delPdf",
        		data: "&ids="+pdtId,
        		dataType:"json",timeout : 8000,
        		success: function(resdata){
        			if(resdata.code==1){
        				layer.closeAll('loading');
        				layer.msg('操作成功');
        				reloadTable(1);
        			}else{
        				layer.closeAll('loading');
        				layer.msg(resdata.message, {icon: 5});
        			}
        		},
        		error: function() {
        			layer.closeAll();
        			layer.msg('数据请求失败', {icon: 5});
        		}
        	});
        }
		
	</script>
	<script type="text/javascript" src="js/product_list.js?v=1.2"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>