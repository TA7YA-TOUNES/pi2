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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio']) && isset($_POST['involvements']) && isset($_POST['specialisation'])) {
    $bio = mysqli_real_escape_string($connexion, $_POST['bio']);
    $involvements = mysqli_real_escape_string($connexion, $_POST['involvements']);
    $specialisation = mysqli_real_escape_string($connexion, $_POST['specialisation']);
    $skills = isset($_POST['skills']) ? implode(',', array_map('trim', explode(',', $_POST['skills']))) : '';
    
    $update_query = "UPDATE users SET bio = '$bio', involvements = '$involvements', specialisation = '$specialisation', skills = '$skills' WHERE user_id = $user_id";
    mysqli_query($connexion, $update_query);
    
    // Refresh user data
    $user_result = mysqli_query($connexion, $user_query);
    $user = mysqli_fetch_assoc($user_result);
}

// Get current skills
$current_skills = !empty($user['skills']) ? explode(',', $user['skills']) : [];

// Display success/error messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Settings - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/settings.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="nav-left">
        <h1 class="logo">
          <a href="./home.php" style="text-decoration: none; color: inherit;">TuniLearn.</a>
        </h1>
        <div class="nav-links">
          <a href="./home.php" class="nav-link">
            <img src="assets/images/icons/home.svg" alt="Home" /> Home
          </a>
          <a href="./courses.php" class="nav-link">
            <img src="assets/images/icons/Course.svg" alt="Courses" /> Courses
          </a>
          <a href="./chat.php" class="nav-link">
            <img src="assets/images/icons/mind.svg" alt="AI Chat" /> AI Assistant
          </a>
          <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
          <a href="./add_course.php" class="nav-link">
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
        <a href="./logout.php" class="logout-btn">
          <img src="assets/images/icons/logout.svg" alt="Logout" class="nav-icon" />
        </a>
      </div>
    </nav>

    <main class="settings-container">
      <div class="settings-header">
        <h1>Settings</h1>
        <p>Manage your account settings and preferences</p>
      </div>

      <div class="settings-content">
        <div class="settings-sidebar">
          <a href="#" class="active">Account</a>
          <!-- <a href="#">Notifications</a>
          <a href="#">Privacy</a>
          <a href="#">Security</a>
          <a href="#">Billing</a> -->
        </div>

        <div class="settings-main">
          <div class="settings-section">
            <h2>Account Settings</h2>
            <form class="settings-form" method="POST">
              <div class="form-group">
                <label>Profile Picture</label>
                <div class="profile-upload">
                  <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" />
                  <div class="upload-form">
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display: none;" />
                    <button type="button" class="upload-btn" onclick="document.getElementById('profile_image').click()">Change Photo</button>
                    <button type="button" class="save-btn" style="display: none;" id="save-image-btn" onclick="uploadProfileImage()">Save Photo</button>
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="fullname">Full Name</label>
                  <input type="text" id="fullname" value="<?php echo htmlspecialchars($user['name']); ?>" />
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <div class="email-input-container">
                    <svg class="lock-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
              </div>

              <div class="form-group">
                <label for="involvements">Involvements</label>
                <input id="involvements" name="involvements" value="<?php echo htmlspecialchars($user['involvements'] ?? ''); ?>"/>
              </div>

              <div class="form-group">
                <label for="specialisation">Specialisation</label>
                <input id="specialisation" name="specialisation" value="<?php echo htmlspecialchars($user['specialisation'] ?? ''); ?>"/>
              </div>

              <div class="form-group">
                <label for="skills">Skills (Max 5)</label>
                <div class="skills-container">
                  <div id="skills-tags">
                    <?php foreach($current_skills as $skill): ?>
                      <span class="skill-tag">
                        <?php echo htmlspecialchars($skill); ?>
                        <span class="remove-skill">&times;</span>
                      </span>
                    <?php endforeach; ?>
                  </div>
                  <input type="text" id="skills-input" placeholder="Type a skill and press Enter" maxlength="20">
                  <input type="hidden" name="skills" id="skills-hidden" value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>">
                  <p class="skills-help">Press Enter to add a skill. Click the × to remove a skill.</p>
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="save-btn">Save Changes</button>
                <button type="button" class="cancel-btn">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const skillsInput = document.getElementById('skills-input');
        const skillsTags = document.getElementById('skills-tags');
        const skillsHidden = document.getElementById('skills-hidden');
        const maxSkills = 5;

        function updateHiddenInput() {
          const skills = Array.from(skillsTags.getElementsByClassName('skill-tag'))
            .map(tag => tag.textContent.trim().replace('×', '').trim());
          skillsHidden.value = skills.join(',');
        }

        function addSkill(skill) {
          if (skillsTags.children.length >= maxSkills) {
            alert('You can only add up to 5 skills');
            return;
          }
          
          const skillTag = document.createElement('span');
          skillTag.className = 'skill-tag';
          skillTag.innerHTML = `${skill}<span class="remove-skill">&times;</span>`;
          
          skillTag.querySelector('.remove-skill').addEventListener('click', function() {
            skillTag.remove();
            updateHiddenInput();
          });
          
          skillsTags.appendChild(skillTag);
          updateHiddenInput();
        }

        skillsInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            const skill = this.value.trim();
            if (skill && !Array.from(skillsTags.children).some(tag => 
              tag.textContent.trim().replace('×', '').trim() === skill)) {
              addSkill(skill);
              this.value = '';
            }
          }
        });

        // Initialize existing skills
        document.querySelectorAll('.skill-tag .remove-skill').forEach(removeBtn => {
          removeBtn.addEventListener('click', function() {
            this.parentElement.remove();
            updateHiddenInput();
          });
        });
      });

      document.getElementById('profile_image').addEventListener('change', function() {
        if (this.files && this.files[0]) {
          document.getElementById('save-image-btn').style.display = 'inline-block';
        }
      });

      function uploadProfileImage() {
        const fileInput = document.getElementById('profile_image');
        if (fileInput.files && fileInput.files[0]) {
          const formData = new FormData();
          formData.append('profile_image', fileInput.files[0]);

          fetch('handlers/upload_profile_image.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.text())
          .then(() => {
            window.location.reload();
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error uploading image. Please try again.');
          });
        }
      }
    </script>
  </body>
</html>
