<?php
error_reporting("E_ALL");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("log_errors", 1);
ini_set('error_log',"/var/log/php-scripts.log");
ini_set("memory_limit","200M");
error_log( "Hello, errors!" );
$tsp=time();
$tspi=time-(3600*24*7);


$mon= mysqli_connect("127.0.0.1","system","lazerx0!","mon");
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
} 


$conscoax=$mon->query("select * from connections where type=\"COAX\" and date_end=\"0000-00-00\" and equip_id!=\"\" ");
while($conx=$conscoax->fetch_assoc())
{
	$prop=$mon->query("select * from properties where id=\"".$conx['property_id']."\" ")->fetch_assoc();
	$records=$mon->query("select sum(traf_tx),sum(traf_rx) from history_modem where mac=\"".$conx['equip_id']."\" and timestamp>$tspi")->fetch_assoc();
	echo $prop['ref'].";".$prop['address'].";".$conx['equip_id'].";".round($records['sum(traf_rx)']/1048000) .";".round($records['sum(traf_tx)']/1048000) ." \n";
}



$consf=$mon->query("select * from connections where type=\"GPON\" and date_end=\"0000-00-00\" and equip_id!=\"\" ");
while($conx=$consf->fetch_assoc())
{
	$prop=$mon->query("select * from properties where id=\"".$conx['property_id']."\" ")->fetch_assoc();
	$records=$mon->query("select sum(gpon_traf_tx),sum(gpon_traf_rx) from history_ont where fsan=\"".$conx['equip_id']."\" and timestamp>$tspi")->fetch_assoc();
	echo $prop['ref'].";".$prop['address'].";".$conx['equip_id'].";".round($records['sum(gpon_traf_rx)']/1048000) .";".round($records['sum(gpon_traf_tx)']/1048000) ." \n";



}







