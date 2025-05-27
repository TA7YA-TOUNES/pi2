-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'instructor', 'student') NOT NULL DEFAULT 'student',
    bio TEXT DEFAULT NULL,
    involvements VARCHAR(255) DEFAULT NULL,
    specialisation VARCHAR(255) DEFAULT NULL,
    skills VARCHAR(255) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT 'assets/images/user.png'
);



-- Courses Table
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id INT,
    category VARCHAR(100),
    level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration INT, -- in hours
    course_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(user_id)
);

-- Course Content Table
CREATE TABLE content (
    content_id INT PRIMARY KEY,
    course_id INT,
    content_type VARCHAR(50),
    content_data TEXT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Quizzes Table
CREATE TABLE quizzes (
    quiz_id INT PRIMARY KEY,
    course_id INT,
    pass_mark FLOAT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Quiz Questions Table (optional, useful if you store questions separately)
CREATE TABLE quiz_questions (
    question_id INT PRIMARY KEY,
    quiz_id INT,
    question TEXT,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id)
);

-- Student Enrollments Table
CREATE TABLE student_enrollments (
    student_id INT,
    course_id INT,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Student Completed Quizzes Table
CREATE TABLE student_completed_quizzes (
    student_id INT,
    quiz_id INT,
    score FLOAT,
    PRIMARY KEY (student_id, quiz_id),
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id)
);

-- Certificates Table
CREATE TABLE certificates (
    certificate_id INT PRIMARY KEY,
    student_id INT,
    course_id INT,
    issue_date DATE,
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Course Enrollments Table
CREATE TABLE course_enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('in_progress', 'completed', 'dropped') DEFAULT 'in_progress',
    progress INT DEFAULT 0, -- percentage of completion
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Course Sections Table
CREATE TABLE IF NOT EXISTS course_sections (
    section_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    title VARCHAR(255) NOT NULL,
    order_index INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Course Lessons Table
CREATE TABLE IF NOT EXISTS course_lessons (
    lesson_id INT PRIMARY KEY AUTO_INCREMENT,
    section_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    video_url VARCHAR(255),
    order_index INT NOT NULL,
    duration INT, -- in minutes
    is_completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (section_id) REFERENCES course_sections(section_id) ON DELETE CASCADE
);

-- Student Lesson Progress Table
CREATE TABLE IF NOT EXISTS student_lesson_progress (
    student_id INT,
    lesson_id INT,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    PRIMARY KEY (student_id, lesson_id),
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES course_lessons(lesson_id) ON DELETE CASCADE
);

-- AI Chat Logs Table
CREATE TABLE IF NOT EXISTS ai_chat_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_message TEXT NOT NULL,
    ai_response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
); 