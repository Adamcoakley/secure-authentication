<!-- php code including form validation + data storage -->
<?php
include 'config.php';

    // variables to store the submitted inputs from the form 
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];

        // password complexity rules
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        //error output messages for not meeting the password requirments
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $error = true;
        } 
        else if($password != $confirm_password){
            $passwords_match = false;
        }
        // storing the data in the database making sure we store the secure password 
        else {
            // password_hash() function generates a unique salt and concatenates it to the password before hashing 
            $secure_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "insert into users (name, email, password) values ('$name', '$email', '$secure_password')";
            $result = mysqli_query($connection, $query); 
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
</head>
<body>
    <section class="surrounding-container"> <!-- background, surrounding container-->
        <div class="container">
            <div class="content-section">
                <div class="columns">
                    <div class="left-column">
                        <img src="assets/img/male.svg" alt="">
                    </div>
                    <div class="right-column">
                        <h2>Sign up</h2>
                        <p>Learn more about secure application development </p>
                        <form action="" method="post" id="form">
                            <input type="text" class="name" name="name" placeholder="Name" value="" required>
                            <input type="email" class="email" name="email" placeholder="Email Address" value="" required>
                            <input type="password" class="password" name="password" placeholder="Password" required>
                            <input type="password" class="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
                             <!-- password rules error message -->
                            <?php if(isset($error)) {
                                    echo "<p class='mycss'>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</p>";
                            } ?>
                            <!-- passwords do not match error message -->
                            <?php if(isset($passwords_match)) {
                                    echo "<p class='mycss'>The password combination does not match.</p>";
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