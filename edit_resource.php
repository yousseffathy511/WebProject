<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

$resource_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT r.*, u.username, u.email FROM resources r JOIN users u ON r.user_id = u.user_id WHERE r.resource_id = ?");
$stmt->execute([$resource_id]);
$resource = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resource) {
    echo "Resource not found.";
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $stmt = $pdo->prepare("UPDATE resources SET pending_acceptance = 0 WHERE resource_id = ?");
        $stmt->execute([$resource_id]);
        $success = 'Resource approved successfully.';
        $resource['pending_acceptance'] = 0;
    } elseif (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM resources WHERE resource_id = ?");
        $stmt->execute([$resource_id]);
        header("Location: manage_resources.php");
        exit();
    } elseif (isset($_POST['update'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];

        if (!empty($title) && !empty($description)) {
            $stmt = $pdo->prepare("UPDATE resources SET title = ?, description = ? WHERE resource_id = ?");
            if ($stmt->execute([$title, $description, $resource_id])) {
                $success = 'Resource updated successfully.';
                $resource['title'] = $title;
                $resource['description'] = $description;
            } else {
                $error = 'Failed to update resource. Please try again.';
            }
        } else {
            $error = 'Please fill in all fields.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/edit_resource.css">
    <title>Edit Resource - MMU Resources</title>
    
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <a href="manage_resources.php" class="btn-back">Back to Manage Resources</a>
        <h2>Edit Resource</h2>
        <?php if ($error): ?>
            <div class="alert alert__active error">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($error); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert__active success">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($success); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <form method="post" action="edit_resource.php?id=<?php echo $resource_id; ?>">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($resource['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($resource['description']); ?></textarea>
            </div>
            <button type="submit" name="update" class="btn">Update Resource</button>
        </form>
    </div>
</body>
</html>