<?php
// Set your admin password here (change this to something secure!)
$admin_password = "YourStrongPassword";

// Simple authentication
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_upload.php");
    exit;
}
if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['authenticated'] = true;
        } else {
            $error = "Incorrect password!";
        }
    } else {
        // Show login form
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login</title>
        </head>
        <body>
            <h2>Admin Login</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="post">
                <input type="password" name="password" placeholder="Enter admin password" required>
                <button type="submit">Login</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

// Video upload logic
$upload_dir = "uploads/";
$max_file_size = 100 * 1024 * 1024; // 100MB max
$allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $file = $_FILES['video'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        if ($file['size'] > $max_file_size) {
            $message = "File is too large. Max allowed: 100MB.";
        } elseif (!in_array($file['type'], $allowed_types)) {
            $message = "Only .mp4, .webm, and .ogg videos are allowed.";
        } else {
            // Sanitize file name
            $filename = preg_replace("/[^A-Za-z0-9_\-\.]/", '_', basename($file['name']));
            $target = $upload_dir . $filename;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $message = "Upload successful!";
            } else {
                $message = "Upload failed.";
            }
        }
    } else {
        $message = "No file uploaded or unknown error.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Upload (Admin Only)</title>
</head>
<body>
    <h2>Upload Project Video (Admin Only)</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" required>
        <button type="submit">Upload</button>
    </form>
    <form method="post" style="margin-top:15px;">
        <input type="hidden" name="logout" value="1">
        <button type="submit">Logout</button>
    </form>
    <?php if ($message) echo "<p>$message</p>"; ?>
</body>
</html>
