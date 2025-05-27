<?php
session_start();
require_once 'config/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $connexion->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch chat history
$chat_history = [];
$stmt = $connexion->prepare("SELECT user_message, ai_response, created_at FROM ai_chat_logs WHERE user_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $chat_history[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Educational Assistant</title>
    <link rel="stylesheet" href="./assets/css/global.css">
    <link rel="stylesheet" href="./assets/css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="./chat.php" class="nav-link active">
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

    <main class="chat-page">
        <div class="chat-wrapper">
            <div class="chat-header">
                <h2>AI Educational Assistant</h2>
                <p>Ask me anything about education, learning, or academic topics!</p>
            </div>
            <div class="chat-container">
                <div class="chat-messages" id="chatMessages">
                    <?php if (empty($chat_history)): ?>
                    <div class="message ai-message" style="white-space: normal;">
                        <p>Hello! I'm your educational AI assistant. I can help you with:</p>
                        <ul>
                            <li>Explaining complex concepts</li>
                            <li>Providing study tips</li>
                            <li>Answering academic questions</li>
                            <li>Helping with research</li>
                        </ul>
                        <p>How can I assist you today?</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($chat_history as $chat): ?>
                            <div class="message user-message"><?php echo htmlspecialchars($chat['user_message']); ?></div>
                            <div class="message ai-message"><?php echo htmlspecialchars($chat['ai_response']); ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="typing-indicator" id="typingIndicator">
                    AI is typing...
                </div>
                <div class="chat-input">
                    <form id="chatForm">
                        <input type="text" id="userInput" placeholder="Type your educational question here..." required>
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Scroll to bottom of chat on page load
        window.onload = function() {
            const messagesDiv = document.getElementById('chatMessages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        };

        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const userInput = document.getElementById('userInput');
            const message = userInput.value.trim();
            if (!message) return;

            // Add user message to chat
            addMessage(message, 'user');
            userInput.value = '';

            // Show typing indicator
            document.getElementById('typingIndicator').style.display = 'block';

            try {
                const response = await fetch('handlers/ai_chat_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                // Hide typing indicator
                document.getElementById('typingIndicator').style.display = 'none';

                if (data.error) {
                    addMessage('Sorry, there was an error processing your request. Please try again.', 'ai');
                } else {
                    addMessage(data.response, 'ai');
                }
            } catch (error) {
                document.getElementById('typingIndicator').style.display = 'none';
                addMessage('Sorry, there was an error connecting to the AI service. Please try again.', 'ai');
            }
        });

        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}-message`;
            
            if (sender === 'ai') {
                // Format the AI response
                let formattedText = text
                    // Convert bold text with **text** syntax
                    .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                    // Convert paragraphs
                    .replace(/\n\n/g, '</p><p>');

                // Add paragraph tags if not present
                if (!formattedText.startsWith('<')) {
                    formattedText = '<p>' + formattedText + '</p>';
                }

                messageDiv.innerHTML = formattedText;
            } else {
                messageDiv.textContent = text;
            }
            
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
    </script>
</body>
</html> 