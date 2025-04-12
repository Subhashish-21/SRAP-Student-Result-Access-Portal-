<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// RDS connection details
$servername = "resultdb.c9umyemakbqj.ap-south-1.rds.amazonaws.com";
$username = "admin";
$password = "admin#2003";
$dbname = "studentdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form fields are received
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $userId = trim($_POST['username']);
        $pw = trim($_POST['password']);

        // Check if fields are empty
        if (empty($userId) || empty($pw)) {
            die("Please provide both username and password.");
        }

        // Prepare and execute SQL query using prepared statements
        $stmt = $conn->prepare("SELECT eusername, epassword FROM userdetails WHERE eusername = ? AND epassword = ?");
        $stmt->bind_param("ss", $userId, $pw);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result && $result->num_rows > 0) {
            $_SESSION['username'] = $userId;
            echo 'Valid User';
            ?>
            <script type="text/javascript">
                window.location = "/dashboard.php";
            </script>
            <?php
        } else {
            echo "User Not Found. Please provide a valid Username and Password.";
            ?>
            <script type="text/javascript">
                       window.location = "/index.php";
            </script>
            <?php
        }

        $stmt->close();
    } else {
        echo "Username or password fields are missing in the form submission.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
