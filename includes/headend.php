<?php

echo "  
	<a href=?headend=1&channels=1><img src=img/tv.png></a>
	<a href=?headend=1&cards=1><img src=img/skycard.png></a>
	<a href=?headend=1&lists=1><img src=img/lists.png></a> 
	<img src=img/sp.png>
	<a href=?headend=1&servers=1><img src=img/servers.png></a>


";

$network_Path = MON_ROOT."/network/headend/";



if (0)
{
echo " GPON ONT id <b> $equip </b> <br>";
$ont=$mon3->query("select * from ftth_ont where fsan=\"$equip\"")->fetch_assoc();
$con=$mon3->query("select * from connections where equip_id=\"$equip\"")->fetch_assoc();
$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\"")->fetch_assoc();
$history=$mon3->query("select * from history_ont where fsan=\"$equip\" order by timestamp desc");


echo "status (at ".date("Y-m-d H:i:s",$ont['status_timestamp']).") :".$ont['status'] ."<br>
Address: <a href=?propid=".$prop['id'].">". $prop['address']
."</a><br>

<br>";




 }
 
 
 //############### cards #########
 elseif($_GET['cards']==1)
 {
	 
	 
 
	 
	 
	 
	echo "<h3>Headend TV Cards</h3>";
  
  
  if($_GET['cancelled_cards']==1)
  {	
	$cards=$mon3->query("select * from TV_cards where date_cancelled!=\"0000-00-00\" order by card_nr");
  $cost=0;
  $cards_active=$cards->num_rows;
	  
  }
	elseif(isset($_GET['supplier']))
	{
		$cards=$mon3->query("select * from TV_cards where supplier=\"".$_GET['supplier']."\" order by card_nr");
  $cost=$mon3->query("select sum(cost) from TV_cards where supplier=\"".$_GET['supplier']."\" AND date_cancelled=\"0000-00-00\"")->fetch_assoc();
  $cards_act=$mon3->query("select * from TV_cards where supplier=\"".$_GET['supplier']."\" AND  date_cancelled!=\"0000-00-00\" order by card_nr");
  $cards_active=$cards_act->num_rows;
	  
	
	}
  
  else
  {
  $cards=$mon3->query("select * from TV_cards where date_cancelled=\"0000-00-00\" order by card_nr");
  $cost=$mon3->query("select sum(cost) from TV_cards where date_cancelled=\"0000-00-00\"")->fetch_assoc();
  $cards_active=$cards->num_rows;
  }
  
  
  echo "<a href=?headend=1&cards=1&cancelled_cards=1> show cancelled cards</a>
  <a href=?headend=1&cards=1> show active cards</a>
  
  <br>"
  
  
  
  ." total active cards: $cards_active | total cost:".ceil($cost['sum(cost)'])."€" 
  .""
  
  
  ;
  
  
  
  
   echo "<table cellpadding=5>
 <tr> 

 <th>card_nr </th>
 <th>thumb</th> 
 <th>supplier </th> 
 <th>channel id</th>
 <th>package</th> 
 <th>receiver type</th>
 <th>location</th>  
  <th>cost</th>  
  <th>send email </th> 
 ";
 
 while($card=$cards->fetch_assoc())
 {
  
	echo "<tr><td align=center><a href=?headend=1&card=".$card['id'].">".$card['card_nr']."</a></b><br>".$card['broadcaster']."
	<td><a href=includes/resources/satcards/".$card['id'].".jpg target=_blank> <img  width=200px height=113px src=includes/resources/satcards/".$card['id'].".jpg></a>
	<td><a href=?headend=1&cards=1&supplier=".$card['supplier'].">".$card['supplier']."</a>
	<td>";
	
	$channels=$mon3->query("select * from TV_channels where card_id=\"".$card['id']."\"");
	
	while($channel=$channels->fetch_assoc())
	{
		echo "<a href=?headend=1&channel=".$channel['sid'].">".$channel['name']."</a><br>";
	}
	//.$card['channel_id'].



	echo "<td ";
	if($card['date_cancelled']!="0000-00-00")
		echo "bgcolor=red";
	echo ">".trim($card['package_active']).
	"<td>".$card['receiver_type']."
	<td>".$card['location']."	
	<td>".$card['cost']."€	
	";

	
	
	
	

	
 }
 
 
 
 
 }
 
 
 
 
 //###individual  card
 elseif($_GET['card']>0)
 {
	 	 $id=$_GET['card'];
	 
	 
	 if($_POST['update'])
	 {
		 
		 $reason=mysqli_real_escape_string($mon3, $_POST['reason']);
		 $notes=mysqli_real_escape_string($mon3, $_POST['notes']);
		$mon3->query("INSERT INTO `TV_cards_notes` (`card_id`, `reason`, `notes`, `date`, `user`) VALUES ($id, \"$reason\", \"$notes\", \"".date("Y-m-d")."\", \"".$_SERVER['PHP_AUTH_USER']."\");");
		 
		 echo "updating notes";
		 
		 var_dump($_POST);
		 
	 }

	echo "<h3>Headend TV Cards</h3>";
	$card=$mon3->query("select * from TV_cards where id=$id")->fetch_assoc();
	
	
	
	echo" <b>Card id $id - ".$card['card_nr'] ."</b> for ".$card['broadcaster'] ."<br>".
	"<table>".  
	"<tr><td>supplier: <td>".$card['supplier'] .
	"<tr><td>activation date: <td>".$card['date_activation'] .
	"<tr><td>box id: <td>".$card['receiver_type'] . " - ".$card['paired_box'] . " - <a href=?headend=1&receiver_id=".$card['receiver_id'].">".$card['receiver_id']."</a>".
	"<tr><td>packages active: <td>".$card['package_active'] .
	"<tr><td>location: <td>".$card['location'].
	"<tr><td>channels: <td>";
	$channels=$mon3->query("select sid,name from TV_channels where card_id=$id");
	if($channels->num_rows>0)
		while($channel=$channels->fetch_assoc())
		{echo "<a href=?headend=1&channel=".$channel['sid']." >".$channel['name']." </a>";}
	
	
	
	echo	"<tr><td>status:<td>";
	if($card['date_cancelled']!="0000-00-00")
	{
		echo "<font color=red><b>Cancelled on the ".$card['date_cancelled'];
	}
	else
	{
		echo "active";
		
	
	
	}
	echo "<tr><td>cost:<td>".$card['cost']."€";
	
	echo "<tr><td colspan=2><br><a href=includes/resources/satcards/".$id.".jpg target=_blank> <img  width=400px  src=includes/resources/satcards/".$id.".jpg></a>";

 	echo "
	<tr><td><br><form action=?headend=1&card=$id method=post>
	<tr><td><br>new log: 
	<tr><td> note:<td><input type=text name=notes size=60>
	<tr><td>reason: <td>
	<select name=reason>
	<option>box down</option>
	<option>cam/box stuck-reset</option>
	<option>card subs down</option>
	<option>lost pairing</option>
	<option>no payment</option>
	<option>cancelled for streaming</option>
	<option>cancelled lost card</option>
	<option>cancelled faulty card</option>
	<option>faulty box</option>
	<option><font color=green>back on</font></option>
	</select>
	
	<input type=submit name=update value=update>
	</form>
	
	";
	
	echo "<tr><td><tr><td>log: <td>";
	
	$logs=$mon3->query("select * from TV_cards_notes where card_id=$id order by date");
	while($log=$logs->fetch_assoc())
	{
		echo $log['date']."-".$log['user'].": <b>".$log['reason']."</b> -> ". $log['notes']."<br>";
	}
	
	echo"</table>";
	
	
	
	
	
	
	
	
	
	 
	 
	 
	 
	 
	 
	 
	 
	 
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
  //############### channel list ########
 
 elseif($_GET['channels']==1)
 {
 	echo "<h3>Headend TV Channels</h3><br>";

/*
 ?>
 
 <script type="text/javascript">
setTimeout(function() {
  location.reload();
}, 30000);
 </script>
 <?php
*/ 
 $channels=$mon3->query("select * from TV_channels order by channel_type,name");


 
  echo "<table cellpadding=5>
 <tr> <th>channel </th><th>thumb</th> <th>card </th> <th>lists </th>   
 ";
 
 while($channel=$channels->fetch_assoc())
 {

	echo "<tr><td align=center><a href=?headend=1&channel="
	.$channel['sid']."> <img width=64px src=https://mon.lazertelecom.com/channels/".$channel['sid'].".png><br>"
	.$channel['sid']."<br>".$channel['name']."</a>
	
	<td>";	
	if($channel['channel_type']=="TV")
	{echo "<img  width=200px height=113px src=https://mon.lazertelecom.com/img/get_thumb.php?sid=".$channel['sid']."&for=gif>";}
	else{echo "<img  height=113px src=https://mon.lazertelecom.com/img/radio.png>";}
	echo 
	"<td>	";
	
	$card=$mon3->query("select * from TV_cards where id=\"".$channel['card_id']."\" ")->fetch_assoc();
	
	
	echo "<a href=?headend=1&card=".$channel['card_id'].">".$card['broadcaster']." <br> ".$card['supplier']." <br> ".$card['card_nr']."</a>";
 	



	
 }
 
 
 
 }
 
 
 
// #### individual channel ##############################
 
 
  elseif($_GET['channel']>0)
  {
	  
	  
	  
	$id=$_GET['channel'];
	$channel=$mon3->query("select * from TV_channels where sid=$id")->fetch_assoc();
	
	echo "<h3>Headend TV channel id $id <br> <font color=red>".$channel['name']."</font>
	<br><img width=64px src=../channels/".$channel['sid'].".png> </h3><br>";
	  
	 
 
	 
	 if($_POST['update_channel'])
	 {
		 
		 $card=mysqli_real_escape_string($mon3, $_POST['card_id']);
		 $notes=mysqli_real_escape_string($mon3, $_POST['notes']);
		 
		$mon3->query("update TV_channels set card_id=\"$card\", notes=\"$notes\" where sid=\"$id\" ;");
		 echo mysqli_error($mon3); 
		 
		 if($notes==$channel['notes']) $notes="";
		 else $notes=" new notes: ".$notes;
		
		 if($card!=$channel['card_id']) $notes=" new card assigned:$card ".$notes;
		
		
		chanlog($id,$card,"updated channel.  $notes");
		 echo "<font color=green><b> Saved</b></font>";
		 
		$channel=$mon3->query("select * from TV_channels where sid=$id")->fetch_assoc();
		 
	 }



	//	chanlog($id,$channel['card_id'],$text);
	
	
	
	echo"<form name=chinfo method=post action=?headend=1&channel=$id>
	<table>	 ".
	"<tr><td colspan=2> ";
	if($channel['channel_type']=="TV")
	{echo "<img  width=200px height=113px src=../img/get_thumb.php?sid=".$channel['sid']."&for=gif>";}
	else{echo "<img  height=113px src=../img/radio.png>";}
	
	
	
	echo
 	"<tr><td>broadcaster: <td>".$channel['broadcaster'] .
	
	
	"<tr><td>card_id: <td> <select name=card_id><option value=0"; 
	if($channel['card_id']==0) echo " selected"; 
	echo "> no card</option>";
	
	
	$cards=$mon3->query("select * from TV_cards where date_cancelled=\"0000-00-00\" order by broadcaster,card_nr");
	while($card=$cards->fetch_assoc())
 {
	 echo"<option value=".$card['id'];
	 if($card['id']==$channel['card_id']) echo " selected";
	 echo ">".$card['broadcaster']." - ".$card['supplier']." - ".$card['card_nr']."</option>";
					
 }
	echo "</select> <a href=?headend=1&card=".$channel['card_id'].">".$channel['card_id']."</a>".
	"<tr><td>channel_type: <td> ".$channel['channel_type'].
	"<tr><td>genre: <td> ".$channel['genre'].
	"<tr><td>language: <td> ".$channel['lang'].
	"<tr><td>country: <td> ".$channel['country'].	



	"<tr><td>Input IPTV: <td>".$channel['input_iptv_stream'].
	
	
	"<tr><td>Satellite: <td> <a target=_blank href=https://en.kingofsat.net/".$channel['sat'].".php>".$channel['sat']."</a> Antenna_id:".$channel['antenna_id'].

	

	"<tr><td>receiver: <td> ".$channel['receiver_type']." ip:<a target=_blank href=http://"	.$channel['receiver_ip'].">"	.$channel['receiver_ip']."</a> card/port: "	.$channel['receiver_card_port']." cam: "	.$channel['cam_card_id']." at "	.$channel['receiver_location'].

	"<tr><td>encoder: <td> <a target=_blank href=http://"	.$channel['encoder_ip'].">"	.$channel['encoder_ip']."</a> port: "	.$channel['encoder_port'].

	"<tr><td>processor: <td> <a target=_blank href=http://"	.$channel['processor_ip'].">"	.$channel['processor_ip']."</a> card: "	.$channel['processor_card'].

	
	"<tr><td>SPTS_ip: <td> rtp://".$channel['SPTS_ip'].":1234".
	"<tr><td>vlan: <td> ".$channel['vlan'].
	
	
	"<tr><td>Notes: <td><textarea name=notes>".$channel['notes']."</textarea>".
	
	"<tr><td> <td> <input type=submit name=update_channel>
	</table></form>".
	
	
	
	
	
	
/*	
	"<tr><td> SPTS_ip:<td><input type=text name=SPTS_ip value=".$channel['SPTS_ip']." size=20>".
	"<tr><td> vlan:<td><select name=vlan> <option value=501"; if($channel['vlan']==501) echo " selected"; echo "> 501 </option> <option value=502"; if($channel['vlan']==502) echo " selected"; echo "> 502  </option> ".
*/	
	
	
	
	
	
	"";
	
	
	
	
	
	
	
	
	
	
	
	
	

 	echo "
	<table><tr><td><br>
	<form name=chlog action=?headend=1&channel=$id method=post>
	<tr><td><br>new log: <td><input type=text name=notes size=60>
	<tr><td>reason: <td>
	
	<select name=reason>
	<option>channel changed frequency</option>
	<option>box down</option>
	<option>receiver stuck</option>
	<option>card subs down</option>
	<option>lost pairing</option>
	<option>no payment</option>
	<option>cancelled for streaming</option>
	<option>cancelled lost card</option>
	<option>cancelled faulty card</option>
	<option>faulty box</option>
	</select>
	
	<input type=submit name=insert_log value=update>
	</form>
	
	";
	
	echo "
	<tr><td>
	<tr><td>logs: <td>";
	$lines = `tail -20 /var/www/html/mon/channels/$id.txt`;
echo $lines;

	echo"</table>";
	
	
























	 
  }
 
 
 
 
 
 
 
 
 
 
// ######################## channel lists
 
 
  elseif($_GET['lists']==1)
 {
 	echo "<h3>Headend TV lists</h3><br>";

	 $list_tv=$_GET['list_tv'];
	 $tech_details = $_GET['tech_details'];

	 echo"<form action=index.php method=get> 
	<input type=hidden name=headend value=1>
	<input type=hidden name=lists value=1>";

	echo "<select name=list_tv onchange=\"submit();\"><option value=''>Select a TV List</option>";
	$lists_names=$mon3->query("select DISTINCT list from TV_lists order by list");
	while($list_name=$lists_names->fetch_assoc())
	{
		echo "<option value='".$list_name['list']."'";
		if($list_name['list']==$list_tv) echo " selected ";
		echo " > ".$list_name['list']."</option>";
	}	
	
	echo "</select>";

	echo "<select name=tech_details onchange=\"submit();\"><option value=''>Select a Tech Details</option>";
	$tech_dets=$mon3->query("select DISTINCT tech_details from TV_lists WHERE tech_details != '' order by tech_details");
	while($tech_det=$tech_dets->fetch_assoc())
	{
		echo "<option value='".$tech_det['tech_details']."'";
		if($tech_det['tech_details']===$tech_details) echo " selected ";
		echo " > ".$tech_det['tech_details']."</option>";
	}	
	
	echo "</select>";




	echo "</form>";

	 echo "<table cellpadding=5>
	 <tr> <th>ID</th> <th>List </th> <th>SID </th> <th>position</th> <th>Tech Details</th>
	 ";

	 $q_lists = "SELECT * FROM TV_lists where 1";

	 if($list_tv!="")
	 {
	   $q_lists.=" AND list='".$list_tv."'";
	 }

	 if($tech_details !="")
	 {
	   $q_lists.=" AND tech_details='".$tech_details."'";
	 }

	 $tvlists = $mon3->query($q_lists);
	 
	 while($tvlist=$tvlists->fetch_assoc())
	 {
	  
		echo "<tr><td align=center>".$tvlist['id']."
		<td align=center>".$tvlist['list']."
		<td align=center>".$tvlist['sid']."
		<td align=center>".$tvlist['position']."
		<td align=center>".$tvlist['tech_details']."
		";
	
		
		
		
		
	
		
	 }
 


}


// ######################## servers
 

elseif($_GET['servers']==1)
 {
 	echo "<h3>Headend servers/equipment</h3><br>";

$popsel=$_GET['pop'];

echo"<form action=index.php method=get> 
<input type=hidden name=headend value=1>
<input type=hidden name=servers value=1>
<select name=pop onchange=\"submit();\"><option value=''>Select a Headend POP</option>";
$pops=$mon3->query("select OGR_FID,name from headend_pop order by OGR_FID");
while($pop=$pops->fetch_assoc())
{
	echo "<option value=".$pop['OGR_FID'];
	if($pop['OGR_FID']==$popsel) echo " selected ";
	echo " > ".$pop['name']."</option>";
}	

echo "</select></form>";

	
//$q="select headend_equip.id,headend_equip.ip_addr,headend_equip.title,headend_equip.pop_id,headend_equip.location, headend_pop.name from headend_equip left join headend_pop on headend_equip.pop_id=headend_pop.OGR_FID ";
if($popsel>0)
{
//$q.=" where headend_pop.OGR_FID=$popsel ";
$headendpop=$mon3->query("select * from headend_pop where OGR_FID=\"$popsel\"")->fetch_assoc();
$coord=explode(",",$headendpop['coords']);
$lat=trim($coord[0]);
$lng=trim($coord[1]);
?>

<table >
<tr>
<td>
 <div id="map"></div>

<script>
function initMap() {
  // The location of Uluru
  const uluru = <?php echo "{ lat: $lat, lng: $lng };";?>
  // The map, centered at Uluru
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 18,
    center: uluru,
	mapTypeId: 'hybrid',
  });
  // The marker, positioned at Uluru
  const marker = new google.maps.Marker({
    position: uluru,
    map: map,

  });
}
window.initMap = initMap;


</script>


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap&v=weekly" defer>
	</script>
<br><a target=_blank href=https://www.google.com/maps/search/?api=1&query=<?php echo $lat.",".$lng ; ?> >open in maps</a>
<td style="width: 100%">









<?php
if($_FILES['randfile']['name'] != null)
{
	$countfiles = count($_FILES['randfile']['name']);
	for($i=0;$i<$countfiles;$i++){
		if(file_exists($_FILES['randfile']['tmp_name'][$i]))
		{	
				$ext=explode(".",$_FILES['randfile']['name'][$i]);
				echo uploadfile("randfile",$network_Path.$popsel."/", date("Y-m-d_His")."_".
				$localuser['username'].$i.".".strtolower($ext[sizeof($ext)-1]),0,$i);
		}
	}
}




$is_images = '';
$i=0;



if(file_exists($network_Path.$popsel))
{
    $files1 = scandir($network_Path.$popsel);
    
    
    foreach($files1 as $file1){
        if(substr($file1,0,1)!=".")
        {
            if($i%3==0)
            {
                $is_images .="<tr>";
            }

            if(strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_leads.$lead_id."/".$file1, PATHINFO_EXTENSION))=="jpeg")
            {
                $is_images .= "<td align=center><a href=network/headend/".$popsel."/".$file1." class=link_slider target=_blank onclick=\"window.open('network/headend/".$popsel."/".$file1."','popup','width=600,height=600,scrollbars=no,resizable=no'); return false;\">";
                $is_images .= "<img src=network/headend/".$popsel."/".$file1." width=100px height=100px alt=".$file1." class=img_slider > <br> $file1 </a> ";
            }
                
            else
            {
                $is_images .= "<td align=center> <a href=network/headend/".$popsel."/".$file1." class=link_pdf target=_blank onclick=\"window.open('network/headend/".$popsel."/".$file1."','popup','width=600,height=600,scrollbars=no,resizable=no'); return false;\">";
                $is_images .= "<img src=img/file.png height=100px class=\"img_pdf\" alt=".$file1."> <br> $file1 </a> ";
            }
            $i++;
            
        }
    }

    
}


	echo "<table class=bod-modal data-title=center ><tr>";
    echo $is_images;
    echo "</tr></table>";


echo "<table style=\"width: 100%;\">
<tr><td colspan=2 align=center><br><br><b>upload new file(.jpg or .pdf)</b><br>
<form name=addrandfile method=post enctype=\"multipart/form-data\" action=index.php?headend=1&servers=1&pop=".$popsel.">
<label for=fileInput> 
  <img id=icon´ height=100px src=\"img/upload.png\" style=\"cursor: pointer;\">
</label>
<input type=file name=randfile[] accept=\".pdf,image/jpeg\" id=fileInput multiple style=\"display:none;\" onchange=\"this.form.submit()\">
</form>";

echo "</table>







</table>";

}
//$q.=" order by pop_id,ip_addr ";
else
{
//map of all pops to select




}

	
//add entry form
if($_POST['addip'])
{
$name=mysqli_real_escape_string($mon3, $_POST['title']);
$ip=mysqli_real_escape_string($mon3, $_POST['ip']);
$location=mysqli_real_escape_string($mon3, $_POST['location']);



$mon3->query("insert into headend_equip (ip_addr, title,location,pop_id,date_installed) values
(\"$ip\",\"$name\",\"$location\",\"$popsel\", \"".date("Y-m-d")."\" )");
echo $mon3->insert_id;
}





if($popsel>0)
{
$q="select headend_equip.id,headend_equip.ip_addr,headend_equip.title ,headend_equip.pop_id,headend_equip.location, headend_pop.name from headend_equip left join headend_pop on headend_equip.pop_id=headend_pop.OGR_FID  where headend_pop.OGR_FID=$popsel order by ip_addr";

$qc="select coax_cmts.id,coax_cmts.ip,coax_cmts.name as title,coax_cmts.headend_pop_id,coax_cmts.location, headend_pop.name from coax_cmts left join headend_pop on coax_cmts.headend_pop_id=headend_pop.OGR_FID where headend_pop.OGR_FID=$popsel order by ip";

$qf="select ftth_olt.id, ftth_olt.ip, ftth_olt.name as title, ftth_olt.headend_pop_id, ftth_olt.location, headend_pop.name from  ftth_olt left join headend_pop on  ftth_olt.headend_pop_id=headend_pop.OGR_FID where headend_pop.OGR_FID=$popsel order by ip";

$qw="select fwa_antennas.id,fwa_antennas.ip,fwa_antennas.name as title,fwa_antennas.headend_pop_id,fwa_antennas.location, headend_pop.name from fwa_antennas left join headend_pop on fwa_antennas.headend_pop_id=headend_pop.OGR_FID where headend_pop.OGR_FID=$popsel order by ip";

}
else
{
$q="select headend_equip.id,headend_equip.ip_addr,headend_equip.title,headend_equip.pop_id,headend_equip.location, headend_pop.name from headend_equip left join headend_pop on headend_equip.pop_id=headend_pop.OGR_FID order by pop_id,ip_addr";

$qc="select coax_cmts.id,coax_cmts.ip,coax_cmts.name as title,coax_cmts.headend_pop_id,coax_cmts.location, headend_pop.name from coax_cmts left join headend_pop on coax_cmts.headend_pop_id=headend_pop.OGR_FID  order by ip";

$qf="select ftth_olt.id, ftth_olt.ip, ftth_olt.name as title, ftth_olt.headend_pop_id, ftth_olt.location, headend_pop.name from  ftth_olt left join headend_pop on  ftth_olt.headend_pop_id=headend_pop.OGR_FID  order by ip";

$qw="select fwa_antennas.id,fwa_antennas.ip,fwa_antennas.name as title,fwa_antennas.headend_pop_id,fwa_antennas.location, headend_pop.name from fwa_antennas left join headend_pop on fwa_antennas.headend_pop_id=headend_pop.OGR_FID order by ip";

}

	
$equips=$mon3->query($q);
$eq_f=$mon3->query($qf);
$eq_c=$mon3->query($qc);
$eq_w=$mon3->query($qw);







echo "<table cellpadding=5>
 <tr> <th>id </th><th>title</th> <th>ip </th> <th>pop </th> <th>location</th>  
 ";
 
 
 
 
 
 
 
 
 //display equips from cmts, olt, fwa and equip table
 
 if($eq_f->num_rows>0)
 {
  while($eqf=$eq_f->fetch_assoc())
 {
  
	echo "<tr><td align=center>".$eqf['id']."
	<td align=center>".$eqf['title']."
	<td><a target=_blank href=http://".$eqf['ip'].">".$eqf['ip']."</a>
	<td>".$eqf['name']."
	<td>".$eqf['location']."
	";

}
 }
 
 if($eq_c->num_rows>0)
 {
   while($eqc=$eq_c->fetch_assoc())
 {
  
	echo "<tr><td align=center>".$eqc['id']."
	<td align=center>".$eqc['title']."
	<td><a target=_blank href=http://".$eqc['ip'].">".$eqc['ip']."</a>
	<td>".$eqc['name']."
	<td>".$eqc['location']."
	";

}
 }
 
 if($eq_w->num_rows>0)
 {
    while($eqw=$eq_w->fetch_assoc())
 {
  
	echo "<tr><td align=center>".$eqw['id']."
	<td align=center>".$eqw['title']."
	<td><a target=_blank href=http://".$eqw['ip'].">".$eqw['ip']."</a>
	<td>".$eqw['name']."
	<td>".$eqw['location']."
	";

}
 }
 

if($equips->num_rows>0)
 { 
 while($equip=$equips->fetch_assoc())
 {
  
	echo "<tr><td align=center><a href=?headend=1&equips=".$equip['id'].">".$equip['id']."</a>
	<td align=center>".$equip['title']."
	<td><a target=_blank href=http://".$equip['ip_addr'].">".$equip['ip_addr']."</a>
	<td>".$equip['name']."
	<td>".$equip['location']."
	";

}
 }

 
 
 
 
 
 
 //add new equipment
if( $popsel>0)
{
 echo "<form cellpadding=5 action=?headend=1&servers=1&pop=$popsel method=post>
 <tr> <td> 
 <td><input type=text name=title> <input type=hidden name=popid value=$popsel>
 <td><input type=text name=ip>
  <td>pop $popsel
 <td><input type=text name=location>
 <td> <input type=submit name=addip value=add>
 </form>";
} 
 
 echo "</table>";	
 


}


 //########### default view alarms/probe/graphs
 else
 {
  	echo "<h3>Headend</h3><br>";
	
	
	
 
 }
 
 