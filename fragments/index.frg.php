<?php

require("fragments/default_mg.frg.php");
require("fragments/default_md.frg.php");
require("fragments/default_bas.frg.php");

$db = new BD_connexion();
$link = $db->getConnexion();
$query = "SELECT * FROM train_nouveautes";

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
