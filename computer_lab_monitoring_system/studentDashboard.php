<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['Stud_ID'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Fetch current student info
$student_id = $_SESSION['Stud_ID'];
$sql_student = "SELECT Stud_Name, Stud_Part FROM Student WHERE Stud_ID = '$student_id'";
$result_student = $conn->query($sql_student);
$student_info = $result_student->fetch_assoc();

$stud_id = $_SESSION['Stud_ID'];
$lab_id = isset($_GET['lab_id']) ? intval($_GET['lab_id']) : 1;
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y'); // Default to current year

// Fetch distinct years for dropdown
$sql_years = "SELECT DISTINCT YEAR(Entry_Date) AS year FROM Entry";
$result_years = $conn->query($sql_years);
$years = [];
while ($row = $result_years->fetch_assoc()) {
    $years[] = $row['year'];
}

// Fetch total entry by month for the selected year
$totalEntriesByMonth = [];
for ($month = 1; $month <= 12; $month++) {
    $sql = "SELECT COUNT(*) AS total 
            FROM Entry 
            WHERE MONTH(Entry_Date) = $month AND YEAR(Entry_Date) = $selected_year 
                  AND Stud_ID = $stud_id AND Lab_ID = $lab_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $totalEntriesByMonth[$month] = (int)$row['total'];
}

// Fetch average duration in lab by month for the selected year
$avgDurationByMonth = [];
for ($month = 1; $month <= 12; $month++) {
    $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, Entry_StartTime, Entry_EndTime)) AS avg_duration 
            FROM Entry 
            WHERE MONTH(Entry_Date) = $month AND YEAR(Entry_Date) = $selected_year 
                  AND Stud_ID = $stud_id AND Lab_ID = $lab_id AND Entry_EndTime IS NOT NULL";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $avgDurationByMonth[$month] = $row['avg_duration'] ? round($row['avg_duration']) : 0;
}

// Fetch percentage of entry by each lab
$labEntryPercentage = [];
$sql = "SELECT Lab_ID, COUNT(*) AS total 
        FROM Entry 
        WHERE YEAR(Entry_Date) = $selected_year AND Stud_ID = $stud_id 
        GROUP BY Lab_ID";
$result = $conn->query($sql);
$totalEntries = 0;
while ($row = $result->fetch_assoc()) {
    $totalEntries += $row['total'];
    $labEntryPercentage[$row['Lab_ID']] = $row['total'];
}
foreach ($labEntryPercentage as $key => $value) {
    $labEntryPercentage[$key] = round(($value / $totalEntries) * 100, 2);
}

// Fetch last entry
$sql = "SELECT Entry_StartTime, Entry_EndTime, Lab_ID 
        FROM Entry 
        WHERE Stud_ID = $stud_id 
        ORDER BY Entry_StartTime DESC LIMIT 1";
$result = $conn->query($sql);
$lastEntry = $result->fetch_assoc();

// Fetch total entry count for the selected year
$sql = "SELECT COUNT(*) AS total 
        FROM Entry 
        WHERE YEAR(Entry_Date) = $selected_year AND Stud_ID = $stud_id";
$result = $conn->query($sql);
$totalEntryCount = $result->fetch_assoc()['total'];

// Fetch entry logs for the selected year
$sql = "SELECT Entry_StartTime, Entry_EndTime, Lab_ID 
        FROM Entry 
        WHERE YEAR(Entry_Date) = $selected_year AND Stud_ID = $stud_id 
        ORDER BY Entry_StartTime DESC";
$entryLogs = $conn->query($sql);
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        #bar_chart, #line_chart, #pie_chart {
            width: 100%; /* Full width of the parent div */
            height: 500px; /* Fixed height for consistency */
            padding: 10px; /* Add padding for aesthetics */
            box-sizing: border-box; /* Include padding in element width */
        }
        <style>
	.table-container {
            width: 100%;
            overflow-x: auto; 
            margin: 0; 
            padding: 0; 
        }
	table{
		width: 100%;
		border-collapse: collapse;
		margin: 0 auto;
	}
	th, td{
		padding: 8px;
		border: 1px solid;
	}
	thead th {
    background-color: #4caf50; 
    color: white; 
    text-align: center;
    padding: 8px;
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
	.actions button, .actions a {
		margin: 0 5px;
	}
	
    </style>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawBarChart();
            drawLineChart();
            drawPieChart();
        }

        function drawBarChart() {
            const data = google.visualization.arrayToDataTable([
                ['Month', 'Entries'],
                <?php
                foreach ($totalEntriesByMonth as $month => $total) {
                    echo "['" . date('F', mktime(0, 0, 0, $month, 1)) . "', $total],";
                }
                ?>
            ]);
            const options = { title: 'Total Entry by Month', hAxis: { title: 'Month' }, vAxis: { title: 'Entries' } };
            const chart = new google.visualization.ColumnChart(document.getElementById('bar_chart'));
            chart.draw(data, options);
        }

        function drawLineChart() {
            const data = google.visualization.arrayToDataTable([
                ['Month', 'Average Duration'],
                <?php
                foreach ($avgDurationByMonth as $month => $avg) {
                    echo "['" . date('F', mktime(0, 0, 0, $month, 1)) . "', $avg],";
                }
                ?>
            ]);
            const options = { title: 'Average Duration in Lab by Month', hAxis: { title: 'Month' }, vAxis: { title: 'Duration (Minutes)' } };
            const chart = new google.visualization.LineChart(document.getElementById('line_chart'));
            chart.draw(data, options);
        }

        function drawPieChart() {
            const data = google.visualization.arrayToDataTable([
                ['Lab', 'Percentage'],
                <?php
                foreach ($labEntryPercentage as $lab => $percentage) {
                    echo "['Lab $lab', $percentage],";
                }
                ?>
            ]);
            const options = { title: 'Percentage of Entry by Lab', pieHole: 0.4 };
            const chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
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

        <!-- Page Content  -->
		<div id="content" class="p-4 p-md-5 pt-5">
        <h1>Student Statistics</h1>
    <form method="GET">
        <label for="year">Select Year:</label>
        <select name="year" id="year" onchange="this.form.submit()">
            <?php foreach ($years as $year): ?>
                <option value="<?= $year ?>" <?= $selected_year == $year ? 'selected' : '' ?>><?= $year ?></option>
            <?php endforeach; ?>
        </select>
        <label for="lab">Select Lab:</label>
        <select name="lab_id" id="lab" onchange="this.form.submit()">
            <option value="1" <?= $lab_id == 1 ? 'selected' : '' ?>>Makmal Komputer - Security & Networking</option>
            <option value="2" <?= $lab_id == 2 ? 'selected' : '' ?>>Makmal Komputer (AI)</option>
        </select>
    </form>
    <div id="bar_chart"></div>
    <div id="line_chart"></div>
    <div id="pie_chart"></div>
    <h2>Statistics Panel</h2>
    <p>Last Entry: <?= $lastEntry ? $lastEntry['Entry_StartTime'] : 'No record found' ?></p>
    <p>Total Entries in <?= $selected_year ?>: <?= $totalEntryCount ?></p>
    <h2>Entry Logs</h2>
    <table>
        <thead>
        <tr>
            <th>Entry Time</th>
            <th>Exit Time</th>
            <th>Lab</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($entryLogs->num_rows > 0) {
            while ($log = $entryLogs->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$log['Entry_StartTime']}</td>";
                echo "<td>" . ($log['Entry_EndTime'] ? $log['Entry_EndTime'] : 'Still in lab') . "</td>";
                echo "<td>Lab {$log['Lab_ID']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No records found</td></tr>";
        }
        ?>
        </tbody>
    </table>
    </div>
		</div>
	</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
