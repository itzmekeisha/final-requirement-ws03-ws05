<?php
include "../config/db.php";
session_start();

// SECURITY CHECK - Superadmin access only
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'superadmin'){
    die("ACCESS DENIED");
}

$res = $conn->query("SELECT * FROM users WHERE role='admin' AND status='archived'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cafe N Chill | Archived Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: #0c0a09;
            color: #fafaf9;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h2 { color: #f87171; margin: 0; }

        .back-btn {
            text-decoration: none;
            color: #a8a29e;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .back-btn:hover { color: #d4a373; }

        .table-container {
            background: #1c1917;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 15px;
            border-bottom: 2px solid #3f3f46;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #3f3f46;
            color: #d1d5db;
        }

        .status-archived {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fa-solid fa-trash-can"></i> Archived Admins</h2>
        <a href="superadmin_dashboard.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Date Archived</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $res->fetch_assoc()){ ?>
                <tr>
                    <td>
                        <i class="fa-solid fa-user-slash" style="margin-right: 10px; opacity: 0.5;"></i> 
                        <?= htmlspecialchars($row['username']) ?>
                    </td>
                    <td>
                        <span class="status-archived"><?= ucfirst($row['status']) ?></span>
                    </td>
                    <td style="font-size: 0.85rem; opacity: 0.6;">Recently</td>
                </tr>
                <?php } ?>
                
                <?php if($res->num_rows == 0){ ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px; opacity: 0.5;">
                        No archived accounts found.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>