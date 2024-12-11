<?php 
include 'db_connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $query = "SELECT * FROM harana_dedications WHERE id = '$id'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $song_name = $conn->real_escape_string($_POST['song_name']);
        $to_name = $conn->real_escape_string($_POST['to_name']);
        $from_name = $conn->real_escape_string($_POST['from_name']);
        $message = $conn->real_escape_string($_POST['message']);
        
        // Keep the current file path by default
        $fileNewName = $row['file_path'];

        // Handle file upload, only if a new file is uploaded
        if (isset($_FILES['haranaFile']) && $_FILES['haranaFile']['error'] === 0) {
            $file = $_FILES['haranaFile'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['mp3', 'wav'];
            if (in_array($fileExt, $allowed)) {
                $fileNewName = uniqid('', true) . "." . $fileExt;
                $fileDestination = 'uploads/' . $fileNewName;

                // Move the uploaded file to the destination folder
                if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                    echo "Error uploading file.";
                    exit();
                }

                // If a new file was uploaded, delete the old file (optional, to save space)
                if ($row['file_path'] && file_exists('uploads/' . $row['file_path'])) {
                    unlink('uploads/' . $row['file_path']);
                }
            } else {
                echo "Invalid file type. Allowed types: mp3, wav.";
                exit();
            }
        }

        // Update the dedication in the database
        $updateQuery = "UPDATE harana_dedications SET song_name = '$song_name', to_name = '$to_name', from_name = '$from_name', message = '$message', file_path = '$fileNewName' WHERE id = '$id'";

        if ($conn->query($updateQuery)) {
            header('Location: FindYourHarana.php?name=' . $to_name);
            exit();
        } else {
            echo "Error updating harana: " . $conn->error;
        }
    }
} else {
    echo "Invalid ID.";
}
?>

<html>
<head>
    <title>Edit Harana</title>
    <link rel="stylesheet" href="edit_harana.css">
    <link href="https://fonts.googleapis.com/css2?family=Qwitcher+Grypen:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sono:wght@200..800&display=swap" rel="stylesheet">
</head>
<body>
    <h2>Edit Harana</h2>
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="song_name">Song Name:</label>
            <input type="text" name="song_name" placeholder="Enter the song title" value="<?php echo htmlspecialchars($row['song_name']); ?>" required><br>
            
            <label for="haranaFile">Audio File:</label>
            <input type="file" name="haranaFile" accept=".mp3,.wav"><br>
            
            <?php if (!empty($row['file_path'])): ?>
                <p>Current Audio:</p>
                <audio controls>
                    <source src="uploads/<?php echo htmlspecialchars($row['file_path']); ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            <?php endif; ?>
            
            <label for="to_name">To:</label>
            <input type="text" name="to_name" placeholder="Enter the recipient's name" value="<?php echo htmlspecialchars($row['to_name']); ?>" required><br>
            
            <label for="from_name">From:</label>
            <input type="text" name="from_name" placeholder="Enter your name" value="<?php echo htmlspecialchars($row['from_name']); ?>" required><br>
            
            <label for="message">Message:</label>
            <textarea name="message" placeholder="Write your personalized message here" required><?php echo htmlspecialchars($row['message']); ?></textarea><br>
            
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>