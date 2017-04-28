<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
<?php  require $this->getIncludeFile('common/page.css')  ?>
</style>
</head>

<body>
<h1><?php echo $this->_DATA['title']?></h1>
<?php   foreach(  $this->_DATA['arr'] as  $value ){  ?>
	<p><?php echo $value['name']?></p>
<?php }?>
<?php   foreach(  $this->_DATA['rows'] as  $value ){  ?>
	<p><?php echo $value['id']?><?php echo $value['name']?><?php echo $value['school']?></p>
<?php }?>
<?php  require $this->getIncludeFile('common/header.html')  ?>
<?php  require $this->getIncludeFile('common/footer.html')  ?>
<?php echo $this->_DATA['page']?>
<img src="/index.php/Article/createCode" />
</body>
</html>
