<?php

/**
 *
 * @author GUILLAUME
 */
class NouveauteManager {

    var $nb_par_page;
    var $page;

    function NouveauteManager() {
        $this->nb_par_page = 30;

        if(isset($_GET['limit']))
            $this->page = $_GET['limit'];
        else
            $this->page = 0;
    }

    function afficher() {
        $html = "";

        $html .= "<h1>Gestion des nouveautés</h1>\n";
        $html .= "<p><a href='index.php?a=ges_nouv&form=new'>Ajouter une nouveauté</a></p>";

        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT * FROM train_nouveautes";

        $offset = $this->page*$this->nb_par_page;
        $lignes = $this->nb_par_page;
        $query .= " LIMIT {$offset},{$lignes}";

        $result = mysql_query($query) or die(mysql_error($link));

        if (mysql_num_rows($result) == 0)
            $html .= "<p>Aucune nouveauté</p>\n";
        else {
            $html .= "<table id=\"manager\">\n";
            $html .= "  <tr class=\"firstLine\">\n";
            $html .= "      <td id=\"tdRef\">Id</td>\n";
            $html .= "      <td id=\"tdTitre\">Titre</td>\n";
            $html .= "      <td id=\"tdOp\">Opérations</td>\n";
            $html .= "  </tr>\n";

            $n = 0;
            while ($ligne = mysql_fetch_array($result)) {
                if ($n / 2 == floor($n / 2))
                    $class = "paire";
                else
                    $class = "impaire";

                $html .= "  <tr class=\"{$class}\">\n";
                $html .= "      <td>{$ligne['idNouveaute']}</td>\n";
                $html .= "      <td>{$ligne['titre']}</td>\n";
                $html .= "      <td><a href=\"{$_SERVER['PHP_SELF']}?a=ges_nouv&form={$ligne['idNouveaute']}\">modifier</a> | <a href=\"{$_SERVER['PHP_SELF']}?a=ges_nouv&suppr={$ligne['idNouveaute']}\">supprimer</a></td>\n";
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

        $query = "SELECT COUNT(*) FROM train_nouveautes";
        $result = mysql_query($query) or die(mysql_error($link));
        $nb = mysql_result($result, 0);
        $bd->closeConnexion();

        $html = "<p class=\"pager\">Pages : ";
        for ($x = 0; $x < ceil($nb / $this->nb_par_page); $x++) {
            if($this->page == $x)
                $currentPageHTML = "<span class=\"currentPage\">" . ($x + 1) . "</span>";
            else
                $currentPageHTML = $x+1;

            $html .= "<a href=\"index.php?a=ges_nouv&limit={$x}\">{$currentPageHTML}</a> ";
        }
        $html .= "</p>";

        return $html;
    }
    
    function supprimerFormulaire() {
        $html = "<p>Etes-vous sûr de vouloir supprimer cette nouveauté ?</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";

        return $html;
    }

    function supprimer() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        
        // on supprime la promo
        $query = "DELETE FROM train_nouveautes WHERE idNouveaute=".$_GET['suppr'];
        mysql_query($query, $link) or die(mysql_error($link));
        
        $db->closeConnexion();
    }

}

?>
