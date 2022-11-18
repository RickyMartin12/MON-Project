<?php

echo "Whenever we are requested to block domains and prevent users to access specific websites of the internet, that domain should be added below, with all the information about the request, either the court, gambling associations or the regulators
<br>
this database is converted to file and applied to our DNS servers.
<br><br>";




if(isset($_POST['submdomain']))
{

$domains=$mon3->query("insert into domains_blocked values(
\"".$_POST['domain']."\", 
\"".$_POST['date']."\",
\"".$_POST['reason']."\",
\"".$_POST['comm']."\",
\"added by ".$_SERVER['PHP_AUTH_USER']." ".date("Y-m-d H:i")." ".$_POST['notes']."\"
) " );

echo $mon3->error;
$mon3->query("update settings set valor=1 where nome=\"domains_blocked_changed\" "); 

}







echo "<form name=subm action=?procedures=blocked_domains method=post>
<table><th>domain<th>date<th>reason <th>communication<th>notes";
echo "<tr><td><input type=text name=domain>
<td><input type=text size=5 name=date value=\"".date("Y-m-d")."\">
<td><input type=text size=5 name=reason >
<td><input type=text name=comm value=>
<td><input type=text  name=notes><input type=submit name=submdomain value=add domain>
</tr>
</form>
";


$domains=$mon3->query("select * from domains_blocked");
while($dominio=$domains->fetch_assoc())
{

echo "<tr>
<td><a href=http://".$dominio['domain'].">".$dominio['domain']."</a>
<td>".$dominio['date_in']."
<td>".$dominio['reason']."
<td>".$dominio['communication']."
<td>".$dominio['notes']."



";
}