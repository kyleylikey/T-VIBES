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
?>