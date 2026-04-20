<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_name = $_POST['display_name'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $theme = $_POST['theme'];
    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $target = "uploads/" . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target);
    }
    // Example: Save to database (pseudo-code)
    /*
    $query = $db->prepare("UPDATE users SET display_name=?, email=?, bio=?, theme=?, avatar=? WHERE id=?");
    $query->execute([$display_name, $email, $bio, $theme, $target, $user_id]);
    */
    echo "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
<div class="settings-container">
    <h2>Profile Settings</h2>
    <form action="update-profile.php" method="POST" enctype="multipart/form-data">
        <label>Profile Picture</label>
        <div class="avatar-preview">
            <img src="uploads/default.png" alt="Profile Picture">
        </div>
        <input type="file" name="avatar">
        <label>Display Name</label>
        <input type="text" name="display_name" placeholder="Your name" required>
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" required>
        <label>Bio</label>
        <textarea name="bio" placeholder="Tell us about yourself"></textarea>
        <label>Theme</label>
        <select name="theme">
            <option value="light">Light</option>
            <option value="dark">Dark</option>
        </select>
        <?php include ('../php/settings.php'); ?>
        <button type="submit">Save Changes</button>
    </form>
</div>
</body>
</html>
