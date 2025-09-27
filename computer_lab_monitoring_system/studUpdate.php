<?php
session_start(); // Start session

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();	
}

include("connect.php");


$Stud_ID = isset($_GET['Stud_ID']) ? intval($_GET['Stud_ID']) : 0;
if ($Stud_ID <= 0) {
    echo "<script>
            alert('Invalid Student ID.');
            window.location.href = 'studList.php';
          </script>";
    exit();
}

// Fetch the student's details for pre-filling the form
$sql = "SELECT Stud_Name, Stud_ID, Stud_Part FROM student WHERE Stud_ID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param("i", $Stud_ID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
  echo "<script>
          alert('Student not found.');
          window.location.href = 'studList.php';
        </script>";
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Stud_Name = trim($_POST['Stud_Name']);
    $Updated_Stud_ID = intval($_POST['Stud_ID']); //Allow studentID edit
    $Stud_Part = (int)$_POST['Stud_Part'];

    // Input validation
    if (!preg_match("/^[a-zA-Z\s]+$/", $Stud_Name)) {
      echo "<script>
              alert('Invalid student name. Only letters and spaces are allowed.');
              window.history.back();
            </script>";
      exit();
    }

    if (!preg_match("/^\d{10}$/", $Updated_Stud_ID)) {
      echo "<script>
              alert('Invalid Student ID. It must be numbers only and exactly 10 digits.');
              window.history.back();
            </script>";
      exit();
    }

    if ($Stud_Part < 1 || $Stud_Part > 7) {
      echo "<script>
              alert('Invalid Student Part. It must be between 1 and 7.');
              window.history.back();
            </script>";
      exit();
    }

    // Update query
    $updateSQL = "UPDATE student SET Stud_Name = ?, Stud_ID = ?, Stud_Part = ? WHERE Stud_ID = ?";
    $updateStmt = $conn->prepare($updateSQL);
    if (!$updateStmt) {
      die("Failed to prepare update statement: " . $conn->error);
    }
    $updateStmt->bind_param("siii", $Stud_Name, $Updated_Stud_ID, $Stud_Part, $Stud_ID);

    if ($updateStmt->execute()) {
      echo "<script>
              alert('Student information successfully updated!');
              window.location.href = 'studList.php';
            </script>";
      exit();
    } else {
      echo "<script>
              alert('Error updating record. Please try again.');
              window.history.back();
            </script>";
      exit();
    }

    $updateStmt->close();
}

$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">
  <head>
  	<title>Sidebar 05</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
		
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/style.css">
  </head>
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

nav#sidebar{
  position: fixed;
  height: 100vh;
  z-index: 100;
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
	                    <a href="dashboard.php"><span class="fa fa-home mr-3"></span> Lab Statistics</a>
	                </li>
                    <li>
	        	        <a href="labLogs.php"><span class="fa fa-user mr-3"></span> Entry/Exit Logs</a>
	                </li>
                    <li>
	        	        <a href="studList.php"><span class="fa fa-user mr-3"></span> Student List</a>
	                </li>
                    <li>
	        	        <a href="studRegister.php"><span class="fa fa-user mr-3"></span> Register New Student</a>
	                </li>
                    <li class="active">
	        	        <a href="studUpdate.php"><span class="fa fa-user mr-3"></span> Update Student Info</a>
	                </li>
                    <li>
	        	        <a href="lectList.php"><span class="fa fa-user mr-3"></span> Lecturer List</a>
	                </li>
                    <li>
	        	        <a href="lectRegister.php"><span class="fa fa-user mr-3"></span> Register New Lecturer</a>
	                </li>
                    <li>
	        	        <a href="timetable.php"><span class="fa fa-user mr-3"></span> Timetable</a>
	                </li>
                    <li>
                        <a href='logout.php'><span class="fa fa-paper-plane mr-3"></span> Log Out</a>
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
        <form method="post">
          <div id="content" class="p-4 p-md-5 pt-5">
            <h2 class="mb-4">UPDATE STUDENT INFORMATIONS</h2>
		        <div class="container">
              <h2>UPDATE STUDENT FORM</h2>
              <p>STUDENT NAME: <input type="text" name="Stud_Name" value="<?= htmlspecialchars($student['Stud_Name']) ?>" required></input></p>
              <p>STUDENT ID: <input type="text" name="Stud_ID" value="<?= $student['Stud_ID'] ?>" required></input></p>
              <p>STUDENT PART: <input type="number" name="Stud_Part" value="<?= $student['Stud_Part'] ?>" required min="1" max="7"></input></p>
              <button type="submit">UPDATE</button>
            </div>
          </div>
        </form>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>