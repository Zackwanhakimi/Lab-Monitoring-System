<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $timetableName = $_POST['timetable_Name'];

    $sql = "DELETE FROM timetable WHERE timetable_Name = '$timetableName'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Delete entry
    $sql = "DELETE FROM timetable WHERE timetable_Name = '$timetableName'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Timetable entry removed successfully!'); window.location.href='timetable.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

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
		padding: 10px;
		border: 1px solid;
	}
	thead th {
    background-color: #4caf50; 
    color: white; 
    text-align: center;
    padding: 10px;
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

	/* dropdown style */

	.dropdown-container{
    display: none;
    padding-left: 8px;
}

	.dropdown-container a{
    font-size: medium;
}

	.fa-caret-down {
    float: right;
    padding-right: 8px;
}

.dropdown-btn{
	padding: 8px 8px 8px 32px;
    text-decoration: none;
    color: #818181;
    display: block;
    transition: 0.3s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    outline: none;
}
  </style>

<!doctype html>
<html lang="en">
<head>
    <title>Remove Timetable</title>
</head>
<body>
<div class="container mt-5">
    <h2>Remove Timetable Entry</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="timetableName">Timetable Name:</label>
            <input type="text" class="form-control" id="timetableName" name="timetable_Name" required>
        </div>
        <button type="submit" class="btn btn-danger">Remove Timetable Entry</button>
    </form>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
