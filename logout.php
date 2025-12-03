<?php
// logout.php
session_start();
session_unset();
session_destroy();
setcookie('apotek_user', '', time() - 3600, "/");
header("Location: index.php");
exit();
