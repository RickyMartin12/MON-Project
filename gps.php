<?php
$rootPath = realpath($_SERVER['DOCUMENT_ROOT']);

require $rootPath.'/init_web.php';
$zoom=20;

if($_GET["coord"]!="")
{
    $coord=$_GET["coord"];
    $coord=str_replace(array('(',')'),"",$coord);
    $coord=explode(",",$coord);
    $lat=$coord[0];
    $lng=$coord[1];

}
else
{
    $address=$_GET["address"];
    $address = urlencode($address);
    $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=" . $address . "&key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc";
    $response = file_get_contents($url);
    $json = json_decode($response,true);
    //var_dump($json);
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
}

?>
<!DOCTYPE html>
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
</head>

<body>

        <div id="map"></div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        </script>

        <script>
            // Localizacções das conexões das leads
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

            var marker;

            var lat = <?php echo $lat; ?>;
            var lng = <?php echo $lng; ?>;
            var zoom = <?php echo $zoom; ?>;
            var marker;
        </script>
        <?php
        if($lng && $lat)
        {

        ?>
            <script>

                var myLatLng = {lat: lat , lng: lng };
                function initMap() {
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: myLatLng,
                        zoom: zoom,
                        mapTypeId: 'hybrid'
                    });
                }

            </script>
        <?php
        }
        else
        {
        ?>
            <script>


                var myLatLng = {lat: 37.060512480933 , lng: -8.0269711051 };

                function initMap() {
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: myLatLng,
                        zoom: zoom,
                        mapTypeId: 'hybrid'
                    });
                    //console.log(map);
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(pos) {
                                map.setCenter({lat:pos.coords.latitude, lng:pos.coords.longitude});
                            }, function(error) {
                                console.log(error);
                            }
                        );
                    }
                }

                //console.log(map);
            </script>
        <?php
        }
        ?>



        <script>
            // Carregamento dos dados
            $(document).ready(function() {

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
                            console.log(error);
                        });
                    }
                    else
                    {
                        //try requesting geoloc
                        navigator.geolocation.getCurrentPosition(function(pos) {
                            var me = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                            myloc.setPosition(me);
                        }, function(error) {
                            console.log(error);
                        });

                    }
                    setTimeout(autoUpdate, 5000);
                }

                autoUpdate();


                marker = new google.maps.Marker({position: myLatLng, map: map});

                //console.log(marker);

                <?php

                    include "includes/coverage_polygon.txt";
                ?>

            });

        </script>

        <?php

            // active Leads (Activar as Leads)

            $pins=$mon3->query("select id,address,coords,status from property_leads 
            where coords!=\"\" $qwhere AND status <= 20 AND status!=19 AND status!=11 AND status!=9 ");
            echo mysqli_error($mon3);

            while($pin=$pins->fetch_assoc()) {
                $coord = explode(",", $pin['coords']);
                $lon = $coord[1];
                $lat = $coord[0];
                $url_leads = "index.php?propleads=1&lead_id=".$pin['id'];
                $title = $pin['id']." - ".$pin['address'];
                ?>
                <script>
                    // Carregamento dos dados
                    $(document).ready(function()
                    {
                        var pin = <?php echo $pin['id']; ?>;
                        var lat = <?php echo $lat; ?>;
                        var lng = <?php echo $lon; ?>;

                        var title = "id:"+ "<?php echo $title; ?>";

                        var lead_id = "lead "+pin;
                        var url = "<?php echo $url_leads; ?>";


                        var lead_id = new google.maps.Marker({
                            position: {lat: lat, lng: lng},
                            map: map,
                            icon: imgl,
                            ZIndex: 3,
                            title: title,
                            url: url
                        });
                        google.maps.event.addListener(lead_id, 'click', function () {
                            window.open(this.url);
                        });



                    });



                </script>
            <?php
        }
        ?>



        <?php
            // Leads que não foram aceites ou nao possiveis  - Estados 19, 11 e 9


        $pins_not_accept=$mon3->query("select id,address,coords,status from property_leads
        where coords!=\"\" $qwhere AND (status=19 OR status=11 OR status=9) ");
        echo mysqli_error($mon3);

        while($pin_not_accept=$pins_not_accept->fetch_assoc()) {
                $coord_not_accept = explode(",", $pin_not_accept['coords']);
                $lon_not_accept = $coord_not_accept[1];
                $lat_not_accept = $coord_not_accept[0];
                $url_leads_not_accept = "index.php?propleads=1&lead_id=" . $pin_not_accept['id'];
                $title_not_accept = $pin_not_accept['id'] . " - " . $pin_not_accept['address'];
            ?>
            <script>
                // Carregamento dos dados
                $(document).ready(function()
                {
                    var pin = <?php echo $pin_not_accept['id']; ?>;
                    var lat = <?php echo $lat_not_accept; ?>;
                    var lng = <?php echo $lon_not_accept; ?>;

                    var title = "id:"+ "<?php echo $title_not_accept; ?>";

                    var lead_id_not_accept = "lead "+pin;
                    var url = "<?php echo $url_leads_not_accept; ?>";

                    var lead_id_not_accept = new google.maps.Marker({
                        position: {lat: lat, lng: lng},
                        map: map,
                        icon: imgbr,
                        ZIndex: 3,
                        title: title,
                        url: url
                    });
                    google.maps.event.addListener(lead_id_not_accept, 'click', function () {
                        window.open(this.url);
                    });

                });
            </script>
            <?php
        }

        ?>

        <?php
            // Leads que não foram instalados - Estados das Leads das conexões entre 21 e 49

            $pins_installing=$mon3->query("select id,address,coords,status from property_leads
            where coords!=\"\" $qwhere AND status > 20 AND status < 50");
            echo mysqli_error($mon3);

            while($pin_installing=$pins_installing->fetch_assoc())
            {
                $coord_installing = explode(",", $pin_installing['coords']);
                $lon_installing = $coord_installing[1];
                $lat_installing = $coord_installing[0];
                $url_leads_installing = "index.php?propleads=1&lead_id=" . $pin_installing['id'];
                $title_installing = $pin_installing['id'] . " - " . $pin_installing['address'];

                ?>
                <script>
                    // Carregamento dos dados
                    $(document).ready(function()
                    {
                        var pin = <?php echo $pin_installing['id']; ?>;
                        var lat = <?php echo $lat_installing; ?>;
                        var lng = <?php echo $lon_installing; ?>;

                        var title = "id:"+ "<?php echo $title_installing; ?>";

                        var lead_id_installing = "lead "+pin;
                        var url = "<?php echo $url_leads_installing; ?>";

                        var lead_id_installing = new google.maps.Marker({
                            position: {lat: lat, lng: lng},
                            map: map,
                            icon: imgi,
                            ZIndex: 3,
                            title: title,
                            url: url
                        });
                        google.maps.event.addListener(lead_id_installing, 'click', function () {
                            window.open(this.url);
                        });

                    });
                </script>
                <?php
            }

        ?>

        <?php
            //fibre coax connection
            $pinq="select properties.id,properties.address,properties.coords,connections.type from connections 
            left join properties on properties.id=connections.property_id where properties.coords!=\"\" AND connections.date_end =\"0000-00-00\"";

            $pins_fibra_optica=$mon3->query($pinq);
            echo mysqli_error($mon3);

            while($pin_fibra_optica=$pins_fibra_optica->fetch_assoc())
            {
                $coord_fibra_optica = explode(",", $pin_fibra_optica['coords']);
                $lon_fibra_optica = $coord_fibra_optica[1];
                $lat_fibra_optica = $coord_fibra_optica[0];
                $url_fibra_optica = "index.php?propleads=1&lead_id=" . $pin_fibra_optica['id'];
                $title_fibra_optica = $pin_fibra_optica['id'] . " - " . $pin_fibra_optica['address'];

                $lig = "";
                if($pin_fibra_optica['type']=="GPON")
                {
                    $lig = "imgf";
                }
                elseif($pin_fibra_optica['type']=="COAX")
                {
                    $lig = "imgc";
                }
		        elseif($pin_fibra_optica['type']=="FWA")
                {
                    $lig = "imgcn";
                }
                else
                {
                    $lig = "imgdf";
                }

                ?>
                <script>
                    // Carregamento dos dados
                    $(document).ready(function()
                    {
                        var pin = <?php echo $pin_fibra_optica['id']; ?>;
                        var lat = <?php echo $lat_fibra_optica; ?>;
                        var lng = <?php echo $lon_fibra_optica; ?>;

                        var title = "id:"+ "<?php echo $title_fibra_optica; ?>";

                        var lead_id_fibra_optica = "lead "+pin;
                        var url = "<?php echo $url_fibra_optica; ?>";



                        var lead_id_fibra_optica = new google.maps.Marker({
                            position: {lat: lat, lng: lng},
                            map: map,
                            icon: <?php echo $lig; ?>,
                            ZIndex: 3,
                            title: title,
                            url: url
                        });
                        google.maps.event.addListener(lead_id_fibra_optica, 'click', function () {
                            window.open(this.url);
                        });

                    });
                </script>

                <?php
            }


        ?>


        <?php
            // spliceboxes

            $shpl=$qgis->query("select OGR_FID,ST_AsText(SHAPE),date_install from juntas where estado=1");


            while($shp=$shpl->fetch_assoc())
            {
                $coord=explode("(",$shp['ST_AsText(SHAPE)']);
                $coord=explode(")",$coord[1]);
                $coord=explode(" ",$coord[0]);

                $lon_shp=$coord[0];
                $lat_shp=$coord[1];

                $url_shp = "index.php?network=1&splicebox_id=".$shp['OGR_FID'];

                $title_shp = "fat_id:".$shp['OGR_FID']." date install:".$shp['date_install'];

                ?>
                <script>
                    // Carregamento dos dados
                    $(document).ready(function()
                    {
                        var pin = <?php echo $shp['OGR_FID']; ?>;
                        var lat = <?php echo $lat_shp; ?>;
                        var lng = <?php echo $lon_shp; ?>;

                        var title = "<?php echo $title_shp; ?>";

                        var lead_id_shp = "lead "+pin;
                        var url = "<?php echo $url_shp; ?>";

                        var lead_id_shp = new google.maps.Marker({
                            position: {lat: lat, lng: lng},
                            map: map,
                            icon: imgqg,
                            ZIndex: 3,
                            title: title,
                            url: url
                        });
                        google.maps.event.addListener(lead_id_shp, 'click', function () {
                            window.open(this.url);
                        });


                    });
                </script>
                <?php
            }

        ?>


        <?php
            // spliceboxes coverage

            $shpl_conv=$qgis->query("select OGR_FID,ST_AsText(SHAPE) from grupos");
            //echo $shpl_conv;
            while($polygon=$shpl_conv->fetch_assoc())
            {
                $polyg=substr($polygon['ST_AsText(SHAPE)'],9);
                $polyg=str_replace(")","",$polyg);
                $coords=explode(",",$polyg);

                $plo_OGR_FID = $polygon['OGR_FID'];

                $i=0;
                $v = "";
                $coords_array = array();
                foreach($coords as $coord) {
                    $coord = explode(" ", $coord);

                    //$coords_array[] = "{lat: " . $coord[1] . ",lng: " . $coord[0] . "}";

                    if($i>0) $v .= ",";
                    $v .= "{lat: ".floatval($coord[1]).", lng: ".floatval($coord[0])."}";

                    $coords_array[$plo_OGR_FID] = $v;
                    $i = 1;
                }

                //echo $v;

                ?>

                <script>
                    // Carregamento dos dados
                    $(document).ready(function()
                    {
                        var p = "<?php echo $plo_OGR_FID; ?>";
                        var poly_coord = "poly"+p;
                        var conv = "coord"+p;
                        // Array de poligons na base de dados gpis dos grupos do OGR_FID
                        var conv = [<?php echo $coords_array[$plo_OGR_FID]; ?>];

                        //console.log(conv);

                        poly_coord = new google.maps.Polygon({
                            paths: conv ,
                            strokeColor: '#00ff00',
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: '#00ff00',
                            ZIndex: 1,
                            clickable: false,
                            fillOpacity: 0.15
                        });
                        poly_coord.setMap(map);









                    });
                </script>




                <?php


            }

            ?>

        <script>
            // Carregamento dos dados
            $(document).ready(function()
            {
                //console.log(myLatLng);
                google.maps.event.addListener(map, "rightclick", function(event) {
                    var lat = event.latLng.lat();
                    var lng = event.latLng.lng();
                    // populate yor box/field with lat, lng
                    alert("Lat=" + lat + "; Lng=" + lng);
                });


                var infoWindow = new google.maps.InfoWindow();



                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        //console.log(pos);

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


                /*google.maps.event.addListener(map, 'click', function(e) {
                    var positionDoubleclick = e.latLng;
                    console.log(positionDoubleclick);
                    //window.opener.document.getElementById("coord").value = e.latLng;
                    marker.setPosition(positionDoubleclick);
                    // if you don't do this, the map will zoom in
                    e.stopPropagation();

                })*/






                /*google.maps.event.addListener(map, 'click', function(e) {
                    var marker = new google.maps.Marker({position: myLatLng, map: map});
                    var positionDoubleclick = e.latLng;
                    window.opener.document.getElementById("coord").value = e.latLng;
                    marker.setPosition(positionDoubleclick);
                    // if you don't do this, the map will zoom in
                    e.stop();
                    e.cancelBubble = true;
                    if (e.stopPropagation) {
                        e.stopPropagation();
                    }
                    if (e.preventDefault) {
                        e.preventDefault();
                    } else {
                        e.returnValue = false;
                    }

                });*/

                function initialize()
                {



                    google.maps.event.addListener(map, "click", function(e) {
                        latLng = e.latLng;

                        console.log(e.latLng.lat());
                        console.log(e.latLng.lng());

                        console.log("Marker");
                        // if marker exists and has a .setMap method, hide it
                        if (marker && marker.setMap) {
                            marker.setMap(null);
                        }
                        marker = new google.maps.Marker({
                            position: latLng,
                            map: map
                            //draggable: true
                        });

                        var positionDoubleclick = e.latLng;
                        window.opener.document.getElementById("coord").value = e.latLng;
                        marker.setPosition(positionDoubleclick);




                    });
                }

                initialize();
                //window.addEventListener('load', initialize);

                //initialize();

                //$( window ).on( 'load', initialize );
                //google.maps.event.addDomListener(window, 'load', initialize);
            });




        </script>


        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap" type="text/javascript">
        </script>



</body>
