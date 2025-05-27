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
          <a href="./chat.php" class="sidebar-link">
            <img src="assets/images/icons/mind.svg" alt="AI Chat" />
            <p>AI Assistant</p>
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
            <div class="quotes-section">
              <div class="quote-card">
                <p class="quote-text" id="quote1-text"></p>
                <p class="quote-author" id="quote1-author"></p>
              </div>
              <div class="quote-card">
                <p class="quote-text" id="quote2-text"></p>
                <p class="quote-author" id="quote2-author"></p>
              </div>
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
          <h3 class="main-content-title">Started Courses</h3>
          <div class="right-reminders-container">
            <?php
            // Get started courses
            $started_courses_query = "SELECT c.*, u.name as instructor_name 
                                    FROM courses c 
                                    JOIN users u ON c.instructor_id = u.user_id 
                                    JOIN course_enrollments ce ON c.course_id = ce.course_id 
                                    WHERE ce.student_id = ? 
                                    ORDER BY ce.enrolled_at DESC LIMIT 3";
            
            // Prepare and execute the query with proper error handling
            $stmt = mysqli_prepare($connexion, $started_courses_query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $started_courses_result = mysqli_stmt_get_result($stmt);
                    
                    if ($started_courses_result && mysqli_num_rows($started_courses_result) > 0) {
                        while($course = mysqli_fetch_assoc($started_courses_result)): ?>
                        <div class="right-reminders-card">
                            <div>
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p><?php echo htmlspecialchars($course['instructor_name']); ?></p>
                            </div>
                            <div class="right-reminders-card-time">
                                <a href="courses.php?id=<?php echo $course['course_id']; ?>" class="continue-btn">Continue</a>
                            </div>
                        </div>
                        <?php endwhile;
                    } else {
                        echo '<div class="right-reminders-card"><div><p>No courses started yet</p></div></div>';
                    }
                } else {
                    echo '<div class="right-reminders-card"><div><p>Error executing query: ' . mysqli_stmt_error($stmt) . '</p></div></div>';
                }
                mysqli_stmt_close($stmt);
            } else {
                echo '<div class="right-reminders-card"><div><p>Error preparing statement: ' . mysqli_error($connexion) . '</p></div></div>';
            }
            ?>
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
    <script src="./assets/js/quotes.js"></script>
  </body>
</html>
