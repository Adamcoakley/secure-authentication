<!-- php code including form validation + data storage -->
<?php
include 'db_setup.php';
include_once 'functions.php';
session_start();

    // variables to store the submitted inputs from the form 
    if (isset($_POST['submit'])) {
        $username = removeXSS($_POST['username']);
        $email = removeXSS($_POST['email']);
        $password = removeXSS($_POST['password']);
        $confirm_password = removeXSS($_POST['confirm-password']);

        // username complexity rules
        $uppercase = preg_match('@[A-Z]@', $username);
        $lowercase = preg_match('@[a-z]@', $username);

        // password complexity rules
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        // query to see if the username exists in database
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt); 
                $checkUsername = mysqli_stmt_get_result($stmt);
        } else {
            echo "There was an error!";
            exit();
        }
        //$checkUsername = mysqli_query($connection, "SELECT * FROM users WHERE username = '$username'");

        // query to see if the email exists in database
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt); 
                $checkEmail = mysqli_stmt_get_result($stmt);
        } else {
            echo "!There was an error!";
            exit();
        }
        //$checkEmail = mysqli_query($connection, "SELECT * FROM users WHERE email = '$email'");

        //check if the username contains whitespace
        if (preg_match('/\s/', $username) ){
            $whitespace = true;  
        }
        // check to see if the username exists in database
        else if(mysqli_num_rows($checkUsername)) {
            $usernameDuplicate = true;
        } 
        // check to see if the emaiil exists in database
        else if(mysqli_num_rows($checkEmail)) {
            $emailDuplicate = true;
        } 
        // error output messages for not meeting the password requirments
        else if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $error = true;
        } 
        else if($password != $confirm_password){
            $passwords_match = false;
        }
        // storing the data in the database making sure we store the secure password 
        else {
            // hash password
            $secure_password = md5($password);
            // generate salt
            $salt = generateSalt();
            // assign user type
            $user_type = "user";
            $query = "INSERT INTO users (username, email, salt, password, user_type) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($connection);
            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $salt, $secure_password, $user_type);
                mysqli_stmt_execute($stmt); 
                //header function is used to redirect the browser, users are forced to login after creating an account
                header("Location: login.php");
                die();
            } else {
                echo "There was an error!";
                exit();
            }
        }
    }
?> <!-- end of php --> 

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
    <!-- css link -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="all" />
    <!-- eye icon --> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <section class="surrounding-container"> <!-- background, surrounding container-->
        <div class="container">
            <div class="content-section">
                <div class="columns">
                    <div class="left-column">
                        <img width="250" height="250" src="assets/img/male.svg" alt="">
                    </div>
                    <div class="right-column">
                        <h2>Sign up</h2>
                        <p>Learn more about secure application development </p>
                        <form action="" method="post" id="form">
                            <input type="text" name="username" placeholder="Username" value="" required>
                            <input type="email" name="email" placeholder="Email Address" value="" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <input type="password" name="confirm-password" placeholder="Confirm Password" required>
                            <!-- check to see if username contains whitespace -->
                            <?php if(isset($whitespace)) {
                                    echo "<p class='error-message'>The username cannot contain a space.</p>";
                            } ?>
                            <!-- username duplicate -->
                            <?php if(isset($usernameDuplicate)) {
                                    echo "<p class='error-message'>The username is already in use.</p>";
                            } ?>
                            <!-- email duplicate -->
                            <?php if(isset($emailDuplicate)) {
                                    echo "<p class='error-message'>The email address is already in use.</p>";
                            } ?>
                             <!-- password rules error message -->
                            <?php if(isset($error)) {
                                    echo "<p class='error-message'>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</p>";
                            } ?>
                            <!-- passwords do not match error message -->
                            <?php if(isset($passwords_match)) {
                                    echo "<p class='error-message'>The password combination does not match.</p>";
                            } ?>
                            <button name="submit" class="btn" type="submit">Sign up</button>
                        </form>
                        <div class="login-text">
                            <p>Already registered? <a href="login.php">Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>