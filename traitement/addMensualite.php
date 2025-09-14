<?php
session_start();
require_once './connect.php';

$anne = date('Y');

// Vérifie si plusieurs membres sont sélectionnés
if (isset($_POST['idm']) && is_array($_POST['idm'])) {
    // Prépare les requêtes une seule fois pour réutilisation
    $insertCotisation = $con->prepare("INSERT INTO cotisation VALUES(null,:montant,:datepayement,:anne,:id_membre,:id_mois)");

    $checkTotal = $con->prepare("SELECT * from cotisationtotal where id_m=:id_m and anne=:anne");
    $updateTotal = $con->prepare("UPDATE cotisationtotal set montant=montant+:montant where id_m=:id_m and anne=:anne");

    $getTotal = $con->prepare("SELECT SUM(montant) as mt from cotisation where id_membre=:id_membre and anne=:anne");
    $insertTotal = $con->prepare("INSERT into cotisationtotal VALUES(null,:id_m,:montant,:anne)");

    foreach ($_POST['idm'] as $id_membre) {
        // Insertion dans la table cotisation
        $insertCotisation->execute([
            'montant' => $_POST['montant'],
            "datepayement" => $_POST['datepayement'],
            "anne" => $anne,
            "id_membre" => $id_membre,
            "id_mois" => $_POST['id_mois']
        ]);

        // Vérifie si le total existe déjà
        $checkTotal->execute([
            "id_m" => $id_membre,
            "anne" => $anne
        ]);
        $exist = $checkTotal->fetch(PDO::FETCH_ASSOC);

        if ($exist) {
            // Mise à jour du total
            $updateTotal->execute([
                "id_m" => $id_membre,
                "anne" => $anne,
                "montant" => $_POST['montant']
            ]);
        } else {
            // Calcul du total pour nouvelle insertion
            $getTotal->execute([
                "id_membre" => $id_membre,
                "anne" => $anne
            ]);
            $finds = $getTotal->fetch(PDO::FETCH_ASSOC);
            $total = $finds['mt'] ?? 0;

            if ($finds) {
                $insertTotal->execute([
                    "id_m" => $id_membre,
                    "montant" => $total,
                    "anne" => $anne
                ]);
            }
        }
    }

    $_SESSION['message'] = "Enregistrement effectué avec succès";
    $location = '../mensualites.php';
    header('location:' . $location);
    exit;
} else {
    // Si un seul membre est sélectionné (sécurité)
    $_SESSION['error'] = "Aucun membre sélectionné.";
    $location = '../ajoutmensualite.php';
    header('location:' . $location);
    exit;
}
