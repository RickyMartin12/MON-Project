
<?php

echo "<h2>Network equipments</h2> <br><br>";

$mon_slidebox = MON_ROOT."/network/spliceboxes/";

if (isset($_GET['splicebox_id']))
{
	
	$splicebox_id=mysqli_real_escape_string($mon3,$_GET['splicebox_id']);
	if(isset($_POST['savesplicebox']))
	{
		
        $coverage_polygon_id=$_POST['coverage_polygon_id'];
        $date_install=$_POST['date_install'];
        $name=$_POST['namen'];
        $description=$_POST['description'];
        $type=$_POST['type'];
        $network_type=$_POST['network_type'];
        $zone=$_POST['zone'];
        $install_type=$_POST['install_type'];
        $estado=$_POST['estado'];
        $armario=$_POST['armario'];
        $cv_pole_id	=$_POST['cv_pole_id'];
        $homes_covered=$_POST['homes_covered'];
        $homes_capacity	=$_POST['homes_capacity'];
        $homes_connected=$_POST['homes_connected'];
        $notes	=$_POST['notes'];
        $var_plan = uploadfile("schematicser",$mon_slidebox.$splicebox_id."/", "schematic_".$splicebox_id."_".date("Y-m-d_H_i_s")."_".$localuser['username'].".pdf",0,0);
		
        //		var_dump($_POST);

            $qgis->query("update juntas SET coverage_polygon_id=\"$coverage_polygon_id\", name=\"$name\", description=\"$description\", type=\"$type\", network_type=\"$network_type\", zone=\"$zone\", install_type=\"$install_type\", estado=\"$estado\", date_install=\"$date_install\", armario=\"$armario\",cv_pole_id=\"$cv_pole_id\",homes_covered=\"$homes_covered\",homes_capacity=\"$homes_capacity\",homes_connected=\"$homes_connected\",notes=\"$notes\" where OGR_FID=$splicebox_id ;");

            echo "mysql:". $qgis->error;

            ?>
            <script>
                        var var_s = "<?php echo $var_plan; ?>";

                        var sd = "";

                        sd += var_s +"<br>";
                        $('#warning_services').html(sd);
                    </script>
            <?php
		
		
		
		
		
		
		echo "<font color=green>saved</font>";
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$splicebox=$qgis->query("select ST_AsText(SHAPE), `coverage_polygon_id`, `name`, `description`, `type`, `network_type`, `zone`, `install_type`, `estado`, `date_planned`, `date_install`, `armario`, `cv_pole_id`, `homes_covered`, `homes_capacity`, `homes_connected`, `morada`, `projeto`, `notes`

	from juntas where OGR_FID=$splicebox_id")->fetch_assoc();



    
	
	echo "
	<table><tr><td width=500px>
	<form action=\"?network=1&splicebox_id=$splicebox_id\" name=spliceboxedit method=post enctype=\"multipart/form-data\">
	<table><tr><td colspan=2> <h3>Splicebox id: <b> $splicebox_id </b></h3>
	
	<tr><td>coverage polygon<td><select name=coverage_polygon_id>";
	
	if($splicebox['coverage_polygon_id']=="" || $splicebox['coverage_polygon_id']=="0")
		echo "<option selected> </option>";
	
$shpl=$qgis->query("select OGR_FID from grupos");	
 while($polid=$shpl->fetch_assoc())
 {		
	echo "<option ";
	if($polid['OGR_FID']==$splicebox['coverage_polygon_id']) echo " selected";
	echo ">".$polid['OGR_FID']."</option>";
	
 }
	echo "</select>";
	
	echo "<tr><td>date_install<td><input type=text name=date_install value=\"".$splicebox['date_install']."\">
	<tr><td>name<td><input type=text name=namen value=\"".$splicebox["name"]."\">
	<tr><td>description<td><input type=text name='description' value=\"".$splicebox['description']."\">
	<tr><td>type<td><input type=text name=type value=\"".$splicebox['type']."\">";

$juntas_network_type = $qgis->query("SELECT * FROM juntas WHERE OGR_FID=".$splicebox_id)->fetch_assoc();

echo "<tr><td>network_type<td><select name=network_type >";
$options=$qgis->query("SELECT DISTINCT network_type FROM juntas");
foreach($options as $option){	

echo "<option ";
if($juntas_network_type['network_type']==$option['network_type']) echo " selected";
echo ">". $option['network_type']."</option>";
}	
echo 	"</select>";	


echo "<tr><td>zone<td><select name=zone >";
$options=$qgis->query("SELECT DISTINCT zone FROM juntas");
foreach($options as $option){	

echo "<option ";
if($juntas_network_type['zone']==$option['zone']) echo " selected";
echo ">". $option['zone']."</option>";
}	
echo 	"</select>";	


echo "<tr><td>install_type<td><select name=install_type >";
$options=$qgis->query("SELECT DISTINCT install_type FROM juntas");
foreach($options as $option){	

echo "<option ";
if($juntas_network_type['install_type']==$option['install_type']) echo " selected";
echo ">". $option['install_type']."</option>";
}	
echo 	"</select>";

echo "<tr><td>status<td><select name=estado >";
$options=$qgis->query("SELECT DISTINCT estado FROM juntas");
foreach($options as $option){	

echo "<option ";
if($juntas_network_type['estado']==$option['estado']) echo " selected";
echo ">". $option['estado']."</option>";
}	
echo 	"</select>";
	
	


echo	"
<tr><td>armario<td><input type=text name=armario value=\"".$splicebox['armario']."\">
<tr><td>cv_pole_id<td><input type=text name=cv_pole_id value=\"".$splicebox['cv_pole_id']."\">
<tr><td>homes_covered<td><input type=text name=homes_covered value=\"".$splicebox['homes_covered']."\">
<tr><td>homes_capacity<td><input type=text name=homes_capacity value=\"".$splicebox['homes_capacity']."\">
<tr><td>homes_connected<td><input type=text name=homes_connected value=\"".$splicebox['homes_connected']."\">
<tr><td>Schematic<td><input type=file name=schematicser>
<tr><td>notes<td><textarea name=notes>".$splicebox['notes']."</textarea>
<tr><td><td> <input type=submit value=save name=savesplicebox>
</table>

	</form>";


    echo"
	
<td>




";


$coord=explode(" ",str_replace(")","",substr($splicebox['ST_AsText(SHAPE)'],6)));
$coordlat=trim($coord[1]);
$coordlng=trim($coord[0]);

if($splicebox['coverage_polygon_id']!="" && $splicebox['coverage_polygon_id']!="0"){
$shpl=$qgis->query("select OGR_FID,ST_AsText(SHAPE) from grupos where OGR_FID=\"".$splicebox['coverage_polygon_id']."\"")->fetch_assoc();	

 $polyg=substr($shpl['ST_AsText(SHAPE)'],9);
 $polyg=str_replace(")","",$polyg);
 $coords=explode(",",$polyg);

}

echo "<div id=\"map\" >

</div>

    <script>
// Initialize and add the map
function initMap() {
	
       var imgf = 'img/red_12px.png';
		var imgdf = 'img/black_12px.png';
		var imgc = 'img/blue_12px.png';
		var imgi = 'img/orange_12px.png';
		var imgl = 'img/yellow_12px.png';
		var imgqp = 'img/qpink_12px.png';
		var imgqg = 'img/qgreen_12px.png';	
	
	
	
	
	
  // The location of Uluru
  var uluru = {lat: ".$coordlat.", lng:".$coordlng."};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 18, center: uluru, mapTypeId: 'satellite'});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru,icon: imgqg, map: map});";
  
if($splicebox['coverage_polygon_id']!="" && $splicebox['coverage_polygon_id']!="0"){  
echo "var cov".$shpl['OGR_FID']." = [ ";
$i=0; 
foreach($coords as $coord)
{
$coord=explode(" ",$coord);	
if($i>0) echo ",";
echo "{lng: ".$coord[0].", lat: ".$coord[1]."}"; 

$i=1;
}	

echo "]; 
        var poly".$shpl['OGR_FID']." = new google.maps.Polygon({
          paths: cov".$shpl['OGR_FID']." ,
          strokeColor: '#00ff00',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#00ff00',
		  title: \"cov_id:".$shpl['OGR_FID']."\",
		  url: \"index.php?network=1&cov_id=".$shpl['OGR_FID']."\",
		  ZIndex: 2,
          fillOpacity: 0.15
        });
        poly".$shpl['OGR_FID'].".setMap(map);
		 google.maps.event.addListener(poly".$shpl['OGR_FID'].", 'click', function (event) {
        //alert the index of the polygon
        alert(\"polygon id: ".$shpl['OGR_FID']."\");
    });
 
 ";
}
echo" 
  
  
  
  
  
  
  
}
    </script>

    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
	<a href=https://www.google.com/maps/search/?api=1&query=".$coordlat.",".$coordlng." target=_blank>open in maps</a>
	
	









</table>	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	";

    $images = "";
    $schematic = "";
    $images_upload = "";


    echo "<div id=warning_services></div> ";


    $images_upload .= "<table >
<tr><td colspan=4 align=center><br><br><b>upload new file(.jpg or .pdf)</b><br>
<form name=addrandfile method=post enctype=\"multipart/form-data\" action=index.php?network=1&splicebox_id=".$splicebox_id.">
<label for=fileInput> 
<img id=icon´ height=100px src=\"img/upload.png\" style=\"cursor: pointer;\">
</label>
<input type=file name=randfile[] accept=\".pdf,image/jpeg\" id=fileInput multiple style=\"display:none;\" onchange=\"this.form.submit()\">
</form>";
	
	
if($_FILES['randfile'])
{
$var_rec = "";
$countfiles = count($_FILES['randfile']['name']);
for($i=0;$i<$countfiles;$i++){
    if(file_exists($_FILES['randfile']['tmp_name'][$i]))
    {
        $ext=explode(".",$_FILES['randfile']['name'][$i]);
        $var_rec .= uploadfile("randfile",$mon_slidebox.$splicebox_id."/", $splicebox_id."_".date("Y-m-d_H:i:s")."_".
            $localuser['username'].$i.".".strtolower($ext[sizeof($ext)-1]),0,$i);
    }
}
}	
	
	
?>
<script>
    var val_upload_rand = "<?php echo $var_rec; ?>"
    $("#warning_services").html(val_upload_rand);
</script>
<?php



    

    $i=0;
    $j=0;
    $n_t=0;
    $merge = array();
    if(file_exists($mon_slidebox.$splicebox_id))
    {
        $files1 = scandir($mon_slidebox.$splicebox_id);
        foreach($files1 as $file1)
        {
            //echo $file1;
            if($i%4==0)
            {
                $images .="<tr>";
            }
            if($j%1==0)
            {
                $schematic .="<tr>";
            }


            if(strtolower(pathinfo($mon_slidebox.$splicebox_id."/".$file1, PATHINFO_EXTENSION))=="jpg" || strtolower(pathinfo($mon_slidebox.$splicebox_id."/".$file1, PATHINFO_EXTENSION))=="jpeg" )
            {
                $images .= "<td align=center><a href=network/spliceboxes/".$splicebox_id."/".$file1." title = ".$file1." data-link_page=network/spliceboxes/".$splicebox_id."/".$file1." class=link_slider target=_blank>";
                $images .= "<img src=network/spliceboxes/".$splicebox_id."/".$file1." height=100px alt=".$file1." class=img_slider > </a> ";
                $i++;   
            }
            elseif(preg_match("/_pdf/", $file1) and !preg_match("/schematic_/", $file1))
            {
                $file_teste = preg_replace("/_pdf/", '', $file1);
                $file_teste = preg_replace("/.png/", '', $file_teste);
                $file_teste = $file_teste.".pdf";

                $images .= "<td align=center> <a href=network/spliceboxes/".$splicebox_id."/".$file1." data-link_page=network/spliceboxes/".$splicebox_id."/".$file_teste." title = ".$file_teste.">";
                $images .= "<img src=network/spliceboxes/".$splicebox_id."/".$file1." height=100px class=\"img_pdf\" alt=".$file_teste.">  </a> ";
                $i++;
            }
            elseif(preg_match("/schematic_/", $file1) and preg_match("/.png/", $file1) )
            {
                $file_teste = preg_replace("/_pdf/", '', $file1);
                $file_teste = preg_replace("/.png/", '', $file_teste);
                $file_teste = $file_teste.".pdf";

                $id[$j] = $splicebox_id;
                $files_a[$j] = $file1;
                $file_array[$j] = $file_teste;
                $j++;



                
            }

        }

        if($j == 0)
        {
            $schematic = "<b>no schematic</b><br>";
        }
        else if($i == 0)
        {
            $images = "<b>no images</b><br>";
        }

        
        if($id != null)
        {
            $merge = array_merge($merge, ['id' => $id, 'files_a' => $files_a, 'file_array' => $file_array]);
            //var_dump($merge);
            
            //array_multisort($merge['id'], SORT_DESC, $merge['files_a'], SORT_DESC, $merge['file_array'], SORT_DESC);

            //print_r($merge);

                $schematic .= "<td align=center> <a href=network/spliceboxes/".$merge['id'][$j-1]."/".$merge['file_array'][$j-1]." data-link_page=network/spliceboxes/".$merge['id'][$j-1]."/".$merge['file_array'][$j-1]." title = ".$merge['files_a'][$j-1]." target=_blank>";
                $schematic .= "<img src=network/spliceboxes/".$merge['id'][$j-1]."/".$merge['files_a'][$j-1]." height=100px class=\"img_pdf\" alt=".$merge['files_a'][$j-1]."> <br>".$merge['file_array'][$j-1]." </a> ";
        }

        






    }
    else
    {
        $images = "<b>no images</b><br>";
        $schematic = "<b>no pdf</b><br>";
    }

    
    echo "</td></tr>";
    


    echo"
    <table>
    <tr><td colspan=2 align=center>";
    echo "<fieldset>";

        echo "<legend><b>Schematic (Recent File):</b></legend>";

        

        echo "<table class=bod-modal data-title=center ><tr>";
        echo $schematic;
        echo "</tr></table>";


    echo "</fieldset>";
    echo "</td>";

    echo"
    <td>";
    echo "<fieldset>";
            echo "<legend><b>Images Gallery:</b></legend>";
            echo $images_upload;
            echo "<table class=bod-modal data-title=center ><tr>";
            echo $images;
            echo "</tr></table>";
    echo "</fieldset>";
    echo "</td>";


    echo "</tr></table>";

    


    echo "</td></tr>";

    echo "</td></tr></table>";

	





	
	
	
}
else
{
	
	echo "network map here";
	
}