<?php
function conditionalProbability($data, callable $condition_A, callable $condition_B) {
    $count_B = 0;       // Compteur pour l'événement B (dénominateur)
    $count_A_and_B = 0; // Compteur pour les événements A et B (numérateur)

    foreach ($data as $item) {
        $is_B = $condition_B($item);

        if ($is_B) {
            $count_B++;

            // Si B est vrai, on vérifie si A est vrai aussi
            if ($condition_A($item)) {
                $count_A_and_B++;
            }
        }
    }

    if ($count_B === 0) {
        // Impossible de calculer la probabilité si l'événement conditionnel B ne s'est jamais produit
        return 0.0;
    }

    // P(A|B) = Nombre(A et B) / Nombre(B)
    return $count_A_and_B / $count_B;
}



