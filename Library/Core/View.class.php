<?php
	class View{
		public $_DATA =array();
		private $_cache_file = NULL;
		private 	$_expire_time  =	 0;
		private $rules = array(
				'/\<\?([^=])/'		=>	'<?php $1',
				/**
				* <?=${data.name}?>	=>	<?php echo $data['name']?>
				*/
				'/\<\?\=\$\{(\w+)\.(\w+)\}/'	=>	'<?php echo $$1[\'$2\']' ,
				/**
				* <?=${title}?>	=>	<?php echo $title?>
				*/
				'/\<\?\=\$\{(\w+)\}/'	=>	'<?php echo $this->_DATA[\'$1\']' ,
				/**
				* <? for( $val : ${result}  )  {  ?>	=>	<?php foreach(  $result as  $val){  ?>
					<? } ?>						=>	}
				*/
				'/for\(\s{0,}\$(\w+)\s{0,}\:\s{0,}\$\{(\w+)\}\s{0,}\)\{/'	=>	' foreach(  $this->_DATA[\'$2\'] as  $$1 ){ ' ,
				// include common/header.html 	=>	require  '$this->getIncludeFile(common/header.html) '
				'/include\s{0,}([\w\.\/]+)/'	=>	'require \$this->getIncludeFile(\'$1\') ' ,

				);

		public function display( $tpl , $expire_time = 0 ){
			$this->_expire_time = $expire_time;
			$isCache=$this->isCache( $tpl );
			if( $isCache == FALSE){
				$this->compile($tpl);
			}

			ob_start();//ob流开启
			require_once $this->_cache_file;//包含缓存文件（绝对路径）
			$html = ob_get_contents();//将缓存文件编译并返回编译后的结果
			ob_clean();//清除ob流
			return $html;//输出编译缓存文件后的结果
		}

		public function isCache( $tpl ){
			$this->_cache_file = DR_CACHEPATH."tpl/".md5($tpl).".php";//将传入的路径用md5转换，不管目录有多少层，都只存到tpl文件夹
			if( $this->_expire_time == -1 ){//如果$_expire_time设为-1,则永久缓存
				return TRUE;
			}
			$last_time = filemtime($this->_cache_file);//获取缓存文件最后更新时间
			if( time() - $last_time > $this->_expire_time){//判断缓存是否过期
				return FALSE;
			}
			return TRUE;
		}
		
		/**
		*模板编译方法
		*@param 	$tpl->模板文件路径
		*@return 	 无
		*/
		public function compile($tpl){
			$tpl_path = DR_VIEWPATH.$tpl;

			$html = file_get_contents($tpl_path);
			if( $html == FALSE){
				die('ERROR not such file : {$tpl}');
			}
			$html = preg_replace( array_keys($this->rules) ,$this->rules , $html);
			if( $html == NULL){
				die('模板格式错误！');
			}
			
			$cache_path = dirname($this->_cache_file);//dirname表示返回上级目录，获取缓存文件路径
			if( ! file_exists($this->_cache_file) ){
				@mkdir($cache_path, 0777, TRUE );//创建缓存文件夹
			}
			$ret = file_put_contents($this->_cache_file, $html );//将$html写入到$cache_file文件
			if( $ret == FALSE){
				die( 'ERROR not such file : {$this->_cache_file}' );
			}
		}

		public function getIncludeFile( $tpl ){
			$isCache=$this->isCache( $tpl );
			if( $isCache == FALSE){
				$this->compile($tpl);
			}
			 return $this->_cache_file;
		}

		/**
		*模板注册方法
		*@param $key->变量名称 , 	$val->变量值
		*@return 无
		*/
		public function assign( $key , $val ){
			$this->_DATA[ $key ] = $val ;
		}
	}//类结束
?>
