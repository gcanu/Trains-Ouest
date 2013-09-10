<?php

/**
 * Description of produitclass
 *
 * @author gcanu
 */
class Promotion {
    var $promotion;
    
    var $errors;
    var $messages;
    
    function Promotion($promotion = null) {
        if($promotion == null) {
            $db = new BD_connexion();
            $link = $db->getConnexion();        
            $query = "SELECT valeur FROM train_promotions WHERE type = 'GLOBAL'";
            $result = mysql_query($query, $link) or die(mysql_error($link));
            
            if(mysql_num_rows($result) > 0)
                $this->promotion = mysql_result($result, 0);
            else
                $this->promotion = "";
        }
        else
            $this->promotion = $promotion;    
        
        //$db->closeConnexion();
        
        $this->errors = "";
        $this->messages = "";
    }
    
    function afficherFormulaire() {
        $html = "";
        
        if($this->errors != "")
            $html .= "<p id=\"errors\">{$this->errors}</p>";
        if($this->messages != "")
            $html .= "<p id=\"messages\">{$this->messages}</p>";    
        
        $html .= "<form action=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}\" method=\"POST\">";
        $html .= "<p>";
        $html .= "<label>Promotion globale</label>\n";
        $html .= "<input type=\"text\" name=\"promotion\" value=\"{$this->promotion}\"/> %\n";
        $html .= "<input type=\"submit\" name=\"submitButton\" value=\"Valider\" />\n";
        $html .= "<input type=\"hidden\" name=\"valid\" value=\"1\" />\n";
        $html .= "</p>";
        $html .= "</form>";
        
        $db = new BD_connexion();
        $link = $db->getConnexion();        
        $query = "SELECT COUNT(*) FROM train_promotions WHERE type = 'GLOBAL'";
        $result = mysql_query($query, $link) or die(mysql_error($link));
        $db->closeConnexion();
        
        $global = mysql_result($result, 0);        
        if($global == 0) {
            $html .= "<p>Aucune promotion globale n'a été définie, vous pouvez donc en ajouter une.</p>\n";
            $html .= "<p>Note : une seule promotion globale est possible.</p>\n";
        }
        else {
            $html .= "<p>Une promotion globale à déjà été définie, vous ne pouvez en ajouter une autre, veuillez modifier l'existante.</p>\n";
            $html .= "<p>Pour supprimer une promotion globale, mettez là à 0</p>\n";
        }
        
        return $html;
    }
    
    function afficherPromotions() {
        $outils = new Outils();

        $html = "";
        
        $html .= "<h1>Promotions</h1>";
        
        $db = new BD_connexion();
        $link = $db->getConnexion();
        
        $query = "SELECT PM.*, PD.idProduit, PD.nom, PD.img, C.intitule FROM train_promotions AS PM 
            INNER JOIN train_produits AS PD ON PM.idProduit = PD.idProduit
            INNER JOIN train_categories AS C ON PD.idCat = C.idCat";
        $result = mysql_query($query, $link) or die(mysql_error($link));
        
        while ($ligne = mysql_fetch_array($result)) {
            $nom = $ligne['nom'];
            $promo = $ligne['valeur'];
            $img = $ligne['img'];
            $cat = $ligne['intitule'];
            $idProd = $ligne['idProduit'];

            $prix = $outils->getPrix($idProd, false, true);
            $prix_promo = $outils->getPrix($idProd, true, true);
            
            $html .= "<a href=\"index.php?a=prod&idProd={$idProd}\">\n";
            $html .= "  <div class=\"produit\">\n";
            $html .= "      <div class=\"nom_produit\">{$nom}</div>\n";
            $html .= "      <div class=\"img_produit_ctn\">\n";
            $html .= "          <img class=\"img_produit\" src=\"images/uploaded/{$img}\"/>\n";
            $html .= "      </div>\n";
            $html .= "      <div class=\"description\">{$prix_promo} au lieu de {$prix}</div>\n";
            $html .= "      <div class=\"zoom\"></div>\n";
            $html .= "  </div>\n";
            $html .= "</a>\n";
        }
        
        $db->closeConnexion();
        
        return $html;
    }
    
    function enregistrer() {
        $this->messages = "";
        $this->verifierFormulaire();
        
        if($this->errors == "") {
            $db = new BD_connexion();
            $link = $db->getConnexion();
            
            $query = "DELETE FROM train_promotions WHERE type = 'GLOBAL'";
            mysql_query($query) or die(mysql_error($link));
            
            if($this->promotion != 0) {
                $query = "INSERT INTO train_promotions SET type = 'GLOBAL', idProduit = NULL, valeur = {$this->promotion}";
                mysql_query($query) or die(mysql_error($link));
                $this->messages = "<span>La promotion a été bien enregistrée.</span>";
            }
            else {
                $this->messages = "<span>La promotion a été supprimée.</span>";
                $this->promotion = "";
            }
            
            $db->closeConnexion();
        }
        
        return $this->afficherFormulaire();
            
    }
    
    function verifierFormulaire() {
        $this->errors = "";
        
        if(!is_numeric($this->promotion))
            $this->errors = "<span>Une valeur <b>numérique</b> doit être entrée</span>";
        elseif($this->promotion < 0)
            $this->errors = "<span>Un poucentage ne peut être inférieur à zéro</span>";
        elseif($this->promotion > 100)
            $this->errors = "<span>Un pourcentage ne peut être supérieur à 100</span>";
    }
    
    function supprimerFormulaire() {
        $html = "<p>Etes-vous sûr de vouloir supprimer cette promotion ?</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";

        return $html;
    }

    function supprimer() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        
        // on supprime la promo
        $query = "DELETE FROM train_promotions WHERE id={$this->promotion}";
        mysql_query($query, $link) or die(mysql_error($link));
    }
}

?>
