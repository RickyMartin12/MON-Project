<?php
// EMAILS DESATIVADOS
$mon_leads = MON_ROOT."/leads/";
$mon_prop = MON_ROOT."/properties/";



// EMAILS DA EMPRESA


?>
    <a href=?propleads=1&list_leads=1><img src=img/leads.png></a>
    <a href=?propleads=1&propleadsadd=1><img src=img/leadadd.png></a>
    <a href=?propleads=1&covmaps=1&fats=on&poly=on&cables=on&leads=on&customers=on><img src=img/maps.png></a>
    <a href=?props=1><img src=img/house.png></a>
    <a href=?custs=1&list_custs=1><img src=img/user.png></a>
    <h3>Property Leads  - If its not in leads, it will not happen</h3><br>

 

<?php




if($_GET['covmaps']==1){



include MON_ROOT."/includes/covmaps.php";

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
	<b>Lead id</b>: <span id=lead_id_edit>$lead_id</span>" ;





$prop=$mon3->query("select * from property_leads where id=$lead_id;")->fetch_assoc();
$agent=$mon3->query("select id,name from customers where id=\"".$prop['agent_id']."\" ")->fetch_assoc();
$created_by=$mon3->query("select name,email from users where username=\"".$prop['created_by']."\" ")->fetch_assoc();

echo "<br> created by: <span id=localuser_username>".$prop['created_by']."</span><br> <div id=warning_services></div> <div id=info_submit></div>";
// Editar as Informações das Leads (Informação da Lead) - Na base de dados - property_leads
if($prop['created_by']==$localuser['username'])
{echo "<a href=?propleads=1&propleadsedit=$lead_id><img src=img/leadedit.png></a><br>";}
echo "<br>";



if($_POST['supdatelead']!="")  //lead update
{
    ?>
    <script>
        var s = '';
    </script>
    <?php
    $succ = '';
    $error = '';
	$status=mysqli_real_escape_string($mon3, $_POST['status']);
    //$status=50;
    //echo $status;
	$notes=escapechars($_POST['notes']);
	$notesa=$prop['notes'];


	if($notes!="")
	{
        $dest_email = "ricardo.peleira@lazerspeed.com";

		$notesa=date("Y-m-d H:i:s")." ".$_SERVER['PHP_AUTH_USER']." added: ".$notes."<br>".$prop['notes'];

		$mon3->query("update property_leads set notes=\"$notesa\",date_modified=\"".date("Y-m-d")."\" where id=$lead_id;");

		$assunto="Lazer - Lead $lead_id (".$prop['address'].") new notes";
        $corpo="<html> <b>Server Test</b><br>";
		$corpo.= "
		 Dear Ricardo P<br><br>
		Your lead <a href=".MON_SERVER."?propleads=1&lead_id=$lead_id>$lead_id</a> has new notes.<br><br>
		Current status: $status <br><br> 
		
		Notes:<br>".$notesa
		."
		<br><br>Regards,<br>The System</html>";

	}
	if($status!="")
	{
		$notesa=date("Y-m-d H:i:s")." ".$_SERVER['PHP_AUTH_USER']." moved to status ".$status."<br>".$notesa;

		$update_leads_status = $mon3->query("update property_leads set status=\"$status\",date_modified=\"".date("Y-m-d")."\",notes=\"$notesa\" where id=$lead_id;");
		//echo $mon3->error;
        if($update_leads_status)
        {
            $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status."</font><br>";
        }
        else
        {
            $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status."</font><br>";
        }



		$assunto="Lazer - Lead $lead_id (".$prop['address'].") updated";
		$corpo= "
		<html> Dear".$created_by['name']."<br><br>
		Your lead <a href=".MON_SERVER."?propleads=1&lead_id=$lead_id>$lead_id</a> was updated.<br><br>
		Current status: $status <br><br> 
		
		<br><br>Regards,<br>The System</html>";


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

            if($_FILES["plan"]['tmp_name'][0])
            {
                //echo uploadfile("plan",$mon_leads.$lead_id."/", "plan_".time().".kmz",0,"unico");
                $var_plan = uploadfile("plan",$mon_leads.$lead_id."/", "plan_".time().".kmz",0,0);
            }

            if($_FILES["planz"]['tmp_name'][0])
            {
                //echo uploadfile("planz",$mon_leads.$lead_id."/", "planz_".time().".zip",0,"unico");
                $var_plan2 = uploadfile("planz",$mon_leads.$lead_id."/", "planz_".time().".kmz",0,0);
            }
            
            

            if($quoted == "")
            {
                $quoted_id = 0;
            }
            else
            {
                $quoted_id = $quoted;
            }

            

			$update_lead_status_0_4_14 = $mon3->query("update property_leads set date_viability=\"".date("Y-m-d")."\",
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
            quoted=\"$quoted_id\"
            where id=$lead_id;");

            if($status==1)
            {
                if($update_lead_status_0_4_14)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." on viability network, no costs to customer</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." on viability network, no costs to customer</font><br>";
                }
            }
            else if($status==2)
            {
                if($update_lead_status_0_4_14)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." on viability network, with costs to customer, see estimate and timeframe</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." on viability network, with costs to customer, see estimate and timeframe</font><br>";
                }
            }
            else if($status==3)
            {
                if($update_lead_status_0_4_14)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." not viability network, out of network</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." not viability network, out of network</font><br>";
                }
            }
            else if($status==14)
            {
                if($update_lead_status_0_4_14)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." paperwork ready, network ready</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." paperwork ready, network ready</font><br>";
                }
            }

            


                if($error != "")
                {
                    ?>
                        <script>
                            s += "<?php echo $error; ?>";
                            //$("#info_submit").html(s);
                        </script>
                    <?php
                }
                else
                {
                    ?>
                        <script>
                            s += "<?php echo $succ; ?>";
                        </script>
                    <?php
                }

                ?>
                <script>
                    $("#info_submit").html(s);
                </script>


                <?php

                $var_warn = $var_plan."<br>".$var_plan2

                ?>
                <script>
                    var var_s = "<?php echo $var_warn; ?>";
                    $('#warning_services').html(var_s);
                </script>
                <?php



		}

        // RECONNECTION - STATUS = 7


        if($status==7)
		{
				// Actualizar a PROP_ID da lead
                $refe_rec_7 = mysqli_real_escape_string($mon3, $_POST['refe_rec_7']);
                $con_type_rec = mysqli_real_escape_string($mon3, $_POST['con_type_rec']);

                $conn_id_prop_rec = mysqli_real_escape_string($mon3, $_POST['conn_id_prop_rec']);
                $is_rec = 1;
                $is_change_over = 0;

                $update_lead_status_7 = $mon3->query("update property_leads set con_type=\"$con_type_rec\",	
                prop_id=\"$refe_rec_7\",	
                is_changeover=\"$is_change_over\",
                is_reconnection=\"$is_rec\",
                lead_conn_id_rcn=\"$conn_id_prop_rec\"
			    where id=$lead_id;");

                
                
                

				
				if($con_type_rec == "DIA" || $con_type_rec == "DARKF" || $con_type_rec == "COAX")
                {
                    $update_lead_not_gpon_fwa = $mon3->query("update property_leads set status=\"0\",date_modified=\"".date("Y-m-d")."\",notes=\"$notesa\", is_reconnection=\"0\" where id=$lead_id;");
                }


                  if($update_lead_status_7)
                  {
                    $succ .= "<font color=green>Update Lead Number ".$lead_id." on property reconnection</font>";
                  }
                  else
                  {
                    $error .= "<font color=red>Error Update Lead Number ".$lead_id." on property reconnection</font>";
                  }

                    if($error != "")
                    {
                        ?>
                            <script>
                                s += "<?php echo $error; ?>";
                                //$("#info_submit").html(s);
                            </script>
                        <?php
                    }
                    else
                    {
                        ?>
                            <script>
                                s += "<?php echo $succ; ?>";
                            </script>
                        <?php
                    }

                ?>
                <script>
                    $("#info_submit").html(s);
                </script>

                <?php
		}


		if($status==20)
		{
				$update_lead_data_accept = $mon3->query("update property_leads set date_accept=\"".date("Y-m-d")."\" where id=$lead_id;");
                if($update_lead_data_accept)
                {
                        $succ .= "<font color=green>Update Lead Number ".$lead_id." on data accept ".date("Y-m-d")." on all approved paperwork ready, to be insert into system</font><br>";
                }
                else
                {
                        $error .= "<font color=red>Error Update Lead Number ".$lead_id." on data accept ".date("Y-m-d")." on all approved paperwork ready, to be insert into system</font><br>";
                }

                if($error != "")
                {
                    ?>
                        <script>
                            s += "<?php echo $error; ?>";
                        </script>
                    <?php
                }
                else
                {
                    ?>
                        <script>
                            s += "<?php echo $succ; ?>";
                        </script>
                    <?php
                }

                ?>
                <script>
                    $("#info_submit").html(s);
                </script>


                <?php

		}

        if($status==30 )
		{
			// formulario de submissao de contratos
            if( $prop['date_papwk']=="")
            {
                $update_lead_paperwork = $mon3->query("update property_leads set date_papwk=\"".date("Y-m-d")."\" where id=$lead_id;");
            }
			
			// FORM LEAD ON PROPERTIES
                    $address=mysqli_real_escape_string($mon3, $_POST['address']);

                    if($_POST['freg'] != "")
                    {
                        $freg=mysqli_real_escape_string($mon3, $_POST['freg']);
                    }
                    else
                    {
                        $feg_lead = $mon3->query("SELECT * FROM property_leads where id=".$lead_id)->fetch_assoc();
                        $freg = $feg_lead['freguesia'];
                    }


                    // REFERENCIA PROPRIEDADE
                    $ref=mysqli_real_escape_string($mon3, $_POST['ref']);


                    // PROPERTIES CHANGE OVER
                    $refe=mysqli_real_escape_string($mon3, $_POST['refe']);


                    $owner_id=mysqli_real_escape_string($mon3, $_POST['owner_id']);

                    $mng_id=mysqli_real_escape_string($mon3, $_POST['mng_id']);

                    // VALORES INICIAIS
                    $is_changeover = 0;
                    $is_reconnection = 0;
					
					if($_POST['con_type'] != "")
                    {
                        $con_type=mysqli_real_escape_string($mon3, $_POST['con_type']);
                    }
                    else
                    {
                        $con_type = $prop['con_type'];
                    }

                    $model=mysqli_real_escape_string($mon3, $_POST['model']);




                    $contract_id=mysqli_real_escape_string($mon3, $_POST['contract_id']);
                    $tv=mysqli_real_escape_string($mon3, $_POST['tv']);
                    $internet_prof=mysqli_real_escape_string($mon3, $_POST['internet_prof']);
                    $fixed_ip=mysqli_real_escape_string($mon3, $_POST['fixed_ip']);
                    $phone1=mysqli_real_escape_string($mon3, $_POST['phone1']);
                    $phone2=mysqli_real_escape_string($mon3, $_POST['phone2']);
                    $aps=mysqli_real_escape_string($mon3, $_POST['aps']);

                    $monthly_price=mysqli_real_escape_string($mon3, $_POST['monthly_price']);

                    $prev_rev_month=mysqli_real_escape_string($mon3, $_POST['prev_rev_month']);

                    if($_POST['ref'] != "")
                    {
                        //echo "select olt_id from area_codes where areacode=\"$ref\" ";
                        $olt=$mon3->query("select olt_id from area_codes where areacode=\"$ref\" ")->fetch_assoc();
			            $olt=$olt['olt_id'];
                    }
                    else
                    {
                        $olt=0;
                    }

                    if($_POST['is_changeover'] == "")
                    {
                        $is_changeover = 0;
                    }
                    else
                    {
                        $is_changeover = 1;
                    }

                    $olt_id = $olt;
                    
					
					$var_chg_rec = '';
                    $chg = ($is_changeover == 1 || $prop['is_changeover'] == 1) ? "Yes" : "No";
                    echo "Is changeOver? ".$chg."<br>";

                    $rec = $prop['is_reconnection'] ? "Yes" : "No";
                    echo "reconnection: ".$rec."<br>";

                    ?>
                        <script>
                            var warn_chg_rec = "<?php echo $var_chg_rec ?>";
                            //$("#warning_services").html(warn_chg_rec);
                        </script>    

            
                    <?php
					
					// RECONNECTION

                    
					
					if($prop['is_reconnection'] == 1 && ($prop['is_changeover'] == 0 || $is_changeover == 0))
                    {
						$is_reconnection = 1;
						$is_changeover = 0;
						
						$refe_rec = $prop['prop_id'];
                        $lead_sub = $_POST['owner_id'];

                        $is_change_over = $prop['is_changeover'];
                        $is_rec = $prop['is_reconnection'];
									
						$conn_id = $prop['lead_conn_id_rcn'];

                        $prop_id = $prop['prop_id'];
						
						$propid = $refe_rec;
						
						$conn_id_rec = $prop['lead_conn_id_rcn'];
						$conn_chg_over = 0;
					
					}
					
					// CHANGE OVER
					
					else if($prop['is_reconnection'] == 0 && ($prop['is_changeover'] == 1 || $is_changeover == 1))
                    {
						$is_reconnection = 0;
						$is_changeover = 1;

                        if($refe != "")
                        {
                                $prop_rfe = $mon3->query("select * from properties where id=\"$refe\" ")->fetch_assoc();
                                $refas_prop = substr($prop_rfe['ref'], 0, strlen($prop_rfe['ref']) - 3);
                                $olt=$mon3->query("select olt_id from area_codes where areacode=\"$refas_prop\" ")->fetch_assoc();
                                $olt=$olt['olt_id'];
                        }
                        else
                        {
                                $olt = 0;
                        }
							
						$property_id = mysqli_real_escape_string($mon3, $_POST['refe']);
                        $conn_id = mysqli_real_escape_string($mon3, $_POST['con']);
                        $lead_sub = mysqli_real_escape_string($mon3, $_POST['owner_chg']);
                        

                        $equip_id = mysqli_real_escape_string($mon3, $_POST['equip_id_chg']);

                        $type_conn_old = mysqli_real_escape_string($mon3, $_POST['type_conn_old']);
						
						
						$conn_id_rec = 0;
						$conn_chg_over = $conn_id;
						
						
						$propid = $property_id;
						
					}
                    // NO CHANGE OVER AND NO RECONNECTION
					else if($prop['is_reconnection'] == 0 && ($prop['is_changeover'] == 0 || $is_changeover == 0))
                    {
							$is_reconnection = 0;
                            $is_changeover = 0;

                            $conn_id_rec = 0;
						    $conn_chg_over = 0;
                            $lead_sub = 0;
                            

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
                    aps=\"$aps\",
                    model=\"$model\",
                    con_type=\"$con_type\",
                    monthly_price=\"$monthly_price\",
                    is_changeover=\"$is_changeover\",
                    is_reconnection=\"$is_reconnection\",          
                    date_modified=\"".date("Y-m-d")."\",
                    date_papwk=\"".date("Y-m-d")."\",
                    prev_rev_month=\"$prev_rev_month\",
					lead_sub=\"$lead_sub\",
					lead_conn_id_chg_over=\"$conn_chg_over\",
					lead_conn_id_rcn=\"$conn_id_rec\"

                    where id=$lead_id ");

                    if($update_lead_status_30)
                    {
                        if($is_changeover == 1)
                        {
                            $succ.= "<font color=green>Update Lead ".$lead_id." on form 'Property Change Over'</font><br>";
                        }
                        else if($is_reconnection == 1)
                        {
                            $succ.= "<font color=green>Update Lead ".$lead_id." on form 'Property Reconnection'</font><br>";
                        }
                        else
                        {
                            $succ.= "<font color=green>Update Lead ".$lead_id." on form 'New Connection'</font><br>";
                        }
                        $succ.= "<font color=green>Saved on form 'Property Services Properties / Information'</font><br>";
                    }
                    else
                    {
                        $error.= "<font color=red>Error Code on form 'Property Services Properties / Information'</font><br>";
                    }

                    if($error != "")
                    {
                        ?>
                            <script>
                                s += "<?php echo $error; ?>";
                            </script>
                        <?php
                    }
                    else
                    {
                        ?>
                            <script>
                                s += "<?php echo $succ; ?>";
                            </script>
                        <?php
                    }

                    ?>
                    <script>
                        $("#info_submit").html(s);
                    </script>


                    <?php

                        $var_contract_upl = uploadfile("contract",$mon_leads.$lead_id."/", "contract_".time().".pdf",0,0);

                    ?>

                    <script>
                        var var_s = "<?php echo $var_contract_upl; ?>";

                        var sd = "";

                        sd += var_s +"<br>" + warn_chg_rec;
                        $('#warning_services').html(sd);
                    </script>



                <?php


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

        if($network_cost == "")
        {
            $net = 0;
        }
        else
        {
            $net = $network_cost;
        }

        if($estimated_quote=="")
        {
            $est = 0;
        }
        else
        {
            $est = $estimated_quote;
        }

			$update_status_31_33 = $mon3->query("update property_leads set date_viability=\"".date("Y-m-d")."\",
	drop_length=\"$drop_length\",
	ORAC_pits=\"$ORAC_pits\",
	ORAP_poles=\"$ORAP_poles\",
	connection_cost=\"$connection_cost\",
	network_cost=\"$net\",
	is_network_ready=\"$is_network_ready\",
	estimated_quote=\"$est\",
	timeframe=\"$timeframe\",
	quoted=\"$quoted\",
	ORAC_id=\"$ORAC_id\",
	ORAP_id=\"$ORAP_id\",
    drop_length=\"0\"
			where id=$lead_id;");

            if($status == 31)
            {
                if($update_status_31_33)
                {
                    $succ.= "<font color=green>Saved on Process Started</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on Process Started</font><br>";
                }
            }
            else if($status == 32)
            {
                if($update_status_31_33)
                {
                    $succ.= "<font color=green>Saved on Authorizations started- ORAC/ORAP infralobo, schematics, etc</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on Authorizations started- ORAC/ORAP infralobo, schematics, etc</font><br>";
                }

            }
            else if($status == 33)
            {
                if($update_status_31_33)
                {
                    $succ.= "<font color=green>Saved on Needs networking (note with scheduling/jobsheet id)</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on Needs networking (note with scheduling/jobsheet id)</font><br>";
                }

            }



            



            if($error != "")
            {
                ?>
                    <script>
                        s += "<?php echo $error; ?>";
                    </script>
                <?php
            }
            else
            {
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                    </script>
                <?php
            }

            ?>
            <script>
                $("#info_submit").html(s);
            </script>

            <?php
        



		}

        if($status==38)
		{

            
            $succ.= "<font color=green>Saved on NOC check equipment</font><br>";


            if($error != "")
            {
                ?>
                    <script>
                        s += "<?php echo $error; ?>";
                    </script>
                <?php
            }
            else
            {
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                    </script>
                <?php
            }

            ?>
            <script>
                $("#info_submit").html(s);
            </script>

            <?php





		}



		if($status==40)
		{

            //send customer welcome email


            if($error != "")
            {
                ?>
                    <script>
                        s += "<?php echo $error; ?>";
                    </script>
                <?php
            }
            else
            {
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                    </script>
                <?php
            }

            ?>
            <script>
                $("#info_submit").html(s);
            </script>

            <?php

		}











		if($status==41 )
		{
			if( $prop['date_book']=="")
			{
				$mon3->query("update property_leads set date_book=\"".date("Y-m-d")."\" where id=$lead_id;");
			}
			$update_status_40 = $date_install=mysqli_real_escape_string($mon3, $_POST['date_install']);
			$mon3->query("update property_leads set 
			date_install=\"".$date_install."\"
			where id=$lead_id;");


                if($update_status_40)
                {
                    $succ.= "<font color=green>Saved on Booked with customer (notes with date and time)</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on Booked with customer (notes with date and time)</font><br>";
                }


            if($error != "")
            {
                ?>
                    <script>
                        s += "<?php echo $error; ?>";
                    </script>
                <?php
            }
            else
            {
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                    </script>
                <?php
            }

            ?>
            <script>
                $("#info_submit").html(s);
            </script>

            <?php





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

            $eq_assoc_conn = $_POST['equip_assoc_not'];

            $con_type=$prop['con_type'];
			if($con_type=="") $con_type="GPON";


			$propery=$mon3->query("select * from properties where id=\"".$prop['prop_id']."\"")->fetch_assoc();

            // CHANGE OVER & RECONNECTION
            $rec_pon_chg = 0;

            $connection_adder_new = 1;

            if($_POST['con_id'] != "")
            {
                $conn_id = $_POST['con_id'];
            }

            $text_ch = '';
            $text_pro_log = '';
            if($prop['is_reconnection'] == 1 || $prop['is_changeover'] == 1)
            {
                // MUDANCA DO IDENTIFICADOR DO EQUIPAMENTO
                switch($eq_assoc_conn)
                {
                    case 1:
                    case 2:

                        $equi_ant_conn=$mon3->query("SELECT * FROM connections WHERE id=".$conn_id)->fetch_assoc();
                        $equip_ant = $equi_ant_conn['equip_id'];
                        
                        if($equip_ant != "")
                        {
                            if($equip_ant!=$fsan)
                            {
                                if($prop['is_reconnection'] == 1)
                                {
                                    $text_ch .= " on connection number ".$conn_id;
                                    $text_pro_log .= " on connection number <b>".$conn_id."</b>";
                                    
                                }
                                proplog($prop['prop_id'],"Previous Equipment <b>".$equip_ant."</b> to <b>".$fsan."</b> ".$text_pro_log);
                                monlog("Previous Equipment ".$equip_ant." to ".$fsan."".$text_ch." ");
                            }
                        }
                        
                        
                    break;
                }
                $rec_pon_chg = 1;
                if($prop['is_changeover'] == 1)
                {
                    $num_chg_over = $mon3->query("SELECT * FROM connections WHERE id='".$conn_id."' AND type='".$con_type."' AND date_end='0000-00-00'")->num_rows;
                    if($num_chg_over > 0)
                    {
                        $connection_adder_new = 0;
                        $update_equip_exist_not_assoc = $mon3->query("update connections set equip_id=\"".$fsan."\", type=\"".$con_type."\", date_start=\"".date("Y-m-d")."\",  subscriber=\"".$propery['owner_id']."\" where id=$conn_id");
                        // ACTUALIZAR OS EQUIPAMENTOS DOS SERVIÇOS
                        $mon3->query("UPDATE services set equip_id = '".$fsan."', subscriber = ".$propery['owner_id'].", contract_id = ".$prop['contract_id']." WHERE connection_id=".$conn_id);
                    }
                    else
                    {
                        $update_chg_equip = $mon3->query("update connections set date_end=\"".date("Y-m-d")."\" where id=$conn_id;");
                        if($update_chg_equip)
                        {
                            $succ .= '<font color=green>OLD connection on date end <b>'.date("Y-m-d").'</b> on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=red>Error on OLD connection on date end <b>'.date("Y-m-d").'</b> on connection number '.$conn_id.'</font><br>';
                        }
                        
                        $connection_adder_new = 1;
                    }
                }
                if($prop['is_reconnection'] == 1)
                {
                    $connection_adder_new=0;
                    $update_equip_exist_not_assoc = $mon3->query("update connections set equip_id=\"".$fsan."\", type=\"".$con_type."\", date_start=\"".date("Y-m-d")."\",  subscriber=\"".$propery['owner_id']."\" where id=$conn_id");
                    // ACTUALIZAR OS EQUIPAMENTOS DOS SERVIÇOS
                    $mon3->query("UPDATE services set equip_id = '".$fsan."', subscriber = ".$propery['owner_id'].", contract_id = ".$prop['contract_id']." WHERE connection_id=".$conn_id);
                    
                    $dis_services_conn = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id)->fetch_assoc();

                    $dis_serv = $dis_services_conn['dis_services'];

                    $prop_id = $dis_services_conn['property_id'];


                    if($dis_serv == 2)
                    {
                        // ACTUALIZAR AS DATAS DIS CONN E DIS SERVICES
                        
                        $update_dis_conn = $mon3->query("UPDATE connections SET dis_services = \"0\", date_dis_services=\"0000-00-00\", date_dis_conn=\"0000-00-00\", date_rea_services=\"0000-00-00\" WHERE id=".$conn_id);

                        if($update_dis_conn)
                        {
                            $succ .= "<font color=green>Connection Number ".$lead_id." are activated successfully</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Connection Number ".$lead_id." are not activated</font><br>";
                        }

                        $update_dis_services = $mon3->query("UPDATE services SET is_susp_serv= \"0\" WHERE connection_id=".$conn_id);

                        if($update_dis_services)
                        {
                            $succ .= "<font color=green>Services on Connection Number ".$conn_id." are activated successfully</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Services on Connection Number ".$conn_id." are not activated</font><br>";
                        }

                        proplog($prop_id,"Activate connection <b>".$conn_id."</b> and services are activated to edit/create");

                    }
                    
                }

                

                


                // LEAD_SUB != OWNER_ID

                // VERIFICAR SE A LEAD SUBSCRIBER E DIFERENTE AO OWNER DA PROPERTY DO CUSTOMER
                if($propery['owner_id'] != $prop['lead_sub'])
                {
                    $update_prop_owner_status_30 = $mon3->query("update properties set owner_id=\"".$prop['lead_sub']."\" WHERE id=".$prop['prop_id']);
                    if($update_prop_owner_status_30)
                    {
                        $succ .= "<font color=green>Update Property on number ".$prop['prop_id']." on owner ".$prop['lead_sub']."</font><br>";
                    }
                    else
                    {
                        $error .= "<font color=red>Error Property on number ".$prop['prop_id']." on owner ".$prop['lead_sub']."</font><br>";
                    }
                    proplog($prop['prop_id'],"Update Property on number <b>".$prop['prop_id']."</b> on owner <b>".$prop['lead_sub']."</b>");
                }

            }


            // PRECISA DE FAZER UMA NOVA CONNECTION

            if($connection_adder_new == 1)
            {
                        if($prop['is_changeover'] == 1)
                        {  
                            proplog($prop['prop_id'],"OLD connection <b>".date("Y-m-d")."</b> on connection number <b>".$conn_id."</b>");
                        }
                        $insert_connecti = $mon3->query("insert into connections (property_id,type,equip_id,date_start,subscriber) VALUES (
                            \"".$prop['prop_id']."\",
                            \"$con_type\",
                            \"$fsan\",
                            \"".date("Y-m-d")."\",
                            \"".$propery['owner_id']."\"
                            ) ");

                        $conn_id_new=$mon3->insert_id;
                        proplog($prop['prop_id'],"Insert new Connection on Equipment <b>".$fsan."</b> on new connection number <b>".$conn_id_new."</b>");
                        $conn_id=$conn_id_new;
                        if($prop['is_changeover'] == 1)
                        {
                            $mon3->query("UPDATE property_leads set lead_conn_id_chg_over=\"".$conn_id."\"  where id=$lead_id; ");
                        }
            }

            $mon3->query("update connections set subscriber=\"".$prop['lead_sub']."\" where id=$conn_id;");
            
            /// ---------- CONNECTIONS EQUIPMENTS -------------

            // GPON

            if($con_type == "GPON")
            {
                if($rec_pon_chg==1)
                {
                        $ont=$mon3->query("select * from ftth_ont where fsan=\"".$equip_ant."\";")->fetch_assoc();	
                        if($ont['ont_id'] != "")
                        {
                            $ont_x=explode("-",$ont['ont_id']);           
                            $ontnext="1-".$pon."-".$ont_x[3];

                            $ont_blacked_old=$mon3->query("update ftth_ont set ont_id = \"\" where fsan=\"".$equip."\";");
                        }
                        else
                        {
                            $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                            echo $ontnext."<br>";
                        }
                }
                else
                {
                    $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                    echo $ontnext."<br>";
                }

                $select_ont_id = $mon3->query("select ont_id from ftth_ont where fsan=\"$fsan\"")->fetch_assoc();

                    $num_ont=$mon3->query("select fsan from ftth_ont where fsan=\"$fsan\"")->num_rows;
		            $ont_id_ex=$mon3->query("select fsan from ftth_ont where fsan=\"$fsan\" and ont_id=\"\" ")->num_rows;

                    $model_ant = $mon3->query("SELECT * FROM ftth_ont WHERE fsan=\"$fsan\"")->fetch_assoc();

                    $m_ant = $model_ant['meprof'];

                    if($num_ont>0 && $ont_id_ex>0)
                    {
                        $update_equip_ont_50 = $mon3->query("update ftth_ont set olt_id=\"$olt_id\",ont_id=\"$ontnext\", meprof=\"$model\", model=\"$model\" where fsan=\"$fsan\"");
                        if($update_equip_ont_50)
                                {
                                    $succ .= '<font color=green>Update ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                                }
                                else
                                {
                                    $error .= '<font color=red>Error on Updating ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                                }
                        proplog($prop['prop_id'],"Update ONT from <b>".$fsan."</b> for ont <b>".$ontnext."</b> and olt <b>". $olt_id. "</b> and model from <b>".$m_ant."</b> to <b>".$model."</b> on connection number <b>".$conn_id."</b> (ONT, FWA CPE)");
                        gpon_change_ont($olt_id,$ontnext,$fsan,$model);

                        monlog("FSAN changed on ".$propery['ref']." from ".$equip." to $fsan for ont ".$ontnext." and olt ". $olt_id. " and model from ".$m_ant." to ".$model."");
                    }
                    elseif($num_ont>0 && $ont_id_ex==0) //tem ont_id
                    {
                        echo "<font color=red>ONT in database with ID.. not registering to connection.</font><br>";
                    }
                    else //nao tem ONT
                    {
                        $insert_ont_50 = $mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,model,meprof) values (\"$fsan\",$olt_id,\"$ontnext\", \"$model\", \"$model\")");
                        if($insert_ont_50)
                                    {
                                        $succ .= '<font color=green>Insert ONT '.$fsan.'</font><br>';
                                    }
                                    else
                                    {
                                        $error .= '<font color=green>Error on Insert ONT '.$fsan.'</font><br>';
                                    }
                        proplog($prop['prop_id'],"Insert ONT <b>".$fsan."</b>  for ont <b>".$ontnext."</b> and olt <b>". $olt_id."</b> and model <b>".$model."</b> on connection number <b>".$conn_id."</b>");

                        monlog("FSAN Inserted on ".$propery['ref']." equipment ".$fsan." for ont ".$ontnext." and olt ". $olt_id." and model ".$model);

                    }
            }

            // FWA

            else if($con_type == "FWA")
            {
                $model_ant = $mon3->query("SELECT * FROM fwa_cpe WHERE mac=\"$fsan\"")->fetch_assoc();

                $m_ant = $model_ant['model'];

                $select_fwa_cpe_num = $mon3->query("select * from fwa_cpe where mac=\"$fsan\"")->num_rows;

                // DESIGNACAO DA ANTENNA

                if($model_ant['antenna'] == "")
                {
                    $antenna_ant = 0;
                }
                else
                {
                    $antenna_ant = $model_ant['antenna'];
                }

                $antenna_des_ant = $mon3->query("SELECT * FROM `fwa_antennas` WHERE id=".$antenna_ant)->fetch_assoc();

                $designacao_antenna_ant = $antenna_des_ant['name'];

                $antenna_des = $mon3->query("SELECT * FROM `fwa_antennas` WHERE id=".$antenna)->fetch_assoc();

                $designacao_antenna = $antenna_des['name'];

                if($select_fwa_cpe_num > 0)
                {
                    $update_equip_fwa_cpe_50 = $mon3->query("update fwa_cpe set model=\"$model\",antenna=\"$antenna\" where mac=\"$fsan\"");

                    if($update_equip_fwa_cpe_50)
                            {
                                $succ .= '<font color=green>Update FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                            }
                            else
                            {
                                $error .= '<font color=green>Error on Updating FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                            }
                    proplog($prop['prop_id'],"Update FWA CPE <b>".$fsan."</b> on connection number <b>".$conn_id."</b> for antenna <b>".$designacao_antenna_ant."</b> to <b>".$designacao_antenna."</b> and model from ".$m_ant." to ".$model."");

                    monlog("FWA CPE ".$fsan." Updated on ".$propery['ref']." for antenna ".$designacao_antenna_ant." to ".$designacao_antenna." and model from ".$m_ant." to ".$model."");

                }
                else
                {
                    $insert_fwa_equip = $mon3->query("insert into fwa_cpe (mac,model,antenna) VALUES (\"$fsan\",\"$model\",\"$antenna\") ");
                    if($insert_fwa_equip)
                                    {
                                        $succ .= "<font color=green>FWA CPE was insert sucessfully</font><br>";
                                    }
                                    else
                                    {
                                        $error .= "<font color=red>Error on insert FWA CPE</font><br>";
                                    }
                    proplog($prop['prop_id'],"Insert FWA CPE <b>".$fsan."</b> antenna <b>".$designacao_antenna."</b> and model <b>".$model."</b> on connection number <b>".$conn_id."</b>");

                    monlog("FWA CPE Inserted on ".$propery['ref']." equipment ".$fsan." antenna ".$designacao_antenna." and model ".$model."");
                }
            }


            /// ---------- SERVICES -------------

            /// SERVICES - FORM ///

            // VERIFICAR SE OS SERVICOS ESTAO ATIVOS NESTA CONEXAO - IS RECONNECTION DA PROP

            if(isset($_POST['dis_serv_rec']))
            {
                $services_list_enabled = $mon3->query("SELECT * FROM services where connection_id=".$conn_id." AND date_end='0000-00-00'");

                while($service_list_enabled = $services_list_enabled->fetch_assoc())
                {
                    $sv_id = $service_list_enabled['id'];
                    $ty_sv_id = $service_list_enabled['type'];
                    //echo "UPDATE services set date_end =\"".date("Y-m-d")."\" WHERE id=".$sv_id;
                    $update_serv_each_type = $mon3->query("UPDATE services set date_end =\"".date("Y-m-d")."\" WHERE id=".$sv_id);
                    if($update_serv_each_type)
                        {
                            $succ .= "<font color=green>Service number ".$sv_id." was deactivated sucessfully on type service ".$ty_sv_id."</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Error on Service number ".$sv_id." was not deactivated on type service ".$ty_sv_id."</font><br>";
                        }
                    proplog($prop['prop_id'],"Service number <b>".$sv_id."</b> was deactivated sucessfully on type service <b>".$ty_sv_id."</b>");

                }

            }

            $con_id = $conn_id;


            // ADICIONAR OS SERVICOS DAS CONEXOES (NORMALMENTE)

            // TV

            if($con_type!="FWA")
            {
                if($tv!="0")
                {
                    $insert_serv_tv = $mon3->query("insert into services (connection_id,equip_id,type,date_start,date_end,contract_id,subscriber) VALUES (
                        \"$con_id\",
                        \"$fsan\",
                        \"TV\",
                        \"".date("Y-m-d")."\",
                        \"0000-00-00\",
                        \"".$prop['contract_id']."\",
                        \"".$propery['owner_id']."\"
                        ) ");
                        if($insert_serv_tv)
                            {
                                $succ .= "<font color=green>Service type 'TV' was insert successfully on connection number ".$con_id."</font><br>";
                            }
                            else
                            {
                                $error .= "<font color=red>Error while on insert service type 'TV' on connection number ".$con_id."</font><br>";
                            }
                    proplog($prop['prop_id'],"Service type 'TV' was insert successfully on connection number <b>".$con_id."</b>");

                }
            }

            // INT

            if($internet_prof>0)
            {
                $insert_serv_int = $mon3->query("insert into services (connection_id,equip_id,type,date_start,date_end,contract_id,subscriber) VALUES (
                    \"$con_id\",
                    \"$fsan\",
                    \"INT\",
                    \"".date("Y-m-d")."\",
                    \"0000-00-00\",
                    \"".$prop['contract_id']."\",
                    \"".$propery['owner_id']."\"
                    ) ");

                    if($insert_serv_int)
                        {
                            $succ .= "<font color=green>Service type 'INT' was insert successfully on connection number ".$con_id."</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Error while on insert service type 'INT' on connection number ".$con_id."</font><br>";
                        }

                    //$logs .= 
                    proplog($prop['prop_id'],"Service type 'INT' was insert successfully on connection number <b>".$con_id."</b>");




                    $intserv=$mon3->insert_id;

                    $servi=$mon3->query("select name,prof_up,prof_down from int_services where id=$internet_prof")->fetch_assoc();
                    echo mysqli_error($mon3);
                    $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"speed\",
                    \"".$internet_prof."\",
                    \"".date("Y-m-d")."\"
                    ) ");

                    $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"is_router\",
                    \"$is_router\",
                    \"".date("Y-m-d")."\"
                    ) ");
                    if($is_router==0){
                        $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"bridge_port\",
                    \"1\",
                    \"".date("Y-m-d")."\"
                    ) ");
                    }
                    $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"vlan\",
                    \"$vlan\",
                    \"".date("Y-m-d")."\"
                    ) ");

                    if($fixed_ip!=""){
                    $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"fixed_ip\",
                    \"$fixed_ip\",
                    \"".date("Y-m-d")."\"
                    ) ");
                    $mon3->query("update in_fixed_ips set in_use=1, mac=\"xxx\" where ip=\"$fixed_ip\" ");
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
                    $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"wifi_key\",
                    \"$wifi_key\",
                    \"".date("Y-m-d")."\"
                    ) ");

                    }
                    else
                    {
                    $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                    \"$intserv\",
                    \"wifi\",
                    \"0\",
                    \"".date("Y-m-d")."\"
                    ) ");

                    }
            }

            // PHONE 1 & PHONE 2 - PHN

            if($phone1!="")
            {
                        $insert_serv_phn_1 = $mon3->query("insert into services (connection_id,equip_id,type,date_start,date_end,contract_id,subscriber) VALUES (
                        \"$con_id\",
                        \"$fsan\",
                        \"PHN\",
                        \"".date("Y-m-d")."\",
                        \"0000-00-00\",
                        \"".$prop['contract_id']."\",
                        \"".$propery['owner_id']."\"
                        ) ");

                        if($insert_serv_phn_1)
                        {
                            $succ .= "<font color=green>Service type 'PHN' (Phone 1) was insert successfully on connection number ".$con_id."</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Error while on insert service type 'PHN' (Phone 1) on connection number ".$con_id."</font><br>";
                        }


                        proplog($prop['prop_id'],"Service type 'PHN' (Phone 1) was insert successfully on connection number <b>".$con_id."</b>");



                        $intserv=$mon3->insert_id;



                        $mon3->query("insert into voip_accounts (password,caller_id,voicemail,voicemail_time,call_limit) VALUES (
                        \"".substr(time()*10/3,2,8)."\",
                        \"351".$phone1."\",
                        \"1\",
                        \"50\",
                        \"1\"
                        ) ");
                        $voip1=$mon3->insert_id;

                        $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                        \"$intserv\",
                        \"account\",
                        \"$voip1\",
                        \"".date("Y-m-d")."\"
                        ) ");

                        $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                        \"$intserv\",
                        \"phn_port\",
                        \"1\",
                        \"".date("Y-m-d")."\"
                        ) ");



            }
            if($phone2!="")
            {
                        $insert_serv_phn_2 = $mon3->query("insert into services (connection_id,equip_id,type,date_start,date_end,contract_id,subscriber) VALUES (
                        \"$con_id\",
                        \"$fsan\",
                        \"PHN\",
                        \"".date("Y-m-d")."\",
                        \"0000-00-00\",
                        \"".$prop['contract_id']."\",
                        \"".$propery['owner_id']."\"
                        ) ");

                        if($insert_serv_phn_2)
                        {
                            $succ .= "<font color=green>Service type 'PHN' (Phone 2) was inssert successfully on connection number ".$con_id."</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Error while on insert service type 'PHN' (Phone 2) on connection number ".$con_id."</font><br>";
                        }

                        proplog($prop['prop_id'],"Service type 'PHN' (Phone 2) was insert successfully on connection number <b>".$con_id."</b>");


                        $intserv=$mon3->insert_id;



                        $mon3->query("insert into voip_accounts (password,caller_id,voicemail,voicemail_time,call_limit) VALUES (
                        \"".substr(time()*10/3,2,8)."\",
                        \"351".$phone2."\",
                        \"1\",
                        \"50\",
                        \"1\"
                        ) ");
                        $voip2=$mon3->insert_id;

                        $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                        \"$intserv\",
                        \"account\",
                        \"$voip2\",
                        \"".date("Y-m-d")."\"
                        ) ");

                        $mon3->query("insert into service_attributes (service_id,name,value,date) VALUES (
                        \"$intserv\",
                        \"phn_port\",
                        \"2\",
                        \"".date("Y-m-d")."\"
                        ) ");
                        


            }

            if($error != "")
                    {
                        ?>
                            <script>
                                s += "<?php echo $error; ?>";
                            </script>
                        <?php
                    }
                    else
                    {
                        ?>
                            <script>
                                s += "<?php echo $succ; ?>";
                            </script>
                        <?php
                    }

                    ?>
                    <script>
                        $("#info_submit").html(s);
                    </script>


                    <?php



		}






		if($status==51 )
		{

			$installation_job_id=mysqli_real_escape_string($mon3, $_POST['installation_job_id']);
			$technician=mysqli_real_escape_string($mon3, $_POST['technician']);

			$update_status_51 = $mon3->query("update property_leads set 
	installation_job_id=\"$installation_job_id\",
	technician=\"$technician\"
			where id=$lead_id;");


            if($update_status_51)
                {
                    $succ.= "<font color=green>Saved on Job sheet closed</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on Job sheet closed</font><br>";
                }


            if($error != "")
            {
                ?>
                    <script>
                        s += "<?php echo $error; ?>";
                    </script>
                <?php
            }
            else
            {
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                    </script>
                <?php
            }

            ?>
            <script>
                $("#info_submit").html(s);
            </script>

            <?php

			$var1 = uploadfile("file1",$mon_leads.$lead_id."/", "pic1_".time().".jpg",1, 0);
			$var2 = uploadfile("file2",$mon_leads.$lead_id."/", "pic2_".time().".jpg",1,0);
			$var3 = uploadfile("file3",$mon_leads.$lead_id."/", "pic3_".time().".jpg",1,0);
			$var4 = uploadfile("file4",$mon_leads.$lead_id."/", "pic4_".time().".jpg",1,0);

            $var_warn = $var1."<br>".$var2."<br>".$var3."<br>".$var4;
            ?>

                <script>
                    var var_s = "<?php echo $var_warn; ?>";
                    $('#warning_services').html(var_s);
                </script>

            <?php



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

			$update_status_60 = $mon3->query("update property_leads set 
			date_closed=\"".date("Y-m-d")."\",
            installation_job_id=\"$installation_job_id\",
            technician=\"$technician\",
            NPS_score=\"$NPS_score\",
            manager_score=\"$manager_score\",
            has_pictures=\"$has_pictures\",
            speedtest=\"$speedtest\"
			where id=$lead_id;");


            if($update_status_60)
                {
                    $succ.= "<font color=green>Saved on Closed (NPS scores, techinal evaluation)</font><br>";
                }
                else
                {
                    $error.= "<font color=red>Error Code on Closed (NPS scores, techinal evaluation)</font><br>";
                }


            if($error != "")
            {
                ?>
                    <script>
                        s += "<?php echo $error; ?>";
                    </script>
                <?php
            }
            else
            {
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                    </script>
                <?php
            }

            ?>
            <script>
                $("#info_submit").html(s);
            </script>

            <?php






			$var1 = uploadfile("filea",$mon_leads.$lead_id."/", "pic1_".time().".jpg",1, 0);
			$var2 = uploadfile("fileb",$mon_leads.$lead_id."/", "pic2_".time().".jpg",1, 0);
			$var3 = uploadfile("filec",$mon_leads.$lead_id."/", "pic3_".time().".jpg",1, 0);
			$var4 = uploadfile("filed",$mon_leads.$lead_id."/", "pic4_".time().".jpg",1, 0);

            $var_warn = $var1."<br>".$var2."<br>".$var3."<br>".$var4;
            ?>

                <script>
                    var var_s = "<?php echo $var_warn; ?>";
                    $('#warning_services').html(var_s);
                </script>

            <?php



		}

        if($status==99)
        {
            // FORMULARIOS INFO / SERVICES
            $update_lead_status_99 = $mon3->query("update property_leads set
            prop_id=\"0\",
            olt_id=\"0\",
            contract_id=\"0\",
            tv=\"0\",
            internet_prof=\"0\",
            fixed_ip=\"\",
            phone1=\"\",
            phone2=\"\",
            aps=\"0\",
            model=\"\",
            con_type=\"\",
            monthly_price=\"0\",
            is_changeover=\"0\",
            is_reconnection=\"0\",          
            date_modified=\"0000-00-00\",
            date_papwk=\"0000-00-00\",
            prev_rev_month=\"0\",
            lead_sub=\"0\",
            lead_conn_id_chg_over=\"0\",
            lead_conn_id_rcn=\"0\"
            where id=$lead_id ");
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
if( $prop['prop_id']!=0){
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
    //var soption=7;
    //console.log(soption);
    
	textdiv += '<font color=red> details are only savend on a status change. Please use notes if you want to notify of changes on submitted data</font><br><br>';
	
	
	if(soption>0 && soption<3) 
	{
        $('#warning_services').html('');
        $('#info_submit').html('');
		textdiv += ' form to prepare rough estimates <br><table>' +
		'<tr><td>Connection: '+

	
		'<tr><td>Type<td> 	<select name=con_type id=con_type onchange=\"updatecpe(this.options[this.selectedIndex].value,);\" style=\"width: 180px;\">	' +			
        '<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +	
        '</select>'+	
		
		'<div id=model_cpe><tr><td>CPE model<td><select name=model id=models style=\"width: 180px;\"></select></div>'+	

		
		
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
        $('#warning_services').html('');
        $('#info_submit').html('');
        textdiv += '<tr><td><br>Connection <input type=hidden id=con_type_id>' +
        '<tr id=type_con_sta_7><td>Type <td><select name=con_type_rec id=con_type_rec onchange=change_type_connection(this.value) style=\"width: 200px;\">' +		
		'<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +     
        '</select><br>';";

        // SE EXISTIR CONEXAO NA LEAD CORRESPONDENTE
        $con_type = $prop['con_type'];
        if($con_type == "")
            {
                $con_type = "GPON";
            }
        // VERIFICAR AS PROPRIEDADES COM SERVIÇOS DESATIVOS (DISABLED)
        echo "textdiv += '<label>Disabled Prop Services</label> <input type=checkbox name=disabled_prop_services id=disabled_prop_services onchange=\"changePropServicesDisabled(this, 0)\" checked><br> ";

        // LISTAR AS PROPRIEDADES COM SERVIÇOS DESATIVOS (DISABLED)
        echo "<label id=text_conn_prop_rec>Property Reconnection</label> <div id=lists_reconnections_bt_type><select name=refe_rec_7 id=refe_rec_7 onchange=\"con_prop_rec(this.value); \" style=\"width: 500px\">";

        echo "</select></div><br> <span id=serv_prop_desativados></span> <input type=hidden name=conn_id_prop_rec id=conn_id_prop_rec value=".$conn_prop_rec.">'; ";
        echo "textdiv += '<div id=prop_conn_servicos_des></div>';";
        echo "textdiv += '<div id=conn_assoc_prop_status_7><input type=hidden name=rec_assoc value=1></div>';";
        echo "
	}
	
	
	else if(soption==14) 
	{
	    $('#warning_services').html('');
        $('#info_submit').html('');
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
	    $('#warning_services').html('');
        $('#info_submit').html('');
		textdiv += ' form to get details of service, create property   <br><table>'+
		'<tr><td >Address<td><input type=text name=address value=\"".str_replace("'","",$prop['address'])."\" size=50><br>";

        $form_customer_add = "";
        $form_chg_over = "";
        // Verificar a Change Over
        if($prop['is_changeover'] == 1 || $prop['is_reconnection'] == 1)
        {
            $dis = "disabled"; // DESABILITAR A PROP REF
            $dis_conc = "disabled"; // DESABILITAR O CONCELHO
            $dis_freg = "disabled"; // DESABILITAR A FREGUESIA

            
            
            if($prop['is_changeover'] == 1)
            {
                $teste_check_over = "block";

                //$refer_props_chg_rec = "(Prop id: ".$prop['prop_id']. " (Change Over))";
                $subs = "disabled"; // DESABILITAR O SUBSCRIBER

                $active = "checked";

                $con_ty = "";

                $dis_change_over = "block";
                $form_customer_add .= "";

                $p_r = $prop['prop_id'];
                $con_prop = $prop['con_type'];
                $l_sub = $prop['lead_sub'];
                $conn_lead_chg_over = $prop['lead_conn_id_chg_over'];
                $conx = $mon3->query("SELECT * FROM connections WHERE id=\"$conn_lead_chg_over\" ")->fetch_assoc();
                $prrti = $mon3->query("SELECT * FROM properties WHERE id=\"$p_r\" ")->fetch_assoc();

                $om = $mon3->query("SELECT * FROM customers WHERE id=\"$l_sub\" ")->fetch_assoc();

                if($p_r != 0 || $p_r != "")
                {
                    $form_chg_over .= "<fieldset>";
                    $form_chg_over .= "<legend>Properties Change Over Connection on Lead Status 50 </legend>";
                    $form_chg_over .= "<label>Property: </label>". $prrti['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$prrti['address']))."<br>";
                    $form_chg_over .= "<label>Connection: </label>". $conx['id']." - ".$conx['equip_id']."<br>";
                    $form_chg_over .= "<label>Type Connection (Change Over): </label>". $conx['type']."<br>";
                    $form_chg_over .= "<label>Subscriber: </label>". $om['id']."-".addslashes($om['name'])."#".$om['fiscal_nr']."<br>";
                    $form_chg_over .= "</fieldset>";
                }

                
            }
            else if($prop['is_reconnection'] == 1)
            {
                $subs = "";
                $teste_check_over = "none";

                //$refer_props_chg_rec = "(Prop id: ".$prop['prop_id']. " (Reconnection))";

                $active = "";

                $con_ty = "disabled";

                $dis_change_over = "none";

                $form_customer_add .= "'<tr><td><br><br><button type=button id=add_client_but onclick=addClientNew() >Active \'Add New Client\' </button><td>";
                $form_customer_add .= "<tr><td colspan=2><div id=add_new_client style=\"display:none;\" >";
                
                $form_customer_add .= "<fieldset>";
                $form_customer_add .= "<legend>New Customer:</legend>";
                $form_customer_add .= "<table  cellspacing=10><tr><td valign=center colspan=2 ><div id=form_cust_add_rec>";
                $form_customer_add .= "<tr><td> <div id=divname> <b>Name:</b> <font color=red>*</font></div> <td><select name=salut_cust id=salut_cust style=\"width: 100px;\">";
                $form_customer_add .= "<option value=\"Sr.\">Sr.</option>";
                $form_customer_add .= "<option value=\"Sra.\">Sra.</option>";
                $form_customer_add .= "<option value=\"Eng.\">Eng.</option>";
                $form_customer_add .= "<option value=\"Sr.\">Dr.</option>";
                $form_customer_add .= "<option value=\"Dra.\">Dra.</option>";
                $form_customer_add .= "<option value=\"Mr.\">Mr.</option>";
                $form_customer_add .= "<option value=\"Mrs.\">Mrs.</option>";
                $form_customer_add .= "<option value=\"Miss.\">Miss.</option>";
                $form_customer_add .= "<option value=\"Lady\">Lady</option>";
                $form_customer_add .= "<option value=\"Sir\">Sir</option>";
                $form_customer_add .= "</select> ";

                $form_customer_add .= "<input type=text name=name_cust id=name_cust onkeyup=\"return validateCustForm()\">";
                $form_customer_add .= "<tr><td> <div id=divbillingaddr> <b>Billing Address:</b> <font color=red>*</font></div><td> <input type=text name=address_cust id=address_cust onkeyup=\"return validateCustForm()\">";
                $form_customer_add .= "<tr><td> <div id=divemailcust> <b>Email:</b> <font color=red>*</font></div> <td> <input type=text name=email_cust id=email_cust onkeyup=\"return validateCustForm()\">";
                $form_customer_add .= "<tr><td> <div id=divphone> <b>Phone:</b> <font color=red>*</font></div> <td> <input type=text name=telef_cust id=telef_cust onkeyup=\"return validateCustForm()\">";
                $form_customer_add .= "<tr><td> <div id=divfiscalnumber> <b>Fiscal Number:</b> <font color=red>*</font></div> <td> <input type=text name=fiscal_nr_cust id=fiscal_nr_cust onkeyup=\"return validateCustForm()\" oninput=checkFiscalNumber(this.value)> <span id=fiscal_num_warn></span>";
                $form_customer_add .= "<tr><td> <b>Prefered Lang:</b><td> <select name=lang_cust id=lang_cust style=\"width: 100px;\">";
                $form_customer_add .= "<option value=\"pt\">pt</option>";
                $form_customer_add .= "<option value=\"en\">en</option>";
                $form_customer_add .= "<option value=\"fr\">fr</option>";
                $form_customer_add .= "<option value=\"es\">es</option>";
                $form_customer_add .= "</select>";

                $form_customer_add .= "<tr><td><td><input type=checkbox name=is_commercial_cust> Is a company";
                $form_customer_add .= "<tr><td> <b>Roles</b> <td><input type=checkbox name=is_management_cust> Is a management company of the owner";
                $form_customer_add .= "<tr><td><td><input type=checkbox name=is_agent_cust> Is an agent for leads";
                
                $form_customer_add .= "<tr><td> <b>Notes:</b><td> <input type=text name=notes_cust>";
                $form_customer_add .= "<tr><td><td><br>";




                $form_customer_add .= "<tr><td><button type=button onclick=NewCustomerState30(".$lead_id.") id=new_cust disabled>New Customer</button>";

                $form_customer_add .= "<tr><td colspan=2><span id=warn_submit_cust></span>";
                $form_customer_add .= "</div></table>";
                $form_customer_add .= "</fieldset>";
                
                $form_customer_add .= "</div><td>' +";

                $form_chg_over .= "";
            }
        }
        else
        {
            $dis = "";
            $subs = "onchange=owner_prop(this.value)";
            $dis_conc = "";
            $dis_freg = "";
            $teste_check_over = "block";

            $active = "selecteded";

            $con_ty = "";

            $dis_change_over = "none";

            $form_customer_add .= "";

            $form_chg_over .= "";
        }

        // TIPO DE CONEXAO VALIDAR - CONNECTION CHANGE OVER ESTADO 30

        if($prop['con_type'] != "")
        {
            $wq .= " AND connections.type != '".$prop['con_type']."'";
			// DESATIVAR A OPCAO TV SERVICE
            if($prop['con_type']=='FWA')
            {
                $ds = "disabled";
            }
            else
            {
                $ds = "";
            }
        }
        else
        {
            $wq .= " AND connections.type != 'GPON'";
        }

        // VALIDAR CONTRATO

        $prop['contract_id'] == "" ? 0 : $prop['contract_id'];

        

        

        // CONCELHO
        echo "<tr><td>Concelho:<td><select name=concelho id=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\" ".$dis_conc." style=width:400px>";
        $concs=$mon3->query("select * from concelhos  order by distrito,concelho;");
        while($conca=$concs->fetch_assoc())
        {
            echo "<option value=".$conca['id'];
            if ($conca['id']==176)
                echo " selected";
            echo ">".$conca['distrito']." - ".$conca['concelho']."</option>";
        }

        echo"</select>";

        // FREGUESIA
		echo "<tr><td>Freguesia:<td><select name=freg id=freg ".$dis_freg." style=width:400px>";

        $concp=$mon3->query("select concelho from freguesias where id=".$prop['freguesia'].";")->fetch_assoc();
        $fregs=$mon3->query("select * from freguesias where concelho=".$concp['concelho'].";");
        
        
        while($frega=$fregs->fetch_assoc())
        {
            echo "<option value=".$frega['id'];
            if ($frega['id']==$prop['freguesia'])
                echo " selected";
            echo ">".$frega['freguesia']."</option>";
        }
        echo"</select>";



        

        // PROPERTY REF 
		echo "<tr><td >Prop Ref: <td> <select name=ref id=ref ".$dis." style=width:400px>"; 
		$refs=$mon3->query("select areacode,description from area_codes order by areacode"); 
        while($ref=$refs->fetch_assoc())
        { 
            echo "<option value=".$ref['areacode'];
            echo ">".$ref['areacode']." - ".$ref['description']."</option>";
        }

        // SUBSCRIBER
        echo " </select><br> '+
		'<tr><td>Subscriber: <td><select name=owner_id id=owner_id ".$subs." style=width:400px>";

        $property_cust = $mon3->query("SELECT * FROM properties WHERE id = ".$prop['prop_id'])->fetch_assoc();


	    $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
	    while($cust=$custs->fetch_assoc())
	    {
            echo "<option value=".$cust['id'];
            if($cust['id']==$property_cust['owner_id']) echo " selected";
            echo ">". $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr']."</option>";
        }

        // MANAGEMENT ID
		echo " </select><br> '+
		'<tr><td>Management<td><select name=mng_id> <option selected value=0 style=width:400px>no management</option>";
		$refs=$mon3->query("select id,name from customers where is_management=1 order by name"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['id'].">".$ref['name']."</option>";} echo " </select><br>";


        // CONTRACT ID AND PDF
		echo "<tr><td> <br>'+
		'<tr><td>Contract id<td><input type=text name=contract_id value=".$prop['contract_id']." size=5> '+
		'<tr><td>contract pdf <td><input type=file name=contract>'+ ";

        echo "'<tr><td><br>Connection <input type=hidden id=con_type_id>'+ ";


        // TYPE CONNECTION - MUDANCA DA CONNECTION SE FOR CHANGE OVER = 1
		echo "'<tr id=type_con_sta_30><td>Type<td><select name=con_type id=con_type onchange=\"updatecpe(this.options[this.selectedIndex].value,); changeConOver(this.options[this.selectedIndex].value); updateInternet(this.options[this.selectedIndex].value);\" ".$con_ty." style=\"width: 200px;\">' +		
		'<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +     
        '</select>'+";

        // MODELO
        echo "'<tr><td id=cpe_text>CPE model<td><div id=model_cpe><select name=model id=models style=width:200px>	<option  value=zhone-2427 "; if($prop['model']=='zhone-2427') echo "selected"; echo ">zhone-2427</option>		<option value=zhone-2727a "; if($prop['model']=='zhone-2727a') echo "selected"; echo ">zhone-2727a</option>		<option value=zhone-2428 "; if($prop['model']=='zhone-2428') echo "selected"; echo ">zhone-2428 (internet only)</option>		<option value=SFP "; if($prop['model']=='SPF') echo "selected"; echo ">SFP (DIA connection)</option> 		<option value=RF-conv "; if($prop['model']=='rfconv') echo "selected"; echo ">fibre (TV only))</option> 		</select></div>'+";


        // IDENTIFICAR SE E CHANGE OVER OU RECONNECTION
        echo "'<input type=hidden id=is_rec_input name=is_rec_input value=".$prop['is_reconnection'].">' +";
        echo "'<input type=hidden id=is_chg_input name=is_chg_input value=".$prop['is_changeover'].">' +";

        echo $form_customer_add;

        // FORMULARIO DA CHANGE OVER    
        echo "'<tr style=\"display: ".$teste_check_over."\"><td>is changeover<input type=checkbox name=is_changeover id=is_changeover ".$active." onchange=\"changeOverState(this)\"><input type=hidden id=is_changeover_val value=".$prop['is_changeover'].">' + ";
		echo "'<tr><td colspan=2><div id=conexao_changeOVER style=\"display: ".$dis_change_over."\">' +";
        echo"'<fieldset>' +
            '<legend>Change Over:</legend>' + ";

        // PROPERTY DA CHANGE OVER = 1 - DIFEFRENTE DA TECNOLOGIA DAS CONNECTIONS (!= GPON (POR EXEMPLO))  
        echo "'<label id=text_conn_prop>Property Change Over Connection:</label> <select name=refe id=refe onchange=con_prop_type(this.value) style=\"width: 600px\">";
		echo "</select><br>' + ";

        // PROPERTY DA CHANGE OVER = 1 - DIFEFRENTE DA TECNOLOGIA DAS CONNECTIONS (!= GPON (POR EXEMPLO))      
        echo "'<label>Connection: </label>' +
        '<select id=con_id name=con onchange=\"connection_equip(this.value)\" style=\"width: 150px; \">";
        echo "</select><br>";

        // SUBSCRIBER 
        echo "<label>Subscriber Change Over</label> <select name=owner_chg id=owner_chg style=\"width: 180px; \">";
        echo "</select><br>";

        // FORMULARIO DA CHNAGE OVER = 1
        echo "<div id=conn_type_fsan style=\"display: grid\">";
        echo "</div>";

        echo "</fieldset>";

        echo $form_chg_over;

        echo "</div>' +";

        // FIM DO FORMULARIO DA CHANGE OVER


        // SERVIÇOS
        
		echo "'<tr><td><br>Services'+";
		
        // DESATIVA O SERVIÇO TV NO CASO DA TYPE CONNECTION FOR FWA
		echo "'<tr><td>TV service<td><select name=tv id=tv ".$ds." style=\"width: 200px;\"><option value=0 "; if($prop['tv']==0) echo "selected"; echo ">no TV</option><option value=AMLA "; if($prop['tv']=="AMLA") echo "selected"; echo ">AMLA</option><option value=NOWO "; if($prop['tv']=="NOWO") echo "selected"; echo ">NOWO</option></select>'+	
		
		'<tr><td>Internet service<td><select name=internet_prof id=internet_prof style=\"width: 200px;\"><option value=0 "; if($prop['internet_prof']==0) echo "selected"; echo ">no internet</option>";

		$intservices=$mon3->query("select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down");
		while($serv=$intservices->fetch_assoc())
		{
			echo "<option value=".$serv['id'];
			if($prop['internet_prof']==$serv['id']) echo " selected";
			echo "> ".$serv['name']."</option>";
		}

		echo "</select>";

        echo " Fixed ip:<select name=fixed_ip style=\"width: 120px;\"> <option selected value= >no fixed ip</option>";
		$refs=$mon3->query("select ip from int_fixed_ips where in_use!=1 order by ip");
        while($ref=$refs->fetch_assoc())
        {
            echo "<option value=".$ref['ip'];
            if($prop['fixed_ip']==$ref['ip']) echo " selected";
            echo ">".$ref['ip']."</option>";
        }
        echo " </select>'+
		
		'<tr><td>phone line 1<td><select name=phone1 style=\"width: 200px;\"> <option selected value= >no line</option>";
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
        while($ref=$refs->fetch_assoc())
        {
            echo "<option value=".$ref['phone_number'];
            if($prop['phone1']==$ref['phone_number']) echo " selected";
            echo ">".$ref['phone_number']."</option>";
        }
        echo " </select><br>'+
		
		'<tr><td>phone line 1<td><select name=phone2 style=\"width: 200px;\"> <option selected value= >no line</option>";
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
        while($ref=$refs->fetch_assoc())
        {
            echo "<option value=".$ref['phone_number'];
             if($prop['phone2']==$ref['phone_number']) echo " selected";
            echo ">".$ref['phone_number']."</option>";
        }

        $prop['APs'] == "" ? 0 : $prop['APs'];
        $prop['monthly_price'] == "" ? 0 : $prop['monthly_price'];
        $prop['prev_rev_month'] == "" ? 0 : $prop['prev_rev_month'];

        echo " </select><br>'+

		'<tr><td>Need APs? how many?<td><input type=text name=aps size=5 value=\"".$prop['APs']."\" >un (250€ each)<br> '+
		'<tr><td>monthly revenue<td><input type=text name=monthly_price size=5 value=\"".$prop['monthly_price']."\" >€/month<br> '+
		
		'<tr>' +";
        echo "'<tr><td>Previous income /month <td><input type=text name=prev_rev_month size=5 value=\"".$prop['prev_rev_month']."\" >€<br> '+
	'</table>'+
	'';";
    echo "
	}	
    else if((soption >30) && (soption <34) )
	{";

	    echo "$('#warning_services').html('');
        $('#info_submit').html('');
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
	    $('#warning_services').html('');
        $('#info_submit').html('');
		textdiv += ' form to specify booked date'+
		'<table> '+
		'<tr><td>Date booked with customer<td><input type=text size=15 name=date_install value=\"";
		if($prop['date_install']!="") echo $prop['date_install']; else echo date("Y-m-d H:i",
		mktime(9,0,0,date("m"),date("d")+3,date("Y")));
		echo "\"><br> '+
		'</table>';
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	else if(soption==50)
	{
	    $('#warning_services').html('');
        $('#info_submit').html('');
	    ";
        $warn_at_serv = '';
        $dis_update_50= "";
        $v1 = 1;
        $val="";

        // IS RECONNECTION
        if($prop['is_reconnection'] == 1)
        {       
                $servicos_at = $mon3->query("SELECT * FROM `services` where connection_id=".$prop['lead_conn_id_rcn']);
                while($servico_at=$servicos_at->fetch_assoc())
                {
                    // DESATIVAR OS SERVICOS PARA CRIAR UM NOVO SERVIÇO

                    $servico_id = $servico_at['id'];
                    $serv_type = $servico_at['type'];


                    if($servico_at['date_end'] == '0000-00-00')
                    {
                        $serv_id_arr[] = $servico_id;
                        $serv_type_arr[] = $serv_type;
                    }
                }
                
                if($serv_id_arr!= null)
                {
                        for($l=0; $l<count($serv_id_arr); $l++)
                        {
                            $warn_at_serv .= "<font color=red>This Service number ".$serv_id_arr[$l]." on this connection number ".$conexao_id_status_warning_50['id']. " are ativated. Please deactivated one of services</font><br>";
                            $dis_update_50 = "disabled";
                            $v1 = 0;
                        }
                }

                $conexao=$mon3->query("SELECT * FROM connections where id = ".$prop['lead_conn_id_rcn']." ")->fetch_assoc();
                
                
        }
        else if($prop['is_changeover'] == 1)
        {
            $conexao=$mon3->query("SELECT * FROM connections where id = ".$prop['lead_conn_id_chg_over']." ")->fetch_assoc();
        }


        echo "var warn = '<font color=red>".$warn_at_serv."</font>'; ";        
        echo "textdiv += ' form to activate services'+
		'<table>";

        $eq_id = "";
        $olt_id=0;

        echo "<input type=hidden id=serv_act name=serv_act value=".$v1.">";
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
        }
        else
        {
            echo "<input type=hidden id=con_id name=con_id>";
        }

        // IDENTIFICAR O EQUIPMENTO SE FOR CHANGE OVER OU RECONNECTION
        if($conexao != null)
        {
            if($prop['is_reconnection'] == 1)
            {
                echo "<tr><td>Disable Services? <td><input type=checkbox name=dis_serv_rec id=dis_serv_rec value=disabled_service onclick=disabled_services_check_update(this)>";
                echo "<tr><td>Connection <font color=#663399>".$conexao['id']."</font>";
            }
            else if($prop['is_changeover'] == 1)
            {
                echo "<tr><td>OLD Connection <font color=#663399>".$conexao['id']."</font>";
            }
        }


    echo "<tr><td>Type<td> <span id=con name=connection_type>".$prop['con_type']."</span>";
    if($prop['con_type']=="") echo "GPON?(please select type on status 30)";


    echo "';";


    if($prop['con_type']=='GPON' || $prop['con_type']=='')
    {
        // IDENTIFICAR O EQUIPMENTO - ONT
        $modelo = $mon3->query("SELECT * FROM ftth_ont WHERE fsan = '".$eq_id."'")->fetch_assoc();
        if($modelo != null)
        {
            if($modelo['fsan'] != "")
            {
                $olt_id = $modelo['olt_id'];
            }
            else
            {
                $olt_id = 0;
            }           
        }
        else
        {
            $olt_id = 0;
        }

        if($eq_id == "")
        {
            $val = "";
        }
        else
        {
            $val = "value=".$eq_id;
        }
            echo"textdiv += '<tr><td>ONT FSAN<td><input type=text name=fsan id=fsan size=20 ".$val." oninput=equipConnectionAssoc(this.value) ><span id=equip_conn_assoc></span><input type=hidden name=equip_assoc_not id=equip_assoc_not value=0> <span id=warn_equip></span>'+	
            '<tr><td>CPE model<td><select name=model id=models onchange=ModelChg(this.value) style=\"width: 200px;\">		</select><span id=warn_model></span>' +
            
            '<tr><td>OLT<td><select name=olt_id id=olt_id onchange=\"updatepon(this.options[this.selectedIndex].value); updatevlan(this.options[this.selectedIndex].value);\" style=\"width: 180px;\"> ";

            echo "<option value=\"0\">Select OLT</option>";

            
            $olts=$mon3->query("select * from ftth_olt");
            while($olt=$olts->fetch_assoc()){ echo "<option value=".$olt['id'];
            if($olt['id']==$olt_id) echo " selected ";
            echo ">".$olt['name']."</option>";}



            echo"</select> <span id=warn_olt></span>";


            echo "<tr><td>PON<td><select id=pons name=pon onchange=PONChg(this.value) style=\"width: 180px;\">";
            if($olt_id == 0)
            {
                echo "<option value=\"0\">Select PON</option>";
            }
            
            $pons=$mon3->query("select card,pon,name from ftth_pons where olt_id=".$olt_id." order by name ");
            while($pon=$pons->fetch_assoc())
            {
                echo "<option value=".$pon['card']."-".$pon['pon'].">".$pon['card']."-".$pon['pon']." - ".$pon['name'];
            }
            echo "</select> <span id=warn_pon></span>';   ";
    }

    elseif($prop['con_type']=='FWA')
    {
        // IDENTIFICAR O EQUIPMENTO - FWA CPE
        $modelo = $mon3->query("SELECT * FROM fwa_cpe WHERE mac = '".$eq_id."'")->fetch_assoc();
        if($modelo != null)
        {
            if($modelo['mac'] != "")
            {
                $antenna = $modelo['antenna'];
            }
            else
            {
                $antenna = 0;
            }
            
        }
        else
        {
            $antenna = 0;
        }


        if($eq_id == "")
        {
            $val = "";
        }
        else
        {
            $val = "value=".$eq_id;
        }

        echo"		
            textdiv += '<tr><td>FWA cpe MAC<td><input type=text name=fsan id=fsan size=10 ".$val." oninput=equipConnectionAssoc(this.value)><span id=equip_conn_assoc></span><input type=hidden id=equip_assoc_not name=equip_assoc_not> <span id=warn_equip></span>'+	
            '<tr><td>CPE model<td><select name=model id=models onchange=ModelChg(this.value) style=\"width: 200px;\">		</select><span id=warn_model></span>' +
            '<tr><td>FWA antenna<td><select name=antenna id=antenna onchange=AntennaChg(this.value) style=\"width: 180px;\"> ";
            if($antenna == 0)
            {
                echo "<option value=\"0\">Select Antenna</option>";
            }
            $antennas=$mon3->query("select * from fwa_antennas");
            while($antenna=$antennas->fetch_assoc()){ echo "<option value=".$antenna['id'];
            if($antenna['id']==$antenna) echo " selected ";
            echo ">".$antenna['name']."</option>";}


            echo"</select> <span id=warn_antenna></span>';	";


    }
    else
    {
        echo" textdiv += '<br> Please select connection type at status 30	';";
    }







    echo "textdiv += ' <tr><td><br>Services'+";

    if($prop['con_type']=='FWA')
    {
        $ds = "disabled";
    }
    else
    {
        $ds = "";
    }



        
    echo "'<tr><td>TV service<td><select name=tv ".$ds." style=\"width: 200px;\"><option value=0 "; if($prop['tv']==0) echo "selected"; echo ">no TV</option><option value=AMLA "; if($prop['tv']=="AMLA") echo "selected"; echo ">AMLA</option><option value=NOWO "; if($prop['tv']=="NOWO") echo "selected"; echo ">NOWO</option></select>'+	
    
        
    
    
    
    
    '<tr><td>Internet service<td><select name=internet_prof style=\"width: 200px;\"><option value=0 "; if($prop['internet_prof']==0) echo "selected"; echo ">no internet</option>";


    $intservices=$mon3->query("select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down");
    while($serv=$intservices->fetch_assoc())
    {
        echo "<option value=".$serv['id'];
        if($prop['internet_prof']==$serv['id']) echo " selected";
        echo "> ".$serv['name']."</option>";
    }


    echo "</select>'+	
    'Fixed ip:<select name=fixed_ip style=\"width: 120px;\"> <option ";
    if($prop['fixed_ip']=="") echo "selected"; echo " value=>no fixed ip</option>";
    $refs=$mon3->query("select ip from int_fixed_ips where in_use!=1 order by ip"); while($ref=$refs->fetch_assoc()){ echo "<option "; if($prop['fixed_ip']==$ref['ip']) echo "selected"; echo " value=".$ref['ip'].">".$ref['ip']."</option>";} echo " </select>'+
    
    '<tr><td>router mode<td><select name=is_router style=\"width: 200px;\"> <option value=1>Router</option> <option value=0>Bridge on eth1</option></select>    <br> '+	


    '<tr><td>vlan<td><select id=vlans name=vlan style=\"width: 200px;\">";
    $vlans=$mon3->query("select vlan,description,total_dynamic_ips,olt_id from int_vlans where olt_id=".$prop['olt_id']." ");
    while($vlan=$vlans->fetch_assoc())
    {
        //olt_id
        $inuse=$mon3->query("select count(name) from service_attributes where name=\"vlan\" and value=\"".$vlan['vlan']."\"  ")->fetch_assoc();
        echo "<option value=".$vlan['vlan'];
        echo ">".$vlan['description']." - ".$inuse['count(name)']." of ".$vlan['total_dynamic_ips'];
    }
    echo "</select>  '+
    
    
    '<tr><td>Wifi<td><select name=wifi style=\"width: 200px;\"><option value=1 selected>enabled</option><option value=0>disabled</option></select> '+	
    '<tr><td>Wifi SSID<td><input type=text name=wifi_ssid size=10 value=Lazer_".explode(" ",$prop['address'])[0]."><br> '+	
    '<tr><td>Wifi passwd<td><input type=text name=wifi_key size=10 value=lzr".substr($refc['ref'],3,3).strtolower(substr($refc['ref'],0,3))."> <tr><td><br> '+	
    
    
    
    
    
    
    
    
    '<tr><td>phone line 1<td><select name=phone1 style=\"width: 200px;\"> <option "; if($prop['phone1']=="") echo " selected "; echo " value= >no line</option>";
    $refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
    while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'];
    if($prop['phone1']==$ref['phone_number']) echo " selected ";
    echo ">".$ref['phone_number']."</option>";}
    echo " </select><br>'+
    
    '<tr><td>phone line 2<td><select name=phone2 style=\"width: 200px;\"> <option "; if($prop['phone2']=="") echo " selected "; echo " value= >no line</option>";
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
    $('#warning_services').html('');
    $('#info_submit').html('');
    textdiv += ' form to close jobsheet'+
    '<table>'+
    '<tr><td>Job sheet id<td><input type=text name=installation_job_id size=10><br> '+	
    '<tr><td>technician<td><select name=technician style=\"width: 200px;\">";
            $refs=$mon3->query("select username from users where is_tech=1 order by username");
            while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['username'].">".$ref['username']."</option>";}
    echo "</select><br> '+
    '</table>'
    ;

}









else if(soption==60)
{
    $('#warning_services').html('');
    $('#info_submit').html('');
    textdiv += ' form to close jobsheet and evaluate the connection'+
    '<table>'+
    '<tr><td>Job sheet id<td><input type=text name=installation_job_id size=10><br> '+	
    '<tr><td>technician<td><select name=technician style=\"width: 200px;\"> <option selected></option>";
            $refs=$mon3->query("select username from users where (is_tech=1 or is_subcont=1) order by username"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['username'].">".$ref['username']."</option>";}
    echo "</select><br> '+	
    '<tr><td>NPS score<td><select name=NPS_score style=\"width: 200px;\">";
    for($i=0;$i<11;$i++)echo "<option value=$i>$i</option>";
    echo"</select> <br> '+	
    '<tr><td>Install manager score<td><select name=manager_score style=\"width: 200px;\">";
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
    $('#warning_services').html('');
    $('#info_submit').html('');
    textdiv='';
}








document.getElementById('additionalset').innerHTML = textdiv;

$('#warning_services').html(warn);


$('select').select2();  


    updatecpe('";
        if($prop['con_type']=='') echo "GPON";
        else echo $prop['con_type'];

        echo "','".$prop['model']."','".$_GET['lead_id']."' ); 
";

echo " if(soption==7) {
    console.log($('#disabled_prop_services:checked').length);
    changePropServicesDisabled('', $('#disabled_prop_services:checked').length);


}";


echo " if(soption==30) {
        updateInternet('";
        if($prop['con_type']=='') echo "GPON";
        else echo $prop['con_type'];

        echo "'); 
    
    
    }";


if($prop['is_changeover'] == 1)
{
    echo " 
      changeConOver('";
        if($prop['con_type']=='') echo "GPON";
        else echo $prop['con_type'];

        echo "'); ";
}


echo "

}




</script>
";

        $cond = "";

        if($prop['is_reconnection'] == 1 && $prop['is_changeover'] == 0 )
        {
            $cond = $prop['status']<38;
        }
        else if($prop['is_reconnection'] == 0 && $prop['is_changeover'] == 1)
        {
            $cond = $prop['status']<31;
        }
        else if($prop['is_reconnection'] == 0 && $prop['is_changeover'] == 0)
        {
            $cond = $prop['status']<31;
        }

        


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
if ( $localuser['is_plan']==0 || ($prop['status']>19 && $prop['status']<99)) echo " disabled ";
echo "> 7 - Property Reconnection</option>

<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Customer Decision</option>
<option value=9 
"; if ($prop['status']==9) echo "selected";
if ($prop['status']==0 || ($prop['status']!=3 && $prop['status']!=4)) echo " disabled "; echo "
>9- Customer is notified, Not possible </option>


<option value=10 
"; if ($prop['status']==10) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>10- Customer is notified, waiting for reply </option>

<option value=11 
"; if ($prop['status']==11) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>11- Not accepted by customer  - note with justification  </option>

<option value=12 
"; if ($prop['status']==12) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>12- Accepted by customer, collecting paperwork</option>

<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled>  Quote for install costs(optional)</option>


<option value=13 
"; if ($prop['status']==13) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>13- paperwork ready, tech dep. to quote final price </option>

<option value=14 
"; if ($prop['status']==14) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>14- paperwork ready, quote ready, see notes </option>
<option value=15 
"; if ($prop['status']==15) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>15- Customer is notified about quote, waiting for reply </option>

<option value=19 
"; if ($prop['status']==19) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>19- Not accepted by customer  - note with justification </option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled>Internal process</option>

<option value=20 
"; if ($prop['status']==20) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6)) echo " disabled "; echo "
>20- All approved, paperwork ready, to be insert into system </option>


<option value=21 
"; if ($prop['status']==21) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==0) echo " disabled "; echo "
>21- On hold until paperwork is completed </option>


<option value=29 
"; if ($prop['status']==29) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==0) echo " disabled "; echo "
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
if ($prop['status']<30 || $localuser['is_plan']==0) echo " disabled "; echo "
>31- Process started </option>


<option value=32
"; if ($prop['status']==32) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>32- Authorizations started- ORAC/ORAP infralobo, schematics, etc </option>

<option value=33
"; if ($prop['status']==33) echo "selected";  if ($prop['status']<31) echo " disabled "; echo "
>33- Needs networking (note with scheduling/jobsheet id) </option>

<option value=38
"; if ($prop['status']==38) echo "selected";  if ($prop['status']<31 || ($prop['is_reconnection'] == 0)) echo " disabled "; echo "
>38 - NOC check equipment </option>

<option value=\"\" disabled></option>
<option value=\"0\" disabled> Booking </option>

<option value=40 
"; if ($prop['status']==40) echo "selected";  if ($cond) echo " disabled "; echo "
>40- Network ready, can be booked w customer (sends email to customer)</option>

<option value=41 
"; if ($prop['status']==41) echo "selected";  if ($cond) echo " disabled "; echo "
>41- Booked with customer (notes with date and time) </option>

<option value=42 
"; if ($prop['status']==42) echo "selected";  if ($cond) echo " disabled "; echo "
>42- Infrastucture issues waiting on us to reschedule </option>

<option value=43 
"; if ($prop['status']==43) echo "selected";  if ($cond) echo " disabled "; echo "
>43- Infrastucture issues waiting on customer </option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled> Installation </option>
<option value=50 
"; if ($prop['status']==50) echo "selected";
if ($prop['status']<40) echo " disabled "; echo "
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
//aditionalstatus();
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
<tr><td colspan=2 align=center><br> <br> <input type=submit name=supdatelead value=update >	
</form></table>	<br><br>


";

if($_FILES['randfile'])
{
$var_rec = "";
$countfiles = count($_FILES['randfile']['name']);
for($i=0;$i<$countfiles;$i++){
    if(file_exists($_FILES['randfile']['tmp_name'][$i]))
    {
        $ext=explode(".",$_FILES['randfile']['name'][$i]);
        $var_rec .= uploadfile("randfile",$mon_leads.$lead_id."/", date("Y-m-d_His")."_".
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

//echo $_POST['file_rem'];

if(isset($_POST['removeFILE_lead']))
{

    $rem = $_POST['file_rem'];

    $val_rem_file_leads = remove_file($rem, $mon_leads);

//echo $val;

?>

<script>
    var val_rem_file_leads = "<?php echo $val_rem_file_leads; ?>";
    $("#warning_services").html(val_rem_file_leads);
</script>

<?php

}

$is_images = '';

echo"
<table width=900px><tr><td>
Files:<br>
<tr><td colspan=2 align=center>";

$i=0;
if(file_exists($mon_leads.$lead_id))
{
    $files1 = scandir($mon_leads.$lead_id);
    
    
    foreach($files1 as $file1){
        if(substr($file1,0,1)!=".")
        {
            if($i%3==0)
            {
                $is_images .="<tr>";
            }

            if(strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpeg")
            {
                $is_images .= "<td align=center><a href=leads/".$lead_id."/".$file1." class=link_slider target=_blank>";
                $is_images .= "<img src=leads/".$lead_id."/".$file1." height=100px alt=".$file1." class=img_slider > <br> $file1 </a> ";
            }
                
            else
            {
                $is_images .= "<td align=center> <a href=leads/".$lead_id."/".$file1." class=link_pdf target=_blank>";
                $is_images .= "<img src=img/file.png height=100px class=\"img_pdf\" alt=".$file1."> <br> $file1 </a> ";
            }
            $i++;
            
        }
    }

    
}


    echo "<table class=bod-modal data-title=center ><tr>";
    echo $is_images;
    echo "</tr></table>";



    ?>
      
    <?php




echo"<table width=900px>
<tr><td colspan=2 align=center><br><br><b>upload new file(.jpg or .pdf)</b><br>
<form name=addrandfile method=post enctype=\"multipart/form-data\" action=index.php?propleads=1&lead_id=".$lead_id.">
<label for=fileInput> 
<img id=icon´ height=100px src=\"img/upload.png\" style=\"cursor: pointer;\">
</label>
<input type=file name=randfile[] accept=\".pdf,image/jpeg\" id=fileInput multiple style=\"display:none;\" onchange=\"this.form.submit()\">
</form>



";

?>

<?php



$prop_point=$mon3->query("select * from property_leads where id=$lead_id;")->fetch_assoc();

$prop_point = remove_element_array($prop_point, "point");
$prop_point = remove_element_array($prop_point, "lead_conn_id_chg_over");
$prop_point = remove_element_array($prop_point, "lead_conn_id_rcn");
echo "<br><br> <br><br><br><br>Dump DB</table><table style=\"left: 0;display: block;\">";
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
    $gg=$mon3->query("insert into property_leads (address,freguesia,coords,name,email,phone,agent_id,prop_id,status,date_lead,date_modified,contract_id,created_by) values(
    
    \"".$address."\", 
    \"".$freg."\", 
    \"".$coords."\", 
    \"".$name."\", 
    \"".$email."\",
    \"".$phone."\",		
    \"".$agent."\",
    \"0\",
    \"0\",
    \"".date("Y-m-d")."\",
    \"".date("Y-m-d")."\",
    \"0\",
    \"".$_SERVER['PHP_AUTH_USER']."\"
    ) ;");
echo mysqli_error($mon3);
    $lead_id=$mon3->insert_id;



    $assunto="Lazer - Lead $lead_id ($address) created";
    $corpo="<html><b>Server Test</b> <br>";
    $corpo.= "
    Dear ".$localuser['name']."<br><br>
    Your lead <a href=".MON_SERVER."?propleads=1&lead_id=$lead_id>$lead_id</a> was just created.<br><br>
    Please wait while the planing team analyzes your request
    
    <br><br>Regards,<br>The System</html>";
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

<tr><td>	<div id=divaddr>Address: <font color=red>*</font></div>
<td> <input type=text name=address size=60 id=address onkeyup=\"return validateForm()\"> <br>";

echo "
<tr><td>Concelho:<td><select name=concelho id=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\" style=width:400px>";
$concs=$mon3->query("select * from concelhos  order by distrito,concelho;");

while($conca=$concs->fetch_assoc())
{
echo "<option value=".$conca['id'];
if ($conca['id']==176)
    echo " selected";
echo ">".$conca['distrito']." - ".$conca['concelho']."</option>";
}
echo"</select>";

echo "<tr><td>Freguesia:<td><select name=freg id=freg style=width:400px>";
$fregs=$mon3->query("select * from freguesias where concelho=176;");
while($frega=$fregs->fetch_assoc())
{
echo "<option value=".$frega['id'];
if ($frega['id']==1160)
    echo " selected";
echo ">".$frega['freguesia']."</option>";
}
echo"</select>";

echo "<tr><td>Country:
<td><select name=country class=\"country\"onchange=updateconcelhosep(this.options[this.selectedIndex].value) style=\"width: 150px;\">
<option value=PORTUGAL selected>Portugal</option>
<option value=Espanha>Spain</option>
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
    $corpo="<html><b>Server Test</b> <br>";
    $corpo.= "
     Dear ".$localuser['name']."<br><br>
    Your lead <a href=".MON_SERVER."?propleads=1&lead_id=$leadid>$leadid</a> was modified.<br><br>
    $notes<br>

    
    <br><br>Regards,<br>The System</html>";
    if($rstatus==1)
    $emails=$localuser['email'].";".$planing_email;
    else
    $emails=$localuser['email'];

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
<input type=hidden name=id size=60 id=address value=\"".$lead['address']."\">";
echo "
<tr><td>Concelho:<td><select id=concelho name=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value)\" style=width:400px>";
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
</select>";
echo "
<tr><td>Freguesia:<td><select name=freg id=freg style=width:400px>";
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
</select>";

echo "
<tr><td>Country:
<td><select name=country class=\"country\" onchange=updateconcelhosep(this.options[this.selectedIndex].value) style=\"width: 150px;\">
<option value=PORTUGAL selected>Portugal</option>
<option value=Espanha>Spain</option>
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

<select name=property style=\"width: 180px;\">
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
    {
        $qwhere.=" AND created_by=\"$owner\" ";
    }
    if($sstatus!="all" && $sstatus!="")
    {
        $qwhere.=" AND status=\"$sstatus\" ";
    }    
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

    //var infoWindow = new google.maps.InfoWindow();
//            infoWindow.setPosition(pos);
//            infoWindow.setContent('Location found.');
//            infoWindow.open(map);
        map.setCenter(pos);
      }, function() {
        //handleLocationError(true, infoWindow, map.getCenter());
      });
    } 
    else 
    {
      
      //handleLocationError(false, infoWindow, map.getCenter());
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
Search: <input type=text name=searchb Onkeyup=\"searchlead(this.value,'".$_GET['status']."','".$_GET['owner']."')\" ";


if(isset($_GET['searchb']))
{

    echo " value=\"$searchb\"";

    $qwhere= " where (address LIKE '%".$searchb."%' or name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%')";

}


echo">  
<input type=hidden name=propleads value=1>
<input type=hidden name=owner value=$owner>
<input type=hidden name=status value=$sstatus>
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
    <select name=filt_op onchange=this.form.submit() style=\"width: 110px\">
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
    <input type=hidden name=status value=$sstatus>
    
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
<input type=hidden name=filt_op value=$filter>
<input type=hidden name=owner value=$owner>
<input type=hidden name=searchb value=$searchb>
<select name=status onchange=this.form.submit() style=\"width: 100px\">";

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
<input type=hidden name=filt_op value=$filter>
<input type=hidden name=status value=$sstatus>
<input type=hidden name=searchb value=$searchb>
<select name=owner onchange=this.form.submit() style=\"width: 110px\">";

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


/*echo "select id,address,name,agent_id,status,date_lead,created_by,notes from property_leads ".$qwhere.
" order by status,date_modified desc limit ".$offset.",50 ";*/

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
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=0&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus>|<</a> ";
}
//print page -2
if($curpage>2)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage-3)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus>".($curpage-2) ."</a> ";
}
//print page -1
if($curpage>1)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage-2)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus>".($curpage-1) ."</a> ";
}
//print curpage

    echo " <b> $curpage </b> ";
//print page -1
if($curpage<$lastp)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus>".($curpage+1) ."</a> ";
}
if($curpage<$lastp-1)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($curpage+1)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus>".($curpage+2) ."</a> ";
}

    if($curpage<$lastp)
{
    echo "<a href=?propleads=1&searchb=$searchb&propleadslist=1&offset=".($lastp-1)*50 ."&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus>>|</a> ";
}

}
echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results</div>";

echo "<br><a href=# id=click_dump_leads>dump leads</a>";
?>
<script>
$("#click_dump_leads").on('click', function()
{
    var filter = $("select[name=filt_op]").val();
    var status = $("select[name=status]").val();
    var owner = $("select[name=owner]").val();
    var searchb = $("input[name=searchb]").val();
    var username = "<?php echo $localuser['username']; ?>"
    var url_leads = "webservice.php?dump_leads=1&filter="+filter+"&owner="+owner+"&filt_op="+filter+"&status="+status+"&searchb="+searchb+"&username="+username;
    //console.log(url_leads);
    //&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus
	//console.log("webservice.php?dump_prop=1&searchb="+$("input[name=searchb]").val());
	location.href = url_leads;
});
</script>	
<?php
}







