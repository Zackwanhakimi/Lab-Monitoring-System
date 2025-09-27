<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['Timetable_Day'];
    $startTime = $_POST['Timetable_StartTime'];
    $endTime = $_POST['Timetable_EndTime'];
    $className = $_POST['Timetable_ClassName'];

    // Validate input
    if (empty($day) || empty($startTime) || empty($endTime) || empty($className)) {
        $error = "All fields are required.";
    } elseif (strtotime($endTime) <= strtotime($startTime)) {
        $error = "End time must be after start time.";
    } else {
        // Insert into timetable
        $sql = "INSERT INTO timetable (Timetable_Day, Timetable_StartTime, Timetable_EndTime, Timetable_ClassName) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $day, $startTime, $endTime, $className);

        if ($stmt->execute()) {
            $success = "Class successfully registered.";
        } else {
            $error = "Failed to register class: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<style>
		/* General styling for the container */
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

/* Styling for the headings */
h1 {
  font-size: 20px;
  color: #333;
  text-transform: uppercase;
  font-weight: bold;
  margin-bottom: 10px;
}

h2 {
  font-size: 16px;
  color: #333;
  font-weight: bold;
  border-bottom: 2px solid #333;
  margin-bottom: 20px;
}

/* Input field styling */
label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
  color: #333;
}

input[type="text"] {
  width: 100%;
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 15px;
}

/* Submit button styling */
button {
  background-color: #333;
  color: #fff;
  font-size: 14px;
  font-weight: bold;
  text-transform: uppercase;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #555;
}

nav#sidebar{
  position: fixed;
  height: 100vh;
  z-index: 100;
}
</style>

<!doctype html>
<html lang="en">
<head>
  	<title>Sidebar 05</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
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
                    <li>
	        	        <a href="timetable.php"><span class="fa fa-table mr-3"></span> Timetable</a>
	                </li>
                  <li class="active">
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
    <h2>Register New Timetable Class</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"> <?= htmlspecialchars($success) ?> </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="Timetable_Day">Day</label>
            <select class="form-control" id="Timetable_Day" name="Timetable_Day" required>
                <option value="">Select Day</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
            </select>
        </div>

        <div class="form-group">
            <label for="Timetable_StartTime">Start Time</label>
            <input type="time" class="form-control" id="Timetable_StartTime" name="Timetable_StartTime" required>
        </div>

        <div class="form-group">
            <label for="Timetable_EndTime">End Time</label>
            <input type="time" class="form-control" id="Timetable_EndTime" name="Timetable_EndTime" required>
        </div>

         <div class="form-group">
            <label for="Timetable_ClassName">Class Name</label>
            <input type="text" class="form-control" id="Timetable_ClassName" name="Timetable_ClassName" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
        <a href="timetable.php" class="btn btn-secondary">Back to Timetable</a>
    </form>
  </div>
  <script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
