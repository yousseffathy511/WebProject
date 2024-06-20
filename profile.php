<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
require 'db.php';

$user_id = $_SESSION['user_id'];
$default_profile_picture = 'profilePicture/super/default.png'; // Update this path to your default profile picture

// Function to fetch user data from the database
function getUserData($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT username, email, profile_picture FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$error = '';
$success = '';

// Initial load, get user data
$users = getUserData($pdo, $user_id);

if (!$users) {
    // Handle case where users data is not found
    $error = "User data not found.";
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['remove_picture'])) {
            $profile_picture = $default_profile_picture;

            // Update the profile picture to default in the database
            $stmt = $pdo->prepare("UPDATE Users SET profile_picture = ? WHERE user_id = ?");
            $stmt->execute([$profile_picture, $user_id]);

            $success = 'Profile picture removed successfully.';

            // Refresh user data
            $users = getUserData($pdo, $user_id);
            // Refresh session data
            $_SESSION['profile_picture'] = $users['profile_picture'];
        } else {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                $target_dir = "profilePicture/";
                $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                        $profile_picture = $target_file;
                    } else {
                        $error = 'Failed to upload profile picture.';
                    }
                } else {
                    $error = 'Only JPG, JPEG, PNG, and GIF files are allowed for the profile picture.';
                }
            } else {
                $profile_picture = $users['profile_picture'];
            }

            if (!empty($username) && !empty($email)) {
                if (!empty($password) && $password !== $confirm_password) {
                    $error = 'Passwords do not match.';
                } else {
                    try {
                        // Update username, email, and profile picture
                        $stmt = $pdo->prepare("UPDATE Users SET username = ?, email = ?, profile_picture = ? WHERE user_id = ?");
                        $stmt->execute([$username, $email, $profile_picture, $user_id]);

                        if (!empty($password)) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
                            $stmt->execute([$hashed_password, $user_id]);
                        }
                        $success = 'Profile updated successfully.';

                        // Refresh user data
                        $users = getUserData($pdo, $user_id);
                        // Refresh session data
                        $_SESSION['username'] = $users['username'];
                        $_SESSION['email'] = $users['email'];
                        $_SESSION['profile_picture'] = $users['profile_picture'];
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) { // Duplicate entry
                            $error = 'Username or email already exists.';
                        } else {
                            $error = 'Failed to update profile: ' . $e->getMessage();
                        }
                    }
                }
            } else {
                $error = 'Please fill in all required fields.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile-style.css">
    <link rel="stylesheet" href="css/alert.css">
    <title>Profile - MMU Resources</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-pic-container">
                <img src="<?php echo htmlspecialchars($users['profile_picture'] ?? $default_profile_picture); ?>" alt="Profile Picture" class="profile-pic">
                <div class="svg-container">
                    <label for="profile_picture" class="custom-file-upload">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 7C9.24 7 7 9.24 7 12C7 14.76 9.24 17 12 17C14.76 17 17 14.76 17 12C17 9.24 14.76 7 12 7ZM12 15C10.35 15 9 13.65 9 12C9 10.35 10.35 9 12 9C13.65 9 15 10.35 15 12C15 13.65 13.65 15 12 15ZM20 4H16.83L15 2H9L7.17 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 18H4V6H6.85L8.66 4H15.34L17.15 6H20V18Z" fill="white"/>
                     </svg>
                    </label>
                </div>
                <form method="post" action="profile.php" class="remove-picture-form">
                    <?php if ($users['profile_picture'] && $users['profile_picture'] != $default_profile_picture): ?>
                        <button type="submit" class="btn remove-photo-btn">Remove Picture</button>
                    <?php endif; ?>
                    <input type="hidden" name="remove_picture" value="1">
                </form>
            </div>
            <h2><?php echo htmlspecialchars($users['username']); ?></h2>
            <p><?php echo htmlspecialchars($users['email']); ?></p>
        </div>
        <div class="profile-main">
            <h2>Manage Profile</h2>
            <?php if ($error): ?>
                <div class="alert error">
                    <div class="alert--content">
                        <div class="alert--words"><?php echo htmlspecialchars($error); ?></div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert success">
                    <div class="alert--content">
                        <div class="alert--words"><?php echo htmlspecialchars($success); ?></div>
                    </div>
                </div>
            <?php endif; ?>
            <form method="post" action="profile.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($users['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($users['email']); ?>" required>
                </div>
                <div class="form-group">
                    <input type="file" id="profile_picture" name="profile_picture" accept=".jpg,.jpeg,.png,.gif" style="display:none">
                </div>
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" placeholder="••••••••••••">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••••••">
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn">Update Profile</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='home.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
