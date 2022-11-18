<?php

require '/var/www/html/init_web.php';

$folder = dirname(__FILE__);

serverNameFolder($folder);


// Funcoes do Webservices - Alterados

// Funcoes do WebServices - Nao Alterados


if($_GET['dump_serv']!="")
{
    $cons= $mon3->query("select * from connections where (date_end=\"0000-00-00\" or date_end=\"\") order by date_start");




    echo $mon3->error;
    $csv="";
    // echo "total: ".$cons->num_rows."<br>";

    while($con=$cons->fetch_assoc())
    {

        $ignore=$mon3->query("select conid from billing_ignored where conid=".$con['id']);
        if($ignore->num_rows==0)
        {

            //echo $con['id']."<br>";
            $prop= $mon3->query("select * from properties where id=".$con['property_id'])->fetch_assoc();
            echo $mon3->error;

            $servs=$mon3->query("select * from services where connection_id=".$con['id']." and (date_end=\"0000-00-00\" or date_end=\"\") ");
            if($servs->num_rows>0)
            {

                while($serv=$servs->fetch_assoc())
                {

                    if($serv['type']=="INT")
                    {
                        $speed=$mon3->query("select int_services.name from service_attributes left join int_services on service_attributes.value=int_services.id where service_attributes.service_id=".$serv['id']." and service_attributes.name=\"speed\"")->fetch_assoc();
                        echo $mon3->error;


                        $csv[]=$con['id'].";".$prop['ref'].";\"".$prop['address']."\";".$con['type'].";".$con['date_start'].";".$serv['type'].";".$speed['name'];
                    }
                    elseif($serv['type']=="PHN")
                    {
                        $nr=$mon3->query("select voip_accounts.caller_id from service_attributes left join voip_accounts on service_attributes.value=voip_accounts.username where service_attributes.service_id=".$serv['id']." and service_attributes.name=\"account\"")->fetch_assoc();
                        echo $mon3->error;

                        $csv[]= $con['id'].";".$prop['ref'].";\"".$prop['address']."\";".$con['type'].";".$con['date_start'].";".$serv['type'].";".$nr['caller_id'];
                    }
                    else
                    {
                        $csv[]= $con['id'].";".$prop['ref'].";\"".$prop['address']."\";".$con['type'].";".$con['date_start'].";".$serv['type'].";";
                    }



                }


            }





        }
    }


    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=services.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";

}




// PAGINA - GPON - index.php?gpon=1 - OBTER A PESQUISA DAS GPONS (DOWNLOAD)
elseif($_GET['dump_onts']!="")
{

    $search_onts = "";
    $pon = $_GET['pon'];
    $olt = $_GET['olt'];
    $status = $_GET['status'];

    $wq = "";

    // PON

    if($pon!="" && $olt>0)
    {
        $wq .= " AND ont_id LIKE \"1-".$pon."-%\" ";
    }   

    // OLT

    if($olt != "")
    {
        $wq .= " AND olt_id=\"".$olt."\" ";
    }

    // STATUS

    if($status != "")
    {
        $wq .= " AND status LIKE '%".$status."%'";
    }


    $search_onts = "select ftth_ont.fsan,ftth_ont.olt_id,ftth_ont.ont_id,ftth_ont.mng_ip,ftth_ont.status_timestamp,ftth_ont.status, ftth_ont.serial, properties.address,properties.id as 'prop_id',properties.ref, connections.id as 'con_id', connections.date_start
    from ftth_ont 
    left join connections on ftth_ont.fsan=connections.equip_id
    left join properties on connections.property_id=properties.id
    where olt_id>0 and ont_id!=\"\" ".$wq." and connections.date_end = '0000-00-00' ORDER BY connections.date_start DESC";

    $cons= $mon3->query($search_onts);

    $csv[]="fsan;serial;ref;connection_id;date_start";
    while($con=$cons->fetch_assoc())
    {
        $url_link = MON_SERVER."index.php?props=1&propid=".$con['prop_id'];

        $fsan = $con['fsan'];
        $serial = $con['serial'];
        $ref = $con['ref'];
        $date_start = $con['date_start'];
        $prop_id = $con['prop_id'];


        //$csv[]=$con['fsan'].";".$con['serial'].";".$con['ref'].";=HYPERLINK(".$url_link.",'".$con['prop_id']."');".$con['date_start'];
        $csv[] = $fsan.';'.$serial.';'.$ref.';"=HYPERLINK(""'.$url_link.'"";'.$prop_id.')"' . ";" . $date_start;
    }


    $text_searchs_props = '';

    if($pon != "")
    {
        $text_searchs_props .= "pon-".$pon." ";
    }
    if($olt!="")
    {
        $text_searchs_props .= "olt_id-".$olt." ";
    }

    if($status!="")
    {
        $text_searchs_props .= "status-".$status." ";
    }

    if($status != "")
    {
        $text_searchs_props .= "status-".$status." ";
    }


    $search_l = "-search_onts=".$text_searchs_props;






    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=onts".$search_l.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";



}

// PAGINA - FWA - index.php?fwa=1 - OBTER A PESQUISA DAS DOS EQUIPAMENTOS EM FWA (DOWNLOAD)
elseif($_GET['dump_antenna']!="")
{
    $search_antenna = "";
    $ant = $_GET['ant'];
    $site = $_GET['site'];

    $wq = "";


    if($ant!="" && $site>0)
    {
        $wq.=" AND antenna=\"$ant\" ";
    }

    if($site != "")
    { 
        $wq.=" AND fwa_antennas.headend_pop_id=\"".$site."\" ";
    }


    
    $search_antenna = "select fwa_cpe.mac,model,mng_ip,status,name,antenna,ip,headend_pop_id,property_id,address,ref, connections.date_start
    from fwa_cpe 
    left join fwa_antennas on fwa_cpe.antenna=fwa_antennas.id 
    left join connections on fwa_cpe.mac=connections.equip_id
    left join properties on connections.property_id=properties.id
    where fwa_cpe.mac!=\"\" ".$wq." and connections.date_end = '0000-00-00' ORDER BY connections.date_start DESC";

    $csv[]="mac;mng_ip;antenna;ref;connection_id;date_start";

    $cons= $mon3->query($search_antenna);

    while($con=$cons->fetch_assoc())
    {
        $url_link = MON_SERVER."index.php?props=1&propid=".$con['property_id'];

        $mac = $con['mac'];
        $name = $con['name'];
        $mng_ip = $con['mng_ip'];
        $ref = $con['ref'];
        $date_start = $con['date_start'];
        $prop_id = $con['property_id'];

        $csv[] = $mac.';'.$mng_ip.';'.$name.';'.$ref.';"=HYPERLINK(""'.$url_link.'"";'.$prop_id.')"' . ";" . $date_start;

    }

    $text_searchs_props = '';

    if($ant != "")
    {
        $text_searchs_props .= "antenna-".$ant." ";
    }
    if($site!="")
    {
        $text_searchs_props .= "site-".$site." ";
    }


    $search_l = "-search_antenna=".$text_searchs_props;

    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=onts".$search_l.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";






    
}

// PAGINA - FWA - index.php?coax=1 - OBTER A PESQUISA DAS COAXIAIS (DOWNLOAD)
elseif($_GET['dump_coax']!="")
{
    $search_coax = "";
    $cmts = $_GET['cmts'];
    $upstream = $_GET['upstream'];
    $status = $_GET['status'];
    $where = "";

    if($upstream!="" && $cmts>0)
    {
        $where.=" AND interface LIKE \"".$upstream."\" ";
    }


    if($cmts != "")
    { 
        $where.=" AND cmts=\"".$cmts."\" ";
    }
    if($status!="")
    {
        $where.=" AND status LIKE '%".$status."%'";
    } 
    //if($con_on!="")
    //{
    $where.=" AND connections.date_end =\"0000-00-00\" ";
    //} 


    if($where!="")
    {
        $where=" where ". substr($where, 5);
    }

    $csv[]="mac;cmts;mng_ip;ref;connection_id;date_start";

    $search_coax = "select coax_modem.mac,coax_modem.cmts,coax_modem.mng_ip,coax_modem.status_timestamp, coax_cmts.name, properties.ref,
    coax_modem.status,coax_modem.us_power,coax_modem.ds_power,coax_modem.interface,properties.address,properties.id as 'prop_id',
    properties.ref,connections.id as 'conid', connections.date_start
    from coax_modem left join connections on coax_modem.mac=connections.equip_id
    left join properties on connections.property_id=properties.id left join coax_cmts on coax_cmts.id=coax_modem.cmts" . $where." AND connections.type = 'COAX' ";

    

    $cons=$mon3->query($search_coax);

    while($con=$cons->fetch_assoc())
    {
        $url_link = MON_SERVER."index.php?props=1&propid=".$con['prop_id'];
        $prop_id = $con['prop_id'];
        $mac = $con['mac'];
        $cmts_name = $con['name'];
        $mng_ip = $con['mng_ip'];
        $ref = $con['ref'];
        $date_start = $con['date_start'];

        $url_mng_ip = "http://".$mng_ip."/";


        $csv[] = $mac.';'.$cmts_name.';"=HYPERLINK(""'.$url_mng_ip.'"";""'.$mng_ip.'"")"'.";".$ref.';"=HYPERLINK(""'.$url_link.'"";'.$prop_id.')"' . ";" . $date_start;





    }


    $text_searchs_props = '';

    if($cmts != "")
    {
        $text_searchs_props .= "cmts-".$cmts." ";
    }
    if($upstream!="")
    {
        $text_searchs_props .= "upstream-".$upstream." ";
    }
    if($status!="")
    {
        $text_searchs_props .= "status-".$status." ";
    }


    $search_l = "-search_coax=".$text_searchs_props;

    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=onts".$search_l.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";

}

// PAGINA - FWA - index.php?props=1 - OBTER A PESQUISA DAS PROPRIEDADES (DOWNLOAD)
elseif($_GET['dump_prop']!="")
{
    $wq = "";
    if($_GET['searchb'] != "")
    {
        $wq .= " AND (address LIKE '%".$_GET['searchb']."%' or ref LIKE '%".$_GET['searchb']."%') ";
    }

    $props= $mon3->query("select * from properties where 1 ".$wq);
    if($props->num_rows>0)
    {

        while($prop=$props->fetch_assoc())
        {
            $csv[]="\"".$prop['ref']."\",\"".$prop['address']."\",\"".$prop['ref']."@lazerspeed.com\"";


        }
    }

    if($_GET['searchb'] != "")
    {
        $search_prop = "-search_prop=".$_GET['searchb'];
    }
    else
    {
        $search_prop = "";
    }



    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=props".$search_prop.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";

}



// PAGINA - LEADS - index.php?propleads=1 - OBTER A PESQUISA DAS LEADS (DOWNLOAD)
elseif($_GET['dump_leads']!="")
{
    $csv[]="\"id\",  \"address\", \"freguesia\", \"coords\", \"name\", \"email\", \"phone\", \"agent_id\", \"status\", \"prop_id\", \"date_lead\", \"date_viability\", \"date_accept\", \"date_quoted\", \"date_papwk\", \"date_book\", \"date_install\", \"date_installed\", \"date_closed\", \"date_modified\", \"drop_length\", \"FAT_coords\", \"ORAC_pits\", \"ORAP_poles\", \"connection_cost\", \"is_network_ready\", \"network_cost\", \"estimated_quote\", \"timeframe\", \"quoted\", \"contract_id\", \"con_type\", \"fwa_id\", \"olt_id\", \"internet_prof\", \"fixed_ip\", \"tv\", \"phone1\", \"phone2\", \"aps\", \"install_price\", \"monthly_price\", \"model\", \"is_changeover\", \"prev_rev_month\", \"notes\", \"ORAP_id\", \"ORAC_id\", \"networking_job_id\", \"installation_job_id\", \"technician\", \"final_netw_cost\", \"final_inst_cost\", \"NPS_score\", \"manager_score\", \"has_pictures\", \"speedtest\", \"created_by\", \"is_active\", \"is_reconnection\", \"lead_sub\", \"lead_conn_id_chg_over\", \"lead_conn_id_rcn\" ";

    $search_lead = "";
    $searchb = $_GET['searchb'];
    $filter = $_GET['filter'];
    $status = $_GET['status'];

    $username = $_GET['username'];

    $owner = $_GET['owner'];

    $type_user = $mon3->query("SELECT * FROM users WHERE username = '".$username."'")->fetch_assoc();

    if($searchb != "")
    {
        $wq .= " AND (address LIKE '%".$searchb."%' or name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%')";
    }

    if($filter != "")
    {
        if($filter == "active")
        {
            $wq .=" AND status<60 and is_active=1 ";
        }
        else if($filter == "refer")
        {
            if($type_user['is_plan']==1){
                $wq.=" OR status=0 OR status=6 OR status=7 OR status=13";
            }
            if($type_user['is_admin']==1){
                $wq.=" OR status=20  OR status=31  OR status=40 ";
            }
            if($type_user['is_tmng']==1){
                $wq.=" OR status=30 OR status=31 OR status=32 OR status=33 OR status=40 OR status=41 OR status=42 OR status=43 OR status=50 ";
            }
            if($type_user['is_sales']==1){
                $wq= "   (".substr($status,3)." OR status=1 OR status=2 OR status=3 OR status=4 OR status=6 OR status=10 OR status=14 OR status=15 OR status=40 ) AND created_by=\"".$username."\" ";
            }
        }
        
    }

    if($owner!="all" && $owner!="")
    {
        $wq .= " AND created_by = '".$owner."'";
    }

    if($status!="all" && $status!="")
    {
        $wq .= " AND status=\"$status\"";
    }


    $leads_search = "SELECT * FROM `property_leads` where 1 ".$wq;


    //echo $leads_search;

    
    $props= $mon3->query($leads_search);
    echo $mon3->error;
    if($props->num_rows>0)
    {
        while($prop=$props->fetch_assoc())
        {
            $csv[]="\"".$prop['id']."\",\"".$prop[ 'address']."\",\"".$prop[ 'freguesia']."\",\"".$prop[ 'coords']."\",\"".$prop[ 'name']."\",\"".$prop[ 'email']."\",\"".$prop[ 'phone']."\",\"".$prop[ 'agent_id']."\",\"".$prop[ 'status']."\",\"".$prop[ 'prop_id']."\",\"".$prop[ 'date_lead']."\",\"".$prop[ 'date_viability']."\",\"".$prop[ 'date_accept']."\",\"".$prop[ 'date_quoted']."\",\"".$prop[ 'date_papwk']."\",\"".$prop[ 'date_book']."\",\"".$prop[ 'date_install']."\",\"".$prop[ 'date_installed']."\",\"".$prop[ 'date_closed']."\",\"".$prop[ 'date_modified']."\",\"".$prop[ 'drop_length']."\",\"".$prop[ 'FAT_coords']."\",\"".$prop[ 'ORAC_pits']."\",\"".$prop[ 'ORAP_poles']."\",\"".$prop[ 'connection_cost']."\",\"".$prop[ 'is_network_ready']."\",\"".$prop[ 'network_cost']."\",\"".$prop[ 'estimated_quote']."\",\"".$prop[ 'timeframe']."\",\"".$prop[ 'quoted']."\",\"".$prop[ 'contract_id']."\",\"".$prop[ 'con_type']."\",\"".$prop[ 'fwa_id']."\",\"".$prop[ 'olt_id']."\",\"".$prop[ 'internet_prof']."\",\"".$prop[ 'fixed_ip']."\",\"".$prop[ 'tv']."\",\"".$prop[ 'phone1']."\",\"".$prop[ 'phone2']."\",\"".$prop[ 'aps']."\",\"".$prop[ 'install_price']."\",\"".$prop[ 'monthly_price']."\",\"".$prop[ 'model']."\",\"".$prop[ 'is_changeover']."\",\"".$prop[ 'prev_rev_month']."\",\"".$prop[ 'notes']."\",\"".$prop[ 'ORAP_id']."\",\"".$prop[ 'ORAC_id']."\",\"".$prop[ 'networking_job_id']."\",\"".$prop[ 'installation_job_id']."\",\"".$prop[ 'technician']."\",\"".$prop[ 'final_netw_cost']."\",\"".$prop[ 'final_inst_cost']."\",\"".$prop[ 'NPS_score']."\",\"".$prop[ 'manager_score']."\",\"".$prop[ 'has_pictures']."\",\"".$prop[ 'speedtest']."\",\"".$prop[ 'created_by']."\",\"".$prop[ 'is_active']."\",\"".$prop[ 'is_reconnection']."\",\"".$prop[ 'lead_sub']."\",\"".$prop[ 'lead_conn_id_chg_over']."\",\"".$prop[ 'lead_conn_id_rcn']."\" ";
        }
    }

    $text_searchs_leads = '';

    if($searchb != "")
    {
        $text_searchs_leads .= "searchb-".$searchb." ";
    }
    if($owner!="")
    {
        $text_searchs_leads .= "owner-".$owner." ";
    }

    if($status!="")
    {
        $text_searchs_leads .= "status-".$status." ";
    }

    if($filter != "")
    {
        $text_searchs_leads .= "filter-".$filter." ";
    }


    $search_l = "-search_prop=".$text_searchs_leads;





    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=props".$search_l.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";
    

}



elseif($_GET['downloadgponlivestatus']!="")
{
    $pon=mysqli_real_escape_string($mon3,$_GET['pon']);
    $olt=mysqli_real_escape_string($mon3,$_GET['olt']);

    if($olt>0 && $pon!="")
    {



        $onts= $mon3->query("SELECT olt_id,ont_id,mng_ip,address FROM `ftth_ont` left join connections on ftth_ont.fsan=connections.equip_id left join properties on connections.property_id=properties.id
 WHERE olt_id=$olt AND ont_id like \"1-".$pon."%\"  ORDER BY olt_id,ont_id ");


        while($ont=$onts->fetch_assoc())
        {

            $rf="";
            $rx="";

            $ip=$ont['mng_ip'];

            $model = snmp2_get($ip, "ZhonePrivate", ".1.3.6.1.2.1.1.1.0","500000",2);
            $model=explode('"',$model);
            $model=explode(' ',$model[1]);
            $model=$model[0];
            if($model=="")
                $model="offline";
            else{
                $rf = snmp2_get($ip, "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.12.1","400000",2);
                $rf=explode('"',$rf);
                $rf=explode(' ',$rf[1]);
                $rf=$rf[0];

                $rx= snmp2_get($ip, "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.7.1","400000",2);
                $rx=explode('"',$rx);
                $rx=explode(' ',$rx[1]);
                $rx=$rx[0];
            }



            $csv[]= $ont['olt_id'].";".$ont['ont_id'].";$model;".$ont['address'].";".$ont['mng_ip'].";$rx;$rf";
        }








        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename=liveonts.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        foreach($csv as $line)
            echo $line."\n";


    }
}

else
{

    
    header('Content-type: text/html; charset=UTF-8');

    
    // OBTER AS CMTS - COAX
    if($_GET['cmtsbyolt']!="")
    {
        $cmts=mysqli_real_escape_string($mon3, $_GET['cmtsbyolt']);

        $cm=$mon3->query("select * from coax_cmts order by name ");
        echo $mon3->error;
        while($c=$cm->fetch_assoc())
        {
            $cb[]=array($c['name'],$c['id']);
        }
        echo json_encode($cb, JSON_UNESCAPED_UNICODE);
    }

    // OBTER AS CONEXOES DO TIPO ETH
    elseif($_GET['ethbyolt']!="")
    {
        $eth=mysqli_real_escape_string($mon3, $_GET['ethbyolt']);

        $e=$mon3->query("select properties.ref,connections.id,properties.address,connections.type from properties left join connections on properties.id=connections.property_id where connections.type='ETH' order by properties.ref");
        echo $mon3->error;
        while($e_s=$e->fetch_assoc())
        {
            $eb[]=array($e_s['ref']." ".$e_s['type'],$e_s['id']);
        }
        echo json_encode($eb, JSON_UNESCAPED_UNICODE);
    }



    





    // LISTAR OS CLIENTES - LEADS CUSTS
    elseif($_GET['customer']!="")
    {
        $cust=mysqli_real_escape_string($mon3, $_GET['customer']);
        $mng=mysqli_real_escape_string($mon3, $_GET['mng']);
        $query="select id,name,email,fiscal_nr from customers where ( name LIKE \"%$cust%\" OR email LIKE \"%$cust%\" OR fiscal_nr  LIKE \"%$cust%\" ) ";
        if ($mng==1)
            $query .= "AND is_management=1 ";

        $fregs=$mon3->query($query." order by name limit 0,10");
    }





    // OBTER AS FREGUESIAS - functions.js - updatefregep(conc)
    elseif($_GET['getfreg']!="")
    {
        $conc=mysqli_real_escape_string($mon3, $_GET['conc']);
        $fregs=$mon3->query("select id,freguesia from freguesias where concelho=$conc order by freguesia");
        while($freg=$fregs->fetch_assoc())
        {
            $frg[]=$freg;
        }

        echo json_encode($frg, JSON_UNESCAPED_UNICODE);
    }

    // ADICIONEI
    // OBTER OS CONCELHOS DAS FREGUESIAS - functions.js - updateconcelhosep(country)
    elseif($_GET['getconcelho']!="")
    {
        $merge = array();
        $i=0;
        $concelho=mysqli_real_escape_string($mon3, $_GET['country']);
        $concs=$mon3->query('select * from concelhos where pais="'.$concelho.'" order by concelho');
        while($conc_e=$concs->fetch_assoc())
        {
            $co[]=$conc_e;
            $merge = array('concelhos' => $co);
            $i++;
        }
        for($j=0; $j<$i; $j++)
        {
            $q = 'select * from freguesias where concelho = '.$co[$j]['id'];
            $fregs=$mon3->query($q);
            while($freg=$fregs->fetch_assoc())
            {
                //echo $freg['id'].$freg['freguesia'];
                $fg[]=$freg;
                $merge = array_merge($merge, ['freguesia' => $fg]);
            }
        }
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    // ----------------------------------------------------------------  LEADS.PHP -----------------------------------------------------------------------------------------

    // ----------------------------------------------------------- ESTADOS 1 A 5 & 30 & 50  -----------------------------------------------------------------------------------------


    // RECONNECTION

    // Propriedades do tipo de conxao correspondente

    // functions.js - chg_type_conn_not_check(con_type)
    // VERIFICAR AS CONEXOES QUE TEM SERVIÇOS ATIVOS E DESATIVOS - LEADS 1 & 5 - RECONNECTION

    elseif($_GET['prop_conn_type'] != '')
    {
        $wq='';
        $merge = array();
        if($_GET['con_type'] != "" )
        {
            $wq .= "connections.type = '".$_GET['con_type']."'";
        }
        

        $conn_di=$mon3->query("SELECT DISTINCT properties.id as 'prop_id',
        properties.ref,properties.address,connections.id as 'conn_id' 
        from properties 
        INNER JOIN connections on connections.property_id=properties.id 
        WHERE 1 AND ".$wq." AND connections.date_end = '0000-00-00' order by properties.ref");

        while($conn_di_each=$conn_di->fetch_assoc())
        {
            //echo addslashes(str_replace(array("\n", "\r"), '',$conn_di_each['address']))."<br>";
            $coa[]=array($conn_di_each['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$conn_di_each['address'])),$conn_di_each['prop_id'],$conn_di_each['conn_id']);
           
        }
        $merge = array('prop_conn_type' => $coa);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }


    // Propriedades do tipo de conxao do serviço da conexao que estao desativados

    
    // functions.js - chg_type_conn_check(con_type)
    // VERIFICAR AS CONEXOES QUE TEM SERVIÇOS DESATIVOS - LEADS 1 & 5 - RECONNECTION

    elseif($_GET['prop_conn_type_serv_des'] != '')
    {
        $wq = '';
        $wq_not_des_serv = '';
        //$owner_id = $_GET['con_type'];
        if ($_GET['con_type'] != "") {
            $wq .= " AND connections.type = '" . $_GET['con_type'] . "'";
        }

        $conn_serv_des=$mon3->query("select DISTINCT properties.id as 'prop_id',properties.ref as 'ref_prop', 
        properties.address as 'prop_addr', connections.id as 'conn_id', 
        connections.type as 'conn_type', connections.equip_id as 'conn_equip_id', connections.dis_services as 'dis_services' 
        from properties 
        inner join connections on connections.property_id=properties.id 
        inner join services on services.connection_id=connections.id 
        where 1 ".$wq." and connections.date_end = '0000-00-00'
        AND services.date_end != '0000-00-00' and connections.id NOT IN (SELECT connections.id from 
        connections inner join services on services.connection_id=connections.id 
        where 1 ".$wq." and connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00') OR connections.equip_id = ''
        ".$wq." and connections.date_end = '0000-00-00' AND services.date_end != '0000-00-00'
        and connections.id NOT IN (SELECT connections.id from connections 
        inner join services on services.connection_id=connections.id 
        where 1 ".$wq." AND connections.equip_id = '' and connections.date_end = '0000-00-00' 
        AND services.date_end = '0000-00-00') 
        order by properties.ref ASC");

            $merge = array();
            while($conn_di_each=$conn_serv_des->fetch_assoc())
            {
                if($conn_di_each['dis_services'] == 2)
                {
                    $susp_ser = "(Disconnected)";
                }
                else if($conn_di_each['dis_services'] == 1)
                {
                    $susp_ser = "(Serviçes Disabled Suspended)";
                }
                else if($conn_di_each['dis_services'] == 0)
                {
                    $susp_ser = "";
                }
                $coa[]=array($conn_di_each['ref_prop']."-".addslashes(str_replace(array("\n", "\r"), '',$conn_di_each['prop_addr']))." - Connection:".$conn_di_each['conn_id']." ".$susp_ser,$conn_di_each['prop_id'], $conn_di_each['conn_id'],$conn_di_each['serv_id'] );
            }

        $merge = array('prop_conn_type' => $coa);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);



    }


    // VERIFICAR UMA LISTA DE PROPRIEDADES DIFERENTES AO TIPO DE CONEXAO SELECCIONADA
    elseif($_GET['conn_prop_id_diff'] != "")
    {
        $type = $_GET['type'];
        $arr_con = array();
        if($type == "GPON")
        {
            //$dist_conns=$mon3->query("SELECT DISTINCT type FROM connections WHERE type='FWA' OR type='COAX'");
            //$arr_con[]=array(['FWA, COAX']);
            array_push($arr_con, 'FWA', 'COAX');
            $props=$mon3->query("select DISTINCT properties.id,properties.ref,properties.address
            from properties left join connections on connections.property_id=properties.id where connections.type='FWA' AND connections.date_end = '0000-00-00' order by properties.ref");
        }
        else if($type == "FWA")
        {
            array_push($arr_con, 'GPON');
            //$dist_conns=$mon3->query("SELECT DISTINCT type FROM connections WHERE type='GPON'");
            $props=$mon3->query("select DISTINCT properties.id,properties.ref,properties.address
            from properties left join connections on connections.property_id=properties.id where connections.type='GPON' AND connections.date_end = '0000-00-00' order by properties.ref");
        }
        else if($type == "COAX")
        {
            array_push($arr_con, 'GPON', 'FWA');
            //$dist_conns=$mon3->query("SELECT DISTINCT type FROM connections WHERE type='GPON'");
            $props=$mon3->query("select DISTINCT properties.id,properties.ref,properties.address
            from properties left join connections on connections.property_id=properties.id where connections.type='COAX' AND connections.date_end = '0000-00-00' order by properties.ref");
        }
        else
        {
            //$arr_con[]=array(['FWA', 'GPON', 'COAX']);
            array_push($arr_con, 'GPON', 'FWA','COAX');
            //$dist_conns=$mon3->query("SELECT DISTINCT type FROM connections WHERE type !='".$type."'");
            $props=$mon3->query("select DISTINCT properties.id,properties.ref,properties.address
            from properties left join connections on connections.property_id=properties.id where connections.type!='".$type."' AND connections.date_end = '0000-00-00' order by properties.ref");
        }

        while($prop=$props->fetch_assoc())
        {
            $ref = $prop['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$prop['address']));
            $ponb[]=array($ref,$prop['id']);

        }


        $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
        while($cust=$custs->fetch_assoc())
        {
            $customers = $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr'];
            $cust_b[]=array($customers,$cust['id']);
        }

        $ponb = array_merge($ponb, ['arr_con' => $arr_con, 'customers' => $cust_b]);

        echo json_encode($ponb, JSON_UNESCAPED_UNICODE);

    }


    // SUBSCRIBER ON PROP ID RECONNECTION - LEAD 30

    elseif($_GET['subscriber_prop_id'] != "")
    {
        $arr = array();
                $prop_id = $_GET['prop_id'];
                if($prop_id != 0)
                {
                    $wq_subs .= "AND properties.id = ".$prop_id;
                }


                $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
                INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq_subs);
                while($conn_di_each=$conn_di->fetch_assoc())
                {
                    $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                }

                $arr = array_merge($arr, ['subs' => $conn_di_each_b]);
                echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    // LIST SUBSCRIBERS CHANGE OVER & REC
    elseif($_GET['subscriber_list'] != "")
    {
        $merge = array();
        $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
        while($cust=$custs->fetch_assoc())
        {
            $customers = $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr'];
            $cust_b[]=array($customers,$cust['id']);
        }


        // Susbcriber

        $wq_subs='';
        $prop_id = $_GET['prop_id'];
        if($prop_id != 0)
        {
            $wq_subs .= "AND properties.id = ".$prop_id;
        }

        $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
        INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq_subs);
        while($conn_di_each=$conn_di->fetch_assoc())
        {
            $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
        }

        $merge = array_merge($merge, ['subs' => $conn_di_each_b, 'list_cuts' => $cust_b]);

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    // INDIQUE A LISTA DE CONNECTIONS QUE FAZ A CHANGE OVER DO ESTADO 1 DAS LEADS

    elseif($_GET['conn_prop_chg_over'] != "")
    {
        $type = $_GET['type'];
        $props=$mon3->query("select DISTINCT properties.id,properties.ref,properties.address
            from properties left join connections on connections.property_id=properties.id where connections.type='".$type."' AND connections.date_end = '0000-00-00' order by properties.ref");

        while($prop=$props->fetch_assoc())
        {
            $ref = $prop['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$prop['address']));
            $ponb[]=array($ref,$prop['id']);
        }

        $ponb = array_merge($ponb);

        echo json_encode($ponb, JSON_UNESCAPED_UNICODE);

    }

    // CONNECTION TYPE CHANGE OVER NAME CONNECTION
    // LEADS 1 & 5 E 30

    elseif($_GET['prop_id_type_connection_chnage_over'] != "")
    {
        $merge = array();
        $i=0;
        $wq='';
        $wq_type='';
        if($_GET['prop_id'] != 0)
        {
            $wq .= "AND property_id = ".$_GET['prop_id'];
        }
        if($_GET['type'] != "")
        {
            $wq .= " AND connections.type = '".$_GET['type']."'";
            $wq_type .= " AND type = '".$_GET['type']."'";
        }
        else
        {
            $wq .= " AND connections.type = 'GPON'";
            $wq_type .= " AND connections.type = 'GPON'";
        }

        $coaxs=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', 
        connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` 
        INNER JOIN properties ON connections.property_id = properties.id where 1 ".$wq." and connections.date_end='0000-00-00'" );
        while($coax=$coaxs->fetch_assoc())
        {
            //echo $freg['id'].$freg['name'];
            $custb[]=array_map('utf8_encode',$coax);
            $merge = array('conexoes' => $custb);
            $i++;
        }


        if($custb != NULL)
        {
            $q = '';
            for($j=0; $j<$i; $j++)
            {
                if($custb[$j]['connection_id'] != '0')
                {
                    $q .= ' AND id = '.$custb[$j]['connection_id'];
                }

                if($custb[$j]['type'] != '')
                {
                    $q .= ' AND type = "'.$custb[$j]['type'].'"';
                }

                // INDICA OS TIPOS DE CONEXOES QUE SÃO DIFERENTES AO TIPO DE CONNECTION ACTUAL
                $qquery = 'SELECT DISTINCT type from connections where type IN(select DISTINCT type from connections where 1 '.$q.' and connections.date_end="0000-00-00")';
                $fregs=$mon3->query($qquery);

                while($freg=$fregs->fetch_assoc())
                {
                    $fg[]=array_map('utf8_encode',$freg);
                }


                $qd = 'SELECT DISTINCT type from connections where 1 '.$q;
                $qds=$mon3->query($qd);

                while($qd=$qds->fetch_assoc())
                {
                    $qgd[]=array_map('utf8_encode',$qd);
                }

                // Susbcriber

                $wq_subs='';
                $prop_id = $_GET['prop_id'];
                if($prop_id != 0)
                {
                    $wq_subs .= "AND properties.id = ".$prop_id;
                }

                $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
                INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq_subs);
                while($conn_di_each=$conn_di->fetch_assoc())
                {
                    $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                }


            }
            $fg = array_map("unserialize", array_unique(array_map("serialize", $fg)));
            $merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'subs' => $conn_di_each_b]);

        }
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // OBTER AS VLANS ATRAVES DA OLT - GPON
    elseif($_GET['vlansbyolt']!="")
    {
        $olt=mysqli_real_escape_string($mon3, $_GET['vlansbyolt']);

        $vlans=$mon3->query("select vlan,description,total_dynamic_ips from int_vlans where olt_id=\"$olt\" order by vlan ");

        while($vlan=$vlans->fetch_assoc())
        {
            $inuse=$mon3->query("select count(name) from service_attributes left join services on service_attributes.service_id=services.id where service_attributes.name=\"vlan\" and service_attributes.value=\"".$vlan['vlan']."\" and services.date_end=\"0000-00-00\" ")->fetch_assoc();

            $vlanb[]=array($vlan['vlan'],$vlan['description'],$vlan['total_dynamic_ips'],$inuse['count(name)']);
        }


        echo json_encode($vlanb, JSON_UNESCAPED_UNICODE);
    }

    // OBTER AS PONS ATRAVÈS DA OLT - GPON
    elseif($_GET['ponsbyolt']!="")
    {
        $olt=mysqli_real_escape_string($mon3, $_GET['ponsbyolt']);

        $pons=$mon3->query("select card,pon,name from ftth_pons where olt_id=\"$olt\" order by name ");
        echo $mon3->error;
        while($pon=$pons->fetch_assoc())
        {
            $ponb[]=array($pon['card']."-".$pon['pon'],$pon['name']);
        }

        //var_dump($ponb);
        echo json_encode($ponb, JSON_UNESCAPED_UNICODE);
    }

    // VERIFICAR SE OS EQUIPMENTOS ESTAO ASSOCIADOS A CONNECTION - ESTADO 50 - LEADS
    elseif($_GET['equip_connection_assoc'] != "")
    {
        $merge = array();
        $equip_id = $_GET['equip_id'];

        $prop_id = $_GET['prop_id'];

        $msg = "";

        $conn_id = $_GET['conn_id'];

        $eq_check = 0;

        if($equip_id != "")
        {
            // VER OS EQUIPAMENTOS DAS CONEXOES QUE PERTENCEM NA BD
                // GPON
                $onts_num = $mon3->query("SELECT * FROM ftth_ont WHERE fsan = '".$equip_id."'")->num_rows;
                // FWA_CPE
                $fwa_mac = $mon3->query("SELECT * FROM fwa_cpe WHERE mac = '".$equip_id."'")->num_rows;

                if($onts_num > 0 || $fwa_mac > 0)
                {
                    $conn_num_valid = $mon3->query("SELECT * FROM connections WHERE equip_id = '".$equip_id."' ");
                    $conn_fsan = $conn_num_valid->fetch_assoc();
                    $propert_id = $conn_fsan['property_id'];

                    if($propert_id != $prop_id)
                    {
                        if($conn_num_valid->num_rows >= 1)
                        {
                            $msg .= "<font color=red>This Equipment ".$equip_id." is already associated the connection number ".$conn_fsan['id']." please choose a equipment which has not associated to this connection on equipment</font>";
                            $eq_check = 0;
                        }
                        else
                        {
                            $msg .= "<font color=cyan>This ONT / FWA CPE doesn't associate on connections list</font>";
                            $eq_check = 1;
                        }
                    }
                    else if($propert_id == $prop_id)
                    {
                        if($conn_id != $conn_fsan['id'])
                        {
                            if($conn_num_valid->num_rows >= 1)
                            {
                                $msg .= "<font color=red>This Equipment ".$equip_id." is already associated the connection number ".$conn_fsan['id']." please choose a equipment which has not associated to this connection on equipment</font>";
                                $eq_check = 0;
                            }
                            else
                            {
                                $msg .= "<font color=cyan>This ONT / FWA CPE doesn't associate on connections list</font>";
                                $eq_check = 1;
                            }
                        }
                        else
                        {
                            $eq_check = 1;
                        }                 
                    }
                    else
                    {
                        $eq_check = 1;
                    }

                    
                }
                else
                {
                    $msg .= "<font color=blue>The ONT / FWA CPE needs to be created in this connection</font>";
                    $eq_check = 2;
                }
        }
        else
        {
            $msg .= "<font color=red>Missing Equipment</font>";
        }

        

        

        $merge = array_merge($merge, ['msg' => $msg, 'eq_check' => $eq_check]);

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['address_prop_id'] != "")
    {
        $prop_id = $_GET['prop_id'];

        $merge = array();

        $addr_prop_id = $mon3->query("SELECT * FROM properties WHERE id=".$prop_id)->fetch_assoc();

        $addr = $addr_prop_id['address'];
        $subs = $addr_prop_id['owner_id'];
        $freguesia = $addr_prop_id['freguesia'];
        $ref = substr($addr_prop_id['ref'], 0, strlen($addr_prop_id['ref']) - 3);

        $ar_code = $mon3->query("SELECT * FROM area_codes WHERE areacode='".$ref."'")->fetch_assoc();

        $des = $ar_code['description'];

        $feg_con = $mon3->query("SELECT * FROM `freguesias` where id=".$freguesia)->fetch_assoc();

        $concelho = $feg_con['concelho'];

        $fregs=$mon3->query("select * from freguesias where concelho=".$concelho.";");
        
        
        while($frega=$fregs->fetch_assoc())
        {
            $fregb[]=array($frega['freguesia'],$frega['id']);
        }

        if($prop_id != 0)
                {
                    $wq_subs .= "AND properties.id = ".$prop_id;
                }

                $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
                INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq_subs);
                while($conn_di_each=$conn_di->fetch_assoc())
                {
                    $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                }

        $merge = array_merge($merge, ['address' => $addr, 'subs' => $conn_di_each_b, 'freguesia' => $freguesia, 'ref' => $ref, 'desc' => $des, 'freg_list' => $fregb]);

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['prop_lead_address'] != "")
    {
        $lead_id = $_GET['lead_id'];
        $merge = array();
        $lead_add = $mon3->query("SELECT * FROM property_leads WHERE id=".$lead_id)->fetch_assoc();

        $address = $lead_add['address'];


        $concp=$mon3->query("select concelho from freguesias where id=".$lead_add['freguesia'].";")->fetch_assoc();
        $fregs=$mon3->query("select * from freguesias where concelho=".$concp['concelho'].";");

        $prop_id = $lead_add['prop_id'];

        // FREGUESIA

        $fregs=$mon3->query("SELECT freguesias.id as 'freg_id', freguesias.freguesia, concelhos.concelho, concelhos.distrito, concelhos.id as 'conc_id' FROM `freguesias` INNER JOIN concelhos ON concelhos.id = freguesias.concelho WHERE freguesias.id=".$lead_add['freguesia']."");
        while($freg=$fregs->fetch_assoc())
        {
            $freg_arr[]=array_map('utf8_encode',$freg);
        }

        // PROP REF

        $addr_prop_id = $mon3->query("SELECT * FROM properties WHERE id=".$prop_id)->fetch_assoc();
        $ref = substr($addr_prop_id['ref'], 0, strlen($addr_prop_id['ref']) - 3);
        

        // Subscriber
        $subs = $addr_prop_id['owner_id'];

       

        

        $merge = array_merge($merge, ['address' => $address, 'freg_arr' => $freg_arr, 'ref' => $ref, 'subs' => $subs]);

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // IDENTIFICAR SE O NUMERO FISCAL DO CLIENTE E VALIDO OU NAO
    elseif($_GET['num_fiscal_customer'] != "")
    {
        $merge = array();
        $num_fiscal = $_GET['num_fiscal'];

        $num_fiscal_val = $mon3->query("SELECT * FROM customers WHERE fiscal_nr = '".$num_fiscal."'")->num_rows;

        if(strlen($num_fiscal) != 9)
        {
            $msg .= "<font color=red>This fiscal number '".$num_fiscal."' must be 9 characters</font><br>";
            $eq_check = 0;
        }
        else if(strlen($num_fiscal) == 9)
        {
            if($num_fiscal_val > 0)
            {
                if (preg_match('/9{8}[0-9]{1}/', $num_fiscal)) 
                {
                    $msg .= "<font color=orange>This fiscal number '".$fiscal_nr."' may be duplicated on custumer (owner)</font><br>";
                    $eq_check = 1;

                }
                else
                {
                    $msg .= "<font color=red>This fiscal number ".$num_fiscal." exists on list of custmers</font>";
                    $eq_check = 0;
                }
                
            }
            else
            {
                $msg .= "<font color=cyan>This fiscal number ".$num_fiscal." not associate on list of customers</font>";
                $eq_check = 1;
            }
        }
                


                $merge = array_merge($merge, ['msg' => $msg, 'eq_check' => $eq_check, 'length' => strlen($num_fiscal)]);

                echo json_encode($merge, JSON_UNESCAPED_UNICODE);




    }

    // OBTER A PROPRIEDADE DO CLIENTE SELECCIONADO - ESTADO 30 - LEADS - functions.js - owner_prop(owner_id)
    elseif($_GET['owner_id_prop'] != '')
    {
        $i = 0;
        $merge = array();
        $wq='';
        $owner_id = $_GET['owner_id'];
        if($owner_id != 0)
        {
            $wq .= "AND owner_id = ".$owner_id;
        }

        $conn_di=$mon3->query("select properties.id,properties.ref,properties.address, connections.type
        from properties left join connections on connections.property_id=properties.id WHERE 1 ".$wq." order by properties.ref");
        while($conn_di_each=$conn_di->fetch_assoc())
        {
            $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
            $merge = array('prop_cust' => $conn_di_each_b);
        }
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    // INSERIR NOVO CLIENTE - RECONNECTION - ESTADO 30
    elseif($_POST['new_customer_post'] != "")
    {
        $salut=mysqli_real_escape_string($mon3, $_POST['salut']);
        $name=mysqli_real_escape_string($mon3, $_POST['name']);
        $address=mysqli_real_escape_string($mon3, $_POST['address']);
        $email=mysqli_real_escape_string($mon3, $_POST['email']);
        $telef=mysqli_real_escape_string($mon3, $_POST['telef']);
        $fiscal_nr=mysqli_real_escape_string($mon3, $_POST['fiscal_nr']);
        $lang=mysqli_real_escape_string($mon3, $_POST['lang']);
        $is_commercial=mysqli_real_escape_string($mon3, $_POST['is_commercial']);
        $is_management=mysqli_real_escape_string($mon3, $_POST['is_management']);
        $is_agent=mysqli_real_escape_string($mon3, $_POST['is_agent']);
        $notes=mysqli_real_escape_string($mon3, $_POST['notes']);

        $localuser=mysqli_real_escape_string($mon3, $_POST['localuser']);

        $merge = array();

        $msg = '';
        $succ = '';
        $error = '';


        if($notes!="")
        {
            $notes.=date("Y-m-d H:i:s").": ".$localuser.": ".mysqli_real_escape_string($mon3, $_POST['notes'])."<br>";	
        }

        $num_fiscal_val = $mon3->query("SELECT * FROM customers WHERE fiscal_nr = '".$fiscal_nr."'")->num_rows;

        if($name == "")
        {
            $msg .= "<font color=red>Name: * required</font><br>";
        }

        if($address == "")
        {
            $msg .= "<font color=red>Billing Address: * required</font><br>";
        }

        if($email == "")
        {
            $msg .= "<font color=red>Email: * required</font><br>";
        }

        if($telef == "")
        {
            $msg .= "<font color=red>Phone: * required</font><br>";
        }

        if($fiscal_nr == "")
        {
            $msg .= "<font color=red>Fiscal Number: * required</font><br>";
        }

        //echo $num_fiscal_val;



        if($fiscal_nr != "")
        {
            if(strlen($fiscal_nr) != 9)
            {
                $msg .= "<font color=red>This fiscal number '".$fiscal_nr."' must be 9 characters</font><br>";
            }
            else
            {
                if($num_fiscal_val > 0)
                {
                    if($fiscal_nr != "999999990")                    
                    {
                        $msg .= "<font color=red>This fiscal number '".$fiscal_nr."' exists on list of custmers</font><br>";
                    }
                     
                }
            }
            
        }
        

        if($email != "")
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msg .= "<font color=red>Invalid email format</font><br>";
            }
        }


        if($msg == "")
        {
            if($notes!="")
            {
                $notes.=date("Y-m-d H:i:s").": ".$localuser['username'].": ".mysqli_real_escape_string($mon3, $_POST['notes'])."<br>";
            }

            //monlog("modified customer $cust_id -> salut=\"$salut\",	name=\"$name\",	address=\"$address\",telef=\"$telef\",email=\"$email\",	is_commercial=\"$is_commercial\",is_management=\"$is_management\",is_agent=\"$is_agent\",fiscal_nr=\"$fiscal_nr\",");

            $data_cust = date("Y-m-d");
            
            $gg=$mon3->query("insert into customers 
	        (salut,name,address,telef,email,is_commercial,is_management,is_agent,fiscal_nr,notes,date_created,language) values 
	        (\"$salut\",\"$name\",\"$address\",\"$telef\",\"$email\",\"$is_commercial\",\"$is_management\",\"$is_agent\",
	        \"$fiscal_nr\",\"$notes\", \"$data_cust\", \"$lang\" );");
            if($gg)
            {
                $succ .= "<font color=green>Customer was inserted successfully</font><br>";
            }
            else
            {
                $error .= "<font color=red>Error on inserting customer</font><br>";
            }
	         $cust_id=$mon3->insert_id;



             $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
	         while($cust=$custs->fetch_assoc())
	         {
                $cust_a = $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr'];
                $cust_b[]=array($cust_a,$cust['id']);
             }


             // PROPERTIES CUSTOMERS

             $prop_lead_id = $mon3->query("SELECT * FROM property_leads WHERE id=".$_POST['lead_id'])->fetch_assoc();

             $prop = $prop_lead_id['prop_id'];

             $owner_select = $mon3->query("SELECT * FROM properties WHERE id=".$prop)->fetch_assoc();

             $own_sel = $owner_select['owner_id'];	
        }  

        $merge = array_merge($merge, ['msg' => $msg, 'succ' => $succ, 'error' => $error, 'owner' => $cust_b, 'owner_select' => $cust_id]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }


    // CHANGE OVER - ESTADO 30 - CONNECTION TYPE - connections_list(conn_id)
    elseif($_GET['prop_id_conn_id_type'] != '')
    {
        $i = 0;
        $merge = array();
        $wq='';
        $conn_id = $_GET['conn_id'];
        if($conn_id != 0)
        {
            $wq .= "AND connections.id = ".$conn_id;
        }

        $conn_chg_over=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', 
        connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` 
        INNER JOIN properties ON connections.property_id = properties.id where 1 ".$wq." AND connections.date_end=\"0000-00-00\" ")->fetch_assoc();

        if($conn_chg_over['type'] != '')
        {
            $q .= ' AND type = "'.$conn_chg_over['type'].'"';
        }
            $qd = 'SELECT DISTINCT type from connections where 1 '.$q;
            $qds=$mon3->query($qd);

            while($qd=$qds->fetch_assoc())
            {
                $qgd[]=array_map('utf8_encode',$qd);
            }
            $merge = array_merge($merge, ['t_conn' => $qgd]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    // FUNCTION.JS - con_prop_type(prop_id)
    // OBTER A CONEXAO DO TIPO DA PROPRIEDADE SELECCIONADA
    elseif($_GET['prop_id_type_connection'] != "")
    {
        $merge = array();
        $i=0;
        $wq='';
        $wq_type='';
        if($_GET['prop_id'] != 0)
        {
            $wq .= "AND property_id = ".$_GET['prop_id'];
        }
        if($_GET['type'] != "")
        {
            $wq .= " AND connections.type != '".$_GET['type']."'";
            $wq_type .= " AND type = '".$_GET['type']."'";
        }
        else
        {
            $wq .= " AND connections.type != 'GPON'";
            $wq_type .= " AND connections.type = 'GPON'";
        }

        $coaxs=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', 
        connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` 
        INNER JOIN properties ON connections.property_id = properties.id where 1 ".$wq." and connections.date_end='0000-00-00'" );
        while($coax=$coaxs->fetch_assoc())
        {
            //echo $freg['id'].$freg['name'];
            $custb[]=array_map('utf8_encode',$coax);
            $merge = array('conexoes' => $custb);
            $i++;
        }


        if($custb != NULL)
        {
            $q = '';
            for($j=0; $j<$i; $j++)
            {
                if($custb[$j]['connection_id'] != '1')
                {
                    $q .= ' AND id = '.$custb[$j]['connection_id'];
                }

                if($custb[$j]['type'] != '')
                {
                    $q .= ' AND type = "'.$custb[$j]['type'].'"';
                }

                // INDICA OS TIPOS DE CONEXOES QUE SÃO DIFERENTES AO TIPO DE CONNECTION ACTUAL
                $qquery = 'SELECT DISTINCT type from connections where type NOT IN(select DISTINCT type from connections where 1 '.$q.' and connections.date_end="0000-00-00")';
                $fregs=$mon3->query($qquery);

                while($freg=$fregs->fetch_assoc())
                {
                    $fg[]=array_map('utf8_encode',$freg);
                }


                $qd = 'SELECT DISTINCT type from connections where 1 '.$q;
                $qds=$mon3->query($qd);

                while($qd=$qds->fetch_assoc())
                {
                    $qgd[]=array_map('utf8_encode',$qd);
                }

                // Susbcriber

                $wq_subs='';
                $prop_id = $_GET['prop_id'];
                if($prop_id != 0)
                {
                    $wq_subs .= "AND properties.id = ".$prop_id;
                }

                $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
                INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq_subs);
                while($conn_di_each=$conn_di->fetch_assoc())
                {
                    $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                }


            }
            $fg = array_map("unserialize", array_unique(array_map("serialize", $fg)));
            $merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'subs' => $conn_di_each_b]);

        }
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // INCIALIZAR O EQUIPAMENTO QUE FOI ADICIONADO NA CONEXAO DESTA PROPRIEDADE SELECCIONADA - ESTADO 50 - LEADS
    elseif($_GET['initial_equip_con_prop'] != "")
    {
        //'type': type, 'prop_id': prop_id, 'con_id': con_id
        $type = $_GET['type'];
        $prop_id = $_GET['prop_id'];

        $l_id = $_GET['lead_id'];

        $connners = $_GET['con_id'];

        $lead_id = $mon3->query("SELECT * FROM property_leads where id = ".$l_id."")->fetch_assoc();

        $conexao=$mon3->query("SELECT * FROM connections where id = ".$connners."")->fetch_assoc();
        $merge = array();

        // Equipamento
        $equip_id = $conexao['equip_id'];

        // Conexao
        $conn_id = $conexao['id'];


        if($lead_id['is_reconnection'] == 1 && $lead_id['is_changeover'] == 0)
        {
            $num_serv_des = $mon3->query("SELECT * FROM services where connection_id = ".$conn_id." AND date_end = '0000-00-00'")->num_rows;

            if($num_serv_des > 0)
            {
                $des_sr = 0;
            }
            else
            {
                $des_sr = 1;
            }
        }
        else if($lead_id['is_reconnection'] == 0 && $lead_id['is_changeover'] == 1)
        {
            $des_sr = 1;
        }
        else if($lead_id['is_reconnection'] == 0 && $lead_id['is_changeover'] == 0)
        {
            $des_sr = 1;
        } 

        if ($type == "GPON")
        {
            $modelo = $mon3->query("SELECT * FROM ftth_ont where fsan = '".$equip_id."'")->fetch_assoc();
            // CPE Model
            $cpe_model = $modelo['model'];
            

            $me_model = $modelo['meprof'];

            $ont_id = $modelo['ont_id'];


            // OLT
            $olt = $modelo['olt_id'];

            if($olt == null || $olt == "" || $olt == 0)
            {

                $pon_select[]=array_map('utf8_encode',$pon);
            }

            if($olt != null || $olt != "")
            {
                if($olt != 0)
                {
                    $pons = $mon3->query("SELECT * FROM ftth_pons where olt_id = ".$olt." order by name ");
                    while($pon=$pons->fetch_assoc())
                    {
                        $pon_select[]=array_map('utf8_encode',$pon);
                    }
                }
            }


            $merge = array_merge($merge, ['equip_id' => $equip_id, 'cpe_model' => $cpe_model, 'me_model' => $me_model, 'ont_id' => $ont_id, 'olt' => $olt, 'pon' => $pon_select, 'serv_des' => $des_sr]);


        }

        

        

        

        

        

        else if($type == "FWA")
        {
            // Equipamento
            $equip_id = $conexao['equip_id'];

            // Conexao
            $conn_id = $conexao['id'];




            // CPE Model

            // FWA CPE
            $modelo = $mon3->query("SELECT * FROM fwa_cpe WHERE mac = '".$equip_id."'")->fetch_assoc();

            if($modelo != null || $modelo != "")
            {
                // FWA antenna
                $antennas = $mon3->query("SELECT * FROM fwa_antennas where id = ".$modelo['antenna']." order by name ")->fetch_assoc();
            }

            






            $merge = array_merge($merge, ['equip_id' => $equip_id, 'model_fwa' => $modelo['model'], 'antenna' => $antennas['id'], 'antenna_des' => $antennas['name'], 'serv_des' => $des_sr]);





        }



        

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // OBTER A CONNECTION TYPE DO TIPO DE SERVICO INT
    elseif($_GET['connection_type_prop_internet'] != "")
    {
        $type = $_GET['type'];

        $merge = array();

        //select id,name from int_services where con_type=\"".$prop['con_type']."\" order by prof_down

        $internets = $mon3->query("select id,name from int_services where con_type=\"".$type."\" order by prof_down");

        while($internet=$internets->fetch_assoc())
            {
                $internet_select[]=array_map('utf8_encode',$internet);
            }

            $merge = array_merge($merge, ['internet_select' => $internet_select]);

            echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    // INDIQUE AS CARACTERISTICAS DO EQUIPMENTO A CONNECTION SELECCIONADA - CHANGE OVER = 1 - LEAD ESTADO 30 
    elseif($_GET['conexao_id_prop_equ'] != "")
    {
        $m = array();
        $con = $_GET['conn_id'];
        $tipo = $_GET['tipo'];



        $wq='';
        if($con != '')
        {
            $wq .= " AND id = ".$con;
        }

        //echo "SELECT * from connections where 1 ".$wq;
        $conn=$mon3->query("SELECT * from connections where 1".$wq);



        // TIPO de conexao
        if ($tipo != '') {
            $wq .= " AND type = '" . $tipo . "'";

        }


        while($con=$conn->fetch_assoc())
        {
            //echo $freg['id'].$freg['freguesia'];
            $co[]=array_map('utf8_encode',$con);
            $equip = $con['equip_id'];
        }
        $m = array_merge($m, ['equip' => $equip]);

        $conn_diff=$mon3->query("SELECT DISTINCT type from connections where type NOT IN( SELECT DISTINCT type from connections where 1".$wq.")");
        while($conn_diff_te=$conn_diff->fetch_assoc()) {
            //echo $freg['id'].$freg['freguesia'];
            $conn_diff_te_array[] = array_map('utf8_encode', $conn_diff_te);
            $m = array_merge($m, ['conn_diff' => $conn_diff_te_array]);


        }
        echo json_encode($m, JSON_UNESCAPED_UNICODE);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // END LEADS.PHP - SCRIPTS

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------














    // PESQUISA DE CONEXOES COM SERVIÇOS DESATIVADOS - index.php?servs=1&type=NOS
    elseif($_GET["search_validate_serv_disabled"] != "")
    {
        $wq = "";
        $merge = array();

        $prop_ref = $_GET['prop_ref'];

        $prop_address = $_GET['prop_address'];

        $con_type = $_GET['con_type'];

        if ($prop_ref != "") {
            $wq .= " AND properties.ref LIKE '" . $prop_ref . "%'";
        }

        if ($prop_address != "") {
            $wq .= " AND properties.address LIKE '" . $prop_address . "%'";
        }

        if ($con_type != "0") {
            $wq .= " AND connections.type = '" . $con_type . "'";
        }

        if ($con_type == "0") {
            $wq .= "";
        }


        $lists_conn_des_serv=$mon3->query("select DISTINCT properties.id as 'prop_id',properties.ref as 'ref_prop',
        properties.address as 'prop_addr', connections.id as 'conn_id',
        connections.type as 'conn_type', connections.equip_id as 'conn_equip_id', LEFT(properties.ref , 3) as 'code_area'
        from properties inner join connections on connections.property_id=properties.id
        inner join services on services.connection_id=connections.id where 1 ".$wq." and connections.date_end = '0000-00-00'
        AND services.date_end != '0000-00-00' and connections.property_id NOT IN (SELECT connections.property_id from connections inner join services on services.connection_id=connections.id where 1 ".$wq." and connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00')
        AND connections.id NOT IN(SELECT id FROM connections WHERE connections.date_end != '0000-00-00')
        order by properties.ref ASC limit 0,50");


        $num = $mon3->query("select DISTINCT properties.id as 'prop_id',properties.ref as 'ref_prop',
        properties.address as 'prop_addr', connections.id as 'conn_id',
        connections.type as 'conn_type', connections.equip_id as 'conn_equip_id', LEFT(properties.ref , 3) as 'code_area'
        from properties inner join connections on connections.property_id=properties.id
        inner join services on services.connection_id=connections.id where 1 ".$wq." and connections.date_end = '0000-00-00'
        AND services.date_end != '0000-00-00' and connections.property_id NOT IN (SELECT connections.property_id from connections inner join services on services.connection_id=connections.id where 1 ".$wq." and connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00')
        AND connections.id NOT IN(SELECT id FROM connections WHERE connections.date_end != '0000-00-00')
        order by properties.ref ASC")->num_rows;

        $lastp=ceil($num/50);
	    $curpage=($offset/50)+1;

        while($list_conn_des_serv=$lists_conn_des_serv->fetch_assoc())
        {
            //echo $list_conn_des_serv['prop_id'];
            $list_conn_des_serv_b[]=array_map('utf8_encode',$list_conn_des_serv);
            $merge = array('list_conn_des_serv' => $list_conn_des_serv_b);
        }

        

        $merge = array_merge($merge, ['num_rows' => $num, 'lastp' => $lastp, 'curpage' => $curpage]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // PESQUISAR CONEXOES QUE SO TEM SERVICOS DO TIPO INT - index.php?servs=1&type=INTonly
    elseif($_GET["search_validate_int_serv"] != "")
    {
        $wq = "";
        $merge = array();

        $prop_ref = $_GET['prop_ref'];

        $prop_address = $_GET['prop_address'];

        $con_type = $_GET['con_type'];

        if ($prop_ref != "") {
            $wq .= " AND properties.ref LIKE '" . $prop_ref . "%'";
        }

        if ($prop_address != "") {
            $wq .= " AND properties.address LIKE '" . $prop_address . "%'";
        }

        if ($con_type != "") {
            $wq .= " AND connections.type = '" . $con_type . "'";
        }

        if ($con_type == "") {
            $wq .= "";
        }
		
		
		
		$num=$mon3->query("SELECT properties.id as 'prop_id', properties.ref as 'ref', 
        properties.address as 'address', connections.type as 'type', 
        services.date_start as 'serv_date_start' 
        FROM properties 
        INNER JOIN connections ON connections.property_id=properties.id 
        INNER JOIN services ON services.connection_id= connections.id 
        WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type='INT' 
        ".$wq." 
        AND properties.id NOT IN (SELECT properties.id FROM properties 
        INNER JOIN connections ON connections.property_id=properties.id 
        INNER JOIN services ON services.connection_id= connections.id 
        WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type!='INT' ".$wq.")")->num_rows;


        $res=$mon3->query("SELECT properties.id as 'prop_id', properties.ref as 'ref', 
        properties.address as 'address', connections.type as 'type', 
        services.date_start as 'serv_date_start' 
        FROM properties 
        INNER JOIN connections ON connections.property_id=properties.id 
        INNER JOIN services ON services.connection_id= connections.id 
        WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type='INT' 
        ".$wq."
        AND properties.id NOT IN (SELECT properties.id FROM properties 
        INNER JOIN connections ON connections.property_id=properties.id 
        INNER JOIN services ON services.connection_id= connections.id 
        WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type!='INT' ".$wq.") order by properties.ref limit 0,50");

        $lastp=ceil($num/50);
	    $curpage=($offset/50)+1;

        while($list_conn_des_serv=$res->fetch_assoc())
        {
            $list_conn_des_serv_b[]=array_map('utf8_encode',$list_conn_des_serv);
            $merge = array('list_conn_des_serv' => $list_conn_des_serv_b);
        }

        

        $merge = array_merge($merge, ['num_rows' => $num, 'lastp' => $lastp, 'curpage' => $curpage]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    // PESQUISAR CONEXOES QUE TÊM SERVICOS DO TIPO INT - index.php?servs=1&type=INT
    elseif($_GET["search_validate_int_serv_all"] != "")
    {
        $wq = "";
        $merge = array();

        $prop_ref = $_GET['prop_ref'];

        $prop_address = $_GET['prop_address'];

        $con_type = $_GET['con_type'];

        if ($prop_ref != "") {
            $wq .= " AND properties.ref LIKE '" . $prop_ref . "%'";
        }

        if ($prop_address != "") {
            $wq .= " AND properties.address LIKE '" . $prop_address . "%'";
        }

        if ($con_type != "") {
            $wq .= " AND connections.type = '" . $con_type . "'";
        }

        if ($con_type == "") {
            $wq .= "";
        }
		
		
		
		$num=$mon3->query("SELECT DISTINCT properties.ref as 'ref', properties.address as 'address', connections.type as 'type', service_attributes.value as 'value'
        FROM properties
        INNER JOIN connections ON connections.property_id=properties.id
        INNER JOIN services ON services.connection_id=connections.id
        INNER JOIN service_attributes ON service_attributes.service_id=services.id
        WHERE connections.date_end = '0000-00-00' AND service_attributes.name=\"speed\" AND services.type=\"INT\" ".$wq."")->num_rows;


        $res=$mon3->query("SELECT DISTINCT properties.id as 'prop_id', properties.ref as 'ref', properties.address as 'address', connections.type as 'type', service_attributes.value as 'value'
        FROM properties
        INNER JOIN connections ON connections.property_id=properties.id
        INNER JOIN services ON services.connection_id=connections.id
        INNER JOIN service_attributes ON service_attributes.service_id=services.id
        WHERE connections.date_end = '0000-00-00' AND service_attributes.name=\"speed\" AND services.type=\"INT\" ".$wq." order by properties.ref limit 0,50");

        // Numero de linhas (dados)

        $lastp=ceil($num/50);
	    $curpage=($offset/50)+1;

        while($list_conn_des_serv=$res->fetch_assoc())
        {
            $list_conn_des_serv_b[]=array_map('utf8_encode',$list_conn_des_serv);
            $merge = array('list_conn_des_serv' => $list_conn_des_serv_b);
        }

        

        $merge = array_merge($merge, ['num_rows' => $num, 'lastp' => $lastp, 'curpage' => $curpage]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    // PESQUISAR CONEXOES QUE TÊM SERVICOS DO TIPO TV - index.php?servs=1&type=TV
    elseif($_GET['search_validate_tv_serv_all'] != "")
    {
        $wq = "";
        $merge = array();

        $prop_ref = $_GET['prop_ref'];

        $prop_address = $_GET['prop_address'];

        $con_type = $_GET['con_type'];

        if ($prop_ref != "") {
            $wq .= " AND properties.ref LIKE '" . $prop_ref . "%'";
        }

        if ($prop_address != "") {
            $wq .= " AND properties.address LIKE '" . $prop_address . "%'";
        }

        if ($con_type != "") {
            $wq .= " AND connections.type = '" . $con_type . "'";
        }

        if ($con_type == "") {
            $wq .= "";
        }

        $num=$mon3->query("SELECT DISTINCT properties.ref as 'ref', properties.id as 'prop_id', properties.address as 'address', connections.type as 'type'
		FROM properties
		INNER JOIN connections ON connections.property_id=properties.id
		INNER JOIN services ON services.connection_id=connections.id
		WHERE connections.date_end = '0000-00-00' AND services.type=\"TV\" AND connections.date_end='0000-00-00' ".$wq." ")->num_rows;


        $res=$mon3->query("SELECT DISTINCT properties.ref as 'ref', properties.id as 'prop_id', properties.address as 'address', connections.type as 'type'
		FROM properties
		INNER JOIN connections ON connections.property_id=properties.id
		INNER JOIN services ON services.connection_id=connections.id
		WHERE connections.date_end = '0000-00-00' AND services.type=\"TV\" AND connections.date_end='0000-00-00' ".$wq." order by properties.ref limit 0,50");

        $lastp=ceil($num/50);
        $curpage=($offset/50)+1;

        while($list_conn_des_serv=$res->fetch_assoc())
        {
            //echo $list_conn_des_serv['prop_id'];
            $list_conn_des_serv_b[]=array_map('utf8_encode',$list_conn_des_serv);
            $merge = array('list_conn_des_serv' => $list_conn_des_serv_b);
        }

        

        $merge = array_merge($merge, ['num_rows' => $num, 'lastp' => $lastp, 'curpage' => $curpage]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);




    }

    // PESQUISAR CONEXOES QUE SO TEM SERVICOS DO TIPO TV - index.php?servs=1&type=TVonly
    elseif($_GET['search_validate_tv_serv'] != "")
    {
        $wq = "";
        $merge = array();

        $prop_ref = $_GET['prop_ref'];

        $prop_address = $_GET['prop_address'];

        $con_type = $_GET['con_type'];

        if ($prop_ref != "") {
            $wq .= " AND properties.ref LIKE '" . $prop_ref . "%'";
        }

        if ($prop_address != "") {
            $wq .= " AND properties.address LIKE '" . $prop_address . "%'";
        }

        if ($con_type != "") {
            $wq .= " AND connections.type = '" . $con_type . "'";
        }

        if ($con_type == "") {
            $wq .= "";
        }
        


        $num=$mon3->query("SELECT properties.id as 'prop_id', properties.ref as 'ref', 
		properties.address as 'address', connections.type as 'type', 
		services.id as 'serv_id'
		FROM properties 
		INNER JOIN connections ON connections.property_id=properties.id 
		INNER JOIN services ON services.connection_id= connections.id 
		WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type='TV' 
		".$wq." 
		AND properties.id NOT IN (SELECT properties.id FROM properties 
		INNER JOIN connections ON connections.property_id=properties.id 
		INNER JOIN services ON services.connection_id= connections.id 
		WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type!='TV' ".$wq.")")->num_rows;


        $res=$mon3->query("SELECT properties.id as 'prop_id', properties.ref as 'ref', 
		properties.address as 'address', connections.type as 'type', 
		services.id as 'serv_id'
		FROM properties 
		INNER JOIN connections ON connections.property_id=properties.id 
		INNER JOIN services ON services.connection_id= connections.id 
		WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type='TV' 
		".$wq."
		AND properties.id NOT IN (SELECT properties.id FROM properties 
		INNER JOIN connections ON connections.property_id=properties.id 
		INNER JOIN services ON services.connection_id= connections.id 
		WHERE connections.date_end = '0000-00-00' AND services.date_end = '0000-00-00' AND services.type!='TV' ".$wq.") order by properties.ref limit 0,50");

        $lastp=ceil($num/50);
        $curpage=($offset/50)+1;

        while($list_conn_des_serv=$res->fetch_assoc())
        {
            //echo $list_conn_des_serv['prop_id'];
            $list_conn_des_serv_b[]=array_map('utf8_encode',$list_conn_des_serv);
            $merge = array('list_conn_des_serv' => $list_conn_des_serv_b);
        }

        

        $merge = array_merge($merge, ['num_rows' => $num, 'lastp' => $lastp, 'curpage' => $curpage]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }


    // VISUALIZAR AS CONEXOES COM SERVIÇOS DESATIVADOS EM CADA TIPO DE SERVIÇO
    elseif($_GET['disabled_serv_conn_each_type_recent'] != "")
    {
        $conn_id = $_GET['conn_id'];

        $type = $_GET['type'];

        $text = '';

        $merge = array();

        $serv_link= "";

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

        //$servs = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.date_start = (SELECT MAX(s2.date_start) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) and s1.date_end = (SELECT MAX(s2.date_end) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.date_start DESC");
        $servs = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.id DESC");
        while($serv=$servs->fetch_assoc())
        {
            $serv_b[]=array_map('utf8_encode',$serv);
            //$merge = array('services_des' => $serv_b);

            $dis_susp_conn = $eq_conn['dis_services'];
            $serv_susp = $serv['is_susp_serv'];

            $serv_id = $serv['id'];
            $tr_dis_services = "";
            $tr_dis_services_end = "";

            if(($dis_susp_conn == 1 && $serv_susp == 1))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_serv_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }

            else if(($dis_susp_conn == 2 && $serv_susp == 2))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_conn_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }


            $text.= "<tr><td style=width:100%><br>
	        <tr><td>type: ".$serv['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$serv['id']." >".$serv['id']."</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$serv['date_start']." <br>";
		    $text.= "status: <font color=red><b> Disabled</b></font>"; 

            //echo $serv['id']."<br>";

            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$serv['id']);

            $text .= "<table>";
            while($att=$atts->fetch_assoc())
	        {
                //echo $att['name'];
                
                $att_b[]=array_map('utf8_encode',$att);
                //$merge = array_merge($merge, ['attr_nome' => $att_b]);
                

                if($att['name']=="account")
		        {
                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="unifi_site")
		        {
                    $unifi_site[]=array($att['value'], explode("/",$att['value'])[5]);
			        //$unifi_site_b[]=array_map('utf8_encode',$unifi_site);
                    //$merge = array_merge($merge, ['unifi_site' => $unifi_site]);
                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
		        }
		
		       elseif($att['name']=="speed")
		       {
			       $speedname=$mon3->query("select * from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                   $speedname_b=array_map('utf8_encode',$speedname);
                   //$merge = array_merge($merge, ['speedname' => $speedname_b]);


                   $speed[]=array($speedname['name'], ($equip['total_traf_tx_month']+$equip['total_traf_rx_month']));
			       //$speed_b[]=array_map('utf8_encode',$speed);
                   //$merge = array_merge($merge, ['speed' => $speed]);
			       $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
		       }
	
		       else
		       {
                   $opt[]=array($att['name'], $att['value']);
				   //$opt_b[]=array_map('utf8_encode',$opt);
                   //$merge = array_merge($merge, ['opt' => $opt]);

                   $text .=  " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
		       }

            }

            if($serv['type']=="INT")
	        {
	           $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn_id."\" order by datetime desc limit 0,1    ")->fetch_assoc();
	           $fmac=substr($ip['mac'],0,8);
	           $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();

               $int[]=array($ip['ip'], $ip['datetime'], $ip['mac'], $brand['brand']);
               //$int_b[]=array_map('utf8_encode',$int);
               //$merge = array_merge($merge, ['int' => $int_b]);

               $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" ."<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
	
	
	       }
           $text .= "</table>";

        }

        $merge = array('text' => $text);

        $merge = array_merge($merge, ['dis_serv_id' => $dis_serv_id_a, 'dis_conn_id' => $dis_conn_id_a]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // MOSTRAR OS SERVIÇOS DESATIVADOS
    elseif($_GET['disabled_serv_conn'] != "")
    {
        $conn_id = $_GET['conn_id'];

        $type = $_GET['type'];

        $text = '';

        $merge = array();

        $serv_link= "";

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

        $servs = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn_id." AND date_end != '0000-00-00' order by id");

        while($serv=$servs->fetch_assoc())
        {
            $serv_b[]=array_map('utf8_encode',$serv);
            //$merge = array('services_des' => $serv_b);

            $dis_susp_conn = $eq_conn['dis_services'];
            $serv_susp = $serv['is_susp_serv'];

            $serv_id = $serv['id'];
            $tr_dis_services = "";
            $tr_dis_services_end = "";

            
            if(($dis_susp_conn == 1 && $serv_susp == 1))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_serv_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }

            else if(($dis_susp_conn == 2 && $serv_susp == 2))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_conn_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }


            $text.= "<tr><td style=width:100%><br>
	        <tr><td>type: ".$serv['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$serv['id']." ".$serv_link.">".$serv['id']."</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$serv['date_start']." <br>";
		    $text.= "status: <font color=red><b> Disabled</b></font>"; 

            //echo $serv['id']."<br>";

            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$serv['id']);

            $text .= "<table>";
            while($att=$atts->fetch_assoc())
	        {
                //echo $att['name'];
                
                $att_b[]=array_map('utf8_encode',$att);
                //$merge = array_merge($merge, ['attr_nome' => $att_b]);
                

                if($att['name']=="account")
		        {
                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="unifi_site")
		        {
                    $unifi_site[]=array($att['value'], explode("/",$att['value'])[5]);
			        //$unifi_site_b[]=array_map('utf8_encode',$unifi_site);
                    //$merge = array_merge($merge, ['unifi_site' => $unifi_site]);
                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
		        }
		
		       elseif($att['name']=="speed")
		       {
			       $speedname=$mon3->query("select * from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                   $speedname_b=array_map('utf8_encode',$speedname);
                   //$merge = array_merge($merge, ['speedname' => $speedname_b]);

                   $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				   $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];


                   $speed[]=array($speedname['name'], ($equip['total_traf_tx_month']+$equip['total_traf_rx_month']));
			       //$speed_b[]=array_map('utf8_encode',$speed);
                   //$merge = array_merge($merge, ['speed' => $speed]);
			       $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
		       }
	
		       else
		       {
                   $opt[]=array($att['name'], $att['value']);
				   //$opt_b[]=array_map('utf8_encode',$opt);
                   //$merge = array_merge($merge, ['opt' => $opt]);

                   $text .=  " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
		       }

            }

            if($serv['type']=="INT")
	        {
	           $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn_id."\" order by datetime desc limit 0,1    ")->fetch_assoc();
	           $fmac=substr($ip['mac'],0,8);
	           $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();

               $int[]=array($ip['ip'], $ip['datetime'], $ip['mac'], $brand['brand']);
               //$int_b[]=array_map('utf8_encode',$int);
               //$merge = array_merge($merge, ['int' => $int_b]);

               $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" ."<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
	
	
	       }

           $text .= "</table>";

        }

        $merge = array('text' => $text);

        $merge = array_merge($merge, ['dis_serv_id' => $dis_serv_id_a, 'dis_conn_id' => $dis_conn_id_a]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    // MOSTRAR TODOS OS SERVIÇOS DE CADA CONEXAO
    elseif($_GET['show_all_services'] != "")
    {
        $conn_id = $_GET['conn_id'];

        $type = $_GET['type'];

        $text = '';

        $merge = array();

        $serv_link= "";

        $eq_conn = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id."")->fetch_assoc();

        $int_b = array();
        $int = array();

        

        
        
        if($type == "GPON"){
            $equip=$mon3->query("select * from ftth_ont where fsan=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "FWA"){
            $equip=$mon3->query("select * from fwa_cpe where mac=\"".$eq_conn['equip_id']."\"")->fetch_assoc();
        }
        else if($type == "COAX"){
            $equip=$mon3->query("select * from coax_modem where UPPER(mac)= UPPER(\"".$eq_conn['equip_id']."\")")->fetch_assoc();
        }

        $servs = $mon3->query("select id,connection_id,type,date_start,date_end,contract_id,subscriber,is_susp_serv from services where connection_id=\"".$conn_id."\" order by id DESC;");

        
        while($serv=$servs->fetch_assoc())
        {
            $serv_b[]=array_map('utf8_encode',$serv);
            //$merge = array('services_des' => $serv_b);
			
            $serv_susp = $serv['is_susp_serv'];

            $dis_susp_conn = $eq_conn['dis_services'];

            

            $serv_id = $serv['id'];
            $tr_dis_services = "";
            $tr_dis_services_end = "";

            if(($dis_susp_conn == 1 && $serv_susp == 1))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_serv_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }

            else if(($dis_susp_conn == 2 && $serv_susp == 2))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_conn_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }


            $text.= "<tr><td style=width:100%><br>
	        <tr><td>type: ".$serv['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$serv['id']." >".$serv['id']."</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$serv['date_start']." <br>";
            if($serv['date_end']!="0000-00-00")
	        {
		        $text .= "status: <font color=red><b> Disabled</b></font>"; 
	        }
            //echo $serv['id']."<br>";

            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$serv['id']);

            $text .= "<table>";
            while($att=$atts->fetch_assoc())
	        {
                //echo $att['name'];
                
                $att_b[]=array_map('utf8_encode',$att);
                //$merge = array_merge($merge, ['attr_nome' => $att_b]);
                

                if($att['name']=="account")
		        {
                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="unifi_site")
		        {
                    $unifi_site[]=array($att['value'], explode("/",$att['value'])[5]);
			        //$unifi_site_b[]=array_map('utf8_encode',$unifi_site);
                    //$merge = array_merge($merge, ['unifi_site' => $unifi_site]);
                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
		        }
		
		       elseif($att['name']=="speed")
		       {
			       $speedname=$mon3->query("select * from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                   $speedname_b=array_map('utf8_encode',$speedname);
                   //$merge = array_merge($merge, ['speedname' => $speedname_b]);

                   $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				   $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];


                   $speed[]=array($speedname['name'], ($equip['total_traf_tx_month']+$equip['total_traf_rx_month']));
			       //$speed_b[]=array_map('utf8_encode',$speed);
                   //$merge = array_merge($merge, ['speed' => $speed]);
			       $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
		       }
	
		       else
		       {
                   $opt[]=array($att['name'], $att['value']);
				   //$opt_b[]=array_map('utf8_encode',$opt);
                   //$merge = array_merge($merge, ['opt' => $opt]);

                   $text .=  " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
		       }

            }

            if($serv['type']=="INT")
	        {
	           $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn_id."\" order by datetime desc limit 0,1    ")->fetch_assoc();
	           $fmac=substr($ip['mac'],0,8);
	           $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();

               $int[]=array($ip['ip'], $ip['datetime'], $ip['mac'], $brand['brand']);
               //$int_b[]=array_map('utf8_encode',$int);
               //$merge = array_merge($merge, ['int' => $int_b]);

               $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" ."<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
	
	
	       }

           $text .= "</table>";

        }

        $merge = array('text' => $text);

        $merge = array_merge($merge, ['dis_serv_id' => $dis_serv_id_a, 'dis_conn_id' => $dis_conn_id_a]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    // MOSTRAR OS SERVIÇOS ATIVADOS DE CADA CONEXAO
    elseif($_GET['enabled_serv_conn'] != "")
    {
        $serv_link = "";
        $conn_id = $_GET['conn_id'];

        $type = $_GET['type'];

        $text = '';

        $merge = array();

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

        $servs = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn_id." AND date_end = '0000-00-00' order by id DESC");

        while($serv=$servs->fetch_assoc())
        {

            $serv_b[]=array_map('utf8_encode',$serv);


            $serv_susp = $serv['is_susp_serv'];

            $dis_susp_conn = $eq_conn['dis_services'];

            

            $serv_id = $serv['id'];
            $tr_dis_services = "";
            $tr_dis_services_end = "";

            if(($dis_susp_conn == 1 && $serv_susp == 1))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_serv_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }

            else if(($dis_susp_conn == 2 && $serv_susp == 2))
            {
                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\" ";
                $dis_susp[] = "disabled";
                $dis_conn_id_a[] = $serv_id;
                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\">";
                $tr_dis_services_end = "</div>";
            }


            $text.= "<tr><td style=width:100%><br>
	        <tr><td>type: ".$serv['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$serv['id']." >".$serv['id']."</a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$serv['date_start']." <br>";

            //echo $serv['id']."<br>";

            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$serv['id']);

            $text .= "<table>";
            while($att=$atts->fetch_assoc())
	        {
                //echo $att['name'];
                
                $att_b[]=array_map('utf8_encode',$att);
                //$merge = array_merge($merge, ['attr_nome' => $att_b]);
                

                if($att['name']=="account")
		        {
                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="unifi_site")
		        {
                    $unifi_site[]=array($att['value'], explode("/",$att['value'])[5]);
			        //$unifi_site_b[]=array_map('utf8_encode',$unifi_site);
                    //$merge = array_merge($merge, ['unifi_site' => $unifi_site]);
                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
		        }
		
		       elseif($att['name']=="speed")
		       {
			       $speedname=$mon3->query("select * from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                   $speedname_b=array_map('utf8_encode',$speedname);
                   //$merge = array_merge($merge, ['speedname' => $speedname_b]);

                   $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				   $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];


                   $speed[]=array($speedname['name'], ($equip['total_traf_tx_month']+$equip['total_traf_rx_month']));
			       //$speed_b[]=array_map('utf8_encode',$speed);
                   //$merge = array_merge($merge, ['speed' => $speed]);
			       $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
		       }
	
		       else
		       {
                   $opt[]=array($att['name'], $att['value']);
				   //$opt_b[]=array_map('utf8_encode',$opt);
                   //$merge = array_merge($merge, ['opt' => $opt]);

                   $text .=  " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
		       }

            }

            if($serv['type']=="INT")
	        {
	           $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn_id."\" order by datetime desc limit 0,1    ")->fetch_assoc();
	           $fmac=substr($ip['mac'],0,8);
	           $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();

               $int[]=array($ip['ip'], $ip['datetime'], $ip['mac'], $brand['brand']);
               //$int_b[]=array_map('utf8_encode',$int);
               //$merge = array_merge($merge, ['int' => $int_b]);

               $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" ."<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
	
	
	       }

           $text .= "</table>";

        }

        $merge = array('text' => $text);

        $merge = array_merge($merge, ['dis_serv_id' => $dis_serv_id_a, 'dis_conn_id' => $dis_conn_id_a]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    // SUSPENDER OS SERVIÇOS DA CONEXAO CORRESPONDENTE
    elseif($_POST['disabled_services_all_date'] != "")
    {
        $conn_id = $_POST['conn_id'];

        $date_submit_disabled = $_POST['date_submit_disabled'];

        $prop_id = $_POST['prop_id'];

        $date_now = date("Y-m-d");

        $at_msg_log_serv = 0;
        $at_msg_log_serv_end = 0;

        $merge = array();
        $msg = "";

        

        $services_date_not_end = $mon3->query("SELECT * FROM services where date_end = '0000-00-00' AND connection_id=".$conn_id );

        $services_date_end = $mon3->query("SELECT * FROM services where date_end != '0000-00-00' AND connection_id=".$conn_id );

        if($date_submit_disabled <= $date_now)
        {

            $dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"1\", 
            date_dis_services=\"".$date_submit_disabled."\",date_rea_services=\"0000-00-00\", date_dis_conn=\"0000-00-00\" WHERE id=".$conn_id);

				// Disabled Services all on table services on connections approach

				while($service_date_not_end=$services_date_not_end->fetch_assoc())
				{
                    //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
					$dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"1\" WHERE id=".$service_date_not_end['id']);
					$at_msg_log_serv = 1;
				}

                while($service_date_end=$services_date_end->fetch_assoc())
				{
                    //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
					$dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"1\" WHERE id=".$service_date_end['id']);
					$at_msg_log_serv_end = 1;
				}



				//$dis_all_services_by_conn = $mon3->query("UPDATE services set date_end=\"".$conn_date_dis_service['date_dis_services']."\" WHERE connection_id=".$conn['id']);
				
				if($at_msg_log_serv == 1 || $at_msg_log_serv_end == 1)
				{
					proplog($prop_id,"All Services on connection number <b>".$conn_id."</b> was suspended on date <b>".$date_submit_disabled."</b>");
				}
                $msg .= "<font color=green>All Services on connection number <b>".$conn_id."</b> was suspended on date <b>".$date_submit_disabled."</b></font>";
        }
        else
        {

                $dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"0\", 
                date_dis_services=\"".$date_submit_disabled."\",date_rea_services=\"0000-00-00\" WHERE id=".$conn_id);

                while($service_date_not_end=$services_date_not_end->fetch_assoc())
                {
                    //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
                    $dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"1\" WHERE id=".$service_date_not_end['id']);
                }

                while($service_date_end=$services_date_end->fetch_assoc())
                {
                    //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
                    $dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"1\" WHERE id=".$service_date_end['id']);
                }

				proplog($prop_id,"All Services on connection number <b>".$conn_id."</b> are going to be suspended on date <b>".$date_submit_disabled."</b>");

                $msg .= "<font color=green>All Services on connection number <b>".$conn_id."</b> are going to be suspended on date <b>".$date_submit_disabled."</b></font>";


                //$services_susp = $mon3->query("SELECT * FROM services where is_susp_serv=\"1\" AND connection_id=".$conn_id );

                //$merge = array_merge($merge, ['serv_id_dis' => $msg]);
        }
        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }

    // REACTIVAR OS SERVIÇOS DE UMA DADA CONEXAO
    elseif($_POST['reactivate_services_all_date'] != "")
    {
        $conn_id = $_POST['conn_id'];

        $date_submit_reactivated = $_POST['date_submit_reactivated'];
        //echo $date_submit_reactivated;

        $prop_id = $_POST['prop_id'];

        $date_now = date("Y-m-d");

        $at_msg_log_serv = 0;
        $at_msg_log_serv_end = 0;

        $merge = array();
        $msg = "";

        

        $services_all_date_suspended = $mon3->query("SELECT * FROM services where is_susp_serv='1' AND connection_id=".$conn_id );

		$conn_all_date_suspended = $mon3->query("SELECT * FROM connections where dis_services='1' AND id=".$conn_id )->fetch_assoc();
		
		$date_submit_disabled = $conn_all_date_suspended['date_dis_services'];

        if($date_submit_reactivated <= $date_now)
        {

				$dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"0\", 
				date_dis_services=\"0000-00-00\", date_rea_services=\"".$date_submit_reactivated."\", date_dis_conn=\"0000-00-00\" WHERE id=".$conn_id);

				// Disabled Services all on table services on connections approach

				while($service_all_date_suspended=$services_all_date_suspended->fetch_assoc())
				{
                    //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
					$dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"0\" WHERE id=".$service_all_date_suspended['id']);
					$at_msg_log_serv = 0;
				}


				//$dis_all_services_by_conn = $mon3->query("UPDATE services set date_end=\"".$conn_date_dis_service['date_dis_services']."\" WHERE connection_id=".$conn['id']);
				
				if($at_msg_log_serv == 0)
				{
					proplog($prop_id,"All Services on connection number <b>".$conn_id."</b> was reactivated on date <b>".$date_submit_reactivated."</b>");
				}
                $msg .= "<font color=green>All Services on connection number <b>".$conn_id."</b> was reactivated on date <b>".$date_submit_reactivated."</b></font>";
        }
        else
        {

                $dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"1\", 
                date_dis_services=\"0000-00-00\", date_rea_services=\"".$date_submit_reactivated."\" WHERE id=".$conn_id);

                while($service_all_date_suspended=$services_all_date_suspended->fetch_assoc())
                {
                    $dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"1\" WHERE id=".$service_date_not_end['id']);
                }

				proplog($prop_id,"All Services on connection number <b>".$conn_id."</b> are going to be reactivated on date <b>".$date_submit_reactivated."</b>");

                $msg .= "<font color=green>All Services on connection number <b>".$conn_id."</b> are going to be reactivated on date <b>".$date_submit_reactivated."</b></font>";
        }
        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }


    // DESATIVAR A CONEXAO E DESATIVAR OS SERVIÇOS QUE ESTAVAM DESATIVOS
    elseif($_POST['disabled_conn_all_date'] != "")
    {
        $conn_id = $_POST['conn_id'];

        $date_end_conn = $_POST['date_end_conn'];

        $prop_id = $_POST['prop_id'];

        $remove_equipment = $_POST['remove_equipment'];

        $date_now = date("Y-m-d");

        $at_msg_log_conn = 0;
        $at_msg_log_conn_end = 0;

        $merge = array();
        $msg = "";

        $services_date_not_end = $mon3->query("SELECT * FROM services where date_end = '0000-00-00' AND connection_id=".$conn_id );

        $services_date_end = $mon3->query("SELECT * FROM services where date_end != '0000-00-00' AND connection_id=".$conn_id );

        if($date_end_conn <= $date_now)
        {
            // DESATIVAR O BOTAO DOS SERVIÇOS
            // DESATIVAR O BOTAO DE SUSPENDER OS SERVICOS
            // DESATIVAR O BOTAO DE EDICAO DOS SERVIÇOS


            $dis_conn = $mon3->query("UPDATE connections set dis_services =\"2\", 
            date_dis_conn=\"".$date_end_conn."\",date_rea_services=\"0000-00-00\",date_dis_services=\"0000-00-00\" WHERE id=".$conn_id);

            while($service_date_not_end=$services_date_not_end->fetch_assoc())
            {
                //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
                $dis_serv_each_id = $mon3->query("UPDATE services set date_end=\"".$date_end_conn."\", is_susp_serv=\"2\" WHERE id=".$service_date_not_end['id']);
                $at_msg_log_conn = 2;
            }

            while($service_date_end=$services_date_end->fetch_assoc())
            {
                //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
                $dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"2\", date_end=\"".$date_end_conn."\" WHERE id=".$service_date_end['id']);
                $at_msg_log_conn_end = 2;
            }


				proplog($prop_id,"The connection number <b>".$conn_id."</b> was disconnected on date <b>".$date_end_conn."</b>");

                if($remove_equipment == 1)
                {
                    $conn_type_re_equip = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id)->fetch_assoc();
                    if($conn_type_re_equip['type'] == 'GPON')
                    {
                        $mon3->query("UPDATE ftth_ont set ont_id = \"\" WHERE fsan='".$conn_type_re_equip['equip_id'] ."'");
                        $mon3->query("DELETE FROM ftth_ont WHERE fsan = \"\" ");
                    }
                    else if($conn_type_re_equip['type'] == 'FWA')
                    {
                        $mon3->query("UPDATE fwa_cpe set antenna = \"\" WHERE mac='".$conn_type_re_equip['equip_id'] ."'");
                        $mon3->query("DELETE FROM fwa_cpe WHERE mac = \"\" ");
                    }
                    else if($conn_type_re_equip['type'] == 'COAX')
                    {
                        $mon3->query("DELETE FROM coax_modem WHERE mac = \"\" ");
                    }
                    else if($conn_type_re_equip['type'] == 'ETH' || $conn_type_re_equip['type'] == 'ETHF')
                    {
                        $mon3->query("DELETE FROM coax_modem WHERE mac = \"\" ");
                    }
                    // UPDATE EQUIP NULL ON CONNECTIONS
                    $update_equip = $mon3->query("UPDATE connections set equip_id =\"\" WHERE id=".$conn_id);

                    

                    
                    // UPDATE EQUIP NULL ON SERVICES
                    $update_equip_services = $mon3->query("UPDATE services set equip_id =\"\" WHERE connection_id=".$conn_id );
                }

                


			//}
            $msg .= "<font color=green>The connection number <b>".$conn_id."</b> was disconnected on date <b>".$date_end_conn."</b></font>";
        }
        else
        {
            // DESATIVAR O BOTAO DE SUSPENDER A CONEXAO
            // DESATIVAR O BOTAO DE SUSPENDER OS SERVICOS

            $dis_conn = $mon3->query("UPDATE connections set dis_services =\"0\", 
            date_dis_conn=\"".$date_end_conn."\",date_rea_services=\"0000-00-00\" WHERE id=".$conn_id);

            $dis_ser_conn = $mon3->query("UPDATE connections set dis_services =\"0\", 
            date_dis_services=\"".$date_end_conn."\",date_rea_services=\"0000-00-00\" WHERE id=".$conn_id);

            while($service_date_not_end=$services_date_not_end->fetch_assoc())
            {
                //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
                $dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"2\" WHERE id=".$service_date_not_end['id']);
            }

            while($service_date_end=$services_date_end->fetch_assoc())
            {
                //echo "UPDATE services set date_end=\"".$date_submit_disabled."\" WHERE id=".$service_date_not_end['id'];
                $dis_serv_each_id = $mon3->query("UPDATE services set is_susp_serv=\"2\" WHERE id=".$service_date_end['id']);
            }

            proplog($prop_id,"The connection number <b>".$conn_id."</b> are going to be disconnected on date <b>".$date_end_conn."</b>");

            $msg .= "<font color=green>The connection number <b>".$conn_id."</b> are going to be disconnected on date <b>".$date_end_conn."</b></font>";


        }

        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }

    // SUBMTERE A PROPERTY LOG
    elseif($_POST['submit_log_entry'] != "")
    {
        $prop_id = $_POST['prop_id'];

        $merge = array();

        $notes = $_POST['notes'];

        if($notes != "")
        {
            //proplog($prop_id,"a");

            $n = nl2br($notes);

            proplog($prop_id,"<b>Entry Log on Property number ".$prop_id."</b> - ".$n);

            $msg = "<font color=green>Entry Log was submitted sucessfully. Page will reload in 5 seconds</font>";

            $merge = array_merge($merge, ['msg' => $msg, 'count' => '5']);
        }
        else
        {
            $msg = "<font color=red>Insert Log entry text</font>";

            $merge = array_merge($merge, ['msg' => $msg, 'count' => '0']);
        }   

        

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }


    

    


    



    elseif($_GET['search_props']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchprop']);
        $wq = "";

        if($str != "")
        {
            $wq .= " AND (address LIKE '%".$str."%' or ref LIKE '%".$str."%')";
        }
        else
        {
            $wq .= "";
            
        }

        //echo "select count(*) as 'counter' from property_leads where 1 ".$wq." order by date_lead ";

        $props=$mon3->query("select id,ref,address from properties where 1 ".$wq." order by ref limit 0,50 ");
        $count=$mon3->query("select count(*) as 'counter' from properties where 1 ".$wq." order by ref ")->fetch_row();
        $props_array= array("id"=>"total", "address"=> $count[0]);
        while($prop=$props->fetch_assoc())
        {
            $prop_b[] = array_map('utf8_encode', $prop);
            $props_array = array_merge($props_array, ['props' => $prop_b]);
        }

        $lastp=ceil($count[0]/50);
        $curpage=($offset/50)+1;

        $props_array = array_merge($props_array, ['num_rows' => $count[0], 'lastp' => $lastp, 'curpage' => $curpage]);	

        echo json_encode($props_array);
    }

    elseif($_GET['search_custs']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchcusts']);
        $wq = "";

        if($str != "")
        {
            $wq .= " AND (name LIKE '%".$str."%' or id LIKE '%".$str."%' or 
            email LIKE '%".$str."%' or telef LIKE '%".$str."%' or fiscal_nr LIKE '%".$str."%')";
        }
        else
        {
            $wq .= "";
            
        }


        $custs=$mon3->query("select id,name,email,telef,fiscal_nr from customers where 1 ".$wq." order by name limit 0,50 ");
        $count=$mon3->query("select count(*) as 'counter' from customers where 1 ".$wq." order by name ")->fetch_row();
        $custs_array= array("id"=>"total", "address"=> $count[0]);
        while($cust=$custs->fetch_assoc())
        {
            $notes=explode("<br>",$cust['notes']);
            $notes=$notes[0];
            $cust_b[] = array_map('utf8_encode', $cust);
            $custs_array = array_merge($custs_array, ['custs' => $cust_b]);
        }

        $lastp=ceil($count[0]/50);
        $curpage=($offset/50)+1;

        $custs_array = array_merge($custs_array, ['num_rows' => $count[0], 'lastp' => $lastp, 'curpage' => $curpage]);	

        echo json_encode($custs_array);



    }





    elseif($_GET['search_leads']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchlead']);
        $status=mysqli_real_escape_string($mon3, $_GET['status']);
        $owner=mysqli_real_escape_string($mon3, $_GET['owner']);
        $wq = "";

        if($str != "")
        {
            $wq .= " AND (address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%')";
        }
        else
        {
            $wq .= "";
            
        }


        if($status == "all" || $status == "")
        {
            $wq .= "";
        }
        else
        {
            
            $wq .= " AND status = ".$status."";
            
        }

        if($owner == "all" || $owner == "")
        {
            $wq .= "";
        }
        else
        {
            $wq .= " AND created_by = '".$owner."'";
        }

        //echo "select count(*) as 'counter' from property_leads where 1 ".$wq." order by date_lead ";

        $props=$mon3->query("select id,address,name,agent_id,status,date_lead,created_by,notes from property_leads where 1 ".$wq." order by date_lead limit 0,50 ");
        $count=$mon3->query("select count(*) as 'counter' from property_leads where 1 ".$wq." order by date_lead ")->fetch_row();
        $props_array= array("id"=>"total", "address"=> $count[0]);
        while($prop=$props->fetch_assoc())
        {
            $notes=explode("<br>",$prop['notes']);
            $notes=$notes[0];
            $prop_b[] = array_map('utf8_encode', $prop);
            $props_array = array_merge($props_array, ['props' => $prop_b]);
        }

        $lastp=ceil($count[0]/50);
        $curpage=($offset/50)+1;

        $props_array = array_merge($props_array, ['num_rows' => $count[0], 'lastp' => $lastp, 'curpage' => $curpage]);	

        echo json_encode($props_array);
    }

    elseif($_GET['search_status_owner_cond1']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchlead']);
        $status=mysqli_real_escape_string($mon3, $_GET['status']);
        $owner=mysqli_real_escape_string($mon3, $_GET['owner']);
        $wq = "";

        if($str != "")
        {
            $wq .= " AND (address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%')";
        }
        else
        {
            $wq .= "";
            
        }


        if($status == "all" || $status == "")
        {
            $wq .= "";
        }
        else
        {
            
            $wq .= " AND status = ".$status."";
            
        }

        if($owner == "all" || $owner == "")
        {
            $wq .= "";
        }
        else
        {
            $wq .= " AND created_by = '".$owner."'";
        }
        


        $props=$mon3->query("select id,address,name,agent_id,status,date_lead,created_by,notes from property_leads where 1 ".$wq." order by date_lead limit 0,50 ");
        $count=$mon3->query("select count(*) as 'counter' from property_leads where 1 ".$wq." order by date_lead ")->fetch_row();
        $props_array= array("id"=>"total", "address"=> $count[0]);
        while($prop=$props->fetch_assoc())
        {
            $notes=explode("<br>",$prop['notes']);
            $notes=$notes[0];
            $prop_b[] = array_map('utf8_encode', $prop);
            $props_array = array_merge($props_array, ['props' => $prop_b]);
        }

        $lastp=ceil($count[0]/50);
        $curpage=($offset/50)+1;

        $props_array = array_merge($props_array, ['num_rows' => $count[0], 'lastp' => $lastp, 'curpage' => $curpage]);	

        echo json_encode($props_array);
    }

    elseif($_GET['search_status_owner_cond2']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchlead']);
        $status=mysqli_real_escape_string($mon3, $_GET['status']);
        $owner=mysqli_real_escape_string($mon3, $_GET['owner']);
        $wq = "";

        if($str != "")
        {
            $wq .= " AND (address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%')";
        }
        else
        {
            $wq .= "";
            
        }


        if($status == "all" || $status == "")
        {
            $wq .= "";
        }
        else
        {
            
            $wq .= " AND status = ".$status."";
            
        }

        if($owner == "all" || $owner == "")
        {
            $wq .= "";
        }
        else
        {
            $wq .= " AND created_by = '".$owner."'";
        }


        $props=$mon3->query("select id,address,name,agent_id,status,date_lead,created_by,notes from property_leads where 1 ".$wq." order by date_lead limit 0,50 ");
        $count=$mon3->query("select count(*) as 'counter' from property_leads where 1 ".$wq." order by date_lead ")->fetch_row();
        $props_array= array("id"=>"total", "address"=> $count[0]);
        while($prop=$props->fetch_assoc())
        {
            $notes=explode("<br>",$prop['notes']);
            $notes=$notes[0];
            $prop_b[] = array_map('utf8_encode', $prop);
            $props_array = array_merge($props_array, ['props' => $prop_b]);
        }

        $lastp=ceil($count[0]/50);
        $curpage=($offset/50)+1;

        $props_array = array_merge($props_array, ['num_rows' => $count[0], 'lastp' => $lastp, 'curpage' => $curpage]);	

        echo json_encode($props_array);


    }

    elseif($_GET['search_leads_status']!="")
    {
        $props_array_status = array();
        $props_status = $mon3->query("select DISTINCT(status) from property_leads order by status");
        while($prop_status=$props_status->fetch_assoc())
        {
            $prop_status_b[] = array_map('utf8_encode', $prop_status);
            $props_array_status = array_merge($props_array_status, ['props_status' => $prop_status_b]);
        }

        echo json_encode($props_array_status);
    }

    elseif($_GET['search_leads_users']!="")
    {
        $props_array_username = array();
        $props_username = $mon3->query("select username from users order by username");
        while($prop_username=$props_username->fetch_assoc())
        {
            $prop_username_b[] = array_map('utf8_encode', $prop_username);
            $props_array_username = array_merge($props_array_username, ['props_username' => $prop_username_b]);
        }

        echo json_encode($props_array_username);
    }








    elseif($_GET['status_ont']!="")
    {
        $status['date']=date("Y-m-d H:i:s");
        $olt=mysqli_real_escape_string($mon3, $_GET['olt']);
        $ont=mysqli_real_escape_string($mon3, $_GET['ont']);
        $ont_a=explode("-",$ont);
        $ont_= $mon3->query("select * from ftth_ont where olt_id=$olt AND ont_id=\"$ont\" ")->fetch_assoc();
        $olt_ip = $mon3->query("select ip from ftth_olt where id=$olt")->fetch_assoc();
        $msg="";
            
        $rf = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.12.1","100000",2);
        $rf=explode('"',$rf);
        $rf=explode(' ',$rf[1]);
        $status['rf']=$rf[0];

        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.7.1","100000",2);
        $rx=explode('"',$rx);
        $rx=explode(' ',$rx[1]);
        $status['rx']=$rx[0];

        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.41.1.6.2","100000",2);
        $rx=explode(':',$rx);
        $status['ip_voip']=$rx[1];

        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.41.1.6.2","100000",2);
        $rx=explode(':',$rx);
        $status['ip_wan']=$rx[1];


        $status['ip_mng']=$ont_['mng_ip'];


        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.2.1.1.1","100000",2);
        $rx=explode('"',$rx);
        $status['model']=$rx[1];


        $sw = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.2.1.1.21","100000",2);
        $sw=explode('"',$sw);
        $status['sw']=trim($sw[1]);

        $uptime=snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.2.1.1.3.0","100000",2);
        $uptime=explode(')',$uptime);
        $status['uptime']=trim($uptime[1]);

        $uptime=snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.3.1.21.1.3.1.1.3","100000",2);
        $uptime=explode('"',$uptime);
        $status['fsan']=trim($uptime[1]);

        




        $status['msg']=$msg;
        //	var_dump($status);
        echo json_encode($status);




    }


    elseif($_GET['status_modem']!="")
    {
        $status['date']=date("Y-m-d H:i:s");
        $olt=mysqli_real_escape_string($mon3, $_GET['cmts']);
        $ont=mysqli_real_escape_string($mon3, $_GET['modem']);
        $ont_= $mon3->query("select * from coax_modem where mac=\"$ont\" ")->fetch_assoc();
        $olt_ip = $mon3->query("select ip from coax_cmts where id=$olt")->fetch_assoc();

        $status['uptime']=snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.1.3.0",500000);

        if($status['uptime']!="")
        {
            $msg="";

            $status['model']=htmlspecialchars(snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.1.1.0",1000000));
            $status['uptime']=snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.1.3.0",1000000);
            $status['phone']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.4.1.4491.2.1.14.1.1.2.1.2.16",1000000))[1];
            $status['bootfile']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.69.1.4.5.0",1000000))[1];
            $status['ssid']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.4.1.8595.80211.5.1.14.1.3.1",1000000))[1];
            $status['pass']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.4.1.8595.80211.5.2.4.1.2.1",1000000))[1];
            
            $status['pubip']=explode(":",snmpgetnext($ont_['mng_ip'],"public",".1.3.6.1.2.1.4.22.1.3.1",1000000))[1];
            $status['ds_freq']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.1.1.2.3",1000000))[1]/1000000 ."MHz";
            $status['us_freq']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.2.1.2.4",1000000))[1]/1000000 ."MHz";

            $status['us_lev']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.2.2.1.3.2",1000000))[1]/10 ."dBm";

            
            $status['ds_lev']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.1.1.6.3",1000000))[1]/10 ."dBm";
            $status['ds_snr']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.4.1.5.3",1000000))[1]/10 ."dB";

            
            
        //	.1.3.6.1.2.1.10.127.1.1.1.1.2.3
            
        }


        $status['msg']=$msg;
//	var_dump($status);
        echo json_encode($status);

    }


    elseif($_GET['reboot_modem']!="")
    {
        $status['date']=date("Y-m-d H:i:s");
        $olt=mysqli_real_escape_string($mon3, $_GET['cmts']);
        $ont=mysqli_real_escape_string($mon3, $_GET['modem']);
        $ont_= $mon3->query("select * from coax_modem where mac=\"$ont\" ")->fetch_assoc();
        $olt_ip = $mon3->query("select ip from coax_cmts where id=$olt")->fetch_assoc();
        $msg="";

        $msg .= snmpset($ont_['mng_ip'], "private", ".1.3.6.1.2.1.69.1.1.3.0","i",1);


        $status['msg']=$msg;
//	var_dump($status);
        echo json_encode($status);
    }


    // SHOW ALL CONNECTIONS

    elseif($_GET['show_all_connections']!="")
    {
        $prop_id = $_GET['prop_id'];
        $merge = array();
        $conns = $mon3->query("SELECT * FROM connections WHERE property_id = ".$prop_id);

        $text = "";

        while($conn=$conns->fetch_assoc())
        {
            $text .= "<table>";
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

            $conn_date_end = 0;


            $coax_text = '';
            $gpon_text = '';
            $fwa_text = '';
            $eth_ethf_text = '';
            $darkf_text = '';

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
                            

                            proplog($prop_id,"All Services on connection number <b>".$conn['id']."</b> was suspended on date <b>".$conn_date_dis_service['date_dis_services']."</b>");
                        
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

                            proplog($prop_id,"The connection number <b>".$conn['id']."</b> are disabled on date <b>".$conn_date_dis_con['date_dis_conn']."</b>");		
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


                        proplog($prop_id,"All Services on connection number <b>".$conn['id']."</b> was reactivated on date <b>".$conn_date_dis_con['date_rea_services']."</b>");

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



                //is_sups_serv=1

                //$date_now = "2022-09-30";

                if($conn['date_end'] != '0000-00-00')
                {
                    $conn_date_end = 1;
                }

                if($conn_date_end != 1)
                {
                    if($dis_services == 0)
                    {
                        if($conn['type']=="COAX")
                        {
                            $coax_text .= "
                            <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                            <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                            id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-".$conn['id'].">
                            <div class=modal-content>
                                    <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                        <h1>Suspend Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center;\">
                                                <button type=button onclick=\"submitDisabledServices(".$conn['id'].", ".$prop_id.")\" >Submit Suspend Services</button>
                                            </div>
    
                                            <span id=submit-dis-services-".$conn['id']."></span>
                                        </div>
                            </div>";


                            $coax_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                        else if($conn['type']=="GPON")
                        {
                            $gpon_text .= "
                            <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                            <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                            id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-".$conn['id'].">
                            <div class=modal-content>
                                    <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                        <h1>Suspended Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center;\">
                                                <button type=button onclick=\"submitDisabledServices(".$conn['id'].", ".$prop_id.")\" >Submit Disable Services</button>
                                            </div>
    
                                            <span id=submit-dis-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $gpon_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";


                        }
                        else if($conn['type']=="FWA")
                        {
                            $fwa_text .= "
                            <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                            <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                            id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-".$conn['id'].">
                            <div class=modal-content>
                                    <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                        <h1>Suspend Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center;\">
                                                <button type=button onclick=\"submitDisabledServices(".$conn['id'].", ".$prop_id.")\" >Submit Suspend Services</button>
                                            </div>
    
                                            <span id=submit-dis-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $fwa_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                            
                        }
                        else if($conn['type']=="ETH" || $conn['type']=="ETHF")
                        {
                            $eth_ethf_text .= "
                            <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                            <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                            id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-".$conn['id'].">
                            <div class=modal-content>
                                    <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                        <h1>Suspend Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center;\">
                                                <button type=button onclick=\"submitDisabledServices(".$conn['id'].", ".$prop_id.")\" >Submit Suspend Services</button>
                                            </div>
    
                                            <span id=submit-dis-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $eth_ethf_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                        else if($conn['type']=="DARKF")
                        {
                            $darkf_text .= "
                            <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                            <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                            id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-".$conn['id'].">
                            <div class=modal-content>
                                    <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                        <h1>Suspend Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center;\">
                                                <button type=button onclick=\"submitDisabledServices(".$conn['id'].", ".$prop_id.")\" >Submit Suspend Services</button>
                                            </div>
    
                                            <span id=submit-dis-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $darkf_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                            
                        }

                    }
                    else if($dis_services == 1)
                    {
                        if($conn['type']=="COAX")
                        {
                            $coax_text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                            <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                            </div>
                            <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-rea-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                            <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitReactiveServices(".$conn['id'].", ".$prop_id.")\" >Submit Reactive Services</button>
                                            </div>
                            
                                            <span id=submit-rea-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $coax_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].", , '$prop_id')\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                        else if($conn['type']=="GPON")
                        {
                            $gpon_text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                            <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                            </div>
                            <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-rea-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                            <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitReactiveServices(".$conn['id'].", ".$prop_id.")\"\" >Submit Reactive Services</button>
                                            </div>
                            
                                            <span id=submit-rea-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $gpon_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                        else if($conn['type']=="FWA")
                        {
                            $fwa_text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                            <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                            </div>
                            <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-rea-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                            <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitReactiveServices(".$conn['id'].", ".$prop_id.")\" >Submit Reactive Services</button>
                                            </div>
                            
                                            <span id=submit-rea-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $fwa_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                        else if($conn['type']=="ETH" || $conn['type']=="ETHF")
                        {
                            $eth_ethf_text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                            <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                            </div>
                            <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-rea-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                            <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitReactiveServices(".$conn['id'].", ".$prop_id.")\" >Submit Reactive Services</button>
                                            </div>
                            
                                            <span id=submit-rea-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $eth_ethf_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                        else if($conn['type']=="DARKF")
                        {
                            $darkf_text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                            <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                            </div>
                            <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-conn-rea-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                            <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                            <br>
                                            <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitReactiveServices(".$conn['id'].", ".$prop_id.")\" >Submit Reactive Services</button>
                                            </div>
                            
                                            <span id=submit-rea-services-".$conn['id']."></span>
                                        </div>
                            </div>";

                            $darkf_text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                            <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                            </div>
                            <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                            <div class=modal id=modal-prop-conn-".$conn['id'].">
                                        <div class=modal-content>
                                            <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                            <h1>Disconnect Connection ".$conn['id']."</h1>
                                            <br>
                                            <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                            <br>
                                            <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                            <div style=\"text-align: center; \">
                                                <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disconnect Connection</button>
                                            </div>

                                            <span id=submit-dis-conn-".$conn['id']."></span>
                                        </div>

                            </div>";
                        }
                    }
                }




                

                
                

                $text .= "<table >";


                $text .= "</table >";
                $text .= "<table style=\"width: 100%\">";
                $text .= "<tr><td colspan=2>";

                if($conn['type']=="COAX")
                {
                    $equip=$mon3->query("select * from coax_modem 
                    where UPPER(mac)= UPPER(\"".$conn['equip_id']."\")")->fetch_assoc();

                    $card=$mon3->query("select name from coax_upstreams where cmts_id=\"".$equip['cmts']."\" and upstream_id=\"".$equip['interface']."\" ")->fetch_assoc();
                    //echo "error4: ".mysqli_error($mon3);


                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type:".$conn['type']." <br>
                    equip: <a href=?equip=".$conn['equip_id']."&equip_type=COAX>".$conn['equip_id']. "</a> - 
                    <a href=http://".$equip['mng_ip'].">". $equip['mng_ip']. "</a><br>
                    CELL: <a href=?coax=1&upstream=".$equip['interface']."&cmts=".$equip['cmts'].">".$equip['interface']."-".$card['name']."</a><br>
                    date start:".$conn['date_start']."<br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }

                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    $text .= $coax_text;

                    


                    $text .= "</div></td><tr><td><b>status</b> ( by ".date("Y-m-d H:i:s ",$equip['status_timestamp'])."):<br>
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


                    $text .= "
                    <br><br>
                    <table width=300px>
                    <tr><td width=25% align=center ".$id_status ."><img width=48px src=img/status_green.png onclick=\"popup2('status','COAX','".$equip['cmts']."','".$equip['mac']."');\" ".$dis_but_serv_equip_mac."> <span id=\"add-status-coax-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_reboot ."><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','COAX','".$equip['cmts']."','".$equip['mac']."');\" ".$dis_but_serv_equip_mac."> <span id=\"add-reboot-coax-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    
                     
                    </table>
                    <br><br>
                    ";

                }

                elseif($conn['type']=="GPON")
                {

                    $equip=$mon3->query("select * from ftth_ont 
                    where fsan=\"".$conn['equip_id']."\"")->fetch_assoc();


                    $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] ." ". $equip['olt_id'];
                        $ont2=explode("-",$equip['ont_id']);
                        $olt=$mon3->query("select name from ftth_olt where id=\"".$equip['olt_id']."\"" )->fetch_assoc();
                        $pon=$mon3->query("select name from ftth_pons where olt_id=\"".$equip['olt_id']."\" and
                        card=\"".$ont2[1]."\" AND pon=\"".$ont2[2]."\"; ")->fetch_assoc();

                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type:".$conn['type']." <br>
                    olt: ".$olt['name']." <br>
                    PON: <a href=?gpon=1&pon=".$ont2[1]."-".$ont2[2]."&olt=".$equip['olt_id'].">".$pon['name']."</a><br>
                    equip: <a href=?equip=".$conn['equip_id']."&equip_type=GPON>".$conn['equip_id']. "</a> - 
                    <a href=http://".$equip['mng_ip'].">".$equip['ont_id']. "</a><br>
                    model: ".$equip['meprof']."<br>
                    date start:".$conn['date_start']."<br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }

                    


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


                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    $text .= $gpon_text;
                    
                    $text .= "</div></td>";

                    $text .= "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
                    <span id=status1 onmouseover=\"status1()\" onmouseout=\"status1o()\">".$equip['status']." </span>
                    <div class=popup1 id=popup1> ".str_replace("\n","<br>",$equip['errors'])." </div> <br> 
                    oltrx: ".$equip['tx']." oltrx: ".$equip['rx']."<br> 
                    rf: ".$equip['rf']."<br><br>";







                    $text .= "
                    <table width=300px>
                    <tr><td width=25% align=center ".$id_status ."><img width=48px src=img/status_green.png onclick=\"popup2('status','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-status-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_reboot ."><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-reboot-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_sync ."><img width=48px src=img/sync_green.png onclick=\"popup2('sync','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-sync-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_reset ."><img width=48px src=img/reset_green.png onclick=\"popup2('reset','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-reset-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    </table>

                    <br><br>";



                }

                elseif($conn['type']=="FWA")
                {
                    $equip=$mon3->query("select * from fwa_cpe where mac=\"".$conn['equip_id']."\"")->fetch_assoc();

                    $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] ." - ". $equip['antenna'];

                        $ant=$mon3->query("select * from fwa_antennas where id=\"".$equip['antenna']."\"" )->fetch_assoc();


                    $text .=  "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type:".$conn['type']." <br>
                    Antenna: ".$ant['name']." <br>
                    equip: <a href=?equip=".$conn['equip_id']."&equip_type=fwa>".$conn['equip_id']. "</a> - 
                    <a href=http://".$equip['mng_ip'].">".$equip['mac']. "</a><br>
                    model: ".$equip['model']."<br>
                    date start:".$conn['date_start']."<br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }


                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";


                    $text .= $fwa_text;

                    $text .= "</div></td><br><br>";




                }


                elseif($conn['type']=="ETH" || $conn['type']=="ETHF")
                {
                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type: ".$conn['type']." <br>";
                    if($conn['linked_prop']>0)
                    {
                        $lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
                        $lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
                        $text .= "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
                    }

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }

                    $text .= "<br><br>";

                    

                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    $text .= $eth_ethf_text;

                    $text .= "</div></td>";




                }


                elseif($conn['type']=="DARKF")
                {
                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type: ".$conn['type']." <br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }
                        
                    $text .= "<br><br>";

                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    $text .= $darkf_text;

                    $text .= "</div></td>";

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


                    $text .= "<tr>";

                    $servs_diss = 0;
                    $conn_id = $conn['id'];

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
                    $text .= "<br>";
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

                    $text .= "</table><table style=\"width: 100%;\"><tr><td style=\"width: 70%\"><b>Services: </b><br><br>  ";

                    if($conn_date_end != 1)
                    {
                        $text .= "<td align=\"center\" ><div ".$id_disabled_add_service."><a id=serv-conn-".$conn['id']." href=?servs=1&addserv=".$conn['id']." ".$dis_but_serv."> <img width=60px src=img/packageadd.png></a> <span id=\"add-serv-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span></div>
                        <tr><td>";
                    }
                    $text .= "<br></table>";

                    

                    if($serv_dis_num > 0 && $serv_en_num > 0)
                    {
                        $text .= "<table>
                        <button id=en_serv-".$conn['id']." type=button onclick=ShowAllServices(".$conn['id'].",\"".$conn['type']."\")>Show All Services</button>
                        <input type=hidden id=disabled_services-".$conn['id']." value=0>
                        <input type=hidden id=enabled_services-".$conn['id']." value=1>
                        <input type=hidden id=click_en_serv-".$conn['id']." value=0>
                        </table>";

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

                        $text .= "<table id=dis_serv_lists-".$conn['id'].">";

                        while($service=$services->fetch_assoc())
                        {
                            $dis_services = $eq_conn['dis_services'];
                            $serv_susp = $service['is_susp_serv'];

                            $serv_id = $service['id'];

                            if(($dis_services == 1 && $serv_susp == 1) || ($dis_services == 2 && $serv_susp == 2))
                            {
                                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
                                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
                                $tr_dis_services_end = "</div>";
                            }


                            $text .= "<tr><td style=width:100%><br>
                            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
                            </a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";

                            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);
                            $text .= "<table>";

                            while($att=$atts->fetch_assoc())
	                        {
                                if($att['name']=="account")
                                {
                                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                                }

                                elseif($att['name']=="unifi_site")
                                {

                                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                                }
                                
                                elseif($att['name']=="speed")
                                {

                                    $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				                    $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];

                                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                                    
                                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                                }
        
                                else
                                {
                                        $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value'].
                                        "<br>";
                                }

                            }

                            if($service['type']=="INT")
                            {
                                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                                $fmac=substr($ip['mac'],0,8);
                                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                                
                                
                                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                            
                            
                            }
	                        $text .= "</table>";


                        }

                        $text .= "</table>";

                    }
                    else if($serv_dis_num > 0 && $serv_en_num == 0)
                    {

                        $text .= "
                        <table>
                        <button id=des_serv-".$conn['id']." type=button onclick=ShowAllServices(".$conn['id'].",\"".$conn['type']."\")>Show All Services</button> 
                        <input type=hidden id=disabled_services-".$conn['id']." value=1>
                        <input type=hidden id=enabled_services-".$conn['id']." value=0>
                        <input type=hidden id=click_des_serv-".$conn['id']." value=0>
                        </table>";

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

                        $services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.id DESC");


                        $text .= "<table id=dis_serv_lists-".$conn['id'].">";

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


                            $text .= "<tr><td style=width:100%><br>
                            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
                            </a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";
                            $text .= "status: <font color=red><b> Disabled</b></font>"; 

                            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


                            $text .= "<table>";
                            while($att=$atts->fetch_assoc())
                            {
                                if($att['name']=="account")
                                {
                                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                                }

                                elseif($att['name']=="unifi_site")
                                {

                                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                                }
                                
                                elseif($att['name']=="speed")
                                {

                                    $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				                    $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];

                                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                                    
                                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                                }
                                
                                
                                
                                else
                                {
                                
                                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                                
                                }


                            }

                            if($service['type']=="INT")
                            {
                                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                                $fmac=substr($ip['mac'],0,8);
                                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                                
                                
                                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                                
                            
                            }
                            $text .= "</table>";
                        }

                        $text .= "</table>";

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


                        $text .= "<table id=dis_serv_lists-".$conn['id'].">";

                        while($service=$services->fetch_assoc())
                        {

                            $dis_services = $eq_conn['dis_services'];
                            $serv_susp = $service['is_susp_serv'];

                            $serv_id = $service['id'];
                            
                            if(($dis_services == 1 && $serv_susp == 1) || ($dis_services == 2 && $serv_susp == 2))
                            {
                                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
                                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
                                $tr_dis_services_end = "</div>";
                            }

                            $text .= "<tr><td style=width:100%><br>
                            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
                            </a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";	
                            if($service['date_end']!="0000-00-00")
                            {
                                $text .= "status: <font color=red><b> Disabled</b></font>"; 
                            }	

                            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


                            $text .= "<table>";

                            while($att=$atts->fetch_assoc())
		                    {
                                if($att['name']=="account")
                                {
                                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                                }

                                elseif($att['name']=="unifi_site")
                                {

                                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                                }
                                
                                elseif($att['name']=="speed")
                                {

                                    $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				                    $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];

                                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                                    
                                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                                }
                                
                                
                                
                                else
                                {
                                
                                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                                
                                }
                            }

                            if($service['type']=="INT")
                            {
                                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                                $fmac=substr($ip['mac'],0,8);
                                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                                
                                
                                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                                
                            
                            }
                            $text .= "</table>";



                        }

                        $text .= "</table>";
                    }







                }

                $text .= "<table><tr><td colspan=2><br><br><tr><td colspan=2><br><br>";











        $merge = array('text' => $text);

        //$merge = array_merge($merge, ['dis_serv_id' => $dis_serv_id_a, 'dis_conn_id' => $dis_conn_id_a]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }


    // SHOW CONNECTIONS ENABLED

    elseif($_GET['show_connections_enabled']!="")
    {
        $prop_id = $_GET['prop_id'];
        $merge = array();
        $conns = $mon3->query("SELECT * FROM connections WHERE property_id = ".$prop_id." AND date_end = '0000-00-00' ");

        $text = "";

        while($conn=$conns->fetch_assoc())
        {
            $text .= "<table>";
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
                $text .= "<table >";


                $text .= "</table >";
                $text .= "<table style=\"width: 100%\">";
                $text .= "<tr><td colspan=2>";

                if($conn['type']=="COAX")
                {
                    $equip=$mon3->query("select * from coax_modem 
                    where UPPER(mac)= UPPER(\"".$conn['equip_id']."\")")->fetch_assoc();

                    $card=$mon3->query("select name from coax_upstreams where cmts_id=\"".$equip['cmts']."\" and upstream_id=\"".$equip['interface']."\" ")->fetch_assoc();
                    //echo "error4: ".mysqli_error($mon3);


                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type:".$conn['type']." <br>
                    equip: <a href=?equip=".$conn['equip_id']."&equip_type=COAX>".$conn['equip_id']. "</a> - 
                    <a href=http://".$equip['mng_ip'].">". $equip['mng_ip']. "</a><br>
                    CELL: <a href=?coax=1&upstream=".$equip['interface']."&cmts=".$equip['cmts'].">".$equip['interface']."-".$card['name']."</a><br>
                    date start:".$conn['date_start']."<br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }

                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    if($dis_services == 0) 
                    {
                        $text .= "
                        <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                        <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                        id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-".$conn['id'].">
                        <div class=modal-content>
                                <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                    <h1>Suspended Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center;\">
                                            <button type=button onclick=\"submitDisabledServices(".$conn['id'].",".$prop_id.")\" >Submit Disable Services</button>
                                        </div>

                                        <span id=submit-dis-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    else if($dis_services == 1) 
                    {

                        $text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                        <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                        </div>
                        <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-rea-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                        <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitReactiveServices(".$conn['id'].",".$prop_id.")\" >Submit Reactive Services</button>
                                        </div>
                        
                                        <span id=submit-rea-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }


                    $text .= "</div></td><tr><td><b>status</b> ( by ".date("Y-m-d H:i:s ",$equip['status_timestamp'])."):<br>
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


                    $text .= "
                    <br><br>
                    <table width=300px>
                    <tr><td width=25% align=center ".$id_status ."><img width=48px src=img/status_green.png onclick=\"popup2('status','COAX','".$equip['cmts']."','".$equip['mac']."');\" ".$dis_but_serv_equip_mac."> <span id=\"add-status-coax-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_reboot ."><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','COAX','".$equip['cmts']."','".$equip['mac']."');\" ".$dis_but_serv_equip_mac."> <span id=\"add-reboot-coax-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    
                     
                    </table>
                    <br><br>
                    ";

                }

                elseif($conn['type']=="GPON")
                {

                    $equip=$mon3->query("select * from ftth_ont 
                    where fsan=\"".$conn['equip_id']."\"")->fetch_assoc();


                    $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] ." ". $equip['olt_id'];
                        $ont2=explode("-",$equip['ont_id']);
                        $olt=$mon3->query("select name from ftth_olt where id=\"".$equip['olt_id']."\"" )->fetch_assoc();
                        $pon=$mon3->query("select name from ftth_pons where olt_id=\"".$equip['olt_id']."\" and
                        card=\"".$ont2[1]."\" AND pon=\"".$ont2[2]."\"; ")->fetch_assoc();

                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type:".$conn['type']." <br>
                    olt: ".$olt['name']." <br>
                    PON: <a href=?gpon=1&pon=".$ont2[1]."-".$ont2[2]."&olt=".$equip['olt_id'].">".$pon['name']."</a><br>
                    equip: <a href=?equip=".$conn['equip_id']."&equip_type=GPON>".$conn['equip_id']. "</a> - 
                    <a href=http://".$equip['mng_ip'].">".$equip['ont_id']. "</a><br>
                    model: ".$equip['meprof']."<br>
                    date start:".$conn['date_start']."<br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }

                    


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


                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";


                    if($dis_services == 0) 
                    {
                        $text .= "
                        <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                        <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                        id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-".$conn['id'].">
                        <div class=modal-content>
                                <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                    <h1>Suspended Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center;\">
                                            <button type=button onclick=\"submitDisabledServices(".$conn['id'].",".$prop_id.")\" >Submit Disable Services</button>
                                        </div>

                                        <span id=submit-dis-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    else if($dis_services == 1) 
                    {

                        $text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                        <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                        </div>
                        <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-rea-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                        <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitReactiveServices(".$conn['id'].",".$prop_id.")\" >Submit Reactive Services</button>
                                        </div>
                        
                                        <span id=submit-rea-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    $text .= "</div></td>";

                    $text .= "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
                    <span id=status1 onmouseover=\"status1()\" onmouseout=\"status1o()\">".$equip['status']." </span>
                    <div class=popup1 id=popup1> ".str_replace("\n","<br>",$equip['errors'])." </div> <br> 
                    oltrx: ".$equip['tx']." oltrx: ".$equip['rx']."<br> 
                    rf: ".$equip['rf']."<br><br>";







                    $text .= "
                    <table width=300px>
                    <tr><td width=25% align=center ".$id_status ."><img width=48px src=img/status_green.png onclick=\"popup2('status','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-status-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_reboot ."><img width=48px src=img/reboot_green.png onclick=\"popup2('reboot','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-reboot-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_sync ."><img width=48px src=img/sync_green.png onclick=\"popup2('sync','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-sync-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    <td width=25% align=center ".$id_reset ."><img width=48px src=img/reset_green.png onclick=\"popup2('reset','GPON','".$equip['olt_id']."','".$equip['ont_id']."');\" ".$dis_but_serv_equip."> <span id=\"add-reset-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span>
                    </table>

                    <br><br>";



                }

                elseif($conn['type']=="FWA")
                {
                    $equip=$mon3->query("select * from fwa_cpe where mac=\"".$conn['equip_id']."\"")->fetch_assoc();

                    $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] ." - ". $equip['antenna'];

                        $ant=$mon3->query("select * from fwa_antennas where id=\"".$equip['antenna']."\"" )->fetch_assoc();


                    $text .=  "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type:".$conn['type']." <br>
                    Antenna: ".$ant['name']." <br>
                    equip: <a href=?equip=".$conn['equip_id']."&equip_type=fwa>".$conn['equip_id']. "</a> - 
                    <a href=http://".$equip['mng_ip'].">".$equip['mac']. "</a><br>
                    model: ".$equip['model']."<br>
                    date start:".$conn['date_start']."<br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }


                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";


                    if($dis_services == 0) 
                    {
                        $text .= "
                        <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                        <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                        id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-".$conn['id'].">
                        <div class=modal-content>
                                <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                    <h1>Suspended Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center;\">
                                            <button type=button onclick=\"submitDisabledServices(".$conn['id'].",".$prop_id.")\" >Submit Disable Services</button>
                                        </div>

                                        <span id=submit-dis-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    else if($dis_services == 1) 
                    {

                        $text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                        <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                        </div>
                        <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-rea-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                        <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitReactiveServices(".$conn['id'].",".$prop_id.")\" >Submit Reactive Services</button>
                                        </div>
                        
                                        <span id=submit-rea-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    $text .= "</div></td><br><br>";




                }


                elseif($conn['type']=="ETH" || $conn['type']=="ETHF")
                {
                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type: ".$conn['type']." <br>";
                    if($conn['linked_prop']>0)
                    {
                        $lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
                        $lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
                        $text .= "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
                    }

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }

                    $text .= "<br><br>";

                    

                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    if($dis_services == 0) 
                    {
                        $text .= "
                        <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                        <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                        id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-".$conn['id'].">
                        <div class=modal-content>
                                <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                    <h1>Suspended Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center;\">
                                            <button type=button onclick=\"submitDisabledServices(".$conn['id'].",".$prop_id.")\" >Submit Disable Services</button>
                                        </div>

                                        <span id=submit-dis-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    else if($dis_services == 1) 
                    {

                        $text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                        <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                        </div>
                        <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-rea-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                        <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitReactiveServices(".$conn['id'].",".$prop_id.")\" >Submit Reactive Services</button>
                                        </div>
                        
                                        <span id=submit-rea-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }


                    $text .= "</div></td>";




                }


                elseif($conn['type']=="DARKF")
                {
                    $text .= "<tr><td style=\"width: 70%;\">
                    id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                    type: ".$conn['type']." <br>";

                    if($conn['date_end'] != '0000-00-00')
                    {
                        $text .= "date end: <b>".$conn['date_end']."</b><br>";
                    }
                        
                    $text .= "<br><br>";

                    $text .= "<td>
                    <div class=\"services_connect_add\" id=\"connect-services-".$conn['id']."\">";

                    if($dis_services == 0) 
                    {
                        $text .= "
                        <div id=dis-services-div-".$conn['id']." ".$div_dis_services.">
                        <img src=img/power-off.png class=\"img_mon ".$dis_but_serv_img."\" onclick=clickDisService(".$conn['id'].") 
                        id=dis-services-".$conn['id']." onmouseover=mouseOverDisService(".$conn['id']."); onmouseout=mouseOutDisService(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-".$conn['id'].">
                        <div class=modal-content>
                                <span class=close-button onclick=clickCloseDisServices(".$conn['id'].")>×</span>
                                    <h1>Suspended Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date End Services:</b> <input type=date name=date_end_services-".$conn['id']." id=date_end_services-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center;\">
                                            <button type=button onclick=\"submitDisabledServices(".$conn['id'].",".$prop_id.")\" >Submit Disable Services</button>
                                        </div>

                                        <span id=submit-dis-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }

                    else if($dis_services == 1) 
                    {

                        $text .= "<div id=rea-services-div-".$conn['id']." ".$div_rea_services.">
                        <img src=img/power-button.png class=\"img_mon ".$rea_button_serv_img."\" onclick=clickReaService(".$conn['id'].") id=rea-services-".$conn['id']." onmouseover=mouseOverReaService(".$conn['id'].") onmouseout=mouseOutReaService(".$conn['id'].")> 
                        </div>
                        <span id=title-rea-services-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-conn-rea-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickCloseReaServices(".$conn['id'].")>×</span>
                                        <h1>Reactivate Serviçes ".$conn['id']."</h1>
                                        <br>
                                        <b>Date to Reactivate Services:</b> <input type=date name=date_end_services_rea-".$conn['id']." id=date_end_services_rea-".$conn['id']." class=dates_conn_services  data-date=\"\" data-date-format=\"YYYY-MM-DD\"  value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitReactiveServices(".$conn['id'].", ".$prop_id.")\" >Submit Reactive Services</button>
                                        </div>
                        
                                        <span id=submit-rea-services-".$conn['id']."></span>
                                    </div>
                        </div>";

                        $text .= "<div id=dis-conn-div-".$conn['id']." ".$div_dis_conn.">
                        <img src=img/multiply.png class=\"img_mon ".$dis_but_conn_img."\" onclick=disablePropConnection(".$conn['id'].") id=dis-prop-conn-".$conn['id']." onmouseover=mouseOverPropConn(".$conn['id'].") onmouseout=mouseOutPropConn(".$conn['id'].")> 
                        </div>
                        <span id=title-dis-conn-".$conn['id']." class=warning-serv-add-servs-conns style=\"display: none;\"></span>
                        <div class=modal id=modal-prop-conn-".$conn['id'].">
                                    <div class=modal-content>
                                        <span class=close-button onclick=clickClosePropConnection(".$conn['id'].")>×</span>
                                        <h1>Disconnect Connection ".$conn['id']."</h1>
                                        <br>
                                        <b>Date Connection Disconnect:</b> <input type=date name=date_end_conn-".$conn['id']." id=date_end_conn-".$conn['id']." data-date=\"\" class=dates_conn_services  data-date-format=\"YYYY-MM-DD\" class=dates_conn_services value=".date("Y-m-d")." size=10> YYYY-MM-DD<br>
                                        <br>
                                        <input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment
                                        <div style=\"text-align: center; \">
                                            <button type=button onclick=\"submitPropConnection(".$conn['id'].",".$prop_id.")\" >Submit Disable Connection</button>
                                        </div>

                                        <span id=submit-dis-conn-".$conn['id']."></span>
                                    </div>

                        </div>";


                    }


                    $text .= "</div></td>";

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


                    $text .= "<tr>";

                    $servs_diss = 0;
                    $conn_id = $conn['id'];

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
                    $text .= "<br>";
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

                    $text .= "</table><table style=\"width: 100%;\"><tr><td style=\"width: 70%\"><b>Services: </b>  <td align=\"center\" ><div ".$id_disabled_add_service."><a id=serv-conn-".$conn['id']." href=?servs=1&addserv=".$conn['id']." ".$dis_but_serv."> <img width=60px src=img/packageadd.png></a> <span id=\"add-serv-span-$conn_id\" class=\"warning-serv-add-conn\" style=\"display: none;\"></span></div>
                    <tr><td><br></table>";

                    if($serv_dis_num > 0 && $serv_en_num > 0)
                    {
                        $text .= "<table>
                        <button id=en_serv-".$conn['id']." type=button onclick=ShowAllServices(".$conn['id'].",\"".$conn['type']."\")>Show All Services</button>
                        <input type=hidden id=disabled_services-".$conn['id']." value=0>
                        <input type=hidden id=enabled_services-".$conn['id']." value=1>
                        <input type=hidden id=click_en_serv-".$conn['id']." value=0>
                        </table>";

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

                        $text .= "<table id=dis_serv_lists-".$conn['id'].">";

                        while($service=$services->fetch_assoc())
                        {
                            $dis_services = $eq_conn['dis_services'];
                            $serv_susp = $service['is_susp_serv'];

                            $serv_id = $service['id'];

                            if(($dis_services == 1 && $serv_susp == 1) || ($dis_services == 2 && $serv_susp == 2))
                            {
                                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
                                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
                                $tr_dis_services_end = "</div>";
                            }


                            $text .= "<tr><td style=width:100%><br>
                            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
                            </a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";

                            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);
                            $text .= "<table>";

                            while($att=$atts->fetch_assoc())
	                        {
                                if($att['name']=="account")
                                {
                                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                                }

                                elseif($att['name']=="unifi_site")
                                {

                                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                                }
                                
                                elseif($att['name']=="speed")
                                {


                                    $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				                    $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];

                                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                                    
                                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                                }
        
                                else
                                {
                                        $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value'].
                                        "<br>";
                                }

                            }

                            if($service['type']=="INT")
                            {
                                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                                $fmac=substr($ip['mac'],0,8);
                                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                                
                                
                                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                            
                            
                            }
	                        $text .= "</table>";


                        }

                        $text .= "</table>";

                    }
                    else if($serv_dis_num > 0 && $serv_en_num == 0)
                    {

                        $text .= "
                        <table>
                        <button id=des_serv-".$conn['id']." type=button onclick=ShowAllServices(".$conn['id'].",\"".$conn['type']."\")>Show All Services</button> 
                        <input type=hidden id=disabled_services-".$conn['id']." value=1>
                        <input type=hidden id=enabled_services-".$conn['id']." value=0>
                        <input type=hidden id=click_des_serv-".$conn['id']." value=0>
                        </table>";

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

                        $services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.id DESC");


                        $text .= "<table id=dis_serv_lists-".$conn['id'].">";

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


                            $text .= "<tr><td style=width:100%><br>
                            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
                            </a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";
                            $text .= "status: <font color=red><b> Disabled</b></font>"; 

                            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


                            $text .= "<table>";
                            while($att=$atts->fetch_assoc())
                            {
                                if($att['name']=="account")
                                {
                                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                                }

                                elseif($att['name']=="unifi_site")
                                {

                                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                                }
                                
                                elseif($att['name']=="speed")
                                {

                                    $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				                    $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];

                                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                                    
                                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                                }
                                
                                
                                
                                else
                                {
                                
                                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                                
                                }


                            }

                            if($service['type']=="INT")
                            {
                                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                                $fmac=substr($ip['mac'],0,8);
                                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                                
                                
                                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                                
                            
                            }
                            $text .= "</table>";
                        }

                        $text .= "</table>";

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


                        $text .= "<table id=dis_serv_lists-".$conn['id'].">";

                        while($service=$services->fetch_assoc())
                        {

                            $dis_services = $eq_conn['dis_services'];
                            $serv_susp = $service['is_susp_serv'];

                            $serv_id = $service['id'];
                            
                            if(($dis_services == 1 && $serv_susp == 1) || ($dis_services == 2 && $serv_susp == 2))
                            {
                                $serv_link = "class=\"disabledLink\" gloss=\"Service number $serv_id are suspended\" id=\"serv-dis-link-$serv_id\"  ";
                                $tr_dis_services = "<div id=\"tr-serv-dis-link-$serv_id\" style=\"display: inline-block;\" onmouseover=\"mouseOverDisServer(".$serv_id.")\" onmouseout=\"mouseOutDisServer(".$serv_id.")\">";
                                $tr_dis_services_end = "</div>";
                            }

                            $text .= "<tr><td style=width:100%><br>
                            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id']." >".$service['id']."
                            </a>".$tr_dis_services_end."<span id=\"serv_span-$serv_id\" class=\"warning-data_dis_serv\" style=\"display: none;\"></span><br>started on: ".$service['date_start']." <br>";	
                            if($service['date_end']!="0000-00-00")
                            {
                                $text .= "status: <font color=red><b> Disabled</b></font>"; 
                            }	

                            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


                            $text .= "<table>";

                            while($att=$atts->fetch_assoc())
		                    {
                                if($att['name']=="account")
                                {
                                    $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                                    $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                                }

                                elseif($att['name']=="unifi_site")
                                {

                                    $text .= " <tr><td>  &nbsp; unifi_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                                }
                                
                                elseif($att['name']=="speed")
                                {

                                    $equip['total_traf_tx_month'] = $equip['total_traf_tx_month'] == "" ? 0 : $equip['total_traf_tx_month'];
				                    $equip['total_traf_rx_month'] = $equip['total_traf_rx_month'] == "" ? 0 : $equip['total_traf_rx_month'];

                                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                                    
                                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                                }
                                
                                
                                
                                else
                                {
                                
                                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                                
                                }
                            }

                            if($service['type']=="INT")
                            {
                                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                                $fmac=substr($ip['mac'],0,8);
                                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                                
                                
                                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                                
                            
                            }
                            $text .= "</table>";



                        }

                        $text .= "</table>";
                    }







                }

                $text .= "<table><tr><td colspan=2><br><br><tr><td colspan=2><br><br>";











        $merge = array('text' => $text);

        //$merge = array_merge($merge, ['dis_serv_id' => $dis_serv_id_a, 'dis_conn_id' => $dis_conn_id_a]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }

    elseif($_POST['submit_each_services_attr'] != "")
    {
        $merge = array();
        $propid=mysqli_real_escape_string($mon3, $_POST['prop_id']);
        $type=mysqli_real_escape_string($mon3, $_POST['type']);
        $serv_attr=mysqli_real_escape_string($mon3, $_POST['serv_attr']);
        $sid=mysqli_real_escape_string($mon3, $_POST['sid']);
        
        $mon3->query("DELETE FROM service_attributes WHERE service_id=".$sid." AND name='".$serv_attr."'");
        proplog($propid,"Remove Service Attribute name ".$serv_attr." on service type <b>".$type."</b> on service number <b>".$sid."</b>");

        
        $msg ="Remove Service Attribute name ".$serv_attr." on service type <b>".$type."</b> on service number <b>".$sid."</b>....";

        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }
    


    elseif($_POST['services_suspended_all_date'] != "")
    {
        $conn_id = $_POST['conn_id'];

        $date_submit_disabled = $_POST['date_submit_disabled'];

        $prop_id = $_POST['prop_id'];

        $merge = array();
        $msg = "";
        
        $mon3->query("UPDATE connections set dis_services =\"2\" WHERE id=".$conn_id); 	
		
        $msg .= "<font color=green>All Services on connection number <b>".$conn_id."</b> was suspended on date <b>".$date_submit_disabled."</b></font>";

        proplog($prop_id,"All Services on connection number <b>".$conn_id."</b> was suspended on date <b>".$date_submit_disabled."</b>");


        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }

    elseif($_POST['services_disconnected_all_date'] != "")
    {
        $conn_id = $_POST['conn_id'];

        $date_submit_disconncted = $_POST['date_submit_disconncted'];

        $prop_id = $_POST['prop_id'];

        $remove_equipament = $_POST['remove_equipament'];



        $merge = array();
        $msg = "";

        $services_date_not_end = $mon3->query("SELECT * FROM services where date_end = '0000-00-00' AND connection_id=".$conn_id )->num_rows;

		if($services_date_not_end > 0)
		{
			$mon3->query("UPDATE connections set dis_services =\"0\" WHERE id=".$conn_id);
			$mon3->query("UPDATE services set date_end='".$date_submit_disconncted."' WHERE connection_id=".$conn_id);
		}	

        $msg .= "<font color=green>The connection number <b>".$conn_id."</b> was disconnected on date <b>".$date_submit_disconncted."</b></font>";

        proplog($prop_id,"The connection number <b>".$conn_id."</b> was disconnected on date <b>".$date_submit_disconncted."</b>");


                if($remove_equipament == 1)
                {
                    $conn_type_re_equip = $mon3->query("SELECT * FROM connections WHERE id=".$conn_id)->fetch_assoc();
                    proplog($prop_id,"The equipment <b>".$conn_type_re_equip['equip_id']."</b> was removed on connection <b>".$conn_id."</b>");
                    // UPDATE EQUIP NULL ON CONNECTIONS
                    $update_equip = $mon3->query("UPDATE connections set equip_id =\"\" WHERE id=".$conn_id);

                    // UPDATE EQUIP NULL ON SERVICES
                    $update_equip_services = $mon3->query("UPDATE services set equip_id =\"\" WHERE connection_id=".$conn_id );
                }


        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);



    }


    elseif($_POST['services_reactive_all_date'] != "")
    {
        $conn_id = $_POST['conn_id'];

        $date_submit_reactive = $_POST['date_submit_reactive'];

        $prop_id = $_POST['prop_id'];

        $merge = array();
        $msg = "";
        
        $mon3->query("UPDATE connections set dis_services =\"1\" WHERE id=".$conn_id); 	
		
        $msg .= "<font color=green>All Services on connection number <b>".$conn_id."</b> was reactivate on date <b>".$date_submit_reactive."</b></font>";

        proplog($prop_id,"All Services on connection number <b>".$conn_id."</b> was reactivate on date <b>".$date_submit_reactive."</b>");


        $merge = array_merge($merge, ['msg' => $msg]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }
    



    elseif($_GET['show_all_connections_prop']!="")
    {
        $prop_id = $_GET['prop_id'];
        $merge = array();
        $conns = $mon3->query("SELECT * FROM connections WHERE property_id = ".$prop_id);
        $text = "";
        $date_end_con = "";
        while($conn=$conns->fetch_assoc())
        {
            $conn_id = $conn['id'];
            $sus_but_serv_img_disabled="";
            $disconn_service_action_add_services="";
            $servs_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id'])->num_rows;
            $buutons_acts = "";
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
                        proplog($prop_id,"Activate connection <b>".$conn_id."</b> and services are activated to edit/create");
                    }
                }
            }


            if($dis_services == 1)
            {
                // SUSPENDED - BUTTON
                $buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
                $buutons_acts .= "<img width=50px src=img/suspended.jpg style=\"margin-top: 7px\" onclick=\"clickSuspendedServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\" class=\"img_mon ".$sus_but_serv_img."\" onmouseover=\"mouseOverSuspendedServices(".$conn['id'].");\" onmouseout=\"mouseOutSuspendedServices(".$conn['id'].");\"> &nbsp;";
                $buutons_acts .= "</span> ";
                // END SUSPENDED - BUTTON
                // WARNING SERVICE SUSPENDED
                $buutons_acts .= "<span id=title-sus-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";
                
                // DISCONNCTED - BUTTON
                $buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
                $buutons_acts .= "<img width=50px src=img/power-off.png onclick=\"clickDisconnectedServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\" class=\"img_mon ".$dis_button_serv_img."\" onmouseover=\"mouseOverDisconnectedServices(".$conn['id'].");\" onmouseout=\"mouseOutDisconnectedServices(".$conn['id'].");\">";
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
                $buutons_acts .= "<img width=50px src=img/power-button.png onclick=\"clickReactiveServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\" class=\"img_mon ".$rea_button_serv_img."\" onmouseover=\"mouseOverReactiveServices(".$conn['id'].");\" onmouseout=\"mouseOutReactiveServices(".$conn['id'].");\"> &nbsp;";
                $buutons_acts .= "</span> ";
                // END SUSPENDED - BUTTON
                // WARNING SERVICE SUSPENDED
                $buutons_acts .= "<span id=title-rea-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";

                // DISCONNCTED - BUTTON
                $buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
                $buutons_acts .= "<img width=50px src=img/power-off.png onclick=\"clickDisconnectedServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\"  class=\"img_mon ".$dis_button_serv_img."\" onmouseover=\"mouseOverDisconnectedServices(".$conn['id'].");\" onmouseout=\"mouseOutDisconnectedServices(".$conn['id'].");\">";
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

	        $text .= "<tr><td colspan=2><br><br>";

            if($conn['date_end'] != '0000-00-00')
            {
                $date_end_con .= "date end: <b>".$conn['date_end']."</b><br>";
            }

            if($conn['type']=="COAX")
            {
                $equip=$mon3->query("select * from coax_modem where UPPER(mac)= UPPER(\"".$conn['equip_id']."\")")->fetch_assoc();
                $card=$mon3->query("select name from coax_upstreams where cmts_id=\"".$equip['cmts']."\" and upstream_id=\"".$equip['interface']."\" ")->fetch_assoc();
                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type:".$conn['type']." <br>
                equip: <a href=?equip=".$conn['equip_id']."&equip_type=COAX>".$conn['equip_id']. "</a> 
                <a href=http://".$equip['mng_ip'].">". $equip['mng_ip']. "</a><br>
                CELL: <a href=?coax=1&upstream=".$equip['interface']."&cmts=".$equip['cmts'].">".$equip['interface']."-".$card['name']."</a><br>
                date start:".$conn['date_start']."<br>";
                $text .= $date_end_con;
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
                $text .= "<tr><td><b>status</b> ( by ".date("Y-m-d H:i:s ",$equip['status_timestamp'])."):<br>
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
                $equip=$mon3->query("select * from ftth_ont where fsan=\"".$conn['equip_id']."\"")->fetch_assoc();
                $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] . $equip['olt_id'];
                    $ont2=explode("-",$equip['ont_id']);
                    $olt=$mon3->query("select name from ftth_olt where id=\"".$equip['olt_id']."\"" )->fetch_assoc();
                    $pon=$mon3->query("select name from ftth_pons where olt_id=\"".$equip['olt_id']."\" and
                    card=\"".$ont2[1]."\" AND pon=\"".$ont2[2]."\"; ")->fetch_assoc();

                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type:".$conn['type']." <br>
                olt: ".$olt['name']." <br>
                PON: <a href=?gpon=1&pon=".$ont2[1]."-".$ont2[2]."&olt=".$equip['olt_id'].">".$pon['name']."</a><br>
                equip: <a href=?equip=".$conn['equip_id']."&equip_type=GPON>".$conn['equip_id']. "</a> 
                <a href=http://".$equip['mng_ip'].">".$equip['ont_id']. "</a><br>
                model: ".$equip['meprof']."<br>
                date start:".$conn['date_start']."<br>";
                $text .= $date_end_con;
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
                $text .= "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
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
                    $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] ." - ". $equip['antenna'];
                    $ant=$mon3->query("select * from fwa_antennas where id=\"".$equip['antenna']."\"" )->fetch_assoc();
                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type:".$conn['type']." <br>
                Antenna: <a href=?fwa=1&ant=".$ant['id'].">".$ant['name']." <br>
                equip: <a href=?equip=".$conn['equip_id']."&equip_type=fwa>".$conn['equip_id']. "</a> 
                <a href=http://".$equip['mng_ip'].">".$equip['mac']. "</a><br>
                model: ".$equip['model']."<br>
                date start:".$conn['date_start']."<br>";
                $text .= $date_end_con;
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
            }

            elseif($conn['type']=="ETH" || $conn['type']=="ETHF")
            {
                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type: ".$conn['type']." <br>";
                $text .= $date_end_con;
                if($conn['linked_prop']>0)
                {
                    $lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
                    $lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
                    $text .= "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
                }
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
            }
            elseif($conn['type']=="DARKF")
            {

                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type: ".$conn['type']." <br>";
                $text .= $date_end_con;
                if($conn['linked_prop']>0)
                {
                    $lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
                    $lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
                    $text .= "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
                }	
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";	
                
            }

            $serv_link= "";

            if($conn['equip_id'] == "" || $dis_services == 0 || $dis_services == 2)
            {
                $serv_link = "class=\"disabledLink\"";
            }
            //services


            $text .= " <tr><td><b>Services:</b>  <td align=center><a href=?servs=1&addserv=".$conn['id']." ".$serv_link."> <img width=60px src=img/packageadd.png></a>
            <tr><td><br>";

            if($servs_disabled_num > 0 && $servs_enabled_num > 0)
            {
                $click_dis_serv = "onclick=\"ShowAllServicesConn(".$conn['id'].",'".$conn['type']."','0','1');\"";
                $text .= "<input type=hidden id=click_en_serv-".$conn['id']." value=0>";
                $services = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end = '0000-00-00' order by id DESC");

            }
            else if($servs_disabled_num > 0 && $servs_enabled_num == 0)
            {
                $click_dis_serv = "onclick=\"ShowAllServicesConn(".$conn['id'].",'".$conn['type']."','1','0');\"";
                $text .= "<input type=hidden id=click_des_serv-".$conn['id']." value=0>";
                $services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.date_end DESC");
            }
            else if($servs_disabled_num == 0 && $servs_enabled_num > 0 || $servs_disabled_num == 0 && $servs_enabled_num == 0)
            {
                $click_dis_serv = "style=\"display: none\"";
                $services=$mon3->query("select * from services where connection_id=\"".$conn['id']."\" order by id DESC");
            }
            $text .= "<button id=dis_en_serv-".$conn['id']." type=button ".$click_dis_serv.">Show All Services</button>";

            $text .= "<table id=servs_lists-".$conn['id'].">";
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

                $text .= "<tr><td><br>
                <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
                </a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
                if($service['date_end']!="0000-00-00")
                {
                    $text .= "status: <font color=red><b> Disabled</b></font>"; 
                }
                $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);
                $text .= "<table>";
                while($att=$atts->fetch_assoc())
                {
                    if($att['name']=="account")
                    {
                        $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                        $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                        $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                    }
                    elseif($att['name']=="wifi_site")
                    {
                        $text1=explode("/",$att['value'])[5];
                        if($text1=="")
                            $text1=explode("/",$att['value'])[3];
                        $text .= " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text1."</a><br>";
                    }
                    elseif($att['name']=="unms_site")
                    {
                        $text .= " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                    }		
                    elseif($att['name']=="acs_site")
                    {
                        $text .= " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                    }	
                    elseif($att['name']=="speed")
                    {
                        $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                        $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                    }
                    else
                    {
                        $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value'].
                            "<br>";
                    }
                }
                if($service['type']=="INT")
                {
                    $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                    $fmac=substr($ip['mac'],0,8);
                    $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                    $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                    "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                }
                $text .= "</table>";
            }
            $text .= "</table>";
            $text .= "<tr><td colspan=2><br><br><tr><td colspan=2><br><br>";
        }
        $merge = array('text' => $text);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['show_connections_enabled_prop']!="")
    {
        $prop_id = $_GET['prop_id'];
        $merge = array();
        $conns = $mon3->query("SELECT * FROM connections WHERE property_id = ".$prop_id." AND date_end = '0000-00-00' ");

        $text = "";


        while($conn=$conns->fetch_assoc())
        {
            $conn_id = $conn['id'];
            $sus_but_serv_img_disabled="";
            $disconn_service_action_add_services="";
            $servs_num = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id'])->num_rows;
            $buutons_acts = "";
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
                        proplog($prop_id,"Activate connection <b>".$conn_id."</b> and services are activated to edit/create");
                    }
                }
            }


            if($dis_services == 1)
            {
                // SUSPENDED - BUTTON
                $buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
                $buutons_acts .= "<img width=50px src=img/suspended.jpg style=\"margin-top: 7px\" onclick=\"clickSuspendedServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\" class=\"img_mon ".$sus_but_serv_img."\" onmouseover=\"mouseOverSuspendedServices(".$conn['id'].");\" onmouseout=\"mouseOutSuspendedServices(".$conn['id'].");\"> &nbsp;";
                $buutons_acts .= "</span> ";
                // END SUSPENDED - BUTTON
                // WARNING SERVICE SUSPENDED
                $buutons_acts .= "<span id=title-sus-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";
                
                // DISCONNCTED - BUTTON
                $buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
                $buutons_acts .= "<img width=50px src=img/power-off.png onclick=\"clickDisconnectedServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\" class=\"img_mon ".$dis_button_serv_img."\" onmouseover=\"mouseOverDisconnectedServices(".$conn['id'].");\" onmouseout=\"mouseOutDisconnectedServices(".$conn['id'].");\">";
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
                $buutons_acts .= "<img width=50px src=img/power-button.png onclick=\"clickReactiveServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\" class=\"img_mon ".$rea_button_serv_img."\" onmouseover=\"mouseOverReactiveServices(".$conn['id'].");\" onmouseout=\"mouseOutReactiveServices(".$conn['id'].");\"> &nbsp;";
                $buutons_acts .= "</span> ";
                // END SUSPENDED - BUTTON
                // WARNING SERVICE SUSPENDED
                $buutons_acts .= "<span id=title-rea-services-".$conn['id']." class=\"warning-serv\" style=\"display: none;\"></span>";

                // DISCONNCTED - BUTTON
                $buutons_acts .= "<span id=span_conn_services_".$conn_id." ".$sus_but_serv_img_disabled.">";
                $buutons_acts .= "<img width=50px src=img/power-off.png onclick=\"clickDisconnectedServicesModal(".$conn_id.", ".$prop_id.",'".$date_now."');\"  class=\"img_mon ".$dis_button_serv_img."\" onmouseover=\"mouseOverDisconnectedServices(".$conn['id'].");\" onmouseout=\"mouseOutDisconnectedServices(".$conn['id'].");\">";
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

	        $text .= "<tr><td colspan=2><br><br>";

            if($conn['date_end'] != '0000-00-00')
            {
                $date_end_con .= "date end: <b>".$conn['date_end']."</b><br>";
            }

            if($conn['type']=="COAX")
            {
                $equip=$mon3->query("select * from coax_modem where UPPER(mac)= UPPER(\"".$conn['equip_id']."\")")->fetch_assoc();
                $card=$mon3->query("select name from coax_upstreams where cmts_id=\"".$equip['cmts']."\" and upstream_id=\"".$equip['interface']."\" ")->fetch_assoc();
                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type:".$conn['type']." <br>
                equip: <a href=?equip=".$conn['equip_id']."&equip_type=COAX>".$conn['equip_id']. "</a> 
                <a href=http://".$equip['mng_ip'].">". $equip['mng_ip']. "</a><br>
                CELL: <a href=?coax=1&upstream=".$equip['interface']."&cmts=".$equip['cmts'].">".$equip['interface']."-".$card['name']."</a><br>
                date start:".$conn['date_start']."<br>";
                $text .= $date_end_con;
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
                $text .= "<tr><td><b>status</b> ( by ".date("Y-m-d H:i:s ",$equip['status_timestamp'])."):<br>
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
                $equip=$mon3->query("select * from ftth_ont where fsan=\"".$conn['equip_id']."\"")->fetch_assoc();
                $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] . $equip['olt_id'];
                    $ont2=explode("-",$equip['ont_id']);
                    $olt=$mon3->query("select name from ftth_olt where id=\"".$equip['olt_id']."\"" )->fetch_assoc();
                    $pon=$mon3->query("select name from ftth_pons where olt_id=\"".$equip['olt_id']."\" and
                    card=\"".$ont2[1]."\" AND pon=\"".$ont2[2]."\"; ")->fetch_assoc();

                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type:".$conn['type']." <br>
                olt: ".$olt['name']." <br>
                PON: <a href=?gpon=1&pon=".$ont2[1]."-".$ont2[2]."&olt=".$equip['olt_id'].">".$pon['name']."</a><br>
                equip: <a href=?equip=".$conn['equip_id']."&equip_type=GPON>".$conn['equip_id']. "</a> 
                <a href=http://".$equip['mng_ip'].">".$equip['ont_id']. "</a><br>
                model: ".$equip['meprof']."<br>
                date start:".$conn['date_start']."<br>";
                $text .= $date_end_con;
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
                $text .= "<tr><td colspan=2><b>Status:</b>( by ".date("Y-m-d H:i:s ",$equip['status_timestamp']).") <br> 
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
                    $text .= "error4: ".mysqli_error($mon3).$conn['equip_id'] ." - ". $equip['antenna'];
                    $ant=$mon3->query("select * from fwa_antennas where id=\"".$equip['antenna']."\"" )->fetch_assoc();
                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type:".$conn['type']." <br>
                Antenna: <a href=?fwa=1&ant=".$ant['id'].">".$ant['name']." <br>
                equip: <a href=?equip=".$conn['equip_id']."&equip_type=fwa>".$conn['equip_id']. "</a> 
                <a href=http://".$equip['mng_ip'].">".$equip['mac']. "</a><br>
                model: ".$equip['model']."<br>
                date start:".$conn['date_start']."<br>";
                $text .= $date_end_con;
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
            }

            elseif($conn['type']=="ETH" || $conn['type']=="ETHF")
            {
                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type: ".$conn['type']." <br>";
                $text .= $date_end_con;
                if($conn['linked_prop']>0)
                {
                    $lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
                    $lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
                    $text .= "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
                }
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";
            }
            elseif($conn['type']=="DARKF")
            {

                $text .= "<tr><td>
                id: <a href=?props=1&conedit=".$conn['id'].">".$conn['id']."</a><br>
                type: ".$conn['type']." <br>";
                $text .= $date_end_con;
                if($conn['linked_prop']>0)
                {
                    $lprop=$mon3->query("select property_id from connections where id=\"".$conn['linked_prop']."\"")->fetch_assoc();
                    $lpropd=$mon3->query("select ref from properties where id=\"".$lprop['property_id']."\"")->fetch_assoc();
                    $text .= "linked_prop: <a href=?props=1&propid=".$lprop['property_id']." >".$lpropd['ref']."</a> con_id ".$conn['linked_prop']."<br>";
                }	
                $text .= "</td><td style=\"display: flex; justify-content: center;\">".$buutons_acts;
                $text .= "<td></tr>";	
                
            }

            $serv_link= "";

            if($conn['equip_id'] == "" || $dis_services == 0 || $dis_services == 2)
            {
                $serv_link = "class=\"disabledLink\"";
            }
            //services

            $text .= " <tr><td><b>Services:</b>  <td align=center><a href=?servs=1&addserv=".$conn['id']." ".$serv_link."> <img width=60px src=img/packageadd.png></a>
            <tr><td><br>";

            if($servs_disabled_num > 0 && $servs_enabled_num > 0)
            {
                $click_dis_serv = "onclick=\"ShowAllServicesConn(".$conn['id'].",'".$conn['type']."','0','1');\"";
                $text .=  "<input type=hidden id=click_en_serv-".$conn['id']." value=0>";
                $services = $mon3->query("SELECT * FROM services WHERE connection_id=".$conn['id']." AND date_end = '0000-00-00' order by id DESC");

            }
            else if($servs_disabled_num > 0 && $servs_enabled_num == 0)
            {
                $click_dis_serv = "onclick=\"ShowAllServicesConn(".$conn['id'].",'".$conn['type']."','1','0');\"";
                $text .=  "<input type=hidden id=click_des_serv-".$conn['id']." value=0>";
                $services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.date_end DESC");
            }
            else if($servs_disabled_num == 0 && $servs_enabled_num > 0 || $servs_disabled_num == 0 && $servs_enabled_num == 0)
            {
                $click_dis_serv = "style=\"display: none\"";
                $services=$mon3->query("select * from services where connection_id=\"".$conn['id']."\" order by id DESC");
            }
            $text .=  "<button id=dis_en_serv-".$conn['id']." type=button ".$click_dis_serv.">Show All Services</button>";



            $text .=  "<table id=servs_lists-".$conn['id'].">";


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

                $text .= "<tr><td><br>
                <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
                </a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
                if($service['date_end']!="0000-00-00")
                {
                    $text .= "status: <font color=red><b> Disabled</b></font>"; 
                }
                $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);
                $text .= "<table>";
                while($att=$atts->fetch_assoc())
                {
                    if($att['name']=="account")
                    {
                        $text .= " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                        $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                        $text .= " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                    }
                    elseif($att['name']=="wifi_site")
                    {
                        $text1=explode("/",$att['value'])[5];
                        if($text1=="")
                            $text1=explode("/",$att['value'])[3];
                        $text .= " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text1."</a><br>";
                    }
                    elseif($att['name']=="unms_site")
                    {
                        $text .= " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                    }		
                    elseif($att['name']=="acs_site")
                    {
                        $text .= " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                    }	
                    elseif($att['name']=="speed")
                    {
                        $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                        $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                    }
                    else
                    {
                        $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value'].
                            "<br>";
                    }
                }
                if($service['type']=="INT")
                {
                    $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                    $fmac=substr($ip['mac'],0,8);
                    $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                    $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                    "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
                }
                $text .= "</table>";
            }
            $text .= "</table>";
            $text .= "<tr><td colspan=2><br><br><tr><td colspan=2><br><br>";
        }
        $merge = array('text' => $text);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['show_all_services_enable_serv'] != "")
    {
        $conn_id = $_GET['conn_id'];
        $type = $_GET['type'];
        $text = '';
        $merge = array();
        $serv_link= "";


        $status_services = $mon3->query("SELECT dis_services FROM connections WHERE id=".$conn_id)->fetch_assoc();

        $dis_services = $status_services['dis_services'];


        $services=$mon3->query("select * from services where connection_id=\"".$conn_id."\" order by id DESC");
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

            $text .= "<tr><td><br>
            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
            </a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
            if($service['date_end']!="0000-00-00")
            {
                $text .= "status: <font color=red><b> Disabled</b></font>"; 
            }
            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


            $text .=  "<table>";
            while($att=$atts->fetch_assoc())
            {


            
                if($att['name']=="account")
                {
                    $text .=  " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .=  " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="wifi_site")
                {
                    $text1=explode("/",$att['value'])[5];
                    if($text1=="")
                        $text1=explode("/",$att['value'])[3];

                    $text .= " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text."</a><br>";
                }

                elseif($att['name']=="unms_site")
                {

                    $text .= " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }		
                elseif($att['name']=="acs_site")
                {

                    $text .= " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }	
                
                
                elseif($att['name']=="speed")
                {

                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                    
                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                }
                
                
                
                else
                {
                
                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                
                }
                
                
                
            }
            if($service['type']=="INT")
            {
                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                $fmac=substr($ip['mac'],0,8);
                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
            }
            
            
            $text .= "</table>";



        }
        $merge = array('text' => $text);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }


    elseif($_GET['enabled_serv_conn_all_services'] != "")
    {
        $conn_id = $_GET['conn_id'];
        $type = $_GET['type'];
        $text = '';
        $merge = array();
        $serv_link= "";


        $status_services = $mon3->query("SELECT dis_services FROM connections WHERE id=".$conn_id)->fetch_assoc();

        $dis_services = $status_services['dis_services'];


        $services=$mon3->query("select * from services where connection_id=\"".$conn_id."\" AND date_end = '0000-00-00' order by id DESC");
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

            $text .= "<tr><td><br>
            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
            </a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
            if($service['date_end']!="0000-00-00")
            {
                $text .= "status: <font color=red><b> Disabled</b></font>"; 
            }
            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


            $text .=  "<table>";
            while($att=$atts->fetch_assoc())
            {


            
                if($att['name']=="account")
                {
                    $text .=  " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .=  " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="wifi_site")
                {
                    $text1=explode("/",$att['value'])[5];
                    if($text1=="")
                        $text1=explode("/",$att['value'])[3];

                    $text .= " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text."</a><br>";
                }

                elseif($att['name']=="unms_site")
                {

                    $text .= " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }		
                elseif($att['name']=="acs_site")
                {

                    $text .= " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }	
                
                
                elseif($att['name']=="speed")
                {

                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                    
                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                }
                
                
                
                else
                {
                
                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                
                }
                
                
                
            }
            if($service['type']=="INT")
            {
                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                $fmac=substr($ip['mac'],0,8);
                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
            }
            
            
            $text .= "</table>";



        }
        $merge = array('text' => $text);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }
    
    elseif($_GET['disabled_serv_conn_all_services'] != "")
    {
        $conn_id = $_GET['conn_id'];
        $type = $_GET['type'];
        $text = '';
        $merge = array();
        $serv_link= "";


        $status_services = $mon3->query("SELECT dis_services FROM connections WHERE id=".$conn_id)->fetch_assoc();

        $dis_services = $status_services['dis_services'];


        $services=$mon3->query("select * from services where connection_id=\"".$conn_id."\" AND date_end != '0000-00-00' order by id DESC");
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

            $text .= "<tr><td><br>
            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
            </a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
            if($service['date_end']!="0000-00-00")
            {
                $text .= "status: <font color=red><b> Disabled</b></font>"; 
            }
            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


            $text .=  "<table>";
            while($att=$atts->fetch_assoc())
            {


            
                if($att['name']=="account")
                {
                    $text .=  " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .=  " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="wifi_site")
                {
                    $text1=explode("/",$att['value'])[5];
                    if($text1=="")
                        $text1=explode("/",$att['value'])[3];

                    $text .= " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text."</a><br>";
                }

                elseif($att['name']=="unms_site")
                {

                    $text .= " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }		
                elseif($att['name']=="acs_site")
                {

                    $text .= " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }	
                
                
                elseif($att['name']=="speed")
                {

                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                    
                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                }
                
                
                
                else
                {
                
                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                
                }
                
                
                
            }
            if($service['type']=="INT")
            {
                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                $fmac=substr($ip['mac'],0,8);
                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
            }
            
            
            $text .= "</table>";



        }
        $merge = array('text' => $text);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['disabled_service_each_type_conn'] != "")
    {
        $conn_id = $_GET['conn_id'];
        $type = $_GET['type'];
        $text = '';
        $merge = array();
        $serv_link= "";


        $status_services = $mon3->query("SELECT dis_services FROM connections WHERE id=".$conn_id)->fetch_assoc();

        $dis_services = $status_services['dis_services'];


		$services = $mon3->query("SELECT s1.* FROM services s1 WHERE s1.connection_id=".$conn_id." AND s1.date_end != '0000-00-00' and s1.id = (SELECT MAX(s2.id) FROM services s2 WHERE s2.connection_id=".$conn_id." AND s2.date_end != '0000-00-00' AND s1.type = s2.type) order by s1.date_end DESC");
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

            $text .= "<tr><td><br>
            <tr><td>type: ".$service['type']." <br>id: ".$tr_dis_services."<a href=?servs=1&sid=".$service['id'].">".$service['id']."
            </a>".$tr_dis_services_end."<br>started on: ".$service['date_start']." <br>";
            if($service['date_end']!="0000-00-00")
            {
                $text .= "status: <font color=red><b> Disabled</b></font>"; 
            }
            $atts=$mon3->query("select `id`, `service_id`, `name`, `value`, `date`, `notes` from service_attributes where service_id=".$service['id']);


            $text .=  "<table>";
            while($att=$atts->fetch_assoc())
            {


            
                if($att['name']=="account")
                {
                    $text .=  " <tr><td> &nbsp; ".$att['name']." <td>  ".$att['value']."<br>";
                    $nr=$mon3->query("select caller_id from voip_accounts where username=\"".$att['value']."\" ;")->fetch_assoc();
                    $text .=  " <tr><td> caller_id <td>  ".$nr['caller_id']."<br>";
                }

                elseif($att['name']=="wifi_site")
                {
                    $text1=explode("/",$att['value'])[5];
                    if($text1=="")
                        $text1=explode("/",$att['value'])[3];

                    $text .= " <tr><td>  &nbsp; wifi_site <td>  <a href=".$att['value'].">".$text."</a><br>";
                }

                elseif($att['name']=="unms_site")
                {

                    $text .= " <tr><td>  &nbsp; unms_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }		
                elseif($att['name']=="acs_site")
                {

                    $text .= " <tr><td>  &nbsp; acs_site <td>  <a href=".$att['value'].">".explode("/",$att['value'])[5]."</a><br>";
                }	
                
                
                elseif($att['name']=="speed")
                {

                    $speedname=$mon3->query("select name from int_services where id=\"".$att['value']."\"")->fetch_assoc();
                    
                    $text .= " <tr><td>  &nbsp; speed <td>  ".$speedname['name']." (usage: ". ($equip['total_traf_tx_month']+$equip['total_traf_rx_month'])."GB/m)<br>";
                }
                
                
                
                else
                {
                
                    $text .= " <tr><td> &nbsp;  ".$att['name']." <td>  ".$att['value']."<br>";
                
                }
                
                
                
            }
            if($service['type']=="INT")
            {
                $ip=$mon3->query("select datetime,ip,mac from history_ip where connection_id=\"".$conn['id']."\" order by datetime desc limit 0,1    ")->fetch_assoc();
                $fmac=substr($ip['mac'],0,8);
                $brand=$mon3->query("select brand from oui_lookup where mac LIKE \"$fmac\" ")->fetch_assoc();
                $text .= "<tr><td>Public_ip <td> <a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a> (at ". $ip['datetime'].")" .
                "<tr><td>Public_mac <td> <a href=?servs=1&type=IPs&mac=".$ip['mac'].">".$ip['mac']."</a>(".$brand['brand'].")"; 
            }
            
            
            $text .= "</table>";



        }
        $merge = array('text' => $text);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }
    
    

    

    


    






















    else{}

}






$mon3->close();
