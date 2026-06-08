<?php

include("db.php");

$distance = $_GET['distance'];
$status = $_GET['led_status'];

$sql = "INSERT INTO sensor_log(distance,led_status) VALUES('$distance', '$status')";

if(mysqli_query($conn,$sql)){
    echo"OK";
}


?>