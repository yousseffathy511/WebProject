<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

// Fetch unanswered questions
$stmt = $pdo->prepare("SELECT uq.*, u.username FROM userquestions uq JOIN users u ON uq.user_id = u.user_id WHERE uq.answer_text IS NULL ORDER BY uq.asked_at DESC");
$stmt->execute();
$unanswered_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch answered questions
$stmt = $pdo->prepare("SELECT uq.*, u.username FROM userquestions uq JOIN users u ON uq.user_id = u.user_id WHERE uq.answer_text IS NOT NULL ORDER BY uq.asked_at DESC");
$stmt->execute();
$answered_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $question_id = $_POST['question_id'];

    if ($action == 'answer') {
        $answer_text = $_POST['answer_text'];
        if (!empty($answer_text)) {
            $stmt = $pdo->prepare("UPDATE userquestions SET answer_text = ? WHERE question_id = ?");
            if ($stmt->execute([$answer_text, $question_id])) {
                $success = 'Answer submitted successfully.';
            } else {
                $error = 'Failed to submit the answer. Please try again.';
            }
        } else {
            $error = 'Answer text cannot be empty.';
        }
    } elseif ($action == 'edit') {
        $question_text = $_POST['question_text'];
        $answer_text = $_POST['answer_text'];
        if (!empty($question_text) && !empty($answer_text)) {
            $stmt = $pdo->prepare("UPDATE userquestions SET question_text = ?, answer_text = ? WHERE question_id = ?");
            if ($stmt->execute([$question_text, $answer_text, $question_id])) {
                $success = 'Question and answer updated successfully.';
            } else {
                $error = 'Failed to update the question and answer. Please try again.';
            }
        } else {
            $error = 'Question and answer text cannot be empty.';
        }
    } elseif ($action == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM userquestions WHERE question_id = ?");
        if ($stmt->execute([$question_id])) {
            // $success = 'Question deleted successfully.';
        } else {
            $error = 'Failed to delete the question. Please try again.';
        }
    }

    // Refresh the questions list
    $stmt = $pdo->prepare("SELECT uq.*, u.username FROM userquestions uq JOIN users u ON uq.user_id = u.user_id WHERE uq.answer_text IS NULL ORDER BY uq.asked_at DESC");
    $stmt->execute();
    $unanswered_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT uq.*, u.username FROM userquestions uq JOIN users u ON uq.user_id = u.user_id WHERE uq.answer_text IS NOT NULL ORDER BY uq.asked_at DESC");
    $stmt->execute();
    $answered_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Manage User Questions - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>User Questions</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <h3>Unanswered Questions</h3>
        <div class="question-list">
            <?php if (count($unanswered_questions) > 0): ?>
                <ul>
                    <?php foreach ($unanswered_questions as $question): ?>
                        <li>
                            <p><strong>User:</strong> <?php echo htmlspecialchars($question['username']); ?></p>
                            <p><strong>Question:</strong> <?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>
                            <p><strong>Asked at:</strong> <?php echo htmlspecialchars($question['asked_at']); ?></p>
                            <form method="post" action="manage_questions.php">
                                <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                                <textarea name="answer_text" placeholder="Write your answer here..." ></textarea>
                                <button type="submit" name="action" value="answer" class="btn">Submit Answer</button>
                                <button type="submit" name="action" value="delete" class="btn">Delete Question</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No unanswered questions available.</p>
            <?php endif; ?>
        </div>

        <hr>

        <h3>Answered Questions</h3>
        <div class="question-list">
            <?php if (count($answered_questions) > 0): ?>
                <ul>
                    <?php foreach ($answered_questions as $question): ?>
                        <li>
                            <p><strong>User:</strong> <?php echo htmlspecialchars($question['username']); ?></p>
                            <form method="post" action="manage_questions.php">
                                <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                                <label for="question_text">Question:</label>
                                <textarea name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                                <label for="answer_text">Answer:</label>
                                <textarea name="answer_text" required><?php echo htmlspecialchars($question['answer_text']); ?></textarea>
                                <button type="submit" name="action" value="edit" class="btn">Update Question and Answer</button>
                                <button type="submit" name="action" value="delete" class="btn">Delete Question</button>
                            </form>
                            <p><strong>Asked at:</strong> <?php echo htmlspecialchars($question['asked_at']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No answered questions available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
