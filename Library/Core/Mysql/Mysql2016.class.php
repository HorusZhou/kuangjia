<?php

require_once 'Idb.class.php';
class Mysql implements Idb
{
	
	private $conn = NULL;
	private $_debug = false;
	private $hosts = array();

	public function __construct($host)
	{
		$this->hosts = $host;
	}
	
	private function connnect( )
	{
		$this->conn = mysqli_connect($this->hosts['HOST'],$this->hosts['USER'],$this->hosts['PWD']);
		if ( mysqli_connect_errno() )
		{
			die('Connect Error: ' . mysqli_connect_error());
		}
		mysqli_select_db($this->conn,$this->hosts['DB']);
		mysqli_query($this->conn,'set names utf8');
		date_default_timezone_set('PRC');
	}
	
	/**
	 *
	 * $data = array(
		'name' => 'wein',
		'age'  => 2,
		'city' => '东莞'
	 );
	 */
	public function insert( $_table, $_data )
	{
		$fileds = '';
		$values = '';
		foreach( $_data as $key => $val )
		{	
			if ( $fileds == '' ) $fileds = $key;
			else $fileds .= ','.$key;
			
			if ( $values == '' ) $values ="'{$val}'";
			else $values .= ",'{$val}'";
		}
		
		$sql = "INSERT INTO {$_table} ( {$fileds}) VALUES ( {$values})";
		$res = $this->query($sql);

		if ( $res == FALSE ) return FALSE;
		return mysqli_insert_id($this->conn);
	}

	public function query($sql)
	{
		if ( $this->conn == NULL )	$this->connnect();
		$res = mysqli_query($this->conn,$sql);
		if ( $this->_debug ) echo "Query sql: {$sql}<br/>";
		if ( $res == FALSE ) return FALSE;
		return $res;
	}

	/**
	* $where = array(
		'name' => "='wein'",
		'&age' => '=2'
	)
	*
	**/
	public function delete( $_table, $_where )
	{
		$sql = "DELETE FROM {$_table} WHERE ";
		$where = '';
		foreach( $_where as $key => $val )
		{
			if ( $where == '' )	$where = "{$key}{$val}"; 		
			else 
			{
				$where .= " {$key}{$val}";
			}
		}

		$sql .= "{$where}";
		$res = $this->query($sql);
		if ( $res == FALSE ) return FALSE;
		
		return  mysqli_affected_rows($this->conn);//需要连接标记
	}

	public function setDebug( $_debug = false )
	{
		$this->_debug = $_debug;	
	}


}


?>
