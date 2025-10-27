<?php 
function main_menu_extend( $rootnode='<ul>||</ul>', $parentnode='<li>||</li>', $childnode='<li>||</li>', $expand=0,$deep=0){//所有频道主栏目浮动菜单 dl,dd,dt 或者  ul,li,li 
	$_style = formatstyle($rootnode, $parentnode, $childnode);
	exec_menus(0,$expand,$_style,$deep);
}
function sub_menu_extend( $rootnode='<ul>||</ul>', $parentnode='<li>||</li>', $childnode='<li>||</li>', $expand=0,$deep=0){//频道下子栏目浮动菜单
	global $db,$params,$menu_arr;	
	$flag=$db->get_var("SELECT count(id) from `".TB_PREFIX."menu`  where parentId={$params['id']}");
	if(!$flag){
		$sid = $menu_arr['parentId']==0?$params['id']:$menu_arr['parentId'];
	}else{
		$sid = $params['id'];
	}
	$_style = formatstyle($rootnode, $parentnode, $childnode);
	exec_menus($sid,$expand,$_style);	
}
function call_sub_menu_extend($sid, $rootnode='<ul>||</ul>', $parentnode='<li>||</li>', $childnode='<li>||</li>', $expand=1){//指定频道子栏目浮动菜单
	if(intval($sid)>0)
	{
		$_style = formatstyle($rootnode, $parentnode, $childnode);
		exec_menus($sid,$expand,$_style);
	}
}
function exec_menus($sid,$expand,$_style,$deep=0){//所有频道栏目菜单
	$tempmenus=get_menus();
	if(!empty($tempmenus))
	{
		if($_style){
			$_stylearr=explode('||',$_style);
		}else{
			
		}
		//echo '<'.$_stylearr[0].' id="'.$_stylearr[3].'">'."\r\n";
		findChild($sid,$tempmenus,$expand,$_stylearr,$deep);
		//echo '</'.$_stylearr[0].'>'."\r\n";
	}
}
function formatstyle($rootnode='<ul>||</ul>', $parentnode='<li>||</li>', $childnode='<li>||</li>')
{
	if(!empty($rootnode) && !empty($parentnode) && !empty($childnode)) 
	{
		$rootnodes = explode('||',$rootnode);
		$parentnodes = explode('||',$parentnode);
		$childnodes = explode('||',$childnode);
		return trim(substr($rootnodes[0],1,strlen($rootnodes[0])-2)).'||'.trim(substr($parentnodes[0],1,strlen($parentnodes[0])-2)).'||'.trim(substr($childnodes[0],1,strlen($childnodes[0])-2));
	}
	else
	{
		return '';
	}
}
function get_menus()
{
	global $db;
	$sql="select *,(SELECT count(id) from `".TB_PREFIX."menu` b where b.parentId=a.id ) hassub  from ".TB_PREFIX."menu a where a.isHidden=0 order by  ordering  asc ";
	return $db->get_results($sql);
}
function get_root_menu($menuArr,$deep)
{
		$tempArr = array();
		foreach($menuArr as $key=>$o)
		{
			if($o->deep == $deep)
			{
				$tempArr[] = $o;
			}
		}
		return $tempArr;
}
function get_current_submenus($menuArr,$sid,$deep)
{
		$tempArr = array();
		foreach($menuArr as $o)
			{
				if(intval($deep)>0)
				{
					if($o->parentId == $sid && intval($o->deep)<(intval($deep)+1))
					{
						$tempArr[]=$o;
					}
				}
				else
				{
					if($o->parentId == $sid)
					{
						$tempArr[]=$o;
					}
				}
			}
		return $tempArr;	
}
function findChild($sid,$menuArr,$expand,$_stylearr,$deep)
{
	$tempArr=array();
	if(!empty($menuArr))
	{
		if(!$sid){
			$tempArr = get_root_menu($menuArr,0);//得到顶级菜单
		}else{
			$tempArr=get_current_submenus($menuArr,$sid,$deep);//得到子菜单
		}
	}
	if(count($tempArr)>0)
	{
		if(URLREWRITE){
			foreach($tempArr as $o)
			{
				if(intval($o->hassub)>0)
				{
					echo "\t".outputSpace($o->deep).'<'.$_stylearr[1].' '.(!intval($expand)?'class="closed"':'').'><a href="/'.$o->menuName.'/">'.$o->title.'</a>'."\r\n"; 
					echo "\t".outputSpace($o->deep).'<'.$_stylearr[0].'>'."\r\n";
					findChild($o->id,$menuArr,$expand,$_stylearr,$deep);
					echo "\t".outputSpace(intval($o->deep)).'</'.$_stylearr[0].'>'."\r\n";
					echo "\t".outputSpace(intval($o->deep)).'</'.$_stylearr[1].'>'."\r\n";
				}else{
					if(!intval($o->parentId)){
						echo "\t".outputSpace($o->deep).'<'.$_stylearr[1].'><a href="/'.$o->menuName.'/">'.$o->title.'</a></'.$_stylearr[1].'>'."\r\n";
					}else{
						echo "\t".outputSpace($o->deep).'<'.$_stylearr[2].'><a href="/'.$o->menuName.'/">'.$o->title.'</a></'.$_stylearr[2].'>'."\r\n";
					}
				}
			}
		}else{
			foreach($tempArr as $o)
			{
				if(intval($o->hassub)>0)
				{
					echo "\t".outputSpace($o->deep).'<'.$_stylearr[1].' '.(!intval($expand)?'class="closed"':'').'><a href="./?p='.$o->id.'">'.$o->title.'</a>'."\r\n"; 
					echo "\t".outputSpace($o->deep).'<'.$_stylearr[0].'>'."\r\n";
					findChild($o->id,$menuArr,$expand,$_stylearr,$deep);
					echo "\t".outputSpace(intval($o->deep)).'</'.$_stylearr[0].'>'."\r\n";
					echo "\t".outputSpace(intval($o->deep)).'</'.$_stylearr[1].'>'."\r\n";
				}else{
					if(!intval($o->parentId)){
						echo "\t".outputSpace($o->deep).'<'.$_stylearr[1].'><a href="./?p='.$o->id.'">'.$o->title.'</a></'.$_stylearr[1].'>'."\r\n";
					}else{
						echo "\t".outputSpace($o->deep).'<'.$_stylearr[2].'><a href="./?p='.$o->id.'">'.$o->title.'</a></'.$_stylearr[2].'>'."\r\n";
					}
				}
				
			}
		}
		return true;
	}
	else
	{
		return false;
	}
}
function outputSpace($deep)
{
	$tempStr="";
	for($i=-1;$i<$deep;$i++)
	{
		if($i==($deep-1))
		$tempStr.="";
		else
		$tempStr.="\t";
	}
	return $tempStr;
}
	?>
