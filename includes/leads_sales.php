<?php
$rootPath = realpath($_SERVER['DOCUMENT_ROOT']);

require $rootPath.'/init_web.php';
echo "	
	 
	<div id=mapl>
	
	</div>
	

	
	 <script> 
function initMap() {

  var uluru = {lat: 37.0642249, lng:-8.1128986};
  var map = new google.maps.Map(
      document.getElementById('mapl'), {zoom: 12, center: uluru, mapTypeId: 'hybrid',gestureHandling: 'greedy'});




if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
var pinpoint = 'img/pinpoint.png';
var pinpointb = new google.maps.Marker({ 
			position: pos,
          map: map,
          icon: pinpoint,
		  ZIndex: 1,
		  title: \"you are here\",
		  url: \"\"
        });	 
		});		
}

  infoWindow = new google.maps.InfoWindow();
  const locationButton = document.createElement(\"button\");
  locationButton.textContent = \"Pan to Current Location\";
  locationButton.classList.add(\"custom-map-control-button\");
  map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(locationButton);
  locationButton.addEventListener(\"click\", () => {
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
          infoWindow.setPosition(pos);
          infoWindow.setContent(\"You are here.\");
          infoWindow.open(map);
          map.setCenter(pos);
		  map.setZoom(18);
        },
        () => {
//          handleLocationError(true, infoWindow, map.getCenter());
        }
      );
    } else {
      // Browser doesn't support Geolocation
//      handleLocationError(false, infoWindow, map.getCenter());
    }
  });































";





include "coverage_polygon.txt";















echo "
        var imgf = 'img/red_12px.png';
		var imgc = 'img/blue_12px.png';
		var imgi = 'img/orange_12px.png';
		var imgl = 'img/yellow_12px.png';
		var imgqp = 'img/qpink_12px.png';
		var imgqg = 'img/qgreen_12px.png';
		var imgpk = 'img/pink_12px.png';
		var imgbr = 'img/brown_12px.png';
		var imgcn = 'img/cian_12px.png';
";

	$qwhere="";
	if(isset($_GET['searchb']))
	{
		$qwhere.= "AND (address LIKE '%".$searchb."%' or name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%')";
	
	}
	else
	{
		if($owner!="all" && $owner!="")
			$qwhere.="AND created_by=\"$owner\" ";
		if(isset($filter))
		{
			if($filter=="active"){ $qwhere.="AND status<50 AND is_active=1 ";}	
		}
		
		
		
	}




$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status <= 20 AND (status!=19 AND status!=15 AND status!=11 AND status!=10  AND status !=9)");
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
		  ZIndex: 1,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";	
}






// not possible
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status=9");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"    var lead".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: imgcn,
		  ZIndex: 4,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";	
}





// rejected 
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND (status=19 OR status=11)");
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
		  ZIndex: 4,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";	
}






// waiting on sales
$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND (status=15 OR status=10 )");
echo mysqli_error($mon3);

while($pin=$pins->fetch_assoc())
{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	
 echo"    var lead".$pin['id']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: imgpk,
		  ZIndex: 4,
		  title: \"id:".$pin['id']." - ".$pin['address']."\",
		  url: \"index.php?propleads=1&lead_id=".$pin['id']."\"
        });
		google.maps.event.addListener(lead".$pin['id'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";	
}


























$pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status > 20");
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

$shpl=$qgis->query("select OGR_FID,ST_AsText(SHAPE) from juntas where estado=\"installed\" ");


while($pin=$shpl->fetch_assoc())
{
	$coord=explode("(",$pin['ST_AsText(SHAPE)']);
	$coord=explode(")",$coord[1]);
	$coord=explode(" ",$coord[0]);
	
	$lon=$coord[0];
	$lat=$coord[1];
	
 echo"       var lead".$pin['OGR_FID']." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: imgqg,
		  ZIndex: 0,
		  title: \"fat_id:".$pin['OGR_FID']."\",
		  url: \"index.php?network=1&splicebox_id=".$pin['OGR_FID']."\"
        });
		google.maps.event.addListener(lead".$pin['OGR_FID'].", 'click', function() {
        window.location.href = this.url;
    });
";	
}






echo"
}




    </script>";
	

	
	
echo"
    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
	

	
	
	<img src=img/red_12px.png>-fibre <img src=img/blue_12px.png>-coax <img src=img/yellow_12px.png>-leads 
	<img src=img/brown_12px.png>-rejected <img src=img/pink_12px.png>-waiting on sales
	<img src=img/cian_12px.png>-not possible
	
	<img src=img/orange_12px.png>-installing &nbsp;	<img src=img/qgreen_12px.png>-FAT

	<br><br>
	<div>
	
	";
