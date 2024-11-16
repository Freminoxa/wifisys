<?php
session_start();
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
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR phone_number = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                
                header("Location: account.html");
                exit();
            } else {
                throw new Exception("Invalid password!");
            }
        } else {
            throw new Exception("User not found!");
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red; text-align: center; margin-top: 20px;'>";
    echo "Error: " . $e->getMessage();
    echo "<br><a href='javascript:history.back()'>Go Back</a>";
    echo "</div>";
}

$conn->close();
