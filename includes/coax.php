<?php
////////// COAX LISTS //////////////////////////////////////////











$upstream=mysqli_real_escape_string($mon3,$_GET['upstream']);
$cmts=mysqli_real_escape_string($mon3,$_GET['cmts']);
//$con_on=mysqli_real_escape_string($mon3,$_GET['con_on']);
$status=mysqli_real_escape_string($mon3,$_GET['status']);
$orderby=mysqli_real_escape_string($mon3,$_GET['orderby']);
$offset=mysqli_real_escape_string($mon3,$_GET['offset']);
if ($offset=="")
	$offset=0;

	
echo "<form name=upstreams action=?>";
/*		
		<select name=con_on onchange=\"this.form.submit()\"> <option value=\"\" ";
			if($con_on=="")
				echo " selected ";
		echo"></option>
		<option value=1 ";
		if($con_on=="1")
				echo " selected ";
				echo ">only active connections</option>

		</select>
*/		
		
echo "		
		<select name=cmts onchange=\"this.form.submit()\" style=width:250px> <option value=\"\" ";
			if($cmts=="")
				echo " selected ";
		echo">Select CMTS</option>";
		$cmtss=$mon3->query("select name,id from coax_cmts");
		while($cmtsa=$cmtss->fetch_assoc())
		{
			echo "<option value=".$cmtsa['id'];
				if($cmtsa['id']==$cmts)
					echo " selected";
			echo ">".$cmtsa['name']." </option>";
		}
	
echo	"</select>


";



if($cmts>0)
{		echo "<select name=upstream onchange=\"this.form.submit()\" style=width:250px> <option value=\"\"  ";
			if($upstream=="")
				echo " selected ";
		echo">Select Upstream</option>";
		$upstreams=$mon3->query("select name,upstream_id from coax_upstreams where cmts_id=$cmts order by name");
		while($upstreama=$upstreams->fetch_assoc())
		{
			echo "<option value=".$upstreama['upstream_id'] ;
			if($upstream==$upstreama['upstream_id'])
				echo " selected ";
			echo ">".$upstreama['upstream_id']."-".$upstreama['name']." </option>";
		}
	
	echo	"</select>";
}
	echo"<select name=status onchange=\"this.form.submit()\" style=width:250px> <option value=\"\"  ";
			if($status=="")
				echo " selected ";
		echo">Select Status</option>
		<option value=offline";
			if($status=="offline")
				echo " selected ";
			echo ">Offline</option>
		<option value=init";
			if($status=="init")
				echo " selected ";
			echo ">init</option>
						
			<option value=online(d)";
			if($status=="online(d)")
				echo " selected ";
			echo ">online(d)</option>
			
			<option value=online";
			if($status=="online")
				echo " selected ";
			echo ">online</option>";
	
	echo	"</select>";
	
	
	
	
	
	

	echo "<input type=hidden name=coax value=1>
	</form>
	";
	
if($upstream!="" && $cmts>0)
{$where.=" AND interface LIKE \"".$upstream."\" ";


 $upstreamd=$mon3->query("select * from coax_upstreams where cmts_id=$cmts and upstream_id=\"$upstream\" ")->fetch_assoc();
 echo "<table><tr>
 <td><a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=".$upstreamd['cacti_graph_snr']."&rra_id=all>
 <img  width=450px  src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=".$upstreamd['cacti_graph_snr'].">  </a>
 
 <td><a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=".$upstreamd['cacti_graph_modems']."&rra_id=all>
 <img  width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=".$upstreamd['cacti_graph_modems']."> </a> 
<tr>
<td><a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=".$upstreamd['cacti_graph_traf']."&rra_id=all>
<img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=".$upstreamd['cacti_graph_traf'].">  </a>

<td><a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=".$upstreamd['cacti_graph_ds']."&rra_id=all>
<img  width=450px  src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=".$upstreamd['cacti_graph_ds']."> </a>
</table><br> ";


}
if($cmts != "")
{ 
$where.=" AND cmts=\"".$cmts."\" ";
}
if($status!="")
{$where.=" AND status LIKE '%".$status."%'";
} 
//if($con_on!="")
//{
$where.=" AND connections.date_end =\"0000-00-00\" ";
//} 

if($orderby!="")
{$where.=" order by ".$orderby;
} 
if($where!="")
{
	$where=" where ". substr($where, 5);
}

//echo "query:". $where ." -> upstream".$upstream ;


$onts=$mon3->query("select coax_modem.mac,coax_modem.cmts,coax_modem.mng_ip,coax_modem.status_timestamp,
coax_modem.status,coax_modem.us_power,coax_modem.ds_power,coax_modem.interface,properties.address,properties.id,properties.ref,connections.id as conid
 from coax_modem left join connections on coax_modem.mac=connections.equip_id
 left join properties on connections.property_id=properties.id " . $where." and connections.date_end = '0000-00-00' AND connections.type = 'COAX' limit ".$offset.",50");
 echo mysqli_error($mon3) ;
 


 echo"<table cellpadding=5>
 <tr> <th>mac</th><th>cmts</th><th>web</th> <th>ref </th><th>address </th><th>bootfile </th> <th>status </th><th>US </th> <th>DS </th> <th>Interface </th>    <tr>";

	$count=$mon3->query("select count(*) from coax_modem left join connections on coax_modem.mac=connections.equip_id ".$where)->fetch_row();

	while($ont=$onts->fetch_assoc())
	{  
	
	echo	"<tr><td><a href=?equip=".$ont['mac']."&equip_type=COAX>  ".$ont['mac']."</a> </td>
			<td>".$ont['cmts']. "</td>
			<td><a href=http://".$ont['mng_ip'].">".$ont['mng_ip']. "</a></td>
			<td width=100px><a href=?props=1&propid=".$ont['id'].">".$ont['ref']. "</td>
			<td width=250px><a href=?props=1&propid=".$ont['id'].">".$ont['address']. "</td>";
			
			$bootfile=$mon3->query("select int_services.filename from connections left join services on connections.id=services.connection_id left join service_attributes on service_attributes.service_id=services.id LEFT JOIN int_services on service_attributes.value=int_services.id where service_attributes.name=\"speed\" and connections.id=\"".$ont['conid']."\" ")->fetch_assoc();
			echo mysqli_error($mon3) ;
			
			echo"<td width=80px>".$bootfile['filename']. "</td>
			<td width=70px>".$ont['status']. "</td>
			<td>".$ont['us_power']. "</td>
			<td>".$ont['ds_power']. "</td>
			<td>".$ont['interface']. "</td>
		
			
			
			
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
		echo "<a href=?coax=1&upstream=$upstream&cmts=$cmts&order=$orderby&status=$status&offset=0>|<</a> ";
	}
//print page -2
	if($curpage>2)
	{
		echo "<a href=?coax=1&upstream=$upstream&cmts=$cmts&order=$orderby&status=$status&offset=".($curpage-3)*50 .">".($curpage-2) ."</a> ";
	}
//print page -1
	if($curpage>1)
	{
		echo "<a href=?coax=1&upstream=$upstream&cmts=$cmts&order=$orderby&status=$status&offset=".($curpage-2)*50 .">".($curpage-1) ."</a> ";
	}
//print curpage	
	
		echo " <b> $curpage </b> ";
//print page -1
	if($curpage<$lastp)
	{
		echo "<a href=?coax=1&upstream=$upstream&cmts=$cmts&order=$orderby&status=$status&offset=".($curpage)*50 .">".($curpage+1) ."</a> ";
	}
	if($curpage<$lastp-1)
	{
		echo "<a href=?coax=1&upstream=$upstream&cmts=$cmts&order=$orderby&status=$status&offset=".($curpage+1)*50 .">".($curpage+2) ."</a> ";
	}	
		
		if($curpage<$lastp)
	{
		echo "<a href=?coax=1&upstream=$upstream&cmts=$cmts&order=$orderby&status=$status&offset=".($lastp-1)*50 .">>|</a> ";
	}	
		
}
else
{
	$curpage=1;
}

//href=webservice.php?dump_coax=1

echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results <br><br>
<a href=# id=click_dump_coax>download serials</a>";
 
?>

<script>
$("#click_dump_coax").on('click', function()
{
    var cmts = $("select[name=cmts]").val();
    var upstream = $("select[name=upstream]").val();
	var status = $("select[name=status]").val();
    var url_coax = "webservice.php?dump_coax=1&cmts="+cmts+"&upstream="+upstream+"&status="+status;
    //console.log(url_leads);
    //&filter=$filter&owner=$owner&filt_op=$fil_op&status=$sstatus
	//console.log("webservice.php?dump_prop=1&searchb="+$("input[name=searchb]").val());
	location.href = url_coax;
});
</script>	
 
 
 
 
 
 
 
 
 
 
 
 
 



