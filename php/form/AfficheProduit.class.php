<?php

class AfficheProduit extends Produit {

    function afficher() {

        $outils = new Outils();

        // recherche du tarif minimum
        $prix = $this->tarif;
        $prix_formate = $outils->formatPrix($prix);

        //prise en compte de la promo
        $promo = $outils->getPrix($this->idProduit);
        $promo_formate = $outils->formatPrix($promo);

        $commentaires = ereg_replace("\n", "<br/>", $this->commentaires);

        $db = new BD_connexion();
        $link = $db->getConnexion();

        $query = "SELECT intitule FROM train_categories WHERE idCat = {$this->idCat}";
        $result = mysql_query($query, $link) or die(mysql_error($link));
        $nomCat = mysql_result($result, 0);
        $db->closeConnexion();

        $html = "";
        
        $html .= $outils->ariane();

        $html .= "<div id=\"fiche_produit\">";
        $html .= "  <div id=\"titre_produit\">{$this->nom}</div>";
        $html .= "  <div id=\"img_produit\"><img src=\"images/uploaded/{$this->img}\" onClick=\"modalbox('images/uploaded/zoom/{$this->img_zoom}')\" /></div>";
        $html .= "  <div id=\"descriptif_produit\">";

        if ($prix == $promo)
            $html .= "      <p id=\"tarif\">{$prix_formate}</p>";
        else {
            $html .= "      <p id=\"tarif\">{$promo_formate}</p>";
            $valeurPromo = $outils->getPromo($this->idProduit);
            $html .= "      <p class=\"ancien_tarif\"><span>- " . $valeurPromo['valeur'] . "% </span>{$prix_formate}</p>";
        }
        
        $html .= "      </p>";
        $html .= "      <div id=\"options_produit\">";

        $html .= "          <p id=\"comment\">";
        $html .= "              {$commentaires}";
        $html .= "          </p>";
        $html .= "      </div>";
        $html .= "   </div>";
        $html .= "</div>";

        return $html;
    }

}

?>
