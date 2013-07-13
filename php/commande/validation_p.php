<?php

// note : lettre attribuée pour la query string :
// s = soumettre (confirmation de la commande)
// l = login
// p = paiement

if (!isset($_GET['s']) && !isset($_GET['p'])) {
    $commande = new Commande();
    $contenu .= "<h1>Récapitulatif de votre commande</h1>";
    $contenu .= $commande->afficheDetails();
}
elseif (!isset($_GET['p']) && $_GET['s'] == 1) {
    // on teste si la personne s'est authentifiée
    if ($_SESSION['auth']->getStatut() == "defaut") {

        if (!isset($_GET['l'])) {
            $contenu .= "Vous n'êtes pas authentifié, merci d'entrer votre
            login/mot de passe ou de
            <a href=\"{$_SERVER['PHP_SELF']}?a=commande&s=1&l=2\">créer un compte</a>";
            $action = $_SERVER['PHP_SELF'] . "?a=commande&s=1&l=1";
            $contenu .= $_SESSION['auth']->afficheFormulaire($action);
        }        
        elseif($_GET['l'] == 1) {
            $_SESSION['auth']->traite_action("login");
            $contenu .= "<script>document.location = 'index.php?a=commande&s=1'</script>";
        }
        else {
            // la personne doit s'inscrire, on affiche le formulaire d'inscription
            if(!isset($_POST['valid'])) {
                $personne = new Personne();
                $contenu .= $personne->afficherFormulaire();
            }
            else {
                $personne = new Personne($_POST['idUser'], $_POST);
                $resultat = $personne->enregistrer(false);
                
                if($resultat == null) {
                    $_SESSION['auth']->login($_POST['login'], $_POST['passwd']);
                    $contenu .= "<script>document.location = 'index.php?a=commande&s=1'</script>";
                }
                else // un résultat non null signifie un échec d'inscription, le formulaire est réaffiché
                    $contenu .= $resultat;
            }
        }
    }
    else {
        // la personne est authentifiée, on affiche le formulaire de renseigne-
        // ments servant à saisir les informations de livraisons et de factura-
        // tion.

        $idPersAuth = $_SESSION['auth']->getId();

        if(!isset($_POST['valid'])) {
            // on demande les différentes adresses
            $renseignements = new Renseignements($idPersAuth);
            $contenu .= $renseignements->afficherFormulaire();
        }
        else {
            $renseignements = new Renseignements($idPersAuth, $_POST);
            $renseignements->enregistrer();

            $contenu .= $renseignements->afficherRenseignements();
            
            $contenu .= "<div class=\"actions\">";
            $contenu .= "   <p class=\"title\">Ces renseignements sont-ils corrects ?</p>";
            $contenu .= "   <p>";
            $contenu .= "       <a href=\"index.php?a=commande&s=1\">Non, modifier mon identité</a><br/>";
            $contenu .= "       <a href=\"index.php?a=commande&s=1\">Non, modifier les adresses</a><br/>";
            $contenu .= "       <a href=\"index.php?a=print\" target=\"_blank\">Imprimer ces renseignements</a> ";
            $contenu .= "   </p>";
            $contenu .= "</div>";
        }
    }
}
// Le cas suivant repr�sente la fin du processus de validation de la commande et le lancement du processus de paiement en ligne
elseif($_GET['p'] == 1) {
    // on inscrit la commande en base
    $commande = new Commande();
    $serializedCommande = $commande->toString();

    $db = new BD_connexion();
    $link = $db->getConnexion();

    $query = "INSERT INTO train_commandes SET idUser = ".$_SESSION['auth']->getId().", factureObjet = '".$serializedCommande."'";
    mysql_query($query, $link) or die(mysql_error($link));

   /**
    * Definition des parametres pour le paiement en ligne
    */

    //mode d'appel
    $PBX_MODE        = '4';    //pour lancement paiement par ex�cution
    //$PBX_MODE        = '1';    //pour lancement paiement par URL

    //identification
    $PBX_SITE        = '0875343';
    $PBX_RANG        = '01';
    $PBX_IDENTIFIANT = '322715974';

    //gestion de la page de connection : param�trage "invisible"
    $PBX_WAIT        = '0';
    $PBX_TXT         = " ";
    $PBX_BOUTPI      = "nul";
    $PBX_BKGD        = "white";

    //informations paiement (appel)
    $PBX_TOTAL       = $commande->getTotal()*100; // conversion en centimes du total a payer
    $PBX_DEVISE      = '978'; // transaction en euros
    $PBX_CMD         = "ref cmd";
    $PBX_PORTEUR     = "test@e-transactions.fr";

    //informations n�cessaires aux traitements eponse)
    $PBX_RETOUR      = "auto:A\;amount:M\;ident:R\;trans:T";
    $PBX_EFFECTUE    = "http://www.decobac.fr/projet/index.php?a=paiement_ok";
    $PBX_REFUSE      = "http://www.decobac.fr/projet/index.php?a=paiement_ko";
    $PBX_ANNULE      = "http://www.decobac.fr/projet/index.php?a=paiement_annule";

    //page en cas d'erreur
    $PBX_ERREUR      = "http://www.decobac.fr/index.php?a=paiement_erreur";

    //construction de la cha�ne de param�tres
    $PBX             = "PBX_MODE=$PBX_MODE PBX_SITE=$PBX_SITE PBX_RANG=$PBX_RANG PBX_IDENTIFIANT=$PBX_IDENTIFIANT PBX_WAIT=$PBX_WAIT PBX_TXT=$PBX_TXT PBX_BOUTPI=$PBX_BOUTPI PBX_BKGD=$PBX_BKGD PBX_TOTAL=$PBX_TOTAL PBX_DEVISE=$PBX_DEVISE PBX_CMD=$PBX_CMD PBX_PORTEUR=$PBX_PORTEUR PBX_EFFECTUE=$PBX_EFFECTUE PBX_REFUSE=$PBX_REFUSE PBX_ANNULE=$PBX_ANNULE PBX_ERREUR=$PBX_ERREUR PBX_RETOUR=$PBX_RETOUR"; 

    //lancement paiement par ex�cution
    echo shell_exec( "/var/www/cgi-bin/modulev2.cgi $PBX" );
}
?>
