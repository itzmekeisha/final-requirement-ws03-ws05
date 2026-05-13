<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$success = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $n = trim($_POST['name']);
    $c = trim($_POST['category']);
    $q = $_POST['quantity'];
    $u_id = $_SESSION['user']['id']; 
    $status = "pending"; 

    $stmt = $conn->prepare("INSERT INTO item_requests (name, category, quantity, user_id, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $n, $c, $q, $u_id, $status);

    if($stmt->execute()){
        $success = true;
    }
}

$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cafe N Chill | Add Item</title>
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
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            justify-content: center; 
            align-items: center;     
            box-sizing: border-box;
        }

        .form-container {
            background: var(--card-bg);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(132, 92, 68, 0.2);
        }

        h1 {
            color: var(--cream-accent);
            margin-bottom: 10px;
            text-align: center;
            font-weight: 600;
        }

        .input-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: #a8a29e; }

        input {
            width: 100%;
            padding: 12px 15px;
            background: #1c1917;
            border: 1px solid #444;
            border-radius: 10px;
            color: white;
            outline: none;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus { border-color: var(--coffee-brown); }

        button {
            width: 100%;
            padding: 15px;
            background: var(--coffee-brown);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover { background: var(--cream-accent); transform: translateY(-2px); }

        .back-link { 
            display: inline-block;
            margin-top: 25px; 
            color: #a8a29e; 
            text-decoration: none; 
            font-size: 0.85rem;
            transition: 0.3s;
        }
        .back-link:hover { color: var(--cream-accent); }
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
    <a href="view_items.php"><i class="fa-solid fa-box-open"></i> View Items</a>
    <a href="update_item.php"><i class="fa-solid fa-pen-to-square"></i> Update Item</a>
    <a href="archived_items.php"><i class="fa-solid fa-box-archive"></i> Archived Items</a>
    <a href="view_user.php"><i class="fa-solid fa-users"></i> View Users</a>
    <a href="add_user.php"><i class="fa-solid fa-user-plus"></i> Add User</a>
    <a href="archived_users.php"><i class="fa-solid fa-user-slash"></i> Archived Users</a>
    <a href="reset_password.php"><i class="fa-solid fa-key"></i> Reset Password</a>
    <a href="approve_item.php"><i class="fa-solid fa-check-double"></i> Approve Items</a>

    <a onclick="confirmLogout()" style="margin-top: 20px; color: #f87171;">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>

<div class="main">
    <div class="form-container">
        <h1><i class="fa-solid fa-mug-hot"></i> New Item</h1>
        <p style="text-align: center; color: #d4a373; font-size: 0.8rem; margin-bottom: 25px; opacity: 0.8;">Waiting for Admin Approval</p>
        
        <form method="POST">
            <div class="input-group">
                <label>Item Name</label>
                <input type="text" name="name" placeholder="e.g. Arabica Beans" required>
            </div>

            <div class="input-group">
                <label>Category</label>
                <input type="text" name="category" placeholder="e.g. Coffee" required>
            </div>

            <div class="input-group">
                <label>Quantity</label>
                <input type="number" name="quantity" placeholder="0" required>
            </div>

            <button type="submit">Confirm Add Item</button>
        </form>

        <div style="text-align: center;">
            <a href="admin_dashboard.php" class="back-link">Cancel and go back</a>
        </div>
    </div>
</div>

<script>
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
</script>

<?php if($success){ ?>
<script>
    Swal.fire({
        title: 'Request Submitted!',
        text: 'Waiting for Admin approval.',
        icon: 'info',
        confirmButtonColor: '#845c44',
        background: '#1c1917',
        color: '#fafaf9'
    }).then(() => {
        window.location.href = 'add_item.php';
    });
</script>
<?php } ?>

</body>
</html>