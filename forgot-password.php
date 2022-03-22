<?php

if(isset($_POST["forgot-password-submit"])){
    require 'config.php';
    include 'functions.php';
    session_start();
    
    // store user's email after submit 
    $email = $_POST["email"];
    // check if the username exists
    $emailExists = false;
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $checkEmail = mysqli_stmt_get_result($stmt);
    } else {
        echo "There was an error!";
        exit();
    }
    if(mysqli_num_rows($checkEmail)) {
        $emailExists = true;

        // token and url 
        $token = strval(rand());
        $url = "http://localhost/project/SecureAppsProject/reset-password.php?token=" . $token;

        // expiration for token (active for one hour)
        // $expires = date('Y/m/d H:i:s', strtotime('+1 hours'));
        $expires = date('Y/m/d H:i:s', time() + 180);

        // need to remove any existing token inside the database if a user tried to reset their password 
        // create a prepared statement and an sql query
        $stmt = mysqli_stmt_init($connection);
        $query = "DELETE FROM reset_password WHERE email=?";
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
        } else{
            echo "There was an error!";
            exit();
        }

        // insert into database
        $query = "INSERT INTO reset_password (email, token, expiry_date) VALUES (?,?,?)";
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, $query)) {
            // hash the token before inserting into database
            $hashed_token = password_hash($token, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sss", $email, $token, $expires);
            mysqli_stmt_execute($stmt);
        } else{
            echo "There was an error!";
            exit();
        }

        try {
            // send the email to the stored user's email
            $usersEmail = $email;
            $subject = 'Reset your password';
            $message = '<p>Please click on the following link to reset your password: </br>';
            $message .= '<a href="'. $url . '">'. $url . '</a></p>';
            $headers = "From: AdamCoakley <AdamCoakleySAD@gmail.com>" . "\r\n";
            $headers .= "Reply to: AdamCoakleySAD@gmail.com" . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; // to allow html to work in email
            mail($usersEmail, $subject, $message, $headers);
            // mail is sent 
            $success = true;
        }
        catch (Exception $exception) {
            $success = false;
        }
    }
} 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
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
                        <img src="assets/img/forgot-password.svg" width="300" height="250" alt=""> 
                    </div>
                    <div class="right-column">
                        <h2>Forgot Password</h2>
                        <p> Enter your email address to reset your password </p>
                        <form action="" method="post">
                            <input type="email" name="email" placeholder="Email Address" value="" required>
                            <!-- if the email does not exist, echo error (no password exists to be reset) -->
                            <?php if(isset($emailExists) && $emailExists === false) {
                                    echo "<p class='error-message'>The email does not belong to a registered user.</p>";
                            } ?>
                            <!-- success message -->
                            <?php if(isset($emailExists) && $emailExists === true && $success === true) {
                                    echo "<p class='success-message'>Success! Check your email.</p>";
                            } ?>
                            <!-- failed to send email message -->
                            <?php if(isset($success) && $success === false) {
                                    echo "<p class='error-message'>Oops! Something went wrong.</p>";
                            } ?>
                            <button name="forgot-password-submit" class="btn" type="submit">Submit</button>
                        </form>
                        <div class="login-text">
                            <p><a href="login.php">Go back</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>