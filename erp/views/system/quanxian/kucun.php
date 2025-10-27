<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$zongkuguan = $db->get_row("select * from demo_quanxian where comId=$comId and model='kucun' and functions='all' limit 1");
$fanwei = '+选择人员';
if(!empty($zongkuguan)){
  $fanwei = $zongkuguan->departNames;
  if(!empty($zongkuguan->userNames)){
    if(empty($fanwei)){
      $fanwei = $zongkuguan->userNames;
    }else{
      $fanwei = $fanwei.','.$zongkuguan->userNames;
    }
  }
}
$qxArry = array('edit'=>'编辑上下限','ruku'=>'入库','chuku'=>'出库','pandian'=>'盘点','mingxi'=>'出入库明细','huizong'=>'商品收发汇总','chengben'=>'成本调整');
$cangkus = $stores = $db->get_results("select id,title from demo_kucun_store where comId=$comId");
$product_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
$ruku_types = array();
$chuku_types = array();
if(!empty($product_set->ruku_types)){
  $ruku_types = explode('@_@',$product_set->ruku_types);
}
if(!empty($product_set->chuku_types)){
  $chuku_types = explode('@_@',$product_set->chuku_types);
}
$cangkuOptions = '';
foreach ($cangkus as $ck){
  $cangkuOptions .= '<option value="'.$ck->id.'">'.$ck->title.'</option>';
}
$shenpis = $db->get_results("select * from demo_kucun_shenpi where comId=$comId");
$ruku_shenpis = array();
$chuku_shenpis = array();
$diaobo_shenpis = array();
$caigou_shenpis = array();
$caigou_tuihuo_shenpis = array();
if(!empty($shenpis)){
  foreach ($shenpis as $shenpi) {
    switch ($shenpi->type){
      case 1:
        $ruku_shenpis[] = $shenpi;
      break;
      case 2:
        $chuku_shenpis[] = $shenpi;
      break;
      case 3:
        $diaobo_shenpis[] = $shenpi;
      break;
      case 4:
        $caigou_shenpis[] = $shenpi;
      break;
      case 5:
        $caigou_tuihuo_shenpis[] = $shenpi;
      break;
    }
  }
}
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
  <link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery.reveal.js"></script>
  <script type="text/javascript"  src="layui/layui.js"></script>
  <script type="text/javascript" src="js/common.js"></script>
  <script type="text/javascript">
    var optionstr = '<?=$cangkuOptions?>';
  </script>
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
       <div class="kucunguanli">
        <div class="kucunguanli_1">
          <div class="kucunguanli_1_01">
            库存管理
          </div>
          <div class="kucunguanli_1_02">
            <div class="shangpinguanli_2_left">
              总库管：
            </div>
            <div class="shangpinguanli_2_right">
              <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('zongkuguan');">
                <div class="shangpinguanli_2_right_guanliyuan_left">
                  <a id="fanwei_zongkuguan"><?=$fanwei?></a>
                </div>
                <input type="hidden" id="departs_zongkuguan" value="<?=$zongkuguan->departs?>">
                <input type="hidden" id="users_zongkuguan" value="<?=$zongkuguan->userIds?>">
                <input type="hidden" id="departNames_zongkuguan" value="<?=$zongkuguan->departNames?>">
                <input type="hidden" id="userNames_zongkuguan" value="<?=$zongkuguan->userNames?>">
                <input type="hidden" id="zongkuguan_id" value="<?=empty($zongkuguan)?'0':$zongkuguan->id?>">
                <div class="clearBoth"></div>
              </div>
            </div>
            <div class="clearBoth"></div>
          </div>
          <div class="kucunguanli_1_03">
            *总库管可以管理所有仓库库存，拥有库存管理的最高权限，并有调拨的权限
          </div>
        </div>
        <div class="kucunguanli_2 layui-form">
          <? foreach ($stores as $store) {
            $y = $db->get_row("select * from demo_quanxian where comId=$comId and model='kucun' and storeIds='$store->id'");
            $fanwei = '+选择人员';
            $arry = array();
            if(!empty($y)){
              $fanwei = $y->departNames;
              if(!empty($y->userNames)){
                if(empty($fanwei)){
                  $fanwei = $y->userNames;
                }else{
                  $fanwei = $fanwei.','.$y->userNames;
                }
              }
              $arry = explode(',',$y->functions);
            }
            ?>
            <div class="kucunguanli_2_01">
              <div class="kucunguanli_2_up">
                <?=$store->title?>
              </div>
              <div class="kucunguanli_2_down">
                <div class="kucunguanli_2_down_1">
                  <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('<?=$store->id?>');">
                    <div class="shangpinguanli_2_right_guanliyuan_left">
                      <a id="fanwei_<?=$store->id?>"><?=$fanwei?></a>
                    </div>
                    <div class="clearBoth"></div>
                  </div>
                  <input type="hidden" id="qxid_<?=$store->id?>" value="<?=$y->id?>">
                  <input type="hidden" id="departs_<?=$store->id?>" value="<?=$y->departs?>">
                  <input type="hidden" id="users_<?=$store->id?>" value="<?=$y->userIds?>">
                  <input type="hidden" id="departNames_<?=$store->id?>" value="<?=$y->departNames?>">
                  <input type="hidden" id="userNames_<?=$store->id?>" value="<?=$y->userNames?>">
                </div>
                <div class="kucunguanli_2_down_3" id="functions_<?=$store->id?>">
                  <? foreach($qxArry as $key=>$val){?><input type="checkbox" <? if(in_array($key,$arry)){?>checked="true"<? }?> lay-skin="primary" data-id="<?=$y->id?>" lay-filter="changeQx" value="<?=$key?>" title="<?=$val?>" /> <? }?>
                </div>
              </div>
            </div>
            <?
          }
          ?>
        </div>
      </div>
      <form action="?m=system&s=quanxian&a=churuku&tijiao=1" id="setForm" class="layui-form" method="post">
        <div class="churukushezhi_03">
          <div class="churukushezhi_01_up">
            <span>设置审批</span>
          </div>
          <div class="churukushezhi_03_down">
            <ul>
              <li>
                <div class="churukushezhi_03_down_1">
                  <input type="checkbox" name="ruku_shenpi" <? if($product_set->ruku_shenpi==1){?>checked<? }?> title="开启入库审批" lay-skin="primary" lay-filter="ruku_shenpi">
                </div>
                <div class="churukushezhi_03_down_2">
                  不开启则入库不需要审批
                </div>
                <div class="churukushezhi_03_down_3" id="ruku_shenpi_cont" <? if($product_set->ruku_shenpi==0){?>style="display:none"<? }?> rows="<?=count($ruku_shenpis)?>">
                  <? if(!empty($ruku_shenpis)){
                    $i= 0;
                    foreach ($ruku_shenpis as $shenpi) {
                      $i++;
                      $options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
                      ?>
                      <div id="ruku_shenpi<?=$i?>">
                        <div class="churukushezhi_03_down_3_01">
                          设置审批人
                        </div>
                        <div class="churukushezhi_03_down_3_02">
                          <select name="ruku_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
                        </div>
                        <div class="churukushezhi_03_down_3_03">
                          <div class="churukushezhi_03_down_3_03_up" id="ruku_shenpi_user<?=$i?>" onclick="selectSpUser('ruku',<?=$i?>);">
                            <?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
                          </div>
                        </div>
                        <div class="churukushezhi_03_down_3_04">
                          <a href="javascript:" onclick="addShenpiRow('ruku');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('ruku',<?=$i?>);"><img src="images/biao_66.png"/></a>
                        </div>
                        <div class="clearBoth"></div>
                        <input type="hidden" name="ruku_shenpi_user[<?=$i?>]" id="ruku_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
                        <input type="hidden" name="ruku_shenpi_id[<?=$i?>]" id="ruku_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
                      </div>
                      <?
                    }
                  }?>
                  
                </div>
                <div class="churukushezhi_03_down_2" <? if($product_set->ruku_shenpi==0){?>style="display:none"<? }?>>
                  说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则入库时不需要审批
                </div>
              </li>
              <li>
                <div class="churukushezhi_03_down_1">
                  <input type="checkbox" name="chuku_shenpi" <? if($product_set->chuku_shenpi==1){?>checked<? }?> title="开启出库审批" lay-skin="primary" lay-filter="chuku_shenpi">
                </div>
                <div class="churukushezhi_03_down_2">
                  不开启则出库不需要审批
                </div>
                <div class="churukushezhi_03_down_3" id="chuku_shenpi_cont" <? if($product_set->chuku_shenpi==0){?>style="display:none"<? }?> rows="<?=count($chuku_shenpis)?>">
                  <? if(!empty($chuku_shenpis)){
                    $i= 0;
                    foreach ($chuku_shenpis as $shenpi) {
                      $i++;
                      $options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
                      ?>
                      <div id="chuku_shenpi<?=$i?>">
                        <div class="churukushezhi_03_down_3_01">
                          设置审批人
                        </div>
                        <div class="churukushezhi_03_down_3_02">
                          <select name="chuku_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
                        </div>
                        <div class="churukushezhi_03_down_3_03">
                          <div class="churukushezhi_03_down_3_03_up" id="chuku_shenpi_user<?=$i?>" onclick="selectSpUser('chuku',<?=$i?>);">
                            <?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
                          </div>
                        </div>
                        <div class="churukushezhi_03_down_3_04">
                          <a href="javascript:" onclick="addShenpiRow('chuku');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('chuku',<?=$i?>);"><img src="images/biao_66.png"/></a>
                        </div>
                        <div class="clearBoth"></div>
                        <input type="hidden" name="chuku_shenpi_user[<?=$i?>]" id="chuku_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
                        <input type="hidden" name="chuku_shenpi_id[<?=$i?>]" id="chuku_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
                      </div>
                      <?
                    }
                  }?>
                  
                </div>
                <div class="churukushezhi_03_down_2" <? if($product_set->chuku_shenpi==0){?>style="display:none"<? }?>>
                  说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则出库时不需要审批
                </div>
              </li>
              <li>
                <div class="churukushezhi_03_down_1">
                  <input type="checkbox" name="diaobo_shenpi" <? if($product_set->diaobo_shenpi==1){?>checked<? }?> title="开启调拨审批" lay-skin="primary" lay-filter="diaobo_shenpi">
                </div>
                <div class="churukushezhi_03_down_2">
                  不开启则调拨不需要审批
                </div>
                <div class="churukushezhi_03_down_3" id="diaobo_shenpi_cont" <? if($product_set->diaobo_shenpi==0){?>style="display:none"<? }?> rows="<?=count($diaobo_shenpis)?>">
                  <? if(!empty($diaobo_shenpis)){
                    $i= 0;
                    foreach ($diaobo_shenpis as $shenpi) {
                      $i++;
                      $options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
                      ?>
                      <div id="diaobo_shenpi<?=$i?>">
                        <div class="churukushezhi_03_down_3_01">
                          设置审批人
                        </div>
                        <div class="churukushezhi_03_down_3_02">
                          <select name="diaobo_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
                        </div>
                        <div class="churukushezhi_03_down_3_03">
                          <div class="churukushezhi_03_down_3_03_up" id="diaobo_shenpi_user<?=$i?>" onclick="selectSpUser('diaobo',<?=$i?>);">
                            <?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
                          </div>
                        </div>
                        <div class="churukushezhi_03_down_3_04">
                          <a href="javascript:" onclick="addShenpiRow('diaobo');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('diaobo',<?=$i?>);"><img src="images/biao_66.png"/></a>
                        </div>
                        <div class="clearBoth"></div>
                        <input type="hidden" name="diaobo_shenpi_user[<?=$i?>]" id="diaobo_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
                        <input type="hidden" name="diaobo_shenpi_id[<?=$i?>]" id="diaobo_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
                      </div>
                      <?
                    }
                  }?>
                  
                </div>
                <div class="churukushezhi_03_down_2" <? if($product_set->diaobo_shenpi==0){?>style="display:none"<? }?>>
                  说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则调拨时不需要审批
                </div>
              </li>
              <li>
                <div class="churukushezhi_03_down_1">
                  <input type="checkbox" name="caigou_shenpi" <? if($product_set->caigou_shenpi==1){?>checked<? }?> title="开启采购审批" lay-skin="primary" lay-filter="caigou_shenpi">
                </div>
                <div class="churukushezhi_03_down_2">
                  不开启则采购不需要审批
                </div>
                <div class="churukushezhi_03_down_3" id="caigou_shenpi_cont" <? if($product_set->caigou_shenpi==0){?>style="display:none"<? }?> rows="<?=count($caigou_shenpis)?>">
                  <? if(!empty($caigou_shenpis)){
                    $i= 0;
                    foreach ($caigou_shenpis as $shenpi) {
                      $i++;
                      $options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
                      ?>
                      <div id="caigou_shenpi<?=$i?>">
                        <div class="churukushezhi_03_down_3_01">
                          设置审批人
                        </div>
                        <div class="churukushezhi_03_down_3_02">
                          <select name="caigou_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
                        </div>
                        <div class="churukushezhi_03_down_3_03">
                          <div class="churukushezhi_03_down_3_03_up" id="caigou_shenpi_user<?=$i?>" onclick="selectSpUser('caigou',<?=$i?>);">
                            <?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
                          </div>
                        </div>
                        <div class="churukushezhi_03_down_3_04">
                          <a href="javascript:" onclick="addShenpiRow('caigou');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('caigou',<?=$i?>);"><img src="images/biao_66.png"/></a>
                        </div>
                        <div class="clearBoth"></div>
                        <input type="hidden" name="caigou_shenpi_user[<?=$i?>]" id="caigou_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
                        <input type="hidden" name="caigou_shenpi_id[<?=$i?>]" id="caigou_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
                      </div>
                      <?
                    }
                  }?>
                  
                </div>
                <div class="churukushezhi_03_down_2" <? if($product_set->caigou_shenpi==0){?>style="display:none"<? }?>>
                  说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则采购时不需要审批
                </div>
              </li>
              <li>
                <div class="churukushezhi_03_down_1">
                  <input type="checkbox" name="caigou_tuihuo_shenpi" <? if($product_set->caigou_tuihuo_shenpi==1){?>checked<? }?> title="开启采购退货审批" lay-skin="primary" lay-filter="caigou_tuihuo_shenpi">
                </div>
                <div class="churukushezhi_03_down_2">
                  不开启则采购退货不需要审批
                </div>
                <div class="churukushezhi_03_down_3" id="caigou_tuihuo_shenpi_cont" <? if($product_set->caigou_tuihuo_shenpi==0){?>style="display:none"<? }?> rows="<?=count($dcaigou_tuihuo_shenpis)?>">
                  <? if(!empty($caigou_tuihuo_shenpis)){
                    $i= 0;
                    foreach ($caigou_tuihuo_shenpis as $shenpi) {
                      $i++;
                      $options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
                      ?>
                      <div id="caigou_tuihuo_shenpi<?=$i?>">
                        <div class="churukushezhi_03_down_3_01">
                          设置审批人
                        </div>
                        <div class="churukushezhi_03_down_3_02">
                          <select name="caigou_tuihuo_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
                        </div>
                        <div class="churukushezhi_03_down_3_03">
                          <div class="churukushezhi_03_down_3_03_up" id="caigou_tuihuo_shenpi_user<?=$i?>" onclick="selectSpUser('caigou_tuihuo',<?=$i?>);">
                            <?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
                          </div>
                        </div>
                        <div class="churukushezhi_03_down_3_04">
                          <a href="javascript:" onclick="addShenpiRow('caigou_tuihuo');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('caigou_tuihuo',<?=$i?>);"><img src="images/biao_66.png"/></a>
                        </div>
                        <div class="clearBoth"></div>
                        <input type="hidden" name="caigou_tuihuo_shenpi_user[<?=$i?>]" id="caigou_tuihuo_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
                        <input type="hidden" name="caigou_tuihuo_shenpi_id[<?=$i?>]" id="caigou_tuihuo_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
                      </div>
                      <?
                    }
                  }?>
                  
                </div>
                <div class="churukushezhi_03_down_2" <? if($product_set->caigou_tuihuo_shenpi==0){?>style="display:none"<? }?>>
                  说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则采购退货时不需要审批
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="churukushezhi_04">
          <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao"> 保 存 </button>
        </div>
      </form>
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
<input type="hidden" name="return" id="return" value="kucun">
<script type="text/javascript" src="js/selectUser.js"></script>
<script type="text/javascript">
  layui.use(['form'], function(){
    form = layui.form;
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
<script type="text/javascript" src="js/kuncun_set.js"></script>
<? require('views/help.html');?>
</body>
</html>