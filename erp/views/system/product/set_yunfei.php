<?
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
//$company = json_decode($_SESSION[TB_PREFIX.'company']);
$id = (int)$request['id'];
$_SESSION['tijiao']=1;

$scene = empty($request['scene'])?1:(int)$request['scene'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>运费模板</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles2/index.css" rel="stylesheet" type="text/css" />
<link href="styles2/lrtk.css" rel="stylesheet" type="text/css" />
<link href="styles/set_common.css" rel="stylesheet" type="text/css" />
<link href="styles/shouye/qiyerenzheng.css" rel="stylesheet" type="text/css" />
<link href="styles/shouye/qiyeshezhi.css" rel="stylesheet" type="text/css" />
<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
<link href="styles/yingyongguanli.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js2/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js2/jquery.reveal.js"></script>
<script type="text/javascript" src="layui/layui.js"></script>
</head>
<body>
<div id="root">
    <div id="main">
    	<div class="cont">
        	<div class="cont_01">
                运费设置
            </div>
                <?
                $yunfeis = $db->get_results("select * from yunfei_moban where comId=$comId ".(empty($scene)?'':'and scene='.$scene)." and mendianId=0 order by id desc");
                ?>
                <div class="splist_up_addtab">
                    <ul>
                        <li>
                            <a href="?m=system&s=product&a=set_yunfei&scene=1" <? if($scene==1){?>class="splist_up_addtab_on"<? }?>>零售运费</a>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="yunfeishezhi" style="margin-top:20px;">
                    <div class="yunfeishezhi_2">
                        <div class="yunfeishezhi_2_down">
                            <div class="yunfeishezhi_2_01">
                                <a href="?m=system&s=product&a=add_yunfei_moban&scene=1">+ 新建运费模板</a>
                            </div>
                            <div class="yunfeishezhi_2_02">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr height="40">
                                        <td bgcolor="#e5f0f3" width="62" align="center" valign="middle">
                                            
                                        </td>
                                        <td bgcolor="#e5f0f3" width="250" align="left" valign="middle">
                                            模板名称
                                        </td>
                                        <td bgcolor="#e5f0f3" width="100" align="left" valign="middle">
                                            应用场景
                                        </td>
                                        <td bgcolor="#e5f0f3" width="150" align="left" valign="middle">
                                            计算依据
                                        </td>
                                        <td bgcolor="#e5f0f3" width="150" align="left" valign="middle">
                                            满包邮
                                        </td>
                                        <td bgcolor="#e5f0f3" width="150" align="left" valign="middle">
                                            最近编辑时间
                                        </td>
                                        <td bgcolor="#e5f0f3" width="262" align="left" valign="middle">
                                            操作
                                        </td>
                                    </tr>
                                    <? if(!empty($yunfeis)){
                                        foreach ($yunfeis as $yunfei) {
                                            ?>
                                            <tr height="42" id="tr_<?=$yunfei->id?>">
                                                <td bgcolor="#ffffff" align="center" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <img src="images/yunfei_12.png" onclick="show_detail(this,<?=$yunfei->id?>);" class="openIcon"/>
                                                </td>
                                                <td bgcolor="#ffffff" align="left" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <?=$yunfei->title?>
                                                </td>
                                                <td bgcolor="#ffffff" align="left" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <?=$yunfei->scene==2?'订货端':'零售端'?>
                                                </td>
                                                <td bgcolor="#ffffff" align="left" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <?=$yunfei->accordby==2?'按重量':'按数量'?>
                                                </td>
                                                <td bgcolor="#ffffff" align="left" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <?=$yunfei->if_man==1?'满'.$yunfei->man.'元包'.($yunfei->mantype==1?'全部':'基础').'运费':'否'?>
                                                </td>
                                                <td bgcolor="#ffffff" align="left" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <?=date("Y-m-d H:i",strtotime($yunfei->dtTime))?>
                                                </td>
                                                <td bgcolor="#ffffff" align="left" valign="middle" class="yunfeishezhi_2_02_tt">
                                                    <a href="?m=system&s=product&a=add_yunfei_moban&mobanid=<?=$yunfei->id?>">复制模板</a><a href="?m=system&s=product&a=add_yunfei_moban&id=<?=$yunfei->id?>">编辑</a><a href="javascript:delete_moban(<?=$yunfei->id?>);">删除</a>
                                                </td>
                                            </tr>
                                            <?
                                        }
                                    }else{?>
                                        <tr height="42"><td colspan="7" width="100%" align="center">暂无运费模板~~</td></tr>
                                    <?  }?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/set_yunfei.js"></script>
</body>
</html>
