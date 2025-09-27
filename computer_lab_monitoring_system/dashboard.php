<?php
session_start(); // Start session

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();	
}

include 'connect.php';

// Fetch total number of students who have entered the lab
$sql_students = "SELECT COUNT(DISTINCT Stud_ID) AS total_students FROM Entry";
$result_students = $conn->query($sql_students);
$total_students = $result_students->fetch_assoc()['total_students'];

// Fetch frequent users (students who entered the lab more than once)
$sql_frequent_users = "SELECT Stud_ID, COUNT(*) AS entry_count 
                       FROM Entry 
                       GROUP BY Stud_ID 
                       HAVING COUNT(*) > 1";
$result_frequent_users = $conn->query($sql_frequent_users);

// Fetch average time spent in the lab
$sql_avg_time = "SELECT AVG(TIMESTAMPDIFF(SECOND, Entry_StartTime, Entry_EndTime)) AS avg_time 
                 FROM Entry WHERE Entry_EndTime IS NOT NULL";
$result_avg_time = $conn->query($sql_avg_time);
$avg_time_spent = $result_avg_time->fetch_assoc()['avg_time'];

// Get the selected year from the dropdown or default to the current year
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Fetch yearly data for line charts (entries for each month in both labs)
$lineData = [];
for ($month = 1; $month <= 12; $month++) {
    for ($lab = 1; $lab <= 2; $lab++) {
        $query = "SELECT COUNT(*) AS total FROM Entry 
                  WHERE MONTH(Entry_Date) = $month AND YEAR(Entry_Date) = $selectedYear AND Lab_ID = $lab";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $lineData[$lab][] = [(string)$month, (int)$row['total']];
    }
}

// Fetch data for student parts (entries for each part in each month)
$partData = [];
for ($month = 1; $month <= 12; $month++) {
    $query = "SELECT Student.Stud_Part, COUNT(*) AS total 
              FROM Entry 
              INNER JOIN Student ON Entry.Stud_ID = Student.Stud_ID 
              WHERE MONTH(Entry_Date) = $month AND YEAR(Entry_Date) = $selectedYear 
              GROUP BY Student.Stud_Part";
    $result = $conn->query($query);
    $partRow = [];
    while ($row = $result->fetch_assoc()) {
        $partRow[$row['Stud_Part']] = (int)$row['total'];
    }
    $partData[] = array_merge(['Month' => (string)$month], $partRow);
}

// Fetch data for pie chart (entry distribution by student part)
$pieData = [];
$query = "SELECT Student.Stud_Part, COUNT(*) AS total 
          FROM Entry 
          INNER JOIN Student ON Entry.Stud_ID = Student.Stud_ID 
          WHERE YEAR(Entry_Date) = $selectedYear 
          GROUP BY Student.Stud_Part";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $pieData[] = [$row['Stud_Part'], (int)$row['total']];
}

// Fetch total students entered today
$query = "SELECT COUNT(DISTINCT Stud_ID) as total_today FROM entry WHERE DATE(Entry_Date) = CURDATE()";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$totalToday = (int)$row['total_today'];

// Fetch average entries per day
$query = "SELECT COUNT(*) / COUNT(DISTINCT DATE(Entry_Date)) as avg_entries FROM entry";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$avgEntries = (float)$row['avg_entries'];

?>


<!doctype html>
<html lang="en">
<head>
  	<title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">

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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'line']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawLineCharts();
            drawPieChart();
        }

        function drawLineCharts() {
            // Line chart for Lab 1
            var data1 = new google.visualization.DataTable();
            data1.addColumn('string', 'Month');
            data1.addColumn('number', 'Total Entries');
            <?php foreach ($lineData[1] as $row) {
                echo "data1.addRow(['{$row[0]}', {$row[1]}]);";
            } ?>
            var options1 = {
                title: 'Total Entries in Makmal Komputer - Security & Networking by Month',
                hAxis: {title: 'Month'},
                vAxis: {title: 'Total Entries'}
            };
            var chart1 = new google.visualization.LineChart(document.getElementById('line_chart_lab1'));
            chart1.draw(data1, options1);

            // Line chart for Lab 2
            var data2 = new google.visualization.DataTable();
            data2.addColumn('string', 'Month');
            data2.addColumn('number', 'Total Entries');
            <?php foreach ($lineData[2] as $row) {
                echo "data2.addRow(['{$row[0]}', {$row[1]}]);";
            } ?>
            var options2 = {
                title: 'Total Entries in Makmal Komputer (AI) by Month',
                hAxis: {title: 'Month'},
                vAxis: {title: 'Total Entries'}
            };
            var chart2 = new google.visualization.LineChart(document.getElementById('line_chart_lab2'));
            chart2.draw(data2, options2);

            // Line chart for student parts
            var partData = new google.visualization.DataTable();
            partData.addColumn('string', 'Month');
            <?php
            $parts = array_keys($partData[0]);
            foreach ($parts as $index => $part) {
                if ($part !== 'Month') {
                    $partLabel = 'Part ' . ($index + 1); // Ensure labels start from Part 1
                    echo "partData.addColumn('number', '{$partLabel}');";
                }
            }
            ?>
            <?php
            // Add rows for each month and part
            foreach ($partData as $row) {
                $rowValues = "['{$row['Month']}'";
                foreach ($parts as $part) {
                    if ($part !== 'Month') {
                        $rowValues .= ", " . (isset($row[$part]) ? $row[$part] : 0);
                    }
                }
                $rowValues .= "]";
                echo "partData.addRow($rowValues);";
            }
            ?>
            var partOptions = {
                title: 'Total Entries by Student Part',
                hAxis: {title: 'Month'},
                vAxis: {title: 'Total Entries'},
            };
            var partChart = new google.visualization.LineChart(document.getElementById('line_chart_parts'));
            partChart.draw(partData, partOptions);
        }

        function drawPieChart() {
            var data = google.visualization.arrayToDataTable([
                ['Student Part', 'Total Entries'],
                <?php foreach ($pieData as $row) {
                    echo "['{$row[0]}', {$row[1]}],";
                } ?>
            ]);
            var options = {
                title: 'Entry Percentage by Student Part',
                pieHole: 0.4,
            };
            var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
            chart.draw(data, options);
        }
    </script>
</head>

<body>
    <!-- this is the sidebar -->
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
                  <li class="active">
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
    <div id="content" class="p-4 p-md-5 pt-5" style="flex-grow: 1;">
        <h1>Lab Access Statistics</h1>
        <div class="row">
            <div style="width: 50%; padding: 10px; ">
                <h6>Makmal Komputer - Security & Networking</h6>
                                <table>
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
                                                        WHERE e.Entry_EndTime IS NULL AND e.Lab_ID = 1
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
            <div style="width: 50%; padding: 10px;">
                <h6>Makmal AI</h6>
                            <table>
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
                                                    WHERE e.Entry_EndTime IS NULL AND e.Lab_ID = 2
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
        </div>
        
        <form method="get" action="Dashboard.php">
        <label for="year">Select Year:</label>
        <select id="year" name="year" onchange="this.form.submit()">
            <?php for ($year = 2024; $year <= date('Y'); $year++) { ?>
                <option value="<?php echo $year; ?>" <?php echo $year == $selectedYear ? 'selected' : ''; ?>><?php echo $year; ?></option>
            <?php } ?>
        </select>
    </form>

        <div class="row">
            <div id="line_chart_lab1" style="width: 50%; padding: 10px; height: 400px;"></div>
            <div id="line_chart_lab2" style="width: 50%; padding: 10px; height: 400px;"></div>
        </div>
        <div class="row">
            <div id="line_chart_parts" style="width: 50%; padding: 10px; height: 400px;"></div>
            <div id="pie_chart" style="width: 50%; padding: 10px; height: 400px;"></div>
        </div>
             
        <div><h2>Total Students Entered Lab Today: <?php echo $totalToday; ?></h2></div>
        <div><h2>Average Number of Entries Each Day: <?php echo round($avgEntries, 2); ?></h2></div>
                                
        <div class="stat">
            <h3>Total number of students who used the lab: <?php echo $total_students; ?></h3>
        </div>

        <div class="stat">
            <h3>Average Time Spent in Lab (in seconds): <?php echo $avg_time_spent; ?></h3>
        </div>

        <div class="stat">
    <h3>Top 10 Frequent Users:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center;">Rank</th>
                <th>Student Name</th>
                <th style="text-align: center;">Entries</th>
                <th style="text-align: center;">Total Time Spent (Minutes)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Optimized query to get the top 10 frequent users
            $sql_frequent_users = "
                SELECT 
                    Student.Stud_Name, 
                    Entry.Stud_ID, 
                    COUNT(Entry.Entry_ID) AS entry_count,
                    SUM(TIMESTAMPDIFF(MINUTE, Entry_StartTime, Entry_EndTime)) AS total_minutes
                FROM Entry
                JOIN Student ON Entry.Stud_ID = Student.Stud_ID
                WHERE Entry_EndTime IS NOT NULL
                GROUP BY Entry.Stud_ID
                ORDER BY entry_count DESC, total_minutes DESC
                LIMIT 10
            ";

            $result_frequent_users = $conn->query($sql_frequent_users);

            // Check if there are results
            if ($result_frequent_users->num_rows > 0) {
                $rank = 1;
                while ($row = $result_frequent_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style='text-align: center;'>" . $rank++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['Stud_Name']) . "</td>";
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row['entry_count']) . "</td>";
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row['total_minutes'] ?? 0) . " minutes</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No data available.</td></tr>";
            }
            ?>
        </tbody>
    </table>
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
    <?php
$conn->close();
?>
  </body>
</html>