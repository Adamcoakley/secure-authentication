<?php
include 'config.php';

    //check if form is submitted 
    if (isset($_POST['submit'])) {
        // variables to store the submitted inputs from the form 
        $email = $_POST['email'];
        $password = $_POST['password'];

        //check if the email exists
        $result = mysqli_query($connection, "SELECT * FROM users WHERE email = '".$_POST['email']."'");
        if(mysqli_num_rows($result)) {
            //email exists so we store row (array) of data in variable 
            $row = mysqli_fetch_row($result);
            //variable to store hashed password, data returned from db in the form of [id, name, email, password]
            $hashedPassword = $row[3];
            //verify the matching password vs password submitted
            if (password_verify($password, $hashedPassword)){
                header("Location: index.php");
            } else{
                $noPasswordMatch = true;
            }
        } else{
            $emailNotFound = true;
        }
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
                        <img src="assets/img/female.svg" alt="">
                    </div>
                    <div class="right-column">
                        <h2>Sign in</h2>
                        <p>Learn more about secure application development </p>
                        <form action="" method="post">
                            <input type="email" class="email" name="email" placeholder="Email Address" value="" required>
                            <input type="password" class="password" name="password" placeholder="Password" required>
                            <div class="forgot-password">
                                <a href="login.php">Forgot password?</a>
                            </div>
                            <!-- login failed as a result of an incorrect email or password -->
                            <?php if(isset($noPasswordMatch) || isset($emailNotFound)) {
                                    echo "<p class='mycss'>The email " .$email. " and password could not be authenticated at the moment</p>";
                            } ?>
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