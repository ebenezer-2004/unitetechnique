<?php

use Dompdf\Dompdf;

require '../vendor/autoload.php';
require '../database/dbconnection.php';

if (isset($_POST['titre'])) {
    $titre = htmlspecialchars($_POST['titre']);

    $membres = $database->query('SELECT * FROM membre');

    $toPrint = "
        <style>
            table {
                margin: 20px auto;
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid #2A2F5B;
                padding: 8px 12px;
            }
            td, h2 {
                text-align: center;
            }
        </style>

            <h2>";

    $toPrint .= $titre;

    $toPrint .= "
            </h2>
            
            <table>
                <thead>
                    <th>Nom & Prénom(s)</th>
                    <th>Sexe</th>
                    <th>Contact</th>
                    <th>Profession</th>
                    <th>Date d'adhésion</th>
                </thead>
    
                <tbody>";
                    
    while ($membre = $membres->fetch(PDO::FETCH_ASSOC)):

        $toPrint .= '<tr>';
        $toPrint .= '<td>'. strtoupper($membre['nom']) . ' '. $membre['prenom']. '</td>';
        $toPrint .= '<td>'. $membre['sexe'] . '</td>';
        $toPrint .= '<td>'. $membre['contact'] . '</td>';
        $toPrint .= '<td>'. $membre['profession'] . '</td>';
        $toPrint .= '<td>'. date_format(date_create($membre['date_adhesion']), 'd/m/Y') . '</td>';
        $toPrint .= '</tr>';
                        
    endwhile; 
    $membres->closeCursor();

    $toPrint .= "
                </tbody>
            </table>";


    $dompdf = new Dompdf();

    $dompdf->loadHtml($toPrint);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    $dompdf->stream($titre);
}