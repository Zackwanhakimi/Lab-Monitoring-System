<?php
include("connect.php");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $LectName = trim($_POST['Lect_Name']);
    $LectID = trim($_POST['Lect_ID']);

    // Validation
    if (empty($LectName) || !preg_match('/^[a-zA-Z\s@\/]+$/', $LectName)) {
        echo "<script>
                alert('Invalid Lecturer Name. Please use letters and allowed symbols (@ and /) only.');
                window.history.back();
              </script>";
        exit();
    }

    if (empty($LectID) || !preg_match('/^[a-zA-Z0-9]+$/', $LectID)) {
        echo "<script>
                alert('Invalid Lecturer ID. Must contain only alphanumeric characters.');
                window.history.back();
              </script>";
        exit();
    }

    // Insert Data into the Lecturer table
    $insertSQL = "INSERT INTO Lecturer (Lect_Name, Lect_ID) VALUES (?, ?)";
    $stmt = $conn->prepare($insertSQL);

    if ($stmt) {
        $stmt->bind_param("ss", $LectName, $LectID); // Both fields are strings
        if ($stmt->execute()) {
            // Redirect on success
            echo "<script>
                    alert('Registration Successful!');
                    window.location.href = 'LectList.php';
                  </script>";
            exit();
        } else {
            // Handle execution error
            echo "<script>
                    alert('Error registering Lecturer. Please try again.');
                    window.history.back();
                  </script>";
            exit();
        }
        $stmt->close();
    } else {
        // Handle statement preparation error
        echo "<script>
                alert('Database error. Please contact the administrator.');
                window.history.back();
              </script>";
        exit();
    }
}

// Close database connection
if ($conn) {
    $conn->close();
}
?>
