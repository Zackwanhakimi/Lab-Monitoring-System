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
    // Validate and sanitize Lecturer ID
    if (isset($_POST['Lect_ID']) && preg_match('/^\d{5}$/', $_POST['Lect_ID'])) {
        $Lect_ID = trim($_POST['Lect_ID']);
    } else {
        echo "<script>
                alert('Invalid Lecturer ID. It must be exactly 5 numeric digits.');
                window.history.back();
              </script>";
        exit();
    }

    // Delete lecturer record
    $deleteSQL = "DELETE FROM Lecturer WHERE Lect_ID = ?";
    $stmt = $conn->prepare($deleteSQL);
    
    if ($stmt) {
        $stmt->bind_param("s", $Lect_ID); // 's' for string as Lect_ID might be treated as VARCHAR
        if ($stmt->execute()) {
            echo "<script>
                    alert('Lecturer successfully removed!');
                    window.location.href = 'LectList.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error removing lecturer. Please try again.');
                    window.history.back();
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Database error. Please try again later.');
                window.history.back();
              </script>";
        exit();
    }
}

// Close the database connection
if ($conn) {
    $conn->close();
}
?>