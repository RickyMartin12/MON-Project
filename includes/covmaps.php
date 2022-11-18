<?php

$meo=$_GET['meo'];
$fats=$_GET['fats'];
$poly=$_GET['poly'];
$cables=$_GET['cables'];
$footprint=$_GET['footprint'];
$leads=$_GET['leads'];
$customers=$_GET['customers'];




?>
<table>
<tr><td style="vertical-align:top;">

<form name=filter method=get target="?propleads=1&covmaps=1">
<input type=hidden name=propleads value=1>
<input type=hidden name=covmaps value=1>
<table>
<tr><td>MEO fats<td> <input type=checkbox name=meo onchange="this.form.submit();" <?php if($meo=="on") echo "checked"; ?> >
<tr><td>LZR fats<td><input type=checkbox name=fats onchange="this.form.submit();" <?php if($fats=="on") echo "checked"; ?>>
<tr><td>Polygons<td> <input type=checkbox name=poly onchange="this.form.submit();" <?php if($poly=="on") echo "checked"; ?>>
<tr><td>cables<td> <input type=checkbox name=cables onchange="this.form.submit();" <?php if($cables=="on") echo "checked"; ?>>
<tr><td>footprint<td> <input type=checkbox name=footprint onchange="this.form.submit();" <?php if($footprint=="on") echo "checked"; ?>>
<tr><td>leads<td> <input type=checkbox name=leads onchange="this.form.submit();" <?php if($leads=="on") echo "checked"; ?>>
<tr><td>customers<td> <input type=checkbox name=customers onchange="this.form.submit();" <?php if($customers=="on") echo "checked"; ?>>
</table>
</form>



<td>







<?php
echo "
<div id=mapcov>
	
	</div>
	
	 <script>
function initMap() {


  var uluru = {lat: 37.0642249, lng:-8.1128986};

 var map = new google.maps.Map(
      document.getElementById('mapcov'), {zoom: 12, center: uluru, mapTypeId: 'hybrid',gestureHandling: 'greedy'});
	  
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
		map.setCenter({lat:pos.coords.latitude, lng:pos.coords.longitude});
    }, function(error) {}
	);
}

// geolocation marker

var loco = 'img/googlemapbluedot_30px.png';

var myloc = new google.maps.Marker({
    clickable: false,
    icon: loco,
    shadow: null,
    zIndex: 999,
    map: map
});

function autoUpdate() {

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        var me = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
	        myloc.setPosition(me);
    }, function(error) {
        // ...
    });
}
else
{
//try requesting geoloc
  navigator.geolocation.getCurrentPosition(function(pos) {
        var me = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
        myloc.setPosition(me);
    }, function(error) {
        // ...
    });
}
  // Call the autoUpdate() function every 5 seconds
  setTimeout(autoUpdate, 5000);
}
autoUpdate();


        var imgf = 'img/red_12px.png';
		var imgdf = 'img/black_12px.png';
		var imgc = 'img/blue_12px.png';
		var imgi = 'img/orange_12px.png';
		var imgl = 'img/yellow_12px.png';
		var imgpk = 'img/pink_12px.png';
		var imgbr = 'img/brown_12px.png';		
		var imgqp = 'img/qpink_12px.png';
		var imgqg = 'img/qgreen_12px.png';
		var imgqgy = 'img/qgray_12px.png';
		var imgcn = 'img/cian_12px.png';
		var imgmeo = 'img/meo.png';





";




if($footprint=="on")
	include "coverage_polygon.txt";


if($meo=="on")
	include "meo.txt";


	
	
	
	
if($leads=="on")
{
// active Leads 
	
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status <= 20 AND status != 19 AND status != 15 AND status != 11 AND status != 10  AND status != 9 AND status !=4 AND status != 3 order by id");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"    var lead".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: imgl,
		  ZIndex: 3,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";	
}


// not accepted / not possible
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND (status=19 OR status=15 OR status=11 OR status=10 OR status=9 OR status=4 OR status=3) order by id");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"    var lead".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: imgbr,
		  ZIndex: 2,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";	
}


// installing 
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status > 20 AND status < 50");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"       var lead".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: imgi,
		  ZIndex: 2,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
 //       window.location.href = this.url;
		window.open(this.url);
    });
";	
}

}






if($customers=="on")
{
//fibre coax connection
$pinq="select properties.id,properties.address,properties.coords,connections.type from connections 
left join properties on properties.id=connections.property_id where properties.coords!=\"\" AND connections.date_end =\"0000-00-00\"";

$pins=$mon3->query($pinq);
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"       var pin".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
		  ZIndex: 1,
          icon: ";

		  if($pin['type']=="GPON"){echo "imgf";}elseif($pin['type']=="COAX"){echo "imgc";}
		  elseif($pin['type']=="FWA"){echo "imgcn";}else{echo "imgdf";}
		  	  
		  
		  echo",
		  title: \"".$pin['address']."\",
		  url: \"index.php?props=1&propid=".$pin['id']."\"
        });
		google.maps.event.addListener(pin".$pin['id'].", 'click', function() {
 //       window.location.href = this.url;
		window.open(this.url);
    });
";	
}


}




if($fats=="on")
{

	include "fats.txt";

}





if($poly=="on")
{
	include "fat_polygons.txt";
 
}


if($cables=="on")
{
	include "cables.txt";
 
}




echo"




}

  </script>";
	

	
	
echo"
    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
	
	
	
	
	<img src=img/red_12px.png>-fibre <img src=img/blue_12px.png>-coax <img src=img/cyan_12px.png>-FWA <img src=img/yellow_12px.png>-leads <img src=img/orange_12px.png>-installing &nbsp;	<img src=img/qgreen_12px.png>-FAT &nbsp;	<img src=img/meo.png>-MEO FAT

	<br><br>
	<div>
	<a href=includes/fat_polygons.kml>export Fat Polygons</a>
	

";





?>















</table>



