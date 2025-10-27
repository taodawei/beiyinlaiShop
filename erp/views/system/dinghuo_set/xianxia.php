<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$xianxias = $db->get_results("select * from demo_kehu_bank where comId=$comId order by id asc");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/duanxin.css" rel="stylesheet" type="text/css">
    <link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/dinghuo_xianxia.js"></script>
</head>
<body>
	<div class="kehushezhi">
    	<div class="kehushezhi_01">
        	<img src="images/duanxin_20.png"/> 收款帐户设置
            <div class="bangzhulist_up_right" style="height:45px;line-height:45px;" onclick="showHelp(366);">帮助</div>
        </div>
    	<div class="shoukuanzhanghushezhi">
        	<div class="shoukuanzhanghushezhi_01">
            	<ul>
            		<li>
                    	<a href="?m=system&s=dinghuo_set&a=shoukuan" >在线支付</a>
                    </li>
                    <li>
                    	<a href="javascript:" class="shoukuanzhanghushezhi_01_on">线下支付</a>
                    </li>
                    <div class="clearBoth"></div>
            	</ul>
            </div>
        	<div class="shoukuanzhanghushezhi_02">
            	<div class="xianxiapay">
                    <div class="xianxiapay_01">
                        <a href="javascript:" onclick="edit_channel(0,'','','');">新增</a>
                    </div>
                    <div class="xianxiapay_02">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr height="43">
                                <td bgcolor="#f8e6c3" width="60">
                                </td>
                                <td bgcolor="#f8e6c3" class="xianxiapay_02_title" align="left" valign="middle">
                                    账户名称
                                </td>
                                <td bgcolor="#f8e6c3" class="xianxiapay_02_title" align="left" valign="middle">
                                    开户银行
                                </td>
                                <td bgcolor="#f8e6c3" class="xianxiapay_02_title" align="left" valign="middle">
                                    银行账号
                                </td>
                                <td bgcolor="#f8e6c3" class="xianxiapay_02_title" align="left" valign="middle">
                                    操作
                                </td>
                            </tr>
                            <?
                            if(!empty($xianxias)){
                                foreach ($xianxias as $xianxia) {
                                    ?>
                                    <tr height="43" data-id=<?=$xianxia->id?>>
                                        <td bgcolor="#ffffff" width="60">
                                        </td>
                                        <td bgcolor="#ffffff" class="xianxiapay_02_tt" align="left" valign="middle">
                                            <?=$xianxia->bank_user?>
                                        </td>
                                        <td bgcolor="#ffffff" class="xianxiapay_02_tt" align="left" valign="middle">
                                            <?=$xianxia->bank_name?>
                                        </td>
                                        <td bgcolor="#ffffff" class="xianxiapay_02_tt" align="left" valign="middle">
                                            <?=$xianxia->bank_account?>
                                        </td>
                                        <td bgcolor="#ffffff" class="xianxiapay_02_tt" align="left" valign="middle">
                                            <a href="javascript:" onclick="edit_channel(<?=$xianxia->id?>,'<?=$xianxia->bank_user?>','<?=$xianxia->bank_name?>','<?=$xianxia->bank_account?>');" class="kehujibieshezhi_xiugai">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript:" onclick="z_confirm('确定要删除该收款账户吗？',delChannel,<?=$xianxia->id?>);">删除</a>
                                        </td>
                                    </tr>
                                   <?
                                }
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? require('views/help.html');?>
</body>
</html>