<?php
include "../config/db.php";
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
        die("Security Error: CSRF verification failure.");
    }

   
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = "archived";

    if($id > 0) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'admin'");
        $stmt->bind_param("si", $status, $id);

        if($stmt->execute()){
            $_SESSION['success'] = "Admin account has been successfully archived.";
        } else {
            $_SESSION['error'] = "Database Error: Unable to archive admin.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid Admin ID.";
    }
}

header("Location: superadmin_dashboard.php");
exit();
?>
