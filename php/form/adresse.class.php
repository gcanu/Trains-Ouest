<?php

class Adresse {

    var $formPrefix;
    var $id;
    var $adresse1;
    var $adresse2;
    var $cp;
    var $ville;
    var $errors;

    function Adresse($formPrefix, $id = 0, $tab = array()) {
        $this->formPrefix = $formPrefix;

        if ($id == 0 && count($tab) == 0) {
            $this->id = 0;
            $this->adresse1 = "";
            $this->adresse2 = "";
            $this->cp = "";
            $this->ville = "";
        }
        elseif ($id == 0 && count($tab) > 0) {
            $this->id = 0;

            $this->adresse1 = trim($tab[$this->formPrefix . '_adresse1']);
            $this->adresse2 = trim($tab[$this->formPrefix . '_adresse2']);
            $this->cp = trim($tab[$this->formPrefix . '_codePostal']);
            $this->ville = trim($tab[$this->formPrefix . '_ville']);
        }
        elseif ($id > 0 && count($tab) > 0) {
            $this->id = $id;

            $this->adresse1 = trim($tab[$this->formPrefix . '_adresse1']);
            $this->adresse2 = trim($tab[$this->formPrefix . '_adresse2']);
            $this->cp = trim($tab[$this->formPrefix . '_codePostal']);
            $this->ville = trim($tab[$this->formPrefix . '_ville']);
        }
        elseif ($id > 0 && count($tab) == 0) {
            $cnxObject = new BD_connexion();
            $cnx = $cnxObject->getConnexion();

            $requete = "SELECT * FROM adresses WHERE idAdresse = {$id}";
            $resultat = mysql_query($requete, $cnx) or die(mysql_error($cnx));

            $this->adresse1 = mysql_result($resultat, 0, "adresse1");
            $this->adresse2 = mysql_result($resultat, 0, "adresse2");
            $this->cp = mysql_result($resultat, 0, "codePostal");
            $this->ville = mysql_result($resultat, 0, "ville");

            $cnxObject->closeConnexion();
        }

        // prévention d'un bug pouvant survenir
        if ($this->cp == "")
            $this->cp = 0;
    }

    function afficherFormulaire() {
        if ($this->formPrefix != null) {
            $html = "";

            $html .= "  <p>\n";
            $html .= "      <label>adresse 1</label>\n";
            $html .= "      <input class=\"tf_adresse\" type=\"text\" name=\"{$this->formPrefix}_adresse1\" value=\"{$this->adresse1}\"/>\n";
            $html .= "  </p>\n";

            $html .= "  <p>\n";
            $html .= "      <label>adresse 2</label>\n";
            $html .= "      <input class=\"tf_adresse\" type=\"text\" name=\"{$this->formPrefix}_adresse2\" value=\"{$this->adresse2}\"/>\n";
            $html .= "  </p>\n";

            $html .= "  <p>\n";
            $html .= "      <label>code postal</label>\n";
            
            if($this->cp == 0 || $this->cp == "")
                $cp = "";
            else
                $cp = $this->cp;
                
            $html .= "      <input class=\"tf_cp\" type=\"text\" name=\"{$this->formPrefix}_codePostal\" value=\"{$cp}\"/>\n";
            $html .= "  </p>\n";

            $html .= "  <p>\n";
            $html .= "      <label>ville</label>\n";
            $html .= "      <input class=\"tf_ville\" type=\"text\" name=\"{$this->formPrefix}_ville\" value=\"{$this->ville}\"/>\n";
            $html .= "  </p>\n";
        }
        else {
            $html = "Impossible d'afficher le formulaire. Merci de contacter l'administrateur du site";
        }

        return $html;
    }

    function enregistrer() {
        if ($this->adresse1 != "" || $this->adresse2 != "" || $this->cp != 0 || $this->ville != "") {
            $cnxObject = new BD_connexion();
            $cnx = $cnxObject->getConnexion();

            if ($this->id == 0) { // nouvelle adresse
                $requete = "INSERT INTO adresses SET adresse1 = '{$this->adresse1}',
                adresse2 = '{$this->adresse2}',
                codePostal = '{$this->cp}',
                ville = '{$this->ville}'";
                $requete2 = "SELECT LAST_INSERT_ID()";
            }
            else {
                $requete = "UPDATE adresses SET
                adresse1 = '{$this->adresse1}',
                adresse2 = '{$this->adresse2}',
                codePostal = '{$this->cp}',
                ville = '{$this->ville}'
                WHERE idAdresse = {$this->id}";
            }

            mysql_query($requete, $cnx) or die(mysql_error($cnx));

            if ($this->id == 0) {
                $resultat = mysql_query($requete2, $cnx) or die(mysql_error($cnx));
                $this->id = mysql_result($resultat, 0);
            }

            $cnxObject->closeConnexion();
            return $this->id;
        }
        return "NULL";
    }

    function supprimer() {
        $cnxObject = new BD_connexion();
        $cnx = $cnxObject->getConnexion();

        $requete = "DELETE FROM adresses WHERE idAdresse = {$this->id}";
        mysql_query($requete, $cnx) or die(mysql_error($cnx));

        $cnxObject->closeConnexion();
    }

    function supprimerFormulaire() {
        
    }

    function verifierFormulaire() {
        $this->errors = "";

        if ($this->adresse1 == "")
            $this->errors .= "<span>le champ <b>adresse1</b> doit être renseigné</span>";
        if (preg_match('/[0-9]{5}/', $this->cp) == 0)
            $this->errors .= "<span>le champ <b>code postal</b> n'est pas valide</span>";
        if ($this->ville == "")
            $this->errors .= "<span>le champ <b>ville</b> doit être renseigné</span>";

        return $this->errors;
    }

    function isEmpty() {
        if (trim($this->adresse1) == "" &&
                trim($this->adresse2) == "" &&
                $this->cp == 0 &&
                trim($this->ville) == "")
            return true;
        return false;
    }

}

?>