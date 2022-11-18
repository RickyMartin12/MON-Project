<?php
error_reporting("E_ALL");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("log_errors", 1);
ini_set('error_log',"/var/log/php-scripts.log");
ini_set("memory_limit","200M");
error_log( "Hello, errors!" );
echo "starting.."."<br>";
$tsp=time();

$updatemon=0;


$olt=1;
if(isset($_GET['olt']))
 $olt=$_GET['olt'];
 
$pon="";
if(isset($_GET['pon']))
 $pon=$_GET['pon'];
 
  

if(isset($_GET['newpon']))
 $newpon=$_GET['newpon'];
 
 
$newolt=7;
if(isset($_GET['newolt']))
 $newolt=$_GET['newolt'];
 
 
 
 
 
$register=0;
if(isset($_GET['register']))
 $register=$_GET['register'];












$mon= mysqli_connect("127.0.0.1","system","lazerx0!","mon");
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
} 
else
{
	echo "mysql success<bR>";
}














function gpon_register_ont($equip,$npon,$reg,$nolt){
global $mon;
	$commands=array();


	$con=$mon->query("select * from connections where equip_id=\"$equip\"");
	if($con->num_rows>1) echo "Caution: theres more than one connection with this ONT";
	$con=$con->fetch_assoc();
	$prop=$mon->query("select * from properties where id=\"".$con['property_id']."\"")->fetch_assoc();
	$ont=$mon->query("select * from ftth_ont where fsan=\"$equip\"")->fetch_assoc();
	
	echo "<br>Address: <a href=http://mon.lazertelecom.com/index.php?props=1&propid=".$prop['id'].">". $prop['address']."</a><br>";
	$ontid=$ont['ont_id'];
	echo "original ONT id: $ontid<br>";
	
	$ontid1=explode("-",$ont['ont_id']);
	$pon=explode("-",$npon);
	
	$ontid3=$pon[1]."/".$pon[2]."/".$ontid1[3];
$newolt_id=8;	
//$newolt_id=$ont['olt_id'];
	
	
	echo "new ONT id: $ontid3<br>
	new Olt id: $newolt_id<br>
	";	
	
	
	
	$olt=$mon->query("select ip from ftth_olt where id=\"$newolt_id\" ;")->fetch_assoc();

	
	echo "
<br><br>
OLT: ".$olt['ip']."<br>";

$commands[]="gpononu set $ontid3 vendorid ZNTS serno fsan ".substr($ont['fsan'],4)." meprof ".$ont['meprof'];
$commands[]="cpe system add $ontid3";
$commands[]="cpe rf add $ontid3 admin-state down";
$commands[]="bridge add 1/".$ontid3."/gpononu gem 15".sprintf("%02d",$ontid1[3])." gtp 1 epktrule 1 ipktrule 1 downlink vlan 15 tagged mgmt rg-bridged";
$commands[]="cpe rg wan modify $ontid3 vlan 15 ip-com-profile 6";

$srvs=$mon->query("select * from services where connection_id=\"".$con['id']."\" and date_end=\"0000-00-00\"");
while($srv=$srvs->fetch_assoc())
{
	
	$srvatts=$mon->query("select * from service_attributes where service_id=\"".$srv['id']."\" ");
	while($srvatt=$srvatts->fetch_assoc())
	{
		$atts[$srvatt['name']]=$srvatt['value'];
	}

//echo " dump for srv  ".$srv['id']." <br>";	
//var_dump($atts);	
	

	
	
	if($srv['type']=="TV")
	{
		echo "<br>TV srv ".$srv['id']."<br>";
		$commands[]="cpe rf modify $ontid3"."/1 admin-state up";
		
	}
	

	
	elseif($srv['type']=="INT")
	{
		echo "<br>INT  srv ".$srv['id']." <br>";
		$speed=$mon->query("select * from int_services where id= \"".$atts['speed']."\"")->fetch_assoc();
		
		
		
		if($atts['is_router']==1)
		{
$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 1 rg-brouted";
$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 2 rg-brouted ";
$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 3 rg-brouted ";
$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 4 rg-brouted";
		}
		else
		{
			$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule " . $speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth ".$atts['bridge_port']." rg-bridged";
		}
		
		
		
		
		
		
		if($atts['wifi']==1)
		{
$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged wlan 1 rg-brouted ";
$commands[]="cpe wlan add ".$ontid3."/1 admin-state up ssid \"".$atts['wifi_ssid']."\" encrypt-key ".$atts['wifi_key']." wlan-com-profile 4 wlan-com-adv-profile 2";
			
			if($ont['meprof']=="zhone-2727a" || $ont['meprof']=="zhone-2428")
			{
$commands[]="bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid1[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged wlan 5 rg-brouted ";
$commands[]="cpe wlan add ".$ontid3."/5 admin-state up ssid \"".$atts['wifi_ssid']."\" encrypt-key ".$atts['wifi_key']." wlan-com-profile 4";
			}
		
		
		}
		else
		{
			$commands[]="cpe wlan add ".$ontid3."/1 admin-state down";
			if($ont['meprof']=="zhone-2727a" || $ont['meprof']=="zhone-2428")  $commands[]="cpe wlan add ".$ontid3."/5 admin-state down";
		}
		
		
		
		if($atts['ip_address']!="" || $atts['dhcp_id']!="")
		{
			$cmdv="cpe rg lan modify $ontid3 eth 1 "; 
			if($atts['ip_address']!="") $cmdv.=" ip-addr ".$atts['ip_address'];
			if($atts['dhcp_id']!="") $cmdv.=" dhcp-server-profile ".$atts['dhcp_id'];
$commands[]=$cmdv;
	
		}
		if($atts['portfw_id']!=""){
$commands[]="cpe rg wan modify $ontid3 vlan ".$atts['vlan']." port-fwd-list-profile ".$atts['portfw_id'];
			
		}

		if($atn['dyndns']==1)
					{
					
					}

					
		
	}
	
	
	
	
	
	elseif($srv['type']=="PHN")
	{
	echo "<br>PHN  srv ".$srv['id']."<br>";
	
		if($atts['account']>0)
		{
$commands[]="bridge add 1/".$ontid3."/gpononu gem 7".sprintf("%02d",$ontid1[3])." gtp 1 epktrule 1 ipktrule 1 downlink vlan 10 tagged sip rg-bridged";$commands[]="cpe ip add $ontid3 voip ip-com 2";

			$pass=$mon->query("select password from voip_accounts where username=\"".$atts['account']."\" ;")->fetch_assoc();
			
$commands[]="cpe voip add ".$ontid3."/1 admin-state up dial-number ".$atts['account']." username ".$atts['account'].
" password ".$pass['password']." voip-server-profile 1";
		}
	}

			
	}	

$curdate=date("Y-m-d H:i:s");	
foreach ($commands as $command	)
{
	 $command = $mon->real_escape_string($command);
	echo $command ."<br>";
	if($reg==1)
	{
		$mon->query("insert into olt_command_buf (olt_id,command,date_subm,user) values (\"$nolt\",\"$command\",\"$curdate\",\"carlosr\"); ");
		echo $mon->error;
	}
}
	
	
} //end of function register



















echo "<html><body><br><br>
olt $olt<br>
pon $pon<br>
<br>
newolt $newolt<br>
newpon $newpon<br>


<table border=1 width=1600px  style=\" word-break: break-all;\">
<tr><th width=200px> OLT backup file<th width=300px>current MON <th width=100px>to update mon<th width=1000px>register commands</tr>";
//limit 100,50


$q="select * from olt_check where olt_id=$olt ";

if($pon!="")
{$q.= " and ont_id LIKE \"$pon"."%\" ";}


$oltb=$mon->query($q);


while($olt_ont=$oltb->fetch_assoc())
{



	$ont=$olt_ont['ont_id'];
	$fsan=$olt_ont['fsan'];
	  echo "<tr><td> ";
	
//#####################  print OLT config	
	
	
	foreach($olt_ont as $key => $row)
		echo $key . ': ' . $row . "<br>";

	
//################### now MON


	
	
	if(!$mon_ont=$mon->query("select * from ftth_ont where ont_id=\"$ont\" and olt_id=\"$olt\" ")->fetch_assoc())
		echo "<td><font color=red><b>NO ONT on MON for \"$ont\" </b></font><br><br>";
	else
		echo "<td>";
		

	foreach($mon_ont as $key => $row)
		echo $key . ': ' . $row . "<br>";
	
	if($fsan!=$mon_ont['fsan'])
	{
		echo "<font color=red><b>FSAN mismatch!! MON has ".$mon_ont['fsan']." mon has $fsan</b></font><br>";
		$fsanmismatch++;
	}	
	else
	{
// update ONT settings	
// 	$mon->query("update ftth_ont set model=\"".$olt_ont['model']."\", where fsan=\"".$mon_ont['fsan']."\" "); 
	}

	
	if($olt_ont['meprof']!=$mon_ont['meprof'] || $olt_ont['model']!=$mon_ont['model'])
	{
		echo "<font color=red><b>model mismatch!! MON has ".$mon_ont['model']."</b></font><br>";
		
		$mon->query("update ftth_ont set model=\"".$olt_ont['model']."\",meprof=\"".$olt_ont['meprof']."\" where fsan=\"$fsan\" ");
 		$meprofmismatch++;
	}	


	
	$connection=$mon->query("select * from connections where equip_id=\"".$mon_ont['fsan']."\" ");
	echo $mon->error;
	if($connection->num_rows==0)
	{	echo "<font color=red><b> Connection mismatch!! on MON</b></font><br>";
		$conmismatch++;
	}
	else
	{
	$connection=$connection->fetch_assoc();

	echo "<br> <b>Connection: <br>";
//print mon connection
		foreach($connection as $key => $row)
		echo $key . ': ' . $row . "</b><br> ";








// print mon address

	$prop=$mon->query("select address from properties where id=\"".$connection['property_id']."\" ")->fetch_assoc();
		echo $mon->error;
	echo "<br> address: ".$prop['address']."<br>";
	

//print services/attributes
	














if($olt_ont['vlan']!=10 && $olt_ont['vlan']!="") //vlan for internet
{
	
	$int_services=$mon->query("select * from service_attributes left join services on service_attributes.service_id=services.id 
	where services.connection_id=\"".$connection['id']."\" and date_end=\"0000-00-00\" and  service_attributes.name=\"vlan\" 
	and value=\"".$olt_ont['vlan']."\"    ");
	echo $mon->error;
		
		
	if($int_services->num_rows!=1)
	{
		echo "<font color=red><b> vlan service mismatch!!</b></font><br>".$olt_ont['vlan']."-". $attn['vlan'] ."<br>";
		$vlanmismatch++;
	}
	else
	{
		
	$int_services=$int_services->fetch_assoc();
//	var_dump($int_services);
	$sservice=$mon->query("select * from services where id=\"".$int_services['service_id']."\" " )->fetch_assoc(); 
	$sattribues=$mon->query("select * from service_attributes where service_id=\"".$int_services['service_id']."\" " ); 
	
	
//	var_dump($sservice);
		echo "<br>Service ";
		foreach($sservice as $key => $row)
		{
		if($key=="id")
			echo "id : <a href=https://mon.lazertelecom.com/index.php?servs=1&sid=$row>$row</a><br>";
		else
			echo $key . ': ' . $row . "<br>";
		}
	
	
//	var_dump($sattribues);
	
		$attn=array();
		while($att=$sattribues->fetch_assoc())
		{
			echo "&nbsp;&nbsp;&nbsp;".$att['name'].":". $att['value']."<br>";
			$attn[$att['name']]=$att['value'];
		}	
	
	
	

	
		if($sservice['type']=="INT" and $olt_ont['vlan']!=10 and $olt_ont['vlan']== $attn['vlan'])
		{
		
		

			//speeds
				
				
			$speeds=$mon->query("select prof_up,prof_down from int_services where id=\"".$attn['speed']."\" ")->fetch_assoc();
			
			if($speeds['prof_up']!= $olt_ont['bwup'] or $speeds['prof_down']!= $olt_ont['bwdown'])
			{	echo "<font color=red><b> speed service mismatch!!</b></font> ".  $speeds['prof_down']."/". $speeds['prof_up'] ."-". $olt_ont['bwdown'] ."/". $olt_ont['bwup']."       <br>";
						$speedmismatch++;
			}	
			//rg router bridge
				
			if(($olt_ont['bridge_mode']=="brouted" and $attn['is_router']==0) or ($olt_ont['bridge_mode']=="bridged" and $attn['is_router']==1))
			{	echo "<font color=red><b> router mode mismatch!!</b></font><br>";
				$routermismatch++;
	
			}
			//wifi settings

			if(($olt_ont['wifi1_status']=="up" and $attn['wifi']!=1)or($olt_ont['wifi1_status']=="" and $attn['wifi']!=0))
			{
				echo "<font color=red><b> wifi status  mismatch!!</b></font>
				updating mon now with OLT settings...<br>";
				
				if($olt_ont['wifi1_status']=="up") $nwifi=1; 	else $nwifi=0;
				
				$mon->query("update service_attributes set value=$nwifi	where name=\"wifi\" and service_id=\"".$int_services['service_id']."\"");
				echo $mon->error;
			
			}

			if($olt_ont['wifi1_status']=="up" and (($olt_ont['wifi1_ssid']!=$attn['wifi_ssid']) or ($olt_ont['wifi1_passwd']!=$attn['wifi_key'])))
			{
				echo "<font color=red><b> wifi settings  mismatch!!</b></font><br>";
				$wifimismatch++;
			}



			
				
				
		}


	
	}
	





	$int_services=$mon->query("select * from  services where services.connection_id=\"".$connection['id']."\" 
	and date_end=\"0000-00-00\" and  services.type=\"TV\"  ")->fetch_assoc();
	echo $mon->error;

//	var_dump($int_services);
	
	$sservice=$mon->query("select * from services where id=\"".$int_services['id']."\" " )->fetch_assoc(); 
	
	
	
//	var_dump($sservice);
		echo "<br>Service ";
		foreach($sservice as $key => $row)
		{
		if($key=="id")
			echo "id : <a href=https://mon.lazertelecom.com/index.php?servs=1&sid=$row>$row</a><br>";
		else
			echo $key . ': ' . $row . "<br>";
		}

	if($sservice['date_end']=="0000-00-00" && $olt_ont['tv']=="down" )
	{	
		echo "<font color=red><b> TV service mismatch!!</b></font><br>".$olt_ont['tv'].$service['date_end'];
		$tvmismatch++;
	}
	elseif($sservice['date_end']!="0000-00-00" && $olt_ont['tv']=="up" )
	{	
		echo "<font color=red><b> TV service mismatch!!</b></font><br>".$olt_ont['tv'].$service['date_end'];
		$tvmismatch++;
	}



	
	

}
elseif($olt_ont['vlan']==10) //phone service
{

	
	$sservice=$mon->query("select * from services where connection_id=\"".$connection['id']."\" and date_end=\"0000-00-00\" and  type=\"PHN\" " ); 
	
	
	while($pservice=$sservice->fetch_assoc())
	{
		
		$sattribues=$mon->query("select * from service_attributes where service_id=\"".$pservice['id']."\" " ); 
		$attn=array();
		while($att=$sattribues->fetch_assoc())
		{
			echo "&nbsp;&nbsp;&nbsp;".$att['name'].":". $att['value']."<br>";
			$attn[$att['name']]=$att['value'];
		}	
	
	
	
	
			if($attn['phn_port']==1) 
			{
				if($olt_ont['ph1']!=$attn['account'])
				{
				echo "<font color=red><b> phone settings  mismatch!!</b></font><br>";
				$phonemismatch++;
				}
			}
			if($attn['phn_port']==2) 
			{
				if($olt_ont['ph2']!=$attn['account'])
				{
					echo "<font color=red><b> phone settings  mismatch!!</b></font><br>";
				$phonemismatch++;
				}
			}


	
	
	
	
	
	}
	
	

}
else
{
	echo "<font color=red><b> No Vlan/internet !!</b></font><br>";
	
}

	
	
	

	
	
	
	
	
	
	
	
	
	
/*	
	while($service=$services->fetch_assoc())
	{
		echo "<br>Service ";
		foreach($service as $key => $row)
		echo $key . ': ' . $row . "<br>";
	
		

			$atts=$mon->query("select * from service_attributes where service_id=\"".$service['id']."\" ");
		


			echo $mon->error;
			$attn=array();
			while($att=$atts->fetch_assoc())
			{
				echo "&nbsp;&nbsp;&nbsp;".$att['name'].":". $att['value']."<br>";
				$attn[$att['name']]=$att['value'];
			}


		if($service['type']=="TV")
		{
			if(($olt_ont['tv']!="up" and  $service['date_end']=="0000-00-00") or ($olt_ont['tv']=="up" and  $service['date_end']!="0000-00-00"))
			{	echo "<font color=red><b> TV service mismatch!!</b></font><br>".$olt_ont['tv'].$service['date_end'];
				$tvmismatch++;
			}
		}
		
		if($service['type']=="INT" and $olt_ont['vlan']!=10 and $olt_ont['vlan']== $attn['vlan'])
		{
		
		
		
			if($olt_ont['vlan']!= $attn['vlan'])
			{	echo "<font color=red><b> vlan service mismatch!!</b></font><br>".$olt_ont['vlan']."-". $attn['vlan'] ."<br>";
				$vlanmismatch++;
			}	
			//speeds
				
				
			$speeds=$mon->query("select prof_up,prof_down from int_services where id=\"".$attn['speed']."\" ")->fetch_assoc();
			if($speeds['prof_up']!= $olt_ont['bwup'] or $speeds['prof_down']!= $olt_ont['bwdown'])
			{	echo "<font color=red><b> speed service mismatch!!</b></font><br>";
						$speedmismatch++;
			}	
			//rg router bridge
				
			if(($olt_ont['bridge_mode']=="brouted" and $attn['is_router']==0) or ($olt_ont['bridge_mode']=="bridged" and $attn['is_router']==1))
			{	echo "<font color=red><b> router mode mismatch!!</b></font><br>";
				$routermismatch++;
	
			}
			//wifi settings

			if(($olt_ont['wifi1_status']=="up" and $attn['wifi']!=1)or($olt_ont['wifi1_status']=="down" and $attn['wifi']!=0))
			{
				echo "<font color=red><b> wifi status  mismatch!!</b></font><br>";
			}

			if($olt_ont['wifi1_status']=="up" and (($olt_ont['wifi1_ssid']!=$attn['wifi_ssid']) or ($olt_ont['wifi1_passwd']!=$attn['wifi_key'])))
			{
				echo "<font color=red><b> wifi settings  mismatch!!</b></font><br>";
				$wifimismatch++;
			}
	}
		if($service['type']=="PHN" and $mon_ont['vlan']==10)
		{
		
			if(($olt_ont['ph1']!=$attn['account'] and $attn['phn_port']==1) or (($olt_ont['ph2']!=$attn['account']) and $attn['phn_port']==2))
			{
				echo "<font color=red><b> phone settings  mismatch!!</b></font><br>";
				$phonemismatch++;
			}
	
		}
	}
*/	
	
	
	































	
	
	 /// set newattributes in mon
	 
	 echo "<td>";
	 echo "update ftth_ont set serial=\"".$olt_ont['serial']."\",model=\"".$olt_ont['model']."\",
	 

	 
	 
	 
	 
	<td>
	
";	
	
	gpon_register_ont($fsan,$newpon,$register,$newolt);
	
	

	}// connection exist in mon


echo "</tr>";

$font++;
}


echo "</table><br> 
total exec time:".(time()-$tsp)."s<br>".

"
total onts on OLT: $font <br>
fsanmismatch: $fsanmismatch <br>
meprofmismatch: $meprofmismatch <br>
conmismatch: $conmismatch <br>
tvmismatch:	$tvmismatch <br>
vlanmismatch: $vlanmismatch <br>	
speedmismatch: $speedmismatch <br>
routermismatch: $routermismatch <br>
wifimismatch: $wifimismatch <br>
phonemismatch: $phonemismatch <br>
 ";



