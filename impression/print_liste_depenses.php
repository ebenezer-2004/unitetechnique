<?php

require_once '../vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Courier');
$dompdf = new Dompdf($options);

require '../database/dbconnection.php';


$depenses = $database->query('SELECT * FROM depense ORDER BY id_depense DESC');


$html = '<h1 style="text-align: center;">Liste des Dépenses</h1>';
$html .= '<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>Montant</th>
            <th>Motif</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>';

while ($depense = $depenses->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
        <td>' . number_format($depense['montant'], 0, '', ' ') . ' FCFA</td>
        <td>' . htmlspecialchars($depense['motif']) . '</td>
        <td>' . date('d/m/Y', strtotime($depense['date'])) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$dompdf->loadHtml($html);


$dompdf->setPaper('A4', 'portrait');


$dompdf->render();


$dompdf->stream("Liste_des_depenses", ["Attachment" => false]);
