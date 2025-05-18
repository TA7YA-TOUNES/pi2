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

// Handle course enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course'])) {
    $course_id = (int)$_POST['course_id'];
    
    // Check if already enrolled
    $check_query = "SELECT * FROM course_enrollments WHERE student_id = $user_id AND course_id = $course_id";
    $check_result = mysqli_query($connexion, $check_query);
    
    if (mysqli_num_rows($check_result) === 0) {
        $enroll_query = "INSERT INTO course_enrollments (student_id, course_id) VALUES ($user_id, $course_id)";
        mysqli_query($connexion, $enroll_query);
    }
}

// Get all courses with instructor names
$courses_query = "SELECT c.*, u.name as instructor_name, 
                 (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.course_id) as enrolled_students,
                 (SELECT status FROM course_enrollments WHERE course_id = c.course_id AND student_id = $user_id) as enrollment_status
                 FROM courses c 
                 JOIN users u ON c.instructor_id = u.user_id 
                 ORDER BY c.created_at DESC";
$courses_result = mysqli_query($connexion, $courses_query);

// Get user's enrolled courses
$enrolled_query = "SELECT c.*, u.name as instructor_name, ce.status, ce.progress 
                  FROM course_enrollments ce 
                  JOIN courses c ON ce.course_id = c.course_id 
                  JOIN users u ON c.instructor_id = u.user_id 
                  WHERE ce.student_id = $user_id 
                  ORDER BY ce.enrolled_at DESC";
$enrolled_result = mysqli_query($connexion, $enrolled_query);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Courses - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/courses.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="nav-left">
        <h1 class="logo">TuniLearn.</h1>
        <div class="nav-links">
          <a href="./home.php" class="nav-link">
            <img src="assets/images/icons/home.svg" alt="Home" /> Home
          </a>
          <a href="./courses.php" class="nav-link active">
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

    <main class="courses-container">
      <div class="courses-header">
        <h1>Available Courses</h1>
        <div class="courses-filters">
          <select id="category-filter">
            <option value="">All Categories</option>
            <option value="Web Development">Web Development</option>
            <option value="Mobile Development">Mobile Development</option>
            <option value="Data Science">Data Science</option>
            <option value="Design">Design</option>
          </select>
          <select id="level-filter">
            <option value="">All Levels</option>
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
          </select>
        </div>
      </div>

      <div class="courses-grid">
        <?php while($course = mysqli_fetch_assoc($courses_result)): ?>
          <div class="course-card">
            <div class="course-image">
              <img src="assets/images/course1.png" alt="<?php echo htmlspecialchars($course['title']); ?>" />
              <span class="course-level"><?php echo ucfirst($course['level']); ?></span>
            </div>
            <div class="course-content">
              <div class="course-category"><?php echo htmlspecialchars($course['category']); ?></div>
              <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
              <p class="course-description"><?php echo htmlspecialchars($course['description']); ?></p>
              <div class="course-meta">
                <span class="instructor">By <?php echo htmlspecialchars($course['instructor_name']); ?></span>
                <span class="duration"><?php echo $course['duration']; ?> hours</span>
                <span class="students"><?php echo $course['enrolled_students']; ?> students</span>
              </div>
              <?php if ($course['enrollment_status']): ?>
                <div class="enrollment-status">
                  <?php if ($course['enrollment_status'] === 'in_progress'): ?>
                    <a href="course-content.php?id=<?php echo $course['course_id']; ?>" class="continue-btn">Continue Learning</a>
                  <?php elseif ($course['enrollment_status'] === 'completed'): ?>
                    <span class="completed-badge">Completed</span>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <form method="POST" class="enroll-form">
                  <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                  <button type="submit" name="enroll_course" class="enroll-btn">Enroll Now</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <div class="enrolled-courses">
        <h2>My Courses</h2>
        <div class="enrolled-courses-grid">
          <?php while($course = mysqli_fetch_assoc($enrolled_result)): ?>
            <div class="enrolled-course-card">
              <div class="course-image">
                <img src="assets/images/course1.png" alt="<?php echo htmlspecialchars($course['title']); ?>" />
                <div class="progress-bar">
                  <div class="progress" style="width: <?php echo $course['progress']; ?>%"></div>
                </div>
              </div>
              <div class="course-content">
                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                <p>Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                <div class="course-progress">
                  <span class="progress-text"><?php echo $course['progress']; ?>% Complete</span>
                  <a href="course-content.php?id=<?php echo $course['course_id']; ?>" class="continue-btn">Continue</a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </main>

    <script>
      // Filter functionality
      document.getElementById('category-filter').addEventListener('change', filterCourses);
      document.getElementById('level-filter').addEventListener('change', filterCourses);

      function filterCourses() {
        const category = document.getElementById('category-filter').value;
        const level = document.getElementById('level-filter').value;
        const courses = document.querySelectorAll('.course-card');

        courses.forEach(course => {
          const courseCategory = course.querySelector('.course-category').textContent;
          const courseLevel = course.querySelector('.course-level').textContent.toLowerCase();
          
          const categoryMatch = !category || courseCategory === category;
          const levelMatch = !level || courseLevel === level;

          course.style.display = categoryMatch && levelMatch ? 'block' : 'none';
        });
      }
    </script>
  </body>
</html> 