<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Get the admin user_id from the session

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, user_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $content, $user_id])) {
            $success = 'Announcement created successfully.';
        } else {
            $error = 'Failed to create announcement. Please try again.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/create_announcement.css">
    <title>Create Announcement - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Create Announcement</h2>
        <?php if ($error): ?>
            <div class="alert error">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($error); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($success); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <form method="post" action="create_announcement.php">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn">Create Announcement</button>
                <a href="my_announcements.php" class="btn btn-secondary">Back to My Announcements</a>
            </div>
        </form>
    </div>
</body>
</html>