<?php
    include("connect.php");

    //Database connection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studName = trim($_POST['Stud_Name']);
    $studID = trim($_POST['Stud_ID']);
    $studPart = trim($_POST['Stud_Part']);

    // Validation
    if (empty($studName) || !preg_match('/^[a-zA-Z\s@\/]+$/', $studName)) {
        echo "<script>
                alert('Invalid Student Name. Please use letters and allowed symbols(@ and /) only.');
                window.history.back();
              </script>";
        exit();
    }

    if (empty($studID) || !preg_match('/^\d{10}$/', $studID)) {
        echo "<script>
                alert('Invalid Student ID. Must be numbers only and exactly 10 digits.');
                window.history.back();
              </script>";
        exit();
    }

    if (empty($studPart) || !preg_match('/^[1-7]$/', $studPart)) {
        echo "<script>
                alert('Invalid Student Part. Must be a number between 1 and 7.');
                window.history.back();
              </script>";
        exit();
    }

    // Step 1: Insert Data
    $insertSQL = "INSERT INTO student (Stud_Name, Stud_ID, Stud_Part) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertSQL);

    if ($stmt) {
        $stmt->bind_param("sii", $studName, $studID, $studPart);
        if ($stmt->execute()) {
            // Redirect on success
            echo "<script>
                    alert('Registration Successful!');
                    window.location.href = 'studList.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error registering student. Please try again.');
                    window.history.back();
                  </script>";
            exit();
        }
        $stmt->close();
    }

    
}


if ($conn) {
    $conn->close();
}

?>