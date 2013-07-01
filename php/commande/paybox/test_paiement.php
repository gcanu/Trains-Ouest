<?php
//mode d'appel
     $PBX_MODE        = '4';    //pour lancement paiement par exécution
     //$PBX_MODE        = '1';    //pour lancement paiement par URL
//identification
     $PBX_SITE        = '1999888';
     $PBX_RANG        = '98';
     $PBX_IDENTIFIANT = '3';
//gestion de la page de connection : paramétrage "invisible"
     $PBX_WAIT        = '0';
     $PBX_TXT         = " ";
     $PBX_BOUTPI      = "nul";
     $PBX_BKGD        = "white";
//informations paiement (appel)
     $PBX_TOTAL       = '1290';
     $PBX_DEVISE      = '978';
     $PBX_CMD         = "ref cmd";
     $PBX_PORTEUR     = "test@e-transactions.fr";
//informations nécessaires aux traitements (réponse)
     $PBX_RETOUR      = "auto:A\;amount:M\;ident:R\;trans:T";
     $PBX_EFFECTUE    = "http://www.xxxxxxxxxx/effectue.php";
     $PBX_REFUSE      = "http://www.xxxxxxxxxx/refuse.php";
     $PBX_ANNULE      = "http://www.xxxxxxxxxx/annule.php";
//page en cas d'erreur
     $PBX_ERREUR      = "http://www.xxxxxxxxxx/erreur.php";

//construction de la chaîne de paramètres
     $PBX             = "PBX_MODE=$PBX_MODE PBX_SITE=$PBX_SITE PBX_RANG=$PBX_RANG PBX_IDENTIFIANT=$PBX_IDENTIFIANT PBX_WAIT=$PBX_WAIT PBX_TXT=$PBX_TXT PBX_BOUTPI=$PBX_BOUTPI PBX_BKGD=$PBX_BKGD PBX_TOTAL=$PBX_TOTAL PBX_DEVISE=$PBX_DEVISE PBX_CMD=$PBX_CMD PBX_PORTEUR=$PBX_PORTEUR PBX_EFFECTUE=$PBX_EFFECTUE PBX_REFUSE=$PBX_REFUSE PBX_ANNULE=$PBX_ANNULE PBX_ERREUR=$PBX_ERREUR PBX_RETOUR=$PBX_RETOUR";

//lancement paiement par exécution
    echo shell_exec( "/var/www/cgi-bin/modulev2.cgi $PBX" );
    
//lancement paiement par URL
//"http://www.xxxxxxxxxx/modulev2.cgi?PBX_MODE=$PBX_MODE&PBX_SITE=$PBX_SITE&PBX_RANG=$PBX_RANG&PBX_IDENTIFIANT=$PBX_IDENTIFIANT&PBX_WAIT=$PBX_WAIT&PBX_TXT=$PBX_TXT&PBX_BOUTPI=$PBX_BOUTPI&PBX_BKGD=$PBX_BKGD&PBX_TOTAL=$PBX_TOTAL&PBX_DEVISE=$PBX_DEVISE&PBX_CMD=$PBX_CMD&PBX_PORTEUR=$PBX_PORTEUR&PBX_EFFECTUE=$PBX_EFFECTUE&PBX_REFUSE=$PBX_REFUSE&PBX_ANNULE=$PBX_ANNULE&PBX_ERREUR=$PBX_ERREUR&PBX_RETOUR=$PBX_RETOUR"
?>
