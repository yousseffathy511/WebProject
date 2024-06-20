<?php
session_start();
require 'db.php';

// Restrict access to signed-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $_SESSION['user_id']]);
    header("Location: announcements.php");
    exit();
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ? WHERE announcement_id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $edit_id, $_SESSION['user_id']]);
        header("Location: announcements.php");
        exit();
    } else {
        $error = 'Please fill in all fields';
    }
}

// Fetch all announcements along with the admin user who created them
$stmt = $pdo->prepare("SELECT a.*, u.username AS admin_username FROM announcements a JOIN users u ON a.user_id = u.user_id ORDER BY a.created_at DESC");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch specific announcement for editing if edit_id is set
$edit_announcement = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE announcement_id = ? AND user_id = ?");
    $stmt->execute([$edit_id, $_SESSION['user_id']]);
    $edit_announcement = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/announcements.css">
    <title>Announcements - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Announcements</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($edit_announcement): ?>
            <h3>Edit Announcement</h3>
            <form method="post" action="announcements.php">
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_announcement['announcement_id']); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_announcement['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($edit_announcement['content']); ?></textarea>
                </div>
                <button type="submit" class="btn">Update Announcement</button>
            </form>
        <?php else: ?>
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                        <small>Posted by <?php echo htmlspecialchars($announcement['admin_username']); ?> on <?php echo htmlspecialchars($announcement['created_at']); ?></small>
                        <?php if ($_SESSION['user_id'] == $announcement['user_id']): ?>
                            <div class="announcement-actions">
                                <a href="announcements.php?edit_id=<?php echo $announcement['announcement_id']; ?>" class="btn">Edit</a>
                                <form method="post" action="announcements.php" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $announcement['announcement_id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No announcements available.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>