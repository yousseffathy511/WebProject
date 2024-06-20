<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
require 'db.php';

// Get the resource ID from the URL
$resource_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch resource details including faculty_id
$stmt = $pdo->prepare("SELECT r.title, r.description, r.price, r.cover_picture, r.faculty_id, u.username, u.email FROM Resources r JOIN Users u ON r.user_id = u.user_id WHERE r.resource_id = ? AND r.pending_acceptance = 0");
$stmt->execute([$resource_id]);
$resource = $stmt->fetch();

if (!$resource) {
    // Resource not found or not accepted
    header("Location: home.php");
    exit();
}

// Fetch faculty details
$stmt = $pdo->prepare("SELECT faculty_name FROM Faculties WHERE faculty_id = ?");
$stmt->execute([$resource['faculty_id']]);
$faculty = $stmt->fetch();

// Check if the resource is already in the cart
$in_cart = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_check = $pdo->prepare("SELECT * FROM Cart WHERE user_id = ? AND resource_id = ?");
    $cart_check->execute([$user_id, $resource_id]);
    $in_cart = $cart_check->rowCount() > 0;
}

// Add to cart functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart']) && !$in_cart) {
    $stmt = $pdo->prepare("INSERT INTO Cart (user_id, resource_id, added_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $resource_id]);
    $in_cart = true; // Update the flag after adding to the cart
    $message = "Resource added to cart successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title><?php echo htmlspecialchars($resource['title']); ?> - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="resource-detail">
            <h2><?php echo htmlspecialchars($resource['title']); ?></h2>
            <img src="<?php echo htmlspecialchars($resource['cover_picture']); ?>" alt="Cover Picture" class="cover-picture">
            <p><strong>Price:</strong> RM <?php echo number_format($resource['price'], 2); ?></p>
            <p><?php echo nl2br(htmlspecialchars($resource['description'])); ?></p>
            <p><strong>Faculty:</strong> <?php echo htmlspecialchars($faculty['faculty_name']); ?></p>
            <p><strong>Uploaded by:</strong> <?php echo htmlspecialchars($resource['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($resource['email']); ?></p>
            <form method="post" action="">
                <button type="submit" name="add_to_cart" class="btn" <?php echo $in_cart ? 'disabled' : ''; ?>>
                    <?php echo $in_cart ? 'Added to Cart' : 'Add to Cart'; ?>
                </button>
            </form>
            <?php if (isset($message)): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
