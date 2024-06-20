<?php
session_start();
require 'db.php';

$error = '';
$success = '';
$is_signup = false;

$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        // Sign Up Logic
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $profile_picture = $_FILES['profile_picture'];

        if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
            if ($password === $confirm_password) {
                $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE username = :username OR email = :email");
                $stmt->execute([':username' => $username, ':email' => $email]);
                if ($stmt->fetch()) {
                    $error = 'Username or email already exists';
                } else {
                    $target_file = "profilePicture/super/default.png";
                    if (!empty($profile_picture['name'])) {
                        $target_dir = "profilePicture/";
                        $target_file = $target_dir . basename($profile_picture["name"]);
                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($imageFileType, $allowed_types)) {
                            if (!move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
                                $error = 'Failed to upload profile picture';
                            }
                        } else {
                            $error = 'Only JPG, JPEG, PNG, and GIF files are allowed';
                        }
                    }

                    if (empty($error)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO Users (username, email, password, profile_picture) VALUES (:username, :email, :password, :profile_picture)");
                        $stmt->execute([':username' => $username, ':email' => $email, ':password' => $hashed_password, ':profile_picture' => $target_file]);

                        $user_id = $pdo->lastInsertId();
                        $stmt = $pdo->prepare("SELECT is_admin FROM Users WHERE user_id = :user_id");
                        $stmt->execute([':user_id' => $user_id]);
                        $user = $stmt->fetch();

                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['profile_picture'] = $target_file;
                        $_SESSION['is_admin'] = $user['is_admin'];

                        if ($user['is_admin']) {
                            header("Location: adminpanel.php");
                        } else {
                            header("Location: home.php");
                        }
                        exit();
                    }
                }
            } else {
                $error = 'Passwords do not match';
            }
        } else {
            $error = 'Please fill in all fields';
        }
        $is_signup = true;
    } else if (isset($_POST['signin'])) {
        // Sign In Logic
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if (!empty($email) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT user_id, email, password, profile_picture, is_admin FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['profile_picture'] = $user['profile_picture'];
                $_SESSION['is_admin'] = $user['is_admin'];

                if ($user['is_admin']) {
                    header("Location: adminpanel.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Please fill in all fields';
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
    <link rel="stylesheet" href="css/signin.css">
    <title>Sign In / Sign Up - MMU Resources</title>

</head>
<body>
    <div class="container <?= $is_signup ? 'right-panel-active' : '' ?>" id="container">
        <div class="form-container sign-up-container">
            <form method="post" action="signin.php" enctype="multipart/form-data">
                <h1>Create Account</h1>
                </br>
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required />
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="confirm_password" placeholder="Confirm Password" required />
                <label for="profile_picture" class="custom-file-upload">Choose a pfp</label>
                <input type="file" id="profile_picture" name="profile_picture" accept=".jpg,.jpeg,.png,.gif">
                </br>
                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="post" action="signin.php">
                <h1>Sign in</h1>
                <div class="social-container"></div>
                <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
                <input type="password" name="password" placeholder="Password" required />
                </br>
                <button type="submit" name="signin">Sign In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="alert error">
        <div class="alert--content">
            <div class="alert--words"><?php echo htmlspecialchars($error); ?></div>
        </div>
    </div>
    <?php endif; ?>
    <script src="javascript/script.js"></script>
</body>
</html>