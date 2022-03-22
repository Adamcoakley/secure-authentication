<?php
include 'db_setup.php';
include_once 'functions.php';

    // check if session variable is set on page load
    if(isset($_SESSION['username'])){
        session_destroy();
    }

    session_start();

    // check if form is submitted 
    if (isset($_POST['submit'])) {
        // variables to store the submitted inputs from the form 
        $username = removeXSS($_POST['username']);
        $password = removeXSS($_POST['password']);
        $password = md5($password);

        // Obtain IP Address
        $IPAddress = $_SERVER['REMOTE_ADDR'];

        // Obtain User Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        //current time
        $time = time(); 

        // fetch the user's attempt count (5 rows = 5 attempts)
        $connection = new mysqli("localhost", "SADUSER", "SADUSER");
        mysqli_select_db($connection, 'users_db');

        $query = "SELECT count(*) AS attempt_count FROM login_attempts WHERE ip_address='$IPAddress'";
        if($result = $connection->query($query)){
            $login_attempts = mysqli_fetch_assoc($result);
        }
        else{
            echo $connection->error;
        }
        //$result = mysqli_fetch_assoc(mysqli_query($connection, "SELECT count(*) AS attempt_count FROM login_attempts WHERE ip_address='$IPAddress'"));
        $attempt_count = $login_attempts['attempt_count'];
        //echo "Attempt count: ".$attempt_count;

        // get the max entry from timestamp column
        $query = "SELECT MAX(timer) as max FROM login_attempts";
        if($result = $connection->query($query)){
            $row = mysqli_fetch_array($result);
        }
        else{
            echo $connection->error;
        }
        $timestamp = $row['max'];
        //$query = mysqli_query($connection, "SELECT MAX(timer) as max FROM login_attempts");

        // timeout user's who exceed attempt limit
        if($attempt_count == 5 && $time < $timestamp){
            $limitReached = true;
        } else {
            // check if current time is later than expiry timestamp 
            if($time > $timestamp){
                // delete all records (reset attempt count)
                $query = "DELETE FROM login_attempts WHERE ip_address = ?";
                $stmt = mysqli_stmt_init($connection);
                if (mysqli_stmt_prepare($stmt, $query)) {
                        mysqli_stmt_bind_param($stmt, "s", $IPAddress);
                        mysqli_stmt_execute($stmt); 
                } else {
                    echo "There was an error!";
                    exit();
                }
            }
            // check if the username exists
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = mysqli_stmt_init($connection);
            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt); 
                 $result = mysqli_stmt_get_result($stmt);
            } else {
                echo "There was an error!";
                exit();
            }
            
            if(mysqli_num_rows($result)) {
                // username exists so we store row (array) of data in variable 
                $row = mysqli_fetch_row($result);
                // variables to store hashed password and salt, data returned from db in the form of [id, username, email, salt, password, user_type] 
                $salt = $row[3];
                $hashedPassword = $row[4];
                $db_password = $salt . $hashedPassword;
                // put salt and password together
                $password = $salt . $password;
                // verify the matching password vs password submitted 
                if ($password == $db_password){
                    // Login succesful, assign session variable to track session length
                    $_SESSION['inactivity_count'] = time();
                    $_SESSION['session_start'] = time();
                    $_SESSION['session_end'] = (time() + 3600); // 60 seconds x 60 minutes = 3600 seconds
                    // add a succesful login attempt to log
                    $currentDate =  date('Y/m/d H:i:s');
                    $query = "INSERT INTO event_log (username, success_failure, reason, date_time, ip_address, user_agent) 
                        VALUES (?,?,?,?,'$IPAddress',?)";
                    $stmt = mysqli_stmt_init($connection);
                    if (mysqli_stmt_prepare($stmt, $query)) {
                            $reason = "no errors found.";
                            $success = "success";
                            mysqli_stmt_bind_param($stmt, "sssss", $username, $success, $reason, $currentDate, $userAgent);
                            mysqli_stmt_execute($stmt); 
                            $result = mysqli_stmt_get_result($stmt);
                    } else {
                        echo "There was an error!";
                        exit();
                    }         
                    // delete all records (reset attempt count)
                    $query = "DELETE FROM login_attempts WHERE ip_address = '$IPAddress'";
                    $result = mysqli_query($connection, $query);
                    // check if it's the admin
                    if($row[5] == "admin"){
                        // Redirect them to the admin page
                        $_SESSION['admin'] = $username;
                        header("Location: admin.php");
                    } else{
                        // Redirect them to the home page
                        $_SESSION['username'] = $username;
                        header("Location: home.php");
                    }
            } else{
                // increment attempt count
                $attempt_count++;  
                $attempts_remaining = 5 - $attempt_count;
                // failed login attempt to login attempts table (keep track of the number of attempts)
                $expiry_time = (time() + 180); // 60 seconds x 3 minutes = 180 seconds
                $query = "INSERT INTO login_attempts (username, ip_address, timer) VALUES (?, '$IPAddress', ?)";
                //$query = "INSERT INTO attempts_table (username, ip_address, timer) VALUES (?, '$IPAddress', ?)";
                $stmt = mysqli_stmt_init($connection);
                if (mysqli_stmt_prepare($stmt, $query)) {
                        mysqli_stmt_bind_param($stmt, "ss", $username, $expiry_time);
                        mysqli_stmt_execute($stmt); 
                        $result = mysqli_stmt_get_result($stmt);
                } else {
                    echo "There was an error!";
                    exit();
                } 
                $query = "INSERT INTO event_log (username, success_failure, reason, date_time, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, '$IPAddress', ?)";
                $stmt = mysqli_stmt_init($connection);
                if (mysqli_stmt_prepare($stmt, $query)) {
                        $reason = "incorrect password.";
                        $failure = "failure";
                        $currentDate =  date('Y/m/d H:i:s');
                        mysqli_stmt_bind_param($stmt, "sssss", $username, $failure, $reason, $currentDate, $userAgent);
                        mysqli_stmt_execute($stmt); 
                        $result = mysqli_stmt_get_result($stmt);
                } else {
                    echo "There was an error!";
                    exit();
                } 
                // check if user attempt limit reached
                if($attempts_remaining == 0){
                    $limitReached = true;
                   
                } else{
                    $noPasswordMatch = true;
                }
            }
        } else{
                // increment attempt count
                $attempt_count++;  
                $attempts_remaining = 5 - $attempt_count;
                // failed login attempt to login attempts table (keep track of the number of attempts)
                $expiry_time = (time() + 180); // 60 seconds x 3 minutes = 180 seconds
                $query = "INSERT INTO login_attempts (username, ip_address, timer) VALUES (?, '$IPAddress', ?)";
                $stmt = mysqli_stmt_init($connection);
                if (mysqli_stmt_prepare($stmt, $query)) {
                        mysqli_stmt_bind_param($stmt, "ss", $username, $expiry_time);
                        mysqli_stmt_execute($stmt); 
                        $result = mysqli_stmt_get_result($stmt);
                } else {
                    echo "There was an error!";
                    exit();
                } 
                // insert attempt into event log table
                $query = "INSERT INTO event_log (username, success_failure, reason, date_time, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, '$IPAddress', ?)";
                $currentDate =  date('Y/m/d H:i:s');
                $stmt = mysqli_stmt_init($connection);
                if (mysqli_stmt_prepare($stmt, $query)) {
                        $reason = "username not found.";
                        $failure = "failure";
                        mysqli_stmt_bind_param($stmt, "sssss", $username, $failure, $reason, $currentDate, $userAgent);
                        mysqli_stmt_execute($stmt); 
                        $result = mysqli_stmt_get_result($stmt);
                } else {
                    echo "There was an error!";
                    exit();
                } 
                // check if user attempt limit reached
                if($attempts_remaining == 0){
                    $limitReached = true;
                } else{
                    $noPasswordMatch = true;
                }
            }
        } //end of else statement
    }
?> <!-- end of php --> 

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="all" />
    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
</head>
<body>
    <section class="surrounding-container"> <!-- background, surrounding container-->
        <div class="container">
            <div class="content-section">
                <div class="columns">
                    <div class="left-column">
                        <img src="assets/img/female.svg" width="250" height="250" alt="">
                    </div>
                    <div class="right-column">
                        <h2>Sign in</h2>
                        <p>Learn more about secure application development </p>
                        <form action="" method="post">
                            <input type="username" name="username" placeholder="Username" value="" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <!-- attempt limit reached -->
                            <?php if(isset($limitReached)) {
                                    echo "<p class='error-message'>Try again later. Temporarily blocked.</p>";
                            } ?>
                            <!-- login failed as a result of an incorrect username or password -->
                            <?php if(isset($noPasswordMatch)) {
                                    // bug fix, code goes to -1 after reset
                                    // countdown = -1, 3, 2, 1
                                    // so setting it as 4, it becomes 4, 3, 2, 1
                                    if($attempts_remaining == -1){
                                        $attempts_remaining = 4;
                                    }
                                    echo "<p class='error-message'>The username " . removeXSS($username). " and password could not be authenticated at the moment. " . $attempts_remaining . " attempts remaining.</p>";
                            } ?>
                            <!-- username not found -->
                            <?php if(isset($usernameNotFound)) {
                                    if($attempts_remaining == -1){
                                        $attempts_remaining = 4;
                                    }
                                    echo "<p class='error-message'>The username " . removeXSS($username). " and password could not be authenticated at the moment. " . $attempts_remaining . " attempts remaining.</p>";
                            } ?>
                            <div class="forgot-password login-text">
                                <a href="forgot-password.php">Forgot password?</a>
                            </div>
                            <button name="submit" class="btn" type="submit">Sign in</button>
                        </form>
                        <div class="login-text">
                            <p>Not registered? <a href="register.php">Create an account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

