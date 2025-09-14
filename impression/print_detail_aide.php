<?php
require '../database/dbconnection.php';
require '../vendor/autoload.php'; 

use Dompdf\Dompdf;

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    die("Aide invalide.");
}

$aide = (int)$_GET['id'];


$aides = $database->prepare("SELECT * FROM aide_sociale d
                    JOIN membre b ON b.id_membre = d.id_membre
                    WHERE d.id_aide = :id");
$aides->execute(['id' => $aide]);
$laide = $aides->fetch(PDO::FETCH_ASSOC);

if (!$laide) {
    die("Aide introuvable.");
}

$status = $database->prepare("SELECT * FROM aide_sociale_status WHERE idAide = :id");
$status->execute(['id' => $aide]);
$status = $status->fetch(PDO::FETCH_ASSOC);


$membreAideQuery = $database->prepare("
    SELECT m.nom, m.prenom, f.date_aide
    FROM membre m
    JOIN faire f ON m.id_membre = f.id_membre
    WHERE f.id_aide = :id
");
$membreAideQuery->execute(['id' => $aide]);
$membreAide = $membreAideQuery->fetchAll(PDO::FETCH_ASSOC);

$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Aide Sociale</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Rapport d\'Aide Sociale</h1>
    <h2>Aide : ' . htmlspecialchars($laide['type']) . '</h2>
    <p><strong>Description :</strong> ' . htmlspecialchars($laide['description']) . '</p>
    <p><strong>Destiné à :</strong> ' . htmlspecialchars($laide['nom'] . ' ' . $laide['prenom']) . '</p>
    <p><strong>Montant par personne :</strong> ' . number_format($laide['montant_aide'], 0, '', ' ') . ' FCFA</p>
    <p><strong>Montant Total Cotisé :</strong> ' . number_format($status['totalPercue'], 0, '', ' ') . ' FCFA</p>
    ' . ($status['status'] == 'Cloturé' ? '<p><strong>Montant Total Remis :</strong> ' . number_format($status['totalRemis'], 0, '', ' ') . ' FCFA</p>' : '') . '
    <p><strong>Status :</strong> ' . htmlspecialchars($status['status']) . '</p>
    <h3>Membres ayant participé</h3>
    <table>
        <thead>
            <tr>
                <th>Nom & Prénom(s)</th>
                <th>Date du don</th>
            </tr>
        </thead>
        <tbody>';
if ($membreAide) {
    foreach ($membreAide as $membre) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']) . '</td>
                    <td>' . date('d/m/Y', strtotime($membre['date_aide'])) . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="2" style="text-align: center;">Aucun membre n\'a participé</td></tr>';
}
$html .= '
        </tbody>
    </table>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream("Rapport_Aide_Sociale.pdf", ["Attachment" => true]);
