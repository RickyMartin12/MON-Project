<?php
require 'connection.php';


if($_GET["coord"]!="")
{
    $coord=$_GET["coord"];
    $coord=str_replace(array('(',')'),"",$coord);
    $coord=explode(",",$coord);
    $lat=$coord[0];
    $lng=$coord[1];
    $zoom=20;
}
else
{


    /*
    // Geolocation Geocoding API at 5$/1000 requests

        $address=$_GET["address"];
        $address = urlencode($address);
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=" . $address . "&key=AIzaSyAWO_epCqh-ulvpAdar6SycwndXLjRCsDg";
        $response = file_get_contents($url);
        $json = json_decode($response,true);
        if($json['results'][0]=="")
        {
            $lat=37.06058;
            $lng=-8.02696;
            $zoom=12;
        }
        else
        {
        $lat = $json['results'][0]['geometry']['location']['lat'];
        $lng = $json['results'][0]['geometry']['location']['lng'];
        $zoom=14;
        }

    */


}

?><!DOCTYPE html>
<html>
<head>
    <title>New Lead Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 100%;
            width: 100%;
        }
    </style>

    <script>
        var map;




        <?php
        if(	$lat ){
            echo " 
	var myLatLng = {lat: $lat , lng: $lng };
	function initMap() {
		  
//		geocoder = new google.maps.Geocoder();  
		  
        map = new google.maps.Map(document.getElementById('map'), {
          center: myLatLng,
          zoom: $zoom,
		  mapTypeId: 'hybrid'

        });
";

        }
        else
        {
//center: {lat: <?php echo $lat; , lng: <?php echo $lng; },
            echo"
var myLatLng = {lat: 37.060512480933 , lng: -8.0269711051 }; 

	function initMap() {
		  
//		geocoder = new google.maps.Geocoder();  
		  
        map = new google.maps.Map(document.getElementById('map'), {
          center: myLatLng,
          zoom:  15 ,
		  mapTypeId: 'hybrid'

		  
		  
		  
		  
		  
		  
		  
		  
        });	  
	  
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
		map.setCenter({lat:pos.coords.latitude, lng:pos.coords.longitude});
    }, function(error) {}
	);
}


";
        }

        ?>

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







        var marker = new google.maps.Marker({position: myLatLng, map: map});





        <?php

        include "includes/coverage_polygon.txt";















        echo "
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

";





        // active Leads

        $pins=$mon3->query("select id,address,coords,status from property_leads
where coords!=\"\" $qwhere AND status <= 20 AND status!=19 AND status!=11 AND status!=9 ");
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
where coords!=\"\" $qwhere AND (status=19 OR status=11 OR status=9) ");
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









        // spliceboxes
        $shpl=$qgis->query("select OGR_FID,ST_AsText(SHAPE),date_install from juntas where estado=\"1\" ");


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
		  title: \"fat_id:".$pin['OGR_FID']." date install:".$pin['date_install']."  \",
		  url: \"index.php?network=1&splicebox_id=".$pin['OGR_FID']."\"
        });
		google.maps.event.addListener(lead".$pin['OGR_FID'].", 'click', function() {
//        window.location.href = this.url;
		window.open(this.url);
    });
";
        }




        //splicebox coverage




        $shpl=$qgis->query("select OGR_FID,ST_AsText(SHAPE) from grupos");
        echo $shpl;
        while($polygon=$shpl->fetch_assoc())
        {
            $polyg=substr($polygon['ST_AsText(SHAPE)'],9);
            $polyg=str_replace(")","",$polyg);
            $coords=explode(",",$polyg);

            echo "var cov".$polygon['OGR_FID']." = [ ";
            $i=0;
            foreach($coords as $coord)
            {
                $coord=explode(" ",$coord);
                if($i>0) echo ",";
                echo "{lng: ".$coord[0].", lat: ".$coord[1]."}";

                $i=1;
            }

            echo "]; 
        var poly".$polygon['OGR_FID']." = new google.maps.Polygon({
          paths: cov".$polygon['OGR_FID']." ,
          strokeColor: '#00ff00',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#00ff00',
		  ZIndex: 1,
		  clickable: false,
          fillOpacity: 0.15
        });
        poly".$polygon['OGR_FID'].".setMap(map);
		
";
        }








        echo"



google.maps.event.addListener(map, \"rightclick\", function(event) {
    var lat = event.latLng.lat();
    var lng = event.latLng.lng();
    // populate yor box/field with lat, lng
    alert(\"Lat=\" + lat + \"; Lng=\" + lng);
});





        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };

            infoWindow.setPosition(pos);
            infoWindow.setContent('Location found.');
            infoWindow.open(map);
            map.setCenter(pos);
          }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
          });
        } 
		else 
		{
          
          handleLocationError(false, infoWindow, map.getCenter());
        }
  

 ";
        ?>




        google.maps.event.addListener(map, 'click', function(e) {
            var positionDoubleclick = e.latLng;
            window.opener.document.getElementById("coord").value = e.latLng;
            marker.setPosition(positionDoubleclick);
            // if you don't do this, the map will zoom in
            e.stopPropagation();

        });








        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWO_epCqh-ulvpAdar6SycwndXLjRCsDg&callback=initMap"
            async defer>
    </script>

</head>







<body>
<div id="map"></div>


</body>
</html>