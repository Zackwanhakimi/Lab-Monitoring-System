<?php
session_start();
include 'connect.php'; // Ensure this file connects to your MySQL database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate session data for user type and ID
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        echo json_encode(['status' => 'error', 'message' => 'User session invalid.']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type']; // Expected values: 'Admin', 'Student', 'Lecturer'

    // Determine the table and column based on user type
    $table = '';
    $idColumn = '';
    $passwordColumn = '';

    if ($userType === 'Admin') {
        $table = 'Admin';
        $idColumn = 'Admin_ID';
        $passwordColumn = 'Admin_Password';
    } elseif ($userType === 'Student') {
        $table = 'Student';
        $idColumn = 'Stud_ID';
        $passwordColumn = 'Stud_Password';
    } elseif ($userType === 'Lecturer') {
        $table = 'Lecturer';
        $idColumn = 'Lect_ID';
        $passwordColumn = 'Lect_Password';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user type.']);
        exit;
    }

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // Fetch current password from the database
    $stmt = $conn->prepare("SELECT $passwordColumn FROM $table WHERE $idColumn = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if ($currentPassword !== $storedPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit;
    }

    // Update the password
    $updateStmt = $conn->prepare("UPDATE $table SET $passwordColumn = ? WHERE $idColumn = ?");
    $updateStmt->bind_param("ss", $newPassword, $userId);

    if ($updateStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating password.']);
    }
    $updateStmt->close();
}
?>
