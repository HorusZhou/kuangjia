<?php
class Dbfactory{
	private static $_classes = array();
	private static $host = array();
		
	public static function create($type = "Mysql"){
		self::$host = Loader::config('Db');//导入Db配置文件
		if(!isset(self::$_classes["{$type}"])){//单例模式，防止多次实例化对象
			require_once $type.'.class.php';
			self::$_classes["{$type}"] = new $type(self::$host);
		}
		return self::$_classes["{$type}"];//返回数据库操作类对象
	}

}//类结束


?>
