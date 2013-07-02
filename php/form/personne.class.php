<?php

/**
 * Description of produitclass
 *
 * @author gcanu
 */
class Personne {

    var $idUser;
    var $nom;
    var $prenom;
    var $adresseDomicile;
    // les deux attributs ne sont initialisés que dans le cas n°3 car ce dernier
    // convient à une utilisation hors formulaire. Il est donc possible d'appeler
    // l'objet Personne en lui passant simplement l'id et d'accéder ensuite à ces
    // deux attributs. Le formulaire lié à Personne ne les utilises PAS.
    var $adresseLivraison;
    var $adresseFacturation;
    var $login;
    var $passwd;
    var $email;

    function Personne($id = "", $tab = Array()) {
        $this->errors = "";
        $this->adresseLivraison = -1;
        $this->adresseFacturation = -1;

        // cas n°1 : formulaire vierge, initialisation par défaut des variables
        if ($id == "" && count($tab) == 0) {
            $this->idUser = 0;
            $this->nom = "";
            $this->prenom = "";
            $this->adresseDomicile = new Adresse("USER");
            $this->login = "";
            $this->passwd = "";
            $this->email = "";
        }
        // cas n°2 : formulaire validé, id vierge mais tab rempli
        elseif ($id == "" && count($tab) > 0) {
            $this->idUser = $tab['idUser'];
            $this->nom = $tab['nom'];
            $this->prenom = $tab['prenom'];
            $this->adresseDomicile = new Adresse("USER", $this->idUser, Outils::filtrerTableau($tab, "USER"));
            $this->login = $tab['login'];
            $this->passwd = $tab['passwd'];
            $this->email = $tab['email'];
        }
        // cas n°3 : formulaire vierge mais id renseigné, on recherche les infos en base
        elseif ($id != "" && count($tab) == 0) {
            $db = new BD_connexion();
            $link = $db->getConnexion();
            $query = "SELECT * FROM train_users WHERE idUser = {$id}";
            $result = mysql_query($query, $link) or die(mysql_error($link));

            $this->idUser = mysql_result($result, 0, 'idUser');
            $this->nom = mysql_result($result, 0, 'nom');
            $this->prenom = mysql_result($result, 0, 'prenom');
            $this->adresseDomicile = new Adresse("USER", mysql_result($result, 0, 'adresseDomicile'));
            $this->adresseLivraison = new Adresse(null, mysql_result($result, 0, 'adresseLivraison'));
            $this->adresseFacturation = new Adresse(null, mysql_result($result, 0, 'adresseFacturation'));
            $this->login = mysql_result($result, 0, 'login');
            $this->passwd = mysql_result($result, 0, 'passwd');
            $this->email = mysql_result($result, 0, 'email');

            $db->closeConnexion();
        }
        // cas n°4 : formulaire rempli et id aussi : modification, on màj les infos
        elseif ($id != "" && count($tab) > 0) {
            $this->idUser = $id;
            $this->nom = $tab['nom'];
            $this->prenom = $tab['prenom'];
            $this->adresseDomicile = new Adresse("USER", $this->idUser, Outils::filtrerTableau($tab, "USER"));
            $this->login = $tab['login'];
            $this->passwd = $tab['passwd'];
            $this->email = $tab['email'];
        }
    }

    function afficherFormulaire() {
        $html = "";

        if ($this->errors != "")
            $html .= "<p id=\"errors\">{$this->errors}</p>";

        $html .= "<form method=\"POST\" action=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}\">\n";

        $html .= "<input type=\"hidden\" name=\"idUser\" value=\"{$this->idUser}\" />\n";

        $html .= "<div class=\"comments\">";
        $html .= "  <div class=\"title\">Renseignements personnels</div>";
        $html .= "  <div class=\"inputs\">";
        $html .= "      <p>\n";
        $html .= "          <label>Nom</label>\n";
        $html .= "          <input type=\"text\" name=\"nom\" value=\"{$this->nom}\" />\n";
        $html .= "      </p>\n";

        $html .= "      <p>\n";
        $html .= "          <label>Pr&eacute;nom</label>\n";
        $html .= "          <input type=\"text\" name=\"prenom\" value=\"{$this->prenom}\" />\n";
        $html .= "      </p>\n";

        $html .= $this->adresseDomicile->afficherFormulaire();
        $html .= "  </div>";
        $html .= "</div>";

        $html .= "<div class=\"comments\">";
        $html .= "  <div class=\"title\">Création de votre compte</div>";
        $html .= "  <div class=\"inputs\">";
        $html .= "      <p>\n";
        $html .= "          <label>Login</label>\n";
        $html .= "          <input type=\"text\" name=\"login\" value=\"{$this->login}\" />\n";
        $html .= "      </p>\n";

        $html .= "      <p>\n";
        $html .= "          <label>Mot de passe</label>\n";
        $html .= "          <input type=\"password\" name=\"passwd\" value=\"{$this->passwd}\" />\n";
        $html .= "      </p>\n";

        $html .= "      <p>\n";
        $html .= "          <label>E-Mail</label>\n";
        $html .= "          <input type=\"text\" name=\"email\" value=\"{$this->email}\" />\n";
        $html .= "      </p>\n";

        $html .= "  </div>";
        $html .= "</div>";

        $html .= "<div class=\"submit\">";
        $html .= "  <input type=\"submit\" name=\"valid\" id=\"submit\" value=\"\" /> <input type=\"hidden\" name=\"valid\" value=\"1\" />";
        $html .= "</div>";

        return $html;
    }

    function enregistrer() {
        $this->verifierFormulaire($standalone = true);

        if ($this->errors == "") {
            if ($this->idUser == 0) { //nouvel enregistrement
                $id = $this->adresseDomicile->enregistrer();

                $query = "INSERT INTO train_users
                    SET nom = '{$this->nom}',
                    prenom = '{$this->prenom}',
                    adresseDomicile = {$id},
                    login = '{$this->login}',
                    passwd = '{$this->passwd}',
                    email = '{$this->email}'";
            }
            else {
                $id = $this->adresseDomicile->enregistrer();

                $query = "UPDATE train_users SET
                    nom = '{$this->nom}',
                    prenom = '{$this->prenom}',
                    adresseDomicile = {$id},
                    login = '{$this->login}',
                    passwd = '{$this->passwd}',
                    email = '{$this->email}'
                    WHERE idUser = {$this->idUser}";
            }

            $db = new BD_connexion();
            $link = $db->getConnexion();
            mysql_query($query) or die(mysql_error($link));
            $db->closeConnexion();

            if ($standalone) {
                $html = "<p id=\"confirm-inscription\">Votre compte a bien été créé, vous pouvez maintenant revenir en <a href=\"index.php\">page d'accueil</a> et vous connecter...</p>";
                return $html;
            }
            else
                return null;
        }
        else
            return $this->afficherFormulaire();
    }

    function verifierFormulaire() {
        $this->errors = "";

        if ($this->nom == "")
            $this->errors .= "<span>le champ <b>nom</b> doit être renseigné</span>";
        if ($this->prenom == "")
            $this->errors .= "<span>le champ <b>prenom</b> doit être renseigné</span>";

        $this->errors .= $this->adresseDomicile->verifierFormulaire();

        if ($this->login == "")
            $this->errors .= "<span>le champ <b>login</b> doit être renseigné</span>";
        if ($this->passwd == "")
            $this->errors .= "<span>le champ <b>mot de passe</b> doit être renseigné</span>";
        if ($this->email == "")
            $this->errors .= "<span>le champ <b>email</b> doit être renseigné</span>";

        return $this->errors;
    }

    function supprimerFormulaire() {
        $html = "<p>Etes-vous sûr de vouloir supprimer cette personne ?</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";

        return $html;
    }

    function supprimer() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "DELETE FROM train_users WHERE idUser={$this->idUser}";
        mysql_query($query) or die(mysql_error($link));
        $db->closeConnexion();
    }
    
    function getEmail($login) {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "SELECT email FROM train_users WHERE login='{$login}'";
        $result = mysql_query($query) or die(mysql_error($link));
        $db->closeConnexion();

        if(mysql_num_rows($result) > 0)
            return mysql_result($result, 0, "email");
        else
            return null;
    }
}

?>
