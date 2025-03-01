<?php
function generateStarRating($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;

    $html = '';

    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="bi bi-star-fill"></i>';
    }

    if ($halfStar) {
        $html .= '<i class="bi bi-star-half"></i>';
    }

    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="bi bi-star"></i>';
    }

    return $html;
}

function getMonthlyVisitCount($year = null, $month = null) {
    // Use current year and month if not specified
    if ($year === null) $year = date('Y');
    $month = date('m');
    
    // Path to monthly counter file
    $counterFile =  __DIR__ .'/../../src/data/' . $year . '_' . $month . '_visits.txt';
    
    // Return count if file exists, otherwise 0
    return (file_exists($counterFile)) ? (int)file_get_contents($counterFile) : 0;
}

function getTotalVisitCount() {
    $counterFile = __DIR__ .'/../../src/data/total_visits.txt';
    
    return (file_exists($counterFile)) ? (int)file_get_contents($counterFile) : 0;

}
?>