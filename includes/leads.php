<?php
// EMAILS DESATIVADOS
$mon_leads = MON_ROOT."/leads/";
$mon_prop = MON_ROOT."/properties/";

// $operations_email = "operations@lazerspeed.com";


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
// NOME DO UTILIZADOR
echo "<br> created by: <span id=localuser_username>".$prop['created_by']."</span><br> ";
// MOSTRA OS AVISOS SE A CONNECTION TEM SERVICOS ATIVOS
echo "<div id=warning_services></div> ";
// MOSTRA O SUCESSO DA SUBMISSAO DAS LEADS
echo "<div id=info_submit></div>";
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
    //$status=30;
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

        /*
        echo $mon3->error;
		echo "<font color=green>Saved</font>";
		
		$dest_email=$created_by['email'];
		
		
		if($status==0||$status==6||$status==13)
			$dest_email.=";".$planing_email;
		if($status>=20 && $status<30)
			$dest_email.=";".$intproc_email;
		if($status>=30 && $status<=33)
			$dest_email.=";".$planing_email.";".$installs_email.";".$salesm_email;	
        if($status==38)
			$dest_email.=";".$operations_email;	    
		if($status>=40 && $status<=43)
			$dest_email.=";".$installs_email;	
		if($status==50)
			$dest_email.=";".$installs_email.";".$salesm_email;
		if($status==60)
			$dest_email.=";".$installs_email.";".$intproc_email;
        */


		$assunto="Lazer - Lead $lead_id (".$prop['address'].") updated";
		$corpo= "
		<html> Dear".$created_by['name']."<br><br>
		Your lead <a href=".MON_SERVER."?propleads=1&lead_id=$lead_id>$lead_id</a> was updated.<br><br>
		Current status: $status <br><br> 
		
		<br><br>Regards,<br>The System</html>";


		//colocar datas consoante estados

		// status de viabilidades/
		if(($status>0 && $status<5)||$status==14)
		{
            // CONNECTION TYPE & MODEL
            $con_type=mysqli_real_escape_string($mon3, $_POST['con_type']);
            $model=mysqli_real_escape_string($mon3, $_POST['model']);
            
            // CHANGE OVER = 1
            $chg_over = $_POST['changeover_form'];
            // RECONNECTION = 1
            $reconn = $_POST['reconnection_form'];
            // NEW CONNECTION = 1
            $new_conn = $_POST['new_conn_form'];
            // WHERE PARA ACTUALIZAR O FORMULARIO DA LEAD 1,2,3,4
            $where_update_lead_1 = "";

            // FORMULARIO DE RECEBER AS FIEDLS SE FOR CHANGE OVER OU NEW CONNECTION
            if($chg_over == 1 || $new_conn == 1)
            {


                
                $drop_length=mysqli_real_escape_string($mon3, $_POST['drop_length']);
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

                if($new_conn == 1)
                {
                    $is_reconnection = 0;
                    $is_changeover = 0;
                    $where_update_lead_1 .= ",is_changeover=\"$is_changeover\", is_reconnection=\"$is_reconnection\",prop_id=\"0\",
                    lead_conn_id_chg_over=\"0\",lead_conn_id_rcn=\"0\",";
                }
                

                // DADOS DE SUBMISSAO DO FORMULARIO DA LEAD 1 SE FOR CHANGE OVER
                if($chg_over == 1)
                {

                    $is_reconnection = 0;
                    $is_changeover = 1;

                    // PROP ID DA CHANGE OVER
                    $refe = mysqli_real_escape_string($mon3, $_POST['refe']);
                    // CONNECTION ID DA CHANGE OVER
                    $conn_id = mysqli_real_escape_string($mon3, $_POST['con']);
                    // LEAD SUB DO FORMUALRIO DA CHANGE OVER
                    $lead_sub = mysqli_real_escape_string($mon3, $_POST['owner_chg']);

                    // SE TIVER A PROP ID VAI BUSCAR A REFERENCIA PARA BUSCAR A OLT DA AREA
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


                    // CHANGE OVER = 1, RECONNECTION = 0
                    $conn_chg_over = $conn_id;
                    $conn_id_rec = 0;

                    // LEAD 1 QUE VAI ACTUALIZAR 
                    // CHANGE OVER = 1, RECONNECTION = 0, LEAD_SUB = OWNER DO FORMULARIO DA CHANGE OVER 'OWNER_CHG', PROP_ID = PROPPRIEDADE DO FORMUALRIO DA CHANGE OVER 'REFE'
                    // lead_conn_id_chg_over = 'FORMUALRIO DA CONNECTION ID DA CHANGE OVER'
                    $where_update_lead_1 .= ",olt_id=\"$olt\", prop_id= \"$refe\", 
                    is_changeover=\"$is_changeover\", is_reconnection=\"$is_reconnection\",
                    lead_conn_id_chg_over=\"$conn_chg_over\",lead_conn_id_rcn=\"$conn_id_rec\",";

                }

                

                // SE TIVER VALORES DO FORMULARIO DA CHANGE OVER = 1
                $where_update_lead_1 == "" ? $where_update_lead_1 .="," : $where_update_lead_1;
                $where_update_lead_1 .=" ORAC_pits=\"$ORAC_pits\",
                drop_length=\"$drop_length\",
                ORAP_poles=\"$ORAP_poles\",
                connection_cost=\"$connection_cost\",
                network_cost=\"$network_cost\",
                is_network_ready=\"$is_network_ready\",
                estimated_quote=\"$estimated_quote\",
                timeframe=\"$timeframe\",
                quoted=\"$quoted_id\"";

            }

            // RECONNECTION
            else if($reconn == 1)
            {
                // PROP ID DA RECONNECTION
                $refe_rec_7 = mysqli_real_escape_string($mon3, $_POST['refe_rec_7']);
                // CONNECTION ID DA RECONNECTION DA PROP ID
                $conn_id_prop_rec = mysqli_real_escape_string($mon3, $_POST['conn_id_prop_rec']);

                $is_reconnection = 1;
                $is_changeover = 0;
                // LEAD 1 QUE VAI ACTUALIZAR 
                // CHANGE OVER = 0', RECONNECTION = 1,
                // prop_id='PROPERTY ID DA RECONNECTION',
                // lead_conn_id_rcn='LEAD REL CON DA RECONNECTION'
                $where_update_lead_1 .= ",prop_id=\"$refe_rec_7\",	
                is_changeover=\"$is_changeover\",
                is_reconnection=\"$is_reconnection\",
                lead_conn_id_rcn=\"$conn_id_prop_rec\",
                lead_conn_id_chg_over=\"0\"";


            }

            //echo $where_update_lead_1;


            $query_status_1 = "update property_leads set date_viability=\"".date("Y-m-d")."\",
            con_type=\"$con_type\",
            model=\"$model\"
            ".$where_update_lead_1."
            where id=$lead_id ";

            echo $query_status_1;


            // SUBMISSAO DE RESPOSTAS A QUERY SE DEU SUCESSO A ACTUALIZACAO DAS LEADS OU DEU ERRO
            $update_lead_status_1 = $mon3->query($query_status_1);


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


            if($status==1)
            {
                if($update_lead_status_1)
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
                if($update_lead_status_1)
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
                if($update_lead_status_1)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." not viability network, out of network</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." not viability network, out of network</font><br>";
                }
            }
            else if($status==4)
            {
                if($update_lead_status_1)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." not viability network, not infrastrutures</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." not viability network, not infrastrutures</font><br>";
                }
            }
            else if($status==14)
            {
                if($update_lead_status_1)
                {
                    $succ .= "<font color=green>Update lead number ".$lead_id." on status ".$status." paperwork ready, network ready</font><br>";
                }
                else
                {
                    $error .= "<font color=red>Error on update lead number ".$lead_id." on status ".$status." paperwork ready, network ready</font><br>";
                }
            }

                        
                        

                    // INFORMACOES DE SUBMISSAO AO ACTUALIZAR OS ESTADOS DE UMA DADA LEAD
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

            // TYPE PROCEDURES
            // CHANGE OVER = 1
            $chg_over = $_POST['changeover_form'];
            // RECONNECTION = 1
            $reconn = $_POST['reconnection_form'];
            // NEW CONNECTION = 1
            $new_conn = $_POST['new_conn_form'];

            // FORMS INITIALS
            $address=mysqli_real_escape_string($mon3, $_POST['address']);
            $concelho=mysqli_real_escape_string($mon3, $_POST['concelho']);
            $freg=mysqli_real_escape_string($mon3, $_POST['freg']);
            $ref=mysqli_real_escape_string($mon3, $_POST['ref']);
            // LEAD SUB DO FORMUALRIO DA RECONNECTION
            // NOVO CLIENTE DE UMA NOVA PROPRIEDADE DE UMA NOVA CONNECTION
            $owner_id=mysqli_real_escape_string($mon3, $_POST['owner_id']);
            $mng_id=mysqli_real_escape_string($mon3, $_POST['mng_id']);
            $contract_id=mysqli_real_escape_string($mon3, $_POST['contract_id']);  
            $var_contract_upl = uploadfile("contract",$mon_leads.$lead_id."/", "contract_".time().".pdf",0,0);

            // CONNECTION TYPE AND MODEL
            $con_type=mysqli_real_escape_string($mon3, $_POST['con_type']);
            $model=mysqli_real_escape_string($mon3, $_POST['model']);

             // CHANGE OVER
             // PROP ID DA CHANGE OVER
             $refe = mysqli_real_escape_string($mon3, $_POST['refe']);
             // CONNECTION ID DA CHANGE OVER
             $con_id = mysqli_real_escape_string($mon3, $_POST['con']);
             // LEAD SUB DO FORMUALRIO DA CHANGE OVER
             $owner_chg = mysqli_real_escape_string($mon3, $_POST['owner_chg']);
 
 
             // RECONNECTION
             // PROP ID DA RECONNECTION
             $refe_rec_7= mysqli_real_escape_string($mon3, $_POST['refe_rec_7']);
             // CONNECTION ID DA RECONNECTION
             $conn_id_prop_rec = mysqli_real_escape_string($mon3, $_POST['conn_id_prop_rec']);
             // LEAD SUB DA RECONNECTION
             $owner_rec = mysqli_real_escape_string($mon3, $_POST['owner_rec']);


            
            // SERVICES
            // TV
            $tv=mysqli_real_escape_string($mon3, $_POST['tv']);
            // INTERNET
            $internet_prof=mysqli_real_escape_string($mon3, $_POST['internet_prof']);
            //echo $internet_prof;
            // IP FIXO
            $fixed_ip=mysqli_real_escape_string($mon3, $_POST['fixed_ip']);
            // PHONE 1 E 2
            $phone1=mysqli_real_escape_string($mon3, $_POST['phone1']);
            $phone2=mysqli_real_escape_string($mon3, $_POST['phone2']);


            // OTHERS FORMS
            $aps=mysqli_real_escape_string($mon3, $_POST['aps']);
            $monthly_price=mysqli_real_escape_string($mon3, $_POST['monthly_price']);
            $prev_rev_month=mysqli_real_escape_string($mon3, $_POST['prev_rev_month']);

           

            $where_update_lead_30 = "";

            // CHANGE OVER & RECONNECTION

            if($chg_over == 1 || $reconn == 1)
            {
                // CHANGE OVER 

                if($chg_over == 1)
                {
                    // CONNECTION 
                    $conn_id_prop_rec = 0;

                    // SE TIVER A PROP ID VAI BUSCAR A REFERENCIA PARA BUSCAR A OLT DA AREA
                    $prop_rfe = $mon3->query("select * from properties where id=\"$refe\" ")->fetch_assoc();
                    $refas_prop = substr($prop_rfe['ref'], 0, strlen($prop_rfe['ref']) - 3);
                    $olt=$mon3->query("select olt_id from area_codes where areacode=\"$refas_prop\" ")->fetch_assoc();
                    // IDENTIFICAR A OLT DA REF DA PROP ID ESCOLHIDA SE FOR CHANGE OVER
                    $olt=$olt['olt_id'];

                    // FREGUESIA DA PROP ID ESCOLHIDA SE FOR CHANGE OVER
                    $freg = $prop_rfe['freguesia'];
                    
                    // UPDATE LEAD ESTADO 30
                    // ENDERECO, FREGUESIA, PROPRIEDADE ID (CHANGE OVER), OLT, LEAD SUB DA CHANGE OVER (OWNER CHG)
                    // CHANGE OVER = 1 
                    $where_update_lead_30_change_over .= ",address=\"$address\",
                    freguesia=\"$freg\",
                    prop_id=\"$refe\",
                    olt_id=\"$olt\",
                    lead_sub=\"$owner_chg\",
                    prev_rev_month=\"$prev_rev_month\"";




                    


                }
                // RECONNECTION
                else if($reconn == 1)
                {
                    $con_id = 0;
                    // SE TIVER A PROP ID VAI BUSCAR A REFERENCIA PARA BUSCAR A OLT DA AREA
                    $prop_rfe = $mon3->query("select * from properties where id=\"$refe_rec_7\" ")->fetch_assoc();
                    $refas_prop = substr($prop_rfe['ref'], 0, strlen($prop_rfe['ref']) - 3);
                    $olt=$mon3->query("select olt_id from area_codes where areacode=\"$refas_prop\" ")->fetch_assoc();
                    // IDENTIFICAR A OLT DA REF DA PROP ID ESCOLHIDA SE FOR CHANGE OVER
                    $olt=$olt['olt_id'];

                    // FREGUESIA DA PROP ID ESCOLHIDA SE FOR RECONNECTION
                    $freg = $prop_rfe['freguesia'];
                    // UPDATE LEAD ESTADO 30
                    // ENDERECO, FREGUESIA, PROPRIEDADE ID (RECONNECTION), OLT, LEAD SUB DA RECONNECTION(OWNER ID)
                    // CHANGE OVER = 1 
                    $where_update_lead_30_reconnection .= ",address=\"$address\",
                    freguesia=\"$freg\",
                    prop_id=\"$refe_rec_7\",
                    olt_id=\"$olt\",
                    lead_sub=\"$owner_rec\"";
                }
            }
            else
            {
                $conn_id_prop_rec = 0;
                $con_id = 0;

                // IDENTIFICAR A OLT ID DA REFERENCIA DA PROPRIEDADE
                $olt=$mon3->query("select olt_id from area_codes where areacode=\"$ref\" ")->fetch_assoc();
			    $olt=$olt['olt_id'];

                // SE TIVER A PROPRIEDADE ID DA REFERNCIA CORRESPONDENTE PARA CRIAR UMA NOVA PROPRIEDADE


                                // BUSCAR A ULTIMA REFERENCIA DA PROPRIEDADE ID
                                $lastref=$mon3->query("select ref from properties where ref like \"$ref"."%\" order by ref desc")->fetch_assoc();
                                // IDENTIFICAR A ULTIMA REFERENCIA + 1 QUE ESTA INSERIDO NA AREA CODE NA BASE DE DADOS
                                $nref=substr($lastref['ref'],0,3).sprintf( '%03d',substr($lastref['ref'],3,3)+1);

                                // CRIAR UMA NOVA PROPRIEDADE SE FOR NEW CONNECTION
                                $mon3->query("insert into properties 
                                (ref, address, freguesia,coords,owner_id,management,date)
                                values (\"$nref\" , \"$address\" , \"$freg\" ,\"".$prop['coords']."\", \"$owner_id\",
                                \"$mng_id\", \"".date("Y-m-d")."\" )");
                                echo mysqli_error($mon3);
                                // IDENTIFICAR A PROPRIEDADE ID 
                                $propid=$mon3->insert_id;

                // NEW CONNECTION PARA ACTUALIZAR A LEAD 30
                // ENDERECO, FREGUESIA, PROPRIEDADE ID CRIADA, MES ANTERIOR DE CUSTOS
                $where_update_lead_30_new_connection .= ",address=\"$address\",
                freguesia=\"$freg\",
                prop_id=\"$propid\",
                olt_id=\"$olt\",
                prev_rev_month=\"$prev_rev_month\"";
            }

            // UPDATE DA LEAD NO ESTADO 30
            // MODELO, CONNECTION, IS CHANGE OVER, IS RECONNECTION, DATA DA MODIFICACAO, DATA DO CONTRATO
            // TV, INTERNET, FIEXD IP, PHONE 1, PHONE 2, APS, CONNECTION ID DA CHANGE OVER OU RECONNECTION
            // FORMULARIO DE CHANGE OVER
            // FORMULARIO DE RECONNECTION
            // FORMULARIO DA NEW CONNECTION
            $where_update_lead_30 .= "update property_leads set   
            model=\"$model\",
            con_type=\"$con_type\",
            is_changeover=\"$chg_over\",
            is_reconnection=\"$reconn\",          
            date_modified=\"".date("Y-m-d")."\",
            date_papwk=\"".date("Y-m-d")."\",
            tv=\"$tv\",
            internet_prof=\"$internet_prof\",
            fixed_ip=\"$fixed_ip\",
            phone1=\"$phone1\",
            phone2=\"$phone2\",
            aps=\"$aps\",
            monthly_price=\"$monthly_price\",
            lead_conn_id_chg_over=\"$con_id\",
            lead_conn_id_rcn=\"$conn_id_prop_rec\"
            ".$where_update_lead_30_new_connection."
            ".$where_update_lead_30_reconnection."
            ".$where_update_lead_30_change_over."
            where id=$lead_id ";

            //echo $where_update_lead_30;

            $lead_status_30 = $mon3->query($where_update_lead_30);


            // SUBMISSAO DE RESPOSTAS A QUERY SE DEU SUCESSO A ACTUALIZACAO DAS LEADS OU DEU ERRO
            if($lead_status_30)
                    {
                        if($chg_over == 1)
                        {
                            $succ.= "<font color=green>Update Lead ".$lead_id." on form 'Property Change Over'</font><br>";
                        }
                        else if($reconn == 1)
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


                    // INFORMACOES DE SUBMISSAO AO ACTUALIZAR OS ESTADOS DE UMA DADA LEAD
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

                    <script>
                        var var_s = "<?php echo $var_contract_upl; ?>";

                        var sd = "";

                        sd += var_s +"<br>";
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
            $succ.= "<font color=green>Saved on Operations check equipment</font><br>";
                ?>
                    <script>
                        s += "<?php echo $succ; ?>";
                        $("#info_submit").html(s);
                    </script>
                <?php
		}



		if($status==40)
		{
            //send customer welcome email
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

            // DATA DE INSTALACAO
			if( $prop['date_installed']=="")
			{
				$mon3->query("update property_leads set date_installed=\"".date("Y-m-d")."\" where id=$lead_id;");
			}

	        // CONNECTIONS FWA & GPON
            // EQUIPAMENTO
			$fsan=mysqli_real_escape_string($mon3, $_POST['fsan']);
            // MODELO
		    $model=mysqli_real_escape_string($mon3, $_POST['model']);
            // ANTENNA - FWA
		    $antenna=mysqli_real_escape_string($mon3, $_POST['antenna']);
            // OLT_ID - GPON
			$olt_id=mysqli_real_escape_string($mon3, $_POST['olt_id']);
            // PON - GPON
			$pon=mysqli_real_escape_string($mon3, $_POST['pon']);

            // SERVICES DA CONNECTION
            // TV
			$tv=mysqli_real_escape_string($mon3, $_POST['tv']);

            //INTERNET
			$internet_prof=mysqli_real_escape_string($mon3, $_POST['internet_prof']);
            // IP FIXO
			$fixed_ip=mysqli_real_escape_string($mon3, $_POST['fixed_ip']);
            // SE É ROUTER
			$is_router=mysqli_real_escape_string($mon3, $_POST['is_router']);
            // VLAN
			$vlan=mysqli_real_escape_string($mon3, $_POST['vlan']);
            // WIFI
			$wifi=mysqli_real_escape_string($mon3, $_POST['wifi']);
            // IDENTIFICADOR DA WIFI
			$wifi_ssid=mysqli_real_escape_string($mon3, $_POST['wifi_ssid']);
            // CHAVE DA WIFI
			$wifi_key=mysqli_real_escape_string($mon3, $_POST['wifi_key']);

            // TELEFONES
			$phone1=mysqli_real_escape_string($mon3, $_POST['phone1']);
			$phone2=mysqli_real_escape_string($mon3, $_POST['phone2']);

            // EQUIPAMENTO SE FOI CRIADO NA FTTH_ONT OU FWA_CPE NA BASE DE DADOS MAS NAO ESTA NA CONNECTION = 1
            // EQUIPAMENTO QUE NAO ESTA CRIADO NA CONNECTION E NEM NA FTTH_ONT OU NA FWA_CPE QUE NAO ESTA NA BASE DE DADOS = 2
            // EQUIPAMENTO QUE ESTA NA CONNECTION ID ASSOCIADO E QUE ESTA NA FTTH_ONT OU NA FWA_CPE QUE ESTA NA BASE DE DADOS = 0
            $eq_assoc_conn = $_POST['equip_assoc_not'];

            // ESCOLHER O TIPO DE CONNECTIPN
            $con_type=$prop['con_type'];
			if($con_type=="") $con_type="GPON";


            // SELECCIONAR OS CAMPOS DA PROPRIEDADE QUE SELECCIONADA
			$propery=$mon3->query("select * from properties where id=\"".$prop['prop_id']."\"")->fetch_assoc();

            // CHANGE OVER & RECONNECTION
            $rec_pon_chg = 0;
            // ADICIONAR UMA NOVA CONNECTION (CHANGE OVER OU NEW CONNECTION)
            $connection_adder_new = 1;

            // SE TIVER A CONNECTION ASSOCIADO A PROPRIEDADE ESCOLHIDA (CHANGE OVER & RECONNECTION)
            if($_POST['con_id'] != "")
            {
                $conn_id = $_POST['con_id'];
            }

            // TEXTO QUE MOSTRAR A CONEXAO QUE ESTA ASSOCIADO A UMA RECONNECTION OU CHANGE OVER
            $text_ch = '';
            // LOG QUE MOSTRAR A CONEXAO QUE ESTA ASSOCIADO A UMA RECONNECTION OU CHANGE OVER
            $text_pro_log = '';
            // RECONNECTION = 1 & CHANGE OVER = 1
            if($prop['is_reconnection'] == 1 || $prop['is_changeover'] == 1)
            {
                // MUDANCA DO IDENTIFICADOR DO EQUIPAMENTO
                switch($eq_assoc_conn)
                {
                    case 1:
                    case 2:

                        // MOSTRAR OS EQUIPAMENTOS ANTIGOS QUE FORAM ALTERADOS NO CASO QUE MUDA O CAMPO EQUIPMENTO ID (FSAN OU FWA CPE)
                        // LEAD ESTADO 50 
                        $equi_ant_conn=$mon3->query("SELECT * FROM connections WHERE id=".$conn_id)->fetch_assoc();
                        // BUSCAR O EQUIPAMENTO ANTIGO QUE FOI MODIFICADO
                        $equip_ant = $equi_ant_conn['equip_id'];
                        
                        // SE TIVER EQUIPAMENTO ANTIGO NA CONNECTION (CHANGE OVER OU RECONNECTION)
                        if($equip_ant != "")
                        {
                            if($equip_ant!=$fsan)
                            {
                                // SE FOR RECONNECTION NO CASO DE MUDANCA DE CAMPO FWA CPE OU FSAN (GPON OU FWA)
                                if($prop['is_reconnection'] == 1)
                                {
                                    $text_ch .= " on connection number ".$conn_id;
                                    $text_pro_log .= " on connection number <b>".$conn_id."</b>";
                                    
                                }
                                // LOG DA PROP
                                proplog($prop['prop_id'],"Previous Equipment <b>".$equip_ant."</b> to <b>".$fsan."</b> ".$text_pro_log);
                                // LOG DA MON
                                monlog("Previous Equipment ".$equip_ant." to ".$fsan."".$text_ch." ");
                            }
                        }
                        
                        
                    break;
                }
                // PON DA RECONNECTION = 1 - GPON (MUDANCA DO EQUIPAMENTO PARA MANTER A ONT_ID)
                $rec_pon_chg = 1;
                // SE FOR CHANGE OVER = 1
                if($prop['is_changeover'] == 1)
                {
                    // SE A CONNECTION ANTIGA FOR ATIVA NA PROP (CHANGE OVER = 1)
                    $num_chg_over = $mon3->query("SELECT * FROM connections WHERE id='".$conn_id."' AND type='".$con_type."' AND date_end='0000-00-00'")->num_rows;
                    if($num_chg_over > 0)
                    {
                        // NAO E PRECISA DE ADICIONAR UMA NOVA CONNECTION SE JA FEZ A CHANGE OVER
                        $connection_adder_new = 0;
                        // ACTUALIZAR O EQUIPAMENTO, O TIPO DE CONNECTION E O SUSBCRIBER
                        $update_equip_exist_not_assoc = $mon3->query("update connections set equip_id=\"".$fsan."\", type=\"".$con_type."\", date_start=\"".date("Y-m-d")."\",  subscriber=\"".$propery['owner_id']."\" where id=$conn_id");
                        // ACTUALIZAR OS EQUIPAMENTOS DOS SERVIÇOS
                        $mon3->query("UPDATE services set equip_id = '".$fsan."', subscriber = ".$propery['owner_id'].", contract_id = ".$prop['contract_id']." WHERE connection_id=".$conn_id);
                    }
                    else
                    {
                        // FECHAR A CONNECTION ANTIGA E CRIAR UMA NOVA CONNECTION (CHANGE OVER = 1)
                        $update_chg_equip = $mon3->query("update connections set date_end=\"".date("Y-m-d")."\" where id=$conn_id;");
                        if($update_chg_equip)
                        {
                            $succ .= '<font color=green>OLD connection on date end <b>'.date("Y-m-d").'</b> on connection number '.$conn_id.'</font><br>';
                        }
                        else
                        {
                            $error .= '<font color=red>Error on OLD connection on date end <b>'.date("Y-m-d").'</b> on connection number '.$conn_id.'</font><br>';
                        }
                        // ADICIONAR UMA NOVA CONNECTION
                        $connection_adder_new = 1;
                    }
                }
                if($prop['is_reconnection'] == 1)
                {
                    // NAO E PRECISA DE ADICIONAR UMA NOVA CONNECTION SE FOR UMA RECONNECTION
                    $connection_adder_new=0;
                    // ACTUALIZAR O EQUIPAMENTO, TIPO DE CONNECTION E O SUBSCRIBER DA CONNECTION 
                    $update_equip_exist_not_assoc = $mon3->query("update connections set equip_id=\"".$fsan."\", type=\"".$con_type."\", date_start=\"".date("Y-m-d")."\",  subscriber=\"".$propery['owner_id']."\" where id=$conn_id");
                    // ACTUALIZAR OS EQUIPAMENTOS DOS SERVIÇOS
                    $mon3->query("UPDATE services set equip_id = '".$fsan."', subscriber = ".$propery['owner_id'].", contract_id = ".$prop['contract_id']." WHERE connection_id=".$conn_id);
                    
                    $dis_services_conn = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id)->fetch_assoc();

                    // LISTAR O ESTADO DOS SERVIÇOS DE UMA DADA CONNECTION
                    $dis_serv = $dis_services_conn['dis_services'];
                    // LISTAR A PROPRIEDADE EXISTENTE NA LEAD
                    $prop_id = $dis_services_conn['property_id'];

                    // VERIFICAR SE A CONNECTION POSSUEM SERVIÇOS DISCONNECTED
                    if($dis_serv == 0)
                    {
                        // ACTUALIZAR AS DATAS DIS CONN E DIS SERVICES
                        // CASO QUE A CONNECTION POSSUI SERVIÇOS DISCONNECTED (STATUS=2 PARA 0)
                        $update_dis_conn = $mon3->query("UPDATE connections SET dis_services = \"1\" WHERE id=".$conn_id);

                        if($update_dis_conn)
                        {
                            $succ .= "<font color=green>Connection Number ".$lead_id." are activated successfully</font><br>";
                        }
                        else
                        {
                            $error .= "<font color=red>Connection Number ".$lead_id." are not activated</font><br>";
                        }

                        proplog($prop_id,"Activate connection <b>".$conn_id."</b> and services are activated to edit/create");

                    }
                    
                }

                

                


                // LEAD_SUB != OWNER_ID

                // VERIFICAR SE A LEAD SUBSCRIBER E DIFERENTE AO OWNER DA PROPERTY DO CUSTOMER
                if($propery['owner_id'] != $prop['lead_sub'])
                {
                    $owner_name_old = $mon3->query("SELECT * FROM customers WHERE id=".$propery['owner_id'])->fetch_assoc();
                    $update_prop_owner_status_30 = $mon3->query("update properties set owner_id=\"".$prop['lead_sub']."\" WHERE id=".$prop['prop_id']);
                    if($update_prop_owner_status_30)
                    {
                        $succ .= "<font color=green>Update Property on number ".$prop['prop_id']." on owner ".$prop['lead_sub']."</font><br>";
                    }
                    else
                    {
                        $error .= "<font color=red>Error Property on number ".$prop['prop_id']." on owner ".$prop['lead_sub']."</font><br>";
                    }

                    $owner_name_new = $mon3->query("SELECT * FROM customers WHERE id=".$prop['lead_sub'])->fetch_assoc();



                    // LOG DA PROPRIEDADE SE A LEAD SUBSCRIBER DA MUDANCA DE PROPRIETARIO DESTA PROPRIEDADE CRIADA NA LEAD
                    proplog($prop['prop_id'],"Update Property on number <b>".$prop['prop_id']."</b> on owner <b>".$owner_name_old['name']."</b> to <b>".$owner_name_new['name']."</b>");
                }

            }


            // PRECISA DE FAZER UMA NOVA CONNECTION

            if($connection_adder_new == 1)
            {
                // CHANGE OVER = 1 VAI ACTUALIZAR A CONNECTION ANTERIOR PARA FAZER UMA DISCONNECTION (DATE_END = DATA DE HOJE)
                        if($prop['is_changeover'] == 1)
                        {  
                            proplog($prop['prop_id'],"OLD connection <b>".date("Y-m-d")."</b> on connection number <b>".$conn_id."</b>");
                        }
                        // INSERCAO DE UMA NOVA CONNECTION
                        $insert_connecti = $mon3->query("insert into connections (property_id,type,equip_id,date_start,subscriber,dis_services) VALUES (
                            \"".$prop['prop_id']."\",
                            \"$con_type\",
                            \"$fsan\",
                            \"".date("Y-m-d")."\",
                            \"".$propery['owner_id']."\",
                            \"1\"
                            ) ");

                        $conn_id_new=$mon3->insert_id;
                        // INSERCAO DE UMA EQUIPAMENTO ID DA CONNECTION CRIADA
                        proplog($prop['prop_id'],"Insert new Connection on Equipment <b>".$fsan."</b> on new connection number <b>".$conn_id_new."</b>");
                        $conn_id=$conn_id_new;
                        // ACTUALIZA A NOVA CONNECTION CASO QUE SEJA CHANGE OVER
                        if($prop['is_changeover'] == 1)
                        {
                            $mon3->query("UPDATE property_leads set lead_conn_id_chg_over=\"".$conn_id."\"  where id=$lead_id; ");
                        }
            }


            // ACTUALIZAR A SUSBCRIBER DA LEAD NA CONNECTION CORRESPONDENTE
            $mon3->query("update connections set subscriber=\"".$prop['lead_sub']."\" where id=$conn_id;");
            
            /// ---------- CONNECTIONS EQUIPMENTS -------------

            // GPON

            if($con_type == "GPON")
            {
                // CHANGE OVER E RECONNECTION NO CASO SE FOR A MUDANCA DO EQUIPAMENTO ID E DA FTTH_ONT MANTER ONT_ID ATRAVES
                // DA PON E DA OLT_ID
                if($rec_pon_chg==1)
                {
                    // VERIFICAR O EQUIPAMENTO ANTIGO PARA BUSCAR A ONT_ID
                        $ont=$mon3->query("select * from ftth_ont where fsan=\"".$equip_ant."\";")->fetch_assoc();
                        // SE TEM ONT
                        if($ont['ont_id'] != "")
                        {
                            $ont_x=explode("-",$ont['ont_id']);           
                            $ontnext="1-".$pon."-".$ont_x[3];

                            // APAGA A ONT DO EQUIPAMENTO ANTERIOR
                            $ont_blacked_old=$mon3->query("update ftth_ont set ont_id = \"\" where fsan=\"".$equip."\";");
                        }
                        else
                        {
                            // CRIAR UMA NOVA ONT ID ATRAVES DA OLT E DA PON - GPON
                            $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                            //echo $ontnext."<br>";
                        }
                }
                else
                {
                    // CRIAR UMA NOVA ONT ID ATRAVES DA OLT E DA PON - GPON
                    $ontnext="1-".$pon."-".nextont($olt_id,$pon);
                    //echo $ontnext."<br>";
                }

                // IDENTIFICAR A ONT DO EQUIPAMENTO CORRESPONDENTE

                $select_ont_id = $mon3->query("select ont_id from ftth_ont where fsan=\"$fsan\"")->fetch_assoc();
                // OBTER O EQUIPAMENTO DA FTTH_ONT
                    $num_ont=$mon3->query("select fsan from ftth_ont where fsan=\"$fsan\"")->num_rows;
                    // OBTER O EQUIPAMENTO DA ONT ANTERIOR
		            $ont_id_ex=$mon3->query("select fsan from ftth_ont where fsan=\"$fsan\" and ont_id=\"\" ")->num_rows;
                    // OBTER O EQUIPAMENTO DO MODELO ANTERIOR
                    $model_ant = $mon3->query("SELECT * FROM ftth_ont WHERE fsan=\"$fsan\"")->fetch_assoc();
                    // GPON - MEPROF | MODEL
                    $m_ant = $model_ant['meprof'];
                    // SE TIVER EQUIPAMENTO INSERIDO NA BASE DE DADOS DEVE ACTIUALIZAR O MODELO, PROXIMO ONT, OLT ID E O SEU EQUIPAMENTO
                    if($num_ont>0 && $ont_id_ex>0)
                    {
                        // MODELO, PROXIMO ONT, OLT ID E O SEU EQUIPAMENTO
                        $update_equip_ont_50 = $mon3->query("update ftth_ont set olt_id=\"$olt_id\",ont_id=\"$ontnext\", meprof=\"$model\", model=\"$model\" where fsan=\"$fsan\"");
                        if($update_equip_ont_50)
                                {
                                    $succ .= '<font color=green>Update ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                                }
                                else
                                {
                                    $error .= '<font color=red>Error on Updating ONT '.$fsan.' on connection number '.$conn_id.'</font><br>';
                                }
                                // ACTUALIZAR A ONT ID DO EQUIPAMENTO (MUDANCA DE EQUIPAMENTOS EXISTENTES NA FTTH_ONT)
                        proplog($prop['prop_id'],"Update ONT from <b>".$fsan."</b> for ont <b>".$ontnext."</b> and olt <b>". $olt_id. "</b> and model from <b>".$m_ant."</b> to <b>".$model."</b> on connection number <b>".$conn_id."</b> (ONT, FWA CPE)");
                        gpon_change_ont($olt_id,$ontnext,$fsan,$model);

                        monlog("FSAN changed on ".$propery['ref']." from ".$equip." to $fsan for ont ".$ontnext." and olt ". $olt_id. " and model from ".$m_ant." to ".$model."");
                    }
                    // JA TEM ONT NA FTTH_ONT
                    elseif($num_ont>0 && $ont_id_ex==0) //tem ont_id
                    {
                        echo "<font color=red>ONT in database with ID.. not registering to connection.</font><br>";
                    }
                    // SE NAO ESTA NA FTTH_ONT O EQUIPAMENTO INTRODUZIDO NO ESTADO 50
                    else //nao tem ONT
                    {
                        // INSERE FTTH_ONT - EQUIPAMENTO ID, OLT_ID, ONT_ID, MODELO, MEPROF
                        $insert_ont_50 = $mon3->query("insert into ftth_ont (fsan,olt_id,ont_id,model,meprof) values (\"$fsan\",$olt_id,\"$ontnext\", \"$model\", \"$model\")");
                        if($insert_ont_50)
                                    {
                                        $succ .= '<font color=green>Insert ONT '.$fsan.'</font><br>';
                                    }
                                    else
                                    {
                                        $error .= '<font color=green>Error on Insert ONT '.$fsan.'</font><br>';
                                    }
                                    // INSERE A ONT QUE MOSTRA NA PROPM LOG
                        proplog($prop['prop_id'],"Insert ONT <b>".$fsan."</b>  for ont <b>".$ontnext."</b> and olt <b>". $olt_id."</b> and model <b>".$model."</b> on connection number <b>".$conn_id."</b>");

                        monlog("FSAN Inserted on ".$propery['ref']." equipment ".$fsan." for ont ".$ontnext." and olt ". $olt_id." and model ".$model);

                    }
            }

            // FWA

            else if($con_type == "FWA")
            {
                // BUSCAR O EQUIPAMENTO ANTERIOR QUE ESTA POR DEFEITO NO ESTADO 50
                $model_ant = $mon3->query("SELECT * FROM fwa_cpe WHERE mac=\"$fsan\"")->fetch_assoc();

                // MODELO ANTERIOR NO EQUIPAMENTO NA FTTH_ONT
                $m_ant = $model_ant['model'];


                $select_fwa_cpe_num = $mon3->query("select * from fwa_cpe where mac=\"$fsan\"")->num_rows;

                // DESIGNACAO DA ANTENNA - IDENTIFICAR O ID DA ANTENNA ANTERIOR QUE ESTA NA PROP ID DA CONNECTION
                if($model_ant['antenna'] == "")
                {
                    $antenna_ant = 0;
                }
                else
                {
                    $antenna_ant = $model_ant['antenna'];
                }

                // IDENTIFICAR A DESCRICAO DA ANTENNA INTRODUZIDA NA PROP ID NA CONNECTION CORRESPONDENTE
                $antenna_des_ant = $mon3->query("SELECT * FROM `fwa_antennas` WHERE id=".$antenna_ant)->fetch_assoc();
                // INDICA A DESCRICAO DA ANTENNA ANTERIOR POR DEFEITO NA CONNECTION
                $designacao_antenna_ant = $antenna_des_ant['name'];
                // INDICA A SELECCAO DA ANTENNA INTRODUZIDA NO ESTADO 50 
                $antenna_des = $mon3->query("SELECT * FROM `fwa_antennas` WHERE id=".$antenna)->fetch_assoc();
                // INDICA A DESCRICAO DA ANTENNA SELECCIONADA NO ESTADO 50
                $designacao_antenna = $antenna_des['name'];
                // SELECCIONA SE TEM O EQUIPAMENTO DA FWA CPE
                if($select_fwa_cpe_num > 0)
                {
                    // SE TIVER SO ACTUALIZA O MODELO E A ANTENNA 
                    $update_equip_fwa_cpe_50 = $mon3->query("update fwa_cpe set model=\"$model\",antenna=\"$antenna\" where mac=\"$fsan\"");

                    if($update_equip_fwa_cpe_50)
                            {
                                $succ .= '<font color=green>Update FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                            }
                            else
                            {
                                $error .= '<font color=green>Error on Updating FWA CPE '.$fsan.' on connection number '.$conn_id.'</font><br>';
                            }
                            // PROP ID DA LOG QUE ACTUALIZOU O MODELO DO EQUIPAMENTO FWA
                    proplog($prop['prop_id'],"Update FWA CPE <b>".$fsan."</b> on connection number <b>".$conn_id."</b> for antenna <b>".$designacao_antenna_ant."</b> to <b>".$designacao_antenna."</b> and model from ".$m_ant." to ".$model."");

                    monlog("FWA CPE ".$fsan." Updated on ".$propery['ref']." for antenna ".$designacao_antenna_ant." to ".$designacao_antenna." and model from ".$m_ant." to ".$model."");

                }
                else
                {
                    // INSERE O FWA CPE DO EQUIPAMENTO QUE FOI INSERIDO NO ESTADO 50
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

            // VERIFICAR SE OS SERVICOS ESTAO ATIVOS NESTA CONEXAO - IS RECONNECTION DA PROP (RECONNECTION ID)

            if(isset($_POST['serv_en_warn']))
            {
                // LISTA DE SERVIÇOS QUE ESTAO ATIVOS DE UMA DADA CONNECTION
                $services_list_enabled = $mon3->query("SELECT * FROM services where connection_id=".$conn_id." AND date_end='0000-00-00'");

                // 
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

            // SE FOR FWA DO EQUIPAMENTO DA CONNECTION (VERIFICAR NA PROP LEAD DA CON TYPE) NAO E PRECISO IDNICAR O SERVIÇO TV
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

            // INFO DE SUBMISSOA DE RESPOSTA AO ACTUALIZAR UMA DADA LEAD 
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
	var soptionb=document.getElementById('idstatus');";
    // SELECCIONAR OS ESTADOS DAS LEADS - FOPRMULARIOS
    echo "
	var soption=soptionb.options[soptionb.selectedIndex].value;
    //ar soption=30;
    //console.log(soption);
    ";
    // FORMULARIOS DAS LEADS
    $disabled_serv_rec = "";

    echo "
	textdiv += '<font color=red> details are only savend on a status change. Please use notes if you want to notify of changes on submitted data</font><br><br>';
	";

    // VIAVEL E NAO VIAVEL - RECONNECTION & CHANGE OVER & NEW CONNECTION
    echo "	
	if(soption>0 && soption<5) 
	{
        ";
        // MOSTRAR OS WARNINGS DA SUBMISSAO DE MUDANCA DE ESTADOS DAS LEADS
        echo "
        $('#warning_services').html('');
        $('#info_submit').html('');";

        // IS RECONNECTION
        // CHECKED - VALOR POR DEFEITO SE A CONNECTION TEM SERVIÇOS DESATIVOS 
        // UNCHECKED - vALOR POR DEFEITO SE A CONNECTION TEM SERVICOS ATIVOS / DESATIVOS OU SEM SERVICOS 
        $check_rec = "checked";

        // IS RECONNECTION
        if($prop['is_reconnection'] == 1)
        {       
            // VERIFICAR SE A CONNECTION TEM SERVIÇOS ATIVOS
            $servicos_at = $mon3->query("SELECT * FROM `services` where connection_id=".$prop['lead_conn_id_rcn']." and date_end='0000-00-00'");
            $num_ser = $servicos_at->num_rows;
            if($num_ser > 0)
            {
                //LISTAGEM DE SERVIÇOS ATIVOS NO CASO QUE A PROPRIEDADE DA CONNECTION É UMA RECONNECTION
                while($servico_at=$servicos_at->fetch_assoc())
                {
                    // DESATIVAR OS SERVICOS PARA CRIAR UM NOVO SERVIÇO

                    $servico_id = $servico_at['id'];
                    $serv_type = $servico_at['type'];
                    $serv_id_arr[] = $servico_id;
                    $serv_type_arr[] = $serv_type;

                    // LISTAR OS SERVIÇOS ATIVOS DA CONNECTION
                
                    $warn_at_serv .= "<font color=red>Service number ".$serv_type." on this connection number ".$servico_id. " is active. Service must be deactivated before finishing the reconnection </font><br>";
                    $check_rec = "";
                    
                }
            }
            else
            {
                
                $check_rec = "checked";  
            }
        }

        // MOSTRAR OS TYPE CONNECTIOS EXISTENTES NA MON
        
		echo "textdiv += ' form to prepare rough estimates <br><table>' +
		'<tr><td>Connection: '+

        
		'<tr><td>Type<td> 	<select name=con_type id=con_type onchange=\"updatecpe(this.options[this.selectedIndex].value,); change_type_connection(this.value); changeOver_State_1(this.options[this.selectedIndex].value);\" style=\"width: 180px;\">	' +			
        '<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +	
        '</select>'+	
		
		'<div id=model_cpe><tr><td>CPE model<td><select name=model id=models style=\"width: 180px;\"></select></div><br><br>'+	";

        // SE FOR UMA NOVA CONNECTION
        if($prop['is_changeover']==0 && $prop['is_reconnection']==0)
        {
            $Val_re = "1";
        }

        // OPCOES SE E CHANGE OVER OU RECONNECTION OU UMA NOVA CONNECTION

        // RADIO BUTTON - NEW CONNECTION
        echo "'<tr><td><input type=radio id=is_new_connection name=con_type_proc value=is_new_connection onclick=\"FormChgOverReconnection(this,0,0,\'\',\'soption\');\" ";if($prop['is_changeover']==0 && $prop['is_reconnection']==0)
		echo " checked"; echo "><label for=is_new_connection>Is New Connection</label><input type=hidden id=new_conn_form name=new_conn_form value=".$Val_re."><br><br>";
		// RADIO BUTTON - CHANGE OVER
        echo "<td><input type=radio id=is_changeover name=con_type_proc value=is_changeover onclick=\"FormChgOverReconnection(this,0,0,\'\',\'soption\');\" ";if($prop['is_changeover']==1)
		echo " checked"; echo "><label for=is_changeover>Is Change Over</label><input type=hidden id=changeover_form name=changeover_form value=".$prop['is_changeover']."><br><br>";
        // RADIO BUTTON - ~RECONNECTION
        echo "<td><input type=radio id=is_reconnection name=con_type_proc value=is_reconnection onclick=\"FormChgOverReconnection(this,0,0,\'\',\'soption\');\" ";if($prop['is_reconnection']==1)
        echo " checked"; echo "><label for=is_reconnection>Is Reconnection</label><input type=hidden id=reconnection_form name=reconnection_form value=".$prop['is_reconnection']."><br><br>";
		

        echo "</table>";
        // LISTA DAS PROPRIEDADES

        // RECONNECTION

        // FORMULARIOS DE CHANGE OVER & RECONNECTIONS
        $form_chg = "display: none";
        $form_rec = "display: none";
        $dis_fields = "";

        // DESATIVAR OS CAMPOS DO How many ORAC pits (drop)? - ATE Timeframe from paper to service

        // FORMULARIO RECONNECTION
        if($prop['is_reconnection']==1 )
        {
            $form_rec = "display: block";
            $dis_fields = "disabled";
        }

        // FORMULARIO DA CHANGE OVER
        if($prop['is_changeover']==1)
        {
            $form_chg = "display: block";
            $dis_fields = "";
        }

        // CHANGE OVER
        echo "<table><tr><td colspan=2>";
        echo "<div id=chg_over_form style=\"".$form_chg."\">";
            echo "<fieldset>";
                echo "<legend>Change Over:</legend>";

                // CONNECTION TYPE - CHANGE OVER

                echo "<label>Connection Type Change Over: </label> ";
                echo "<select name=con_type_chg_over id=con_type_chg_over onchange=\"changeOverTypeConnection(this.options[this.selectedIndex].value);\" style=\"width: 180px;\"></select><br>";

                // PROPERTY CHANGE OVER
                echo "<label id=text_conn_prop>Property Change Over Connection:</label> <select name=refe id=refe onchange=con_prop_type(this.value) style=\"width: 600px\">";
                echo "</select><br>";

                // PROPERTY DA CHANGE OVER = 1 - DIFEFRENTE DA TECNOLOGIA DAS CONNECTIONS (!= GPON (POR EXEMPLO))      
                echo "<label>Connection: </label> ";
                echo "<select id=con_id name=con onchange=\"connection_equip(this.value)\" style=\"width: 150px; \">";
                echo "</select><br>";

                // FORMULARIO DA CHANGE OVER = 1 - NUMERO DA CONNECTION, EQUIPAMENTO, CONNECTION TYPE DA CONNECTION ID
                echo "<div id=conn_type_fsan style=\"display: grid\">";
                echo "</div>";

            echo "</fieldset>";
        echo "</div></table>";


        // RECONNECTION
        echo "<table><tr><td colspan=2>";
        echo "<div id=rec_form style=\"".$form_rec."\">";
            echo "<fieldset>";
                echo "<legend>Reconnection:</legend>";
                    
                // CHECKED DAS PROPRIEDADES COM SERVIÇOS DESABILITADOS
                echo "<label>Disabled Prop Services</label> <input type=checkbox name=disabled_prop_services id=disabled_prop_services onchange=\"changePropServicesDisabled(this, 0)\" checked><br>";
                // MOSTRAR AS PROPRIEDADES COM A TYPE CONNECTION SELECCIONADA COM SERVIÇOS DESABILITADOS OU COM OU SEM SERVIÇOS
                echo "<label id=text_conn_prop_rec>Property Reconnection</label> <div id=lists_reconnections_bt_type><select name=refe_rec_7 id=refe_rec_7 onchange=\"con_prop_rec(this.value); \" style=\"width: 500px\">";
                echo "</select></div><br> <span id=serv_prop_desativados></span> <input type=hidden name=conn_id_prop_rec id=conn_id_prop_rec>";
                // MOSTRAR A CONNECTION DA PROPRIEDADE SELECCIONADA
                echo "<div id=prop_conn_servicos_des></div>";
                echo "<div id=conn_assoc_prop_status_7><input type=hidden name=rec_assoc value=1></div>";

                // SUBSCRIBER 
                echo "<label>Subscriber Change Over</label> <select name=owner_rec id=owner_rec style=\"width: 180px; \">";
                echo "</select><br>";


            echo "</fieldset>";
        echo "</div></table>";

        echo "' +";

        // DESATIVA OS CAMPOS TODOS SE FOR UMA RECONNECTION - 1 (RADIO BUTTON IS_RECONNECTION = 1)
		echo "'<table><tr><td>How many ORAC pits (drop)?<td><input type=text name=ORAC_pits value=".$prop['ORAC_pits']." size=5 ".$dis_fields."> '+
		'<tr><td>How many ORAP poles (drop)?<td><input type=text name=ORAP_poles value=".$prop['ORAP_poles']." size=5 ".$dis_fields."><br> '+
		'<tr><td>Drop length?<td><input type=text size=5 name=drop_length value=".$prop['drop_length']." ".$dis_fields.">m<br> '+	
		'<tr><td>Connection cost?<td><input type=text name=connection_cost value=".$prop['connection_cost']." size=5 ".$dis_fields.">€ '+
		'<tr><td>kmz file<td><input type=file name=plan ".$dis_fields."><tr><td><br> '+
		'<tr><td>zipfile<td><input type=file name=planz ".$dis_fields."><tr><td><br> '+
		'<tr><td>Network: '+	
		'<tr><td>Is Network Ready? <td><input type=checkbox ".$dis_fields." name=is_network_ready value=1 "; if($prop['is_network_ready']==1)
		echo " checked";
		echo "><br> '+
		'<tr><td>Network investment?<td><input size=5 ".$dis_fields." type=text name=network_cost value=".$prop['network_cost']." >€<tr><td><br> '+
		
		'<tr><td>Customer Info: '+
		'<tr><td>Estimated costs to customer?<td><input size=5 ".$dis_fields." type=text name=estimated_quote value=".$prop['estimated_quote']." >€<br> '+
		'<tr><td>Timeframe from paper to service<td><input type=text ".$dis_fields." name=timeframe value=".$prop['timeframe']." size=5>days<br></table> '+
		
		'</table>';";

        // SE TEM PROPRIEDADE SELECCIONADA ANTERIOR NO CASO SE FOR RECONNECTION OU CHANGE OVER
        if($prop['prop_id'] != 0 || $prop['prop_id'] != '')
        {
            // VERIFICAR SE E UMA RECONNECTION OU UMA CHANGE OVER
            $conn_ant_type = $mon3->query("SELECT type FROM connections WHERE id=".$prop['lead_conn_id_chg_over'])->fetch_assoc();

            // FUNCAO QUE VAI BUSCAR POR DEFEITO A PROPRIEDADE QUE QUER FAZER UMA RECONNECTION
            if($prop['is_reconnection']==1)
            {
                echo "RecPropConnType(".$prop['prop_id'].",'".$prop['con_type']."',".$prop['lead_conn_id_rcn'].",".$prop['lead_sub'].",'".$check_rec."');";
            }
            // FUNCAO QUE VAI BUSCAR POR DEFEITO A PROPRIEDADE QUE QUER FAZER UMA CHANGE OVER
            else if($prop['is_changeover']==1)
            {
                echo "ChgOverPropConnType(".$prop['prop_id'].",'".$prop['con_type']."','".$conn_ant_type['type']."',".$prop['lead_conn_id_chg_over'].",".$prop['lead_sub'].");";
            } 
        }

        echo "
	}
	";


    echo "
	else if(soption==14) 
	{";
        // MOSTRAR OS WARNINGS DA SUBMISSAO DE MUDANCA DE ESTADOS DAS LEADS
        echo "
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
	";
	
	
	
	
	
	
	
	
	
	echo "
	else if(soption==30)
	{ ";
        // MOSTRAR OS WARNINGS DA SUBMISSAO DE MUDANCA DE ESTADOS DAS LEADS
        echo "
	    $('#warning_services').html('');
        $('#info_submit').html('');";

        // ------------ ALTERACAO DO CODIGO ------------------

        // MOSTRAR UMA LISTA DOS SERVIÇOS ATIVOS DE UMA DADA CONNECTION
        $warn_at_serv = '';
        // IS RECONNECTION
        // CHECKED - VALOR POR DEFEITO SE A CONNECTION TEM SERVIÇOS DESATIVOS 
        // UNCHECKED - VALOR POR DEFEITO SE A CONNECTION TEM SERVICOS ATIVOS / DESATIVOS OU SEM SERVICOS 
        $check_rec = "checked";

        // IS RECONNECTION
        if($prop['is_reconnection'] == 1)
        {       
            // VERIFICAR SE A CONNECTION TEM SERVIÇOS ATIVOS
            $servicos_at = $mon3->query("SELECT * FROM `services` where connection_id=".$prop['lead_conn_id_rcn']." and date_end='0000-00-00'");
            $num_ser = $servicos_at->num_rows;
            if($num_ser > 0)
            {
                //LISTAGEM DE SERVIÇOS ATIVOS NO CASO QUE A PROPRIEDADE DA CONNECTION É UMA RECONNECTION
                while($servico_at=$servicos_at->fetch_assoc())
                {
                    // DESATIVAR OS SERVICOS PARA CRIAR UM NOVO SERVIÇO

                    $servico_id = $servico_at['id'];
                    $serv_type = $servico_at['type'];
                    $serv_id_arr[] = $servico_id;
                    $serv_type_arr[] = $serv_type;

                    // LISTAR OS SERVIÇOS ATIVOS DA CONNECTION
                
                    $warn_at_serv .= "<font color=red>Service number ".$serv_type." on this connection number ".$servico_id. " is active. Service must be deactivated before finishing the reconnection </font><br>";
                    $check_rec = "";
                    
                }
            }
            else
            {
                $check_rec = "checked";  
            }
            

                         
                
        }

		echo "textdiv += ' form to get details of service, create property   <br><table>";
        echo "<br><br><font color=red>".$warn_at_serv."</font><br><br>";

        // ------------ FIM DA ALTERACAO DO CODIGO ------------------
        $property_cust = $mon3->query("SELECT * FROM properties WHERE id = ".$prop['prop_id'])->fetch_assoc();
        if($prop['prop_id'] == 0 || $prop['prop_id'] == '')
        {
            $addr = str_replace("'","",$prop['address']);
        }
        else
        {
            $addr = $property_cust['address'];
        }

		echo "<tr><td >Address<td><input type=text name=address value=\"".$addr."\" size=50><br>";

        // FORMULARIO DE ADICIONAR CUSTOMERS NO CASO SE FOR UMA RECONNECTION
        // FIELD 'PREV MONTH' 
        // ATIVA SE FOR CHANGE OVER OU NEW CONNECTION
        // DESATIVA SE FOR UMA RECONNECTION
        $prev_month = "";        

        // ------------ ALTERACAO DO CODIGO ------------------

        $disa = "";

        // Verificar a Change Over
        if($prop['is_changeover'] == 1 || $prop['is_reconnection'] == 1)
        {
            $dis = "disabled"; // DESABILITAR A PROP REF
            $dis_conc = "disabled"; // DESABILITAR O CONCELHO
            $dis_freg = "disabled"; // DESABILITAR A FREGUESIA
            $subs = "disabled"; // SUSBCRIBER DESABILITAR
            $prev_month = "disabled";
            $disa = "disabled";
        }
        else
        {
            $dis = "";
            $subs = "onchange=owner_prop(this.value)";
            $dis_conc = "";
            $dis_freg = "";
            $prev_month = "";
        }


        $form_customer_add_reconn_cgh_over .= "<table><button type=button class=add_client_but onclick=addClientNew() >Add New Client</button></td>";

        $form_customer_new_conn .= "<button type=button id=add_client_but_new_conn onclick=addClientNew() ".$disa.">Add New Client</button></td>";

        //<table>

        $form_customer_add_reconn .= "<td><tr><td colspan=2><table><tr><td colspan=2><div id=add_new_client_reconn style=\"display:none;\" >";
        $form_customer_add_chg .= "<td><tr><td colspan=2><table><tr><td colspan=2><div id=add_new_client_chg_over style=\"display:none;\" >";

        $form_customer_add_new_conn .= "<td><tr><td colspan=2><table><tr><td colspan=2><div id=add_new_client style=\"display:none;\" >";
        
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

        $form_customer_add .= "<input type=text name=name_cust id=name_cust onkeyup=\"return validateCustForm(".$lead_id.")\">";
        $form_customer_add .= "<tr><td> <div id=divbillingaddr> <b>Billing Address:</b> <font color=red>*</font></div><td> <input type=text name=address_cust id=address_cust onkeyup=\"return validateCustForm(".$lead_id.")\">";
        $form_customer_add .= "<tr><td> <div id=divemailcust> <b>Email:</b> <font color=red>*</font></div> <td> <input type=text name=email_cust id=email_cust onkeyup=\"return validateCustForm(".$lead_id.")\">";
        $form_customer_add .= "<tr><td> <div id=divphone> <b>Phone:</b> <font color=red>*</font></div> <td> <input type=text name=telef_cust id=telef_cust onkeyup=\"return validateCustForm(".$lead_id.")\">";
        $form_customer_add .= "<tr><td> <div id=divfiscalnumber> <b>Fiscal Number:</b> <font color=red>*</font></div> <td> <input type=text name=fiscal_nr_cust id=fiscal_nr_cust onkeyup=\"return validateCustForm(".$lead_id.")\" > <span id=fiscal_num_warn></span>";
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
        $form_customer_add .= "<tr><td><td><br><div>";



        // SUBMISSAO DE ADICIONAR UM NOVO CLIENTE A UMA LISTA DE OWNERS A FIELD SUBSCRIBER DA LEAD NO ESTADO 30
        $form_customer_add .= "<tr><td><button type=button onclick=NewCustomerState30(".$lead_id.") class=new_cust id=new_cust disabled>New Customer</button>";

        $form_customer_add .= "<tr><td colspan=2><span id=warn_submit_cust></span>";
        $form_customer_add .= "</div></table>";
        $form_customer_add .= "</fieldset>";
        
        $form_customer_add_new_conn_end .= "</div><td></table></td></tr></td>";

        $form_customer_add_chg_end .= "</div><td></table></td></tr></table>";

        $form_customer_add_reconn_end .= "</div><td></table></td></tr></table>";

        // TIPO DE CONEXAO VALIDAR - CONNECTION CHANGE OVER ESTADO 30

        // SERVIÇOS DE TV DESATIVADOS SE FOR FWA

        if($prop['con_type'] != "")
        {
            if($prop['con_type']=='FWA')
            {
                $ds = "disabled";
            }
            else
            {
                $ds = "";
            }
        }

        // ------------ FIM DA ALTERACAO DO CODIGO ------------------

        // VALIDAR CONTRATO

        $prop['contract_id'] == "" ? 0 : $prop['contract_id'];

        

        // --------------------------------- ORIGINAL ---------------------------------

        // CONCELHO
        echo "<tr><td>Concelho:<td><select name=concelho id=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value,\'\')\" ".$dis_conc." style=width:400px>";
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


        
        $refas_prop = substr($property_cust['ref'], 0, strlen($property_cust['ref']) - 3);
        

        // PROPERTY REF 
		echo "<tr><td >Prop Ref: <td> <select name=ref id=ref ".$dis." style=width:400px>"; 
		$refs=$mon3->query("select areacode,description from area_codes order by areacode"); 
        while($ref=$refs->fetch_assoc())
        { 
            echo "<option value=".$ref['areacode'];
            if($ref['areacode']==$refas_prop) echo " selected";
            echo ">".$ref['areacode']." - ".$ref['description']."</option>";
        }

        // SUBSCRIBER
        echo " </select><br> '+
		'<tr><td>Subscriber: <td><select name=owner_id id=owner_id ".$subs." style=width:205px>";

        


	    $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
	    while($cust=$custs->fetch_assoc())
	    {
            echo "<option value=".$cust['id'];
            if($cust['id']==$property_cust['owner_id']) echo " selected";
            echo ">". $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr']."</option>";
        }

        
		echo " </select> ".$form_customer_new_conn."".$form_customer_add_new_conn."".$form_customer_add."".$form_customer_add_new_conn_end;
        // MANAGEMENT ID    
		echo "<tr><td>Management<td><select name=mng_id> <option selected value=0 style=width:400px>no management</option>";
		$refs=$mon3->query("select id,name from customers where is_management=1 order by name"); while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['id'].">".$ref['name']."</option>";} echo " </select><br>";


        // CONTRACT ID AND PDF
		echo "<tr><td> <br>'+
		'<tr><td>Contract id<td><input type=text name=contract_id value=".$prop['contract_id']." size=5> '+
		'<tr><td>contract pdf <td><input type=file name=contract>'+ ";

        echo "'<tr><td><br>Connection <input type=hidden id=con_type_id>'+ ";


        // TYPE CONNECTION - MUDANCA DA CONNECTION SE FOR CHANGE OVER = 1
		echo "'<tr id=type_con_sta_30><td>Type<td><select name=con_type id=con_type onchange=\"updatecpe(this.options[this.selectedIndex].value,); change_type_connection(this.value); changeOver_State_1(this.options[this.selectedIndex].value); updateInternet(this.options[this.selectedIndex].value);\"  style=\"width: 200px;\">' +		
		'<option value=GPON ";if($prop['con_type']=='GPON') echo "selected"; echo ">GPON</option>' +	
        '<option value=FWA "; if($prop['con_type']=='FWA') echo "selected"; echo ">FWA</option>' + 		
        '<option value=COAX "; if($prop['con_type']=='COAX') echo "selected"; echo ">COAX</option>' + 
        '<option value=DIA "; if($prop['con_type']=='DIA') echo "selected"; echo ">DIA</option>' +
        '<option value=DARKF "; if($prop['con_type']=='DARKF') echo "selected"; echo ">DARKF</option>' +     
        '</select>'+";

        // --------------------------------- FIM ORIGINAL ---------------------------------

         // ------------ ALTERACAO DO CODIGO ------------------

        // MODELO
        echo "'<tr><td id=cpe_text>CPE model<td><div id=model_cpe><select name=model id=models style=width:200px></select></div></table>';"; 

        // ATIVA SE FOR UMA NEW CONNECTION (NAO HA FORMULARIOS PARA UMA NOVA CONNECTION)
        if($prop['is_changeover']==0 && $prop['is_reconnection']==0)
        {
            $Val_re = "1";
        }

        // OPCOES SE E CHANGE OVER OU RECONNECTION
        echo "textdiv += '<table><tr><td><input type=radio id=is_new_connection name=con_type_proc value=is_new_connection onclick=\"FormChgOverReconnection(this,0,0,\'\',\'\',".$lead_id.");\" ";if($prop['is_changeover']==0 && $prop['is_reconnection']==0)
		echo " checked"; echo "><label for=is_new_connection>Is New Connection</label><input type=hidden id=new_conn_form name=new_conn_form value=".$Val_re."><br><br>";
		echo "<td><input type=radio id=is_changeover name=con_type_proc value=is_changeover onclick=\"FormChgOverReconnection(this,0,0,\'\',\'\',".$lead_id.");\" ";if($prop['is_changeover']==1)
		echo " checked"; echo "><label for=is_changeover>Is Change Over</label><input type=hidden id=changeover_form name=changeover_form value=".$prop['is_changeover']."><br><br>";
        echo "<td><input type=radio id=is_reconnection name=con_type_proc value=is_reconnection onclick=\"FormChgOverReconnection(this,0,0,\'\',\'\',".$lead_id.");\" ";if($prop['is_reconnection']==1)
        echo " checked"; echo "><label for=is_reconnection>Is Reconnection</label><input type=hidden id=reconnection_form name=reconnection_form value=".$prop['is_reconnection']."><br><br></table>';";
        // RECONNECTION & CHANGE OVER

        $form_chg = "display: none";
        $form_rec = "display: none";

        // FORMULARIO DA RECONNECTION
        if($prop['is_reconnection']==1 )
        {
            $form_rec = "display: block";
        }
        // FORMULARIO DA CHANGE OVER
        if($prop['is_changeover']==1)
        {
            $form_chg = "display: block";
        }


        // CHANGE OVER
        echo "textdiv += '<table><tr><td colspan=2>";
        echo "<div id=chg_over_form style=\"".$form_chg."\">";
            echo "<fieldset>";
                echo "<legend>Change Over:</legend>';";

                // CONNECTION TYPE - CHANGE OVER

                

                

                echo "textdiv += '<label>Connection Type Change Over: </label> ";
                echo "<select name=con_type_chg_over id=con_type_chg_over onchange=\"changeOverTypeConnection(this.options[this.selectedIndex].value);\" style=\"width: 180px;\"></select><br>";

                // PROPERTY CHANGE OVER
                echo "<label id=text_conn_prop>Property Change Over Connection:</label> <select name=refe id=refe onchange=con_prop_type(this.value) style=\"width: 600px\">";
                echo "</select><br>";

                // PROPERTY DA CHANGE OVER = 1 - DIFEFRENTE DA TECNOLOGIA DAS CONNECTIONS (!= GPON (POR EXEMPLO))      
                echo "<label>Connection: </label> ";
                echo "<select id=con_id name=con onchange=\"connection_equip(this.value)\" style=\"width: 150px; \">";
                echo "</select><br>";

                // SUBSCRIBER 
                echo "<label>Subscriber Change Over</label> <select name=owner_chg id=owner_chg style=\"width: 180px; \">";
                echo "</select><br>";

                // FORMULARIO DA CHNAGE OVER = 1
                echo "<div id=conn_type_fsan style=\"display: grid\">";
                echo "</div>";

                echo $form_customer_add_reconn_cgh_over."".$form_customer_add_chg."".$form_customer_add."".$form_customer_add_chg_end;

                

            echo "</fieldset>";
        echo "</div></table>';";


        // RECONNECTION
        echo "textdiv += '<table><tr><td colspan=2>";
        echo "<div id=rec_form style=\"".$form_rec."\">";
            echo "<fieldset>";
                echo "<legend>Reconnection:</legend>';";

                // ATIVA - LISTAR AS PROPRIEDADES DAS CONNECTIONS COM SERVICOS DESABILITADOS DO MESMO TYPE CONNECTION
                // DESATIVOS - LISTAR AS PROPRIEDADES DAS CONNECTIONS COM OU SEM SERVICOS DO MESMO TYPE CONNECTION
                echo "textdiv += '<label>Disabled Prop Services</label> <input type=checkbox name=disabled_prop_services id=disabled_prop_services onchange=\"changePropServicesDisabled(this, 0)\" ".$check_rec."><br>";
                echo "<label id=text_conn_prop_rec>Property Reconnection</label> <div id=lists_reconnections_bt_type><select name=refe_rec_7 id=refe_rec_7 onchange=\"con_prop_rec(this.value); \" style=\"width: 500px\">";
                echo "</select></div><span id=serv_prop_desativados></span> <input type=hidden name=conn_id_prop_rec id=conn_id_prop_rec>";
                // CONNECTION ID DA PROP ID SELECCIONADA
                echo "<div id=prop_conn_servicos_des style=\"height: 0\"></div>";
                echo "<div id=conn_assoc_prop_status_7 style=\"height: 0\"><input type=hidden name=rec_assoc value=1></div>";

                 // SUBSCRIBER 
                 echo "<br><label>Subscriber Reconnection: </label> <select name=owner_rec id=owner_rec style=\"width: 180px; \">";
                 echo "</select><br>";

                 echo $form_customer_add_reconn_cgh_over."".$form_customer_add_reconn."".$form_customer_add."".$form_customer_add_reconn_end;


            echo "</fieldset>";
        echo "</div></table>';";

       // ------------ FIM DA ALTERACAO DO CODIGO ------------------
        


        // SERVIÇOS
        
		echo "textdiv += '<table><tr><td><br>Services'+";
		
        // DESATIVA O SERVIÇO TV NO CASO DA TYPE CONNECTION FOR FWA
        // TV & INTERNET
		echo "'<tr><td>TV service<td><select name=tv id=tv ".$ds." style=\"width: 200px;\"><option value=0 "; if($prop['tv']==0) echo "selected"; echo ">no TV</option><option value=AMLA "; if($prop['tv']=="AMLA") echo "selected"; echo ">AMLA</option><option value=NOWO "; if($prop['tv']=="NOWO") echo "selected"; echo ">NOWO</option></select>'+	
		
		'<tr><td>Internet service<td><select name=internet_prof id=internet_prof style=\"width: 200px;\"><option value=0 "; if($prop['internet_prof']==0) echo "selected"; echo ">no internet</option>";

        // INTERNET
        
		$intservices=$mon3->query("select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down");
		while($serv=$intservices->fetch_assoc())
		{
			echo "<option value=".$serv['id'];
			if($prop['internet_prof']==$serv['id']) echo " selected";
			echo "> ".$serv['name']."</option>";
		}

		echo "</select>";

        // IP FIXO
        echo " Fixed ip:<select name=fixed_ip style=\"width: 120px;\"> <option selected value= >no fixed ip</option>";
		$refs=$mon3->query("select ip from int_fixed_ips where in_use!=1 order by ip");
        while($ref=$refs->fetch_assoc())
        {
            echo "<option value=".$ref['ip'];
            if($prop['fixed_ip']==$ref['ip']) echo " selected";
            echo ">".$ref['ip']."</option>";
        }
        echo " </select>'+";
        // PHONE 1
		echo "'<tr><td>phone line 1<td><select name=phone1 style=\"width: 200px;\"> <option selected value= >no line</option>";
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
        while($ref=$refs->fetch_assoc())
        {
            echo "<option value=".$ref['phone_number'];
            if($prop['phone1']==$ref['phone_number']) echo " selected";
            echo ">".$ref['phone_number']."</option>";
        }
        echo " </select><br>'+";
		// PHONE 2
		echo "'<tr><td>phone line 2<td><select name=phone2 style=\"width: 200px;\"> <option selected value= >no line</option>";
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
        while($ref=$refs->fetch_assoc())
        {
            echo "<option value=".$ref['phone_number'];
             if($prop['phone2']==$ref['phone_number']) echo " selected";
            echo ">".$ref['phone_number']."</option>";
        }

        echo " </select><br>'+

		'<tr><td>Need APs? how many?<td><input type=text name=aps size=5 value=\"".$prop['aps']."\" >un (250€ each)<br> '+
		'<tr><td>monthly revenue<td><input type=text name=monthly_price size=5 value=\"".$prop['monthly_price']."\" >€/month<br> '+
		
		'<tr>' +";
        echo "'<tr><td>Previous income /month <td><input type=text name=prev_rev_month ".$prev_month." size=5 value=\"".$prop['prev_rev_month']."\" >€<br> '+
        '</table></table>'+
        '';";
    

        // ------------ ALTERACAO DO CODIGO ------------------
        // SE TEM PROPRIEDADE SELECCIONADA ANTERIOR NO CASO SE FOR RECONNECTION OU CHANGE OVER
        if($prop['prop_id'] != 0)
        {
            // VERIFICAR SE E UMA RECONNECTION OU UMA CHANGE OVER
            $conn_ant_type = $mon3->query("SELECT type FROM connections WHERE id=".$prop['lead_conn_id_chg_over'])->fetch_assoc();

            // FUNCAO QUE VAI BUSCAR POR DEFEITO A PROPRIEDADE QUE QUER FAZER UMA RECONNECTION
            if($prop['is_reconnection']==1)
            {
                echo "RecPropConnType(".$prop['prop_id'].",'".$prop['con_type']."',".$prop['lead_conn_id_rcn'].",".$prop['lead_sub'].",'".$check_rec."');";
            }
            // FUNCAO QUE VAI BUSCAR POR DEFEITO A PROPRIEDADE QUE QUER FAZER UMA CHANGE OVER
            else if($prop['is_changeover']==1)
            {
                echo "ChgOverPropConnType(".$prop['prop_id'].",'".$prop['con_type']."','".$conn_ant_type['type']."',".$prop['lead_conn_id_chg_over'].",".$prop['lead_sub'].");";
            } 

            // BUSCAR OS CAMPOS CONCELHO, FREGUESIA, Prop REF e Subscriber

            
        }
        else if($prop['prop_id'] == 0)
        {
            echo "PropLeadNewConnection(".$lead_id.")";
        }

        // ------------ FIM DA ALTERACAO DO CODIGO ------------------

        echo "
	}";	
    
    echo "else if((soption >30) && (soption <34) )
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
	";
	
	
	
	
	
	
	
	
	
	
	
	
	
	echo "
	else if(soption==50)
	{";
        // MOSTRAR OS WARNINGS DA SUBMISSAO DE MUDANCA DE ESTADOS DAS LEADS
        echo "
	    $('#warning_services').html('');
        $('#info_submit').html('');";

        // ------------ ALTERACAO DO CODIGO ------------------

        // MOSTRAR UMA LISTA DOS SERVIÇOS ATIVOS DE UMA DADA CONNECTION
        $warn_at_serv = '';
        // IS RECONNECTION
        // CHECKED - VALOR POR DEFEITO SE A CONNECTION TEM SERVIÇOS DESATIVOS 
        // UNCHECKED - VALOR POR DEFEITO SE A CONNECTION TEM SERVICOS ATIVOS / DESATIVOS OU SEM SERVICOS 
        $check_rec = "checked";

        $v1 = 1;
        $val="";
        $warn_at_serv = '';
        

        // IS RECONNECTION
        if($prop['is_reconnection'] == 1)
        {       
                // VERIFICAR SE OS SERVICOS TEM CONNECTIONS COM SERVICOS DESATIVOS
                $servicos_at = $mon3->query("SELECT * FROM `services` where connection_id=".$prop['lead_conn_id_rcn']." and date_end='0000-00-00'");
                while($servico_at=$servicos_at->fetch_assoc())
                {
                    // DESATIVAR OS SERVICOS PARA CRIAR UM NOVO SERVIÇO

                    $servico_id = $servico_at['id'];
                    $serv_type = $servico_at['type'];
                    $serv_id_arr[] = $servico_id;
                    $serv_type_arr[] = $serv_type;
                
                    // WARNING DOS SERVICOS ATIVOS DA PROP ID DA CONNECTION SELECCIONADA QUE FOI FEITO NOS ESTADOS 1 A 5
                    $warn_at_serv .= "<font color=red>Service number ".$serv_type." on this connection number ".$servico_id. " is active. Service must be deactivated before finishing the reconnection </font><br>";
                    $v1 = 0;
                }
                // MOSTRAR A CONNECTION SE FOR UMA RECONNECTION
                $conexao=$mon3->query("SELECT * FROM connections where id = ".$prop['lead_conn_id_rcn']." ")->fetch_assoc();


                
                
        }
        else if($prop['is_changeover'] == 1)
        {
            if($prop['lead_conn_id_chg_over'] == "" || $prop['lead_conn_id_chg_over'] == 0)
            {
                $conexao = $mon3->query("SELECT * FROM connections WHERE property_id=".$prop['prop_id'])->fetch_assoc();
            }
            else
            {
                // MOSTRAR A CONNECTION SE FOR UMA CHANGE OVER
                $conexao=$mon3->query("SELECT * FROM connections where id = ".$prop['lead_conn_id_chg_over']." ")->fetch_assoc();
            }
            
        }

        
        

        // LISTAR OS WARNINGS DOS SERVICOS QUE ESTAO ATIVOS DA CONNECTION INSERIDA NOS ESTADOS 1 A 5 E NOS ESTADO 30
        // MOSTRAR O SERVICOS ATIVOS
        echo "var warn = '".$warn_at_serv."'; ";        
        echo "textdiv += ' form to activate services'+
		'<table>";

        
        // EQUIPAMENTOS ID
        $eq_id = "";
        // OLT ID SE FOR O TYPE CONNECTION 'GPON'
        $olt_id=0;

        // VERIFICAR SE OS SERVICOS ESTAO DESATIVOS OU NAO
        echo "<input type=hidden id=serv_act name=serv_act value=".$v1.">";
        if($prop['prop_id'] != 0 || $prop['prop_id'] != "")
        {
            echo "<input type=hidden id=prop_id name=prop_id value=".$prop['prop_id'].">";
        }

        // VERIFICAR SE TEM CONNECTION (FUNCIONA APENAS NAS RECONNECTIONS E NAS CHANGE OVERS)
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
            // CASO QUE FACA UMA NEW CONNECTION DA PROP CRIADA (ESTADO 30)
            echo "<input type=hidden id=con_id name=con_id>";
        }

        // IDENTIFICAR O EQUIPMENTO SE FOR CHANGE OVER OU RECONNECTION
        if($conexao != null)
        {
            // MOSTRAR A OPCAO DISABLED SERVICES SE FOR UMA RECONNECTION
            // 
            if($prop['is_reconnection'] == 1)
            {
                //echo "<tr><td>Disable Services? <td><input type=checkbox name=dis_serv_rec id=dis_serv_rec value=disabled_service onclick=disabled_services_check_update(this)>";
                echo "<tr><td>Connection <font color=#663399>".$conexao['id']."</font>";
            }
            // MOSTRAR A CONNECTION ANTERIOR PARA FAZER UMA NOVA LIGACAO DE CONVERTER UMA NOVA TECNOLOGIA (CHANGE OVER)
            else if($prop['is_changeover'] == 1)
            {
                echo "<tr><td>OLD Connection <font color=#663399>".$conexao['id']."</font>";
            }
            $con_b = $conexao['id'];
        }
        else
        {
            $con_b  = 0;
        }


        echo "<tr><td>Type<td> <span id=con name=connection_type>".$prop['con_type']."</span>";
        if($prop['con_type']=="") echo "GPON?(please select type on status 30)";


        

        if($v1 == 0)
        {
            $disabled_serv_rec = "onClick=\"if(confirm(`The connection number ".$conexao['id']." exists enabled services.\n Do you want to proceed?`))	{
                $('#supdatelead').prop('type','submit');	}else{ $('#supdatelead').prop('type','button'); }\"";
            echo "<input type=hidden id=serv_en_warn name=serv_en_warn value=".$v1.">";    
        }

        echo "';";


        if($prop['con_type']=='GPON' || $prop['con_type']=='')
        {
            // IDENTIFICAR O EQUIPMENTO - ONT
            $modelo = $mon3->query("SELECT * FROM ftth_ont WHERE fsan = '".$eq_id."'")->fetch_assoc();
            // CASO QUE EXISTE O EQUIPAMENTO DA ONT - GPON
            if($modelo != null)
            {
                // CAS QUE EXISTE EQUIPAMENTOS NAO VAZIOS NA ONT PARA BUSCAR A OLT ID
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

            // VERIFICAR SE TEM EQUIPAMENTO ID DA CONNECTION SELECCIONADA (CHANGE OVER & RECONNECTION) - GPON - ONT
            if($eq_id == "")
            {
                $val = "";
            }
            else
            {
                $val = "value=".$eq_id;
            }


                // ONT FSAN DO EQUIPAMENTO ID DA PROPRIEDADE  - AVISO SE TEM O EQUIPAMENTO
                echo" textdiv += '<tr><td>ONT FSAN<td><input type=text name=fsan id=fsan size=20 ".$val." oninput=equipConnectionAssoc(this.value,".$prop['prop_id'].",".$con_b .") ><span id=equip_conn_assoc></span><input type=hidden name=equip_assoc_not id=equip_assoc_not value=0> <span id=warn_equip></span>'+";
                // MODELO - GPON (MEPROF) - AVISO SE TEM MODELO
                echo "'<tr><td>FSAN model<td><select name=model id=models onchange=ModelChg(this.value) style=\"width: 200px;\">		</select><span id=warn_model></span>' +";
                
                // OLT ID PARA FAZER BUSCAR A PON E A VLAN PARA CRIAR ONT ID DA OLT CORRESPONDENTE - AVISO SE TEM A OLT DA GPON
                echo "'<tr><td>OLT<td><select name=olt_id id=olt_id onchange=\"updatepon(this.options[this.selectedIndex].value); updatevlan(this.options[this.selectedIndex].value);\" style=\"width: 180px;\"> ";
                echo "<option value=\"0\">Select OLT</option>";
                $olts=$mon3->query("select * from ftth_olt");
                while($olt=$olts->fetch_assoc()){ echo "<option value=".$olt['id'];
                if($olt['id']==$olt_id) echo " selected ";
                echo ">".$olt['name']."</option>";}
                echo"</select> <span id=warn_olt></span>";

                // MUDANCA DA PON PARA BUSCAR A 1-PON-OLT(NEXT ONT) - AVISO SE TEM A PON DA GPON
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
                // VERIFICAR SE TEM A ANTENNA DO EQUIPAMENTO FWA
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

            // VERIFICAR SE TEM EQUIPAMENTO ID DA CONNECTION SELECCIONADA (CHANGE OVER & RECONNECTION) - FWA - FWA CPE

            if($eq_id == "")
            {
                $val = "";
            }
            else
            {
                $val = "value=".$eq_id;
            }

            // FWA CPE DO EQUIPAMENTO ID DA PROPRIEDADE  - AVISO SE TEM O EQUIPAMENTO MAC
            echo"textdiv += '<tr><td>FWA cpe MAC<td><input type=text name=fsan id=fsan size=10 ".$val." oninput=equipConnectionAssoc(this.value,".$prop['prop_id'].",".$con_b .")><span id=equip_conn_assoc></span><input type=hidden id=equip_assoc_not name=equip_assoc_not> <span id=warn_equip></span>'+";
            // CPE MODELO - AVISO SE TEM CPE FWA
            echo "'<tr><td>CPE model<td><select name=model id=models onchange=ModelChg(this.value) style=\"width: 200px;\">		</select><span id=warn_model></span>' +";
            // ANTENNA FWA - VERIFICAR SE TEM A ANTENNA SELECCIONADA
            echo "'<tr><td>FWA antenna<td><select name=antenna id=antenna onchange=AntennaChg(this.value) style=\"width: 180px;\"> ";
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

        // LISTAR OS SERVICOS
        echo "textdiv += ' <tr><td><br>Services'+";

        // DESATIVAR A OPCAO TV SE FOR A TYPE CONNECTION 'FWA'
        if($prop['con_type']=='FWA')
        {
            $ds = "disabled";
        }
        else
        {
            $ds = "";
        }
        // SERVICOS TV - LISTAGEM - TV
        echo "'<tr><td>TV service<td><select name=tv ".$ds." style=\"width: 200px;\"><option value=0 "; if($prop['tv']==0) echo "selected"; echo ">no TV</option><option value=AMLA "; if($prop['tv']=="AMLA") echo "selected"; echo ">AMLA</option><option value=NOWO "; if($prop['tv']=="NOWO") echo "selected"; echo ">NOWO</option></select>'+";	
        // SERVICOS DE INTERNET - INT
        echo "'<tr><td>Internet service<td><select name=internet_prof id=internet_prof style=\"width: 200px;\"><option value=0 "; if($prop['internet_prof']==0) echo "selected"; echo ">no internet</option>";
        $intservices=$mon3->query("select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down");
        while($serv=$intservices->fetch_assoc())
        {
            echo "<option value=".$serv['id'];
            if($prop['internet_prof']==$serv['id']) echo " selected";
            echo "> ".$serv['name']."</option>";
        }
        echo "</select>'+";	
        // IP FIXO
        echo "'Fixed ip:<select name=fixed_ip style=\"width: 120px;\"> <option ";
        if($prop['fixed_ip']=="") echo "selected"; echo " value=>no fixed ip</option>";
        $refs=$mon3->query("select ip from int_fixed_ips where in_use!=1 order by ip"); while($ref=$refs->fetch_assoc()){ echo "<option "; if($prop['fixed_ip']==$ref['ip']) echo "selected"; echo " value=".$ref['ip'].">".$ref['ip']."</option>";} echo " </select>'+";
        // ROUTER
        echo "'<tr><td>router mode<td><select name=is_router style=\"width: 200px;\"> <option value=1>Router</option> <option value=0>Bridge on eth1</option></select>    <br> '+";
        // VLAN
        echo "'<tr><td>vlan<td><select id=vlans name=vlan style=\"width: 200px;\">";
        $vlans=$mon3->query("select vlan,description,total_dynamic_ips,olt_id from int_vlans where olt_id=".$prop['olt_id']." ");
        while($vlan=$vlans->fetch_assoc())
        {
            //olt_id
            $inuse=$mon3->query("select count(name) from service_attributes where name=\"vlan\" and value=\"".$vlan['vlan']."\"  ")->fetch_assoc();
            echo "<option value=".$vlan['vlan'];
            echo ">".$vlan['description']." - ".$inuse['count(name)']." of ".$vlan['total_dynamic_ips'];
        }
        echo "</select>  '+";
        
        // WIFI
        echo "'<tr><td>Wifi<td><select name=wifi style=\"width: 200px;\"><option value=1 selected>enabled</option><option value=0>disabled</option></select> '+	";
        // WIFI SSID
        echo "'<tr><td>Wifi SSID<td><input type=text name=wifi_ssid size=10 value=Lazer_".explode(" ",$prop['address'])[0]."><br> '+	";
        // WIFI PASSWORD
        echo "'<tr><td>Wifi passwd<td><input type=text name=wifi_key size=10 value=lzr".substr($refc['ref'],3,3).strtolower(substr($refc['ref'],0,3))."> <tr><td><br> '+	";
        
        
        
        
        
        
        
        // PHONE 1
        echo "'<tr><td>phone line 1<td><select name=phone1 style=\"width: 200px;\"> <option "; if($prop['phone1']=="") echo " selected "; echo " value= >no line</option>";
        $refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
        while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'];
        if($prop['phone1']==$ref['phone_number']) echo " selected ";
        echo ">".$ref['phone_number']."</option>";}
        echo " </select><br>'+";
        // PHONE 2
        echo "'<tr><td>phone line 2<td><select name=phone2 style=\"width: 200px;\"> <option "; if($prop['phone2']=="") echo " selected "; echo " value= >no line</option>";
        $refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number");
        while($ref=$refs->fetch_assoc()){ echo "<option value=".$ref['phone_number'];
        if($prop['phone2']==$ref['phone_number']) echo " selected ";
        echo ">".$ref['phone_number']."</option>";}
        echo " </select><br>'+
        
        '<tr><td><br>'+
        '</table>';";

        echo "
    }";



echo "
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

echo "

}




</script>
";


echo "
<b>status: </b><br> <select id=idstatus name=status onchange=\"aditionalstatus()\">


<option value=\"0\"
"; if ($prop['status']==0) echo "selected"; echo "
>0- waiting for analisis </option>




<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Viability </option>

<option value=1 
"; if ($prop['status']==1) echo "selected";
if ( $localuser['is_plan']==0 ) echo " disabled ";
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>1- viable, no costs to customer </option>

<option value=2 
"; if ($prop['status']==2) echo "selected";
if ( $localuser['is_plan']==0 ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>2- viable, with costs to customer, see estimate and timeframe</option>

<option value=3
"; if ($prop['status']==3) echo "selected";
if ( $localuser['is_plan']==0 ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>3- not viable - out of network </option>

<option value=4 
"; if ($prop['status']==4) echo "selected";
if ( $localuser['is_plan']==0 ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>4- not viable - no infrastructures </option>

<option value=5 
"; if ($prop['status']==5) echo "selected";
if ( $localuser['is_plan']==0 ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>5- Incorrect address or coordenates </option>


<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Specific Proposals</option>

<option value=6 
"; if ($prop['status']==6) echo "selected";
if ( $localuser['is_plan']==0 ) echo " disabled ";
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "> 6- Special project-CTO and sales to present proposal</option>

<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled> Customer Decision</option>
<option value=9 
"; if ($prop['status']==9) echo "selected";
if ($prop['status']==0 || ($prop['status']!=3 && $prop['status']!=4) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>9- Customer is notified, Not possible </option>


<option value=10 
"; if ($prop['status']==10) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>10- Customer is notified, waiting for reply </option>

<option value=11 
"; if ($prop['status']==11) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>11- Not accepted by customer  - note with justification  </option>

<option value=12 
"; if ($prop['status']==12) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>12- Accepted by customer, collecting paperwork</option>

<option value=\"0\" disabled>  </option>
<option value=\"0\" disabled>  Quote for install costs(optional)</option>


<option value=13 
"; if ($prop['status']==13) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>13- paperwork ready, tech dep. to quote final price </option>

<option value=14 
"; if ($prop['status']==14) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>14- paperwork ready, quote ready, see notes </option>
<option value=15 
"; if ($prop['status']==15) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>15- Customer is notified about quote, waiting for reply </option>

<option value=19 
"; if ($prop['status']==19) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>19- Not accepted by customer  - note with justification </option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled>Internal process</option>

<option value=20 
"; if ($prop['status']==20) echo "selected";
if ($prop['status']==0 || ($prop['status']>2 && $prop['status']<6) ) echo " disabled ";
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>20- All approved, paperwork ready, to be insert into system </option>


<option value=21 
"; if ($prop['status']==21) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==0 ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>21- On hold until paperwork is completed </option>


<option value=29 
"; if ($prop['status']==29) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==0 ) echo " disabled "; 
// no caso que ja instalou a connections e os seus servicos (estado 50)
echo "
>29- Contract in the system, but waiting for payment of the installation costs </option>



<option value=\"\" disabled></option>
<option value=\"0\" disabled> Technical dep.</option>";

echo "
<option value=30 
"; if ($prop['status']==30) echo "selected";
if ($prop['status']<20 || $localuser['is_admin']==0 ) echo " disabled ";
echo "
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
>33- Needs networking (note with scheduling/jobsheet id) </option>";

echo "<option value=38
"; if ($prop['status']==38) echo "selected";  if ($prop['status']<30 || ($prop['is_reconnection'] == 0)) echo " disabled "; echo "
>38 - Operations check equipment </option>

<option value=\"\" disabled></option>
<option value=\"0\" disabled> Booking </option>

<option value=40 
"; if ($prop['status']==40) echo "selected";  if ($prop['status']<38 && $prop['is_reconnection'] == 1) echo " disabled "; echo "
>40- Network ready, can be booked w customer (sends email to customer)</option>

<option value=41 
"; if ($prop['status']==41) echo "selected";  if ($prop['status']<38 && $prop['is_reconnection'] == 1) echo " disabled "; echo "
>41- Booked with customer (notes with date and time) </option>

<option value=42 
"; if ($prop['status']==42) echo "selected";  if ($prop['status']<38 && $prop['is_reconnection'] == 1) echo " disabled "; echo "
>42- Infrastucture issues waiting on us to reschedule </option>

<option value=43 
"; if ($prop['status']==43) echo "selected";  if ($prop['status']<38 && $prop['is_reconnection'] == 1) echo " disabled "; echo "
>43- Infrastucture issues waiting on customer </option>


<option value=\"\" disabled></option>
<option value=\"0\" disabled> Installation </option>
<option value=50 
"; if ($prop['status']==50) echo "selected";
if ($prop['status']<38 && $prop['is_reconnection'] == 1) echo " disabled "; echo "
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
<tr><td colspan=2 align=center><br> <br> <input type=submit id=supdatelead name=supdatelead value=update ".$disabled_serv_rec." >	
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
$pdf_png_image = '';
$val = array();

$val_file_pdf = array();

echo"
<table width=900px><tr><td>
Files:<br>
<tr><td colspan=2 align=center>";

$i=0;
if(file_exists($mon_leads.$lead_id))
{
    $files1 = scandir($mon_leads.$lead_id);
    
    
    foreach($files1 as $file1)
    {
        if(substr($file1,0,1)!=".")
        {
            if($i%6==0)
            {
                $is_images .="<tr>";
            }

            if(strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpeg" )
            {
                $is_images .= "<td align=center><a href=leads/".$lead_id."/".$file1." title = ".$file1." data-link_page=leads/".$lead_id."/".$file1." class=link_slider target=_blank>";
                $is_images .= "<img src=leads/".$lead_id."/".$file1." height=100px alt=".$file1." class=img_slider > </a> ";
            }
                
            elseif(preg_match("/_pdf/", $file1))
            {
                 
                $file_teste = preg_replace("/_pdf/", '', $file1);
                $file_teste = preg_replace("/.png/", '', $file_teste);
                $file_teste = $file_teste.".pdf";
                $is_images .= "<td align=center> <a href=leads/".$lead_id."/".$file1." data-link_page=leads/".$lead_id."/".$file_teste." title = ".$file_teste.">";
                $is_images .= "<img src=leads/".$lead_id."/".$file1." height=100px class=\"img_pdf\" alt=".$file_teste.">  </a> ";
            }

			
            elseif (preg_match("/.pdf/", $file1) and (!file_exists($mon_leads.$lead_id."/".preg_replace("/.pdf/", '_pdf', $file1).".png")))
			{
			
		            $file_im = $mon_leads.$lead_id."/".$file1."[0]";	
					$im = new Imagick();
					$im->setResolution(300, 300);     //set the resolution of the resulting jpg
					try
					{
						$im->readImage($file_im);    //[0] for the first page
						$file1png = preg_replace("/.pdf/", '_pdf', $file1).".png";
						$full_file = $mon_leads.$lead_id."/".$file1png;
						$im->setImageFilename($full_file);
						$im->writeImage();
					}
					catch(ImagickException $e) {
						$var .= "Error: " . $e -> getMessage() . "\n";
						echo $var;
						
						
				    }

                    $file_pic = "leads/".$lead_id."/".$file1png;
			
			
			
			
				$is_images .= "<td align=center> <a href=".$file_pic." data-link_page=leads/".$lead_id."/".$file1." target=_blank  title = ".$file1.">";
                $is_images .= "<img src=".$file_pic." height=100px> </a> ";
		
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
<tr><td>Concelho:<td><select name=concelho id=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value,\'\')\" style=width:400px>";
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
<tr><td>Concelho:<td><select id=concelho name=concelho onchange=\"updatefregep(this.options[this.selectedIndex].value,\'\')\" style=width:400px>";
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







