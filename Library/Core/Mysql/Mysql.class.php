<?php
require_once "Idb.class.php";
class Mysql implements Idb{
	private $conn 	= NULL;
	private $debug 	= FALSE;
	private $host 	= array();
	
	/**
	*构造函数，连接数据库
	*@param $_host->要连接的数据库配置信息，一维数组
	*/
	public function __construct($_host){
		$this->host   =  $_host;
	}

	private function connect(){
		$this->conn  =	mysqli_connect($this->host['server'],$this->host['user'],$this->host['passwd'],$this->host['db'],$this->host['port']); 
		 echo "---连接数据库---";
		if( ! $this->conn){
			die( '数据库连接失败：'.mysqli_connect_errno() );
		}
		mysqli_set_charset($this->conn,$this->host['charset']);
		date_default_timezone_set('PRC');
	}

	private function query( $sql ){
		if( $this->conn == NULL ){ $this->connect(); }//判断是否连接数据库，若无，则连接数据库
		$ret=mysqli_query($this->conn,$sql);//执行sql语句
		if( $this->debug ){ echo "Query sql :".$sql ; }//若debug开启，则输出sql语句
		if( $ret == FALSE ){  return FAlse; }
		return $ret;
	}

	/**
	*插入新记录到数据库
	*@param $_table 	数据表名称
	*@param $data 	要插入的数据数组
	*@return  新增数据id值	 |	false
	*/
	public function add( $_table, $data){
		//INSERT INTO table_name set('name','age','school') VALUES ('','','');
		$sql="INSERT INTO "."{$this->host['prefix']}".$_table." (";
		$fields='';
		$values='';
		foreach ($data as $key => $value) {
			if($fields == ''){
				$fields = "`{$key}`";
			}else{
				$fields .= ", `{$key}`";
			}

			if($values == '' ){
				$values = "'{$value}'";
			}else{
				$values .= ", '{$value}'";
			}
			
		}
		$sql .= "{$fields} ) VALUES ( {$values} )";
		$ret = $this->query( $sql );
		if( $ret == false ){  return false;  }
		return mysqli_insert_id($this->conn);
	}

	/**
	*删除数据库记录
	*@param $_table 	数据表名称
	*@param $_where 	where条件，数组形式或字符串
	*@return  int()	|	false  	删除成功返回影响条数,否则返回false
	*/
	public function delete($_table , $_where){
		//DELETE FROM $_table WHERE id=1 and name="" ;
		$sql ="DELETE FROM ".$this->host['prefix'].$_table;
		
		if( $_where != '' ){
			$where = '' ;
			if( is_string($_where) ){
				$where = $_where;
			}
			elseif( is_array($_where) ){
				foreach ($_where as $key => $value) {
					if(strpos( $key , '|') !== FALSE ){//strpos查找字符串并返回首次出现的索引，所以要排除0
						$key = str_replace('|', ' OR ', $key);
					}else{
						if( $where != '' ){
							$key = " and {$key}";
						}
					}
					$where .= $key."="."'{$value}'";
				}
			}
			$sql .= " WHERE ".$where;
		}
		$ret=$this->query( $sql );
		if($ret == false){
			return false;
		}
		return mysqli_affected_rows( $this->conn );
	}

	/**
	*更新数据库记录
	*@param $_table 	数据表名称，必须
	*@param $data 	更新的内容，必须，数组形式
	*@param $_where 	where条件，必须，数组形式或字符串，可为空（为空则更新整个数据表）
	*@return  int()	|	false  	更新成功返回影响行数（int型）,否则返回false
	*/
	public function  update( $_table ,$data ,$_where ){
		//UPDATE $_table set $key=new_value, $key2=new_value2  WHERE $_where
		$sql="UPDATE ".$this->host['prefix'].$_table." SET ";
		$fields 	= '' ;
		foreach( $data as $key => $value ){
			if( $fields == '' ){
				$fields .= "`{$key}`"."="."'{$value}'";
			}else{
				$fields .= " , `{$key}`"."="."'{$value}'";
			}
		}
		$sql .= $fields;

		if( $_where != '' ){
			$where = '' ;
			if( is_string($_where) ){
				$where=$_where;
			}elseif( is_array( $_where ) ){
				foreach ($_where as $_key => $_value) {
					if(strpos($_key,'|') !== FALSE ){
						$_key = str_replace('|', ' or ', $_key);
					}else{
						if( $where != '' ){
							$_key = " and {$_key}";
						}
					}
					$where .= $_key."="."'{$_value}'";
				}
			}
			$sql .=" WHERE ".$where;
		}
		$ret = $this->query( $sql );
		if($ret == false){
			return false;
		}
		return mysqli_affected_rows( $this->conn );
	}

	/**
	*获取单条数据库记录
	*@param $_table 	数据表名称 ，必须
	*@param $_fields 	要获取的字段，数组或字符串形式，可选，为空则返回所有字段
	*@param $_where 	where条件，数组或字符串形式，可选，为空则返回所有记录的第一条
	*@return  $row  |	false  	成功返回获取到的内容（一维数组），否则返回false
	*/
	public function get( $_table , $_fields = '', $_where = '' ){
		//SELECT $_fields  FROM $_table WHERE id=1 AND name="小马"
		$sql ="SELECT ";
		$fields='';
		if(is_string($_fields)){
			$fields=$_fields;
		}elseif(is_array($_fields)){
			$fields=implode(",", $_fields);
		}elseif($_fields == '' ){
			$fields="*";
		}
		$sql .=$fields ." FROM {$this->host['prefix']}{$_table}";

		if( $where != '' ){
			$where='';
			if( is_string($_where) ){
				$where=$_where;
			}
			elseif( is_array($_where) ){
				foreach ($_where as $key => $value) {
					if(strpos($key,'|') !== FALSE ){
						$key = str_replace('|', ' OR ', $key);
					}else{
						if( $where != '' ){
							$key = " and {$key}";
						}
					}
					$where .= $key."="."'{$value}'";
				}
			}
			$sql .= " WHERE ".$where;
		}

		$ret = $this->query( $sql );
		if($ret == false){
			return false;
		}
		$rows=mysqli_fetch_assoc( $ret );//查询结果，为二维数组
		return $rows[0];//取索引0,返回一维数组
	}

	/**
	*获取多条数据库记录
	*@param $_table 		数据表名称，必须
	*@param $_fields 		要获取的字段，数组或字符串形式，可选，为空则返回所有字段
	*@param $_where 		where条件，数组或字符串形式，可选，为空则返回所有记录
	*@param $_orderby 	orderby条件，数组或字符串形式，可选，为空则按id升序排序
	*@param $_limit 		limit条件，数组或字符串形式，可选
	*@return  $rows	|	false  	成功返回获取到的内容（二维数组），否则返回false
	*/
	public function getList($_table , $_fields = '' , $_where = '' ,$_orderby = '' , $_limit = '' ){//赋空值
		//SELECT $fields FROM $_table WHERE id=2 and name="小马"  ORDER BY id desc LIMIT $_start , $_num
		$sql ="SELECT ";
		if($_fields != '' ){
			$fields = '';
			if( is_string( $_fields ) ){
				$fields = $_fields;
			}elseif( is_array( $_fields ) ){
				$fields = implode(" , " ,$_fields);
			}
		}else{
			$fields = "*";
		}
		$sql .=$fields." FROM ".$this->host['prefix'].$_table;
		
		if( $_where != '' ){
			$where='';
			if(is_string($_where)){
				$where=$_where;
			}elseif(is_array($_where)){
				foreach( $_where as $key => $value){
					if(strpos( $key , '|') !== FALSE){
						$key= str_replace('|', " OR ", $key );
					}else{
						if($where != ''){
							$key = "and {$key}";
						}
					}
					$where .= $key."="."'{$value}'";
				}
			}
		$sql .=" WHERE ".$where;
		}

		if($_orderby != ''){
			$range = "ASC";//默认排序为升序
			if( is_string( $_orderby )){
				$orderby = $_orderby;
			}
			elseif( is_array( $_orderby ) ){
				$orderby = array();
				foreach ($_orderby as $value) {
					if( strtoupper( $value ) != "DESC" && strtoupper( $value ) != "ASC"){
						$orderby[] = $value;
					}else{
						$range = strtoupper( $value );
					}
				}
				$orderby = implode("," , $orderby);
			}
			$sql .= " ORDER BY ".$orderby." ".$range;
		}

		if( $_limit != '' ){
			if( is_string( $_limit ) ||is_int( $_limit ) ){
				$limit = $_limit;
			}elseif( is_array( $_limit ) ){
				$limit = implode("," , $_limit);
			}
			$sql .=" LIMIT ".$limit;
		}

		$ret = $this->query( $sql );
		if($ret == false){
			return false;
		}
		if( $row = mysqli_fetch_assoc($ret) ){
			$rows[] = $row;
			while( $row = mysqli_fetch_assoc($ret) ){
				$rows[] = $row;
			}
			return $rows;
		}else{
			echo  "查询结果为空！" ;
			return false;
		}
		
	}

	/**
	*统计方法
	*@param $_table 		数据表名称，必须
	*@param $_where 		where条件，数组或字符串形式，可选，为空则统计所有记录
	*@return  int	|	false  	成功返回统计数量，否则返回false
	*/
	public function count( $_table , $_where = '' ){
		//SELECT COUNT(*) FROM $_table WHERE name="小小"
		$sql = "SELECT COUNT(*) FROM ".$this->host['prefix'].$_table;
		if( $_where != '' ){
			$where='';
			if(is_string($_where)){
				$where=$_where;
			}
			elseif(is_array($_where)){
				foreach( $_where as $key => $value){
					if(strpos( $key , '|') !== FALSE){
						$key= str_replace('|', " OR ", $key );
					}else{
						if($where != ''){
							$key = "and {$key}";
						}
					}
					$where .= $key."="."'{$value}'";
				}
			}
		$sql .=" WHERE ".$where;
		}
		$ret = $this->query( $sql );
		if($ret == false){
			return false;
		}
		$arr = mysqli_fetch_array( $ret );//执行结果为一维索引数组，取索引0即为COUNT的数量
		return intval( $arr[0] );//转换为整型，并返回
	}

	/**
	*调试方法
	*@param $_debug 	为true则开启调试，为false则关闭调试
	*@return 无
	*/
	public function setDeBug( $_debug = FALSE ){
		$this->debug = $_debug;
	}
}//类结束

