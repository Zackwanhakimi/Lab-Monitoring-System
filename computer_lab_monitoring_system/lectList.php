<?php
session_start(); // Start session

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();    
}

include("connect.php");

// Fetch all lecturers from the lecturer table
$sql = "SELECT Lect_ID, Lect_Name FROM lecturer ORDER BY Lect_Name ASC";
$result = $conn->query($sql);

$lecturers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lecturers[] = $row;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Lecturer List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
    margin: 0 auto;
}
th, td {
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
tr:nth-child(even) {
    background-color: #f2f2f2;
}
tr:hover {
    background-color: #ddd;
}
.actions button, .actions a {
    margin: 0 5px;
}

nav#sidebar{
    position: fixed;
    height: 100vh;
    z-index: 100;
}
</style>
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
                  <li class="active">
	        	        <a><span class="fa fa-user mr-3"></span>Lecturer Manage</a>
	                </li>
                    <li class="active">
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

    <!-- Page Content -->
    <div id="content" class="p-4 p-md-5 pt-5">
        <h2 class="mb-4">LECTURER LIST</h2>
        <h3>ALL REGISTERED LECTURERS</h3>
        <?php if (!empty($lecturers)): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th style="text-align: center;">Lecturer Name</th>
                        <th>Lecturer ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lecturers as $index => $lecturer): ?>
                        <tr>
                            <td style="text-align: center;"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($lecturer['Lect_Name']) ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($lecturer['Lect_ID']) ?></td>
                            <td class="actions" style="text-align: center;">
                                <a href="LectUpdate.php?Lect_ID=<?= $lecturer['Lect_ID'] ?>" class="btn btn-warning btn-sm" title="Edit Lecturer">
                                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="LectRemove.php" method="post" style="display:inline;">
                                    <input type="hidden" name="Lect_ID" value="<?= $lecturer['Lect_ID'] ?>">
                                    <button type="submit" 
                                            class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to remove this lecturer?');" 
                                            title="Remove Lecturer">
                                        <i class="fa-sharp fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No lecturers registered yet.</p>
        <?php endif; ?>
        <?php $conn->close(); ?>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
