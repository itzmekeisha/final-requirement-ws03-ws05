<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = false;
$error_msg = "";
$item = null;

if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        $item = $res->fetch_assoc();
    }
    $stmt->close();
}

if(isset($_POST['update'])){

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Error: CSRF token verification failed.");
    }

    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $current_quantity = intval($_POST['current_quantity']); 
    $added_stock = intval($_POST['added_stock']); 
    $uom = $_POST['uom']; 
    $cost_price = floatval($_POST['cost_price']); 
    $selling_price = floatval($_POST['selling_price']); 
    $description = trim($_POST['description']); 

    if ($added_stock < 0 || $cost_price < 0 || $selling_price < 0) {
        $error_msg = "Stock, Cost Price, and Selling Price cannot be negative values.";
    } elseif (empty($name) || empty($category)) {
        $error_msg = "Item name and category are required fields.";
    } else {
        $new_quantity = $current_quantity + $added_stock;

        if(!empty($id) && $id > 0){
            $stmt_upd = $conn->prepare("UPDATE items SET name = ?, category = ?, quantity = ?, uom = ?, cost_price = ?, selling_price = ?, description = ?, updated_at = NOW() WHERE id = ?");
            $stmt_upd->bind_param("ssisddsi", $name, $category, $new_quantity, $uom, $cost_price, $selling_price, $description, $id);
            if($stmt_upd->execute()){
                $success = true;
            } else {
                $error_msg = "Database Error: Failed to update the item.";
            }
            $stmt_upd->close();
        }
    }
}

$logs_query = "SELECT name, category, updated_at FROM items WHERE updated_at IS NOT NULL ORDER BY updated_at DESC LIMIT 6";
$logs_result = $conn->query($logs_query);
$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Inventory | Cafe N Chill</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        :root {
            --bg-color: #0c0a09;
            --sidebar-color: #171717;
            --coffee-brown: #845c44;
            --cream-accent: #d4a373;
            --text-light: #fafaf9;
            --card-bg: #1c1917;
            --input-bg: #262626;
            --border-color: rgba(212, 163, 115, 0.1);
        }

        body {
            margin: 0; font-family: 'Poppins', sans-serif;
            background: var(--bg-color); color: var(--text-light);
            display: flex; min-height: 100vh;
        }

        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; padding-top: 20px; border-right: 1px solid rgba(132, 92, 68, 0.2); overflow-y: auto; }
        .sidebar h2 { color: var(--cream-accent); text-align: center; font-size: 1.3rem; margin-bottom: 30px; border-bottom: 1px solid rgba(212, 163, 115, 0.1); padding-bottom: 20px; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #a8a29e; padding: 12px 25px; text-decoration: none; font-size: 0.9rem; transition: 0.3s; cursor: pointer; }
        .sidebar a:hover { background: rgba(132, 92, 68, 0.1); color: var(--cream-accent); }
        .sidebar a.active { background: var(--coffee-brown); color: white; border-left: 4px solid var(--cream-accent); }

        .main-content {
            margin-left: 260px; padding: 40px; width: 100%;
            display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; align-items: start;
            box-sizing: border-box;
        }

        .glass-card {
            background: var(--card-bg); padding: 30px; border-radius: 16px;
            border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .section-title {
            font-size: 1.1rem; color: var(--cream-accent); margin-bottom: 25px;
            display: flex; align-items: center; gap: 10px; font-weight: 500;
        }

        .form-group { margin-bottom: 20px; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }

        label { display: block; font-size: 0.7rem; color: #737373; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        input, select, textarea {
            width: 100%; padding: 12px 15px; background: var(--input-bg); border: 1px solid transparent;
            border-radius: 8px; color: white; transition: 0.3s; outline: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--coffee-brown); background: #2d2d2d; }
        input:disabled { background: #1a1a1a; color: #737373; cursor: not-allowed; border: 1px dashed rgba(255,255,255,0.05); }

        .btn-update {
            width: 100%; padding: 14px; background: var(--coffee-brown); color: white;
            border: none; border-radius: 8px; cursor: pointer; font-weight: 600;
            transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-update:hover { background: #6b4a36; transform: translateY(-2px); }

        .log-table { width: 100%; border-collapse: collapse; }
        .log-table th { text-align: left; font-size: 0.7rem; color: #737373; text-transform: uppercase; padding: 12px; border-bottom: 1px solid var(--border-color); }
        .log-table td { padding: 15px 12px; font-size: 0.85rem; border-bottom: 1px solid rgba(255,255,255,0.03); }
        
        .status-badge {
            background: rgba(212, 163, 115, 0.1); color: var(--cream-accent);
            padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;
        }
        .time-col { color: #737373; font-size: 0.8rem; }

        @media (max-width: 1100px) {
            .main-content { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CAFE N CHILL</h2>
    <a href="admin_dashboard.php" class="<?= $page == 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="add_item.php" class="<?= $page == 'add_item.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Add Item</a>
    <a href="view_items.php" class="<?= $page == 'view_items.php' ? 'active' : '' ?>"><i class="fa-solid fa-box-open"></i> View Items</a>
    <a href="update_item.php" class="<?= $page == 'update_item.php' ? 'active' : '' ?>"><i class="fa-solid fa-pen-to-square"></i> Update Item</a>
    <a href="archived_items.php" class="<?= $page == 'archived_items.php' ? 'active' : '' ?>"><i class="fa-solid fa-box-archive"></i> Archived Items</a>
    <a href="view_user.php" class="<?= $page == 'view_user.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> View Users</a>
    <a href="add_user.php" class="<?= $page == 'add_user.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-plus"></i> Add User</a>
    <a href="archived_users.php" class="<?= $page == 'archived_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-slash"></i> Archived Users</a>
    <a href="reset_password.php" class="<?= $page == 'reset_password.php' ? 'active' : '' ?>"><i class="fa-solid fa-key"></i> Reset Password</a>
    <a href="approve_item.php" class="<?= $page == 'approve_item.php' ? 'active' : '' ?>"><i class="fa-solid fa-check-double"></i> Approve Items</a>
    <a onclick="confirmLogout()" style="margin-top: 20px; color: #f87171; cursor: pointer;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<main class="main-content">
    <section class="glass-card">
        <div class="section-title">
            <i class="fa-solid fa-pen-nib"></i> Edit Item Details
        </div>

        <?php if($item): ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="id" value="<?= intval($item['id']); ?>">
            <input type="hidden" name="current_quantity" value="<?= intval($item['quantity']); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" required placeholder="e.g. Wintermelon">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="<?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?>" required placeholder="e.g. Milktea">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Current Stock (Read-Only)</label>
                    <input type="number" value="<?= intval($item['quantity']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Add Additional Stock</label>
                    <input type="number" name="added_stock" value="0" min="0" required placeholder="Enter amount to add">
                </div>
            </div>

            <div class="form-group">
                <label>Unit / Size Measurement (UOM)</label>
                <select name="uom" required>
                    <option value="pcs" <?= (isset($item['uom']) && $item['uom'] == 'pcs') ? 'selected' : ''; ?>>Pieces (per piece)</option>
                    <option value="12oz" <?= (isset($item['uom']) && $item['uom'] == '12oz') ? 'selected' : ''; ?>>12 oz (Small Size)</option>
                    <option value="16oz" <?= (isset($item['uom']) && $item['uom'] == '16oz') ? 'selected' : ''; ?>>16 oz (Medium Size)</option>
                    <option value="22oz" <?= (isset($item['uom']) && $item['uom'] == '22oz') ? 'selected' : ''; ?>>22 oz (Large Size)</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cost Price / Capital (₱)</label>
                    <input type="number" step="0.01" min="0" name="cost_price" value="<?= isset($item['cost_price']) ? floatval($item['cost_price']) : '0.00'; ?>" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Selling Price / Retail (₱)</label>
                    <input type="number" step="0.01" min="0" name="selling_price" value="<?= isset($item['selling_price']) ? floatval($item['selling_price']) : '0.00'; ?>" required placeholder="0.00">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Enter item description here..."><?= isset($item['description']) ? htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <button type="submit" name="update" class="btn-update">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </form>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 0;">
                <i class="fa-solid fa-hand-pointer" style="font-size: 2rem; color: #444; margin-bottom: 15px; display: block;"></i>
                <p style="color: #737373; font-size: 0.9rem;">Please select an item from the inventory list first before editing</p>
                <a href="view_items.php" style="color: var(--cream-accent); text-decoration: none; font-size: 0.8rem; border: 1px solid var(--cream-accent); padding: 8px 15px; border-radius: 5px; display: inline-block; margin-top: 10px;">Go to Inventory</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="glass-card">
        <div class="section-title">
            <i class="fa-solid fa-clock-rotate-left"></i> Activity Log
        </div>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs_result && $logs_result->num_rows > 0): ?>
                    <?php while($log = $logs_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span style="display:block; font-weight: 500;"><?= htmlspecialchars($log['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span style="font-size: 0.7rem; color: #737373;"><?= htmlspecialchars($log['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td class="time-col"><?= date('M d, h:i A', strtotime($log['updated_at'])); ?></td>
                        <td><span class="status-badge">Updated</span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; color:#737373; padding: 20px;">No recent activities found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Logout?',
            text: "Are you sure you want to end your session?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#845c44',
            cancelButtonColor: '#262626',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Stay Here',
            background: '#1c1917',
            color: '#fafaf9'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = '../auth/logout.php'; }
        });
    }

    <?php if($success): ?>
    Swal.fire({
        title: 'Updated!',
        text: 'Successfully saved the changes and calculated stock.',
        icon: 'success',
        confirmButtonColor: '#845c44',
        background: '#1c1917',
        color: '#fafaf9'
    }).then(() => { window.location.href = 'view_items.php'; });
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
    Swal.fire({
        title: 'Validation Error',
        text: '<?= htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8'); ?>',
        icon: 'error',
        confirmButtonColor: '#845c44',
        background: '#1c1917',
        color: '#fafaf9'
    });
    <?php endif; ?>
</script>

</body>
</html>
