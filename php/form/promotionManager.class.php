<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author GUILLAUME
 */
class PromotionManager {

    var $nb_par_page;
    var $page;

    function PromotionManager() {
        $this->nb_par_page = 30;

        if(isset($_GET['limit']))
            $this->page = $_GET['limit'];
        else
            $this->page = 0;
    }

    function afficher() {
        $html = "";

        $html .= "<h1>Gestion des promotions</h1>\n";

        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT PM.*, PD.refProduit, PD.nom FROM promotions AS PM
                    INNER JOIN produits AS PD ON PD.idProduit = PM.idProduit";

        $offset = $this->page * $this->nb_par_page;
        $lignes = $this->nb_par_page;
        $query .= " ORDER BY nom ASC";
        $query .= " LIMIT {$offset},{$lignes}";       

        $result = mysql_query($query) or die(mysql_error($link));

        if (mysql_num_rows($result) == 0)
            $html .= "<p>Aucun produit n'est en promotion</p>\n";
        else {
            $html .= "<table id=\"manager\">\n";
            $html .= "  <tr class=\"firstLine\">\n";
            $html .= "      <td id=\"tdRef\">R&eacute;f&eacute;rence</td>\n";
            $html .= "      <td id=\"tdNom\">Nom du produit</td>\n";
            $html .= "      <td id=\"tdPromo\">Promotion</td>\n";
            $html .= "      <td id=\"tdOp\">Opérations</td>\n";
            $html .= "  </tr>\n";

            $n = 0;
            while ($ligne = mysql_fetch_array($result)) {
                if ($n / 2 == floor($n / 2))
                    $class = "paire";
                else
                    $class = "impaire";

                $html .= "  <tr class=\"{$class}\">\n";
                $html .= "      <td>{$ligne['refProduit']}</td>\n";
                $html .= "      <td>{$ligne['nom']}</td>\n";
                $html .= "      <td>{$ligne['valeur']}%</td>\n";
                $html .= "      <td><a href=\"{$_SERVER['PHP_SELF']}?a=ges_pro&form={$ligne['idProduit']}\">modifier</a> | <a href=\"{$_SERVER['PHP_SELF']}?a=ges_promo&suppr={$ligne['id']}\">supprimer</a></td>\n";
                $html .= "  </tr>\n";

                $n++;
            }

            $html .= "</table>\n";
            
            $html .= $this->afficherPager();
        }

        $bd->closeConnexion();

        return $html;
    }

    function afficherPager() {
        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT COUNT(*) FROM promotions WHERE type <> 'GLOBAL'";
        $result = mysql_query($query) or die(mysql_error($link));
        $nb = mysql_result($result, 0);
        $bd->closeConnexion();

        $html = "<p class=\"pager\">Pages : ";
        for ($x = 0; $x < ceil($nb / $this->nb_par_page); $x++) {
            if($this->page == $x)
                $currentPageHTML = "<span class=\"currentPage\">" . ($x + 1) . "</span>";
            else
                $currentPageHTML = $x + 1;

            $html .= "<a href=\"index.php?a=ges_promo&limit={$x}\">{$currentPageHTML}</a> ";
        }
        $html .= "</p>";

        return $html;
    }

}

?>
