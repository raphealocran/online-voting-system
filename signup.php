<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input sanitization
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div style='color: red; margin-top: 20px;'>Invalid email format. Please try again.</div>";
    } elseif (strlen($password) < 8) {
        echo "<div style='color: red; margin-top: 20px;'>Password must be at least 8 characters long.</div>";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT email, username FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<div style='color: red; margin-top: 20px;'>Email or Username already taken. Please choose a different one.</div>";
        } else {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $username, $hashedPassword);

            if ($stmt->execute()) {
                // Log sign-up in `signup_tracker`
                $userId = $conn->insert_id; // Get the user ID of the newly created user
                $trackerStmt = $conn->prepare("INSERT INTO signup_tracker (user_id) VALUES (?)");
                $trackerStmt->bind_param("i", $userId);

                if ($trackerStmt->execute()) {
                    echo "<div style='color: green; margin-top: 20px;'>Sign-up successful! You can now <a href='login.php'>log in here</a>.</div>";
                } else {
                    echo "<div style='color: red; margin-top: 20px;'>Error logging signup: " . $trackerStmt->error . "</div>";
                }
                $trackerStmt->close();
            } else {
                echo "<div style='color: red; margin-top: 20px;'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body style="margin: 0; height: 100vh; display: flex; justify-content: center; align-items: center; flex-direction: column; text-align: center; background-color: lightslategray; font-family: Arial, sans-serif;">
    <div style="background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
        <header>
            <h1 style="margin-bottom: 20px;">Sign Up</h1>
            <p>Enter your details to create an account and start voting.</p>
        </header>

        <form method="post" action="signup.php">
            <div style="margin-bottom: 15px;">
                <label for="email" style="font-weight: bold;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;" />
            </div>
            <div style="margin-bottom: 15px;">
                <label for="username" style="font-weight: bold;">Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;" />
            </div>
            <div style="margin-bottom: 15px;">
                <label for="password" style="font-weight: bold;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;" />
            </div>
            <button type="submit" style="background-color: #2980b9; color: white; padding: 12px 25px; border: none; font-size: 1rem; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 20px;">Sign Up</button>
        </form>

        <p style="margin-top: 20px;">Already have an account? <a href="login.php" style="color: #2980b9;">Log in here</a></p>
    </div>
</body>
</html>
