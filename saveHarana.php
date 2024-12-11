<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = mysqli_real_escape_string($conn, $_POST['to']);
    $from = mysqli_real_escape_string($conn, $_POST['from']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $fileNewName = null;
    if (isset($_FILES['haranaFile']) && $_FILES['haranaFile']['error'] === 0) {
        $file = $_FILES['haranaFile'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['mp3', 'wav'];
        if (in_array($fileExt, $allowed)) {
            $fileNewName = uniqid('', true) . "." . $fileExt;
            $fileDestination = 'uploads/' . $fileNewName;

            if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Invalid file type. Allowed types: mp3, wav.";
            exit();
        }
    }

    $query = "INSERT INTO harana_dedications (to_name, from_name, message, file_path, song_name) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $songName = isset($_POST['song_name']) ? $_POST['song_name'] : null;

    $stmt->bind_param('sssss', $to, $from, $message, $fileNewName, $songName);

    if ($stmt->execute()) {
        header("Location: DedicateAHarana.php?success=1");
        exit();
    } else {
        echo "Error saving dedication: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>