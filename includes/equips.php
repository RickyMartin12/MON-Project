<?php
	

	
$equip=mysqli_real_escape_string($mon3,$_GET['equip']);
$equip_type=mysqli_real_escape_string($mon3,$_GET['equip_type']);	
$list=mysqli_real_escape_string($mon3,$_GET['list']);	









if ($equip_type=="GPON")
{



echo " GPON ONT id <b> $equip </b> <br>";
$ont=$mon3->query("select * from ftth_ont where fsan=\"$equip\"")->fetch_assoc();
$con=$mon3->query("select * from connections where equip_id=\"$equip\"")->fetch_assoc();
$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\"")->fetch_assoc();



if($_GET['commands']==1)
{


gpon_register_ont($equip);

	
}
else{   //if not commands
?>
<script type=text/javascript> 

function getstatus(olt,ont)
{ 
      $.ajax({ method: "GET", url: "webservice.php", data: {'status_ont': 1 ,'olt':olt, 'ont':ont}})
    
        .done(function( data ) 
		{ 
			console.log(data);
          var result = $.parseJSON(data); 
          var string = "<table><tr> <th>ref</th><th>address</th><tr>";
		  var total= result[0]['ref'];
		  console.log(total);
		  var i=0;
        $.each( result, function( key, value ) 
		{ 
			if(i!=0)
			{
             string += "<tr><td><a href=?props=1&propid="+ value['id'] +">  " + value['ref'] + "</a> </td><td>"
			 + value['address'] + "</td></tr>";
			}
			i=1;
		}); 
        string += '</table>'; 
        document.getElementById("tablec").innerHTML = string; 
		document.getElementById("paging").innerHTML = "";
		
		
		
       }); 
}; 
</script>
<?php




echo "status (at ".date("Y-m-d H:i:s",$ont['status_timestamp']).") :<br>
Status: <b>".$ont['status'] ."</b> <br>
ONT rf: ".$ont['rf']."<br>
ONT rx: ".$ont['rx']."<br>
OLT rx: ".$ont['tx']."<br>

<a target=_blank href=?equip=$equip&equip_type=$equip_type&commands=1>commands</a><br><br>



<br>
Address: <a href=?props=1&propid=".$prop['id'].">". $prop['address']
."</a><br>

<a href=?equip=$equip&equip_type=$equip_type&list="; 
if($list!=1)
	echo "1>view as list";
else	
	echo ">view as graph";

echo "</a>

<br>";



if($list!=1)
{

			$i=0;
			$month_1=time()-2678400; //31 dias para tras
			$history=$mon3->query("select timestamp,rx,tx,rf,status from history_ont where fsan=\"$equip\" 
			AND timestamp>$month_1 order by timestamp desc ");
			while($event=$history->fetch_assoc())
			{		
				$eventb[]=$event;
			}
	//		var_dump($eventb);


?>




<canvas id="myChart" width="400" height="200"></canvas>
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        
        datasets: [{
            label: ' ONT rx level',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['rx']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['rx']."}," ;
						$i++;
					}
					else
					{
					//	echo "{ x:".$event['timestamp']*1000 .", y:-40 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(255, 0, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
		{
            label: ' OLT rx level',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['tx']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['tx']."}," ;
						$i++;
					}
					else
					{
					//	echo "{ x:".$event['timestamp']*1000 .", y:-40 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(255, 150, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
		{
            label: ' ONT rf level',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['rf']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['rf']."}," ;
						$i++;
					}
					else
					{
					//	echo "{ x:".$event['timestamp']*1000 .", y:-40 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(255, 240, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
				{
            label: ' ONT Status',
            data: [ <?php 

				foreach($eventb as $event)
				{
					
					if($event['status']=="Up-Active-Active")
					{
						echo "{ x:".$event['timestamp']*1000 .", y:0}," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 240, 0, 0)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				showline: false,
				pointBackgroundColor: 'rgba(0, 240, 0, 1)',
				pointRadius: 2,
        },
			{
            label: ' ONT Status',
            data: [ <?php 

				foreach($eventb as $event)
				{
					
					if($event['status']=="Down")
					{
						echo "{ x:".$event['timestamp']*1000 .", y:0}," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 240, 0, 0)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				showline: false,
				pointBackgroundColor: 'rgba(240, 0, 0, 1)',
				pointRadius: 2,
        },
		
					{
            label: ' ONT Status',
            data: [ <?php 

				foreach($eventb as $event)
				{
					
					if($event['status']!="Down" && $event['status']!="Up-Active-Active")
					{
						echo "{ x:".$event['timestamp']*1000 .", y:0}," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 240, 0, 0)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				showline: false,
				pointBackgroundColor: 'rgba(240, 120, 0, 1)',
				pointRadius: 2,
        },
		
		
		
		
		
		
		
		
		
		]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }],
			xAxes: [{
                ticks: {
                    beginAtZero:false
                },
				type: 'time',
				ticks: {
								source: 'data{x}'
							},
				time: {
                            unit: 'day',
                            tooltipFormat: 'lll',
                        }
            }]
			
        }
    }
});





</script>




<?php
			
			$month_1=time()-2678400; //31 dias para tras
			$history=$mon3->query("select timestamp,gpon_traf_rx,gpon_traf_tx from history_ont where fsan=\"$equip\" 
			AND timestamp>$month_1 order by timestamp ");
			while($event=$history->fetch_assoc())
			{		
				$eventb[]=$event;
			}

?>








traffic (in MB)

<canvas id="myChart2" width="400" height="200"></canvas>
<script>
var ctx2 = document.getElementById("myChart2").getContext('2d');
var myChart2 = new Chart(ctx2, {
    type: 'line',
    data: {
        
        datasets: [{
            label: ' ONT traffic RX',
            data: [ <?php 

				$i=0;
				$rxa=0;
				
				foreach($eventb as $event)
				{
					if(is_numeric($event['gpon_traf_rx']) && $event['gpon_traf_rx']>0 )
					{
						
						$rxdiff=$event['gpon_traf_rx']-$rxa;
						if($rxdiff<0) 
							$rxdiff=$event['gpon_traf_rx'];
						
						echo "{ x:".$event['timestamp']*1000 .", y:".($rxdiff/1000000)."}," ;
						$i++;
						
					echo "\n<!--". $event['gpon_traf_rx']." - ".$rxa." - ".$rxdiff."-->\n";	
						
						$rxa=$event['gpon_traf_rx'];

					}
					else
					{
						//echo "{ x:".$event['timestamp']*1000 .", y:-1 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 255, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
		{
            label: ' OLT traffic TX',
            data: [ <?php 

				$i=0;
				$txa=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['gpon_traf_tx']) && $event['gpon_traf_tx']>0 )
					{
					
						$txdiff=$event['gpon_traf_tx']-$txa;
						if($txdiff<0) 
							$txdiff=$event['gpon_traf_tx'];
					
						echo "{ x:".$event['timestamp']*1000 .", y:".($txdiff/1000000)."}," ;
						$i++;
						$txa=$event['gpon_traf_tx'];
					}
					else
					{
						//echo "{ x:".$event['timestamp']*1000 .", y:-1 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 0, 255, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        }
		
		
		]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }],
			xAxes: [{
                ticks: {
                    beginAtZero:false
                },
				type: 'time',
				ticks: {
								source: 'data{x}'
							},
				time: {
                            unit: 'day',
                            tooltipFormat: 'lll',
                        }
            }]
			
        }
    }
});











</script>



<?php
			
			$month_1=time()-2678400; //31 dias para tras
			$history=$mon3->query("select timestamp,gpon_traf_rx,gpon_traf_tx from history_ont where fsan=\"$equip\" 
			AND timestamp>$month_1 order by timestamp ");
			while($event=$history->fetch_assoc())
			{		
				$eventb[]=$event;
			}


/*			
?>



tests: traffic incremental (in MB)

<canvas id="myChart3" width="400" height="200"></canvas>
<script>
var ctx3 = document.getElementById("myChart3").getContext('2d');
var myChart3 = new Chart(ctx3, {
    type: 'line',
    data: {
        
        datasets: [{
            label: ' ONT traffic inc RX',
            data: [ <?php 

				$i=0;
				$rxa=0;
				
				foreach($eventb as $event)
				{
					if(is_numeric($event['gpon_traf_rx']) && $event['gpon_traf_rx']>0 )
					{
						
						$rxdiff=$event['gpon_traf_rx'];
						
						echo "{ x:".$event['timestamp']*1000 .", y:".($rxdiff/1000000)."}," ;
						$i++;
					
					}
					else
					{
						//echo "{ x:".$event['timestamp']*1000 .", y:-1 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 255, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
		{
            label: ' OLT traffic inc TX',
            data: [ <?php 

				$i=0;
				$txa=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['gpon_traf_tx']) && $event['gpon_traf_tx']>0 )
					{
					
						$txdiff=$event['gpon_traf_tx'];
					
						echo "{ x:".$event['timestamp']*1000 .", y:".($txdiff/1000000)."}," ;
						$i++;
				
					}
					else
					{
						//echo "{ x:".$event['timestamp']*1000 .", y:-1 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 0, 255, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        }
		
		
		]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }],
			xAxes: [{
                ticks: {
                    beginAtZero:false
                },
				type: 'time',
				ticks: {
								source: 'data{x}'
							},
				time: {
                            unit: 'day',
                            tooltipFormat: 'lll',
                        }
            }]
			
        }
    }
});











</script>





<?php

*/





}
else
{


 echo"<table cellpadding=5>
 <tr> <th>datetime</th><th>status</th><th>rx olt </th> <th>rx ont </th> <th>rf </th> <th>traf rx</th><th>traf tx </th> <th>err </th>    
 ";
 

 
$history=$mon3->query("select * from history_ont where fsan=\"$equip\" order by timestamp desc");
echo $mon3->error;
 while($event=$history->fetch_assoc())
{
	echo "<tr><td>".date("Y-m-d H:i:s",$event['timestamp'])."<td>".$event['status']."<td>".$event['rx'].
	"<td>".$event['tx']."<td>".$event['rf']."<td>".$event['gpon_traf_rx']."<td>".$event['gpon_traf_tx']."<td>".$event['errors']."
	
	";

}

echo "</table>";

}


}

}






















//###################################   COAX           ##############################################


elseif($equip_type=="COAX")
{


echo " Modem id <b> $equip </b> <br>";
$ont=$mon3->query("select * from coax_modem where mac=\"$equip\"")->fetch_assoc();
$con=$mon3->query("select * from connections where equip_id=\"$equip\"")->fetch_assoc();
$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\"")->fetch_assoc();





echo "status (at ".date("Y-m-d H:i:s",$ont['status_timestamp']).") :<br>
Status: <b>".$ont['status'] ."</b> <br>
DS pwr: ".$ont['ds_power']."dBm<br>
DS snr: ".$ont['ds_snr']."dB<br>
US pwr: ".$ont['us_power']."dBm<br>
US snr: ".$ont['us_snr']."dB<br>
Rcv pwr: ".$ont['rcv_power']."dBm<br>
Bootfile: ".$ont['bootfile']."<br>
Interface: ".$ont['interface']."<br>
IP: <a href=http://".$ont['mng_ip']."> ".$ont['mng_ip']." </a><br>


<br>
Address: <a href=?props=1&propid=".$prop['id'].">". $prop['address']
."</a><br>

<a href=?equip=$equip&equip_type=$equip_type&list="; 
if($list!=1)
	echo "1>view as list";
else	
	echo ">view as graph";

echo "</a>

<br>";



if($list!=1)
{

			$i=0;
			$month_1=time()-2678400; //31 dias para tras
			$history=$mon3->query("select *	from history_modem where mac=\"$equip\" 
			AND timestamp>$month_1 order by timestamp desc ");
			echo $mon3->error;
			while($event=$history->fetch_assoc())
			{		
				$eventb[]=$event;
			}
			//var_dump($eventb);


?>




<canvas id="myChart" width="400" height="210"></canvas>
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        
        datasets: [{
            label: ' modem US power',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['up_level']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['up_level']."}," ;
						$i++;
					}
					else
					{
						echo "{ x:".$event['timestamp']*1000 .", y:-30 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(255, 0, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
		{
            label: ' Modem US snr',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['up_snr']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".($event['up_snr']+0)."}," ;
						$i++;
					}
					else
					{
						echo "{ x:".$event['timestamp']*1000 .", y:-30 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(255, 150, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
		{
            label: ' Modem DS power',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['down_level']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['down_level']."}," ;
						$i++;
					}
					else
					{
						echo "{ x:".$event['timestamp']*1000 .", y:-30 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 0, 255, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },
			
		{
            label: ' Modem DS snr',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
					if(is_numeric($event['down_snr']))
					{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['down_snr']."}," ;
						$i++;
					}
					else
					{
						echo "{ x:".$event['timestamp']*1000 .", y:-30 }," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 128, 255, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)'
        },


			{
            label: ' Modem status',
            data: [ <?php 

				foreach($eventb as $event)
				{
					
					if($event['status']=="online(pt)")
					{
						echo "{ x:".$event['timestamp']*1000 .", y:60}," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 240, 0, 0)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				showline: false,
				pointBackgroundColor: 'rgba(0, 240, 0, 1)',
				pointRadius: 2,
        },
			{
            label: 'Modem status',
            data: [ <?php 

				foreach($eventb as $event)
				{
					
					if($event['status']=="offline")
					{
						echo "{ x:".$event['timestamp']*1000 .", y:58}," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 240, 0, 0)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				showline: false,
				pointBackgroundColor: 'rgba(240, 0, 0, 1)',
				pointRadius: 2,
        },
		
					{
            label: 'Modem status',
            data: [ <?php 

				foreach($eventb as $event)
				{
					
					if($event['status']!="offline" && $event['status']!="online(pt)")
					{
						echo "{ x:".$event['timestamp']*1000 .", y:59}," ;
						$i++;
					}
				}

				?> ],
				borderColor: 'rgba(0, 240, 0, 0)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				showline: false,
				pointBackgroundColor: 'rgba(240, 120, 0, 1)',
				pointRadius: 2,
        },
		
		
		
		
		
		
		
		
		
		]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }],
			xAxes: [{
                ticks: {
                    beginAtZero:false
                },
				type: 'time',
				ticks: {
								source: 'data{x}'
							},
				time: {
                            unit: 'day',
                            tooltipFormat: 'lll',
                        }
            }]
			
        }
    }
});
</script>




<?php

}
else
{


 echo"<table cellpadding=5>
 <tr> <th>datetime</th><th>status</th>
 <th>DS level</th> <th>DS SNR </th> 
 <th>US level </th> <th>US SNR</th><th>rcv level </th>
 ";
 

 
$history=$mon3->query("select * from history_modem where mac=\"$equip\" order by timestamp desc");

 while($event=$history->fetch_assoc())
{
	echo "<tr><td>".date("Y-m-d H:i:s",$event['timestamp'])."<td>".$event['status'].
	"<td>".$event['down_level']."dBm<td>".$event['down_snr'].
	"dB<td>".$event['up_level']."dBm<td>".$event['up_snr'].
	"dB<td>".$event['gpon_traf_tx']."<td>".$event['errors']."<td>".$event['rcv_level']."dBm
	
	";

}

echo "</table>";

}




}



