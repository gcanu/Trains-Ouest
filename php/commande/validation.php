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
elseif($_GET['p'] == 1) {
    // message
    $contenu .= "<div>";
    $contenu .= "    <h1>Traitement de votre pré-commande</h1>";
    $contenu .= "    <p style='font-family: Arial; font-size: 13px;'>Vous pouvez adresser votre ch&egrave;que a Trains-Ouest.</p>";
    $contenu .= "    <p style='font-family: Arial; font-size: 13px;'>Nous traiterons votre commande d&egrave;s r&eacute;ception de votre paiement. Un email de confirmation vous a &eacute;t&eacute; envoy&eacute; avec le descriptif de votre commande. Merci de votre confiance...</p>";
    $contenu .= "</div>";

    // envoi de la commande par email
    $auth = $_SESSION['auth'];
    $email = Personne::getEmail($auth->login);
    
    if(!is_null($email)) {
        // on retrouve le commande
        $commande = new Commande();
         
        $to = $email;
        $subject = "Votre commande Trains-Ouest";
        
        $html = "
            <html>
              <head>
                  <title>R&eacute;capitulatif de votre commande Trains-Ouest</title>
                  <style type=\"text/css\">
                      table {
                          border-collapse: collapse;
                      }
                      
                      table, td, tr {
                          border: solid 1px black;
                      }

                      #total {
                          margin-top: 20px;
                          float: left;
                     }
                  </style>
              </head>
              <body>
                  <p>Voici le r&eacute;capitulatif de votre commande :</p>
        ";

        $html .= $commande->afficheDetails(false);

        $html .= "
                <p>Votre commande a &eacute;t&eacute; confirm&eacute;e, elle sera trait&eacute;e des r&eacute;ception de votre ch&egrave;que.</p>          
              </body>
            </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $headers .= "From: guy.canu@gmail.com" . "\r\n";

        $result = mail($to, $subject, $html, $headers);

        // enregistrement de la commande
        $commande = new Commande();
        $serializedCommande = $commande->toString();

        $db = new BD_connexion();
        $link = $db->getConnexion();

        $query = "INSERT INTO commandes SET idUser = ".$_SESSION['auth']->getId().", factureObjet = '".$serializedCommande."'";
        mysql_query($query, $link) or die(mysql_error($link));
        
        $db->closeConnexion(); 
    }
}
?>
