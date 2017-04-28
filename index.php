<?php
/**
*kuangjia  

**/
//框架路径
define( 'BASEPATH' ,		'/var/www/html/kuangjia/Library/' );


//项目目录结构路径
define( 'APPPATH' ,			dirname(__FILE__) . '/App/'	);	//项目当前目录
define( 'DR_CONFPATH' ,			APPPATH . 'Conf/'		);	//配置文件目录
define( 'DR_STATICPATH' ,		APPPATH . 'Static/'		);	//静态文件目录
define( 'DR_MODELPATH' ,		APPPATH . 'Model/'		);	//数据库模型目录
define( 'DR_CTRLPATH' ,			APPPATH . 'Action/'		);	//控制器目录
define( 'DR_VIEWPATH' ,			APPPATH . 'View/'		);	//模板文件目录
define( 'DR_CACHEPATH' ,		APPPATH . 'Cache/'		);	//缓存文件目录
define( 'DR_UPLOADPATH' ,		APPPATH . 'Uploadfiles/'		);	//上传文件目录
define( 'DR_LIB',				APPPATH . 'Lib/'			);	//
define( 'DR_COREPATH' ,			BASEPATH . 'Core/'		);	//导入操作类
define( 'DR_DBPATH',			DR_COREPATH . 'Mysql/'	);	//数据库操作类目录


require_once BASEPATH.'Kuangjia.class.php';
Kuangjia::start();
?>
