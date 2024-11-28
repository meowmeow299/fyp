<?php
include("header.php");

$electionsQuery = "SELECT electionId, electionName FROM election";
$electionsResult = mysqli_query($connect, $electionsQuery);


$selectedElectionId = isset($_GET['electionId']) ? $_GET['electionId'] : null;


if ($selectedElectionId) {
    $query = "SELECT * FROM candidate WHERE electionId = '$selectedElectionId' ORDER BY cId ASC";
} else {
    
    $query = "SELECT * FROM candidate WHERE electionId IS NULL ORDER BY cId ASC";
}
$result = mysqli_query($connect, $query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['cId']) && isset($_POST['cStatus']) && isset($_POST['electionId'])) {
        $cId = $_POST['cId'];
        $cStatus = $_POST['cStatus'];
        $electionId = $_POST['electionId'];  

        
        $cId = mysqli_real_escape_string($connect, $cId);
        $cStatus = mysqli_real_escape_string($connect, $cStatus);
        $electionId = mysqli_real_escape_string($connect, $electionId);

        
        $updateQuery = "UPDATE candidate SET cStatus = '$cStatus' WHERE cId = '$cId' AND electionId = '$electionId'";

        if (mysqli_query($connect, $updateQuery)) {
            
            $message = "<div class='success-box'>Candidate status updated to '$cStatus'.</div>";
        } else {
            
            $message = "<div class='error-box'>Error updating candidate status: " . mysqli_error($connect) . "</div>";
        }
    }

    
    if (isset($_POST['deleteCandidate']) && isset($_POST['electionId'])) {
        $cId = $_POST['deleteCandidate'];
        $electionId = $_POST['electionId'];

        
        $cId = mysqli_real_escape_string($connect, $cId);
        $electionId = mysqli_real_escape_string($connect, $electionId);

        
        $deleteQuery = "DELETE FROM candidate WHERE cId = '$cId' AND electionId = '$electionId'";

        if (mysqli_query($connect, $deleteQuery)) {
            
            $message = "<div class='success-box'>Candidate with ID $cId has been deleted.</div>";
        } else {
            
            $message = "<div class='error-box'>Error deleting candidate: " . mysqli_error($connect) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate's List</title>
    <link rel="stylesheet" href="adminc.css">
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

        <h1>CANDIDATE'S LIST</h1>

        
        <?php if (isset($message)) { echo $message; } ?>

       
        <form method="GET" action="adminc.php">
            <label for="electionId">Select Election:</label>
            <select name="electionId" id="electionId" onchange="this.form.submit()">
                <option value="">-- Select Election --</option>
                <?php while ($election = mysqli_fetch_assoc($electionsResult)): ?>
                    <option value="<?php echo $election['electionId']; ?>" 
                            <?php echo ($selectedElectionId == $election['electionId']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($election['electionName']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        
        <table>
            <thead>
                <tr>
                    <th>Picture</th>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Manifesto</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";

                        
                        $cPicPath = $row["cImage"]; 
                        if ($cPicPath) {
                            echo "<td><img src='$cPicPath' alt='Candidate Picture' width='100' height='100'></td>";
                        } else {
                            echo "<td>No picture</td>";
                        }

                        echo "<td>" . $row["cId"] . "</td>";
                        echo "<td>" . $row["cName"] . "</td>";
                        echo "<td>" . $row["cPhone"] . "</td>";
                        echo "<td>" . $row["cEmail"] . "</td>";
                        echo "<td>" . $row["cAddress"] . "</td>";

                        $cPicPath = $row["cManifesto"];  
                        if ($cPicPath) {
                            echo "<td><img src='$cPicPath' alt='Manifesto' width='100' height='100'></td>";
                        } else {
                            echo "<td>No picture</td>";
                        }
                        echo "<td>" . $row["cStatus"] . "</td>";
                        echo "<td>";

                        
                        if ($row["cStatus"] == "pending") {
                            echo "<form method='POST' action='adminc.php'>";
                            echo "<input type='hidden' name='cId' value='" . $row["cId"] . "'>";
                            echo "<input type='hidden' name='electionId' value='" . $selectedElectionId . "'>";  // Pass the selected electionId
                            echo "<button type='submit' name='cStatus' value='approved' class='approve-btn'>Approve</button>";
                            echo "<button type='submit' name='cStatus' value='rejected' class='reject-btn'>Reject</button>";
                            echo "</form>";
                        }

                        
                        if ($row["cStatus"] == "approved" || $row["cStatus"] == "rejected") {
                            echo "<form method='POST' action='adminc.php'>";
                            echo "<input type='hidden' name='deleteCandidate' value='" . $row["cId"] . "'>";
                            echo "<input type='hidden' name='electionId' value='" . $selectedElectionId . "'>";  // Pass the selected electionId
                            echo "<button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this candidate?\");'>Delete</button>";
                            echo "</form>";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No candidates found for this election</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php

mysqli_close($connect);
?>
