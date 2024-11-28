<?php
session_start();

include("header.php");


if (!isset($_SESSION['vId'])) {
    header("Location: vlogin.php"); 
    exit();
}

$voterId = $_SESSION['vId']; 


$error_message = "";


$electionsQuery = "SELECT electionId, electionName, startTime, endTime 
                    FROM election 
                    WHERE NOW() BETWEEN startTime AND endTime";
$electionsResult = $connect->query($electionsQuery);

$selectedElectionId = null;
$candidatesResult = null;
$voteMessage = "";
$showTable = false;
$electionStatusMessage = ''; 


if (isset($_GET['electionId'])) {
    $selectedElectionId = mysqli_real_escape_string($connect, $_GET['electionId']);
    

    $electionQuery = "SELECT electionName, startTime, endTime FROM election WHERE electionId = '$selectedElectionId'";
    $electionResult = $connect->query($electionQuery);
    
    if ($electionResult && $electionResult->num_rows > 0) {
        $election = $electionResult->fetch_assoc();
        
    
        $electionStatusMessage = "";
        $candidatesQuery = "SELECT * FROM candidate WHERE electionId = '$selectedElectionId'";
        $candidatesResult = $connect->query($candidatesQuery);
        $showTable = true;

   
        $checkVoteStatusQuery = "SELECT * FROM votes WHERE vId = '$voterId' AND electionId = '$selectedElectionId'";
        $checkVoteStatusResult = $connect->query($checkVoteStatusQuery);

        if ($checkVoteStatusResult && $checkVoteStatusResult->num_rows > 0) {
            $showTable = false;
            $voteMessage = "You have already voted in this election!";
        }
    }
}


if (isset($_POST['vote']) && $selectedElectionId) {
    $cId = $_POST['cId'];
    $cId = mysqli_real_escape_string($connect, $cId);

    $voteQuery = "INSERT INTO votes (vId, electionId, cId) VALUES ('$voterId', '$selectedElectionId', '$cId')";
    if ($connect->query($voteQuery)) {
        $updateVoteQuery = "UPDATE candidate SET cVoting = cVoting + 1 WHERE cId = '$cId'";
        $connect->query($updateVoteQuery);
        echo "<script>alert('Your vote has been cast successfully.'); window.location.href = 'vvote.php';</script>";
    } else {
        echo "<script>alert('Error voting: " . $connect->error . "'); window.location.href = 'vvote.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for Candidates</title>
    <link rel="stylesheet" href="voting.css">
</head>
<body>

<div class="container">
    <div class="header">
        <img src="logog.png" alt="UPTM Logo" class="logo">
        <div class="menu">
           <a href="vhome.php"><button>Home</button></a>
            <a href="vvote.php"><button>Vote</button></a>
            <a href="vresult.php"><button>View Results</button></a>
            <a href="vlogout.php"><button>Logout</button></a>
        </div>
    </div>

    <main>
        <h1>
            <?php 
           
            if ($selectedElectionId && $electionResult && $electionResult->num_rows > 0) {
                echo htmlspecialchars($election['electionName']); 
            } else {
                echo "Choose Election :";
            }
            ?>
        </h1>

       
        <div class="election-form">
            <form method="GET" action="vvote.php">
                
                <select name="electionId" id="electionId" onchange="this.form.submit()">
                    <option value="">-- Select Election --</option>
                    <?php while ($election = $electionsResult->fetch_assoc()): ?>
                        <option value="<?php echo $election['electionId']; ?>" 
                                <?php echo ($selectedElectionId == $election['electionId']) ? 'selected' : ''; ?> >
                            <?php echo htmlspecialchars($election['electionName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

       
        <?php if ($electionStatusMessage): ?>
            <div class="election-status">
                <p><?php echo $electionStatusMessage; ?></p>
            </div>
        <?php endif; ?>

       
        <?php if ($voteMessage): ?>
            <div class="vote-message">
                <p><?php echo $voteMessage; ?></p>
            </div>
        <?php endif; ?>

       
        <?php if ($showTable && $candidatesResult && $candidatesResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Manifesto</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($candidate = $candidatesResult->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="#candidate-<?php echo $candidate['cId']; ?>">
                                <img src="<?php echo htmlspecialchars($candidate['cImage'] ? $candidate['cImage'] : 'candidate_placeholder.jpg'); ?>" 
                                     alt="Candidate Picture" 
                                     class="candidate-pic" 
                                     height="100">
                            </a>
                            <div id="candidate-<?php echo $candidate['cId']; ?>" class="lightbox">
                                <a href="#" class="close">&times;</a>
                                <img src="<?php echo htmlspecialchars($candidate['cImage'] ? $candidate['cImage'] : 'candidate_placeholder.jpg'); ?>" 
                                     alt="Candidate Picture">
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($candidate['cId']); ?></td>
                        <td><?php echo htmlspecialchars($candidate['cName']); ?></td>
                        <td><?php echo htmlspecialchars($candidate['cPhone']); ?></td>
                        <td><?php echo htmlspecialchars($candidate['cEmail']); ?></td>
                        <td>
                            <a href="#manifesto-<?php echo $candidate['cId']; ?>">
                                <img src="<?php echo htmlspecialchars($candidate['cManifesto'] ? $candidate['cManifesto'] : 'candidate_placeholder.jpg'); ?>" 
                                     alt="Manifesto Poster" 
                                     class="manifesto" 
                                     height="100">
                            </a>
                            <div id="manifesto-<?php echo $candidate['cId']; ?>" class="lightbox">
                                <a href="#" class="close">&times;</a>
                                <img src="<?php echo htmlspecialchars($candidate['cManifesto'] ? $candidate['cManifesto'] : 'candidate_placeholder.jpg'); ?>" 
                                     alt="Manifesto Poster">
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="vvote.php?electionId=<?php echo $selectedElectionId; ?>">
                                <input type="hidden" name="cId" value="<?php echo $candidate['cId']; ?>">
                                <button type="submit" name="vote" class="vote-btn">Vote</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
