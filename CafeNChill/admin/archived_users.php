<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page = basename($_SERVER['PHP_SELF']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_type'])) {
    
 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Error: CSRF token verification failed.");
    }

    $id = intval($_POST['user_id']);
    $action = $_POST['action_type'];

    if ($action === 'restore') {
        $stmt = $conn->prepare("UPDATE users SET status='active' WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "User restored successfully!";
        }
        $stmt->close();
    } 
    
    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "User deleted permanently!";
        }
        $stmt->close();
    }

    header("Location: archived_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe N Chill | Archived Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --bg-color: #0c0a09;
            --sidebar-color: #1c1917;
            --coffee-brown: #845c44;
            --cream-accent: #d4a373;
            --text-light: #fafaf9;
            --card-bg: #292524;
            --danger-red: #ef4444;
            --success-green: #10b981;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg-color);
            color: var(--text-light);
            display: flex;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-color);
            position: fixed;
            padding-top: 20px;
            border-right: 1px solid rgba(132, 92, 68, 0.2);
            overflow-y: auto;
        }

        .sidebar h2 {
            color: var(--cream-accent);
            text-align: center;
            font-size: 1.3rem;
            margin-bottom: 30px;
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(212, 163, 115, 0.1);
            padding-bottom: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #a8a29e;
            padding: 12px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.9rem;
        }

        .sidebar a:hover {
            background: rgba(132, 92, 68, 0.1);
            color: var(--cream-accent);
        }

        .sidebar a.active {
            background: var(--coffee-brown);
            color: white;
            border-left: 4px solid var(--cream-accent);
        }

        .main {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .header-section {
            margin-bottom: 30px;
        }

        h1 { color: var(--cream-accent); margin: 0; font-weight: 600; }

        .table-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            text-align: left;
            padding: 15px;
            color: var(--cream-accent);
            border-bottom: 2px solid #3f3f46;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #3f3f46;
            color: #d1d5db;
            font-size: 0.9rem;
        }

        .role-badge {
            color: var(--cream-accent);
            font-weight: 600;
            background: rgba(212, 163, 115, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .btn-action {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-restore { background: var(--success-green); }
        .btn-restore:hover { background: #059669; transform: translateY(-2px); }

        .btn-delete { background: #444; margin-left: 5px; }
        .btn-delete:hover { background: var(--danger-red); transform: translateY(-2px); }

        .no-data { text-align: center; padding: 50px; color: #a8a29e; font-style: italic; }
    </style>
</head>
<body>

<form id="securityForm" method="POST" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="user_id" id="formUserInput">
    <input type="hidden" name="action_type" id="formActionInput">
</form>

<div class="sidebar">
    <h2>CAFE N CHILL</h2>
    <a href="admin_dashboard.php" class="<?= $page == 'admin_dashboard.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="add_item.php"><i class="fa-solid fa-plus"></i> Add Item</a>
    <a href="view_items.php"><i class="fa-solid fa-box-open"></i> View Items</a>
    <a href="update_item.php"><i class="fa-solid fa-pen-to-square"></i> Update Item</a>
    <a href="archived_items.php"><i class="fa-solid fa-box-archive"></i> Archived Items</a>
    <a href="view_user.php"><i class="fa-solid fa-users"></i> View Users</a>
    <a href="add_user.php"><i class="fa-solid fa-user-plus"></i> Add User</a>
    <a href="archived_users.php" class="active"><i class="fa-solid fa-user-slash"></i> Archived Users</a>
    <a href="reset_password.php"><i class="fa-solid fa-key"></i> Reset Password</a>
    <a href="approve_item.php"><i class="fa-solid fa-check-double"></i> Approve Items</a>

    <a onclick="confirmLogout()" style="margin-top: 20px; color: #f87171; cursor: pointer;">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>

<div class="main">
    <div class="header-section">
        <h1><i class="fa-solid fa-users-slash"></i> Archived Users</h1>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM users WHERE status='archived' ORDER BY id DESC");
                
                if($res && $res->num_rows > 0){
                    while($r = $res->fetch_assoc()){
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['full_name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td>@<?= htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="role-badge"><?= htmlspecialchars(strtoupper($r['role']), ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td>
                        <button onclick="handleUserAction(<?= intval($r['id']) ?>, 'restore')" class="btn-action btn-restore">
                            <i class="fa-solid fa-rotate-left"></i> Restore
                        </button>
                        <button onclick="handleUserAction(<?= intval($r['id']) ?>, 'delete')" class="btn-action btn-delete">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-data'>No archived users found.</td></tr>";
                } 
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function handleUserAction(id, type) {
        const isDelete = type === 'delete';
        
        Swal.fire({
            title: isDelete ? 'Delete Permanently?' : 'Restore User?',
            text: isDelete 
                ? "Warning: This account will be permanently deleted from the database!" 
                : "This user will be restored to the active users list.",
            icon: isDelete ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: isDelete ? '#ef4444' : '#10b981',
            cancelButtonColor: '#444',
            confirmButtonText: isDelete ? 'Yes, Delete!' : 'Yes, Restore!',
            background: '#1c1917',
            color: '#fafaf9'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formUserInput').value = id;
                document.getElementById('formActionInput').value = type;
                document.getElementById('securityForm').submit();
            }
        });
    }

    <?php if(isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>',
            background: '#1c1917',
            color: '#fafaf9',
            confirmButtonColor: '#845c44'
        });
    <?php unset($_SESSION['success']); endif; ?>

    function confirmLogout() {
        Swal.fire({
            title: 'Logout?',
            text: "Are you sure you want to log out?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#845c44',
            cancelButtonColor: '#292524',
            confirmButtonText: 'Logout',
            background: '#1c1917',
            color: '#fafaf9'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../auth/logout.php";
            }
        });
    }
</script>

</body>
</html>
