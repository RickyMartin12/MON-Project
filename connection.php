<?php

date_default_timezone_set('Europe/Lisbon');//or change to whatever timezone you want

// Conexao da MON

header('Content-Type: text/html; charset=UTF-8');

// MEU COMPUTADOR
if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '192.168.11.162' )
{
$servername = "localhost";
$username = "sys";
$password = "12345";
$dbname = "mon";
}
// SERVIDOR DE TESTE
else if ($_SERVER['SERVER_NAME'] == '94.126.144.9')
{
    $servername = "localhost";
    $username = "sys";
    $password = "12345";
    $dbname = "mon_tst";
}
// SERVIDOR DE PRODUCAO
else if ($_SERVER['SERVER_NAME'] == 'mon.lazertelecom.com' || $_SERVER['SERVER_NAME'] == '89.31.231.9')
{
    $servername = "localhost";
    $username = "system";
    $password = "lazerx0!";
    $dbname = "mon";
}
// SERVIDOR DE PRODUCAO - VERSOES
else if ($_SERVER['SERVER_NAME'] == 'mon1.lazertelecom.com')
{
    $servername = "localhost";
    $username = "system";
    $password = "lazerx0!";
    $dbname = "mon_1";
}

// Project MON
if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '94.126.144.9' || $_SERVER['SERVER_NAME'] == '192.168.11.162' )
{
    define("PROJECT_WEB", $_SERVER['DOCUMENT_ROOT']."/mon");
    define("SERVER_WEB", $_SERVER['SERVER_NAME']."/mon/");
}
else if($_SERVER['SERVER_NAME'] == 'mon.lazertelecom.com' || $_SERVER['SERVER_NAME'] == '89.31.231.9')
{
    /*
    define("PROJECT_WEB", $_SERVER['DOCUMENT_ROOT']); // - /home/www/mon
    define("SERVER_WEB", $_SERVER['SERVER_NAME']."/"); // - /home/www/mon/
    */

    define("PROJECT_WEB", "/home/www/mon");
    define("SERVER_WEB", "mon.lazertelecom.com/");
}


// MOSTRAR OS ERROS
error_reporting(E_ALL & ~E_NOTICE);
// & ~E_NOTICE
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

$mon3 = mysqli_connect($servername, $username, $password, $dbname);
if (!$mon3) {
    echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
}

//header('Access-Control-Allow-Methods: POST');

mysqli_set_charset($mon3,"utf8");

// Conexao da QGIS

$dbname_QGIS = "qgis";

$qgis= mysqli_connect($servername, $username, $password, $dbname_QGIS);
if (!$qgis) {
    echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
}

header('Access-Control-Allow-Methods: POST');
//header('Cache-Control: no cache'); //disable validation of form by the browser

mysqli_set_charset($qgis,"utf8");

// Conexoes dos utilizadores do acesso a MON
// Utilizador da app MON
//$login_usr = $_SERVER['PHP_AUTH_USER'];

// UTILIZAODR POR DEFEITO
//$login_usr = 'danielj';

$_SERVER['PHP_AUTH_USER'] = 'danielj';

$localuser=$mon3->query("select * from users where username='".$_SERVER['PHP_AUTH_USER']."' ;")->fetch_assoc();
echo mysqli_error($mon3);

?>