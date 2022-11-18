<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);
//ini_set('display_startup_errors', 1);

session_start();
$mon3= mysqli_connect("127.0.0.1","system","lazerx0!","mon");

if($_SERVER['REMOTE_ADDR']!="89.31.226.13" && ($_SERVER['REMOTE_ADDR']!=gethostbyname("app.planr.io")) && ($_SERVER['REMOTE_ADDR']!="34.247.15.1"))
{
	echo "IP address not allowed: ".$_SERVER['REMOTE_ADDR'];
	//.gethostbyname("app.planr.io")	
}
elseif (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL mon3: " . mysqli_connect_error()  ;
  
}
else
{ 
/*
https://mon.lazertelecom.com/api/?getleadsbymonth=2022-01
https://mon.lazertelecom.com/api/?getleadsconnectedbymonth=2022-01
https://mon.lazertelecom.com/api/?getleadbyid=7203
https://mon.lazertelecom.com/api/?allsubs=1






*/




  
  
if (isset($_GET['getleadsbymonth']) && $_GET['getleadsbymonth']!="") 
{  
	$month=explode("-",$mon3->real_escape_string($_GET['getleadsbymonth'])); 
	
	if(is_numeric($month[0]) && $month[0]>2000 && is_numeric($month[1]) && $month[1]<13)
	{
	$result=array();
	$leads=$mon3->query("select id,status,date_lead,created_by from property_leads 
	where date_lead like \"".$month[0]."-".$month[1]."%\" and status<=60 ");
	if($leads->num_rows>0)
	{
//	header("Access-Control-Allow-Origin: *");
//	header("Content-Type: application/json; charset=UTF-8");  
	while($lead=$leads->fetch_assoc())
	{
		$result[] = $lead;

	}

		echo json_encode($result);
	}
	else
	{	
		echo json_encode(array());
		//http_response_code(400);    
		// no results
	}
	
	}
	else
	{
		http_response_code(400);
	}
}

elseif (isset($_GET['getleadsconnectedbymonth']) && $_GET['getleadsconnectedbymonth']!="") 
{  
	$month=explode("-",$mon3->real_escape_string($_GET['getleadsconnectedbymonth'])); 
	
	if(is_numeric($month[0]) && $month[0]>2000 && is_numeric($month[1]) && $month[1]<13)
	{
	$result=array();
	$leads=$mon3->query("select id,status,date_lead,date_installed,created_by from property_leads 
	where date_installed like \"".$month[0]."-".$month[1]."%\" and status<=60 ");
	if($leads->num_rows>0)
	{
//	header("Access-Control-Allow-Origin: *");
//	header("Content-Type: application/json; charset=UTF-8");  
	while($lead=$leads->fetch_assoc())
	{
		$result[] = $lead;

	}

		echo json_encode($result);
	}
	else
	{	
		echo json_encode(array());
		//http_response_code(400);    
		// no results
	}
	
	}
	else
	{
		http_response_code(400);
	}
}

elseif(isset($_GET['getleadbyid']) && $_GET['getleadbyid']!="")
{
	$leadid=$mon3->real_escape_string($_GET['getleadbyid']);
	if(is_numeric($leadid) && $leadid>0)
	{
	
	$lead=$mon3->query("select id,address,freguesia,name,status,prop_id,date_lead,date_viability,date_accept,date_papwk,date_book,date_install,
	date_installed,date_closed,date_modified,connection_cost,is_network_ready,network_cost,estimated_quote,
	timeframe,quoted,contract_id,con_type,internet_prof,fixed_ip,tv,phone1,phone2,aps,install_price,monthly_price,
	is_changeover,prev_rev_month,technician,final_netw_cost,final_inst_cost,NPS_score,manager_score,created_by 
	from property_leads where id=\"".$leadid."\" and status<=60 and is_active=1")->fetch_assoc();

	echo json_encode($lead);
	
	}
	else
	{
		http_response_code(400);
	}


}



elseif(isset($_GET['allsubs']) && $_GET['allsubs']=="1")
{

	$result=array();
	$leads=$mon3->query("select distinct properties.ref,properties.id,properties.address from services left join connections on services.connection_id=connections.id left join properties on connections.property_id=properties.id where services.date_end=\"0000-00-00\" 
	");
	if($leads->num_rows>0)
	{
//	header("Access-Control-Allow-Origin: *");
//	header("Content-Type: application/json; charset=UTF-8");  
	while($lead=$leads->fetch_assoc())
	{
		$result[] = $lead;

	}



	
	
	
	echo json_encode($result);
	
	}
	else
	{
	echo "no results";
	}

}



else
{
	echo "no method called";
}


}


  