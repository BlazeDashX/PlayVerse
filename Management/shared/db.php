<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "playverse";

/* Establish database connection */
$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Connection Failed!");
}
?>