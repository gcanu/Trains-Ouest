<?php
$num_err=$_GET['NUMERR'];
if ( $num_err == -1 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : erreur de lecture des param�tres via stin. <br>");
	}
else if ( $num_err == -2 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : erreur d'allocation m�moire. <br>");
	}
else if ( $num_err == -3 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : erreur de lecture des param�tres QUERY_STRING ou CONTENT_LENGTH. <br>");
	}
else if ( $num_err == -4 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_RETOUR, PBX_ANNULE, PBX_REFUSE ou PBX_EFFECTUE sont trop longs (<150 caract�res). <br>");
	}
else if ( $num_err == -5 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : ouverture de fichiers (si PBX_MODE contient 3) : fichier local inexistant, non trouv� ou erreur d'acc�s. <br>");
	}

else if ( $num_err == -6 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : ouverture de fichiers (si PBX_MODE contient 3) : fichier local mal form�, vide ou ligne mal format�e. <br>");
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
	print (" message erreur : Une variable num�rique contient un caract�re non num�rique. <br>");
	}
else if ( $num_err == -9 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_SITE contient un num�ro de site qui ne fait pas exactement 7 caract�res. <br>");
	}
else if ( $num_err == -10 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_RANG contient un num�ro de rang qui ne fait pas exactement 2 caract�res. <br>");
	}
else if ( $num_err == -11 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_TOTAL fait plus de 10 ou moins de 3 caract�res num�riques. <br>");
	}
else if ( $num_err == -12 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_LANGUE ou PBX_DEVISE contient un code qui ne fait pas exactement 3 caract�res. <br>");
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
	print (" message erreur : PBX_CLE ne contient pas une cl� (mot de passe) valide. <br>");
	}
else if ( $num_err == -18 )
	{
	print ("<center><b><h2>Erreur appel PAYBOX.</h2></center></b>");
	print ("<br><br><br>");
	print (" message erreur : PBX_RETOUR : Donn�es � retourner inconnues. <br>");
	}

print("<b>Num�ro de l'erreur : </b>$num_err\n");
?>