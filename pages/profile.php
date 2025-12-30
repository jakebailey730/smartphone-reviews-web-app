<?php
require_once '../includes/functions.php'; // load functions
if (session_status() === PHP_SESSION_NONE) { // if statement to check if php session is open and if not start session
    session_start();
}

$tusers = loadUsers(); // loads users from the JSON file
$tuser = null;

// finds the currently logged in user
$tuser = null;
foreach ($tusers as $key => $entry) {
    if ($entry['username'] === $_SESSION['username']) {
        $tuser = &$tusers[$key];
        break;
    }
}

// loads all the smartphone and review data and locates the reviews made by this user 
$tsmartphones = loadSmartphones();
$treviews = loadReviews();
$tuserReviews = array_filter($treviews, fn($r) => $r['username'] === $_SESSION['username']);

$tsuccess = '';
$terrorUser = '';
$terrorEmail = '';

// handle login and logout success alerts
$tloginSuccess = false;
$tlogoutSuccess = false;

if (isset($_SESSION['login_success'])) {
    $tloginSuccess = true;
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['logout_success'])) {
    $tlogoutSuccess = true;
    unset($_SESSION['logout_success']);
}

// form for if the user is attempting to update their profile details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tnewUser = trim($_POST['username']);
    $tnewEmail = trim($_POST['email']);
    $thasError = false;

    // foreach statement to prevent the user from updating either the username or email to one already registered 
    foreach ($tusers as $check) {
        if ($check['username'] !== $tuser['username'] && strtolower($check['username']) === strtolower($tnewUser)) {
            $terrorUser = "That username is already taken.";
            $thasError = true;
        }
        if ($check['email'] !== $tuser['email'] && strtolower($check['email']) === strtolower($tnewEmail)) {
            $terrorEmail = "That email is already registered.";
            $thasError = true;
        }
    }

    // if statement to update the users details to the JSON file if thasError did not take place
    if (!$thasError) {
        $tuser['username'] = $tnewUser;
        $tuser['first_name'] = trim($_POST['first_name']);
        $tuser['last_name'] = trim($_POST['last_name']);
        $tuser['email'] = $tnewEmail;
        $tuser['favourite'] = trim($_POST['favourite']);
        saveUsers($tusers);
        $_SESSION['username'] = $tnewUser;
        $tsuccess = "Profile updated successfully!";
    }
}

function buildProfilePage($tuser, $tsmartphones, $tuserReviews, $tsuccess, $terrorUser, $terrorEmail, $tloginSuccess, $tlogoutSuccess)
{
    $tuserEsc = $tuser; // function created for the profile page 

    // adds success and error messages to the displayed to the user using bootstrap alert box styling
    $tfeedback = '';
    if ($tsuccess) $tfeedback .= '<div class="alert alert-success">' . ($tsuccess) . '</div>';
    if ($tloginSuccess) $tfeedback .= '<div class="alert alert-success">Login successful!</div>';
    if ($tlogoutSuccess) $tfeedback .= '<div class="alert alert-success">Logout successful!</div>';

    $terrorUserHTML = $terrorUser ? '<div class="text-danger">' . ($terrorUser) . '</div>' : '';
    $terrorEmailHTML = $terrorEmail ? '<div class="text-danger">' . ($terrorEmail) . '</div>' : '';

    // creatation of the dropdown menu showing all the smartphones on the JSON file, dynamically updated if any further smartphones are added to the file
    $tselectOptions = '';
    foreach ($tsmartphones as $phone) {
        $model = ($phone['model']);
        $selected = ($tuser['favourite'] === $phone['model']) ? 'selected' : '';
        $tselectOptions .= "<option value=\"{$model}\" {$selected}>{$model}</option>";
    }

    // shows the user a message if no reviews are found linking to their username 
    // if statement if reviews are found linking to their username
    // presents the user with their reviews from the JSON file
    $treviewSection = '<p>You haven’t submitted any reviews yet.</p>';
    if (!empty($tuserReviews)) {
        $treviewSection = '<ul class="list-group">';
        foreach ($tuserReviews as $review) {
            $model = 'Unknown Smartphone';
            foreach ($tsmartphones as $s) {
                if ($s['id'] == $review['smartphone_id']) {
                    $model = $s['model'];
                    break;
                }
            }
            $rtext = ($review['review']);
            $rdate = ($review['timestamp']);

            // HTML styling creatation for the review box using bootstrap classes
            $treviewSection .= <<<REVIEW
<li class="list-group-item">
    <strong>{$model}:</strong><br>
    <p class="review-box">{$rtext}</p>
    <small class="text-muted">Submitted on: {$rdate}</small>
</li>
REVIEW;
        }
        $treviewSection .= '</ul>';
    }

    // HTML content creatation for the main body of the page 
    $tcontent = <<<PAGE
<main>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Profile</h2>
        <a href="/Coursework2/index.php" class="btn btn-secondary">Return Home</a>
    </div>
    {$tfeedback}
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{$tuserEsc['username']}" required>
            {$terrorUserHTML}
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" value="{$tuserEsc['first_name']}" required>
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" value="{$tuserEsc['last_name']}" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{$tuserEsc['email']}" required>
            {$terrorEmailHTML}
        </div>
        <div class="mb-3">
            <label>Favourite Smartphone</label>
            <select name="favourite" class="form-select">
                <option value="">-- Select Favourite --</option>
                {$tselectOptions}
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Profile</button>
    </form>

    <div class="mt-5">
        <h3>My Reviews</h3>
        {$treviewSection}
    </div>
</div>
</main>
PAGE;

    return $tcontent; // returns the HTML content so the function can be called effectively  
}

// includes both the header and the footer and echos the function to create the visualisation
include '../includes/header.php';
echo buildProfilePage($tuser, $tsmartphones, $tuserReviews, $tsuccess, $terrorUser, $terrorEmail, $tloginSuccess, $tlogoutSuccess);
include '../includes/footer.php';
