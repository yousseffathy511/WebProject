<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

$success = '';

// Handle delete request for FAQs
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM faq WHERE id = ?");
    $stmt->execute([$delete_id]);
    $success = 'FAQ has been deleted successfully.';
}

// Handle form submissions for FAQs
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    if (isset($_POST['add_faq'])) {
        $stmt = $pdo->prepare("INSERT INTO faq (question, answer) VALUES (?, ?)");
        $stmt->execute([$question, $answer]);
        $success = 'FAQ has been added successfully.';
    } elseif (isset($_POST['edit_faq'])) {
        $edit_id = $_POST['edit_id'];
        $stmt = $pdo->prepare("UPDATE faq SET question = ?, answer = ? WHERE id = ?");
        $stmt->execute([$question, $answer, $edit_id]);
        $success = 'FAQ has been updated successfully.';
    }
}

// Retrieve existing FAQs
$stmt = $pdo->prepare("SELECT * FROM faq ORDER BY created_at DESC");
$stmt->execute();
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch FAQ for editing if requested
$edit_faq = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_faq = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all user questions
$stmt = $pdo->prepare("SELECT * FROM userquestions ORDER BY asked_at DESC");
$stmt->execute();
$user_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_faq.css">
    <title>Manage FAQs and User Questions - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>User Questions</h1>
        <table>
            <tr>
                <th>Question</th>
                <th>Submitted At</th>
            </tr>
            <?php foreach ($user_questions as $question): ?>
                <tr>
                    <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                    <td><?php echo htmlspecialchars($question['asked_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h1><?php echo $edit_faq ? 'Edit FAQ' : 'Add FAQ'; ?></h1>
        <form method="post" action="faq_admin.php">
            <input type="hidden" name="<?php echo $edit_faq ? 'edit_faq' : 'add_faq'; ?>" value="1">
            <?php if ($edit_faq): ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_faq['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="question">Question:</label>
                <textarea id="question" name="question" required><?php echo $edit_faq['question'] ?? ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="answer">Answer:</label>
                <textarea id="answer" name="answer" required><?php echo $edit_faq['answer'] ?? ''; ?></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn">Submit</button>
                <?php if ($edit_faq): ?>
                    <a href="faq_admin.php"><button type="button" class="btn btn-cancel">Cancel</button></a>
                <?php endif; ?>
            </div>
        </form>

        <h2>Existing FAQs</h2>
        <table>
            <tr>
                <th>Question</th>
                <th>Answer</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($faqs as $faq): ?>
                <tr>
                    <td><?php echo htmlspecialchars($faq['question']); ?></td>
                    <td><?php echo htmlspecialchars($faq['answer']); ?></td>
                    <td>
                        <a href="faq_admin.php?edit_id=<?php echo $faq['id']; ?>"><button type="button" class="btn-table">Edit</button></a>
                        <a href="faq_admin.php?delete_id=<?php echo $faq['id']; ?>"><button type="button" class="btn-table btn-cancel-table">Delete</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
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