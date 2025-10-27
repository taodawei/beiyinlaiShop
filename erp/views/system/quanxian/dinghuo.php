<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$liucheng = array('if_caiwu'=>0,'if_chuku'=>1,'if_fahuo'=>1,'if_shouhuo'=>0);
$liuchengContent = $db->get_var("select content from demo_liucheng where comId=$comId and type=1");
if(!empty($liuchengContent)){
  $liucheng = json_decode($liuchengContent,true);
}
$qxArry = array(
  'money'=>array('shoukuan'=>'收款确认','tuikuan'=>'退款确认','account'=>'资金账户','shouzhi'=>'收支明细','tongji'=>'订单收款统计'),
  'yingxiao'=>array('banner'=>'广告发布','gonggao'=>'通知公告'),
  'kehu'=>array('kehu'=>'客户管理','jiameng'=>'加盟信息','fankui'=>'客户反馈')
);
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$qxTitle = array('money'=>'资金','yingxiao'=>'营销','kehu'=>$kehu_title);
$qxs = $db->get_results("select * from demo_quanxian where comId=$comId and model='dinghuo'");
if(!empty($qxs)){
  foreach ($qxs as $q){
    $arry = explode(',',$q->functions);
    if(in_array('add',$arry)){
      $quanxian_add = $q;
      continue;
    }else if(in_array('shenhe',$arry)){
      $quanxian_shenhe = $q;
      continue;
    }else if(in_array('caiwu',$arry)){
      $quanxian_caiwu = $q;
      continue;
    }else if(in_array('chuku',$arry)){
      $quanxian_chuku = $q;
      continue;
    }else if(in_array('fahuo',$arry)){
      $quanxian_fahuo = $q;
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
          订货管理
        </div>
        <div class="dinggouguanli_2">
          <img src="images/quanxian_11.gif" alt=""/> 表示此订单步骤不允许调整 &nbsp;&nbsp;  
          <img src="images/quanxian_12.gif" alt=""/> 表示已启用此订单步骤  &nbsp;&nbsp;  
          <img src="images/quanxian_13.gif" alt=""/> 表示已禁用此订单步骤 &nbsp;&nbsp;  
        </div>
        <form action="?m=system&s=quanxian&a=editLiucheng" method="post" class="layui-form">
          <div class="dinggouguanli_3">
            <input type="hidden" name="if_caiwu" id="if_caiwu" value="<?=$liucheng['if_caiwu']?>">
            <input type="hidden" name="if_chuku" id="if_chuku" value="<?=$liucheng['if_chuku']?>">
            <input type="hidden" name="if_fahuo" id="if_fahuo" value="<?=$liucheng['if_fahuo']?>">
            <input type="hidden" name="if_shouhuo" id="if_shouhuo" value="<?=$liucheng['if_shouhuo']?>">
            <ul>
              <li>
                <div class="dinggouguanli_3_img">
                  <img src="images/quanxian_14.png" alt=""/>
                </div>
                <div class="dinggouguanli_3_tt">  
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    提交订单（代下单）
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
                    <div class="dinggouguanli_3_tt_con_2">
                      <? foreach($qxArry as $key=>$val){?>
                      <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                      <div class="xitongshezhiguanli_down_quanxian_2">
                        <? foreach($val as $k=>$v){?>
                        <input type="checkbox" lay-skin="primary" <? if(in_array($k,$arry)){?>checked="true"<? }?> name="functions[add][]" value="<?=$k?>" title="<?=$v?>" />
                        <? }?>
                      </div>
                      <? }?>
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
                    <div>订单审核</div>
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
                    <div class="dinggouguanli_3_tt_con_2">
                      <? foreach($qxArry as $key=>$val){?>
                      <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                      <div class="xitongshezhiguanli_down_quanxian_2">
                        <? foreach($val as $k=>$v){?>
                        <input type="checkbox" lay-skin="primary" <? if(in_array($k,$arry)){?>checked="true"<? }?> name="functions[shenhe][]" value="<?=$k?>" title="<?=$v?>" />
                        <? }?>
                      </div>
                      <? }?>
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
                    <div>财务审核 <img src="images/quanxian_25.png"  onmouseover="tips(this,'开启节点后需要全部收款后才能进行下一步',1)" onmouseout="hideTips();"/></div>
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
                    <div class="dinggouguanli_3_tt_con_2">
                      <? foreach($qxArry as $key=>$val){?>
                      <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                      <div class="xitongshezhiguanli_down_quanxian_2">
                        <? foreach($val as $k=>$v){?>
                        <input type="checkbox" lay-skin="primary" <? if(in_array($k,$arry)){?>checked="true"<? }?> name="functions[caiwu][]" value="<?=$k?>" title="<?=$v?>" />
                        <? }?>
                      </div>
                      <? }?>
                    </div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img" data-img="17|22" onclick="updateJiedian('if_chuku');">
                  <img src="images/quanxian_<? if($liucheng['if_chuku']==1){echo '17';}else{echo '22';}?>.png" id="if_chuku_img"/>
                </div>
                <div class="dinggouguanli_3_tt">
                  <div class="qx_bg" id="if_chuku_bg" <? if($liucheng['if_chuku']==0){?>style="display:block"<?}?>></div>
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    <div style="position:relative;">
                      出库审核 <img src="images/quanxian_25.png" onmouseover="tips(this,'商品库存在“出库审核”节点完成后扣减，如需进行库存管理或核算销售成本毛利，需开启此节点；默认为库管权限。',1)" onmouseout="hideTips();" />
                    </div>
                    <div class="dinggouguanli_3_tt_con_1">
                      <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('chuku');">
                        <div class="shangpinguanli_2_right_guanliyuan_left">
                          <? 
                            $arry = array();
                            $fanwei = '+选择人员';
                            if(!empty($quanxian_chuku)){
                              $fanwei = $quanxian_chuku->departNames;
                              if(!empty($quanxian_chuku->userNames)){
                                if(empty($fanwei)){
                                  $fanwei = $quanxian_chuku->userNames;
                                }else{
                                  $fanwei = $fanwei.','.$quanxian_chuku->userNames;
                                }
                              }
                              $arry = explode(',',$quanxian_chuku->functions);
                            }
                            if(empty($fanwei))$fanwei = '+选择人员';
                          ?>
                          <a id="fanwei_chuku"><?=$fanwei?></a>
                        </div>
                        <div class="clearBoth"></div>
                      </div>
                      <input type="hidden" name="id_chuku" id="id_chuku" value="<?=$quanxian_chuku->id?>">
                      <input type="hidden" name="departs_chuku" id="departs_chuku" value="<?=$quanxian_chuku->departs?>">
                      <input type="hidden" name="users_chuku" id="users_chuku" value="<?=$quanxian_chuku->userIds?>">
                      <input type="hidden" name="departNames_chuku" id="departNames_chuku" value="<?=$quanxian_chuku->departNames?>">
                      <input type="hidden" name="userNames_chuku" id="userNames_chuku" value="<?=$quanxian_chuku->userNames?>">
                    </div>
                    <div class="dinggouguanli_3_tt_con_2">
                      <? foreach($qxArry as $key=>$val){?>
                      <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                      <div class="xitongshezhiguanli_down_quanxian_2">
                        <? foreach($val as $k=>$v){?>
                        <input type="checkbox" lay-skin="primary" <? if(in_array($k,$arry)){?>checked="true"<? }?> name="functions[chuku][]" value="<?=$k?>" title="<?=$v?>" />
                        <? }?>
                      </div>
                      <? }?>
                    </div>
                  </div>
                </div>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="dinggouguanli_3_img" data-img="18|23" onclick="updateJiedian('if_fahuo');">
                  <img src="images/quanxian_<? if($liucheng['if_fahuo']==1){echo '18';}else{echo '23';}?>.png" id="if_fahuo_img"/>
                </div>
                <div class="dinggouguanli_3_tt">
                  <div class="qx_bg" id="if_fahuo_bg" <? if($liucheng['if_fahuo']==0){?>style="display:block"<?}?>></div>
                  <div class="dinggouguanli_3_tt_biao"></div>
                  <div class="dinggouguanli_3_tt_con">
                    <div>
                      发货确认 <img src="images/quanxian_25.png"  onmouseover="tips(this,'如需跟踪订单物流信息，需开启此节点。',1)" onmouseout="hideTips();"/>
                    </div>
                    <div class="dinggouguanli_3_tt_con_1">
                      <div class="shangpinguanli_2_right_guanliyuan" onclick="fanwei('fahuo');">
                        <div class="shangpinguanli_2_right_guanliyuan_left">
                          <? 
                            $arry = array();
                            $fanwei = '+选择人员';
                            if(!empty($quanxian_fahuo)){
                              $fanwei = $quanxian_fahuo->departNames;
                              if(!empty($quanxian_fahuo->userNames)){
                                if(empty($fanwei)){
                                  $fanwei = $quanxian_fahuo->userNames;
                                }else{
                                  $fanwei = $fanwei.','.$quanxian_fahuo->userNames;
                                }
                              }
                              $arry = explode(',',$quanxian_fahuo->functions);
                            }
                            if(empty($fanwei))$fanwei = '+选择人员';
                          ?>
                          <a id="fanwei_fahuo"><?=$fanwei?></a>
                        </div>
                        <div class="clearBoth"></div>
                      </div>
                      <input type="hidden" name="id_fahuo" id="id_fahuo" value="<?=$quanxian_fahuo->id?>">
                      <input type="hidden" name="departs_fahuo" id="departs_fahuo" value="<?=$quanxian_fahuo->departs?>">
                      <input type="hidden" name="users_fahuo" id="users_fahuo" value="<?=$quanxian_fahuo->userIds?>">
                      <input type="hidden" name="departNames_fahuo" id="departNames_fahuo" value="<?=$quanxian_fahuo->departNames?>">
                      <input type="hidden" name="userNames_fahuo" id="userNames_fahuo" value="<?=$quanxian_fahuo->userNames?>">
                    </div>
                    <div class="dinggouguanli_3_tt_con_2">
                      <? foreach($qxArry as $key=>$val){?>
                      <div class="xitongshezhiguanli_down_quanxian_1"><?=$qxTitle[$key]?></div>
                      <div class="xitongshezhiguanli_down_quanxian_2">
                        <? foreach($val as $k=>$v){?>
                        <input type="checkbox" lay-skin="primary" <? if(in_array($k,$arry)){?>checked="true"<? }?> name="functions[fahuo][]" value="<?=$k?>" title="<?=$v?>" />
                        <? }?>
                      </div>
                      <? }?>
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
                  <div class="dinggouguanli_3_tt_con" style="margin-top:15px;">
                    <div>收货确认</div>
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