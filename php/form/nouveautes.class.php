<?php

/**
 * Description of nouveautes
 *
 * @author gcanu
 */
class Nouveautes {

    var $idNouveaute;
    var $titre;
    var $img;
    var $errors;

    function Nouveautes($id = "", $tab = Array()) {
        $this->errors = "";
        
        if ($id == "" && count($tab) == 0) {
            $this->idNouveaute = "";
            $this->titre = "";
            $this->img = "";
        } 
        elseif ($id == "" && count($tab) > 0) {
            $this->idNouveaute = $tab["idNouveaute"];
            $this->titre = $tab["titre"];
        } 
        elseif ($id != "" && count($tab) == 0) {
            $db = new BD_connexion();
            $link = $db->getConnexion();
            $query = "SELECT * FROM nouveautes WHERE idNouveaute = {$id}";
            echo $query;
            $result = mysql_query($query, $link) or die(mysql_error($link));
            
            $this->idNouveaute = mysql_result($result, 0, "idNouveaute");
            $this->titre = mysql_result($result, 0, "titre");
            $this->img = mysql_result($result, 0, "image");
            
            $db->closeConnexion();
        } 
        elseif ($id != "" && count($tab) > 0) {
            $this->idNouveaute = $tab["idNouveaute"];
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
        $html .= "<td><input type=\"text\" name=\"idNouveaute\" value=\"{$this->idNouveaute}\" readonly=\"readonly\" /></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>Titre</td>";
        $html .= "<td><input type=\"text\" name=\"titre\" value=\"{$this->titre}\" /></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>Image</td>";
        $html .= "<td>";
        if ($this->img != "")
            $html .= "<img src=\"images/news/{$this->img}\" style=\"width:60px;\" />";
        $html .= "<input type=\"file\" name=\"img\" />\n";
        $html .= "</td>";
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

            // traitement des images
            $this->img = $this->traiteImage($_FILES['img'], 'images/news/');

            if ($this->idNouveaute == "") { //nouvel enregistrement
                $query = "INSERT INTO nouveautes
                    SET titre = '{$this->titre}',";
                    
                if ($this->img != null)
                    $query .= "image = '{$this->img}'";

                mysql_query($query) or die(mysql_error($link));

                $result_id = mysql_query("SELECT LAST_INSERT_ID()") or die(mysql_error($link));
                $id = mysql_result($result_id, 0);
            }
            else {
                $query = "UPDATE nouveautes SET
                    titre = '{$this->titre}'";

                if ($this->img != null)
                    $query .= ", image = '{$this->img}'";

                $query .= " WHERE idNouveaute = {$this->idNouveaute}";

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
        $html = "<p>Etes-vous sûr de vouloir supprimer cette nouveauté ?</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";

        return $html;
    }

    function supprimer() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "DELETE FROM nouveautes WHERE idNouveaute={$this->idNouveaute}";
        mysql_query($query) or die(mysql_error($link));
        $db->closeConnexion();
    }

    function traiteImage($f, $path) {
        if (isset($f['name']) && $f['error'] == UPLOAD_ERR_OK) {
            $chemin_destination = $path;
            move_uploaded_file($f['tmp_name'], $chemin_destination . $f['name']);
            return $f['name'];
        }

        return null;
    }
}

?>
