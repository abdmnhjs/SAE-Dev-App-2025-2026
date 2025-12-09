<?php
function mean($values){
    $length = count($values);
    $sum = 0;
    for ($i = 0; $i < $length; $i++) {
        $sum += $values[$i];
    }

    return $sum/$length;
}

function variance($values) {
    // La moyenne est nécessaire pour calculer la variance
    $mu = mean($values);
    $length = count($values);
    $sum_of_squared_differences = 0;

    if ($length === 0) {
        return 0;
    }

    foreach ($values as $value) {
        // (valeur - moyenne) au carré
        $sum_of_squared_differences += pow($value - $mu, 2);
    }

    // On divise par le nombre d'éléments pour obtenir la variance
    return $sum_of_squared_differences / $length;
}

function standardDeviation($values) {
    // L'écart-type est simplement la racine carrée de la variance
    $variance = variance($values);

    return sqrt($variance);
}

function medial($values) {
    // Vérifier que le tableau n'est pas vide
    if (empty($values)) {
        return false;
    }

    // Trier le tableau par ordre croissant
    sort($values);

    // Compter le nombre d'éléments
    $count = count($values);

    // Si nombre impair : prendre l'élément du milieu
    if ($count % 2 == 1) {
        $middleIndex = floor($count / 2);
        return (float)$values[$middleIndex];
    }

    // Si nombre pair : moyenne des deux éléments du milieu
    $middleIndex1 = ($count / 2) - 1;
    $middleIndex2 = $count / 2;
    return (float)(($values[$middleIndex1] + $values[$middleIndex2]) / 2);
}

function calculateFrequency($values) {
    $total_count = count($values);
    $frequencies = [];

    if ($total_count === 0) {
        return $frequencies;
    }

    // 1. Calcul de la Fréquence Absolue (Comptage des occurrences)
    $absolute_frequencies = array_count_values($values);

    // 2. Calcul de la Fréquence Relative (Pourcentage)
    foreach ($absolute_frequencies as $value => $count) {
        // Fréquence Relative = (Fréquence Absolue / Total) * 100
        $percentage = ($count / $total_count) * 100;

        $frequencies[$value] = [
            'count' => $count,         // Fréquence Absolue
            'percentage' => round($percentage, 2) // Fréquence Relative (arrondie à 2 décimales)
        ];
    }

    return $frequencies;
}
