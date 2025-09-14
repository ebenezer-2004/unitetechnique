<?php
require '../vendor/autoload.php'; 
require '../database/dbconnection.php'; 
include '../tresorerie/traitementPatrimoine.php'; 
use Dompdf\Dompdf;

$dompdf = new Dompdf();

$html = '
<!DOCTYPE html>
<html>
<head>
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
      margin: 20px 0;
    }
    th, td {
      border: 1px solid #ddd;
      text-align: center;
      padding: 8px;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .total-row th {
      color: black;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h1>Patrimoine</h1>
  
  <h4>Entrées</h4>
  <table>
    <thead>
      <tr>
        <th>Mensualité</th>
        <td>' . htmlspecialchars($solde) . ' FCFA</td>
      </tr>
      <tr>
        <th>Adhésion</th>
        <td>' . htmlspecialchars($totAdhes) . ' FCFA</td>
      </tr>
      <tr>
        <th>Aides Sociales</th>
        <td>' . htmlspecialchars($aides) . ' FCFA</td>
      </tr>
    </thead>
    <tbody>
      <tr class="total-row">
        <th>TOTAL</th>
        <td>' . htmlspecialchars($totalEntre) . ' FCFA</td>
      </tr>
    </tbody>
  </table>

  <h4>Sorties</h4>
  <table>
    <thead>';
    
foreach ($depenses as $depse) {
    $html .= '
      <tr>
        <th>' . htmlspecialchars($depse['motif']) . '</th>
        <td>' . htmlspecialchars($depse['montant']) . ' FCFA</td>
      </tr>';
}

$html .= '
      <tr>
        <th>Aides sociales</th>
        <td>' . htmlspecialchars($aideSort) . ' FCFA</td>
      </tr>
    </thead>
    <tbody>
      <tr class="total-row">
        <th>TOTAL</th>
        <td>' . htmlspecialchars($totDepense) . ' FCFA</td>
      </tr>
    </tbody>
  </table>

  <h4>Total Patrimoine</h4>
  <table>
    <thead>
      <tr>
        <th>' . htmlspecialchars($totPatrimoine) . ' FCFA</th>
      </tr>
    </thead>
  </table>

  <h4>Répartition des Avoirs</h4>
  <table>
    <thead>
      <tr>
        <th>Espèce</th>
        <td>' . htmlspecialchars($caisse) . ' FCFA</td>
      </tr>';

foreach ($bicig as $el) {
    $html .= '
      <tr>
        <th>' . htmlspecialchars($el['banque']) . '</th>
        <td>' . htmlspecialchars($el['solde']) . ' FCFA</td>
      </tr>';
}

$html .= '
    </thead>
  </table>
</body>
</html>';


$dompdf->loadHtml($html);


$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("etat_patrimoine.pdf", ["Attachment" => 0]);
?>
