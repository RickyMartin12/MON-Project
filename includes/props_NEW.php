<?php 
//add notes to properties: 
//$q=$mon3->query("update properties set notes= CONCAT(notes, '".date("Y-m-d H:i:s")." ".$localuser['username'].": connection $type added with equipment $equip_id <br>') where id=$propid");	

//proplog($propid,$text)



/*TO DO
Trim spaces from fsan on props and conns





*/
	$mon_prop = MON_ROOT."/properties/";
	$mon_leads = MON_ROOT."/leads/";
echo"
	<a href=?propleads=1><img src=img/leads.png></a>
	<a href=?props=1&props=1><img src=img/house.png></a>
	
		<a href=?propleads=1&covmaps=1><img src=img/maps.png></a>
	
	<a href=?props=1&new_prop=1><img src=img/houseadd.png></a>
	<a href=?custs=1&list_custs=1><img src=img/user.png></a>

<h3>Properties</h3><br>";





if($_POST['deletecon']==1)
{
	$conid=mysqli_real_escape_string($mon3, $_POST['conid']);
	$con=$mon3->query("select * from connections where id=\"$conid\"")->fetch_assoc();
	$propid=$con['property_id'];
	if($con['type']=="GPON")
	{
		$notes=date("Y-m-d H:i:s")." ".$localuser['username'].": deassigned from prop id".$con['property_id']."<br>";
		$q=$mon3->query("update ftth_ont set olt_id=\"\",ont_id=\"\",mng_ip=\"\",notes=concat(\"$notes\",notes) where fsan=\"".$con['equip_id']."\"");
		echo "ont de-assigned ".$con['equip_id']. $mon3->error;
		
	}
	if($con['type']=="COAX")
	{
		$notes=date("Y-m-d H:i:s")." ".$localuser['username'].": deassigned from prop id".$con['property_id']."<br>";
		$q=$mon3->query("update coax_modem set cmts=\"\",mng_ip=\"\",bootfile=\"\",notes=concat(\"$notes\",notes) where mac=\"".$con['equip_id']."\"");
		echo "modem de-assigned ".$con['equip_id']. $mon3->error;
		
	}
	
	
	
	
	$servs=$mon3->query("select id,type from services where connection_id=\"".$con['id']."\" ");
	while($serv=$servs->fetch_assoc())
	{
		$txt.=" id ".$serv['id']."type ".$serv['type'] ;
		$a=$mon3->query("delete from service_attributes where service_id=\"".$serv['id']."\"");
		echo $mon3->error;
		$a=$mon3->query("delete from services where id=\"".$serv['id']."\" ");
		echo $mon3->error;
		echo "<font color=red>DELETED service ".$serv['id']."</font><br>";
		
	}
	
	$a=$mon3->query("delete from connections where id=\"".$con['id']."\" ");
	
	proplog($propid,"deleted connection $conid ".$con['type'].", equip ".$con['equip_id']." and its services ".$txt);
	echo "<font color=red>DELETED Connection $conid</font><br>";

	
}



//
/////// Property info//////////////////////////////////////////////////////////////////////////////////////////////////////////
//


if($_GET['propid']!=0)
{


$propid=mysqli_real_escape_string($mon3, $_GET['propid']);

$prop=$mon3->query("select id,ref,address,freguesia,coords,owner_id,owner_ref,management,notes,date 
from properties where id=$propid;")->fetch_assoc();
echo "<table border=1><tr>
<td width=550px valign=top>

<table><tr><td>
<b>Property:</b><br>
id: $propid  - <b>".$prop['ref']."</b> <br>";

$lead=$mon3->query("select id from property_leads where prop_id=$propid");
if($lead->num_rows){
while($leada=$lead->fetch_assoc())
{
	echo "<a href=?propleads=1&lead_id=".$leada['id'] . "> lead ".$leada['id'] . "</a> <br>";
}
}
echo" <td>
<a href=?props=1&editprop=$propid> <img src=img/houseedit.png></a> 
</table>
 <br>";


//echo "error2:  ".mysqli_error($mon3).$prop['freguesia'];
$freg=$mon3->query("select freguesia,concelho from freguesias where id=".$prop['freguesia'])->fetch_assoc();
//echo "error3:  ".mysqli_error($mon3).$freg['concelho'];
$conc=$mon3->query("select concelho,distrito,pais from concelhos where id=".$freg['concelho'])->fetch_assoc();
//echo "error4: ".mysqli_error($mon3);


$conns=$mon3->query("select * from connections 
where property_id=$propid AND date_end=\"0000-00-00\" ;");

$conns_disabled=$mon3->query("select * from connections 
where property_id=$propid AND date_end!=\"0000-00-00\" ;");

$num_cons = $conns->num_rows;
$num_cons_disabled = $conns_disabled->num_rows;



$connbs=$mon3->query("select zone from connections 
where property_id=$propid AND date_end=\"0000-00-00\" ;")->fetch_assoc();
echo mysqli_error($mon3);

echo "<b>Address:</b><br> ".$prop['address']."<br> &nbsp;&nbsp;&nbsp;&nbsp;".$freg['freguesia'].", ".$conc['concelho'] .", ".$conc['distrito'].", ".$conc['pais']."  <br>
zone: ".$connbs['zone']." <br><br>

<b>Owner:</b><br>
";
$own=$mon3->query("select name,telef,email,language from customers where id=".$prop['owner_id'] )->fetch_assoc(); 
echo mysqli_error($mon3);
echo "<a href=?custs=1&cust_id=".$prop['owner_id'].">".$own['name']."</a> (".$own['language'].") <br> contacts: ". $own['telef']." ".$own['email']."<br>";

if ($prop['management']>0)
{
echo "<br><b>Management:</b><br>
";
$own=$mon3->query("select name,telef,email,language from customers where id=".$prop['management'] )->fetch_assoc();
 echo mysqli_error($mon3);
echo "<a href=?cust=".$prop['management'].">".$own['name']."</a> (".$own['language']." - contacts: ". $own['telef']." ".$own['email'];
}

echo "<br><table style=\"width: 100%\">
<tr><td><b>Connections: </b><td align=center>
<a href=?props=1&conadd=$propid><img width=50px alt=\"add new connection to this property\" src=img/connectionadd.png></a>
</td>
</tr>
";
?>
<tr>
<td style="width: 70%">
<?php
if($num_cons > 0 && $num_cons_disabled > 0) {
?>
<button id="prop-conn-id-<?php echo $propid; ?>" type="button" onclick="ShowAllConnectionsProps(<?php echo $propid; ?>)">Show All Connections</button>
<?php
}
?>

        
</table>
<table id="list_connetions-<?php echo $propid; ?>" style="width: 100%">
<?php


while($conn=$conns->fetch_assoc())
{

	$conn_id = $conn['id'];
	
	// SUSPENDED - span info
	$sus_but_serv_img_disabled="";
	// REACTIVE - span info

	/*
	0 - DISCONNECTED
	1 - OK
	2 - SUSPENDED
	*/
	// DISCONNECTED - span info

	$disconn_service_action_add_services="";

	$servs_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id'])->num_rows;

	$buutons_acts = "";

	//$dis_services = $conn['dis_services'];

	$date_now = date("Y-m-d");

	$dis_services = $conn['dis_services'];

	$s = 1;
	if($servs_num == 0)
	{
		$sus_but_serv_img_disabled=" onmouseover=\"spanMouseOverNoService(".$conn_id.")\" onmouseout=\"spanMouseOutNoService(".$conn_id.")\"";
		// SUSPENDED
		$sus_but_serv_img='disabledLink';
		
		// REACTIVAR
		$rea_button_serv_img='disabledLink';
		// DISCONNECTED
		$dis_button_serv_img='disabledLink';

	}
	else
	{
		if($dis_services == 0)
		{
			$servs_disabled_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end != '0000-00-00' order by id DESC")->num_rows;
			$servs_enabled_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end = '0000-00-00' order by id DESC")->num_rows;
			if($servs_disabled_num > 0 && $servs_enabled_num > 0)
			{
				$mon3->query("UPDATE connections set dis_services=\"1\" WHERE id=".$conn_id);
				$dis_services = 1;
				proplog($propid,"Activate connection <b>".$conn_id."</b> and services are activated to edit/create");
			}
		}
		
		
	}

	if($dis_services == 1)
	{
		// SUSPENDED - BUTTON
		$buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
		$buutons_acts .= "<img width=50px src=img/suspended.jpg style=\"margin-top: 7px\" onclick=\"clickSuspendedServicesModal(".$conn_id.", ".$propid.",'".$date_now."');\" class=\"img_mon ".$sus_but_serv_img."\" onmouseover=\"mouseOverSuspendedServices(".$conn['id'].");\" onmouseout=\"mouseOutSuspendedServices(".$conn['id'].");\"> &nbsp;";
		$buutons_acts .= "</span> ";
		// END SUSPENDED - BUTTON
		// WARNING SERVICE SUSPENDED
		$buutons_acts .= "<span id=title-sus-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";
		
		// DISCONNCTED - BUTTON
		$buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
		$buutons_acts .= "<img width=50px src=img/power-off.png onclick=\"clickDisconnectedServicesModal(".$conn_id.", ".$propid.",'".$date_now."');\" class=\"img_mon ".$dis_button_serv_img."\" onmouseover=\"mouseOverDisconnectedServices(".$conn['id'].");\" onmouseout=\"mouseOutDisconnectedServices(".$conn['id'].");\">";
		$buutons_acts .= "</span> ";
		// END DISCONNCTED - BUTTON
		// WARNING SERVICE DISCONNECTED
		$buutons_acts .= "<span id=title-dis-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";

		$buutons_acts .= "<div class=\"modal act_button_susp_".$conn['id']."\"></div>";
		$buutons_acts .= "<div class=\"modal act_button_disc_".$conn['id']."\"></div>";
	}
	else if($dis_services == 2)
	{
		// SUSPENDED - BUTTON
		$buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
		$buutons_acts .= "<img width=50px src=img/power-button.png onclick=\"clickReactiveServicesModal(".$conn_id.", ".$propid.",'".$date_now."');\" class=\"img_mon ".$rea_button_serv_img."\" onmouseover=\"mouseOverReactiveServices(".$conn['id'].");\" onmouseout=\"mouseOutReactiveServices(".$conn['id'].");\"> &nbsp;";
		$buutons_acts .= "</span> ";
		// END SUSPENDED - BUTTON
		// WARNING SERVICE SUSPENDED
		$buutons_acts .= "<span id=title-rea-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";

		// DISCONNCTED - BUTTON
		$buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
		$buutons_acts .= "<img width=50px src=img/power-off.png onclick=\"clickDisconnectedServicesModal(".$conn_id.", ".$propid.",'".$date_now."');\"  class=\"img_mon ".$dis_button_serv_img."\" onmouseover=\"mouseOverDisconnectedServices(".$conn['id'].");\" onmouseout=\"mouseOutDisconnectedServices(".$conn['id'].");\">";
		$buutons_acts .= "</span> ";
		// END DISCONNCTED - BUTTON
		// WARNING SERVICE DISCONNECTED
		$buutons_acts .= "<span id=title-dis-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";

		$buutons_acts .= "<div class=\"modal act_button_rea_".$conn['id']."\"></div>";
		$buutons_acts .= "<div class=\"modal act_button_disc_".$conn['id']."\"></div>";

		// VERIFICAR SE OS SERVICOS ESTAO ATIVOS

		
	}
	else if($dis_services == 0)
	{
		$buutons_acts .= "";
		
	}


	

	
	

	echo "<tr><td colspan=2><br><br>";





	if($conn['type']=="COAX")
	{
		///echo "rr ".$conn['equip_id'];
			$equip=$mon3->query("select * from coax_modem 
		where UPPER(mac)= UPPER(\"".$conn['equip_id']."\")")->fetch_assoc();

		$card=$mon3->query("select name from coax_upstreams where cmts_id=\"".$equip['cmts']."\" and upstream_id=\"".$equip['interface']."\" ")->fetch_assoc();
		//echo "error4: ".mysqli_error($mon3);


		echo "<tr><td>
		id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
		type:".$conn['type']." <br>
		equip: <a href=?equip=".$conn['equip_id']."&equip_type=COAX>".$conn['equip_id']. "</a> 
		<a href=http://".$equip['mng_ip'].">". $equip['mng_ip']. "</a><br>
		CELL: <a href=?coax=1&upstream=".$equip['interface']."&cmts=".$equip['cmts'].">".$equip['interface']."-".$card['name']."</a><br>
		date start:".$conn['date_start'];
		echo "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
		echo "<td></tr>";

		echo "<tr><td><b>status</b> ( by ".date("Y-m-d H:i:s ",$equip['status_timestamp'])."):<br>
		DSlevel: ".$equip['ds_power']."dBm - DSsnr: ".$equip['ds_snr']."dB<br> 
		USlevel: ".$equip['us_power']."dBm - USsnr: ".$equip['us_snr']."dB -  Rcv_level: ".$equip['rcv_power']."<br> 



		<br><br>
		<table width=300px>
		<tr><td width=25% align=center><img width=48px src=img/status_green.png onclick=\"popup2('status','COAX','".$equip['cmts']."','".$equip['mac']."', ".$conn['id'].", ".$conn['id'].");\"> 
		<td width=25% align=center><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','COAX','".$equip['cmts']."','".$equip['mac']."', ".$conn['id'].", ".$conn['id'].");\"> 

		<div id=info_connection_".$conn['id'].">
		</div>

		</table>

		";
	}

	elseif($conn['type']=="GPON")
	{
			$equip=$mon3->query("select * from ftth_ont 
		where fsan=\"".$conn['equip_id']."\"")->fetch_assoc();

		echo "error4: ".mysqli_error($mon3).$conn['equip_id'] . $equip['olt_id'];
			$ont2=explode("-",$equip['ont_id']);
			$olt=$mon3->query("select name from ftth_olt where id=\"".$equip['olt_id']."\"" )->fetch_assoc();
			$pon=$mon3->query("select name from ftth_pons where olt_id=\"".$equip['olt_id']."\" and
			card=\"".$ont2[1]."\" AND pon=\"".$ont2[2]."\"; ")->fetch_assoc();

		echo "<tr><td>
		id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
		type:".$conn['type']." <br>
		olt: ".$olt['name']." <br>
		PON: <a href=?gpon=1&pon=".$ont2[1]."-".$ont2[2]."&olt=".$equip['olt_id'].">".$pon['name']."</a><br>
		equip: <a href=?equip=".$conn['equip_id']."&equip_type=GPON>".$conn['equip_id']. "</a> 
		<a href=http://".$equip['mng_ip'].">".$equip['ont_id']. "</a><br>
		model: ".$equip['meprof']."<br>
		date start:".$conn['date_start']."<br><br>";
		echo "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
		echo "<td></tr>";


		echo "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
		<span id=status1 onmouseover=\"status1()\" onmouseout=\"status1o()\">".$equip['status']." </span>
		<div class=popup1 id=popup1> ".str_replace("\n","<br>",$equip['errors'])." </div> <br> 
		oltrx: ".$equip['tx']." oltrx: ".$equip['rx']."<br> 
		rf: ".$equip['rf']."<br><br>


		<table width=300px>
		<tr><td width=25% align=center><img width=48px src=img/status_green.png onclick=\"popup2('status','GPON','".$equip['olt_id']."','".$equip['ont_id']."', ".$conn['id'].");\"> 
		<td width=25% align=center><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','GPON','".$equip['olt_id']."','".$equip['ont_id']."', ".$conn['id'].");\"> 
		<td width=25% align=center><img width=48px src=img/sync_green.png onclick=\"popup2('sync','GPON','".$equip['olt_id']."','".$equip['ont_id']."', ".$conn['id'].");\"> 
		<td width=25% align=center><img width=48px src=img/reset_green.png onclick=\"popup2('reset','GPON','".$equip['olt_id']."','".$equip['ont_id']."', ".$conn['id'].");\"> 
		</table>

		<div id=info_connection_".$conn['id'].">
		</div>

		<br><br>";

	}







	elseif($conn['type']=="FWA")
	{
			$equip=$mon3->query("select * from fwa_cpe where mac=\"".$conn['equip_id']."\"")->fetch_assoc();

		echo "error4: ".mysqli_error($mon3).$conn['equip_id'] ." - ". $equip['antenna'];

			$ant=$mon3->query("select * from fwa_antennas where id=\"".$equip['antenna']."\"" )->fetch_assoc();


		echo "<tr><td>
		id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
		type:".$conn['type']." <br>
		Antenna: <a href=?fwa=1&ant=".$ant['id'].">".$ant['name']." <br>
		equip: <a href=?equip=".$conn['equip_id']."&equip_type=fwa>".$conn['equip_id']. "</a> 
		<a href=http://".$equip['mng_ip'].">".$equip['mac']. "</a><br>
		model: ".$equip['model']."<br>
		date start:".$conn['date_start']."<br><br>";
		echo "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
		echo "<td></tr>";
	}











	elseif($conn['type']=="ETH" || $conn['type']=="ETHF")
	{

		echo "<tr><td>
		id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
		type: ".$conn['type']." <br>";
		if($conn['linked_prop']>0)
		{
			$lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
			$lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
			echo "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
		}
		echo "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
		echo "<td></tr>";




	}






	elseif($conn['type']=="DARKF")
	{

		echo "<tr><td>
		id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
		type: ".$conn['type']." <br>";
		if($conn['linked_prop']>0)
		{
			$lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
			$lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
			echo "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
		}	
		echo "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
		echo "<td></tr>";	
		
	}
	$serv_link= "";

	if($conn['equip_id'] == "" || $dis_services == 0 || $dis_services == 2)
	{
		$serv_link = "class=\"disabledLink\"";
	}


		$servs_disabled_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end != '0000-00-00' order by id DESC")->num_rows;
		$servs_enabled_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end = '0000-00-00' order by id DESC")->num_rows;
		//echo $servs_disabled_num." ".$servs_enabled_num;
	//services

	
	
	echo " <tr><td><b>Services:</b>  <td align=center><a href=?servs=1&addserv=".$conn['id']." ".$serv_link."> <img width=60px src=img/packageadd.png></a>
	<tr><td><br>";
	
	if($servs_disabled_num > 0 && $servs_enabled_num > 0)
	{
		$click_dis_serv = "onclick=\"ShowAllServicesConn(".$conn['id'].",'".$conn['type']."','0','1');\"";
		echo "<input type=hidden id=click_en_serv-".$conn['id']." value=0>";
		$services = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end = '0000-00-00' order by id DESC");

	}
	else if($servs_disabled_num > 0 && $servs_enabled_num == 0)
	{
		$click_dis_serv = "onclick=\"ShowAllServicesConn(".$conn['id'].",'".$conn['type']."','1','0');\"";
		echo "<input type=hidden id=click_des_serv-".$conn['id']." value=0>";
		$services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.date_end DESC");
	}
	else if($servs_disabled_num == 0 && $servs_enabled_num > 0 || $servs_disabled_num == 0 && $servs_enabled_num == 0)
	{
		$click_dis_serv = "style=\"display: none\"";
		$services=$mon3->query("select * from services where connection_id=\"".$conn['id']."\" order by id DESC");
	}
	echo "<button id=dis_en_serv-".$conn['id']." type=button ".$click_dis_serv.">Show All Services</button>";



	echo "<table id=servs_lists-".$conn['id'].">";
	while($service=$services->fetch_assoc())
	{
		$tr_dis_services = "";
		$tr_dis_services_end = "";

		$serv_id = $service['id'];

		if($dis_services == 2)
		{
			$tr_dis_services = "<div id=\"tr-serv-susp-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverSuspendedServicesId(".$serv_id.")\" onmouseout=\"mouseOutSuspendedServicesId(".$serv_id.")\">";
			$tr_dis_services_end = "</div><span id=\"serv_span-susp-$serv_id\" class=\"warning-serv\" style=\"display: none;\"></span>";
		}
		else if($dis_services == 0)
		{
			$tr_dis_services = "<div id=\"tr-serv-dis-conn-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisconnectedServicesId(".$serv_id.")\" onmouseout=\"mouseOuDisconnectedServicesId(".$serv_id.")\">";
			$tr_dis_services_end = "</div><span id=\"serv_span-dis-$serv_id\" class=\"warning-serv\" style=\"display: none;\"></span>";
		}

		echo "<tr><td><br>
		<tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
		</a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
		if($service['date_end']!="0000-00-00")
		{
			echo "status: <font color=red><b> Disabled</b></font>"; 
		}
		
		

		$atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


		echo "<table>";
		while($att=$atts->fetch_assoc())
		{


		
			if($att['name']=="account")
			{
				echo " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
				$nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
				echo " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
			}

			elseif($att['name']=="wifi_site")
			{
				$text=explode("/",$att['value'])[5];
				if($text=="")
					$text=explode("/",$att['value'])[3];

				echo " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text."</a><br>";
			}

			elseif($att['name']=="unms_site")
			{

				echo " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
			}		
			elseif($att['name']=="acs_site")
			{

				echo " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
			}	
			
			
			elseif($att['name']=="speed")
			{

				$speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
				
				echo " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
			}
			
			
			
			else
			{
			
					echo " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value'].
					"<br>";
			
			}
			
			
			
		}
		if($service['type']=="INT")
		{
			$ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
			$fmac=substr($ip['mac'],0,8);
			$brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
			echo "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
			"<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
		}
		
		
		echo "</table>";
	}
	echo "</table>";
	echo "<tr><td colspan=2><br><br><tr><td colspan=2><br><br>
	";
	
}
echo "</table>";









if($prop['coords']=="")
{
	$coordlat="37.060521";
	$coordlng="-8.026970";
}
else
{
$coord=explode(",",$prop['coords']);
$coordlat=trim($coord[0]);
$coordlng=trim($coord[1]);
}



echo " <td  valign=top>
<div id=\"map\" ></div>
    <script>
// Initialize and add the map
function initMap() {
  // The location of Uluru
  var uluru = {lat: ".$coordlat.", lng:".$coordlng."};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 18, center: uluru, mapTypeId: 'satellite'});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru, map: map});
}
    </script>
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
		<br><a href=https://www.google.com/maps/search/?api=1&query=".$coordlat.",".$coordlng.">open in maps</a>
		<br><a href=\"http://maps.google.com/maps?daddr=".$coordlat.",".$coordlng."&amp;ll=\"> directions to prop</a>
	";

	
		
	echo "
	
	<table>	
		<tr><td><div id=divnotes style=\"width: 430px; height: 400px; overflow: scroll; white-space: nowrap;\"> <b>Notes:</b> <br>".$prop['notes']."</div>
		
	</table>"	;






	//galerias
	
	
	
	$property_leads_l = $mon3->query("SELECT * FROM property_leads WHERE prop_id=".$_GET['propid']. " order by date_installed desc" );




		// UPLOADS DE IMAGENS E 
		echo "<table style=\"width: 100%\">";

				echo "<div id=warning_services></div>";

				echo "<tr><td colspan=2 align=center><br><br><b>upload new file(.jpg or .pdf)</b><br>
				<form name=addrandfile method=post enctype=\"multipart/form-data\" action=index.php?props=1&propid=".$propid.">
				<label for=fileInput> 
				<img id=icon´ height=100px src=\"img/upload.png\" style=\"cursor: pointer;\">
				</label>
				<input type=file name=randfile[] accept=\".pdf,image/jpeg\" id=fileInput multiple style=\"display:none;\" onchange=\"this.form.submit()\">
				</form></tr>";


				if($_FILES['randfile'])
				{
					$var_rec = "";
					$countfiles = count($_FILES['randfile']['name']);
					for($i=0;$i<$countfiles;$i++){
						if(file_exists($_FILES['randfile']['tmp_name'][$i]))
						{
							$ext=explode(".",$_FILES['randfile']['name'][$i]);
							$var_rec .= uploadfile("randfile",$mon_prop.$propid."/", $propid."_".date("Y-m-d_H:i:s")."_".
								$localuser['username'].$i.".".strtolower($ext[sizeof($ext)-1]),0,$i);
						}
					}
				}

				?>
					<script>
						var val_upload_rand = "<?php echo $var_rec; ?>"
						$("#warning_services").html(val_upload_rand);
					</script>

				<?php

		echo "</table>";

		echo "<table class=bod-modal data-title=center><tr>";

				$gallery_images = "";
				$i=0;
				// GALERY DE IMAGENS DE LEADS
				while($property_lead_l=$property_leads_l->fetch_assoc())
				{
					$lead_id = $property_lead_l['id'];
					if(file_exists($mon_leads.$lead_id))
    				{
						$files_leads = scandir($mon_leads.$lead_id);
						foreach($files_leads as $file_lead)
        				{
							if(substr($file_lead,0,1)!=".")
        					{
								if($i%3==0)
								{
									$gallery_images .="<tr>";
								}

								if(strtolower(pathinfo($mon_leads.$lead_id."/".$file_lead, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_leads.$lead_id."/".$file_lead, PATHINFO_EXTENSION))=="jpeg" )
								{
									$gallery_images .= "<td align=center><a href=leads/".$lead_id."/".$file_lead." title = ".$file_lead." data-link_page=leads/".$lead_id."/".$file_lead." class=link_slider target=_blank>";
									$gallery_images .= "<img src=leads/".$lead_id."/".$file_lead." width=120px height=80px alt=".$file_lead." class=img_slider > </a> ";
								}
									
								elseif(preg_match("/_pdf/", $file_lead) && !preg_match("/contract_/", $file_lead))
								{
									
									$file_teste = preg_replace("/_pdf/", '', $file_lead);
									$file_teste = preg_replace("/.png/", '', $file_teste);
									$file_teste = $file_teste.".pdf";
									$gallery_images .= "<td align=center> <a href=leads/".$lead_id."/".$file_lead." data-link_page=leads/".$lead_id."/".$file_teste." title = ".$file_teste.">";
									$gallery_images .= "<img src=leads/".$lead_id."/".$file_lead." width=120px height=80px class=\"img_pdf\" alt=".$file_teste.">  </a> ";
								}
								elseif (preg_match("/.pdf/", $file_lead) and (!file_exists($mon_leads.$lead_id."/".preg_replace("/.pdf/", '_pdf', $file_lead).".png")) and !preg_match("/contract_/", $file_lead))
								{
								
										$file_im = $mon_leads.$lead_id."/".$file_lead."[0]";	
										$im = new Imagick();
										$im->setResolution(300, 300);     //set the resolution of the resulting jpg
										try
										{
											$im->readImage($file_im);    //[0] for the first page
											$file1png = preg_replace("/.pdf/", '_pdf', $file_lead).".png";
											$full_file = $mon_leads.$lead_id."/".$file1png;
											$im->setImageFilename($full_file);
											$im->writeImage();
										}
										catch(ImagickException $e) {
											$var .= "Error: " . $e -> getMessage() . "\n";
											echo $var;
											
											
										}
										$file_pic = "leads/".$lead_id."/".$file1png;
										$gallery_images .= "<td align=center> <a href=".$file_pic." data-link_page=leads/".$lead_id."/".$file_lead." target=_blank  title = ".$file_lead.">";
										$gallery_images .= "<img src=".$file_pic." width=120px height=80px> </a> ";
								}	
								$i++;
							}
						}
							
					}
					
				}

				if(file_exists($mon_prop.$propid))
				{
					$files1 = scandir($mon_prop.$propid);

					foreach($files1 as $file1)
					{
						if(substr($file1,0,1)!=".")
						{
							if($i%3==0)
							{
								$gallery_images .="<tr>";
							}

							if(strtolower(pathinfo($mon_prop.$propid."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_prop.$propid."/".$file1, PATHINFO_EXTENSION))=="jpeg" )
							{
								$gallery_images .= "<td align=center><a href=properties/".$propid."/".$file1." title = ".$file1." data-link_page=properties/".$propid."/".$file1." class=link_slider target=_blank>";
								$gallery_images .= "<img src=properties/".$propid."/".$file1." width=120px height=80px alt=".$file1." class=img_slider > </a> ";
							}
								
							elseif(preg_match("/_pdf/", $file1))
							{
								
								$file_teste = preg_replace("/_pdf/", '', $file1);
								$file_teste = preg_replace("/.png/", '', $file_teste);
								$file_teste = $file_teste.".pdf";
								$gallery_images .= "<td align=center> <a href=properties/".$propid."/".$file1." data-link_page=properties/".$propid."/".$file_teste." title = ".$file_teste.">";
								$gallery_images .= "<img src=properties/".$propid."/".$file1." width=120px height=80px class=\"img_pdf\" alt=".$file_teste.">  </a> ";
							}

							$i++;

						}
					}
				}				

				echo $gallery_images;



		echo "</tr></table>";












	
		
		
echo"	<tr><td colspan=2><div id=divlog style=\"width: 990px; height: 200px; overflow: scroll; white-space: nowrap;\"> <b>Property log:</b> <br>";
		$p = $mon_prop.$propid."/";
		$p_file = $p."log.txt";


		if(file_exists($p_file))
		{
			$log=file_get_contents($p_file);
			str_replace(PHP_EOL,"<br>",$log);
			echo $log;
		}
		else{echo "no records yet!";}
		
		echo "</div>
			
		
	<script>
	var divk=document.getElementById('divnotes');
	divk.scrollTop = divk.scrollHeight;
	var divj=document.getElementById('divlog');
	divj.scrollTop = divj.scrollHeight;
	
	</script>
		
		
		";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
echo "		
<b>Add Note:</b> <input type=text name=notes id=notes>
<input type=button value=\"Submit Sugestion\" onclick=\"SubmitSugestion(".$propid.");\">
<br><span id=submit_notes_succ></span>
</td></tr>	
</table>
";






































}




// EDIT connection//////////////////////////////////////////////////////////////////////////////////////////////////////////////

elseif($_GET['conedit']>0)
{

//retrieve data from current database
$conid=mysqli_real_escape_string($mon3, $_GET['conedit']);
$con=$mon3->query("select * from connections where id=\"".$conid."\";")->fetch_assoc();	
$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\";")->fetch_assoc();




	
if($_POST['coneditsubm'])
{

if($conid>0)
{ 	

	//dados do formulario
	$equip_id=trim(mysqli_real_escape_string($mon3, $_POST['equip_id']));
	$date_start=mysqli_real_escape_string($mon3, $_POST['date_start']);
	$date_end=mysqli_real_escape_string($mon3, $_POST['date_end']);	
	$meprof=mysqli_real_escape_string($mon3, $_POST['meprof']);	


	

	if($con['type']=="GPON")
	{
		$pon=mysqli_real_escape_string($mon3, $_POST['pon']);
			echo 	$conid.$equip_id.$date_start.$date_end.$pon.$meprof.$notes;
		//original ont:
		$ont=$mon3->query("select * from ftth_ont where fsan=\"".$con['equip_id']."\";")->fetch_assoc();	
		$ont_x=explode("-",$ont['ont_id']);
		$olt=$mon3->query("select * from ftth_olt where id=\"".$ont['olt_id']."\" ;")->fetch_assoc();

		
		//check whether the new fsan exists and has id assigned to it
		$fsanexists=$mon3->query("select fsan from ftth_ont where fsan=\"$equip_id\" AND ont_id!=\"\" ")->num_rows;
		
		echo "specified fsan exists? active= $fsanexists <br>";
		$equip_x=explode("ZNTS",$equip_id);
		$pona=$ont_x[1]."-".$ont_x[2];

	echo "$equip_id ".$ont['fsan']." $pon $pona " . $fsanexists;
		if($equip_id!=$ont['fsan'] && $pon==$pona && $fsanexists==0) //trocar de ont no mesmo id
		{
			echo "<br>replace ONT <br>exists active= $fsanexists <br>";
			
			gpon_change_ont($olt['id'],$ont['ont_id'],$equip_id,$meprof);

			
			
			monlog("FSAN changed on ".$prop['ref']." from ".$con['equip_id']." to $equip_id for ont ".$ont['ont_id']." and olt". $olt['id']);
			
			$gg=$mon3->query("update connections set equip_id=\"$equip_id\" where id=$conid ");
				echo "<br>update connection..".mysqli_error($mon3);
			$gg=$mon3->query("update ftth_ont set ont_id=\"\" where fsan=\"".$ont['fsan']."\" ");
				echo "<br>deassign old ont".mysqli_error($mon3);
			$exists=$mon3->query("select fsan from ftth_ont where fsan=\"$equip_id\" ")->num_rows;
				echo "<br>verify if new ont exists unassigned".mysqli_error($mon3);

			if($exists==0){ //se o ont nao esta na lista, criar
			$gg=$mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,meprof) values 
			(\"$equip_id\", \"".$ont['olt_id']."\", \"".$ont['ont_id']."\", \"$meprof\") ");
				echo "<br>new ont, insert into ftth_ont".mysqli_error($mon3);
			proplog($propid,"insert ont $equip_id , olt ".$ont['olt_id'].", ont id".$ont['ont_id']." model $meprof");
				
			}else //se ja esta, associa ao id 
			{
			$gg=$mon3->query("update ftth_ont set ont_id=\"".$ont['ont_id']."\", olt_id=\"".$ont['olt_id']."\" where fsan=\"$equip_id\" ");
			echo "<br>ont exists unassigned,update id on ftth_ont".mysqli_error($mon3);
			proplog($propid,"update ont $equip_id , olt ".$ont['olt_id'].", ont id".$ont['ont_id']." model $meprof");
				echo mysqli_error($mon3);			
			}
		}
		elseif($equip_id==$ont['fsan'] && $pon!=$pona) // mudar de pon
		{
			echo "<br>ONT move to different pon <br>exists active= $fsanexists <br>";
			//check if the same ID is available in the new pon to move
		 	$new_id="1-".$pon."-".$ont_x[3];
			//echo "current id: ".$ont['ont_id']."<br> new id:".$new_id;
			$exists=$mon3->query("select fsan from ftth_ont where ont_id=\"$new_id\" and olt_id=\"".$ont['olt_id']."\" ")->num_rows;
			if($exists==0){
				gpon_move_ont($olt['id'],$ont['ont_id'],$new_id);
				$gg=$mon3->query("update ftth_ont set ont_id=\"".$new_id."\" where fsan=\"$equip_id\" ");
				proplog($propid,"update ftth_ont set ont_id=\"".$new_id."\" where fsan=\"$equip_id\"");
				echo mysqli_error($mon3);	
				$notes.=date("Y-m-d H:i:s").$localuser['username'].": "."ONT moved from ".$ont['ont_id']." to $new_id <br>";
				monlog("ONT moved from ".$ont['ont_id']." to $new_id ");
				
			}
		
			
			
			//if not, delete and create it on new pon
			else
			{
				$new_id="1-".$pon."-".nextont($olt['id'],$pon);
				//echo "new id= ".$new_id;
				$gg=$mon3->query("update ftth_ont set ont_id=\"".$new_id."\" where fsan=\"$equip_id\" ");
				proplog($propid,"update ftth_ont set ont_id=\"".$new_id."\" where fsan=\"$equip_id\" ");
				gpon_delete_ont($olt['id'],$ont['ont_id']);
				gpon_register_ont($olt['id'],$conedit);
				$notes.=date("Y-m-d H:i:s").$localuser['username'].": "."ONT id changed on ".$prop['ref']." $equip_id for ont ".$ont['ont_id']." to $new_id and olt". $olt['id']."<br>";
				monlog("ONT id changed on ".$prop['ref']." $equip_id for ont ".$ont['ont_id']." to $new_id and olt". $olt['id']);
			
			}
				
		}
		elseif($fsanexists==1)
		{
			echo "Sorry that ONT $equip_id exist on database and is active. Please de-assign the ONT and then assign to this connection.";
		}
		
		
	
	}
	elseif($con['type']=="COAX")
	{
		$cmts=mysqli_real_escape_string($mon3, $_POST['cmts_id']);
		echo "modifying coax connection: $cmts  ";
		
		//check if the mac exists if not create
		$macexists=$mon3->query("select mac from coax_modem where mac=\"$equip_id\"")->num_rows;
		if($macexists==0)
		{		
			
			$mng_ip=$mon3->query("select mng_ip from coax_modem where cmts=$cmts order by mng_ip desc")->fetch_assoc();
			$ip=explode(".",$mng_ip['mng_ip']);
			$ip[3]++;
			if($ip[3]>254)
			{
			$ip[2]++; 
			$ip[3]=0;
			}
			$ipf="10.".(99+$cmts).".".$ip[2].".".$ip[3];
			
									
				$q=$mon3->query("insert into coax_modem (mac,cmts,mng_ip,model) values (
				\"$equip_id\",
				\"$cmts\",
				\"$ipf\",
				\"$model\")
				 ");
				 
				 
				 echo mysqli_error($mon3);
				$q=$mon3->query("update connections set equip_id=\"$equip_id\" where id=\"".$conid."\" ");echo mysqli_error($mon3);
				$q=$mon3->query("update settings set valor=1 where nome=\"modems_changed\" "); echo mysqli_error($mon3);
				
			
			
			//verificar o cmts original, se for diferente, é preciso calcular novo ip
			
			echo $ipf." - ".$cmts;
		
		
			$q=$mon3->query("update coax_modem set cmts=\"$cmts\",mng_ip=\"$ipf\" where mac=\"$equip_id\" "); echo mysqli_error($mon3);
			$q=$mon3->query("update settings set valor=1 where nome=\"modems_changed\" "); echo mysqli_error($mon3);
		}

		else //if modem exists, deassign other connections, and update the remaining items
		{

			if($con['equip_id']!=$equip_id) //if modem is different from previous one on this connection
			{
			echo "checking modem on other connections...<br>";
			
			$concs=$mon3->query("select connections.id,property_id,ref from connections left join properties on connections.property_id=properties.id where equip_id=\"$equip_id\" ");
			echo mysqli_error($mon3);
			
			while($conc=$concs->fetch_assoc())
			{
				echo "<a href=?props=1&propid=".$conc['property_id'].">".$conc['ref']."</a> <br>";
				
				$mon3->query("update connections set equip_id=\"\" where id=\"".$conc['id']."\" ");
				
				proplog($conc['property_id'],"deleted modem $equip_id from connection ".$conc['id']." as it was assigned to property ".$prop['ref']." ");
			
			}
	
			}
			
			
			//update this connection to the modem inserted		
			$q=$mon3->query("update connections set equip_id=\"$equip_id\" where id=\"".$conid."\" ");echo mysqli_error($mon3);
			
			//update details of the modem
			
			$cmtsi=$mon3->query("select cmts from coax_modem where mac=\"$equip_id\"")->fetch_assoc();
			if($cmtsi['cmts']!=$cmts)  //need to generate new ip
			{
				echo "cmts changed";
				$mng_ip=$mon3->query("select mng_ip from coax_modem where cmts=$cmts order by mng_ip desc")->fetch_assoc();
				$ip=explode(".",$mng_ip['mng_ip']);
				$ip[3]++;
				if($ip[3]>254)
				{
				$ip[2]++; 
				$ip[3]=0;
				}
				$ipf="10.".(99+$cmts).".".$ip[2].".".$ip[3];
				echo $ipf;
					
				$q=$mon3->query("update coax_modem set 
			cmts=$cmts,
			model=\"$model\",
			mng_ip=\"$ipf\"
			where mac=\"$equip_id\"	
			");echo mysqli_error($mon3);
				
			}				
			
			
			
			
			$q=$mon3->query("update coax_modem set 
			model=\"$model\"
			where mac=\"$equip_id\"	
			");echo mysqli_error($mon3);
			
			
			

			
		$q=$mon3->query("update settings set valor=1 where nome=\"modems_changed\" "); echo mysqli_error($mon3);

		
		}



			
		echo "coax";
	
	
	
	}
	if($date_end!="0000-00-00")
	{
		// end services as well
		$mon3->query("update services set date_end=\"$date_end\" where connection_id=$conid");
		
	}
	
	$gg=$mon3->query("update connections set date_start=\"$date_start\",date_end=\"$date_end\"
	 where id=$conid");
	echo mysqli_error($mon3);
		echo "<br><font color=green>saved</font><br>";
}
}
////////save done///////







$con=$mon3->query("select * from connections where id=\"".$conid."\";")->fetch_assoc();	



echo"<table><tr><td> <b>EDIT Connection ID ".$con['id']." </b>(".$con['type'].") <td>

<form id=deleteconf method=post action=?props=1&propid=".$prop['id'].">
<input type=hidden name=conid value=".$con['id'].">
<input type=hidden name=deletecon value=1>
<a href=# onClick=\"if(confirm(`If it is a changeover, use the end date to terminate an old connection.\n As long as the cable is in the property, connection should not be deleted.\n This will also delete all services assigned to this connection.\n Do you want to proceed?`))	{
		document.getElementById('deleteconf').submit();	}\">
		<img src=img/del.png width=40px></a>
</form>
</table>


<br><br>
Prop Ref.: <a href=?props=1&propid=".$prop['id'].">".$prop['ref']."</a><br>
Address: ".$prop['address']."
<br><br>";


echo "
<form name=editcon action=?conedit=$conid&props=1 method=post>
<table><tr>
<tr><td>date start:<td> <input type=text name=date_start value=\"".$con['date_start']."\" size=50> YYYY-MM-DD<br>
<tr><td>date end:<td> <input type=text name=date_end value=\"".$con['date_end']."\" size=50> 0000-00-00 for active
<tr><td><br>";



if($con['type']=="GPON")
{
$ont=$mon3->query("select * from ftth_ont where fsan=\"".$con['equip_id']."\";")->fetch_assoc();	
$ont_x=explode("-",$ont['ont_id']);
$olt=$mon3->query("select * from ftth_olt where id=\"".$ont['olt_id']."\" ;")->fetch_assoc();
echo "
<tr><td>OLT: <td>  ".$olt['name']."
<tr><td>ONT id:<td>  ".$ont['ont_id']."
<tr><td>Equipment:<td> <input type=text name=equip_id value=\"".$con['equip_id']."\" size=50> <br>";



echo"
<tr><td>PON: <td> <select name=pon>";
$pons=$mon3->query("select * from ftth_pons where olt_id=\"".$ont['olt_id']."\" order by name;");
while($pon=$pons->fetch_assoc())
{
	echo "<option value=".$pon['card']."-".$pon['pon'];
	if( $pon['card']==$ont_x[1] && $pon['pon']==$ont_x[2] )
		echo " selected ";
	echo "> ".$pon['card']."-".$pon['pon']." - ".$pon['name']." </option> ";
}	
echo "</select>";




echo "
<tr><td>Model:<td><select name=meprof>
<option value=zhone-2424";if($ont['meprof']=="zhone-2424") echo " selected"; echo">zhone-2424</option>
<option value=zhone-2428";if($ont['meprof']=="zhone-2428") echo " selected"; echo">zhone-2428</option>
<option value=zhone-2427";if($ont['meprof']=="zhone-2427") echo " selected"; echo">zhone-2427</option>
<option value=zhone-2727a";if($ont['meprof']=="zhone-2727a") echo " selected"; echo">zhone-2727a</option>
<option value=zhone-SFP";if($ont['meprof']=="zhone-SFP") echo " selected"; echo">zhone-SFP</option>
</select>



";
}
elseif($con['type']=="COAX")
{


	$modem=$mon3->query("select * from coax_modem where mac=\"".$con['equip_id']."\";")->fetch_assoc();	
	echo "
	<tr><td>Modem type<td> <select name=model><option value=>no modem / tv only</option>
	<option value=cve ";if($modem['model']=="cve") echo " selected"; echo">hitron cve</option>
	<option value=cva ";if($modem['model']=="cva") echo " selected"; echo">hitron cva</option></select>
	
	<tr><td>Modem MAC:<td><input type=text name=equip_id value=".$modem['mac'].">format: aabbccddeeff
	<tr><td>CMTS:<td><select name=cmts_id>
	<option value=1 ";if($modem['cmts']=="1") echo " selected"; echo">CMTS1_QDL</option>
	<option value=2 ";if($modem['cmts']=="2") echo " selected"; echo">CMTS2_QDL</option>
	<option value=3 ";if($modem['cmts']=="3") echo " selected"; echo">CMTS_BAL</option></select>
	<tr><td><br>
	
	";





}
else
{
	echo "not defined";

}


echo "


<input type=hidden name=conedit value=$conid>
<input type=hidden name=props value=1>
<tr><td><input type=submit name=coneditsubm value=save>
</table>
</form>
<td>
";
}









// ADD connection//////////////////////////////////////////////////////////////////////////////////////////////////////////////

elseif($_GET['conadd']>0)
{

//retrieve data from current database
$propid=mysqli_real_escape_string($mon3, $_GET['conadd']);
$prop=$mon3->query("select * from properties where id=\"$propid\" ;")->fetch_assoc();
echo $mon3->error;
	
if($_POST['conaddsubm'])
{

	//dados do formulario
	$equip_id=str_replace(":","",trim(mysqli_real_escape_string($mon3, $_POST['equip_id'])));
	$linked_prop=trim(mysqli_real_escape_string($mon3, $_POST['linked_prop']));
	$date_start=mysqli_real_escape_string($mon3, $_POST['date_start']);
	$pon=mysqli_real_escape_string($mon3, $_POST['pon']);
	$olt_id=mysqli_real_escape_string($mon3, $_POST['olt_id']);
	$model=mysqli_real_escape_string($mon3, $_POST['model']);	
	$type=mysqli_real_escape_string($mon3, $_POST['type']);
	$cmts=mysqli_real_escape_string($mon3, $_POST['cmts_id']);

	echo "$equip_id $date_start $pon $olt_id $model $type $cmts"; 
	if($type=="GPON")
	{
		$fsanexists=$mon3->query("select fsan,ont_id,olt_id from ftth_ont where fsan=\"$equip_id\" ")->num_rows;
		echo mysqli_error($mon3);
	
		if($fsanexists==0) //se o ont nao esta na dB
		{
			echo "$equip_id is not in dB, adding ont...<br>";
			$ont_id="1-".$pon."-".nextont($olt_id,$pon);
			echo "ont_id - ".$ont_id;
			$q=$mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,model) values (
			\"$equip_id\",
			$olt_id,
			\"$ont_id\",
			\"$model\"
			)");
			echo mysqli_error($mon3);	
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,dis_services) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"1\"
			)");
			echo mysqli_error($mon3);
			echo "update notes on prop";	
			proplog($propid,"connection $type added with equipment $equip_id");	
				
			echo mysqli_error($mon3);			
			
		}
		else   // 
		{
			echo "ont in db, checking in use..";
			$ont=$mon3->query("select fsan,ont_id,olt_id from ftth_ont where fsan=\"$equip_id\"  ")->fetch_assoc();
			if($ont['ont_id']=="")
			{
				
			$ont_id="1-".$pon."-".nextont($olt_id,$pon);
			echo "ONT free - new ont_id - ".$ont_id;
			$q=$mon3->query("update ftth_ont set 
			olt_id=$olt_id,
			ont_id=\"$ont_id\",
			model=\"$model\"			
			where fsan=\"$equip_id\"
			");
			echo mysqli_error($mon3);	
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,dis_services) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"1\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	






				
				
				
			}
			else
			{
			echo "Sory that ONT $equip_id exist on database and is active. Please de-assign the ONT and then assign to this connection.";
			}
		
		}
	
	}
	elseif($type=="COAX")
	{

	
			$mng_ip=$mon3->query("select mng_ip from coax_modem where cmts=$cmts order by mng_ip desc")->fetch_assoc();
			$ip=explode(".",$mng_ip['mng_ip']);
			$ip[3]++;
			if($ip[3]>254)
			{
			$ip[2]++; 
			$ip[3]=0;
			}
			$ipf="10.".(99+$cmts).".".$ip[2].".".$ip[3];
//			echo $ipf;
		
			$q=$mon3->query("insert into coax_modem (mac,cmts,mng_ip,model) values( 
			\"$equip_id\",
			$cmts,
			\"$ipf\",
			\"$model\"
			)");
			echo mysqli_error($mon3);	
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,dis_services) values( 
			$propid,
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"1\"
			)");
			echo mysqli_error($mon3);	
	}
	
	elseif($type=="ETH"||$type=="ETHF")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,linked_prop,dis_services) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"$linked_prop\",
			\"1\"
			)");

			$q=$mon3->query("insert into ftth_eth (mac,model) values( 
			\"$equip_id\",
			\"$model\"
			)");
			
			
			
			proplog($propid,"connection $type added with equipment $equip_id ");	

	
	
	}
	elseif($type=="DARKF")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,dis_services) values (
			\"$propid\",
			\"$type\",
			\"\",
			\"$date_start\",
			\"1\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	
	}
	elseif($type=="WIFI")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,dis_services) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"1\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	
	}
	elseif($type=="OTT")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,dis_services) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"1\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	
	}
	
	
	
	echo "<br><font color=green>saved</font><br>";

}
////////save done///////





echo" <br> <b>ADD Connection </b> <br><br>
Prop Ref.: <a href=?props=1&propid=".$prop['id'].">".$prop['ref']."</a><br>
Address: ".$prop['address']."
<br><br>";


echo" <script>
function updatepon(olt)
{ 
      $.ajax({ method: \"GET\", url: \"webservice.php\", data: { 'ponsbyolt': olt}})
        .done(function( data ) 
		{ 
          var result = $.parseJSON(data); 
		  	var pona=document.getElementById(\"pons\");
			pona.innerHTML = \"\";
		var ponb;
        $.each( result, function( key, value ) 
		{ 
			ponb = new Option(value[0]+' - '+value[1], value[0]);
            pona.options.add(ponb);
		}); 
       }); 
}; 




function updateconform()
{
	var textdiv='';
	var soptionb=document.getElementById('conformsel');
	var soption=soptionb.options[soptionb.selectedIndex].value;
	
	textdiv += '<table>';
	
	
	if(soption=='GPON') 
	{
		textdiv += '<tr><td>CPE model<td><select name=model><option selected value=zhone-2427>zhone-2427</option><option value=zhone-2727a>zhone-2727a</option><option value=zhone-2427>SFP zhone-2427</option><option value=SFP>DIA connection</option><option value=mininode>fibre mininode</option></select>'+	


		'<tr><td>ONT FSAN<td><input type=text name=equip_id size=10 value=ZNTS><br> '+	
		'<tr><td>OLT<td><select name=olt_id onchange=\"updatepon(this.options[this.selectedIndex].value); updatevlan(this.options[this.selectedIndex].value)\"> ";
		
		$olts=$mon3->query("select * from ftth_olt"); 
		while($olt=$olts->fetch_assoc()){ echo "<option value=".$olt['id'].">".$olt['name']."</option>";} 
		
		
		echo"</select>'+	
		'<tr><td>PON<td><select id=pons name=pon >";
		$pons=$mon3->query("select card,pon,name from ftth_pons where olt_id=1 order by name ");
		while($pon=$pons->fetch_assoc())
		{
			echo "<option value=".$pon['card']."-".$pon['pon'].">".$pon['card']."-".$pon['pon']." - ".$pon['name'];
		}
		echo "</select> ';	

	}
	else if((soption=='COAX'))
	{
		textdiv += '<tr><td>CPE model<td><select name=model><option selected value=cve>hitron cve</option><option value=cva>hitron cva</option><option value=k310i>kathrein 310i </option><option value=k10>kathrein router</option></select>'+	
		'<tr><td>Modem MAC<td><input type=text name=equip_id size=17 value= >aabbccddeeff'+	
		'<tr><td>CMTS<td><select name=cmts_id>";
		
		$cmtss=$mon3->query("select * from coax_cmts"); 
		while($cmts=$cmtss->fetch_assoc()){ echo "<option value=".$cmts['id'].">".$cmts['name']."</option>";} 
		
		
		echo"</select>'+ 
		'';	
		
	}
	
	else if((soption=='ETH'))
	{
		textdiv += '<tr><td>CPE model<td><select name=model><option selected value=tplink>tplinkrt</option><option value=zyxelrt>zyxelrt</option><option value=comega>comega</option><option value=switch>switch</option><option value=sfp>sfp</option></select>'+	
		'<tr><td>ETH MAC<td><input type=text name=equip_id size=17 value= > ex: aabbccddeeff	<tr><td>linked prop<td><select name=linked_prop style=\"width: 150px;\"><option value=>not linked</option>";
		
		$propsv=$mon3->query("select properties.ref,connections.id,properties.address,connections.type from properties left join connections on properties.id=connections.property_id where connections.date_end=\"0000-00-00\" order by properties.ref");
		while($propv=$propsv->fetch_assoc())
		{
			
			echo "<option value=".$propv['id'].">".$propv['ref']." ".$propv['type']." ".escape4js($propv['address'])."</option>";
		}


		
		
		
		
		echo "</select>	';	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	textdiv += '</table>';
	document.getElementById('additionalset').innerHTML = textdiv;
};
</script>
";

echo "

<form name=editcon action=?conadd=$propid&props=1 method=post>
<table>
<tr><td>date start:<td> <input type=text name=date_start value=\"".date("Y-m-d")."\" size=10> YYYY-MM-DD<br>
<tr><td>connection type<td><select id=conformsel name=type onchange=\"updateconform()\">
<option  selected value= >please select</option>
<option value=GPON>GPON</option>
<option value=COAX>COAX</option>
<option value=ETH>ETH</option>
<option value=DARKF>DARKF</option>
<option value=WIFI>WIFI</option>
<option value=OTT>OTT</option>
</select>


<tr><td><br>
<tr><td colspan=2><div id=additionalset >
</div>
";



echo "
<tr><td><br>

<tr><td><input type=submit name=conaddsubm value=save><br>

</table></form>
";
}



















// EDIT porperty

elseif($_GET['editprop']>0)
{

$propid=mysqli_real_escape_string($mon3, $_GET['editprop']);
	
if($_POST['propeditsubm'])
{
	$address=mysqli_real_escape_string($mon3, $_POST['address']);
	$freg=mysqli_real_escape_string($mon3, $_POST['freg']);
	$coords=trim(mysqli_real_escape_string($mon3, $_POST['coords']), '()');
	$owner=mysqli_real_escape_string($mon3, $_POST['owner']);	
	$manage=mysqli_real_escape_string($mon3, $_POST['manage']);
	$notes=mysqli_real_escape_string($mon3, $_POST['notes']);
	if($propid>0)
	{ 

//		echo $address.$freg.$coords." o ".$owner." m ".$manage."kkkk<br>";
		$gg=$mon3->query("update properties set address=\"".$address."\", 
		freguesia=\"".$freg."\", 
		coords=\"".$coords."\", 
		owner_id=\"".$owner."\", 
		management=\"".$manage."\",
		notes=\"".$notes."\"
		where id=\"".$propid."\" ;");
 echo mysqli_error($mon3);
		//save
		echo "<br><font color=green>saved</font><br>";
	}
}	
$prop=$mon3->query("select * from properties where id=\"".$propid."\";")->fetch_assoc();
$freg=$mon3->query("select * from freguesias where id=\"".$prop['freguesia']."\";")->fetch_assoc();
$conc=$mon3->query("select * from concelhos where id=\"".$freg['concelho']."\";")->fetch_assoc();
$owner=$mon3->query("select * from customers where id=\"".$prop['customer_id']."\";")->fetch_assoc();
$manage=$mon3->query("select * from customers where id=\"".$prop['management']."\";")->fetch_assoc();

echo" <br> EDIT property ID $propid - Ref.: <a href=?props=1&propid=$propid>".$prop['ref']."</a><br><br><br>";

echo "
<table><tr>

<form name=editprop action=?editprop=$propid&props=1 method=post>
<tr><td>Adress:<td> <input type=text name=address value=\"".$prop['address']."\" id=address size=50> <br>
<tr><td>Freguesia:<td><select name=freg id=freg>";
$fregs=$mon3->query("select * from freguesias where concelho=\"".$freg['concelho']."\";");

while($frega=$fregs->fetch_assoc())
{
	echo "<option value=".$frega['id'];
	if ($frega['id']==$prop['freguesia'])
		echo " selected";
	echo ">".$frega['freguesia']."</option>";
	
}


echo"
</select>

<tr><td>Concelho:<td><select select name=concelho id=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value,\'\')\">";

$concs=$mon3->query("select * from concelhos order by pais,distrito,concelho;");

while($conca=$concs->fetch_assoc())
{
	echo "<option value=".$conca['id'];
	if ($conca['id']==$freg['concelho'])
		echo " selected";
	echo ">".$conca['distrito']." - ".$conca['concelho']."</option>";
	
}


$owner=$mon3->query("select id,name,email,fiscal_nr from customers where id=".$prop['owner_id'])->fetch_assoc();
echo mysqli_error($mon3);
if($prop['manage']>0)
{
	$manage=$mon3->query("select id,name,email,fiscal_nr from customers where id=".$prop['manage'])->fetch_assoc();
	echo mysqli_error($mon3);
}

echo"


</select>
<tr><td>Country:
<td><select name=country class=\"country\"onchange=updateconcelhosep()>
<option value=PORTUGAL selected>Portugal</option>
<option value=SPAIN>Spain</option>
<option value=\"UNITED KINGDOM\">United Kingdom</option>
</select>



<tr><td>coords: <td><input type=text name=coords value=\"".$prop['coords']."\"  id=coord size=40>
<a href=# onclick=gpslink()>GPS</a> 


<tr><td><br>

<tr><td>Subscriber:<td>
<select name=owner id=idowner style='width: 500px;'>

";
if ($owners['id']=="")
	{echo "<option selected value=></option>";}
$owners=$mon3->query("select id,name,email,fiscal_nr from customers order by name ");
while($owns=$owners->fetch_assoc())
{
	echo "<option value=\"".$owns['id']."\"";
	if ($owns['id']==$owner['id'])
	{echo "selected";}
	
	
	echo ">".$owns['id']."-".$owns['name']." #".$owns['fiscal_nr']."</option>";
	
}
 

echo"
</select>

<br>

<tr><td>Management comp:<td>
<select name=manage id=idowner style='width: 500px;'><option 

";
	if ($manage['id']=="")
	{echo " selected ";}
	echo "value=>no external management</option>";

$owners=$mon3->query("select id,name,email,fiscal_nr from customers where is_management=1 order by name ");
while($owns=$owners->fetch_assoc())
{
	echo "<option value=\"".$owns['id']."\"";
	if ($owns['id']==$manage['id'])
	{echo "selected";}
	
	
	echo "> ".$owns['name']." #".$owns['fiscal_nr']."</option>";
	
}
 

echo"
</select>

<tr><td> <br>

<tr><td>Notes <td> <textarea cols=65 rows=10 name=notes>".$prop['notes']."</textarea>
<input type=hidden name=editprop value=$propid>
<input type=hidden name=props value=1>
<tr><td><input type=submit name=propeditsubm value=save><br>
</form>
<td>
";
}






















//Default - list properties + search





else{

if(isset($_GET['offset']))
		$offset=mysqli_real_escape_string($mon3, $_GET['offset']);
	else
		$offset=0;

	if(isset($_GET['searchb']))
	{
		$searchb=mysqli_real_escape_string($mon3, $_GET['searchb']);
		echo " value=\"$searchb\"";
		$qwhere= " where address LIKE '%".$searchb."%' or ref LIKE '%".$searchb."%'";
	
	}	
	echo" 
	

	
	
	
	<div id=mapl>
	
	</div>
	
	 <script>
function initMap() {

  var uluru = {lat: 37.140653, lng:-8.019443};
  var map = new google.maps.Map(
      document.getElementById('mapl'), {zoom: 11, center: uluru, mapTypeId: 'hybrid',gestureHandling: 'greedy'});

        var imgf = 'img/red_12px.png';
		var imgc = 'img/blue_12px.png';
		var imgi = 'img/orange_12px.png';
		var imgl = 'img/yellow_12px.png';
		var imgb = 'img/black_12px.png';
		var imgcn = 'img/cian_12px.png';
";

$pinq="select properties.id,properties.address,properties.coords,connections.type from connections 
left join properties on properties.id=connections.property_id where properties.coords!=\"\" AND connections.date_end =\"0000-00-00\"";
if($searchb!="")
{
	$pinq.=" AND (address LIKE '%".$searchb."%' or ref LIKE '%".$searchb."%') ";
}


$pins=$mon3->query($pinq);
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"       var pin".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: ";

		  if($pin['type']=="GPON"){echo "imgf";}
		  elseif($pin['type']=="COAX"){echo "imgc";}
		  elseif($pin['type']=="FWA"){echo "imgcn";}
		  else{echo "imgi";}
		  	  
		  
		  echo",
		  title: \"".$pin['address']."\",
		  url: \"index.php?props=1&propid=".$pin['id']."\"
        });
		google.maps.event.addListener(pin".$pin['id'].", 'click', function() {
        window.location.href = this.url;
    });
";	
}

/* 
//Leads points


$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" AND status<\"50\" ");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"       var lead".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: ";
		  if($pin['status'] <= 20){echo "imgl ";} else {echo "imgi ";}
		  
		  echo",
		  title: \"".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
        window.location.href = this.url;
    });
";	
}

*/





	  
echo"
}
    </script>
    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
	
	<img src=img/red_12px.png>-fibre <img src=img/blue_12px.png>-coax <img src=img/cian_12px.png>-FWA <img src=img/orange_12px.png>-installing 
	
	
	
	
	
	
	<br><br>
	<div>
	<form name=serachp method=get>
	Search: <input type=text name=searchb value=\"$searchb\" Onkeyup=\"searchprop(this.value)\" ";

	
	echo">  
	<input type=hidden name=props value=1>
	</form>
	

	</div><br>

	<div id=tablec>   
	<table><tr> <th>ref</th><th>address</th><tr>";
	$props=$mon3->query("select id,ref,address from properties ".$qwhere." order by id desc limit ".$offset.",50 ");
	$count=$mon3->query("select count(*) from properties".$qwhere)->fetch_row();
	while($value=$props->fetch_assoc())
	{  
	echo	"<tr><td><a href=?props=1&propid=".$value['id'].">  ".$value['ref']."</a> </td>
			<td>".$value['address']. "</td></tr>"; 
	}	

echo "</table></div>

<div id=paging><br>";

if ($count[0]>50)
{
	$lastp=ceil($count[0]/50);
	$curpage=($offset/50)+1;
	
//	echo "curr: $curpage <br>";

	
//print initial page
	if($curpage>1)
	{
		echo "<a href=?searchb=".urlencode($searchb)."&props=1&offset=0>|<</a> ";
	}
//print page -2
	if($curpage>2)
	{
		echo "<a href=?searchb=".urlencode($searchb)."&props=1&offset=".($curpage-3)*50 .">".($curpage-2) ."</a> ";
	}
//print page -1
	if($curpage>1)
	{
		echo "<a href=?searchb=".urlencode($searchb)."&props=1&offset=".($curpage-2)*50 .">".($curpage-1) ."</a> ";
	}
//print curpage	
	
		echo " <b> $curpage </b> ";
//print page -1
	if($curpage<$lastp)
	{
		echo "<a href=?searchb=".urlencode($searchb)."&props=1&offset=".($curpage)*50 .">".($curpage+1) ."</a> ";
	}
	if($curpage<$lastp-1)
	{
		echo "<a href=?searchb=".urlencode($searchb)."&props=1&offset=".($curpage+1)*50 .">".($curpage+2) ."</a> ";
	}	
		
		if($curpage<$lastp)
	{
		echo "<a href=?searchb=".urlencode($searchb)."&props=1&offset=".($lastp-1)*50 .">>|</a> ";
	}	
		
}




echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results</div>";



echo "<br><a href=webservice.php?dump_prop=1>dump props</a>";

}





















