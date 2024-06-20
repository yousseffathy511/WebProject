<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['payment_success']) || !isset($_SESSION['selected_items'])) {
    header("Location: signin.php");
    exit();
}

require 'db.php';

// Fetch selected cart items for the user
$user_id = $_SESSION['user_id'];
$selected_items = $_SESSION['selected_items'];
$placeholders = str_repeat('?,', count($selected_items) - 1) . '?';
$stmt = $pdo->prepare("SELECT cart_id, resource_id FROM Cart WHERE cart_id IN ($placeholders) AND user_id = ?");
$params = array_merge($selected_items, [$user_id]);
$stmt->execute($params);
$cart_items = $stmt->fetchAll();

$pdo->beginTransaction();
try {
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO PurchasedResources (user_id, resource_id, purchase_date) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $item['resource_id']]);
    }
    $stmt = $pdo->prepare("DELETE FROM Cart WHERE cart_id IN ($placeholders) AND user_id = ?");
    $stmt->execute($params);
    $pdo->commit();
    unset($_SESSION['payment_success']); // Clear payment success flag
    unset($_SESSION['selected_items']); // Clear selected items
    header("Location: purchased.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed to complete purchase: " . $e->getMessage();
}
?>
