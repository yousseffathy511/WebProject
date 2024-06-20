<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session to manage user authentication
}

// Check if the user is logged in and total amount is set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['total_amount'])) {
    header("Location: signin.php"); // Redirect to signin page if not logged in or total amount not set
    exit();
}

require 'db.php'; // Include the database connection

// Initialize error message variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted
    // Sanitize user inputs to prevent XSS attacks
    $card_number = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
    $card_expiry = filter_input(INPUT_POST, 'card_expiry', FILTER_SANITIZE_STRING);
    $card_cvc = filter_input(INPUT_POST, 'card_cvc', FILTER_SANITIZE_STRING);

    // Validate payment details
    $expiryRegex = '/^(0[1-9]|1[0-2])\/?([0-9]{2})$/';
    $errors = [];

    if (!preg_match($expiryRegex, $card_expiry)) {
        $errors[] = 'Invalid expiry date format. Use MM/YY.';
    }

    if (!preg_match('/^\d{3}$/', $card_cvc)) {
        $errors[] = 'CVC must be 3 digits.';
    }

    if (empty($errors)) {
        // Assume payment is successful
        $_SESSION['payment_success'] = true;
        header("Location: checkout.php"); // Redirect to the checkout page after successful payment
        exit();
    } else {
        $error = implode(' ', $errors); // Concatenate error messages
    }
}

$total_amount = $_SESSION['total_amount']; // Retrieve the total amount from the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"> <!-- Link to the CSS file -->
    <link rel="stylesheet" href="css/payment.css"> <!-- Link to the payment CSS file -->
    <title>Payment - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Include the header -->
    <div class="container">
        <h2>Payment</h2>
        <p>Outstanding Sum: RM <?php echo number_format($total_amount, 2); ?></p> <!-- Display the total amount -->
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p> <!-- Display error message -->
        <?php endif; ?>
        <form method="post" action="payment.php">
            <div class="form-group">
                <label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number" required placeholder="1234 5678 9101 1121"> <!-- Card number input field -->
                <div class="card-icons">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
                 </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="card_expiry">Expiration date (MM/YY):</label>
                    <input type="text" id="card_expiry" name="card_expiry" required placeholder="MM / YY"> <!-- Expiration date input field -->
                </div>
                <div class="form-group">
                    <label for="card_cvc">Security code:</label>
                    <input type="text" id="card_cvc" name="card_cvc" required placeholder="CVC"> <!-- CVC input field -->
                </div>
            </div>
            <button type="submit" class="btn">Pay Now</button> <!-- Submit button for the form -->
        </form>
    </div>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            const cardExpiry = document.getElementById('card_expiry').value;
            const cardCvc = document.getElementById('card_cvc').value;
            const expiryRegex = /^(0[1-9]|1[0-2])\/?([0-9]{2})$/;

            let errorMessage = '';

            // Validate the expiration date format
            if (!expiryRegex.test(cardExpiry)) {
                errorMessage += 'Invalid expiry date format. Use MM/YY. ';
            }

            // Validate the CVC format
            if (!/^\d{3}$/.test(cardCvc)) {
                errorMessage += 'CVC must be 3 digits.';
            }

            // Display error message if validation fails
            if (errorMessage) {
                event.preventDefault();
                alert(errorMessage);
            }
        });
    </script>
</body>
</html>
