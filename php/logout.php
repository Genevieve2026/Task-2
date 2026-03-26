<?php
// Logout script
session_start();
session_destroy();
header('Location: html/index.php');
exit;
?>
