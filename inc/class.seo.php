<?php
/**
 * @author 一滴水 QQ:348681066
 * 标签优化
 * @copyright SHLCMS
 */
class seo{ 
	public $title='';
	public $keywords=''; 
	public $description='';

	public $channelTitle='';
	public $channelKeywords=''; 
	public $channelDescription='';

	//public $siteTitle='';
	//public $siteKeywords=''; 
	//public $siteDescription='';
	
	static $_instance=null;
	private function __construct(){
		global $params;
		//rss模块等无表模块 需要设计标准
		if($params['args']>0 && $params['action']!='get_rss'){
			global $db;
			global $menu_arr;
			$result=$db->get_row("SELECT * FROM ".TB_PREFIX.$menu_arr['type']." WHERE id=".$params['args'] );
			$this->title		= $result->title;
			$this->keywords 	= $result->keywords;
			$this->description  = $result->description;
			
			$this->channelTitle		   = $menu_arr['title'];
			$this->channelKeywords 	   = $menu_arr['keys'];
			$this->channelDescription  = $menu_arr['summary'];
			
		}elseif($params['id']>0){
			global $menu_arr;
			$this->channelTitle		   = $menu_arr['title'];
			$this->channelKeywords 	   = $menu_arr['keys'];
			$this->channelDescription  = $menu_arr['summary'];
		}else{
						
		}
	}
	public static function join($var,$join='-'){//功能同 /inc/function.php中的函数 string_join($var,$join='-')
		return $var?$var.' '.$join.' ':'';
	}
	private function __clone(){
		
	}
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new seo();
		}
		return self::$_instance;
	}
	static function getTitle(){
		return self::getInstance()->title;
	}
	static function getKeywords(){
		return self::getInstance()->keywords;
	}
	static function getDescription(){
		return self::getInstance()->description;
	}
	
	static function getChannelTitle(){
		return self::getInstance()->channelTitle;
	}
	static function getChannelKeywords(){
		return self::getInstance()->channelKeywords;
	}
	static function getChannelDescription(){
		return self::getInstance()->channelDescription;
	}
	
 }
?>