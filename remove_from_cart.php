<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $resource_id = $_POST['resource_id'];

    // Remove the item from the cart
    $stmt = $pdo->prepare("DELETE FROM Cart WHERE user_id = ? AND resource_id = ?");
    $stmt->execute([$user_id, $resource_id]);

    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Remove from Cart - MMU Resources</title>
</head>
<body>
    <p>Removing item...</p>
</body>
</html>
