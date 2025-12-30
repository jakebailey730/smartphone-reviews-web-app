<?php
require_once '../includes/functions.php'; // import functions

function buildSmartphoneListPage()
{ // creation of the function for the smartphone list page
    $tsmartphones = loadSmartphones(); // input smartphone data from JSON file into array
    $tcards = '';

    // foreach statement to loop through each smartphone held in the smartphones array and get the needed data
    foreach ($tsmartphones as $tphone) {
        $timg = "/Coursework2/" . ($tphone['image']);
        $tmodel = ($tphone['model']);
        $tmanufacturer = ($tphone['manufacturer']);
        $tid = urlencode($tphone['id']);

        // creation of card using bootstrap classes and styles including a view details button to take the user directly to the individual smartphone page
        $tcards .= <<<CARD
        <div class="col-md-4 d-flex align-items-stretch mb-4">
            <div class="card shadow-sm">
                <img src="{$timg}" class="card-img-top" alt="{$tmodel}">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{$tmodel}</h5>
                    <p class="card-text">{$tmanufacturer}</p>
                    <a href="/Coursework2/pages/individual_smartphone.php?id={$tid}&from=list" class="btn btn-primary mt-auto">View Details</a>
                </div>
            </div>
        </div>
CARD;
    }

    // creation of the HTML content main body 
    $tcontent = <<<PAGE
<main>
    <div class="container mt-5">
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/Coursework2/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Smartphone List</li>
            </ol>
        </nav>

        <h1 class="text-center mb-4">All Smartphones</h1>

        <div class="row">
            {$tcards}
        </div>
    </div>
</main>
PAGE;

    return $tcontent; // return all HTML content so the function can be called effectively
}

// include both header and footer and echo the smartphone list page function to create visualisation
include '../includes/header.php';
echo buildSmartphoneListPage();
include '../includes/footer.php';
