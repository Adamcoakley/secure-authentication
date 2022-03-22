<?php
include 'config.php';
include_once 'functions.php';
session_start();

if (!isset($_SESSION['admin']))
{
    header("Location: access-denied.php");
    die();
} 

//logout of the application and destroy / unset the session variables 
if(isset($_POST["logout"])){
    session_destroy();
    header("Location: login.php");
}

// fetch the data from event_log table
$query = "SELECT id, username, success_failure, reason, date_time, ip_address, user_agent FROM event_log ORDER BY date_time DESC";
$result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="all" />
</head>
<body>
<div class="admin-content">
  <h2 class="admin-heading">Event log</h2>
  <form action="" method="post">
      <button name="logout" class="admin-logout" type="submit">Logout</button>
  </form>
<table border ="1" cellspacing="0" cellpadding="6">
  <tr>
    <th>id</th>
    <th>Username</th>
    <th>Success / Failure</th>
    <th>Reason</th>
    <th>Timestamp</th>
    <th>IP Address</th>
    <th>User Agent </th>
  </tr>

<?php
if (mysqli_num_rows($result) > 0) {
  $id = 1;
  while($row = mysqli_fetch_assoc($result)){
    ?>
    <tr>
      <td><?php echo $id; ?> </td>
      <td><?php echo $row['username']; ?> </td>
      <td><?php echo $row['success_failure']; ?> </td>
      <td><?php echo $row['reason']; ?> </td>
      <td><?php echo $row['date_time']; ?> </td>
      <td><?php echo $row['ip_address']; ?> </td>
      <td><?php echo $row['user_agent']; ?> </td>
    </tr>
    <?php 
    $id++;
  }
}
?>
</table>
</div>
</body>
