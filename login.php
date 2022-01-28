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
                            <button name="submit" class="btn" type="submit">Sign in</button>
                        </form>
                        <div class="login-text">
                            <p>Not registered? <a href="register.php">Create an account</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->

</body>

</html>