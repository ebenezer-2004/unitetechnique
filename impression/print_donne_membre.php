<?php
use Dompdf\Dompdf;
require '../vendor/autoload.php';
require '../database/dbconnection.php';
use Dompdf\Options;

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
    <title>Impression - Données des Membres</title>
    <style>
  
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
        ul {
            padding: 0;
            margin: 0;
        }
        ul li {
            list-style: none;
            margin-bottom: 5px;
        }
        .text-success {
            color: green;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Données des Membres</h1>
    <table>
        <thead>
            <tr>
                <th>Membre</th>
                <th>Arriérés Totaux Cotisations (FCFA)</th>
                <th>Mois Non Cotisés</th>
                <th>Dons Non Reçus</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            include '../database/dbconnection.php';

            $sql = "
                SELECT 
                    m.id_membre, 
                    m.nom AS nom_membre, 
                    m.prenom AS prenom_membre, 
                    c.mois, 
                    c.annee, 
                    c.montant AS cotisation_montant,
                    IFNULL(co.date_cotisation, 'Non payé') AS statut_cotisation,
                    d.id_aide, 
                    d.type AS nom_don, 
                    d.montant_aide AS montant_don,
                    IFNULL(f.date_aide, 'Non reçu') AS statut_don
                FROM membre m
                CROSS JOIN cotisation c
                LEFT JOIN cotiser co ON m.id_membre = co.id_membre AND c.id_cotisation = co.id_cotisation
                CROSS JOIN aide_sociale d
                LEFT JOIN faire f ON m.id_membre = f.id_membre AND d.id_aide = f.id_aide
                WHERE f.date_aide IS NULL OR co.date_cotisation IS NULL
                ORDER BY m.nom, c.annee, c.mois, d.type;
            ";
            $result = $database->query($sql);

            $membersData = [];
            $totalArrears = 0;
            $totalAides = 0;

            if ($result && $result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $idMembre = $row['id_membre'];
                    if (!isset($membersData[$idMembre])) {
                        $membersData[$idMembre] = [
                            'nom' => $row['nom_membre'],
                            'prenom' => $row['prenom_membre'],
                            'arrears_total' => 0,
                            'non_paid_months' => [],
                            'non_received_dons' => []
                        ];
                    }

                    if ($row['statut_cotisation'] === 'Non payé') {
                        $key = "{$row['mois']} {$row['annee']}";
                        if (!isset($membersData[$idMembre]['non_paid_months'][$key])) {
                            $membersData[$idMembre]['arrears_total'] += $row['cotisation_montant'];
                            $membersData[$idMembre]['non_paid_months'][$key] = "{$row['mois']} {$row['annee']}";
                            $totalArrears += $row['cotisation_montant'];
                        }
                    }

                    if ($row['statut_don'] === 'Non reçu' && !empty($row['nom_don'])) {
                        $aideKey = "{$row['nom_don']}-{$row['montant_don']}";
                        if (!isset($membersData[$idMembre]['non_received_dons'][$aideKey])) {
                            $membersData[$idMembre]['non_received_dons'][$aideKey] = [
                                'nom' => $row['nom_don'],
                                'montant' => $row['montant_don']
                            ];
                            $totalAides += $row['montant_don'];
                        }
                    }
                }
            }

            foreach ($membersData as $member) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($member['nom'] . ' ' . $member['prenom']) . "</td>";
                echo "<td>" . number_format($member['arrears_total'], 0, ',', ' ') . " FCFA</td>";
                echo "<td>";
                if (!empty($member['non_paid_months'])) {
                    echo implode(', ', $member['non_paid_months']);
                } else {
                    echo "<span class='text-success'>Aucun</span>";
                }
                echo "</td>";
                echo "<td>";
                if (!empty($member['non_received_dons'])) {
                    echo "<ul>";
                    foreach ($member['non_received_dons'] as $don) {
                        echo "<li>" . htmlspecialchars($don['nom']) . " : " . number_format($don['montant'], 0, ',', ' ') . " FCFA</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<span class='text-success'>Aucun</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total Général</td>
                <td><?= number_format($totalArrears, 0, ',', ' ') ?> FCFA</td>
                <td><?= number_format($totalAides, 0, ',', ' ') ?> FCFA</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
<?php

$html = ob_get_clean();


$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream("donnees_membres.pdf", ["Attachment" => false]);
exit;
?>
