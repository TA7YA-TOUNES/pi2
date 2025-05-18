<?php
session_start();
require_once '../config/connection.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_image'];
    
    // Debug information
    error_log("File upload attempt - User ID: $user_id");
    error_log("File details: " . print_r($file, true));
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        error_log("Invalid file type: " . $file['type']);
        $_SESSION['error'] = "Only JPG, PNG and GIF files are allowed.";
        header("Location: ../settings.php");
        exit();
    }
    
    if ($file['size'] > $max_size) {
        error_log("File too large: " . $file['size']);
        $_SESSION['error'] = "File size must be less than 5MB.";
        header("Location: ../settings.php");
        exit();
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/profile_images/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            error_log("Failed to create upload directory: $upload_dir");
            $_SESSION['error'] = "Error creating upload directory.";
            header("Location: ../settings.php");
            exit();
        }
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Update database with new image path
        $image_path = 'uploads/profile_images/' . $filename;
        $update_query = "UPDATE users SET profile_image = '$image_path' WHERE user_id = $user_id";
        
        if (mysqli_query($connexion, $update_query)) {
            error_log("Profile image updated successfully for user $user_id");
            $_SESSION['success'] = "Profile image updated successfully!";
        } else {
            error_log("Database error: " . mysqli_error($connexion));
            $_SESSION['error'] = "Error updating profile image in database.";
        }
    } else {
        error_log("Failed to move uploaded file to: $target_path");
        error_log("Upload error: " . print_r(error_get_last(), true));
        $_SESSION['error'] = "Error uploading file. Please try again.";
    }
    
    header("Location: ../settings.php");
    exit();
} else {
    error_log("Invalid request method or missing file");
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../settings.php");
    exit();
}
?> 