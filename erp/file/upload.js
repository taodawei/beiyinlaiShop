
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
        { title : "Other files", extensions : "doc,docx,xls,xlsx,ppt,pptx,pdf,txt" }
        ],
        max_file_size : '10mb', //最大只能上传10mb的文件
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
                //获取后缀名
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
                var upload_type=document.getElementById('upload_type').value;//1:个人文件上传；2：公司文件更新
                if(upload_type==1){
                    document.getElementById('ossfile').innerHTML += '<div class="progress_title" id="' + file.id + '">' +'<span class="progress_text"><img src="'+src+'"/> ' + file.name +'</span>'+ ' <span class="progress_daxiao">' + plupload.formatSize(file.size) + '</span><b></b>'
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
                        url:"/qiyundongli/index.php?m=system&s=file&a=upload_file",
                        data:"oss_fileId="+file.id+"&oss_fileName="+oss_fileName+"&file_parentId="+$("#file_parentId").val()+"&file_name="+file.name+"&file_size="+plupload.formatSize(file.size)+"&file_suffix="+get_suffix(file.name),
                        timeout:"4000",
                        dataType:"json",                                 
                        success: function(html){
                                if(html.code==0){
                                    layer.msg(html.message, {icon: 5});
                                }else{
                                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<img src="images3/ok.png"/>';//'upload to oss success, object name:' + get_uploaded_object_name(file.name) + ' 回调服务器返回的内容是:' + info.response;
                                    //上传完成，隐藏进度条
                                    $(".progress").css("display","none");
                                    //渲染文件列表
                                    showFileList($("#file_parentId").val());
                                    $(".shangchuanwenjian_tk").css("display","none");
                                    $(".bj1").css("display","none");
                                    $("#file_count").html(html.file_count);
                                    $("#company_count").html(html.company_count);
                                    $("#share_count").html(html.share_count);
                                    $("#ossfile").html("");
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
                }else{
                    var oss_fileName=get_uploaded_object_name(file.name);
                    //更新文件
                    $.ajax({
                        type:"POST",
                        url:"/qiyundongli/index.php?m=system&s=file&a=update_file",
                        data:"oss_fileId="+file.id+"&oss_fileName="+oss_fileName+"&file_public_id="+$("#operating_file_id").val()+"&file_size="+plupload.formatSize(file.size)+"&file_suffix="+get_suffix(file.name),
                        timeout:"4000",
                        dataType:"json",                                 
                        success: function(html){
                                if(html.code==0){
                                    layer.msg(html.message, {icon: 5});
                                }else{
                                    layer.msg("上传成功", {icon: 6});
                                    showFileList_company();
                                    $(".gengxin_wj_tc").css("display","none");
                                    $(".bj1").css("display","none");
                                    $("#shenpi_count").html(html.shenpi_count);
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
                layer.msg("文件的大小不可以超过10M", {icon: 5});
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
