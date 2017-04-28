<?php
class Kuangjia{
	public function __construct(){

	}
	public static function start(){
		self::parseUrl();
	}

	/**
	*
	**/
	private static function parseUrl(){
		//var_dump($_SERVER);
		$args = $_SERVER['REQUEST_URI'];
		$tmp_path = explode('?',$args);
		$url_args = $tmp_path[0];
		$ctrl_args = explode('/' , $url_args);
		$path_args=array();
		foreach ($ctrl_args as $value) {
			if($value != '' && $value != 'index.php'){
				$path_args[]=$value;
			}
		}
		$len = count($path_args);
		if( $len == 0){
			$ctrl_url = DR_CTRLPATH."Index/";
			$ActionName = "IndexController.class.php";
			$ctrl_dir = $ctrl_url.$ActionName;
			$method = "index";
			$ActionClass= "Index"."Controller";
		}elseif($len == 1){
			$ctrl_url = DR_CTRLPATH.ucfirst($path_args[ 0 ])."/";
			$ActionName = ucfirst($path_args[ 0 ])."Controller.class.php";
			$ctrl_dir = $ctrl_url.$ActionName;
			$ActionClass = ucfirst($path_args[ 0 ])."Controller";
			$method = "index";
		}else{
			$method = $path_args[ $len-1 ];
			$ActionName = ucfirst($path_args[ $len-2 ] )."Controller.class.php";
			$ActionClass = ucfirst($path_args[ $len-2 ] )."Controller";
			unset($path_args[ $len-1 ]);
			$_path_args=array();
			foreach ($path_args as $path_str) {
				$_path_args[] = ucfirst($path_str);
			}
			$ctrl_url = DR_CTRLPATH.implode("/", $_path_args)."/";
			$ctrl_dir = $ctrl_url.$ActionName;
		}
		// $ctrl_url = DR_CTRLPATH.$path_args['0']."/";

		// $ActionName = ucfirst($path_args['0'])."Controller.class.php";
		// $ActionClass = ucfirst($path_args['0'])."Controller";
		// $method = ($path_args['1']);
		// $ctrl_dir = $ctrl_url.$ActionName;
		if( !file_exists( $ctrl_dir ) ){
			die("Controller File is not exists !");
		}
	 	require_once $ctrl_dir;
	 	if( !class_exists( $ActionClass ) ){
	 		die("Controller Class is not exists !");
	 	}
	 	if( !method_exists(  $ActionClass ,$method ) ){
	 		die(" Method of {$ActionClass} is not exists : {$method} ");
	 	}
	 	$action =new $ActionClass();

	 	require_once DR_COREPATH.'Loader.class.php';//加载导入操作类

	 	Loader::import('View');//加载控制器模板类
		$View = new View();
		$action->View = $View;

		Loader::import('Code');//加载验证码类
		$Code = new Code();
		$action->Code = $Code;

		Loader::import('Page');//加载分页类
		$Page = new Page();
		$action->Page = $Page;

		require_once DR_DBPATH.'Dbfactory.class.php';//加载数据库操作类模板
		$db =Dbfactory::create();//创建数据库操作类对象，默认创建mysql
		$action->db = $db;//将$db对象写入$action
		
		$action->$method();



	}
}//类结束
