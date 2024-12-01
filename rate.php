<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ReviewSystem1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$bookId = $_GET['book_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate and Review Anime - AnimeVerset</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Rate and Review Anime</h2>
        <form method="POST" action="Combined.php">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($bookId); ?>">
            <label for="rating">Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required><br>
            <label for="review">Review:</label>
            <textarea name="review" required></textarea><br>
            <button type="submit" name="rate">Submit Review</button>
        </form>
    </div>
</body>
</html>