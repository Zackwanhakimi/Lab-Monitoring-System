<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['Stud_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();	
}

include 'connect.php';

// Fetch current student info
$student_id = $_SESSION['Stud_ID'];
$sql_student = "SELECT Stud_Name, Stud_Part FROM Student WHERE Stud_ID = '$student_id'";
$result_student = $conn->query($sql_student);
$student_info = $result_student->fetch_assoc();

// Get selected lab
$selected_lab_id = $_GET['lab_id'] ?? null;

// Fetch lab options
$sql_labs = "SELECT Lab_ID, Lab_Name FROM Lab";
$result_labs = $conn->query($sql_labs);

// Fetch student stats for selected lab
if ($selected_lab_id) {
    $sql_stats = "SELECT 
                    COUNT(*) AS total_entries,
                    MAX(Entry_StartTime) AS last_entry,
                    AVG(TIMESTAMPDIFF(MINUTE, Entry_StartTime, Entry_EndTime)) AS avg_duration
                  FROM Entry
                  WHERE Stud_ID = '$student_id' AND Lab_ID = '$selected_lab_id'";
    $result_stats = $conn->query($sql_stats);
    $stats = $result_stats->fetch_assoc();

    // Fetch student logs for selected lab
    $sql_logs = "SELECT 
                    Entry_Date, Entry_StartTime, Entry_EndTime, 
                    TIMESTAMPDIFF(MINUTE, Entry_StartTime, Entry_EndTime) AS duration
                 FROM Entry
                 WHERE Stud_ID = '$student_id' AND Lab_ID = '$selected_lab_id'
                 ORDER BY Entry_StartTime DESC";
    $result_logs = $conn->query($sql_logs);
}
?>

<!doctype html>
<html lang="en">
  <head>
  	<title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
						<a href="#"><span class="fa fa-home mr-3"></span> Home</a>
					</li>
					<li>
						<a href='logout.php'><span class="fa fa-paper-plane mr-3"></span> Log Out</a>
					</li>
				</ul>
			</div>
    	</nav>

        <!-- Page Content  -->
		<div id="content" class="p-4 p-md-5 pt-5">
			<h2 class="mb-4">Student Dashboard</h2>

			<!-- Current Student Info -->
			<div>
				<h3>Current Student Info</h3>
				<p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>
				<p><strong>Student Name:</strong> <?php echo htmlspecialchars($student_info['Stud_Name']); ?></p>
				<p><strong>Student Part:</strong> <?php echo htmlspecialchars($student_info['Stud_Part']); ?></p>
			</div>

			<!-- Lab Selection -->
			<div>
				<h3>Select Lab</h3>
				<form method="GET">
					<select name="lab_id" onchange="this.form.submit()">
						<option value="">--Select a Lab--</option>
						<?php while ($lab = $result_labs->fetch_assoc()): ?>
							<option value="<?= $lab['Lab_ID']; ?>" <?= $selected_lab_id == $lab['Lab_ID'] ? 'selected' : ''; ?>>
								<?= htmlspecialchars($lab['Lab_Name']); ?>
							</option>
						<?php endwhile; ?>
					</select>
				</form>
			</div>

			<?php if ($selected_lab_id): ?>
				<!-- Statistics -->
				<div>
					<h3>Lab Statistics</h3>
					<p><strong>Total Entries:</strong> <?= $stats['total_entries'] ?? 0; ?></p>
					<p><strong>Last Entry:</strong> <?= $stats['last_entry'] ?? 'N/A'; ?></p>
					<p><strong>Average Duration:</strong> <?= round($stats['avg_duration'] ?? 0, 2); ?> minutes</p>
				</div>

				<!-- Logs Table -->
				<div>
					<h3>Student Logs</h3>
					<table>
						<thead>
							<tr>
								<th>Entry Date</th>
								<th>Entry Time</th>
								<th>Exit Time</th>
								<th>Duration (Minutes)</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($result_logs->num_rows > 0): ?>
								<?php while ($log = $result_logs->fetch_assoc()): ?>
									<tr>
										<td><?= htmlspecialchars($log['Entry_Date']); ?></td>
										<td><?= htmlspecialchars($log['Entry_StartTime']); ?></td>
										<td><?= htmlspecialchars($log['Entry_EndTime'] ?? 'N/A'); ?></td>
										<td><?= htmlspecialchars($log['duration'] ?? 'N/A'); ?></td>
									</tr>
								<?php endwhile; ?>
							<?php else: ?>
								<tr>
									<td colspan="4">No logs found for this lab.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
