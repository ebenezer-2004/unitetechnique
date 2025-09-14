<?php
require './traitement/connect.php';

$id=$_GET['id'];
$pst=$con->prepare("DELETE FROM retraits_mensuels WHERE id_rm=:id_ac");
$pst->execute([
    'id_ac'=>$id
]);
$message='Suppression effectué avec succes';

$location='./listeretraitMensualite.php';
header("location:".$location);

?>