var title = '',channelId = '',sn = '',status = '';
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#super_channel").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}
function selectChannel(channelId,title){
	$("#channelId").val(channelId);
	$(".splist_up_01_left_01_up span").html(title);
	reloadTable(0);
}
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	$("#yushou_price").bind('input propertychange', function(){
		var dingjin = $("#dingjin").val();
		var yushou_price = $(this).val();
		var weikuan = yushou_price-dingjin;
		if(!isNaN(weikuan)&&weikuan>=0){
			$("#weikuan").html(weikuan);
		}else{
			$("#weikuan").html('');
		}
	});
	$("#dingjin").bind('input propertychange', function(){
		var dingjin = $(this).val();
		var yushou_price = $("#yushou_price").val();
		var weikuan = yushou_price-dingjin;
		if(!isNaN(weikuan)&&weikuan>=0){
			$("#weikuan").html(weikuan);
		}else{
			$("#weikuan").html('');
		}
	});
});
//隐藏搜索框
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
function search_pdt(){
	title = $("#s_title").val();
	sn = $("#s_sn").val();
	channelId = $("#super_channel").val();
	status = $("#s_status option:selected").val();
	reloadTable(0);
}
function add_yushou(id,sn,price){
	$("#a_pdtId").val(id);
	$("#a_sn").html(sn);
	$("#a_price").html(price);
	$("#shezhi_ykjdingjin_tc").show();
}
function add_qujian(){
	var rows = parseInt($("#rows_div").attr("rows"));
	rows = rows+1;
	var str = '<div id="rows_'+rows+'" class="add_qujian_div" style="margin-top:5px;">'+
        '满 <input type="number" name="man_'+rows+'" placeholder="0" class="shezhi_ykjdingjin_2_down_tt_input1"/> 份，预售价：<input type="number" name="price_'+rows+'" placeholder="0.00" class="shezhi_ykjdingjin_2_down_tt_input1"/> 元'+
        '<input type="hidden" name="rows[]" value="'+rows+'">&nbsp;&nbsp;&nbsp;<a href="javascript:" onclick="del_rows('+rows+')"><img src="images/yingxiao_30.png"></a>'+
    '</div>';
    $("#rows_div").append(str);
    $("#rows_div").attr("rows",rows);
}
function del_rows(id){
	$("#rows_"+id).remove();
}
function reloadTable(curpage){
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	productListTalbe.reload({
		where: {
			order1: order1
			,order2: order2
			,title:title,
	    	sn:sn,
	    	channelId:channelId,
	    	status:status
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}