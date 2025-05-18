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
    <title>Course Content - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/course-content.css" />
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

    <div class="course-layout">
      <aside class="course-sidebar">
        <h2>Course Content</h2>
        <div class="course-sections">
          <div class="section">
            <details open>
              <summary>Getting Started</summary>
              <ul>
                <li class="active">
                  <img
                    src="assets/images/icons/Courseinside.svg"
                    alt="Document"
                  />
                  Welcome to the course
                </li>
                <li>
                  <img src="assets/images/icons/play-circle.svg" alt="Clock" />
                  What is React Js ?
                </li>
                <li>
                  <img src="assets/images/icons/play-circle.svg" alt="Clock" />
                  Why "React" but not "JavaScript"?
                </li>
                <li>
                  <img src="assets/images/icons/play-circle.svg" alt="Clock" />
                  Setting up Environment
                </li>
              </ul>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>JavaScript refresher</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>React Basics & Working with Components</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>React States & Working with events</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>Rendering listings</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>Styling React Components</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>Debugging React Apps</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>Practice - A complete project</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>Diving Deeper</summary>
            </details>
          </div>
          <div class="section">
            <details>
              <summary>Advance Topics</summary>
            </details>
          </div>
        </div>
      </aside>

      <main class="content">
        <div class="content-header">
          <div class="navigation">
            <a href="./courses.php" class="back-btn">
              <img src="assets/images/icons/arrow.svg" alt="Back" />
              Welcome to the course
            </a>
            <div class="nav-controls">
              <button class="prev">Prev</button>
              <button class="next">Next</button>
              <button class="complete">Mark as Complete</button>
            </div>
          </div>
          <div class="user-profile">
            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-pic" />
            <span><?php echo htmlspecialchars($user['username']); ?></span>
          </div>
        </div>

        <div class="video-container">
          <img
            src="assets/images/course-image-full.png"
            alt="React Logo"
            class="react-logo"
          />
        </div>

        <div class="lesson-content">
          <h1>Welcome to the course</h1>
          <p>
            Lorem ipsum is Simply Dummy Text Of The Printing And Typesetting
            Industry. Lorem Ipsum Has Been The Industry's Standard Dummy Text
            Ever Since The 1500s, When An Unknown Printer Took A Galley Of
          </p>
          <p>
            Type And Scrambled It To Make A Type Specimen Book. It Has Survived
            Not Only Five Centuries, But Also The Leap Into Electronic
            Typesetting, Remaining Essentially Unchanged. It Was Popularised In
            The 1960s With The
          </p>
          <p>
            Release Of Letraset Sheets Containing Lorem Ipsum Passages, And More
            Recently With Desktop Publishing Software Like Aldus PageMaker
            Including Versions Of Lorem Ipsum.
          </p>
        </div>
      </main>
    </div>
  </body>
</html>
