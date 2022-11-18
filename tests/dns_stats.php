<?php
error_reporting("E_ALL");
$mon= mysqli_connect("127.0.0.1","system","lazerx0!","lazer_dns");
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
} 


echo "<html><body><br><br>

<table border=1 width=800px >
<tr><th width=500px>domain<th width=250px>count
";




$stats=$mon->query("SELECT DISTINCT(domain),count(domain) FROM dns_queries group by domain ORDER BY count(domain) DESC limit 0,500");

while($dns=$stats->fetch_assoc())
{

if(strpos($dns['domain'],"facebook.com")!==false || strpos($dns['domain'],"fbcdn.com")!==false || strpos($dns['domain'],"facebook.net")!==false)
{
	$cellf=" style=background-color:#8888ff ";
}
elseif(strpos($dns['domain'],"instagram.com")!==false )
{
	$cellf=" style=background-color:#cc4500 ";
}
elseif(strpos($dns['domain'],"whatsapp")!==false )
{
	$cellf=" style=background-color:#00FF00 ";
}
elseif(strpos($dns['domain'],"netflix")!==false )
{
	$cellf=" style=background-color:#FF0000 ";
}
elseif(strpos($dns['domain'],"google")!==false || strpos($dns['domain'],"gstatic")!==false )
{
	$cellf=" style=background-color:#ffff00 ";
}
elseif(strpos($dns['domain'],"apple")!==false || strpos($dns['domain'],"icloud")!==false)
{
	$cellf=" style=background-color:#999999 ";
}


else
{
	$cellf=" color=white ";
}



echo "<tr><td width=500px ".$cellf." >".$dns['domain']."<td>".$dns['count(domain)']

;


}
echo "</table> finished";