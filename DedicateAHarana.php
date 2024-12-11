<?php  
include 'db_connection.php'; 
?>

<?php
include('fetchHarana.php');
?>

<html>
<head>
    <title>Dedicate a Harana</title>
    <link rel="stylesheet" href="DedicateAHarana.css">

    <!-- FONTS USED -->
    <link href="https://fonts.googleapis.com/css2?family=Qwigley&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Qwitcher+Grypen:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sono:wght@200..800&display=swap" rel="stylesheet">

</head>

<body>
    <?php include 'navbar.php'; ?>

    <h1>Dedicate a Harana</h1>

    <!-- SONG CARD SECTION -->
    <div class="song-card">
				        <div class="song-search">
								            <form id="songSearchForm" action="searchsong.php" method="get" onsubmit="event.preventDefault();">
								                <input type="text" id="songSearch" name="q" placeholder="🔍 Search for a song" required>
								                <button id="searchButton" type="button">Search</button>
								            </form>   
								                
				         <div class="results" id="searchResults">
				                <!-- Search results from searchsong.php will be displayed here -->
				         </div>
     </div>

    <!-- Added Song Section -->
    <div id="song-card-container">
        <!-- Added songs will be displayed here -->
    </div>

    <!-- Dedication Form -->
    <form id="dedicationForm" class="dedication-form" action="saveHarana.php" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" id="selectedSong" name="song_name" value="">

        <label for="haranaFile">🎙️ Sing your own harana? Upload a file here:</label>
        <input type="file" id="haranaFile" name="haranaFile">

        <label for="to">To:</label>
        <input type="text" id="to" name="to" placeholder="Recipient's name" required>
  
        <label for="from">From:</label>
        <input type="text" id="from" name="from" placeholder="Your name" required>
  
        <label for="message">Attach a Message:</label>
        <textarea id="message" name="message" rows="4" placeholder="Write your message here..." required></textarea>
  
        <!-- Form Buttons -->
        <div class="form-footer">
            <div class="send">
                <button type="submit" id="sendButton">Send</button>
            </div>
        </div>
    </form>

<!-- Previous Entries Preview -->
<div id="previous-dedications">
    <h2>Previous Dedications</h2>

    <?php if (empty($entries)): ?>
        <p>No previous dedications available.</p>
    <?php else: ?>
        
        <?php foreach ($entries as $entry): ?>
            <div class="dedication-card">
                <h3>Song: <?php echo htmlspecialchars($entry['song_name']); ?></h3>
                <p><strong>To:</strong> <?php echo htmlspecialchars($entry['to_name']); ?></p>
                <p><strong>From:</strong> <?php echo htmlspecialchars($entry['from_name']); ?></p>
                <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($entry['message'])); ?></p>
                
                <?php if (!empty($entry['file_path']) && file_exists('uploads/' . $entry['file_path'])): ?>
                    <audio controls>
                        <source src="uploads/<?php echo htmlspecialchars($entry['file_path']); ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                <?php else: ?>
                    <p>No recording available for this dedication.</p>
                <?php endif; ?>
                
                <p><strong>Posted:</strong> <?php echo date('F j, Y, g:i a', strtotime($entry['timestamp'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

    <script>
            // Function to add song to the song card section
        function addSong(trackName, artistName, trackUrl) {
            const songCardContainer = document.getElementById("song-card-container");

            // Check if the song is already added to avoid duplicates
            const existingSongs = songCardContainer.querySelectorAll("p");
            for (let song of existingSongs) {
                if (song.textContent === `${trackName} by ${artistName}`) {
                    return; // Song is already added, no need to add again
                }
            }

            const songCard = document.createElement("div");
            songCard.classList.add("song-card");

            const songTitle = document.createElement("p");
            songTitle.textContent = `${trackName} by ${artistName}`;

            const iframe = document.createElement("iframe");
            iframe.src = trackUrl;
            iframe.allowTransparency = "true";
            iframe.allow = "encrypted-media";

            songCard.appendChild(songTitle);
            songCard.appendChild(iframe);

            // Add the song card to the container
            songCardContainer.appendChild(songCard);

            // Set the hidden input value to the selected song name
            document.getElementById("selectedSong").value = `${trackName} by ${artistName}`;

            // Remove the song from the search results
            const results = document.getElementById("searchResults");
            results.innerHTML = ''; // Clear the search results after adding the song
        }

        document.getElementById("searchButton").addEventListener("click", function() {
            const query = document.getElementById("songSearch").value.trim();
            
            if (!query) {
                alert("Please enter a search term.");
                return;
            }

            // Make an AJAX request to searchsong.php
            fetch(`searchsong.php?q=${encodeURIComponent(query)}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("searchResults").innerHTML = data;
                })
                .catch(error => {
                    console.error("Error fetching search results:", error);
                    alert ("Error occurred while fetching the results.");
                });
        });
    </script>
</body>
</html>
