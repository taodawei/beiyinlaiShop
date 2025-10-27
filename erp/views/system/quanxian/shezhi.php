<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$qxs = $db->get_results("select * from demo_quanxian where comId=$comId and model='shezhi'");
$qxArry = array(
  'basic'=>array('product_channel'=>'商品分类','product_brand'=>'商品品牌','product_unit'=>'计量单位','quanxian'=>'角色权限设置','store'=>'仓库'),
  'canshu'=>array('product_set'=>'商品设置','kucun_set'=>'库存设置','churuku'=>'出入库设置'),
  'dinghuo'=>array('dinghuo_set'=>$kehu_title.'设置','level'=>$kehu_title.'级别','shoukuan'=>'收款帐户设置'),
  'mendian_set'=>array('mendian_set_index'=>'基础设置','mendian_set_type'=>'门店类型','mendian_set_addrows'=>'会员字段设置','mendian_set_level'=>'会员等级','mendian_set_jifen'=>'积分规则','mendian_set_yue'=>'余额设置')
);
$qxTitle = array('basic'=>'基础设置','canshu'=>'系统参数设置','dinghuo'=>'收款帐户设置','mendian_set'=>'零售设置');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><? echo SITENAME;?></title>
  <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
  <link href="styles/common.css" rel="stylesheet" type="text/css">
  <link href="styles/duanxin.css" rel="stylesheet" type="text/css">
  <link href="styles/jueseshezhi.css" rel="stylesheet" type="text/css">
  <link href="styles/selectUsers.css" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery.reveal.js"></script>
  <script type="text/javascript"  src="layui/layui.js"></script>
  <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="jueseshezhi">
   <div class="jueseshezhi_up">
     权限设置
   </div>
   <div class="jueseshezhi_down">
    <? include('header.php');?>
    <div class="jueseshezhi_down_2">
     <div class="jueseshezhi_down_2_01">	
       <div class="shangpinguanli">
         <div class="shangpinguanli_1">
           系统设置
         </div>
         <div class="shangpinguanli_2">
          <form action="?m=system&s=quanxian&a=addQx" method="post" class="layui-form">
            <input type="hidden" name="return" id="return" value="shezhi">
            <input type="hidden" name="departs" id="departs_shezhi">
            <input type="hidden" name="users" id="users_shezhi">
            <input type="hidden" name="departNames" id="departNames_shezhi">
            <input type="hidden" name="userNames" id="userNames_shezhi">
            <ul>
              <li>
               <div class="shangpinguanli_2_left">
                 管理员：
               </div>
               <div class="shangpinguanli_2_right">
                 <div class="shangpinguanli_2_right_guanliyuan">
                   <div class="shangpinguanli_2_right_guanliyuan_left" onclick="fanwei('shezhi');">
                    <a href="javascript:" id="fanwei_shezhi">+选择人员</a>
                  </div>
                  <div class="clearBoth"></div>
                </div>
              </div>
              <div class="clearBoth"></div>
            </li>
            <li>
             <div class="shangpinguanli_2_left">
               设置权限：
             </div>
             <div class="shangpinguanli_2_right">
               <div class="shangpinguanli_2_right_shezhi" style="width:100%;">
                <? foreach($qxArry as $key=>$val){?>
                <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                <div class="xitongshezhiguanli_down_quanxian_2" style="margin-bottom:10px;width:100%;">
                  <? foreach($val as $k=>$v){?>
                  <input type="checkbox" lay-skin="primary" name="functions[shezhi][]" value="<?=$k?>" title="<?=$v?>" />
                  <? }?>
                </div>
                <? }?>
               </div>
             </div>
             <div class="clearBoth"></div>
           </li>
           <li>
             <div class="shangpinguanli_2_tijiao">
               <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 新 增 </button>
             </div>
           </li>
         </ul>
       </form>
     </div>
     <div class="shangpinguanli_3 layui-form">
       <table width="100%" border="0" cellpadding="0" cellspacing="0">
         <tr height="45">
           <td bgcolor="#f3fbff" width="30%" align="left" valign="middle">
             管理员
           </td>
           <td bgcolor="#f3fbff" width="60%" align="left" valign="middle">
             权限
           </td>
           <td bgcolor="#f3fbff" width="10%" align="left" valign="middle">
             操作
           </td>
         </tr>
         <? if(!empty($qxs)){
          foreach ($qxs as $y){
            $fanwei = $y->departNames;
            if(!empty($y->userNames)){
              if(empty($fanwei)){
                $fanwei = $y->userNames;
              }else{
                $fanwei = $fanwei.','.$y->userNames;
              }
            }
            $arry = explode(',',$y->functions);
            ?>
            <tr height="45">
               <td bgcolor="#ffffff" align="left" valign="middle">
                  <?=$fanwei?>
               </td>
               <td bgcolor="#ffffff" align="left" valign="middle">
                <div style="padding:10px 0px;">
                  <? foreach($qxArry as $key=>$val){?>
                  <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                  <div class="xitongshezhiguanli_down_quanxian_2">
                    <? foreach($val as $k=>$v){?>
                    <input type="checkbox" lay-skin="primary" <? if(in_array($k,$arry)){?>checked="true"<? }?> data-id="<?=$y->id?>" lay-filter="changeQx" name="functions[shezhi][]" value="<?=$k?>" title="<?=$v?>" />
                    <? }?>
                  </div>
                  <? }?>
                </div>
               </td>
               <td bgcolor="#ffffff" align="left" valign="middle">
                 <a href="javascript:" onclick="z_confirm('确定要删除吗？',del,<?=$y->id?>);">删除</a>
               </td>
             </tr>
            <?
          }
         }?>
       </table>
     </div>
   </div>
 </div>
</div>
</div>
</div>
<div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<div class="reveal-modal-bg" style="display:none; cursor: pointer;"></div>
<input type="hidden" id="departs" value="" />
<input type="hidden" id="users" value="" />
<input type="hidden" id="departNames" value=""/>
<input type="hidden" id="userNames" value="" />
<input type="hidden" id="editId" value="0" />
<script type="text/javascript" src="js/selectUser.js"></script>
<script type="text/javascript">
  layui.use(['form'], function(){
    form = layui.form;
    form.on('submit(tijiao)',function(){
      var departs = $("#departs_shezhi").val();
      var users = $("#users_shezhi").val();
      var qxs = $("input[name^='functions[']:checked").length;
      if(departs==''&&users==''){
        layer.msg("请先选择要授权的员工",function(){});
        return false;
      }
      if(qxs<1){
        layer.msg("请先选择要授权的权限",function(){});
        return false;
      }
      layer.load();
    });
    form.on('checkbox(changeQx)',function(data){
      var elem = $(data.elem);
      var value = elem.val();
      var id = elem.attr('data-id');
      var opt = 'del';
      if(data.elem.checked){
        opt = 'add';
      }
      layer.load();
      $.ajax({
        type: "POST",
        url: "?m=system&s=quanxian&a=editFunc",
        data: "function="+value+"&id="+id+"&opt="+opt,
        dataType:"json",timeout : 30000,
        success: function(resdata){
          layer.closeAll();
        },
        error: function() {
            layer.msg('超时，请重试', {icon: 5});
        }
      });
    });
  });
  function del(id){
    layer.load();
    var re = $("#return").val();
    location.href='?m=system&s=quanxian&a=delete&id='+id+"&return="+re;
  }
</script>
<? require('views/help.html');?>
</body>
</html>