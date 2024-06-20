<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: signin.php");
    exit();
}

$success = ''; // Variable to store success messages

// Fetch all resources
$stmt = $pdo->prepare("SELECT r.*, u.username, u.email, f.faculty_name FROM resources r JOIN users u ON r.user_id = u.user_id JOIN faculties f ON r.faculty_id = f.faculty_id ORDER BY r.created_at DESC");
$stmt->execute();
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

$approved_resources = array_filter($resources, function($resource) {
    return $resource['pending_acceptance'] == 0;
});

$pending_resources = array_filter($resources, function($resource) {
    return $resource['pending_acceptance'] == 1;
});

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $resource_id = $_POST['resource_id'];
        $stmt = $pdo->prepare("UPDATE resources SET pending_acceptance = 0 WHERE resource_id = ?");
        $stmt->execute([$resource_id]);
        header("Location: manage_resources.php?success=Resource+approved+successfully.");
        exit();
    } elseif (isset($_POST['delete'])) {
        $resource_id = $_POST['resource_id'];
        $stmt = $pdo->prepare("DELETE FROM resources WHERE resource_id = ?");
        $stmt->execute([$resource_id]);
        header("Location: manage_resources.php?success=Resource+deleted+successfully.");
        exit();
    }
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/manage_resources.css">
    <title>Manage Resources - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <h1>Manage Resources</h1>

    <div class="container">
        <h2>Pending Approval</h2>
        <?php if (count($pending_resources) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Uploaded By</th>
                        <th>Faculty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_resources as $resource): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($resource['cover_picture']); ?>" alt="Resource Image" class="resource-img"></td>
                            <td><?php echo htmlspecialchars($resource['title']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($resource['description'])); ?></td>
                            <td>RM <?php echo number_format($resource['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($resource['username']); ?> (<?php echo htmlspecialchars($resource['email']); ?>)</td>
                            <td><?php echo htmlspecialchars($resource['faculty_name']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" class="btn">View</a>
                                <a href="edit_resource.php?id=<?php echo $resource['resource_id']; ?>" class="btn btn-edit">Edit</a>
                                <form method="post" action="manage_resources.php" style="display:inline;">
                                    <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                                    <button type="submit" name="approve" class="btn">Approve</button>
                                    <button type="submit" name="delete" class="btn btn-cancel">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <h4>No resources pending approval.</h4>
        <?php endif; ?>
    </div>

    <br>

    <div class="container">
        <br>
        <?php if ($success): ?>
            <div class="alert success">
                <div class="alert--content">
                    <div class="alert--words"><?php echo htmlspecialchars($success); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <h2>Approved Resources</h2>
        <?php if (count($approved_resources) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Uploaded By</th>
                        <th>Faculty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approved_resources as $resource): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($resource['cover_picture']); ?>" alt="Resource Image" class="resource-img"></td>
                            <td><?php echo htmlspecialchars($resource['title']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($resource['description'])); ?></td>
                            <td>RM <?php echo number_format($resource['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($resource['username']); ?> (<?php echo htmlspecialchars($resource['email']); ?>)</td>
                            <td><?php echo htmlspecialchars($resource['faculty_name']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" class="btn">View</a>
                                <a href="edit_resource.php?id=<?php echo $resource['resource_id']; ?>" class="btn btn-edit">Edit</a>
                                <form method="post" action="manage_resources.php" style="display:inline;">
                                    <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-cancel">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <h4>No approved resources available.</h4>
        <?php endif; ?>
    </div>
</body>
</html>