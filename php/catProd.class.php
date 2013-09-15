<?php

/**
 * Description of authentification
 *
 * @author gcanu
 */
class catProd {

    var $id;
    var $intitule;
    var $outils;
    var $mode;

    function catProd($id, $mode) {

        $this->mode = $mode;
        $this->id = $id;
        $db = new BD_connexion();
        $link = $db->getConnexion();

        if(strcmp($this->mode, 'cat') == 0)
            $query = "SELECT intitule FROM train_categories WHERE idCat={$this->id}";
        elseif (strcmp($this->mode, 'mq') == 0)
            $query = "SELECT marque FROM train_marques WHERE idMarque={$this->id}";
        
        $result = mysql_query($query, $link) or die(mysql_error($link));

        $this->intitule = mysql_result($result, 0);
        $db->closeConnexion();

        $this->outils = new Outils();
    }

    function afficherProduits() {
        $html = "";

        $db = new BD_connexion();
        $link = $db->getConnexion();

        if(strcmp($this->mode, 'cat') == 0)
            $query = "SELECT * FROM train_produits WHERE idCat = {$this->id}";
        elseif (strcmp($this->mode, 'mq') == 0)
            $query = "SELECT * FROM train_produits WHERE idMarque = {$this->id}";

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

            $html .= "<p>" . utf8_encode($this->intitule) . "<br/><span class=\"help\">pointez un produit pour voir son nom complet</span></p>";
            $html .= "<div id=\"product_wrapper\" class='clearfix'>";

            // on affiche le tableau trié
            for ($x = 0; $x < count($produits_tries); $x++) {
                $html .= "<a href=\"index.php?a=prod&idProd={$produits_tries[$x]['idProduit']}\" title=\"{$produits_tries[$x]['nom']}\">";
                $html .= "  <div class=\"produit\">\n";
                $html .= "      <div class=\"nom_produit\">{$produits_tries[$x]['nom']}</div>\n";

				if ($produits_tries[$x]['img'] != "") {
					$url = "images/uploaded/{$produits_tries[$x]['img']}";

					// redimensionnement de l'image
					$resultResize = resizeImage($url, 172, 80);
                    $styleSize = "";

					if($resultResize !== false) {
						$url = $resultResize["filename"];
                        $styleSize = "width:".$resultResize["width"]."px;";
                        $styleSize .= "height:".$resultResize["height"]."px;";
                    }
				}
				else
					$url = "images/noimg.png";
					
                $html .= "      <div class=\"img_produit_ctn\" style=\"background-image: url({$url});{$styleSize}\">\n";
                $html .= "      </div>\n";

                $html .= "      <div class=\"description\">\n";
                $html .= $this->outils->formatPrix($produits_tries[$x]['tarif']);
                $html .= "      </div>\n";
                $html .= "      <div class=\"zoom\"></div>";
                $html .= "  </div>\n";
                $html .= "</a>\n";
            }

            $html .= "</div>";
        }
        else {
            $html .= "<p>Il n'y a pas de produits associés à cette ";

            if(strcmp($this->mode, 'cat') == 0)
                $html .= "catégorie";
            elseif(strcmp($this->mode, 'mq') == 0)
                $html .= "marque";

            $html .= "</p>";
        }

        $db->closeConnexion();

        return $html;
    }
}

?>