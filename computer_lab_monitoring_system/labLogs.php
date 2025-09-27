<?php
session_start(); // Start session

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();	
}
include 'connect.php';

// Fetch labs
$labs_query = "SELECT Lab_ID, Lab_Name FROM Lab";
$result_labs = $conn->query($labs_query);

// Default selected lab
$selected_lab_id = isset($_GET['lab_id']) ? intval($_GET['lab_id']) : 1;

// Handle filtering parameters
$filters = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['student_id'])) {
        $filters[] = "s.Stud_ID LIKE '%" . intval($_GET['student_id']) . "%'";
    }
    if (!empty($_GET['student_name'])) {
        $filters[] = "s.Stud_Name LIKE '%" . mysqli_real_escape_string($conn, $_GET['student_name']) . "%'";
    }
    if (!empty($_GET['entry_date'])) {
        $filters[] = "DATE(e.Entry_Date) = '" . mysqli_real_escape_string($conn, $_GET['entry_date']) . "'";
    }
    if (!empty($_GET['entry_duration'])) {
        $filters[] = "TIMESTAMPDIFF(MINUTE, e.Entry_StartTime, e.Entry_EndTime) >= " . intval($_GET['entry_duration']);
    }
    if (!empty($_GET['entry_month'])) {
        $filters[] = "MONTH(e.Entry_Date) = " . intval($_GET['entry_month']);
    }
    if (!empty($_GET['student_part'])) {
        $filters[] = "s.Stud_Part LIKE '%" . mysqli_real_escape_string($conn, $_GET['student_part']) . "%'";
    }
}

// Construct the SQL query with filters
$where_clause = count($filters) > 0 ? 'AND ' . implode(' AND ', $filters) : '';
$sql_logs = "SELECT s.Stud_ID, s.Stud_Name, s.Stud_Part, e.Entry_Date, 
             TIMESTAMPDIFF(MINUTE, e.Entry_StartTime, e.Entry_EndTime) AS Entry_Duration
             FROM Entry e
             JOIN Student s ON e.Stud_ID = s.Stud_ID
             WHERE e.Lab_ID = $selected_lab_id $where_clause
             ORDER BY e.Entry_Date DESC";
$result_logs = $conn->query($sql_logs);
?>

<!doctype html>
<html lang="en">
<head>
    <title>Lab Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .lab-tabs, .filters, .table-container {
            margin-bottom: 20px;
        }
        .lab-tabs a {
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            margin-right: 10px;
        }
        .lab-tabs a.active {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }

        /* Center the dropdown button */
.center {
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;
}

/* Dropdown button */
.dropdown-btn {
	display: flex;
    align-items: center;
    padding: 8px 16px;
    text-decoration: none;
    color: #FFFFFF99;
    display: flex; /* Keep the icon and text aligned */
    justify-content: space-between;
    width: 100%; /* Full width */
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
    font-size: 16px;
    transition: color 0.3s;
	z-index: 101;
	
}

.dropdown-btn:hover {
    color: #f2f2f2;
}

/* Dropdown container list styling */
.dropdown-container {
    display: none; /* Hidden by default */
    flex-direction: row;
    margin-top: 10px;
    padding-left: 20px;
}

.dropdown-container a {
	display: flex;
	flex-direction: column;
    color: #FFFFFF99;
    text-decoration: none;
    padding: 5px 10px;
    transition: color 0.3s;
}

.dropdown-container a:hover {
    color: #f2f2f2;
}

/* Optional: Icon alignment */
.fa-caret-down {
    margin-left: auto; /* Push the caret icon to the far right */
}

.fa-pencil-square-o{
	color:#6fb5ff;
}


nav#sidebar{
    position: fixed;
    height: 100vh;
    z-index: 100;
}

    </style>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
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
                    <li class="active">
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
                    <li>
	        	        <a href="timetable.php"><span class="fa fa-table mr-3"></span> Timetable</a>
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

    <!-- Page Content  -->
    <div id="content" class="p-4 p-md-5 pt-5">
    <div class="wrapper">
        <h2>Lab Logs</h2>

        <!-- Lab selection -->
        <div class="lab-tabs">
            <?php while ($lab = $result_labs->fetch_assoc()): ?>
                <a href="LabLogs.php?lab_id=<?= $lab['Lab_ID']; ?>" 
                   class="<?= $selected_lab_id == $lab['Lab_ID'] ? 'active' : ''; ?>">
                    <?= htmlspecialchars($lab['Lab_Name']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Filter form -->
<form method="GET" class="filters">
    <input type="hidden" name="lab_id" value="<?= $selected_lab_id; ?>">
    <label for="student_id">Student ID:</label>
    <input type="text" name="student_id" id="student_id" value="<?= $_GET['student_id'] ?? ''; ?>">

    <label for="student_name">Student Name:</label>
    <input type="text" name="student_name" id="student_name" value="<?= $_GET['student_name'] ?? ''; ?>">

    <label for="entry_date">Entry Date (YYYY-MM-DD):</label>
    <input type="date" name="entry_date" id="entry_date" value="<?= $_GET['entry_date'] ?? ''; ?>">

    <label for="entry_duration">Minimum Entry Duration (Minutes):</label>
    <input type="number" name="entry_duration" id="entry_duration" value="<?= $_GET['entry_duration'] ?? ''; ?>">

    <label for="entry_month">Entry Month (1-12):</label>
    <input type="number" name="entry_month" id="entry_month" value="<?= $_GET['entry_month'] ?? ''; ?>">

    <label for="student_part">Student Part:</label>
    <input type="text" name="student_part" id="student_part" value="<?= $_GET['student_part'] ?? ''; ?>">

    <button type="submit">Apply Filters</button>
</form>

<!-- Log table -->
<!-- Log table -->
<!-- Log table -->
<div class="table-container">
    <h3>Logs for Selected Lab</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Student Part</th>
                <th>Entry Date</th>
                <th>Entry Time</th>
                <th>Exit Time</th>
                <th>Entry Duration (Minutes)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Build the SQL query with filters
            $conditions = ["e.Lab_ID = $selected_lab_id"];
            if (!empty($_GET['student_id'])) {
                $student_id = $conn->real_escape_string($_GET['student_id']);
                $conditions[] = "e.Stud_ID = '$student_id'";
            }
            if (!empty($_GET['student_name'])) {
                $student_name = $conn->real_escape_string($_GET['student_name']);
                $conditions[] = "s.Stud_Name LIKE '%$student_name%'";
            }
            if (!empty($_GET['entry_date'])) {
                $entry_date = $conn->real_escape_string($_GET['entry_date']);
                $conditions[] = "e.Entry_Date = '$entry_date'";
            }
            if (!empty($_GET['entry_duration'])) {
                $entry_duration = intval($_GET['entry_duration']);
                $conditions[] = "TIMESTAMPDIFF(MINUTE, e.Entry_StartTime, e.Entry_EndTime) >= $entry_duration";
            }
            if (!empty($_GET['entry_month'])) {
                $entry_month = intval($_GET['entry_month']);
                $conditions[] = "MONTH(e.Entry_Date) = $entry_month";
            }
            if (!empty($_GET['student_part'])) {
                $student_part = $conn->real_escape_string($_GET['student_part']);
                $conditions[] = "s.Stud_Part LIKE '%$student_part%'";
            }

            $where_clause = implode(' AND ', $conditions);

            // Fetch logs with formatted times
            $sql_logs = "SELECT e.Stud_ID, s.Stud_Name, s.Stud_Part, e.Entry_Date, 
                                DATE_FORMAT(e.Entry_StartTime, '%l:%i %p') AS Entry_Time, 
                                DATE_FORMAT(e.Entry_EndTime, '%l:%i %p') AS Exit_Time, 
                                TIMESTAMPDIFF(MINUTE, e.Entry_StartTime, e.Entry_EndTime) AS Entry_Duration
                         FROM Entry e
                         INNER JOIN Student s ON e.Stud_ID = s.Stud_ID
                         WHERE $where_clause
                         ORDER BY e.Entry_Date DESC, e.Entry_StartTime DESC";
            $result_logs = $conn->query($sql_logs);

            if ($result_logs->num_rows > 0): 
                while ($row = $result_logs->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Stud_ID']); ?></td>
                        <td><?= htmlspecialchars($row['Stud_Name']); ?></td>
                        <td><?= htmlspecialchars($row['Stud_Part']); ?></td>
                        <td><?= htmlspecialchars($row['Entry_Date']); ?></td>
                        <td><?= htmlspecialchars($row['Entry_Time']); ?></td>
                        <td><?= htmlspecialchars($row['Exit_Time'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($row['Entry_Duration'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endwhile; 
            else: ?>
                <tr>
                    <td colspan="7">No logs found for the selected filters.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



    </div>
    </div>
</div>

<script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

	<script>
		//* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}

	</script>
</body>
</html>
