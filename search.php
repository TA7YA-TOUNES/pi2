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

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Search courses
$courses = [];
if (!empty($search_query)) {
    $search_sql = "SELECT c.*, u.name as instructor_name, 
                          (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.course_id) as enrolled_students
                   FROM courses c 
                   JOIN users u ON c.instructor_id = u.user_id 
                   WHERE c.title LIKE ? 
                   OR c.description LIKE ? 
                   OR c.category LIKE ?
                   ORDER BY enrolled_students DESC";
    
    $stmt = mysqli_prepare($connexion, $search_sql);
    if ($stmt) {
        $search_term = "%{$search_query}%";
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $courses[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
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
        <a href="./search.php">
          <img src="assets/images/icons/search.svg" alt="Search" class="nav-icon" />
        </a>
        <a href="./profile.php">
          <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-pic" />
        </a>
        <a href="./logout.php" class="logout-btn">
          <img src="assets/images/icons/logout.svg" alt="Logout" class="nav-icon" />
        </a>
      </div>
    </nav>

    <main class="search-container">
      <div class="search-header">
        <h1>Finding course made easy</h1>
        <p>Search. Explore. Learn</p>
        <form class="search-bar" action="search.php" method="GET">
          <img src="assets/images/icons/search.svg" alt="Search" class="search-icon" />
          <input 
            type="text" 
            name="q" 
            placeholder="Search courses..." 
            value="<?php echo htmlspecialchars($search_query); ?>"
          />
        </form>
      </div>

      <div class="search-results">
        <?php if (!empty($search_query)): ?>
          <p class="results-text">Search results for "<?php echo htmlspecialchars($search_query); ?>"</p>
          <div class="search-tabs">
            <a href="#" class="active">Courses (<?php echo count($courses); ?>)</a>
          </div>

          <div class="course-cards">
            <?php if (empty($courses)): ?>
              <div class="no-results">
                <p>No courses found matching your search.</p>
                <p>Try different keywords or browse our course catalog.</p>
              </div>
            <?php else: ?>
              <?php foreach ($courses as $course): ?>
                <div class="course-card">
                  <img
                    src="<?php echo htmlspecialchars($course['thumbnail'] ?? 'assets/images/course1.png'); ?>"
                    alt="<?php echo htmlspecialchars($course['title']); ?>"
                    class="course-avatar"
                  />
                  <div class="course-info">
                    <span class="course-tag"><?php echo htmlspecialchars($course['category']); ?></span>
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="course-author">
                      <?php echo htmlspecialchars($course['instructor_name']); ?>
                      <span class="dot"></span>
                      <?php echo htmlspecialchars($course['duration'] ?? 'N/A'); ?>
                    </p>
                  </div>
                  <a href="courses.php?id=<?php echo $course['course_id']; ?>" class="start-btn">Start Learning</a>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="no-search">
            <p>Enter a search term to find courses.</p>
          </div>
        <?php endif; ?>
      </div>
    </main>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.querySelector('.search-bar');
        const searchInput = searchForm.querySelector('input');
        
        // Auto-submit form when user stops typing
        let timeout = null;
        searchInput.addEventListener('input', function() {
          clearTimeout(timeout);
          timeout = setTimeout(() => {
            if (this.value.trim()) {
              searchForm.submit();
            }
          }, 500);
        });
      });
    </script>
  </body>
</html>
