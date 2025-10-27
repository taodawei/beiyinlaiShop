<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$id = (int)$request['id'];
$cuxiao = $db->get_row("select * from cuxiao_order where id=$id and comId=$comId");
if(empty($cuxiao)){
    die("促销活动不存在");
}
if(!empty($cuxiao->guizes)){
    $guizes = json_decode($cuxiao->guizes);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div class="right_up">
        <a href="<?=urldecode($request['returnurl'])?>"><img src="images/back.gif"/></a> 订单促销详情
    </div>
    <div class="right_down">
        <div class="yx_shangpincuxiaoxiangqing">
            <div class="yx_shangpincuxiaoxiangqing_01">
                <div class="yx_shangpincuxiaoxiangqing_01_left">
                    <?
                    $startTime = strtotime($cuxiao->startTime);
                    $endTime = strtotime($cuxiao->endTime);
                    $now = time();
                    if($cuxiao->status!=1){
                        echo '已作废';
                    }else{
                        if($now<$startTime){
                            echo '未开始';
                        }else if($now>$endTime){
                            echo '已结束';
                        }else{
                            echo '<font color="green">促销中</font>';
                        }
                    }
                    ?>
                </div>
                <div class="yx_shangpincuxiaoxiangqing_01_right">
                    <a href="?s=yyyx&a=create_order&id=<?=$id?>"><img src="images/yingxiao_26.png"/>复制</a><? if($cuxiao->status==1){?><a href="javascript:" onclick="z_confirm('确定要作废该促销吗？',zuofei,<?=$id?>);"><img src="images/yingxiao_27.png"/>作废</a><? }?>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="yx_shangpincuxiaoxiangqing_02">
                <ul>
                    <li>
                        <div class="yx_shangpincuxiaoxiangqing_02_left">
                            促销主题：
                        </div>
                        <div class="yx_shangpincuxiaoxiangqing_02_right">
                            <?=$cuxiao->title?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_shangpincuxiaoxiangqing_02_left">
                            促销时间：
                        </div>
                        <div class="yx_shangpincuxiaoxiangqing_02_right">
                            <?=date("Y-m-d",strtotime($cuxiao->startTime))?> ~ <?=date("Y-m-d",strtotime($cuxiao->endTime))?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_shangpincuxiaoxiangqing_02_left">
                            促销规则：
                        </div>
                        <div class="yx_shangpincuxiaoxiangqing_02_right">
                            <? 
                            $type1 = '元';
                            $type2 = $cuxiao->type==1?'赠':($cuxiao->type==2?'减':'享');
                            foreach ($guizes as $rule){
                                echo '• 每满<span>'.$rule->man.'</span>'.$type1.$type2.'<span>'.$rule->jian.'</span> ';
                                switch($cuxiao->type){
                                    case 1:
                                        $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
                                        echo $inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
                                    break;
                                    case 2:
                                        echo '元';
                                    break;
                                    case 3:
                                        echo '折';
                                    break;
                                }
                                echo '<br>';
                            }?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_shangpincuxiaoxiangqing_02_left">
                            应用场景：
                        </div>
                        <div class="yx_shangpincuxiaoxiangqing_02_right">
                            <? switch ($cuxiao->scene) {
                                case 1:
                                $scene = '线上商城';
                                break;
                                case 2:
                                $scene = '订货平台';
                                break;
                                case 3:
                                $scene = '线下门店';
                                break;
                            }
                            echo $scene;
                            ?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_shangpincuxiaoxiangqing_02_left">
                            促销对象：
                        </div>
                        <div class="yx_shangpincuxiaoxiangqing_02_right">
                            <? if($cuxiao->scene==2){
                                if(!empty($cuxiao->levelIds1)){
                                    echo $db->get_var("select group_concat(title) from demo_kehu_level where id in($cuxiao->levelIds1)");
                                }else{
                                    echo '全部'.$_SESSION[TB_PREFIX.'kehu_title'];
                                }
                            }else{
                                if(!empty($cuxiao->levelIds)){
                                    echo $db->get_var("select group_concat(title) from user_level where id in($cuxiao->levelIds)");
                                }else{
                                    echo '全部会员';
                                }
                            }?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <? if($cuxiao->scene!=3){?>
                    <li>
                        <div class="yx_shangpincuxiaoxiangqing_02_left">
                            促销区域：
                        </div>
                        <div class="yx_shangpincuxiaoxiangqing_02_right">
                            <?
                            if(!empty($cuxiao->areaIds)){
                                echo $db->get_var("select group_concat(title) from demo_area where id in($cuxiao->areaIds)");
                            }else{
                                echo '全部';
                            }
                            ?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <? 
                    }
                    if($cuxiao->scene==3){?>
                        <li>
                            <div class="yx_shangpincuxiaoxiangqing_02_left">
                                促销门店：
                            </div>
                            <div class="yx_shangpincuxiaoxiangqing_02_right">
                                <?
                                if(!empty($cuxiao->mendianIds)){
                                    echo $db->get_var("select group_concat(title) from mendian where id in($cuxiao->mendianIds)");
                                }else{
                                    echo '全部门店';
                                }
                                ?>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    <? }?>
                </ul>
            </div>
            <div class="yx_shangpincuxiaoxiangqing_03">
                <a href="<?=urldecode($request['returnurl'])?>">返 回</a>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function zuofei(param){
            layer.load();
            ajaxpost=$.ajax({
                type: "POST",
                url: "?s=yyyx&a=piliang_zuofei_order",
                data: "&ids="+param,
                dataType:"json",timeout : 10000,
                success: function(resdata){
                    layer.closeAll('loading');
                    if(resdata.code==0){
                        layer.msg(resdata.message,{icon: 5});
                    }else{
                        location.reload();
                    }
                },
                error: function() {
                    layer.closeAll();
                    layer.msg('数据请求失败', {icon: 5});
                }
            });
        }
    </script>
<? require('views/help.html');?>
</body>
</html>