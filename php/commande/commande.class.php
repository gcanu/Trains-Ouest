<?php

class Commande {

    var $cookie;
    var $commande;
    var $totalHT;
    var $totalTVA;
    var $totalTTC;
    var $promotion;
    var $montantPromotion;
    var $transport;

    function Commande($commande = array()) {
        if (count($commande) == 0) {
            // nous devons tenter de lire le cookie
            if (isset($_COOKIE['decobac']))
                $this->cookie = $_COOKIE['decobac'];
            else
                $this->cookie = "";
            $this->initCommande();
        }
        else {
            $this->commande = $commande;
            $this->totalHT = 0;
            $this->totalTVA = 0;
            $this->totalTTC = 0;

            for ($x = 0; $x < count($this->commande); $x++) {
                $this->totalHT += $this->commande[$x]['PTHT'];
                $this->totalTVA += $this->commande[$x]['TVA'];
                $this->totalTTC += $this->commande[$x]['PTTC'];
            }
        }

        $this->promotion = Outils::isPromoGlobale();
        $this->montantPromotion = round($this->totalTTC * $this->promotion) / 100;

        $this->transport = 0; // initalisation par defaut des frais de transport

    }

    function afficheDetails($montreLienConfirmation = true, $montreAcceptCgv = false) {
        $html = "";

        if (count($this->commande) == 0)
            $html .= "<p>Aucune commande n'a été enregistrée.</p>";
        else {
            $html .= "<table>";
            $html .= "  <tr>";
            $html .= "      <th>Réf.</th>";
            $html .= "      <th>Désignation</th>";
            $html .= "      <th>Qté</th>";
            $html .= "      <th>Prix unitaire TTC</th>";
            $html .= "      <th>Total TTC</th>";
            $html .= "  </tr>";
            for ($x = 0; $x < count($this->commande); $x++) {
                $html .= "  <tr>";
                $html .= "      <td>{$this->commande[$x]['ref']}</td>";
                $html .= "      <td>{$this->commande[$x]['designation']}</td>";
                $html .= "      <td>{$this->commande[$x]['quantite']}</td>";

                // si le prix est en promo, affichage particulier
                $produit = Outils::getProduit($this->commande[$x]['ref']);
                $promo = Outils::getPromo($produit['id']);

                if ($promo == 0)
                    $html .= "      <td>" . Outils::formatPrix($this->commande[$x]['PUTTC']) . "</td>";
                else {
                    $html .= "      <td>";
                    $html .= "      <span style=\"text-decoration: line-through\">" . Outils::formatPrix($produit['tarif']) . "</span><br/>";
                    $html .= "prix promo " . Outils::formatPrix($this->commande[$x]['PUTTC']);
                    $html .= "      </td>";
                }

                $html .= "      <td>" . Outils::formatPrix($this->commande[$x]['PTTC']) . "</td>";
                $html .= "  </tr>";
            }

            $html .= "</table>";

            $html .= "<table id=\"total\">";
            $html .= "  <tr>";
            $html .= "      <td class=\"firstColumn\">Total TTC</td><td>" . Outils::formatPrix($this->totalTTC) . "</td>";
            $html .= "  </tr>";

            if ($this->promotion > 0) {
                $html .= "  <tr>";
                $html .= "      <td class=\"firstColumn\">Remise {$this->promotion}%</td><td>" . Outils::formatPrix($this->montantPromotion) . "</td>";
                $html .= "  </tr>";
            }

            // TODO créer un parametre pour livraison gratuite
            $html .= "  <tr>";
            $html .= "      <td class=\"firstColumn\">Frais de transport</td>";
            $html .= "      <td>";

	    $transport = $this->getTransport();
            if($transport > 0)
                $html .= Outils::formatPrix($transport);
            else
                $html .= "gratuits";

            $html .= "      </td>";
            $html .= "  </tr>";

            $html .= "  <tr>";
            $html .= "      <td class=\"firstColumn\">Total TTC à Payer</td><td>" . Outils::formatPrix($this->totalTTC - $this->montantPromotion + $transport) . "</td>";
            $html .= "  </tr>";

            $html .= "  <tr>";
            $html .= "      <td class=\"firstColumn\">dont TVA 19,6%</td><td>" . Outils::formatPrix($this->totalTVA) . "</td>";
            $html .= "  </tr>";

            $html .= "</table>";

            $html .= "<div class=\"spacer\">&nbsp;</div>";

            if ($montreAcceptCgv) {
                $html .= "  <p id=\"cgv-acceptance\">";
                $html .= "      <input id=\"cgv-cb\" type=\"checkbox\">";
                $html .= "      J'ai lu et j'accepte les <a href=\"index.php?a=cgv\" target=\"_blank\">conditions générales de vente</a>";
                $html .= "  </p>";
                $html .= "  <p id=\"validation-button\">";
                $html .= "      <a href=\"index.php?a=commande&p=1\"></a>";
                $html .= "  </p>";
            }
            
            if($montreLienConfirmation) {
                $html .= "<div class=\"actions\">";
                $html .= "  <p class=\"title\">Votre commande</p>";
                $html .= "  <p>";
                $html .= "      <a href=\"{$_SERVER['PHP_SELF']}?a=commande&s=1\">confirmer la commande</a><br/>";
                $html .= "      <a href=\"{$_SERVER['PHP_SELF']}\">continuer vos achats</a>";
                $html .= "  </p>";
                $html .= "</div>";
            }
        }

        return $html;
    }
    
    function afficheDetailsMail() {
        $html = "";

        if (count($this->commande) == 0)
            $html .= "<p>Aucune commande n'a été enregistrée.</p>\n";
        else {
            $html .= "<table align=\"center\" border=\"1\" style=\"border-collapse:collapse\" width=\"100%\">\n";
            $html .= "  <tr height=\"24\" style=\"color:white; background-color: #7AB91F\" bordercolor=\"#7AB91F\">\n";
            $html .= "      <th>Réf.</th>\n";
            $html .= "      <th>Désignation</th>\n";
            $html .= "      <th>Qté</th>\n";
            $html .= "      <th>Prix unitaire TTC</th>\n";
            $html .= "      <th>Total TTC</th>\n";
            $html .= "  </tr>\n";
            for ($x = 0; $x < count($this->commande); $x++) {
                $html .= "  <tr height=\"18\" align=\"center\">\n";
                $html .= "      <td>{$this->commande[$x]['ref']}</td>\n";
                $html .= "      <td>{$this->commande[$x]['designation']}</td>\n";
                $html .= "      <td>{$this->commande[$x]['quantite']}</td>\n";

                // si le prix est en promo, affichage particulier
                $produit = Outils::getProduit($this->commande[$x]['ref']);
                $promo = Outils::getPromo($produit['id']);

                if ($promo == 0)
                    $html .= "      <td>" . Outils::formatPrix($this->commande[$x]['PUTTC']) . "</td>\n";
                else {
                    $html .= "      <td>\n";
                    $html .= "      <span style=\"text-decoration: line-through\">" . Outils::formatPrix($produit['tarif']) . "</span><br/>";
                    $html .= "prix promo " . Outils::formatPrix($this->commande[$x]['PUTTC']) . "\n";
                    $html .= "      </td>\n";
                }

                $html .= "      <td>" . Outils::formatPrix($this->commande[$x]['PTTC']) . "</td>\n";
                $html .= "  </tr>\n";
            }

            $html .= "</table>\n";
            
            $html .= "<br/>";

            $html .= "<table border=\"1\" align=\"right\" style=\"border-collapse:collapse\">\n";
            $html .= "  <tr height=\"18\" align=\"center\">\n";
            $html .= "      <td style=\"color:white; background-color: #7AB91F\">Total TTC</td><td width=\"60\">" . Outils::formatPrix($this->totalTTC) . "</td>\n";
            $html .= "  </tr>\n";

            if ($this->promotion > 0) {
                $html .= "  <tr height=\"18\" align=\"center\">\n";
                $html .= "      <td style=\"color:white; background-color: #7AB91F\">Remise {$this->promotion}%</td><td width=\"60\">" . Outils::formatPrix($this->montantPromotion) . "</td>\n";
                $html .= "  </tr>\n";
            }

            // TODO créer un parametre pour livraison gratuite
            $html .= "  <tr height=\"18\" align=\"center\">\n";
            $html .= "      <td style=\"color:white; background-color: #7AB91F\">Frais de transport</td>\n";
            $html .= "      <td width=\"60\">\n";

	    $transport = $this->getTransport();
            if($transport > 0)
                $html .= Outils::formatPrix($transport);
            else
                $html .= "gratuits";

            $html .= "      </td>\n";
            $html .= "  </tr>\n";

            $html .= "  <tr height=\"18\" align=\"center\">\n";
            $html .= "      <td style=\"color:white; background-color: #7AB91F\">Total TTC à Payer</td><td width=\"60\">" . Outils::formatPrix($this->totalTTC - $this->montantPromotion + $transport) . "</td>\n";
            $html .= "  </tr>\n";

            $html .= "  <tr height=\"18\" align=\"center\">\n";
            $html .= "      <td style=\"color:white; background-color: #7AB91F\">dont TVA 19,6%</td><td width=\"60\">" . Outils::formatPrix($this->totalTVA) . "</td>\n";
            $html .= "  </tr>\n";

            $html .= "</table>\n";

            $html .= "<div class=\"spacer\">&nbsp;</div>";
        }

        return $html;
    }

    function initCommande() {
        $this->commande = array();

        $this->totalHT = 0;
        $this->totalTVA = 0;
        $this->totalTTC = 0;

        if (strlen($this->cookie) > 0) {
            $items = explode("~", $this->cookie);

            // pour chaque item, on parcourt les données
            for ($x = 0; $x < count($items); $x++) {
                $infos = explode(";", $items[$x]);
                $idTaille = $infos[0];
                $quantite = $infos[1];

                $db = new BD_connexion();
                $link = $db->getConnexion();

                // on récupère toutes les infos pour déterminer la référence du produit
                $query = "SELECT idProduit, taille FROM train_tailles WHERE idTaille = {$idTaille}";
                $result = mysql_query($query, $link) or die(mysql_error($link));
                $idProduit = mysql_result($result, 0, 'idProduit');
                $taille = mysql_result($result, 0, 'taille');

                $outils = new Outils();
                $puttc = $outils->getPrix($idProduit, $idTaille);
                $pttc = $puttc * $quantite;
                $ptht = $pttc / 1.196;
                $tva = $pttc - $ptht;
                $puht = $ptht / $quantite;

                $query2 = "SELECT idCouleur, nomCouleur FROM train_couleurs WHERE codeCouleur = '{$infos[2]}' AND idProduit = {$idProduit}";
                $result2 = mysql_query($query2, $link) or die(mysql_error($link));
                $idCouleur = mysql_result($result2, 0, 'idCouleur');
                $refCouleur = mysql_result($result2, 0, 'nomCouleur');

                // on génère une designation à partir des informations recueillies
                $query3 = "SELECT * FROM train_produits WHERE idProduit = {$idProduit}";
                $result3 = mysql_query($query3, $link) or die(mysql_error($link));
                $designation = mysql_result($result3, 0, 'nom');
                $designation .= ", taille : " . $taille . "cm, ";

                $designation .= " couleur : " . $refCouleur;

                $db->closeConnexion();

                $ref = Outils::getReference($idProduit, $idCouleur, $idTaille);

                $this->commande[$x] = array(
                    'ref' => $ref,
                    'designation' => $designation,
                    'quantite' => $quantite,
                    'PUHT' => $puht,
                    'PTHT' => $ptht,
                    'PUTTC' => $puttc,
                    'TVA' => $tva,
                    'PTTC' => $pttc);

                $this->totalHT += $ptht;
                $this->totalTVA += $tva;
                $this->totalTTC += $pttc;
            }
        }
    }

    // fonction de calcul des frais de transport
    function getTransport() {
        if($this->totalTTC < 50)
            $this->transport = 6;
        else if($this->totalTTC < 100)
            $this->transport = 7.5;
        else if($this->totalTTC < 200)
            $this->transport = 10;
        else
            $this->transport = 0;

        return $this->transport;
    }

    function getTotal() {
        return $this->totalTTC + $this->getTransport();
    }

    // fonction de sérialisation de la commande pour export ou stockage
    function toString() {
        $commandeString = "";
        if (count($this->commande) > 0)
            $commandeString = Outils::hash($this->commande);
        return $commandeString;
    }

}

?>
