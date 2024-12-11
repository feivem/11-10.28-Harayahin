<?php  
include 'db_connection.php'; 
?>

<html>
<head>
    <title>Find Your Harana</title>
    <link href="https://fonts.googleapis.com/css2?family=Sono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="FindYourHarana.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="header-text">
        Find your Harana
    </div>

    <div class="search-bar">
        <form method="GET" action="">
            <input 
                type="text" 
                name="name" 
                placeholder="Enter recipient's name..." 
                class="search-input" 
                required>
            <button type="submit" class="search-button">Search</button>
        </form> 
    </div>

    <div class="results-section" id="resultsSection" <?php echo isset($_GET['name']) ? 'style="display:block;"' : 'style="display:none;"'; ?>>
        <h2>Search Results</h2>
        <?php
        if (isset($_GET['name']) && !empty(trim($_GET['name']))) {
            $name = $conn->real_escape_string(trim($_GET['name']));

            $query = "SELECT * FROM harana_dedications WHERE to_name LIKE '%$name%' ORDER BY timestamp DESC";
            $result = $conn->query($query);

            if (!$result) {
                echo "Query Error: " . $conn->error;
            }

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="dedication-card">';
                    echo '<h3>Song: ' . htmlspecialchars($row['song_name']) . '</h3>';
                    echo '<p><strong>To:</strong> ' . htmlspecialchars($row['to_name']) . '</p>';
                    echo '<p><strong>From:</strong> ' . htmlspecialchars($row['from_name']) . '</p>';
                    echo '<p><strong>Message:</strong> ' . nl2br(htmlspecialchars($row['message'])) . '</p>';
                    
                    if (!empty($row['file_path']) && file_exists('uploads/' . $row['file_path'])) {
                        echo '<audio controls>';
                        echo '<source src="uploads/' . htmlspecialchars($row['file_path']) . '" type="audio/mpeg">';
                        echo 'Your browser does not support the audio element.';
                        echo '</audio>';
                    } else {
                        echo '<p>No recording available for this dedication.</p>';
                    }

                    echo '<p><strong>Posted: </strong> ' . htmlspecialchars($row['timestamp']) . '</p>';

                    echo '<p><a href="edit_harana.php?id=' . $row['id'] . '" class="edit-button">Edit</a> ';
                    echo '<a href="delete_harana.php?id=' . $row['id'] . '" class="delete-button" onclick="return confirm(\'Are you sure you want to delete this harana?\')">Delete</a></p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No haranas found for this name.</p>';
            }
        } else {
            echo '<p>Please enter a name to search for.</p>';
        }
        ?>
    </div>

    <script>
        const resultsSection = document.getElementById('resultsSection');
        const searchForm = document.querySelector('form');

        if (window.location.search.includes('name=')) {
            resultsSection.style.display = 'block';
        } else {
            resultsSection.style.display = 'none';
        }

        searchForm.addEventListener('submit', function() {
            resultsSection.style.display = 'block';
        });
    </script>
</body>
</html>