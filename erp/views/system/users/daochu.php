<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=会员列表—".date("Y-m-d H:i:s").".xls");
global $db,$request;
$allRows = array(
	"nickname"=>array("title"=>"姓名","rowCode"=>"{field:'nickname',title:'姓名',width:150}"),
	"username"=>array("title"=>"手机号","rowCode"=>"{field:'username',title:'手机号',width:180}"),
	"cost"=>array("title"=>"累计消费","rowCode"=>"{field:'cost',title:'累计消费',width:200}"),
	"lastLogin"=>array("title"=>"最后登录时间","rowCode"=>"{field:'lastLogin',title:'最后登录时间',width:180}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$level = (int)$request['level'];
$mendianId = (int)$request['mendianId'];
$keyword = $request['keyword'];
$money_start = $request['money_start'];
$money_end = $request['money_end'];
$jifen_start = $request['jifen_start'];
$jifen_end = $request['jifen_end'];
$dtTime_start = $request['dtTime_start'];
$dtTime_end = $request['dtTime_end'];
$login_start = $request['login_start'];
$login_end = $request['login_end'];
$sql="select * from users where comId=$comId";
if(!empty($level)){
	$sql.=" and level=$level";
}
if(!empty($keyword)){
	$sql.=" and (nickname like '%$keyword%' or username like '%$keyword%')";
}
if(!empty($mendianId)){
	$sql.=" and mendianId=$mendianId";
}
if(!empty($money_start)){
	$sql.=" and money>='$money_start'";
}
if(!empty($money_end)){
	$sql.=" and money<='$money_end'";
}
if(!empty($jifen_start)){
	$sql.=" and jifen>=$jifen_start";
}
if(!empty($jifen_end)){
	$sql.=" and jifen<=$jifen_end";
}
if(!empty($dtTime_start)){
	$sql.=" and dtTime>='$dtTime_start'";
}
if(!empty($dtTime_end)){
	$sql.=" and dtTime<='$dtTime_end'";
}
$sql.=" order by id desc limit 30000";
$pdts = $db->get_results($sql);
?>
<table border="1" >
	<tbody><tr>
		<?
		foreach ($allRows as $row=>$isshow){
				?>
				<td><?=$isshow['title']?></td>
				<?
		}
		?>
	</tr>
	<?
	if(!empty($pdts)){
		foreach ($pdts as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->level = empty($j->level)?'无':$db->get_var("select title from user_level where id=$j->level");
			$j->renzheng = $j->renzheng==1?'已认证':'未认证';
			$j->mendian  =$db->get_var("select title from mendian where id=$j->mendianId");
			$j->yhq = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$j->id");
			$j->gift_card = $db->get_var("select sum(yue) from gift_card$fenbiao where comId=$comId and userId=$j->id");
			if(empty($j->gift_card))$j->gift_card='0';
			$j->nickname = '<span onclick="user_info(\'basic\','.$j->id.')" style="cursor:pointer;">'.$j->nickname.'</span>';
			$j->money = '<span onclick="user_info(\'money_jilu\','.$j->id.')" style="cursor:pointer;color:#f00">'.$j->money.'</span>';
			$j->jifen = '<span onclick="user_info(\'jifen_jilu\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->jifen.'</span>';
			$j->yhq = '<span onclick="user_info(\'yhq\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->yhq.'</span>';
			$j->gift_card = '<span onclick="user_info(\'gift_card\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->gift_card.'</span>';
			$j->cost = '<span onclick="user_info(\'order_jilu\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->cost.'</span>';
			$j->lastLogin = date("Y-m-d H:i",strtotime($j->lastLogin));
			?>
			<tr>
				<?
				foreach ($allRows as $row=>$isshow){
						?>
						<td style="vnd.ms-excel.numberformat:@"><?=$j->$row?></td>
						<?
				}
				?>
			</tr>
		<?
		}
	}
?>
</tbody></table>
