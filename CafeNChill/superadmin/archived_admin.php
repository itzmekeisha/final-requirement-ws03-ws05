<?php
include "../config/db.php";
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$msg_status = "";
$msg_text = "";

if (isset($_SESSION['success'])) {
    $msg_status = "success";
    $msg_text = $_SESSION['success'];
    unset($_SESSION['success']);
} elseif (isset($_SESSION['error'])) {
    $msg_status = "error";
    $msg_text = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_restore'])) {
    
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
        die("Security Error: CSRF verification failure.");
    }

    $restore_id = isset($_POST['restore_id']) ? intval($_POST['restore_id']) : 0;
    
    if ($restore_id > 0) {
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ? AND role = 'admin'");
        $stmt->bind_param("i", $restore_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Admin account has been successfully restored!";
        } else {
            $_SESSION['error'] = "Database Error: Unable to restore account.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid Account ID.";
    }

    header("Location: archived_admin.php");
    exit();
}
$res = $conn->query("SELECT * FROM users WHERE role='admin' AND status='archived'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cafe N Chill | Archived Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
            max-width: 950px;
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

        .restore-btn {
            background: transparent;
            border: none;
            color: #4ade80;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95rem;
            padding: 0;
            transition: 0.2s;
        }

        .restore-btn:hover {
            text-decoration: underline;
            color: #22c55e;
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
                    <th>Action</th> 
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
                    <td>
                        <button type="button" class="restore-btn" onclick="confirmRestore(<?= intval($row['id']) ?>, '<?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?>')">
                            <i class="fa-solid fa-rotate-left"></i> Restore
                        </button>
                    </td>
                </tr>
                <?php } ?>
                
                <?php if($res->num_rows == 0){ ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px; opacity: 0.5;">
                        <i class="fa-solid fa-folder-open" style="display:block; font-size: 2rem; margin-bottom: 10px;"></i>
                        No archived accounts found.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<form id="secureRestoreForm" method="POST" action="" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="restore_id" id="restoreAdminId" value="">
    <input type="hidden" name="btn_restore" value="1">
</form>

<script>
function confirmRestore(userId, username) {
    Swal.fire({
        title: 'Restore Account?',
        text: "Do you want to restore the admin account of @" + username + "?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4ade80',
        cancelButtonColor: '#292524', 
        confirmButtonText: 'Yes, Restore it!',
        cancelButtonText: 'Cancel',
        background: '#1c1917',         
        color: '#fafaf9'          
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('restoreAdminId').value = userId;
            document.getElementById('secureRestoreForm').submit();
        }
    })
}
</script>

<?php if(!empty($msg_status) && !empty($msg_text)): ?>
<script>
    Swal.fire({
        icon: '<?= $msg_status ?>',
        title: '<?= $msg_status == "success" ? "Restored!" : "Notice!" ?>',
        text: '<?= $msg_text ?>',
        confirmButtonColor: '#845c44',
        background: '#1c1917',
        color: '#fafaf9'
    });
</script>
<?php endif; ?>

</body>
</html>
