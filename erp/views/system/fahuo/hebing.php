<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dianzimiandan.css" rel="stylesheet" type="text/css">
    <link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <style type="text/css">
        .dqdd_kehebingdd_3_up #tijiao{    
            display: inline-block;
            width: 105px;
            height: 30px;
            background-color: #106c99;
            border-radius: 5px;
            text-align: center;
            line-height: 30px;
            color: #fff;
            border: 0px;
        }
    </style>
</head>
<body>
<? require('views/system/fahuo/header.php')?>
<div id="content">
    <div class="content1">
        <div class="content_1">
            可合并订单
        </div>
        <div class="content_2">
            <div class="splist_up_01" style="position: static;">
                <div class="splist_up_01_right">
                    <div class="splist_up_01_right_1">
                        <form method="post" id="forms" action="?s=fahuo&a=hebing">
                        <div class="splist_up_01_right_1_left">
                            <input type="number" id="keyword" name="keyword" value="<?=$request['keyword']?>" placeholder="重量（kg）只能输入数字"/>
                        </div>
                        <div class="splist_up_01_right_1_right">
                            <a href="javascript:$('#forms').submit();"><img src="images/biao_21.gif"/></a>
                        </div>
                        </form>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="clearBoth"></div>
            <div class="dangqiandd">
                <div class="dangqiandd_3">
                    <div class="dqdd_kehebingdd">
                        <?
                            global $db,$request,$tag;
                            $mendianId = $_SESSION[TB_PREFIX.'mendianId'];
                            $sql = "select addressId,group_concat(id) as fahuoIds,count(*) as num,sum(weight) as weight from order_fahuo0 where mendianId=$mendianId and status=1 and is_hebing=0 and if_yushou=0";
                            /*if($_SESSION[TB_PREFIX.'admin_roleId']<7){
                                $sql.=" and storeId in(".$_SESSION[TB_PREFIX.'quanxian']['kucun']['storeIds'].")";
                            }*/
                            $sql .=" group by addressId having count(*)>1";
                            if($request['keyword']){
                                $sql .=" and weight<=".$request['keyword'];
                            }
                            //$res = $db->get_results($sql);
                            //$sb = new sqlbuilder('list',$sql,'id desc',$db,3);
                            $sb = new sqlbuilders('list',$sql,'id desc',$db,3);
                        ?>
                        <?
                            if(!empty($sb->results)){
                                foreach ($sb->results as $k => $v) {$i++;
                                    $addr = $db->get_row("select * from user_address where id=".$v['addressId']);
                                    ?>
                                    <form method="post" id="form<? echo $i;?>" action="?s=fahuo">
                                    <input type="hidden" id="fun<? echo $i;?>" name="a" value="bingdan">
                                    <div class="dqdd_kehebingdd_3">
                                        <div class="dqdd_kehebingdd_3_up" style="padding-left: 15px;">
                                            <span style="max-width: 650px;display: inline-block; overflow-x: scroll;height: 47px;float: left;white-space: nowrap;"><input type="checkbox" id="quanxian<? echo $i;?>" onclick="select_all(<? echo $i;?>)">&nbsp;&nbsp;收货人信息：姓名：<? echo $addr->name;?>　电话：<? echo $addr->phone;?> 收货地址：<? echo $addr->areaname;?><? echo $addr->address;?>   数量:<? echo $v['num']?>    商品总重量 ：<? echo $v['weight']?>kｇ</span>&nbsp;<input id="tijiao" type="submit" value="合并发货">&nbsp;&nbsp;<input id="tijiao" type="button" onclick="quxiao_hebing('quxiao_hebing',<? echo $i;?>);" value="不合并">
                                        </div>
                                        <div class="dqdd_kehebingdd_3_down">
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <?
                                                    $fahuos = $db->get_results("select * from order_fahuo0 where id in (".$v['fahuoIds'].")");
                                                    if(!empty($fahuos)){
                                                        foreach ($fahuos as $fahuo) {
                                                            ?>
                                                            <tr height="43">
                                                                <td bgcolor="#ffffff" width="40" align="center" valign="middle" class="table_line">
                                                                    <input type="checkbox" class="quanxuan<? echo $i;?>" value="<? echo $fahuo->id;?>" name="hebing[]">
                                                                </td>
                                                                <td bgcolor="#ffffff" width="222" align="left" valign="middle" class="table_line">
                                                                    <div style="color:#0e87c3;"><? echo $fahuo->orderId;?></div>      
                                                                </td>
                                                                <td bgcolor="#ffffff" width="322" align="left" valign="middle" class="table_line">
                                                                    <? echo $fahuo->product_title;?>
                                                                </td>
                                                                <td bgcolor="#ffffff" width="250" align="left" valign="middle" class="table_line">
                                                                    <? echo $fahuo->weight;?>kg
                                                                </td>
                                                                <td bgcolor="#ffffff" width="222" align="left" valign="middle" class="table_line">
                                                                    <? echo $fahuo->dtTime;?> 
                                                                </td>
                                                            </tr>
                                                            <?
                                                        }
                                                    }
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                    </form>
                                    <?
                                }
                            }
                        ?>
                    </div>
                    <style type="text/css">
                        .pager a{ font-size: 14px; border: 1px solid #ccc; padding: 5px; }
                        .pager span{ font-size: 14px; border: 1px solid #ccc; padding: 5px; }
                    </style>
                     <div class="zzp-right" style="margin-top: 20px;">
                      <ul class="pager" >
                        <? echo $sb->get_pager_show();?>
                      </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
  $("#firstBtn").attr("href",$("#firstBtn").attr("href")+"&keyword="+$("#keyword").val()+"&status="+$("#status").val()+"&ispay="+$("#ispay").val());
  $("#prevBtn").attr("href",$("#prevBtn").attr("href")+"&keyword="+$("#keyword").val()+"&status="+$("#status").val()+"&ispay="+$("#ispay").val());
  $("#nextBtn").attr("href",$("#nextBtn").attr("href")+"&keyword="+$("#keyword").val()+"&status="+$("#status").val()+"&ispay="+$("#ispay").val());
  $("#lastBtn").attr("href",$("#lastBtn").attr("href")+"&keyword="+$("#keyword").val()+"&status="+$("#status").val()+"&ispay="+$("#ispay").val());
  //$("#jumpBtn").attr("href",$("#lastBtn").attr("href")+"&searchTime="+$("#searchTime").val()+"&searchZt="+$("#searchZt").val());
})
</script>
<script type="text/javascript">
    function quxiao_hebing(funs,num){
        $("#fun"+num).val(funs);
        $("#form"+num).submit();
    }
    function select_all(num){
        if($(".quanxuan"+num).is(':checked')){
            $(".quanxuan"+num).prop('checked',false);
        }else{
            $(".quanxuan"+num).prop('checked',true);
        }
    }
</script>
</body>
</html>