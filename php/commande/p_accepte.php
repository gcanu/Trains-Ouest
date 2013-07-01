<?php

/** Désactivation temporaire des lignes suivantes**/
//$montant = $_GET['montant'];
//$ref_com = $_GET['ref'];
//$auto = $_GET['auto'];
//$trans = $_GET['trans'];
//print ("<center><b><h2>Votre transaction a été acceptée</h2></center></b><br>");
//print ("<br><b>MONTANT : </b>$montant\n");
//print ("<br><b>REFERENCE : </b>$ref_com\n");
//print ("<br><b>AUTO : </b>$auto\n");
//print ("<br><b>TRANS : </b>$trans\n");

$auth = $_SESSION['auth'];
$email = Personne::getEmail($auth->login);

if (!is_null($email)) {
    // on retrouve le commande
    $commande = new Commande();

    $to = $email;
    $subject = "Votre commande DECOBAC";

    $html = "
            <html xmlns=\"http://www.w3.org/1999/xhtml\">
              <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
                <style type=\"text/css\">
                td {
                    padding: 3px;
                }
                </style>
              </head>
              <body>
        ";

    //$html .= $commande->afficheDetails(false);
    $html .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"600\" align=\"center\">";
    
    $html .= "<tr height=\"24\">";
    $html .= "<td><p>Voici le récapitulatif de votre achat :</p></td>";
    $html .= "</tr>";
    
    $html .= "<tr>";
    $html .= "<td>" . $commande->afficheDetailsMail(false) . "</td>";
    $html .= "</tr>";
    
    $html .= "<tr height=\"24\">";
    $html .= "<td><p>La commande a été enregistrée et est en cours de traitement. Merci de votre confiance.</p></td>";
    $html .= "</tr>";
    
    $html .= "</table>";

    $html .= "
              </body>
            </html>
        ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    $headers .= "From: guy.canu@gmail.com" . "\r\n";

    $result = mail($to, $subject, $html, $headers);
    
    if($result)
        $contenu .= "Le mail a été envoyé à ".$to;
    else
        $contenu .= "Le mail n'a pas été envoyé";

    // enregistrement de la commande
    $commande = new Commande();
    $serializedCommande = $commande->toString();

    $db = new BD_connexion();
    $link = $db->getConnexion();

    $query = "INSERT INTO commandes SET idUser = " . $_SESSION['auth']->getId() . ", factureObjet = '" . $serializedCommande . "'";
    mysql_query($query, $link) or die(mysql_error($link));

    $db->closeConnexion();
}
?>