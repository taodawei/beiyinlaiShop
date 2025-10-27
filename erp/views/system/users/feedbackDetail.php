<?
global $db,$request;
$id = (int)$request['id'];
$feedback = $db->get_row("select * from feedback_log where id=$id");

if($feedback->status == 0){
    $feedback->statusInfo = '<span style="color:red">未处理</span>';
}else{
    $feedback->statusInfo = '<span style="color:green">已处理</span>';
}

// if($feedback->read == 0){
//     $feedback->readInfo = '<span style="color:red">未读</span>';
// }else{
//     $feedback->readInfo = '<span style="color:green">已读</span>';
// }

$userInfo = $db->get_row("select * from users where id= $feedback->userId");
if(!$userInfo->nickname){
    $userInfo->nickname = $userInfo->phone;
}
$image = [];
if($feedback->originalPic){
    $image = explode('|', $feedback->originalPic);
}

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
        // layui.use(['layer'], function(){
        //     <? if(empty($supplier)){?>
        //         layer.confirm('商家不存在或已删除',{
        //           btn: ['确定'],
        //         }, function(){
        //             location.href='?m=system&s=supplier';
        //         });
        //     <? }?>
        // });
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
            <!--<li>-->
            <!--    <a href="?s=mendian&a=caiwu&id=<?=$id?>&returnurl=<?=urlencode($request['returnurl'])?>"><img src="images/switch_4.gif" /></a>-->
            <!--</li>-->
        </ul>
    </div>
    <!--startprint-->
    <div class="cont">
        <div class="cont_sup_details">
            <div class="cont_h2"> 
                反馈详情
            </div>
         
            <div class="clearBoth"></div>
            <table width="100%" style="border:1px #d5e4eb solid; border-collapse:collapse;" cellpadding="0" cellspacing="0">
               <tr>
                    <td class="sup_details_td1">姓名：</td>
                   
                    <td class="sup_details_td2"><?=$feedback->name?></td>
                        
                    <td class="sup_details_td1">手机号：</td>
                    <td class="sup_details_td2"><?=$feedback->phone?></td>
                </tr>
               
                <tr>
                    <td class="sup_details_td1">内容：</td>
                   
                    <td class="sup_details_td2"><?=$feedback->content?></td>
                        
                    <td class="sup_details_td1">创建时间：</td>
                    <td class="sup_details_td2"><?=date("Y-m-d H:i",strtotime($feedback->dtTime))?></td>
                </tr>
               <?foreach($image as $k => $i){ ?>
              
                    <tr>
                        <td class="sup_details_td1">图片<?=$k+1?>：</td>
                        <td class="sup_details_td2"><? if(!empty($i)){?><a href="<?=$i?>" target="_blank"><img src="<?=$i?>" width="100"></a><? }?></td>
                    </tr>                   
                <? }?>
                
                 <tr>
                    <!--<td class="sup_details_td1">是否已经处理：</td>-->
                   
                    <!--<td class="sup_details_td2"><?=$feedback->statusInfo?></td>-->
                        
                    <td class="sup_details_td1">备注：</td>
                    <td class="sup_details_td2"><?=$feedback->reply?></td>
                </tr>
               
                <!-- <tr>-->
                <!--    <td class="sup_details_td1">是否已读：</td>-->
                   
                <!--    <td class="sup_details_td2"><?=$feedback->readInfo?></td>-->
                        
                <!--    <td class="sup_details_td1">回复时间：</td>-->
                <!--    <td class="sup_details_td2"><?=$feedback->reply_at?></td>-->
                <!--</tr>-->
               
            </table>
            <div class="cont_h2"> 
                个人信息
            </div>
            <table width="100%" style="border:1px #d5e4eb solid; border-collapse:collapse;" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="sup_details_td1">手机号：</td>
                    <td class="sup_details_td2"><?=$feedback->phone?></td>
                    <!--<td class="sup_details_td1">手机：</td>-->
                    <!--<td class="sup_details_td2"><?=$userInfo->phone?></td>-->
                </tr>
                <!--<tr>-->
                <!--    <td class="sup_details_td1">电话：</td>-->
                <!--    <td class="sup_details_td2"><?=$userInfo['phone1']?></td>-->
                <!--    <td class="sup_details_td1">E-mail：</td>-->
                <!--    <td class="sup_details_td2"><?=$userInfo['email']?></td>-->
                <!--</tr>-->
                <!--<tr>-->
                <!--    <td class="sup_details_td1">职位：</td>-->
                <!--    <td class="sup_details_td2"><?=$userInfo['job']?></td>-->
                <!--    <td class="sup_details_td1">微信</td>-->
                <!--    <td class="sup_details_td2"><?=$userInfo['weixin']?></td>-->
                <!--</tr>-->
            </table>
         
        </div>
    </div>
    <!--endprint-->
    <? require('views/help.html');?>
</body>
</html>