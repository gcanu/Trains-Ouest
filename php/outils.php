<?php

class Outils {

    function formatPrix($prix) {
        // on arrondi le prix par sécurité
        $prix = round($prix, 2);

        if (floor($prix) == $prix)
            $prix = $prix . ",00 €";
        elseif (floor($prix * 10) == $prix * 10)
            $prix = $prix . "0 €";
        else
            $prix = $prix . " €";

        return str_replace(".", ",", $prix);
    }

    function getPrix($idProduit, $calculePromo=true, $formate=false) {
        $db = new BD_connexion();
        $link = $db->getConnexion();
	
        $query = "SELECT tarif FROM produits WHERE idProduit = {$idProduit}";
        $result = mysql_query($query, $link) or die(mysql_error());

        if (mysql_num_rows($result) > 0) {
            $tarif = mysql_result($result, 0, 'tarif');

            if ($calculePromo) {

                $query2 = "SELECT valeur FROM promotions WHERE idProduit = {$idProduit}";
                $result2 = mysql_query($query2, $link) or die(mysql_error());

                $promo = 0;
                if (mysql_num_rows($result2) > 0)
                    $promo = mysql_result($result2, 0, 'valeur');

                $prix = $tarif * (1 - $promo / 100);

                $db->closeConnexion();
            }
            else {
                $db->closeConnexion();
                $prix = $tarif;
            }

            if ($formate)
                return $this->formatPrix($prix);
            else
                return $prix;
        }
        else
            return false;
    }

    function isPromoGlobale() {
        $db = new BD_connexion();
        $link = $db->getConnexion();

        $query = "SELECT * FROM promotions WHERE type='GLOBAL'";
        $result = mysql_query($query, $link) or die(mysql_error());

        $db->closeConnexion();

        if (mysql_num_rows($result) > 0)
            return mysql_result($result, 0, 'valeur');
        else
            return 0;
    }

    function getPromo($idProduit) {
        $db = new BD_connexion();
        $link = $db->getConnexion();

        $query = "SELECT * FROM promotions WHERE idProduit={$idProduit}";
        $result = mysql_query($query, $link) or die(mysql_error());

        $db->closeConnexion();
        if (mysql_num_rows($result) > 0) {
            $type = mysql_result($result, 0, 'type');
            $valeur = mysql_result($result, 0, 'valeur');
            return array("type" => $type, "valeur" => $valeur);
        }
        else
            return 0;
    }

    // cette fonction serialise un objet et le crypte
    function hash($objet) {
        $serialized_object = serialize($objet);
        $compressed_object = gzcompress($serialized_object, 9);
        return base64_encode($compressed_object);
    }

    // cette fonction decrypte et désérialise un objet (c'est la fonction in-
    // verse de la fonction hash
    function unhash($hash) {
        $compressed_object = base64_decode($hash);
        $serialized_object = gzuncompress($compressed_object);
        return unserialize($serialized_object);
    }

    function filtrerTableau($tab, $ch) { // filtre un tableau en fonction d'un prefixe
        $newTab = array();

        for ($x = 0; $x < count($tab); $x++) {
            $element = each($tab);
            if (substr($element['key'], 0, strlen($ch)) == $ch)
                $newTab[$element['key']] = $element['value'];
        }

        return $newTab;
    }

    function removeaccents($string) {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    function replaceSpace($string) {
        $string = strtr($string, ' ', '_');
        return $string;
    }

    function SQLDate($normalDate) {
        return substr($normalDate, 6, 4) . "-" . substr($normalDate, 3, 2) . "-" . substr($normalDate, 0, 2);
    }

    function NormalDate($sqlDate) {
        return substr($sqlDate, 8, 2) . "/" . substr($sqlDate, 5, 2) . "/" . substr($sqlDate, 0, 4);
    }

    function ariane() {
        $html = "<p id=\"ariane\"><a href=\"index.php\">accueil</a>";

        $queryString = $_SERVER['QUERY_STRING'];
        $ariane = "";

        if (preg_match("/a=(.+)&/", $queryString, $matches)) {
            $db = new BD_connexion();
            $link = $db->getConnexion();

            $action = $matches[1];
            $category = 0;

            if ($action == "prod") {
                if (preg_match("/idProd=([0-9]+)/", $queryString, $matches))
                    $idProduit = $matches[1];

                $query = "SELECT * FROM produits WHERE idProduit = {$idProduit}";
                $result = mysql_query($query, $link) or die(mysql_error());

                $category = mysql_result($result, 0, 'idCat');
                $nomProduit = mysql_result($result, 0, 'nom');
            }

            if ($action == "view_cat") {
                if (preg_match("/cat=([0-9]+)/", $queryString, $matches))
                    $category = $matches[1];
            }

            $idCatMere = $category;

            while ($idCatMere != null) {
                $query = "SELECT * FROM categories WHERE idCat={$idCatMere}";
                $result = mysql_query($query, $link) or die(mysql_error());

                $intitule = utf8_encode(mysql_result($result, 0, 'intitule'));
                $ariane = " > <a href=\"index.php?a=view_cat&cat={$idCatMere}\">" . $intitule . "</a>" . $ariane;
                $idCatMere = mysql_result($result, 0, 'idCatMere');
            }

            if ($action == "prod") {
                $ariane .= " > " . $nomProduit;
            }
        }

        $html .= $ariane . "</p>";

        $db->closeConnexion();

        return $html;
    }

    function getURI() {
        echo "<pre>";
        var_dump($_SERVER);
        echo "</pre>";
    }
}

?>
