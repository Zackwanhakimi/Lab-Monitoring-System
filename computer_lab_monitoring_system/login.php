<?php
session_start(); // Start or resume a session

// Include the database connection file
include 'connect.php';

// Initialize a variable for messages
$message = "";

// Check if form data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST request
    $user_type = $_POST['switchPlan']; // Toggle switch value: Admin or Student
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
    } elseif ($user_type === "Student") {
        // Student Login
        $sql = "SELECT * FROM Student WHERE Stud_ID = '$username' AND Stud_Password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Fetch student data
            $user = $result->fetch_assoc();

            // Set session variables for Student
            $_SESSION['Stud_ID'] = $user['Stud_ID'];
            $_SESSION['Stud_Name'] = $user['Stud_Name'];

            // Redirect to student dashboard
            header("Location: studentDashboard.php");
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

<style>
    form .option {
        display: flex;
        justify-content: center; /* Centers horizontally */
        align-items: center; /* Aligns vertically */
        gap: 10px; /* Adds spacing between radio buttons */
    }

    form .option label {
        font-size: 20px; /* Optional: Adjust label font size */
        display: flex;
        align-items: center; /* Vertically align label text with radio buttons */
    }

    label, input {
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .text {
        margin-left: 3px;
    }

</style>

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

    <form method="POST" action="login.php">
        <a href="homepage.php" style="text-decoration: none; color: inherit;"><h3>KPPIM UiTMKK</h3></a>

        <!-- Toggle Switch for Admin/Student -->
        <!-- <div class="switches-container">
            <input type="radio" id="switchAdmin" name="switchPlan" value="Admin" checked />
            <input type="radio" id="switchStudent" name="switchPlan" value="Student" />
            <label for="switchAdmin">Admin</label>
            <label for="switchStudent">Student</label>
            <div class="switch-wrapper">
                <div class="switch">
                    <div>Admin</div>
                    <div>Student</div>
                </div>
            </div>
        </div> -->

        
        <div class="option">
            <label>
                <input type="radio" id="switchAdmin" name="switchPlan" value="Admin" />
                <a class="text">Admin</a>
            
                <input type="radio" id="switchStudent" name="switchPlan" value="Student" />
                <a class="text">Student</a>
            
                <input type="radio" name="switchPlan" value="Lecturer" />
                <a class="text">Lecturer</a>
            </label>
        </div>

        </div>

        <!-- Login Form -->
        <label for="username">User ID</label>
        <input type="text" id="username" placeholder="Username or Student ID" name="username" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Password" name="password" required>
        
        <button type="submit">Log In</button>
        
        <p><?php echo $message; ?></p> <!-- Display error or success messages -->
    </form>

</body>
</html>
