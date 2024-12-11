<?php 
include 'db_connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Delete the record from the database
    $query = "DELETE FROM harana_dedications WHERE id = '$id'";

    if ($conn->query($query)) {
        // Redirect back to the FindYourHarana page after deletion
        header('Location: FindYourHarana.php?name=' . $_GET['name']);
        exit();
    } else {
        echo "Error deleting the harana: " . $conn->error;
    }
} else { 
    echo "Invalid request."; 
}
?>