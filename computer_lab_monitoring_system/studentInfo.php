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
        body {
            font-family: Arial, sans-serif;
        }

        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
        }

        /* Popup Box */
        .popup {
            background: #fff;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
        }

        .popup input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .popup button {
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup button:hover {
            background: #0056b3;
        }

        .close-btn {
            background: red;
            color: #fff;
            float: right;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
		button {
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
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
			<h2 class="mb-4">Your Info</h2>

			<!-- Current Student Info -->
			<div>
				<p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>
				<p><strong>Student Name:</strong> <?php echo htmlspecialchars($student_info['Stud_Name']); ?></p>
				<p><strong>Student Part:</strong> <?php echo htmlspecialchars($student_info['Stud_Part']); ?></p>
			</div>
			<div>
			<div><button id="openPopup">Change Password</button></div>

			<div id="passwordPopup" class="overlay">
				<div class="popup">
					<button class="close-btn" id="closePopup">X</button>
					<h2>Change Password</h2>
					<form id="changePasswordForm">
						<input type="password" id="currentPassword" name="currentPassword" placeholder="Current Password" required>
						<input type="password" id="newPassword" name="newPassword" placeholder="New Password" required>
						<input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
						<button type="submit">Change Password</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
	<script>
        const popup = document.getElementById('passwordPopup');
        const openPopup = document.getElementById('openPopup');
        const closePopup = document.getElementById('closePopup');

        openPopup.addEventListener('click', () => popup.style.display = 'block');
        closePopup.addEventListener('click', () => popup.style.display = 'none');

        window.addEventListener('click', (e) => {
            if (e.target === popup) popup.style.display = 'none';
        });
		const form = document.getElementById('changePasswordForm');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const response = await fetch('changePassword.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        alert(result.message);

        if (result.status === 'success') {
            document.getElementById('passwordPopup').style.display = 'none';
            form.reset();
        }
    });
    </script>
  </body>
</html>
