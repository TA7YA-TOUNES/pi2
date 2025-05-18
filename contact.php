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
    <title>Contact Us - TuniLearn</title>
    <link rel="stylesheet" href="./assets/css/global.css" />
    <link rel="stylesheet" href="./assets/css/contact.css" />
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

    <main class="contact-container">
      <div class="contact-header">
        <h1>Get in Touch</h1>
        <p>
          We'd love to hear from you. Send us a message and we'll respond as
          soon as possible.
        </p>
      </div>

      <div class="contact-content">
        <div class="contact-info">
          <div class="info-card">
            <div class="icon-wrapper">
              <img src="assets/images/icons/home.svg" alt="Email" />
            </div>
            <h3>Email Us</h3>
            <p>support@tunilearn.com</p>
            <a href="mailto:support@tunilearn.com" class="contact-link"
              >Send email</a
            >
          </div>
          <div class="info-card">
            <div class="icon-wrapper">
              <img src="assets/images/icons/home.svg" alt="Phone" />
            </div>
            <h3>Call Us</h3>
            <p>+216 123 456 789</p>
            <a href="tel:+216123456789" class="contact-link">Make call</a>
          </div>
          <div class="info-card">
            <div class="icon-wrapper">
              <img src="assets/images/icons/home.svg" alt="Location" />
            </div>
            <h3>Visit Us</h3>
            <p>Tunis, Tunisia</p>
            <a href="#" class="contact-link">Get directions</a>
          </div>
        </div>

        <div class="contact-form">
          <h2>Send us a Message</h2>
          <form id="contactForm">
            <div class="form-row">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input
                  type="text"
                  id="name"
                  placeholder="Enter your full name"
                />
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Enter your email" />
              </div>
            </div>
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" placeholder="Enter subject" />
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea
                id="message"
                rows="5"
                placeholder="Enter your message"
              ></textarea>
            </div>
            <button type="submit" class="submit-btn">
              <span>Send Message</span>
            </button>
          </form>
        </div>
      </div>
    </main>

    <script src="assets/js/contact-validation.js"></script>
  </body>
</html>
