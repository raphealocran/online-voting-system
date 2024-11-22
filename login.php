<?php
session_start(); // Start a session to maintain login state

// Enable error reporting for development (disable in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection settings
$servername = "localhost";
$dbUsername = "root"; // Default username for XAMPP is 'root'
$dbPassword = "";     // Default password for XAMPP is empty
$dbname = "voting";   // Updated to match your SQL schema

try {
    // Create a new MySQLi connection
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        echo "<div style='color: red; margin-top: 20px;'>Please enter both username and password.</div>";
    } else {
        // Check if the username exists in the database
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Log the successful login in `login_tracker`
                $userId = $user['id'];
                $logStmt = $conn->prepare("INSERT INTO login_tracker (user_id, status) VALUES (?, 'Success')");
                $logStmt->bind_param("i", $userId);
                $logStmt->execute();
                $logStmt->close();

                // Set session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;

                // Redirect to a secure page or dashboard
                header("Location: homepage.html");
                exit();
            } else {
                // Log the failed login in `login_tracker`
                $logStmt = $conn->prepare("INSERT INTO login_tracker (user_id, status) VALUES ((SELECT id FROM users WHERE username = ?), 'Failure')");
                $logStmt->bind_param("s", $username);
                $logStmt->execute();
                $logStmt->close();

                echo "<div style='color: red; margin-top: 20px;'>Invalid username or password.</div>";
            }
        } else {
            echo "<div style='color: red; margin-top: 20px;'>Invalid username or password.</div>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body style="margin: 0; height: 100vh; display: flex; justify-content: center; align-items: center; flex-direction: column; text-align: center; background-color: lightslategray; font-family: Arial, sans-serif;">
    <div style="background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
        <header>
            <h1 style="margin-bottom: 20px;">Login</h1>
            <p>Enter your username and password to log in.</p>
        </header>

        <form method="post" action="login.php">
            <div style="margin-bottom: 15px;">
                <label for="username" style="font-weight: bold;">Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;" />
            </div>
            <div style="margin-bottom: 15px;">
                <label for="password" style="font-weight: bold;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;" />
            </div>
            <button type="submit" style="background-color: #2980b9; color: white; padding: 12px 25px; border: none; font-size: 1rem; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 20px;">Log In</button>
        </form>

        <p style="margin-top: 20px;">Don't have an account? <a href="signup.php" style="color: #2980b9;">Sign up here</a></p>
    </div>
</body>
</html>

