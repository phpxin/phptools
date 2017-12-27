<?php

header("Content-Type: text/plain") ;
include '../UaParser.class.php' ;
$fp = fopen('uaTester.txt', 'r') ;

$result = [] ;
while($line=fgets($fp)){
	$line = trim($line) ;
	$uaParse = new UaParser($line) ;
	//
	$result[] = [
		'ua' => $line ,
		'ret' => $uaParse->getRet() 
	] ;
}



var_dump($result) ;