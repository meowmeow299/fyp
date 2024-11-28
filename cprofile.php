<?php

session_start();

include("header.php");


if (!isset($_SESSION['cId'])) {
    header("Location: candidatelogin.php"); 
    exit();
}

$cId = $_SESSION['cId'];


$error_message = "";


$q = "SELECT * FROM candidate WHERE cId = '$cId'";
$result = $connect->query($q);
$candidate = $result->fetch_assoc();

if (!$candidate) {
    echo "No candidate data found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cAddress = trim($_POST['cAddress']);
    $cManifesto = $candidate['cManifesto']; 
    $cImage = $candidate['cImage']; 

   
    if (empty($cAddress)) {
        $error_message = "Address cannot be empty.";
    } else {
        
        if (isset($_FILES['cImage']) && $_FILES['cImage']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploaded_img/";
            $target_file = $target_dir . basename($_FILES["cImage"]["name"]);

            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            
            if (move_uploaded_file($_FILES["cImage"]["tmp_name"], $target_file)) {
                $cImage = $target_file; 
            }
        }

        
        if (isset($_FILES['cManifesto']) && $_FILES['cManifesto']['error'] == UPLOAD_ERR_OK) {
            $poster_dir = "uploaded_posters/";
            $poster_file = $poster_dir . basename($_FILES["cManifesto"]["name"]);

            
            if (!is_dir($poster_dir)) {
                mkdir($poster_dir, 0777, true);
            }

            
            if (move_uploaded_file($_FILES["cManifesto"]["tmp_name"], $poster_file)) {
                $cManifesto = $poster_file; 
            }
        }

        
        if ($cAddress != $candidate['cAddress'] || $cManifesto != $candidate['cManifesto'] || $cImage != $candidate['cImage']) {
            $q = "UPDATE candidate SET cAddress = '$cAddress', cManifesto = '$cManifesto', cImage = '$cImage' WHERE cId = '$cId'";
            if ($connect->query($q) === TRUE) {
               
                $q = "SELECT * FROM candidate WHERE cId = '$cId'";
                $result = $connect->query($q);
                $candidate = $result->fetch_assoc();

                echo "Profile updated successfully!";
            } else {
                echo "Error updating profile: " . $connect->error;
            }
        } else {
            echo "No changes detected. Profile remains the same.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile</title>
    <link rel="stylesheet" href="cprofile.css">
</head>
<body>
<div class="container">
    <div class="header">
        <img src="logog.png" alt="UPTM Logo" class="logo">
        <div class="menu">
            <a href="chome.php"><button>Home</button></a>
            <a href="cprofile.php"><button>Edit Profile</button></a>
            <a href="cresult.php"><button>View Results</button></a>
            <a href="clogout.php"><button>Logout</button></a>
        </div>
    </div>
</div>


<?php if (isset($message)) { echo $message; } ?>


<main>
    
    <h1>Hello, <?php echo htmlspecialchars($candidate['cName']); ?>!</h1>
    

    <div class="profile-container">
        <div class="photo-section">
            <label>Your Photo :</label>
            <div class="photo-placeholder">
                <img src="<?php echo $candidate['cImage'] ? $candidate['cImage'] : 'placeholder.png'; ?>" alt="Profile Photo" height="200">
            </div>
        </div>
        <div class="form-section">
            
            <form action="cprofile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name :</label>
                    <input type="text" value="<?php echo htmlspecialchars($candidate['cName']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>ID Number :</label>
                    <input type="text" value="<?php echo htmlspecialchars($candidate['cId']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Email :</label>
                    <input type="text" value="<?php echo htmlspecialchars($candidate['cEmail']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Address :</label>
                    <input type="text" name="cAddress" value="<?php echo htmlspecialchars($candidate['cAddress']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Manifesto Poster:</label>
                    <input type="file" name="cManifesto" value="<?php echo htmlspecialchars($candidate['cManifesto']); ?>" required">
                </div>
                <div class="form-group">
                    <label>Photo:</label>
                    <input type="file" name="cImage" accept="image/*">
                </div>
                <button class="update" type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>

<?php
$connect->close();
?>
