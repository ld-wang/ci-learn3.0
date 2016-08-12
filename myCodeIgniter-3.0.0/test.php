<?php


var_dump(   $_SERVER['REQUEST_METHOD']);
var_dump(	$_SERVER['REQUEST_URI'] );
var_dump(	$_SERVER['QUERY_STRING']);
var_dump(	$_SERVER['SCRIPT_NAME'] );
var_dump(	$_SERVER['PHP_SELF']   );
var_dump(__FILE__);
var_dump(pathinfo());

die;
var_dump($argc);
var_dump($_SERVER['argv']);

die;

$a = array(1,2,3);
$b = array(10,20,30);

$c = array_map('map_func',$a,$b);

function map_func($v1,$v2){
	
	$v3 = $v1+$v2;
	return ($v3);
}

var_dump($a);
var_dump($b);
var_dump($c);


var_dump($GLOBALS);

die;


function &test2(){
	static $str = 5;
	echo $str;echo '<br>';
	return $str;
}
$a = &test2();
$a = 6;
$a = &test2();
die;



var_dump( pathinfo(__FILE__));  echo '<br>';  //多个信息的数组
echo pathinfo(__FILE__, PATHINFO_BASENAME);  echo '<br>';//文件名
echo pathinfo(__FILE__, PATHINFO_DIRNAME);  echo '<br>';//目录名
echo pathinfo(__FILE__, PATHINFO_EXTENSION);  echo '<br>';  //扩展名


die;


$hello  = "Hello World";

$trimmed = trim($hello, "Hdle");
var_dump($trimmed);

die;

$realpath = realpath('../ahjy');

echo $realpath;   //D:\php2\ahjy


die;
echo __FILE__;echo '<br>';    //D:\php2\ci3.0\test.php

echo dirname(__FILE__);echo '<br>';   //D:\php2\ci3.0

die(123);




?>