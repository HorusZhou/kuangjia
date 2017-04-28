<?php
/**
* 
*/
class Loader{

	public static function import($classname ,$type = false){
		if( $type == false ){
			$file_path = DR_COREPATH."{$classname}.class.php";
		}else{
			$file_path = DR_LIB."{$classname}.class.php";
		}
		require_once $file_path;
	}

	public static function config($configname){
		$file_path = DR_CONFPATH."{$configname}.config.php";
		return require_once $file_path;
	}
}//类结束
?>