<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php'; // Include the database connection

$current_page = basename($_SERVER['PHP_SELF']);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT username, profile_picture, is_admin FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
        $_SESSION['is_admin'] = $user['is_admin'];
    } else {
        // If user data is not found, log out the user
        require 'logout.php';
    }

    // Fetch the number of items in the cart
    if (!$_SESSION['is_admin']) {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cart_count FROM Cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch();
        $cart_count = $cart['cart_count'];
    } else {
        $cart_count = 0;
    }
} else {
    $cart_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <title>MMU Resources</title>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <a href="index.php"><img src="profilePicture/super/logo.png" alt="MMU Resources Logo"></a>
            </div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <nav>
                    <ul>
                        <li><a href="<?php echo $_SESSION['is_admin'] ? 'adminpanel.php' : 'home.php'; ?>" class="<?php echo $current_page == ($_SESSION['is_admin'] ? 'adminpanel.php' : 'home.php') ? 'active' : ''; ?>">Home</a></li>
                        <?php if (!$_SESSION['is_admin']): ?>
                            <li><a href="announcements.php" class="<?php echo $current_page == 'announcements.php' ? 'active' : ''; ?>">Announcements</a></li>
                            <li><a href="upload_resource.php" class="<?php echo $current_page == 'upload_resource.php' ? 'active' : ''; ?>">Upload Resource</a></li>
                            <li><a href="purchased.php" class="<?php echo $current_page == 'purchased.php' ? 'active' : ''; ?>">My Resources</a></li>
                            <li><a href="posted_resources.php" class="<?php echo $current_page == 'posted_resources.php' ? 'active' : ''; ?>">Posted Resources</a></li>
                            <li><a href="about_us.php" class="<?php echo $current_page == 'about_us.php' ? 'active' : ''; ?>">About Us</a></li>
                        <?php else: ?>
                            <li><a href="my_announcements.php" class="<?php echo $current_page == 'my_announcements.php' ? 'active' : ''; ?>">My Announcements</a></li>
                            <li><a href="manage_resources.php" class="<?php echo $current_page == 'manage_resources.php' ? 'active' : ''; ?>">Manage Resources</a></li>
                            <li><a href="manage_feedback.php" class="<?php echo $current_page == 'manage_feedback.php' ? 'active' : ''; ?>">Manage Feedback</a></li>
                            <li><a href="faq_admin.php" class="<?php echo $current_page == 'faq_admin.php' ? 'active' : ''; ?>">Manage FAQs</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            <div class="user-info">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if (!$_SESSION['is_admin']): ?>
                        <div class="cart-container">
                            <a href="cart.php" class="cart-icon <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cart_count > 0): ?>
                                    <span class="cart-count"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="profile">
                        <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Picture" class="profile-picture">
                        <div class="profile-dropdown">
                            <span><?php echo $_SESSION['username']; ?> <i class="fas fa-chevron-down arrow"></i></span>
                            <div class="dropdown-content">
                                <a href="profile.php">Profile</a>
                                <a href="logout.php">Log Out</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="signin.php" class="btn">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <script src="javascript/script.js"></script>
</body>
</html>
