<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

// Fetch all feedback
$stmt = $pdo->prepare("SELECT f.*, u.username FROM feedback f JOIN users u ON f.user_id = u.user_id ORDER BY f.submitted_at DESC");
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/manage_feedback.css">
    <title>Manage Feedback - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Users Feedbacks</h2>
        <div class="feedback-list">
            <?php if (count($feedbacks) > 0): ?>
                <ul>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <li>
                            <p class="feedback-text"><?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?></p>
                            <p class="feedback-user">by user: <?php echo htmlspecialchars($feedback['username']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No feedback available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>