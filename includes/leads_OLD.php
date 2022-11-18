<?php
$mon_leads = PROJECT_WEB."/leads/";
$planing_email="daniel.jesus@lazerspeed.com;mihai.petrovici@lazerspeed.com";
$intproc_email="info@lazerspeed.com;carina.ludgero@lazerspeed.com";
$installs_email="howard.lopes@lazerspeed.com;jorge.guerreiro@lazerspeed.com;catarina.borges@lazerspeed.com;daniel.jesus@lazerspeed.com";
$salesm_email="";
$welcomem_email="marketing@lazerspeed.com;customer.support@lazerspeed.com;des.wynne@lazerspeed.com;catarina.borges@lazerspeed.com;carlos.rosario@lazerspeed.com;daniel.jesus@lazerspeed.com";


include PROJECT_WEB."/functions.php";
?>
    <a href=?propleads=1&list_leads=1><img src=img/leads.png></a>
    <a href=?propleads=1&propleadsadd=1><img src=img/leadadd.png></a>
    <a href=?propleads=1&covmaps=1&fats=on&poly=on&cables=on&leads=on&customers=on><img src=img/maps.png></a>
    <a href=?props=1><img src=img/house.png></a>
    <a href=?custs=1&list_custs=1><img src=img/user.png></a>
    <h3>Property Leads  - If its not in leads, it will not happen</h3><br>



<?php







if($_GET['covmaps']==1){



	/*

$coverages = json_decode(file_get_contents("$_SERVER["DOCUMENT_ROOT"]."/mon/grupos.geojson"), true);
print_r($coverages);

	*/


include $_SERVER["DOCUMENT_ROOT"]."/mon/includes/covmaps.php";

}










elseif($_GET['welcomeemail']!=0 && $_GET['lead_id']!=0)
{
$lead_id=mysqli_real_escape_string($mon3, $_GET['lead_id']);

include "welcomeemail/welcome.php";
echo $welcomeemail;

}


elseif($_GET['status30']!=0 && $_GET['lead_id']!=0)
{
$lead_id=mysqli_real_escape_string($mon3, $_GET['lead_id']);

include "welcomeemail/status30.php";
echo $status30;

}

elseif($_GET['status40']!=0 && $_GET['lead_id']!=0)
{
$lead_id=mysqli_real_escape_string($mon3, $_GET['lead_id']);

include "welcomeemail/status40.php";
echo $status40;

}

///////////// show Property lead ///////////////////////
elseif($_GET['lead_id']!=0)
{
	$lead_id=mysqli_real_escape_string($mon3, $_GET['lead_id']);
	echo" 
	<b>Lead id</b>: $lead_id" ;





$prop=$mon3->query("select * from property_leads where id=$lead_id;")->fetch_assoc();
$agent=$mon3->query("select id,name from customers where id=\"".$prop['agent_id']."\" ")->fetch_assoc();
$created_by=$mon3->query("select name,email from users where username=\"".$prop['created_by']."\" ")->fetch_assoc();

echo "<br> created by: ".$prop['created_by']."<br> <div id=warning_services></div>";
// Editar as Informações das Leads (Informação da Lead) - Na base de dados - property_leads
if($prop['created_by']==$localuser['username'])
{echo "<a href=?propleads=1&propleadsedit=$lead_id><img src=img/leadedit.png></a><br>";}
echo "<br>";






if($_POST['supdatelead']!="")  //lead update
{

	$status=mysqli_real_escape_string($mon3, $_POST['status']);
    //$status=50;
    //echo $status;
	$notes=escapechars($_POST['notes']);
	$notesa=$prop['notes'];


	if($notes!="")
	{
		$notesa=date("Y-m-d H:i:s")." ".$_SERVER['PHP_AUTH_USER']." added: ".$notes."<br>".$prop['notes'];

		$mon3->query("update property_leads set notes=\"$notesa\",date_modified=\"".date("Y-m-d")."\" where id=$lead_id;");

		$dest_email=$created_by['email'];
		$assunto="Lazer - Lead $lead_id (".$prop['address'].") new notes";
		$corpo= "
		<html> Dear ".$created_by['name']."<br><br>
		Your lead <a href=https://mon.lazertelecom.com/?propleads=1&lead_id=$lead_id>$lead_id</a> has new notes.<br><br>
		Current status: $status <br><br> 
		
		Notes:<br>".$notesa
		."
		<br><br>Regards,<br>The System</html>";
		//enviamail($dest_email,$assunto, $corpo);

	}
	if($status!="")
	//if($status!=$prop['status'] && $status!="")
	{
		$notesa=date("Y-m-d H:i:s")." ".$_SERVER['PHP_AUTH_USER']." moved to status ".$status."<br>".$notesa;


		$mon3->query("update property_leads set status=\"$status\",date_modified=\"".date("Y-m-d")."\",notes=\"$notesa\" where id=$lead_id;");
		echo $mon3->error;


		$dest_email=$created_by['email'];


		if($status==0||$status==6||$status==13)
			$dest_email.=";".$planing_email;
		if($status>=20 && $status<30)
			$dest_email.=";".$intproc_email;
		if($status>=30 && $status<=33)
			$dest_email.=";".$planing_email.";".$installs_email.";".$salesm_email;
		if($status>=40 && $status<=43)
			$dest_email.=";".$installs_email;
		if($status==50)
			$dest_email.=";".$installs_email.";".$salesm_email;
		if($status==60)
			$dest_email.=";".$installs_email.";".$intproc_email;

		$assunto="Lazer - Lead $lead_id (".$prop['address'].") updated";
		$corpo= "
		<html> Dear ".$created_by['name']."<br><br>
		Your lead <a href=https://mon.lazertelecom.com/?propleads=1&lead_id=$lead_id>$lead_id</a> was updated.<br><br>
		Current status: $status <br><br> 
		
		<br><br>Regards,<br>The System</html>";
		/*enviamail($dest_email,$assunto, $corpo);*/



		//colocar datas consoante estados

		// status de viabilidades/
		if(($status>0 && $status<4)||$status==14)
		{
            $drop_length=mysqli_real_escape_string($mon3, $_POST['drop_length']);
            $con_type=mysqli_real_escape_string($mon3, $_POST['con_type']);
            $model=mysqli_real_escape_string($mon3, $_POST['model']);

            $ORAC_pits=mysqli_real_escape_string($mon3, $_POST['ORAC_pits']);
            $ORAP_poles=mysqli_real_escape_string($mon3, $_POST['ORAP_poles']);
            $connection_cost=mysqli_real_escape_string($mon3, $_POST['connection_cost']);
            $network_cost=mysqli_real_escape_string($mon3, $_POST['network_cost']);
            if(mysqli_real_escape_string($mon3, $_POST['is_network_ready'])!="")
                $is_network_ready=1;
            else
                $is_network_ready=0;
            $estimated_quote=mysqli_real_escape_string($mon3, $_POST['estimated_quote']);
            $timeframe=mysqli_real_escape_string($mon3, $_POST['timeframe']);
            $quoted=mysqli_real_escape_string($mon3, $_POST['quoted']);
            uploadfile("plan",$mon_leads.$lead_id."/", "plan_".time().".kmz",0);
            uploadfile("planz",$mon_leads.$lead_id."/", "planz_".time().".zip",0);

			$mon3->query("update property_leads set date_viability=\"".date("Y-m-d")."\",
	drop_length=\"$drop_length\",
	con_type=\"$con_type\",	
	model=\"$model\",	
	ORAC_pits=\"$ORAC_pits\",
	ORAP_poles=\"$ORAP_poles\",
	connection_cost=\"$connection_cost\",
	network_cost=\"$network_cost\",
	is_network_ready=\"$is_network_ready\",
	estimated_quote=\"$estimated_quote\",
	timeframe=\"$timeframe\",
	quoted=\"$quoted\"
			where id=$lead_id;");
		}

        // RECONNECTION - STATUS = 7

        if($status==7)
		{
            // Actualizar a PROP_ID da lead

            //echo $_POST['rec_assoc'];

            if($_POST['rec_assoc'] == 1)
            {
                $refe_rec_7 = mysqli_real_escape_string($mon3, $_POST['refe_rec_7']);
                $con_type_rec = mysqli_real_escape_string($mon3, $_POST['con_type_rec']);
                $is_rec = 1;
                $is_change_over = 0;







                $update_lead_status_7 = $mon3->query("update property_leads set con_type=\"$con_type_rec\",	
                prop_id=\"$refe_rec_7\",	
                is_changeover=\"$is_change_over\",
                is_reconnection=\"$is_rec\"
			    where id=$lead_id;");

                if($update_lead_status_7)
                  {
                    echo "<font color=green>Saved</font>";
                  }
                  else
                  {
                    echo "<font color=red>Error Code</font>";
                  }


                  // TYPE CONNECTION = FWA, GPON


                // TYPE CONNECTION = DIA, DARKF, COAX
                if($con_type_rec == "DIA" || $con_type_rec == "DARKF" || $con_type_rec == "COAX")
                    {
                        $mon3->query("update property_leads set status=\"0\",date_modified=\"".date("Y-m-d")."\",notes=\"$notesa\", is_reconnection=\"0\" where id=$lead_id;");

                    }
            }
            else if($_POST['rec_assoc'] == 0)
            {
                echo "<font color=red>Cannot associate the property to make a reconnection. Please choose a property which has a connection</font>";
            }



		}


		if($status==20 && $prop['date_accept']=="")
		{

				$mon3->query("update property_leads set date_accept=\"".date("Y-m-d")."\" where id=$lead_id;");

		}



		if($status==30 )
		{
            // formulario de submissao de contratos



			    $error='';
                $succ = '';
                if( $prop['date_papwk']=="")
                {
                    $mon3->query("update property_leads set date_papwk=\"".date("Y-m-d")."\" where id=$lead_id;");
                }
			    $address=mysqli_real_escape_string($mon3, $_POST['address']);
			    $freg=mysqli_real_escape_string($mon3, $_POST['freg']);
			    $ref=mysqli_real_escape_string($mon3, $_POST['ref']);
			    $refe=mysqli_real_escape_string($mon3, $_POST['refe']);
			    $owner_id=mysqli_real_escape_string($mon3, $_POST['owner_id']);
			    $mng_id=mysqli_real_escape_string($mon3, $_POST['mng_id']);

                if($_POST['con_type'] != "")
                {
                    $con_type=mysqli_real_escape_string($mon3, $_POST['con_type']);
                }
                else
                {
                    $con_type = $prop['con_type'];
                }

		        $model=mysqli_real_escape_string($mon3, $_POST['model']);


                $olt=$mon3->query("select olt_id from area_codes where areacode=\"$ref\" ")->fetch_assoc();
                $olt=$olt['olt_id'];
                $contract_id=mysqli_real_escape_string($mon3, $_POST['contract_id']);
                $tv=mysqli_real_escape_string($mon3, $_POST['tv']);
                $internet_prof=mysqli_real_escape_string($mon3, $_POST['internet_prof']);
                $fixed_ip=mysqli_real_escape_string($mon3, $_POST['fixed_ip']);
                $phone1=mysqli_real_escape_string($mon3, $_POST['phone1']);
                $phone2=mysqli_real_escape_string($mon3, $_POST['phone2']);
                $aps=mysqli_real_escape_string($mon3, $_POST['aps']);

                $monthly_price=mysqli_real_escape_string($mon3, $_POST['monthly_price']);

                $prev_rev_month=mysqli_real_escape_string($mon3, $_POST['prev_rev_month']);

                // IS RECONNECTION

                if($prop['is_reconnection'] == 1 && $prop['is_changeover'] == 0)
                {
                    $rec = $prop['is_reconnection'] ? "Yes" : "No";
                    echo "reconnection: ".$rec."<br>";

                    // submissao de formularios da reconnection

                        if($_POST['refe_rec'] != null && $_POST['owner_rec'] != null)
                            {
                                $refe_rec = $_POST['refe_rec'];
                                $lead_sub = $_POST['owner_rec'];

                                $is_change_over = $prop['is_changeover'];
                                $is_rec = $prop['is_reconnection'];
                                $update_lead_status_30_rec = $mon3->query("update property_leads set	
                                prop_id=\"$refe_rec\",	
                                is_changeover=\"$is_change_over\",
                                is_reconnection=\"$is_rec\",
                                lead_sub=\"$lead_sub\"
                                where id=$lead_id;");

                                  if($update_lead_status_30_rec)
                                  {
                                    $succ.= "<font color=green>Saved on form 'Property Reconnection'</font><br>";
                                  }
                                  else
                                  {
                                    $error.= "<font color=red>Error Code on form 'Property Reconnection'</font><br>";
                                  }

                                  $propid = $refe_rec;

                                  // TYPE CONNECTION = DIA, DARKF, COAX - PASSA PARA O ESTADO 0
                                if($con_type == "DIA" || $con_type == "DARKF" || $con_type == "COAX")
                                    {
                                        $mon3->query("update property_leads set status=\"0\",date_modified=\"".date("Y-m-d")."\",notes=\"$notesa\", is_reconnection=\"0\" where id=$lead_id;");

                                    }
                            }
                            else if($_POST['refe_rec'] == null && $_POST['owner_rec'] == null)
                            {
                                $error.= "<font color=red>Select a Property to make a reconnection *</font><br><font color=red>Select a Owner to associate a Property to make a reconnection *</font><br>";
                                $propid = "";
                            }


                }

                // IS CHANGE OVER

                else if($prop['is_reconnection'] == 0 && $prop['is_changeover'] == 1)
                {
                    $chg = $prop['is_changeover'] ? "Yes" : "No";
                    echo "changeover: ".$chg;
                }

                // NAO TEM CONEXAO (NEM CHANGE OVER E NEM RECONNECTION)

                else if($prop['is_reconnection'] == 0 && $prop['is_changeover'] == 0)
                {
                    echo "changeover:?".$_POST['is_changeover'];
                    if($_POST['is_changeover'] == 0 || $_POST['is_changeover'] == "")
                    {
                        if($prop['prop_id']==""||$prop['prop_id']==0)//se ainda nao tiver criado prop
                        {
                            $lastref=$mon3->query("select ref from properties where ref like \"$ref"."%\" order by ref desc")->fetch_assoc();
                            $nref=substr($lastref['ref'],0,3).sprintf( '%03d',substr($lastref['ref'],3,3)+1);
                            $mon3->query("insert into properties 
                            (ref, address, freguesia,coords,owner_id,management,date)
                            values (\"$nref\" , \"$address\" , \"$freg\" ,\"".$prop['coords']."\", \"$owner_id\",
                            \"$mng_id\", \"".date("Y-m-d")."\" )");
                            echo mysqli_error($mon3);
                            $propid=$mon3->insert_id;
                        }
                        else
                        {
                            $propid=$prop['prop_id'];
                        }
                    }

                }

                if($olt == "")
                {
                    $olt_id = 0;    
                }
                else
                {
                    $olt_id = $olt;
                }


                if($aps == "")
                {
                    $aps_id = 0;    
                }
                else
                {
                    $aps_id = $aps;
                }

                // FORMULARIOS INFO / SERVICES
                $update_lead_status_30 = $mon3->query("update property_leads set
                address=\"$address\",
                freguesia=\"$freg\",
                prop_id=\"$propid\",
                olt_id=\"$olt_id\",
                contract_id=\"$contract_id\",
                tv=\"$tv\",
                internet_prof=\"$internet_prof\",
                fixed_ip=\"$fixed_ip\",
                phone1=\"$phone1\",
                phone2=\"$phone2\",
                aps=\"$aps_id\",
                model=\"$model\",
                con_type=\"$con_type\",
                monthly_price=\"$monthly_price\",
                date_modified=\"".date("Y-m-d")."\",
                date_papwk=\"".date("Y-m-d")."\",
                prev_rev_month=\"$prev_rev_month\"

                where id=$lead_id ");

                


                if($update_lead_status_30)
                {
                    $succ.= "<font color=green>Saved on form 'Property Services Properties / Information'</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on form 'Property Services Properties / Information'</font><br>";
                }



                if($error != "")
                {
                    echo $error;
                }
                else
                {
                    echo $succ;
                }



                uploadfile_EditLeads_Status30("contract",$mon_leads.$lead_id."/", "contract_".time().".pdf",1);








                 //send customer welcome email

                /*include "welcomeemail/status30.php";

                $dest_email=$prop['email']."#".$welcomem_email.$created_by['email']."#customer.support@lazerspeed.com";


                enviamail($dest_email,"Lazer Telecom - Update", $status30);

                echo "email sent to customer $dest_email <br><br>";*/



			    //echo "<font color=green>Saved</font>";









		}

		if($status>30 && $status<34)
		{
		$drop_length=mysqli_real_escape_string($mon3, $_POST['drop_length']);
		$ORAC_pits=mysqli_real_escape_string($mon3, $_POST['ORAC_pits']);
		$ORAP_poles=mysqli_real_escape_string($mon3, $_POST['ORAP_poles']);
		$connection_cost=mysqli_real_escape_string($mon3, $_POST['connection_cost']);
		$network_cost=mysqli_real_escape_string($mon3, $_POST['network_cost']);
		if(mysqli_real_escape_string($mon3, $_POST['is_network_ready'])=="on")
			$is_network_ready=1;
		else
			$is_network_ready=0;
		$estimated_quote=mysqli_real_escape_string($mon3, $_POST['estimated_quote']);
		$timeframe=mysqli_real_escape_string($mon3, $_POST['timeframe']);
		$quoted=mysqli_real_escape_string($mon3, $_POST['quoted']);
		$ORAC_id=mysqli_real_escape_string($mon3, $_POST['ORAC_id']);
		$ORAP_id=mysqli_real_escape_string($mon3, $_POST['ORAP_id']);



			$mon3->query("update property_leads set date_viability=\"".date("Y-m-d")."\",
	drop_length=\"$drop_length\",
	ORAC_pits=\"$ORAC_pits\",
	ORAP_poles=\"$ORAP_poles\",
	connection_cost=\"$connection_cost\",
	network_cost=\"$network_cost\",
	is_network_ready=\"$is_network_ready\",
	estimated_quote=\"$estimated_quote\",
	timeframe=\"$timeframe\",
	quoted=\"$quoted\",
	ORAC_id=\"$ORAC_id\",
	ORAP_id=\"$ORAP_id\"
			where id=$lead_id;");



		}



		if($status==40)
		{


			//send customer welcome email

			//include "welcomeemail/status40.php";

			//$dest_email=$prop['email']."#".$welcomem_email.$created_by['email']."#customer.support@lazerspeed.com";


			//enviamail($dest_email,"Lazer Telecom - Update", $status40);

			//echo "email sent to customer $dest_email <br><br>";



		}











		if($status==41 )
		{
			if( $prop['date_book']=="")
			{
				$mon3->query("update property_leads set date_book=\"".date("Y-m-d")."\" where id=$lead_id;");
			}
			$date_install=mysqli_real_escape_string($mon3, $_POST['date_install']);
			$mon3->query("update property_leads set 
			date_install=\"".$date_install."\"
			where id=$lead_id;");
		}













		if($status==50 )
		{

			if( $prop['date_installed']=="")
			{
				$mon3->query("update property_leads set date_installed=\"".date("Y-m-d")."\" where id=$lead_id;");
			}

	        // CONNECTIONS FWA & GPON

		    $model=mysqli_real_escape_string($mon3, $_POST['model']);
		    $antenna=mysqli_real_escape_string($mon3, $_POST['antenna']);
			$fsan=mysqli_real_escape_string($mon3, $_POST['fsan']);
			$olt_id=mysqli_real_escape_string($mon3, $_POST['olt_id']);
			$pon=mysqli_real_escape_string($mon3, $_POST['pon']);

            // SERVICES
			$tv=mysqli_real_escape_string($mon3, $_POST['tv']);

			$internet_prof=mysqli_real_escape_string($mon3, $_POST['internet_prof']);
			$fixed_ip=mysqli_real_escape_string($mon3, $_POST['fixed_ip']);
			$is_router=mysqli_real_escape_string($mon3, $_POST['is_router']);
			$vlan=mysqli_real_escape_string($mon3, $_POST['vlan']);
			$wifi=mysqli_real_escape_string($mon3, $_POST['wifi']);
			$wifi_ssid=mysqli_real_escape_string($mon3, $_POST['wifi_ssid']);
			$wifi_key=mysqli_real_escape_string($mon3, $_POST['wifi_key']);

            // TELEFONES
			$phone1=mysqli_real_escape_string($mon3, $_POST['phone1']);
			$phone2=mysqli_real_escape_string($mon3, $_POST['phone2']);


            $error = '';
            $succ = '';

			$con_type=$prop['con_type'];
			if($con_type=="") $con_type="GPON";


			$propery=$mon3->query("select * from properties where id=\"".$prop['prop_id']."\"")->fetch_assoc();

            // VERIFICAR SE O EQUIPAMENTO ASSOCIA-SE A UMA DADA CONNECTION

            // IS_RECONNECTION = 1
            if($prop['is_reconnection'] == 1 && $prop['is_changeover'] == 0)
            {
                $eq_assoc_conn = $_POST['equip_assoc_not'];
                $conn_id = $_POST['con_id'];
                $conn_equip_type_conn = $mon3->query("SELECT * FROM connections WHERE id =".$conn_id)->fetch_assoc();
                $con_type_connection_id = $conn_equip_type_conn['type'];
                $equip = $conn_equip_type_conn['equip_id'];
                // FSAN
                if($_POST['fsan'] != "")
                {
                    $fsan = $_POST['fsan'];
                }
                else
                {
                    $fsan = $conn_equip_type_conn['equip_id'];
                }

                // MODELO
                if($_POST['model'] != "")
                {
                    $model = $_POST['model'];
                }
                else
                {
                    if($con_type_connection_id == "GPON")
                    {
                        // MODEL
                        $ont=$mon3->query("select * from ftth_ont where fsan=\"$equip\"")->fetch_assoc();
                        $model = $ont['model'];
                    }
                    else if($con_type_connection_id == "FWA")
                    {
                        // MODEL
                        $fwa_cpe=$mon3->query("select * from fwa_cpe where mac=\"$equip\"")->fetch_assoc();
                        $model = $fwa_cpe['model'];
                    }

                }

                // PON/ ONT & ANTENNA

                if($_POST['antenna'] != "" || $_POST['olt_id'] != "" || $_POST['pon'] != "")
                {
                    if($con_type_connection_id == "GPON")
                    {
                        // OLT
                        $olt_id = $_POST['olt_id'];

                        $pon_f = $_POST['pon'];

                    }
                    else if($con_type_connection_id == "FWA")
                    {
                        // ANTENNA
                        $fwa_antenna = $_POST['antenna'];
                    }


                }
                else
                {
                    if($con_type_connection_id == "GPON")
                    {
                            // OLT
                            $olt_id = $ont['olt_id'];

                            $olt_f=$mon3->query("select * from ftth_olt where id=\"$olt_id\"")->fetch_assoc();


                            // PON
                            $pon_first=$mon3->query("select * from ftth_pons where olt_id=\"$olt_id\" order by name")->fetch_assoc();
                            $pon_f = $pon_first['pon'];


                    }
                    else if($con_type_connection_id == "FWA")
                    {
                        // FWA ANTENNA
                        $fwa_antenna = $fwa_cpe['antenna'];
                    }

                }





                // MUDANCA DO IDENTIFICADOR DO EQUIPAMENTO
                if($eq_assoc_conn == 1)
                {
                    // Actualizar EQUIP_ID A CONNECTION
                    $update_equip_exist_not_assoc = $mon3->query("update connections set equip_id=\"".$fsan."\" where id=$conn_id;");

                    if($update_equip_exist_not_assoc)
                    {
                        $succ .= '<font color=green>Update Equipment '.$fsan.' on connection number '.$conn_id.'</font><br>';
                    }
                    else
                    {
                        $error .= '<font color=green>Error on Updating Equipment '.$fsan.' on connection number '.$conn_id.'</font><br>';
                    }

                    if($con_type_connection_id == "GPON")
                    {
                        // Actualizar o Equipamento ONT
                        $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                        $update_equip_ont_50 = $mon3->query("update ftth_ont set olt_id=\"$olt_id\",ont_id=\"$ontnext\", model=\"$model\"
                        where fsan=\"$fsan\"");

                        if($update_equip_ont_50)
                        {
                            $succ .= '<font color=green>Update ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Updating ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                    }
                    else if($con_type_connection_id == "FWA")
                    {
                        // Actualizar o Equipamento FWA CPE

                        $update_equip_fwa_cpe_50 = $mon3->query("update fwa_cpe set model=\"$model\",antenna=\"$fwa_antenna\" where mac=\"$fsan\"");

                        if($update_equip_fwa_cpe_50)
                        {
                            $succ .= '<font color=green>Update FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Updating FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                    }






                }
                else if($eq_assoc_conn == 2)
                {
                    // CRIAR O SEU EQUIPAMENTO
                    if($con_type_connection_id == "GPON")
                    {
                        // INSERT ONT

                        $ont_id="1-".$pon_f."-".nextont($olt_id,$pon_f);
                        $insert_ont_50 = $mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,model) values (
                        \"$fsan\",
                        $olt_id,
                        \"$ont_id\",
                        \"$model\"
                        )");



                        if($insert_ont_50)
                        {
                            $succ .= '<font color=green>Insert ONT '.$fsan.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Insert ONT '.$fsan.'</font><br>';
                        }

                    }
                    else if($con_type_connection_id == "FWA")
                    {
                        // ADICIONAR FWA CPE NA BASE DE DADOS

                        $insert_fwa_cpe_50 = $mon3->query("insert into fwa_cpe 
                            (mac, model, antenna)
                            values (\"$fsan\" , \"$model\" , \"$fwa_antenna\" )");

                        if($insert_fwa_cpe_50)
                        {
                            $succ .= '<font color=green>Insert FWA CPE '.$fsan.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on FWA CPE '.$fsan.'</font><br>';
                        }



                    }

                    // ACTUALIZAR O ID da Connection e do TIPO connection FWA / GPON

                    $update_equip_new_50 = $mon3->query("update connections set equip_id=\"".$fsan."\" where id=$conn_id;");

                        if($update_equip_new_50)
                        {
                            $succ .= '<font color=green>Update Equipment '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Updating Equipment '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }


                }

                else if($eq_assoc_conn == 0 || $eq_assoc_conn == '')
                {
                    // ACTUALIZAR AS CARACTERISTICAS DO EQUIPAMENTO

                    if($con_type_connection_id == "GPON")
                    {
                        // ONT

                        $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                        $update_equip_ont_50 = $mon3->query("update ftth_ont set olt_id=\"$olt_id\",ont_id=\"$ontnext\", model=\"$model\"
                        where fsan=\"$fsan\"");

                        if($update_equip_ont_50)
                        {
                            $succ .= '<font color=green>Update ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Updating ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }






                    }

                    else if($con_type_connection_id == "FWA")
                    {
                        // Actualizar o Equipamento FWA CPE

                        $update_equip_fwa_cpe_50 = $mon3->query("update fwa_cpe set model=\"$model\",antenna=\"$fwa_antenna\" where mac=\"$fsan\"");

                        if($update_equip_fwa_cpe_50)
                        {
                            $succ .= '<font color=green>Update FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Updating FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                        }
                    }

                    $fsan_ont_assoc = $mon3->query("SELECT * FROM connections WHERE equip_id ='".$fsan."'");
                    $fsan_conn = $fsan_ont_assoc->fetch_assoc();

                    if($fsan_ont_assoc->num_rows >= 1)
                    {
                        $succ .= "<font color=red>This Equipment ".$fsan." is already associated the connection number ".$fsan_conn['id']." please choose a equipment which has not associated to this connection on equipment</font><br>";
                    }

                }


                // VERIFICAR SE OS MODELOS SAO DIFERENTES

                $lead_model = $mon3->query("SELECT * FROM property_leads WHERE id=".$_GET['lead_id'])->fetch_assoc();



                if($lead_model['model'] != $model)
                {
                    // Actualizar o Modelo da LEAD
                    echo "update property_leads set model=\"$model\" where id=".$_GET['lead_id'];
                    $update_model_50 = $mon3->query("update property_leads set model=\"$model\" where id=".$_GET['lead_id']);

                        if($update_model_50)
                        {
                            $succ .= '<font color=green>Update Model '.$fsan.' on property lead number '.$_GET['lead_id'].'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=green>Error on Updating Model '.$fsan.' on property lead number '.$_GET['lead_id'].'</font><br>';
                        }

                }

                // LEAD_SUB != OWNER_ID

                // VERIFICAR SE A LEAD SUBSCRIBER E DIFERENTE AO OWNER DA PROPERTY DO CUSTOMER
                if($propery['owner_id'] != $prop['lead_sub'])
                {
                    //update property_leads set date_book=\"".date("Y-m-d")."\" where id=$lead_id;
                    $update_prop_owner_status_30 = $mon3->query("update properties set owner_id=\"".$prop['lead_sub']."\" WHERE id=".$prop['prop_id']);
                    
                    if($update_prop_owner_status_30)
                    {
                        $succ .= "<font color=green>Update Property on number ".$prop['prop_id']." on owner ".$prop['lead_sub']."</font>";
                    }
                    else
                    {
                        $error .= "<font color=green>Error Property on number ".$prop['prop_id']." on owner ".$prop['lead_sub']."</font>";
                    }
                }

                //echo $prop['lead_sub'];
                $mon3->query("update connections set subscriber=\"".$prop['lead_sub']."\" where id=$conn_id;");




            }
            else if($prop['is_reconnection'] == 0 && $prop['is_changeover'] == 1)
            {
                // NOVA CONEXAO - QUE FOI ALTERADA COMO CHANGE OVER

                // FAZER
                echo "teste";
            }
            else if($prop['is_reconnection'] == 0 && $prop['is_changeover'] == 0)
            {
                // ADICIONAR CONEXAO

                $propery=$mon3->query("select * from properties where id=\"".$prop['prop_id']."\"")->fetch_assoc();

                

                echo "inserting connection<br>";
                $mon3->query("insert into connections (property_id,type,equip_id,date_start,subscriber) VALUES (
                    \"".$prop['prop_id']."\",
                    \"$con_type\",
                    \"$fsan\",
                    \"".date("Y-m-d")."\",
                    \"".$propery['owner_id']."\"			
                    ) ");
                    $con_id=$mon3->insert_id;
                     echo mysqli_error($mon3);

                        if($con_type=="GPON")
                        {
                                $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                                $num_ont=$mon3->query("select fsan from ftth_ont where fsan=\"$fsan\"")->num_rows;
                                $ont_id_ex=$mon3->query("select fsan from ftth_ont where fsan=\"$fsan\" and ont_id=\"\" ")->num_rows;

                                if($num_ont>0 && $ont_id_ex>0)
                                    {
                                    echo "update ONT $fsan to $ontnext <br>";
                                    $mon3->query("update ftth_ont set olt_id=\"$olt_id\",ont_id=\"$ontnext\"			
                                        where fsan=\"$fsan\"");
                                     echo mysqli_error($mon3);

                                    }
                                    elseif($num_ont>0 && $ont_id_ex==0) //tem ont_id
                                    {
                                        echo "ONT in database with ID.. not registering to connection.<br>";
                                    }
                                    else
                                    {

                                    echo"inserting ont $ontnext <br>";

                                    $mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,model) VALUES (
                                    \"$fsan\",
                                    \"$olt_id\",
                                    \"$ontnext\",
                                    \"$model\"			
                                    ) ");
                                     echo mysqli_error($mon3);
                                    }
                        }
                        elseif($con_type=="FWA")
                        {

                                    $mon3->query("insert into fwa_cpe (mac,model,antenna) VALUES (
                                    \"$fsan\",
                                    \"$model\",
                                    \"$antenna\"			
                                    ) ");
                                     echo mysqli_error($mon3);

                        }
            }

            // ACTUALIZAR OS EQUIPAMENTOS DOS SERVIÇOS

            $mon3->query("UPDATE services set equip_id = '".$fsan."', subscriber = ".$propery['owner_id'].", contract_id = ".$prop['contract_id']." WHERE connection_id=".$conn_id);

            // VERIFICAR SE OS SERVICOS ESTAO ATIVOS NESTA CONEXAO



            $servicos_at = $mon3->query("SELECT * FROM `services` where connection_id=".$conn_id." AND equip_id='".$fsan."'");




            //while($servico_at=$servicos_at->fetch_assoc())
            //{
                //echo $servico_at['id'];
                // DESATIVAR OS SERVICOS PARA CRIAR UM NOVO SERVIÇO

                //$servico_id = $servico_at['id'];

                //$date_end_services = $mon3->query("SELECT * FROM services WHERE id=".$servico_id)->fetch_assoc();

                /*if($date_end_services['date_end'] == '0000-00-00')
                {
                    $error .= "<font color=red>These Services on this connection number ".$conn_id. " are ativated. Please deactivated one of services</font>";
                    break;
                }*/



                // DESATIVAR OS SERVIÇOS TODOS
                /*$des_services_status_50 = $mon3->query("UPDATE services set date_end = '".date("Y-m-d")."' WHERE id=".$servico_id);

                if($des_services_status_50)
                {
                        $succ .= "<font color=green>Services number ".$servico_id." was deactivated sucessfully on date ".date("Y-m-d")."</font><br>";
                }
                else
                {
                        $error .= "<font color=green>Error Services number ".$servico_id." </font><br>";
                }*/
            //}


            // AREA CODES ACTUALIZAR PELA REFERENCIA




            // INSERIR SERVICOS PARA ATIVAR A CONNECTION EXISTENTE


            if($tv!="0")
            {
                $mon3->query("insert into services (connection_id,equip_id,type,date_start,contract_id,subscriber) VALUES (
                \"$conn_id\",
                \"$fsan\",
                \"TV\",
                \"".date("Y-m-d")."\",
                \"".$prop['contract_id']."\",
                \"".$propery['owner_id']."\"
                ) ");
                echo mysqli_error($mon3);

            }

		if($internet_prof>0)
		{
			$mon3->query("insert into services (connection_id,equip_id,type,date_start,contract_id,subscriber) VALUES (
			\"$conn_id\",
			\"$fsan\",
			\"INT\",
			\"".date("Y-m-d")."\",
			\"".$prop['contract_id']."\",
			\"".$propery['owner_id']."\"
			) ");
			echo mysqli_error($mon3);
			$intserv=$mon3->insert_id;

			$servi=$mon3->query("select name,prof_up,prof_down from int_services where id=$internet_prof")->fetch_assoc();
			echo mysqli_error($mon3);
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"speed\",
			\"".$internet_prof."\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);

			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"is_router\",
			\"$is_router\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);
			if($is_router==0){
				$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"bridge_port\",
			\"1\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);
			}
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"vlan\",
			\"$vlan\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);

			if($fixed_ip!=""){
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"fixed_ip\",
			\"$fixed_ip\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);
			$mon3->query("update in_fixed_ips set in_use=1, mac=\"xxx\" where ip=\"$fixed_ip\" ");
			echo mysqli_error($mon3);
			}

			//wifi
			if($wifi=="1"){
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"wifi\",
			\"1\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"wifi_ssid\",
			\"$wifi_ssid\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"wifi_key\",
			\"$wifi_key\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);

			}
			else
			{
			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"wifi\",
			\"0\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);

			}


		}






		if($phone1!=""){


			$mon3->query("insert into services (connection_id,equip_id,type,date_start,contract_id,subscriber) VALUES (
			\"$conn_id\",
			\"$fsan\",
			\"PHN\",
			\"".date("Y-m-d")."\",
			\"".$prop['contract_id']."\",
			\"".$propery['owner_id']."\"
			) ");
			echo mysqli_error($mon3);
			$intserv=$mon3->insert_id;



			$mon3->query("insert into voip_accounts (password,caller_id,voicemail,voicemail_time,call_limit) VALUES (
			\"".substr(time()*10/3,2,8)."\",
			\"351".$phone1."\",
			\"1\",
			\"50\",
			\"1\"
			) ");
			$voip1=$mon3->insert_id;
			echo mysqli_error($mon3);

			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"account\",
			\"$voip1\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);

			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"phn_port\",
			\"1\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);



		}
		if($phone2!=""){

			$mon3->query("insert into services (connection_id,equip_id,type,date_start,contract_id,subscriber) VALUES (
			\"$conn_id\",
			\"$fsan\",
			\"PHN\",
			\"".date("Y-m-d")."\",
			\"".$prop['contract_id']."\",
			\"".$propery['owner_id']."\"
			) ");
			echo mysqli_error($mon3);
			$intserv=$mon3->insert_id;



			$mon3->query("insert into voip_accounts (password,caller_id,voicemail,voicemail_time,call_limit) VALUES (
			\"".substr(time()*10/3,2,8)."\",
			\"351".$phone2."\",
			\"1\",
			\"50\",
			\"1\"
			) ");
			$voip2=$mon3->insert_id;
			echo mysqli_error($mon3);

			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"account\",
			\"$voip2\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);

			$mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
			\"$intserv\",
			\"phn_port\",
			\"2\",
			\"".date("Y-m-d")."\"
			) ");
			echo mysqli_error($mon3);


		}









                if($error != "")
                {
                    echo $error;
                }
                else
                {
                    echo $succ;
                }


                /*
                 *
                 *
                 *  include "welcomeemail/welcome.php";

			        $dest_email=$prop['email']."#".$welcomem_email.$created_by['email']. "#customer.support@lazerspeed.com";


			        enviamail($dest_email,"Welcome to Lazer Telecom", $welcomeemail);

			        echo "email sent to customer $dest_email <br><br>";
                 */



		}






		if($status==51 )
		{

			$installation_job_id=mysqli_real_escape_string($mon3, $_POST['installation_job_id']);
			$technician=mysqli_real_escape_string($mon3, $_POST['technician']);

			$mon3->query("update property_leads set 
	installation_job_id=\"$installation_job_id\",
	technician=\"$technician\"
			where id=$lead_id;");

			echo uploadfile("file1",$mon_leads.$lead_id."/", "pic1_".time().".jpg",1);
			echo uploadfile("file2",$mon_leads.$lead_id."/", "pic2_".time().".jpg",1);
			echo uploadfile("file3",$mon_leads.$lead_id."/", "pic3_".time().".jpg",1);
			echo uploadfile("file4",$mon_leads.$lead_id."/", "pic4_".time().".jpg",1);



		}












		if($status==60 )
		{

			if( $prop['date_closed']=="")
			{
				$mon3->query("update property_leads set date_closed=\"".date("Y-m-d")."\" where id=$lead_id;");
			}


			$installation_job_id=mysqli_real_escape_string($mon3, $_POST['installation_job_id']);
			$technician=mysqli_real_escape_string($mon3, $_POST['technician']);
			$NPS_score=mysqli_real_escape_string($mon3, $_POST['NPS_score']);
			$manager_score=mysqli_real_escape_string($mon3, $_POST['manager_score']);
			$has_pictures=mysqli_real_escape_string($mon3, $_POST['has_pictures']);
			if($has_pictures!="")
				$has_pictures=1;

			$mon3->query("update property_leads set 
			date_closed=\"".date("Y-m-d")."\",
	installation_job_id=\"$installation_job_id\",
	technician=\"$technician\",
	NPS_score=\"$NPS_score\",
	manager_score=\"$manager_score\",
	has_pictures=\"$has_pictures\",
	speedtest=\"$speedtest\"
			where id=$lead_id;");



			echo uploadfile("filea",$mon_leads.$lead_id."/", "pic1_".time().".jpg",1);
			echo uploadfile("fileb",$mon_leads.$lead_id."/", "pic2_".time().".jpg",1);
			echo uploadfile("filec",$mon_leads.$lead_id."/", "pic3_".time().".jpg",1);
			echo uploadfile("filed",$mon_leads.$lead_id."/", "pic4_".time().".jpg",1);



		}



	}




}

//retirar novamente os valores actualizados da lead
$prop=$mon3->query("select * from property_leads where id=$lead_id;")->fetch_assoc();
//echo $prop['status'];
$freg=$mon3->query("select freguesia,concelho from freguesias where id=".$prop['freguesia'])->fetch_assoc();
$conc=$mon3->query("select concelho,distrito,pais from concelhos where id=".$freg['concelho'])->fetch_assoc();


echo "<table><tr>
<td width=550px>
<form action=\"?propleads=1&lead_id=$lead_id\" name=updatelead method=post enctype=\"multipart/form-data\">
<b>Address: </b><br> ".$prop['address']." <br>".$freg['freguesia']." - ".$conc['concelho']." - "
.$conc['distrito']." - ".$conc['pais']."<br>";
if($prop['prop_id']!=""){
	$refc=$mon3->query("select ref from properties where id=".$prop['prop_id'].";")->fetch_assoc();
	echo "Prop: <a href=?props=1&propid=".$prop['prop_id'].">".$refc['ref']." </a>";
}

echo "<br><br> 
<b>name: </b><br> ".$prop['name']." <br> <br> 
<b>email: </b><br> ".$prop['email']." <br> <br> 
<b>phone: </b><br> ".$prop['phone']." <br> <br> 
<b>agent: </b><br> <a href=?cust=".$prop['agent_id'].">".$agent['name']."</a> <br> <br> 
<b>date: </b><br> ".$prop['date_lead']." created by ".$prop['created_by']." <br> <br> 


<script>

function aditionalstatus()
{
	var textdiv='';
	var soptionb=document.getElementById('idstatus');
    
	var soption=soptionb.options[soptionb.selectedIndex].value;
    //var soption=50;
    console.log(soption);
    
    
    
	textdiv += '<font color=red> details are only savend on a status change. Please use notes if you want to notify of changes on submitted data</font><br><br>';
	
	
	if(soption>0 && soption<3) 
	{
		textdiv += ' form to prepare rough estimates <br><table>' +
		'<tr><td>Connection: '+

	
		'<tr><td>Type<td> 	<select name=con_type onchange=\"updatecpe(this.options[this.selectedIndex].value,);\">				<option value=GPON  selected>GPON</option>	 		<option value=FWA >FWA</option> 			<option value=COAX>COAX</option> 		<option value=DIA>DIA</option>		<option value=ETH>ETH</option> 		<option value=DARKF>DARKF</option>		</select>'+	
		
		'<div id=model_cpe><tr><td>CPE model<td><select name=model id=models> 			<option  value=zhone-2427 selected>zhone-2427</option> 				<option selected value=zhone-2727a>zhone-2727a</option>				<option value=zhone-2427>SFP zhone-2427</option> 				<option value=SFP>DIA connection</option> 		<option value=mininode>fibre mininode</option> 				</select></div>'+	

		
		
		
		
		
		
		
		'<tr><td>How many ORAC pits (drop)?<td><input type=text name=ORAC_pits value=".$prop['ORAC_pits']." size=5> '+
		'<tr><td>How many ORAP poles (drop)?<td><input type=text name=ORAP_poles value=".$prop['ORAP_poles']." size=5><br> '+
		'<tr><td>Drop length?<td><input type=text size=5 name=drop_length value=".$prop['drop_length'].">m<br> '+	
		'<tr><td>Connection cost?<td><input type=text name=connection_cost value=".$prop['connection_cost']." size=5>€ '+
		'<tr><td>kmz file<td><input type=file name=plan ><tr><td><br> '+
		'<tr><td>zipfile<td><input type=file name=planz ><tr><td><br> '+
		'<tr><td>Network: '+	
		'<tr><td>Is Network Ready? <td><input type=checkbox name=is_network_ready value=1 "; if($prop['is_network_ready']==1)
		echo " checked";
		echo "><br> '+
		'<tr><td>Network investment?<td><input size=5 type=text name=network_cost value=".$prop['network_cost']." >€<tr><td><br> '+
		
		'<tr><td>Customer Info: '+
		'<tr><td>Estimated costs to customer?<td><input size=5 type=text name=estimated_quote value=".$prop['estimated_quote']." >€<br> '+
		'<tr><td>Timeframe from paper to service<td><input type=text name=timeframe value=".$prop['timeframe']." size=5>days<br> '+
		
		'</table>';

	}
	
	else if(soption==7) 
	{
        
        textdiv += '<tr><td><br>Connection <input type=hidden id=con_type_id>' +
        '<tr id=type_con_sta_7><td>Type <td><select name=con_type_rec id=con_type_rec onchange=change_type_connection(this.value)>' +		
		'<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +     
        '</select><br>';";
        $q_where_conn_prop = "";
        if($prop['prop_id'] != 0)
            {
                $q_where_conn_prop .= " AND property_id = ".$prop['prop_id'];

            }

        $con_type = $prop['con_type'];
        if($con_type == "")
            {
                $con_type = "GPON";
            }

        $conns=$mon3->query("select * from connections where 1".$q_where_conn_prop." AND type = \"$con_type\"")->fetch_assoc();


        echo "textdiv += '<label>Disabled Prop Services</label> <input type=checkbox name=disabled_prop_services id=disabled_prop_services onchange=\"changePropServicesDisabled(this)\"><br>";

        echo "<label id=text_conn_prop_rec>Property Reconnection</label> <select name=refe_rec_7 id=refe_rec_7 onchange=con_prop_rec(this.value)>' +
        '<option value=0>new reconnection</option>";

        $coaxs=$mon3->query("select properties.id,properties.ref,properties.address 
        from properties left join connections on connections.property_id=properties.id where connections.type = \"$con_type\" order by properties.ref");
        while($coax=$coaxs->fetch_assoc())
        {
            echo "<option value=".$coax['id'];

			if($prop['prop_id']==$coax['id']) echo " selected";
			echo "> ".$coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address']))."</option>";
        }
        echo "</select><br>'; ";


                echo "
                textdiv += '<div id=prop_conn_servicos_des></div>';
                
                ";



        if($conns['id'] != "")
            {
                $con_as = 1;
            }
        else
            {
                $con_as = 0;
            }
        echo "
                textdiv += '<div id=conn_assoc_prop_status_7><input type=hidden name=rec_assoc value=".$con_as."></div>';
                
                ";


        echo "
        
	}
	
	
	else if(soption==14) 
	{
		textdiv += ' form to prepare rough estimates <br><table>' +

		'<tr><td>How many ORAC pits for the drop?<td><input type=text name=ORAC_pits value=".$prop['ORAC_pits']." size=5><br> '+
		'<tr><td>How many ORAP poles for the drop?<td><input type=text name=ORAP_poles value=".$prop['ORAP_poles']." size=5><br> '+
		'<tr><td>Drop length?<td><input type=text size=5 name=drop_length value=".$prop['drop_length'].">m<br> '+	
		'<tr><td>Connection cost?<td><input type=text name=connection_cost value=".$prop['connection_cost']." size=5>€<br> '+
		'<tr><td>Is Network Ready? <td><input type=checkbox name=is_network_ready size=5 "; if($prop['is_network_ready']==1)
		echo " checked";
		echo "><br> '+
		'<tr><td>Network investment?<td><input size=5 type=text name=network_cost value=".$prop['network_cost']." >€<br> '+
		'<tr><td>Estimated costs to customer?<td><input size=5 type=text name=estimated_quote value=".$prop['estimated_quote']." >€<br> '+
		'<tr><td>Timeframe from paper to service<td><input type=text name=timeframe value=".$prop['timeframe']." size=5>days<br> '+
		'<tr><td>Quoted to customer <td><input type=text name=quoted value=".$prop['quoted']." size=5>€<br> '+
		'</table>';

	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	else if(soption==30)
	{
		textdiv += ' form to get details of service, create property   <br><table>'+
		'<tr><td >Address<td><input type=text name=address value=\"".str_replace("'","",$prop['address'])."\" size=50><br> '+
		'<tr><td>Freguesia:<td><select name=freg id=freg>";

$concp=$mon3->query("select concelho from freguesias where id=".$prop['freguesia'].";")->fetch_assoc();
$fregs=$mon3->query("select * from freguesias where concelho=".$concp['concelho'].";");
$s_where_conn = "";
$s_where_owner = "";
if($prop['prop_id'] != 0)
{
    $s_where_conn = " AND property_id = ".$prop['prop_id'];
    $s_where_owner = " AND id = ".$prop['prop_id'];
}

$query_conn = "select * from connections WHERE 1".$s_where_conn." AND type = \"GPON\"";
$query_owner = "select * from properties WHERE 1".$s_where_owner;
$conxao=$mon3->query($query_conn)->fetch_assoc();
$owner = $mon3->query($query_owner)->fetch_assoc();


while($frega=$fregs->fetch_assoc())
{
	echo "<option value=".$frega['id'];
	if ($frega['id']==$prop['freguesia'])
		echo " selected";
	echo ">".$frega['freguesia']."</option>";
}
echo"</select>";
echo "<tr><td>Concelho:<td><select name=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\">";
$concs=$mon3->query("select * from concelhos  order by distrito,concelho;");
//where pais=\"PORTUGAL\"
while($conca=$concs->fetch_assoc())
{
	echo "<option value=".$conca['id'];
	if ($conca['id']==176)
		echo " selected";
	echo ">".$conca['distrito']." - ".$conca['concelho']."</option>";
}

echo"</select>'+ ";

if($prop['is_changeover'] == 1 && $prop['is_reconnection'] == 0)
{
    $dis = "disabled";
    $subs = "disabled";
}
else if($prop['is_changeover'] == 0 && $prop['is_reconnection'] == 1)
{
    $dis = "disabled";
    $subs = "disabled";
}
else if($prop['is_changeover'] == 0 && $prop['is_reconnection'] == 0)
{
    $dis = "";
    $subs = "";
}
		echo "'<tr><td>Prop Ref<td><select name=ref id=ref ".$dis.">";
		$refs=$mon3->query("select areacode,description from area_codes order by areacode"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['areacode'].">".$ref['areacode']." - ".$ref['description']."</option>";} echo " </select><br> '+
		'<tr><td>Subscriber<td><select name=owner_id id=owner_id ".$subs.">";
	$custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
	while($cust=$custs->fetch_assoc())
	{echo "<option value=".$cust['id'].">". $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr']."</option>";}

		echo " </select><br> '+
		'<tr><td>Management<td><select name=mng_id> <option selected value= >no management</option>";
		$refs=$mon3->query("select id,name from customers where is_management=1 order by name"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['id'].">".$ref['name']."</option>";} echo " </select><br>'+

		'<tr><td> <br>'+
		'<tr><td>Contract id<td><input type=text name=contract_id value=".$prop['contract_id']." size=5> '+
		'<tr><td>contract pdf <td><input type=file name=contract>'+ ";

        echo "'<tr><td><br>Connection <input type=hidden id=con_type_id>'+ ";

        if($prop['is_reconnection'] == 1)
            {
                $con_ty = "disabled";
            }

		echo "'<tr id=type_con_sta_30><td>Type<td><select name=con_type id=con_type onchange=\"updatecpe(this.options[this.selectedIndex].value,); changeConOver(this.options[this.selectedIndex].value);\" ".$con_ty.">' +		
		'<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +     
        '</select>'+";

        echo "'<tr><td id=cpe_text>CPE model<td><div id=model_cpe><select name=model id=models>	<option  value=zhone-2427 "; if($prop['model']=='zhone-2427') echo "selected"; echo ">zhone-2427</option>		<option value=zhone-2727a "; if($prop['model']=='zhone-2727a') echo "selected"; echo ">zhone-2727a</option>		<option value=zhone-2428 "; if($prop['model']=='zhone-2428') echo "selected"; echo ">zhone-2428 (internet only)</option>		<option value=SFP "; if($prop['model']=='sfp1gbidi') echo "selected"; echo ">SFP (DIA connection)</option> 		<option value=RF-conv "; if($prop['model']=='rfconv') echo "selected"; echo ">fibre (TV only))</option> 		</select></div>'+";


        echo "'<input type=hidden id=is_rec_input name=is_rec_input value=".$prop['is_reconnection'].">' +";
        echo "'<input type=hidden id=is_chg_input name=is_chg_input value=".$prop['is_changeover'].">' +";

        /*if($prop['is_changeover']==0 && $prop['is_reconnection']==0)
            {
                $teste_display_prop = "none";
            }
        else
            {
                $teste_display_prop = "table-row";
            }*/

        // Verificar a Change Over
        $teste_disp_rec = "none";
        $teste_check_over = "block";
        if($prop['is_changeover']==1)
            {
                $active = "checked";

            }
        else
            {
                $active = "selecteded";
            }
            //$valor = "'.$active.'"';


            // Verificar a Reconnection
            if($prop['is_reconnection']==1 && $prop['is_changeover']==0)
                {
                    $active_reconnection = "checked";
                    $teste_disp_rec = "block";
                    $teste_check_over = "none";

                    $dis = "none";

                }
            else if($prop['is_reconnection']==0 && $prop['is_changeover']==1)
                {
                    $active_reconnection = "selecteded";
                    $teste_disp_rec = "none";
                    $teste_check_over = "block";

                    $dis = "block";
                }

            else if($prop['is_reconnection']==0 && $prop['is_changeover']==0)
                {
                    $active_reconnection = "selecteded";
                    $teste_disp_rec = "none";
                    $teste_check_over = "block";

                    $dis = "none";
                }




        echo "'<tr style=\"display: ".$teste_check_over."\"><td>is changeover<input type=checkbox name=is_changeover id=is_changeover ".$active." onchange=\"changeOverState(this)\"><input type=hidden id=is_changeover_val value=".$prop['is_changeover'].">' + ";
        //echo "'<tr><td>Existing COAX/FWA Ref<td> <select name=refe>' +";






		echo "
        
        
        '<tr><td colspan=2><div id=conexao_changeOVER style=\"display: ".$dis."\">' +";
        $where_conections="";
        $p_id=0;
        //$concs=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` INNER JOIN properties ON connections.property_id = properties.id WHERE 1 AND ".$where_conections);
        echo"'<fieldset>' +
            '<legend>Change Over:</legend>' + ";
        echo "'<label id=text_conn_prop>Existing COAX/FWA Ref</label> <select name=refe id=refe onchange=con_prop_type(this.value)>";
        if($active == "selecteded")
            {
                echo "<option value=0>new connection</option>";
            }

        else if($active_reconnection == "selecteded")
            {
                echo "<option value=0>new reconnection</option>";
            }


		$coaxs=$mon3->query("select properties.id,properties.ref,properties.address, connections.type 
        from properties left join connections on connections.property_id=properties.id order by properties.ref");
		while($coax=$coaxs->fetch_assoc())
		{

            echo "<option value=".$coax['id'];
			if($prop['prop_id']==$coax['id']) echo " selected";
			echo "> ".$coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address']))."</option>";

            //echo "'<option value=".$coax['id'].">".$coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address']))."</option>' +";
        }
		echo "</select><br>' + ";
        echo "'<label>Connection:</label>' +
            '<select id=con_id name=con onchange=\"connection_equip(this.value)\">";
        if($prop['prop_id'] != 0)
            {
                $where_conections .= "AND property_id = ".$prop['prop_id']." ";
            }
        /*if($prop['con_type'] == 'GPON')
            {
                $where_conections .= "AND type = \"".$prop['con_type']."\"";

            }

        if($prop['con_type'] == 'FWA')
            {
                $where_conections .= "AND type = \"".$prop['con_type']."\"";

            }
        if($prop['con_type'] == 'COAX')
            {
                $where_conections .= "AND type = \"".$prop['con_type']."\"";

            }

        if($prop['con_type'] == 'DIA')
            {
                $where_conections .= "AND type = \"".$prop['con_type']."\"";
            }

        if($prop['con_type'] == 'DARKF')
            {
                $where_conections .= "AND type = \"".$prop['con_type']."\"";
                //echo "'".$where_conections."' +";


            }*/

        $concs=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` INNER JOIN properties ON connections.property_id = properties.id WHERE 1 ".$where_conections);
                while($conca=$concs->fetch_assoc())
                {
                    echo "<option value=".$conca['connection_id'].">".$conca['connection_id']."</option>";
                }

        //$concs=$mon3->query("connections.id as 'connection_id', connections.type as 'type', connections.equip_id as 'eq', properties.ref as 'referencia' ".$where_conections."INNER JOIN properties ON connections.property_id = properties.id");
        /*while($conca=$concs->fetch_assoc())
                        {
                            ?>
                            <?php
                                 echo"'<option value=".$conca['connection_id']."' +";

                            ?>
                            <option value=<?php echo $conca['connection_id'] ?>>
                                <?php echo $conca['eq']." - ".$conca['referencia']; ?>
                            </option>
                            <?php
                 }*/
        echo "</select><br>";


        echo "<label>Subscriber Change Over</label> <select name=owner_chg id=owner_chg>";

        $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
	while($cust=$custs->fetch_assoc())
	{echo "<option value=".$cust['id'].">". $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr']."</option>";}

        echo "</select><br>";
        //echo "<p style=\"text-align: center;\">".$conxao['id']."</p>";
        echo "<div id=conn_type_fsan style=\"display: grid\"></div>";

        echo "</div>' +
        
        '<div id=conexao_RECONNECTION style=\"display: ".$teste_disp_rec."\">' +";
        echo"'<fieldset>' +
            '<legend>Reconnection:</legend>' +";



        echo "'<label id=text_conn_prop_rec>Reconnection Property</label> <select name=refe_rec id=refe_rec onchange=rec_prop_subs(this.value)>";
        if($active == "selecteded")
            {
                echo "<option value=0>new connection</option>";
            }

        else if($active_reconnection == "selecteded")
            {
                echo "<option value=0>new reconnection</option>";
            }


		$coaxs=$mon3->query("select properties.id,properties.ref,properties.address 
        from properties left join connections on connections.property_id=properties.id order by properties.ref");
		while($coax=$coaxs->fetch_assoc())
		{

            echo "<option value=".$coax['id'];
			if($prop['prop_id']==$coax['id']) echo " selected";
			echo "> ".$coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address']))."</option>";

            //echo "'<option value=".$coax['id'].">".$coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address']))."</option>' +";
        }
		echo "</select><br> ";

        //echo $owner['owner_id'];

        echo "<label>Subscriber Reconnection</label> <select name=owner_rec id=owner_rec >";

        $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
	while($cust=$custs->fetch_assoc())
	{
        echo "<option value=".$cust['id'];
        if($owner['owner_id']==$cust['id']) echo " selected";
        echo ">". $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr']."</option>";
    }

        echo "</select><br>' + ";



        echo "'<div id=connection_type_insert style=\"display: grid\"></div>' +";

        echo "'</div>' +

        '<div id=error_rec_prop_subs></div>' +
        	
		
			
        
        
        
        
		
		'<tr><td><br>Services'+
		
		'<tr><td>TV service<td><select name=tv><option value=0 "; if($prop['tv']==0) echo "selected"; echo ">no TV</option><option value=AMLA "; if($prop['tv']=="AMLA") echo "selected"; echo ">AMLA</option><option value=NOWO "; if($prop['tv']=="NOWO") echo "selected"; echo ">NOWO</option></select>'+	
		
		'<tr><td>Internet service<td><select name=internet_prof><option value=0 "; if($prop['internet_prof']==0) echo "selected"; echo ">no internet</option>";

		$intservices=$mon3->query("select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down");
		while($serv=$intservices->fetch_assoc())
		{
			echo "<option value=".$serv['id'];
			if($prop['internet_prof']==$serv['id']) echo " selected";
			echo "> ".$serv['name']."</option>";
		}

		echo "</select>";

        $active="";
        $a = 0;
        $a_rec = 0;
        $active_reconnection="";

echo " Fixed ip:<select name=fixed_ip> <option selected value= >no fixed ip</option>";
		$refs=$mon3->query("select ip from int_fixed_ips where in_use!=1 order by ip"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['ip'].">".$ref['ip']."</option>";} echo " </select>'+
		
		'<tr><td>phone line 1<td><select name=phone1> <option selected value= >no line</option>";
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'].">".$ref['phone_number']."</option>";} echo " </select><br>'+
		
		'<tr><td>phone line 1<td><select name=phone2> <option selected value= >no line</option>";
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'].">".$ref['phone_number']."</option>";} echo " </select><br>'+

		'<tr><td>Need APs? how many?<td><input type=text name=aps size=5 value=\"".$prop['APs']."\" >un (250€ each)<br> '+
		'<tr><td>monthly revenue<td><input type=text name=monthly_price size=5 value=\"".$prop['monthly_price']."\" >€/month<br> '+
		
		'<tr>' +";




        echo "'<tr><td>Previous income /month <td><input type=text name=prev_rev_month size=5 value=\"".$prop['prev_rev_month']."\" >€<br> '+
	'</table>'+
	'';
	}
	else if((soption >30) && (soption <34) )
	{
		textdiv += ' form to estimate on when is ready to install'+
		'<table> '+
		'<tr><td>How many ORAC pits for the drop?<td><input type=text name=ORAC_pits value=".$prop['ORAC_pits']." size=5><br> '+
		'<tr><td>How many ORAP poles for the drop?<td><input type=text name=ORAP_poles value=".$prop['ORAP_poles']." size=5><br> '+
		'<tr><td>Drop length?<td><input type=text size=5 name=drop_length value=".$prop['drop_length'].">m<br> '+	
		'<tr><td>Connection cost?<td><input type=text name=connection_cost value=".$prop['connection_cost']." size=5>€<br> '+
		
		'<tr><td>Is Network Ready? <td><input type=checkbox name=is_network_ready size=5 "; if($prop['is_network_ready']==1)
		echo " checked";
		echo "><br> '+
		'<tr><td>Network investment?<td><input size=5 type=text name=network_cost value=".$prop['network_cost']." >€<br> '+
		
		'<tr><td>Estimated costs to customer?<td><input size=5 type=text name=estimated_quote value=".$prop['estimated_quote']." >€<br> '+
		'<tr><td>Timeframe from paper to service<td><input type=text name=timeframe value=".$prop['timeframe']." size=5>days<br> '+
		'<tr><td>Quoted to customer <td><input type=text name=quoted value=".$prop['quoted']." size=5>€<br> '+
		'<tr><td>ORAC_id <td><input type=text name=ORAC_id size=5 value=".$prop['ORAC_id']." ><br> '+
		'<tr><td>ORAP_id <td><input type=text name=ORAP_id size=5 value=".$prop['ORAP_id']." ><br> '+
		'</table>';

	
	
	
	}
	else if(soption==41)
	{
		textdiv += ' form to specify booked date'+
		'<table> '+
		'<tr><td>Date booked with customer<td><input type=text size=15 name=date_install value=\"";
		if($prop['date_install']!="") echo $prop['date_install']; else echo date("Y-m-d H:i",
		mktime(9,0,0,date("m"),date("d")+3,date("Y")));
		echo "\"><br> '+
		'</table>';
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	else if(soption==50)
	{";

        if($prop['prop_id'] != 0 || $prop['prop_id'] != "")
        {
            $conexao_id_status_warning_50 = $mon3->query("SELECT * FROM `connections` where property_id=".$prop['prop_id']." AND type='".$prop['con_type']."'")->fetch_assoc();


            if($conexao_id_status_warning_50 != null)
            {
                $servicos_at = $mon3->query("SELECT * FROM `services` where connection_id=".$conexao_id_status_warning_50['id']." AND equip_id='".$conexao_id_status_warning_50['equip_id']."'");
                while($servico_at=$servicos_at->fetch_assoc())
                {
                    // DESATIVAR OS SERVICOS PARA CRIAR UM NOVO SERVIÇO

                    $servico_id = $servico_at['id'];

                    $date_end_services = $mon3->query("SELECT * FROM services WHERE id=".$servico_id)->fetch_assoc();

                    if($date_end_services['date_end'] == '0000-00-00')
                    {
                        $warn_at_serv .= "<font color=red>These Services on this connection number ".$conexao_id_status_warning_50['id']. " are ativated. Please deactivated one of services</font>";
                        break;
                    }
                }
            }
            else
            {
                $warn_at_serv = "<font color=red>Not Exists connections on property number ".$prop['prop_id']."</font>";
            }
        }
        else
        {
            $warn_at_serv = "<font color=red>Not Exists properties</font>";
        }


        echo "var warn = '<font color=red>".$warn_at_serv."</font>'; ";
        echo "textdiv += ' form to activate services'+
		'<table>";

        if($prop['is_reconnection'] == 1)
            {
                $conexao=$mon3->query("SELECT * FROM connections where property_id = ".$prop['prop_id']." AND type='".$prop['con_type']."'")->fetch_assoc();


            }

        $eq_id = "";

        if($prop['prop_id'] != 0 || $prop['prop_id'] != "")
        {
            echo "<input type=hidden id=prop_id name=prop_id value=".$prop['prop_id'].">";
        }

        if($conexao != null)
        {
            echo "<input type=hidden id=con_id name=con_id value=".$conexao['id'].">";

            if($conexao['equip_id'] != "")
            {
                // GPON &  FWA
                $eq_id = $conexao['equip_id'];
            }
            else
                {
                    // GPON
                    if($prop['con_type'] == "GPON")
                        {
                            $eq_id = "ZNTS";
                        }
                    // FWA
                    if($prop['con_type'] == "FWA")
                        {
                            $eq_id = "aabbccddeeff";
                        }


                }
        }


        if($prop['is_reconnection'] == 1)
            {
                $l = $prop['con_type'];

                echo "<tr><td>Stay Equipment? <input type=checkbox id=stay_ont name=stay_ont onchange=\"checkbox_equip_rec(this, \'$l\')\">";
            }

    if($conexao != null)
    {
        echo "<tr><td>Connection <font color=#663399>".$conexao['id']."</font>";
    }

    echo "<tr><td>Type<td> <span id=con>".$prop['con_type']."</span>";
    if($prop['con_type']=="") echo "GPON?(please select type on status 30)";


    echo "';";


    if($prop['con_type']=='GPON' || $prop['con_type']=='')
    {
        $modelo = $mon3->query("SELECT * FROM ftth_ont WHERE fsan = '".$eq_id."'")->fetch_assoc();
        if($modelo != null)
        {
            $olt_id = $modelo['olt_id'];
        }
        else
        {
            $olt_id = 1;
        }
    echo"
            textdiv += '<tr><td>ONT FSAN<td><input type=text name=fsan id=fsan size=20 value=".$eq_id." oninput=equipConnectionAssoc(this.value)><span id=equip_conn_assoc></span><input type=hidden name=equip_assoc_not id=equip_assoc_not value=0>'+	
            '<tr><td>CPE model<td><select name=model id=models>		</select>' +
            
            '<tr><td>OLT<td><select name=olt_id id=olt_id onchange=\"updatepon(this.options[this.selectedIndex].value); updatevlan(this.options[this.selectedIndex].value)\"> ";
            $olts=$mon3->query("select * from ftth_olt");
            while($olt=$olts->fetch_assoc()){ echo "<option value=".$olt['id'];
            if($olt['id']==$olt_id) echo " selected ";
            echo ">".$olt['name']."</option>";}



            echo"</select>";


            echo "<tr><td>PON<td><select id=pons name=pon >";
            $pons=$mon3->query("select card,pon,name from ftth_pons where olt_id=".$olt_id." order by name ");
            while($pon=$pons->fetch_assoc())
            {
                echo "<option value=".$pon['card']."-".$pon['pon'].">".$pon['card']."-".$pon['pon']." - ".$pon['name'];
            }
            echo "</select> ';   ";
    }

    elseif($prop['con_type']=='FWA')
    {
        $modelo = $mon3->query("SELECT * FROM fwa_cpe WHERE mac = '".$eq_id."'")->fetch_assoc();
        if($modelo != null)
        {
            $antenna = $modelo['antenna'];
        }
        else
        {
            $antenna = 1;
        }

        echo"		
            textdiv += '<tr><td>FWA cpe MAC<td><input type=text name=fsan id=fsan size=10 value=".$eq_id." oninput=equipConnectionAssoc(this.value)><span id=equip_conn_assoc></span><input type=hidden id=equip_assoc_not name=equip_assoc_not>'+	
            '<tr><td>CPE model<td><select name=model id=models>		</select>' +
            '<tr><td>FWA antenna<td><select name=antenna id=antenna> ";

            $antennas=$mon3->query("select * from fwa_antennas");
            while($antenna=$antennas->fetch_assoc()){ echo "<option value=".$antenna['id'];
            if($antenna['id']==$antenna) echo " selected ";
            echo ">".$antenna['name']."</option>";}


            echo"</select>';	";


    }
    else
    {
        echo" textdiv += '<br> Please select connection type at status 30	';";
    }







    echo "textdiv += ' <tr><td><br>Services'+	


    '<tr><td>TV service<td><select name=tv><option value=0 "; if($prop['tv']==0) echo "selected"; echo ">no TV</option><option value=AMLA "; if($prop['tv']=="AMLA") echo "selected"; echo ">AMLA</option><option value=NOWO "; if($prop['tv']=="NOWO") echo "selected"; echo ">NOWO</option></select>'+	
    
        
    
    
    
    
    '<tr><td>Internet service<td><select name=internet_prof><option value=0 "; if($prop['internet_prof']==0) echo "selected"; echo ">no internet</option>";


    $intservices=$mon3->query("select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down");
    while($serv=$intservices->fetch_assoc())
    {
        echo "<option value=".$serv['id'];
        if($prop['internet_prof']==$serv['id']) echo " selected";
        echo "> ".$serv['name']."</option>";
    }


    echo "</select>'+	
    'Fixed ip:<select name=fixed_ip> <option ";
    if($prop['fixed_ip']=="") echo "selected"; echo " value=>no fixed ip</option>";
    $refs=$mon3->query("select ip from int_fixed_ips where in_use!=1 order by ip"); while($ref=$refs->fetch_assoc()){ echo "<option "; if($prop['fixed_ip']==$ref['ip']) echo "selected"; echo " value=".$ref['ip'].">".$ref['ip']."</option>";} echo " </select>'+
    
    '<tr><td>router mode<td><select name=is_router > <option value=1>Router</option> <option value=0>Bridge on eth1</option></select>       <br> '+	
    
    '<tr><td>vlan<td><select id=vlans name=vlan >";
    $vlans=$mon3->query("select vlan,description,total_dynamic_ips from int_vlans where olt_id=".$prop['olt_id']." ");
    while($vlan=$vlans->fetch_assoc())
    {
        $inuse=$mon3->query("select count(name) from service_attributes where name=\"vlan\" and value=\"".$vlan['vlan']."\"  ")->fetch_assoc();
        echo "<option value=".$vlan['vlan'].">".$vlan['description']." - ".$inuse['count(name)']." of ".$vlan['total_dynamic_ips'];
    }
    echo "</select>  '+
    '<tr><td>Wifi<td><select name=wifi><option value=1 selected>enabled</option><option value=0>disabled</option></select> '+	
    '<tr><td>Wifi SSID<td><input type=text name=wifi_ssid size=10 value=Lazer_".explode(" ",$prop['address'])[0]."><br> '+	
    '<tr><td>Wifi passwd<td><input type=text name=wifi_key size=10 value=lzr".substr($refc['ref'],3,3).strtolower(substr($refc['ref'],0,3))."> <tr><td><br> '+	
    
    
    
    
    
    
    
    
    '<tr><td>phone line 1<td><select name=phone1> <option "; if($prop['phone1']=="") echo " selected "; echo " value= >no line</option>";
    $refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
    while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'];
    if($prop['phone1']==$ref['phone_number']) echo " selected ";
    echo ">".$ref['phone_number']."</option>";}
    echo " </select><br>'+
    
    '<tr><td>phone line 2<td><select name=phone2> <option "; if($prop['phone2']=="") echo " selected "; echo " value= >no line</option>";
    $refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
    while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'];
    if($prop['phone2']==$ref['phone_number']) echo " selected ";
    echo ">".$ref['phone_number']."</option>";}
    echo " </select><br>'+
    
'<tr><td><br>'+

    
    
    
    

    '</table>'

    
    ;


}




else if(soption==51)
{
    textdiv += ' form to close jobsheet'+
    '<table>'+
    '<tr><td>Job sheet id<td><input type=text name=installation_job_id size=10><br> '+	
    '<tr><td>technician<td><select name=technician>";
            $refs=$mon3->query("select username from users where is_tech=1 order by username");
            while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['username'].">".$ref['username']."</option>";}
    echo "</select><br> '+
    '</table>'
    ;

}









else if(soption==60)
{
    textdiv += ' form to close jobsheet and evaluate the connection'+
    '<table>'+
    '<tr><td>Job sheet id<td><input type=text name=installation_job_id size=10><br> '+	
    '<tr><td>technician<td><select name=technician> <option selected></option>";
            $refs=$mon3->query("select username from users where (is_tech=1 or is_subcont=1) order by username"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['username'].">".$ref['username']."</option>";}
    echo "</select><br> '+	
    '<tr><td>NPS score<td><select name=NPS_score>";
    for($i=0;$i<11;$i++)echo "<option value=$i>$i</option>";
    echo"</select> <br> '+	
    '<tr><td>Install manager score<td><select name=manager_score>";
    for($i=0;$i<11;$i++)echo "<option value=$i>$i</option>";
    echo"</select><br> '+	
    '<tr><td>speedtest result?<td><input type=text name=speedtest size=10> '+
    '<tr><td>has pictures?<td><input type=checkbox name=has_pictures size=10><br> '+
    '<tr><td>picture1<td><input type=file name=\"filea\" ><br> '+
    '<tr><td>picture2<td><input type=file name=\"fileb\" ><br> '+	
    '<tr><td>picture3<td><input type=file name=\"filec\" ><br> '+	
    '<tr><td>picture4<td><input type=file name=\"filed\" ><br> '+	
    '</table>'

    
    ;

}
else
{
    textdiv='';
}








document.getElementById('additionalset').innerHTML = textdiv;

$('#warning_services').html(warn);


    updatecpe('";
        if($prop['con_type']=='') echo "GPON";
        else echo $prop['con_type'];

        echo "','".$prop['model']."'); 





}


</script>
";

        if($prop['is_reconnection'] == 1)
        {
            $st = 30;
        }
        else
        {
           $st = 31;
        }

        //echo $localuser['is_plan']." ".$localuser['is_helpdesk'];


echo "
<b>status: </b><br> <select id=idstatus name=status onchange=\"aditionalstatus()\">


<option value=\"0\"
"; if ($prop['status']==0) echo "selected"; echo "
>0- waiting for analisis </option>




<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Viability </option>

<option value=1 
"; if ($prop['status']==1) echo "selected";
if ( $localuser['is_plan']==0) echo " disabled ";
echo "
>1- viable, no costs to customer </option>

<option value=2 
"; if ($prop['status']==2) echo "selected";
if ( $localuser['is_plan']==0) echo " disabled "; echo "
>2- viable, with costs to customer, see estimate and timeframe</option>

<option value=3
"; if ($prop['status']==3) echo "selected";
if ( $localuser['is_plan']==0) echo " disabled "; echo "
>3- not viable - out of network </option>

<option value=4 
"; if ($prop['status']==4) echo "selected";
if ( $localuser['is_plan']==0) echo " disabled "; echo "
>4- not viable - no infrastructures </option>

<option value=5 
"; if ($prop['status']==5) echo "selected";
if ( $localuser['is_plan']==0) echo " disabled "; echo "
>5- Incorrect address or coordenates </option>


<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Specific Proposals</option>

<option value=6 
"; if ($prop['status']==6) echo "selected";
if ( $localuser['is_plan']==0) echo " disabled ";
echo "> 6- Special project-CTO and sales to present proposal</option>

<option value=7 
"; if ($prop['status']==7) echo "selected";
if ( $localuser['is_plan']==0 || $prop['status']<30) echo " disabled ";
echo "> 7 - Property Reconnection</option>

<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Customer Decision</option>
<option value=9 
"; if ($prop['status']==9) echo "selected";
if ($prop['status']==0 || ($prop['status']!=3 && $prop['status']!=4) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>9- Customer is notified, Not possible </option>


<option value=10 
"; if ($prop['status']==10) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>10- Customer is notified, waiting for reply </option>

<option value=11 
"; if ($prop['status']==11) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>11- Not accepted by customer  - note with justification  </option>

<option value=12 
"; if ($prop['status']==12) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>12- Accepted by customer, collecting paperwork</option>

<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled>  Quote for install costs(optional)</option>


<option value=13 
"; if ($prop['status']==13) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>13- paperwork ready, tech dep. to quote final price </option>

<option value=14 
"; if ($prop['status']==14) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>14- paperwork ready, quote ready, see notes </option>
<option value=15 
"; if ($prop['status']==15) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>15- Customer is notified about quote, waiting for reply </option>

<option value=19 
"; if ($prop['status']==19) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>19- Not accepted by customer  - note with justification </option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled>Internal process</option>

<option value=20 
"; if ($prop['status']==20) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) || $prop['is_reconnection'] == 1) echo " disabled "; echo "
>20- All approved, paperwork ready, to be insert into system </option>


<option value=21 
"; if ($prop['status']==21) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==1) echo " disabled "; echo "
>21- On hold until paperwork is completed </option>


<option value=29 
"; if ($prop['status']==29) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==1) echo " disabled "; echo "
>29- Contract in the system, but waiting for payment of the installation costs </option>



<option value=\"\" disabled></option>
<option value=\"0\" disabled> Technical dep.</option>

<option value=30 
"; if ($prop['status']==30) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==0) echo " disabled ";
echo "
>30- Contract into system, start process (sends email to customer)</option>

<option value=31 
"; if ($prop['status']==31) echo "selected";
if ($prop['status']<30 || ($localuser['is_admin']==0 && $localuser['is_plan']==0)) echo " disabled "; echo "
>31- Process started </option>


<option value=32
"; if ($prop['status']==32) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>32- Authorizations started- ORAC/ORAP infralobo, schematics, etc </option>

<option value=33
"; if ($prop['status']==33) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>33- Needs networking (note with scheduling/jobsheet id) </option>



<option value=\"\" disabled></option>
<option value=\"0\" disabled> Booking </option>

<option value=40 
"; if ($prop['status']==40) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>40- Network ready, can be booked w customer (sends email to customer)</option>

<option value=41 
"; if ($prop['status']==41) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>41- Booked with customer (notes with date and time) </option>

<option value=42 
"; if ($prop['status']==42) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>42- Infrastucture issues waiting on us to reschedule </option>

<option value=43 
"; if ($prop['status']==43) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>43- Infrastucture issues waiting on customer </option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled> Installation </option>
<option value=50 
"; if ($prop['status']==50) echo "selected";
if ($prop['status']<30 || ($localuser['is_plan']==0 && $localuser['is_helpdesk']==0)) echo " disabled "; echo "
>50- Installed, activate services (sends email to customer) </option>

<option value=51 
"; if ($prop['status']==51) echo "selected";
if ( $prop['status']<50 || $localuser['is_tmng']==0 ) echo " disabled "; echo "
>51- Job sheet closed</option>

<option value=60 
"; if ($prop['status']==60) echo "selected";
if ( $prop['status']<50 || $localuser['is_tmng']==0 ) echo " disabled "; echo "
>60- Closed (NPS scores, technician evaluation)</option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled> Other status </option>
<option value=99 
"; if ($prop['status']==99) echo "selected"; echo "
>99- Disabled - to be deleted (duplicates, upsells, stupid queries) </option>

";



echo "</select> <br><br>


<div id='additionalset'>


</div>



";






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
";










echo "
    var imgf = 'img/red_12px.png';
    var imgdf = 'img/black_12px.png';
    var imgc = 'img/blue_12px.png';
    var imgi = 'img/orange_12px.png';
    var imgl = 'img/yellow_12px.png';
    var imgpk = 'img/pink_12px.png';
    var imgbr = 'img/brown_12px.png';		
    var imgqp = 'img/qpink_12px.png';
    var imgqg = 'img/qgreen_12px.png';
    var imgqgy = 'img/qgray_12px.png';
    var imgcn = 'img/cian_12px.png';

";

include "cables.txt";
include "coverage_polygon.txt";
include "fats.txt";
include "fat_polygons.txt";




// active Leads

$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status <= 20 AND status != 19 AND status != 15 AND status != 11 AND status != 10  AND status != 9");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
$coord=explode(",",$pin['coords']);
$lon=$coord[1];
$lat=$coord[0];

echo"    var lead".$pin['id']." = new google.maps.Marker({
      position: {lat: $lat, lng: $lon },
      map: map,
      icon: imgl,
      ZIndex: 3,
      title: \"id:".$pin['id']." - ".$pin['address']."\",
      url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
    });
    google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
    window.open(this.url);
});
";
}


// not accepted / not possible
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND (status=19 OR status=11 OR status=9)");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
$coord=explode(",",$pin['coords']);
$lon=$coord[1];
$lat=$coord[0];

echo"    var lead".$pin['id']." = new google.maps.Marker({
      position: {lat: $lat, lng: $lon },
      map: map,
      icon: imgbr,
      ZIndex: 2,
      title: \"id:".$pin['id']." - ".$pin['address']."\",
      url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
    });
    google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
    window.open(this.url);
});
";
}






// installing
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status > 20 AND status < 50");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
$coord=explode(",",$pin['coords']);
$lon=$coord[1];
$lat=$coord[0];

echo"       var lead".$pin['id']." = new google.maps.Marker({
      position: {lat: $lat, lng: $lon },
      map: map,
      icon: imgi,
      ZIndex: 2,
      title: \"id:".$pin['id']." - ".$pin['address']."\",
      url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
    });
    google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//       window.location.href = this.url;
    window.open(this.url);
});
";
}




//fibre coax connection
$pinq="select properties.id,properties.address,properties.coords,connections.type from connections 
left join properties on properties.id=connections.property_id where properties.coords!=\"\" AND connections.date_end =\"0000-00-00\"";

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
      ZIndex: 1,
      icon: ";

      if($pin['type']=="GPON"){echo "imgf";}elseif($pin['type']=="COAX"){echo "imgc";}
      elseif($pin['type']=="FWA"){echo "imgcn";}else{echo "imgdf";}


      echo",
      title: \"".$pin['address']."\",
      url: \"index.php?props=1&propid=".$pin['id']."\"
    });
    google.maps.event.addListener(pin".$pin['id'].", 'click', function() {
//       window.location.href = this.url;
    window.open(this.url);
});
";
}














echo"



google.maps.event.addListener(map, \"rightclick\", function(event) {
var lat = event.latLng.lat();
var lng = event.latLng.lng();
// populate yor box/field with lat, lng
alert(\"Lat=\" + lat + \"; Lng=\" + lng);
});


}











</script>
<script async defer
src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
</script>
<a href=https://www.google.com/maps/search/?api=1&query=".$coordlat.",".$coordlng." target=_blank>open in maps</a>



<tr><td colspan=2>
<b>Notes: </b><br> ".$prop['notes']." <br> <br> 

<b>Add note:</b> <br><textarea name=notes cols=90 rows=5></textarea>
<tr><td colspan=2 align=center><br> <br> <input type=submit name=supdatelead value=update>	
</form></table>	<br><br>


";


            $l = $_GET['lead_id'];
            $ld = $mon3->query("select is_reconnection, is_changeover from property_leads where id=$l;")->fetch_assoc();
            //var_dump($ld);

            if($ld['is_changeover'] == 0 && $ld['is_reconnection'] == 1)
                {
                    ?>
                        <script>
                            $("#conexao_changeOVER").css('display', 'none');
                            $("#conexao_RECONNECTION").css('display', 'block');

                        </script>
                    <?php
                }
            elseif($ld['is_changeover'] == 1 && $ld['is_reconnection'] == 0)
                {
                    ?>
                        <script>
                            $("#conexao_changeOVER").css('display', 'block');
                            $("#conexao_RECONNECTION").css('display', 'none');
                        </script>
                    <?php
                }

            elseif($ld['is_changeover']== 0 && $ld['is_reconnection'] == 0)
                {
                    ?>
                        <script>
                            $("#conexao_changeOVER").css('display', 'none');
                            $("#conexao_RECONNECTION").css('display', 'none');
                        </script>
                    <?php
                }

if($_FILES['randfile'])
{
$countfiles = count($_FILES['randfile']['name']);

for($i=0;$i<$countfiles;$i++){
    if(file_exists($_FILES['randfile']['tmp_name'][$i]))
    {
        $ext=explode(".",$_FILES['randfile']['name'][$i]);
        echo uploadfile("randfile",$mon_leads.$lead_id."/", date("Y-m-d_His")."_".
            $localuser['username'].$i.".".strtolower($ext[sizeof($ext)-1]),0,$i);
    }
}
}

//echo $_POST['file_rem'];

if($_POST['file_rem'])
{
$rem = $_POST['file_rem'];

$val = remove_file($rem, $mon_leads);

echo $val;

}


echo"
<table width=900px><tr><td>
Files:<br>
<tr><td colspan=2 align=center>
<table border=0><tr>";
if(file_exists($mon_leads.$lead_id))
{
$files1 = scandir($mon_leads.$lead_id);
$i=0;
foreach($files1 as $file1){
if(substr($file1,0,1)!=".")
{
    $i++;
    if($i%3==0)
        echo"<tr>";

    //echo '<form name="removeFILE" method="post" enctype="multipart/form-data" action=index.php?propleads=1&lead_id="'.$lead_id.'">';
    echo " <td align=center> 
    <form name=addrandfile method=post action=index.php?propleads=1&lead_id=".$lead_id.">
    <a href=leads/".$lead_id."/".$file1."><input type=hidden  name=file_rem value=leads/".$lead_id."/".$file1." />";
    if(strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpeg")
        echo "<img src=leads/".$lead_id."/".$file1." height=100px> <br> $file1   ";
    else
        echo "<img src=img/file.png height=100px> <br> $file1 ";
    echo "</a> &nbsp; <br><br>
    <input type='submit' name='removeFILE_lead' id='removeFILE_lead' value='Remove File' />
    </form>";
}
}

echo "</table>";
}else echo "none<br>";





echo"
<tr><td colspan=2 align=center><br><br><b>upload new file(.jpg or .pdf)</b><br>
<form name=addrandfile method=post enctype=\"multipart/form-data\" action=index.php?propleads=1&lead_id=".$lead_id.">
<label for=fileInput> 
<img id=icon´ height=100px src=\"img/upload.png\" style=\"cursor: pointer;\">
</label>
<input type=file name=randfile[] accept=\".pdf,image/jpeg\" id=fileInput multiple style=\"display:none;\" onchange=\"this.form.submit()\">
</form>



";

?>
</table>
<table>
<?php



$prop_point=$mon3->query("select *, ST_AsText(point) as Coord from property_leads where id=$lead_id;")->fetch_assoc();

$prop_point = remove_element_array($prop_point, "point");

echo "<br><br> <br><br><br><br>Dump DB";
;

foreach($prop_point as $key => $row) {
    echo "<tr>";
        echo "<td>" . $key . "</td>";
        echo "<td>" . $row . "</td>";
    echo "</tr>";
}
echo "</table>

</table>
";



}


///////////// ADD Property leads ///////////////////////
elseif($_GET['propleadsadd']!=0)
{

echo" Add new property Lead<br><br>


";
?>
<script>
function validateForm() {
var formready=0;

var nm = document.forms["addproplead"]["address"].value;
if (nm == "")
{
//        alert("Address must be filled out, make sure it matches coordinates");
    document.getElementById("divaddr").innerHTML="Address: <font color=red>*</font>";
    document.forms["addproplead"]["addpropleadsubm"].disabled=true;
}
else
{
    document.getElementById("divaddr").innerHTML="Address: <font color=green>*</font>";
    formready+=1;
}



nm = document.forms["addproplead"]["coord"].value;
var filter =/^[(]?([0-9\.])+\,[\s]?[-]?([0-9\.])+[)]?$/;
if (!filter.test(nm))
{
    document.getElementById("divcoords").innerHTML="Coords: <font color=red>*</font>";
    document.forms["addproplead"]["addpropleadsubm"].disabled=true;
}
else
{
    document.getElementById("divcoords").innerHTML="Coords: <font color=green>*</font>";
    formready+=1;
}



nm = document.forms["addproplead"]["name"].value;
if (nm == "")
{
    document.getElementById("divname").innerHTML="Name: <font color=red>*</font>";
    document.forms["addproplead"]["addpropleadsubm"].disabled=true;
}
else
{
    document.getElementById("divname").innerHTML="Name: <font color=green>*</font>";
    formready+=1;
}



nm = document.forms["addproplead"]["email"].value;
filter =/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
if (!filter.test(nm))
{
    document.getElementById("divemail").innerHTML="Email: <font color=red>*</font>";
    document.forms["addproplead"]["addpropleadsubm"].disabled=true;
}
else
{
    document.getElementById("divemail").innerHTML="Email : <font color=green>*</font>";
    formready+=1;
}





nm = document.forms["addproplead"]["phone"].value;
filter =/^[+]?([0-9])+$/;
if (!filter.test(nm))
{
    document.getElementById("divphone").innerHTML="Phone: <font color=red>*</font>";
    document.forms["addproplead"]["addpropleadsubm"].disabled=true;
}
else
{
    document.getElementById("divphone").innerHTML="Phone : <font color=green>*</font>";
    formready+=1;
}

















if(formready==5)
{
    document.forms["addproplead"]["addpropleadsubm"].disabled=false;
    return true;
}
else
{
    document.forms["addproplead"]["addpropleadsubm"].disabled=true;
    return false;
}






}
</script>










<?php









if($_POST['addpropleadsubm'])
{
$address=mysqli_real_escape_string($mon3, $_POST['address']);
$freg=mysqli_real_escape_string($mon3, $_POST['freg']);
$coords=trim(mysqli_real_escape_string($mon3, $_POST['coords']), '()');
$name=mysqli_real_escape_string($mon3, $_POST['name']);
$email=mysqli_real_escape_string($mon3, $_POST['email']);
$phone=mysqli_real_escape_string($mon3, $_POST['phone']);
$agent=mysqli_real_escape_string($mon3, $_POST['agent']);

$p_id=mysqli_real_escape_string($mon3, $_POST['property']);

if($address!="")
{

//		echo $address.$freg.$coords." o ".$name." m ".$agent."kkkk<br>";
    $gg=$mon3->query("insert into property_leads (id,address,freguesia,coords,name,email,phone,agent_id,status,prop_id,date_lead,date_modified,created_by) values(
    \"\",
    \"".$address."\", 
    \"".$freg."\", 
    \"".$coords."\", 
    \"".$name."\", 
    \"".$email."\",
    \"".$phone."\",		
    \"".$agent."\",
    \"0\",
    \"".$p_id."\",
    \"".date("Y-m-d")."\",
    \"".date("Y-m-d")."\",
    \"".$_SERVER['PHP_AUTH_USER']."\"
    ) ;");
echo mysqli_error($mon3);
    $lead_id=$mon3->insert_id;



    $assunto="Lazer - Lead $lead_id ($address) created";
    $corpo= "
    <html> Dear ".$localuser['name']."<br><br>
    Your lead <a href=https://mon.lazertelecom.com/?propleads=1&lead_id=$lead_id>$lead_id</a> was just created.<br><br>
    Please wait while the planing team analyzes your request
    
    <br><br>Regards,<br>The System</html>";
    //enviamail($localuser['email'].";".$planing_email,$assunto, $corpo);

    //save
    echo "<br><font color=green>saved</font><br>";
}

}

//document.getElementById('addpropleadsubm').innerText='saving'; document.getElementById('addpropleadsubm').disabled=true;  return true;
//onsubmit=\"this.addpropleadsubm.disabled=true; return true;\"


echo "
<table><tr>
<form name=addproplead action=?propleads=1&propleadsadd=1 method=post 

>

<tr><td>	<div id=divaddr>Adress: <font color=red>*</font></div>
<td> <input type=text name=address size=60 id=address onkeyup=\"return validateForm()\"> <br>



<tr><td>Freguesia:<td><select name=freg id=freg>";
$fregs=$mon3->query("select * from freguesias where concelho=176;");
while($frega=$fregs->fetch_assoc())
{
echo "<option value=".$frega['id'];
if ($frega['id']==1160)
    echo " selected";
echo ">".$frega['freguesia']."</option>";
}
echo"</select>
<tr><td>Concelho:<td><select name=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\">";
$concs=$mon3->query("select * from concelhos  order by distrito,concelho;");
//where pais=\"PORTUGAL\"
while($conca=$concs->fetch_assoc())
{
echo "<option value=".$conca['id'];
if ($conca['id']==176)
    echo " selected";
echo ">".$conca['distrito']." - ".$conca['concelho']."</option>";
}
echo"</select>
<tr><td>Country:
<td><select name=country class=\"country\"onchange=updateconcelhosep()>
<option value=PORTUGAL selected>Portugal</option>
<option value=SPAIN>Spain</option>
<option value=\"UNITED KINGDOM\">United Kingdom</option>
</select>





<tr><td><div id=divcoords>Coords: <font color=red>*</font></div> 
<td><input type=text name=coords id=coord size=40 onchange=\"return validateForm()\">
<a href=# onclick=gpslink('l')>GPS</a> 


<tr><td><br>

<tr><td><div id=divname>Name: <font color=red>*</font></div><td>
<input type=text name=name size=60 id=nname onkeyup=\"return validateForm()\">

<tr><td><div id=divemail>Email: <font color=red>*</font></div> <td>
<input type=text name=email id=email onkeyup=\"return validateForm()\">

<tr><td><div id=divphone>Phone: <font color=red>*</font></div> <td>
<input type=text name=phone id=phone onkeyup=\"return validateForm()\">
<br>



<tr><td><br>Agent:<td><br>
<select name=agent >";
$owners=$mon3->query("select id,name,email,fiscal_nr from customers where is_agent=1 order by name ");
while($owns=$owners->fetch_assoc())
{
echo "<option value=\"".$owns['id']."\" ";
if($owns['id']==0) echo " selected ";
echo "> ".$owns['name']." #".$owns['fiscal_nr']."</option>";
}
echo"</select>


<tr><td><br>Propriety:<td><br>

<select name=property >
";
$properties=$mon3->query("select id,ref from properties");
while($property=$properties->fetch_assoc())
{
echo "<option value=\"".$property['id']."\" ";
if($property['id']==0)
    echo "selected ";
echo"\> ".$property['id']." - ".$property['ref']."</option>";
}
echo "
</select>



<tr><td> <br>
<tr><td colspan=2 align=center><input type=submit name=addpropleadsubm id=addpropleadsubm value=\"add new lead\" disabled=true><br>
</form>




<td>


";


}















/////////EDIT LEAD ID/////////////////
elseif($_GET['propleadsedit']!=0)
{
$leadid=mysqli_real_escape_string($mon3, $_GET['propleadsedit']);
echo" Edit property Lead <a href=?propleads=1&lead_id=$leadid>$leadid</a><br><br>";
$leadq=	$mon3->query("select * from property_leads where id=$leadid");
if($leadq->num_rows==1)
{
$lead=$leadq->fetch_assoc();

if($lead['created_by']!=$localuser['username'])
{
echo "You cannot edit a lead you do not manage<br><br>
<a href=?propleads=1&lead_id=$leadid> back to lead $leadid</a>";
}
else
{



if($_POST['editpropleadsubm'])
{
$address=mysqli_real_escape_string($mon3, $_POST['address']);
$freg=mysqli_real_escape_string($mon3, $_POST['freg']);
$coords=trim(mysqli_real_escape_string($mon3, $_POST['coords']), '()');
$name=mysqli_real_escape_string($mon3, $_POST['name']);
$email=mysqli_real_escape_string($mon3, $_POST['email']);
$phone=mysqli_real_escape_string($mon3, $_POST['phone']);
$agent=mysqli_real_escape_string($mon3, $_POST['agent']);
$p_id=mysqli_real_escape_string($mon3, $_POST['property']);



$changed=0;
if($address!="" && $name!="" && $coords!="" && ($email!="" || $phone!="") && $p_id != "")
{


    if($address!=$lead['address'])
    {
        $insq.=",address=\"$address\" ";
        $notes.=" address changed from #".$lead['address']."# to #$address#;";
        $changed=1;
    }
    if($freg!=$lead['freguesia'])
    {
        $insq.=",freguesia=\"$freg\" ";
        $notes.=" freguesia changed from #".$lead['freguesia']."# to #$freg#;";
        $changed=1;
    }
    if($coords!=$lead['coords'])
    {
        $insq.=",coords=\"$coords\",status=0 ";
        $rstatus=1;
        $notes.=" coords changed from #".$lead['coords']."# to #$coords# , status reset back to 0;";
        $changed=1;
    }
    if($name!=$lead['name'])
    {
        $insq.=",name=\"$name\" ";
        $notes.=" name changed from #".$lead['name']."# to #$name#;";
        $changed=1;
    }
    if($email!=$lead['email'])
    {
        $insq.=",email=\"$email\" ";
        $notes.=" email changed from #".$lead['email']."# to #$email#;";
        $changed=1;
    }
    if($phone!=$lead['phone'])
    {
        $insq.=",phone=\"$phone\" ";
        $notes.=" phone changed from #".$lead['phone']."# to #$phone#;";
        $changed=1;
    }
    if($agent!=$lead['agent_id'])
    {
        $insq.=",agent_id=\"$agent\" ";
        $notes.=" agent changed from #".$lead['agent_id']."# to #$agent#;";
        $changed=1;
    }
    if($p_id!=$lead['prop_id'])
    {
        $insq.=",prop_id=\"$p_id\" ";
        $notes.=" Property ID changed from #".$lead['prop_id']."# to #$p_id#;";
        $changed=1;
    }

    if($changed==1)
    {
        $notes=date("Y-m-d H:i:s")." ". $localuser['username']." modified details:".$notes." <br>". $lead['notes'];

//		echo $address." o ".$freg." o ".$coords." o ".$name." o ".$agent."<br>";
    echo "$insq <br> $notes <br>";
    $gg=$mon3->query("update property_leads set 
    ".substr($insq,1).",date_modified=\"".date("Y-m-d")."\",notes=\"".$notes."\" where id=$leadid;");
    echo mysqli_error($mon3);

    $assunto="Lazer - Lead $lead_id ($address) edited";
    $corpo= "
    <html> Dear ".$localuser['name']."<br><br>
    Your lead <a href=https://mon.lazertelecom.com/?propleads=1&lead_id=$leadid>$leadid</a> was modified.<br><br>
    $notes<br>

    
    <br><br>Regards,<br>The System</html>";
    if($rstatus==1)
    $emails=$localuser['email'].";".$planing_email;
    else
    $emails=$localuser['email'];



    //enviamail($emails,$assunto, $corpo);

    //save
    echo "<br><font color=green>saved</font><br>";
}
else echo "<br><font color=orange>nothing changed</font><br>";
}
}

$lead=$mon3->query("select * from property_leads where id=$leadid")->fetch_assoc();
echo "
<table><tr>
<form name=addproplead action=?propleads=1&propleadsedit=$leadid method=post>
<tr><td>Adress:<td> <input type=text name=address size=60 id=address value=\"".$lead['address']."\"> <br>
<input type=hidden name=id size=60 id=address value=\"".$lead['address']."\">
<tr><td>Freguesia:<td><select name=freg id=freg>";
$freg=$mon3->query("select * from freguesias where id=".$lead['freguesia'].";")->fetch_assoc();
$fregs=$mon3->query("select * from freguesias where concelho=".$freg['concelho'].";");

while($frega=$fregs->fetch_assoc())
{
echo "<option value=".$frega['id'];
if ($frega['id']==$lead['freguesia'])
    echo " selected";
echo ">".$frega['freguesia']."</option>";

}

echo"
</select>
<tr><td>Concelho:<td><select name=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\">";
$concs=$mon3->query("select * from concelhos order by distrito,concelho;");
//where pais=\"PORTUGAL\"
while($conca=$concs->fetch_assoc())
{
echo "<option value=".$conca['id'];
if ($conca['id']==$freg['concelho'])
    echo " selected";
echo ">".$conca['distrito']." - ".$conca['concelho']."</option>";
}

echo"
</select>
<tr><td>Country:
<td><select name=country class=\"country\"onchange=updateconcelhosep()>
<option value=PORTUGAL selected>Portugal</option>
<option value=SPAIN>Spain</option>
<option value=\"UNITED KINGDOM\">United Kingdom</option>
</select>



<tr><td>coords: <td><input type=text name=coords id=coord size=40  value=\"".$lead['coords']."\">
<a href=# onclick=gpslink('lp')>GPS</a> 


<tr><td><br>

<tr><td>Owner name:<td> <input type=text name=name size=60  value=\"".$lead['name']."\">

<tr><td>email:<td> <input type=text name=email  value=\"".$lead['email']."\">

<tr><td>Phone:<td> <input type=text name=phone  value=\"".$lead['phone']."\">


<br>

<tr><td><br>Agent:<td><br>
<select name=agent >

";
$owners=$mon3->query("select id,name,email,fiscal_nr from customers where is_agent=1 order by name ");
while($owns=$owners->fetch_assoc())
{
echo "<option value=\"".$owns['id']."\" ";
if($lead['agent_id']==$owns['id'])
    echo "selected ";
echo"\> ".$owns['name']." #".$owns['fiscal_nr']."</option>";
}
echo"
</select>

<tr><td><br>Propriety:<td><br>

<select name=property >
";
$properties=$mon3->query("select id,ref from properties");
while($property=$properties->fetch_assoc())
{
echo "<option value=\"".$property['id']."\" ";
if($lead['prop_id']==$property['id'])
    echo "selected ";
echo"\> ".$property['id']." - ".$property['ref']."</option>";
}
echo "
</select>


<tr><td> <br>
<tr><td colspan=2 align=center><input type=submit name=editpropleadsubm value=\"edit lead\"><br>
</form>




<td>


";

}
}else{echo "lead not found";}
}









//Default - List leads and search ##############################################################################################






else{



if(isset($_GET['offset']))
    $offset=mysqli_real_escape_string($mon3, $_GET['offset']);
else
    $offset=0;

if(isset($_GET['filter']))
    $filter=mysqli_real_escape_string($mon3, $_GET['filter']);
else
    $filter="active";

if(isset($_GET['owner']))
    $owner=mysqli_real_escape_string($mon3, $_GET['owner']);
else
    $owner="all";

if(isset($_GET['status']))
    $sstatus=mysqli_real_escape_string($mon3, $_GET['status']);
else
    $sstatus="all";

if(isset($_GET['searchb']))
{
    $searchb=mysqli_real_escape_string($mon3, $_GET['searchb']);
}


$qwhere="";
if($searchb!="")
{
    $qwhere.= "AND (address LIKE '%".$searchb."%' or name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%') ";

}
else
{
    if($owner!="all" && $owner!="")
        $qwhere.=" AND created_by=\"$owner\" ";
    if(isset($filter))
    {
        if($filter=="active"){ $qwhere.=" AND status<50 AND is_active=1 ";}
    }
    if($sstatus!="all" && $sstatus!="")
        $qwhere.=" AND status=\"$status\" ";


}




echo"





<div id=mapl>

</div>

 <script>
function initMap() {


var uluru = {lat: 37.0642249, lng:-8.1128986};

var map = new google.maps.Map(
  document.getElementById('mapl'), {zoom: 12, center: uluru, mapTypeId: 'hybrid',gestureHandling: 'greedy'});
  
if (navigator.geolocation) {
navigator.geolocation.getCurrentPosition(function(pos) {
    map.setCenter({lat:pos.coords.latitude, lng:pos.coords.longitude});
}, function(error) {}
);
}


  


// geolocation marker

var loco = 'img/googlemapbluedot_30px.png';

var myloc = new google.maps.Marker({
clickable: false,
icon: loco,
shadow: null,
zIndex: 0,
map: map
});


function autoUpdate() {

if (navigator.geolocation) {
navigator.geolocation.getCurrentPosition(function(pos) {
    var me = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
        myloc.setPosition(me);
}, function(error) {
    // ...
});
}
else
{


//try requesting geoloc
navigator.geolocation.getCurrentPosition(function(pos) {
    var me = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
    myloc.setPosition(me);
}, function(error) {
    // ...
});

}




// Call the autoUpdate() function every 5 seconds
setTimeout(autoUpdate, 5000);
}

autoUpdate();











  
  
";


















echo "
    var imgf = 'img/red_12px.png';
    var imgdf = 'img/black_12px.png';
    var imgc = 'img/blue_12px.png';
    var imgi = 'img/orange_12px.png';
    var imgl = 'img/yellow_12px.png';
    var imgpk = 'img/pink_12px.png';
    var imgbr = 'img/brown_12px.png';		
    var imgqp = 'img/qpink_12px.png';
    var imgqg = 'img/qgreen_12px.png';
    var imgqgy = 'img/qgray_12px.png';
    var imgcn = 'img/cian_12px.png';

";




include "coverage_polygon.txt";
include "fats.txt";
include "fat_polygons.txt";


// active Leads
if(	$filter="all")
{
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere order by id");
echo mysqli_error($mon3);
}

else
{
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status <= 20 AND status != 19 AND status != 15 AND status != 11 AND status != 10  AND status != 9 AND status !=4 AND status != 3 order by id");
echo mysqli_error($mon3);
}


while($pin=$pins->fetch_assoc())
{
$coord=explode(",",$pin['coords']);
$lon=$coord[1];
$lat=$coord[0];

echo"    var lead".$pin['id']." = new google.maps.Marker({
      position: {lat: $lat, lng: $lon },
      map: map,";
if(in_array($pin['status'], array(3,4,9,10,11,15,19)))
{       echo "
        icon: imgbr, 
        ";
}
else
{       echo "
        icon: imgl, 
        ";
}
echo " 		
      ZIndex: 3,
      title: \"id:".$pin['id']." - ".$pin['address']."\",
      url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
    });
    google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
    window.open(this.url);
});
";
}

// installing
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status > 20 AND status < 50");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
$coord=explode(",",$pin['coords']);
$lon=$coord[1];
$lat=$coord[0];

echo"       var lead".$pin['id']." = new google.maps.Marker({
      position: {lat: $lat, lng: $lon },
      map: map,
      icon: imgi,
      ZIndex: 4,
      title: \"id:".$pin['id']." - ".$pin['address']."\",
      url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
    });
    google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//       window.location.href = this.url;
    window.open(this.url);
});
";
}




//fibre coax connection
$pinq="select properties.id,properties.address,properties.coords,connections.type from connections 
left join properties on properties.id=connections.property_id where properties.coords!=\"\" AND connections.date_end =\"0000-00-00\"";

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
      ZIndex: 1,
      icon: ";

      if($pin['type']=="GPON"){echo "imgf";}elseif($pin['type']=="COAX"){echo "imgc";}
      elseif($pin['type']=="FWA"){echo "imgcn";}else{echo "imgdf";}


      echo",
      title: \"".$pin['address']."\",
      url: \"index.php?props=1&propid=".$pin['id']."\"
    });
    google.maps.event.addListener(pin".$pin['id'].", 'click', function() {
//       window.location.href = this.url;
    window.open(this.url);
});
";
}














echo"



google.maps.event.addListener(map, \"rightclick\", function(event) {
var lat = event.latLng.lat();
var lng = event.latLng.lng();
// populate yor box/field with lat, lng
alert(\"Lat=\" + lat + \"; Lng=\" + lng);
});












    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };

    var infoWindow = new google.maps.InfoWindow();
//            infoWindow.setPosition(pos);
//            infoWindow.setContent('Location found.');
//            infoWindow.open(map);
        map.setCenter(pos);
      }, function() {
        handleLocationError(true, infoWindow, map.getCenter());
      });
    } 
    else 
    {
      
      handleLocationError(false, infoWindow, map.getCenter());
    }





















}





 function handleLocationError(browserHasGeolocation, infoWindow, pos) {

//       infoWindow.setPosition(pos);
//        infoWindow.setContent(browserHasGeolocation ?
//                              'Error: The Geolocation service failed.' :
//                              'Error: Your browser doesn\'t support geolocation.');
//        infoWindow.open(map);
  }

</script>";




echo"
<script async defer
src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
</script>




<img src=img/red_12px.png>-fibre <img src=img/blue_12px.png>-coax <img src=img/cyan_12px.png>-FWA <img src=img/yellow_12px.png>-leads <img src=img/orange_12px.png>-installing &nbsp;	<img src=img/qgreen_12px.png>-FAT

<br><br>
<div>
























    <br><br>
<div>
<form name=serachp method=get>
Search: <input type=text name=searchb Onkeyup=\"searchlead(this.value)\" ";


if(isset($_GET['searchb']))
{

    echo " value=\"$searchb\"";

    $qwhere= " where (address LIKE '%".$searchb."%' or name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%')";

}


echo">  
<input type=hidden name=propleads value=1>
</form>

";

if($searchb=="")
{
    $fil_op = 'all';
    if(isset($_GET['filt_op']))
    {
        $fil_op = $_GET['filt_op'];
    }

    echo "<div style=\"position:absolute; right:150px; top:750px; \">
    <form name=filt method=get> Filter: 
    <select name=filt_op onchange=this.form.submit()>
    <option value=active ";if($fil_op=="active"){ echo " selected"; $status="    status<60 and is_active=1 ";} echo" >active</option>
    <option value=all ";if($fil_op=="all") {echo " selected"; $status="   1";}  echo" >all leads</option>
    <option value=refer ";if($fil_op=="refer")
    {
        echo " selected";
        if($localuser['is_plan']==1){
            $status.=" OR status=0 OR status=6 OR status=7 OR status=13";
        }
        if($localuser['is_admin']==1){
            $status.=" OR status=20  OR status=31  OR status=40 ";
        }
        if($localuser['is_tmng']==1){
            $status.=" OR status=30 OR status=31 OR status=32 OR status=33 OR status=40 OR status=41 OR status=42 OR status=43 OR status=50 ";
        }
        if($localuser['is_sales']==1){
            $status= "   (".substr($status,3)." OR status=1 OR status=2 OR status=3 OR status=4 OR status=6 OR status=10 OR status=14 OR status=15 OR status=40 ) AND created_by=\"".$localuser['username']."\" ";
        }
    }
    echo" >require my att</option>
    

    </select>
    
    <input type=hidden name=propleads value=1>
    <input type=hidden name=owner value=$owner>
    
    </form>
    </div>";




        $qwhere=" where ".substr($status,3);
        //echo $qwhere;


    //echo $fil_op;


}





echo "


</div>
<br>

<div id=tablec>   
<table><tr> <th>id</th><th>address</th><th>name</th>
<th>
<form method=get>
<input type=hidden name=propleads value=1>
<input type=hidden name=filter value=$filter>
<select name=status onchange=this.form.submit()>";

if($sstatus=="all" || $sstatus=="")
{
    echo "<option value=all selected>status</option>";
}
else
{
    echo "<option value=all>status</option>";
    $qwhere.=" AND status=\"$sstatus\" ";
}

$statuss=$mon3->query("select distinct(status) from property_leads where is_active=\"1\" order by status ");
while($statusr=$statuss->fetch_assoc())
{
    echo " <option value=" . $statusr['status'];
    if ($statusr['status']==$sstatus)
    {
        echo " selected ";
    }


    echo "> ".$statusr['status']." </option>";
}

echo "</select> </form>	</th>

<th>date_in</th>
<th><form method=get>
<input type=hidden name=propleads value=1>
<input type=hidden name=filter value=$filter>
<select name=owner onchange=this.form.submit()>";

if($owner=="all" || $owner=="")
{
    echo "<option value=all selected>created by</option>";
}
else
{
    echo "<option value=all>created by</option>";
    $qwhere.=" AND created_by=\"$owner\" ";
}

$owners=$mon3->query("select distinct(created_by) from property_leads order by created_by");
while($ownerr=$owners->fetch_assoc())
{
    echo " <option value=" . $ownerr['created_by'];
    if ($ownerr['created_by']==$owner)
    {
        echo " selected ";
    }


    echo "> ".$ownerr['created_by']." </option>";
}

echo "</select> </form></th>";




$props=$mon3->query("select id,address,name,agent_id,status,date_lead,created_by,notes from property_leads ".$qwhere.
" order by status,date_modified desc limit ".$offset.",50 ");
$count=$mon3->query("select count(*) from property_leads ".$qwhere)->fetch_row();
while($value=$props->fetch_assoc())
{
    $notes=explode("<br>",$value['notes']);
    $notes=$notes[0];
    echo	"<tr><td><a href=?propleads=1&lead_id=".$value['id'].">  ".$value['id']."</a> </td>
        <td width=400px>".$value['address']. "</td>
        <td width=200px>".$value['name']. "
        <td align=center title=\"notes: $notes \">".$value['status']. "<br>".

            "<td>".$value['date_lead']. "
        <td>".$value['created_by']. "
        ";




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
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=0&filter=$filter&owner=$owner&filt_op=$fil_op>|<</a> ";
}
//print page -2
if($curpage>2)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage-3)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op>".($curpage-2) ."</a> ";
}
//print page -1
if($curpage>1)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage-2)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op>".($curpage-1) ."</a> ";
}
//print curpage

    echo " <b> $curpage </b> ";
//print page -1
if($curpage<$lastp)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op>".($curpage+1) ."</a> ";
}
if($curpage<$lastp-1)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage+1)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op>".($curpage+2) ."</a> ";
}

    if($curpage<$lastp)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($lastp-1)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op>>|</a> ";
}

}
echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results</div>";
}







