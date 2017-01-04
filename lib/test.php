<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = new PDO("dblib:host=erpdbserver.cloudapp.net:14435;dbname=MTNL_App;", "sa", "4Dragon2Cheese"); //SyteLine_AppDemo //MTNL_App
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION );

$sql = "SELECT inc_num,inc_date,description,stat_code FROM fs_incident_mst WHERE ser_num='5237';";

foreach ($conn->query($sql) as $row) {
    echo $row[0]." ".$row[1]." ".$row[2]." ".$row[3]."<br>";
} 


 ?>