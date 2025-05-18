<?php
session_start();
require_once 'config/connection.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($connexion, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/profile.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="nav-left">
        <h1 class="logo">TuniLearn.</h1>
        <div class="nav-links">
          <a href="./home.php" class="nav-link">
            <img src="assets/images/icons/home.svg" alt="Home" /> Home
          </a>
          <a href="./courses.php" class="nav-link">
            <img src="assets/images/icons/Course.svg" alt="Courses" />
            Courses
          </a>
        </div>
      </div>
      <div class="nav-right">
        <a href="./contact.php">Contact us</a>
        <a href="./search.php"
          ><img
            src="assets/images/icons/search.svg"
            alt="Search"
            class="nav-icon"
        /></a>
        <a href="./profile.php">
          <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-pic" />
        </a>
      </div>
    </nav>

    <main class="profile-container">
      <div class="profile-header">
        <div class="profile-info">
          <img
            src="<?php echo htmlspecialchars($user['profile_image']); ?>"
            alt="<?php echo htmlspecialchars($user['name']); ?>"
            class="profile-image"
          />
          <div class="profile-details">
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <p><span class="badge"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span> <?php echo htmlspecialchars($user['email']); ?></p>
          </div>
        </div>
        <div class="profile-meta">
          <div class="meta-item">
            <h3>Involvements</h3>
            <p><?php echo htmlspecialchars($user['involvements']); ?></p>
          </div>
          <div class="meta-item">
            <h3>Specialisation</h3>
            <p><?php echo htmlspecialchars($user['specialisation']); ?></p>
          </div>
        </div>
      </div>

      <div class="profile-nav">
        <a href="#" class="active"
          ><img src="assets/images/icons/user.svg" alt="About" /> About me</a
        >
        <a href="#"
          ><img src="assets/images/icons/Course.svg" alt="Courses" /> Enrolled
          Courses</a
        >
        <a href="#"
          ><img src="assets/images/icons/Course.svg" alt="Saved" /> Saved
          Courses</a
        >
      </div>

      <div class="profile-completion">
        <div class="completion-header">
          <h3>Complete your Profile</h3>
          <img
            src="assets/images/icons/lock.svg"
            alt="Lock"
            class="lock-icon"
          />
        </div>
        <div class="progress-bar">
          <div class="progress" style="width: 33%"></div>
        </div>
        <p class="completion-text">Please complete your profile (1/3)</p>
      </div>

      <div class="profile-about">
        <h3>About</h3>
        <?php if (!empty($user['bio'])): ?>
          <p><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
        <?php else: ?>
          <p class="empty-bio">
            No bio added yet. 
            <a href="./settings.php" style="color: #2563eb; text-decoration: none;">
              Add your bio in settings
            </a>
          </p>
        <?php endif; ?>
      </div>

      <div class="profile-skills">
        <h3>Skills</h3>
        <div class="skills-list">
          <?php 
          if (!empty($user['skills'])) {
            $skills = explode(',', $user['skills']);
            foreach($skills as $skill) {
              echo '<span class="skill-badge">' . htmlspecialchars($skill) . '</span>';
            }
          } else {
            echo '<p>No skills added yet. <a href="./settings.php" style="color: #2563eb; text-decoration: none;">Add your skills in settings</a></p>';
          }
          ?>
        </div>
      </div>
    </main>
  </body>
</html>
