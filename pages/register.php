<?php
require_once '../includes/functions.php'; // load functions 

// if statement to sure the PHP session is open and if not to start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$terror = '';
$tsuccess = '';

// if statement to ensure the form was sumbitted using POST and to process the data if so 
// retrieve the form inputs and trim it to ensure no errors 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tusername = trim($_POST['username']);
    $tfirst = trim($_POST['first_name']);
    $tlast = trim($_POST['last_name']);
    $temail = trim($_POST['email']);
    $tpass = trim($_POST['password']);

    // if statement to inform the user all fields are required if a form is sumbitted with emtpy fields
    if (empty($tusername) || empty($tfirst) || empty($tlast) || empty($temail) || empty($tpass)) {
        $terror = "All fields are required.";
        // else statement to load all the users saved on the JSON file to check if the username or email is already registered
    } else {
        $tusers = loadUsers();
        foreach ($tusers as $tuser) {
            if (strtolower($tuser['username']) === strtolower($tusername)) {
                $terror = "Username already taken.";
                break;
            }
            if (strtolower($tuser['email']) === strtolower($temail)) {
                $terror = "Email is already registered.";
                break;
            }
        }
        // continues only when terror is not called and creates a new user with all the sumbitted data
        // passwords are hashed for security
        if (!$terror) {
            $tusers[] = [
                'username' => $tusername,
                'first_name' => $tfirst,
                'last_name' => $tlast,
                'email' => $temail,
                'password' => password_hash($tpass, PASSWORD_DEFAULT),
                'favourite' => ''
            ];
            // saves the data to the JSON file and displays a success message with a direct login link
            saveUsers($tusers);
            $tsuccess = "Registration successful! You can now <a href='login.php'>log in</a>.";
        }
    }
}

// creatation of the register page function
function buildRegisterPage($terror, $tsuccess)
{
    // if statement to input the error messages into a bootstrap style alert box
    $tmsg = '';
    if ($terror) {
        $tmsg .= '<div class="alert alert-danger">' . ($terror) . '</div>';
    }
    if ($tsuccess) {
        $tmsg .= '<div class="alert alert-success">' . $tsuccess . '</div>';
    }

    // creatation of the HTML main content
    $tcontent = <<<PAGE
<main>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Register</h2>
            <a href="/Coursework2/index.php" class="btn btn-secondary">Return Home</a>
        </div>

        {$tmsg}

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</main>
PAGE;

    return $tcontent; // return all HTML content so the function can be called effectively 
}

// include both the header and footer and echo the function to create the visualisation
include '../includes/header.php';
echo buildRegisterPage($terror, $tsuccess);
include '../includes/footer.php';
