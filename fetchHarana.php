<?php
// Include the database connection
require_once('db_connection.php');

// Fetch previous entries
$entries = [];
$sql = "SELECT * FROM harana_dedications ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
}
?>