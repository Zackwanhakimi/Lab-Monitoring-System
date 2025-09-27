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
$selected_lab_id = isset($_GET['lab_id']) ? intval($_GET['lab_id']) : null;

// Initialize variables
$logs_available = false;
$filter_error = false;

// Handle filtering parameters
$filters = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['filter_type']) && !empty($_GET['filter_value']) && $selected_lab_id) {
    $filter_type = $_GET['filter_type'];
    $filter_value = mysqli_real_escape_string($conn, $_GET['filter_value']);

    switch ($filter_type) {
        case 'student_id':
            $filters[] = "s.Stud_ID LIKE '%$filter_value%'";
            break;
        case 'student_name':
            $filters[] = "s.Stud_Name LIKE '%$filter_value%'";
            break;
        case 'entry_date':
            $filters[] = "DATE(e.Entry_Date) = '$filter_value'";
            break;
        case 'entry_duration':
            $filters[] = "TIMESTAMPDIFF(MINUTE, e.Entry_StartTime, e.Entry_EndTime) >= $filter_value";
            break;
        case 'entry_month':
            $filters[] = "MONTH(e.Entry_Date) = $filter_value";
            break;
        case 'student_part':
            $filters[] = "s.Stud_Part LIKE '%$filter_value%'";
            break;
        default:
            $filter_error = true;
    }

    if (!$filter_error) {
        // Construct the SQL query with filters
        $where_clause = count($filters) > 0 ? 'AND ' . implode(' AND ', $filters) : '';
        $sql_logs = "SELECT s.Stud_ID, s.Stud_Name, s.Stud_Part, e.Entry_Date, 
                     DATE_FORMAT(e.Entry_StartTime, '%l:%i %p') AS Entry_Time, 
                     DATE_FORMAT(e.Entry_EndTime, '%l:%i %p') AS Exit_Time, 
                     TIMESTAMPDIFF(MINUTE, e.Entry_StartTime, e.Entry_EndTime) AS Entry_Duration
                     FROM Entry e
                     JOIN Student s ON e.Stud_ID = s.Stud_ID
                     WHERE e.Lab_ID = $selected_lab_id $where_clause
                     ORDER BY e.Entry_Date DESC, e.Entry_StartTime DESC";
        $result_logs = $conn->query($sql_logs);

        $logs_available = $result_logs && $result_logs->num_rows > 0;
    }
}
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
	        	        <a href="timetable.php"><span class="fa fa-table mr-3"></span> Timetable </a>
	                </li>
                    <li>
	        	        <a href="timetableRegister.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Add new class to Timetable</a>
	                </li>
                    <li>
	        	        <a href="adminInfo.php"><span class="fa fa-user mr-3"></span> Admin Info </a>
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

        <label for="filter_type">Filter By:</label>
        <select name="filter_type" id="filter_type" onchange="updateInputPlaceholder()">
            <option value="">Select Filter</option>
            <option value="student_id" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'student_id' ? 'selected' : ''; ?>>Student ID</option>
            <option value="student_name" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'student_name' ? 'selected' : ''; ?>>Student Name</option>
            <option value="entry_date" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'entry_date' ? 'selected' : ''; ?>>Entry Date</option>
            <option value="entry_duration" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'entry_duration' ? 'selected' : ''; ?>>Minimum Entry Duration</option>
            <option value="entry_month" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'entry_month' ? 'selected' : ''; ?>>Entry Month</option>
            <option value="student_part" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'student_part' ? 'selected' : ''; ?>>Student Part</option>
        </select>

        <label for="filter_value">Value:</label>
        <input type="text" name="filter_value" id="filter_value" value="<?= $_GET['filter_value'] ?? ''; ?>" placeholder="Enter value">

        <button type="submit">Apply Filter</button>
    </form>

    <!-- Log table -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_type'])): ?>
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
                <?php if ($logs_available): ?>
                    <?php while ($row = $result_logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Stud_ID']); ?></td>
                            <td><?= htmlspecialchars($row['Stud_Name']); ?></td>
                            <td><?= htmlspecialchars($row['Stud_Part']); ?></td>
                            <td><?= htmlspecialchars($row['Entry_Date']); ?></td>
                            <td><?= htmlspecialchars($row['Entry_Time']); ?></td>
                            <td><?= htmlspecialchars($row['Exit_Time'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($row['Entry_Duration'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No logs found for the selected filters.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    function updateInputPlaceholder() {
        const filterType = document.getElementById('filter_type').value;
        const filterInput = document.getElementById('filter_value');
        switch (filterType) {
            case 'student_id':
                filterInput.placeholder = 'Enter Student ID';
                break;
            case 'student_name':
                filterInput.placeholder = 'Enter Student Name';
                break;
            case 'entry_date':
                filterInput.placeholder = 'Enter Date (YYYY-MM-DD)';
                break;
            case 'entry_duration':
                filterInput.placeholder = 'Enter Duration (Minutes)';
                break;
            case 'entry_month':
                filterInput.placeholder = 'Enter Month (1-12)';
                break;
            case 'student_part':
                filterInput.placeholder = 'Enter Student Part';
                break;
            default:
                filterInput.placeholder = 'Enter value';
        }
    }

    document.addEventListener('DOMContentLoaded', updateInputPlaceholder);
</script>
    </div>
</div>

</body>
</html>
