<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log In - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/login.css" />
  </head>
  <body>
    <div class="login-container">
      <div class="login-container-left">
        <a href="./home.php" style="text-decoration: none">
          <h1>TuniLearn</h1>
        </a>
        <p>
          TuniLearn is a platform that helps you learn and grow. It is a
          platform that helps you learn and grow.
        </p>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        <form action="handlers/login_handler.php" method="POST">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
          />
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
          />
          <button type="submit">Log In</button>
        </form>
        <p class="login-link">
          Don't have an account? <a href="register.php">Register here</a>
        </p>
      </div>
      <div class="login-container-right">
        <img src="assets/images/login-image.jpg" alt="Login Image" />
      </div>
    </div>
  </body>
</html>
