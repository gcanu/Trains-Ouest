<?php

$db = new BD_connexion();
$link = $db->getConnexion();


$menu_gauche = '
   <div class="cat_wrapper">
       <div class="title">Boutique</div>
       <div class="content">
         <a href="index.php">Accueil</a>
         <a href="index.php?a=presentation">Présentation</a>
         <a href="index.php?a=boutique">Boutique</a>
         <a href="index.php?a=commander">Commander</a>
         <a href="index.php?a=contact">Contact</a>
         <a href="index.php?a=cgv">CGV</a>
       </div>
   </div>
';

$menu_gauche .= '
  <div class="cat_wrapper">
    <div class="title">Catégories</div>
    <div class="content">
';

$query = "SELECT * FROM train_categories";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    $menu_gauche .= "<a href='index.php?a=view_cat&cat=".$row['idCat']."'>".$row['intitule']."</a>";
}

$menu_gauche .= '
    </div>
  </div>
  <div class="cat_wrapper">
    <div class="title">Marques</div>
    <div class="content">
';

$query = "SELECT * FROM train_marques";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    if($row['idMarque'] > 0)
      $menu_gauche .= "<a href='index.php?a=view_cat&mq=".$row['idMarque']."'>".$row['marque']."</a>";
}

$db->closeConnexion();

$menu_gauche .= '
      </div>
    </div>
';

?>
