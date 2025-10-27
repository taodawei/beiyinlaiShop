<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$shezhi = $db->get_row("select sn,addrows from user_shezhi where comId=$comId");
$rowss = $shezhi->addrows;
$arrays = array();
$rows = 0;
if(!empty($rowss)){
    $arrays = unserialize($rowss);
    $rows = count($arrays);
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
	<link href="styles/mendianshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="yueshezhi">
    	<div class="yueshezhi_up">
        	<img src="images/mdsz_12.png" alt=""/> 会员列表字段设置
        </div>
    	<div class="hyziduanshezhi">
            <form action="?m=system&s=mendian_set&a=addrows&submit=1" method="post" id="submitForm">
        	<div class="hyziduanshezhi_up">

            </div>
        	<div class="hyziduanshezhi_down">
            	<div class="hyziduanshezhi_up_1">
                	配置
                </div>
                
                <div class="hyziduanshezhi_down_2">
                <table border="0" cellspacing="0" cellpadding="0" class="kehuziduan_table" width="100%">
                 <tr>
                   <td colspan="2">
                     <ul>
                      <input type="hidden" name="rows" id="rows" value="<?=$rows?>" />
                      <li id="rows_contnt">
                        <? if(!empty($arrays)){
                          foreach($arrays as $arr){
                            ?>
                            <div id="row<?=$arr['id']?>" class="lirow">
                              <div class="gonggao_2_left">
                                字段名称：
                            </div>
                            <div class="gonggao_2_right">
                                <input type="text" name="name<?=$arr['id']?>" lay-verify="required" onchange="changeItem('<?=$arr['id']?>');" value="<?=$arr['name']?>" <? if(($commonId==1||$commonId==3)&&$arr['id']<4){?>readonly="true"<? }?> class="shenpi_add_2_input1"/>
                                
                              
                              &nbsp;&nbsp;是否显示：<select name="if_must<?=$arr['id']?>" <? if(($commonId==1||$commonId==3)&&$arr['id']<4){?>onmouseover="this.disabled=true" onmouseout="this.disabled=false"<? }?> id="mustSelect<?=$arr['id']?>" onchange="checkMust('<?=$arr['id']?>');" class="shenpi_add_2_input1">
                                  <option value="0" >否</option>
                                  <option value="1" <? if($arr['if_must']=='1'){?> selected="selected"<? }?>>是</option>
                              </select>
                              &nbsp;&nbsp;描述：<input type="text" name="detail<?=$arr['id']?>" value="<?=$arr['detail']?>" class="shenpi_add_2_input1"/>
                              <br />
                              <div id="shenpi_select<?=$arr['id']?>" class="shenpi_ziduan_set1" <? if($arr['type']=='select'){}else{?> style="display:none"<? }?>>
                                 <div class="shenpi_ziduan_set1_01">请输入列表项：  </div>
                                 <div class="shenpi_ziduan_set1_02">
                                    <ul>
                                        <li id="liebiao<?=$arr['id']?>">
                                            <? if(!empty($arr['select'])){
                                              $selects = explode('@',$arr['select']);
                                              foreach($selects as $value){?>
                                                <input type="text" name="select<?=$arr['id']?>[]" value="<?=$value?>" class="shenpi_add_2_input1" />
                                                <? }
                                            }?>
                                        </li>
                                        <li style="background:none"><img src="images/shenpi_add.png" style="cursor:pointer" onclick="addSelect(<?=$arr['id']?>);" width="27" height="27">&nbsp;&nbsp;<img src="images/shenpi_jian.png" style="cursor:pointer" onclick="delSelect(<?=$arr['id']?>);" width="27" height="27"></li>
                                    </ul>
                                 </div>
                                </div>
                            </div><div class="shenpi_add_2_dele1" onclick="del_ziduan('<?=$arr['id']?>')"><img src="images/shenpi_dele1.png"></div>
                            <div class="clearBoth"></div>
                        </div>
                        <?}}?>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                      <td width="11%">&nbsp;</td>
                      <td width="89%"><img src="images/addziduan.jpg" onclick="add_ziduan();" style="cursor: pointer;" width="95" height="27" /></td>
                  </tr>
              </table>
            </div>
        	<div class="yueshezhi_down_03">
                <button class="layui-btn" onclick="layer.load();$('#submitForm').submit();">立即提交</button>
            </div>
        </div>
        </form>
    </div>
    <script type="text/javascript">
    	var rows = <?=$rows ?>;
    </script>
    <script type="text/javascript" src="js/users/addrows.js"></script>
    <? require('views/help.html');?>
</body>
</html>