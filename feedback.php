<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session to manage user authentication
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php"); // Redirect to signin page if not logged in
    exit();
}

require 'db.php'; // Include the database connection

$error = ''; // Initialize an error message variable
$success = ''; // Initialize a success message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted
    // Sanitize user input to prevent XSS attacks
    $feedback_text = filter_input(INPUT_POST, 'feedback_text', FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    if (!empty($feedback_text)) { // Check if the feedback text is not empty
        // Insert the user's feedback into the database using prepared statements to prevent SQL injection
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)");
        $stmt->execute([$user_id, $feedback_text]);
        $success = 'Feedback submitted successfully.'; // Set success message
    } else {
        $error = 'Please provide your feedback.'; // Set error message if feedback text is empty
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"> <!-- Link to the CSS file -->
    <title>Feedback - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Include the header -->
    <div class="container">
        <h2>Feedback</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p> <!-- Display error message -->
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p> <!-- Display success message -->
        <?php endif; ?>
        <form method="post" action="feedback.php">
            <div class="form-group">
                <label for="feedback_text">Your Feedback:</label>
                <textarea id="feedback_text" name="feedback_text" required></textarea> <!-- Textarea for the user to enter their feedback -->
            </div>
            <button type="submit" class="btn">Submit Feedback</button> <!-- Submit button for the form -->
        </form>
    </div>
    <?php include 'footer.php'; ?> <!-- Include the footer -->
</body>
</html>
