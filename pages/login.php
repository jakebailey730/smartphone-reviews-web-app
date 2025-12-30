<?php
require_once '../includes/functions.php'; // load functions 
if (session_status() === PHP_SESSION_NONE) session_start(); // start PHP session if one is not open 

$tusers = loadUsers(); // load the user data 

// Message handling
$terror = '';
$tlogoutSuccess = false;

// logout success message shown if the user was redirected from the logout page
// the session flag is removed to ensure this message is only shown one
if (isset($_SESSION['logout_success'])) {
    $tlogoutSuccess = true;
    unset($_SESSION['logout_success']);
}

// gets the sumbitted username and password from the user and trims any whitespace 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tusername = trim($_POST['username']);
    $tpassword = trim($_POST['password']);
    // foreach statement to loop through the username provided with usernames on the JSON file
    // verifies the password with the hashed passion
    // stores the username and a success flag in the session and redirects to the profile page if correct
    // incorrect password message shown if the password could not be verfified
    foreach ($tusers as $tuser) {
        if ($tuser['username'] === $tusername) {
            if (password_verify($tpassword, $tuser['password'])) {
                $_SESSION['username'] = $tuser['username'];
                $_SESSION['login_success'] = true;
                header('Location: profile.php');
                exit;
            } else {
                $terror = "Incorrect password.";
                break;
            }
        }
    }
    // user not found message if the username provided cannot be found within the JSON file
    if (!$terror) {
        $terror = "User not found.";
    }
}

// function to generate the HTML style for the login page
function buildLoginPage($tlogoutSuccess, $terror)
{
    $tmessage = '';
    // if statements to check for logoutsuccess or error and present the correct message in a bootstrap alert box style
    if ($tlogoutSuccess) $tmessage .= '<div class="alert alert-success">Logout successful!</div>';
    if ($terror) $tmessage .= '<div class="alert alert-danger">' . ($terror) . '</div>';

    // HTML main content page
    $tcontent = <<<LOGIN
<main>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Login</h2>
        <a href="/Coursework2/index.php" class="btn btn-secondary">Return Home</a>
    </div>
    {$tmessage}
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</main>
LOGIN;

    return $tcontent;
}

// include both the header and footer and echo the buildLoginPage function to present the visualisation
include '../includes/header.php';
echo buildLoginPage($tlogoutSuccess, $terror);
include '../includes/footer.php';
