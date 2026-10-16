<?php
include('db.php');
include('auth_check.php');

// Strict Role-Based Access Check
if ($_SESSION['role'] !== 'admin') {
    echo "<h2 style='color:red;'>Access Denied! Only Admin can access this page.</h2>";
    echo "<p><a href='student_dashboard.php'>Go to Student Dashboard</a></p>";
    exit();
}

// Fetch Last Login Timestamp from DB
$user_id = $_SESSION['user_id'];
$res = mysqli_query($conn, "SELECT last_login FROM users WHERE id = $user_id");
$data = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f5; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 8px; width: 450px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge { background: #007bff; color: white; padding: 3px 8px; border-radius: 4px; font-size: 14px; }
        .logout-btn { color: red; text-decoration: none; font-weight: bold; }
        .success-banner {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

<div class="card">
    <?php if (isset($_SESSION['login_success'])): ?>
        <div class="success-banner">
            <?php 
                echo $_SESSION['login_success']; 
                unset($_SESSION['login_success']); // Page refresh par message hat jayega
            ?>
        </div>
    <?php endif; ?>

    <h2>Admin Dashboard</h2>
    <p>Welcome, <b><?php echo $_SESSION['username']; ?></b>!</p>
    <p>Role: <span class="badge"><?php echo $_SESSION['role']; ?></span></p>
    <p>Last Login Time: <b><?php echo $data['last_login'] ? $data['last_login'] : 'First Time Login'; ?></b></p>
    <hr>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>