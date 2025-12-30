<?php
require_once '../includes/functions.php'; // import the functions

function buildSmartphoneRankingPage()
{ // create function for the smartphone ranking page 
    $tsmartphones = loadSmartphones(); // load of all the smartphone data held in the JSON file into an array

    $torder = $_GET['order'] ?? 'desc'; // read the order to determine direction and default to decending

    // gets the current page number for pagination and defaults to page 1 and sets the number of smartphones per page to 3
    $tpage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $tperPage = 3;

    // sort the smartphones array by the recommendation score 
    usort($tsmartphones, function ($a, $b) use ($torder) {
        return $torder === 'asc'
            ? $a['recommendation_score'] <=> $b['recommendation_score']
            : $b['recommendation_score'] <=> $a['recommendation_score'];
    });

    // calculate the total number of pages required by the total number of smartphones 
    // calculate the index to start slicing from and gets only the smartphones for the current page
    $ttotal = count($tsmartphones);
    $ttotalPages = ceil($ttotal / $tperPage);
    $tstart = ($tpage - 1) * $tperPage;
    $tsmartphonesToShow = array_slice($tsmartphones, $tstart, $tperPage);


    // foreach statement to loop through the sliced smartphones and build table rows for each smartphone
    $trows = '';
    foreach ($tsmartphonesToShow as $tphone) {
        $tid = urlencode($tphone['id']);
        // construct one table row per smartphone and include all the needed values
        // view button included for each individual smartphone to take the user directly to its individual page while maintaining the breadcrumb trial
        $trows .= <<<ROW
            <tr> 
                <td>{$tphone['model']}</td>
                <td>{$tphone['manufacturer']}</td>
                <td>{$tphone['release_date']}</td>
                <td>{$tphone['screen_size']}"</td>
                <td>{$tphone['recommendation_score']}/10</td>
                <td>{$tphone['recommendation_text']}</td>
                <td>
                    <a href="/Coursework2/pages/individual_smartphone.php?id={$tid}&from=ranking" class="btn btn-primary mt-auto">View</a>
                </td> 
            </tr>
ROW;
    }

    // pagination setup - if statement included to allow removal/additional of smartphones in the future
    $tpagination = '';
    if ($ttotalPages > 1) {
        $tpagination .= '<nav aria-label="Page navigation"><ul class="pagination justify-content-center mt-4">';
        if ($tpage > 1) { // if statement included to create a prev button for the user to go back a page on the pagination if the user is past page 1 
            $prev = $tpage - 1;
            $tpagination .= "<li class='page-item'><a class='page-link' href='?page={$prev}&order={$torder}'>« Prev</a></li>";
        }
        // numbered page links given to the user to select which page they would like to use, active class used for styling
        for ($i = 1; $i <= $ttotalPages; $i++) {
            $active = ($i === $tpage) ? 'active' : '';
            $tpagination .= "<li class='page-item {$active}'><a class='page-link' href='?page={$i}&order={$torder}'>{$i}</a></li>";
        }
        // if statement include to create a next button if further pages of the pagination exist
        if ($tpage < $ttotalPages) {
            $next = $tpage + 1;
            $tpagination .= "<li class='page-item'><a class='page-link' href='?page={$next}&order={$torder}'>Next »</a></li>";
        }

        $tpagination .= '</ul></nav>';
    }

    // HTML main content page including all visible content such as breadcrumbs, page heading, sort buttons, table and pagination
    $tcontent = <<<PAGE
<main>
    <div class="container mt-5">
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/Coursework2/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Smartphone Ranking</li>
            </ol>
        </nav>

        <h1 class="text-center mb-4">Smartphone Rankings</h1>

        <div class="text-center mb-4">
            <a href="smartphone_ranking.php?order=desc" class="btn btn-primary me-4">Sort by Highest Score</a>
            <a href="smartphone_ranking.php?order=asc" class="btn btn-primary">Sort by Lowest Score</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Model</th>
                        <th>Manufacturer</th>
                        <th>Release Date</th>
                        <th>Screen Size (inches)</th>
                        <th>Smartphone Picks R Us Score</th>
                        <th>Smartphone Picks R Us Review</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    {$trows}
                </tbody>
            </table>
        </div>

        {$tpagination}
    </div>
</main>
PAGE;

    return $tcontent; // return all the HTML content so the function can be called effectively
}

// include both the header and footer and echo the function created for this page to give the visualisation
include '../includes/header.php';
echo buildSmartphoneRankingPage();
include '../includes/footer.php';
