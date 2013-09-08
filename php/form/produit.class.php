<?php

/**
 * Description of produitclass
 *
 * @author gcanu
 */
class Produit {

    var $idProduit;
    var $refProduit;
    var $nom;
    var $marque;
    var $tarif;
    var $nouveaute;
    var $etat;
    var $img;
    var $img_zoom;
    var $nomCouleur;
    var $commentaires;
    var $idCat;
    var $errors;
    
    // variables promotion
    var $idPromo;
    var $valeurPromo;

    function Produit($id = "", $tab = Array()) {
        $this->errors = "";

        // cas n°1 : formulaire vierge, initialisation par défaut des variables
        if ($id == "" && count($tab) == 0) {
            $this->idProduit = "";
            $this->refProduit = "";
            $this->nom = "";
            $this->marque = "";
            $this->tarif = 0;
            $this->nouveaute = 1;
            $this->etat = 0;
            $this->img = "";
            $this->img_zoom = "";
            $this->commentaires = "";
            $this->idCat = 0;
            
            $this->idPromo = "";
            $this->valeurPromo = "";
        }
        // cas n°2 : formulaire validé, id vierge mais tab rempli
        elseif ($id == "" && count($tab) > 0) {
            $this->idProduit = $tab['idProduit'];
            $this->refProduit = $tab['refProduit'];
            $this->nom = $tab['nom'];
            $this->marque = $tab['marque'];
            $this->tarif = $tab['tarif'];
            if(isset($tab['new']) && $tab['new'] == "new")
                $this->nouveaute = true;
            else
                $this->nouveaute = false;
            
            $this->etat = $tab['etat'];
            $this->commentaires = $tab['commentaires'];
            $this->idCat = $tab['idCat'];
            
            $this->idPromo = "";
            $this->valeurPromo = $tab['valeurPromo'];
        }
        // cas n°3 : formulaire vierge mais id renseigné, on recherche les infos en base
        elseif ($id != "" && count($tab) == 0) {
            $db = new BD_connexion();
            $link = $db->getConnexion();
            $query = "SELECT p.*, m.marque FROM train_produits AS p, train_marques AS m WHERE p.idProduit = {$id} AND p.idMarque = m.idMarque";
            $result = mysql_query($query, $link) or die(mysql_error($link));

            $this->idProduit = mysql_result($result, 0, 'idProduit');
            $this->refProduit = mysql_result($result, 0, 'refProduit');
            $this->nom = mysql_result($result, 0, 'nom');
            $this->marque = mysql_result($result, 0, 'idMarque');
            $this->tarif = mysql_result($result, 0, 'tarif');
            
            // verifier si la base retourne bien un type booléen
            $this->nouveaute = mysql_result($result, 0, 'nouveaute');
            $this->etat = mysql_result($result, 0, 'etat');
            $this->img = mysql_result($result, 0, 'img');
            $this->img_zoom = mysql_result($result, 0, 'img_zoom');

            $this->commentaires = mysql_result($result, 0, 'commentaires');
            $this->idCat = mysql_result($result, 0, 'idCat');
            
            // recherche d'une éventuelle promo sur le produit
            $query3 = "SELECT * FROM train_promotions WHERE idProduit = {$this->idProduit}";
            $result3 = mysql_query($query3, $link) or die(mysql_error($link));
            
            if(mysql_num_rows($result3) > 0) {
                $this->idPromo = mysql_result($result3, 0, 'id');
                $this->valeurPromo = mysql_result($result3, 0, 'valeur');
            } else {
                $this->idPromo = "";
                $this->valeurPromo = "";
            }

            $db->closeConnexion();
        }
        // cas n°4 : formulaire rempli et id aussi : modification, on màj les infos
        elseif ($id != "" && count($tab) > 0) {
            $this->idProduit = $id;
            $this->refProduit = $tab['refProduit'];
            $this->nom = $tab['nom'];
            $this->marque = $tab['marque'];
            $this->tarif = $tab['tarif'];
            
            if(isset($tab['new']) && $tab['new'] == "new")
                $this->nouveaute = true;
            else
                $this->nouveaute = false;
            
            $this->etat = $tab['etat'];
            $this->commentaires = $tab['commentaires'];
            $this->idCat = $tab['idCat'];
            
            $this->idPromo = $tab['idPromo'];
            $this->valeurPromo = $tab['valeurPromo'];
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
        $html .= "<td>idProduit</td>";
        $html .= "<td><input type=\"text\" name=\"idProduit\" value=\"{$this->idProduit}\" readonly=\"readonly\" /></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>référence catalogue</td>";
        $html .= "<td><input type=\"text\" name=\"refProduit\" value=\"{$this->refProduit}\" /></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>nom Produit</td>";
        $html .= "<td><input type=\"text\" name=\"nom\" value=\"{$this->nom}\" />\n";
        $html .= "  <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2097152\"/></td>\n";
        $html .= "</tr>";
        
        $html .= "<tr>";
        $html .= "<td>marque</td>";
        $html .= "<td>\n";
        $html .= "<select name=\"marque\">\n";
        
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "SELECT * FROM train_marques"; 
        $result = mysql_query($query, $link) or die(mysql_error($link));
        
        $html .= "<option value=\"0\"></option>";
        
        while ($row = mysql_fetch_array($result)) {
            if($row["idMarque"] == $this->marque)
                $checked = " selected=\"selected\"";
            else
                $checked = "";
            
            $html .= "<option value=\"".$row["idMarque"]."\"".$checked.">".utf8_encode($row["marque"])."</option>";
        }
        
        $html .= "</select>"; 
        $html .= "</td>\n";
        $html .= "</tr>";
        
        $html .= "<tr>";
        $html .= "<td>tarif</td>";
        $html .= "<td><input type=\"text\" name=\"tarif\" value=\"{$this->tarif}\" />\n";
        $html .= "</tr>";
        
        $html .= "<tr>";
        $html .= "<td>nouveauté</td>";
        if($this->nouveaute)
            $checked = " checked=\"checked\"";
        else
            $checked = "";
        $html .= "<td><input type=\"checkbox\" name=\"new\" value=\"new\"{$checked}/></td>";
        $html .= "</tr>";
        
        // affichage de l'état
        $html .= "<tr>";
        $html .= "<td>état</td>";
        $html .= "<td>";
        $html .= "<select name=\"etat\">"; 
        
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "SELECT * FROM train_etats"; 
        $result = mysql_query($query, $link) or die(mysql_error($link));
        
        $html .= "<option value=\"0\"></option>";
        while ($row = mysql_fetch_array($result)) {
            if($row["idEtat"] == $this->etat)
                $checked = " selected=\"selected\"";
            else
                $checked = "";
            
            $html .= "<option value=\"".$row["idEtat"]."\"".$checked.">".utf8_encode($row["intitule"])."</option>";
        }
        $html .= "</select>"; 
        $html .= "</td>";
        $html .= "</tr>";
        
        $html .= "<tr>";
        $html .= "<td>image</td>";
        $html .= "<td>";
        if ($this->img != "")
            $html .= "<img src=\"images/uploaded/{$this->img}\" style=\"width:60px;\" />";
        $html .= "<input type=\"file\" name=\"img\" />\n";
        $html .= "</td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>zoom par défaut</td>";
        $html .= "<td>";
        if ($this->img_zoom != "")
            $html .= "<img src=\"images/uploaded/zoom/{$this->img_zoom}\" style=\"width:60px;\" />";
        $html .= "<input type=\"file\" name=\"img_zoom\" />\n";
        $html .= "</td>";
        $html .= "</tr>";
        
        $html .= "<tr class=\"spacer\"></tr>";
        
        $html .= "<tr class=\"promotion\">";
        $html .= "<td>Promotion</td>";
        $html .= "<td>";
        $html .= "id<input type=\"text\" name=\"idPromo\" value=\"{$this->idPromo}\" readonly=\"readonly\" />";
        $html .= "valeur <input type=\"text\" name=\"valeurPromo\" value=\"{$this->valeurPromo}\" /> %";
        $html .= "</td>";
        $html .= "<tr>";
        
        $html .= "<tr class=\"spacer\"></tr>";

        $html .= "<tr>";
        $html .= "<td>commentaires</td>";
        $html .= "<td><textarea name=\"commentaires\">{$this->commentaires}</textarea></td>\n";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td>cat&eacute;gorie</td>";
        $html .= "<td><select name=\"idCat\">\n";


        // on initialise les catégories
        $query = "SELECT * FROM train_categories WHERE idCatMere IS NULL";
        $result = mysql_query($query, $link) or die(mysql_error($link));
        $db->closeConnexion();

        $html .= "<option value=\"0\">Choisissez une cat&eacute;gorie</option>\n";
        while ($ligne = mysql_fetch_array($result)) {
            $selected = "";
            if ($this->idCat == $ligne['idCat'])
                $selected = "selected=\"selected\"";

            $html .= "<option value=\"0\"></option>";

            $requete = "SELECT * FROM train_categories WHERE idCatMere = {$ligne['idCat']}";
            $db = new BD_connexion();
            $link = $db->getConnexion();
            $result2 = mysql_query($requete, $link) or die(mysql_error($link));
            $db->closeConnexion();

            if (mysql_num_rows($result2) == 0) {
                $html .= "<option value=\"{$ligne['idCat']}\" {$selected}>-- " . utf8_encode($ligne['intitule']) . " --</option>";
            } else {
                $html .= "<option value=\"0\">-- " . utf8_encode($ligne['intitule']) . " --</option>";
                while ($ligne2 = mysql_fetch_array($result2)) {
                    // on initialise le selected pour afficher la catégorie d'appartenance
                    // (si possible)
                    $selected = "";
                    if ($this->idCat == $ligne2['idCat'])
                        $selected = " selected=\"selected\"";

                    $html .= "<option value=\"{$ligne2['idCat']}\"{$selected}>" . utf8_encode($ligne2['intitule']) . "</option>\n";
                }
            }
        }
        $html .= "</select></td>\n";
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
            $this->img = $this->traiteImage($_FILES['img'], 'images/uploaded/');
            $this->img_zoom = $this->traiteImage($_FILES['img_zoom'], 'images/uploaded/zoom/');

            if ($this->idProduit == "") { //nouvel enregistrement
                $query = "INSERT INTO train_produits
                    SET refProduit = '{$this->refProduit}',
                    nom = '" . addslashes($this->nom) . "',
                    idMarque = {$this->marque},
                    tarif = '{$this->tarif}',
                    nouveaute = ".($this->nouveaute?1:0).",
                    etat = ".$this->etat.",";

                if ($this->img != null)
                    $query .= "img = '{$this->img}',";
                if ($this->img_zoom != null)
                    $query .= "img_zoom = '{$this->img_zoom}',";

                $query .= "
                    commentaires = '" . addslashes($this->commentaires) . "',
                    idCat = {$this->idCat}";

                mysql_query($query) or die(mysql_error($link));

                $result_id = mysql_query("SELECT LAST_INSERT_ID()") or die(mysql_error($link));
                $id = mysql_result($result_id, 0);
                
                // enregistrement de la promotion
                if($this->valeurPromo != "") {
                    $query = "INSERT INTO train_promotions SET type = 'NORMAL', idProduit = {$id}, valeur = {$this->valeurPromo}";
                    mysql_query($query) or die(mysql_error($link));
                }
            }
            else {
                $query = "UPDATE train_produits SET
                    refProduit = '{$this->refProduit}',
                    nom = '" . addslashes($this->nom) . "',
                    idMarque = {$this->marque},
                    tarif = '{$this->tarif}',
                    nouveaute = ".($this->nouveaute?1:0).",
                    etat = ".$this->etat.",";

                if ($this->img != null)
                    $query .= "img = '{$this->img}',";
                if ($this->img_zoom != null)
                    $query .= "img_zoom = '{$this->img_zoom}',";

                $query .= "
                    commentaires = '" . addslashes($this->commentaires) . "',
                    idCat = {$this->idCat}
                    WHERE idProduit = {$this->idProduit}";

                echo $query;
                mysql_query($query) or die(mysql_error($link));

                // on met à jour la promotion
                if($this->valeurPromo != "") {
                    if($this->idPromo == "")
                        $query = "INSERT INTO train_promotions SET type = 'NORMAL', idProduit = {$this->idProduit}, valeur = {$this->valeurPromo}";
                    else
                        $query = "UPDATE train_promotions SET type = 'NORMAL', valeur = {$this->valeurPromo} WHERE idProduit = {$this->idProduit}";
                    mysql_query($query) or die(mysql_error($link));
                }
            }

            $db->closeConnexion();

            return null;
        }
        else
            return $this->afficherFormulaire();
    }

    // à revoir pour gérer le non remplissage des rajouts que j'ai pu faire suite
    // à la demande de JLD
    function verifierFormulaire() {
        $this->errors = "";

        if ($this->refProduit == "")
            $this->errors .= "<span>le champ <b>r&eacute;f&eacute;rence catalogue</b> doit être renseigné</span>";
        if ($this->nom == "")
            $this->errors .= "<span>le champ <b>nom</b> doit être renseigné</span>";
        if ($this->tarif == "" || $this->tarif <= 0)
            $this->errors .= "<span>le champ <b>tarif</b> doit être renseigné ou n'est pas valide</span>";
        
        if ($this->idCat == 0)
            $this->errors .= "<span>le produit doit appartenir à une catégorie</span>";

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

        if ($_FILES['img_zoom']['error']) {
            switch ($_FILES['img_zoom']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->errors .= "Le fichier <b>image pour zoom</b> dépasse la limite autorisée par le serveur";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->errors .= "Le fichier <b>image pour zoom</b> dépasse la limite autorisée dans le formulaire";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->errors .= "L'envoi du fichier <b>image pour zoom</b> a été interrompu pendant le transfert";
                    break;
            }
        }

        // vérification de la promotion
        if($this->valeurPromo != "") {
            if(!is_numeric($this->valeurPromo))
                $this->errors .= "<span>Une valeur <b>numérique</b> doit être entrée</span>";
            elseif($this->valeurPromo < 0)
                $this->errors .= "<span>Un poucentage ne peut être inférieur à zéro</span>";
            elseif($this->valeurPromo > 100)
                $this->errors .= "<span>Un pourcentage ne peut être supérieur à 100</span>";
        }
        return $this->errors;
    }

    function supprimerFormulaire() {
        $html = "<p>Etes-vous sûr de vouloir supprimer ce produit ?</p>";
        $html .= "<p><a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=n\">Non</a>
            / <a href=\"{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}&confirm=y\">Oui</a></p>";

        return $html;
    }

    function supprimer() {
        $db = new BD_connexion();
        $link = $db->getConnexion();
        $query = "DELETE FROM train_produits WHERE idProduit={$this->idProduit}";
        $query2 = "DELETE FROM train_promotions WHERE idProduit={$this->idProduit}";
        mysql_query($query) or die(mysql_error($link));
        mysql_query($query2) or die(mysql_error($link));
        $db->closeConnexion();
    }

    function traiteImage($f, $path) {
        if (isset($f['name']) && $f['error'] == UPLOAD_ERR_OK) {
            $chemin_destination = $path; // 'images/uploaded/'
            move_uploaded_file($f['tmp_name'],
                    $chemin_destination . $f['name']);
            return $f['name'];
        }

        return null;
    }

}

?>
