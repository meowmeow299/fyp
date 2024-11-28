<?php
include("header.php");

// Fetch all elections for the dropdown
$electionsQuery = "SELECT electionId, electionName FROM election";
$electionsResult = $connect->query($electionsQuery);

// Get the selected election from GET request
$selectedElectionId = isset($_GET['electionId']) ? $_GET['electionId'] : null;

// Initialize variables
$electionName = "Select an Election";
$results = [];

if ($selectedElectionId) {
    // Fetch the name of the selected election
    $electionQuery = "SELECT electionName FROM election WHERE electionId = '$selectedElectionId'";
    $electionResult = $connect->query($electionQuery);

    if ($electionResult && $electionResult->num_rows > 0) {
        $election = $electionResult->fetch_assoc();
        $electionName = $election['electionName'];
    }

    // Fetch results for the selected election
    $resultsQuery = "SELECT c.cId, c.cName, c.cPhone, c.cEmail, c.cManifesto, c.cVoting, c.cImage
                     FROM candidate c
                     WHERE c.electionId = '$selectedElectionId'
                     ORDER BY c.cVoting DESC";
    $resultsResult = $connect->query($resultsQuery);

    if ($resultsResult && $resultsResult->num_rows > 0) {
        $results = $resultsResult->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link rel="stylesheet" href="result.css">
</head>
<body>
<main>
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
    </div>

    <h1>Election Results</h1>

    
    <div class="election-form">
        <form method="GET" action="adminr.php">
            <label for="electionId">Select Election:</label>
            <select name="electionId" id="electionId" onchange="this.form.submit()">
                <option value="">-- Select Election --</option>
                <?php while ($election = $electionsResult->fetch_assoc()): ?>
                    <option value="<?php echo $election['electionId']; ?>" 
                            <?php echo ($selectedElectionId == $election['electionId']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($election['electionName']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <?php if ($selectedElectionId): ?>
        <h2><?php echo htmlspecialchars($electionName); ?></h2>

        <?php if (!empty($results)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Manifesto</th>
                        <th>Total Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $rank = 1;
                        foreach ($results as $result): 
                    ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($result['cId']); ?></td>
                            <td><?php echo htmlspecialchars($result['cName']); ?></td>
                            <td><?php echo htmlspecialchars($result['cPhone']); ?></td>
                            <td><?php echo htmlspecialchars($result['cEmail']); ?></td>
                            <td>
                                <a href="#manifesto-<?php echo $result['cId']; ?>">
                                    <img src="<?php echo htmlspecialchars($result['cManifesto'] ?: 'candidate_placeholder.jpg'); ?>" 
                                         alt="Manifesto Poster" class="manifesto" height="100">
                                </a>
                                <div id="manifesto-<?php echo $result['cId']; ?>" class="lightbox">
                                    <a href="#" class="close">&times;</a>
                                    <img src="<?php echo htmlspecialchars($result['cManifesto'] ?: 'candidate_placeholder.jpg'); ?>" 
                                         alt="Manifesto Poster">
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($result['cVoting']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center; color: #555;">No candidates or results available for this election.</p>
        <?php endif; ?>
    <?php endif; ?>

    <?php $connect->close(); ?>
</main>
</body>
</html>
