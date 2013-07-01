<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
        <title>Trains-Ouest</title>
        <?php
        for ($x = 0; $x < sizeof($css); $x++)
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/{$css[$x]}\" media=\"screen\" />\n";
        for ($x = 0; $x < sizeof($scripts); $x++)
            echo "<script src=\"scripts/{$scripts[$x]}\" type=\"text/javascript\"></script>\n";
        ?>
        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-2092908-3']);
            _gaq.push(['_setDomainName', 'decobac.fr']);
            _gaq.push(['_setAllowLinker', true]);
            _gaq.push(['_trackPageview']);

           (function() {
               var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
               ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
               var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
           })();

        </script>
    </head>
    <body>
        <div id="bg">
            <div id="wrapper_top"></div>
            <div id="wrapper" class="clearfix">
                <div>
                    <a href="index.php"><div id="banner"></div></a>
                    <div id="navigh">
                        <a href="index.php?a=connexion"><div id="navigh1"></div></a>
                        <a href="index.php?a=inscription"><div id="navigh2"></div></a> 
                    </div>
                    <div id="panier">
                        <span id="p_total">0,00 &euro;</span>
                        <div id="liste_article"></div>
                        <div id="panier-aide"></div>
                    </div>
                </div>
                <div id="main_wrapper" class="clearfix">
                    <div id="content_wrapper">
                        <?php echo $contenu; ?>
                    </div>
                    <div class="columns" id="left_column">
                        <?php if(isset($menu_gauche)) echo $menu_gauche; ?>
                    </div>
                </div>
                <div id="bottom_wrapper" class="clearfix">
                    <?php echo $bas; ?>
                </div>
            </div>
            <div id="wrapper_bottom"></div>
            <div id="panier-aide-content">
                <div class="content">
                    <h2>Fonctionnement du panier</h2>
                    <p>
                        Le panier contient les produits que vous avez commandé. 
                        A tout moment vous pouvez modifier son contenu
                    </p>
                    <p>
                        Si vous souhaitez supprimer un article, cliquez sur <img src="images/fermer_petit.png" /> à côté du produit.
                    </p>
                    <p>
                        Si vous souhaitez modifier un article, supprimez-le et refaite la commande.
                    </p>
                </div>
                <div class="close"></div>
            </div>
        </div>
    </body>
</html>
