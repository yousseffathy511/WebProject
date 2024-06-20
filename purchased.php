<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
require 'db.php';

// Fetch purchased items for the user, ordered by purchase date descending
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT r.title, r.price, r.cover_picture, r.file_path, p.purchase_date FROM PurchasedResources p JOIN Resources r ON p.resource_id = r.resource_id WHERE p.user_id = ? ORDER BY p.purchase_date DESC");
$stmt->execute([$user_id]);
$purchased_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/purchased.css">
    <title>Purchased Items - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Your Purchased Items</h2>
        <?php if (count($purchased_items) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Price (RM)</th>
                        <th>Purchase Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchased_items as $item): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($item['cover_picture']); ?>" alt="Cover Picture" class="cover-picture-small"></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['purchase_date']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($item['file_path']); ?>" class="btn">View</a>
                                <a href="<?php echo htmlspecialchars($item['file_path']); ?>" download class="btn btn-download">Download</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not purchased any items yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>