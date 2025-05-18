<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - TuniLearn</title>
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
          Join TuniLearn today and start your learning journey. Access hundreds
          of courses and grow your skills with our expert-led content.
        </p>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <form action="handlers/register_handler.php" method="POST">
          <div class="input-row">
            <div class="input-group">
              <label for="fullname">Full Name</label>
              <input
                type="text"
                id="fullname"
                name="fullname"
                placeholder="Enter your full name"
                required
              />
            </div>
            <div class="input-group">
              <label for="email">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
              />
            </div>
          </div>
          <div class="input-row">
            <div class="input-group">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
              />
            </div>
            <div class="input-group">
              <label for="confirm-password">Confirm Password</label>
              <input
                type="password"
                id="confirm-password"
                name="confirm-password"
                placeholder="Confirm your password"
                required
              />
            </div>
          </div>
          <button type="submit">Create Account</button>
        </form>
        <p class="login-link">
          Already have an account? <a href="index.php">Login here</a>
        </p>
      </div>
      <div class="login-container-right">
        <img src="assets/images/login-image.jpg" alt="Register Image" />
      </div>
    </div>
  </body>
</html>
