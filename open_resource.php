<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

$file_path = isset($_GET['path']) ? urldecode($_GET['path']) : '';
if (!$file_path || !file_exists($file_path)) {
    echo "File not found.";
    exit();
}

$file_type = mime_content_type($file_path);
if ($file_type != 'application/pdf') {
    echo "Unsupported file type.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>View Document - MMU Resources</title>
</head>
<body>
 <iframe src="<?php echo htmlspecialchars($file_path); ?>" width="100%" height="1000px"></iframe>
</body>
</html>
