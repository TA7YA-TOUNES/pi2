<?php
session_start();
require_once 'config/connection.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($connexion, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get courses in progress count
$in_progress_query = "SELECT COUNT(*) as count FROM student_enrollments WHERE student_id = $user_id";
$in_progress_result = mysqli_query($connexion, $in_progress_query);
$in_progress = mysqli_fetch_assoc($in_progress_result)['count'];

// Get completed courses count (assuming completion is tracked in student_completed_quizzes)
$completed_query = "SELECT COUNT(DISTINCT c.course_id) as count 
                   FROM student_completed_quizzes scq 
                   JOIN quizzes q ON scq.quiz_id = q.quiz_id 
                   JOIN courses c ON q.course_id = c.course_id 
                   WHERE scq.student_id = $user_id";
$completed_result = mysqli_query($connexion, $completed_query);
$completed = mysqli_fetch_assoc($completed_result)['count'];

// Get recent courses
$recent_courses_query = "SELECT c.*, u.name as instructor_name 
                        FROM courses c 
                        JOIN users u ON c.instructor_id = u.user_id 
                        JOIN student_enrollments se ON c.course_id = se.course_id 
                        WHERE se.student_id = $user_id 
                        ORDER BY c.course_id DESC LIMIT 4";
$recent_courses_result = mysqli_query($connexion, $recent_courses_query);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/style.css" />
  </head>
  <body>
    <div class="dashboardContainer">
      <div class="sidebar">
        <h1 class="logo">TuniLearn.</h1>
        <div class="sidebar-links">
          <a href="./home.php" class="sidebar-link">
            <img
              src="assets/images/icons/home.svg"
              class="icon"
              alt="Dashboard"
            />
            <p>Dashboard</p>
          </a>
          <a href="./courses.php" class="sidebar-link">
            <img src="assets/images/icons/Course.svg" alt="Dashboard" />
            <p>Courses</p>
          </a>
          <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
          <a href="./add_course.php" class="sidebar-link">
            <img src="assets/images/icons/add.svg" alt="Add Course" />
            <p>Add Course</p>
          </a>
          <?php endif; ?>
          

          <a href="./settings.php" class="sidebar-link">
            <img src="assets/images/icons/setting.svg" alt="Dashboard" />
            <p>Settings</p>
          </a>
        </div>

        <div class="premium">
          <h1>Go Premium</h1>
          <p>Explore 100+ expert created courses prepared for you.</p>
          <button>Get Access</button>
        </div>
        
      </div>
      <div class="main">
        <div class="main-header">
          <div class="main-header-left">
            <h2>Hi <?php echo htmlspecialchars($user['name']); ?>, Good <?php echo date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening'); ?>!</h2>
            <p>Lets learn something new today</p>
          </div>
          <form class="search-bar" id="searchForm" action="search.php">
            <input
              type="text"
              placeholder="Search"
              class="search-input"
              name="q"
            />
            <img
              src="assets/images/icons/search.svg"
              alt="Search"
              class="search-icon"
              id="searchIcon"
            />
          </form>
        </div>

        <div class="main-content">
          <div class="main-content-premium">
            <div>
              <h1 class="goPremium-title">Go Premium</h1>
              <p>Explore 100+ expert created courses prepared for you.</p>
              <button>Get Access</button>
            </div>
            <div>
              <img src="assets/images/courseimage.png" alt="Premium" />
            </div>
          </div>

          <div>
            <div class="main-content-title-container">
              <h3 class="main-content-title">Overview</h3>
            </div>
            <div class="main-content-overview">
              <div>
                <p>Course in progress</p>
                <h2><?php echo $in_progress; ?></h2>
              </div>
              <div>
                <p>Course completed</p>
                <h2><?php echo $completed; ?></h2>
              </div>
              <div>
                <p>Chats & Discussions</p>
                <h2>09</h2>
              </div>
            </div>
          </div>

          <div>
            <div class="main-content-title-container">
              <h3 class="main-content-title">Conitnue Reading</h3>
              <a href="#">View All</a>
            </div>
            <div class="main-content-courses">
              <?php while($course = mysqli_fetch_assoc($recent_courses_result)): ?>
              <div class="main-content-courses-card">
                <div>
                  <img src="assets/images/course1.png" alt="<?php echo htmlspecialchars($course['title']); ?>" />
                </div>
                <div class="main-content-courses-card-info">
                  <div class="course-category">
                    <p>Web Development</p>
                  </div>
                  <div class="course-title">
                    <p><?php echo htmlspecialchars($course['title']); ?></p>
                  </div>
                  <div class="course-author">
                    <p><?php echo htmlspecialchars($course['instructor_name']); ?></p>
                    <div class="circle"></div>
                    <p>5hrs</p>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="right">
        <div class="right-header">
          <a href="./logout.php">
            <div class="right-header-notification">
              <img
                src="assets/images/icons/logout.svg"
                alt="Notification"
              />
            </div>
            
          </a>
          <div href="./profile.php" class="right-header-user">
            <a href="./profile.php">
              <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="User" />
              <p><?php echo htmlspecialchars($user['name']); ?></p>
            </a>
          </div>
        </div>

        <div class="right-reminders">
          <h3 class="main-content-title">Reminders</h3>
          <div class="right-reminders-container">
            <div class="right-reminders-card">
              <div>
                <h3>Week 01 Assignment</h3>
                <p>Js Assignment</p>
              </div>
              <div class="right-reminders-card-time">
                <h4>7:00 PM</h4>
              </div>
            </div>

            <div class="right-reminders-card">
              <div>
                <h3>Week 01 Assignment</h3>
                <p>Js Assignment</p>
              </div>
              <div class="right-reminders-card-time">
                <h4>7:00 PM</h4>
              </div>
            </div>

            <div class="right-reminders-card">
              <div>
                <h3>Week 01 Assignment</h3>
                <p>Js Assignment</p>
              </div>
              <div class="right-reminders-card-time">
                <h4>7:00 PM</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="right-reminders">
          <h3 class="main-content-title">Events</h3>
          <div class="right-events-container">
            <div class="right-events-card">
              <div>
                <div>
                  <h3>
                    Online TuniLearn Hackathon: Health Platform Development
                  </h3>
                  <p>Hosted by TuniLearn & ESB</p>
                </div>
                <div>
                  <h2>Meeting Attending</h2>
                  <div class="right-events-team">
                    <img
                      src="assets/images/team/t1.png"
                      alt="Meeting"
                      class="t1"
                    />
                    <img
                      src="assets/images/team/t2.png"
                      alt="Meeting"
                      class="t2"
                    />
                    <img
                      src="assets/images/team/t3.png"
                      alt="Meeting"
                      class="t3"
                    />
                    <img
                      src="assets/images/team/t1.png"
                      alt="Meeting"
                      class="t4"
                    />
                    <img
                      src="assets/images/team/t2.png"
                      alt="Meeting"
                      class="t5"
                    />
                    <img
                      src="assets/images/team/t3.png"
                      alt="Meeting"
                      class="t6"
                    />
                  </div>
                </div>
                <div class="right-events-card-time">
                  <p>Dec 12th, 2024 - Dec 13th, 2024</p>
                  <div class="right-events-card-time-line"></div>
                  <p>13h - 13h</p>
                </div>
              </div>
            </div>
            <div class="right-events-card">
              <div>
                <div>
                  <h3>
                    Online TuniLearn Hackathon: Health Platform Development
                  </h3>
                  <p>Hosted by TuniLearn & ESB</p>
                </div>
                <div>
                  <h2>Meeting Attending</h2>
                  <div class="right-events-team">
                    <img
                      src="assets/images/team/t1.png"
                      alt="Meeting"
                      class="t1"
                    />
                    <img
                      src="assets/images/team/t2.png"
                      alt="Meeting"
                      class="t2"
                    />
                    <img
                      src="assets/images/team/t3.png"
                      alt="Meeting"
                      class="t3"
                    />
                    <img
                      src="assets/images/team/t1.png"
                      alt="Meeting"
                      class="t4"
                    />
                    <img
                      src="assets/images/team/t2.png"
                      alt="Meeting"
                      class="t5"
                    />
                    <img
                      src="assets/images/team/t3.png"
                      alt="Meeting"
                      class="t6"
                    />
                  </div>
                </div>
                <div class="right-events-card-time">
                  <p>Dec 12th, 2024 - Dec 13th, 2024</p>
                  <div class="right-events-card-time-line"></div>
                  <p>13h - 13h</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const searchForm = document.getElementById("searchForm");
        const searchIcon = document.getElementById("searchIcon");

        // Handle form submission (Enter key)
        searchForm.addEventListener("submit", (e) => {
          e.preventDefault();
          const searchInput = searchForm.querySelector(".search-input");
          if (searchInput.value.trim()) {
            window.location.href = `search.php?q=${encodeURIComponent(
              searchInput.value.trim()
            )}`;
          }
        });

        // Handle search icon click
        searchIcon.addEventListener("click", () => {
          const searchInput = searchForm.querySelector(".search-input");
          if (searchInput.value.trim()) {
            window.location.href = `search.php?q=${encodeURIComponent(
              searchInput.value.trim()
            )}`;
          }
        });
      });
    </script>
  </body>
</html>
