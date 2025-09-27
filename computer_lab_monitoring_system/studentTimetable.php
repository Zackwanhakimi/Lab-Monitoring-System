<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Stud_ID'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

// Fetch current student info
$student_id = $_SESSION['Stud_ID'];
$sql_student = "SELECT Stud_Name, Stud_Part FROM Student WHERE Stud_ID = '$student_id'";
$result_student = $conn->query($sql_student);
$student_info = $result_student->fetch_assoc();

// Fetch all labs
$labsQuery = "SELECT Lab_ID, Lab_Name FROM lab";
$labsResult = $conn->query($labsQuery);
$labs = [];
if ($labsResult->num_rows > 0) {
    while ($labRow = $labsResult->fetch_assoc()) {
        $labs[] = $labRow;
    }
}

// Fetch timetable data
$timetableQuery = "SELECT Timetable_ID, Lab_ID, Timetable_Day, Timetable_StartTime, Timetable_EndTime, Timetable_ClassName FROM timetable";
$timetableResult = $conn->query($timetableQuery);
$timetables = [];
if ($timetableResult->num_rows > 0) {
    while ($row = $timetableResult->fetch_assoc()) {
        $labID = $row['Lab_ID'];
        $day = $row['Timetable_Day'];
        $startTime = intval($row['Timetable_StartTime']);
        $endTime = intval($row['Timetable_EndTime']);
        $className = $row['Timetable_ClassName'];
        $timetableID = $row['Timetable_ID'];

        // Populate registered slots for each lab
        for ($slot = $startTime; $slot < $endTime; $slot++) {
            $timetables[$labID][$day][$slot - 8] = [
                'className' => $className,
                'Timetable_ID' => $timetableID,
                'duration' => $endTime - $startTime // Class duration in slots
            ];
        }
    }
}

$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
$timeSlots = [];
for ($i = 1; $i <= 14; $i++){
$timeSlots[] = "Slot $i";}
?>

<!doctype html>
<html lang="en">
<head>
<title>Timetable</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
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
    </style>
</head>
<body>
<div class="wrapper d-flex align-items-stretch">
<nav id="sidebar">
			<div class="custom-menu">
				<button type="button" id="sidebarCollapse" class="btn btn-primary">
	          	<i class="fa fa-bars"></i>
	          	<span class="sr-only">Toggle Menu</span>
	        	</button>
        	</div>
			<div class="p-4">
				<h1><a href="index.html" class="logo"><?php echo htmlspecialchars($student_info['Stud_Name']); ?><span><?php echo htmlspecialchars($student_id); ?><span>Student</span></a></h1>
				<ul class="list-unstyled components mb-5">
					<li class="active">
						<a href="studentDashboard.php"><span class="fa fa-home mr-3"></span> Home</a>
					</li>
					<li>
                    <a href="studentTimetable.php"><span class="fa fa-calendar mr-3"></span> Timetable</a>
                </li>
				<li>
						<a href="studentInfo.php"><span class="fa fa-user mr-3"></span> Your Info</a>
					</li>
					<li>
						<a href='logout.php'><span class="fa fa-paper-plane mr-3"></span> Log Out</a>
					</li>
				</ul>
			</div>
    	</nav>


		<div class="container mt-5">
        <h2 class="text-center">Weekly Lab Timetables</h2>

        <?php foreach ($labs as $lab): ?>
            <h3><?= htmlspecialchars($lab['Lab_Name']) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Day/Time</th>
                        <?php foreach ($timeSlots as $time): ?>
                            <th><?= htmlspecialchars($time) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($days as $day): ?>
                    <tr>
                        <th><?= htmlspecialchars($day) ?></th>
                        <?php
                        for ($slotIndex = 0; $slotIndex < count($timeSlots); $slotIndex++):
                            if (isset($timetables[$lab['Lab_ID']][$day][$slotIndex])) {
                                $class = $timetables[$lab['Lab_ID']][$day][$slotIndex];
                                $duration = $class['duration']; // Duration in slots

                                // Render clickable cell spanning multiple slots
                                echo "<td colspan='$duration' class='registered'>
                                        <a style='text-decoration: none; color: black;'>
                                            {$class['className']}
                                        </a>
                                    </td>";

                                // Skip spanned slots
                                $slotIndex += $duration - 1;
                            } else {
                                // Empty slot
                                echo "<td></td>";
                            }
                        endfor;
                        ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
        <div class="slot-timings" style="margin-top: 20px; padding: 10px; border-top: 1px solid #ccc;">
            <h4>Slot Timings:</h4>
            <ul style="list-style-type: none; padding: 0;">
                <?php
                $startHour = 8; // Start time for Slot 1
                for ($slot = 1; $slot <= 14; $slot++) {
                    $startTime = $startHour + ($slot - 1);
                    $endTime = $startTime + 1;
                    echo "<li>Slot $slot: {$startTime}:00 - {$endTime}:00</li>";
                }
                ?>
            </ul>
        </div>
    </div>
</div>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
