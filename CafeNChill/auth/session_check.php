<?php

session_start();
include "../config/db.php";

if (!isset($_SESSION['user']) && isset($_COOKIE['user_login'])) {
    $username = $_COOKIE['user_login'];
  
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    

    if ($user && $user['status'] === 'active') {
        $_SESSION['user'] = $user;
    }
}


if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
