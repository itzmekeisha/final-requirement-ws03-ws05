<?php
include "../config/db.php";
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'superadmin'){
    die("ACCESS DENIED");
}

$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cafe N Chill | </title>
    
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
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg-color);
            color: var(--text-light);
        }

      
        .sidebar {
            width: 240px;
            height: 100vh;
            background: var(--sidebar-color);
            position: fixed;
            padding-top: 30px;
            border-right: 1px solid rgba(132, 92, 68, 0.2);
        }

        .sidebar h2 {
            color: var(--cream-accent);
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #a8a29e;
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 1rem;
            cursor: pointer;
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
            margin-left: 240px;
            padding: 40px;
        }

        .header {
            background: linear-gradient(135deg, var(--coffee-brown), #5a3e2e);
            color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }

        .header h2 { margin: 0; font-weight: 600; }

       
        .cards {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            flex: 1;
            padding: 25px;
            color: white;
            border-radius: 15px;
            text-align: left;
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }

        .card:hover { transform: translateY(-5px); }

        .card h3 { margin: 0; font-size: 1rem; opacity: 0.8; }
        .card p { margin: 10px 0 0; font-size: 2.5rem; font-weight: 700; }

        .coffee-card { background: var(--card-bg); border-bottom: 4px solid var(--coffee-brown); }
        .archived-card { background: var(--card-bg); border-bottom: 4px solid #ef4444; }

      
        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: var(--coffee-brown);
            color: white;
            text-decoration: none;
            margin: 25px 0;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .add-btn:hover { background: var(--cream-accent); color: var(--bg-color); }

        
        .table-container {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            background: rgba(132, 92, 68, 0.1);
            color: var(--cream-accent);
            padding: 15px;
            font-weight: 600;
            border-bottom: 2px solid #3f3f46;
        }

        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #3f3f46;
            color: #d1d5db;
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            background: rgba(34, 197, 94, 0.1);
            color: #4ade80;
        }

        .action-link {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }

        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>


<div class="sidebar">
    <h2>CAFE N CHILL</h2>

    <a href="superadmin_dashboard.php" class="<?= $page == 'superadmin_dashboard.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>
    <a href="add_admin.php" class="<?= $page == 'add_admin.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-user-plus"></i> Add Admin
    </a>
    <a href="archived_admin.php" class="<?= $page == 'archived_admin.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-box-archive"></i> Archived
    </a>
    
    <a onclick="confirmLogout()" style="margin-top: 50px; color: #f87171;">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>


<div class="main">

    <div class="header">
        <h2>Welcome</h2>
        <p style="margin: 5px 0 0; opacity: 0.8;">CAFE N CHILL Inventory Management System!</p>
    </div>


    <div class="cards">
        <div class="card coffee-card">
            <h3>Total Active Admins</h3>
            <p>
                <?php
                echo $conn->query("SELECT COUNT(*) as total FROM users WHERE role='admin' AND status='active'")->fetch_assoc()['total'];
                ?>
            </p>
        </div>

        <div class="card archived-card">
            <h3>Archived Admins</h3>
            <p>
                <?php
                echo $conn->query("SELECT COUNT(*) as total FROM users WHERE role='admin' AND status='archived'")->fetch_assoc()['total'];
                ?>
            </p>
        </div>
    </div>

    <a class="add-btn" href="add_admin.php"><i class="fa-solid fa-plus"></i> Add New Admin</a>

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
                <?php
                $res = $conn->query("SELECT * FROM users WHERE role='admin' AND status='active'");
                while($r=$res->fetch_assoc()){
                ?>
                <tr>
                    <td><i class="fa-solid fa-user-tie" style="color: var(--cream-accent); margin-right: 10px;"></i> <?= htmlspecialchars($r['username']) ?></td>
                    <td><span class="status-pill"><?= ucfirst($r['status']) ?></span></td>
                    <td>
                        <a class="action-link" href="archive_admin.php?id=<?= $r['id'] ?>">
                            <i class="fa-solid fa-folder-minus"></i> Archive
                        </a> 
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>


<script>
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#845c44',
        cancelButtonColor: '#292524', 
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        background: '#1c1917',         
        color: '#fafaf9'          
    }).then((result) => {
        if (result.isConfirmed) {
         
            window.location.href = "../auth/logout.php";
        }
    })
}
</script>

</body>
</html>