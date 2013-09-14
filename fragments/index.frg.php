<?php

require("fragments/default_mg.frg.php");
require("fragments/default_md.frg.php");
require("fragments/default_bas.frg.php");



if(isset($_GET['id']))
	$id = $_GET['id'];
else
	$id = 0;



$db = new BD_connexion();
$link = $db->getConnexion();
$query = "SELECT * FROM train_nouveautes WHERE idDossier = ".$id." ORDER BY idNouveaute DESC";

$result = mysql_query($query, $link) or die(mysql_error($link));

$contenu = "";
while($ligne = mysql_fetch_array($result)) {
    $contenu .= "
        <div class='title'>{$ligne['titre']}</div>
        <div class='info'><img src='images/news/{$ligne['image']}' /></div>
        ";
}

$db->closeConnexion();

?>
