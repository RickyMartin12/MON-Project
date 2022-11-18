<?php


////////// GPON LISTS //////////////////////////////////////////











$ref_prop=mysqli_real_escape_string($mon3,$_GET['ref_prop']);
$pon=mysqli_real_escape_string($mon3,$_GET['pon']);
$olt=mysqli_real_escape_string($mon3,$_GET['olt']);
$status=mysqli_real_escape_string($mon3,$_GET['status']);
$orderby=mysqli_real_escape_string($mon3,$_GET['orderby']);
$offset=mysqli_real_escape_string($mon3,$_GET['offset']);
if ($offset=="")
	$offset=0;

	
echo "<form name=pons action=?>";



echo "		
		<select name=olt onchange=\"this.form.submit()\" style=width:250px> <option value=\"\" ";
			if($olt=="")
				echo " selected ";
		echo">Select a OLT</option>";
		$olts=$mon3->query("select name,id from ftth_olt");
		while($olta=$olts->fetch_assoc())
		{
			echo "<option value=".$olta['id'];
				if($olta['id']==$olt)
					echo " selected";
			echo ">".$olta['name']." </option>";
		}
	
echo	"</select>";




if($olt>0)
{		echo "<select name=pon onchange=\"this.form.submit()\" style=width:250px> <option value=\"\"  ";
			if($pon=="")
				echo " selected ";
		echo">Select a PON</option>";
		$pons=$mon3->query("select name,card,pon from ftth_pons where olt_id=$olt order by name");
		while($pona=$pons->fetch_assoc())
		{
			echo "<option value=".$pona['card']."-".$pona['pon'];
			if($pon==$pona['card']."-".$pona['pon'])
				echo " selected ";
			echo ">".$pona['card']."-".$pona['pon']." ".$pona['name']." </option>";
		}
	
	echo	"</select>";
}
	echo"<select name=status onchange=\"this.form.submit()\" style=width:250px> <option value=\"\"  ";
			if($status=="")
				echo " selected ";
		echo">Select ONT Status</option>
		<option value=Down";
			if($status=="Down")
				echo " selected ";
			echo ">Down</option>
		<option value=OMCIE";
			if($status=="OMCIE")
				echo " selected ";
			echo ">OMCI err</option>
			<option value=Rg";
			if($status=="Rg")
				echo " selected ";
			echo ">Rgcom & setup err</option>
			<option value=RXP";
			if($status=="RXP")
				echo " selected ";
			echo ">bad rxp</option>
			
			<option value=Unknown";
			if($status=="Unknown")
				echo " selected ";
			echo ">Unknown</option>

			<option value=Up-Active";
			if($status=="Up-Active")
				echo " selected ";
			echo ">Up-Active</option>";


			
	
	echo	"</select>";
	
	
	
	
	
	

	echo "<input type=hidden name=gpon value=1>
	</form>";
if($pon!="")
 echo " <a href=webservice.php?downloadgponlivestatus=1&olt=$olt&pon=$pon>Download live status</a>";
	
if($pon!="" && $olt>0)
{$where.=" AND ont_id LIKE \"1-".$pon."-%\" ";
}
if($olt != "")
{ 
$where.=" AND olt_id=\"".$olt."\" ";
}
if($status!="")
{$where.=" AND status LIKE '%".$status."%'";
} 
if($orderby!="")
{$where.=" order by ".$orderby;
} 



//echo "query:". $where ." -> pon".$pon ;

$onts=$mon3->query("select ftth_ont.fsan,ftth_ont.olt_id,ftth_ont.ont_id,ftth_ont.mng_ip,ftth_ont.status_timestamp,ftth_ont.status,
ftth_ont.tx,ftth_ont.rx,ftth_ont.rf,ftth_ont.gpon_traf_rx,ftth_ont.gpon_traf_tx,ftth_ont.errors, properties.address,properties.id,properties.ref
 from ftth_ont left join connections on ftth_ont.fsan=connections.equip_id
 left join properties on connections.property_id=properties.id
 where olt_id>0 and ont_id!=\"\" " . $where." and connections.date_end = '0000-00-00' order by ftth_ont.fsan limit ".$offset.",50 ");
 echo mysqli_error($mon3);
 
 echo"<table cellpadding=5>
 <tr> <th>fsan</th><th>olt</th><th>ont</th> <th>ref<th>address </th> <th>status </th><th>rx olt </th> <th>rx ont </th> <th>rf </th>    <tr>";

	$count=$mon3->query("select count(*) from ftth_ont left join connections on ftth_ont.fsan=connections.equip_id where olt_id>0 and ont_id!=\"\" ".$where)->fetch_row();

	while($ont=$onts->fetch_assoc())
	{  
	
	echo	"<tr><td><a href=?equip=".$ont['fsan']."&equip_type=GPON>  ".$ont['fsan']."</a> </td>
			<td>".$ont['olt_id']. "</td>
			<td><a href=http://".$ont['mng_ip'].">".$ont['ont_id']. "</a></td>
			<td width=50px><a href=?props=1&propid=".$ont['id'].">".$ont['ref']. "</td>
			<td width=250px><a href=?props=1&propid=".$ont['id'].">".$ont['address']. "</td>
			<td width=150px>".substr($ont['status'],0,25). "</td>
			<td>".$ont['tx']. "</td>
			<td>".$ont['rx']. "</td>
			<td>".$ont['rf']. "</td>

			<td>".substr($ont['errors'],20,10). "</td>			
			
			
			
			</tr>"; 
	}	

echo "</table><br>";


if ($count[0]>50)
{
	$lastp=ceil($count[0]/50);
	$curpage=($offset/50)+1;
	
//	echo "<br> curr: $curpage <br>";

	
//print initial page
	if($curpage>1)
	{
		echo "<a href=?gpon=1&pon=$pon&olt=$olt&order=$orderby&status=$status&offset=0>|<</a> ";
	}
//print page -2
	if($curpage>2)
	{
		echo "<a href=?gpon=1&pon=$pon&olt=$olt&order=$orderby&status=$status&offset=".($curpage-3)*50 .">".($curpage-2) ."</a> ";
	}
//print page -1
	if($curpage>1)
	{
		echo "<a href=?gpon=1&pon=$pon&olt=$olt&order=$orderby&status=$status&offset=".($curpage-2)*50 .">".($curpage-1) ."</a> ";
	}
//print curpage	
	
		echo " <b> $curpage </b> ";
//print page -1
	if($curpage<$lastp)
	{
		echo "<a href=?gpon=1&pon=$pon&olt=$olt&order=$orderby&status=$status&offset=".($curpage)*50 .">".($curpage+1) ."</a> ";
	}
	if($curpage<$lastp-1)
	{
		echo "<a href=?gpon=1&pon=$pon&olt=$olt&order=$orderby&status=$status&offset=".($curpage+1)*50 .">".($curpage+2) ."</a> ";
	}	
		
		if($curpage<$lastp)
	{
		echo "<a href=?gpon=1&pon=$pon&olt=$olt&order=$orderby&status=$status&offset=".($lastp-1)*50 .">>|</a> ";
	}	
		
}
else
{
	$curpage=1;
}



echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results <br>";
/*
echo "<br>
<a href=webservice.php?dump_onts=1>download serials</a>
";
*/


echo "<br><a href=# id=click_dump_onts>download serials</a>";
?>

<script>
$("#click_dump_onts").on('click', function()
{
    var pon = $("select[name=pon]").val();
    var olt = $("select[name=olt]").val();
    var status = $("select[name=status]").val();
    var url_onts = "webservice.php?dump_onts=1&pon="+pon+"&olt="+olt+"&status="+status;
    //console.log(url_leads);
    //&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus
	//console.log("webservice.php?dump_prop=1&searchb="+$("input[name=searchb]").val());
	location.href = url_onts;
});
</script>	
 
 
 
 
 
 
 
 
 
 
 
 
 
 



