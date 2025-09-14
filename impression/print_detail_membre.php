<?php
require '../vendor/autoload.php';
require '../database/dbconnection.php';


session_start();
if ($_SESSION['role'] != "Admin" && $_SESSION['role'] != "Super Admin" && $_SESSION['role'] != "Trésorier") {
    header('location:accueil.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || intval($_GET['id']) <= 0) {
    header('location:listeMembres.php');
    exit;
}

$id = intval($_GET['id']);
$recuperations = $database->prepare("SELECT * FROM membre WHERE id_membre = :id");
$recuperations->execute(['id' => $id]);
$membre = $recuperations->fetch(PDO::FETCH_ASSOC);

if (!$membre) {
    header('location:listeMembres.php');
    exit;
}

ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Membre</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2A2F5B;
            margin-bottom: 30px;
        }

        .card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background-color: #fefefe;
        }

        .specification-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .specification-list li {
            flex: 1 1 45%;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .name-specification {
            font-weight: bold;
            color: #555;
        }

        .status-specification {
            color: #2A2F5B;
        }

       
    </style>
</head>
<body>
    <div class="container">
        <h1>Informations du membre</h1>
        <div class="card">
            <ul class="specification-list">
                <li><span class="name-specification">Nom:</span> <?= $membre['nom'] ?></li>
                <li><span class="name-specification">Prénom(s):</span> <?= $membre['prenom'] ?></li>
                <li><span class="name-specification">Sexe:</span> <?= $membre['sexe'] ?></li>
                <li><span class="name-specification">Date de naissance:</span> <?= date_format(date_create($membre['datenaiss']), 'd/m/Y') ?></li>
                <li><span class="name-specification">Profession:</span> <?= $membre['profession'] ?></li>
                <li><span class="name-specification">Contact:</span> <?= $membre['contact'] ?></li>
                <li><span class="name-specification">Adresse:</span> <?= $membre['adresse'] ?></li>
                <li><span class="name-specification">N° Carnet:</span> <?= $membre['num_carnet'] ?></li>
                <li><span class="name-specification">Date d'adhésion:</span> <?= date_format(date_create($membre['date_adhesion']), 'd/m/Y') ?></li>
                <li><span class="name-specification">Frais d'adhésion:</span> <?= number_format($membre['frais_adhesion'], 0, '', ' ') ?> FCFA</li>
            </ul>
        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("detail_membre.pdf", ["Attachment" => false]);
exit;
?>
