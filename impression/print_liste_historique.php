<?php
require '../vendor/autoload.php'; 
require '../database/dbconnection.php'; 

use Dompdf\Dompdf;

$dompdf = new Dompdf();


$query = 'SELECT * FROM deposer 
          JOIN avoir_en_banque ON deposer.id_banque=avoir_en_banque.id_banque 
          ORDER BY date DESC';
$depenses = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);


$html = '
<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    h1 {
      text-align: center;
    
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
  <h1>Historiques des Dépenses</h1>
  <table>
    <thead>
      <tr>
        <th>Action effectuée</th>
        <th>Banque</th>
        <th>Montant</th>
        <th>Effectuée par</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>';
  

foreach ($depenses as $depense) {
    $html .= '<tr>
                <td>' . htmlspecialchars($depense['operation']) . '</td>
                <td>' . htmlspecialchars($depense['banque']) . '</td>
                <td>' . number_format($depense['montant'], 0, '', ' ') . ' FCFA</td>
                <td>' . htmlspecialchars($depense['effectue_par']) . '</td>
                <td>' . date('d/m/Y', strtotime($depense['date'])) . '</td>
              </tr>';
}

$html .= '
    </tbody>
  </table>
</body>
</html>';


$dompdf->loadHtml($html);


$dompdf->setPaper('A4', 'portrait');

$dompdf->render();


$dompdf->stream("historiques_de_depenses.pdf", ["Attachment" => 0]);
?>
