<?php

class Renseignements {

    var $idUser;
    var $nom;
    var $prenom;
    var $adr_personne;
    var $adr_livraison;
    var $adr_facturation;
    var $idDom;
    var $idLiv;
    var $idFac;

    function Renseignements($id, $tab = array()) {
        $this->idUser = $id;

        // on récupère les adresses de la personne connectée
        $cnxObject = new BD_connexion();
        $cnx = $cnxObject->getConnexion();

        $requete = "SELECT nom, prenom, adresseDomicile, adresseLivraison, adresseFacturation FROM train_users WHERE idUser = {$this->idUser}";
        $resultat = mysql_query($requete, $cnx) or die(mysql_error($cnx));

        $this->nom = mysql_result($resultat, 0, "nom");
        $this->prenom = mysql_result($resultat, 0, "prenom");
        $this->idDom = mysql_result($resultat, 0, "adresseDomicile");
        $this->idLiv = mysql_result($resultat, 0, "adresseLivraison");
        $this->idFac = mysql_result($resultat, 0, "adresseFacturation");

        $cnxObject->closeConnexion();

        // le formulaire a-t-il été validé
        if (count($tab) == 0) {
            $this->adr_personne = new Adresse('PERS', $this->idDom);

            if ($this->idLiv != "")
                $this->adr_livraison = new Adresse('LIVR', $this->idLiv);
            else
                $this->adr_livraison = new Adresse('LIVR');

            if ($this->idFac != "")
                $this->adr_facturation = new Adresse('FACT', $this->idFac);
            else
                $this->adr_facturation = new Adresse('FACT');
        }
        else { // formulaire validé
            // on récupère chacune des parties du formulaire validé ...
            $postPERS = Outils::filtrerTableau($_POST, 'PERS');
            $postLIVR = Outils::filtrerTableau($_POST, 'LIVR');
            $postFACT = Outils::filtrerTableau($_POST, 'FACT');

            // ... et on initialise chaque objet adresse            
            $this->adr_personne = new Adresse('PERS', $this->idDom, $postPERS);
            $this->adr_livraison = new Adresse('LIVR', $this->idLiv, $postLIVR);
            $this->adr_facturation = new Adresse('FACT', $this->idFac, $postFACT);
        }
    }

    function afficherFormulaire() {
        $html = "<h1>Livraison, facturation</h1>";
        $html .= "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}\">\n";

        $html .= "<div class=\"comments\">\n";
        $html .= "  <div class=\"title\">Renseignements personnels</div>\n";
        $html .= "  <div class=\"inputs\">\n";
        $html .= "    <p>";
        $html .= "      <label>nom</label>\n";
        $html .= "      <span>{$this->nom}</span>\n";
        $html .= "    </p>";
        $html .= "    <p>";
        $html .= "      <label>pr&eacute;nom</label>\n";
        $html .= "      <span>{$this->prenom}</span>\n";
        $html .= "    </p>";
        $html .= "  </div>\n";
        $html .= "</div>\n\n";

        $html .= "<div class=\"comments\">\n";
        $html .= "  <div class=\"title\">Votre adresse</div>\n";
        $html .= "  <div class=\"inputs\">\n";
        $html .= $this->adr_personne->afficherFormulaire();
        $html .= "  </div>\n";
        $html .= "</div>\n\n";

        $html .= "<div class=\"comments\" id=\"comments-0\">\n";
        $html .= "  <div class=\"title\">Adresse de livraison <span class=\"delete\"></span><br/>";
        $html .= "  <span class=\"help\">(à remplir si différente de votre adresse personnelle, laisser vierge sinon)</span></div>\n";
        $html .= "  <div class=\"inputs\">\n";
        $html .= $this->adr_livraison->afficherFormulaire();
        $html .= "  </div>\n";
        $html .= "</div>\n\n";

        $html .= "<div class=\"comments\" id=\"comments-1\">\n";
        $html .= "  <div class=\"title\">Adresse de facturation <span class=\"delete\"></span><br/>";
        $html .= "  <span class=\"help\">(à remplir si différente de votre adresse personnelle, laisser vierge sinon)</span></div>\n";
        $html .= "  <div class=\"inputs\">\n";
        $html .= $this->adr_facturation->afficherFormulaire();
        $html .= "  </div>\n";
        $html .= "</div>\n\n";

        $html .= "<div class=\"submit\"><input id=\"submit\" type=\"submit\" name=\"valid\" value=\"\" /></div>\n";

        $html .= "</form>\n";

        return $html;
    }

    function afficherRenseignements() {
        $html = "<h1>Récapitulatif de votre commande</h1>";

        // on récupère les informations liées à la personne
        $html .= "<div class=\"comments\">";
        $html .= "  <div class=\"title\">Renseignements personnels</div>";
        $html .= "  <div class=\"inputs\">";

        $personne = new Personne($this->idUser);

        $html .= "      <p>";
        $html .= "          <label>Nom prénom</label>";
        $html .= "          <span>{$personne->nom} {$personne->prenom}</span>";
        $html .= "      </p>";

        $html .= "      <p>";
        $html .= "          <label>Adresse électronique</label>";
        $html .= "          <span>{$personne->email}</span>";
        $html .= "      </p>";

        $html .= "      <p>";
        $html .= "          <label>Adresse postale</label>";
        $html .= "          <span>{$personne->adresseDomicile->adresse1}";
        $html .= "                {$personne->adresseDomicile->adresse2}";
        $html .= "                {$personne->adresseDomicile->cp}";
        $html .= "                {$personne->adresseDomicile->ville}</span>";
        $html .= "      </p>";

        $html .= "  </div>";
        $html .= "</div>";

        if (!$personne->adresseFacturation->isEmpty() || !$personne->adresseLivraison->isEmpty()) {
            $html .= "<div class=\"comments\">";
            $html .= "  <div class=\"title\">Information de livraison et facturation</div>";
            $html .= "  <div class=\"inputs\">";

            if (!$personne->adresseLivraison->isEmpty()) {
                $html .= "      <p>";
                $html .= "          <label>Adresse livraison</label>";
                $html .= "          <span>{$personne->adresseLivraison->adresse1}";
                $html .= "                {$personne->adresseLivraison->adresse2}";
                $html .= "                {$personne->adresseLivraison->cp}";
                $html .= "                {$personne->adresseLivraison->ville}</span>";
                $html .= "      </p>";
            }

            if (!$personne->adresseFacturation->isEmpty()) {
                $html .= "      <p>";
                $html .= "          <label>Adresse facturation</label>";
                $html .= "          <span>{$personne->adresseFacturation->adresse1}";
                $html .= "                {$personne->adresseFacturation->adresse2}";
                $html .= "                {$personne->adresseFacturation->cp}";
                $html .= "                {$personne->adresseFacturation->ville}</span>";
                $html .= "      </p>";
            }

            $html .= "  </div>";
            $html .= "</div>";
        }

        $commande = new Commande(); // Commande s'initialise avec le cookie
        $html .= $commande->afficheDetails(false, true);
        $html .= "<div class=\"spacer\"></div>";

        return $html;
    }

    function enregistrer() {
        $id1 = $this->adr_personne->enregistrer();
        $requete = "UPDATE train_users SET adresseDomicile = {$id1} WHERE idUser = {$this->idUser}";

        $id2 = $this->adr_livraison->enregistrer();
        $requete2 = "UPDATE train_users SET adresseLivraison = {$id2} WHERE idUser = {$this->idUser}";

        $id3 = $this->adr_facturation->enregistrer();
        $requete3 = "UPDATE train_users SET adresseFacturation = {$id3} WHERE idUser = {$this->idUser}";

        $cnxObject = new BD_connexion();
        $cnx = $cnxObject->getConnexion();

        mysql_query($requete, $cnx) or die(mysql_error($cnx));
        mysql_query($requete2, $cnx) or die(mysql_error($cnx));
        mysql_query($requete3, $cnx) or die(mysql_error($cnx));

        if ($id2 == "NULL" && $this->idLiv != "") {
            $requete = "DELETE FROM train_adresses WHERE idAdresse = {$this->idLiv}";
            mysql_query($requete, $cnx) or die(mysql_error($cnx));
        }
        if ($id3 == "NULL" && $this->idFac != "") {
            $requete = "DELETE FROM train_adresses WHERE idAdresse = {$this->idFac}";
            mysql_query($requete, $cnx) or die(mysql_error($cnx));
        }

        $cnxObject->closeConnexion();
    }

}

?>