<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of produitManagerclass
 *
 * @author GUILLAUME
 */
class PersonneManager {
    var $nb_par_page;
    var $page;

    function PersonneManager() {
        $this->nb_par_page = 30;

        if(isset($_GET['limit']))
            $this->page = $_GET['limit'];
        else
            $this->page = 0;
    }

    function afficher() {
        $html = "";

        $html .= "<h1>Gestion des personnes</h1>\n";
        $html .= "<p><a href=\"index.php?a=ges_abo&form=new\">Ajouter une nouvelle personne</a></p>\n";

        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT * FROM train_users";

        $offset = $this->page * $this->nb_par_pages;
        $lignes = $this->nb_par_page;
        $query .= " LIMIT {$offset},{$lignes}";

        $result = mysql_query($query) or die(mysql_error($link));

        if(mysql_num_rows($result) == 0)
            $html .= "<p>Aucune personne n'est présente dans la base</p>\n";
        else {
            $html .= "<table>\n";
            $html .= "  <tr class=\"firstLine\">\n";
            $html .= "      <td id=\"tdId\">Id</td>\n";
            $html .= "      <td id=\"tdNom\">Nom prénom</td>\n";
            $html .= "      <td id=\"tdOp\">Opération</td>\n";
            $html .= "  </tr>\n";

            $n = 0;
            while($ligne = mysql_fetch_array($result)) {
                if($n/2 == floor($n/2))
                    $class = "paire";
                else
                    $class = "impaire";

                $html .= "  <tr class=\"{$class}\">\n";
                $html .= "      <td>{$ligne['idUser']}</td>\n";
                $html .= "      <td>{$ligne['nom']} {$ligne['prenom']}</td>\n";
                $html .= "      <td><a href=\"{$_SERVER['PHP_SELF']}?a=ges_abo&form={$ligne['idUser']}\">modifier</a> | <a href=\"{$_SERVER['PHP_SELF']}?a=ges_abo&suppr={$ligne['idUser']}\">supprimer</a></td>\n";
                $html .= "  </tr>\n";

                $n++;
            }

            $html .= "</table>\n";
        }

        $bd->closeConnexion();
        $html .= $this->afficherPager();

        return $html;
    }

    function afficherPager() {
        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT COUNT(*) FROM train_users";
        $result = mysql_query($query) or die(mysql_error($link));
        $nb = mysql_result($result, 0);
        $bd->closeConnexion();

        $html = "<p class=\"pager\">Pages : ";
        for($x=0; $x<ceil($nb/$this->nb_par_page); $x++) {
            if($this->page == $x)
                $currentPageHTML = "<span class=\"currentPage\">" . ($x + 1) . "</span>";
            else
                $currentPageHTML = $x + 1;

            $html .= "<a href=\"index.php?a=ges_abo&limit={$x}\">{$currentPageHTML}</a> ";
        }
        $html .= "</p>";

        return $html;
    }
}
?>
