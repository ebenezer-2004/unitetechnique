<?php
session_start();
require_once './connect.php';

$location = '../particpation_autre.php';

$montant = $_POST['montant'];
$idm = $_POST['idm']; // tableau de membres
$idcot = $_POST['idcot'];
$dates = date("Y-m-d");

// Si aucun membre sélectionné, on annule l'opération
if (!isset($idm) || !is_array($idm) || count($idm) == 0) {
    $_SESSION['error'] = "Aucun participant sélectionné.";
    header("location:" . $location);
    exit;
}

// Si le montant est vide, on va chercher le montant de la cotisation
if (empty($montant)) {
    $pst = $con->prepare('SELECT * FROM autres_cotisations WHERE id_ac=:id_ac');
    $pst->execute([
        "id_ac" => $idcot
    ]);

    $value = $pst->fetch(PDO::FETCH_ASSOC);

    // On vérifie que la cotisation existe
    if (!$value) {
        $_SESSION['error'] = "Cotisation introuvable.";
        header("location:" . $location);
        exit;
    }

    $montant1 = $value['montant_acotisation'];

    // Boucle sur tous les membres sélectionnés
    foreach ($idm as $unMembre) {
        $pst = $con->prepare('INSERT INTO contributions_cotisations VALUES(null, :cotisation_id, :membres_id, :montant, :date_contribution)');
        $pst->execute([
            "cotisation_id" => $idcot,
            "membres_id" => $unMembre,
            "montant" => $montant1,
            "date_contribution" => $dates
        ]);
    }

    $_SESSION['message'] = "Enrégistrement effectué avec success";
    header("location:" . $location);
    exit;

} else {
    // Montant saisi par l'utilisateur
    foreach ($idm as $unMembre) {
        $pst = $con->prepare('INSERT INTO contributions_cotisations VALUES(null, :cotisation_id, :membres_id, :montant, :date_contribution)');
        $pst->execute([
            "cotisation_id" => $idcot,
            "membres_id" => $unMembre,
            "montant" => $montant,
            "date_contribution" => $dates
        ]);
    }

    $_SESSION['message'] = "Enrégistrement effectué avec success";
    header("location:" . $location);
    exit;
}
?>
