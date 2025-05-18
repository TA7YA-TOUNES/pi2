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
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($connexion, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Check if user is instructor or admin
if ($user['role'] !== 'instructor' && $user['role'] !== 'admin') {
    header('Location: home.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($connexion, $_POST['title']);
    $description = mysqli_real_escape_string($connexion, $_POST['description']);
    $category = mysqli_real_escape_string($connexion, $_POST['category']);
    $level = mysqli_real_escape_string($connexion, $_POST['level']);
    $duration = (int)$_POST['duration'];
    
    $insert_query = "INSERT INTO courses (title, description, instructor_id, category, level, duration) 
                    VALUES ('$title', '$description', $user_id, '$category', '$level', $duration)";
    
    if (mysqli_query($connexion, $insert_query)) {
        $success_message = "Course added successfully!";
    } else {
        $error_message = "Error adding course. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Course - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/add_course.css" />
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

    <main class="add-course-container">
      <div class="add-course-header">
        <h1>Add New Course</h1>
        <p>Create a new course for your students</p>
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

      <form class="add-course-form" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Course Title</label>
          <input type="text" id="title" name="title" required placeholder="Enter course title">
        </div>

        <div class="form-group">
          <label for="description">Course Description</label>
          <textarea id="description" name="description" rows="4" required placeholder="Enter course description"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
              <option value="">Select a category</option>
              <option value="Web Development">Web Development</option>
              <option value="Mobile Development">Mobile Development</option>
              <option value="Data Science">Data Science</option>
              <option value="Design">Design</option>
            </select>
          </div>

          <div class="form-group">
            <label for="level">Level</label>
            <select id="level" name="level" required>
              <option value="">Select a level</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
            </select>
          </div>

          <div class="form-group">
            <label for="duration">Duration (hours)</label>
            <input type="number" id="duration" name="duration" min="1" required placeholder="Enter course duration">
          </div>
        </div>

        <div class="form-group">
          <label for="course-image">Course Image</label>
          <input type="file" id="course-image" name="course_image" accept="image/*">
          <p class="help-text">Recommended size: 800x450 pixels</p>
        </div>

        <div class="form-actions">
          <button type="submit" class="submit-btn">Create Course</button>
          <button type="button" class="cancel-btn" onclick="window.location.href='courses.php'">Cancel</button>
        </div>
      </form>
    </main>
  </body>
</html> 