<?php
ob_start();
if (!isset($_GET["File"])) {  
	print "no file selsect"; exit(); 
} 
$file =$_GET["File"];
$type = end(explode('.',$file));
if($_GET['usernameModel']=='basename'){
	$fileName = null;
}else{
	$arrstr = explode('/',$file);
	$last = end($arrstr);
	$lastarr = explode('.',$last);
// 	array_pop($lastarr);
	$fileName = implode('.',$lastarr);
	
}	
include_once "class.httpdownload.php";
$object = new httpdownload();  
$result=$object->set_byfile($file); 
if($type =='ipa'){
	$object->mime ='.ipa application/iphone-package-archive';
}elseif($type =='apk'){
	$object->mime ='.apk application/vnd.android.package-archive';
}else{
 }
if(!$result) {
  	print "下载失败 "; exit();
 }
$object->filename = $fileName;
$object->download(); 
ob_end_flush();
?>
