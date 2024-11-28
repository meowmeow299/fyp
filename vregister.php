<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <?php
    include("header.php"); 

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      
        $error = array();
    
      
        if (empty($_POST['vEmail'])) {
            $error[] = 'You forgot to enter your student email.';
        } else {
            $e = mysqli_real_escape_string($connect, trim($_POST['vEmail']));
        }
    
  
        if (!preg_match("/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i", $e)) {
            $error[] = 'Please enter a valid email address in the format \'user@example.com\'.';
        }
    
  
        if (empty($_POST['vName'])) {
            $error[] = 'You forgot to enter your name.';
        } else {
            $n = mysqli_real_escape_string($connect, trim($_POST['vName']));
        }
    
   
        if (empty($_POST['vId'])) {
            $error[] = 'You forgot to enter your student ID.';
        } else {
            $id = mysqli_real_escape_string($connect, trim($_POST['vId']));
        }
    
    
        if (!preg_match("/^AM\d{10}$/", $id)) {
            $error[] = 'The student ID must be in the format "AMXXXXXXXXXX" (where X is a digit).';
        }
    
      
        if (empty($_POST['vPhone'])) {
            $error[] = 'You forgot to enter your phone number.';
        } else {
            $ph = mysqli_real_escape_string($connect, trim($_POST['vPhone']));
        }
    
    
        if (empty($_POST['vPass'])) {
            $error[] = 'You forgot to enter a password.';
        } else {
            $p = mysqli_real_escape_string($connect, trim($_POST['vPass']));
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
            
            $check_email_query = "SELECT cEmail FROM voter WHERE cEmail = '$e'";
            $check_email_result = mysqli_query($connect, $check_email_query);

            
            $check_id_query = "SELECT cId FROM candidate WHERE cId = '$id'";
            $check_id_result = mysqli_query($connect, $check_id_query);

            
            $check_email_query = "SELECT vEmail FROM voter WHERE vEmail = '$e'";
            $check_email_result = mysqli_query($connect, $check_email_query);

            
            $check_id_query = "SELECT vId FROM voter WHERE vId = '$id'";
            $check_id_result = mysqli_query($connect, $check_id_query);

            
            if (mysqli_num_rows($check_email_result) > 0 && mysqli_num_rows($check_id_result) > 0) {
                $error[] = 'Both the email and student ID you entered are already registered.';
            } elseif (mysqli_num_rows($check_email_result) > 0) {
                
                $error[] = 'The email you entered is already registered.';
            } elseif (mysqli_num_rows($check_id_result) > 0) {
                
                $error[] = 'The student ID you entered is already registered.';
            } else {
                
                $ad = mysqli_real_escape_string($connect, trim($_POST['vAddress'])); // Assuming the address is part of the form
                
                $insert_query = "INSERT INTO voter(vEmail, vPass, vName, vPhone, vAddress, vId, vStatus)
                                VALUES ('$e', '$p', '$n', '$ph', '$ad', '$id', 'Pending')";
                $insert_result = mysqli_query($connect, $insert_query);

                if ($insert_result) {
                    echo '<div class="message-container success">';
                    echo '<h1>Registration Successful!</h1>';
                    echo '<h3>Please wait for the admin to approve your account.</h3>';
                    echo '<p>Go back to <a href="vlogin.php">Login</a></p>';
                    echo '</div>';
                    exit();
                } else {
                    $error[] = 'System Error: ' . mysqli_error($connect);
                }
            }
        }

    

        mysqli_close($connect);
    }
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
                <h2>Voter Registration</h2>
                <form action="vregister.php" method="post">

                    <label for="vEmail">Student Email</label>
                    <input type="text" placeholder="example@student.kuptm.edu.my" name="vEmail" id="vEmail" size="30" maxlength="50"
                    pattern="kl\d{10}@student\.kuptm\.edu\.my"
                    title="Please enter the valid student email address."
                    required value="<?php if (isset($_POST['vEmail'])) echo $_POST['vEmail']; ?>" />
                
                    <label for="vName">Name</label>
                    <input type="text" placeholder="" name="vName" id="vName" size="30" maxlength="50"
                    required value="<?php if (isset($_POST['vName'])) echo $_POST['vName']; ?>" />
                
                    <label for="vId">ID Number</label>
                    <input type="text" id="vId" name="vId" placeholder="AMXXXXXXXXX" 
                    required value ="<?php if (isset($_POST['vId'])) echo $_POST['vId']; ?>"/>
                
                    <label for="vAddress">Address</label>
                    <input type ="text" placeholder="Address" name="vAddress" id="vAddress" size="30" maxlength="50"
                    required value = "<?php if (isset($_POST['vAddress'])) echo $_POST['vAddress']; ?>"/>

                    <label for="vPhone">Phone Number</label>
                    <input type="tel" placeholder="01X-XXXXXXX" name="vPhone" id="vPhone" size="15" maxlength="20"
                    pattern="[0-9]{3}-[0-9]{7}"
                    required value="<?php if (isset($_POST['vPhone'])) echo $_POST['vPhone']; ?>" />

                    <label for="vPass">Password</label>
                    <input type="password" placeholder="" id="vPass" name="vPass" size="15" maxlength="60"
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                    title="Must contain at least one number, one uppercase and lowercase letter and at least 8 or more characters"
                    required value="<?php if (isset($_POST['vPass'])) echo $_POST['vPass']; ?>" />
                
                    <label for="confirm_pass">Re-confirm Password</label>
                    <input type="password" id="confirm_pass" name="confirm_pass" required>
                
                    <button type="submit" class="register-button">Register</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
