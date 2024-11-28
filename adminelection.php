<?php
// Include the header and database connection
include("header.php");

// Handle deletion of an election and its associated candidates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['electionId'])) {
    $electionId = mysqli_real_escape_string($connect, $_POST['electionId']);

    // Start a transaction
    mysqli_begin_transaction($connect);

    try {
        // First, delete the candidates associated with the election
        $deleteCandidatesQuery = "DELETE FROM candidate WHERE electionId = '$electionId'";
        if (!mysqli_query($connect, $deleteCandidatesQuery)) {
            throw new Exception("Error deleting candidates: " . mysqli_error($connect));
        }

        // Then, delete the election itself
        $deleteElectionQuery = "DELETE FROM election WHERE electionId = '$electionId'";
        if (!mysqli_query($connect, $deleteElectionQuery)) {
            throw new Exception("Error deleting election: " . mysqli_error($connect));
        }

        // Commit the transaction if both queries succeed
        mysqli_commit($connect);
        echo "<script>alert('Election and associated candidates deleted successfully!'); window.location.href = 'adminelection.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        mysqli_rollback($connect);
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'adminelection.php';</script>";
    }
}

// Fetch all elections from the database
$query = "SELECT * FROM election ORDER BY startTime DESC";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Elections</title>
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

        <h1>Created Elections</h1>

        
        <table>
            <thead>
                <tr>
                    <th>Election Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row["electionName"] . "</td>";
                        echo "<td>" . $row["startTime"] . "</td>";
                        echo "<td>" . $row["endTime"] . "</td>";
                        echo "<td>";
                        
                        echo "<form method='POST' action='adminelection.php' style='display:inline;'>
                                <input type='hidden' name='electionId' value='" . $row['electionId'] . "'>
                                <button type='submit' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this election and all associated candidates?\");'>Delete</button>
                            </form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No elections found</td></tr>";
                }
                ?>
            </tbody>
        </table>

     
        <div class="add-election-btn">
            <a href="admine.php"><button>Create New Election</button></a>
        </div>
    </div>
</body>
</html>

<?php

mysqli_close($connect);
?>
