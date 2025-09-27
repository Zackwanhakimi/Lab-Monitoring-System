<?php
session_start(); // Start session

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();	
}
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Stud_ID']) && preg_match('/^\d{10}$/', $_POST['Stud_ID'])) {
        $Stud_ID = trim($_POST['Stud_ID']); // Clean the input
    } else {
        die("Invalid Student ID.");
    }

    $deleteSQL = "DELETE FROM student WHERE Stud_ID = ?";
    $stmt = $conn->prepare($deleteSQL);
        
    if ($stmt) {
        $stmt->bind_param("i", $Stud_ID);
        if ($stmt->execute()) {
            echo "<script>
                    alert('Student successfully removed!');
                    window.location.href = 'studList.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error removing student. Please try again.');
                    window.location.href = 'studList.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Database error. Please try again later.');
                window.location.href = 'studList.php';
              </script>";
        exit();
    }
    
}

if ($conn) {
    $conn->close();
}
?>