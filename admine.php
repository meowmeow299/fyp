<?php
include("header.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $electionName = mysqli_real_escape_string($connect, $_POST['electionName']);
    $startTime = mysqli_real_escape_string($connect, $_POST['startTime']);
    $endTime = mysqli_real_escape_string($connect, $_POST['endTime']);

  
    $insertQuery = "INSERT INTO election (electionName, startTime, endTime) 
                    VALUES ('$electionName', '$startTime', '$endTime')";
    if (mysqli_query($connect, $insertQuery)) {
        echo "<script>alert('Election created successfully!'); window.location.href = 'admine.php';</script>";
    } else {
        echo "<script>alert('Error creating election: " . mysqli_error($connect) . "'); window.history.back();</script>";
    }
}





$query = "SELECT * FROM election ORDER BY startTime DESC";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Election</title>
    <link rel="stylesheet" href="election.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="logog.png" alt="UPTM Logo" class="logo">
            <div class="menu">
                <a href="adminhome.php"><button>Home</button></a>
                <a href="adminelection.php"><button>Election</button></a>
                <a href="adminv.php"><button>Voters</button></a>
                <a href="adminc.php"><button>Candidate</button></a>
                <a href="adminr.php"><button>View Results</button></a>
                <a href="adminlogout.php"><button>Logout</button></a>
            </div>
        </div>

        <h1>Create a New Election</h1>
        
        <form action="admine.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="electionName">Election Name</label>
                <input type="text" id="electionName" name="electionName" placeholder="Enter election name" required>
            </div>
            
            <div class="form-group">
                <label for="startTime">Start Time</label>
                <input type="datetime-local" id="startTime" name="startTime" required>
            </div>
            
            <div class="form-group">
                <label for="endTime">End Time</label>
                <input type="datetime-local" id="endTime" name="endTime" required>
            </div>
            
            <button type="submit" class="btn">Create Election</button>
        </form>

    
    </div>

    <script>
        
        function validateForm() {
            const startTime = new Date(document.getElementById('startTime').value);
            const endTime = new Date(document.getElementById('endTime').value);

            if (endTime <= startTime) {
                alert('End time must be after the start time!');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

<?php

mysqli_close($connect);
?>
