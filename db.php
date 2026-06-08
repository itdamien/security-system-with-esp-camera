<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "ultrasonic_security";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    echo"Connection Failed!";
}

?>