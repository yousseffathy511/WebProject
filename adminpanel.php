<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\style.css">
    <title>Admin Panel - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Admin Panel</h1>
        <!-- Admin panel content -->
    </div>
    
</body>
</html>
