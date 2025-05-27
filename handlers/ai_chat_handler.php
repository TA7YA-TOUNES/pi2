<?php
session_start();
require_once '../config/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit();
}

// OpenAI API configuration
$api_key = ''; // Replace with your actual API key
$api_url = 'https://api.openai.com/v1/chat/completions';

// Prepare the system message to focus on educational content
$system_message = "You are an educational AI assistant. You should only provide information and answers related to education, learning, and academic topics. If asked about non-educational topics, politely redirect the conversation back to educational matters. Your responses should be informative, accurate, and helpful for learning purposes.";

// Prepare the API request
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

// Prepare the messages array
$messages = [
    ['role' => 'system', 'content' => $system_message],
    ['role' => 'user', 'content' => $message]
];

// Set the request body
$request_body = [
    'model' => 'gpt-4-1106-preview', // Using GPT-4.1 nano
    'messages' => $messages,
    'temperature' => 0.7,
    'max_tokens' => 500
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));

// Execute the request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $response_data = json_decode($response, true);
    $ai_response = $response_data['choices'][0]['message']['content'];
    
    // Log the conversation in the database
    $user_id = $_SESSION['user_id'];
    $stmt = $connexion->prepare("INSERT INTO ai_chat_logs (user_id, user_message, ai_response) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $message, $ai_response);
    $stmt->execute();
    
    echo json_encode(['response' => $ai_response]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error communicating with AI service']);
} 