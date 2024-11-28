<?php
include("header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $e = !empty($_POST['adminUsername']) ? mysqli_real_escape_string($connect, $_POST['adminUsername']) : FALSE;
    $p = !empty($_POST['adminPass']) ? mysqli_real_escape_string($connect, $_POST['adminPass']) : FALSE;

    if ($e && $p) {
        
        $q = "SELECT adminUsername, adminPass 
              FROM admin 
              WHERE adminUsername = '$e' AND adminPass = '$p'";

        $result = mysqli_query($connect, $q);

        if (@mysqli_num_rows($result) == 1) {
            header("Location: adminhome.php");
                session_start();
                
        } else {
            $error_message = 'The email and password entered do not match our records. Please contact admin for further information.';
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
    <title>Admin Login</title>
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
                <h2>Admin Login</h2>
                <form action="adminlogin.php" method="POST">
                    <label for="username">Username</label>
                    <input type="text" name="adminUsername" id="adminUsername" placeholder="" required
                        value="<?php if (isset($_POST['adminUsername'])) echo $_POST['adminUsername']; ?>" />

                    <label for="password">Password</label>
                    <input type="password" name="adminPass" id="adminPass" required
                        value="<?php if (isset($_POST['adminPass'])) echo $_POST['adminPass']; ?>" />

                    <button type="submit" class="login-button">Sign In</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
