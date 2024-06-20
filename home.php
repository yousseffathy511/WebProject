<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session to manage user authentication
}

// Check if the user is logged in and is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: signin.php"); // Redirect to signin page if not logged in or is admin
    exit();
}

require 'db.php'; // Include the database connection

// Fetch faculties for the dropdown
$faculty_stmt = $pdo->prepare("SELECT faculty_id, faculty_name FROM faculties");
$faculty_stmt->execute();
$faculties = $faculty_stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all faculties as associative array

// Initialize variables
$search = '';
$selected_faculty = '';

// Handle search and filtering
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get and sanitize search and faculty filter inputs
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';

    // Build the query with search and filter
    $query = "SELECT r.resource_id, r.title, r.price, r.cover_picture FROM resources r WHERE r.pending_acceptance = 0";

    if (!empty($search)) {
        $query .= " AND r.title LIKE :search";
    }

    if (!empty($selected_faculty)) {
        $query .= " AND r.faculty_id = :faculty_id";
    }

    $stmt = $pdo->prepare($query); // Prepare the SQL statement to prevent SQL injection

    // Bind values to the query parameters
    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }

    if (!empty($selected_faculty)) {
        $stmt->bindValue(':faculty_id', $selected_faculty);
    }

    $stmt->execute(); // Execute the prepared statement
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all resources as associative array
} else {
    // Fetch accepted resources without filters
    $stmt = $pdo->prepare("SELECT r.resource_id, r.title, r.price, r.cover_picture FROM resources r WHERE r.pending_acceptance = 0");
    $stmt->execute();
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all resources as associative array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
    <title>Home - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="main-container">
        <h1>Welcome to MMU Resources, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h2>Accepted Resources</h2>
        <form method="get" action="">
            <div class="search-filter-container">
                <div class="search-bar">
                    <input type="text" id="search" name="search" placeholder="Search by title" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
                <div class="filter-dropdown">
                    <select id="faculty" name="faculty">
                        <option value="">All Faculties</option>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?php echo htmlspecialchars($faculty['faculty_id']); ?>" <?php echo ($selected_faculty == $faculty['faculty_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($faculty['faculty_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
        <div class="resources">
            <?php if (count($resources) > 0): ?>
                <?php foreach ($resources as $resource): ?>
                    <div class="resource-card">
                        <a href="resource_detail.php?id=<?php echo htmlspecialchars($resource['resource_id']); ?>">
                            <img src="<?php echo htmlspecialchars($resource['cover_picture']); ?>" alt="Cover Picture" class="cover-picture">
                            <div class="resource-details">
                                <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                <p><strong>Price:</strong> RM <?php echo number_format($resource['price'], 2); ?></p>
                                <a href="resource_detail.php?id=<?php echo htmlspecialchars($resource['resource_id']); ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No resources available.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
<!-- 212313 -->