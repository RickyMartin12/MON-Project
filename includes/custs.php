<?php

echo"
	<a href=?propleads=1><img src=img/leads.png></a>
	<a href=?props=1><img src=img/house.png></a>
	<a href=?custs=1><img src=img/user.png></a>
	<a href=?custs=1&addcust=1><img src=img/useradd.png></a>

<h3>Customers</h3><br>

";











//show cust//////////////////////////////////////////////////////////////////////////
if(isset($_GET['cust_id']))
{
	$cust_id=mysqli_real_escape_string($mon3, $_GET['cust_id']);

$owner=$mon3->query("select * 
from customers where id=$cust_id;")->fetch_assoc();
echo "<table ><tr>
<td width=550px valign=top colspan=2>
<table><tr><td>
<b>Customer:</b><br>
id: $cust_id  - <b>".$owner['name']."</b> <br> <td>
<a href=?custs=1&custedit=$cust_id> <img src=img/useredit.png></a> 
</table>
 <br>
 
<tr><td width=550px valign=top> 

<b>Postal Address:</b><br> ".$owner['address']."<br><br>
 <b>Email:</b><br> ".$owner['email']."<br><br>
 <b>Phone:</b><br> ".$owner['telef']."<br><br>
 <b>Fiscal Number:</b><br> ".$owner['fiscal_nr']."<br><br>
 <b>Prefered Language:</b><br> ".$owner['language']."<br><br>
 
  <b>Notes:</b><br> ".$owner['notes']."<br><br>
 
 
 <td  valign=top>
<div id=\"map\" ></div>
    <script>
// Initialize and add the map
function initMap() {
  // The location of Uluru
  var uluru = {lat: 37.060521, lng: -8.026970};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 10, center: uluru, mapTypeId: 'satellite'});


	  
var imgf = 'img/red_12px.png';
var imgc = 'img/blue_12px.png';
var imgp = 'img/pink_12px.png';
";






$pins=$mon3->query("select properties.id,properties.address,properties.coords,connections.type,connections.subscriber,properties.owner_id,properties.management,connections.subscriber
 from properties left join connections on properties.id=connections.property_id
where properties.owner_id=\"$cust_id\" OR properties.management=\"$cust_id\" OR connections.subscriber=\"$cust_id\" ");
echo mysqli_error($mon3);


$i=1;
while($pin=$pins->fetch_assoc())
{
	if($coord=="")
	{
		$lon=-8.026970;
		$lat=37.060521;
	}
	else{
	$coord=explode(",",$pin['coords']);
	$lon=$coord[1];
	$lat=$coord[0];
	}
	
 echo"       var pin".$i." = new google.maps.Marker({
          position: {lat: $lat, lng: $lon },
          map: map,
          icon: ";
		  
		   if($pin['management']>0){echo "imgp";}elseif($pin['type']=="GPON"){echo "imgf";}else{echo "imgc";}
		  
		  echo",
		  title: \"".$pin['address']."\",
		  url: \"index.php?props=1&propid=".$pin['id']."\"
        });
		google.maps.event.addListener(pin".$i.", 'click', function() {
        window.location.href = this.url;
    });
";	
$i++;
}



  
  
  
  
echo"  
  
}
    </script>
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
		

";
 
 
 //ownerof
 
 
 $ownerof=$mon3->query("select id, address,ref from properties where owner_id=$cust_id");
 if($ownerof->num_rows>0)
 {
  echo"
 <tr><td colspan=2>
 <b>Is owner of:</b><br> ";
 while($prop=$ownerof->fetch_assoc())
 {
	echo "<a href=?props=1&propid=".$prop['id'].">".$prop['ref']."</a> ".$prop['address']." <br> ";
 }
 
 
 }
 echo"<br>";
 

 //manager of

 $ownerof=$mon3->query("select id, address,ref from properties where management=$cust_id");
 if($ownerof->num_rows>0)
 {
  echo"  
 <tr><td colspan=2>
 <b>Is prop manager of:</b><br>";
 while($prop=$ownerof->fetch_assoc())
 {
	echo "<a href=?props=1&propid=".$prop['id'].">".$prop['ref']."</a> ".$prop['address']." <br> ";
 }
 
 }
 echo"<br> ";
 
 
 
 
 //subscriber of
 
 $ownerof=$mon3->query("select connections.id,connections.property_id,properties.ref,properties.address from connections left join properties 
 on connections.property_id=properties.id where connections.subscriber=$cust_id");
 if($ownerof->num_rows>0)
 {
  echo"  
  <tr><td colspan=2>
 <b>Is subscriber of:</b><br>";
 while($prop=$ownerof->fetch_assoc())
 {
	
	echo "<a href=?props=1&propid=".$prop['property_id'].">".$prop['ref']."</a> ".$prop['address']." <br> ";
	$servs=$mon3->query("select type,date_start from services where connection_id=".$prop['id']. " AND date_end LIKE \"0000-00-00\" ");
	if($servs->num_rows>0){
	while($serv=$servs->fetch_assoc()){
		echo" &nbsp; &nbsp; &nbsp; ".$serv['type']."(start: ".$serv['date_start']."<br>" ;
	}
	}
	else
	{  echo"  <tr><td colspan=2>
 <b>No services subscibed:</b><br>";
	}
}
 
 }
 

 echo "
</table>";
}











////////////add cust////////////////////////////////////////
elseif(isset($_GET['addcust']))
{







echo "
<form action=?custs=1 method=post>
<table  cellspacing=10><tr>
<td valign=center colspan=2 >
<b>New Customer </b> 
<tr><td><br>
<tr><td><b>Name:</b> <td> <select name=salut>
<option value=\"Sr.\">Sr.</option>
<option value=\"Sra.\">Sra.</option>
<option value=\"Eng.\">Eng.</option>
<option value=\"Sr.\">Dr.</option>
<option value=\"Dra.\">Dra.</option>
<option value=\"Mr.\">Mr.</option>
<option value=\"Mrs.\">Mrs.</option>
<option value=\"Miss.\">Miss.</option>
<option value=\"Lady\">Lady</option>
<option value=\"Sir\">Sir</option>
</select> 
<input type=text name=name value=>
<tr><td><b>Billing Address:</b><td> <input type=text name=address value=>
<tr><td><b>Email:</b><td> <input type=text name=email value=>
<tr><td><b>Phone:</b><td> <input type=text name=telef value=>
<tr><td> <b>Fiscal Number:</b><td> <input type=text name=fiscal_nr value=>
<tr><td> <b>Prefered Lang:</b><td> <select name=lang>
<option value=\"pt\">pt</option>
<option value=\"en\">en</option>
<option value=\"fr\">fr</option>
<option value=\"es\">es</option>
</select>
<tr><td><td><input type=checkbox name=is_commercial value=1 > Is a company
<tr><td> <b>Roles</b> <td><input type=checkbox name=is_management value=1> Is a management company of the owner
<tr><td><td><input type=checkbox name=is_agent value=1> Is an agent for leads

<tr><td> <b>Notes:</b><td> <input type=text name=notes>
<tr><td><td><br>

<tr><td> <td><input type=submit name=custsubmadd value=save>
</form>
</table>";

}






















////////////edit cust////////////////////////////////////////
elseif(isset($_GET['custedit']) || isset($_POST['custsubmadd']))
{

if(isset($_GET['custedit'])){
	$cust_id=mysqli_real_escape_string($mon3, $_GET['custedit']);
	$owner=$mon3->query("select * from customers where id=$cust_id;")->fetch_assoc();
}
elseif(isset($_POST['custsubmadd'])){
	$salut=mysqli_real_escape_string($mon3, $_POST['salut']);
	$name=mysqli_real_escape_string($mon3, $_POST['name']);
	$address=mysqli_real_escape_string($mon3, $_POST['address']);
	$email=mysqli_real_escape_string($mon3, $_POST['email']);
	$telef=mysqli_real_escape_string($mon3, $_POST['telef']);
	$fiscal_nr=mysqli_real_escape_string($mon3, $_POST['fiscal_nr']);
	$lang=mysqli_real_escape_string($mon3, $_POST['lang']);
	$is_commercial=mysqli_real_escape_string($mon3, $_POST['is_commercial']);
	$is_management=mysqli_real_escape_string($mon3, $_POST['is_management']);
	$is_agent=mysqli_real_escape_string($mon3, $_POST['is_agent']);
	$notes=mysqli_real_escape_string($mon3, $_POST['notes']);
	
	
	if($notes!="")
		$notes.=date("Y-m-d H:i:s").": ".$localuser['username'].": ".mysqli_real_escape_string($mon3, $_POST['notes'])."<br>";	
	
	monlog("modified customer $cust_id -> salut=\"$salut\",	name=\"$name\",	address=\"$address\",telef=\"$telef\",email=\"$email\",	is_commercial=\"$is_commercial\",is_management=\"$is_management\",is_agent=\"$is_agent\",fiscal_nr=\"$fiscal_nr\",");
	

	$data_cust = date("Y-m-d");
            
    $gg=$mon3->query("insert into customers 
	        (salut,name,address,telef,email,is_commercial,is_management,is_agent,fiscal_nr,notes,date_created) values 
	        (\"$salut\",\"$name\",\"$address\",\"$telef\",\"$email\",\"$is_commercial\",\"$is_management\",\"$is_agent\",
	        \"$fiscal_nr\",\"$notes\", \"$data_cust\" );");


	$cust_id=$mon3->insert_id;
	echo mysqli_error($mon3);
	echo " <font color=green>saved!</font>";

		$owner=$mon3->query("select * from customers where id=$cust_id;")->fetch_assoc();
}	
	
	
	
	
	
if(isset($_POST['custsubm'])){
	$salut=mysqli_real_escape_string($mon3, $_POST['salut']);
	$name=mysqli_real_escape_string($mon3, $_POST['name']);
	$address=mysqli_real_escape_string($mon3, $_POST['address']);
	$email=mysqli_real_escape_string($mon3, $_POST['email']);
	$telef=mysqli_real_escape_string($mon3, $_POST['telef']);
	$fiscal_nr=mysqli_real_escape_string($mon3, $_POST['fiscal_nr']);
	$lang=mysqli_real_escape_string($mon3, $_POST['lang']);
	$is_commercial=mysqli_real_escape_string($mon3, $_POST['is_commercial']);
	$is_management=mysqli_real_escape_string($mon3, $_POST['is_management']);
	$is_agent=mysqli_real_escape_string($mon3, $_POST['is_agent']);
	$notes=$owner['notes'];
	if(mysqli_real_escape_string($mon3, $_POST['notes'])!="")
		$notes.=date("Y-m-d H:i:s").": ".$localuser['username'].": ".mysqli_real_escape_string($mon3, $_POST['notes'])."<br>";	
	monlog("modified customer $cust_id -> salut=\"$salut\",	name=\"$name\",	address=\"$address\",telef=\"$telef\",email=\"$email\",	is_commercial=\"$is_commercial\",is_management=\"$is_management\",is_agent=\"$is_agent\",fiscal_nr=\"$fiscal_nr\",");
	
	$gg=$mon3->query("update customers set salut=\"$salut\",
	name=\"$name\",
	address=\"$address\",
	telef=\"$telef\",
	email=\"$email\",
	is_commercial=\"$is_commercial\",
	is_management=\"$is_management\",
	is_agent=\"$is_agent\",
	fiscal_nr=\"$fiscal_nr\",
	notes=\"$notes\" where id=$cust_id");
	echo mysqli_error($mon3);
	echo " <font color=green>saved!</font>";

		$owner=$mon3->query("select * from customers where id=$cust_id;")->fetch_assoc();
}





echo "
<form action=?custs=1&custedit=$cust_id method=post>
<table  cellspacing=10><tr>
<td valign=center colspan=2 >
<b>Edit Customer id:</b> $cust_id  
<tr><td><br>
<tr><td><b>Name:</b> <td> <select name=salut>
<option value=\"Sr.\"";if($owner['salut']=="Sr.") echo " selected"; echo ">Sr.</option>
<option value=\"Sra.\"";if($owner['salut']=="Sra.") echo " selected"; echo ">Sra.</option>
<option value=\"Eng.\"";if($owner['salut']=="Eng.") echo " selected"; echo ">Eng.</option>
<option value=\"Sr.\"";if($owner['salut']=="Dr.") echo " selected"; echo ">Dr.</option>
<option value=\"Dra.\"";if($owner['salut']=="Dra.") echo " selected"; echo ">Dra.</option>
<option value=\"Mr.\"";if($owner['salut']=="Mr.") echo " selected"; echo ">Mr.</option>
<option value=\"Mrs.\"";if($owner['salut']=="Mrs.") echo " selected"; echo ">Mrs.</option>
<option value=\"Miss.\"";if($owner['salut']=="Miss.") echo " selected"; echo ">Miss.</option>
<option value=\"Lady\"";if($owner['salut']=="Lady") echo " selected"; echo ">Lady</option>
<option value=\"Sir\"";if($owner['salut']=="Sir") echo " selected"; echo ">Sir</option>
</select> 
<input type=text name=name value=\"".$owner['name']."\">
<tr><td><b>Billing Address:</b><td> <input type=text name=address value=\"".$owner['address']."\">
<tr><td><b>Email:</b><td> <input type=text name=email value=\"".$owner['email']."\">
<tr><td><b>Phone:</b><td> <input type=text name=telef value=\"".$owner['telef']."\">
<tr><td> <b>Fiscal Number:</b><td> <input type=text name=fiscal_nr value=\"".$owner['fiscal_nr']."\">
<tr><td> <b>Prefered Lang:</b><td> <select name=lang>
<option value=\"pt\"";if($owner['language']=="pt") echo " selected"; echo ">pt</option>
<option value=\"en\"";if($owner['language']=="en") echo " selected"; echo ">en</option>
<option value=\"fr\"";if($owner['language']=="fr") echo " selected"; echo ">fr</option>
<option value=\"es\"";if($owner['language']=="es") echo " selected"; echo ">es</option>
</select>
<tr><td><td><input type=checkbox name=is_commercial value=1 ";if($owner['is_commercial']=="1") echo " checked"; echo "> Is a company
<tr><td> <b>Roles</b> <td><input type=checkbox name=is_management value=1";if($owner['is_management']=="1") echo " checked"; echo "> Is a management company of the owner
<tr><td><td><input type=checkbox name=is_agent value=1";if($owner['is_agent']=="1") echo " checked"; echo "> Is an agent for leads

<tr><td> <b>Notes:</b><td> <input type=text name=notes>
<tr><td><td>".$owner['notes']."<br>

<tr><td> <td><input type=submit name=custsubm value=save><input type=hidden name=custedit value=$cust_id>
</form>
</table>";

}



///////////// custs list///////////////////////


//Default - List 

else{
	if(isset($_GET['offset']))
		$offset=mysqli_real_escape_string($mon3, $_GET['offset']);
	else
		$offset=0;
echo "	
	<form name=serachp method=get>
	Search: <input type=text name=searchb Onkeyup=\"searchcust(this.value)\" ";
	if(isset($_GET['searchb']))
	{
		$searchb=mysqli_real_escape_string($mon3, $_GET['searchb']);
		echo " value=$searchb";
		
		$qwhere= " where (name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%' or 
		email LIKE '%".$searchb."%' or telef LIKE '%".$searchb."%' or fiscal_nr LIKE '%".$searchb."%') ";
		/*
		$qwhere= " where (address LIKE '%".$searchb."%' or name LIKE '%".$searchb."%' or id LIKE '%".$searchb."%' or 
		email LIKE '%".$searchb."%' or telef LIKE '%".$searchb."%' or fiscal_nr LIKE '%".$searchb."%') 
		";
		*/
	
	}
	echo">  
	<input type=hidden name=custs value=1>
	</form>	<br>
	<div id=tablec>   
	<table><tr> <th>id</th><th>name</th><th>fiscal</th><th>email</th><th>phone</th>";
	$props=$mon3->query("select id,name,email,telef,fiscal_nr from customers ".$qwhere.
	" order by name limit ".$offset.",50 ");
	$count=$mon3->query("select count(*) from customers ".$qwhere)->fetch_row();
	while($value=$props->fetch_assoc())
	{  
		$notes=explode("<br>",$value['notes']);
		$notes=$notes[0];
		echo	"<tr>
			<td width=60px><a href=?custs=1&cust_id=".$value['id'].">  ".$value['id']."</a> </td>
			<td width=200px>".$value['name']. "
			<td>".$value['fiscal_nr']. "			
			<td>".$value['email']. "
			<td>".$value['telef']. "
			"; 
			
			
			
			
	}	

echo "</table></div>

<div id=paging><br>";

if ($count[0]>50)
{
	$lastp=ceil($count[0]/50);
	$curpage=($offset/50)+1;
	
//	echo "curr: $curpage <br>";

	
//print initial page
	if($curpage>1)
	{
		echo "<a href=?custs=1&searchb=$searchb&offset=0>|<</a> ";
	}
//print page -2
	if($curpage>2)
	{
		echo "<a href=?custs=1&searchb=$searchb&offset=".($curpage-3)*50 .">".($curpage-2) ."</a> ";
	}
//print page -1
	if($curpage>1)
	{
		echo "<a href=?custs=1&searchb=$searchb&offset=".($curpage-2)*50 .">".($curpage-1) ."</a> ";
	}
//print curpage	
	
		echo " <b> $curpage </b> ";
//print page -1
	if($curpage<$lastp)
	{
		echo "<a href=?custs=1&searchb=$searchb&offset=".($curpage)*50 .">".($curpage+1) ."</a> ";
	}
	if($curpage<$lastp-1)
	{
		echo "<a href=?custs=1&searchb=$searchb&offset=".($curpage+1)*50 .">".($curpage+2) ."</a> ";
	}	
		
		if($curpage<$lastp)
	{
		echo "<a href=?custs=1&searchb=$searchb&offset=".($lastp-1)*50 .">>|</a> ";
	}	
		
}
echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $count[0] results</div>";
}







