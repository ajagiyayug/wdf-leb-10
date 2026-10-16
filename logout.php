<?php
session_start();
include('db.php');

// 1. Database se remember_token_hash clear karo
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE users SET remember_token_hash = NULL WHERE id = $uid");
}

// 2. Cookie clear karo
if (isset($_COOKIE['remember_me'])) {
    setcookie("remember_me", "", time() - 3600, "/");
}

// 3. Session unset aur destroy karo
session_unset();
session_destroy();

// Redirect back to login
header("Location: login.php");
exit();
?>