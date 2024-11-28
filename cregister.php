<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<?php
    include("header.php");


    $error = array();


    $elections_query = "SELECT electionId, electionName FROM election";
    $elections_result = mysqli_query($connect, $elections_query);
    $elections = [];
    if ($elections_result) {
        while ($row = mysqli_fetch_assoc($elections_result)) {
            $elections[] = $row;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id = ''; 

       
        if (empty($_POST['electionId'])) {
            $error[] = 'Please select an election.';
        } else {
            $electionId = mysqli_real_escape_string($connect, trim($_POST['electionId']));
        }

     
        if (empty($_POST['cEmail'])) {
            $error[] = 'You forgot to enter your student email.';
        } else {
            $e = mysqli_real_escape_string($connect, trim($_POST['cEmail']));
        }

        if (!preg_match("/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i", $e)) {
            $error[] = 'Please enter a valid email address in the format \'user@example.com\'.';
        }

        if (empty($_POST['cName'])) {
            $error[] = 'You forgot to enter your name.';
        } else {
            $n = mysqli_real_escape_string($connect, trim($_POST['cName']));
        }

        if (empty($_POST['cId'])) {
            $error[] = 'You forgot to enter your student ID.';
        } else {
            $id = mysqli_real_escape_string($connect, trim($_POST['cId']));
        }

        if (!preg_match("/^AM\d{10}$/", $id)) {
            $error[] = 'The student ID must be in the format "AMXXXXXXXXXX" (where X is a digit).';
        }

        if (empty($_POST['cPhone'])) {
            $error[] = 'You forgot to enter your phone number.';
        } else {
            $ph = mysqli_real_escape_string($connect, trim($_POST['cPhone']));
        }

        if (empty($_POST['cPassword'])) {
            $error[] = 'You forgot to enter a password.';
        } else {
            $p = mysqli_real_escape_string($connect, trim($_POST['cPassword']));
        }

        if (empty($_POST['confirm_pass'])) {
            $error[] = 'Please confirm your password.';
        } else {
            $confirm_password = $_POST['confirm_pass'];
            if ($confirm_password !== $p) {
                $error[] = 'Passwords do not match.';
            }
        }

        
        if (empty($error)) {
 
            $check_email_query = "SELECT cEmail FROM candidate WHERE cEmail = '$e'";
            $check_email_result = mysqli_query($connect, $check_email_query);

            $check_id_query = "SELECT cId FROM candidate WHERE cId = '$id'";
            $check_id_result = mysqli_query($connect, $check_id_query);

            $check_email_query_voter = "SELECT vEmail FROM voter WHERE vEmail = '$e'";
            $check_email_result_voter = mysqli_query($connect, $check_email_query_voter);

            $check_id_query_voter = "SELECT vId FROM voter WHERE vId = '$id'";
            $check_id_result_voter = mysqli_query($connect, $check_id_query_voter);

            if (mysqli_num_rows($check_email_result) > 0 || mysqli_num_rows($check_id_result) > 0 || mysqli_num_rows($check_email_result_voter) > 0 || mysqli_num_rows($check_id_result_voter) > 0) {
                $error[] = 'The email or student ID you entered is already registered.';
            } else {
              
                $ad = mysqli_real_escape_string($connect, trim($_POST['cAddress']));
                $insert_query = "INSERT INTO candidate(cEmail, cPassword, cName, cPhone, cAddress, cId, cStatus, cVoting, electionId)
                                VALUES ('$e', '$p', '$n', '$ph', '$ad', '$id', 'Pending', 0, '$electionId')";
                $insert_result = mysqli_query($connect, $insert_query);

                if ($insert_result) {
                    echo '<div class="message-container success">';
                    echo '<h1>Registration Successful!</h1>';
                    echo '<h3>Please wait for the admin to approve your account.</h3>';
                    echo '<p>Go back to <a href="candidatelogin.php">Login</a></p>';
                    echo '</div>';
                    exit();
                } else {
                    $error[] = 'System Error: ' . mysqli_error($connect);
                }
            }
        }
    }

    mysqli_close($connect);
?>

<div class="container">
    <div class="header">
        <img src="logog.png" alt="UPTM Logo" class="logo">
        <div class="menu">
            <a href="homepage.php"><button>Home</button></a>
            <a href="vlogin.php"><button>Voter</button></a>
            <a href="candidatelogin.php"><button>Candidate</button></a>
            <a href="adminlogin.php"><button>Admin</button></a>
        </div>
    </div>

 
    <?php if (!empty($error)): ?>
    <div class="error-messages">
        <?php foreach ($error as $err): ?>
            <p class="error"><?php echo $err; ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <main>
        <div class="register-container">
            <h2>Candidate Registration</h2>
            <form action="cregister.php" method="post">

                <label for="electionId">Select Election</label>
                <select name="electionId" id="electionId" required>
                    <option value="">-- Select an Election --</option>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['electionId']; ?>"><?php echo $election['electionName']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="cEmail">Student Email</label>
                <input type="email" placeholder="example@student.kuptm.edu.my" name="cEmail" id="cEmail" size="30" maxlength="50"
                required value="<?php if (isset($_POST['cEmail'])) echo $_POST['cEmail']; ?>" />

                <label for="cName">Name</label>
                <input type="text" name="cName" id="cName" size="30" maxlength="50"
                required value="<?php if (isset($_POST['cName'])) echo $_POST['cName']; ?>" />

                <label for="cId">ID Number</label>
                <input type="text" id="cId" name="cId" placeholder="AMXXXXXXXXX" 
                required value="<?php if (isset($_POST['cId'])) echo $_POST['cId']; ?>" />

                <label for="cAddress">Address</label>
                <input type="text" name="cAddress" id="cAddress" size="30" maxlength="50"
                required value="<?php if (isset($_POST['cAddress'])) echo $_POST['cAddress']; ?>" />

                <label for="cPhone">Phone Number</label>
                <input type="tel" placeholder="01X-XXXXXXX" name="cPhone" id="cPhone" size="15" maxlength="20"
                pattern="[0-9]{3}-[0-9]{7}"
                required value="<?php if (isset($_POST['cPhone'])) echo $_POST['cPhone']; ?>" />

                <label for="cPassword">Password</label>
                <input type="password" id="cPassword" name="cPassword" size="15" maxlength="60"
                required value="<?php if (isset($_POST['cPassword'])) echo $_POST['cPassword']; ?>" />

                <label for="confirm_pass">Re-confirm Password</label>
                <input type="password" id="confirm_pass" name="confirm_pass" required>

                <button type="submit" class="register-button">Register</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
