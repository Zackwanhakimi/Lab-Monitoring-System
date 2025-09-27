<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

// Fetch timetable data grouped by day
$sql = "SELECT Timetable_ID, Timetable_Day, Timetable_StartTime, Timetable_EndTime, Timetable_ClassName FROM timetable";
$result = $conn->query($sql);

$registeredSlots = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $day = $row['Timetable_Day'];
        $startTime = strtotime($row['Timetable_StartTime']);
        $endTime = strtotime($row['Timetable_EndTime']);
        $className = $row['Timetable_ClassName'];
        $timetableID = $row['Timetable_ID'];

        // Populate registered slots with class details
        foreach (range($startTime, $endTime - 3600, 3600) as $slot) {
            $formattedTime = date("g:i A", $slot) . " - " . date("g:i A", $slot + 3600);
            $registeredSlots[$day][$formattedTime] = [
                'className' => $className,
                'Timetable_ID' => $timetableID,
                'duration' => ($endTime - $startTime) / 3600 // Class duration in hours
            ];
        }
    }
}

$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
$timeSlots = [
    "8:00 AM - 9:00 AM",
    "9:00 AM - 10:00 AM",
    "10:00 AM - 11:00 AM",
    "11:00 AM - 12:00 PM",
    "12:00 PM - 1:00 PM",
    "1:00 PM - 2:00 PM",
    "2:00 PM - 3:00 PM",
    "3:00 PM - 4:00 PM",
    "4:00 PM - 5:00 PM",
    "5:00 PM - 6:00 PM",
    "6:00 PM - 7:00 PM",
    "7:00 PM - 8:00 PM",
    "8:00 PM - 9:00 PM",
    "9:00 PM - 10:00 PM"
];

/*$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
$timeSlots = [
    "8:00 AM - 9:00 AM",
    "9:00 AM - 10:00 AM",
    "10:00 AM - 11:00 AM",
    "11:00 AM - 12:00 PM",
    "12:00 PM - 1:00 PM",
    "1:00 PM - 2:00 PM",
    "2:00 PM - 3:00 PM",
    "3:00 PM - 4:00 PM",
    "4:00 PM - 5:00 PM",
	"5:00 PM - 6:00 PM",
	"6:00 PM - 7:00 PM",
	"7:00 PM - 8:00 PM",
	"8:00 PM - 9:00 PM",
	"9:00 PM - 10:00 PM"
];*/
?>
<!doctype html>
<html lang="en">
<head>
<title>Sidebar 05</title>
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

    nav#sidebar{
        position: fixed;
        height: 100vh;
        z-index: 100;
    }

    /* .wrapper {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
    } */
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
		  		<h1><a href="dashboard.php" class="logo"><?php echo $_SESSION['Admin_name'] ?><span><?php echo $_SESSION['Admin_ID'] ?><span>Admin</span></a></h1>
                  <ul class="list-unstyled components mb-5">
                  <li>
	                    <a href="dashboard.php"><span class="fa fa-pie-chart mr-3"></span> Lab Statistics</a>
	                </li>
                    <li>
	        	        <a href="labLogs.php"><span class="fa fa-clock-o mr-3"></span> Entry/Exit Logs</a>
	                </li>
                    <li>
	        	        <a><span class="fa fa-user mr-3"></span>Student Manage</a>
	                </li>
                    <li>
                        <a href="studList.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Student List</a>
	                </li>
                    <li>
                        <a href="studRegister.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Register New Student</a>
	                </li>
                  <li>
	        	        <a><span class="fa fa-user mr-3"></span>Lecturer Manage</a>
	                </li>
                    <li>
	        	        <a href="lectList.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Lecturer List</a>
	                </li>
                    <li>
	        	        <a href="lectRegister.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Register New Lecturer</a>
	                </li>
                    <li class="active">
	        	        <a href="timetable.php"><span class="fa fa-table mr-3"></span> Timetable</a>
	                </li>
                    <li>
	        	        <a href="timetableRegister.php"><span class="fa mr-3"></span> <span class="fa fa-plus mr-3"></span> Add New Class</a>
	                </li>
                    <li>
                        <a href='logout.php'><span class="fa fa-sign-out mr-3"></span> Log Out</a>
                    </li>
                </ul>

	            <div class="footer">
	        	    <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
						  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | Prototype by UitDevTech <i class="icon-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib.com</a>
						  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
	            </div>
            </div>
    	</nav>
		<div class="container mt-5">
        <h2 class="text-center">Weekly Lab Timetable</h2>
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
                        $colspanTracker = [];
                        foreach ($timeSlots as $index => $time):
                            // Skip already spanned slots
                            if (in_array($index, $colspanTracker)) continue;

                            if (isset($registeredSlots[$day][$time])) {
                                $class = $registeredSlots[$day][$time];
                                $duration = $class['duration']; // Duration in hours
                                $slotCount = 0;

                                // Calculate colspan based on duration
                                foreach (array_slice($timeSlots, $index) as $slot) {
                                    if ($slotCount >= $duration) break;
                                    $colspanTracker[] = $index + $slotCount;
                                    $slotCount++;
                                }

                                // Render clickable cell
                                echo "<td colspan='$slotCount' class='registered'>
                                        <a href='timetableEditOrRemove.php?Timetable_ID={$class['Timetable_ID']}' 
                                        style='text-decoration: none; color: black;'>
                                            {$class['className']}
                                        </a>
                                    </td>";
                            } else {
                                // Print empty cell if not covered by colspan
                                if (!in_array($index, $colspanTracker)) {
                                    echo "<td></td>";
                                }
                            }
                        endforeach;
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.querySelectorAll('td.registered').forEach(td => {
            td.addEventListener('click', function () {
                const timetableID = this.getAttribute('data-id');
                const className = this.getAttribute('data-class');
                if (timetableID) {
                    const url = `timetableUpdate.php?id=${timetableID}`;
                    const proceed = confirm(`Class: ${className}\n\nDo you want to view/edit this class?`);
                    if (proceed) {
                        window.location.href = url;
                    }
                }
            });
        });
    </script>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
