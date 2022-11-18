<?php

// REMOVER ELEMENTOS DOS CAMPOS NA BASE DE DADOS QUE NAO PRECISA (LISTAR OS CAMPOS DAS LEADS QUE NAO PRECISAM DE COLOCAR NA DUMP PROPERTY_LEADS)
function remove_element_array($array, $element)
{
    $arr = array();
    foreach ($array as $key => $value) {

        if($key == $element)
        {
            //print_r($array[$element]);
            unset($array[$key]);
            $arr = $array;
        }

    }

    return $arr;

}

// CARREGAR FICEHIRO

function uploadfile($fieldform,$path,$filename,$is_pic,$id){
    $uploadOk = 1;
    $file = "";
    $file_size = "";
    $file_name = "";
    $var = "";
	
    //if($id=="") $id=0;
    // UPLOAD UM FICHEIRO USANDO UPLOAD FILE - 1
    if(sizeof($_FILES[$fieldform]['tmp_name']) == 1 && $fieldform !== "randfile")
    {
        $file = $_FILES[$fieldform]['tmp_name'];
        $file_size = $_FILES[$fieldform]["size"];
        $file_name = $_FILES[$fieldform]["name"];
    }
    // UPLOAD MULTIUPLOAD USANDO UPLOAD FILE - 1
    else if(sizeof($_FILES[$fieldform]['tmp_name']) == 1 && $fieldform == "randfile")
    {
        $file = $_FILES[$fieldform]['tmp_name'][0];
        $file_size = $_FILES[$fieldform]["size"][0];
        $file_name = $_FILES[$fieldform]["name"][0];
    }
    // UPLOAD MULTIUPLOAD USANDO VARIOS UPLOADS FILES - N
    else if(sizeof($_FILES[$fieldform]['tmp_name']) > 1 && $fieldform == "randfile")
    {
        $file = $_FILES[$fieldform]['tmp_name'][$id];
        $file_size = $_FILES[$fieldform]["size"][$id];
        $file_name = $_FILES[$fieldform]["name"][$id];
    }
    //check if file was uploaded in the form

	
    
    
    if(file_exists($file)){
    
        if(file_exists($path)==false){
            mkdir($path, 0777);
        }
    // Check if image file is a actual image or fake image
    
        if($is_pic==1)
        {
			$check = getimagesize($file);
			if($check !== false) {
				$var .= "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				$var .= "File is not an image.";
				$uploadOk = 0;
			}
        }
    
		// Check file size 10mb max
		if ($file_size > 10000000) {
			$var .= "Sorry, your file is too large.";
			$uploadOk = 0;
		}
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$var .= "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($file, $path.$filename)) 
			{

				//echo $filename;
				if(preg_match("/.pdf/", $path.$filename))
				{
					$file_im = $path.$filename."[0]"; 
					$im = new Imagick();
					$im->setResolution(300, 300);     //set the resolution of the resulting jpg
					try
					{
						$im->readImage($file_im);    //[0] for the first page
						$filename = preg_replace("/.pdf/", '_pdf', $filename);
						$full_file = $path.$filename.".png";
						$im->setImageFilename($full_file);
						$im->writeImage();
					}
					catch(ImagickException $e) {
						$var .= "Error: " . $e -> getMessage() . "\n";
					}
				}
				
				

				$var .= "<font color=green>The file ". basename( $file_name). " has been uploaded.</font>";
			} else {
				$var .= "Sorry, there was an error uploading your file.";
			}
		}	
        
    }
    else
    {
        $var .= "no file selected $fieldform";
    }
    return $var;
}


    // LOGS DA MON


    function monlog($text){
        global $localuser;
        $project_log = MON_ROOT."/log.txt";
        //$as = date("Y-m-d H:i:s")." ".$localuser['username'].": ".$text."<br>";
        $fp=fopen($project_log,"a") or die("can't open file");
        fputs($fp, date("Y-m-d H:i:s")." ".$localuser['username'].": ".$text.PHP_EOL);
        fclose($fp);
    }

    // LOGS DA PROPRIEDADE CORRESPONDENTE

    function proplog($propid,$text){
        global $localuser;

        $mon_prop = MON_ROOT."/properties/"; 
        $p = $mon_prop.$propid."/";

        $p_file = $p."log.txt";

        if(file_exists($p)==false) mkdir($p, 0777);
        $fp=fopen($p_file,"a") or die("can't open file");
        fputs($fp, date("Y-m-d H:i:s")." ".$localuser['username'].": ".$text."<br>");
        fclose($fp);
    }

    // LOGS DE CHANNELS
    
    function chanlog($sid,$cardid,$text){
        global $localuser;
        $p_channels = MON_ROOT."/channels/";
        $p = MON_ROOT."/channels/".$sid.".txt";

        $fp=fopen($p.".txt","a") or die("can't open file");
        fputs($fp, date("Y-m-d H:i:s")." card: $cardid - ".$localuser['username'].": ".$text."<br>");
        fclose($fp);
    }



////////////////////////////////////////////GPON functions/////////////////////////////////////////////////////////////////////////


// GPON APAGAR A ONT

function gpon_delete_ont($olt,$ont_id){
	echo "onu  delete $ont_id <br>	";

	monlog("result gpon_delete_ont($olt,$ont_id): $output");

}

// REGSITO DE UMA GPON

function gpon_register_ont($equip){
	global $mon3;

	$con=$mon3->query("select * from connections where equip_id=\"$equip\"");
	$conid = $con['id'];
	if($con->num_rows>1) echo "Caution: theres more than one connection with this ONT";
	$con=$con->fetch_assoc();
	$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\"")->fetch_assoc();
	$ont=$mon3->query("select * from ftth_ont where fsan=\"$equip\"")->fetch_assoc();
	
	echo "<br>Address: <a href=?props=1&propid=".$prop['id'].">". $prop['address']."</a><br>";
	$ontid=$ont['ont_id'];
	$ontid2=explode("-",$ont['ont_id']);
	$ontid3=$ontid2[1]."/".$ontid2[2]."/".$ontid2[3];
	$olt=$mon3->query("select ip from ftth_olt where id=\"".$ont['olt_id']."\" ;")->fetch_assoc();

	
	echo "
<br><br>
OLT: ".$olt['ip']."<br>
gpononu set $ontid3 vendorid ZNTS serno fsan ".substr($ont['fsan'],4)." meprof ".$ont['model']."<br>
cpe system add $ontid3 <br>
cpe rf add $ontid3 admin-state down <br>
bridge add 1/".$ontid3."/gpononu gem 15".sprintf("%02d",$ontid2[3])." gtp 1 epktrule 1 ipktrule 1 downlink vlan 15 tagged mgmt rg-bridged<br>
cpe rg wan modify $ontid3 vlan 15 ip-com-profile 6 <br>
<br>";
$srvs=$mon3->query("select * from services where connection_id=\"".$con['id']."\"");
while($srv=$srvs->fetch_assoc())
{
	
	$srvatts=$mon3->query("select * from service_attributes where service_id=\"".$srv['id']."\"");
	while($srvatt=$srvatts->fetch_assoc())
	{
		$atts[$srvatt['name']]=$srvatt['value'];
	}

//echo " dump for srv  ".$srv['id']." <br>";	
//var_dump($atts);	
	
	
	
	
	
	
	
	
	
	
	if($srv['type']=="TV")
	{
		echo "<br>TV srv ".$srv['id']."<br> cpe rf modify $ontid3"."/1 admin-state up <br>";
		
	}
	
	
	
	
	
	
	
	
	
	
	
	elseif($srv['type']=="INT")
	{
		echo "<br>INT  srv ".$srv['id']."<br>";
		$speed=$mon3->query("select * from int_services where id= \"".$atts['speed']."\"")->fetch_assoc();
		
		
		
		if($atts['is_router']==1)
			echo "
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 1 rg-brouted<br>
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 2 rg-brouted <br>
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 3 rg-brouted <br>
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth 4 rg-brouted <br>";
		else
			echo "
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule " . $speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged eth ".$atts['bridge_port']." rg-bridged <br>";
		
		if($atts['wifi']==1)
		{
			echo "
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged wlan 1 rg-brouted <br>
cpe wlan add ".$ontid3."/1 admin-state up ssid ".$atts['wifi_ssid']." encrypt-key ".$atts['wifi_key']." wlan-com-profile 4 wlan-com-adv-profile 2<br>";
			
			if($ont['model']=="zhone-2727a" || $ont['model']=="zhone-2428")
			{
				echo "
bridge add 1/".$ontid3."/gpononu gem 9".sprintf("%02d",$ontid2[3])." gtp ".$speed['prof_up']." epktrule ".$speed['prof_down'].
" ipktrule ".$speed['prof_up']." downlink vlan ".$atts['vlan']." tagged wlan 5 rg-brouted <br>
cpe wlan add ".$ontid3."/5 admin-state up ssid ".$atts['wifi_ssid']." encrypt-key ".$atts['wifi_key']." wlan-com-profile 4<br>";
			}
		
		
		}
		else
			echo "
cpe wlan add ".$ontid3."/1 admin-state down <br>
cpe wlan add ".$ontid3."/5 admin-state down	<br>";

		if($atts['ip_address']!=""){
			echo "
cpe rg lan modify $ontid3 eth 1 ip-addr ".$atts['ip_address']." dhcp-server-profile ".$atts['dhcp_id']." <br>";
	
		}
		if($atts['portfw_id']!=""){
			echo "
cpe rg wan modify $ontid3 vlan ".$atts['vlan']." port-fwd-list-profile ".$atts['portfw_id']." <br>";
			
		}
		if($atn['dhcp']==1)
					{
					
					}
		if($atn['portfw']==1)
					{
					
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
			echo "bridge add 1/".$ontid3."/gpononu gem 7".sprintf("%02d",$ontid2[3])." gtp 1 epktrule 1	ipktrule 1 downlink vlan 10 tagged sip rg-bridged <br>cpe ip add $ontid3 voip ip-com 2<br>";
			$pass=$mon3->query("select password from voip_accounts where username=\"".$atts['account']."\" ;")->fetch_assoc();
			echo"cpe voip add ".$ontid3."/1 admin-state up dial-number ".$atts['account']." username ".$atts['account'].
" password ".$pass['password']." voip-server-profile 1";
		}
	}

			
	}	

	


	monlog("result gpon_register_ont($ontid,$conid): $output");
}


// MUDANCA DA ONT DA GPON

function gpon_change_ont($olt,$ont_id,$fsan,$model){


	echo "onu clear $ont_id<br>
	onu set  $ont_id vendorid ZNTS serno fsan $fsan meprof $model<br>";
	
	
	
	
	monlog("result gpon_change_ont($olt,$ont_id,$fsan,$model) ");
}


function gpon_move_ont($olt,$ont_id,$new_ont_id, $output){


	echo "onu move $ont_id $new_ont_id<br>
	";
	
	
	monlog("result gpon_move_ont($olt,$ont_id,$new_ont_id): $output");
}



// PROXIMA ONT DA GPON AO REGISTAR UM NOVO EQUIPAMENTO


function nextont($olt,$pon){
	global $mon3;
	//pon like 12-1
	$ids=$mon3->query("select right(ont_id,2) from ftth_ont where ont_id LIKE \"1-".$pon."-%\" AND olt_id=$olt order by ont_id; ");
	mysqli_error($mon3);
	$ontsa=array();
	while($id=$ids->fetch_assoc())	{
		$ontsa[]=str_replace("-","0",$id['right(ont_id,2)']);
	}
	print_r($onts);
	$onts=array_unique($ontsa,SORT_NUMERIC);
if(sizeof($onts)!=sizeof($ontsa))
{
	/*echo "theres duplicate ids in this pon, please check ";
	sort($ontsa);
	print_r($ontsa);*/
}
	sort($onts);
	
//	print_r($onts);
	
	for($i=0;$i<sizeof($onts);$i++)	{
		if($onts[$i]!=$i+1)		{
			return($onts[$i-1]+1);	}	
        }
		return($i+1);
}

		
		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		


function getWorkingDays($startDate, $endDate)
{
    $begin = strtotime($startDate);
    $end   = strtotime($endDate);
    if ($begin > $end) {
        echo "xxdates";

        return 0;
    } else {
        $no_days  = 0;
        $weekends = 0;
        while ($begin <= $end) {
            $no_days++; // no of days in the given interval
            $what_day = date("N", $begin);
            if ($what_day > 5) { // 6 and 7 are weekend days
                $weekends++;
            };
            $begin += 86400; // +1 day
        };
        $working_days = $no_days - $weekends;

        return $working_days;
    }
}


function escape4js($string)
{
	$out="";
	if(sizeof($string)>1)
		$out=$mon3->real_escape_string($string);
	else $out="";
	return $out;

}


function escapechars($string)
{
	$toreplace=array("\r\n","\n","\r","'",chr(145),chr(132),chr(130),chr(146),chr(147),chr(148) );
	$out=str_replace($toreplace,"",$string);
//	echo "$string - $out <br>";
	return $out;
	
}

/////////////////////////////////////////////////////////EMAIL////////////////////////////////////////////////////////////////

// Codigo Enviamail do Chefe Carlos

/*

function enviamail($para,$assunto, $corpo, $fromname) { 
	require '/usr/share/php/libphp-phpmailer/src/PHPMailer.php';
	require '/usr/share/php/libphp-phpmailer/src/SMTP.php';
	global $mailerror;
	if(!isset($fromname)) $fromname='LazerMonSystem';
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->IsSMTP();		// Ativar SMTP
	$mail->SMTPDebug = 0;		// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
	$mail->SMTPAuth = true;		// Autenticação ativada
	$mail->SMTPSecure = 'tls';	// SSL REQUERIDO pelo GMail
	$mail->Host = 'smtp.gmail.com';	// SMTP utilizado
	$mail->Port = 587;  		// A porta 587 deverá estar aberta em seu servidor


    $mail->Username = 'ricardo.peleira@lazerspeed.com';
    $mail->Password = 'Almancil#999';
    $mail->SetFrom('ricardo.peleira@lazerspeed.com', $fromname);
	
//$mail->addAddress('ellen@example.com');               // Name is optional
//    $mail->addReplyTo('info@example.com', 'Information');
//    $mail->addCC('cc@example.com');
 //   $mail->addBCC('bcc@example.com');	
	
	
	
	$mail->Subject = $assunto;
	$mail->Body = $corpo;
	$mail->IsHTML(true); 
	
	
	$destinations=explode("#",$para);
	$to=$destinations[0];
	$bcc=$destinations[1];
	$replyto=$destinations[2];
	
	if(strlen($replyto)>0)
		$mail->addReplyTo($replyto);

	if(strlen($to)>0)
	{
	$addresses=explode(';',$to);
	foreach($addresses as $address)
	{
		$mail->AddAddress($address);
	}
	}
	
	if(strlen($bcc)>0)
	{
	$addresses=explode(';',$bcc);
	foreach($addresses as $address)
	{
		$mail->addBCC($address);
	}
	}
	
	if(!$mail->Send()) {
		$mailerror = 'Mail error: '.$mail->ErrorInfo; 
		echo $mailerror."\n\n";
		return false;
	} else {
		$mailerror = 'Mensagem enviada!';
		return true;
	}
} 

*/
