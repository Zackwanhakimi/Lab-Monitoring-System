<?php
session_start(); // Start or resume a session

// Include the database connection file
include 'connect.php';

// Initialize a variable for messages
$message = "";

// Check if form data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST request
    $user_type = $_POST['switchPlan']; // Toggle switch value: Admin or Lecturer
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    if ($user_type === "Admin") {
        // Admin Login
        $sql = "SELECT * FROM Admin WHERE Admin_name = '$username' AND Admin_Password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Fetch admin data
            $user = $result->fetch_assoc();

            // Set session variables for Admin
            $_SESSION['Admin_ID'] = $user['Admin_ID'];
            $_SESSION['Admin_name'] = $user['Admin_name'];

            // Redirect to admin dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid admin username or password.";
        }
    } elseif ($user_type === "Lecturer") {
        // Lecturer Login
        $sql = "SELECT * FROM Lecturer WHERE Lect_ID = '$username' AND Lect_Password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Fetch Lecturer data
            $user = $result->fetch_assoc();

            // Set session variables for Lecturer
            $_SESSION['Lect_ID'] = $user['Lect_ID'];
            $_SESSION['Lect_Name'] = $user['Lect_Name'];

            // Redirect to Lecturer dashboard
            header("Location: lecturerDashboard.php");
            exit();
        } else {
            $message = "Invalid user ID or password.";
        }
    } else {
        $message = "Invalid user type selected.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Lab Monitoring System</title>
    
    <link rel="stylesheet" href="css/toggle.css"> <!-- Link to external CSS file -->
    <link rel="stylesheet" href="css/login.css"> <!-- Link to external CSS file -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="body">
        <div class="overlay"></div>
    </div>
    
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <form method="POST" action="loginLect.php">
        <a href="homepage.php" style="text-decoration: none; color: inherit;"><h3>KPPIM UiTMKK</h3></a>

        <!-- Toggle Switch for Admin/Lecturer -->
        <div class="switches-container">
            <input type="radio" id="switchAdmin" name="switchPlan" value="Admin" checked />
            <input type="radio" id="switchLecturer" name="switchPlan" value="Lecturer" />
            <label for="switchAdmin">Admin</label>
            <label for="switchLecturer">Lecturer</label>
            <div class="switch-wrapper">
                <div class="switch">
                    <div>Admin</div>
                    <div>Lecturer</div>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <label for="username">User ID</label>
        <input type="text" id="username" placeholder="Username or Lecturer ID" name="username" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Password" name="password" required>
        
        <button type="submit">Log In</button>
        
        <p><?php echo $message; ?></p> <!-- Display error or success messages -->
    </form>

</body>
</html>
