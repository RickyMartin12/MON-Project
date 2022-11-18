<?php 
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


















?>





<?php








//
/////// Property info//////////////////////////////////////////////////////////////////////////////////////////////////////////
//


if($_GET['propid']!=0)
{


$propid=mysqli_real_escape_string($mon3, $_GET['propid']);

$prop=$mon3->query("select id,ref,address,freguesia,coords,owner_id,owner_ref,management,notes,date 
from properties where id=$propid;")->fetch_assoc();
echo "<table border=1><tr>
<td width=550px valign=top style=\"position: relative\">

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
// CONNECTIONS ACTIVADAS NAS PROPRIEDADES
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

?>


<?php

echo "<br><table style=\"width: 100%;\" >
<tr><td style=\"width: 70%\"><b>Connections: </b><td align=center>
<a href=?props=1&conadd=$propid><img width=50px alt=\"add new connection to this property\" src=img/connectionadd.png></a></td>
</tr>
</table>
";
?>
<table style="width: 100%">
<tr>
<td style="width: 70%">
<?php
if($num_cons > 0 && $num_cons_disabled > 0) {
?>
<button id="prop-conn-id-<?php echo $propid; ?>" type="button" onclick="ShowAllConnections(<?php echo $propid; ?>)">Show All Connections</button>

<input type="hidden" id="all_conn" value="0">
<input type="hidden" id="conn_enab" value="1">
<?php
}
?>
</tr>
<tr >
	<td id=lists_connections>
        

<?php
$serv_link = "";


while($conn=$conns->fetch_assoc())
{
echo "<table >";
$conn_id = $conn['id'];
$dis_services = $conn['dis_services'];
$date_now = date("Y-m-d");
$dis_but_conn = "";
$dis_button_serv = '';
$rea_but_conn = '';
$rea_button_serv = '';

$dis_but_serv = '';
$dis_link_services = '';

$id_disabled_add_service = '';

// CONNECTIONS QUE PODEM SER DISCONECTADAS (DESATIVAR ADICIONAR SERVICOS E DESATIVAR EDICAO DE SERVICOS DA CONEXAO CORRESPONDENTE)
$conn_date_dis_con = $mon3->query("SELECT * FROM connections where id=".$conn['id'])->fetch_assoc();

$services_date_not_end_susp_2 = $mon3->query("SELECT * FROM services where is_susp_serv='2' AND connection_id=".$conn['id']);

$services_date_end_susp_2 = $mon3->query("SELECT * FROM services where date_end != '0000-00-00' AND is_susp_serv='2' connection_id=".$conn['id']);

// SERVICOS ATIVOS E DESATIVOS QUE PODEM SER SUSPENSOS (SERVICOS SO ATIVOS E SERVICOS ATIVOS E DESATIVOS)

$at_msg_log_serv = 0;
$conn_date_dis_service = $mon3->query("SELECT * FROM connections where id=".$conn['id'])->fetch_assoc();

$services_date_not_end_susp_1 = $mon3->query("SELECT * FROM services where date_end = '0000-00-00' AND is_susp_serv='1' AND connection_id=".$conn['id']);

$services_date_end_susp_1 = $mon3->query("SELECT * FROM services where date_end != '0000-00-00' AND is_susp_serv='1' connection_id=".$conn['id']);


$services_date_not_end_susp_0 = $mon3->query("SELECT * FROM services where date_end = '0000-00-00' AND is_susp_serv='0' AND connection_id=".$conn['id']);

$services_date_end_susp_0 = $mon3->query("SELECT * FROM services where date_end != '0000-00-00' AND is_susp_serv='0' connection_id=".$conn['id']);


// dis-services
$div_dis_services = '';

// dis-conn
$div_dis_conn = '';

// rea-services
$div_rea_services = '';

	//is_sups_serv=1

	//$date_now = "2022-09-30";

	$servs_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id'])->num_rows;

	// SUSPENDER SERVIÇOS

	if($conn_date_dis_service['date_dis_services'] != '0000-00-00')
	{
		if($date_now >= $conn_date_dis_service['date_dis_services'])
		{
			if($conn_date_dis_service['dis_services'] != 1)
				{
					$dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"1\", 
					date_dis_services=\"".$conn_date_dis_service['date_dis_services']."\", date_rea_services=\"0000-00-00\", date_dis_conn=\"0000-00-00\" WHERE id=".$conn['id']);

					//$dis_services = 1;

					$conn_dis = $mon3->query("SELECT * FROM connections WHERE id=".$conn['id'])->fetch_assoc();

					$dis_services = $conn_dis['dis_services'];


					
					while($service_date_not_end_susp_1=$services_date_not_end_susp_1->fetch_assoc())
					{
						$dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"1\" WHERE id=".$service_date_not_end_susp_1['id']);
					}
					

					proplog($propid,"All Services on connection number <b>".$conn['id']."</b> was suspended on date <b>".$conn_date_dis_service['date_dis_services']."</b>");
				
				}
				$dis_but_serv_img='disabledLink';
				$dis_but_serv = 'class=disabledLink';
				$id_disabled_add_service= "id=div-serv-conn-id-".$conn['id']." style=\"display: inline-block\" onmouseover=\"mouseOverSuspAddService(".$conn['id'].")\" onmouseout=\"mouseOutSuspAddService(".$conn['id'].")\" ";

		}
		else
		{
			$dis_button_serv = 'disabled';
			//$dis_but_serv = 'class=disabledLink';
			//$dis_but_serv='class=disabledLink';
			$dis_but_serv_img='disabledLink';
			$rea_button_serv_img='';
			$div_dis_services = "onmouseover=\"mouseOverDivSuspServices(".$conn['id'].",'".$conn_date_dis_service['date_dis_services']."')\" onmouseout=\"mouseOutDivSuspServices(".$conn['id'].",'".$conn_date_dis_service['date_dis_services']."')\" ";
		}
	}

	// DISCONNECTAR CONEXAO

	else if($conn_date_dis_con['date_dis_conn'] != '0000-00-00')
	{
		if($date_now >= $conn_date_dis_con['date_dis_conn'])
		{
			if($conn_date_dis_con['dis_services'] != 2)
			{
				$dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"2\", 
				date_dis_conn=\"".$conn_date_dis_con['date_dis_conn']."\", date_rea_services=\"0000-00-00\", date_dis_services=\"0000-00-00\" WHERE id=".$conn['id']);
					//$dis_services = 2;
					while($service_date_not_end_susp_2=$services_date_not_end_susp_2->fetch_assoc())
					{
						$dis_serv_each_id = $mon3->query("UPDATE services set date_end=\"".$conn_date_dis_con['date_dis_conn']."\", is_susp_serv=\"2\" WHERE id=".$service_date_not_end_susp_2['id']);


					}

					$conn_dis = $mon3->query("SELECT * FROM connections WHERE id=".$conn['id'])->fetch_assoc();

					$dis_services = $conn_dis['dis_services'];

					// UPDATE EQUIP NULL ON CONNECTIONS
					$update_equip = $mon3->query("UPDATE connections set equip_id =\"\" WHERE id=".$conn['id']);

					// UPDATE EQUIP NULL ON SERVICES
					$update_equip_services = $mon3->query("UPDATE services set equip_id =\"\" WHERE connection_id=".$conn['id'] );

					proplog($propid,"The connection number <b>".$conn['id']."</b> are disabled on date <b>".$conn_date_dis_con['date_dis_conn']."</b>");		
			}
		
			$dis_but_serv = 'class=disabledLink';
			$dis_button_serv = 'disabled';

			$dis_but_conn = 'disabled';

			$dis_but_serv_img='disabledLink';
			//$dis_but_conn_img='disabledLink';

			$id_disabled_add_service= "id=div-serv-conn-id-".$conn['id']." style=\"display: inline-block\" onmouseover=\"mouseOverDisAddService(".$conn['id'].")\" onmouseout=\"mouseOutDisAddService(".$conn['id'].")\" ";

		}
		else
		{
			$dis_but_conn = 'disabled';
			$dis_button_serv = 'disabled';

			$dis_but_conn_img='disabledLink';


			$div_dis_conn = "onmouseover=\"mouseOverDivDisConn(".$conn['id'].",'".$conn_date_dis_con['date_dis_conn']."')\" onmouseout=\"mouseOutDivDisConn(".$conn['id'].",'".$conn_date_dis_con['date_dis_conn']."')\" ";

		}
	}

	// REACTIVAR SERVICOS
	
	else if($conn_date_dis_con['date_rea_services'] != '0000-00-00')
	{
		if($date_now >= $conn_date_dis_con['date_rea_services'])
		{
			if($conn_date_dis_con['dis_services'] != 0)
			{
				$rea_ser_conn = $mon3->query("UPDATE connections set dis_services =\"0\", 
					date_dis_services=\"0000-00-00\", date_dis_conn=\"0000-00-00\", date_rea_services=\"".$conn_date_dis_con['date_rea_services']."\" WHERE id=".$conn['id']);

				//$dis_services = 0;

				while($service_date_not_end_susp_0=$services_date_not_end_susp_0->fetch_assoc())
				{
					$dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"0\" WHERE id=".$services_date_not_end_susp_0['id']);
				}

				$conn_dis = $mon3->query("SELECT * FROM connections WHERE id=".$conn['id'])->fetch_assoc();

				$dis_services = $conn_dis['dis_services'];


				proplog($propid,"All Services on connection number <b>".$conn['id']."</b> was reactivated on date <b>".$conn_date_dis_con['date_rea_services']."</b>");

			}
			$dis_but_serv_img='';
		}
		else
		{
			$dis_but_conn = 'disabled';
			$rea_button_serv = 'disabled';

			$dis_but_serv = 'class=disabledLink';
			$id_disabled_add_service= "id=div-serv-conn-id-".$conn['id']." style=\"display: inline-block\" onmouseover=\"mouseOverDisAddService(".$conn['id'].")\" onmouseout=\"mouseOutDisAddService(".$conn['id'].")\" ";


			//$dis_but_conn_img='disabledLink';
			$rea_button_serv_img='disabledLink';

			$div_rea_services = "onmouseover=\"mouseOverDivReaServ(".$conn['id'].",'".$conn_date_dis_con['date_rea_services']."')\" onmouseout=\"mouseOutDivReaServ(".$conn['id'].",'".$conn_date_dis_con['date_rea_services']."')\" ";
		}
	}
	
	
	
	else if($conn_date_dis_con['date_rea_services'] == '0000-00-00' && $conn_date_dis_con['date_dis_conn'] == '0000-00-00' && $conn_date_dis_con['date_dis_services'] == '0000-00-00')
	{
		$dis_but_serv_img='';
		$rea_button_serv_img='';
		$dis_but_conn_img='';
	}



	if($servs_num == 0)
	{
		$dis_but_serv_img='disabledLink';

		$div_dis_services = "onmouseover=\"mouseOverDivSuspNotServices(".$conn['id'].")\" onmouseout=\"mouseOutDivSuspNotServices(".$conn['id'].")\" ";


		$rea_button_serv_img='disabledLink';

		$div_rea_services = '';
	}
	







?>
</table >

<table style="width: 100%">
<?php


echo "<tr><td colspan=2>";





if($conn['type']=="COAX")
{
///echo "rr ".$conn['equip_id'];
	$equip=$mon3->query("select * from coax_modem 
where UPPER(mac)= UPPER(\"".$conn['equip_id']."\")")->fetch_assoc();

$card=$mon3->query("select name from coax_upstreams where cmts_id=\"".$equip['cmts']."\" and upstream_id=\"".$equip['interface']."\" ")->fetch_assoc();
//echo "error4: ".mysqli_error($mon3);


echo "<tr><td style=\"width: 70%;\">
id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
type:".$conn['type']." <br>
equip: <a href=?equip=".$conn['equip_id']."&equip_type=COAX>".$conn['equip_id']. "</a> - 
<a href=http://".$equip['mng_ip'].">". $equip['mng_ip']. "</a><br>
model: ".$equip['model']."<br>
CELL: <a href=?coax=1&upstream=".$equip['interface']."&cmts=".$equip['cmts'].">".$equip['interface']."-".$card['name']."</a><br>
date start:".$conn['date_start']."<br>";

?>
<td>
<div class="services_connect_add">


<!--<img src="img/multiply.png" class="img_mon">-->
<?php
if($dis_services == 0) {
?>
<div id="dis-services-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_services ?>>
<img src="img/power-off.png" class="img_mon <?php echo $dis_but_serv_img; ?>" onclick="clickDisService(<?php echo $conn['id']; ?>);" id="dis-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverDisService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutDisService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseDisServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Suspend Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date End Services:</b> <input type=date name=date_end_services-<?php echo $conn['id']; ?> id=date_end_services-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitDisabledServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Suspend Services</button>
				</div>

				<span id=submit-dis-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disconnect Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>

<?php
} else if($dis_services == 1) {
?>

<div id="rea-services-div-<?php echo $conn['id']; ?>" <?php echo $div_rea_services ?>>
<img src="img/power-button.png" class="img_mon <?php echo $rea_button_serv_img; ?>" onclick="clickReaService(<?php echo $conn['id']; ?>);" id="rea-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverReaService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutReaService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-rea-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-rea-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseReaServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Reactivate Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-<?php echo $conn['id']; ?> id=date_end_services_rea-<?php echo $conn['id']; ?> class="dates_conn_services"  data-date="" data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitReactiveServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Reactive Services</button>
				</div>

				<span id=submit-rea-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disconnect Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>
<?php
}
?>
</div>
</td>
<?php

/*if($conn['date_end'] == "0000-00-00")
{
	echo "Status Connection: <font color=green>Activated</font><br><br>";
}
else
{
	echo "Status Connection: <font color=red>Deactivated</font><br><br>";
}*/

echo "<tr><td><b>status</b> ( by ".date("Y-m-d H:i:s ",$equip['status_timestamp'])."):<br>
DSlevel: ".$equip['ds_power']."dBm - DSsnr: ".$equip['ds_snr']."dB<br> 
USlevel: ".$equip['us_power']."dBm - USsnr: ".$equip['us_snr']."dB -  Rcv_level: ".$equip['rcv_power']."<br>";



if($equip['cmts'] == "" || $equip['mac'] == "")
{
	$dis_but_serv_equip_mac = 'class=disabledLink';
	$id_status = "id=div-status-conn-id-".$conn['id']."  onmouseover=\"mouseOverStatusCTMS(".$conn['id'].")\" onmouseout=\"mouseOutStatusCTMS(".$conn['id'].")\" ";
	$id_reboot = "id=div-reboot-conn-id-".$conn['id']."  onmouseover=\"mouseOverRebootsCTMS(".$conn['id'].")\" onmouseout=\"mouseOutRebootsCTMS(".$conn['id'].")\" ";
}
else if($equip['cmts'] != "" || $equip['mac'] != "")
{
	$dis_but_serv_equip_mac = '';
	$id_status = '';
	$id_reboot = '';
}


echo "
<br><br>
<table width=300px>
<tr><td width=25% align=center ".$id_status ."><img width=48px src=img/status_green.png onclick=\"popup2('status','COAX','".$equip['cmts']."','".$equip['mac']."', ".$conn['id'].");\" ".$dis_but_serv_equip_mac."> <span id=\"add-status-coax-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
<td width=25% align=center ".$id_reboot ."><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','COAX','".$equip['cmts']."','".$equip['mac']."', ".$conn['id'].");\" ".$dis_but_serv_equip_mac."> <span id=\"add-reboot-coax-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>

<div id=info_connection_".$conn['id'].">
</div>
 
</table>
<br><br>
";
}











elseif($conn['type']=="GPON")
{
 
	$equip=$mon3->query("select * from ftth_ont 
where fsan=\"".$conn['equip_id']."\"")->fetch_assoc();

	
echo "error4: ".mysqli_error($mon3).$conn['equip_id'] ." ". $equip['olt_id'];
	$ont2=explode("-",$equip['ont_id']);
	$olt=$mon3->query("select name from ftth_olt where id=\"".$equip['olt_id']."\"" )->fetch_assoc();
	$pon=$mon3->query("select name from ftth_pons where olt_id=\"".$equip['olt_id']."\" and
	card=\"".$ont2[1]."\" AND pon=\"".$ont2[2]."\"; ")->fetch_assoc();

echo "<tr><td style=\"width: 70%;\">
id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
type:".$conn['type']." <br>
olt: ".$olt['name']." <br>
PON: <a href=?gpon=1&pon=".$ont2[1]."-".$ont2[2]."&olt=".$equip['olt_id'].">".$pon['name']."</a><br>
equip: <a href=?equip=".$conn['equip_id']."&equip_type=GPON>".$conn['equip_id']. "</a> - 
<a href=http://".$equip['mng_ip'].">".$equip['ont_id']. "</a><br>
model: ".$equip['meprof']."<br>
date start:".$conn['date_start']."<br>";

if($equip['olt_id'] == "" || $equip['ont_id'] == "")
{
	
	$dis_but_serv_equip = 'class=disabledLink';
	$id_status = "id=div-status-conn-id-".$conn['id']."  onmouseover=\"mouseOverStatusONT(".$conn['id'].")\" onmouseout=\"mouseOutStatusONT(".$conn['id'].")\" ";
	$id_reboot = "id=div-reboot-conn-id-".$conn['id']. "  onmouseover=\"mouseOverRebootONT(".$conn['id'].")\" onmouseout=\"mouseOutRebootONT(".$conn['id'].")\" ";
	$id_sync = "id=div-sync-conn-id-".$conn['id']." onmouseover=\"mouseOverSyncONT(".$conn['id'].")\" onmouseout=\"mouseOutSyncONT(".$conn['id'].")\" ";
	$id_reset = "id=div-reset-conn-id-".$conn['id']." onmouseover=\"mouseOverResetONT(".$conn['id'].")\" onmouseout=\"mouseOutResetONT(".$conn['id'].")\" ";

}
else if($equip['olt_id'] != "" || $equip['ont_id'] != "")
{
	$dis_but_serv_equip = '';
	$id_status = '';
	$id_reboot = '';
	$id_sync = '';
	$id_reset = '';
}

?>
<td >
<div class="services_connect_add">


<!--<img src="img/multiply.png" class="img_mon">-->
<?php

if($dis_services == 0) {
?>
<div id="dis-services-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_services ?>>
<img src="img/power-off.png" class="img_mon <?php echo $dis_but_serv_img; ?>" onclick="clickDisService(<?php echo $conn['id']; ?>);" id="dis-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverDisService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutDisService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseDisServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Suspend Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date End Services:</b> <input type=date name=date_end_services-<?php echo $conn['id']; ?> id=date_end_services-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitDisabledServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Suspend Services</button>
				</div>

				<span id=submit-dis-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disconnect Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>

<?php
} else if($dis_services == 1) {
?>

<div id="rea-services-div-<?php echo $conn['id']; ?>" <?php echo $div_rea_services ?>>
<img src="img/power-button.png" class="img_mon <?php echo $rea_button_serv_img; ?>" onclick="clickReaService(<?php echo $conn['id']; ?>);" id="rea-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverReaService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutReaService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-rea-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-rea-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseReaServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Reactivate Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-<?php echo $conn['id']; ?> id=date_end_services_rea-<?php echo $conn['id']; ?> class="dates_conn_services"  data-date="" data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitReactiveServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Reactive Services</button>
				</div>

				<span id=submit-rea-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disconnect Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>
<?php
}
?>
</div>
</td>
<?php


/*if($conn['date_end'] == "0000-00-00")
{
	echo "Status Connection: <font color=green>Activated</font><br><br>";
}
else
{
	echo "Status Connection: <font color=red>Deactivated</font><br><br>";
}*/


echo "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
 <span id=status1 onmouseover=\"status1()\" onmouseout=\"status1o()\">".$equip['status']." </span>
 <div class=popup1 id=popup1> ".str_replace("\n","<br>",$equip['errors'])." </div> <br> 
oltrx: ".$equip['tx']." oltrx: ".$equip['rx']."<br> 
rf: ".$equip['rf']."<br><br>";







echo "
<table width=300px>
<tr><td width=25% align=center ".$id_status ."><img width=48px src=img/status_green.png onclick=\"popup2('status','GPON','".$equip['olt_id']."','".$equip['ont_id']."',".$conn['id'].");\" ".$dis_but_serv_equip."> <span id=\"add-status-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
<td width=25% align=center ".$id_reboot ."><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','GPON','".$equip['olt_id']."','".$equip['ont_id']."',".$conn['id'].");\" ".$dis_but_serv_equip."> <span id=\"add-reboot-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
<td width=25% align=center ".$id_sync ."><img width=48px src=img/sync_green.png onclick=\"popup2('sync','GPON','".$equip['olt_id']."','".$equip['ont_id']."',".$conn['id'].");\" ".$dis_but_serv_equip."> <span id=\"add-sync-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
<td width=25% align=center ".$id_reset ."><img width=48px src=img/reset_green.png onclick=\"popup2('reset','GPON','".$equip['olt_id']."','".$equip['ont_id']."',".$conn['id'].");\" ".$dis_but_serv_equip."> <span id=\"add-reset-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>

<div id=info_connection_".$conn['id'].">
</div>

</table>

<br><br>";

}







elseif($conn['type']=="FWA")
{
 
	$equip=$mon3->query("select * from fwa_cpe where mac=\"".$conn['equip_id']."\"")->fetch_assoc();

echo "error4: ".mysqli_error($mon3).$conn['equip_id'] ." - ". $equip['antenna'];

	$ant=$mon3->query("select * from fwa_antennas where id=\"".$equip['antenna']."\"" )->fetch_assoc();


echo "<tr><td style=\"width: 70%;\">
id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
type:".$conn['type']." <br>
Antenna: ".$ant['name']." <br>
equip: <a href=?equip=".$conn['equip_id']."&equip_type=fwa>".$conn['equip_id']. "</a> - 
<a href=http://".$equip['mng_ip'].">".$equip['mac']. "</a><br>
model: ".$equip['model']."<br>
date start:".$conn['date_start']."<br>";



?>
<td >
<div class="services_connect_add">


<!--<img src="img/multiply.png" class="img_mon">-->
<?php
if($dis_services == 0) {
?>
<div id="dis-services-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_services ?> >
<img src="img/power-off.png" class="img_mon <?php echo $dis_but_serv_img; ?>" onclick="clickDisService(<?php echo $conn['id']; ?>);" id="dis-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverDisService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutDisService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseDisServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Suspend Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date End Services:</b> <input type=date name=date_end_services-<?php echo $conn['id']; ?> id=date_end_services-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitDisabledServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Suspend Services</button>
				</div>

				<span id=submit-dis-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disconnect Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>

<?php
} else if($dis_services == 1) {
?>

<div id="rea-services-div-<?php echo $conn['id']; ?>" <?php echo $div_rea_services ?>>
<img src="img/power-button.png" class="img_mon <?php echo $rea_button_serv_img; ?>" onclick="clickReaService(<?php echo $conn['id']; ?>);" id="rea-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverReaService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutReaService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-rea-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-rea-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseReaServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Reactivate Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-<?php echo $conn['id']; ?> id=date_end_services_rea-<?php echo $conn['id']; ?> class="dates_conn_services"  data-date="" data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				
				<div style="text-align: center; ">
					<button type="button" onclick="submitReactiveServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Reactive Services</button>
				</div>

				<span id=submit-rea-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disconnect Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>
<?php
}
?>
</div>
</td>
<?php

/*
echo "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
 <span id=status1 onmouseover=\"status1()\" onmouseout=\"status1o()\">".$equip['status']." </span>
 <div class=popup1 id=popup1> ".str_replace("\n","<br>",$equip['errors'])." </div> <br> 
oltrx: ".$equip['tx']." oltrx: ".$equip['rx']."<br> 
rf: ".$equip['rf']."<br><br>


<table width=300px>
<tr><td width=25% align=center><img width=48px src=img/status_green.png onclick=\"popup2('status','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\"> 
<td width=25% align=center><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\"> 
<td width=25% align=center><img width=48px src=img/sync_green.png onclick=\"popup2('sync','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\"> 
<td width=25% align=center><img width=48px src=img/reset_green.png onclick=\"popup2('reset','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\"> 
</table>
<br><br>";
*/
echo "<br><br>";

}











elseif($conn['type']=="ETH" || $conn['type']=="ETHF")
{

echo "<tr><td style=\"width: 70%;\">
id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
type: ".$conn['type']." <br>";
if($conn['linked_prop']>0)
{
	$lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
	$lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
	echo "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
}

echo "<br><br>";

?>
<td >
<div class="services_connect_add">


<!--<img src="img/multiply.png" class="img_mon">-->
<?php
if($dis_services == 0) {
?>
<div id="dis-services-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_services ?>>
<img src="img/power-off.png" class="img_mon <?php echo $dis_but_serv_img; ?>" onclick="clickDisService(<?php echo $conn['id']; ?>);" id="dis-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverDisService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutDisService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseDisServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disable Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date End Services:</b> <input type=date name=date_end_services-<?php echo $conn['id']; ?> id=date_end_services-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitDisabledServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disable Services</button>
				</div>

				<span id=submit-dis-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disconnect Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disable Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>

<?php
} else if($dis_services == 1) {
?>

<div id="rea-services-div-<?php echo $conn['id']; ?>" <?php echo $div_rea_services ?>>
<img src="img/power-button.png" class="img_mon <?php echo $rea_button_serv_img; ?>" onclick="clickReaService(<?php echo $conn['id']; ?>);" id="rea-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverReaService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutReaService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-rea-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-rea-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseReaServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Reactivate Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-<?php echo $conn['id']; ?> id=date_end_services_rea-<?php echo $conn['id']; ?> class="dates_conn_services"  data-date="" data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitReactiveServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Reactive Services</button>
				</div>

				<span id=submit-rea-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disable Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disable Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>
<?php
}
?>
</div>
</td>
<?php


}






elseif($conn['type']=="DARKF")
{

echo "<tr><td style=\"width: 70%;\">
id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
type: ".$conn['type']." <br>";
	
echo "<br><br>";	

?>
<td >
<div class="services_connect_add">


<!--<img src="img/multiply.png" class="img_mon">-->
<?php
if($dis_services == 0) {
?>
<div id="dis-services-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_services ?>>
<img src="img/power-off.png" class="img_mon <?php echo $dis_but_serv_img; ?>" onclick="clickDisService(<?php echo $conn['id']; ?>);" id="dis-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverDisService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutDisService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseDisServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disable Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date End Services:</b> <input type=date name=date_end_services-<?php echo $conn['id']; ?> id=date_end_services-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitDisabledServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disable Services</button>
				</div>

				<span id=submit-dis-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disable Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disable Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>

<?php
} else if($dis_services == 1) {
?>

<div id="rea-services-div-<?php echo $conn['id']; ?>" <?php echo $div_rea_services ?>>
<img src="img/power-button.png" class="img_mon <?php echo $rea_button_serv_img; ?>" onclick="clickReaService(<?php echo $conn['id']; ?>);" id="rea-services-<?php echo $conn['id']; ?>" onmouseover="mouseOverReaService(<?php echo $conn['id']; ?>);" onmouseout="mouseOutReaService(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-rea-services-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-conn-rea-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickCloseReaServices(<?php echo $conn['id']; ?>);">×</span>
				<h1>Reactivate Serviçes <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-<?php echo $conn['id']; ?> id=date_end_services_rea-<?php echo $conn['id']; ?> class="dates_conn_services"  data-date="" data-date-format="YYYY-MM-DD"  value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<div style="text-align: center; ">
					<button type="button" onclick="submitReactiveServices(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Reactive Services</button>
				</div>

				<span id=submit-rea-services-<?php echo $conn['id']; ?>></span>
			</div>
</div>

<div id="dis-conn-div-<?php echo $conn['id']; ?>" <?php echo $div_dis_conn ?>>
<img src="img/multiply.png" class="img_mon <?php echo $dis_but_conn_img; ?>" onclick="disablePropConnection(<?php echo $conn['id']; ?>)" id="dis-prop-conn-<?php echo $conn['id']; ?>" onmouseover="mouseOverPropConn(<?php echo $conn['id']; ?>);" onmouseout="mouseOutPropConn(<?php echo $conn['id']; ?>);"> 
</div>
<span id="title-dis-conn-<?php echo $conn['id']; ?>" class="warning-serv-add-servs-conns" style="display: none;"></span>
<div class="modal" id="modal-prop-conn-<?php echo $conn['id']; ?>">
			<div class="modal-content">
				<span class="close-button" onclick="clickClosePropConnection(<?php echo $conn['id']; ?>);">×</span>
				<h1>Disable Connection <?php echo $conn['id']; ?></h1>
				<br>
				<b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-<?php echo $conn['id']; ?> id=date_end_conn-<?php echo $conn['id']; ?> data-date="" class="dates_conn_services"  data-date-format="YYYY-MM-DD" value=<?php echo date("Y-m-d"); ?> size=10> YYYY-MM-DD<br>
				<br>
				<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
				<div style="text-align: center; ">
					<button type="button" onclick="submitPropConnection(<?php echo $conn['id']; ?>, <?php echo $propid; ?>);" >Submit Disable Connection</button>
				</div>

				<span id=submit-dis-conn-<?php echo $conn['id']; ?>></span>
			</div>

</div>
<?php
}
?>
</div>
</td>
<?php
	

}


$conn_id = $conn['id'];
$serv_dis_num = $mon3->query("SELECT * FROM services WHERE connection_id = ".$conn['id']." and date_end != '0000-00-00'")->num_rows;
$serv_en_num = $mon3->query("SELECT * FROM services WHERE connection_id = ".$conn['id']." and date_end = '0000-00-00'")->num_rows;

// SERVICES SUSPENDED - 1
$serv_susp_1_num = $mon3->query("SELECT * FROM services WHERE connection_id = ".$conn['id']." and is_susp_serv = '1' ")->num_rows;
// SERVICES ATIVATED - OK - 0 
$serv_susp_0_num = $mon3->query("SELECT * FROM services WHERE connection_id = ".$conn['id']." and is_susp_serv = '0' ")->num_rows;
// DISCONNECTION -2
$serv_susp_0_num = $mon3->query("SELECT * FROM services WHERE connection_id = ".$conn['id']." and is_susp_serv = '2' ")->num_rows;
?>

<tr>
	<?php

	





	

	


	

	?>

		<script>
			$(".dates_conn_services").on("change", function() {
				this.setAttribute(
					"data-date",
					moment(this.value, "YYYY-MM-DD")
					.format( this.getAttribute("data-date-format") )
				)
				}).trigger("change");
		</script>	
	<?php
//services

$servs_diss = 0;
$conn_id = $conn['id'];

$conns_services_disc = array();
$conns_services_susp_1 = array();
$conns_id_dis_2 = array();	

if($serv_dis_num > 0 && $serv_en_num == 0)
{
	$servs_diss = 1;
}
else if($serv_dis_num > 0 && $serv_en_num > 0 || $serv_dis_num == 0 && $serv_en_num > 0)
{
	$servs_diss = 0;
}
else if($serv_dis_num == 0 && $serv_en_num == 0)
{
	$servs_diss = 2;
}
echo "<br>";
$con_list = "";
$conns_id = "";
// DISABLED SERVICES
if($servs_diss == 1 && $dis_services == 0)
{
	$conns_services_disc[] = $conn_id;
	//echo "<div id=info_prop_serv_conn><font color=red><b>Disabled Services on connection ".$conn_id."</b></font></div>";
}
else if($dis_services == 1)
{
	$conns_services_susp_1[] = $conn_id;
	//echo "<div id=info_prop_serv_conn><font color=red><b>Disabled Services Suspended on connection ".$conn_id."</b></font></div>";
}
else if($dis_services == 2 )
{
	$conns_id_dis_2[] = $conn_id;
	//var_dump($conns_id_dis_2);
	
}

echo "</table><table style=\"width: 100%;\"><tr><td style=\"width: 70%\"><b>Services: </b>  <td align=\"center\" ><div ".$id_disabled_add_service."><a id=serv-conn-".$conn['id']." href=?servs=1&addserv=".$conn['id']." ".$dis_but_serv."> <img width=60px src=img/packageadd.png></a> <span id=\"add-serv-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span></div>
<tr><td><br></table>";


	








if($serv_dis_num > 0 && $serv_en_num > 0)
{
	
?>
<table>
<button id="en_serv-<?php echo $conn['id'] ?>" type="button" onclick="ShowAllServices(<?php echo $conn['id'] ?>, '<?php echo $conn['type'] ?>')">Show All Services</button>
<input type="hidden" id="disabled_services-<?php echo $conn['id'] ?>" value="0">
<input type="hidden" id="enabled_services-<?php echo $conn['id'] ?>" value="1">
<input type="hidden" id="click_en_serv-<?php echo $conn['id'] ?>" value=0>
</table>
<?php

$conn_id = $conn['id'];

$eq_conn = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id."")->fetch_assoc();
        
        if($type == "GPON"){
            $equip=$mon3->query("select * from ftth_ont where fsan=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "FWA"){
            $equip=$mon3->query("select * from fwa_cpe where mac=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "COAX"){
            $equip=$mon3->query("select * from coax_modem where UPPER(mac)= UPPER(\"".$eq_conn['equip_id']."\")")->fetch_assoc();
        }


$services = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end = '0000-00-00' order by id DESC");

echo "<table id=dis_serv_lists-".$conn['id'].">";
while($service=$services->fetch_assoc())
{
	$dis_services = $eq_conn['dis_services'];
    $serv_susp = $service['is_susp_serv'];

	$serv_id = $service['id'];

	if($dis_services == 1 && $serv_susp == 1)
	{
		$serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
        $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
        $tr_dis_services_end = "</div>";
	}

	else if($dis_services == 2 && $serv_susp == 2)
	{
		$serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
        $tr_dis_services = "<div id=\"tr-serv-dis-conn-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisConn(".$serv_id.")\" onmouseout=\"mouseOutDisConn(".$serv_id.")\">";
        $tr_dis_services_end = "</div>";
	}
	
	echo "<tr><td style=width:100%><br>
	<tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
	</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";

	
	
	
	

	$atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


	echo "<table>";
	while($att=$atts->fetch_assoc())
	{


	
//PHN service
		if($att['name']=="account")
		{
			echo " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
			$nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
			echo " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
		}

		elseif($att['name']=="unifi_site")
		{

			echo " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
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

}
else if($serv_dis_num > 0 && $serv_en_num == 0)
{
	
	?>
	<table>
    <button id="des_serv-<?php echo $conn['id'] ?>" type="button" onclick="ShowAllServices(<?php echo $conn['id'] ?>, '<?php echo $conn['type'] ?>')">Show All Services</button> 
    <input type="hidden" id="disabled_services-<?php echo $conn['id'] ?>" value="1">
    <input type="hidden" id="enabled_services-<?php echo $conn['id'] ?>" value="0">
	<input type="hidden" id="click_des_serv-<?php echo $conn['id'] ?>" value=0>
	</table>
	<?php

$conn_id = $conn['id'];

$eq_conn = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id."")->fetch_assoc();

$serv_link= "";
        
        if($type == "GPON"){
            $equip=$mon3->query("select * from ftth_ont where fsan=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "FWA"){
            $equip=$mon3->query("select * from fwa_cpe where mac=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "COAX"){
            $equip=$mon3->query("select * from coax_modem where UPPER(mac)= UPPER(\"".$eq_conn['equip_id']."\")")->fetch_assoc();
        }


//$services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn['id']." AND s1.date_end != '0000-00-00' and s1.date_start = (SELECT MAX(s2.date_start) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) and s1.date_end = (SELECT MAX(s2.date_end) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.date_start DESC");
$services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.id DESC");


echo "<table id=dis_serv_lists-".$conn['id'].">";
while($service=$services->fetch_assoc())
{

	$dis_services = $eq_conn['dis_services'];
    $serv_susp = $service['is_susp_serv'];

	$serv_id = $service['id'];

	

	if($dis_services == 1 && $serv_susp == 1)
	{
		$serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
        $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
        $tr_dis_services_end = "</div>";
	}

	else if($dis_services == 2 && $serv_susp == 2)
	{
		$serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
        $tr_dis_services = "<div id=\"tr-serv-dis-conn-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisConn(".$serv_id.")\" onmouseout=\"mouseOutDisConn(".$serv_id.")\">";
        $tr_dis_services_end = "</div>";
	}
	
	echo "<tr><td style=width:100%><br>
	<tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
	</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";
	echo "status: <font color=red><b> Disabled</b></font>"; 

	
	

	$atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


	echo "<table>";
	while($att=$atts->fetch_assoc())
	{


	
//PHN service
		if($att['name']=="account")
		{
			echo " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
			$nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
			echo " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
		}

		elseif($att['name']=="unifi_site")
		{

			echo " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
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
}
else
{
	
	$conn_id = $conn['id'];

	

	$eq_conn = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id."")->fetch_assoc();
        
        if($type == "GPON"){
            $equip=$mon3->query("select * from ftth_ont where fsan=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "FWA"){
            $equip=$mon3->query("select * from fwa_cpe where mac=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "COAX"){
            $equip=$mon3->query("select * from coax_modem where UPPER(mac)= UPPER(\"".$eq_conn['equip_id']."\")")->fetch_assoc();
        }

    $services = $mon3->query("select id,connection_id,type,date_start,date_end,contract_id,subscriber,is_susp_serv from services where connection_id=\"".$conn_id."\" order by id DESC;");


	echo "<table id=dis_serv_lists-".$conn['id'].">";
	while($service=$services->fetch_assoc())
	{

		$dis_services = $eq_conn['dis_services'];
		$serv_susp = $service['is_susp_serv'];

		$serv_id = $service['id'];
		
		if($dis_services == 1 && $serv_susp == 1)
		{
			$serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
			$tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
			$tr_dis_services_end = "</div>";
		}

		else if($dis_services == 2 && $serv_susp == 2)
		{
			$serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
			$tr_dis_services = "<div id=\"tr-serv-dis-conn-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisConn(".$serv_id.")\" onmouseout=\"mouseOutDisConn(".$serv_id.")\">";
			$tr_dis_services_end = "</div>";
		}

		
		echo "<tr><td style=width:100%><br>
		<tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
		</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";	
		if($service['date_end']!="0000-00-00")
		{
			echo "status: <font color=red><b> Disabled</b></font>"; 
		}	

		$atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


		echo "<table>";
		while($att=$atts->fetch_assoc())
		{


	
	//PHN service
			if($att['name']=="account")
			{
				echo " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
				$nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
				echo " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
			}

			elseif($att['name']=="unifi_site")
			{

				echo " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
			}
			
			elseif($att['name']=="speed")
			{

				$equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				$equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];
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
}















echo "
";


	
}
?>
<table><tr><td colspan=2><br><br><tr><td colspan=2><br><br>

</td>
    </tr>
</table>
    </tr>

<?php
echo "</table>";
/*
echo "<table style=\"width: 100%;\"><tr><td><div style=\"text-align: center\"><b>Add Log Entry:</b> <br><br><textarea name=notes id=notes cols=60 rows=5></textarea>
<br> <br> <input type=button value=\"Submit Sugestion\" onclick=\"SubmitSugestion(".$propid.");\">
<br><br>
<span id=submit_notes_succ></span>
</div></table>	";
*/
// SERVICES DISABLED
$dis_services = '';
$susp_services = '';
$dis_connec = '';


if(count($conns_services_disc) == 1)
{
	$conns_id .= "<font color=red><b>Disabled Services on connection ".$conns_services_disc[0]."</b></font><br>";
}
else if(count($conns_services_disc) > 1)
{
	for($i=0; $i<count($conns_services_disc); $i++)
		{
			if($i==count($conns_services_disc)-1)
			{
				$dis_services .= $conns_services_disc[$i];
			}
			else
			{
				$dis_services .= $conns_services_disc[$i].",";
			}
		}
	$conns_id .= "<font color=red><b>Disabled Services on connections ".$dis_services."</b></font><br>";
}


// SERVICES SUSPENDED

if(count($conns_services_susp_1) == 1)
{
	$conns_id .= "<font color=red><b>Services Suspended on connection ".$conns_services_susp_1[0]."</b></font><br>";
}
else if(count($conns_services_susp_1) > 1)
{
	for($i=0; $i<count($conns_services_susp_1); $i++)
		{
			if($i==count($conns_services_susp_1)-1)
			{
				$susp_services .= $conns_services_susp_1[$i];
			}
			else
			{
				$susp_services .= $conns_services_susp_1[$i].",";
			}
		}
	$conns_id .= "<font color=red><b>Services Suspended on connections ".$susp_services."</b></font><br>";
}


// CONNECTIONS DISCONNECTED

if(count($conns_id_dis_2) == 1)
	{
		$conns_id .= "<font color=red><b>Disconnected on Connection ".$conns_id_dis_2[0]."</b></font><br>";
	}
	else if(count($conns_id_dis_2) > 1)
	{
		
		for($i=0; $i<count($conns_id_dis_2); $i++)
		{
			if($i==count($conns_id_dis_2)-1)
			{
				$dis_connec .= $conns_id_dis_2[$i];
			}
			else
			{
				$dis_connec .= $conns_id_dis_2[$i].",";
			}
		}
		$conns_id .= "<font color=red><b>Disconnected on Connections ".$dis_connec."</b></font><br>";
	}
	
	echo "<div id=info_prop_serv_conn>".$conns_id."</div>";







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



echo " <td  valign=top  >
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

	$props_notes = $mon3->query("SELECT * FROM properties WHERE id=".$_GET['propid'])->fetch_assoc();
	$property_leads_l = $mon3->query("SELECT * FROM property_leads WHERE prop_id=".$_GET['propid']);
	$mon_prop = MON_ROOT."/properties/";
	$mon_leads = MON_ROOT."/leads/";
		echo "
	
		<table>	
			<tr><td><div id=divnotes style=\"width: 430px; height: 400px; overflow: scroll; white-space: nowrap;\"> <b>Notes :</b> <br>".$props_notes['notes']."</div>
			
		</table><br>";

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
								if($i%2==0)
								{
									$gallery_images .="<tr>";
								}

								if(strtolower(pathinfo($mon_leads.$lead_id."/".$file_lead, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_leads.$lead_id."/".$file_lead, PATHINFO_EXTENSION))=="jpeg" )
								{
									$gallery_images .= "<td align=center><a href=leads/".$lead_id."/".$file_lead." title = ".$file_lead." data-link_page=leads/".$lead_id."/".$file_lead." class=link_slider target=_blank>";
									$gallery_images .= "<img src=leads/".$lead_id."/".$file_lead." height=100px alt=".$file_lead." class=img_slider > </a> ";
								}
									
								elseif(preg_match("/_pdf/", $file_lead) and (!preg_match("/contract_/", $file_lead)))
								{
									
									$file_teste = preg_replace("/_pdf/", '', $file_lead);
									$file_teste = preg_replace("/.png/", '', $file_teste);
									$file_teste = $file_teste.".pdf";
									$gallery_images .= "<td align=center> <a href=leads/".$lead_id."/".$file_lead." data-link_page=leads/".$lead_id."/".$file_teste." title = ".$file_teste.">";
									$gallery_images .= "<img src=leads/".$lead_id."/".$file_lead." height=100px class=\"img_pdf\" alt=".$file_teste.">  </a> ";
								}
								elseif (preg_match("/.pdf/", $file_lead) and (!file_exists($mon_leads.$lead_id."/".preg_replace("/.pdf/", '_pdf', $file_lead).".png")) and (!preg_match("/contract_/", $file_lead)))
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
										$gallery_images .= "<img src=".$file_pic." height=100px> </a> ";
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
							if($i%2==0)
							{
								$gallery_images .="<tr>";
							}

							if(strtolower(pathinfo($mon_prop.$propid."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_prop.$propid."/".$file1, PATHINFO_EXTENSION))=="jpeg" )
							{
								$gallery_images .= "<td align=center><a href=properties/".$propid."/".$file1." title = ".$file1." data-link_page=properties/".$propid."/".$file1." class=link_slider target=_blank>";
								$gallery_images .= "<img src=properties/".$propid."/".$file1." height=100px alt=".$file1." class=img_slider > </a> ";
							}
								
							elseif(preg_match("/_pdf/", $file1))
							{
								
								$file_teste = preg_replace("/_pdf/", '', $file1);
								$file_teste = preg_replace("/.png/", '', $file_teste);
								$file_teste = $file_teste.".pdf";
								$gallery_images .= "<td align=center> <a href=properties/".$propid."/".$file1." data-link_page=properties/".$propid."/".$file_teste." title = ".$file_teste.">";
								$gallery_images .= "<img src=properties/".$propid."/".$file1." height=100px class=\"img_pdf\" alt=".$file_teste.">  </a> ";
							}

							$i++;

						}
					}
				}				

				echo $gallery_images;



		echo "</tr></table>";



			



		
	
		
	echo "	
		
	<tr><td colspan=2><div id=divlog style=\"width: 990px; height: 200px; overflow: scroll; white-space: nowrap;\"> <b>Property log:</b> <br>";

		
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

	</td></tr>
		
		
		";
		
		
		
		
		
		
		
		
		
		
		
		
		
echo "		
		
</table>
<table>
<tr><td colspan=2><b>Add Note:</b> <input type=text name=notes id=notes></td><td><input type=button value=\"Submit Sugestion\" onclick=\"SubmitSugestion(".$propid.");\"></td></tr>
<tr>
<td><span id=submit_notes_succ></span></td>
</tr>
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

	$propid = $con['property_id'];
	
	$model_fwa = mysqli_real_escape_string($mon3, $_POST['modelo_fwa']);	

	//$modelo_fwa = mysqli_real_escape_string($mon3, $_POST['modelo_fwa']);	
	$antenna = mysqli_real_escape_string($mon3, $_POST['antenna']);	 

	


	

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

		if(strpos($meprof, '-'))
                {
                    $model_p = explode("-", $meprof);
                    $mod = $model_p[1];
                }
                else
                {
                    $mod = $meprof;
                }

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
			$gg=$mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,meprof,model) values 
			(\"$equip_id\", \"".$ont['olt_id']."\", \"".$ont['ont_id']."\", \"$meprof\", \"$meprof\") ");
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
				gpon_move_ont($olt['id'],$ont['ont_id'],$new_id,"");
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

		$mac_coax = $mon3->query("select mac from coax_modem where mac=\"$equip_id\"")->fetch_assoc();
		$cmts_old = $mac_coax['cmts'];

		$ip_old = $mac_coax['mng_ip'];

		$model_old = $mac_coax['model'];

		
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

			proplog($propid,"COAX id created on <b>".$prop['ref']."</b> <b>".$equip_id."</b> for cmts <b>".$cmts."</b> and ip <b>". $ipf."</b> and model <b>".$model."</b>");

			

			monlog("COAX id created on ".$prop['ref']." $equip_id for cmts ".$cmts." and ip". $ipf." and model ".$model);


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
					
					proplog($conc['property_id'],"deleted modem <b>$equip_id</b> from connection <b>".$conc['id']."</b> as it was assigned to property <b>".$prop['ref']."</b> ");


					monlog("deleted modem $equip_id from connection ".$conc['id']." as it was assigned to property ".$prop['ref']." ");
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

		monlog("COAX id updated on ".$prop['ref']." $equip_id for cmts ".$cmts." to $cmts and for ip". $ip_old." to ".$ipf." and model ".$model_old." to ".$model."");
		
		}




			
		echo "coax";
	
	
	
	}
	elseif($con['type']=="FWA")
	{
		//echo $con['equip_id'];

		//echo $antenna;

		$conn_exists_fwa = $mon3->query("SELECT * FROM connections WHERE equip_id ='".$equip_id."'")->num_rows;

		//echo $conn_exists_fwa;

		$mon3->query("UPDATE connections SET equip_id ='".$equip_id."' WHERE id=".$con['id']);



		

		if($equip_id != "")
		{
			if($con['equip_id'] != $equip_id)
			{
				
				proplog($propid,"Previous Equipment <b>".$con['equip_id']."</b> to <b>".$equip_id."</b> on connection number <b>".$con['id']."</b>");

                monlog("Previous Equipment ".$con['equip_id']." to ".$equip_id." on connection number ".$con['id']." ");
			}
		}

		$mon3->query("UPDATE connections SET equip_id ='".$equip_id."' WHERE id=".$con['id']);

		$select_fwa_cpe_num = $mon3->query("select * from fwa_cpe where mac=\"$equip_id\"")->num_rows;

        $model_ant = $mon3->query("SELECT * FROM fwa_cpe WHERE mac=\"$equip_id\"")->fetch_assoc();

        $m_ant = $model_ant['model'];

		if($model_ant['antenna'] == "")
        {
            $antenna_ant = 0;
        }
        else
        {
            $antenna_ant = $model_ant['antenna'];
        }

		$propery = $mon3->query("SELECT * FROM properties WHERE id=".$propid)->fetch_assoc();

		$antenna_des_ant = $mon3->query("SELECT * FROM `fwa_antennas` WHERE id=".$antenna_ant)->fetch_assoc();

                        $designacao_antenna_ant = $antenna_des_ant['name'];

                        $antenna_des = $mon3->query("SELECT * FROM `fwa_antennas` WHERE id=".$antenna)->fetch_assoc();

                        $designacao_antenna = $antenna_des['name'];

                        if($select_fwa_cpe_num > 0)
                        {
                            $update_equip_fwa_cpe_50 = $mon3->query("update fwa_cpe set model=\"$model_fwa\",antenna=\"$antenna\" where mac=\"$equip_id\"");

                            proplog($propid,"Update FWA CPE <b>".$equip_id."</b> on connection number <b>".$con['id']."</b> for antenna <b>".$designacao_antenna_ant."</b> to <b>".$designacao_antenna."</b> and model from ".$m_ant." to ".$model."");

                            monlog("FWA CPE ".$equip_id." Updated on ".$propery['ref']." for antenna ".$designacao_antenna_ant." to ".$designacao_antenna." and model from ".$m_ant." to ".$model."");
                        }
                        else
                        {
                            $insert_fwa_equip = $mon3->query("insert into fwa_cpe (mac,model,antenna) VALUES (
                                \"$equip_id\",
                                \"$model_fwa\",
                                \"$antenna\"			
                                ) ");
                                

                            proplog($propid,"Insert FWA CPE <b>".$equip_id."</b> antenna <b>".$designacao_antenna."</b> and model <b>".$model."</b> on connection number <b>".$con['id']."</b>");

                            monlog("FWA CPE Inserted on ".$propery['ref']." equipment ".$equip_id." antenna ".$designacao_antenna." and model ".$model."");
                        }

		
		/*$fwaexists=$mon3->query("select mac from fwa_cpe where mac=\"$equip_id\"")->num_rows;

		$fwa_model = $mon3->query("select * from fwa_cpe where mac=\"$equip_id\"")->fetch_assoc();

		$model_fwa_old = $fwa_model['model'];

		$antenna_old = $fwa_model['antenna'];

		if($fwaexists>0)
		{
			$update_equip_fwa_cpe_50 = $mon3->query("update fwa_cpe set model=\"$model_fwa\",antenna=\"$antenna\" where mac=\"$equip_id\"");

			proplog($propid,"update fwa cpe $equip_id , antenna ".$antenna." model $model_fwa");

			monlog("FWA id updated on ".$prop['ref']." $equip_id for model ".$model_fwa_old." to $model_fwa and for antenna". $antenna_old." to ".$antenna."");

		}
		else
		{
			$insert_equip_fwa_cpe_50 = $mon3->query("insert into fwa_cpe (mac,model,antenna) VALUES (
				\"$equip_id\",
				\"$model_fwa\",
				\"$antenna\"			
				) ");

			proplog($propid,"insert fwa cpe $equip_id , antenna ".$antenna." model $model_fwa");

			monlog("FWA inserted on ".$prop['ref']." model ".$model_fwa.", antenna". $antenna.", equipment".$equip_id."");
		}*/

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
<tr><td>PON: <td> <select name=pon style=\"width: 180px;\">";
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
<tr><td>Model:<td><select name=meprof style=\"width: 150px; \">
<option value=zhone-2428";if($ont['meprof']=="zhone-2428") echo " selected"; echo">zhone-2428</option>
<option value=zhone-2427";if($ont['meprof']=="zhone-2427") echo " selected"; echo">zhone-2427</option>
<option value=zhone-2727a";if($ont['meprof']=="zhone-2727a") echo " selected"; echo">zhone-2727a</option>
<option value=zhone-2301";if($ont['meprof']=="zhone-2301") echo " selected"; echo">zhone-2301</option>
</select>



";
}
elseif($con['type']=="COAX")
{


	$modem=$mon3->query("select * from coax_modem where mac=\"".$con['equip_id']."\";")->fetch_assoc();	
	echo "
	<tr><td>Modem type<td> <select name=model style=\"width: 180px;\"><option value=>no modem / tv only</option>
	<option value=cve ";if($modem['model']=="cve") echo " selected"; echo">hitron cve</option>
	<option value=cva ";if($modem['model']=="cva") echo " selected"; echo">hitron cva</option></select>
	
	<tr><td>Modem MAC:<td><input type=text name=equip_id value=".$modem['mac'].">format: aabbccddeeff
	<tr><td>CMTS:<td><select name=cmts_id style=\"width: 180px;\">
	<option value=1 ";if($modem['cmts']=="1") echo " selected"; echo">CMTS1_QDL</option>
	<option value=2 ";if($modem['cmts']=="2") echo " selected"; echo">CMTS2_QDL</option>
	<option value=3 ";if($modem['cmts']=="3") echo " selected"; echo">CMTS_BAL</option></select>
	<tr><td><br>
	
	";





}
elseif($con['type']=="FWA")
{
	echo "<tr><td>FWA CPE:<td> <input type=text name=equip_id value=\"".$con['equip_id']."\" size=50> <br>";

	$fwa_cpe=$mon3->query("select * from fwa_cpe where mac=\"".$con['equip_id']."\";")->fetch_assoc();	

	echo "
	<tr><td>Model:<td><select name=modelo_fwa id=modelo_fwa style=\"width: 180px;\">
	<option value=ltulite";if($fwa_cpe['model']=="ltulite") echo " selected"; echo">LTU lite</option>
	<option value=ltulr";if($fwa_cpe['model']=="ltulr") echo " selected"; echo">LTU LR</option>
	<option value=ltupro";if($fwa_cpe['model']=="ltupro") echo " selected"; echo">LTU pro</option>
	</select>";


	echo "<tr><td>FWA antenna<td><select name=antenna id=antenna style=\"width: 180px;\"> ";
		$antennas=$mon3->query("select * from fwa_antennas");
		while($antenna=$antennas->fetch_assoc()){ echo "<option value=".$antenna['id'];
		if($antenna['id']==$fwa_cpe['antenna']) echo " selected ";
		echo ">".$antenna['name']."</option>";}


	echo "</select>";



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

	$antenna=mysqli_real_escape_string($mon3, $_POST['antenna']);

	echo "$equip_id $date_start $pon $olt_id $model $type $cmts"; 
	if($type=="GPON")
	{
		$fsanexists=$mon3->query("select fsan,ont_id,olt_id from ftth_ont where fsan=\"$equip_id\" ")->num_rows;
		echo mysqli_error($mon3);

				if(strpos($model, '-'))
                {
                    $model_p = explode("-", $model);
                    $mod = $model_p[1];
                }
                else
                {
                    $mod = $model;
                }
	
		if($fsanexists==0) //se o ont nao esta na dB
		{
			echo "$equip_id is not in dB, adding ont...<br>";
			$ont_id="1-".$pon."-".nextont($olt_id,$pon);
			echo "ont_id - ".$ont_id;
			$q=$mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,meprof,model) values (
			\"$equip_id\",
			$olt_id,
			\"$ont_id\",
			\"$model\",
			\"$model\"
			)");
			echo mysqli_error($mon3);	

			proplog($propid,"Insert ONT equipment $equip_id on type $type");


			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\"
			)");
			echo mysqli_error($mon3);
			echo "update notes on prop";	
			proplog($propid,"connection $type added with equipment $equip_id");	


			monlog("Insert ONT equipment $equip_id on type $type");

			monlog("connection $type added with equipment $equip_id");	


			//gpon_register_ont($equip_id);
				
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
			model=\"$model\",
			meprof=\"$model\"			
			where fsan=\"$equip_id\"
			");

			proplog($propid,"Update ONT equipment $equip_id on type $type");

			echo mysqli_error($mon3);	
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	

			gpon_change_ont($olt_id,$ont_id,$equip_id,$model);



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

			proplog($propid,"Insert coax equipment $equip_id on type $type");	

			echo mysqli_error($mon3);	
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values( 
			$propid,
			\"$type\",
			\"$equip_id\",
			\"$date_start\"
			)");
			echo mysqli_error($mon3);	

			proplog($propid,"connection $type added with equipment $equip_id ");

			monlog("Insert coax equipment $equip_id on type $type");	

			monlog("connection $type added with equipment $equip_id ");	
	}
	
	elseif($type=="ETH"||$type=="ETHF")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start,linked_prop) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\",
			\"$linked_prop\"
			)");

			

			$q=$mon3->query("insert into ftth_eth (mac,model) values( 
			\"$equip_id\",
			\"$model\"
			)");

			proplog($propid,"Insert ETH equipment $equip_id on type $type");
			
			
			
			proplog($propid,"connection $type added with equipment $equip_id ");	

			monlog("Insert ETH equipment $equip_id on type $type");
			
			monlog("connection $type added with equipment $equip_id ");	

	
	
	}
	elseif($type=="DARKF")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
			\"$propid\",
			\"$type\",
			\"\",
			\"$date_start\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	
	}
	elseif($type=="WIFI")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	

			monlog("connection $type added with equipment $equip_id ");	
	}
	elseif($type=="OTT")
	{
			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
			\"$propid\",
			\"$type\",
			\"$equip_id\",
			\"$date_start\"
			)");

			proplog($propid,"connection $type added with equipment $equip_id ");	

			monlog("connection $type added with equipment $equip_id ");	
	}
	elseif($type=="FWA")
	{
		$fsanexists=$mon3->query("select * from fwa_cpe where mac=\"$equip_id\" ")->num_rows;
		if($fsanexists > 0) //se o fwa cpe nao esta na dB
		{
			$update_fwa =  $mon3->query("update fwa_cpe set model=\"$model\",antenna=\"$fwa_antenna\" where mac=\"$equip_id\"");

			proplog($propid,"Update FWA equipment $equip_id on type $type");

			

			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
				\"$propid\",
				\"$type\",
				\"$equip_id\",
				\"$date_start\"
				)");

			proplog($propid,"connection $type added with equipment $equip_id ");	

			monlog("Update FWA equipment $equip_id on type $type");
		}
		else
		{
			$insert_fwa_equip = $mon3->query("insert into fwa_cpe (mac,model,antenna) VALUES (
				\"$equip_id\",
				\"$model\",
				\"$antenna\"			
				) ");
			
			proplog($propid,"Insert FWA equipment $equip_id on type $type");

			

			$q=$mon3->query("insert into connections (property_id,type,equip_id,date_start) values (
					\"$propid\",
					\"$type\",
					\"$equip_id\",
					\"$date_start\"
					)");	


			proplog($propid,"connection $type added with equipment $equip_id");

			monlog("Insert FWA equipment $equip_id on type $type");
		}


		monlog("connection $type added with equipment $equip_id");

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

		textdiv += '<tr><td>ONT FSAN<td><input type=text name=equip_id size=10 value=ZNTS><br> '+	
		'<tr><td>CPE model<td><select name=model style=\"width: 180px\"><option selected value=zhone-2427>zhone-2427</option><option value=zhone-2727a>zhone-2727a</option><option value=zhone-2428>zhone-2428</option><option value=zhone-2301>zhone-2301</option></select>'+
		'<tr><td>OLT<td><select name=olt_id style=\"width: 180px\" onchange=\"updatepon(this.options[this.selectedIndex].value); updatevlan(this.options[this.selectedIndex].value)\"> ";
		
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
		textdiv += '<tr><td>Modem MAC<td><input type=text name=equip_id size=17 value= >aabbccddeeff'+	
		'<tr><td>CPE model<td><select name=model style=\"width: 180px\"><option selected value=cve>hitron cve</option><option value=cva>hitron cva</option><option value=k310i>kathrein 310i </option><option value=k10>kathrein router</option></select>'+	
		'<tr><td>CMTS<td><select name=cmts_id style=\"width: 180px\">";
		
		$cmtss=$mon3->query("select * from coax_cmts"); 
		while($cmts=$cmtss->fetch_assoc()){ echo "<option value=".$cmts['id'].">".$cmts['name']."</option>";} 
		
		
		echo"</select>'+ 
		'';	
		
	}
	
	else if((soption=='ETH'))
	{
		textdiv += '<tr><td>ETH MAC<td><input type=text name=equip_id size=17 value= > ex: aabbccddeeff	'+	
		'<tr><td>CPE model<td><select name=model style=\"width: 180px\"><option selected value=tplink>tplinkrt</option><option value=zyxelrt>zyxelrt</option><option value=comega>comega</option><option value=switch>switch</option><option value=sfp>sfp</option></select><tr><td>linked prop<td><select name=linked_prop style=\"width: 150px;\"><option value=>not linked</option>";
		
		$propsv=$mon3->query("select properties.ref,connections.id,properties.address,connections.type from properties left join connections on properties.id=connections.property_id where connections.date_end=\"0000-00-00\" order by properties.ref");
		while($propv=$propsv->fetch_assoc())
		{
			
			echo "<option value=".$propv['id'].">".$propv['ref']." ".$propv['type']." ".escape4js($propv['address'])."</option>";
		}


		
		
		
		
		echo "</select>	';	
	}

	else if((soption=='FWA'))
	{
		textdiv += '<tr><td>FWA CPE<td><input type=text name=equip_id size=20> ex: E063DA0F0DD6 <br>' +
		'<tr><td>CPE model<td><select name=model id=models style=\"width: 180px\"><option value=ltulite>LTU lite</option><option value=ltulr>LTU LR</option> <option value=ltupro>LTU pro</option></select>";

		echo "<tr><td>FWA antenna<td><select name=antenna id=antenna style=\"width: 180px\"> ";
		$antenna = 1;
		$antennas=$mon3->query("select * from fwa_antennas");
		while($antenna=$antennas->fetch_assoc()){ echo "<option value=".$antenna['id'];
		if($antenna['id']==$antenna) echo " selected ";
		echo ">".$antenna['name']."</option>";}


		echo "</select>';
	}


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	textdiv += '</table>';
	document.getElementById('additionalset').innerHTML = textdiv;

	$('select').select2();
};


</script>
";

echo "

<form name=editcon action=?conadd=$propid&props=1 method=post>
<table>
<tr><td>date start:<td> <input type=text name=date_start value=\"".date("Y-m-d")."\" size=10> YYYY-MM-DD<br>
<tr><td>connection type<td><select id=conformsel name=type onchange=\"updateconform()\" style=\"width: 180px;\">
<option  selected value= >please select</option>
<option value=GPON>GPON</option>
<option value=COAX>COAX</option>
<option value=FWA>FWA</option>
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





// ADD property

/*elseif($_GET['new_prop']>0)
{
    ?>
        ADD Property<br><br><br>
    <?php

    if($_POST['addpropsibmit'])
    {
		$ref_area = mysqli_real_escape_string($mon3, $_POST['ref_area']);

		//echo "SELECT RIGHT(ref, 3) as 'ref_last' FROM `properties` where ref LIKE '".$ref_area."%' AND id = (SELECT MAX(id) FROM properties WHERE ref LIKE '".$ref_area."%')";

		$ref_prop_last = $mon3->query("SELECT RIGHT(ref, 3) as 'ref_last' FROM `properties` where ref LIKE '".$ref_area."%' AND id = (SELECT MAX(id) FROM properties WHERE ref LIKE '".$ref_area."%')")->fetch_assoc();

		if($ref_prop_last != null)
		{
			$refa = $ref_prop_last['ref_last'] + 1;
			$a = '';

			if(strlen($refa) == 2)
			{
				$a = "0".$refa;
			}

			if(strlen($refa) == 1)
			{
				$a = "00".$refa;
			}

			if(strlen($refa) == 3)
			{
				$a = $refa;
			}


			$ref = $ref_area.$a;
		}
		else
		{
			$ref=$ref_area."001";
		}
		
		
        //$ref=mysqli_real_escape_string($mon3, $_POST['ref']);
        $address=mysqli_real_escape_string($mon3, $_POST['address']);
        $freg=mysqli_real_escape_string($mon3, $_POST['freg']);
        $coords=trim(mysqli_real_escape_string($mon3, $_POST['coords']), '()');
        $owner=mysqli_real_escape_string($mon3, $_POST['owner']);
        //$owner_ref=mysqli_real_escape_string($mon3, $_POST['owner_ref']);
        $manage=mysqli_real_escape_string($mon3, $_POST['manage']);
        $notes=mysqli_real_escape_string($mon3, $_POST['notes']);

        $date_now = date("Y-m-d");

		if($notes != "")
		{
			$notes_a = date("Y-m-d")." -> ".$notes."<br>\r\n";
		}
		

        $gg=$mon3->query('INSERT INTO properties (ref, address, freguesia, coords, 
                        owner_id, owner_ref, management, 
                        notes, date, homes_count) VALUES ("'.$ref.'", "'.$address.'", "'.$freg.'", "'.$coords.'",
                        "'.$owner.'", NULL, "'.$manage.'", "'.$notes_a.'", "'.$date_now.'", 1);');
        echo mysqli_error($mon3);
        //save
        echo "<br><font color=green>saved</font><br>";
    }

    ?>

    <table>
        <tr>

            <form name=new_prop action=?new_prop=1&props=1 method=post>

		<tr>
			<td>Ref (Area Code):<td>
			     <select name=ref_area id=ref_area style='width: 500px;'>
				 	<option value="">Select a Ref Property</option>	
					   <?php
                          $refs=$mon3->query("select areacode,description from area_codes order by areacode"); 
						  while($ref=$refs->fetch_assoc())
						  { 
							echo "<option value=".$ref['areacode'].">".$ref['areacode']." - ".$ref['description']."</option>";
						  }
					   ?>
				 </select>	
        <tr>
            <td>Address:<td>
                <input type=text name=address id=address size=50> <br>

        <tr>
                <td>Concelho:<td>
                <select name=concelho id=concelho onchange="updatefregep(this.options[this.selectedIndex].value)">
				   <option value="">Select Concelho</option>	
                    <?php
                    $concs=$mon3->query("select * from concelhos order by pais,distrito,concelho;");

                    while($conca=$concs->fetch_assoc())
                    {
                        ?>
                        <option value=<?php echo $conca['id'] ?>>
                            <?php echo $conca['distrito']." - ".$conca['concelho']; ?>
                        </option>
                        <?php
                    }

                    ?>
                </select>

        <tr>
                <td>Freguesia:<td>
                <select name=freg id=freg>
				<option value="">Select Freguesia</option>
                <?php
                $fregs=$mon3->query('select * from freguesias;');
                while($frega=$fregs->fetch_assoc())
                {
                    ?>
                        <option value=<?php echo $frega['id'] ?>>
                        <?php echo $frega['freguesia']; ?>
                        </option>

                    <?php

                }

                ?>
                </select>


                <tr>
                    <td>Country:
                    <td>
                        <select name=country class="country" onchange=updateconcelhosep(this.options[this.selectedIndex].value)>
							<option value="">Select Country</option>
                            <option value=PORTUGAL selected>Portugal</option>
                            <option value=Espanha>Spain</option>
                            <option value="UNITED KINGDOM">United Kingdom</option>
                        </select>

						<tr><td>coords: <td><input type=text name=coords  id=coord size=40>
                       <a href=# onclick=gpslink()>GPS</a> 
                       <tr><td><br>

					   <tr><td>Subscriber:<td>
                       <select name=owner id=idowner style='width: 500px;'> 
					   <option value="">Select Owner</option>	
					   <?php
                           $owners=$mon3->query("select id,name,email,fiscal_nr from customers order by name ");
						   while($owns=$owners->fetch_assoc())
						   {
							   echo "<option value=\"".$owns['id']."\"";
							   echo ">".$owns['id']."-".$owns['name']." #".$owns['fiscal_nr']."</option>";
							   
						   }
					   ?>
					   </select>

					   
					   <br>

                       <tr><td>Management comp:<td>
                       <select name=manage id=idowner style='width: 500px;'>
					   <option value=0>no external management</option>
					   <?php
                          $owners=$mon3->query("select id,name,email,fiscal_nr from customers where is_management=1 order by name ");
						  while($owns=$owners->fetch_assoc())
						  {
							  echo "<option value=\"".$owns['id']."\"";
							  echo "> ".$owns['name']." #".$owns['fiscal_nr']."</option>";
							  
						  }
					   ?>
					   </select>

					   <tr><td> <br>

                       <tr><td>Notes <td> <textarea cols=65 rows=10 name=notes></textarea>
                       <tr><td><input type=submit name=addpropsibmit value=save><br>
                       </form>




    <?php



}*/













// EDIT porperty

elseif($_GET['editprop']>0)
{

$propid=mysqli_real_escape_string($mon3, $_GET['editprop']);
$prop_i = $mon3->query("SELECT * FROM properties WHERE id=".$propid)->fetch_assoc();
	
if($_POST['propeditsubm'])
{
	$notas_temp = '';
	$address=mysqli_real_escape_string($mon3, $_POST['address']);
	$freg=mysqli_real_escape_string($mon3, $_POST['freg']);
	$coords=trim(mysqli_real_escape_string($mon3, $_POST['coords']), '()');
	$owner=mysqli_real_escape_string($mon3, $_POST['owner']);	
	$manage=mysqli_real_escape_string($mon3, $_POST['manage']);
	$notes=mysqli_real_escape_string($mon3, $_POST['notes']);

	if($manage == "")
	{
		$manage = 0;
	}
	//echo $propid;

	if($prop_i['notes'] != "" || $prop_i['notes'] != null)
	{
		$lines = explode("\n", trim($prop_i['notes']));
		//$lines = nl2br($prop['notes']);
		$last_line = $lines[count($lines)-1];
		$props_notes_saved = "";
		if (strpos($last_line, "<br>") !== false) 
		{
			$props_notes_saved .= $prop_i['notes'];
		}
		else
		{
			$props_notes_saved .= $prop_i['notes']."<br>\r\n";
		}
		$notas_temp = $props_notes_saved;


		$b = $props_notes_saved;
		//$a = $notes;

		//$order   = array("\r\n", "\n", "\r");
		//$replace = '<br />';
		//$a = str_replace($order, $replace, $notes);
		

		$a = $_POST['notes'];

		//echo $b."\n".$a;

		$newstr = substr($a, (strrpos($a, $b) + strlen($b)));
		$s = date("Y-m-d")." -> ".$newstr."<br>\r\n";


		/*if(strpos($b, $a) !== false) {
			$newstr = substr($b, (strrpos($b, $a) + strlen($a)));
			echo $newstr;
			$s = date("Y-m-d h:i:s")." -> ".$newstr."<br>\n";
		}*/

		$total = $notas_temp ."". $s;


		
	}
	else if($prop_i['notes'] == "" || $prop_i['notes'] == null)
	{
		$notas_temp = date("Y-m-d")." -> ".$_POST['notes']."<br>\r\n";

		$total = $notas_temp;
	}


	

	/*
	desc_valor = $("#descricao_sugestao_edit").val().replace(temp, '');
        var s = $("#user_name").val() + " - " + desc_valor;
        total = temp + " " + s + "\n";
	*/
	


	if($propid>0)
	{ 

//		echo $address.$freg.$coords." o ".$owner." m ".$manage."kkkk<br>";
		$gg=$mon3->query("update properties set address=\"".$address."\", 
		freguesia=\"".$freg."\", 
		coords=\"".$coords."\", 
		owner_id=\"".$owner."\", 
		management=\"".$manage."\",
		notes=\"$total\"
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
<tr><td>Adress:<td> <input type=text name=address value=\"".$prop['address']."\" id=address size=50> <br>";

echo "<tr><td>Concelho:<td><select name=concelho id=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\">";

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
</select>";

echo "<tr><td>Freguesia:<td><select name=freg id=freg>";
$fregs=$mon3->query("select * from freguesias where concelho=\"".$freg['concelho']."\";");

while($frega=$fregs->fetch_assoc())
{
	echo "<option value=".$frega['id'];
	if ($frega['id']==$prop['freguesia'])
		echo " selected";
	echo ">".$frega['freguesia']."</option>";
	
}


echo"
</select>";




echo "<tr><td>Country:
<td><select name=country class=\"country\"onchange=updateconcelhosep(this.options[this.selectedIndex].value)>
<option value=PORTUGAL selected>Portugal</option>
<option value=Espanha>Spain</option>
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
";

if($prop['notes'] != "" || $prop['notes'] != null)
{
	$lines = explode("\n", trim($prop['notes']));
	//$lines = nl2br($prop['notes']);
	$last_line = $lines[count($lines)-1];
	$props_notes = "";
	if (strpos($last_line, "<br>") !== false) 
	{
		$props_notes .= $prop['notes'];
	}
	else
	{
		$props_notes .= $prop['notes']."<br>\r\n";
		//$props_notes .= trim(preg_replace('/\s\s+/', ' ', $prop['notes']));

		/*for($i=0;$i<count($lines)-1;$i++)
		{
			echo $lines[$i];

			
		}*/
	}
}


echo "
<tr><td> <br>

<tr><td>Notes <td> <textarea cols=65 rows=10 name=notes>".$props_notes."</textarea>
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
		//echo " value=\"$searchb\"";
		$qwhere= " where address LIKE '%".$searchb."%' or ref LIKE '%".$searchb."%'";
	
	}	
	echo" 
	

	
	
	
	<div id=mapl>
	
	</div>
	
	 <script>
function initMap() {

  var uluru = {lat: 37.0642249, lng:-8.1128986};
  var map = new google.maps.Map(
      document.getElementById('mapl'), {zoom: 12, center: uluru, mapTypeId: 'hybrid',gestureHandling: 'greedy'});

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


//$dump_prop="dump_prop=1&searchb=".$_GET['serachb'];

//$mon_services = SERVER_WEB."webservices.php";


echo "<br><a href=# id=click_dump_props>dump props</a>";
?>
<script>
$("#click_dump_props").on('click', function()
{
	//console.log("webservice.php?dump_prop=1&searchb="+$("input[name=searchb]").val());
	location.href = "webservice.php?dump_prop=1&searchb="+$("input[name=searchb]").val();
});
</script>	
<?php
}





















