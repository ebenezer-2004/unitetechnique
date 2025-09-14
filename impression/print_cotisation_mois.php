<?php
require '../vendor/autoload.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;

require '../database/dbconnection.php';


if ($_GET !== null && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

  
    $laCotisation = $database->query("SELECT * FROM cotisation WHERE id_cotisation = $id");
    $cotisation = $laCotisation->fetch(PDO::FETCH_ASSOC);

    if (!$cotisation) {
        die("Cotisation introuvable.");
    }

 
    $lesCotisations = $database->query("SELECT M.nom, M.prenom, O.date_cotisation 
                                        FROM cotisation C, cotiser O, membre M
                                        WHERE M.id_membre = O.id_membre
                                        AND O.id_cotisation = C.id_cotisation
                                        AND C.id_cotisation = $id 
                                        ORDER BY date_cotisation DESC");
} else {
    die("Aucun ID de cotisation fourni.");
}


$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

ob_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participation aux cotisations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1,
        h4 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Participation aux cotisations</h1>
    <h4>Mois : <?= htmlspecialchars($cotisation['mois']) ?>, Année : <?= htmlspecialchars($cotisation['annee']) ?></h4>

    <table>
        <thead>
            <tr>
                <th>Nom et Prénom</th>
                <th>Date de cotisation</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cotise = $lesCotisations->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($cotise['nom']) . ' ' . htmlspecialchars($cotise['prenom']) ?></td>
                    <td><?= date_format(date_create($cotise['date_cotisation']), 'd/m/Y') ?></td>
                </tr>
            <?php endwhile;
            $lesCotisations->closeCursor(); ?>
        </tbody>
    </table>
</body>

</html>
<?php
$html = ob_get_clean();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');


$dompdf->render();


$dompdf->stream("cotisation_{$cotisation['mois']}_{$cotisation['annee']}.pdf", ["Attachment" => false]);
exit;
?>