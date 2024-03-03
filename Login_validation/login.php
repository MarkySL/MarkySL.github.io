<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/loginreg.css">
    <title>Login</title>
    <link rel="shortcut icon" href="../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="logo"></div>
    <div class="wrapper">
        <!-- ==================== Login Form ==================== -->
        <div class="login-form">
            <!--Header-->
            <h2>Login</h2>

            <!-- Capture and Display Error Message-->
            <?php
                session_start();
                if (isset($_SESSION['error'])) {

                    echo '<p class="error">' . $_SESSION['error'] . '</p>';
                    unset($_SESSION['error']);  // remove the message so it doesn't persist
                }     
            ?> 

            <form action="login-check.php" method="POST">
                <!--Username-->
                <div class="input-box">
                    <span class="icon"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="login-user" placeholder="Enter your username">
                </div>

                <!--Password-->
                <div class="input-box">
                    <span class="icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="login-pass" placeholder="Enter your password">
                </div>

                <!--Login Button-->
                <button type="submit" name="loginbtn" class="btn">Login</button>

                <!--Registration-->
                <div class="registration">
                    <p>Don't have an account? <a href="register.php" class="register-link">Register</a></p>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>