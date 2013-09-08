<?php

$db = new BD_connexion();
$link = $db->getConnexion();


$menu_gauche = '
   <div class="cat_wrapper">
       <div class="title">Boutique</div>
       <div class="content">
         <a href="index.php">Accueil</a>
         <a href="index.php?a=presentation">Qui sommes nous ?</a>
         <a href="index.php?a=boutique">Magasin</a>
         <a href="index.php?a=commander">Commander</a>
         <a href="index.php?a=contact">Contact</a>
         <a href="index.php?a=cgv">Conditions générales</a>
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
    $menu_gauche .= "<a href='index.php?a=view_cat&cat=" . $row['idCat'] . "'>" . $row['intitule'] . "</a>";
}

$menu_gauche .= '
    </div>
  </div>
  <div class="cat_wrapper">
    <div class="title">Marques</div>
    <div class="content">
';

/*
 *  affichage des marques
 */

$query = "SELECT * FROM train_marques";
$result = mysql_query($query, $link) or die(mysql_error($link));
while ($row = mysql_fetch_array($result)) {
    if ($row['idMarque'] > 0)
        $menu_gauche .= "<a href='index.php?a=view_cat&mq=" . $row['idMarque'] . "'>" . $row['marque'] . "</a>";
}

$menu_gauche .= '
      </div>
    </div>
';

/*
 * Affichage des dossier s'il y en a
 */
$query = "SELECT n.idDossier, d.titre FROM train_nouveautes AS n, train_dossiers AS d WHERE n.idDossier = d.idDossier AND d.idDossier > 0 GROUP BY n.idDossier";
$result = mysql_query($query, $link) or die(mysql_error($link));

if (mysql_num_rows($result) > 0) {

    $menu_gauche .= '
        <div class="cat_wrapper">
          <div class="title">Dossiers</div>
          <div class="content">
    ';

    while ($row = mysql_fetch_array($result)) {
        if($row['idDossier'] > 0)
            $menu_gauche .= "<a href='index.php?a=view_dos&id=" . $row['idDossier'] . "'>" . $row['titre'] . "</a>";
    }

    $menu_gauche .= '
          </div>
        </div>
    ';
}

$db->closeConnexion();

?>
