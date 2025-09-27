<?php
session_start(); // Start or resume a session

// Include the database connection file
include 'connect.php';

// Initialize variables for messages and user type
$message = "";
$user_type = ""; // Default to an empty string

// Check if form data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST request
    $user_type = $_POST['switchPlan'] ?? ''; // Retrieve the user type or default to an empty string
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
            $user = $result->fetch_assoc();
            $_SESSION['Admin_ID'] = $user['Admin_ID'];
            $_SESSION['Admin_name'] = $user['Admin_name'];
            $_SESSION['user_type'] = 'Admin';
            $_SESSION['user_id'] = $user['Admin_ID'];
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
            $user = $result->fetch_assoc();
            $_SESSION['Stud_ID'] = $user['Stud_ID'];
            $_SESSION['Stud_Name'] = $user['Stud_Name'];
            $_SESSION['user_type'] = 'Student';
            $_SESSION['user_id'] = $user['Stud_ID'];
            header("Location: studentDashboard.php");
            exit();
        } else {
            $message = "Invalid user ID or password.";
        }
    } elseif ($user_type === "Lecturer") {
        // Lecturer Login
        $sql = "SELECT * FROM Lecturer WHERE Lect_ID = '$username' AND Lect_Password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['Lect_ID'] = $user['Lect_ID'];
            $_SESSION['Lect_Name'] = $user['Lect_Name'];
            $_SESSION['user_type'] = 'Lecturer';
            $_SESSION['user_id'] = $user['Lect_ID'];
            header("Location: lecturerDashboard.php");
            exit();
        } else {
            $message = "Invalid user ID or password.";
        }
    } else {
        $message = "Please select a user type.";
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
        gap: 20px; /* Adds spacing between radio buttons */
    }

    form .option label {
        font-size: 20px; /* Optional: Adjust label font size */
        display: flex;
        align-items: center; /* Vertically align label text with radio buttons */
    }

    .text {
        margin-left: 3px;
    }
    
    label, input {
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Lab Monitoring System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css"> <!-- Link to external CSS file -->
</head>

<body>
    <div class="overlay"></div>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <form method="POST" action="login.php">
        <a href="homepage.php" style="text-decoration: none; color: inherit;"><h3>KPPIM UiTMKK</h3></a>
        <div class="option">
            <label>
                <input type="radio" id="switchAdmin" name="switchPlan" value="Admin" 
                <?php if ($user_type === 'Admin') echo 'checked'; ?> />
                <a class="text">Admin</a>
            </label>
            <label>
                <input type="radio" id="switchStudent" name="switchPlan" value="Student" 
                <?php if ($user_type === 'Student') echo 'checked'; ?> />
                <a class="text">Student</a>
            </label>
            <label>
                <input type="radio" name="switchPlan" value="Lecturer" 
                <?php if ($user_type === 'Lecturer') echo 'checked'; ?> />
                <a class="text">Lecturer</a>
            </label>
        </div>
        <div>
            <label for="username">User ID</label>
            <input type="text" id="username" placeholder="Username or Student ID" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Password" name="password" required>
            
            <button type="submit">Log In</button>
            
            <p><?php echo $message; ?></p> <!-- Display error or success messages -->
        </div>
    </form>

    <script>
    // Function to get query parameters from the URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Get the userType parameter from the URL
    const userType = getQueryParam('userType');

    // Automatically select the corresponding radio button
    if (userType) {
        const radioButton = document.querySelector(`input[name="switchPlan"][value="${userType}"]`);
        if (radioButton) {
            radioButton.checked = true;
        }
    }
</script>
</body>
</html>
