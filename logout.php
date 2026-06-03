<?php
require_once __DIR__ . '/includes/app.php';
app_start();
logout_user();
header('Location: login.php');
exit;
?>