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
            $html .= "      <p id=\"tarif\"><span>à partir de </span>{$prix_formate}</p>";
        else {
            $html .= "      <p id=\"tarif\"><span>à partir de </span>{$promo_formate}</p>";
            $valeurPromo = $outils->getPromo($this->idProduit);
            $html .= "      <p class=\"ancien_tarif\"><span>- " . $valeurPromo['valeur'] . "% </span>{$prix_formate}</p>";
        }

        $html .= "      <p class=\"help help_caddie\">cliquez sur le caddie pour précommander le produit correspondant</p>";
        $html .= "      <p id=\"listeTarif\">";
        
        $prix = $outils->getPrix($this->idProduit, false, true);
        $promo = $outils->getPrix($this->idProduit, true, true);

        if ($prix != $promo)
            $prix_html = "{$promo} <span class=\"ancien_tarif_liste\">{$prix}</span> ";
        else
            $prix_html = $prix;

        $onClick = "i_panier.displayQtyChooser({$this->idProduit})";
        $html .= "{$prix_html}<img src=\"images/cart.png\" onClick=\"{$onClick}\" /><br/>";
        
        $html .= "      </p>";
        $html .= "      <div id=\"options_produit\">";

        $html .= "          <p id=\"comment\">";
        $html .= "              {$commentaires}";
        $html .= "          </p>";
        if ($this->illustration != null || $this->illustration != "") {
            $html .= "          <p id=\"illustration\">";
            $html .= "              <img src=\"images/uploaded/illustration/{$this->illustration}\" alt=\"image d'illustration\"/>";
            $html .= "          </p>";
        }
        $html .= "      </div>";
        $html .= "   </div>";
        $html .= "</div>";

        return $html;
    }

}

?>
