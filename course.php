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
    <title>Course Details - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/course.css" />
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
        <a href="./search.php">
          <img
            src="assets/images/icons/search.svg"
            alt="Search"
            class="nav-icon"
          />
        </a>
        <a href="./profile.php">
          <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-pic" />
        </a>
      </div>
    </nav>

    <main class="course-container">
      <div class="course-header">
        <div class="course-info">
          <span class="course-tag">Web development</span>
          <h1>Zero to Hero in React Js with Jane Doe</h1>
          <p class="course-description">
            Dive in and learn React.js from scratch! Learn React.js, Hooks,
            Redux, React Routing, Animations, Next.js and way more!
          </p>
          <div class="course-meta">
            <div class="meta-item">
              <img src="assets/images/icons/students.svg" alt="Students" />
              <span>745,123 Students</span>
            </div>
            <div class="meta-item">
              <img src="assets/images/icons/time.svg" alt="Duration" />
              <span>6 hr</span>
            </div>
            <div class="meta-item">
              <img src="assets/images/icons/calender.svg" alt="Last Updated" />
              <span>Last Updated on 23 May, 2023</span>
            </div>
          </div>
          <button class="enroll-btn">Enroll Now</button>
        </div>
        <div class="course-image">
          <img src="assets/images/right.png" alt="React Course Thumbnail" />
        </div>
      </div>

      <div class="course-content">
        <div class="content-tabs">
          <a href="#" class="active">About</a>
          <a href="#">Course Content</a>
          <a href="#">What's included</a>
          <a href="#">Reviews</a>
        </div>

        <div class="content-section">
          <h2>Description</h2>
          <p>
            Lorem ipsum is simply dummy text of the printing and typesetting
            industry. Lorem Ipsum has been the industry's standard dummy text
            ever since the 1500s, when an unknown printer took a galley of type
            and scrambled it to make a type specimen book. It has survived not
            only five centuries, but also the leap into electronic typesetting,
            remaining essentially unchanged. It was popularised in the 1960s
            with the release of Letraset sheets containing Lorem Ipsum passages,
            and more recently with desktop publishing software like Aldus
            PageMaker including versions of Lorem Ipsum.
          </p>
          <p>
            Lorem ipsum is simply dummy text of the printing and typesetting
            industry. Lorem Ipsum has been the industry's standard dummy text
            ever since the 1500s, when an unknown printer took a galley of type
            and scrambled it to make a type specimen book. It has survived not
            only five centuries, but also the leap into electronic typesetting,
            remaining essentially unchanged. It was popularised in the 1960s
            with the release of Letraset sheets containing Lorem Ipsum passages,
            and more recently with desktop publishing software like Aldus
            PageMaker including versions of Lorem Ipsum.
          </p>
        </div>
      </div>
    </main>
  </body>
</html>
