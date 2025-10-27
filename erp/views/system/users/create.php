<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$shezhi = $db->get_row("select sn,addrows from user_shezhi where comId=$comId");
$rowss = $shezhi->addrows;
$arrays = array();
if(!empty($rowss)){
    $arrays = unserialize($rowss);
}
$id = (int)$request['id'];
if($id>0)$user = $db->get_row("select * from users where id=$id and comId=$comId");
if(!empty($user->addRows))$com_fujiaArry = json_decode($user->addRows,true);
$levels = $db->get_results("select id,title from user_level where comId=$comId order by jifen asc");
$mendians = $db->get_results("select id,title from mendian where comId=$comId");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/inc/js/date/WdatePicker.js"></script>
</head>
<body>
	<div class="mendianguanli">
    	<div class="mendianguanli_up">
        	<?=empty($id)?'新增':'修改'?>会员
        </div>
    	<div class="mendianguanli_down">
        	<div class="addhuiyuan">
            	<div class="addhuiyuan_1">
                	必填信息
                </div>
                <form action="?s=users&a=create&id=<?=$id?>&submit=1" id="userForm" method="post" class="layui-form">
            	<div class="addhuiyuan_2">
                	<ul>
                		<li>
                        	<div class="addhuiyuan_2_01">
                            	会员编号
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<input type="text" name="sn" required="required" value="<?=empty($user)?$shezhi->sn.date("ymdHis").rand(1000,9999):$user->sn;?>" class="addhuiyuan_2_02_input"/>
                            </div>
                            <div class="addhuiyuan_2_02">
                            	<a href="?a=shezhi&url=<?=urlencode('?s=mendian_set&a=addrows')?>" target="_blank">设置编号规则 &gt;&gt;</a>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="addhuiyuan_2_01">
                            	<span>*</span> 姓名
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<input type="text" name="nickname" value="<?=$user->nickname?>" required="required" placeholder="请填入会员的真实姓名" class="addhuiyuan_2_02_input"/>
                            </div>
                            <div class="addhuiyuan_2_02">
                            	
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="addhuiyuan_2_01">
                            	<span>*</span> 手机号
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<input type="text" name="phone" id="username" value="<?=$user->phone?>" <? if(!empty($user)){?>readonly="true"<?}?> required="required" placeholder="请输入会员的手机号" class="addhuiyuan_2_02_input"/>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="addhuiyuan_2_01">
                            	<span>*</span> 密码
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<input type="text" name="password" id="password" <? if(empty($user)){?>required="required" placeholder="请输入账号密码"<? }else{?>placeholder="不修改请留空"<? }?>  class="addhuiyuan_2_02_input"/>
                            </div>
                            <div class="addhuiyuan_2_02">
                            	<a href="javascript:" onclick="randPwd();" class="addhuiyuan_2_02_anniu">生成随机密码</a>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="addhuiyuan_2_01">
                            	会员等级
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<select name="level">
                            		<? if(!empty($levels)){
                            			foreach ($levels as $l) {
                            				?>
                            				<option value="<?=$l->id?>" <? if($l->id==$user->level){?>selected="true"<? }?>><?=$l->title?></option>
                            				<?
                            			}
                            		}?>
                            	</select>
                            </div>
                            <div class="addhuiyuan_2_02">
                            	<a href="?a=shezhi&url=<?=urlencode('?s=mendian_set&a=level')?>" target="_blank">设置会员等级 &gt;&gt;</a>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li style="display:none;">
                        	<div class="addhuiyuan_2_01">
                            	所属门店
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<select name="mendianId">
                            		<? if(!empty($mendians)){
                            			foreach ($mendians as $m) {
                            				?>
                            				<option value="<?=$m->id?>" <? if($l->id==$user->mendianId){?>selected="true"<? }?>><?=$m->title?></option>
                            				<?
                            			}
                            		}
                            		?>
                            	</select>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="addhuiyuan_2_01">
                            	性别
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<select name="sex">
                            		<option value="0">未知</option>
                            		<option value="1" <? if($user->sex==1){?>selected="true"<? }?>>男</option>
                            		<option value="2" <? if($user->sex==2){?>selected="true"<? }?>>女</option>
                            	</select>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="addhuiyuan_2_01">
                            	出生日期
                            </div>
                        	<div class="addhuiyuan_2_02">
                            	<input type="text" name="birthday" value="<?=empty($user->birthday)?'':($user->birthday=='0000-00-00'?'':$user->birthday)?>" id="birthday" placeholder="请选择出生日期" class="addhuiyuan_2_02_input"/>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                	</ul>
                </div>
            	<div class="addhuiyuan_3" style="display:none;">
                	详细资料 <a href="?a=shezhi&url=<?=urlencode('?s=mendian_set&a=addrows')?>" target="_blank">自定义会员项 &gt;&gt;</a>
                </div>
                <div class="addhuiyuan_2">
                	<ul>
                        <?
                         if(!empty($arrays)){
                              foreach ($arrays as $i=>$row){
                                switch ($row['type']) {
                                  case 'singleline':
                                  echo '<li id="addLi'.$i.'">
                                  <div class="addhuiyuan_2_01"><span class="add_cont_zhuyi">'.(($row['if_must']==1)?'*':'&nbsp;').'</span> '.$row['name'].'：</div>
                                  <div class="addhuiyuan_2_02"><input name="com_added['.$row['name'].']" value="'.$com_fujiaArry[$row['name']].'" '.(($row['if_must']==1)?'required="required"':'').' placeholder="请填写'.$row['name'].'" type="text" class="layui-input"></div><div class="clearBoth"></div></li>';
                                  break;
                                  case 'textarea':
                                  echo '<li id="addLi'.$i.'">
                                  <div class="addhuiyuan_2_01"><span class="add_cont_zhuyi">'.(($row['if_must']==1)?'*':'&nbsp;').'</span> '.$row['name'].'：</div>
                                  <div class="addhuiyuan_2_02"><textarea name="com_added['.$row['name'].']" class="add_chengjiao_cont_textarea" placeholder="请填写'.$row['name'].'">'.$com_fujiaArry[$row['name']].'</textarea></div><div class="clearBoth"></div></li>';
                                  break;
                                  case 'date':
                                  echo '<li id="addLi'.$i.'">
                                  <div class="addhuiyuan_2_01"><span class="add_cont_zhuyi">'.(($row['if_must']==1)?'*':'&nbsp;').'</span> '.$row['name'].'：</div>
                                  <div class="addhuiyuan_2_02"><input name="com_added['.$row['name'].']" value="'.$com_fujiaArry[$row['name']].'" '.(($row['if_must']==1)?'required="required"':'').' placeholder="请选择'.$row['name'].'" type="text" onclick="WdatePicker();" class="layui-input"></div><div class="clearBoth"></div></li>';
                                  break;
                                  case 'money':
                                  echo '<li id="addLi'.$i.'">
                                  <div class="addhuiyuan_2_01"><span class="add_cont_zhuyi">'.(($row['if_must']==1)?'*':'&nbsp;').'</span> '.$row['name'].'：</div>
                                  <div class="addhuiyuan_2_02"><input name="com_added['.$row['name'].']" value="'.$com_fujiaArry[$row['name']].'" '.(($row['if_must']==1)?'required="required"':'').' placeholder="请填写'.$row['name'].'" type="text" class="layui-input"></div><div class="clearBoth"></div></li>';
                                  break;
                                  case 'num':
                                  echo '<li id="addLi'.$i.'">
                                  <div class="addhuiyuan_2_01"><span class="add_cont_zhuyi">'.(($row['if_must']==1)?'*':'&nbsp;').'</span> '.$row['name'].'：</div>
                                  <div class="addhuiyuan_2_02"><input name="com_added['.$row['name'].']" value="'.$com_fujiaArry[$row['name']].'" '.(($row['if_must']==1)?'required="required"':'').' placeholder="请填写'.$row['name'].'" type="text" class="layui-input"></div><div class="clearBoth"></div></li>';
                                  break;
                                  case 'select':
                                  $selects = explode('@', $row['select']);
                                  $selectstr = '';
                                  if(!empty($selects)){
                                    foreach ($selects as $v) {
                                      $selectstr .='<option value='.$v.' '.(($v==$com_fujiaArry[$row['name']])?'selected="selected"':'').'>'.$v.'</option>';
                                    }
                                  }
                                  echo '<li id="addLi'.$i.'">
                                  <div class="addhuiyuan_2_01"><span class="add_cont_zhuyi">'.(($row['if_must']==1)?'*':'&nbsp;').'</span> '.$row['name'].'：</div>
                                  <div class="addhuiyuan_2_02"><select name="com_added['.$row['name'].']">'.$selectstr.'</select></div><div class="clearBoth"></div></li>';
                                  break;
                                }
                                $str.='';
                              }
                         }
                         ?>
                	</ul>
                </div>
                <div class="addhuiyuan_4">
                	<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    	layui.use(['laydate','form'], function(){
		  var laydate = layui.laydate
		  ,form = layui.form
		  laydate.render({
		  	elem: '#birthday'
		  	,max:'<?=date("Y-m-d H:i:s")?>'
            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
            ,type: 'date'
            ,format: 'yyyy-MM-dd'
		  });
		  form.on('submit(tijiao)',function(){
		  	$yz = 0;
		  	$("#submitForm input[required]").each(function(){
		  		if($(this).val()==''){
		  			layer.msg($(this).attr("placeholder"),{icon:5});
		  			$(this).focus();
		  			$yz = 1;
		  			return false;
		  		}
		  	});
		  	if($yz==1){
		  		return false;
		  	}
		  	<? if(empty($id)){?>
		  	var username = $("#username").val();
		  	layer.load();
		  	$.ajax({
		  		type: "POST",
		  		url: "?s=users&a=check_username",
		  		data: "&username="+username,
		  		dataType:"json",timeout : 8000,
		  		success: function(resdata){
		  			layer.closeAll();
		  			if(resdata.code==0){
		  				layer.msg(resdata.message,{icon: 5});
		  				return false;
		  			}else{
		  				$("#userForm").submit();
		  				return false;
		  			}
		  		},
		  		error: function() {
		  			layer.closeAll();
		  			layer.msg('数据请求失败', {icon: 5});
		  		}
		  	});
		  	<? }else{?>
		  		$("#userForm").submit();
		  	<? }?>
		  	return false;
		  });
		});
		function randPwd(){
			arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
			var str = '';
		    for(var i=0; i<7; i++){
		        pos = Math.round(Math.random() * (arr.length-1));
		        str += arr[pos];
		    }
		    $("#password").val(str);
		}
    </script>
</body>
</html>