<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
if(is_file("../cache/product_set_$comId.php")){
  $product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
    $product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$step = 1;$price_xiaoshu = 100;
if($product_set->number_num>0){
    $chushu = pow(10,$product_set->number_num);
    $step = 1/$chushu;
}
if($product_set->price_num>0){
  $price_xiaoshu = pow(10,$product_set->price_num);
}
$channels = array();
if(is_file("../cache/channels_$comId.php")){
    $content = file_get_contents("../cache/channels_$comId.php");
    $channels = json_decode($content);
}
$areas = $db->get_results("select * from demo_area where parentId=0");
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
  <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
  <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript"  src="layui/layui.js"></script>
  <script type="text/javascript" src="js/common.js"></script>
  <style type="text/css">
  .sprukuadd_04 ul li{display:inline-block;width:523px;margin-right:20px;}
  .add_other,.add_pay{float:right}
  .add_other{padding:17px 15px 5px 0;font-size:13px;color:#6a6a6a;line-height:34px}
  .add_other div{padding-bottom:5px}
  #shouhuoDiv img,#fapiaoCont img{cursor:pointer;}
</style>
</head>
<body>
  <div class="right_up">
    <a href="javascript:history.go(-1);"><img src="images/back.gif"/></a> 新增退货单
 </div>
 <div class="right_down">
   <div class="sprukuadd">
    <form id="addForm" action="?m=system&s=tuihuo&a=add&tijiao=1" method="post" class="layui-form">
      <input type="hidden" name="kehuId" id="kehuId" value="0">
      <div class="dhd_adddinghuodan_1">
        <div class="dhd_adddinghuodan_1_left">
          <span>*</span> <?=$kehu_title?>：
        </div>
        <div class="dhd_adddinghuodan_1_right">
          <div class="dhd_adddinghuodan_1_right_01">
            <input type="text" class="layui-input" id="searchKehuInput" placeholder="输入<?=$kehu_title?>名称/编码/联系人/手机">
            <div class="sprukuadd_03_tt_addsp_erji" id="kehuList">
              <ul>
                <li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
              </ul>
            </div>
          </div>
          <div class="dhd_adddinghuodan_1_right_02">
            <span></span><span></span><span></span>
          </div>
          <div class="clearBoth"></div>
        </div>
        <div class="clearBoth"></div>
      </div>
      <div class="sprukuadd_03">
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
           <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
             数量
           </td>
           <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
             单价（元）
           </td>
           <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
             小计（元）
           </td>
           <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
             重量（<?=$product_set->weight?>）
           </td>
           <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
             备注
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
                 <input type="text" class="layui-input addRowtr" id="searchInput1" row="1" placeholder="输入编码/商品名称" >
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
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
          <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
        </tr>
        <tr height="48" id="rowTrHeji">
         <td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"> 
           合计
         </td>
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
         <td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle"> </td> 
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0</td>
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0.00</td>
         <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0.00<?=$product_set->weight?></td>
         <td></td>
       </tr>
       <tr> 
         <td class="add_td5" colspan="11">
          <div class="add_other" style="text-align:right;">
            <div>已经申请退货，请输入获批退款金额:
              <input name="xieshangMoney" id="xieshangMoney" value="0" lay-verify="required|number" type="text" style="width:100px;display:inline-block;" class="layui-input" onchange="$('#price_all').html($(this).val());">
              元
            </div>
            <div class="clearBoth"></div>
            应付金额：<span style="color:#ff0000; font-size:24px; line-height:24px;" id="price_all">0.00</span>
          </div>
        </td>
      </tr>
    </table>
    <script type="text/javascript">
      var jishiqi;
      var kehu_title = '<?=$kehu_title?>';
      $('#searchInput1').bind('input propertychange', function() {
        clearTimeout(jishiqi);
        var row = $(this).attr('row');
        var val = $(this).val();
        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
      });
      $('#searchInput1').click(function(eve){
        var kehuId = $("#kehuId").val();
        if(kehuId==''||kehuId==0){
         layer.msg('请先选择'+kehu_title+'！',function(){});
         return false
       }
       var nowRow = $(this).attr("row");
       if($("#pdtList"+nowRow).css("display")=="none"){
         showpdtList(nowRow,$(this).val());
       }
       stopPropagation(eve);
     });
   </script>
 </div>
 <div class="dhd_adddinghuodan_3">
   <ul>
    <li>
      <div class="dhd_adddinghuodan_3_left">
        退款信息：
      </div>
      <div class="dhd_adddinghuodan_3_right">
        <div class="dhd_adddinghuodan_3_right_shouhuo" id="shouhuoDiv">
        </div>
        <input type="hidden" name="shouhuoInfo" id="shouhuoInfo">
      </div>
      <div class="clearBoth"></div>
    </li>
    <li>
      <div class="dhd_adddinghuodan_3_left">
        <span style="color:red">*</span> 填写备注：
      </div>
      <div class="dhd_adddinghuodan_3_right">
        <div class="dhd_adddinghuodan_3_right_beizhu">
          <input type="text" name="beizhu" lay-verify="required" placeholder="在此填写备注信息..."/>
        </div>
      </div>
      <div class="clearBoth"></div>
    </li>
    <li>
      附件信息：<a href="javascript:" id="uploadPdtImage" class="dhd_adddinghuodan_3_right_fujian_add"><img src="images/biao_123.png">  添加附件</a><font style="color:#98a8b8;font-size:12px;">（附件最大1M，支持格式：JPG、PNG、BMP、GIF）</font>
      <div class="photo_tu">
        <ul>
          <div class="clearBoth" id="uploadImages" data-num="0"></div>
        </ul>
      </div>
    </li>
  </ul>
</div>
<div class="sprukuadd_05">
  <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
  <button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
</div>
<input type="hidden" name="price" id="price" value="0.00">
<input type="hidden" name="weight" id="z_weight" value="0.00">
<input type="hidden" name="fujianInfo" id="originalPic">
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
          <?}
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

<div class="adddinghuodan_bianjidizhi layui-form">
  <div class="kh_gjsousuo_01">
    编辑
  </div>
  <div class="kh_gjsousuo_03">
    <ul>
      <li>
        <div class="kh_gjsousuo_03_left">
          退款人
        </div>
        <div class="kh_gjsousuo_03_right">
          <input type="text" id="e_name" placeholder="请输入退款人" class="kh_gjsousuo_03_right_input"/>
        </div>
        <div class="kh_gjsousuo_03_left">
          联系电话
        </div>
        <div class="kh_gjsousuo_03_right">
          <input type="text" id="e_phone" placeholder="请输入联系电话" class="kh_gjsousuo_03_right_input"/>
        </div>
        <div class="clearBoth"></div>
      </li>
      <li class="zengzhishui">
        <div class="kh_gjsousuo_03_left">
          开户名称
        </div>
        <div class="kh_gjsousuo_03_right">
          <input type="text" id="e_kaihuming" placeholder="请输入开户名称" class="kh_gjsousuo_03_right_input"/>
        </div>
        <div class="kh_gjsousuo_03_left">
          开户银行
        </div>
        <div class="kh_gjsousuo_03_right">
          <input type="text" id="e_kaihuhang" placeholder="请输入开户银行" class="kh_gjsousuo_03_right_input"/>
        </div>
        <div class="clearBoth"></div>
      </li>
      <li class="zengzhishui">
        <div class="kh_gjsousuo_03_left">
          银行账号
        </div>
        <div class="kh_gjsousuo_03_right">
          <input type="text" id="e_kaihubank" placeholder="请输入银行账号" class="kh_gjsousuo_03_right_input"/>
        </div>
        <div class="clearBoth"></div>
      </li>
    </ul>
  </div>
  <div class="kh_gjsousuo_04">
      <a href="javascript:" onclick="updateFapiao();" class="kh_gjsousuo_04_1">确定</a><a href="javascript:hideAddress();" class="kh_gjsousuo_04_21">取消</a>
    </div>
</div>
<!--新增收货地址结束-->
<input type="hidden" id="channelId" value="<?=$channelId?>">
<script type="text/javascript">
  var step = <?=$step?>;
  var price_xiaoshu = <?=$price_xiaoshu?>;
  var productListTalbe;
  var productListForm;
  var fapiaoArry = [];
  fapiaoArry['taitou'] = '发票抬头';
  fapiaoArry['content'] = '发票内容';
  fapiaoArry['shibie'] = '纳税人识别号';
  fapiaoArry['address'] = '地址';
  fapiaoArry['phone'] = '电话';
  fapiaoArry['kaihuming'] = '开户名称';
  fapiaoArry['kaihuhang'] = '开户银行';
  fapiaoArry['kaihubank'] = '银行账号';
  layui.use(['laydate', 'laypage','table','form','upload'], function(){
    var laydate = layui.laydate
    ,laypage = layui.laypage
    ,table = layui.table
    ,form = layui.form
    ,upload = layui.upload
    ,active = {
     appendCheckData: function(){
      var checkStatus = table.checkStatus('product_list')
      ,data = checkStatus.data;
      if(data.length>0){
       var num = parseInt($("#dataTable").attr("rows"));
       var rownums = $("#dataTable tr").length;
       $("#rowTrHeji").prev().remove();
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
         var price = data[i].price;
         var weight = data[i].weight;
         num = num+1;
         var str = '<tr height="48" id="rowTr'+num+'"><td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
         '<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
         '<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a> '+ 
         '</td>'+
         '<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
         '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
         '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
         '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>'+
         '<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
         '<input type="text" lay-verify="kucun" onchange="renderPrice('+num+');" name="inventoryNum['+num+']" value="'+shuliang+'" class="sprukuadd_03_tt_input">'+
         '<input type="hidden" name="inventoryId['+num+']" value="'+inventoryId+'">'+
         '<input type="hidden" name="inventorySn['+num+']" value="'+sn+'">'+
         '<input type="hidden" name="inventoryTitle['+num+']" value="'+title+'">'+
         '<input type="hidden" name="inventoryKey_vals['+num+']" value="'+key_vals+'">'+
         '<input type="hidden" name="inventoryBeizhu['+num+']" id="beizhu'+num+'" value="">'+
         '<input type="hidden" name="inventoryPdtId['+num+']" value="'+productId+'">'+
         '<input type="hidden" name="inventoryUnits['+num+']" value="'+units+'">'+
         '<input type="hidden" name="inventoryPrice['+num+']" id="inventoryPrice_'+num+'" value="'+price+'">'+
         '<input type="hidden" name="inventoryWeight['+num+']" id="inventoryWeight_'+num+'" value="'+weight+'">'+
         '</td>'+
         '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+price+
         '</td>'+
         '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="price_xiaoji'+num+'"></td>'+
         '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="weight_xiaoji'+num+'"></td>'+
         '<td bgcolor="#ffffff" width="276" class="sprukuadd_03_tt" align="center" valign="middle">'+
         '<span class="sprukuadd_03_tt_addbeizhu" onclick="editBeizhu('+num+')">+</span>'+
         '</td>';
         $("#rowTrHeji").before(str);
         renderPrice(num);
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
var uploadInit = upload.render({
    elem: '#uploadPdtImage'
    ,url: '?m=system&s=upload&a=upload'
    ,before:function(){
      layer.load();
    }
    ,done: function(res){
      layer.closeAll('loading');
      if(res.code > 0){
        return layer.msg(res.msg);
      }else{
        var nums = parseInt($('#uploadImages').attr("data-num"))+1;
        $('#uploadImages').before('<li id="image_li'+nums+'"><a><img src="'+res.url+'?x-oss-process=image/resize,w_122" width="122" height="122"></a><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
        $('#uploadImages').attr("data-num",nums);
        var originalPic = $("#originalPic").val();
          if(originalPic==''){
            originalPic = res.url;
          }else{
            originalPic = originalPic+'|'+res.url;
          }
          $("#originalPic").val(originalPic);
      }
    }
    ,error: function(){
      layer.msg('上传失败，请重试', {icon: 5});
    }
});
productListForm = form;
laydate.render({
  elem: '#jiaohuoTime'
  ,min:'<?=date("Y-m-d")?>'
  ,type: 'date'
  ,format: 'yyyy-MM-dd'
});
productListTalbe = table.render({
  elem: '#product_list'
  ,height: "full-250"
  ,url: '?m=system&s=tuihuo&a=getpdts'
  ,page: true
  ,cols: [[{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:"display:none;"},{field: 'productId', title: 'productId', width:0,style:"display:none;"},{field:'sn',title:'商品编码',width:150},{field:'title',title:'商品名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'units',title:'单位',width:100},{field:'shuliang',title:'数量',width:120},{field:'price',title:'单价（元）',width:120}]]
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
      return '字段不能小于或等于0';
    }
  }
});
form.on('submit(tijiao)', function(data){
  var price = parseFloat($("#price").val());
  var price_payed = parseFloat($("#price_payed").val());
  if(price<=0){
    layer.msg('请先添加退货产品',function(){});
    return false;
  }
  if(parseFloat(data.field.xieshangMoney)<=0){
    layer.msg('退款金额不能小于或等于0',function(){});
    return false;
  }
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
<script type="text/javascript" src="js/tuihuo_add.js"></script>
<? require('views/help.html');?>
</body>
</html>