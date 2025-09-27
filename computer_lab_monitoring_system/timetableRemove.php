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
    if (isset($_POST['Timetable_ID'])) {
        $Timetable_ID = trim($_POST['Timetable_ID']); // Clean the input
    } else {
        die("Invalid Timetable ID.");
    }

    $deleteSQL = "DELETE FROM timetable WHERE Timetable_ID = ?";
    $stmt = $conn->prepare($deleteSQL);
        
    if ($stmt) {
        $stmt->bind_param("i", $Timetable_ID);
        if ($stmt->execute()) {
            echo "<script>
                    alert('Class successfully removed!');
                    window.location.href = 'timetable.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error removing class. Please try again.');
                    window.location.href = 'timetable.php';
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