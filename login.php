<?php
session_start();
include('db.php');

$error = "";

// 1. Pehle se logged in user ko redirect karo
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

// 2. Auto-Login using Remember-Me Cookie (Checking hashed token in DB)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $raw_token = $_COOKIE['remember_me'];
    $hashed_token = hash('sha256', $raw_token);

    $sql = "SELECT * FROM users WHERE remember_token_hash = '$hashed_token'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Regenerate Session ID securely after login
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        $_SESSION['login_success'] = "Successfully logged in!";

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    }
}

// 3. Normal Form Login Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify Password using password_verify()
        if (password_verify($password, $user['password'])) {
            
            // Regenerate session ID for security
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            $_SESSION['login_success'] = "Successfully logged in!";

            // Last-Login Timestamp Update
            $update_login = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
            mysqli_query($conn, $update_login);

            // Remember-Me Token (Random token hashed in DB)
            if (isset($_POST['remember'])) {
                $random_token = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $random_token);

                setcookie("remember_me", $random_token, time() + (86400 * 7), "/");

                $update_token = "UPDATE users SET remember_token_hash = '$token_hash' WHERE id = " . $user['id'];
                mysqli_query($conn, $update_token);
            }

            // Role-Based Redirection
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();

        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Practical 10 - Secure Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 320px;
        }
        .box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .field {
            margin-bottom: 15px;
        }
        .field label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .field input[type="text"],
        .field input[type="password"] {
            width: 93%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 15px;
        }
        .btn {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error-msg {
            color: red;
            background: #ffe6e6;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .info-msg {
            color: #856404;
            background: #fff3cd;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Secure Login</h2>

    <?php if ($error != ""): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'timeout'): ?>
        <div class="info-msg">Session timed out due to inactivity!</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
        <div class="error-msg">Please login to access dashboard!</div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="field">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember Me</label>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>
</div>

</body>
</html>