<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

if (!isset($_GET['Timetable_ID'])) {
    die("Error: Class not selected.");
}

$timetableID = intval($_GET['Timetable_ID']); // Sanitize input

// Fetch class details based on Timetable_ID
$sql = "SELECT * FROM timetable WHERE Timetable_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $timetableID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Class not found.");
}

$class = $result->fetch_assoc();
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $className = $_POST['Timetable_ClassName'];
    $day = $_POST['Timetable_Day'];
    $startTime = $_POST['Timetable_StartTime'];
    $endTime = $_POST['Timetable_EndTime'];

    if (!$className || !$day || !$startTime || !$endTime) {
        die("Error: All fields are required.");
    }

    if ($endTime <= $startTime) {
        die("Error: End time must be after start time.");
    }

    $sqlUpdate = "UPDATE timetable SET Timetable_ClassName = ?, Timetable_Day = ?, Timetable_StartTime = ?, Timetable_EndTime = ? WHERE Timetable_ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssssi", $className, $day, $startTime, $endTime, $timetableID);

    if ($stmtUpdate->execute()) {
        header("Location: timetable.php");
        exit();
    } else {
        die("Error: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Class</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
	.table-container {
            width: 100%;
            overflow-x: auto; 
            margin: 0; 
            padding: 0; 
        }
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }
    th, td {
        padding: 10px;
        border: 1px solid black;
    }
    th {
        background-color: #4caf50;
        color: white;
    }
    td.registered {
        background-color: lightyellow;
        cursor: pointer;
    }
    td.registered:hover {
        background-color: #ffd700;
    }
	thead th {
    background-color: #4caf50; 
    color: white; 
    text-align: center;
    padding: 10px;
	border: 1px solid grey;
	}
	tbody td {
    	color: black;
		border: 1px solid grey;
	}
	tr:nth-child(even){
		background-color: #f2f2f2;
	}
	tr:hover {
		background-color: #ddd;
	}
	tr, td {
		border: 1px solid grey;
	}
	td.registered {
		background-color: lightyellow; /* Lighter yellow */
	}
	.actions button, .actions a {
		margin: 0 5px;
	}
	.timetable {
		width: 100%;
		border-collapse: collapse;
		text-align: center;
	}
	.timetable th, .timetable td {
		border: 1px solid black;
		padding: 10px;
	}
	.timetable th {
		background-color: #4caf50;
		color: white;
	}
	.occupied {
		background-color: #ffffcc;
	}
    .container {
        width: 400px;
        background-color: #fff7e6; /* Light beige */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        margin-left: 20px;
        float: left;
    }
    h1, h2 {
        color: #333;
        font-weight: bold;
    }
    label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        color: #333;
    }
    input, select {
        width: 100%;
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    button {
        background-color: #333;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #555;
    }
</style>
<body style="padding: 20px">
    <h2>Edit or Remove Class</h2>
    <form method="POST" action="">
        <input type="hidden" name="Timetable_ID" value="<?= htmlspecialchars($class['Timetable_ID']) ?>">

        <label for="Timetable_ClassName">Class Name</label>
        <input type="text" id="Timetable_ClassName" name="Timetable_ClassName" value="<?= htmlspecialchars($class['Timetable_ClassName']) ?>" required>

        <label for="Timetable_Day">Day</label>
        <select id="Timetable_Day" name="Timetable_Day" required>
            <option value="">Select Day</option>
            <?php foreach ($days as $day): ?>
                <option value="<?= htmlspecialchars($day) ?>" <?= $class['Timetable_Day'] === $day ? 'selected' : '' ?>><?= htmlspecialchars($day) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="Timetable_StartTime">Start Time</label>
        <select name="Timetable_StartTime" id="Timetable_StartTime" required>
          <option value="">Select Start Time</option>
          <?php for ($i = 8; $i <= 22; $i++): ?>
              <option value="<?= $i ?>" <?= intval($class['Timetable_StartTime']) === $i ? 'selected' : '' ?>><?= date('g:i A', strtotime("$i:00")) ?></option>
          <?php endfor; ?>
        </select>

        <label for="Timetable_EndTime">End Time</label>
        <select name="Timetable_EndTime" id="Timetable_EndTime" required>
          <option value="">Select End Time</option>
          <?php for ($i = 9; $i <= 22; $i++): ?>
              <option value="<?= $i ?>" <?= intval($class['Timetable_EndTime']) === $i ? 'selected' : '' ?>><?= date('g:i A', strtotime("$i:00")) ?></option>
          <?php endfor; ?>
        </select>

        <button type="submit" class="btn btn-primary">Update Class</button>
        <a href="timetable.php" class="btn btn-secondary">Back to Timetable</a>
    </form>

    <!-- Remove Button -->
    <form action="timetableRemove.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="Timetable_ID" value="<?= htmlspecialchars($class['Timetable_ID']) ?>">
        <button type="submit" style="background-color: red; color: white;">Remove Class</button>
    </form>
</body>
</html>
