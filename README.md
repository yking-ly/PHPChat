# PHPChat - Real-Time Chat Application

A modern, real-time one-to-one chat application built with PHP, MySQL, and AJAX polling. Features a beautiful dark theme UI inspired by WhatsApp Web and Facebook Messenger.

![PHPChat](https://img.shields.io/badge/PHPChat-v1.0-6366f1) ![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4) ![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1)

## ✨ Features

### Authentication
- User registration with comprehensive validation
- Secure login with password hashing (bcrypt)
- Session management with security best practices
- Protected routes with automatic redirects
- CSRF protection on all forms

### Real-Time Chat
- One-to-one messaging between users
- AJAX-powered message sending (no page refresh)
- Real-time message receiving via polling (2-3 second intervals)
- Beautiful chat bubble design with sender/receiver distinction
- Auto-scroll to latest messages
- Visual feedback during message sending

### Online/Offline Status
- Real-time online/offline status tracking
- Green indicator for online users
- Heartbeat mechanism (30-second intervals)
- "Last seen" timestamps for offline users
- Automatic offline marking after 2 minutes of inactivity

### Message History
- Complete conversation history storage
- Pagination with "Load More" functionality
- Messages fetched in batches of 20
- Date separators for better organization
- Scroll position maintenance when loading older messages

### User Interface
- Modern dark theme with glassmorphism effects
- Responsive design (works on desktop and mobile)
- Sidebar with user list and unread message counts
- Smooth animations and micro-interactions
- Message status indicators
- Skeleton loading states

## 🚀 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx) or PHP's built-in server
- PDO PHP Extension

### Step 1: Clone or Download
```bash
cd c:\PHPChat
```

### Step 2: Create Database
1. Open MySQL command line or phpMyAdmin
2. Run the SQL script:
```sql
SOURCE c:\PHPChat\database\schema.sql;
```

Or manually run the contents of `database/schema.sql`.

### Step 3: Configure Database
Copy the example config and update with your credentials:
```bash
cp config/database.example.php config/database.php
```

Then edit `config/database.php` with your MySQL credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'php_chat');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Step 4: Start the Server
Using PHP's built-in server:
```bash
cd c:\PHPChat
php -S localhost:8000
```

Or configure your Apache/Nginx to point to the project directory.

### Step 5: Access the Application
Open your browser and navigate to:
- http://localhost:8000 (if using PHP's built-in server)
- Or your configured virtual host

## 📁 Project Structure

```
PHPChat/
├── api/
│   ├── auth/
│   │   ├── login.php         # User login API
│   │   ├── logout.php        # User logout API
│   │   └── register.php      # User registration API
│   ├── chat/
│   │   ├── get_messages.php  # Fetch messages API
│   │   ├── get_users.php     # Fetch users list API
│   │   └── send_message.php  # Send message API
│   └── status/
│       └── heartbeat.php     # Update online status API
├── assets/
│   └── css/
│       └── style.css         # Main stylesheet
├── config/
│   ├── database.php          # Database configuration
│   ├── security.php          # Security functions (CSRF, XSS, etc.)
│   └── session.php           # Session configuration
├── database/
│   └── schema.sql            # Database schema
├── chat.php                  # Main chat interface
├── index.php                 # Entry point (redirects)
├── login.php                 # Login page
├── register.php              # Registration page
└── README.md                 # This file
```

## 🔒 Security Features

- **SQL Injection Prevention**: All database queries use PDO prepared statements
- **XSS Protection**: All user inputs are sanitized with `htmlspecialchars()`
- **CSRF Protection**: Token-based protection on all forms
- **Password Security**: Passwords hashed with `password_hash()` (bcrypt)
- **Session Security**: 
  - HTTP-only cookies
  - Session regeneration on login
  - 30-minute timeout
- **Input Validation**: Server-side validation for all user inputs

## ⚙️ Configuration

### Polling Intervals
- Message polling: 2-3 seconds (adjustable in `chat.php`)
- Heartbeat: 30 seconds

### Timeouts
- Offline threshold: 2 minutes of inactivity
- Session timeout: 30 minutes

### Message Settings
- Batch size: 20 messages per load
- Max message length: 5000 characters

## 🧪 Testing

1. Register two test accounts
2. Login with one account in a regular browser window
3. Login with another account in an incognito window
4. Send messages between the two accounts
5. Verify real-time updates

## 📄 License

This project is open source and available under the MIT License.

