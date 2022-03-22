<?php
include 'db_setup.php';
include_once 'functions.php';
session_start();

if (isset($_GET['change-password'], $_GET['token'])) {
    // the user may try use different token
    if($_GET['token'] != $_SESSION['token']){
      session_destroy();
      header("Location: login.php");
    }
    // variables to store the submitted inputs from the form 
    $oldPass = removeXSS($_GET['old-password']);
    $newPass = removeXSS($_GET['new-password']);
    $confirmPass = removeXSS($_GET['confirm-password']);
    $username = $_SESSION['username'];

    // password complexity rules
    $uppercase = preg_match('@[A-Z]@', $newPass);
    $lowercase = preg_match('@[a-z]@', $newPass);
    $number = preg_match('@[0-9]@', $newPass);
    $specialChars = preg_match('@[^\w]@', $newPass);

    // hash the password
    $oldPass = md5($oldPass);

    // check if the password exists in db
    $query = "SELECT * FROM users WHERE username=? AND password=?";
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $oldPass);
        mysqli_stmt_execute($stmt); 
        $result = mysqli_stmt_get_result($stmt);
    } else {
       echo "There was an error!";
       exit();
    } 
    if(mysqli_num_rows($result)) {
      // error output messages for not meeting the password requirments
      if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($newPass) < 8) {
        $error = true;
      } 
      // check two passwords match
      else if($newPass != $confirmPass){
        $passwords_match = false;
      } 
      else if(md5($newPass) == $oldPass){
        $error_passwords_match = true;
      }
      else {
        // no problems, proceed with updating password
        $newPass = md5($newPass);
        $success = true;
        $query = "UPDATE users SET password=? WHERE username=?";
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "ss", $newPass, $username);
            mysqli_stmt_execute($stmt); 
            $result = mysqli_stmt_get_result($stmt);
            header('Refresh: 5; login.php');
            session_destroy();
        } else {
            echo "There was an error!";
            exit();
        } 
      }
    } else{
        $incorrectPass = true;
    }
  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="all" />
    <title>Profile</title>
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
            <li><a class="nav-links" href="profile.php">Profile</a></li>
            <li><a class="nav-links" href="contact.php">Contact</a></li>
        </ul>
      </nav>
    </header>
    <div class="page-content-pass">
      <h2> Change password </h2>
         <form action="reset-password.php" method="GET">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
            <input type="password" name="old-password" placeholder="Old Password" required>
            <input type="password" name="new-password" placeholder="New password" required>
            <input type="password" name="confirm-password" placeholder="Confirm Password" required>
            <!-- incorrect password -->
            <?php if(isset($incorrectPass)) {
                echo "<p class='error-message'>Incorrect password. Try again.</p>";
             } ?>
             <!-- new pass same as old pass (error) -->
            <?php if(isset($error_passwords_match)) {
                echo "<p class='error-message'>New password cannot be the same as the old password.</p>";
             } ?>
             <!-- password rules error message -->
             <?php if(isset($error)) {
                echo "<p class='error-message'>New password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</p>";
             } ?>
             <!-- passwords do not match error message -->
             <?php if(isset($passwords_match)) {
                echo "<p class='error-message'>The new password combination does not match.</p>";
             } ?>
             <!-- passwords do not match error message -->
             <?php if(isset($success)) {
                echo "<p class='success-message'>Success! You will be redirected in 5 seconds..</p>";
             } ?>
            <button name="change-password" class="btn" type="submit">Change Password</button>
          </form>
    </div>
  </body>
</html>