<?php
session_start();
require_once 'config/connection.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/search.css" />
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
            <img src="assets/images/icons/Course.svg" alt="Courses" /> Courses
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

    <main class="search-container">
      <div class="search-header">
        <h1>Finding course made easy</h1>
        <p>Search. Explore. Learn</p>
        <div class="search-bar">
          <img
            src="assets/images/icons/search.svg"
            alt="Search"
            class="search-icon"
          />
          <input type="text" placeholder="Search" value="React Js" />
        </div>
      </div>

      <div class="search-results">
        <p class="results-text">Search results for "React Js"</p>
        <div class="search-tabs">
          <a href="#" class="active">Courses</a>
        </div>

        <div class="course-cards">
          <div class="course-card">
            <img
              src="assets/images/course1.png"
              alt="Course"
              class="course-avatar"
            />
            <div class="course-info">
              <span class="course-tag">Web development</span>
              <h3>Complete React.js course</h3>
              <p class="course-author">
                Esther Howard <span class="dot"></span> 5hr
              </p>
            </div>
            <button class="start-btn">Start Learning</button>
          </div>

          <div class="course-card">
            <img
              src="assets/images/course1.png"
              alt="Course"
              class="course-avatar"
            />
            <div class="course-info">
              <span class="course-tag">Web development</span>
              <h3>React Crash Course</h3>
              <p class="course-author">
                Brooklyn Simmons <span class="dot"></span> 2hr
              </p>
            </div>
            <button class="start-btn">Start Learning</button>
          </div>

          <div class="course-card">
            <img
              src="assets/images/course1.png"
              alt="Course"
              class="course-avatar"
            />
            <div class="course-info">
              <span class="course-tag">Web development</span>
              <h3>Learn hooks in React Js</h3>
              <p class="course-author">
                Jenny Wilson <span class="dot"></span> 1hr
              </p>
            </div>
            <button class="start-btn">Start Learning</button>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
