<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['id'];

$success = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $n = trim($_POST['name']);
    $c = trim($_POST['cat']);
    $q = $_POST['qty'];
    $status = 'pending';

    $stmt = $conn->prepare("INSERT INTO item_requests (name, category, quantity, status, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $n, $c, $q, $status, $user_id);

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
    <title>Cafe N Chill | Request Item</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        :root { --bg-color: #0c0a09; --sidebar-color: #1c1917; --coffee-brown: #845c44; --cream-accent: #d4a373; --text-light: #fafaf9; --card-bg: #292524; }

        body { margin: 0; font-family: 'Poppins', sans-serif; background: var(--bg-color); color: var(--text-light); display: flex; justify-content: center; align-items: center; height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; left: 0; top: 0; padding-top: 20px; border-right: 1px solid rgba(132, 92, 68, 0.2); }
        .sidebar h2 { color: var(--cream-accent); text-align: center; font-size: 1.3rem; margin-bottom: 30px; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #a8a29e; padding: 12px 25px; text-decoration: none; transition: 0.3s; font-size: 0.9rem; }
        .sidebar a:hover { background: rgba(132, 92, 68, 0.1); color: var(--cream-accent); }
        .form-container { background: var(--card-bg); padding: 40px; width: 100%; max-width: 450px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 1px solid rgba(132, 92, 68, 0.2); z-index: 1; }
        h1 { color: var(--cream-accent); text-align: center; margin-bottom: 10px; font-size: 1.8rem; }
        p.subtitle { text-align: center; color: #a8a29e; font-size: 0.85rem; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.8rem; color: #a8a29e; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        input { width: 100%; padding: 12px 15px; background: #1c1917; border: 1px solid #444; border-radius: 10px; color: white; font-family: inherit; outline: none; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: var(--coffee-brown); box-shadow: 0 0 0 2px rgba(132, 92, 68, 0.1); }
        button { width: 100%; padding: 15px; background: var(--coffee-brown); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 10px; }
        button:hover { background: var(--cream-accent); transform: translateY(-2px); }
        .footer-text { text-align: center; margin-top: 20px; font-size: 0.8rem; color: #57534e; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CAFE N CHILL</h2>
    <a href="user_home.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="request_item.php" style="color: var(--cream-accent); background: rgba(132, 92, 68, 0.1);">
        <i class="fa-solid fa-plus-circle"></i> Request Item
    </a>
</div>

<div class="form-container">
    <h1><i class="fa-solid fa-mug-hot"></i> Request Item</h1>
    <p class="subtitle">Request for:s <strong><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></strong></p>

    <form method="POST">
        <div class="input-group">
            <label>Item Name</label>
            <input type="text" name="name" placeholder="Example: Arabica Coffee Beans" required>
        </div>
        <div class="input-group">
            <label>Category</label>
            <input type="text" name="cat" placeholder="Example: Raw Materials" required>
        </div>
        <div class="input-group">
            <label>Quantity</label>
            <input type="number" name="qty" placeholder="How many are needed?" required>
        </div>

        <button type="submit">
            <i class="fa-solid fa-paper-plane"></i> Submit Request
        </button>
    </form>
    <div class="footer-text">The request will first go through Admin approval.</div>
</div>

<?php if($success): ?>
<script>
    Swal.fire({
        title: 'Request Sent!',
        text: 'Your request has been submitted to the admin.',
        icon: 'success',
        confirmButtonColor: '#845c44',
        background: '#1c1917',
        color: '#fafaf9'
    }).then(() => {
        window.location.href = 'user_home.php';
    });
</script>
<?php endif; ?>
</body>
</html>