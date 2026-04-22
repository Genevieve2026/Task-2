<!-- in searchbar section, add a results container [<div id="search-results"></div>]--> 

<?php
$conn = new mysqli("localhost", "root", "", "glh_db", 3306);

$query = $_GET['query'] ?? '';

$sql = "SELECT id, name, image FROM products WHERE name LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);
$search = "%$query%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <a class='search-item' href='product.php?id={$row['id']}'>
            <img src='images/{$row['image']}' alt=''>
            <span>{$row['name']}</span>
        </a>
        ";
    }
} else {
    echo "<p class='no-results'>No products found</p>";
}
?>