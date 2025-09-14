<?php
require '../vendor/autoload.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;

require '../database/dbconnection.php';


if ($_GET !== null && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    
    $laReunion = $database->query("SELECT * FROM reunion WHERE id_reunion = $id");
    $reunion = $laReunion->fetch(PDO::FETCH_ASSOC);

    if (!$reunion) {
        die("Réunion introuvable.");
    }

   
    $lesMembres = $database->query("SELECT * FROM membre M, participer_reunion P
                                    WHERE M.id_membre = P.id_membre
                                    AND P.id_reunion = $id");
} else {
    die("Aucun ID de réunion fourni.");
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
    <title>Participation Réunion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h4 {
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
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Participation à la réunion</h1>
    <h4>Date : <?= date_format(date_create($reunion['date_reunion']), 'd/m/Y') ?></h4>
    <h4>Sujet : <?= htmlspecialchars($reunion['motif']) ?></h4>

    <table>
        <thead>
            <tr>
                <th>Nom et Prénom</th>
                <th>Sexe</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($membre = $lesMembres->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($membre['nom']) . ' ' . htmlspecialchars($membre['prenom']) ?></td>
                <td><?= htmlspecialchars($membre['sexe']) ?></td>
                <td><?= htmlspecialchars($membre['contact']) ?></td>
            </tr>
            <?php endwhile; $lesMembres->closeCursor() ?>
        </tbody>
    </table>
</body>
</html>
<?php
$html = ob_get_clean();


$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');


$dompdf->render();

$dompdf->stream("participation_reunion.pdf", ["Attachment" => false]);
exit;
?>
