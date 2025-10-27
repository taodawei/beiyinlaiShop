<?php
class Pager
{
	private $cPage;
	private $total;
	private $pageSize;
	private $totalPageNo;
	private $rootpath;
	private $anchor;
	
	function __construct($CPage,$Total,$PageSize=9,$rootpath,$anchor)
	{
		$CPage 			= $CPage<1?1:$CPage;
		$PageSize 		= $PageSize<1?1:$PageSize;

		$this->cPage 	= $CPage;
		$this->total 	= $Total;
		$this->pageSize = $PageSize;
		$this->accountTotalPageNo();
		$this->rootpath = $rootpath;
		$this->anchor = $anchor;
		 
	}
	private function nextNo()
	{
		return $this->cPage+1;
	}
	private function prvNo()
	{
		return $this->cPage-1;
	}
	private function lastNo()
	{
		return $this->totalPageNo;
	}
	private function fristNo()
	{
		return 1;
	}
	private function accountTotalPageNo()
	{
		$this->totalPageNo = $this->total%$this->pageSize>0?(int)($this->total/$this->pageSize)+1:(int)($this->total/$this->pageSize);
	}
	public function totalPage()
	{
		return $this->totalPageNo;
	}
	public function Show($url,$style=0)
	{
		global $request;
		if($request['m'] == 'system'&&isset($request["p"])){
		  global $db;
		  $sql="SELECT menuName FROM `".TB_PREFIX."menu` where id=".$request["p"];
		  $menus=$db->get_row($sql);
		  //$url = $menus->menuName;
		}
		
		if($style==0)
		{
			if(URLREWRITE && ($request['m'] != 'system' && substr($_SERVER['REQUEST_URI'],0,(2+strlen(houtai))) != '/'.houtai.'/') || ($request['m'] = 'system' &&  substr($_SERVER['REQUEST_URI'],0,(38+strlen(houtai))) == '/'.houtai.'/index.php?m=system&s=html&a=contorl&'))
			{
			    if(!empty($request['p'])&&empty($request['f'])){
				$tempStr ='<span>共有'.$this->total.'个记录&nbsp;&nbsp;'.$this->cPage.'/'.$this->totalPageNo.'页&nbsp;&nbsp;</span>';
				$tempStr .= '<div class="fanye_anniu"><a href="'.$this->rootpath.$url.$this->fristNo().$this->anchor.'" id="firstBtn">首页</a></div>';
				$tempStr .=	$this->prvNo()<1?'':'<div class="fanye_anniu"><a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'" id="prevBtn">上一页</a></div>';
				$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<div class="fanye_anniu"><a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'" id="nextBtn">下一页</a></div>';
				$tempStr .= '<div class="fanye_anniu"><a href="'.$this->rootpath.$url.$this->lastNo().$this->anchor.'" id="lastBtn">尾页</a></div>';
				$tempStr .= '<span>跳转至<select name="pagerMenu" onChange="location=\''.$this->rootpath.$url.'\'+this.options[this.selectedIndex].value+\''.$this->anchor.'\'";>';
				for($i=1;$i<$this->totalPageNo+1;$i++)
				{
					$tempStr .= '<option value="'.$i.'"';
					$tempStr .= $i==$this->cPage?' selected="selected"':'';
					$tempStr .= '>'.$i.'</option>';
				}
				$tempStr .= '</select>页</span>';
				}
			    else if(URLREWRITE==2){
				    $tempStr = 	$this->cPage.'/'.$this->totalPageNo.' 页 共'.$this->total.'条';
				    $tempStr .= '<a href="'.$request['f'].'-p'.$this->fristNo().'.html"> 首页</a> ';
					$tempStr .=	$this->prvNo()<1?'':'<a href="'.$request['f'].'-p'.$this->prvNo().'.html">上一页</a> ';
					$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$request['f'].'-p'.$this->nextNo().'.html">下一页</a> ';
					$tempStr .= '<a href="'.$request['f'].'-p'.$this->lastNo().'.html">尾页</a> ';
					$tempStr .= '跳转至<select name="pagerMenu" onChange="location=\''.$request['f'].'-p'.'\'+this.options[this.selectedIndex].value+\'.html\'";>';
					for($i=1;$i<$this->totalPageNo+1;$i++)
					{
						$tempStr .= '<option value="'.$i.'"';
						$tempStr .= $i==$this->cPage?' selected="selected"':'';
						$tempStr .= '>'.$i.'</option>';
					}
					$tempStr .= '</select>';
				}else{
				    $tempStr = 	'<span>第'.$this->cPage.'/'.$this->totalPageNo.'页 共:'.$this->total.'条</span> ';
					$tempStr .= '<a href="'.$this->rootpath.$url.$this->fristNo().'/'.$this->anchor.'" class="number_03">首页</a> ';
					$tempStr .=	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().'/'.$this->anchor.'" class="number_03">上一页</a> ';
					$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$this->rootpath.$url.$this->nextNo().'/'.$this->anchor.'" class="number_03">下一页</a> ';
					$tempStr .= '<a href="'.$this->rootpath.$url.$this->lastNo().'/'.$this->anchor.'" class="number_03">尾页</a> ';
					$tempStr .= '<span>&nbsp;跳转至<select name="pagerMenu" onChange="location=\''.$this->rootpath.$url.'\'+this.options[this.selectedIndex].value+\''.'/'.$this->anchor.'\'";>';
					for($i=1;$i<$this->totalPageNo+1;$i++)
					{
						$tempStr .= '<option value="'.$i.'"';
						$tempStr .= $i==$this->cPage?' selected="selected"':'';
						$tempStr .= '>'.$i.'</option>';
					}
					$tempStr .= '</select></span>';
					}
			}
			else
			{
				$tempStr = 	'<span>第'.$this->cPage.'/'.$this->totalPageNo.'页 共:'.$this->total.'条</span> ';
					$tempStr .= '<a href="'.$this->rootpath.$url.$this->fristNo().'/'.$this->anchor.'" class="number_03">首页</a> ';
					$tempStr .=	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().'/'.$this->anchor.'" class="number_03">上一页</a> ';
					$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$this->rootpath.$url.$this->nextNo().'/'.$this->anchor.'" class="number_03">下一页</a> ';
					$tempStr .= '<a href="'.$this->rootpath.$url.$this->lastNo().'/'.$this->anchor.'" class="number_03">尾页</a> ';
					$tempStr .= '<span>&nbsp;跳转至<select name="pagerMenu" onChange="location=\''.$this->rootpath.$url.'\'+this.options[this.selectedIndex].value+\''.'/'.$this->anchor.'\'";>';
					for($i=1;$i<$this->totalPageNo+1;$i++)
					{
						$tempStr .= '<option value="'.$i.'"';
						$tempStr .= $i==$this->cPage?' selected="selected"':'';
						$tempStr .= '>'.$i.'</option>';
					}
					$tempStr .= '</select></span>';
				
			}
		}
		elseif($style==1)
		{
			$tpageNum=8;
			$tempStr ='<ul id="apartPage">';
			$tempStr .=	$this->prvNo()<1?'':'<li><a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'">Previous</a></li>';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?"<li class='pagebarCurrent'>$i</li>":'<li><a href="'.$this->rootpath.$url.$i.'">'.$i.$this->anchor.'</a></li>';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<li><a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'">下一页</a></li>';
			$tempStr .='</ul>';
		}
		elseif($style==2)
		{
			$tpageNum=8;
			$tempStr ='';
			$tempStr .=	$this->prvNo()<1?'':' <a href="'.$this->rootpath.$url.$this->fristNo().$this->anchor.'">First</a> ';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?" $i ":' <a href="'.$this->rootpath.$url.$i.'">'.$i.$this->anchor.'</a> ';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':' <a href="'.$this->rootpath.$url.$this->lastNo().$this->anchor.'">末页</a> ';
			$tempStr .=' 共'.$this->totalPageNo.'页 共'.$this->total.'条记录 ';
		}
		else
		{
		$tempStr =	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'"><< Previous</a> ';
		$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$url.$this->nextNo().$this->anchor.'">下一页 >></a>';
		}
		
		return $tempStr;
		
	}
	public function wapShow($url)
	{
		global $request;
		
		if($request['m'] == 'system'&&isset($request["p"])){
		  global $db;
		  $sql="SELECT menuName FROM `".TB_PREFIX."menu` where id=".$request["p"];
		  $menus=$db->get_row($sql);
		  $url = $menus->menuName;
		}
		$tempStr = 	$this->cPage.'/'.$this->totalPageNo.'页 共'.$this->total.'条 ';
		$tempStr .= '<a href="?id='.$request['id'].'&mdtp='.$this->fristNo().'">First</a> ';
		$tempStr .=	$this->prvNo()<1?'':'<a href="?id='.$request['id'].'&mdtp='.$this->prvNo().'">Previous</a> ';
		$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="?id='.$request['id'].'&mdtp='.$this->nextNo().'">下一页</a> ';
		$tempStr .= '<a href="?id='.$request['id'].'&mdtp='.$this->lastNo().'">Last</a> ';
		return $tempStr;
		
	}
	public function enShow($url,$style=0)
	{
		if($style==0)
		{
			$tempStr =	$this->prvNo()<1?'':'<A style="FONT-WEIGHT: normal" id=moreprev 
href="index.php?mdtp='.$this->prvNo().'">&lt; 上一页</A>';
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<A style="FONT-WEIGHT: normal" id=morenext href="index.php?mdtp='.$this->nextNo().$this->anchor.'">下一页 &gt;</A>';
		}
		elseif($style==1)
		{
			$tpageNum=8;
			$tempStr ='<ul id="apartPage">';
			$tempStr .=	$this->prvNo()<1?'':'<li><a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'">Previous Page</a></li>';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?"<li class='pagebarCurrent'>$i</li>":'<li><a href="'.$this->rootpath.$url.$i.$this->anchor.'">'.$i.'</a></li>';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<li><a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'">Next Page</a></li>';
			$tempStr .='</ul>';
		}
		elseif($style==2)
		{
			$tpageNum=8;
			$tempStr ='';
			$tempStr .=	$this->prvNo()<1?'':' <a href="'.$this->rootpath.$url.$this->fristNo().$this->anchor.'">First</a> ';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?" $i ":' <a href="'.$this->rootpath.$url.$i.$this->anchor.'">'.$i.'</a> ';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':' <a href="'.$this->rootpath.$url.$this->lastNo().$this->anchor.'">Last</a> ';
		}
		else
		{
		$tempStr =	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'"><< Previous Page</a> ';
		$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$url.$this->nextNo().$this->anchor.'">Next Page >></a>';
		}
		
		return $tempStr;
		
	}
	public function RecordStart()
	{
		return ($this->cPage-1)*$this->pageSize;
	}
	public function RecordSize()
	{
		return $this->pageSize;
	}
}
/**
 * 建立一个MySql的数据源，内置分页需要 Pager 类支持
 * @author shlcms<shlcms@shenhoulong.com>
 * @version 1.100325
 * @copyright deepthroat
 */
class sqlbuilder
{
	public $sql_out;
	public $results;
	public $pager;
	public $name;
	public $rootpath;
	
	private $sql;
	private $order;
	private $anchor;
	function __construct($name,$sql,$order,$db,$pagesize=10,$paging=true,$rootpath='./index.php',$anchor='')
	{
		$this->db=$db;
		$this->sql=$sql;
		$this->name=$name;
		$this->rootpath=$rootpath;
		$this->anchor=$anchor;
		
		$torder = $this->get_str($_GET[$name.'o']);
		$tpage = intval($this->get_str($_GET[$name.'p']));//xss 
		$order=empty($torder)?$order:$torder;
		//print_r($_GET);
		$this->pager = new Pager($tpage,$this->get_count(),$pagesize,$this->rootpath,$this->anchor);
		
		if($paging)
		{
			$sql=$sql.' order by '.$this->prase_order($order).' limit '.$this->pager->RecordStart().','.$this->pager->RecordSize();
		}
		else {
			$sql=$sql.' order by '.$this->prase_order($order);
		}
		$this->results = $this->db->get_results($sql,ARRAY_A);	
	}
	private function prase_order($order)
	{
		$orderarr=explode('|',$order);
		if(count($orderarr)>1)
		{
			if(((int)$orderarr[1])==0)
			{
				return $orderarr[0];
			}
			else 
			{
				return $orderarr[0].' desc';
			}
		}
		else 
		return $order;
	}
	public function get_pager_show()
	{
		return $this->pager->Show($this->build_url());
	}
	public function get_wap_pager_show()
	{
		return $this->pager->wapShow($this->build_url());
	}
	public function get_en_pager_show()
	{
		return $this->pager->enShow($this->build_url());
	}
	public function totalPageNo()
	{
		return $this->pager->totalPage($this->build_url());
	}
	private function get_str($string)
	{
		if (!get_magic_quotes_gpc()) {
			$string = addslashes($string);
		}
		return $string;
	}
	private function build_url()
	{
		global $request;
		foreach ($_GET as $k=>$v)
		{
			$_GET[$k]=RemoveXSS($v);
		}
		$urlstr='';
		//print_r($_GET);exit;
		//$_SERVER['REQUEST_URI'];
		if(URLREWRITE && $request['m'] != 'system' && !strpos(request_uri(),'admin'))
		{
			foreach ($_GET as $k=>$v)
			{
				if(strtoupper($k)!=strtoupper($this->name.'p'))
				{
					$urlstr.=$v.'/';
				}
			}
		}
		else
		{
			$urlstr='?';
			foreach ($_GET as $k=>$v)
			{
				if(strtoupper($k)!=strtoupper($this->name.'p'))
				{
					$urlstr.=$k.'='.$v.'&';
				}
			}
			$urlstr.=$this->name.'p=';
		}
		
		return $urlstr;
	}
	function get_count()
	{
		$tempArr = explode(' union ',strtolower($this->sql));
		$count = count($tempArr);
		$result = 0;
		$tempArr = null;
		$tempArr = explode(' from ',strtolower($this->sql));
		$count = count($tempArr);
		if($count>0)
		{
			$tempSqlStr = 'SELECT COUNT(*) FROM ';
			for($i=1;$i<$count;$i++)
			{
				if($i != $count-1)
					$tempSqlStr .= $tempArr[$i].' from ';
				else
					$tempSqlStr .= $tempArr[$i];
			}
			$result = $this->db->get_var($tempSqlStr);	
		}
		return $result;
	}
}
function request_uri(){
if (isset($_SERVER['argv']))
{
$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['argv'])?'':('?'. $_SERVER['argv'][0]));
}
else
{
$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['QUERY_STRING'])?'':('?'. $_SERVER['QUERY_STRING']));
}
return $_SERVER['REQUEST_URI'] = $uri;
}
?>