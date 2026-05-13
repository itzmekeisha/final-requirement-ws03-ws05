<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cafe N Chill | Active Inventory</title>
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
            --info-blue: #0ea5e9;
            --danger-red: #ef4444;
        }

        body { margin: 0; font-family: 'Poppins', sans-serif; background: var(--bg-color); color: var(--text-light); display: flex; }

        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; padding-top: 20px; border-right: 1px solid rgba(132, 92, 68, 0.2); overflow-y: auto; }
        .sidebar h2 { color: var(--cream-accent); text-align: center; font-size: 1.3rem; margin-bottom: 30px; border-bottom: 1px solid rgba(212, 163, 115, 0.1); padding-bottom: 20px; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #a8a29e; padding: 12px 25px; text-decoration: none; font-size: 0.9rem; transition: 0.3s; cursor: pointer; }
        .sidebar a:hover { background: rgba(132, 92, 68, 0.1); color: var(--cream-accent); }
        .sidebar a.active { background: var(--coffee-brown); color: white; border-left: 4px solid var(--cream-accent); }

        .main { margin-left: 260px; padding: 40px; width: 100%; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { color: var(--cream-accent); font-weight: 600; margin: 0; }

        .table-wrapper { background: var(--card-bg); padding: 25px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: var(--cream-accent); border-bottom: 2px solid #3f3f46; text-transform: uppercase; font-size: 0.85rem; }
        td { padding: 15px; border-bottom: 1px solid #3f3f46; color: #d1d5db; font-size: 0.9rem; }

        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
        .approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; color: white; text-decoration: none; margin-right: 5px; transition: 0.2s; }
        .btn-icon:hover { opacity: 0.8; transform: scale(1.05); }
        .btn-edit { background: var(--info-blue); }
        .btn-archive { background: var(--danger-red); }
    </style>
</head>

<body>
<div class="sidebar">
    <h2>CAFE N CHILL</h2>
    <a href="admin_dashboard.php" class="<?= $page == 'admin_dashboard.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="add_item.php" class="<?= $page == 'add_item.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-plus"></i> Add Item
    </a>
    <a href="view_items.php" class="<?= $page == 'view_items.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-box-open"></i> View Items
    </a>
    <a href="update_item.php"><i class="fa-solid fa-pen-to-square"></i> Update Item</a>
    <a href="archived_items.php"><i class="fa-solid fa-box-archive"></i> Archived Items</a>
    <a href="view_user.php"><i class="fa-solid fa-users"></i> View Users</a>
    <a href="add_user.php"><i class="fa-solid fa-user-plus"></i> Add User</a>
    <a href="archived_users.php"><i class="fa-solid fa-user-slash"></i> Archived Users</a>
    <a href="reset_password.php"><i class="fa-solid fa-key"></i> Reset Password</a>
    <a href="approve_item.php"><i class="fa-solid fa-check-double"></i> Approve Items</a>

    <!-- Pinatinding Logout Style na katulad ng sa Dashboard -->
    <a onclick="confirmLogout()" style="margin-top: 20px; color: #f87171;">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>

<div class="main">
    <div class="header-flex">
        <h1><i class="fa-solid fa-boxes-stacked"></i> Active Inventory</h1>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM items WHERE status='approved' ORDER BY id DESC");
                while($r = $res->fetch_assoc()){
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                    <td><?= htmlspecialchars($r['category']) ?></td>
                    <td><?= $r['quantity'] ?></td>
                    <td><span class="badge approved">APPROVED</span></td>
                    <td>
                        <a href="update_item.php?id=<?= $r['id'] ?>" class="btn-icon btn-edit"><i class="fa-solid fa-pencil"></i></a>
                        <a href="#" onclick="confirmArchive(<?= $r['id'] ?>)" class="btn-icon btn-archive"><i class="fa-solid fa-box-archive"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Logout Function matching Dashboard style
function confirmLogout() {
    Swal.fire({
        title: 'Logout?',
        text: "Are you sure you want to log out?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#845c44',
        cancelButtonColor: '#292524',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Stay Here',
        background: '#1c1917',
        color: '#fafaf9'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../auth/logout.php";
        }
    });
}

function confirmArchive(id){
    Swal.fire({
        title: 'Archive Item?',
        text: "This will move the item to archived inventory.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#292524',
        confirmButtonText: 'Yes, Archive',
        background: '#1c1917',
        color: '#fafaf9'
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = "archived_items.php?archive_id=" + id;
        }
    });
}
</script>
</body>
</html>