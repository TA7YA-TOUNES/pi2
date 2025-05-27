<?php
session_start();
require_once 'config/connection.php';

// Require login and check role
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($connexion, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Check if user is instructor or admin
if ($user['role'] !== 'instructor' && $user['role'] !== 'admin') {
    header('Location: home.php');
    exit();
}

// Get course ID from URL
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get course data
$course_query = "SELECT * FROM courses WHERE course_id = ? AND instructor_id = ?";
$stmt = mysqli_prepare($connexion, $course_query);
mysqli_stmt_bind_param($stmt, "ii", $course_id, $user_id);
mysqli_stmt_execute($stmt);
$course_result = mysqli_stmt_get_result($stmt);
$course = mysqli_fetch_assoc($course_result);

if (!$course) {
    header('Location: courses.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_section'])) {
        $section_title = mysqli_real_escape_string($connexion, $_POST['section_title']);
        
        // Get the next order index
        $order_query = "SELECT MAX(order_index) as max_order FROM course_sections WHERE course_id = ?";
        $stmt = mysqli_prepare($connexion, $order_query);
        mysqli_stmt_bind_param($stmt, "i", $course_id);
        mysqli_stmt_execute($stmt);
        $order_result = mysqli_stmt_get_result($stmt);
        $max_order = mysqli_fetch_assoc($order_result)['max_order'];
        $next_order = ($max_order === null) ? 1 : $max_order + 1;
        
        // Insert new section
        $insert_section = "INSERT INTO course_sections (course_id, title, order_index) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connexion, $insert_section);
        mysqli_stmt_bind_param($stmt, "isi", $course_id, $section_title, $next_order);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Section added successfully!";
        } else {
            $error_message = "Error adding section. Please try again.";
        }
    }
    
    if (isset($_POST['add_lesson'])) {
        $section_id = (int)$_POST['section_id'];
        $lesson_title = mysqli_real_escape_string($connexion, $_POST['lesson_title']);
        $lesson_content = mysqli_real_escape_string($connexion, $_POST['lesson_content']);
        $lesson_duration = (int)$_POST['lesson_duration'];
        
        // Get the next order index
        $order_query = "SELECT MAX(order_index) as max_order FROM course_lessons WHERE section_id = ?";
        $stmt = mysqli_prepare($connexion, $order_query);
        mysqli_stmt_bind_param($stmt, "i", $section_id);
        mysqli_stmt_execute($stmt);
        $order_result = mysqli_stmt_get_result($stmt);
        $max_order = mysqli_fetch_assoc($order_result)['max_order'];
        $next_order = ($max_order === null) ? 1 : $max_order + 1;
        
        // Handle video upload
        $video_url = '';
        if (isset($_FILES['lesson_video']) && $_FILES['lesson_video']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/videos/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_extension = strtolower(pathinfo($_FILES['lesson_video']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            // Check if file is a video
            $allowed_types = ['mp4', 'webm', 'ogg'];
            if (in_array($file_extension, $allowed_types)) {
                if (move_uploaded_file($_FILES['lesson_video']['tmp_name'], $upload_path)) {
                    $video_url = $upload_path;
                } else {
                    $error_message = "Error uploading video. Please try again.";
                }
            } else {
                $error_message = "Invalid file type. Please upload an MP4, WebM, or OGG video.";
            }
        }
        
        if (!isset($error_message)) {
            // Insert new lesson
            $insert_lesson = "INSERT INTO course_lessons (section_id, title, content, video_url, order_index, duration) 
                             VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connexion, $insert_lesson);
            mysqli_stmt_bind_param($stmt, "isssii", $section_id, $lesson_title, $lesson_content, $video_url, $next_order, $lesson_duration);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Lesson added successfully!";
            } else {
                $error_message = "Error adding lesson. Please try again.";
            }
        }
    }
}

// Get course sections
$sections_query = "SELECT * FROM course_sections WHERE course_id = ? ORDER BY order_index";
$stmt = mysqli_prepare($connexion, $sections_query);
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$sections_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course Content - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css">
    <link rel="stylesheet" href="./assets/css/add_course_content.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <h1 class="logo">TuniLearn.</h1>
            <div class="nav-links">
                <a href="./home.php" class="nav-link">
                    <img src="assets/images/icons/home.svg" alt="Home"> Home
                </a>
                <a href="./courses.php" class="nav-link">
                    <img src="assets/images/icons/Course.svg" alt="Courses"> Courses
                </a>
                <a href="./chat.php" class="nav-link">
            <img src="assets/images/icons/mind.svg" alt="AI Chat" /> AI Assistant
          </a>
          <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
          <a href="./add_course.php" class="nav-link active">
            <img src="assets/images/icons/add.svg" alt="Add Course" /> Add Course
          </a>
          <?php endif; ?>
            </div>
        </div>
        <div class="nav-right">
            <a href="./contact.php">Contact us</a>
            <a href="./search.php">
                <img src="assets/images/icons/search.svg" alt="Search" class="nav-icon">
            </a>
            <a href="./profile.php">
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-pic">
            </a>
            <a href="./logout.php" class="logout-btn">
                <img src="assets/images/icons/logout.svg" alt="Logout" class="nav-icon">
            </a>
        </div>
    </nav>

    <main class="content-container">
        <div class="content-header">
            <h1>Add Content to: <?php echo htmlspecialchars($course['title']); ?></h1>
            <p>Create sections and lessons for your course</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="content-forms">
            <!-- Add Section Form -->
            <div class="form-section">
                <h2>Add New Section</h2>
                <form method="POST" class="add-section-form">
                    <div class="form-group">
                        <label for="section_title">Section Title</label>
                        <input type="text" id="section_title" name="section_title" required 
                               placeholder="Enter section title">
                    </div>
                    <button type="submit" name="add_section" class="submit-btn">Add Section</button>
                </form>
            </div>

            <!-- Add Lesson Form -->
            <div class="form-section">
                <h2>Add New Lesson</h2>
                <form method="POST" class="add-lesson-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="section_id">Select Section</label>
                        <select id="section_id" name="section_id" required>
                            <option value="">Select a section</option>
                            <?php while ($section = mysqli_fetch_assoc($sections_result)): ?>
                                <option value="<?php echo $section['section_id']; ?>">
                                    <?php echo htmlspecialchars($section['title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lesson_title">Lesson Title</label>
                        <input type="text" id="lesson_title" name="lesson_title" required 
                               placeholder="Enter lesson title">
                    </div>

                    <div class="form-group">
                        <label for="lesson_content">Lesson Content</label>
                        <textarea id="lesson_content" name="lesson_content" rows="6" required 
                                  placeholder="Enter lesson content"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="lesson_duration">Duration (minutes)</label>
                        <input type="number" id="lesson_duration" name="lesson_duration" 
                               min="1" required placeholder="Enter lesson duration">
                    </div>

                    <div class="form-group">
                        <label for="lesson_video">Lesson Video (optional)</label>
                        <input type="file" id="lesson_video" name="lesson_video" accept="video/*">
                        <p class="help-text">Supported formats: MP4, WebM, OGG</p>
                    </div>

                    <button type="submit" name="add_lesson" class="submit-btn">Add Lesson</button>
                </form>
            </div>
        </div>

        <!-- Preview Sections and Lessons -->
        <div class="content-preview">
            <h2>Course Content Preview</h2>
            <?php
            // Reset the sections result pointer
            mysqli_data_seek($sections_result, 0);
            
            while ($section = mysqli_fetch_assoc($sections_result)):
                // Get lessons for this section
                $lessons_query = "SELECT * FROM course_lessons WHERE section_id = ? ORDER BY order_index";
                $stmt = mysqli_prepare($connexion, $lessons_query);
                mysqli_stmt_bind_param($stmt, "i", $section['section_id']);
                mysqli_stmt_execute($stmt);
                $lessons_result = mysqli_stmt_get_result($stmt);
            ?>
                <div class="preview-section">
                    <h3><?php echo htmlspecialchars($section['title']); ?></h3>
                    <ul class="preview-lessons">
                        <?php while ($lesson = mysqli_fetch_assoc($lessons_result)): ?>
                            <li>
                                <span class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></span>
                                <span class="lesson-duration"><?php echo $lesson['duration']; ?> minutes</span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html> 