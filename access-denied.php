<?php
include 'db_setup.php';
include_once 'functions.php';

if (isset($_POST['reauthenticate'])) {
    header("Location: login.php");
    die();
}

?> <!-- end of php --> 

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
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
                        <img src="assets/img/error.svg" width="200" height="200" alt="">
                    </div>
                    <div class="right-column">
                        <h2>Access denied</h2>
                        <p>Please return to the login page.</p>
                        <form action="" method="post">
                            <button name="reauthenticate" class="btn" type="submit">Return</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>