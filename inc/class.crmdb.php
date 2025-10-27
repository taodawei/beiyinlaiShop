<?php
global $conn2;
class dtdb1 {

	var $show_errors = false;
	var $num_queries = 0;
	var $last_query;
	var $col_info;
	var $queries;
	var $rows_affected;

	// ==================================================================
	//	DB Constructor - connects to the server and selects a database

	function __construct($dbuser, $dbpassword, $dbname, $dbhost) {
		global $conn2;
		$conn2 = mysqli_connect($dbhost, $dbuser, $dbpassword,$dbname);
		if (!$conn2) {
			$this->bail("
		<h1>建立数据库链接时出错！</h1>
		<p>也可能是存在于<code>dt-config.php</code>文件中的数据库用户名或密码不正确，不能建立与数据库服务器<code>$dbhost</code>的连接. </p>
		<ul>
			<li>你确定你的数据库用户名密码没错？</li>
			<li>你确定你输入了正确的主机名？</li>
			<li>你确定你的数据库服务器正在运行？</li>
		</ul>
		<p>如果你不确定这些信息请联系你的虚拟主机服务供应商. .</p>
		");
		}
		//$this->select($dbname);
		$this->query("SET NAMES 'utf8' ");
	}

	// ==================================================================
	//	Select a DB (if another one needs to be selected)
	// ====================================================================
	//	Format a string correctly for safe insert under all PHP conditions

	function escape($string) {
		global $conn2;
		return addslashes( $string ); // Disable rest for now, causing problems
		if( !$conn2 || version_compare( phpversion(), '4.3.0' ) == '-1' )
		return mysql_escape_string( $string );
		else
		return mysql_real_escape_string( $string, $conn2 );
	}

	// ==================================================================
	//	Print SQL/DB error.

	function print_error($str = '') {
		global $EZSQL_ERROR;
		if (!$str) $str = mysqli_error();
		$EZSQL_ERROR[] =
		array ('query' => $this->last_query, 'error_str' => $str);

		$str = htmlspecialchars($str, ENT_QUOTES);
		$query = htmlspecialchars($this->last_query, ENT_QUOTES);
		// Is error output turned on or not..

		
		if ( $this->show_errors ) {
			file_put_contents(date("y-m-d").'.logs', $query.PHP_EOL, FILE_APPEND);
			print "<div id='error'>
			<p class='dtdberror'><strong>系统错误:</strong> ".$str.'<br>'.$query."</code></p>
			</div>";
		} else {
			file_put_contents(date("y-m-d").'.logs', $query.PHP_EOL, FILE_APPEND);
			return false;
		}
	}

	// ==================================================================
	//	Turn error handling on or off..

	function show_errors() {
		$this->show_errors = true;
	}

	function hide_errors() {
		$this->show_errors = false;
	}

	// ==================================================================
	//	Kill cached query results

	function flush() {
		$this->last_result = null;
		$this->col_info = null;
		$this->last_query = null;
	}

	// ==================================================================
	//	Basic Query	- see docs for more detail

	function query($query,$execRows=false) {
		global $conn2;
		// initialise return
		$return_val = 0;
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		// Perform the query via std mysql_query function..
		if (SAVEQUERIES)
		$this->timer_start();
		
		
		if($execRows)
		{
			$sql_arr=explode(';',$query);
			foreach ($sql_arr as $sql_o)
			{
				@mysqli_query($conn2,$sql_o);
			}
		}
		else
		{
			$this->result = @mysqli_query($conn2,$query);
		}
		++$this->num_queries;

		if (SAVEQUERIES)
		$this->queries[] = array( $query, $this->timer_stop() );

		// If there is an error then take note of it..
		if ( mysqli_error($conn2) ) {
			$this->print_error();
			return false;
		}


		if ( preg_match("/^\\s*(insert|delete|update|replace) /i",$query) ) {
			$this->rows_affected = mysqli_affected_rows($conn2);
			// Take note of the insert_id
			if ( preg_match("/^\\s*(insert|replace) /i",$query) ) {
				$this->insert_id = mysqli_insert_id($conn2);
			}
			// Return number of rows affected
			$return_val = $this->rows_affected;
		} else {
			$i = 0;
			while ($i < @mysqli_num_fields($this->result)) {
				$this->col_info[$i] = @mysqli_fetch_field($this->result);
				$i++;
			}
			$num_rows = 0;
			while ( $row = @mysqli_fetch_object($this->result) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			@mysqli_free_result($this->result);
			@mysqli_close($this->result);

			// Log number of rows the query returned
			$this->num_rows = $num_rows;

			// Return number of rows selected
			$return_val = $this->num_rows;
		}

		return $return_val;
	}

	// ==================================================================
	//	Get one variable from the DB - see docs for more detail
	function insert_update($table,$obj,$id){
		if(!empty($obj[$id])){
			$sql = "update $table set ";
			$sql1 = '';
			foreach ($obj as $key => $val) {
				if($key!=$id){
					$sql1.=','.$key."='".$val."'";
				}
			}
			if(!empty($sql1)){
				$sql1 = substr($sql1,1);
				$sql.=$sql1;
				$sql.=" where $id=".$obj[$id];
			}
			$this->query($sql);
			return $obj[$id];
		}else{
			$sql = "insert into $table(";
			$sql1 = '';
			$sql2 = '';
			foreach ($obj as $key => $val) {
				if($key!=$id){
					$sql1.=','.$key;
					$sql2.=",'".$val."'";
				}
			}
			if(!empty($sql1)){
				$sql1 = substr($sql1,1);
				$sql2 = substr($sql2,1);
				$sql.=$sql1.') value('.$sql2.')';
			}
			$this->query($sql);
			return $this->get_var("select last_insert_id();");
		}
	}
	function get_var($query=null, $x = 0, $y = 0) {
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";
		if ( $query )
		$this->query($query);

		// Extract var out of cached results based x,y vals
		if ( $this->last_result[$y] ) {
			$values = array_values(get_object_vars($this->last_result[$y]));
		}

		// If there is a value return it else return null
		return (isset($values[$x]) && $values[$x]!=='') ? $values[$x] : null;
	}

	// ==================================================================
	//	Get one row from the DB - see docs for more detail

	function get_row($query = null, $output = OBJECT, $y = 0) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		if ( $query )
		$this->query($query);

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars($this->last_result[$y]) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values(get_object_vars($this->last_result[$y])) : null;
		} else {
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}
	}

	// ==================================================================
	//	Function to get 1 column from the cached result set based in X index
	// se docs for usage and info

	function get_col($query = null , $x = 0) {
		if ( $query )
		$this->query($query);

		// Extract the column values
		for ( $i=0; $i < count($this->last_result); $i++ ) {
			$new_array[$i] = $this->get_var(null, $x, $i);
		}
		return $new_array;
	}

	// ==================================================================
	// Return the the query as a result set - see docs for more details

	function get_results($query = null, $output = OBJECT) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
		$this->query($query);

		// Send back array of objects. Each row is an object
		if ( $output == OBJECT ) {
			return $this->last_result;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			if ( $this->last_result ) {
				$i = 0;
				foreach( $this->last_result as $row ) {
					$new_array[$i] = (array) $row;
					if ( $output == ARRAY_N ) {
						$new_array[$i] = array_values($new_array[$i]);
					}
					$i++;
				}
				return $new_array;
			} else {
				return null;
			}
		}
	}


	// ==================================================================
	// Function to get column meta data info pertaining to the last query
	// see docs for more info and usage

	function get_col_info($info_type = 'name', $col_offset = -1) {
		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				foreach($this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}

	function timer_start() {

		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$this->time_start = $mtime[1] + $mtime[0];
		return true;
	}

	function timer_stop($precision = 3) {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$time_end = $mtime[1] + $mtime[0];
		$time_total = $time_end - $this->time_start;
		return $time_total;
	}

	function bail($message) { // Just wraps errors in a nice header and footer
		if ( !$this->show_errors )
		return false;
		header( 'Content-Type: text/html; charset=utf-8');
		echo <<<HEAD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<title>错误页面</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style media="screen" type="text/css">
		<!--
		html {
			background: #eee;
		}
		body {
			background: #fff;
			color: #000;
			font-family: Georgia, "Times New Roman", Times, serif;
			margin-left: 25%;
			margin-right: 25%;
			padding: .2em 2em;
		}
		
		h1 {
			color: #006;
			font-size: 18px;
			font-weight: lighter;
		}
		
		h2 {
			font-size: 16px;
		}
		
		p, li, dt {
			line-height: 140%;
			padding-bottom: 2px;
		}
	
		ul, ol {
			padding: 5px 5px 5px 20px;
		}
		#logo {
			margin-bottom: 2em;
		}
		-->
		</style>
	</head>
	<body>
	<h1 id="logo"></h1>
HEAD;
		echo "<div><font color=\"Red\">出现这种错误的原因是可能您还没有安装数安装您的数据库</font></div>";
		echo $message;
		echo "</body></html>";
		die();
}
}
?>