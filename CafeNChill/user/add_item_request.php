<?php
session_start();
include "../config/db.php";


header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}



function clean($data){
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$success = false;

if($_SERVER["REQUEST_METHOD"] == "POST"){

 
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
        die("Invalid CSRF token");
    }

    $n = clean($_POST['name']);
    $c = clean($_POST['cat']);
    $q = intval($_POST['qty']);
    $uom = clean($_POST['uom']);
    $reason = clean($_POST['reason']);
    $status = 'pending';

    $stmt = $conn->prepare("
        INSERT INTO item_requests 
        (name, category, quantity, uom, reason, status, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssisssi", $n, $c, $q, $uom, $reason, $status, $user_id);

    if($stmt->execute()){
        $success = true;
    }
}
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

        body { margin: 0; font-family: 'Poppins', sans-serif; background: var(--bg-color); color: var(--text-light); display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); padding-top: 20px; border-right: 1px solid rgba(132, 92, 68, 0.2); }
        .sidebar h2 { color: var(--cream-accent); text-align: center; font-size: 1.3rem; margin-bottom: 30px; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #a8a29e; padding: 12px 25px; text-decoration: none; transition: 0.3s; font-size: 0.9rem; }
        .sidebar a:hover { background: rgba(132, 92, 68, 0.1); color: var(--cream-accent); }
        
        .main-content { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px; }
        .form-container { background: var(--card-bg); padding: 40px; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 1px solid rgba(132, 92, 68, 0.2); }

        h1 { color: var(--cream-accent); text-align: center; margin-bottom: 5px; font-size: 1.6rem; }
        .subtitle { text-align: center; color: #a8a29e; font-size: 0.8rem; margin-bottom: 25px; }

        .input-group { margin-bottom: 15px; }
        label { display: block; font-size: 0.75rem; color: #a8a29e; margin-bottom: 5px; text-transform: uppercase; }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            background: #1c1917;
            border: 1px solid #444;
            border-radius: 8px;
            color: white;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--coffee-brown);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover { background: var(--cream-accent); }
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

<div class="main-content">
    <div class="form-container">
        <h1>Request Item</h1>
        <p class="subtitle">
            Fill out the form to request supplies for 
            <strong><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
        </p>

        <form method="POST">

            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="input-group">
                <label>Item Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="input-group">
                <label>Category</label>
                <input type="text" name="cat" required>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="input-group" style="flex:1;">
                    <label>Quantity</label>
                    <input type="number" name="qty" min="1" required>
                </div>

                <div class="input-group" style="flex:1;">
                    <label>Unit (UOM)</label>
                    <select name="uom" required>
                        <option value="pcs">Pieces</option>
                    </select>
                </div>
            </div>

            <div class="input-group">
                <label>Reason for Request</label>
                <textarea name="reason" rows="2" required></textarea>
            </div>

            <button type="submit">
                <i class="fa-solid fa-paper-plane"></i> Submit Request
            </button>

        </form>
    </div>
</div>

<?php if($success): ?>
<script>
Swal.fire({
    title: 'Request Sent!',
    text: 'Your request is now pending admin approval.',
    icon: 'success',
    background: '#1c1917',
    color: '#fafaf9',
    confirmButtonColor: '#845c44'
}).then(() => {
    window.location.href = 'user_home.php';
});
</script>
<?php endif; ?>

</body>
</html>
