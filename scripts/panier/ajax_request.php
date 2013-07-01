<?php

require("../../php/connexion.php");
require("../../php/outils.php");

if(!isset($_GET['type']))
    echo "";
elseif($_GET['type'] == 1) {
    $id = $_GET['id'];

    $db = new BD_connexion();
    $link = $db->getConnexion();

    $query = "SELECT * FROM produits WHERE idProduit = {$id}";

    $result = mysql_query($query, $link) or die(mysql_error($link));
    
    $idProduit = mysql_result($result, 0, "idProduit");
    
    $nom = mysql_result($result, 0, 'nom');
    
    $outils = new Outils();
    $tarif = $outils->getPrix($idProduit);
    
    $db->closeConnexion();

    echo "{$id};{$nom};{$tarif}";
}

?>
