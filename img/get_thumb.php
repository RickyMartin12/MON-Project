<?php
ini_set("display_errors", 0);
error_reporting(E_ALL);

$sid = $_GET['sid'];
if($_GET['for']!="")
{
	$for = $_GET['for'];
}
else
{
	$for="jpg";
}
$file="/home/www/scripts/thumbs/img/" . $sid . "." . $for;
header('Content-Type: image/jpeg');
readfile($file);
//echo $file;

?>