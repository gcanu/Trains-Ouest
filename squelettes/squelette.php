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
                </div>
                <div id="logos">
                    <img src="images/logos/electrotren.png" />
                    <img src="images/logos/epm.png" />
                    <img src="images/logos/faller.png" />
                    <img src="images/logos/heki.png" />
                    <img src="images/logos/jouef.png" />
                    <img src="images/logos/mkd.png" />
                    <img src="images/logos/peco.png" />
                    <img src="images/logos/preiser.png" />
                    <img src="images/logos/ree.png" />
                    <img src="images/logos/roco.png" />
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
        </div>
    </body>
</html>
