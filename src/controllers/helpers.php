<?php
function generateStarRating($rating) {
    $rating = max(0, min(5, $rating));
    
    $fullStars = floor($rating);
    $remainder = $rating - $fullStars;
    
    $halfStar = 0;
    $emptyStars = 0;
    
    if ($remainder > 0.74) {
        $fullStars += 1;
    } elseif ($remainder >= 0.25) {
        $halfStar = 1;
    }
    
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
    if ($year === null) $year = date('Y');
    $month = date('m');
    
    $counterFile =  __DIR__ .'/../../src/data/' . $year . '_' . $month . '_visits.txt';
    
    return (file_exists($counterFile)) ? (int)file_get_contents($counterFile) : 0;
}

function getTotalVisitCount() {
    $counterFile = __DIR__ .'/../../src/data/total_visits.txt';
    
    return (file_exists($counterFile)) ? (int)file_get_contents($counterFile) : 0;

}

function getRatingDescription($rating) {
    if ($rating == 0) return "No Ratings Yet";
    
    $rating = min(5, $rating);
    
    if ($rating >= 4.7) return "Exceptional!";
    if ($rating >= 4.2) return "Excellent!";
    if ($rating >= 3.7) return "Very Good!";
    if ($rating >= 3.2) return "Good";
    if ($rating >= 2.7) return "Average";
    if ($rating >= 2.2) return "Fair";
    if ($rating >= 1.7) return "Poor";
    if ($rating >= 1.2) return "Very Poor";
    return "Terrible";
}
?>