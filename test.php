<?php
include 'HttpCurl.class.php' ;


try{
	//$hc = new HttpCurl('https://testssl.com');
	//$hc = new HttpCurl('http://www.baidu.com');
	$hc = new HttpCurl('https://www.baidu.com/');
	$hc->exec() ;
	$result = $hc->getResponse();
	echo htmlspecialchars($result);
}catch (Exception $e){
	echo '请求失败 : ' , $e->getMessage();
}