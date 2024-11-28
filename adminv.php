<?php

include("header.php");


$message = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['vId']) && isset($_POST['vStatus'])) {
        $vId = mysqli_real_escape_string($connect, $_POST['vId']);
        $vStatus = mysqli_real_escape_string($connect, $_POST['vStatus']);

        
        $updateQuery = "UPDATE voter SET vStatus = '$vStatus' WHERE vId = '$vId'";
        if (mysqli_query($connect, $updateQuery)) {
            $message = "<div class='success-box'>Voter status updated to '$vStatus'.</div>";
        } else {
            $message = "<div class='error-box'>Error updating voter status: " . mysqli_error($connect) . "</div>";
        }
    }

    
    if (isset($_POST['deleteVoter'])) {
        $vId = mysqli_real_escape_string($connect, $_POST['deleteVoter']);

        
        $deleteQuery = "DELETE FROM voter WHERE vId = '$vId'";
        if (mysqli_query($connect, $deleteQuery)) {
            $message = "<div class='success-box'>Voter with ID $vId has been deleted.</div>";
        } else {
            $message = "<div class='error-box'>Error deleting voter: " . mysqli_error($connect) . "</div>";
        }
    }

    
    if (isset($_POST['deleteAll'])) {
        $deleteAllQuery = "DELETE FROM voter";
        if (mysqli_query($connect, $deleteAllQuery)) {
            $message = "<div class='success-box'>All voters have been deleted.</div>";
        } else {
            $message = "<div class='error-box'>Error deleting all voters: " . mysqli_error($connect) . "</div>";
        }
    }
}


$query = "SELECT * FROM voter ORDER BY vId ASC";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter's List</title>
    <link rel="stylesheet" href="adminv.css">
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
        <h1>VOTER'S LIST</h1>

        
        <?php if (isset($message)) { echo $message; } ?>

        
        <table>
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row["vId"] . "</td>";
                    echo "<td>" . $row["vName"] . "</td>";
                    echo "<td>" . $row["vPhone"] . "</td>";
                    echo "<td>" . $row["vEmail"] . "</td>";
                    echo "<td>" . $row["vAddress"] . "</td>";
                    echo "<td>" . $row["vStatus"] . "</td>";
                    echo "<td>";

                    
                    if ($row["vStatus"] == "pending") {
                        echo "<form method='POST' action='adminv.php'>";
                        echo "<input type='hidden' name='vId' value='" . $row["vId"] . "'>";
                        echo "<button type='submit' name='vStatus' value='approved' class='approve-btn'>Approve</button>";
                        echo "<button type='submit' name='vStatus' value='rejected' class='reject-btn'>Reject</button>";
                        echo "</form>";
                    }

                    
                    if ($row["vStatus"] == "approved" || $row["vStatus"] == "rejected") {
                        echo "<form method='POST' action='adminv.php' style='display:inline;'>";
                        echo "<input type='hidden' name='deleteVoter' value='" . $row["vId"] . "'>";
                        echo "<button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this voter?\");'>Delete</button>";
                        echo "</form>";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No voters found</td></tr>";
            }
            ?>
            </tbody>
        </table>

        
        <form method="POST" action="adminv.php" style="margin-top: 20px;">
            <button type="submit" name="deleteAll" class="delete-all-btn" onclick="return confirm('Are you sure you want to delete all voters? This action cannot be undone!');">Delete All</button>
        </form>
    </div>
</body>
</html>

<?php

mysqli_close($connect);
?>
