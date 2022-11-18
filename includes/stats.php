<?php
$curyear=date("Y");
$curmonth=date("m");
$today=date("Y-m-d");








echo "<table border=1> 
<tr><td colspan=6> <a href=?stats=1&rep=connections>Waiting list report</a><br><br>";









if($_GET['rep']=="connections")
{
	
	
if(isset($_GET['updatelead']))
{
	$lead=$_GET['updatelead'];
	$nps=$_GET['nps'];
	
	$waitingl=$mon3->query("update property_leads set NPS_score=\"$nps\" where id=\"$lead\"");
echo "<script>
  location.replace(\"index.php?stats=1&rep=connections\")
</script>";
	

}
	
	

if(isset($_GET['month']))
	$month=$_GET['month'];
else	
	$month=date("Y-m");



echo "<form action=index.php method=get>
<input type='hidden' name=stats value=1>
<input type='hidden' name=rep value=connections>";
echo "Month: <select name=month onChange=\"submit()\" style=width:150px>";
for($i=0;$i<24;$i++)
{
	$montha=date("Y-m",mktime(0, 0, 0, date("m")-$i, 1, date("Y")));
	echo "<option ";
	if ($month==$montha) echo "selected";
	echo "> $montha</option>";
}
echo "</select>
</form>";



$waitingl=$mon3->query("select * from property_leads where date_installed LIKE \"$month%\" order by date_installed ");
$num_leads=$waitingl->num_rows;





echo "<br><table border=1><tr align=center >
<th>lead id<th>Name/Address<th style=\"max-width: 50px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;\">phone/email<th>services<th>seller
<th>lead date<th>contract date<th>date installed
<th>technician <th> NPS";
while($lead=$waitingl->fetch_assoc())
{
	$dateppw=explode("-",$lead['date_papwk']);
	echo "<tr><td><a href=?propleads=1&lead_id=".$lead['id'].">".$lead['id']." </a>
	<td  style=\"max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;\"
	>".$lead['name']."<br>".$lead['address']."
	<td style=\"max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;\">".
	$lead['phone']."<br> ".$lead['email']."<td>
	INT: ".$lead['internet_prof']."<br>
	TV: ".$lead['tv']."<br>
	
	<td>".$lead['created_by']."
	
	<td>".$lead['date_lead']."<td>".$lead['date_papwk']."<td><b>".$lead['date_installed']."</b>
	<td>".$lead['technician']."<td>";



echo "<form action=index.php method=get>
<input type='hidden' name=stats value=1>
<input type='hidden' name=rep value=connections>
<input type='hidden' name=updatelead value=".$lead['id'].">";
echo "<select name=nps onChange=\"submit()\" style=width:100px>";

for($i=0;$i<11;$i++)
{
	echo "<option ";
	if ($i==$lead['NPS_score']) echo "selected";
	echo "> $i </option>";
}
echo "</select>
</form>";


	

}

echo "</table>";
echo "total:".$num_leads;
}

















elseif($_GET['rep']=="commissions")
{
if(!isset($_GET['month']))
	$month=date("Y-m", mktime(0,0,0,date("m")-1,1,date("Y")));
else
	$month=$_GET['month'];

echo "<br><h3>Monthly commission report for ".$month."</h3>";

$totaln=$mon3->query("select id from property_leads where date_installed LIKE \"".$month."%\" and is_changeover!=1")->num_rows;
$totalc=$mon3->query("select id from property_leads where date_installed LIKE \"".$month."%\" and is_changeover=1")->num_rows;
$target=$mon3->query("select * from connection_targets where month =\"".$month."\" ")->fetch_assoc();
$perc=ceil((($totaln+$totalc)/($target['new']+$target['changeover']))*100);
$baseval=130; //base value in euros
if($perc<75)
	$val=0;
elseif($perc>=75 && $perc<100)
	$val=$baseval*0.75*0.5;
elseif($perc>=100 && $perc<110)
	$val=$baseval*0.5;
elseif($perc>=110 && $perc<125)
	$val=$baseval*1.25*0.5;
else
	$val=$baseval*1.5*0.5;

$sales=ceil($target['equip_sales']/10); // 4techs 4support 4helpers(50%)

echo " Target: ".$target['new']." new and ".$target['changeover']." changeover<br> ".
"Result: $totaln new and $totalc changeover -> <b>$perc%</b> - base commission= <b>$val €</b> <br><br>
Sales total: ".$target['equip_sales']."€ - tech/support gets 1/10 (<b>".$sales."€</b>) 
<br><br>
formula=> total commission= base + $baseval*(nps*0.2 + Mscore*0.3) + equip sales <br><br>".
"helpers get (base+equip)/2 =>($val + $sales) /2 = <b>".($val+$sales)/2 ."€</b><br><br>

"
;












$techs=$mon3->query("select distinct(technician) from property_leads where date_installed LIKE \"".$month."%\" ");
echo "<table style=\"table-layout: fixed;\">";
while($tech=$techs->fetch_assoc())
{
	
	echo "<tr><td colspan=2>".$tech['technician']."
	<tr><th>lead<th>address<th>changeover<th>date<th>NPS<th>M_score<th>pictures<th>";

	$stats=$mon3->query("select id,address,is_changeover,date_installed,NPS_score,manager_score,has_pictures from property_leads where technician=\"".$tech['technician']."\" and date_installed LIKE \"".$month."%\"  order by date_installed");

$totali=$stats->num_rows;	
$nps=0;
$mscore=0;
$pics=0;	

	while($inst=$stats->fetch_assoc())
	{
		$nps+=$inst['NPS_score'];
		$mscore+=(0.5+$inst['has_pictures']/2)*$inst['manager_score'];
		$pics+=$inst['has_pictures'];
		
		echo "<tr><td><a href=http://mon.lazertelecom.com/?propleads=1&lead_id=".$inst['id'].">".$inst['id']."</a> 
			<td width=150px style=\" display: block; overflow:hidden; white-space: nowrap;  text-overflow: ellipsis;\"> ".$inst['address'].
			"<td> ".$inst['is_changeover'].
			"<td> ".$inst['date_installed'].
			"<td>  ".$inst['NPS_score'].
			"<td>  ".$inst['manager_score'].
			"<td>  ".$inst['has_pictures'].
			
			"";
			
	
	
	
	}
	$nps=round($nps/$totali/10,2);
	$mscore=round($mscore/$totali/10,2);
	$pics=round($pics/$totali,2);
	
	echo "<tr><td colspan=7>total installs: $totali # NPS score=".$nps*100 ."% # M score=".$mscore*100 ."% picture=".$pics*100 ."% ".
		"<tr><td colspan=7>total: ".$val."€ + ".$baseval*0.2*$nps ."€ + ".$baseval*0.3*$mscore. "€ + $sales € =<b>"
		.($val+$baseval*0.2*$nps+$baseval*0.3*$mscore+$sales)."€</b>";

	
	echo "<tr><td><br>";
	
}















}


else
{


$fibrecust=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where connections.type=\"GPON\" ");

$fibrecustff=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where connections.type=\"COAX\" ");


echo $mon3->error;
$coaxcust=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where connections.type=\"COAX\" and connections.date_end=\"0000-00-00\" ");
echo $mon3->error;
$tfinalconns=$mon3->query("select property_id from connections where type=\"GPON\" ");
echo $mon3->error;
$tfinalconc=$mon3->query("select property_id from connections where type=\"COAX\" ");
echo $mon3->error;

$internetsf=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
where services.type=\"INT\" AND connections.type=\"GPON\" ")->fetch_row();
echo $mon3->error;
$internetsc=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
where services.type=\"INT\" AND connections.type=\"COAX\"  ")->fetch_row();
echo $mon3->error;

$tvsf=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
where services.type=\"TV\" AND connections.type=\"GPON\" ")->fetch_row();
echo $mon3->error;
$tvsc=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
where services.type=\"TV\" AND connections.type=\"COAX\"  ")->fetch_row();
echo $mon3->error;

$tlsf=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
where services.type=\"PHN\" AND connections.type=\"GPON\" ")->fetch_row();
echo $mon3->error;
$tlsc=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
where services.type=\"PHN\" AND connections.type=\"COAX\"  ")->fetch_row();
echo $mon3->error;










echo "<tr><td width=200 colspan=6> <br>Connections
<tr><td width=200> Fibre homes active: <td width=150> ".$fibrecust->num_rows ." <td width=100px>  
<td width=200>Coax homes active: <td width=150> ".$coaxcust->num_rows ." <td width=100px>
<tr><td width=200> Fibre homes connected: <td width=150> ".$tfinalconns->num_rows ."<td width=100px><td width=200>Coax homes connected: <td width=150> ".$tfinalconc->num_rows ." <td width=100px></tr>
<tr><td colspan=6> <br> Services</td></tr>
<tr><td width=200px> Internet services active: <td width=150>" . ($internetsf[0]+$internetsc[0]) ." <br> <font size=1>". $internetsf[0]." GPON+ ".$internetsc[0]." COAX</font><td width=100px>
<td width=200>Phone services active: <td width=150> ".($tlsc[0]+$tlsf[0]) ."<br><font size=1>". $tlsf[0]." GPON+".$tlsc[0]." COAX  <td width=100px>
<tr><td width=200> TV services active: <td width=150> ".($tvsc[0]+$tvsf[0]) ."<br><font size=1>". $tvsf[0]." GPON+".$tvsc[0]." COAX <td width=100px>
<td width=200> <td width=150>  <td width=100px>
<tr><td width=350 colspan=6> <br>
<tr><td width=350 colspan=3>";


 
 $conns=array();
 
$year=$curyear-1;
for ($i=1;$i<13;$i++)
{
	$month=str_pad($i, 2, '0', STR_PAD_LEFT);
	$temp=$mon3->query("select count(id) from connections where date_start like \"".$year."-".$month."%\" 
	AND type=\"GPON\" ")->fetch_row();
	echo $mon3->error;
	$conn1[$i]=$temp[0];

}
 
$year=$curyear;
for ($i=1;$i<=$curmonth;$i++)
{
	$month=str_pad($i, 2, '0', STR_PAD_LEFT);
	$temp=$mon3->query("select count(id) from connections where date_start like \"".$year."-".$month."%\" 
	AND type=\"GPON\" ")->fetch_row();
	echo $mon3->error;
	$conn0[$i]=$temp[0];
//	echo "cons =".$temp[0]."<br>";
}


?>



<canvas id="myChart" width="400" height="200"></canvas>
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: '# of Fibre connections 2017',
            data: [ <?php 
			for($i=1;$i<=sizeof($conn1);$i++)
			{	
				if($i>1) echo ",";
				echo $conn1[$i];
			}
				?> ],
            backgroundColor: [
                'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)',
				'rgba(150, 150, 150, 0.2)'
            ],
            borderColor: [
                'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)',
				'rgba(100,100,100,1)'
                
            ],
            borderWidth: 1
        },
		{

            label: '# of Fibre connections 2018',
            data: [ <?php 
			for($i=1;$i<=sizeof($conn0);$i++)
			{	
				if($i>1) echo ",";
				echo $conn0[$i];
			}
				?> ],
            backgroundColor: [
                'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)',
				'rgba(255, 100, 100, 0.2)'
				
 
            ],
            borderColor: [
                'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)',
				'rgba(255,0,0,1)'
            ],
            borderWidth: 1
		
		
		}]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>




 
<?php
 echo "<br><br><br>";


echo "<tr><td colspan=6> <h1>Internet feeds (last month)</h1> 

<tr><td colspan=3> NOS - 2Gbps<br>
<a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=22&rra_id=all> 
<img src=http://mon.lazertelecom.com/cacti/graph_image.php?action=view&local_graph_id=22&rra_id=4 width=450px></a>

<td colspan=3> ONI - 1Gbps<br>
<a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=36&rra_id=all> 
<img src=http://mon.lazertelecom.com/cacti/graph_image.php?action=view&local_graph_id=36&rra_id=4 width=450px></a>


<tr><td colspan=3> Cogent - 1Gbps<br>
<a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=29&rra_id=all> 
<img src=http://mon.lazertelecom.com/cacti/graph_image.php?action=view&local_graph_id=29&rra_id=4 width=450px></a>


<td colspan=3> IPTELECOM - 2Gbps<br>

";

















}


 

echo "</table><br>

";






// connections /month    leads /month












