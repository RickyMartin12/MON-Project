<?php
/*
todo
search by fsan and id on the list fibre

needs: 
-lib-phpmailer

*/

// INITIALIZAR O CAMINHO ABSOLUTO DENTRO DA APACHE

/*$rootPath = realpath($_SERVER['DOCUMENT_ROOT']);

require $rootPath.'/init_web.php';*/

require '/var/www/html/init_web.php';

$folder = dirname(__FILE__);

serverNameFolder($folder);


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />




<title>Lazer technical system</title>
	
<!--<script type=text/javascript src='https://code.jquery.com/jquery-3.1.1.min.js'></script>-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>



<script type=text/javascript> 

$(document).ready(function () {
   $("select").select2();
});

    // PROCURAR AS LEADS - STATUS // OWNER - Link : MON_ROOT."/".?propleads=1
    // PROPS
    // LEADS - SEARCH
    var status = "";
    var owner = "";
    if($("select[name=status]").val() != "")
    {
        status = $("select[name=status]").val();
    }
    else
    {
        status = "<?php echo $_GET['status'];?>";
    }


    if($("select[name=owner]").val() != "")
    {
        owner = $("select[name=owner]").val();
    }
    else
    {
        owner = "<?php echo $_GET['owner'];?>";
    }


</script> 


<!-- SCRIPTS DA MON -->
<script src='js/select2.min.js?v=1.1' type='text/javascript'></script>
<link href='js/select2.min.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="css.css">
<link rel="stylesheet" href="modal.min.css">
<script src="js/moment.js?v=1.1"></script>
<script src="js/Chart.js?v=1.1"></script>
<script src="js/functions.js?v=1.1"></script>
<script src="js/bod-modal.js?v=1.1"></script>



</head>
<body class="bground">

<div class="canvas">
<?php
    echo date("Y-m-d H:m:s")." - login as <b>". $localuser['username']."</b>";
?>
<div class="divlogo">
    <img width="200px" src="img/lazer_logo.png">
</div>
<br><br>
<a class=redlink href=index.php>Home</a> | 
<a class=redlink href=?props=1>Properties</a> 
<a class=redlink href=?servs=1&type=INT>Services</a> | 
<a class=redlink href=?gpon=1>GPON</a> 
<a class=redlink href=?fwa=1>FWA</a> 
<a class=redlink href=?coax=1>COAX</a> |
<a class=redlink href=?headend=1&channels=1>Headend</a>  
 <a class=redlink href=?tickets=1>Tickets</a>  
<!--<a class=redlink href=?jobs=1>Jobs</a>  
<a class=redlink href=?stock=1>Stock</a>  -->
<a class=redlink href=?stats=1>Stats</a> | 
<a class=redlink href=?procedures=1>Procedures</a>
<br><br>








<?php


// function isset anything from get



// LEADS
if($_GET['propleads']==1)
{
	include 'includes/leads.php';
}

// PROPS
elseif($_GET['props']==1 )
{
	include 'includes/props_NEW.php';
}

// CUSTOS

elseif($_GET['custs']==1)
{
	include 'includes/custs.php';
}

// SERVIÇOS

elseif($_GET['servs']==1)
{
	include 'includes/services.php';
}


// TIPO DE CONEXAO - GPON

elseif($_GET['gpon']==1)
{
	include 'includes/gpon.php';
}
// TIPO DE CONEXAO - FWA

elseif($_GET['fwa']==1)
{
	include 'includes/fwa.php';
}
// TIPO DE CONEXAO - COAX

elseif($_GET['coax']==1)
{
	include 'includes/coax.php';
}


// Equipments monitoring
elseif($_GET['equip']!="" && $_GET['equip_type']!="" )
{

	include 'includes/equips.php';
}

// HEADEND
elseif($_GET['headend']==1)
{
	include 'includes/headend.php';
}

// NETWORK

elseif($_GET['network']==1)
{
	include 'includes/network.php';
}




// TICKETS

elseif($_GET['tickets']==1)
{
	include 'includes/tickets.php';
}

// JOBS

elseif($_GET['jobs']==1)
{
	include 'includes/jobs.php';
}

// STOCKS


elseif($_GET['stock']==1)
{
	include 'includes/stock.php';
}

// STATS 

elseif($_GET['stats']==1)
{
	include 'includes/stats.php';

}

// PROCEDURES


elseif($_GET['procedures'])
{
	include 'includes/procedures.php';

}





else
{
    if($_POST['upd_tech'])
    {
        $oncall=$mon3->query("update settings set valor=\"".$_POST['techcall']."\" where nome=\"tech_oncall\" ");

    }
    if($_POST['upd_noc'])
    {
        $oncall=$mon3->query("update settings set valor=\"".$_POST['noc_agent']."\" where nome=\"noc_agent\" ");

    }



    ?>
    <form action=index.php method=post>
    forward notifications to the tech on call: <select name=techcall>
    <?php 
    $techs=$mon3->query("select username,telf from users where is_tech=1");
    $oncall=$mon3->query("select valor from settings where nome=\"tech_oncall\" ")->fetch_assoc();
    while($tech=$techs->fetch_assoc())
    {echo "<option ";
    if($oncall['valor']==$tech['username']) echo " selected ";
    echo " value=\"".$tech['username']."\">".$tech['username']."-".$tech['telf']."</option>";
    }
    ?>
    </select>
    <input type=submit name=upd_tech value=save>
    </form>



    <form action=index.php method=post>
    forward notifications to the NOC agent: <select name=noc_agent>
    <?php 
    $techs=$mon3->query("select username,telf from users where is_helpdesk=1");
    $oncall=$mon3->query("select valor from settings where nome=\"noc_agent\" ")->fetch_assoc();
    while($tech=$techs->fetch_assoc())
    {echo "<option ";
    if($oncall['valor']==$tech['username']) echo " selected ";
    echo " value=\"".$tech['username']."\">".$tech['username']."-".$tech['telf']."</option>";
    }
    ?>
    </select>
    <input type=submit name=upd_noc value=save>
    </form>






    <?php


    exec('tail -100 '.MON_ROOT.'/log.txt', $output);
    echo "<h2>last MON logs:</h2> 
        <table border=\"1\">
        <tr>
            <td id=logt height=150px width=900px style=\"display:block; overflow:auto;\">
            <div>".implode("<br>",$output)."</div>
        </table>";

    echo" <script>
    var objDiv = document.getElementById(\"logt\");
    objDiv.scrollTop = objDiv.scrollHeight;
    </script>";




    ?>
    <h2>Temperature</h2>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1492&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1492&rra_id=0&view_type=tree></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1038&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1038&rra_id=0&view_type=tree></a>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1039&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1039&rra_id=0&view_type=tree></a>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1494&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1494&rra_id=0&view_type=tree></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1519&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1519&rra_id=0&view_type=tree></a>

    <h2>Internet feeds</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=29&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=29&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1496&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1496&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=636&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=636&rra_id=0&view_type=tree></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=214&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=214&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=953&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=953&rra_id=0&view_type=tree ></a>










    <br><br>
    <h2>OLTs</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=5&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=5&rra_id=0&view_type=tree></a>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=640&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=640&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=623&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=623&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1209&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1209&rra_id=0&view_type=tree ></a>



    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=637&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=637&rra_id=0&view_type=tree ></a>



    <h2>CMTSs</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=40&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=40&rra_id=0&view_type=tree ></a>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=41&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=41&rra_id=0&view_type=tree ></a>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=621&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=621&rra_id=0&view_type=tree ></a>


    <h2>VPN UK</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1513&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1513&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=733&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=733&rra_id=0&view_type=tree ></a>

    <h2>FWA</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1571&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1571&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1570&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1570&rra_id=0&view_type=tree ></a>


    <h2>Links</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=46&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=46&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=108&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=108&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=597&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=597&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=889&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=889&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=117&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=117&rra_id=0&view_type=tree ></a>





    <a href= ><img src= ></a>

    <h2>TV equip</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=781&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=781&rra_id=0&view_type=tree ></a>





    <h2>DIAs</h2>
    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=599&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=599&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=249&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=249&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=250&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=250&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=251&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=251&rra_id=0&view_type=tree ></a>


    <br><br>


    <h2>DNS Servers</h2>



    <br>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1707&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1707&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1709&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1709&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1485&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1485&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1698&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1698&rra_id=0&view_type=tree ></a>










    <br><br>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1035&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1035&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1036&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1036&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1486&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1486&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1713&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1713&rra_id=0&view_type=tree ></a>











    <h2>Internet experience</h2>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1493&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1493&rra_id=0&view_type=tree ></a>


    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1475&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1475&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1491&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1491&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1490&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1490&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1479&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1479&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1489&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1489&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1478&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1478&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1476&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1476&rra_id=0&view_type=tree ></a>

    <a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1477&rra_id=all>
    <img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1477&rra_id=0&view_type=tree ></a>

    <?php
}


mysqli_close($mon3);
?>


</div>
</body>
</html>
