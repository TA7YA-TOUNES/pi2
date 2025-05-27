# Educational Platform with AI Chat Integration

A comprehensive educational platform that combines course management with an AI-powered chat assistant for enhanced learning experiences.

## Features

- User Authentication System
- Course Management
  - Add and manage courses
  - Upload course content
  - Search and browse courses
- AI Chat Integration
  - Educational AI assistant
  - Context-aware responses
  - Conversation logging
- User Profiles
  - Profile management
  - Settings customization
- File Management
  - Profile image uploads
  - Course content uploads
- Contact System

## Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Apache/Nginx web server
- OpenAI API key

## Installation

1. Clone the repository:

```bash
git clone [your-repository-url]
```

2. Set up your database:

   - Create a new MySQL database
   - Import the `schema.sql` file to set up the database structure

3. Configure the application:

   - Copy `config.example.php` to `config.php`
   - Update the configuration with your settings:
     - Add your OpenAI API key
     - Configure database connection details

4. Set up the web server:
   - Point your web server to the project directory
   - Ensure the `uploads` directory is writable
   - Configure URL rewriting if needed

## Directory Structure

```
├── assets/           # Static assets (CSS, JS, images)
├── config/           # Configuration files
├── handlers/         # Request handlers
├── includes/         # PHP includes and utilities
├── uploads/          # User uploads
│   ├── profile_images/
│   └── courses/
├── config.php        # Main configuration (not tracked in git)
├── config.example.php # Configuration template
└── [other PHP files] # Main application files
```

## Configuration

### API Keys

The application uses API keys for various services. These are stored in `config.php`:

```php
return [
    'api_keys' => [
        'openai' => 'your_openai_api_key_here'
    ]
];
```

**Important**: Never commit `config.php` to version control. Use `config.example.php` as a template.

## Security

- API keys are stored in a separate configuration file
- User authentication is required for sensitive operations
- File uploads are restricted to specific directories
- Input validation and sanitization are implemented
