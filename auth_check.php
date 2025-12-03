<?php
session_start();

// Jika belum login, arahkan ke login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Session Timeout (1 menit)
$timeout = 60; 
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    }
}

$_SESSION['last_activity'] = time();
?>
