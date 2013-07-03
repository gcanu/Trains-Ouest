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
';

$menu_gauche .= '
  <div id="cat_wrapper" class="subcat">
    <div id="cat_title">Catégories</div>
';

$query = "SELECT * FROM train_categories";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    $menu_gauche .= "<a href='index.php?a=view_cat&cat=".$row['idCat']."'>".$row['intitule']."</a>";
}

$menu_gauche .= '
  </div>
  <div id="cat_wrapper" class="subcat">
    <div id="cat_title">Marques</div>
';

$query = "SELECT * FROM train_marques";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    $menu_gauche .= "<a href='#'>".$row['marque']."</a>";
}

$db->closeConnexion();

$menu_gauche .= '
    </div>
';

?>
