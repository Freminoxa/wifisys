<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "127.0.0.1";
$db_username = "francis";
$db_password = "1234";
$dbname = "wifisystem";
$port = 3306;

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate input
        if (empty($_POST['username']) || empty($_POST['phone']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
            throw new Exception("All fields are required!");
        }

        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Password validation
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match!");
        }
        
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long!");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if username or phone already exists
        $check_stmt = $conn->prepare("SELECT username, phone_number FROM users WHERE username = ? OR phone_number = ?");
        $check_stmt->bind_param("ss", $username, $phone);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Username or phone number already exists!");
        }
        $check_stmt->close();

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, phone_number, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $phone, $hashed_password);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            // Redirect to login page with success message
            header("Location: login.html?registration=success");
            exit();
        } else {
            throw new Exception("Registration failed: " . $stmt->error);
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red; text-align: center; margin-top: 20px;'>";
    echo "Error: " . $e->getMessage();
    echo "<br><a href='javascript:history.back()'>Go Back</a>";
    echo "</div>";
}
?>