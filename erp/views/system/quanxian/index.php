<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$qxs = $db->get_results("select * from demo_quanxian where comId=$comId and model='product'");
$qxArry = array('edit'=>'新增/编辑产品','daoru'=>'导入','daochu'=>'导出','del'=>'删除','price'=>'修改价格','dapei'=>'商品组合推荐');
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
           商品管理
         </div>
         <div class="shangpinguanli_2">
          <form action="?m=system&s=quanxian&a=addQx" method="post" class="layui-form">
            <input type="hidden" name="return" id="return" value="index">
            <input type="hidden" name="departs" id="departs_product">
            <input type="hidden" name="users" id="users_product">
            <input type="hidden" name="departNames" id="departNames_product">
            <input type="hidden" name="userNames" id="userNames_product">
            <ul>
              <li>
               <div class="shangpinguanli_2_left">
                 管理员：
               </div>
               <div class="shangpinguanli_2_right">
                 <div class="shangpinguanli_2_right_guanliyuan">
                   <div class="shangpinguanli_2_right_guanliyuan_left" onclick="fanwei('product');">
                    <a href="javascript:" id="fanwei_product">+选择人员</a>
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
               <div class="shangpinguanli_2_right_shezhi">
                <? foreach($qxArry as $key=>$val){?><input type="checkbox" lay-skin="primary" name="functions[product][]" value="<?=$key?>" title="<?=$val?>" /> <? }?>
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
                  <? foreach($qxArry as $key=>$val){?><input type="checkbox" <? if(in_array($key,$arry)){?>checked="true"<? }?> lay-skin="primary" data-id="<?=$y->id?>" lay-filter="changeQx" value="<?=$key?>" title="<?=$val?>" /> <? }?>
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
      var departs = $("#departs_product").val();
      var users = $("#users_product").val();
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