<?
global $db,$request;
$id = (int)$request['id'];

$banner = null;
$customArr = [];

if($id > 0){
    $banner = $db->get_row("select * from demo_skill where id = $id and is_del = 0 ");
    $ruleJson = $banner->file_info;
}

if($ruleJson){
    $customArr = json_decode($ruleJson, JSON_UNESCAPED_UNICODE);
}
$channels = $db->get_results("select id,title from demo_skill_channel where comId=$comId");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spgl.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />

	<link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
	  
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/keditor/kindeditor1.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.all.js"></script>	
	<script type="text/javascript">
		var $unitOptions = '<?=$unitOptions?>';
		var lipinka_str = '<?=$lipinka_str?>';
		var channelId = <?=$product->channelId?>;
		var unit_type = <?=$product->unit_type?>;
		var step = <?=$step?>;
		var step1 = <?=$step1?>;
	</script>
	<style type="text/css">
		.edit_guige .layui-form-select,#moreGuige .layui-form-select{width:80%;margin:0px auto;}
		.guige_set table tr td .layui-select-title input{width:100%;margin:0px auto;height:32px;}
	</style>
</head>
<body>
	<form action="?m=system&s=skill&a=add&submit=1&type=<?=$type?>" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<input type="hidden" name="url" value="<?=$url?>">
		<input type="hidden" name="id" value="<?=$id?>">
		<div class="content_edit">
			<div class="edit_h">
				<a href="?m=system&s=skill"><img src="images/back.jpg" /></a>
				<span>添加服务</span>
			</div>
			<br>
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 服务标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$banner->title?>" lay-verify="required" placeholder="请输入服务标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 服务分类
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="channelId">
                                <option value="0" <? if($banner->channelId==0){?>selected="true"<? }?>>请选择服务分类</option>
                                <?
                                    if(!empty($channels)){
                                        foreach ($channels as $channel) {
                                            ?>
                                            <option value="<?=$channel->id?>" <? if($banner->channelId==$channel->id){?>selected="true"<? }?>><?=$channel->title?></option>
                                            <?
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 语言选择
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="language" value="0" title="中文" <? if(empty($banner->language)){?>checked="checked"<? }?>>
                            <input type="radio" name="language" value="1" title="英文" <? if($banner->language==1){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 是否展示
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="status" value="0" title="隐藏" <? if(empty($banner->status)){?>checked="checked"<? }?>>
                            <input type="radio" name="status" value="1" title="展示" <? if($banner->status==1 || empty($banner)){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 流程样式
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="process_type" value="1" title="折线型" <? if($banner->process_type == 1){?>checked="checked"<? }?>>
                            <input type="radio" name="process_type" value="2" title="回字型" <? if($banner->process_type == 2){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                     <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span></span> 排序
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="number" step="1" min="0" name="ordering" value="<?=$banner->ordering?>" lay-verify="required" placeholder="数字排序" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                </ul>
            </div>
				
		<div class="edit_guige">
           	<div class="jichu_h">附件链接</div>
            <div class="">
                <table class="layui-table" id="serviceTables">
                    <colgroup>
                        <col width="30%">
                        <col width="50%">
                        <col width="20%">
                    </colgroup>
           
                        <tr>
                            <th>
                                附件名称
                            </th>
                            <th><span>附件下载链接</span><span class="layui-table-sort layui-inline" style="margin-top: 5px"></th>
                            <th>操作</th>
                        </tr>
                        
                    	<? if(!empty($customArr)){
							foreach ($customArr as $ck => $custom){
								?>
								<tr>
                                    <td>
                                        <input type="text" name="addRowKey[]" lay-verify="required" value="<?=$ck?>" autocomplete="off" class="layui-input">
                                    </td>
                                   <td>
                                       <input type="text" name="addRowValue[]"  lay-verify="required"  value="<?=$custom?>" autocomplete="off" class="layui-input">
                                   </td>
                                    <td>
                                        <button type="button" class="layui-btn layui-btn-xs cancelBtn"><i class="layui-icon">删除</i></button>
                                    </td>
                                </tr>
						<?
							}
						}?>
              
                </table>
                <button id="newBtn" type="button" class="layui-btn layui-btn-primary layui-btn-fluid"><i class="layui-icon"></i> 新增自定义</button><br>
            </div>
        </div>
        
        
    <script type="text/javascript">
    layui.use(['upload','form','layer'], function(){
          var $ = layui.jquery
          ,upload = layui.upload
          ,form = layui.form;
          
        // 新增服务项目
        $("#newBtn").click(function() {
            var str = '';
            str += '<tr>';
            str += '<td>';
            str += '<input type="text" name="addRowKey[]" lay-verify="required" placeholder="请输入附件名称" autocomplete="off"';
            str += 'class="layui-input">';
            // str += '<?=$yhqSelect?>';
            str += '</td>';
            str += '<td><input type="text" name="addRowValue[]"  lay-verify="required"  placeholder="请输入附件下载链接" autocomplete="off"';
             str += 'class="layui-input"></td>';                                                    
            str += '<td><button type="button" class="layui-btn layui-btn-xs cancelBtn"><i class="layui-icon">删除</i></button></td>';
            str += '</tr>';        
            
            $("#serviceTables").append(str);
            
            form.render();
        });
        // 确定
        $("#serviceTables").on("click",".qrBtn",function(){
            $("#serviceTables tr:last").remove();
            var newStr = '';
            var serviceName = $(this).parent().parent().find("td:nth-child(1)").find("input").val();
            var servicePrice = $(this).parent().parent().find("td:nth-child(2)").find("input").val();
    
            newStr += '<tr>';
            newStr += '<td>';
            newStr += serviceName;
            newStr += '</td>';
            newStr += '<td>' + servicePrice + '</td>';
            newStr += '<td><a href="#" class="co-green">删除</a></td>';
            newStr += '</tr>';
    
            $("#serviceTables").append(newStr);
        });
    
        $("#serviceTables").on("click", ".cancelBtn", function () {
                var thinLine = $(this).parents('tr');
                thinLine.each(function (i) {
                    // var id = $(this).find("input[name='id']").val();
                    var reg = /编辑/;// 遍历 tr
                    $(this).children('td').each(function (j) {  // 遍历 tr 的各个 td
                        
                        var tdDom = $(this);
                        tdDom.parent().remove();
                    });
                });
            });    
    
        // 编辑
        $("#serviceTables").on("click", ".bjBtn", function () {
            var thinLine = $(this).parents('tr');
            thinLine.each(function (i) {
                // var id = $(this).find("input[name='id']").val();
                var reg = /编辑/;// 遍历 tr
                $(this).children('td').each(function (j) {  // 遍历 tr 的各个 td
                    // alert("第"+(i+1)+"行，第"+(j+1)+"个td的值："+$(this).text()+"。");
    
                    var tdDom = $(this);
                    //保存初始值
                    var tdPreText = $(this).text();
                    //给td设置宽度和给input设置宽度并赋值
                    if (reg.test(tdPreText)) {
                        var newBtns = '<button type="button"  class="layui-btn layui-btn-xs qrBtn"><i class="layui-icon">确定</i></button>';
                        newBtns += '<button type="button" class="layui-btn layui-btn-xs cancelBtn"><i class="layui-icon">取消</i></button>';
                        $(this).html(newBtns);
                    } else {
                        $(this).html("<input type='text'>").find("input").addClass('layui-input').val(tdPreText);
                    }
    
                });
            });
        });    


    });


</script>
				<div class="edit_miaoshu">
					<div class="miaoshu_fenlei" id="pdtcontMenu">
						<ul>
							<li><a href="javascript:" id="pdtcontMenu1" onclick="qiehuan('pdtcont',1,'on');" class="on">服务描述</a></li>
							<li><a href="javascript:" id="pdtcontMenu2" onclick="qiehuan('pdtcont',2,'on');" >解决方案</a></li>
						</ul>
					</div>
					<div class="miaoshu_edit pdtcontCont" id="pdtcontCont1">
						<?php
						ewebeditor(EDITORSTYLE,'content',empty($banner->content)?$banner->content:$banner->content);
						?>
					</div>
					
					<div class="miaoshu_edit pdtcontCont" id="pdtcontCont2"  style="display:none;">
						<?php
						ewebeditor(EDITORSTYLE,'solution',empty($banner->solution)?$banner->solution:$banner->solution);
						?>
					</div>
					
				</div>
				<div class="edit_jiage">
					<? if($product_set->if_dinghuo==1){?>
					<div class="jiage_tt">
						<div class="jiage_h">
							订货价格设置
						</div>
						<div class="jiage_shuoming">
							说明：价格及起订量/限订量均按最小单位设置，价格及数量的精度可在设置中<a href="index.php?url=<?=urlencode('?m=system&s=product_set')?>" target="_blank">配置</a>
						</div>
					</div>
					<div class="jiebie_check">
						<input type="checkbox" name="dinghuo_bylevel" lay-skin="primary" checked="true" disabled title="按<?=$kehu_title?>级别定价" />
					</div>
					<div class="add_gsj">
						<div class="add_gsj_left">
							<a>按折扣一键设置订货价</a>
						</div>
						<div class="add_gsj_right">
							<span style="color:red">*</span> 市场价：<input type="number" step="0.01" id="shichangjia" name="shichangjia" lay-verify="required" value="<?=$product_inventory->shichangjia?>" placeholder=""/> <span>订货价=市场价 * 级别折扣 </span>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div id="dinghuo_dansn">
						<div class="jiebie_table">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<th width="394">客户级别</th>
									<th width="127">默认折扣</th>
									<th width="127">允许订货</th>
									<th width="178">订货价</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>起订量</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>限订量</th>
								</tr>
								<? foreach($levels as $level){
									$dinghuo = $db->get_row("select * from demo_product_dinghuo where inventoryId=$id and levelId=$level->id limit 1");
									$dinghuo->price_sale = getXiaoshu($dinghuo->price_sale,$product_set->price_num);
									$dinghuo->dinghuo_min = getXiaoshu($dinghuo->dinghuo_min,$product_set->number_num);
									$dinghuo->dinghuo_max = getXiaoshu($dinghuo->dinghuo_max,$product_set->number_num);
									?>
									<tr height="48">
										<td><?=$level->title?></td>
										<td><?=$level->zhekou?>%</td>
										<td width="127">
											<input name="d_ifsale_0[<?=$level->id?>]" class="checkbox" type="checkbox" lay-skin="primary" <? if($dinghuo->ifsale==1){?>checked="true"<? }?> title="" lay-filter="ifsale"/>
										</td>
										<td><input type="number" step="<?=$step?>" mustrow value="<?=$dinghuo->price_sale?>" name="d_price_sale0[<?=$level->id?>]" data-zhekou="<?=$level->zhekou?>" min="0" style="width:102px;" <? if($dinghuo->ifsale==0){?>readonly="true" class="disabled dinghuo_money"<? }else{?>class="dinghuo_money"<? }?>/></td>
										<td <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>><input type="number" step="<?=$step1?>" value="<?=$dinghuo->dinghuo_min?>" name="dinghuo_min0[<?=$level->id?>]" min="0" style="width:102px;" <? if($dinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,1);"/></td>
										<td <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>><input type="number" step="<?=$step1?>" value="<?=$dinghuo->dinghuo_max?>" name="dinghuo_max0[<?=$level->id?>]" onmouseover="tips(this,'0或空代表不限制',1)" onmouseout="hideTips();" min="0" style="width:102px;" <? if($dinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,2);"/></td>
									</tr>
									<?
								}?>
							</table>
						</div>
					</div>
					<div class="jiage_kehu">
						<input class="checkbox" name="dinghuo_bykehu" lay-skin="primary" type="checkbox" lay-filter="dinghuo_bykehu" title="按客户定价" <? if(!empty($kehuDinghuos)){?>checked="true"<? }?> />
						<div class="khjg_table" id="khjg_table_dan" <? if(empty($kehuDinghuos)){?>style="display:none;"<? }?>>
							<table width="100%" id="dataTable" rows="1">
								<tbody><tr>
									<th width="102"></th>
									<th width="285">客户名称</th>
									<th width="128">客户级别</th>
									<th width="129">允许订货</th>
									<th width="178">订货价</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>起订量</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>限订量</th>
								</tr>
								<? 
								$row=1;
								if(!empty($kehuDinghuos)){
									foreach ($kehuDinghuos as $kehuDinghuo) {
										$kehuDinghuo->price_sale = getXiaoshu($kehuDinghuo->price_sale,$product_set->price_num);
										$kehuDinghuo->dinghuo_min = getXiaoshu($kehuDinghuo->dinghuo_min,$product_set->number_num);
										$kehuDinghuo->dinghuo_max = getXiaoshu($kehuDinghuo->dinghuo_max,$product_set->number_num);
										$k = $db->get_row("select title,level from demo_kehu where id=$kehuDinghuo->kehuId and comId=$comId limit 1");
										if(!empty($k)){
											$level = $db->get_var("select title from demo_kehu_level where id=".$k->level);
											?>
											<tr id="rowTr<?=$row?>">
												<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle">
													<div style="width:95px;">
														<div class="kehu_set1">
															<a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a>
														</div>
														<div class="kehu_set2">
															<a href="javascript:" onclick="delRow(<?=$row?>);"><img src="images/reduce.png"></a>
														</div>
													</div>
												</td>
												<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle"><?=$k->title?></td>
												<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle"><?=$level?></td>
												<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">
													<input name="k_ifsale_0[<?=$row?>]" class="checkbox" type="checkbox" lay-skin="primary" <? if($kehuDinghuo->ifsale==1){?>checked="true"<? }?> title="" lay-filter="ifsale">
												</td>
												<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">
													<input type="number" step="<?=$step?>" mustrow name="k_price_sale0[<?=$row?>]" min="0" style="width:102px;" value="<?=$kehuDinghuo->price_sale?>" <? if($kehuDinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?>>
													<input type="hidden" name="kehuId[<?=$row?>]" value="<?=$kehuDinghuo->kehuId?>">
													<input type="hidden" name="dinghuoId[<?=$row?>]" value="<?=$kehuDinghuo->id?>">
												</td>
												<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>
													<input type="number" step="<?=$step1?>" value="<?=$kehuDinghuo->dinghuo_min?>" name="k_dinghuo_min0[<?=$row?>]" min="0" style="width:102px;" <? if($kehuDinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,1);">
												</td>
												<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>
													<input type="number" step="<?=$step1?>" value="<?=$kehuDinghuo->dinghuo_max?>" name="k_dinghuo_max0[<?=$row?>]" onmouseover="tips(this,'0或空代表不限制',1)" onmouseout="hideTips();" min="0" style="width:102px;" <? if($kehuDinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,2);">
												</td>
											</tr>
										<?
										}
										$row++;
									}
								}?>
								<tr id="rowTr<?=$row?>">
									<td>
										<div style="width:95px;">
											<div class="kehu_set1">
												<a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a>
											</div>
											<div class="kehu_set2">
												<a href="javascript:" onclick="delRow(<?=$row?>);"><img src="images/reduce.png"></a>
											</div>
										</div>
									</td>
									<td colspan="6">
										<div class="sprukuadd_03_tt_addsp">
											<div class="sprukuadd_03_tt_addsp_left">
												<input type="text" class="layui-input addRowtr" id="searchInput<?=$row?>" row="<?=$row?>" placeholder="输入<?=$kehu_title?>名称/编码/联系人/手机" >
											</div>
											<div class="sprukuadd_03_tt_addsp_right" onclick="showKehus(event,<?=$row?>);">
												●●●
											</div>
											<div class="clearBoth"></div>
											<div class="sprukuadd_03_tt_addsp_erji" id="pdtList<?=$row?>">
												<ul>
													<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
												</ul>
											</div>
										</div>
									</td>
								</tr>
							</tbody></table>
						</div>
					</div>
					<? }?>
					<div class="edit_save">
						<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
						<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
					</div>
				</div>
		</div>
		<input type="hidden" name="originalPic" value="<?=$product->originalPic?>" id="originalPic">
		<input type="hidden" name="unit_type" value="<?=$product->unit_type?>" id="unit_type" >
		<input type="hidden" name="units" id="units" value="<?=$pdtUnitstr1?>">
		<input type="hidden" name="dinghuo_units" id="dinghuo_units" value="<?=$product->dinghuo_units?>">
		<input type="hidden" name="productId" id="productId" value="<?=$productId?>">
		<input type="hidden" name="pdt_status" id="pdt_status" value="<?=$product->status?>">
	</form>
	<div id="addSndiv" data-id="0">
		<div class="spxx_shanchu_tanchu" style="display: block;">
			<div class="spxx_shanchu_tanchu_01">
				<div class="spxx_shanchu_tanchu_01_left">添加规格
				</div>
				<div class="spxx_shanchu_tanchu_01_right">
					<a href="javascript:closeAddSn();"><img src="images/biao_47.png"></a>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div class="spxx_shanchu_tanchu_02" style="padding-left:0px;padding-top:30px;">
				<div class="jiliang_tanchu">
					<input type="text" id="guigesInput" class="xla_k" style="width:450px;">
					<Br><span style="padding-left:17px;padding-top:5px;">多个规格用，分开</span>
				</div>
				<div class="spxx_shanchu_tanchu_03">
					<a href="javascript:" onclick="addSn();" class="spxx_shanchu_tanchu_03_2">确定</a><a href="javascript:" onclick="closeAddSn();" class="spxx_shanchu_tanchu_03_1">取消</a>
				</div>
			</div>
		</div>
	</div>
	<div id="bg"></div>
	<script type="text/javascript">
		var jishiqi;
		var kehu_title = '<?=$kehu_title?>';
		var dinghuoHtml = '';
		var productId = <?=0?>;
		$("#shichangjia").bind('input propertychange', function(){
			var val = parseFloat($(this).val());
			if(!isNaN(val)){
				$(".dinghuo_money").each(function(){
					var zhekou = parseFloat($(this).attr("data-zhekou"))/100;
					var price = parseInt(val*zhekou*100)/100;
					$(this).val(price);
				});
			}
		});
		$('#searchInput<?=$row?>').bind('input propertychange', function() {
			clearTimeout(jishiqi);
			var row = $(this).attr('row');
			var val = $(this).val();
			jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
		});
		$('#searchInput<?=$row?>').click(function(eve){
			var nowRow = $(this).attr("row");
			if($("#pdtList"+nowRow).css("display")=="none"){
				$("#pdtList"+nowRow).show();
				getPdtInfo(nowRow,$(this).val());
			}
			stopPropagation(eve);
		});
	</script>
	<!--<script type="text/javascript" src="js/vip_set.js"></script>-->
	<? require('views/help.html');?>
</body>
</html>