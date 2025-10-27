<?php
 
/**
 * -V2. PHP
 * @author ҹС
 * @Email 37217911@qq.com
 * @Website http://www.yelanxiaoyu.com
 * @version 20130118 
 */
	 function dataConnect()
		{		   
			$con = mysql_connect(DB_HOST, DB_USERS, DB_PWD);
			if (!$con){die('Could not connect: ' . mysql_error());}
			mysql_select_db(DB_NAME, $con);
			mysql_query("SET NAMES 'utf8'");
		}

 
?>