<?php
include 'connect.php';
session_start(); // Start the session

// Initialize a variable for messages
$message = "";

// Check for selected lab in the query string and update the session
if (isset($_GET['lab_id'])) {
    $_SESSION['selected_lab_id'] = intval($_GET['lab_id']);
}

// Get the current selected lab ID from the session, default to 1 if not set
$selected_lab_id = $_SESSION['selected_lab_id'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stud_id = $_POST['stud_id'];
    $lab_id = $_POST['lab_id']; // Capture the selected lab ID

    // Sanitize inputs
    $stud_id = intval($stud_id); // Ensure only the first 10 characters
    $lab_id = intval($lab_id);

    // Check if student ID exists
    $check_student = "SELECT * FROM Student WHERE Stud_ID = '$stud_id'";
    $result = $conn->query($check_student);

    if ($result->num_rows == 0) {
        // Invalid student ID
        $message = "Invalid Student ID.";
    } else {
        // Check for an open entry (no exit time)
        $check_open_entry = "SELECT * FROM Entry WHERE Stud_ID = '$stud_id' AND Lab_ID = $lab_id AND Entry_EndTime IS NULL";
        $result_open_entry = $conn->query($check_open_entry);

        if ($result_open_entry->num_rows > 0) {
            // Open entry found, record exit time
            $update_exit = "UPDATE Entry 
                            SET Entry_EndTime = NOW() 
                            WHERE Stud_ID = '$stud_id' AND Lab_ID = $lab_id AND Entry_EndTime IS NULL";
            if ($conn->query($update_exit) === TRUE) {
                $message = "Exit time recorded successfully.";
            } else {
                $message = "Error updating record: " . $conn->error;
            }
        } else {
            // No open entry, record entry time
            $insert_entry = "INSERT INTO Entry (Entry_Date, Entry_StartTime, Lab_ID, Stud_ID) 
                            VALUES (CURDATE(), NOW(), $lab_id, '$stud_id')";
            if ($conn->query($insert_entry) === TRUE) {
                $message = "Entry time recorded successfully.";
            } else {
                $message = "Error inserting record: " . $conn->error;
            }
        }
    }
}

// Fetch labs
$labs_query = "SELECT Lab_ID, Lab_Name FROM Lab";
$result_labs = $conn->query($labs_query);

// Fetch current students in the selected lab
$sql_current_students = "SELECT e.Entry_ID, e.Stud_ID, s.Stud_Name, e.Entry_StartTime 
                         FROM Entry e
                         JOIN Student s ON e.Stud_ID = s.Stud_ID
                         WHERE e.Lab_ID = $selected_lab_id AND e.Entry_EndTime IS NULL";
$result_current_students = $conn->query($sql_current_students);
?>


<!doctype html>
<html lang="en">
<head>
  	<title>Entry/Exit Record</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="css/EntryExit.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
    <style>
        .lab-tabs {
    margin-bottom: 20px;
}
.lab-tabs a {
    padding: 10px 20px;
    text-decoration: none;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    margin-right: 10px;
}
.lab-tabs a.active {
    background-color: #0056b3;
}
table {
    width: 100%;
    border-collapse: collapse;
}
table th, table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
table th {
    background-color: #f2f2f2;
}
form {
    margin-bottom: 20px;
}
form label, form input, form button {
    display: block;
    margin: 10px 0;
}
form input, form button {
    padding: 10px;
    width: 100%;
    max-width: 300px;
}
.notification {
    color: green;
    margin-bottom: 20px;
}
    </style>
</head>

<body>
    <!-- Page Content  -->
    <div id="content" class="p-4 p-md-5 pt-5">
    <div class="wrapper">
        <h2>Record Lab Entry/Exit</h2>
        
    <!-- Lab Tabs -->
    <div class="lab-tabs">
        <?php while ($lab = $result_labs->fetch_assoc()): ?>
            <a href="EntryExit.php?lab_id=<?= $lab['Lab_ID']; ?>" 
               class="<?= $selected_lab_id == $lab['Lab_ID'] ? 'active' : ''; ?>">
                <?= htmlspecialchars($lab['Lab_Name']); ?>
            </a>
        <?php endwhile; ?>
    </div>

    <!-- Form for Student ID -->
    <form id="entryForm" action="EntryExit.php" method="POST">
        <input type="hidden" name="lab_id" value="<?= $selected_lab_id; ?>">
        <label for="stud_id">Enter Your Student ID:</label>
        <input type="text" id="stud_id" name="stud_id" required maxlength="10">
        <button type="submit">Submit</button>
        <p><?php echo $message; ?></p> <!-- Display error or success messages -->
    </form>


<!-- Display current students in lab -->
<h3>Current Students in Lab:</h3>
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
                                 WHERE e.Entry_EndTime IS NULL AND e.Lab_ID = $selected_lab_id
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

<?php
$conn->close();
?>

<script>
    // Autofocus on the Student ID field
    document.addEventListener("DOMContentLoaded", function () {
        const studIdField = document.getElementById("stud_id");
        if (studIdField) {
            studIdField.focus();
        }
    });
</script>

<script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>