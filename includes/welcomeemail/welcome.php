<?php
$wlead=$mon3->query("select * from property_leads where id=".$lead_id." ")->fetch_assoc();
$int=$mon3->query("select name from int_services where id=".$wlead['internet_prof']."")->fetch_assoc();
$wint=$int['name']; 
$wtv=$wlead['tv'];
$ref=$mon3->query("select ref from properties where id=".$wlead['prop_id']."")->fetch_assoc()['ref'];

// RECONNECTION
if($wlead['is_reconnection'] == 1 && $wlead['is_changeover'] == 0)
{
	$ssid=$mon3->query("select service_attributes.value 
	from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
	where connections.id=".$wlead['lead_conn_id_rcn']." and services.type=\"INT\" and service_attributes.name=\"wifi_ssid\" ")->fetch_assoc();

	$pass=$mon3->query("select service_attributes.value 
	from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
	where connections.id=".$wlead['lead_conn_id_rcn']." and connections.type='".$wlead['con_type']."' and services.type=\"INT\" and service_attributes.name=\"wifi_key\" ")->fetch_assoc();
}

// CHANGE OVER
else if($wlead['is_reconnection'] == 0 && $wlead['is_changeover'] == 1)
{
	$ssid=$mon3->query("select service_attributes.value 
	from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
	where connections.id=".$wlead['lead_conn_id_chg_over']." and services.type=\"INT\" and service_attributes.name=\"wifi_ssid\" ")->fetch_assoc();

	$pass=$mon3->query("select service_attributes.value 
	from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
	where connections.id=".$wlead['lead_conn_id_chg_over']." and services.type=\"INT\" and service_attributes.name=\"wifi_key\" ")->fetch_assoc();
}

// NO RECONNECTION, NO CHANGE OVER
else if($wlead['is_reconnection'] == 0 && $wlead['is_changeover'] == 0)
{
	$ssid=$mon3->query("select service_attributes.value 
	from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
	where connections.property_id=".$wlead['prop_id']." and connections.type='".$wlead['con_type']."' and services.type=\"INT\" and service_attributes.name=\"wifi_ssid\" ")->fetch_assoc();

	$pass=$mon3->query("select service_attributes.value 
	from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
	where connections.property_id=".$wlead['prop_id']." and connections.type='".$wlead['con_type']."' and services.type=\"INT\" and service_attributes.name=\"wifi_key\" ")->fetch_assoc();
}
/*$ssid=$mon3->query("select service_attributes.value 
from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
where properties.id=".$wlead['prop_id']." and services.type=\"INT\" and service_attributes.name=\"wifi_ssid\" ")->fetch_assoc();
$pass=$mon3->query("select service_attributes.value 
from properties left join connections on properties.id=connections.property_id left join services on services.connection_id=connections.id left join service_attributes on service_attributes.service_id=services.id
where properties.id=".$wlead['prop_id']." and services.type=\"INT\" and service_attributes.name=\"wifi_key\" ")->fetch_assoc();*/
$ssid=$ssid['value'];
$pass=$pass['value'];

echo "$ref ".$wlead['con_type']." $wint $wtv $ssid $pass lead: $lead_id";


if(substr($ref,0,3)=="ESP")
{	
		include "welcome_pga.php";
}
elseif($wlead['con_type']=="FWA")
{
	include "welcome_fwa_test.php"; // ALTERACAO TOMAS SILVA
}
else
{
	include "welcome_std.php";

}
//$wifi_ssid
//$wifi_key

/*
Enjoy our super fast internet service of  <strong> $wint </strong> ";
if($wtv>0)
	$welcomeemail.= ", with access to TV channels via our partner AMLA,";

$welcomeemail.= "and 24/7 technical support.<br>
<br>
Your Wi-Fi password is: <u>$wifi_key</u></span></div>
*/




