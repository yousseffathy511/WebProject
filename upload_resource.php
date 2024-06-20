<?php
session_start(); // Start the session to manage user authentication
require 'db.php'; // Include the database connection

// Check if the user is logged in and is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

// Fetch faculties for the dropdown
$stmt = $pdo->prepare("SELECT faculty_id, faculty_name FROM Faculties");
$stmt->execute();
$faculties = $stmt->fetchAll();

$error = ''; // Initialize an error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted
    // Sanitize inputs to prevent XSS attacks
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $faculty_id = filter_input(INPUT_POST, 'faculty', FILTER_SANITIZE_NUMBER_INT);
    $file_path = $_FILES['file_path'];
    $cover_picture = $_FILES['cover_picture'];

    if (!empty($title) && !empty($description) && !empty($price) && !empty($faculty_id) && !empty($file_path['name']) && !empty($cover_picture['name'])) { // Check if all fields are filled
        // Handle resource file upload
        $target_dir = "resources/";
        $target_file = $target_dir . basename($file_path["name"]);
        $fileFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_file_types = ['pdf', 'doc', 'docx'];

        // Handle cover picture upload
        $cover_target_dir = "coverPictures/";
        $cover_target_file = $cover_target_dir . basename($cover_picture["name"]);
        $coverFileType = strtolower(pathinfo($cover_target_file, PATHINFO_EXTENSION));
        $allowed_cover_types = ['jpg', 'jpeg', 'png'];

        if (in_array($fileFileType, $allowed_file_types) && in_array($coverFileType, $allowed_cover_types)) { // Check if the file types are allowed
            if (move_uploaded_file($file_path["tmp_name"], $target_file) && move_uploaded_file($cover_picture["tmp_name"], $cover_target_file)) {
                // Insert new resource into the database using prepared statements to prevent SQL injection
                $stmt = $pdo->prepare("INSERT INTO Resources (title, description, price, file_path, cover_picture, faculty_id, user_id, pending_acceptance) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$title, $description, $price, $target_file, $cover_target_file, $faculty_id, $_SESSION['user_id']]);

                header("Location: home.php"); // Redirect to the home page after successful upload
                exit();
            } else {
                $error = 'Failed to upload files'; // Set error message if file upload fails
            }
        } else {
            $error = 'Only PDF, DOC, DOCX files for resources and JPG, JPEG, PNG files for cover pictures are allowed'; // Set error message if file types are not allowed
        }
    } else {
        $error = 'Please fill in all fields'; // Set error message if fields are empty
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"> <!-- Link to the CSS file -->
    <title>Upload Resource - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Upload Resource</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p> <!-- Display error message -->
        <?php endif; ?>
        <form method="post" action="upload_resource.php" enctype="multipart/form-data"> <!-- Form to handle file uploads -->
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required> <!-- Title input field -->
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea> <!-- Description input field -->
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" id="price" name="price" required> <!-- Price input field -->
            </div>
            <div class="form-group">
                <label for="faculty">Faculty:</label>
                <select id="faculty" name="faculty" required> <!-- Dropdown for selecting faculty -->
                    <option value="">Select Faculty</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?php echo htmlspecialchars($faculty['faculty_id']); ?>">
                            <?php echo htmlspecialchars($faculty['faculty_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="file_path">Resource File:</label>
                <input type="file" id="file_path" name="file_path" accept=".pdf,.doc,.docx" required> <!-- File input field for resource file -->
            </div>
            <div class="form-group">
                <label for="cover_picture">Cover Picture:</label>
                <input type="file" id="cover_picture" name="cover_picture" accept=".jpg,.jpeg,.png" required> <!-- File input field for cover picture -->
            </div>
            <button type="submit" class="btn">Upload Resource</button> <!-- Submit button -->
        </form>
    </div>
</body>
</html>
