<?php

$curyear=date("Y");
$curmonth=date("m");
$today=date("Y-m-d");
$now=date("Y-m-d H:i:s");
$outage_emails=$mon3->query("select valor from settings where nome=\"outage_emails\"")->fetch_assoc()['valor'];
//$outage_emails=$outage_emails['valor'];
//echo $outage_emails;

echo "
<script type=text/javascript>
function selend(){
var sels=document.getElementById('stat');
if(sels.options[sels.selectedIndex].value=='closed'){
	document.getElementById('endd').disabled=false;
	document.getElementById('durationid').disabled=true;
}
else
{
	document.getElementById('endd').disabled=true;
	document.getElementById('durationid').disabled=false;
}
};
</script>




<table border=1 width=1000px> 
<tr><td colspan=6> 
<a href=?tickets=1&newticket=1>new ticket</a>
<a href=?tickets=1&newoutage=1>new outage report</a>
";

if($_GET['newoutage']>0)
{
	
echo "




<h3>Report new Outage</h3>
<table width=500px align=center>
<form action=?tickets=1 method=post>
<tr><td>title<td><input type=text name=title>
<tr><td>date start<td><input type=text name=date_start value=\"".date("Y-m-d H:i:s")."\">
<tr><td>estimated duration<td><input type=text name=duration id=durationid> min
<tr><td>date end<td><input type=text id=endd name=date_end disabled value=\"\">
<tr><td>severity<td><select name=severity title=\"

#	Outage Severity	Description	
P1-Critical	60% of the customers are affected with at least one service down, or services are down at the datacenter.	
Example	 OLT QDL fails; no power at VLM headend; Modulator for QDL down	
 &#13; 
P2-Major	20% of the customers are affected with at least one service down, or services are partially affected.	
Example	 Link to lousada Down-60channels down; link to PGA down; OLT down at Balaia	
 &#13; 
P3-Minor	Local issues affecting up to 20% of the customers, or services slightly affected	
Example	Lose of one ISP link means broadband capacity is at 50% of normal
;couple channels down; no EPG	
 &#13; 
P4-Maintenance	The issue is an inconvenience or annoying but core services are operational.	
Example	Maintenances at headend at late hours, or cabling interventions on network	
				


\">

<option style=\"color:red\">P1 - critical</option><option>P2 - major</option><option>P3 - minor</option>
<option>P4 - maintenance</option>
</select> 
<tr><td>status<td><select name=status id=stat onChange=\"selend()\">
<option>open</option><option value=closed>closed</option>
</select>
<tr><td>services affected<td><input type=text name=service_affected>
<tr><td>area affected<td><input type=text name=area_affected>
<tr><td>nr customers<td><input type=text name=nr_clients_affected>
<tr><td>notify<td><input type=text name=notify value=\"".$outage_emails."\">
<tr><td>description<td><textarea rows=10 cols=50 name=description> </textarea>
<input type=hidden name=action value=new>
<tr><td><td><input type=submit name=newoutage>

</form></table>
";
}

elseif($_GET['editoutage']>0)
{
	$outageid=$mon3->real_escape_string($_GET['editoutage']);
	$outage=$mon3->query("select * from outages where id=$outageid")->fetch_assoc();

	echo "




<h3>Update Outage id $outageid</h3>
<table width=500px align=center>
<form action=?tickets=1&updateoutage=$outageid method=post>
<tr><td>title<td><input type=text name=title value=\"".$outage['title']."\">
<tr><td>date start<td><input type=text name=date_start  value=\"".$outage['date_start']."\">
<tr><td>estimated duration<td><input type=text name=duration id=durationid  value=\"".$outage['duration']."\"> min
<tr><td>date end<td><input type=text id=endd name=date_end disabled  value=\"".$outage['date_end']."\">
<tr><td>severity<td><select name=severity title=\"Use critical for complete service down, or headend down. &#13; 
Major for cable damage or areas with no service\">
<option style=\"color:red\" ";if($outage['severity']=="critical") echo "selected"; echo" > critical</option>
<option ";if($outage['severity']=="major") echo "selected"; echo">major</option>
<option ";if($outage['severity']=="minor") echo "selected"; echo">minor</option>
<option ";if($outage['severity']=="maintenance") echo "selected"; echo">maintenance</option>
</select> 
<tr><td>status<td><select name=status id=stat onChange=\"selend()\">
<option ";if($outage['status']=="open") echo "selected"; echo">open</option>
<option value=closed  ";if($outage['status']=="closed") echo "selected"; echo">closed</option>
</select>
<tr><td>services affected<td><input type=text name=service_affected  value=\"".$outage['services_affected']."\">
<tr><td>area affected<td><input type=text name=area_affected  value=\"".$outage['area_affected']."\">
<tr><td>nr customers<td><input type=text name=nr_clients_affected  value=\"".$outage['nr_clients_affected']."\">
<tr><td>notify<td><input type=text name=notify value=\"".$outage_emails."\">
<tr><td>description<td><textarea rows=10 cols=50 name=description> ".  $outage['description']." \n <br> updated by ".$localuser['username']." on $now: \n <br></textarea>
<input type=hidden name=action value=update>
<input type=hidden name=outageid value=$outageid>
<tr><td><td><input type=submit name=updateoutage>

</form></table>
";
	
}





else
{
	
if($_POST['newoutage'] || $_POST['updateoutage'])
{

$title=$mon3->real_escape_string($_POST['title']);
$date_start=$mon3->real_escape_string($_POST['date_start']);	
$duration=$mon3->real_escape_string($_POST['duration']);	
$date_end=$mon3->real_escape_string($_POST['date_end']);
$status=$mon3->real_escape_string($_POST['status']);
$severity=$mon3->real_escape_string($_POST['severity']);
$services_affected=$mon3->real_escape_string($_POST['service_affected']);
$area_affected=$mon3->real_escape_string($_POST['area_affected']);
$nr_customers=$mon3->real_escape_string($_POST['nr_clients_affected']);
$notify=$mon3->real_escape_string($_POST['notify']);
$description=$mon3->real_escape_string($_POST['description']);
$action=$mon3->real_escape_string($_POST['action']);
$outageid=$mon3->real_escape_string($_POST['outageid']);
$created_by=$localuser['username'];
echo $action;
if($action=="new")
{
$outages=$mon3->query("insert into outages (title,date_start,duration,date_end,status,severity,
services_affected,area_affected,nr_clients_affected,description,date_created,created_by) values 
(\"$title\",\"$date_start\",\"$duration\",\"$date_end\",\"$status\",\"$severity\",
\"$services_affected\",\"$area_affected\",\"$nr_customers\",\"$description\",\"$now\",\"$created_by\") 
");
$outageid=$mon3->insert_id;
}
elseif($action=="update")
{
	
$mon3->query("update outages set
title=\"$title\",
date_start=\"$date_start\",
duration=\"$duration\",
date_end=\"$date_end\",
severity=\"$severity\",
status=\"$status\",
services_affected=\"$services_affected\",
area_affected=\"$area_affected\",
nr_clients_affected=\"$nr_customers\",
description=\"$description\" 
where id=$outageid");
	echo $mon3->error;
$details=$mon3->query("select created_by,date_created from outages where id=$outageid")->fetch_assoc();
echo $mon3->error;
$created_by=$details['created_by'];
	
}

echo $mon3->error;


$body="<html><body>
<h2>Outage Report <a href=http://mon.lazertelecom.com/index.php?tickets=1&editoutage=$outageid>id $outageid</a></h2>
<h3>$title </h3>
<table>
<tr><td>date_start<td>$date_start
<tr><td>estimated duration<td>$duration min
<tr><td>date_end<td>$date_end
<tr><td>severity<td>$severity
<tr><td>status<td>$status
<tr><td>services_affected<td>$services_affected
<tr><td>area_affected<td>$area_affected
<tr><td>nr customers affected<td>$nr_customers
<tr><td>description<td>$description
<tr><td>created by<td>$created_by




</body></html>
";

// Acções de Emails





}
	
	
	
	
	
echo "<h3>Outages</h3>";

echo "<form action=index.php method=get> 
<input type=hidden name=tickets value=1>";

$sev=$_GET['severity'];
$sta=$_GET['status'];
$ser=$_GET['services'];
$ar=$_GET['area'];

echo "<select name=severity onchange=\"submit();\" style=width:280px><option value=''>Select a Severity</option>";
$severits=$mon3->query("select DISTINCT severity from outages order by severity");
while($severit=$severits->fetch_assoc())
{
	echo "<option value='".$severit['severity']."'";
	if($severit['severity']===$sev) echo " selected ";
	echo " > ".$severit['severity']."</option>";
}	

echo "</select>  ";

echo "<select name=status onchange=\"submit();\" style=width:180px><option value=''>Select a Status</option>";
$status_vars=$mon3->query("select DISTINCT status from outages order by status");
while($status_var=$status_vars->fetch_assoc())
{
	echo "<option value='".$status_var['status']."'";
	if($status_var['status']===$sta) echo " selected ";
	echo " > ".$status_var['status']."</option>";
}	

echo "</select>  ";

echo "<select name=services onchange=\"submit();\"><option value=''>Select a Service</option>";
$servs=$mon3->query("select DISTINCT services_affected from outages WHERE services_affected != '' order by services_affected");
while($serv=$servs->fetch_assoc())
{
	echo "<option value='".$serv['services_affected']."'";
	if($serv['services_affected']===$ser) echo " selected ";
	echo " > ".$serv['services_affected']."</option>";
}	
echo "</select>  <br><br>";

echo "<select name=area onchange=\"submit();\"><option value=''>Select an Area</option>";
$areas_affected=$mon3->query("select DISTINCT area_affected from outages WHERE area_affected != '' order by area_affected");
while($area_affected=$areas_affected->fetch_assoc())
{
	echo "<option value='".$area_affected['area_affected']."'";
	if($area_affected['area_affected']===$ar) echo " selected ";
	echo " > ".$area_affected['area_affected']."</option>";
}	
echo "</select>  ";


echo "</form>";	




$wq = "";
if($sev != "")
{
	$wq .= " AND severity ='".$sev."'";
}
if($sta != "")
{
	$wq .= " AND status ='".$sta."'";
}
if($ser != "")
{
	$wq .= " AND services_affected ='".$ser."'";
}
if($ar != "")
{
	$wq .= " AND area_affected ='".$ar."'";
}

$q = "select * from outages WHERE 1 ".$wq." order by date_start desc";
//echo $q;


echo "<table border=1>
<tr><th>id<th width=100>title<th>date start<th>date end<th>severity<th>status<th>services<th>area<th>customers
";
$outages=$mon3->query($q);
while($outage=$outages->fetch_assoc())
{
	echo "<tr><td><a href=?tickets=1&editoutage=".$outage['id'].">".$outage['id']."</a>
	<td>".$outage['title']."
	<td>".$outage['date_start']."
	<td>".$outage['date_end']."
	<td>".$outage['severity']."
	<td>".$outage['status']."
	<td>".$outage['services_affected']."
	<td>".$outage['area_affected']."
	<td>".$outage['nr_clients_affected'];
}
echo "</table>";


	
	
echo "<h3>Tickets</h3>";



	
	
}





echo "</table><br>

";
