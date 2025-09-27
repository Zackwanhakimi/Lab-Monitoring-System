<?php
session_start(); // Start session

// Check if user is logged in
if (!isset($_SESSION['Admin_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();	
}

$status = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

include("connect.php");
?>

<script>
  function validateForm(event) {
    const Stud_Name = document.querySelector('input[name="Stud_Name"]').value.trim();
    const Stud_ID = document.querySelector('input[name="Stud_ID"]').value.trim();
    const Stud_Part = document.querySelector('input[name="Stud_Part"]').value.trim();

    const nameRegex = /^[a-zA-Z\s@/]+$/;  // Only letters and spaces
    const idRegex = /^\d{10}$/;        // Exactly 10 digits
    const partRegex = /^[1-7]$/;       // Only numbers 1-7

    if (!nameRegex.test(Stud_Name)) {
      alert("Student Name must contain only letters.");
      event.preventDefault();
      return false;
    }

    if (!idRegex.test(Stud_ID)) {
      alert("Student ID must be numbers only and exactly 10 digits.");
      event.preventDefault();
      return false;
    }

    if (!partRegex.test(Stud_Part)) {
      alert("Student Part must be a number between 1 and 7.");
      event.preventDefault();
      return false;
    }

    return true; // Allow form submission
  }
</script>

<script>
  function showNotification() {
    const status = "<?php echo $status; ?>";
    const message = "<?php echo $message; ?>";

    if (status === 'success') {
      alert('Registration successful!');
    } else if (status === 'error') {
      alert('Error: ' + decodeURIComponent(message));
    }
  }
</script>

<!doctype html>
<html lang="en">
  <head>
  	<title>Student Register</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
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

/* Submit button styling */
.submit-btn {
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

.submit-btn:hover {
  background-color: #555;
}

nav#sidebar{
    position: fixed;
    height: 100vh;
    z-index: 100;
}

</style>

  <body onload="showNotification()">
		
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
                    <li class="active">
	        	        <a><span class="fa fa-user mr-3"></span>Student Manage</a>
	                </li>
                    <li>
                        <a href="studList.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Student List</a>
	                </li>
                    <li class="active">
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
        <form action="dbstudent.php" method="post" onsubmit="return validateForm(event)">
          <div class="form">
      <div id="content" class="p-4 p-md-5 pt-5">
        <h2 class="mb-4">REGISTER NEW STUDENT</h2>
		<div class="container">
			<h2 class="headname">NEW STUDENT FORM</h2>
			<p>STUDENT NAME: <input type="text" name="Stud_Name"></input></p>
			<p>STUDENT ID: <input type="text" name="Stud_ID"></input></p>
      <p>STUDENT PART: <select name="Stud_Part" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
        </select></input></p>
			<button class="submit-btn">SUBMIT</button>
		</div>
			</div>
		</form>
      </div>
		</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

    <script>
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