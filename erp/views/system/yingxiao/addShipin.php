<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$originalPics = array();
if(!empty($request['id'])){
    $shipin = $db->get_row("select * from demo_faxian where id=".(int)$request['id']);
    $originalPics = explode('|',$shipin->originalPic);
}
$nums = $db->get_results("select count(*) as num,status from demo_faxian where shopId=$comId group by status");
$weishenhe = 0;
$yishenhe = 0;
if(!empty($nums)){
    foreach ($nums as $n){
        if($n->status==0){
            $weishenhe = $n->num;
        }else{
            $yishenhe = $n->num;
        }
    }
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
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/spgl.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="styles/yingxiaoguanli.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript" src="/keditor/kindeditor1.js"></script>
</head>
<body>
    <div class="root">
        <div class="video">
            <div class="video_h">
                发现
            </div>
            <div class="video_fenlei">
                <ul>
                    <li class="video_fenlei_pre">
                        <a href="?s=yingxiao&a=addShipin">新建</a>
                    </li>
                    <li>
                        <a href="?s=yingxiao&a=shipin&status=2">未审核(<font color="red" id="wei_num"><?=$weishenhe?></font>)</a>
                    </li>
                    <li>
                        <a href="?s=yingxiao&a=shipin&status=1">已审核(<font color="red" id="yi_num"><?=$yishenhe?></font>)</a>
                    </li>
                </ul>
                <div class="clearBoth"></div>
            </div>
        </div>
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=yingxiao&a=addShipin&tijiao=1&id=<?=$shipin->id?>" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$shipin->title?>" lay-verify="required" placeholder="请输入广告标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 简介
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="remark" value="<?=$shipin->remark?>" placeholder="分享时显示" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="edit_photo" style="margin-left:50px;">
                            <div class="photo_tt" style="color:#333">
                                图片<span style="color:#9b9b9b">(说明：图片需处理成大小比例1:1，不超过1M)</span>
                            </div>
                            <div class="photo_tu">
                                <ul>
                                    <?
                                    if(!empty($originalPics)){
                                        $i=0;
                                        foreach ($originalPics as $originalPic){
                                            $i++;
                                            ?>
                                            <li id="image_li<?=$i?>"><a><img src="<?=$originalPic?>?x-oss-process=image/resize,w_122" width="122" height="122"></a><div class="close-modal small js-remove-sku-atom" onclick="del_image(<?=$i?>);">×</div></li>
                                            <?
                                        }
                                    }
                                    ?>
                                    <li id="uploadImages" data-num="<?=count($originalPics)?>" style="position:relative;">
                                        <img src="images/photo1.jpg" width="136" height="136" />
                                        <input type="file" name="file" id="uploadPdtImage" multiple="true">
                                    </li>
                                    <div class="clearBoth"></div>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <? if(empty($shipin)){?>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            上传视频
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="button" onclick="showBanner();" id="upload_btn" value="上传视频" style="background-color:#FFFFFF; width:82px; height:26px; color:#333333; border:none; border:1px #cccccc solid; font-size:12px; cursor:pointer;">
                            请上传mp4格式视频，大小不超过25M
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <? }else{?>
                        <li>
                            <video src="<?=$shipin->shipin?>" controls="controls" preload="none" width="412" height="240" style="margin-left:40px;"></video>
                            <div class="clearBoth"></div>
                        </li>
                    <?}?>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            相关商品
                        </div>
                        <div class="sprukuadd_03" style="margin-left:50px;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" id="dataTable" rows="<?=empty($shipin->pdtIds)?'1':count(explode(',',$shipin->pdtIds))?> 1">
                                <tr height="43">
                                    <td bgcolor="#7bc8ed" width="70" class="sprukuadd_03_title" align="center" valign="middle"></td>
                                    <td bgcolor="#7bc8ed" width="118" class="sprukuadd_03_title" align="center" valign="middle"></td>
                                    <td bgcolor="#7bc8ed" width="166" class="sprukuadd_03_title" align="center" valign="middle">商品编码</td>
                                    <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">商品名称</td>
                                    <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">规格</td>
                                    <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle">单位</td>
                                </tr>
                                <? if(empty($shipin->pdtIds)){?>
                                    <tr height="48" id="rowTr1">
                                        <td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"> 1</td>
                                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">
                                            <a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow(1);"><img src="images/biao_66.png"/></a>  
                                        </td>
                                        <td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">
                                            <div class="sprukuadd_03_tt_addsp">
                                                <div class="sprukuadd_03_tt_addsp_left">
                                                    <input type="text" class="layui-input addRowtr" id="searchInput1" row="1" placeholder="输入编码/商品名称" >
                                                </div>
                                                <div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">
                                                   ●●●
                                                </div>
                                                <div class="clearBoth"></div>
                                                <div class="sprukuadd_03_tt_addsp_erji" id="pdtList1">
                                                    <ul><li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li></ul>
                                                </div>
                                            </div>
                                        </td>
                                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                                    </tr>
                                <? }else{
                                    $pdtIds = explode(',',$shipin->pdtIds);
                                    foreach ($pdtIds as $key => $pdtId){
                                        $inventory = $db->get_row("select id,title,sn,key_vals,productId from demo_product_inventory where id=$pdtId");
                                        $units = $db->get_var("select untis from demo_product where id=$inventory->productId");
                                        $unitarr = json_decode($units);
                                        ?>
                                        <tr height="48" id="rowTr<?=$key+1?>">
                                            <td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                                            <td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle"><a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"></a>  <a href="javascript:" onclick="delRow(<?=$key+1?>);"><img src="/erp/images/biao_66.png"></a></td>
                                            <td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle"><?=$inventory->sn?></td>
                                            <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><?=$inventory->title?></td>
                                            <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><?=$inventory->key_vals?></td>
                                            <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><?=$unitarr[0]->title?><input type="hidden" name="inventoryId[<?=$key+1?>]" value="<?=$pdtId?>"></td>
                                        </tr>
                                        <?
                                    }
                                }?>
                            </table>
                            <script type="text/javascript">
                                var jishiqi;
                                var kehu_title = '<?=$kehu_title?>';
                                $('#searchInput1').bind('input propertychange', function() {
                                    clearTimeout(jishiqi);
                                    var row = $(this).attr('row');
                                    var val = $(this).val();
                                    jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
                                });
                                $('#searchInput1').click(function(eve){
                                    var startTime = $("#startTime").val();
                                    var endTime = $("#endTime").val();
                                    if(startTime==''||endTime==''){
                                        layer.msg("请先选择促销时间");
                                        return false;
                                    }
                                    var nowRow = $(this).attr("row");
                                    if($("#pdtList"+nowRow).css("display")=="none"){
                                        showpdtList(nowRow,$(this).val());
                                    }
                                    stopPropagation(eve);
                                });
                           </script>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            详细内容
                        </div>
                        <div class="yx_guanggaoadd_01_right" style="margin-left:40px;">
                            <?php
                                ewebeditor(EDITORSTYLE,'content',$shipin->content);
                            ?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="originalPic" value="<?=$shipin->originalPic?>" id="originalPic">
            <input type="hidden" name="shipin" id="url" value="<?=empty($shipin->shipin)?'':$shipin->shipin?>">
            </form>
        </div>
    </div>
<div class="bj1" id="bj1"></div>
<div class="shangchuanwenjian_tk" id="shangchuanwenjian_tk" style="display:none;">
    <div class="shangchuanwenjian_tk_01">
        <div class="shangchuanwenjian_tk_01_left">
            上传视频
        </div>
        <div class="shangchuanwenjian_tk_01_right" onclick="$('#bj1').hide();$('#shangchuanwenjian_tk').hide();">
            <a href="javascript:"><img src="images/guanbi.png"></a>
        </div>
        <div class="clearBoth"></div>
    </div>
    <input id="upload_type" name="upload_type" value="1" type="hidden">
    <div class="shangchuanwenjian_tk_04" id="container" style="position: relative;">
        <a class="shangchuanwenjian_tk_04_left" id="selectfiles" style="position:relative;z-index:1;line-height:30px;" onmouseover="layer.tips('非H.264编码的MP4文件在浏览器中会出现只有声音没有图像的问题，请上传前检查好',this,{tips: [1,'#666'],time:0});" onmouseout="layer.closeAll('tips');">
            选择视频文件<span style="font-size:10px;color:#f09306;font-family:'微软雅黑','宋体';">(请上传H.264编码且小于25M的MP4文件)</span>
        </a>
        <div class="shangchuanwenjian_tk_04_right">
            <div class="shangchuanwenjian_tk_06">
                <pre id="console" style="color:red"></pre>
                <a id="postfiles" href="javascript:void(0);">上 传</a>
            </div>
        </div>
        <div class="clearBoth"></div>
        <div id="html5_1cmhaeoj219njdr1sr7lg6dvt3_container" class="moxie-shim moxie-shim-html5" style="position: absolute; top: 0px; left: 0px; width: 0px; height: 0px; overflow: hidden; z-index: 0;"><input id="html5_1cmhaeoj219njdr1sr7lg6dvt3" type="file" style="font-size: 999px; opacity: 0; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;" multiple="" accept="video/mp4"></div>
    </div>
    <div id="ossfile" class="shangchuanwenjian_tk_03"></div>
</div>
<div class="sprkadd_xuanzesp">
    <div class="sprkadd_xuanzesp_01">
        <div class="sprkadd_xuanzesp_01_1">选择商品</div>
        <div class="sprkadd_xuanzesp_01_3">
            <div class="sprkadd_xuanzesp_01_3_left">
                <input type="text" id="keyword" placeholder="请输入商品名称/编码/规格/关键字">
            </div>
            <div class="sprkadd_xuanzesp_01_3_right">
               <a href="javascript:reloadTable(0);"><img src="images/biao_21.gif"></a>
           </div>
           <div class="clearBoth"></div>
       </div>
       <div class="clearBoth"></div>
    </div>
    <div class="sprkadd_xuanzesp_02">
        <table id="product_list" lay-filter="product_list"></table>
    </div>
    <div class="sprkadd_xuanzesp_03">
       <a href="javascript:" id="sprkadd_xuanzesp_03_01" class="sprkadd_xuanzesp_03_01">确  认</a><a href="javascript:hideSearch();" class="sprkadd_xuanzesp_03_02">取  消</a>
    </div>
</div>
<script type="text/javascript" src="file/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="js/yingxiao/addShipin.js?v=1"></script>
</body>
</html>