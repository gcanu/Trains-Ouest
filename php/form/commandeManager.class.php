<?php

/**
 * Description of commandeManager
 *
 * @author gcanu
 */
class commandeManager {

    var $nb_par_page;
    var $page;

    function commandeManager() {
        $this->nb_par_page = 30;

        if(isset($_GET['limit']))
            $this->page = $_GET['limit'];
        else
            $this->page = 0;
    }

    function afficher($archive = false) {
        $html = "";


        if (!$archive) {
            $html .= "<h1>Gestion des commandes</h1>\n";
            $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?a=ges_com&p=arch\">Voir les archives</a></p>\n";
        } else {
            $html .= "<h1>Gestion des archives</h1>\n";
            $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?a=ges_com\">Voir les commandes en cours</a></p>\n";
        }

        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT * FROM train_commandes WHERE archive = " . ($archive ? 1 : 0);

        $offset = $this->page * $this->nb_par_page;
        $lignes = $this->nb_par_page;
        $query .= " LIMIT {$offset},{$lignes}";

        $result = mysql_query($query) or die(mysql_error($link));

        if (mysql_num_rows($result) == 0) {
            if(!$archive)
                $html .= "<p>Aucune commande en cours</p>\n";
            else
                $html .= "<p>Aucune commande n'est archivée</p>\n";
        }
        else {
            $html .= "<table>\n";
            $html .= "  <tr class=\"firstLine\">\n";
            $html .= "      <td id=\"tdDate\">Date</td>";
            $html .= "      <td id=\"tdId\">Id commande</td>\n";
            $html .= "      <td id=\"tdUser\">Id user</td>\n";
            $html .= "      <td id=\"tdOp\">Opérations</td>\n";
            $html .= "  </tr>\n";

            $n = 0;
            while ($ligne = mysql_fetch_array($result)) {
                if ($n / 2 == floor($n / 2))
                    $class = "paire";
                else
                    $class = "impaire";

                $html .= "  <tr class=\"{$class}\">\n";
                $html .= "      <td>".Outils::normalDate($ligne['date'])."</td>";
                $html .= "      <td>{$ligne['idCommande']}</td>\n";
                $html .= "      <td>{$ligne['idUser']}</td>\n";
                $html .= "      <td>\n";
                $html .= "          <a href=\"{$_SERVER['PHP_SELF']}?a=ges_com&com={$ligne['idCommande']}\">voir</a> | \n";

                if (!$archive)
                    $html .= "          <a href=\"{$_SERVER['PHP_SELF']}?a=ges_com&arch={$ligne['idCommande']}\">archiver</a></td>\n";
                else
                    $html .= "          <a href=\"{$_SERVER['PHP_SELF']}?a=ges_com&suppr={$ligne['idCommande']}\">supprimer</a></td>\n";

                $html .= "  </tr>\n";

                $n++;
            }

            $html .= "</table>\n";
        }

        $bd->closeConnexion();
        $html .= $this->afficherPager();

        return $html;
    }

    function getCommande($id) {
        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT * FROM train_commandes WHERE idCommande = {$id}";
        $result = mysql_query($query) or die(mysql_error($link));
        $bd->closeConnexion();

        // on extrait la commande sérialisée
        $commandeSerialisee = mysql_result($result, 0, "factureObjet");

        $commande = Outils::unhash($commandeSerialisee);
        return $commande;
    }

    function archiver($id) {
        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "UPDATE train_commandes SET archive = 1 WHERE idCommande = {$id}";
        $result = mysql_query($query);
        $bd->closeConnexion();

        if (!$result)
            return false;
        else
            return true;
    }

    function afficherPager() {
        $bd = new BD_connexion();
        $link = $bd->getConnexion();

        $query = "SELECT COUNT(*) FROM train_commandes";
        $result = mysql_query($query) or die(mysql_error($link));
        $nb = mysql_result($result, 0);
        $bd->closeConnexion();

        $html = "<p class=\"pager\">Pages : ";
        for ($x = 0; $x < ceil($nb / $this->nb_par_page); $x++) {
            if($this->page == $x)
                $currentPageHTML = "<span class=\"currentPage\">" . ($x + 1) . "</span>";
            else
                $currentPageHTML = $x + 1;
            $html .= "<a href=\"index.php?a=ges_com&limit={$x}\">{$currentPageHTML}</a> ";
        }
        $html .= "</p>";

        return $html;
    }

    function supprimerFormulaire() {
        $html = "<p>Etes-vous sûr de vouloir supprimer ce produit ?<br/>Attention, cette opération est irréversible</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";
        return $html;
    }

    function supprimer($id) {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "DELETE FROM train_commandes WHERE idCommande={$id}";
        $result = mysql_query($query) or die(mysql_error($link));
        $db->closeConnexion();

        if (!$result)
            return false;
        else
            return true;
    }

}

?>
