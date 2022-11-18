<?php



// http://mon.lazertelecom.com/webservice.php?dump_serv=1&
// http://mon.lazertelecom.com/webservice.php?dump_onts=1&
// http://mon.lazertelecom.com/webservice.php?dump_prop=1&
// http://mon.lazertelecom.com/webservice.php?dump_leads=1&













require '/var/www/html/init_web.php';

$folder = dirname(__FILE__);

serverNameFolder($folder);

function clean($string) {
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
 
	return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
 }


//ini_set('display_startup_errors', 1);

session_start();

  
  
  
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





elseif($_GET['dump_onts']!="")
{





	$cons= $mon3->query("SELECT `fsan`,`serial`,ref,properties.id,connections.date_start FROM `ftth_ont` left join connections on ftth_ont.fsan=connections.equip_id left join properties on properties.id=connections.property_id WHERE 1 ORDER BY `connections`.`date_start` DESC");
	
$csv[]="fsan;serial;ref;connection_id;date_start";
		while($con=$cons->fetch_assoc())
		{	
		 	$csv[]=$con['fsan'].";".$con['serial'].";".$con['ref'].";http://mon.lazertelecom.com/index.php?props=1&propid=".$con['id'].";".$con['date_start'];
		}


			
			
			

header('Content-type: text/csv');
 header("Content-Disposition: attachment; filename=ONTs.csv");
 header("Pragma: no-cache");
 header("Expires: 0");
foreach($csv as $line)
	echo $line."\n";
  


}










elseif($_GET['dump_prop']!="")
{

	$props= $mon3->query("select * from properties ");
  echo $mon3->error;
		 if($props->num_rows>0)
		 {
		 
		while($prop=$props->fetch_assoc())
		{	
		$csv[]="\"".$prop['ref']."\",\"".$prop['address']."\",\"".$prop['ref']."@lazerspeed.com\"";
		
		
		}
		}

		
		
 header('Content-type: text/csv');
 header("Content-Disposition: attachment; filename=props.csv");
 header("Pragma: no-cache");
 header("Expires: 0");
foreach($csv as $line)
	echo $line."\n";

}









elseif($_GET['dump_leads']!="")
{
$csv[]="\"id\",  \"address\", \"freguesia\", \"coords\", \"name\", \"email\", \"phone\", \"agent_id\", \"status\", \"prop_id\", \"date_lead\", \"date_viability\", \"date_accept\", \"date_quoted\", \"date_papwk\", \"date_book\", \"date_install\", \"date_installed\", \"date_closed\", \"date_modified\", \"drop_length\", \"FAT_coords\", \"ORAC_pits\", \"ORAP_poles\", \"connection_cost\", \"is_network_ready\", \"network_cost\", \"estimated_quote\", \"timeframe\", \"quoted\", \"contract_id\", \"con_type\", \"fwa_id\", \"olt_id\", \"internet_prof\", \"fixed_ip\", \"tv\", \"phone1\", \"phone2\", \"aps\", \"install_price\", \"monthly_price\", \"model\", \"is_changeover\", \"prev_rev_month\", \"notes\", \"ORAP_id\", \"ORAC_id\", \"networking_job_id\", \"installation_job_id\", \"technician\", \"final_netw_cost\", \"final_inst_cost\", \"NPS_score\", \"manager_score\", \"has_pictures\", \"speedtest\", \"created_by\", \"is_active\"";

	$props= $mon3->query("SELECT `id`, `address`, `freguesia`, `coords`, `name`, `email`, `phone`, `agent_id`, `status`, `prop_id`, `date_lead`, `date_viability`, `date_accept`, `date_quoted`, `date_papwk`, `date_book`, `date_install`, `date_installed`, `date_closed`, `date_modified`, `drop_length`, `FAT_coords`, `ORAC_pits`, `ORAP_poles`, `connection_cost`, `is_network_ready`, `network_cost`, `estimated_quote`, `timeframe`, `quoted`, `contract_id`, `con_type`, `fwa_id`, `olt_id`, `internet_prof`, `fixed_ip`, `tv`, `phone1`, `phone2`, `aps`, `install_price`, `monthly_price`, `model`, `is_changeover`, `prev_rev_month`, `notes`, `ORAP_id`, `ORAC_id`, `networking_job_id`, `installation_job_id`, `technician`, `final_netw_cost`, `final_inst_cost`, `NPS_score`, `manager_score`, `has_pictures`, `speedtest`, `created_by`, `is_active` FROM `property_leads`");
  echo $mon3->error;
		 if($props->num_rows>0)
		 {
		 
		while($prop=$props->fetch_assoc())
		{	
		
		
		$csv[]="\"".$prop['id']."\",\"".$prop[ 'address']."\",\"".$prop[ 'freguesia']."\",\"".$prop[ 'coords']."\",\"".$prop[ 'name']."\",\"".$prop[ 'email']."\",\"".$prop[ 'phone']."\",\"".$prop[ 'agent_id']."\",\"".$prop[ 'status']."\",\"".$prop[ 'prop_id']."\",\"".$prop[ 'date_lead']."\",\"".$prop[ 'date_viability']."\",\"".$prop[ 'date_accept']."\",\"".$prop[ 'date_quoted']."\",\"".$prop[ 'date_papwk']."\",\"".$prop[ 'date_book']."\",\"".$prop[ 'date_install']."\",\"".$prop[ 'date_installed']."\",\"".$prop[ 'date_closed']."\",\"".$prop[ 'date_modified']."\",\"".$prop[ 'drop_length']."\",\"".$prop[ 'FAT_coords']."\",\"".$prop[ 'ORAC_pits']."\",\"".$prop[ 'ORAP_poles']."\",\"".$prop[ 'connection_cost']."\",\"".$prop[ 'is_network_ready']."\",\"".$prop[ 'network_cost']."\",\"".$prop[ 'estimated_quote']."\",\"".$prop[ 'timeframe']."\",\"".$prop[ 'quoted']."\",\"".$prop[ 'contract_id']."\",\"".$prop[ 'con_type']."\",\"".$prop[ 'fwa_id']."\",\"".$prop[ 'olt_id']."\",\"".$prop[ 'internet_prof']."\",\"".$prop[ 'fixed_ip']."\",\"".$prop[ 'tv']."\",\"".$prop[ 'phone1']."\",\"".$prop[ 'phone2']."\",\"".$prop[ 'aps']."\",\"".$prop[ 'install_price']."\",\"".$prop[ 'monthly_price']."\",\"".$prop[ 'model']."\",\"".$prop[ 'is_changeover']."\",\"".$prop[ 'prev_rev_month']."\",\"".$prop[ 'notes']."\",\"".$prop[ 'ORAP_id']."\",\"".$prop[ 'ORAC_id']."\",\"".$prop[ 'networking_job_id']."\",\"".$prop[ 'installation_job_id']."\",\"".$prop[ 'technician']."\",\"".$prop[ 'final_netw_cost']."\",\"".$prop[ 'final_inst_cost']."\",\"".$prop[ 'NPS_score']."\",\"".$prop[ 'manager_score']."\",\"".$prop[ 'has_pictures']."\",\"".$prop[ 'speedtest']."\",\"".$prop[ 'created_by']."\",\"".$prop[ 'is_active']."\" "; 
		
		
		
		

		}
		}

		
		
 header('Content-type: text/csv');
 header("Content-Disposition: attachment; filename=props.csv");
 header("Pragma: no-cache");
 header("Expires: 0");
foreach($csv as $line)
	echo $line."\n";

}



elseif($_GET['downloadgponlivestatus']!="")
{
//webservice.php?downloadgponlivestatus=1&olt=$olt&pon=$pon
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
  
  
  
  
  
  
  
// JSON / webservice from here below  
  
  
  
header('Content-type: text/html; charset=UTF-8'); 
if($_GET['ponsbyolt']!="")
{
  $olt=mysqli_real_escape_string($mon3, $_GET['ponsbyolt']);  
  
  $pons=$mon3->query("select card,pon,name from ftth_pons where olt_id=\"$olt\" order by name ");
  echo $mon3->error;
 // echo $olt;

	while($pon=$pons->fetch_assoc())
	{
		$ponb[]=array($pon['card']."-".$pon['pon'],$pon['name']);
	}

	//var_dump($ponb);
	echo json_encode($ponb, JSON_UNESCAPED_UNICODE);
}  

elseif($_GET['cmtsbyolt']!="")
    {
        $cmts=mysqli_real_escape_string($mon3, $_GET['cmtsbyolt']);

        $cm=$mon3->query("select * from coax_cmts order by name ");
        echo $mon3->error;
        // echo $olt;

        while($c=$cm->fetch_assoc())
        {
            $cb[]=array($c['name'],$c['id']);
        }

        //var_dump($ponb);
        echo json_encode($cb, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['ethbyolt']!="")
    {
        $eth=mysqli_real_escape_string($mon3, $_GET['ethbyolt']);

        $e=$mon3->query("select properties.ref,connections.id,properties.address,connections.type from properties left join connections on properties.id=connections.property_id where connections.type='ETH' order by properties.ref");
        echo $mon3->error;
        // echo $olt;

        while($e_s=$e->fetch_assoc())
        {
            $eb[]=array($e_s['ref']." ".$e_s['type'],$e_s['id']);
        }

        //var_dump($ponb);
        echo json_encode($eb, JSON_UNESCAPED_UNICODE);
    }
  
  
  
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
    
  
  
  
  
  
elseif($_GET['customer']!="")
{
  $cust=mysqli_real_escape_string($mon3, $_GET['customer']);  
  $mng=mysqli_real_escape_string($mon3, $_GET['mng']); 
  $query="select id,name,email,fiscal_nr from customers where ( name LIKE \"%$cust%\" OR email LIKE \"%$cust%\" OR fiscal_nr  LIKE \"%$cust%\" ) ";
  if ($mng==1)
	  $query .= "AND is_management=1 ";
  
    $fregs=$mon3->query($query." order by name limit 0,10");
	//echo $query . mysqli_error($mon3);
	
	while($freg=$fregs->fetch_assoc())
	{
		//echo $freg['id'].$freg['name'];
		$custb[]=array_map('utf8_encode',$freg);
	}

	//var_dump($frg);
	echo json_encode($custb, JSON_UNESCAPED_UNICODE);
} 
  
  
  
  
  
elseif($_GET['getfreg']!="")
{
  $conc=mysqli_real_escape_string($mon3, $_GET['conc']);
 // echo "list fregs $conc";
  $fregs=$mon3->query("select id,freguesia from freguesias where concelho=$conc order by freguesia");
	while($freg=$fregs->fetch_assoc())
	{
		//echo $freg['id'].$freg['freguesia'];
		$frg[]=array_map('utf8_encode',$freg);
	}

//	var_dump($frg);
	echo json_encode($frg, JSON_UNESCAPED_UNICODE);
} 
  
  
  
  
  
elseif($_GET['searchprop']!="")
{
	$str=mysqli_real_escape_string($mon3, $_GET['searchprop']);
	if(isset($_GET['offset']))
		$offset=mysqli_real_escape_string($mon3, $_GET['offset']);
	else
		$offset=0;
	
	if(strlen($str)<2)
	{
		$props=$mon3->query("select id,ref,address from properties order by ref limit ".$offset.",30 ");
		$count=$mon3->query("select count(*) from properties")->fetch_row();
	}else
	{
		$props=$mon3->query("select id,ref,address from properties where address LIKE '%".$str."%' or ref LIKE '%".$str."%' order by ref limit 0,30 ");
		$count=$mon3->query("select count(*) from properties where address LIKE '%".$str."%' or ref LIKE '%".$str."%'")->fetch_row();
	}
//	echo "error: ".mysqli_error($mon3);
	$props_array[0]= array("id"=>"total", "ref"=> $count[0], "address"=>"");
	while($prop=$props->fetch_assoc())
	{
		array_push($props_array, $prop);
	}
//	print_r($props_array);
echo json_encode($props_array);
}





elseif($_GET['searchlead']!="")
{
	$str=mysqli_real_escape_string($mon3, $_GET['searchlead']);
//	echo "searching for:".$str;
	if(isset($_GET['offset']))
		$offset=mysqli_real_escape_string($mon3, $_GET['offset']);
	else
		$offset=0;
	
	if(strlen($str)<2)
	{
		$props=$mon3->query("select * from property_leads order by date limit ".$offset.",30 ");
		$count=$mon3->query("select count(*) from property_leads")->fetch_row();
	}else
	{
		$props=$mon3->query("select * from property_leads where address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%' order by date limit 0,30 ");
		$count=$mon3->query("select count(*) property_leads where address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%' order by date ")->fetch_row();
	}
//	echo "error: ".mysqli_error($mon3);
	$props_array[0]= array("id"=>"total", "address"=> $count[0]);
	while($prop=$props->fetch_assoc())
	{
		array_push($props_array, $prop);
	}
//	print_r($props_array);
echo json_encode($props_array);
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
/*	
	if(!($con = ssh2_connect($olt_ip['ip'], 22)))
	{
		 $msg.="fail: OLT busy - unable to establish connection to ".$olt_ip['ip']." <br>";
	} 
	else 
	{
    // try to authenticate with username root, password secretpassword
		if(!ssh2_auth_password($con, "admin", "zhone")) {
			echo "fail: unable to authenticate\n";
		} else 
		{

 //       echo "okay: logged in...\n";

		
		
		if (!($shell = ssh2_shell($con, 'vt102', null, 80, 40, SSH2_TERM_UNIT_CHARS))) {
           $msg.= "fail: unable to set char shell<br>";
			} 
		else
		{
			//		echo "waiting for shell.. \n";
		
			sleep(3);
			do{
				fwrite( $shell, PHP_EOL);
				$buf=fgets($shell,4096);
//				echo $buf;
				sleep(1);
			}while(strpos($buf,"SH")>0);
















/*
	echo "ttttttttttttttttt";


if(!($shell = fsockopen($olt_ip['ip'], 23, $errno, $errstr, 10)))
{
	$msg.= "failed to connect telnet ".$olt_ip['ip']."  ".$errno. $errstr;
	echo $msg;
}	
else{
	echo "con done";
	fputs ($shell, "\r\n");
	$l=fgets($shell, 128);
	sleep(2);
	fputs ($shell, "admin".PHP_EOL);
	$l=fgets($shell, 128);
	echo $l;
	sleep(2);
	fputs ($shell, "zhone".PHP_EOL);
	sleep(2);
	$l=fgets($shell, 128);
	echo $l;
	while(!strpos($l,"SH>"))
	{
		$l=fgets($shell, 128);
		echo $l;
		fputs ($shell, PHP_EOL);
	}
	$l=fgets($shell, 128);

if(!strpos($l,"SH>"))
{
	$msg.= "failed to autenticate";
	
}
else{
	
	if(1){

*/	

/*

			

		
		$res=array();
		
		
		
//		echo "connected! \n";

		
		
		
		fwrite( $shell, 'onu status '.$ont.PHP_EOL);
		do{
			$buf = fgets($shell,4096);
//			echo $buf;
			
				$l=trim($buf);
				if(is_numeric($l[0]))
				{
					$l=preg_replace('/\s+/', ' ',$l);
					$res[]=array($olt['id'],$l);
				}
				else
				{
					$l=trim(preg_replace('/[^A-Za-z0-9- .]/', '', $l )); 
					if(is_numeric($l[0]))
					{
						$l=preg_replace('/\s+/', ' ',$l);
						$res[]=array($olt['id'],$l);
					}
						
				}
				

			
			usleep(100000);
		}while(strpos($buf,"SH>")=== false);
		

		$resb=explode(" ",$res[0][1]);
		$status['status']=$resb[2]."-".$resb[10];
		$status['config']=$resb[3];
		$status['download']=$resb[4];
		$status['rx']=$resb[5];
		$status['tx']=$resb[7];
		$status['dist']=$resb[9];
		

		
		
		fwrite( $shell, 'bridge show '.$ont." vlan 15 slot ".$ont_a[1].PHP_EOL);
		do{
			$buf = fgets($shell,4096);
//			echo $buf;
			
				$l=trim($buf);
				if($l[0]=="D")
				{
					$ipm=explode("D ",$l[0]);
					$status['ipm']=$ipm[0];
				}

			usleep(100000);
		}while(strpos($buf,"SH>")=== false);
		
		
		

		
		
		
		
		fwrite( $shell, 'exit'.PHP_EOL);
		ssh2_disconnect ($shell);
		fclose($shell);
		}
	
		}
//		
	}	
	
	
	
Name/OID: .1.3.6.1.2.1.1.3.0; Value (TimeTicks): 27 hours 41 minutes 59 seconds (9971967)
Name/OID: .1.3.6.1.4.1.5504.2.5.2.1.1.1; Value (OctetString): ZNID-GPON-2427A1-EU

Name/OID: .1.3.6.1.4.1.5504.2.5.2.1.1.21; Value (OctetString): CFE=1.0.38-118.5 (4.1.224)

Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.2.1.1.3.11.1; Value (IpAddress): 192.168.1.1
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.2.1.1.4.11.1; Value (IpAddress): 255.255.255.0
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.1.1.5.11; Value (IpAddress): 192.168.1.10
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.1.1.6.11; Value (IpAddress): 192.168.1.100

Name/OID: .1.3.6.1.4.1.5504.2.5.46.1.2.1.6.1.1; Value (OctetString): Lazer_304
Name/OID: .1.3.6.1.4.1.5504.2.5.46.1.3.1.4.1.1; Value (OctetString): lzr060esp

Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.2.1.1.3.7.1; Value (IpAddress): 172.19.1.33
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.6.1; Value (OctetString): brvlan10
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.6.2; Value (IpAddress): 172.19.6.34

Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.1.1.2.1; Value (OctetString): brvlan10
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.1.1.3.1; Value (IpAddress): 172.19.2.63


Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.2.1.1.3.13.1; Value (IpAddress): 10.211.255.192
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.3.2.1.1.3.14.1; Value (IpAddress): 10.200.233.216

Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.2.1.1.1; Value (OctetString): Enabled
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.2.1.1.2; Value (OctetString): Disabled
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.3.1.1.1; Value (OctetString): 4042
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.3.1.1.2; Value (OctetString): 2001
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.4.1.1.1; Value (OctetString): Up
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.4.1.1.2; Value (OctetString): Disabled
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.5.1.1.1; Value (OctetString): Idle
Name/OID: .1.3.6.1.4.1.5504.2.5.40.1.3.8.1.1.5.1.1.2; Value (OctetString): Idle

Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.6.1; Value (OctetString): eth0.v100
Name/OID: .1.3.6.1.4.1.5504.2.5.41.1.6.2; Value (IpAddress): 89.31.228.217

Name/OID: .1.3.6.1.4.1.5504.2.5.43.1.2.1.7.1; Value (OctetString): -22.84 dBm
Name/OID: .1.3.6.1.4.1.5504.2.5.43.1.2.1.12.1; Value (OctetString): -1.51 dBm
Name/OID: .1.3.6.1.4.1.5504.2.5.43.1.2.1.10.1; Value (OctetString): 53.8 C (128.8 F)	
Name/OID: .1.3.6.1.4.1.5504.3.1.21.1.3.1.1.3; Value (OctetString): ZNTS0372579B	
	
	
	
	
	
	
	
	
	
	
	
	*/
	
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

// RECONNECTION

    // Propriedades do tipo de conxao correspondente

    // functions.js - chg_type_conn_not_check(con_type)
    // VERIFICAR AS CONEXOES QUE TEM SERVIÇOS ATIVOS E DESATIVOS - LEAD 7 - RECONNECTION

	

    elseif($_GET['prop_conn_type'] != '')
    {
        $wq='';
        if($_GET['con_type'] != "" )
        {
            $wq .= "connections.type = '".$_GET['con_type']."'";
        }
        

        $conn_di=$mon3->query("SELECT DISTINCT properties.id as 'prop_id',
        properties.ref,properties.address,connections.id as 'conn_id' 
        from properties 
        INNER JOIN connections on connections.property_id=properties.id 
        INNER JOIN services on services.connection_id=connections.id 
        WHERE 1 AND ".$wq." AND connections.date_end = '0000-00-00' order by properties.ref");

        while($conn_di_each=$conn_di->fetch_assoc())
        {
            $coa[]=array($conn_di_each['ref']."-".clean($conn_di_each['address']),$conn_di_each['prop_id'],$conn_di_each['conn_id']);
           
        }
        $merge = array('prop_conn_type' => $coa);
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }






















else{}

}






$mon3->close();
