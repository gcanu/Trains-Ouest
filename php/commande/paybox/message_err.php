<?php
$num_err=$_GET['NUMERR'];
if ( $num_err == -1 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : erreur de lecture des paramètres via stin. <br>");
	}
else if ( $num_err == -2 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : erreur d'allocation mémoire. <br>");
	}
else if ( $num_err == -3 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : erreur de lecture des paramètres QUERY_STRING ou CONTENT_LENGTH. <br>");
	}
else if ( $num_err == -4 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_RETOUR, PBX_ANNULE, PBX_REFUSE ou PBX_EFFECTUE sont trop longs (<150 caractères). <br>");
	}
else if ( $num_err == -5 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : ouverture de fichiers (si PBX_MODE contient 3) : fichier local inexistant, non trouvé ou erreur d'accès. <br>");
	}

else if ( $num_err == -6 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : ouverture de fichiers (si PBX_MODE contient 3) : fichier local mal formé, vide ou ligne mal formatée. <br>");
	}
else if ( $num_err == -7 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : Il manque une variable obligatoire. <br>");
	}
else if ( $num_err == -8 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : Une variable numérique contient un caractère non numérique. <br>");
	}
else if ( $num_err == -9 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_SITE contient un numéro de site qui ne fait pas exactement 7 caractères. <br>");
	}
else if ( $num_err == -10 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_RANG contient un numéro de rang qui ne fait pas exactement 2 caractères. <br>");
	}
else if ( $num_err == -11 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_TOTAL fait plus de 10 ou moins de 3 caractères numériques. <br>");
	}
else if ( $num_err == -12 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_LANGUE ou PBX_DEVISE contient un code qui ne fait pas exactement 3 caractères. <br>");
	}
else if ( $num_err == -16 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_PORTEUR ne contient pas une adresse e-mail valide. <br>");
	}
else if ( $num_err == -17 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_CLE ne contient pas une clé (mot de passe) valide. <br>");
	}
else if ( $num_err == -18 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_RETOUR : Données à retourner inconnues. <br>");
	}

print("<b>Numéro de l'erreur : </b>$num_err\n");
?>