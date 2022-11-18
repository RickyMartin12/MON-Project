<?php


////////// FWA //////////////////////////////////////////











$ant=mysqli_real_escape_string($mon3,$_GET['ant']);
$site=mysqli_real_escape_string($mon3,$_GET['site']);
$status=mysqli_real_escape_string($mon3,$_GET['status']);
$orderby=mysqli_real_escape_string($mon3,$_GET['orderby']);
$offset=mysqli_real_escape_string($mon3,$_GET['offset']);
if ($offset=="")
	$offset=0;

	
echo "<form name=pons action=?>";



echo "
		<select name=site onchange=\"this.form.submit()\"> <option value=\"\" ";
			if($site=="")
				echo " selected ";
		echo">Select a Headend POP</option>";
		$olts=$mon3->query("select * from headend_pop where type=\"mast\"");
		while($olta=$olts->fetch_assoc())
		{
			echo "<option value=".$olta['OGR_FID'];
				if($olta['OGR_FID']==$site)
					echo " selected";
			echo ">".$olta['name']." </option>";
		}
	
echo	"</select>";

if($site>0)
{		echo "<select name=ant onchange=\"this.form.submit()\"> <option value=\"\"  ";
			if($pon=="")
				echo " selected ";
		echo">Select an Antenna</option>";
		$pons=$mon3->query("select id,name from fwa_antennas where headend_pop_id=\"$site\" order by name");
		while($pona=$pons->fetch_assoc())
		{
			echo "<option value=".$pona['id'];
			if($ant==$pona['id'])
				echo " selected ";
			echo ">".$pona['id']."-".$pona['name']. " </option>";
		}
	
	echo	"</select>";
}

	echo "<input type=hidden name=fwa value=1>
	</form>";
	
if($ant!="" && $site>0)
{$where.=" AND antenna=\"$ant\" ";
}

if($site != "")
{ 
$where.=" AND fwa_antennas.headend_pop_id=\"".$site."\" ";
}

if($orderby!="")
{$where.=" order by ".$orderby;
} 



//echo "query:". $where ." -> pon".$pon ;

$onts=$mon3->query("select fwa_cpe.mac,model,mng_ip,status,name,antenna,ip,headend_pop_id,property_id,address,ref
 from fwa_cpe left join fwa_antennas on fwa_cpe.antenna=fwa_antennas.id left join connections on fwa_cpe.mac=connections.equip_id
 left join properties on connections.property_id=properties.id
 where fwa_cpe.mac!=\"\" ". $where." and connections.date_end = '0000-00-00'  limit ".$offset.",50 ");
 echo mysqli_error($mon3);
 
 echo"<table cellpadding=5>
 <tr> <th>Cpe_mac</th><th>Antenna</th><th>Cpe_ip <th>ref<th>address </th> <th>status </th><th>rx olt </th> <th>rx ont </th> <th>rf </th>    <tr>";

	$count=$mon3->query("select count(fwa_cpe.mac) from fwa_cpe left join fwa_antennas on fwa_cpe.antenna=fwa_antennas.id left join connections on fwa_cpe.mac=connections.equip_id where fwa_cpe.mac!=\"\" ". $where)->fetch_row();

	while($ont=$onts->fetch_assoc())
	{  
	
	echo	"<tr><td><a href=?equip=".$ont['mac']."&equip_type=FWA>".$ont['mac']. "</a></td>
			<td><a href=http://".$ont['ip'].">".$ont['name']."</a> 
			<td><a href=http://".$ont['mng_ip'].">".$ont['mng_ip']. "</a></td>
			<td width=50px><a href=?props=1&propid=".$ont['property_id'].">".$ont['ref']. " </a> </td>
			<td width=250px><a href=?props=1&propid=".$ont['property_id'].">".$ont['address']. "</a> </td>
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
		echo "<a href=?fwa=1&ant=$ant&site=$site&order=$orderby&offset=0>|<</a> ";
	}
//print page -2
	if($curpage>2)
	{
		echo "<a href=?fwa=1&ant=$ant&site=$site&order=$orderby&offset=".($curpage-3)*50 .">".($curpage-2) ."</a> ";
	}
//print page -1
	if($curpage>1)
	{
		echo "<a href=?fwa=1&ant=$ant&site=$site&order=$orderby&offset=".($curpage-2)*50 .">".($curpage-1) ."</a> ";
	}
//print curpage	
	
		echo " <b> $curpage </b> ";
//print page -1
	if($curpage<$lastp)
	{
		echo "<a href=?fwa=1&ant=$ant&site=$site&order=$orderby&offset=".($curpage)*50 .">".($curpage+1) ."</a> ";
	}
	if($curpage<$lastp-1)
	{
		echo "<a href=?fwa=1&ant=$ant&site=$site&order=$orderby&offset=".($curpage+1)*50 .">".($curpage+2) ."</a> ";
	}	
		
		if($curpage<$lastp)
	{
		echo "<a href=?fwa=1&ant=$ant&site=$site&order=$orderby&offset=".($lastp-1)*50 .">>|</a> ";
	}	
		
}
else
{
	$curpage=1;
}


//href=webservice.php?dump_antenna=1
echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results <br><br>
<a href=# id=click_dump_antenna>download serials</a>
";
 ?>

<script>
$("#click_dump_antenna").on('click', function()
{
    var ant = $("select[name=ant]").val();
    var site = $("select[name=site]").val();
    var url_antennas = "webservice.php?dump_antenna=1&ant="+ant+"&site="+site;
    //console.log(url_leads);
    //&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus
	//console.log("webservice.php?dump_prop=1&searchb="+$("input[name=searchb]").val());
	location.href = url_antennas;
});
</script>	
 
 
 
 
 
 
 
 
 
 
 
 
 
 



