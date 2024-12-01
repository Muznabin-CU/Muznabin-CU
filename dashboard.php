<?php 
session_start();

// Check if the user is logged in, else redirect to login
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();  // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: index.html");  // Redirect to the home page
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ReviewSystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user']) && (!isset($_GET['page']) || $_GET['page'] !== 'login' && $_GET['page'] !== 'register')) {
    header("Location: dashboard.php?page=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AnimeVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3b82f6;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-bottom: 1rem;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 1rem;
        }

        section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #1f2937;
            margin-bottom: 1rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        form input, form select, form button {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
        }

        form button {
            background-color: #3b82f6;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #2563eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table th, table td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid #d1d5db;
        }

        table th {
            background-color: #3b82f6;
            color: white;
        }

        table img {
            max-width: 80px;
            border-radius: 6px;
        }

        .logout-btn {
            background-color: #f87171;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 10px 0;
        }

        .logout-btn:hover {
            background-color: #f43f5e;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        <a href="?logout=true" class="logout-btn">Logout</a> <!-- Logout link with query parameter -->
    </header>

    <div class="container">
        <!-- Search Anime Section -->
        <section>
            <h2>Search for an Anime</h2>
            <form method="GET" action="dashboard.php">
                <input type="text" name="search" placeholder="Search by name or genre..." required>
                <button type="submit">Search</button>
            </form>
        </section>

        <!-- Add Anime Section -->
        <section>
            <h2>Add a New Anime</h2>
            <form method="POST" action="Combined.php">
                <input type="text" name="anime-name" placeholder="Anime Name" required>
                <select name="genre" required>
                    <option value="1">Action</option>
                    <option value="2">Adventure</option>
                    <option value="3">Mystery</option>
                    <option value="4">Romance</option>
                </select>
                <input type="text" name="thumbnail" placeholder="Thumbnail URL" required>
                <button type="submit" name="add-anime">Add Anime</button>
            </form>
        </section>

        <!-- Anime List Section -->
        <section>
            <h2>Discover Anime</h2>
            <table>
                <thead>
                    <tr>
                        <th>Anime Name</th>
                        <th>Genre</th>
                        <th>Thumbnail</th>
                        <th>Rating</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT 
                                Animes.AnimeID, 
                                Animes.Name, 
                                Genres.GenreName, 
                                Animes.Thumbnail, 
                                AVG(RatingsReviews.RatingPoints) AS average_rating
                            FROM Animes
                            LEFT JOIN Genres ON Animes.GenreID = Genres.GenreID
                            LEFT JOIN RatingsReviews ON Animes.AnimeID = RatingsReviews.AnimeID
                            GROUP BY Animes.AnimeID";

                    $result = $conn->query($sql);

                    while ($row = $result->fetch_assoc()) {
                        $thumbnail = htmlspecialchars($row['Thumbnail']);
                        $averageRating = $row['average_rating'] ? round($row['average_rating'], 1) : "No Ratings";

                        echo "<tr>
                                <td>" . htmlspecialchars($row['Name']) . "</td>
                                <td>" . htmlspecialchars($row['GenreName']) . "</td>
                                <td><img src='$thumbnail' alt='Thumbnail'></td>
                                <td>$averageRating / 5</td>
                                <td><a href='rate.php?anime_id=" . htmlspecialchars($row['AnimeID']) . "'>Rate</a></td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
