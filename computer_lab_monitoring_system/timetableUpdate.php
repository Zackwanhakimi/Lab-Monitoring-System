<?php
include("connect.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $day = $_POST['day'];
    $startTime = $_POST['lab_StartTime'];
    $endTime = $_POST['lab_EndTime'];

    $sql = "UPDATE timetable SET day = ?, lab_StartTime = ?, lab_EndTime = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $day, $startTime, $endTime, $id);

    if ($stmt->execute()) {
        header("Location: timetable.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $sql = "SELECT * FROM timetable WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $entry = $result->fetch_assoc();
    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Update Timetable Entry</title>
</head>
<body>
    <form method="post">
        <label>Day:</label>
        <select name="day" required>
            <option value="Monday" <?= $entry['day'] == 'Monday' ? 'selected' : '' ?>>Monday</option>
            <option value="Tuesday" <?= $entry['day'] == 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
            <option value="Wednesday" <?= $entry['day'] == 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
            <option value="Thursday" <?= $entry['day'] == 'Thursday' ? 'selected' : '' ?>>Thursday</option>
            <option value="Friday" <?= $entry['day'] == 'Friday' ? 'selected' : '' ?>>Friday</option>
        </select>
        <br>
        <label>Start Time:</label>
        <input type="time" name="lab_StartTime" value="<?= $entry['lab_StartTime'] ?>" required>
        <br>
        <label>End Time:</label>
        <input type="time" name="lab_EndTime" value="<?= $entry['lab_EndTime'] ?>" required>
        <br>
        <button type="submit">Update Entry</button>
    </form>
</body>
</html>
