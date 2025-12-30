<?php
require_once '../includes/functions.php'; // load functions

if (session_status() === PHP_SESSION_NONE) { // start PHP session if one is not already open
    session_start();
}

// gets the smartphone ID and the source page to ensure the breadcrumb is correct
$tid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tfrom = $_GET['from'] ?? 'list';

$tsmartphones = loadSmartphones(); // loads the smartphone data held in the JSON file and inputs into an array
$tsmartphone = null;

// foreach statement to search for the matching ID 
foreach ($tsmartphones as $tphone) {
    if ($tphone['id'] == $tid) {
        $tsmartphone = $tphone;
        break;
    }
}
// handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review']) && isset($_SESSION['username'])) {
    $treviews = loadReviews();
    $treviews[] = [
        'smartphone_id' => $tid,
        'username' => $_SESSION['username'],
        'review' => trim($_POST['review']),
        'timestamp' => date('D, d, M, Y, H:i:s')
    ];
    saveReviews($treviews);
    header("Location: individual_smartphone.php?id=" . urlencode($tid) . "&from=" . urlencode($tfrom));
    exit;
}

function buildIndividualSmartphonePage($tsmartphone, $tid, $tfrom)
{
    // breadcrumb navigation using the tfrom variable to ensure it is accurate based on where the user has come from
    $ttrail = '<li class="breadcrumb-item"><a href="/Coursework2/index.php">Home</a></li>';
    if ($tfrom === 'ranking') {
        $ttrail .= '<li class="breadcrumb-item"><a href="/Coursework2/pages/smartphone_ranking.php">Smartphone Ranking</a></li>';
    } elseif ($tfrom === 'list') {
        $ttrail .= '<li class="breadcrumb-item"><a href="/Coursework2/pages/smartphone_list.php">Smartphone List</a></li>';
    }
    $ttrail .= '<li class="breadcrumb-item active" aria-current="page">' . ($tsmartphone['model']) . '</li>';

    // carousel using bootstrap class styling and nested if statements to include the next image in the carousel
    $tcarousel = <<<CAROUSEL
        <div class="carousel-item active">
            <img src="/Coursework2/{$tsmartphone['image']}" class="d-block w-100 rounded" alt="Smartphone Image">
        </div>
CAROUSEL;

    if (!empty($tsmartphone['image2'])) {
        $tcarousel .= <<<CAROUSEL
        <div class="carousel-item">
            <img src="/Coursework2/{$tsmartphone['image2']}" class="d-block w-100 rounded" alt="Smartphone Image 2">
        </div>
CAROUSEL;
    }

    if (!empty($tsmartphone['image3'])) {
        $tcarousel .= <<<CAROUSEL
        <div class="carousel-item">
            <img src="/Coursework2/{$tsmartphone['image3']}" class="d-block w-100 rounded" alt="Smartphone Image 3">
        </div>
CAROUSEL;
    }

    // smartphone details
    $tdetails = <<<DETAILS
    <h2 class="card-title">{$tsmartphone['model']}</h2>
    <ul class="list-unstyled mt-3">
        <li><strong>Manufacturer:</strong> {$tsmartphone['manufacturer']}</li>
        <li><strong>Screen Size:</strong> {$tsmartphone['screen_size']}"</li>
        <li><strong>Dimensions:</strong> {$tsmartphone['dimensions']}</li>
        <li><strong>Weight:</strong> {$tsmartphone['weight']}</li>
        <li><strong>Release Date:</strong> {$tsmartphone['release_date']}</li>
        <li><strong>Operating System:</strong> {$tsmartphone['os']}</li>
        <li><strong>Recommendation Score:</strong> {$tsmartphone['recommendation_score']}/10</li>
    </ul>
    <h4 class="mt-4">Smartphone Picks R Us Description</h4>
    <p>{$tsmartphone['recommendation_text']}</p>
DETAILS;

    // review form using bootstrap class styling with an if else statement to ensure only logged in users can access this form 
    $treviewForm = '';
    if (isset($_SESSION['username'])) {
        $treviewForm = <<<REVIEWFORM
        <form method="POST" class="mb-5">
            <div class="mb-3">
                <label for="review" class="form-label">Leave a review:</label>
                <textarea name="review" id="review" class="form-control" rows="3" maxlength="500" required></textarea>
                <small id="charCount" class="form-text text-muted">500 characters remaining</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
REVIEWFORM;
    } else {
        $treviewForm = '<p><a href="/Coursework2/pages/login.php">Log in</a> to leave a review!</p>';
    }

    // load reviews for this smartphone ID
    // if statement so only logged in users can view the reviews
    // nested if else statement so show different messages based on whether there is or isnt reviews in the json file for this smartphone ID
    $treviews = '';
    if (isset($_SESSION['username'])) {
        $tallReviews = getReviews($tid);
        if ($tallReviews) {
            foreach ($tallReviews as $tentry) {
                $tuser = ($tentry['username']);
                $ttime = ($tentry['timestamp']);
                $ttext = ($tentry['review']);
                $treviews .= <<<REVIEW
                <div class="border rounded p-3 mb-3 bg-light">
                    <strong>{$tuser}</strong>
                    <small class="text-muted">{$ttime}</small>
                    <p class="review-box">{$ttext}</p>
                </div>
REVIEW;
            }
        } else {
            $treviews = '<p>No reviews yet. Be the first to leave one!</p>';
        }
    }
    // HTML content main body 
    $tcontent = <<<PAGE
<main>
    <div class="container mt-5 fade-in">

        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                {$ttrail}
            </ol>
        </nav>

        <div id="smartphoneCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
            <div class="carousel-inner">{$tcarousel}</div>
            <button class="carousel-control-prev" type="button" data-bs-target="#smartphoneCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#smartphoneCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <div class="col-md-6 mb-5">{$tdetails}</div>

        <hr class="my-5">
        <h3>User Reviews</h3>
        {$treviewForm}
        {$treviews}
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const textarea = document.getElementById("review");
    const counter = document.getElementById("charCount");
    if (textarea && counter) {
        textarea.addEventListener("input", function() {
            const remaining = 500 - textarea.value.length;
            counter.textContent = remaining + " characters remaining";
            counter.classList.toggle("text-danger", remaining < 0);
        });
    }
});
</script>
PAGE;

    return $tcontent; // return all HTML content so the function can be called effectively
}

// include both the header and footer and echo the buildIndividualSmartphonePage function
include '../includes/header.php';
echo buildIndividualSmartphonePage($tsmartphone, $tid, $tfrom);
include '../includes/footer.php';
