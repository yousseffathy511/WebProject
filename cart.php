<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
require 'db.php';

// Fetch cart items for the user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.cart_id, r.title, r.price, r.cover_picture, r.resource_id FROM Cart c JOIN Resources r ON c.resource_id = r.resource_id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Handle remove from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
    $cart_id = $_POST['cart_id'];
    $stmt = $pdo->prepare("DELETE FROM Cart WHERE cart_id = ?");
    $stmt->execute([$cart_id]);
    header("Location: cart.php");
    exit();
}

// Redirect to payment before checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    if (isset($_POST['selected_items']) && count($_POST['selected_items']) > 0) {
        $_SESSION['selected_items'] = $_POST['selected_items'];
        
        // Calculate total amount
        $total = 0;
        foreach ($_POST['selected_items'] as $cart_id) {
            foreach ($cart_items as $item) {
                if ($item['cart_id'] == $cart_id) {
                    $total += $item['price'];
                }
            }
        }
        $_SESSION['total_amount'] = $total;
        
        header("Location: payment.php");
        exit();
    } else {
        $error = 'Please select at least one item to checkout.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>Cart - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Your Cart</h2>
        <?php if (count($cart_items) > 0): ?>
            <form method="post" action="cart.php">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="item-info">
                                <input type="checkbox" name="selected_items[]" value="<?php echo $item['cart_id']; ?>" class="cart-checkbox" data-price="<?php echo $item['price']; ?>">
                                <img src="<?php echo htmlspecialchars($item['cover_picture']); ?>" alt="Cover Picture" class="cover-picture-small">
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p>RM <?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                            <button type="submit" name="remove" class="remove-btn" value="Remove">Remove</button>
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary">
                    <h3>Summary</h3>
                    <p>Items: <span id="item-count"><?php echo count($cart_items); ?></span></p>
                    <p>Total Price: RM <span id="total-price">0.00</span></p>
                    <?php if (isset($error)): ?>
                        <p class="error"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <button type="submit" name="checkout" class="checkout-btn">Checkout</button>
                </div>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.cart-checkbox');
            const totalPriceElement = document.getElementById('total-price');

            function updateTotal() {
                let total = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.getAttribute('data-price'));
                    }
                });
                totalPriceElement.textContent = total.toFixed(2);
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotal);
            });

            updateTotal(); // Initialize the total price on page load
        });
    </script>
</body>
</html>
