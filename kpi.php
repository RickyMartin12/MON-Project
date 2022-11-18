<?php

require '/var/www/html/init_web.php';


$folder = dirname(__FILE__);

serverNameFolder($folder);

$page=$_GET['page'];
$stop=$_GET['stop'];
if($page=="")
{$page=1;}
elseif($page=="4")
{$page=1;}

?>
<html>

<head>
<title>Lazer KPI page</title>
 
<script type=text/javascript src='js/jquery.min.js'></script>
<script src="js/Chart.js"></script> 
 
</head>

<body>

<?php

if($page==1)
{

//kpi 
//echo "page ".$page;

$leads_m=$mon3->query("select count(id) from property_leads where date_lead LIKE \"".date("Y-m")."%\"")->fetch_assoc();
$leads_y=$mon3->query("select count(id) from property_leads where date_lead LIKE \"".date("Y")."%\"")->fetch_assoc();
$contrs_m=$mon3->query("select count(id) from property_leads where date_papwk LIKE \"".date("Y-m")."%\"")->fetch_assoc();
$contrs_y=$mon3->query("select count(id) from property_leads where date_papwk LIKE \"".date("Y")."%\"")->fetch_assoc();
$cons_m=$mon3->query("select count(id) from property_leads where date_installed LIKE \"".date("Y-m")."%\"")->fetch_assoc();
$cons_y=$mon3->query("select count(id) from property_leads where date_installed LIKE \"".date("Y")."%\"")->fetch_assoc();

$subs=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where services.date_end=\"0000-00-00\" ")->num_rows;

echo"
<table style='width: 100%'>
<tr><td align=center> <h2> Network KPIs</h2> 

<table width=100%> 



<tr><td> connections by zone:<br>
<tr><td>

<table style='width: 100%'>
<tr><th>area<th>total properties<th> Total de Connections by Year
";
$zones=array();

$zn=array();
$nt=array();
$znYTD=array();
$n=0;

$refs=$mon3->query("select areacode,zone from area_codes");
while($ref=$refs->fetch_assoc())
{
	$count=$mon3->query("select count(ref) from properties where ref LIKE \"".$ref['areacode']."%\" ")->fetch_assoc();
	
	$countY=$mon3->query("select count(connections.property_id) from connections left join properties on connections.property_id=properties.id 
	where properties.ref LIKE \"".$ref['areacode']."%\" AND connections.date_start LIKE \"".date("Y")."-%\"  ")->fetch_assoc();	
	
	$zones[$ref['zone']]+=$count['count(ref)'];
	$zonesYTD[$ref['zone']]+=$countY['count(connections.property_id)'];
//	echo $ref['areacode']." ".$count['count(ref)']."<br>";
	
}
foreach($zones as $zone => $number)
{
    $zn[$n] = $zone;
    $nt[$n] = $number;
    $znYTD[$n]=$zonesYTD[$zone];
	echo "<tr><td align='center'>".$zone . "<td align='center'>". $number ."<td align='center'>".$zonesYTD[$zone];
	$tot1+=$number;
	$tot2+=$zonesYTD[$zone];
	$n++;
}

echo "
<tr><td align='center'><b>totals:</b><td align='center'>$tot1<td align='center'>$tot2
</table>
<br><br>
";

$val_1 = "";
$val_2 = "";
$val_3 = "";
for($i=0; $i<$n; $i++)
{
    if($i == $n - 1)
    {
        $val_1 .= $zn[$i];
        $val_2 .= $nt[$i];
        $val_3 .= $znYTD[$i];
    }
    else
    {
        $val_1 .= $zn[$i]."," ;
        $val_2 .= $nt[$i]."," ;
        $val_3 .= $znYTD[$i]."," ;
    }
}

?>
<a href="webservice.php?download_kpi_1=1&zn=<?php echo $val_1; ?>&nt=<?php echo $val_2; ?>&znYTD=<?php echo $val_3; ?>&n_val=<?php echo $n; ?>">Download Connections By Zone (Excel)</a>


<?php

    $month_3=date("Y-m", mktime(0, 0, 0, date("m")-3, 1, date("Y")));
    $month_2=date("Y-m", mktime(0, 0, 0, date("m")-2, 1, date("Y")));
    $month_1=date("Y-m", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
    $month=date("Y-m");

    $techs=$mon3->query("select username from users where is_tech=1 ");

    $sales_3=$mon3->query("select count(id) from property_leads where date_papwk LIKE \"$month_3%\" ")->fetch_assoc();
    $sales_2=$mon3->query("select count(id) from property_leads where date_papwk LIKE \"$month_2%\" ")->fetch_assoc();
    $sales_1=$mon3->query("select count(id) from property_leads where date_papwk LIKE \"$month_1%\" ")->fetch_assoc();
    $sales=$mon3->query("select count(id) from property_leads where date_papwk LIKE \"$month%\" ")->fetch_assoc();


    $months = array($month_3, $month_2, $month_1, $month);
    echo"
    <br><br>
    Installs per tech
    <table border=0 cellspacing=0 bgcolor=red style=\"color: white;\" > 
    <tr><th>tech<th>$month_3 <th>$month_2 <th>$month_1 <th> ".$month
    //." <th> Totals </tr>"
    ;

    echo "<tr><td><b>contracts in</b> 
    <td  align=center  bgcolor=white  style=\"color: black;\"><b>".$sales_3['count(id)']." 
    <td  align=center bgcolor=white  style=\"color: black;\"><b> ".$sales_2['count(id)']."
    <td  align=center bgcolor=white  style=\"color: black;\"><b>".$sales_1['count(id)']."
    <td  align=center bgcolor=white  style=\"color: black;\"><b>".$sales['count(id)']
    //."<td   align=center bgcolor=white  style=\"color: black;\"><b>".
    ;

    $sales_ar = array();

    $tinstalls_3=0;
    $tinstalls_2=0;
    $tinstalls_1=0;
    $tinstalls=0;

    while( $tech=$techs->fetch_assoc() ){


        $installs_3=$mon3->query("select count(id) from property_leads where technician=\"".$tech['username']."\" AND date_installed LIKE \"$month_3%\" ")->fetch_assoc();
        $tinstalls_3+=$installs_3['count(id)'];
        $installs_2=$mon3->query("select count(id) from property_leads where technician=\"".$tech['username']."\" AND date_installed LIKE \"$month_2%\" ")->fetch_assoc();
        $tinstalls_2+=$installs_2['count(id)'];
        $installs_1=$mon3->query("select count(id) from property_leads where technician=\"".$tech['username']."\" AND date_installed LIKE \"$month_1%\" ")->fetch_assoc();
        $tinstalls_1+=$installs_1['count(id)'];
        $installs=$mon3->query("select count(id) from property_leads where technician=\"".$tech['username']."\" AND date_installed LIKE \"$month%\" ")->fetch_assoc();
        $tinstalls+=$installs['count(id)'];

        echo "
    <tr><td>".$tech['username']." 
    <td  align=center bgcolor=white  style=\"color: black;\">".$installs_3['count(id)']." 
    <td  align=center bgcolor=white  style=\"color: black;\"> ".$installs_2['count(id)']."
    <td  align=center bgcolor=white  style=\"color: black;\">".$installs_1['count(id)']."
    <td  align=center bgcolor=white  style=\"color: black;\">".$installs['count(id)']
    //."<td  align=center bgcolor=white  style=\"color: black;\">". (($installs['count(id)']) + ($installs_1['count(id)']) + ($installs_2['count(id)']) + ($installs_3['count(id)']))
        ;

    }


    echo "
    <tr><td><b>total techs</b>
    <td  align=center bgcolor=white  style=\"color: black;\">".$tinstalls_3." 
    <td  align=center bgcolor=white  style=\"color: black;\"> ".$tinstalls_2."
    <td  align=center bgcolor=white  style=\"color: black;\">".$tinstalls_1."
    <td  align=center bgcolor=white  style=\"color: black;\">".$tinstalls
    //."<td  align=center bgcolor=white  style=\"color: black;\">"
    ;

    $installs_3=$mon3->query("select count(id) from property_leads where date_installed LIKE \"$month_3%\" ")->fetch_assoc();
    $installs_2=$mon3->query("select count(id) from property_leads where date_installed LIKE \"$month_2%\" ")->fetch_assoc();
    $installs_1=$mon3->query("select count(id) from property_leads where date_installed LIKE \"$month_1%\" ")->fetch_assoc();
    $installs=$mon3->query("select count(id) from property_leads where date_installed LIKE \"$month%\" ")->fetch_assoc();

    echo"
     <tr><td><b>gross installs</b>
    <td  align=center bgcolor=white  style=\"color: black;\"><b>".$installs_3['count(id)']." 
    <td  align=center bgcolor=white  style=\"color: black;\"><b>".$installs_2['count(id)']."
    <td  align=center bgcolor=white  style=\"color: black;\"><b>".$installs_1['count(id)']."
    <td  align=center bgcolor=white  style=\"color: black;\"><b>".$installs['count(id)']
    //."<td  align=center bgcolor=white  style=\"color: black;\"><b> </tr>"
    ;

    echo "</table>";
    
    // KPI on lead time


    ?>
    <br><br>

    <a href="webservice.php?download_kpi_install_kpi=1&z">Download Installs per tech (Download)</a>


    <?php

    echo"
    <br><br>
    
    KPI Information
    
    <table border=0 cellspacing=0 bgcolor=red style=\"color: white;\" > 
    <tr ><th>KPI<th>$month_3 <th>$month_2 <th>$month_1 <th> ".$month." ";

    $kpis=array("leads_in","rejected_leads_in","avg_lead_reply_time","contracts_in","installs","avg_contract_to_install_time");
    foreach($kpis as $kpi)
    {
        echo "<tr><td>" . $kpi;
        $values = $mon3->query("select month,value from kpis where kpi=\"" . $kpi . "\" and ( month=\"$month_3\" or month=\"$month_2\" or month=\"$month_1\" ) order by month");
        while ($qvalue = $values->fetch_assoc())
        {
            echo "<td   align=center bgcolor=white  style=\"color: black;\"> " . $qvalue['value'];
        }

        // Month to date
        $qvalue=0;
        $month=date("Y-m");

        switch($kpi)
        {
            case "leads_in":
                $leads_in=$mon3->query("select count(id) from property_leads where date_lead like \"".$month."%\" ")->fetch_assoc();
                $qvalue=$leads_in['count(id)'];
                break;


            case "rejected_leads_in":
                $leads_in=$mon3->query("select count(id) from property_leads where date_lead like \"".$month."%\" AND (status=3 OR status =4 OR status=9) ")->fetch_assoc();
                $qvalue=$leads_in['count(id)'];
                break;


            case "avg_lead_reply_time":
                $leads_in=$mon3->query("select date_lead,date_viability from property_leads where date_lead like \"".$month."%\" and  date_viability !=\"\"");
                //echo $mon->error;
                $leads_num=$leads_in->num_rows;
                $totaldias=0;
                while($lead=$leads_in->fetch_assoc())
                {

                    $totaldias+=getWorkingDays($lead['date_lead'], $lead['date_viability']);

                }
                $avg=round($totaldias/$leads_num,1);
                $qvalue=$avg;;
                break;

            case "contracts_in":
                $leads_in=$mon3->query("select count(id) from property_leads where date_papwk like \"".$month."%\" ")->fetch_assoc();
                $qvalue=$leads_in['count(id)'];
                break;


            case "installs":
                $leads_in=$mon3->query("select count(id) from property_leads where date_installed like \"".$month."%\" ")->fetch_assoc();
                $qvalue=$leads_in['count(id)'];
                break;


            case "avg_contract_to_install_time":
                $installs_in=$mon3->query("select date_papwk,date_installed from property_leads where date_installed like \"".$month."%\" and date_papwk!=\"\" ");
                //echo $mon->error;
                $installs_num=$installs_in->num_rows;
                $totaldias=0;
                while($install=$installs_in->fetch_assoc())
                {
                    $totaldias+=getWorkingDays($install['date_papwk'], $install['date_installed']);
                }
                $avg=round($totaldias/$installs_num,1);
                $qvalue=$avg;
                break;


        }

        echo "<td   align=center bgcolor=white style=\"color: black;\"> ".$qvalue;



    }

    echo "</table>";


    


?>
<br><br><br>
<table>
    <td width=10% align=center> <h2> Productivity meter </h2> 
        <table height=1000px width=100%> 
            <tr><td>
            <div id=thermo>
            <div>
                <img id=thermogif src="includes/thermo.php?goal=4000&startup=3000&current="<?php echo $subs?>>
            </div>
        </table>
    <td width="45%" align="center" > <h2> Installations KPIs </h2>
        <table  height="1000px" width="100%"> 
            <tr><td colspan="2" width="45%"> <h3>Stats for <?php echo date("M-Y"); ?></h3> <td width=10%> <td colspan=2 width=45%> <h3>Stats for <?php echo date("Y"); ?></h3>
            <tr><td width=25%>total leads in<td width=20%> <?php echo $leads_m['count(id)']; ?> <td width=10%><td width=25%>total leads in<td width=20%><?php echo $leads_y['count(id)']; ?>
            <tr><td>total contracts in<td><?php echo $contrs_m['count(id)']; ?> <td><td>total contracts in<td> <?php echo $contrs_y['count(id)']; ?>
            <tr><td>total connections<td><?php echo $cons_m['count(id)']; ?> <td><td>total connections<td> <?php echo $cons_y['count(id)']; ?>
            <tr><td colspan=5><br>

            <?php
                /////////////////////////numbers
                $curyear=date("Y");
                $curmonth=date("m");

                $fibrecust=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where connections.type=\"GPON\" ");
                $fibrecustff=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where connections.type=\"COAX\" ");

                ////echo $mon3->error;
                $coaxcust=$mon3->query("select distinct(connections.property_id) from services left join connections on services.connection_id=connections.id where connections.type=\"COAX\" ");
                ////echo $mon3->error;
                $tfinalconns=$mon3->query("select property_id from connections where type=\"GPON\" ");
                ////echo $mon3->error;
                $tfinalconc=$mon3->query("select property_id from connections where type=\"COAX\" ");
                ////echo $mon3->error;

                $internetsf=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
                where services.type=\"INT\" AND connections.type=\"GPON\" ")->fetch_row();
                ////echo $mon3->error;
                $internetsc=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
                where services.type=\"INT\" AND connections.type=\"COAX\"  ")->fetch_row();
                ////echo $mon3->error;

                $tvsf=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
                where services.type=\"TV\" AND connections.type=\"GPON\" ")->fetch_row();
                ////echo $mon3->error;
                $tvsc=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
                where services.type=\"TV\" AND connections.type=\"COAX\"  ")->fetch_row();
                ////echo $mon3->error;

                $tlsf=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
                where services.type=\"PHN\" AND connections.type=\"GPON\" ")->fetch_row();
                ////echo $mon3->error;
                $tlsc=$mon3->query("select count(services.id) from services left join connections on services.connection_id=connections.id 
                where services.type=\"PHN\" AND connections.type=\"COAX\"  ")->fetch_row();
                ////echo $mon3->error;
            ?>

            <tr><td  colspan=5> <h3>Connections</h3>
            <tr>
                <td > Fibre homes active: 
                    <td > <?php echo $fibrecust->num_rows; ?> <td>
                        <td >Coax homes active: 
                            <td> <?php echo $coaxcust->num_rows; ?>
            <tr>
                <td > Fibre homes connected: 
                    <td> <?php echo $tfinalconns->num_rows; ?><td>
                        <td >Coax homes connected: 
                            <td> <?php echo $tfinalconc->num_rows; ?>

            <tr>
                <td colspan=5 align=center height=200px>
                <table ><tr><td width=400px>

                <?php
                        $yearc=$curyear-2;
                        for ($i=1;$i<13;$i++)
                        {
                            $month=str_pad($i, 2, '0', STR_PAD_LEFT);
                            $temp=$mon3->query("select count(id) from connections where date_start like \"".$yearc."-".$month."%\" 
                            AND type=\"GPON\" ")->fetch_row();
                            //echo $mon3->error;
                            $conn2[$i]=$temp[0];
                            $yearcn+=$temp[0];
                        
                        }
                        
                        
                         
                         
                        $yearb=$curyear-1;
                        for ($i=1;$i<13;$i++)
                        {
                            $month=str_pad($i, 2, '0', STR_PAD_LEFT);
                            $temp=$mon3->query("select count(id) from connections where date_start like \"".$yearb."-".$month."%\" 
                            AND type=\"GPON\" ")->fetch_row();
                            //echo $mon3->error;
                            $conn1[$i]=$temp[0];
                            $yearbn+=$temp[0];
                        }
                         
                        $year=$curyear;
                        for ($i=1;$i<=$curmonth;$i++)
                        {
                            $month=str_pad($i, 2, '0', STR_PAD_LEFT);
                            $temp=$mon3->query("select count(id) from connections where date_start like \"".$year."-".$month."%\" 
                            AND type=\"GPON\" ")->fetch_row();
                            //echo $mon3->error;
                            $conn0[$i]=$temp[0];
                            $yearn+=$temp[0];
                        //	echo "cons =".$temp[0]."<br>";
                        }
                        
                        
                        
                        
                        
                        
                        
                        ?>
                        
                        
                        <canvas id="myChart" width="400" >
                        
                        <td width=300px>
                        </canvas> <canvas id="myChart2" height=200 ></canvas>
                        
                        </table>
                        <script>
                        var ctx2 = document.getElementById("myChart2").getContext('2d');
                        var myChart = new Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels: ["<?php echo $yearc; ?>", "<?php echo $yearb; ?>", "<?php echo $year; ?>"],
                                datasets: [
                                
                                {
                        
                                    label: '# of Fibre connections <?php echo $yearc; ?>',
                                    data: [ <?php 
                                    echo $yearcn;
                                        ?>,0,0 ],
                                    backgroundColor: [
                                        'rgba(150, 150, 150, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(0,0,255,1)',
                                    ],
                                    borderWidth: 1
                                
                                
                                },
                                
                                
                                
                                {
                                    label: '# of Fibre connections <?php echo $yearb; ?>',
                                    data: [0,<?php 
                                    echo $yearbn;
                                        
                                        ?>,0 ],
                                    backgroundColor: [
                                         'rgba(150, 150, 150, 0.2)',
                                        'rgba(150, 150, 150, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(0,255,0,1)',
                                         'rgba(0,255,0,1)'
                                        
                                    ],
                                    borderWidth: 1
                                },
                                {
                        
                                    label: '# of Fibre connections <?php echo $year; ?>',
                                    data: [ 0,0,<?php 
                                    echo $yearn;
                                        ?> ],
                                    backgroundColor: [
                                            'rgba(255,0,0,1)',
                                            'rgba(255,0,0,1)',
                                            'rgba(255,0,0,1)'
                         
                                    ],
                                    borderColor: [
                                        'rgba(255,0,0,1)',
                                        'rgba(255,0,0,1)',
                                        'rgba(255,0,0,1)'
                                    ],
                                    borderWidth: 1
                                
                                
                                }
                                ]
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
                        
                        
                        
                        
                        
                        
                        var ctx = document.getElementById("myChart").getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                                datasets: [
                                
                                {
                        
                                    label: '# of Fibre connections <?php echo $yearc; ?>',
                                    data: [ <?php 
                                    for($i=1;$i<=sizeof($conn2);$i++)
                                    {	
                                        if($i>1) echo ",";
                                        echo $conn2[$i];
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
                                        'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)',
                                         'rgba(0,0,255,1)'
                                    ],
                                    borderWidth: 1
                                
                                
                                },
                                
                                
                                
                                {
                                    label: '# of Fibre connections <?php echo $yearb; ?>',
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
                                         'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)',
                                        'rgba(0,255,0,1)'
                                        
                                    ],
                                    borderWidth: 1
                                },
                                {
                        
                                    label: '# of Fibre connections <?php echo $year; ?>',
                                    data: [ <?php 
                                    for($i=1;$i<=sizeof($conn0);$i++)
                                    {	
                                        if($i>1) echo ",";
                                        echo $conn0[$i];
                                    }
                                        ?> ],
                                    backgroundColor: [
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
                                
                                
                                }
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                ]
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
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        echo "<tr><td colspan=2> <h3> Services</h3> </td></tr>
                        <tr><td > Internet services active: <td>" . ($internetsf[0]+$internetsc[0]) ." <br> <font size=1>". $internetsf[0]." GPON+ ".$internetsc[0]." COAX</font>
                        <tr><td>Phone services active: <td> ".($tlsc[0]+$tlsf[0]) ."<br><font size=1>". $tlsf[0]." GPON+".$tlsc[0]." COAX  
                        <tr><td > TV services active: <td> ".($tvsc[0]+$tvsf[0]) ."<br><font size=1>". $tvsf[0]." GPON+".$tvsc[0]." COAX 
                        
                        
                        
                        </table>";

                    }
                    elseif($page==2)
                    {
                        //channels

                        $channels=$mon3->query("select * from TV_channels order by name");
                        while($channel=$channels->fetch_assoc())
                        {
                        
                            echo "<img  width=200px height=113px src=img/get_thumb.php?sid=".$channel['sid']."&for=gif>";
                        }

                    }
                    elseif($page==3)
                    {
                        //graphs
                        ?>
                        <font align=center> <h3>Internet feeds</h3> </font>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=22&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=22&rra_id=0&view_type=tree></a>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=636&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=636&rra_id=0&view_type=tree></a>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=29&rra_id=all ><img width=450px src= http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=29&rra_id=0&view_type=tree ></a>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=214&rra_id=all ><img width=450px src= http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=214&rra_id=0&view_type=tree ></a>

                        <font align=center><h3>OLTs</h3></font>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=42&rra_id=all ><img width=450px src= http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=42&rra_id=0&view_type=tree></a>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=640&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=640&rra_id=0&view_type=tree ></a>

                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=623&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=623&rra_id=0&view_type=tree ></a>

                        <font align=center><h3>CMTSs</h3></font>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=40&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=40&rra_id=0&view_type=tree ></a>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=41&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=41&rra_id=0&view_type=tree ></a>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=621&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=621&rra_id=0&view_type=tree ></a>

                        <font align=center><h3>Links</h3></font>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=46&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=46&rra_id=0&view_type=tree ></a>

                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=597&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=597&rra_id=0&view_type=tree ></a>

                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=889&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=889&rra_id=0&view_type=tree ></a>

                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=117&rra_id=all><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=117&rra_id=0&view_type=tree ></a>

                        <font align=center><h3>TV equip</h3></font>
                        <a href=http://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=52&rra_id=all ><img width=450px src=http://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=52&rra_id=0&view_type=tree ></a>
                        <?php
                    }

                    else
                    {
                        echo "no page - something went wrong";
                    }

                ?>






</body>
</html>