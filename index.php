<?php
require_once 'includes/functions.php'; // import functions

function buildHomePage()
{ // create function for the index/home page
    $tsmartphones = loadSmartphones(); // load all smartphone data held in the JSON file and saved to tsmartphone array

    $tcardsHTML = '';
    $tcounter = 0;
    // foreach statement used to get only three smartphones data from the JSON file instead of all
    foreach ($tsmartphones as $tphone) {
        if ($tcounter >= 3) break;

        // extracts the needed values from the array created in the foreach statement
        $timg = ($tphone['image']);
        $tmodel = ($tphone['model']);
        $tmanufacturer = ($tphone['manufacturer']);
        $tid = urlencode($tphone['id']);

        // Bootstrap card and styling used for each phone containing the values extracted
        $tcardsHTML .= <<<CARD
        <div class="col-md-4 d-flex align-items-stretch">
            <div class="card mb-4">
                <img src="{$timg}" class="card-img-top" alt="{$tmodel}">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{$tmodel}</h5>
                    <p class="card-text">{$tmanufacturer}</p>
                    <a href="/Coursework2/pages/individual_smartphone.php?id={$tid}&from=index" class="btn btn-primary mt-auto">View Details</a>
                </div>
            </div>
        </div>
CARD;
        $tcounter++;
    }

    // HTML main content page 
    $tcontent = <<<PAGE
<main>

<section class="hero">
    <div class="container">
        <h1>Welcome to Smartphone Picks R Us!</h1>
        <p>At Smartphone Picks R Us, we personally rate and review the latest smartphones for you and allow you to rate the smartphone yourself, creating a community like no other!</p>
        <a href="/Coursework2/pages/smartphone_ranking.php" class="btn btn-success btn-lg mt-4">View Smartphone Rankings</a>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="text-center">Featured Smartphones</h2>
        <div class="row">
            {$tcardsHTML}
        </div>
    </div>
</section>

</main>
PAGE;

    return $tcontent; // return all the HTML string to the function so it can be called effectively
}

// include both the header and the footer and echo the buildHomePage function that has been created to give the visualisation for this page
include 'includes/header.php';
echo buildHomePage();
include 'includes/footer.php';
