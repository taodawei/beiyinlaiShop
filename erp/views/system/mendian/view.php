<?
global $db,$request;
$id = (int)$request['id'];
$supplier = $db->get_row("select * from mendian where id=$id");
$areaName = '';
if($supplier->areaId>0){
    $area = $db->get_row("select id,title,parentId from demo_area where id=".$supplier->areaId);
    $areaName = $area->title;
    if($area->parentId>0){
        $farea = $db->get_row("select id,title,parentId from demo_area where id=".$area->parentId);
        $areaName = $farea->title.$areaName;
        if($farea->parentId!=0){
            $farea = $db->get_var("select title from demo_area where id=".$farea->parentId);
            $areaName = $farea.$areaName;
        }
    }
}
if(!empty($supplier->userInfo))$userInfo = json_decode($supplier->userInfo,true);
if(!empty($supplier->caiwu))$caiwu = json_decode($supplier->caiwu,true);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$supplier->title?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript">
        layui.use(['layer'], function(){
            <? if(empty($supplier)){?>
                layer.confirm('商家不存在或已删除',{
                  btn: ['确定'],
                }, function(){
                    location.href='?m=system&s=supplier';
                });
            <? }?>
        });
        function doPrint() {   
            bdhtml=window.document.body.innerHTML;   
            sprnstr="<!--startprint-->";   
            eprnstr="<!--endprint-->";   
            prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17);   
            prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));   
            window.document.body.innerHTML=prnhtml;  
            window.print();
        }
    </script>
</head>
<body>
    <div class="back">
        <div><a href="<?=urldecode($request['returnurl'])?>"><img src="images/back.gif" /></a></div>
        <div><?=$supplier->title?></div>
    </div>
    <div class="cont_switch">
        <ul>
            <li>
                <a href="javascript:"><img src="images/switch_1_pre.gif" /></a>
            </li>
            <li>
                <a href="?s=mendian&a=caiwu&id=<?=$id?>&returnurl=<?=urlencode($request['returnurl'])?>"><img src="images/switch_4.gif" /></a>
            </li>
        </ul>
    </div>
    <!--startprint-->
    <div class="cont">
        <div class="cont_sup_details">
            <div class="cont_h2"> 
                商家信息
            </div>
            <div class="cont_operation_rigth noprint">
                <a href="?s=mendian&a=add_mendian&id=<?=$supplier->id?>" style="vertical-align:middle;"><img src="images/change.gif" />&nbsp;&nbsp;修改</a>
                <a href="javascript:" onclick="doPrint();location.reload();" style="vertical-align:middle;margin-left:20px;"><img src="images/print.gif" />&nbsp;&nbsp;打印</a>
            </div>
            <div class="clearBoth"></div>
            <table width="100%" style="border:1px #d5e4eb solid; border-collapse:collapse;" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="sup_details_td1">直发商名称：</td>
                    <td class="sup_details_td2"><?=$supplier->title?></td>
                    <td class="sup_details_td1">直发商编码：</td>
                    <td class="sup_details_td2"><?=$id?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">区域：</td>
                    <td class="sup_details_td2"><?=$areaName?></td>
                    <td class="sup_details_td1">详细地址：</td>
                    <td class="sup_details_td2"><?=$supplier->address?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">经营品类：</td>
                    <td class="sup_details_td2"><?=$supplier->product_type?></td>
                    <td class="sup_details_td1">创建时间：</td>
                    <td class="sup_details_td2"><?=date("Y-m-d H:i",strtotime($supplier->dtTime))?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">营业执照：</td>
                    <td class="sup_details_td2"><? if(!empty($supplier->img_zhizhao)){?><a href="<?=$supplier->img_zhizhao?>" target="_blank"><img src="<?=$supplier->img_zhizhao?>" id="img_zhizhao_img" width="100"></a><? }?></td>
                    <td class="sup_details_td1">法人身份证：</td>
                    <td class="sup_details_td2"><? if(!empty($supplier->img_shenfenzheng)){?><a href="<?=$supplier->img_shenfenzheng?>" target="_blank"><img src="<?=$supplier->img_shenfenzheng?>" id="img_zhizhao_img" width="100"></a><? }?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">状态：</td>
                    <td class="sup_details_td2"><?
                        if($supplier->status==1){echo '<span style="color:green">启用</span>';}else{
                            echo '<span style="color:red">禁用</span>';
                        }
                    ?></td>
                    <td class="sup_details_td1"></td>
                    <td class="sup_details_td2"></td>
                </tr>
            </table>
            <div class="cont_h2"> 
                个人信息
            </div>
            <table width="100%" style="border:1px #d5e4eb solid; border-collapse:collapse;" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="sup_details_td1">姓名：</td>
                    <td class="sup_details_td2"><?=$supplier->name?></td>
                    <td class="sup_details_td1">手机：</td>
                    <td class="sup_details_td2"><?=$supplier->phone?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">电话：</td>
                    <td class="sup_details_td2"><?=$userInfo['phone1']?></td>
                    <td class="sup_details_td1">E-mail：</td>
                    <td class="sup_details_td2"><?=$userInfo['email']?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">职位：</td>
                    <td class="sup_details_td2"><?=$userInfo['job']?></td>
                    <td class="sup_details_td1">微信</td>
                    <td class="sup_details_td2"><?=$userInfo['weixin']?></td>
                </tr>
            </table>
            <div class="cont_h2"> 
                财务信息
            </div>
            <table width="100%" style="border:1px #d5e4eb solid; border-collapse:collapse;" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="sup_details_td1">开户名称：</td>
                    <td class="sup_details_td2"><?=$caiwu['kaihuming']?></td>
                    <td class="sup_details_td1">开户银行：</td>
                    <td class="sup_details_td2"><?=$caiwu['kaihuhang']?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">银行账户：</td>
                    <td class="sup_details_td2"><?=$caiwu['kaihubank']?></td>
                    <td class="sup_details_td1">发票抬头：</td>
                    <td class="sup_details_td2"><?=$caiwu['taitou']?></td>
                </tr>
                <tr>
                    <td class="sup_details_td1">纳税人识别号：</td>
                    <td class="sup_details_td2"><?=$caiwu['shibie']?></td>
                    <td class="sup_details_td1"></td>
                    <td class="sup_details_td2"></td>
                </tr>
            </table>
        </div>
    </div>
    <!--endprint-->
    <? require('views/help.html');?>
</body>
</html>