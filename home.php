<?php
include 'db_setup.php';
include_once 'functions.php';
session_start();

// generate token for session
$_SESSION['token'] = strval(rand());

if (!isset($_SESSION['username'])){
    header("Location: access-denied.php");
    die();
} else if(isset($_SESSION['username'])){
   if((time() - $_SESSION['inactivity_count']) > 600){ // 60 seconds x 10 minutes = 600 seconds
          alert("You have been signed out due to inactivity.", "login.php"); 
   } else {  
          $_SESSION['inactivity_count'] = time();  
   }  
}

// end session after an hour 
if(time() > $_SESSION['session_end']){
    alert("Session expired.", "login.php");
}

//logout of the application and destroy / unset the session variables 
if(isset($_POST["logout"])){
    session_destroy();
    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="all" />
    <title>Home</title>
  </head>
  <body>
    <section class="home-container">
          <image class="home-img" src="assets/img/home.jpg">
    </section>
    <header>
      <h1>SAD Project</h1>
      <nav>
        <ul>
            <li><a class="nav-links" href="home.php">Home</a></li>
            <!-- more links can be added here -->
        </ul>
      </nav>
    </header>
    <div class="page-content">
        <h2>Hello <?php echo removeXSS($_SESSION['username']) ?>,</h2>
        <p>Welcome to the website!</p>
        <form action="" method="post">
          <button name="logout" class="btn" type="submit">Logout</button>
        </form>
    </div>
  </body>
</html>
            