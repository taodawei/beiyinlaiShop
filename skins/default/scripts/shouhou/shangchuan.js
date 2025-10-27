var jishiqi;
$(function(){
	$("#search_addr").bind('input propertychange', function() {
        var keyword = $(this).val();
        clearTimeout(jishiqi);
        jishiqi=setTimeout(function(){
            $("#pdt_search_div").html('<div style="text-align:center;padding:3rem 0rem;"><img src="/skins/default/images/loading.gif" width="3rem"></div>');
            $.ajax({
                type: "POST",
                url: "/index.php?p=4&a=get_pdt_list&pageNum=5&page=1",
                data: "keyword="+keyword,
                dataType:"json",timeout : 8000,
                success: function(res){
                    var str = '';
                    if(res.data.length>0){
                        $.each(res.data, function(index, item){
                            str = str+'<div class="shouhuodizhi_queren_1" onclick="select_inventory('+item.inventoryId+',\''+item.title+'\');"><h2>'+item.title+'</h2></div>';
                        });
                    }else{
                        str = '<div style="text-align:center;padding:3rem 0rem;">未找到对应的产品！</div>';
                    }
                    
                    $("#pdt_search_div").html(str);
                },
                error: function() {
                    layer.closeAll();
                    layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
                }
            });
        },500);
     });
});
function select_inventory(inventoryId,title){
    $("#shouhuodizhi_queren_tc").hide();
    $("#inventoryId").val(inventoryId);
    $("#pdt_title").html('<font style="width:9rem;height: 2.4rem;display: inline-block;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'+title+'</font> <img src="/skins/default/images/querendingdan_11.png" style="position:relative;top:-1rem;display:inline-block;" />');
}

var jishiqi;
$(function(){
    $(document).bind('click',function(){
        if($("#inventoryId").val()==0){
            $("#searchKehuInput").val('');
        }
    });
    $('#searchKehuInput').bind('input propertychange', function() {
        $("#inventoryId").val(0);
        clearTimeout(jishiqi);
        var val = $(this).val();
        jishiqi=setTimeout(function(){getpdtList(val);},500);
    });
    $('.dhd_adddinghuodan_1_right_02').click(function(eve){
        $("#kehuList").show();
        var keyword = $("#searchKehuInput").val();
        getpdtList(keyword);
        stopPropagation(eve);
    });
    $('#searchKehuInput').click(function(eve){
        if($("#kehuList").css("display")=="none"){
            $("#kehuList").show();
            getpdtList('');
        }
        stopPropagation(eve);
    });
});
function getpdtList(keyword){
    $("#kehuList ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
    var hasIds = '0';
    $.ajax({
        type: "POST",
        url: "/erp_service.php?action=getPdtList&id=0&storeId=0",
        data: "keyword="+keyword,
        dataType:'text',timeout : 8000,
        success: function(resdata){
            $("#kehuList ul").html(resdata);
        }
    });
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,unitstr,kucun){
    $("#kehuList").hide();
    $("#searchKehuInput").val(title);
    $("#inventoryId").val(inventoryId);
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
        serverUrl = '/skins/default/file/php/get.php'
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
        if($("#title").val()==''){
            layer.open({content:'请输入视频标题！',skin: 'msg',time: 2});
            return false;
        }
        /*if($("#inventoryId").val()=='0'){
            layer.open({content:'请选择要对应的产品',skin: 'msg',time: 2});
            return false;
        }*/
        if(document.getElementById('ossfile').innerHTML==""){
            layer.open({content:'请添加文件！',skin: 'msg',time: 2});
            return false;
        }else{
            layer.open({
                type: 2
                ,shadeClose: false
                ,content: '视频上传中。。。'
            });
            set_upload_param(uploader, '', false);
            return false;
        }
    };
},

FilesAdded: function(up, files) {
   plupload.each(files, function(file) {
    var suffix=get_suffix(file.name);
    if (suffix== ".gif" || suffix== ".jpg" || suffix== ".png" || suffix== ".jpeg" || suffix== ".bmp") {
        var src="/erp/images3/06.png";
    }else if(suffix== ".zip" || suffix== ".rar"){
        var src="/erp/images3/09.png";
    }else if(suffix== ".txt"){
        var src="/erp/images3/01.png";
    }else if(suffix== ".pdf"){
        var src="/erp/images3/02.png";
    }else if(suffix== ".ppt"){
        var src="/erp/images3/04.png";
    }else if(suffix== ".doc" || suffix== ".docx"){
        var src="/erp/images3/03.png";
    }else if(suffix== ".xlsx" || suffix== ".xls"){
        var src="/erp/images3/05.png";
    }else if(suffix== ".mp3"){
        var src="/erp/images3/07.png";
    }else{
        var src="/erp/images3/08.png";
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
                
                var oss_fileName=get_uploaded_object_name(file.name);
                var title = $("#title").val();
                var inventoryId = $("#inventoryId").val();
                $.ajax({
                    type:"POST",
                    url:"/index.php?p=13&a=upload_shipin&title="+title+"&inventoryId="+inventoryId,
                    data:"oss_fileId="+file.id+"&oss_fileName="+oss_fileName+"&file_parentId="+$("#file_parentId").val()+"&file_name="+file.name+"&file_size="+plupload.formatSize(file.size)+"&file_suffix="+get_suffix(file.name),
                    timeout:"4000",
                    dataType:"json",
                    success: function(html){
                        if(html.code==0){
                            layer.closeAll();
                            layer.open({content:html.message,skin: 'msg',time: 2});
                        }else{
                            layer.open({content:'上传成功，请等待管理员审核',skin: 'msg',time: 2});
                            setTimeout(function(){location.reload();},1800);
                        }
                    },
                    // error:function(){
                    //     alert("超时,请重试");
                    // }
                    error:function(XMLHttpRequest, textStatus, errorThrown){
                      console.log(XMLHttpRequest);
                       // alert("超时,请重试");
                   }
               });
                
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
                layer.open({content:'文件的大小不可以超过25M',skin: 'msg',time: 2});
            }
            else if (err.code == -601) {
                layer.open({content:'不支持该文件类型',skin: 'msg',time: 2});
            }
            else if (err.code == -602) {
                layer.open({content:'这个文件已经上传过一遍了',skin: 'msg',time: 2});
            }
            else 
            {
                layer.open({content:'系统错误，请刷新重试',skin: 'msg',time: 2});
                console.log(err);
            }
        }
    }
});

uploader.init();
