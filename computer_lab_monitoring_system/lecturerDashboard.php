<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['Lect_ID'])) {
    // Redirect to login page if not logged in
    header("Location: loginLect.php");
    exit();
}

include 'connect.php';

// Fetch current Lecturer info
$lecturer_id = $_SESSION['Lect_ID'];
$lecturer_name = $_SESSION['Lect_Name'];

$selectedLab = isset($_POST['Lab_ID']) ? intval($_POST['Lab_ID']) : 1;
$selectedYear = isset($_POST['Year']) ? intval($_POST['Year']) : date("Y");

$labName = "Unknown Lab";

// Get Lab Name
$sql = "SELECT Lab_Name FROM Lab WHERE Lab_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $selectedLab);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $labName = $row['Lab_Name'];
}

// Total entries for the selected lab and year
$sqlTotalEntries = "
    SELECT COUNT(*) AS total_entries 
    FROM Entry 
    WHERE Lab_ID = $selectedLab AND YEAR(Entry_Date) = $selectedYear";
$resultTotalEntries = $conn->query($sqlTotalEntries);
$totalEntries = $resultTotalEntries->fetch_assoc()['total_entries'] ?? 0;

// Entries by month for the selected lab and year
$sqlEntriesByMonth = "
    SELECT MONTH(Entry_Date) AS month, COUNT(*) AS total
    FROM Entry
    WHERE Lab_ID = $selectedLab AND YEAR(Entry_Date) = $selectedYear
    GROUP BY MONTH(Entry_Date)
    ORDER BY MONTH(Entry_Date)";
$resultEntriesByMonth = $conn->query($sqlEntriesByMonth);
$entriesByMonth = [];
while ($row = $resultEntriesByMonth->fetch_assoc()) {
    $entriesByMonth[] = [date('F', mktime(0, 0, 0, $row['month'], 10)), $row['total']];
}

// Average duration by month for the selected lab and year
$sqlAvgDurationByMonth = "
    SELECT MONTH(Entry_Date) AS month, AVG(TIMESTAMPDIFF(MINUTE, Entry_StartTime, Entry_EndTime)) AS avg_duration
    FROM Entry
    WHERE Lab_ID = $selectedLab AND YEAR(Entry_Date) = $selectedYear AND Entry_EndTime IS NOT NULL
    GROUP BY MONTH(Entry_Date)
    ORDER BY MONTH(Entry_Date)";
$resultAvgDurationByMonth = $conn->query($sqlAvgDurationByMonth);
$avgDurationByMonth = [];
while ($row = $resultAvgDurationByMonth->fetch_assoc()) {
    $avgDurationByMonth[] = [date('F', mktime(0, 0, 0, $row['month'], 10)), $row['avg_duration']];
}

// Top 10 students by entry count for the selected year
$sqlTopStudents = "
    SELECT Student.Stud_Name, COUNT(*) AS entry_count
    FROM Entry
    JOIN Student ON Entry.Stud_ID = Student.Stud_ID
    WHERE Lab_ID = $selectedLab AND YEAR(Entry_Date) = $selectedYear
    GROUP BY Entry.Stud_ID
    ORDER BY entry_count DESC
    LIMIT 10";
$resultTopStudents = $conn->query($sqlTopStudents);

// Latest 20 entry logs for the selected year
$sqlEntryLog = "
    SELECT Entry.Entry_StartTime, Entry.Entry_EndTime, TIMESTAMPDIFF(MINUTE, Entry_StartTime, Entry_EndTime) AS duration,
           Entry.Entry_Date, Student.Stud_Name
    FROM Entry
    JOIN Student ON Entry.Stud_ID = Student.Stud_ID
    WHERE Lab_ID = $selectedLab AND YEAR(Entry_Date) = $selectedYear
    ORDER BY Entry.Entry_ID DESC
    LIMIT 20";
$resultEntryLog = $conn->query($sqlEntryLog);
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
	<style>
        #bar_chart, #line_chart, #pie_chart {
            width: 100%;
            height: 500px;
        }
        .stat-panel {
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawBarChart();
            drawLineChart();
        }

        function drawBarChart() {
            var data = google.visualization.arrayToDataTable([
                ['Month', 'Entries'],
                <?php foreach ($entriesByMonth as $row) {
                    echo "['{$row[0]}', {$row[1]}],";
                } ?>
            ]);
            var options = {title: 'Total Entries by Month'};
            var chart = new google.visualization.BarChart(document.getElementById('bar_chart'));
            chart.draw(data, options);
        }

        function drawLineChart() {
            var data = google.visualization.arrayToDataTable([
                ['Month', 'Average Duration (minutes)'],
                <?php foreach ($avgDurationByMonth as $row) {
                    echo "['{$row[0]}', {$row[1]}],";
                } ?>
            ]);
            var options = {title: 'Average Duration in Lab by Month'};
            var chart = new google.visualization.LineChart(document.getElementById('line_chart'));
            chart.draw(data, options);
        }
    </script>
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
			<h1><a href="index.html" class="logo"><?php echo htmlspecialchars($lecturer_name); ?><span><?php echo htmlspecialchars($lecturer_id); ?><span>Lecturer</span></a></h1>
				<ul class="list-unstyled components mb-5">
					<li class="active">
						<a href="lecturerDashboard.php"><span class="fa fa-home mr-3"></span> Home</a>
					</li>
                    <li>
						<a href="lecturerTimetable.php"><span class="fa fa-home mr-3"></span> Timetable</a>
					</li>
					<li>
						<a href="lecturerInfo.php"><span class="fa fa-home mr-3"></span> Your Info</a>
					</li>
					<li>
						<a href='logout.php'><span class="fa fa-paper-plane mr-3"></span> Log Out</a>
					</li>
				</ul>
			</div>
    	</nav>

        <!-- Page Content  -->
		<div id="content" class="p-4 p-md-5 pt-5">
		<h1>Welcome, <?= htmlspecialchars($lecturer_name); ?></h1>

    <form method="post" action="">
        <label for="Lab_ID">Select Lab:</label>
        <select name="Lab_ID" id="Lab_ID" onchange="this.form.submit()">
            <option value="1" <?= $selectedLab == 1 ? 'selected' : '' ?>>Makmal Komputer - Security & Networking</option>
            <option value="2" <?= $selectedLab == 2 ? 'selected' : '' ?>>Makmal Komputer (AI)</option>
        </select>

    <label for="Year">Select Year:</label>
            <select name="Year" id="Year" onchange="this.form.submit()">
                <?php for ($year = 2024; $year <= date("Y"); $year++): ?>
                    <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>><?= $year ?></option>
                <?php endfor; ?>
            </select>
        </form>


        <h4>Lab Statistics for <?= htmlspecialchars($labName) ?> for Year <?= $selectedYear ?></h4>
    <div>
    <table><h5 style="text-align: center">Current Student in Lab</h5>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Student ID</th>
            <th>Entry Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Update the query to fetch current students ordered by latest entry
        $sql_current_students = "SELECT s.Stud_Name, e.Stud_ID, e.Entry_StartTime 
                                 FROM Entry e
                                 INNER JOIN Student s ON e.Stud_ID = s.Stud_ID
                                 WHERE e.Entry_EndTime IS NULL AND e.Lab_ID = $selectedLab
                                 ORDER BY e.Entry_StartTime DESC";

        $result_current_students = $conn->query($sql_current_students);

        if ($result_current_students->num_rows > 0): 
            while ($row = $result_current_students->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Stud_Name']); ?></td>
                    <td><?= htmlspecialchars($row['Stud_ID']); ?></td>
                    <td><?= htmlspecialchars($row['Entry_StartTime']); ?></td>
                </tr>
            <?php endwhile; 
        else: ?>
            <tr>
                <td colspan="3">No students currently in the lab.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
    </div>
        <div id="bar_chart" style="width: 100%;"></div>
        <div id="line_chart" style="width: 100%; height: 500px;"></div>
    
    <h4 style="text-align: center">Top 10 Students with Most Entries</h4>
    <table>
        <tr>
            <th>Rank</th>
            <th>Student Name</th>
            <th>Entry Count</th>
        </tr>
        <?php $rank = 1; while ($row = $resultTopStudents->fetch_assoc()) : ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($row['Stud_Name']); ?></td>
                <td><?= $row['entry_count']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h4 style="text-align: center; margin-top: 20px;">Latest 20 Entry Logs</h4>
    <table>
        <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Duration (minutes)</th>
            <th>Student Name</th>
        </tr>
        <?php while ($row = $resultEntryLog->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['Entry_Date']; ?></td>
                <td><?= date('g:i A', strtotime($row['Entry_StartTime'])); ?></td>
                <td><?= $row['Entry_EndTime'] ? date('g:i A', strtotime($row['Entry_EndTime'])) : 'In Progress'; ?></td>
                <td><?= $row['duration'] ?: 'N/A'; ?></td>
                <td><?= htmlspecialchars($row['Stud_Name']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
		</div>
	</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
