<?php
class ArticleController{
	public function index(){
		echo "this is Article_index function";
	}
	
	public function test(){
		$_where=array("id" =>"21","|name" => "小花");
		$_fields = 'id ,name ,school';
		$orderby = array("id" , "desc" );
		$_limit = array("1","2");
		$rows=$this->db->getList('test');
		$this->View->assign('rows',$rows);
		// print_r($rows);
		// $data=array("name" => '小马',"school" => "清华大学", "age" => "18");
		// $res=$this->db->update('test',$data, $_where);
		// var_dump($res);
		$pageLinks = $this->Page->show(100 , 2 ,2 ,2);
		$offset = $this->Page->getOffset();
		echo $offset;
		$this->View->assign('page',$pageLinks);

		$title = "小名";
		$arr=array(array("name" => "xiaof" ,"sex" => "女"));
		$this->View->assign( 'arr' , $arr );
		$this->View->assign( 'title' , $title );
		$ret=$this->View->display('view.html' , 1);//display( '模板名'  , '模板缓存时间,默认为0s' ),其返回值为编译完成后的html内容
		echo $ret;//输出html内容

	}

	public function createCode(){
		$this->Code->getcode("90","32");
	}
}//类结束

?>
