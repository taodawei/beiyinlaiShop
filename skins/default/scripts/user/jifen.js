$(function(){
	get_cuxiao_product();
})
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
				$("#my_jifen").text(resdata.jifen);
				$("#my_days").text(resdata.days);
				$(".wodejifen_1_03").hide();
				$(".wodejifen_1_04").show();
			}
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
      console.log(XMLHttpRequest);
      console.log(textStatus);
      console.log(errorThrown);
			layer.closeAll();
			layer.open({content:'网络异常',skin: 'msg',time: 2});
		}
	});
}
function get_cuxiao_product(){
  $.ajax({
      type: "POST",
      url: "/index.php?p=4&a=get_pdt_list",
      data: "pagenum=10",
      dataType:"json",timeout : 10000,
      success: function(resdata){
        var str = '';
        if(resdata.data.length>0){
          $.each(resdata.data,function(key,item){
              str+='<li>'+
                    '<a onclick="setInitScroll();" href="/index.php?p=4&a=view&id='+item.inventoryId+'">'+
                        '<div class="shouye_8_down_img">'+
                            '<img src="'+item.img+'" />'+
                        '</div>'+
                        '<div class="shouye_8_down_tt1">'+
                            item.title+
                        '</div>'+
                        '<div class="shouye_8_down_tt2">'+
                          '￥'+item.price_sale+' <b>券</b><br>'+
                            '<span>'+item.price_name+'：'+item.price_market+'</span>'+
                        '</div>';
                        if(item.brand!=''){
                          str+='<div class="shouye_8_down_logo">'+
                            '<img src="'+item.brand+'"/>'+
                          '</div>';
                        }
                        str+='</a></li>';
                  if(key>0&&(key+1)%2==0){
                      str+='<div class="clearBoth"></div>';
                  }
          });
        }
        $("#tuijian_list .clearBoth").before(str);
      },
      error: function() {
        layer.msg('数据请求失败，请刷新页面重试');
      }
  });
}