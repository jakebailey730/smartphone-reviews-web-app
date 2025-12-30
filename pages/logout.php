<?php
if (session_status() === PHP_SESSION_NONE) session_start(); // start the session if one is not open
// clear the session data and destroy session
session_unset();
session_destroy();

// start a new session to show the logout success message
session_start();
$_SESSION['logout_success'] = true;

// redirect to login page where the logout success message is displayed
header('Location: login.php');
exit;
