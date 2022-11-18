<?php

require_once '../../connection.php';

$tsp=time();

$area=$_GET['area'];
$zone=$_GET['zone'];  


if($zone!=""){
$refs=$mon3->query("select areacode from area_codes where zone=\"$zone\"  ");
while($ref=$refs->fetch_assoc())
{
	
$refb[]=$ref['areacode'];
	
}



}
elseif($area!=""){
	
	
}






?><!DOCTYPE html>
<html>
<meta charset="UTF-8">
	<head>
   <style>
      #map {
        height: 400px;  /* The height is 400 pixels */
        width: 400px;  /* The width is the width of the web page */
       }
	   #mapl {
        height: 900px;  /* The height is 400 pixels */
        width: 1000px;  /* The width is the width of the web page */
       }
	   
    </style>	
	
	
	<script type=text/javascript src='http://code.jquery.com/jquery-3.1.1.min.js'></script>
<script src='http://mon.lazertelecom.com/js/select2.min.js' type='text/javascript'></script>
<link href='http://mon.lazertelecom.com/js/select2.min.css' rel='stylesheet' type='text/css'>

	
	
	</head>
	
	
	
	
	<body>
	
	
	<div id=mapl>
	
	</div>
	
	 <script>
function initMap() {

  var uluru = {lat: 37.047588, lng:-8.024387};
  var map = new google.maps.Map(
      document.getElementById('mapl'), {zoom: 14, center: uluru, mapTypeId: 'hybrid',gestureHandling: 'greedy'});

        var imgf = 'red_12px.png';
		var imgc = 'blue_12px.png';
		var imgi = 'blue_12px.png';

<?php

$pinq="select properties.id,properties.address,properties.coords,connections.type from properties 
left join connections on properties.id=connections.property_id ";
if (sizeof($refb>0)) $pinq .= " where ( ";

foreach($refb as $reff)
{
	$pinq .= " properties.ref LIKE \"$reff%\" OR";
}
$pinq=substr($pinq, 0, sizeof($pinq)-3) .")";

$pins=$mon3->query($pinq);
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	if($pin['coords']!=""){
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"       var pin".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: ";

		  if($pin['type']=="GPON"){echo "imgf";$gpon ++;}elseif($pin['type']=="COAX"){echo "imgc"; $coax++;}else{echo "imgi";}
		  	  
		  
		  echo",
		  title: \"".$pin['address']."\",
		  url: \"index.php?props=1&propid=".$pin['id']."\"
        });
		google.maps.event.addListener(pin".$pin['id'].", 'click', function() {
        window.location.href = this.url;
    });
";
	}
	else
	{
		if($pin['type']=="GPON"){$gpon ++;}elseif($pin['type']=="COAX"){$coax++;}
		
	}



	
}




 
echo"
}
    </script>
    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyAWO_epCqh-ulvpAdar6SycwndXLjRCsDg&callback=initMap\">
    </script>
	

	
	
	
	
";

echo "GPON= $gpon  COAX=$coax

</body></html>

";















