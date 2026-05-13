<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = "SELECT * FROM items WHERE status='approved'";
if (!empty($search)) {
    $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>CAFE N CHILL</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --bg-color: #0c0a09;
            --card-bg: #1c1917;
            --text-color: #fafaf9;
            --main-color: #845c44;
            --accent-color: #d6d3d1;
            --border: #292524;
        }

        * { margin: 0; padding: 0;
         box-sizing: border-box; 
         font-family: 'Poppins', sans-serif; }

        body { background-color: var(--bg-color); color: var(--text-color); padding: 20px; }

        .dashboard-container { max-width: 1100px; margin: 40px auto; }

        .header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px;
        }

        .welcome-msg h2 { font-size: 2rem; color: var(--main-color); }
        
        .toolbar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; gap: 15px; flex-wrap: wrap;
        }

        .search-box { display: flex; flex: 1; min-width: 300px; }
        .search-box input {
            width: 100%; padding: 12px 15px; border-radius: 8px 0 0 8px;
            border: 1px solid var(--border); background: var(--card-bg); color: white; outline: none;
        }
        .search-btn {
            padding: 10px 20px; background: var(--main-color); border: none;
            color: white; border-radius: 0 8px 8px 0; cursor: pointer; transition: 0.3s;
        }
        .search-btn:hover { background: #6b4a37; }

        .btn-request {
            background: var(--main-color); color: white; padding: 12px 20px;
            border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-request:hover { background: #6b4a37; transform: translateY(-2px); }
        .item-card { background: var(--card-bg); border-radius: 12px; padding: 25px; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .item-card h3 { margin-bottom: 20px; border-left: 4px solid var(--main-color); padding-left: 10px; color: var(--accent-color); }
        .item-list { width: 100%; border-collapse: collapse; }
        .item-list th, .item-list td {
            text-align: left; padding: 15px; border-bottom: 1px solid var(--border);
        }
        .item-list th { color: var(--main-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }

        .status-badge {
            background: rgba(16, 185, 129, 0.1); color: #10b981;
            padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        }

        .logout-link { color: #ef4444; text-decoration: none; font-weight: 500; cursor: pointer; transition: 0.3s; }
        .logout-link:hover { color: #b91c1c; }
        
        .no-data { text-align: center; padding: 60px; color: #78716c; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="header">
        <div class="welcome-msg">
            <h2>Kumusta, <?php echo htmlspecialchars($user['full_name']); ?>! ☕</h2>
            <p style="color: #78716c;"> Cafe N Chill Management System</p>
        </div>
        <a href="#" onclick="confirmLogout()" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <div class="toolbar">
        <form action="" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Mag-search ng gamit sa inventory..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <a href="add_item_request.php" class="btn-request">
            <i class="fa-solid fa-plus"></i> Request New Item  </a>
    </div>
    <div class="item-card">
        <h3><i class="fa-solid fa-boxes-stacked"></i> Inventory List</h3>
        <table class="item-list">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Stock Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><span class="status-badge">Available</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="no-data">
                            <i class="fa-solid fa-inbox fa-3x" style="margin-bottom: 15px; opacity: 0.3;"></i><br>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmLogout() {
    Swal.fire({
        text: "Are you sure you want to logout?",
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
            window.location.href = '../auth/logout.php';
        }
    })
}
</script>

</body>
</html>