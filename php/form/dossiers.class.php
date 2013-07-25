<?php

/**
 * Description of dossiers
 *
 * @author gcanu
 */
class Dossiers {

    var $idDossier;
    var $titre;
    var $errors;

    function Dossiers($id = "", $tab = Array()) {
        $this->errors = "";
        
        if ($id == "" && count($tab) == 0) {
            $this->idDossier = "";
            $this->titre = "";
        } 
        elseif ($id == "" && count($tab) > 0) {
            $this->idDossier = $tab["idDossier"];
            $this->titre = $tab["titre"];
        } 
        elseif ($id != "" && count($tab) == 0) {
            $db = new BD_connexion();
            $link = $db->getConnexion();
            $query = "SELECT * FROM train_dossiers WHERE idDossier = {$id}";
            echo $query;
            $result = mysql_query($query, $link) or die(mysql_error($link));
            
            $this->idDossier = mysql_result($result, 0, "idDossier");
            $this->titre = mysql_result($result, 0, "titre");
            
            $db->closeConnexion();
        } 
        elseif ($id != "" && count($tab) > 0) {
            $this->idDossier = $tab["idDossier"];
            $this->titre = $tab["titre"];
        }
    }
    
    function afficherFormulaire() {
        $html = "";

        if ($this->errors != "")
            $html .= "<p id=\"errors\">{$this->errors}</p>";

        $html .= "<form method=\"POST\" action=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}\" enctype=\"multipart/form-data\">\n";
        $html .= "<table>";

        $html .= "<tr>";
        $html .= "  <th class=\"firstColumn\"></th>";
        $html .= "  <th class=\"secondColumn\"></th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>Id</td>";
        $html .= "<td><input type=\"text\" name=\"idDossier\" value=\"{$this->idDossier}\" readonly=\"readonly\" /></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>Titre</td>";
        $html .= "<td><input type=\"text\" name=\"titre\" value=\"{$this->titre}\" /></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "  <td colspan=\"2\">";
        $html .= "      <input type=\"submit\" id=\"validation\" value=\"Valider\"/> \n";
        $html .= "      <input type=\"reset\" value=\"R&eacute;initialiser\"/> \n";
        $html .= "      <input type=\"hidden\" name=\"valid\" value=\"1\" />\n";
        $html .= "  </td>";
        $html .= "</tr>";
        $html .= "</form>";
        $html .= "</table>";

        return $html;
    }

    function enregistrer() {
        $this->verifierFormulaire();

        if ($this->errors == "") {
            $db = new BD_connexion();
            $link = $db->getConnexion();

            if ($this->idDossier == "") { //nouvel enregistrement
                $query = "INSERT INTO train_dossiers
                    SET titre = '{$this->titre}'";

                mysql_query($query) or die(mysql_error($link));

                $result_id = mysql_query("SELECT LAST_INSERT_ID()") or die(mysql_error($link));
                $id = mysql_result($result_id, 0);
            }
            else {
                $query = "UPDATE train_dossiers SET
                    titre = '{$this->titre}'";

                $query .= " WHERE idDossier = {$this->idDossier}";

                echo $query;
                mysql_query($query) or die(mysql_error($link));
            }

            $db->closeConnexion();

            return null;
        }
        else
            return $this->afficherFormulaire();
    }

    function verifierFormulaire() {
        $this->errors = "";

        if ($this->titre == "")
            $this->errors .= "<span>le champ <b>titre</b> doit être renseigné</span>";

        if ($_FILES['img']['error']) {
            switch ($_FILES['img']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->errors .= "Le fichier <b>image</b> dépasse la limite autorisée par le serveur";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->errors .= "Le fichier <b>image</b> dépasse la limite autorisée dans le formulaire";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->errors .= "L'envoi du fichier <b>image</b> a été interrompu pendant le transfert";
                    break;
            }
        }

        return $this->errors;
    }

    function supprimerFormulaire() {
        $html = "<p>Etes-vous sûr de vouloir supprimer ce dossier ?</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";

        return $html;
    }

    function supprimer() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "DELETE FROM train_dossiers WHERE idDossier={$this->idDossier}";
        mysql_query($query) or die(mysql_error($link));
        $db->closeConnexion();
    }
}

?>
