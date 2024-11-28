<?php
session_start();
include("header.php"); 


if (isset($_SESSION['voterId'])) {
    header("Location: vhome.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $e = !empty($_POST['vEmail']) ? mysqli_real_escape_string($connect, $_POST['vEmail']) : FALSE;
    $p = !empty($_POST['vPass']) ? mysqli_real_escape_string($connect, $_POST['vPass']) : FALSE;

    if ($e && $p) {
        
        $q = "SELECT vEmail, vName, vId, vAddress, vPhone, vStatus 
              FROM voter 
              WHERE vEmail = '$e' AND vPass = '$p'";

        $result = mysqli_query($connect, $q);

        if (@mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $vStatus = $row['vStatus'];

            if ($vStatus == 'approved') {
                session_start();
                $_SESSION = $row;
                header("Location: vhome.php");
                exit();
            } elseif ($vStatus == 'pending') {
                $error_message = 'Your account is pending approval. Please wait for admin approval.';
            } elseif ($vStatus == 'rejected') {
                $error_message = 'Your account has been rejected. Please contact the admin for more information.';
            }
        } else {
            $error_message = 'The email and password entered do not match our records. Perhaps you need to register, just click the Register button.';
        }

        mysqli_free_result($result);
    } else {
        $error_message = 'Please fill in both the email and password fields.';
    }

    mysqli_close($connect);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Login</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="logog.png" alt="UPTM Logo" class="logo">
            <div class="menu">
                <a href="homepage.php"><button>Home</button></a>
                <a href="result.php"><button>View Result</button></a>
                <a href="vlogin.php"><button>Voter</button></a>
                <a href="candidatelogin.php"><button>Candidate</button></a>
                <a href="adminlogin.php"><button>Admin</button></a>
                <a href="faq.php"><button>FAQ</button></a>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <main>
            <div class="login-container">
                <h2>Voter Login</h2>
                <form action="vlogin.php" method="POST">
                    <label for="email">Email</label>
                    <input type="text" name="vEmail" id="vEmail" placeholder="user@student.kuptm.edu.my" required
                        value="<?php if (isset($_POST['vEmail'])) echo $_POST['vEmail']; ?>" />

                    <label for="password">Password</label>
                    <input type="password" name="vPass" id="vPass" required
                        value="<?php if (isset($_POST['vPass'])) echo $_POST['vPass']; ?>" />

                    <button type="submit" class="login-button">Sign In</button>
                </form>

                <p>Don't have an account yet? <a href="vregister.php">Sign Up</a></p>
            </div>
        </main>
    </div>
</body>
</html>
