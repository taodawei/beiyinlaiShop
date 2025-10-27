var productListTalbe;
var productListForm;
layui.use(['form','upload','table'], function(){
    form = layui.form,
    upload = layui.upload,
    table = layui.table,
    active = { //产品多选后的渲染
            appendCheckData: function(){
              var checkStatus = table.checkStatus('product_list')
              ,data = checkStatus.data;
              if(data.length>0){
               var num = parseInt($("#dataTable").attr("rows"));
               var rownums = $("#dataTable tr").length;
               $("#dataTable tr").eq(rownums-1).remove();
               for (var i = 0; i < data.length; i++) {
                   var inventoryId = data[i].id;
                   var sn = data[i].sn;
                   var title = data[i].title;
                   var key_vals = data[i].key_vals;
                   var units = data[i].units;
                   var productId = data[i].productId;
                   num = num+1;
                   var str = '<tr height="48" id="rowTr'+num+'"><td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                   '<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
                   '<a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="/erp/images/biao_66.png"/></a> '+ 
                   '</td>'+
                   '<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
                   '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
                   '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
                   '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+
                   '<input type="hidden" name="inventoryId['+num+']" value="'+inventoryId+'">'+
                   '<input type="hidden" name="inventorySn['+num+']" value="'+sn+'">'+
                   '<input type="hidden" name="inventoryTitle['+num+']" value="'+title+'">'+
                   '<input type="hidden" name="inventoryKey_vals['+num+']" value="'+key_vals+'">'+
                   '<input type="hidden" name="inventoryPdtId['+num+']" value="'+productId+'">'+
                   '</td></tr>';
                   $("#dataTable").append(str);
                }
                $("#dataTable").attr("rows",num);
                addRow();
                hideSearch();
            }else{
                hideSearch();
            }
        }
    }
    upload.render({
        elem: '#uploadPdtImage'
        ,size:1024
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
          layer.closeAll('loading');
          if(res.code > 0){
            return layer.msg(res.msg);
          }else{
            $('#uploadImages img').prop("src",res.url);
            $("#originalPic").val(res.url);
          }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-250"
        ,url: '?m=system&s=caigou&a=getpdts&cuxiao=1'
        ,page: true
        ,cols: [[{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:"display:none;"},{field: 'productId', title: 'productId', width:0,style:"display:none;"},{field:'sn',title:'商品编码',width:150},{field:'title',title:'商品名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'units',title:'单位',width:175}]]
        ,done: function(res, curr, count){
            $("th[data-field='id']").hide();
            $("th[data-field='productId']").hide();
            $("#page").val(curr);
            layer.closeAll('loading');
        }
    });
    $("#sprkadd_xuanzesp_03_01").on("click", function(){
        active['appendCheckData'].call(this);
    });
    form.on('submit(tijiao)', function(data){
        layer.load();
    });
});
function del_image(id){
    layer.load();
    var img = $("#image_li"+id+" img").eq(0).attr("src");
    $("#image_li"+id).remove();
    img = img.replace('?x-oss-process=image/resize,w_122','');
    var originalPic = $("#originalPic").val();
    pics = originalPic.split('|');
    for (var i = 0; i < pics.length; i++) {  
        if (pics[i] == img){
            pics.splice(i,1);
            break;
        }
    }
    originalPic = pics.join("|");
    $("#originalPic").val(originalPic);
    $.ajax({
        type: "POST",
        url: "?m=system&s=upload&a=delImg",
        data: "img="+img,
        dataType:'text',timeout : 5000,
        success: function(resdata){
            layer.closeAll('loading');
        },
        error: function() {
            layer.closeAll('loading');
        }
    });
}
function hideSearch(){
    $('.sprkadd_xuanzesp').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function showBanner(){
    $("#bj1").show();
    $("#shangchuanwenjian_tk").show();
}
accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
callbackbody = ''
filename = ''
key = ''
expire = 0
g_object_name = ''
g_object_name_type = ''
now = timestamp = Date.parse(new Date()) / 1000; 

function send_request()
{
    var xmlhttp = null;
    if (window.XMLHttpRequest)
    {
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp!=null)
    {
        serverUrl = './file/php/get.php'
        xmlhttp.open( "GET", serverUrl, false );
        xmlhttp.send( null );
        return xmlhttp.responseText
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
};

function check_object_radio() {
    g_object_name_type = 'random_name';//'local_name';
}

function get_signature()
{
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000; 
    if (expire < now + 3)
    {
        body = send_request()
        var obj = eval ("(" + body + ")");
        host = obj['host']
        policyBase64 = obj['policy']
        accessid = obj['accessid']
        signature = obj['signature']
        expire = parseInt(obj['expire'])
        callbackbody = obj['callback'] 
        key = obj['dir']
        return true;
    }
    return false;
};

function random_string(len) {
    　　len = len || 32;
    　　var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';   
    　　var maxPos = chars.length;
    　　var pwd = '';
    　　for (i = 0; i < len; i++) {
        　　pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    pos = filename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}

function calculate_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        g_object_name += "${filename}"
    }
    else if (g_object_name_type == 'random_name')
    {
        suffix = get_suffix(filename)
        g_object_name = key + random_string(18) + suffix
    }
    return ''
}

function get_uploaded_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        tmp_name = g_object_name
        tmp_name = tmp_name.replace("${filename}", filename);
        return tmp_name
    }
    else if(g_object_name_type == 'random_name')
    {
        return g_object_name
    }
}

function set_upload_param(up, filename, ret)
{
    if (ret == false)
    {
        ret = get_signature()
    }
    g_object_name = key;
    if (filename != '') { suffix = get_suffix(filename)
        calculate_object_name(filename)
    }
    new_multipart_params = {
        'key' : g_object_name,
        'policy': policyBase64,
        'OSSAccessKeyId': accessid, 
        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
        'callback' : callbackbody,
        'signature': signature,
    };

    up.setOption({
        'url': host,
        'multipart_params': new_multipart_params
    });

    up.start();
    
}

var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'selectfiles', 
    //multi_selection: false,
    container: document.getElementById('container'),
    flash_swf_url : 'lib/plupload-2.1.2/js/Moxie.swf',
    silverlight_xap_url : 'lib/plupload-2.1.2/js/Moxie.xap',
    url : 'http://oss.aliyuncs.com',
    
    filters: {
        mime_types : [ //只允许上传图片和zip文件
        //{ title : "Image files", extensions : "jpg,gif,png,bmp" }, 
        //{ title : "Zip files", extensions : "zip,rar" },
        { title : "Other files", extensions : "mp4" }
        ],
        max_file_size : '25mb', //最大只能上传10mb的文件
        //prevent_duplicates : true //不允许选取重复文件
    },

    init: {
      PostInit: function() {
       document.getElementById('ossfile').innerHTML = '';
       document.getElementById('postfiles').onclick = function() {
        if(document.getElementById('ossfile').innerHTML==""){
            layer.msg("请添加文件！", {icon: 5});
        }else{
            set_upload_param(uploader, '', false);
            return false;
        }
    };
},

FilesAdded: function(up, files) {
   plupload.each(files, function(file) {
    var suffix=get_suffix(file.name);
    if (suffix== ".gif" || suffix== ".jpg" || suffix== ".png" || suffix== ".jpeg" || suffix== ".bmp") {
        var src="images3/06.png";
    }else if(suffix== ".zip" || suffix== ".rar"){
        var src="images3/09.png";
    }else if(suffix== ".txt"){
        var src="images3/01.png";
    }else if(suffix== ".pdf"){
        var src="images3/02.png";
    }else if(suffix== ".ppt"){
        var src="images3/04.png";
    }else if(suffix== ".doc" || suffix== ".docx"){
        var src="images3/03.png";
    }else if(suffix== ".xlsx" || suffix== ".xls"){
        var src="images3/05.png";
    }else if(suffix== ".mp3"){
        var src="images3/07.png";
    }else{
        var src="images3/08.png";
    }
    var upload_type=document.getElementById('upload_type').value;
    if(upload_type==1){
        document.getElementById('ossfile').innerHTML = '<div class="progress_title" id="' + file.id + '">' +'<span class="progress_text"><img src="'+src+'"/> ' + file.name +'</span>'+ ' <span class="progress_daxiao">' + plupload.formatSize(file.size) + '</span><b></b>'
        +'<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
        +'</div>';
    }else{
        document.getElementById('ossfile').innerHTML = '<div class="progress_title" id="' + file.id + '" style="margin-bottom:0;margin-top:5px;">' +'<span class="progress_text" style="width:90%"><img src="'+src+'"/> ' + file.name +'</span>'
        +'</div>';
    }

});
},

BeforeUpload: function(up, file) {
    check_object_radio();
    set_upload_param(up, file.name, true);
},

UploadProgress: function(up, file) {
   var d = document.getElementById(file.id);
            var upload_type=document.getElementById('upload_type').value;//1:个人文件上传；2：公司文件更新
            if(upload_type==1){
                d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                var prog = d.getElementsByTagName('div')[0];
                var progBar = prog.getElementsByTagName('div')[0]
                progBar.style.width= 5.68*file.percent+'px';
                progBar.setAttribute('aria-valuenow', file.percent);
            }

        },

        FileUploaded: function(up, file, info) {
            if (info.status == 200)
            {
                var upload_type=document.getElementById('upload_type').value;//1:个人文件上传；2：公司文件更新
                if(upload_type==1){
                    var oss_fileName=get_uploaded_object_name(file.name);
                    $.ajax({
                        type:"POST",
                        url:"?f=yingxiao&a=upload_shipin",
                        data:"oss_fileId="+file.id+"&oss_fileName="+oss_fileName+"&file_parentId="+$("#file_parentId").val()+"&file_name="+file.name+"&file_size="+plupload.formatSize(file.size)+"&file_suffix="+get_suffix(file.name),
                        timeout:"4000",
                        dataType:"json",
                        success: function(html){
                            if(html.code==0){
                                layer.msg(html.message, {icon: 5});
                            }else{
                                layer.msg("上传成功", {icon: 6});
                                $("#bj1").hide();
                                $("#shangchuanwenjian_tk").hide();
                                $("#url").val(html.file_url);
                            }
                        },
                        // error:function(){
                        //     alert("超时,请重试");
                        // }
                        error:function(XMLHttpRequest, textStatus, errorThrown){
                          console.log(XMLHttpRequest);
                          console.log(textStatus);
                          console.log(errorThrown);
                           // alert("超时,请重试");
                       }
                   });
                }
            }
            else if (info.status == 203)
            {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '上传到OSS成功，但是oss访问用户设置的上传回调服务器失败，失败原因是:' + info.response;
            }
            else
            {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            } 
        },

        Error: function(up, err) {
            if (err.code == -600) {
                //document.getElementById('console').appendChild(document.createTextNode("\n选择的文件太大了,可以根据应用情况，在upload.js 设置一下上传的最大大小"));
                layer.msg("文件的大小不可以超过25M", {icon: 5});
                //document.getElementById('console').appendChild(document.createTextNode("\n文件的大小不可以超过10M"));
            }
            else if (err.code == -601) {
                //document.getElementById('console').appendChild(document.createTextNode("\n选择的文件后缀不对,可以根据应用情况，在upload.js进行设置可允许的上传文件类型"));
                layer.msg("不支持该文件类型", {icon: 5});
                //document.getElementById('console').appendChild(document.createTextNode("\n不支持该文件类型"));
            }
            else if (err.code == -602) {
                layer.msg("这个文件已经上传过一遍了", {icon: 5});
                //document.getElementById('console').appendChild(document.createTextNode("\n这个文件已经上传过一遍了"));
            }
            else 
            {
                layer.msg("系统错误，请刷新重试", {icon: 5});
                console.log(err);
                //document.getElementById('console').appendChild(document.createTextNode("\n系统错误，请刷新重试" + err.code));
            }
        }
    }
});
uploader.init();

function reloadTable(curpage){
    layer.load();
    var channelId = $("#channelId").val();
    var keyword = $("#keyword").val();
    var kehuId = $("#kehuId").val();
    var startTime = $("#startTime").val();
    var endTime = $("#endTime").val();
    var hasIds = '0';
    $("input[name^='inventoryId[']").each(function(){
        hasIds+=','+$(this).val();
    });
    var page = 1;
    productListTalbe.reload({
        where: {
            channelId:channelId
            ,keyword:keyword
            ,hasIds:hasIds
            ,kehuId:kehuId
            ,startTime:startTime
            ,endTime:endTime
        },page: {
            curr: page
        }
    });
}
//添加新行
function addRow(){
    var num = parseInt($("#dataTable").attr("rows"));
    num = num+1;
    var str='<tr height="48" id="rowTr'+num+'">'+
                '<td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
                    '<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a>'+ 
                '</td>'+
                '<td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">'+
                    '<div class="sprukuadd_03_tt_addsp">'+
                        '<div class="sprukuadd_03_tt_addsp_left">'+
                            '<input type="text" class="layui-input addRowtr" id="searchInput'+num+'" row="'+num+'" placeholder="输入编码/商品名称">'+
                        '</div>'+
                        '<div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">'+
                            '●●●'+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
                        '<div class="sprukuadd_03_tt_addsp_erji" id="pdtList'+num+'">'+
                            '<ul>'+
                                '<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>'+
                            '</ul>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
            '</tr>';
    $("#dataTable").append(str).attr("rows",num);
    $('#searchInput'+num).bind('input propertychange', function() {
        clearTimeout(jishiqi);
        var row = $(this).attr('row');
        var val = $(this).val();
        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
    });
    $('#searchInput'+num).click(function(eve){
        var kehuId = $("#kehuId").val();
        if(kehuId==''||kehuId==0){
            layer.msg('请先选择'+kehu_title+'！',function(){});
            return false;
        }
        var nowRow = $(this).attr("row");
        if($("#pdtList"+nowRow).css("display")=="none"){
            showpdtList(nowRow,$(this).val());
        }
        stopPropagation(eve); 
    });
}
function delRow(nowId){
    if($("#dataTable tr").length<3){
        layer.msg("请至少保留一个产品",function(){});
        return false;
    }
    $("#rowTr"+nowId).remove();
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,price){
    var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
    '<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
        '<a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="/erp/images/biao_66.png"/></a>'+ 
    '</td>'+
    '<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
    '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
    '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
    '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+units+
    '<input type="hidden" name="inventoryId['+id+']" value="'+inventoryId+'">'+
    '<input type="hidden" name="inventoryPdtId['+id+']" value="'+productId+'">'+
    '</td>';
    $("#rowTr"+id).html(str);
    addRow();
}
//输入获取产品列表
function getPdtInfo(id,keyword){
    $("#pdtList"+id+" ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
    var hasIds = '0';
    $("input[name^='inventoryId[']").each(function(){
        hasIds+=','+$(this).val();
    });
    $.ajax({
        type: "POST",
        url: "/erp_service.php?action=getGonghuoList&id="+id,
        data: "keyword="+keyword+"&hasIds="+hasIds+"&cuxiao=1",
        dataType:'text',timeout : 10000,
        success: function(resdata){
            $("#pdtList"+id+" ul").html(resdata);
        }
    });
}
function showpdtList(id,keyword){
    $("#pdtList"+id).show();
    getPdtInfo(id,keyword);
}
function hidePdtList(id,keyword){
    $("#pdtList"+id).hide();
}
//显示所有产品列表
function showAllpdts(){
    var startTime = $("#startTime").val();
    var endTime = $("#endTime").val();
    if(startTime==''||endTime==''){
        layer.msg("请先选择促销时间");
        return false;
    }
    var kehuId = $("#kehuId").val();
    if(kehuId==''||kehuId==0){
        layer.msg('请先选择'+kehu_title+'！',function(){});
        return false;
    }
    $('.sprkadd_xuanzesp').css({'top':'0','opacity':'1','visibility':'visible'});
    reloadTable(0);
}