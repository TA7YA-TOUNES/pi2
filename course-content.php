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
$user_query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($connexion, $user_query);
if ($stmt === false) {
    die("Error preparing user query: " . mysqli_error($connexion));
}
mysqli_stmt_bind_param($stmt, "i", $user_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing user query: " . mysqli_stmt_error($stmt));
}
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header('Location: index.php');
    exit();
}

// Get course ID from URL
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get course data
$course_query = "SELECT c.*, u.name as instructor_name 
                FROM courses c 
                JOIN users u ON c.instructor_id = u.user_id 
                WHERE c.course_id = ?";
$stmt = mysqli_prepare($connexion, $course_query);
if ($stmt === false) {
    die("Error preparing course query: " . mysqli_error($connexion));
}
mysqli_stmt_bind_param($stmt, "i", $course_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing course query: " . mysqli_stmt_error($stmt));
}
$course_result = mysqli_stmt_get_result($stmt);
$course = mysqli_fetch_assoc($course_result);

if (!$course) {
    header('Location: courses.php');
    exit();
}

// Get course sections with lessons
$sections_query = "SELECT cs.*, 
                  (SELECT COUNT(*) FROM course_lessons WHERE section_id = cs.section_id) as lesson_count
                  FROM course_sections cs 
                  WHERE cs.course_id = ? 
                  ORDER BY cs.order_index";
$stmt = mysqli_prepare($connexion, $sections_query);
if ($stmt === false) {
    die("Error preparing sections query: " . mysqli_error($connexion));
}
mysqli_stmt_bind_param($stmt, "i", $course_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing sections query: " . mysqli_stmt_error($stmt));
}
$sections_result = mysqli_stmt_get_result($stmt);

// Get current lesson ID from URL
$current_lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;

// Get current lesson data
$lesson_query = "SELECT cl.*, cs.title as section_title 
                FROM course_lessons cl 
                JOIN course_sections cs ON cl.section_id = cs.section_id 
                WHERE cl.lesson_id = ?";
$stmt = mysqli_prepare($connexion, $lesson_query);
if ($stmt === false) {
    die("Error preparing lesson query: " . mysqli_error($connexion));
}
mysqli_stmt_bind_param($stmt, "i", $current_lesson_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing lesson query: " . mysqli_stmt_error($stmt));
}
$lesson_result = mysqli_stmt_get_result($stmt);
$current_lesson = mysqli_fetch_assoc($lesson_result);

// If no lesson specified, get the first lesson
if (!$current_lesson) {
    $first_lesson_query = "SELECT cl.*, cs.title as section_title 
                          FROM course_lessons cl 
                          JOIN course_sections cs ON cl.section_id = cs.section_id 
                          WHERE cs.course_id = ? 
                          ORDER BY cs.order_index, cl.order_index 
                          LIMIT 1";
    $stmt = mysqli_prepare($connexion, $first_lesson_query);
    if ($stmt === false) {
        die("Error preparing first lesson query: " . mysqli_error($connexion));
    }
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing first lesson query: " . mysqli_stmt_error($stmt));
    }
    $first_lesson_result = mysqli_stmt_get_result($stmt);
    $current_lesson = mysqli_fetch_assoc($first_lesson_result);
    if ($current_lesson) {
        $current_lesson_id = $current_lesson['lesson_id'];
    }
}

// Get lesson progress
$progress_query = "SELECT lesson_id, is_completed 
                  FROM student_lesson_progress 
                  WHERE student_id = ? AND lesson_id IN (
                    SELECT lesson_id FROM course_lessons cl 
                    JOIN course_sections cs ON cl.section_id = cs.section_id 
                    WHERE cs.course_id = ?
                  )";
$stmt = mysqli_prepare($connexion, $progress_query);
if ($stmt === false) {
    die("Error preparing progress query: " . mysqli_error($connexion));
}
mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing progress query: " . mysqli_stmt_error($stmt));
}
$progress_result = mysqli_stmt_get_result($stmt);
$lesson_progress = [];
while ($progress = mysqli_fetch_assoc($progress_result)) {
    $lesson_progress[$progress['lesson_id']] = $progress['is_completed'];
}

// Handle mark as complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $lesson_id = (int)$_POST['lesson_id'];
    
    // Check if progress record exists
    $check_query = "SELECT * FROM student_lesson_progress 
                   WHERE student_id = ? AND lesson_id = ?";
    $stmt = mysqli_prepare($connexion, $check_query);
    if ($stmt === false) {
        die("Error preparing check query: " . mysqli_error($connexion));
    }
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $lesson_id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing check query: " . mysqli_stmt_error($stmt));
    }
    $check_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing record
        $update_query = "UPDATE student_lesson_progress 
                        SET is_completed = TRUE, completed_at = CURRENT_TIMESTAMP 
                        WHERE student_id = ? AND lesson_id = ?";
        $stmt = mysqli_prepare($connexion, $update_query);
        if ($stmt === false) {
            die("Error preparing update query: " . mysqli_error($connexion));
        }
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $lesson_id);
        if (!mysqli_stmt_execute($stmt)) {
            die("Error executing update query: " . mysqli_stmt_error($stmt));
        }
    } else {
        // Insert new record
        $insert_query = "INSERT INTO student_lesson_progress (student_id, lesson_id, is_completed, completed_at) 
                        VALUES (?, ?, TRUE, CURRENT_TIMESTAMP)";
        $stmt = mysqli_prepare($connexion, $insert_query);
        if ($stmt === false) {
            die("Error preparing insert query: " . mysqli_error($connexion));
        }
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $lesson_id);
        if (!mysqli_stmt_execute($stmt)) {
            die("Error executing insert query: " . mysqli_stmt_error($stmt));
        }
    }
    
    // Update course progress
    $total_lessons_query = "SELECT COUNT(*) as total FROM course_lessons cl 
                           JOIN course_sections cs ON cl.section_id = cs.section_id 
                           WHERE cs.course_id = ?";
    $stmt = mysqli_prepare($connexion, $total_lessons_query);
    if ($stmt === false) {
        die("Error preparing total lessons query: " . mysqli_error($connexion));
    }
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing total lessons query: " . mysqli_stmt_error($stmt));
    }
    $total_lessons_result = mysqli_stmt_get_result($stmt);
    $total_lessons = mysqli_fetch_assoc($total_lessons_result)['total'];
    
    $completed_lessons_query = "SELECT COUNT(*) as completed FROM student_lesson_progress slp 
                               JOIN course_lessons cl ON slp.lesson_id = cl.lesson_id 
                               JOIN course_sections cs ON cl.section_id = cs.section_id 
                               WHERE cs.course_id = ? AND slp.student_id = ? AND slp.is_completed = TRUE";
    $stmt = mysqli_prepare($connexion, $completed_lessons_query);
    if ($stmt === false) {
        die("Error preparing completed lessons query: " . mysqli_error($connexion));
    }
    mysqli_stmt_bind_param($stmt, "ii", $course_id, $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing completed lessons query: " . mysqli_stmt_error($stmt));
    }
    $completed_lessons_result = mysqli_stmt_get_result($stmt);
    $completed_lessons = mysqli_fetch_assoc($completed_lessons_result)['completed'];
    
    $progress_percentage = ($completed_lessons / $total_lessons) * 100;
    
    $update_progress_query = "UPDATE course_enrollments 
                             SET progress = ? 
                             WHERE student_id = ? AND course_id = ?";
    $stmt = mysqli_prepare($connexion, $update_progress_query);
    if ($stmt === false) {
        die("Error preparing progress update query: " . mysqli_error($connexion));
    }
    mysqli_stmt_bind_param($stmt, "dii", $progress_percentage, $user_id, $course_id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing progress update query: " . mysqli_stmt_error($stmt));
    }
    
    // Redirect to refresh the page
    header("Location: course-content.php?id=$course_id&lesson=$lesson_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($course['title']); ?> - TuniLearn</title>
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
          <img src="<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'assets/images/user.png'; ?>" alt="Profile" class="profile-pic" />
        </a>
        <a href="./logout.php" class="logout-btn">
          <img src="assets/images/icons/logout.svg" alt="Logout" class="nav-icon" />
        </a>
      </div>
    </nav>

    <div class="course-layout">
      <aside class="course-sidebar">
        <h2>Course Content</h2>
        <div class="course-sections">
          <?php while($section = mysqli_fetch_assoc($sections_result)): ?>
            <div class="section">
              <details <?php echo $current_lesson && $current_lesson['section_id'] == $section['section_id'] ? 'open' : ''; ?>>
                <summary><?php echo htmlspecialchars($section['title']); ?> (<?php echo $section['lesson_count']; ?> lessons)</summary>
                <?php
                $lessons_query = "SELECT * FROM course_lessons 
                                WHERE section_id = {$section['section_id']} 
                                ORDER BY order_index";
                $lessons_result = mysqli_query($connexion, $lessons_query);
                ?>
                <ul>
                  <?php while($lesson = mysqli_fetch_assoc($lessons_result)): ?>
                    <li class="<?php echo $current_lesson_id == $lesson['lesson_id'] ? 'active' : ''; ?>">
                      <img src="assets/images/icons/<?php echo isset($lesson_progress[$lesson['lesson_id']]) && $lesson_progress[$lesson['lesson_id']] ? 'check-circle.svg' : 'play-circle.svg'; ?>" alt="Lesson" />
                      <a href="?id=<?php echo $course_id; ?>&lesson=<?php echo $lesson['lesson_id']; ?>">
                        <?php echo htmlspecialchars($lesson['title']); ?>
                      </a>
                    </li>
                  <?php endwhile; ?>
                </ul>
              </details>
            </div>
          <?php endwhile; ?>
        </div>
      </aside>

      <main class="content">
        <div class="content-header">
          <div class="navigation">
            <a href="./courses.php" class="back-btn">
              <img src="assets/images/icons/arrow.svg" alt="Back" />
              <?php echo htmlspecialchars($course['title']); ?>
            </a>
            <div class="nav-controls">
              <?php
              // Get previous and next lesson IDs
              $prev_next_query = "SELECT cl.lesson_id, cl.title 
                                FROM course_lessons cl 
                                JOIN course_sections cs ON cl.section_id = cs.section_id 
                                WHERE cs.course_id = ? 
                                ORDER BY cs.order_index, cl.order_index";
              $stmt = mysqli_prepare($connexion, $prev_next_query);
              if ($stmt === false) {
                  die("Error preparing prev/next query: " . mysqli_error($connexion));
              }
              mysqli_stmt_bind_param($stmt, "i", $course_id);
              if (!mysqli_stmt_execute($stmt)) {
                  die("Error executing prev/next query: " . mysqli_stmt_error($stmt));
              }
              $prev_next_result = mysqli_stmt_get_result($stmt);
              $lessons = [];
              while ($row = mysqli_fetch_assoc($prev_next_result)) {
                  $lessons[] = $row;
              }
              
              $current_index = -1;
              foreach ($lessons as $index => $lesson) {
                  if ($lesson['lesson_id'] == $current_lesson_id) {
                      $current_index = $index;
                      break;
                  }
              }
              
              $prev_lesson = ($current_index > 0) ? $lessons[$current_index - 1] : null;
              $next_lesson = ($current_index < count($lessons) - 1 && $current_index !== -1) ? $lessons[$current_index + 1] : null;
              ?>
              
              <?php if ($prev_lesson): ?>
                <a href="?id=<?php echo $course_id; ?>&lesson=<?php echo $prev_lesson['lesson_id']; ?>" class="prev">Prev</a>
              <?php endif; ?>
              
              <?php if ($next_lesson): ?>
                <a href="?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['lesson_id']; ?>" class="next">Next</a>
              <?php endif; ?>
              
              <?php if ($current_lesson && (!isset($lesson_progress[$current_lesson_id]) || !$lesson_progress[$current_lesson_id])): ?>
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="lesson_id" value="<?php echo $current_lesson_id; ?>">
                  <button type="submit" name="mark_complete" class="complete">Mark as Complete</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
          <div class="user-profile">
            <img src="<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'assets/images/user.png'; ?>" alt="Profile" class="profile-pic" />
            <span><?php echo htmlspecialchars($user['name']); ?></span>
          </div>
        </div>

        <?php if ($current_lesson): ?>
          <div class="video-container">
            <?php if ($current_lesson['video_url']): ?>
              <video controls>
                <source src="<?php echo htmlspecialchars($current_lesson['video_url']); ?>" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            <?php else: ?>
              <img
                src="<?php echo !empty($course['course_image']) ? htmlspecialchars($course['course_image']) : 'assets/images/course-image-full.png'; ?>"
                alt="Course Image"
                class="course-image"
              />
            <?php endif; ?>
          </div>

          <div class="lesson-content">
            <h1><?php echo htmlspecialchars($current_lesson['title']); ?></h1>
            <div class="lesson-meta">
              <span>Section: <?php echo htmlspecialchars($current_lesson['section_title']); ?></span>
              <?php if ($current_lesson['duration']): ?>
                <span>Duration: <?php echo $current_lesson['duration']; ?> minutes</span>
              <?php endif; ?>
            </div>
            <div class="lesson-text">
              <?php echo nl2br(htmlspecialchars($current_lesson['content'])); ?>
            </div>
          </div>
        <?php else: ?>
          <div class="no-content">
            <h1>No Content Available</h1>
            <p>This course doesn't have any lessons yet.</p>
          </div>
        <?php endif; ?>
      </main>
    </div>
  </body>
</html>
