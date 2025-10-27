var layform;
layui.use(['form'], function(){
  layform = layui.form;
  layform.on('submit(tijiao)', function(data){
    layer.load();
  });
});
function add_ziduan(){
  rows = rows+1;
//   var content = '<div id="row'+rows+'" class="lirow"><div class="gonggao_2_left">标题：</div><div class="gonggao_2_right"><input type="text" lay-verify="required" onchange="changeItem('+rows+');" name="name'+rows+'" value="" class="shenpi_add_2_input1">&nbsp;&nbsp;类型：<select name="type'+rows+'" class="shenpi_add_2_input1" id="typeSelect'+rows+'" onchange="checkSelect('+rows+');"><option value="singleline">单行输入框</option><option value="textarea">多行输入框</option><option value="date">日期选择框</option><option value="money">金钱输入框</option><option value="num">数字输入框</option><option value="select">多项选择框</option></select>&nbsp;&nbsp;是否必填：<select name="if_must'+rows+'" id="mustSelect'+rows+'" onchange="checkMust('+rows+');" class="shenpi_add_2_input1"><option value="0">否</option><option value="1">是</option></select>&nbsp;&nbsp;描述：<input type="text" name="detail'+rows+'" value="" class="shenpi_add_2_input1"/><br /><div id="shenpi_select'+rows+'" class="shenpi_ziduan_set1" style="display:none"><div class="shenpi_ziduan_set1_01">请输入列表项：</div><div class="shenpi_ziduan_set1_02"><ul><li id="liebiao'+rows+'"><input type="text" name="select'+rows+'[]" class="shenpi_add_2_input1" /></li><li style="background:none"><img src="images/shenpi_add.png" style="cursor:pointer" onclick="addSelect('+rows+');" width="27" height="27">&nbsp;&nbsp;<img src="images/shenpi_jian.png" style="cursor:pointer" onclick="delSelect('+rows+');" width="27" height="27"></li></ul></div></div></div><div class="shenpi_add_2_dele1" onclick="del_ziduan(\''+rows+'\')"><img src="images/shenpi_dele1.png"></div><div class="clearBoth"></div></div>';
  
    var content = '<div id="row'+rows+'" class="lirow"><div class="gonggao_2_left">字段名称：</div><div class="gonggao_2_right"><input type="text" lay-verify="required" onchange="changeItem('+rows+');" name="name'+rows+'" value="" class="shenpi_add_2_input1">&nbsp;&nbsp;是否显示：<select name="if_must'+rows+'" id="mustSelect'+rows+'" onchange="checkMust('+rows+');" class="shenpi_add_2_input1"><option value="0">否</option><option value="1">是</option></select>&nbsp;&nbsp;描述：<input type="text" name="detail'+rows+'" value="" class="shenpi_add_2_input1"/><br /><div id="shenpi_select'+rows+'" class="shenpi_ziduan_set1" style="display:none"><div class="shenpi_ziduan_set1_01">请输入列表项：</div><div class="shenpi_ziduan_set1_02"><ul><li id="liebiao'+rows+'"><input type="text" name="select'+rows+'[]" class="shenpi_add_2_input1" /></li><li style="background:none"><img src="images/shenpi_add.png" style="cursor:pointer" onclick="addSelect('+rows+');" width="27" height="27">&nbsp;&nbsp;<img src="images/shenpi_jian.png" style="cursor:pointer" onclick="delSelect('+rows+');" width="27" height="27"></li></ul></div></div></div><div class="shenpi_add_2_dele1" onclick="del_ziduan(\''+rows+'\')"><img src="images/shenpi_dele1.png"></div><div class="clearBoth"></div></div>';
  $("#rows_contnt").append(content);
  var cont = '<div class="wq_shouji_dhwb" id="shenpi_row'+rows+'"><div class="wq_shouji_02_title"></div><div class="wq_shouji_02_shuoming"><span class="wq_shouji_02_shuru">请输入</span><span class="wq_shouji_02_must"></span></div></div>';
  $("#add_cont_right .wq_shouji").append(cont);
  $("#rows").val(rows);
  layform.render();
}
function changeItem(id){
  var title = $("input[name='name"+id+"']").val();
  $("#shenpi_row"+id+" .wq_shouji_02_title").html(title);
}
function del_ziduan(delrow){

  var start = 0;
  if(delrow>start){
    $("#row"+delrow).remove();
    $("#shenpi_row"+delrow).remove();
  }else{
    alert('系统预设字段不能删除');
  }
}
function checkSelect(id){
  var selectValue = $("#typeSelect"+id).find("option:selected").val();
  if(selectValue=='select'){
    $("#shenpi_select"+id).show();
  }else{
    $("#shenpi_select"+id).hide();
  }
  if(selectValue=='textarea'){
    $("#shenpi_row"+id).attr("class","wq_shouji_duohwb");
    $("#shenpi_row"+id+" .wq_shouji_02_shuru").html("请输入");
  }else if(selectValue=='date'||selectValue=='select'){
    $("#shenpi_row"+id).attr("class","wq_shouji_danxuan");
    $("#shenpi_row"+id+" .wq_shouji_02_shuru").html("请选择");
  }else{
    $("#shenpi_row"+id).attr("class","wq_shouji_dhwb");
    $("#shenpi_row"+id+" .wq_shouji_02_shuru").html("请输入");
  }
}
function checkMust(id){
  var selectValue = $("#mustSelect"+id).find("option:selected").val();
  if(selectValue=='0'){
    $("#shenpi_row"+id+" .wq_shouji_02_must").html("");
  }else{
    $("#shenpi_row"+id+" .wq_shouji_02_must").html("（必填）");
  }
}
function addSelect(id){
  var content = '<input type="text" name="select'+id+'[]" class="shenpi_add_2_input1">';
  $("#liebiao"+id).append(content);
}
function delSelect(id){
  if($("#liebiao"+id).find("input").length>1){
    $("#liebiao"+id).find("input").last().remove();
  }
}
