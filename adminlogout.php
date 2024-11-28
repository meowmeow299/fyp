<?php
session_start();


if(isset($_SESSION['adminUsername'])) {

    session_destroy();
    header("Location: homepage.php");
    exit;
} else {

    header("Location: homepage.php");
    exit;
}
?>