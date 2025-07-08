<?php
// Set your admin password here (change this to something secure!)
$admin_password = "YourStrongPassword";
$skills_file = "skills.json";

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

// Skill addition logic
$skill_message = '';
$skills = [];
if (file_exists($skills_file)) {
    $skills = json_decode(file_get_contents($skills_file), true);
    if (!is_array($skills)) $skills = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_skill'])) {
    $new_skill = trim($_POST['new_skill']);
    if ($new_skill !== '' && !in_array($new_skill, $skills)) {
        $skills[] = $new_skill;
        file_put_contents($skills_file, json_encode($skills, JSON_PRETTY_PRINT));
        $skill_message = "Skill added!";
    } elseif (in_array($new_skill, $skills)) {
        $skill_message = "Skill already exists.";
    } else {
        $skill_message = "Please enter a valid skill.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 2rem; }
        .admin-section { background: #fff; padding: 2rem; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 2px 12px rgba(0,0,0,0.07);}
        h2 { color: #1a2230; }
        .message { color: #008060; }
        .error { color: #c00; }
        ul { padding-left: 1.2em; }
        li { margin-bottom: 0.2em; }
    </style>
</head>
<body>
    <div class="admin-section">
        <h2>Upload Project Video (Admin Only)</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" required>
            <button type="submit">Upload</button>
        </form>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>

        <hr>

        <h2>Add New Skill</h2>
        <form method="post">
            <input type="text" name="new_skill" placeholder="e.g. Laravel, React" required>
            <button type="submit">Add Skill</button>
        </form>
        <?php if ($skill_message) echo "<p class='message'>$skill_message</p>"; ?>

        <h3>Current Skills:</h3>
        <ul>
            <?php foreach ($skills as $skill) {
                echo "<li>" . htmlspecialchars($skill) . "</li>";
            } ?>
        </ul>

        <hr>
        <form method="post" style="margin-top:15px;">
            <input type="hidden" name="logout" value="1">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
