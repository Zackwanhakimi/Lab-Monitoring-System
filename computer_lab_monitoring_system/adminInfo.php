<?php 
session_start();

 // Check if user is logged in
 if (!isset($_SESSION['Admin_ID'])) {
     // Redirect to login page if not logged in
     header("Location: loginAdmin.php");
     exit();	
 }

include 'connect.php';

//Fetch current Admin info
$Admin_id = $_SESSION['Admin_ID'];
$Admin_name = $_SESSION['Admin_name'];
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
	        	        <a href="timetable.php"><span class="fa fa-table mr-3"></span> Timetable </a>
	                </li>
                    <li>
	        	        <a href="timetableRegister.php"><span class="fa mr-3"></span><span class="fa fa-plus mr-3"></span> Add new class to Timetable</a>
	                </li>
                    <li class="active">
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
			<h2 class="mb-4">Your Info</h2>

			<!-- Current Admin Info -->
			<div>
				<p>
					<strong>Admin ID:</strong> <?php echo htmlspecialchars($Admin_id); ?><br>
					<strong>Admin name:</strong> <?php echo htmlspecialchars($Admin_name); ?>
				</p>
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
