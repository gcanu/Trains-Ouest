<?php

/**
 * Description of authentification
 *
 * @author gcanu
 */
class authentification {

    var $statut;
    var $login;
    var $id;

    function authentification() {
        $this->statut = "defaut";
        $this->login = "";
        $this->id = 0;
    }

    function afficheFormulaire($action = "") {
        // mise a jour des droits (par securite)
        $this->getStatut();

        if ($this->statut == "defaut") {
            if ($action == "")
                $action = $_SERVER['PHP_SELF'] . "?action=login";

            return "
                <div id=\"authentification\">
                    <form action=\"{$action}\" method=\"POST\">
                        <p>
                            <label for=\"login\">Identifiant : </label>
                            <input class=\"field\" type=\"text\" name=\"login\" id=\"login\"/>
                        </p><p>
                            <label for=\"passwd\">Mot de passe : </label>
                            <input class=\"field\" type=\"password\" name=\"mdp\" id=\"passwd\"/>
                        <p></p>
                            <input class=\"button\" type=\"submit\" id=\"submit\" value=\"Valider\"/>
                            <input class=\"button\" type=\"button\" id=\"create\" onClick=\"document.location = 'index.php?a=inscription'\" value=\"Créer un compte\"/>
                        </p>
                    </form>
                </div>";
        } else {
            if ($action == "")
                $action = $_SERVER['PHP_SELF'] . "?action=deconnexion";

            $html = "<div id=\"auth_connecte\">\n";
    
            if($this->statut == "admin")
                $html .= "<a href=\"index.php?a=admin\">administration</a><br/>\n";
    
            $html .= "<a href=\"{$action}\">d&eacute;connexion</a>\n";
            $html .= "</div>\n\n";

            return $html;
        }
    }

    function traite_action($action) {
        if ($action == "login")
            $this->login($_POST['login'], $_POST['mdp']);
        if ($action == "deconnexion") {
            $this->login = "";
            $this->getStatut();
        }
    }

    function login($login, $mdp) {
        $verif = $this->verifie($login, $mdp);

        if($verif == true)
            $this->login = $login;

        $this->getStatut();
    }

    function verifie($login, $mdp) {
        $db = new BD_connexion();
        $link = $db->getConnexion();

        $query = "SELECT idUser, passwd FROM users WHERE login='{$login}'";
        $r = mysql_query($query, $link) or die("Impossible de lancer la requete");
        $db->closeConnexion();

        $result = mysql_result($r, 0, 'passwd');

        if ($result == $mdp) {
            $this->id = mysql_result($r, 0, 'idUser');
            return true;
        }
        else
            return false;
    }

    function verifie_droits($droit) {
        if ($droit != $this->statut)
            echo "<script>document.location = 'index.php?a=interdit'</script>";
    }

    function getStatut() {
        $this->statut = "defaut";
        if($this->login != "") {
            $db = new BD_connexion();
            $link = $db->getConnexion();
        
            $query = "SELECT admin FROM users WHERE login='{$this->login}'";
            $result = mysql_query($query, $link) or die("Impossible de lancer la requete : ".mysql_error());
            $statut = mysql_result($result, 0, 'admin');

            if($statut == 1)
                $this->statut = "admin";
            else
                $this->statut = "user";

            $db->closeConnexion();
        }
        
        return $this->statut;
    }

    function getId() {
        // retourne l'identifiant de la personne connectée
        return $this->id;
    }

}
?>
