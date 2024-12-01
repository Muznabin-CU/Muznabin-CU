<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ReviewSystem1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    // Check if email already exists
    $checkEmail = "SELECT * FROM Users WHERE Email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='register.html';</script>";
    } else {
        // Insert new user into database
        $sql = "INSERT INTO Users (UserName, Email, Password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! Please login.'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error: Could not register. Please try again.'); window.location.href='register.html';</script>";
        }
    }
}

// Handle Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if user exists
    $sql = "SELECT * FROM Users WHERE Email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user'] = $user['UserName'];
            $_SESSION['user_id'] = $user['UserID'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('No user found! Please register.'); window.location.href='register.html';</script>";
    }
}

// Handle Add Book
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add-book'])) {
    // Sanitize inputs
    $bookName = trim($_POST['book-name']);
    $genre = $_POST['genre'];
    $thumbnail = trim($_POST['thumbnail']);

    // Insert book into the database
    $sql = "INSERT INTO Books (Name, GenreID, Thumbnail) VALUES ('$bookName', '$genre', '$thumbnail')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Book added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Could not add book. Please try again.'); window.location.href='dashboard.php';</script>";
    }
}



// Handle Rating and Review Submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['rate'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $bookId = $_POST['book_id'];
    $rating = $_POST['rating'];
    $review = trim($_POST['review']);

    $sql = "INSERT INTO RatingsReviews (UserID, BookID, RatingPoints, Description) 
            VALUES ('$userId', '$bookId', '$rating', '$review')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Review submitted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Could not submit review. Please try again.'); window.location.href='dashboard.php';</script>";
    }
}
?>