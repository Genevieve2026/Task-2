<?php 
session_start();

// Database connection (not used in this snippet but ready)
$host = 'localhost';
$db = 'glh_db';
$dbuser = 'root';
$pass = '';

$conn = new mysqli($host, $dbuser, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screen Reader Settings</title>
    <link rel="stylesheet" href="../css/profile.css" />

</head>
<body>

<!-- Screen Reader Settings -->

<div id="screen-reader-container">
    <h2>Accessibility Settings</h2>
    <button id="toggle-reader">Enable Screen Reader</button>
    <label>
        Speed: <input type="range" id="speed-control" min="0.5" max="2" step="0.1" value="1">
    </label>
    <label>
        Voice: <select id="voice-select"></select>
    </label>

    <!-- allows user to select language for screen reader -->
    Translator:
    <div id="google_translate_element"></div>
</div>
</div>




<script src="../js/settings.js"></script>
</body>
</html>