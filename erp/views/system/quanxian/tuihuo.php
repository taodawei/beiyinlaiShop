<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$liucheng = array('if_shouhuo'=>1,'if_caiwu'=>1,'if_queren'=>0);
$liuchengContent = $db->get_var("select content from demo_liucheng where comId=$comId and type=2");
if(!empty($liuchengContent)){
  $liucheng = json_decode($liuchengContent,true);
}
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$qxs = $db->get_results("select * from demo_quanxian where comId=$comId and model='tuihuo'");
if(!empty($qxs)){
  foreach ($qxs as $q){
    $arry = explode(',',$q->functions);
    if(in_array('add',$arry)){
      $quanxian_add = $q;
      continue;
    }else if(in_array('shenhe',$arry)){
      $quanxian_shenhe = $q;
      continue;
    }else if(in_array('shouhuo',$arry)){
      $quanxian_shouhuo = $q;
      continue;
    }else if(in_array('caiwu',$arry)){
      $quanxian_caiwu = $q;
      continue;
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
      <div class="dinggouguanli">
        <div class="dinggouguanli_1">
          退货管理
        </div>
        <div class="dinggouguanli_2">
          <img src="images/quanxian_11.gif" alt=""/> 表示此订单步骤不允许调整 &nbsp;&nbsp;  
          <img src="images/quanxian_12.gif" alt=""/> 表示已启用此订单步骤  &nbsp;&nbsp;  
          <img src="images/quanxian_13.gif" alt=""/> 表示已禁用此订单步骤 &nbsp;&nbsp;  
        </div>
        <form action="?m=system&s=quanxian&a=editTLiucheng" method="post" class="layui-form">
          <div class="dinggouguanli_3">
            <input type="hidden" name="if_shouhuo" id="if_shouhuo" value="<?=$liucheng['if_shouhuo']?>">
            <input type="hidden" name="if_caiwu" id="if_caiwu" value="<?=$liucheng['if_caiwu']?>">
            <input type="hidden" name="if_queren" id="if_queren" value="<?=$liucheng['if_queren']?>">
            <ul>
              <li>
                <div class="dinggouguanli_3_img">
                  <img src="images/quanxian_14.png" alt=""/>
                </div>
                <div class="dinggouguanli_3_tt">  
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    提交退货单（代下单）
                    <div class="dinggouguanli_3_tt_con_1">
                      <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('add');">
                        <div class="shangpinguanli_2_right_guanliyuan_left">
                          <? 
                          $fanwei = '+选择人员';
                          $arry = array();
                          if(!empty($quanxian_add)){
                            $fanwei = $quanxian_add->departNames;
                            if(!empty($quanxian_add->userNames)){
                              if(empty($fanwei)){
                                $fanwei = $quanxian_add->userNames;
                              }else{
                                $fanwei = $fanwei.','.$quanxian_add->userNames;
                              }
                            }
                            $arry = explode(',',$quanxian_add->functions);
                          }
                          ?>
                          <a href="javascript:" id="fanwei_add"><?=$fanwei?></a>
                        </div>
                        <div class="clearBoth"></div>
                      </div>
                      <input type="hidden" name="id_add" id="id_add" value="<?=$quanxian_add->id?>">
                      <input type="hidden" name="departs_add" id="departs_add" value="<?=$quanxian_add->departs?>">
                      <input type="hidden" name="users_add" id="users_add" value="<?=$quanxian_add->userIds?>">
                      <input type="hidden" name="departNames_add" id="departNames_add" value="<?=$quanxian_add->departNames?>">
                      <input type="hidden" name="userNames_add" id="userNames_add" value="<?=$quanxian_add->userNames?>">
                    </div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img">
                  <img src="images/quanxian_15.png" alt=""/>
                </div>
                <div class="dinggouguanli_3_tt">
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    <div>退货单审核</div>
                    <div class="dinggouguanli_3_tt_con_1">
                      <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('shenhe');">
                        <div class="shangpinguanli_2_right_guanliyuan_left">
                          <? 
                            $arry = array();
                            $fanwei = '+选择人员';
                            if(!empty($quanxian_shenhe)){
                              $fanwei = $quanxian_shenhe->departNames;
                              if(!empty($quanxian_shenhe->userNames)){
                                if(empty($fanwei)){
                                  $fanwei = $quanxian_shenhe->userNames;
                                }else{
                                  $fanwei = $fanwei.','.$quanxian_shenhe->userNames;
                                }
                              }
                              $arry = explode(',',$quanxian_shenhe->functions);
                            }
                            if(empty($fanwei))$fanwei = '+选择人员';
                          ?>
                          <a id="fanwei_shenhe"><?=$fanwei?></a>
                        </div>
                        <div class="clearBoth"></div>
                      </div>
                      <input type="hidden" name="id_shenhe" id="id_shenhe" value="<?=$quanxian_shenhe->id?>">
                      <input type="hidden" name="departs_shenhe" id="departs_shenhe" value="<?=$quanxian_shenhe->departs?>">
                      <input type="hidden" name="users_shenhe" id="users_shenhe" value="<?=$quanxian_shenhe->userIds?>">
                      <input type="hidden" name="departNames_shenhe" id="departNames_shenhe" value="<?=$quanxian_shenhe->departNames?>">
                      <input type="hidden" name="userNames_shenhe" id="userNames_shenhe" value="<?=$quanxian_shenhe->userNames?>">
                    </div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img" data-img="19|24" onclick="updateJiedian('if_shouhuo');">
                  <img src="images/quanxian_<? if($liucheng['if_shouhuo']==1){echo '19';}else{echo '24';}?>.png" id="if_shouhuo_img"/>
                </div>
                <div class="dinggouguanli_3_tt">
                  <div class="qx_bg" id="if_shouhuo_bg" <? if($liucheng['if_shouhuo']==0){?>style="display:block"<?}?>></div>
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    <div>
                      收货确认 <img src="images/quanxian_25.png" onmouseover="tips(this,'完成收货确认后，选择的退货仓库将自动增加退货数量。如不开启此节点，退货后，需手工调整库存。',1)" onmouseout="hideTips();"/>
                    </div>
                    <div class="dinggouguanli_3_tt_con_1">
                      <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('shouhuo');">
                        <div class="shangpinguanli_2_right_guanliyuan_left">
                          <? 
                            $arry = array();
                            $fanwei = '+选择人员';
                            if(!empty($quanxian_shouhuo)){
                              $fanwei = $quanxian_shouhuo->departNames;
                              if(!empty($quanxian_shouhuo->userNames)){
                                if(empty($fanwei)){
                                  $fanwei = $quanxian_shouhuo->userNames;
                                }else{
                                  $fanwei = $fanwei.','.$quanxian_shouhuo->userNames;
                                }
                              }
                              $arry = explode(',',$quanxian_shouhuo->functions);
                            }
                            if(empty($fanwei))$fanwei = '+选择人员';
                          ?>
                          <a id="fanwei_shouhuo"><?=$fanwei?></a>
                        </div>
                        <div class="clearBoth"></div>
                      </div>
                      <input type="hidden" name="id_shouhuo" id="id_shouhuo" value="<?=$quanxian_shouhuo->id?>">
                      <input type="hidden" name="departs_shouhuo" id="departs_shouhuo" value="<?=$quanxian_shouhuo->departs?>">
                      <input type="hidden" name="users_shouhuo" id="users_shouhuo" value="<?=$quanxian_shouhuo->userIds?>">
                      <input type="hidden" name="departNames_shouhuo" id="departNames_shouhuo" value="<?=$quanxian_shouhuo->departNames?>">
                      <input type="hidden" name="userNames_shouhuo" id="userNames_shouhuo" value="<?=$quanxian_shouhuo->userNames?>">
                    </div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img" data-img="16|21" onclick="updateJiedian('if_caiwu');">
                  <img src="images/quanxian_<? if($liucheng['if_caiwu']==1){echo '16';}else{echo '21';}?>.png" id="if_caiwu_img"/>
                </div>
                <div class="dinggouguanli_3_tt">
                  <div class="qx_bg" id="if_caiwu_bg" <? if($liucheng['if_caiwu']==0){?>style="display:block"<?}?>></div>
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    <div>财务审核 <img src="images/quanxian_25.png"  onmouseover="tips(this,'开启节点后需要财务审核通过后才能进行下一步',1)" onmouseout="hideTips();"/></div>
                    <div class="dinggouguanli_3_tt_con_1">
                      <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('caiwu');">
                        <div class="shangpinguanli_2_right_guanliyuan_left">
                          <? 
                            $arry = array();
                            $fanwei = '+选择人员';
                            if(!empty($quanxian_caiwu)){
                              $fanwei = $quanxian_caiwu->departNames;
                              if(!empty($quanxian_caiwu->userNames)){
                                if(empty($fanwei)){
                                  $fanwei = $quanxian_caiwu->userNames;
                                }else{
                                  $fanwei = $fanwei.','.$quanxian_caiwu->userNames;
                                }
                              }
                              $arry = explode(',',$quanxian_caiwu->functions);
                            }
                            if(empty($fanwei))$fanwei = '+选择人员';
                          ?>
                          <a id="fanwei_caiwu"><?=$fanwei?></a>
                        </div>
                        <div class="clearBoth"></div>
                      </div>
                      <input type="hidden" name="id_caiwu" id="id_caiwu" value="<?=$quanxian_caiwu->id?>">
                      <input type="hidden" name="departs_caiwu" id="departs_caiwu" value="<?=$quanxian_caiwu->departs?>">
                      <input type="hidden" name="users_caiwu" id="users_caiwu" value="<?=$quanxian_caiwu->userIds?>">
                      <input type="hidden" name="departNames_caiwu" id="departNames_caiwu" value="<?=$quanxian_caiwu->departNames?>">
                      <input type="hidden" name="userNames_caiwu" id="userNames_caiwu" value="<?=$quanxian_caiwu->userNames?>">
                    </div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img" data-img="19|24" onclick="updateJiedian('if_queren');">
                  <img src="images/quanxian_<? if($liucheng['if_queren']==1){echo '19';}else{echo '24';}?>.png" id="if_queren_img"/>
                </div>
                <div class="dinggouguanli_3_tt">
                  <div class="qx_bg" id="if_queren_bg" <? if($liucheng['if_queren']==0){?>style="display:block"<?}?>></div>
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con" style="margin-top:15px;">
                    <div>【客户】收款确认</div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img">
                  <img src="images/quanxian_20.png" alt=""/>
                </div>
                <div class="dinggouguanli_3_tt">  
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con" style="margin-top:15px;">
                    完成
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
            </ul>
          </div>
        </div>
        <div class="jueseshezhi_biao">
          <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 保 存 </button>
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
<input type="hidden" name="return" id="return" value="dinghuo">
<script type="text/javascript" src="js/selectUser.js"></script>
<script type="text/javascript">
  layui.use(['form'], function(){
    form = layui.form;
    form.on('submit(tijiao)',function(){
      layer.load();
    });
  });
  function updateJiedian(dom){
    $imgs = $("#"+dom+"_img").parent().attr('data-img').split('|');
    if($("#"+dom).val()=='0'){
      $("#"+dom+"_img").attr("src",'images/quanxian_'+$imgs[0]+'.png');
      $("#"+dom).val('1');
      $("#"+dom+"_bg").hide();
    }else{
      $("#"+dom+"_img").attr("src",'images/quanxian_'+$imgs[1]+'.png');
      $("#"+dom).val('0');
      $("#"+dom+"_bg").show();
    }
  }
</script>
<? require('views/help.html');?>
</body>
</html>