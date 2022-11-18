
<?php
require_once '../../connection.php';
session_start();




$fp=fopen("prado.txt","r");

while(!feof($fp))
{
$ref=trim(fgets($fp));
echo $ref;

$propid=$mon3->query("select * from properties where ref=\"$ref\"")->fetch_assoc();
echo " - ".$propid['id']." - ";


$mon3->query("insert into connections (property_id,type) values (\"".$propid['id']."\",\"ETH\") ");
$conid=$mon3->insert_id;	
echo $conid;

$mon3->query("insert into services (connection_id,type) values (\"".$conid."\",\"TV\") ");
$servtv=$mon3->insert_id;	

$mon3->query("insert into services (connection_id,type) values (\"".$conid."\",\"INT\") ");
$servtv=$mon3->insert_id;	


echo "\n";
}