<?php

$db = new BD_connexion();
$link = $db->getConnexion();


$menu_gauche = '
   <div id="cat_wrapper">
       <a href="index.php"><div class="catv accueil"></div></a>
       <a href="index.php?a=presentation"><div class="catv presentation"></div></a>
       <a href="index.php?a=boutique"><div class="catv boutique"></div></a>
       <a href="index.php?a=commander"><div class="catv commander"></div></a>
       <a href="index.php?a=contact"><div class="catv contact"></div></a>
       <a href="index.php?a=cgv"><div class="catv cgv"></div></a>
   </div>
   <div id="cat_wrapper">
';

$query = "SELECT * FROM marques";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    $menu_gauche .= "<a href='#'><div class='cat'>".$row['marque']."</div></a>";
}

$query = "SELECT * FROM categories";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    $menu_gauche .= "<a href='#'><div class='cat'>".$row['intitule']."</div></a>";
}

$db->closeConnexion();

$menu_gauche .= '
    </div>
';

?>
