<?php
include_once 'functions.php';

$server = "localhost";
$user = "SADUSER";
$pass = "SADUSER";

// Create connection
$connection = new mysqli($server, $user, $pass);
// Check connection
if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}
else{
    $GLOBALS['connection'] = $connection;
}
// Create database
$sql = "CREATE DATABASE IF NOT EXISTS users_db";
$connection->query($sql);
//$connection->close();
//connect to database
//$db = new mysqli($server, $user, $pass, "users_db");
mysqli_select_db($connection, 'users_db');
$user_table = "CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `salt` varchar(20) NOT NULL,
    `password` varchar(255) NOT NULL,
    `user_type` varchar(10) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$resetpass_table = "CREATE TABLE IF NOT EXISTS `reset_password` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expiry_date` datetime(6) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$attempts_table = "CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `ip_address` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `timer` int(20) NOT NULL,
    `expiry_date` datetime(6) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$eventlog_table = "CREATE TABLE IF NOT EXISTS `event_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) NOT NULL,
    `success_failure` varchar(255) NOT NULL,
    `reason` varchar(255) NOT NULL,
    `date_time` datetime(6) NOT NULL,
    `ip_address` varchar(255) NOT NULL,
    `user_agent` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";  
//create tables in database
$connection->query($user_table);
$connection->query($resetpass_table);
$connection->query($attempts_table);
$connection->query($eventlog_table);
//$db->close();

$admin_name = "ADMIN";
$admin_pass = "SaD_2021!";
$salt = generateSalt();
$admin_pass = md5("SaD_2021!");
$adminSQL = "INSERT IGNORE INTO `users` (id, username, salt, password, user_type) VALUES (1, '$admin_name', '$salt', '$admin_pass', 'admin')";
$sname = "localhost";
$pass = "";
$dbname = "users_db";
//$connection = mysqli_connect($sname, $pass, $dbname);
if(!$connection->query($adminSQL)){
    echo $connection->error;
}
$connection->close();

$server = "localhost";
$user = "root";
$pass = "";
$db = "users_db";
/* attempt to connect to database */
$connection = mysqli_connect($server, $user, $pass, $db);
/* if connection to database fails, alert error message*/
if(!$connection){
    //echo "<script>alert('Connection failed.')</script>";
}

?>