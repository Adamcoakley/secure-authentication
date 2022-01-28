<?php

$server = "localhost:3308";
$user = "root";
$pass = "";
$database = "users_db";

/* attempt to connect to database */
$connection = mysqli_connect($server, $user, $pass, $database);

/* if connection to database fails, alert error message */
if(!$connection){
    echo "<script>alert('Connection failed.')</script>";
}

?>