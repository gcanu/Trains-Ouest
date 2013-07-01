<?php

/**
 * Description of authentification
 *
 * @author gcanu
 */
class catProd {

    var $idCat;
    var $intitule;
    var $outils;

    function catProd($cat) {
        $this->idCat = $cat;
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "SELECT intitule FROM categories WHERE idCat={$this->idCat}";
        
	$result = mysql_query($query, $link) or die(mysql_error($link));

        $this->intitule = mysql_result($result, 0);
        $db->closeConnexion();

        $this->outils = new Outils();
    }

    function afficherProduits() {
        $html = "";

        $db = new BD_connexion();
        $link = $db->getConnexion();

        $query = "SELECT * FROM produits WHERE idCat = {$this->idCat}";

	$result = mysql_query($query, $link) or die(mysql_error($link));

        if (mysql_num_rows($result)) {
            $produits = array();
            $i = 0;
            while ($ligne = mysql_fetch_array($result)) {
                $produits[$i]['idProduit'] = $ligne['idProduit'];
                $produits[$i]['nom'] = $ligne['nom'];
                $produits[$i]['img'] = $ligne['img'];

                // on part à la recherche du tarif le plus bas pour le produit
                $produits[$i]['tarif'] = $ligne['tarif'];
                $i++;
            }

            // on fait le tri du tableau
            $produits_tries = array();
            for ($x = 0; $x < count($produits) - 1; $x++) {
                $min = $produits[$x];
                $cle_min = $x;
                for ($y = $x + 1; $y < count($produits); $y++) {
                    //echo $y."<br/>";
                    if ($produits[$y]['tarif'] < $min['tarif']) {
                        $min = $produits[$y];
                        $cle_min = $y;
                    }
                }

                $copie = $produits[$x];
                $produits[$x] = $min;
                $produits[$cle_min] = $copie;
                $produits_tries[$x] = $min;
            }

            $produits_tries[$x] = $produits[count($produits) - 1];

            // ajout du fil d'ariane
            $html .= $this->outils->ariane();

            $html .= "<p>" . utf8_encode($this->intitule) . "<br/><span class=\"help\">pointez un produit pour voir son nom complet</span></p>";
            $html .= "<div id=\"product_wrapper\">";

            // on affiche le tableau trié
            for ($x = 0; $x < count($produits_tries); $x++) {
                $html .= "<a href=\"index.php?a=prod&idProd={$produits_tries[$x]['idProduit']}\" title=\"{$produits_tries[$x]['nom']}\">";
                $html .= "  <div class=\"produit\">\n";
                $html .= "      <div class=\"nom_produit\">{$produits_tries[$x]['nom']}</div>\n";

				if ($produits_tries[$x]['img'] != "") {
					$url = "images/uploaded/{$produits_tries[$x]['img']}";
					
					// redimensionnement de l'image
					$resultResize = resizeImage($url, 108, 147);
					if($resultResize !== false)
						$url = $resultResize;
				}
				else
					$url = "images/noimg.png";
					
                $html .= "      <div class=\"img_produit_ctn\" style=\"background-image: url({$url});\">\n";
                $html .= "      </div>\n";

                $html .= "      <div class=\"description\">\n";
                $html .= "          à partir de " . $this->outils->formatPrix($produits_tries[$x]['tarif']);
                $html .= "      </div>\n";
                $html .= "      <div class=\"zoom\"></div>";
                $html .= "  </div>\n";
                $html .= "</a>\n";
            }

            $html .= "</div>";
        }
        else {
            $html .= $this->outils->ariane();
            $html .= "<p>Il n'y a pas de produits associés à cette catégorie</p>";
        }

        $db->closeConnexion();

        return $html;
    }

    function afficherSousCategories() {
        $html = "";

        $db = new BD_connexion();
        $link = $db->getConnexion();
        
        $requete = "SELECT * FROM categories WHERE idCatMere = {$this->idCat}";
        
        $resultat = mysql_query($requete, $link) or die(mysql_error($link));
        
        // ajout du fil d'ariane
        $outils = new Outils();
        $html .= $outils->ariane();

        $html .= "<p>{$this->intitule}</p>\n";

        $html .= "<div id=\"ssCat\">\n";
        while ($ligne = mysql_fetch_array($resultat)) {
            $html .= "<a href=\"index.php?a=view_cat&cat={$ligne['idCat']}\">";
            $html .= "  <div class=\"ssCat_element\">\n";

            $intitule = $ligne['intitule'];
            $titre = utf8_encode($intitule);
            $fileName = Outils::replaceSpace(Outils::removeaccents(utf8_encode(strtolower($intitule))));

            $html .= "      <div class=\"ssCat_titre\" style=\"background-image: url(images/sscat/titre_{$fileName}.png);\">\n";
            $html .= "      </div>\n";
            $html .= "      <div class=\"ssCat_img\" style=\"background-image: url(images/sscat/img_{$fileName}.png);\">\n";
            $html .= "      </div>\n";
            $html .= "  </div>\n";
            $html .= "</a>";
        }
        $html .= "</div>\n";

        $db->closeConnexion();

        return $html;
    }

    // Vérifie la présence de sous-catégories
    function sousCategories() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        
	$requete = "SELECT * FROM categories WHERE idCatMere = {$this->idCat}";

        $resultat = mysql_query($requete, $link) or die(mysql_error($link));

        $db->closeConnexion();

        if (mysql_num_rows($resultat) > 0)
            return true;
        else
            return false;
    }

}

?>
