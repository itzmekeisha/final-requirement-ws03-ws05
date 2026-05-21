<?php
session_start();
include "../config/db.php";

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password_input = $_POST['password'];
    $role = 'user'; 

    if (empty($fullname) || empty($username) || empty($password_input)) {
        $error_msg = "All fields are required.";
    } else {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error_msg = "Username is already taken. Please choose another one.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

        
            $password = password_hash($password_input, PASSWORD_DEFAULT); 
            
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt->bind_param("ssss", $fullname, $username, $password, $role);

            if ($stmt->execute()) {
                $success_msg = "Registration Successful!";
            } else {
             
                $error_msg = "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            background: #0c0a09; 
            color: white; 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .reg-card { 
            background: #1c1917; 
            padding: 30px; 
            border-radius: 15px; 
            border: 1px solid rgba(132, 92, 68, 0.3); 
            width: 350px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        h2 { 
            color: #d4a373; 
            margin-top: 0; 
            margin-bottom: 20px; 
            font-weight: 500;
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            background: #0c0a09; 
            border: 1px solid #292524; 
            color: white; 
            border-radius: 6px; 
            box-sizing: border-box;
            transition: 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #845c44;
        }
        .btn { 
            width: 100%; 
            background: #845c44; 
            color: white; 
            padding: 12px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600;
            margin-top: 15px;
            transition: 0.2s;
        }
        .btn:hover { 
            background: #d4a373; 
        }
    </style>
</head>
<body>

    <div class="reg-card">
        <h2>Staff Registration</h2>
        <form method="POST" action="">
            <input type="text" name="full_name" placeholder="Full Name" required autocomplete="off">
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Create Account</button>
        </form>
        <p style="font-size: 13px; text-align: center; margin-top: 20px; color: #a8a29e;">
            Already have an account? <a href="login.php" style="color: #d4a373; text-decoration: none;">Login here</a>
        </p>
    </div>

<script>
   
    <?php if(!empty($success_msg)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Registered!',
            text: '<?= htmlspecialchars($success_msg, ENT_QUOTES, "UTF-8") ?>',
            background: '#1c1917',
            color: '#fafaf9',
            confirmButtonColor: '#845c44'
        }).then(() => {
            window.location = 'login.php';
        });
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: '<?= htmlspecialchars($error_msg, ENT_QUOTES, "UTF-8") ?>',
            background: '#1c1917',
            color: '#fafaf9',
            confirmButtonColor: '#845c44'
        });
    <?php endif; ?>
</script>

</body>
</html>
