<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

$success = '';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $_SESSION['user_id']]);
    $success = 'Announcement has been deleted successfully.';
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ? WHERE announcement_id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $edit_id, $_SESSION['user_id']]);
        $success = 'Announcement has been updated successfully.';
    } else {
        $error = 'Please fill in all fields';
    }
}

// Fetch all announcements created by the admin
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
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
    <link rel="stylesheet" href="css/my_announcement.css">
    <title>My Announcements - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <?php if (!$edit_announcement && count($announcements) > 0): ?>
            <h2>My Announcements</h2>
            <a href="create_announcement.php" class="btn">Create Announcement</a>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert error">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($error); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($edit_announcement): ?>
            <h3>Edit Announcement</h3>
            <form method="post" action="my_announcements.php">
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_announcement['announcement_id']); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo isset($edit_announcement) ? htmlspecialchars($edit_announcement['title']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($edit_announcement['content']); ?></textarea>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn">Update Announcement</button>
                    <a href="my_announcements.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                        <small>Posted on <?php echo htmlspecialchars($announcement['created_at']); ?></small>
                        <div class="announcement-actions">
                            <a href="my_announcements.php?edit_id=<?php echo $announcement['announcement_id']; ?>" class="btn-table btn-edit">Edit</a>
                            <form method="post" action="my_announcements.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $announcement['announcement_id']; ?>">
                                <button type="submit" class="btn-table btn-cancel-table">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-announcements">
                    <h3>No announcements available.</h3>
                    <a href="create_announcement.php" class="btn">Create Announcement</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($success); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>