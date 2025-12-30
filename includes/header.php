<?php
// start PHP session is an open one is not identified
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// desclares the document at HTML
// imports the bootstrap CSS and custom CSS 
// navigation links inputted
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Smartphone Site</title>
    <link href="/Coursework2/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Coursework2/css/style.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <nav>
                <a href="/Coursework2/index.php" class="text-white">Home</a> |
                <a href="/Coursework2/pages/smartphone_ranking.php" class="text-white">Smartphone Ranking</a> |
                <a href="/Coursework2/pages/smartphone_list.php" class="text-white">Smartphone List</a>
            </nav>

            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="text-white me-3">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="/Coursework2/pages/profile.php" class="btn btn-light btn-sm me-2">Profile</a>
                    <a href="/Coursework2/pages/logout.php" class="btn btn-danger btn-sm">Logout</a>
                <?php else: ?>
                    <a href="/Coursework2/pages/login.php" class="btn btn-light btn-sm me-2">Login</a>
                    <a href="/Coursework2/pages/register.php" class="btn btn-light btn-sm">Register</a>
                <?php endif; ?>
            </div>

        </div>
    </header>

    <main>