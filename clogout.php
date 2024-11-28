<?php
session_start();


if(isset($_SESSION['cEmail'])) {
    
    session_destroy();
    header("Location: homepage.php");
    exit;
} else {
 
    header("Location: homepage.php");
    exit;
}
?>