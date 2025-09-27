<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stud_id = $_POST['stud_id'];
    $lab_id = $_POST['lab_id']; // Capture the selected lab ID

    // Sanitize inputs
    $stud_id = intval($stud_id); // Ensure only the first 10 characters
    $lab_id = intval($lab_id);

    // Check if student ID exists
    $check_student = "SELECT * FROM Student WHERE Stud_ID = '$stud_id'";
    $result = $conn->query($check_student);

    if ($result->num_rows == 0) {
        // Invalid student ID
        echo "Invalid Student ID.";
        exit();
    }

    // Check for an open entry (no exit time)
    $check_open_entry = "SELECT * FROM Entry WHERE Stud_ID = '$stud_id' AND Lab_ID = $lab_id AND Entry_EndTime IS NULL";
    $result_open_entry = $conn->query($check_open_entry);

    if ($result_open_entry->num_rows > 0) {
        // Open entry found, record exit time
        $update_exit = "UPDATE Entry 
                        SET Entry_EndTime = NOW() 
                        WHERE Stud_ID = '$stud_id' AND Lab_ID = $lab_id AND Entry_EndTime IS NULL";
        if ($conn->query($update_exit) === TRUE) {
            $_SESSION['message'] = "Exit time recorded successfully.";
        } else {
            $_SESSION['message'] = "Error updating record: " . $conn->error;
        }
    } else {
        // No open entry, record entry time
        $insert_entry = "INSERT INTO Entry (Entry_Date, Entry_StartTime, Lab_ID, Stud_ID) 
                         VALUES (CURDATE(), NOW(), $lab_id, '$stud_id')";
        if ($conn->query($insert_entry) === TRUE) {
            $_SESSION['message'] = "Entry time recorded successfully.";
        } else {
            $_SESSION['message'] = "Error inserting record: " . $conn->error;
        }
    }
}

$conn->close();

// Redirect back to the EntryExit page with the same lab ID
header("Location: EntryExit.php?lab_id=$lab_id");
exit();
?>