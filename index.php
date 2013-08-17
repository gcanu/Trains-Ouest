<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// chargement des bibliothèques de fonctions
require("php/connexion.php");
require("php/outils.php");
require("php/authentification.php");
require("php/form/produit.class.php");
require("php/form/AfficheProduit.class.php");
require("php/form/personne.class.php");
require("php/catProd.class.php");
require("php/form/produitManager.class.php");
require("php/form/personneManager.class.php");
require("php/form/commandeManager.class.php");
require("php/commande/renseignements.class.php");
require("php/form/adresse.class.php");
require("php/commande/commande.class.php");
require("php/form/promotion.class.php");
require("php/form/promotionManager.class.php");
require("php/form/nouveautes.class.php");
require("php/form/nouveautesManager.class.php");
require("php/form/dossiers.class.php");
require("php/form/dossiersManager.class.php");
require("php/galerie.class.php");
require("php/resize.php");

session_start();

// vérification de l'authentification
if (!isset($_SESSION['auth']))
    $_SESSION['auth'] = new authentification();

// traitement si besoin de l'authentification
if (isset($_GET['action']))
    $_SESSION['auth']->traite_action($_GET['action']);

if (isset($_GET['a']))
    $a = $_GET['a'];
else
    $a = '';

$css = array("main.css");
array_push($css, "dialogBox.css");

$scripts = array(
    "mooTools.js",
    "mooTools-more.js",
    "Observer.class.js",
    "SimpleDialog.class.js",
    "main.js");

$contenu = "";

switch ($a) {
    case 'interdit':
        $contenu = "Cette page est réservée à l'administrateur de ce site";
        break;

    case 'admin':
        $contenu = "
            <p>Console d'administration</p>
            <p>
                <a href=\"index.php?a=ges_abo\">Gérer les abonnés</a><br/>
                <a href=\"index.php?a=ges_pro\">Gérer les produits</a><br/>
                <a href=\"index.php?a=ges_com\">Gérer les commandes</a><br/>
                <a href=\"index.php?a=ges_promo\">Gérer les promotions</a><br/>
                <a href=\"index.php?a=ges_nouv\">Gérer les nouveautés</a><br/>
                <a href=\"index.php?a=ges_dos\">Gérer les dossiers</a><br/>
            </p>";
        $menu_gauche = "";
        $menu_droit = "";
        $bas = "";
        break;
    case 'ges_abo':
        // ajout des feuilles de styles
        array_push($css, "produitForm.css");
        array_push($css, "personneForm.css");

        $_SESSION['auth']->verifie_droits("admin");

        if (!isset($_GET['form']) && !isset($_GET['suppr'])) {
            $pm = new PersonneManager();
            $contenu = $pm->afficher();
        } elseif (isset($_GET['form'])) {
            if ($_GET['form'] == 'new')
                $id = "";
            else
                $id = $_GET['form'];

            if (!isset($_POST['valid'])) {
                $personne = new Personne($id);
                $contenu = $personne->afficherFormulaire();
            } else {
                $personne = new Personne($id, $_POST);
                $r = $personne->enregistrer();
                if ($r == null)
                    $contenu = "<script>document.location = 'index.php?a=ges_abo'</script>";
                else
                    $contenu = $r;
            }
        }
        elseif (isset($_GET['suppr'])) {
            $personne = new Personne($_GET['suppr']);

            if (!isset($_GET['confirm']))
                $contenu = $personne->supprimerFormulaire();
            else {
                if ($_GET['confirm'] == 'y')
                    $personne->supprimer();
                $contenu = "<script>document.location = 'index.php?a=ges_abo'</script>";
            }
        }
        else
            $contenu = "Erreur d'adressage pour la gestion des personnes - Merci de contacter l'administrateur";

        include("fragments/mg.frg.php");
        break;

    case 'ges_pro':
        // ajout des feuilles de styles
        array_push($css, "produitForm.css");
        array_push($css, "mooRainbow.css");
        array_push($css, "colorPicker.css");

        // ajout des scripts js
        array_push($scripts, "mooRainbow.js");
        array_push($scripts, "couleurs.js");
        array_push($scripts, "tailles.js");

        // verification des droits pour accès
        $_SESSION['auth']->verifie_droits("admin");

        if (!isset($_GET['form']) && !isset($_GET['suppr'])) {
            $pm = new ProduitManager();
            $contenu = $pm->afficher();
        } elseif (isset($_GET['form'])) {
            if ($_GET['form'] == 'new')
                $id = "";
            else
                $id = $_GET['form'];

            if (!isset($_POST['valid'])) {
                $produit = new Produit($id);
                $contenu = $produit->afficherFormulaire();
            } else {
                $produit = new Produit($id, $_POST);
                $r = $produit->enregistrer();
                if ($r == null)
                    $contenu = "<script>document.location = 'index.php?a=ges_pro'</script>";
                else
                    $contenu = $r;
            }
        }
        elseif (isset($_GET['suppr'])) {
            $produit = new Produit($_GET['suppr']);

            if (!isset($_GET['confirm']))
                $contenu = $produit->supprimerFormulaire();
            else {
                if ($_GET['confirm'] == 'y')
                    $produit->supprimer();
                $contenu = "<script>document.location = 'index.php?a=ges_pro'</script>";
            }
        }
        else
            $contenu = "Erreur d'adressage pour la gestion des produits - Merci de contacter l'administrateur";

        include("fragments/mg.frg.php");
        break;

    case 'ges_com':
        array_push($css, "produitForm.css");
        array_push($css, "commandeForm.css");

        // verification des droits pour accès
        $_SESSION['auth']->verifie_droits("admin");

        $cm = new commandeManager();
        if (count($_GET) < 2) {
            $contenu = $cm->afficher();
        } elseif (isset($_GET['com'])) {
            array_push($css, "commande.css"); // pour mettre en forme proprement le tableau
            $contenu = "<h1>Détails de la commande n°{$_GET['com']}</h1>";
            $commande = $cm->getCommande($_GET['com']);
            $c = new Commande($commande);
            $contenu .= $c->afficheDetails(false);
        } elseif (isset($_GET['arch'])) {
            $resultat = $cm->archiver($_GET['arch']);
            if ($resultat)
                $contenu = "<p>La commande a été archivée avec succès.
                    Vous pouvez la voir en cliquant sur lien 'Voir les archives'
                    sur la page 'Gestion des commandes'</p>";
            else
                $contenu = "<p>La commande n'a pas pu être archivée.
                    Veuillez retenter plus tard ou appeler l'administrateur du
                    site</p>";
        }
        elseif (isset($_GET['p']) && $_GET['p'] == "arch") {
            $contenu = $cm->afficher(true);
        } elseif (isset($_GET['suppr'])) {
            if (!isset($_GET['confirm']))
                $contenu = $cm->supprimerFormulaire();
            elseif ($_GET['confirm'] == "y") {
                $resultat = $cm->supprimer($_GET['suppr']);
                if ($resultat)
                    $contenu = "<p>La commande archivée a été supprimée avec succès</p>";
                else
                    $contenu = "<p>La commande archivée n'a pu être supprimée. 
                        Veuillez retenter plus tard ou appeler l'administrateur du
                        site</p>";
            }
            elseif ($_GET['confirm'] == "n")
                $contenu = "<script>document.location = 'index.php?a=ges_com'</script>";
        }

        include("fragments/mg.frg.php");
        break;

    case 'ges_promo':
        array_push($css, 'promotionForm.css');
        array_push($css, "produitForm.css");

        // verification des droits pour accès
        $_SESSION['auth']->verifie_droits("admin");

        if (!isset($_POST['valid']) && !isset($_GET['suppr'])) {
            $promo = new Promotion();
            $contenu = $promo->afficherFormulaire();
        } elseif (isset($_POST['valid']) && !isset($_GET['suppr'])) {
            $promo = new Promotion($_POST['promotion']);
            $contenu = $promo->enregistrer();
        } elseif (isset($_GET['suppr'])) {
            $promo = new Promotion($_GET['suppr']);

            if (!isset($_GET['confirm']))
                $contenu = $promo->supprimerFormulaire();
            else {
                if ($_GET['confirm'] == 'y')
                    $promo->supprimer();
                $contenu = "<script>document.location = 'index.php?a=ges_promo'</script>";
            }
        }

        if (!isset($_GET['suppr'])) {
            $manager = new PromotionManager();
            $contenu .= $manager->afficher();
        }

        include("fragments/mg.frg.php");
        break;

    case 'ges_nouv':
        array_push($css, "produitForm.css");

        // verification des droits pour accès
        $_SESSION['auth']->verifie_droits("admin");

        if (!isset($_GET['form']) && !isset($_GET['suppr'])) {
            $nouveaute = new NouveauteManager();
            $contenu = $nouveaute->afficher();
        } 
        else if (!isset($_GET['form']) && isset($_GET['suppr'])) {
            $nouveaute = new NouveauteManager($_GET['suppr']);

            if (!isset($_GET['confirm']))
                $contenu = $nouveaute->supprimerFormulaire();
            else {
                if ($_GET['confirm'] == 'y')
                    $nouveaute->supprimer();
                $contenu = "<script>document.location = 'index.php?a=ges_nouv'</script>";
            }
        }
        else if(isset($_GET['form'])) {
            if ($_GET['form'] == 'new')
                $id = "";
            else
                $id = $_GET['form'];
            
            if (!isset($_POST['valid'])) {
                $nouveaute = new Nouveautes($id);
                $contenu = $nouveaute->afficherFormulaire();
            } 
            else {
                var_dump($_POST);
                $nouveaute = new Nouveautes($id, $_POST);
                $r = $nouveaute->enregistrer();
                if ($r == null)
                    $contenu = "<script>document.location = 'index.php?a=ges_nouv'</script>";
                else
                    $contenu = $r;
            }
        }

        include("fragments/mg.frg.php");
        break;
        
    case 'ges_dos':
        array_push($css, "produitForm.css");

        // verification des droits pour accès
        $_SESSION['auth']->verifie_droits("admin");

        if (!isset($_GET['form']) && !isset($_GET['suppr'])) {
            $dossier = new DossierManager();
            $contenu = $dossier->afficher();
        } 
        else if (!isset($_GET['form']) && isset($_GET['suppr'])) {
            $dossier = new DossierManager($_GET['suppr']);

            if (!isset($_GET['confirm']))
                $contenu = $dossier->supprimerFormulaire();
            else {
                if ($_GET['confirm'] == 'y')
                    $dossier->supprimer();
                $contenu = "<script>document.location = 'index.php?a=ges_dos'</script>";
            }
        }
        else if(isset($_GET['form'])) {
            if ($_GET['form'] == 'new')
                $id = "";
            else
                $id = $_GET['form'];
            
            if (!isset($_POST['valid'])) {
                $dossier = new Dossiers($id);
                $contenu = $dossier->afficherFormulaire();
            } 
            else {
                var_dump($_POST);
                $dossier = new Dossiers($id, $_POST);
                $r = $dossier->enregistrer();
                if ($r == null)
                    $contenu = "<script>document.location = 'index.php?a=ges_dos'</script>";
                else
                    $contenu = $r;
            }
        }

        include("fragments/mg.frg.php");
        break;

    case 'view_dos':
        array_push($css, "index.css");
        include("fragments/index.frg.php");
        break;

    case 'view_cat':
        array_push($css, "index.css");
        array_push($css, "catview.css");
        array_push($css, "produit.css");

        if(isset($_GET['cat']))
            $catProd = new catProd($_GET['cat'], "cat");
        elseif(isset($_GET['mq']))
            $catProd = new catProd($_GET['mq'], "mq");

        $contenu_plus = $catProd->afficherProduits();

        include("fragments/cat.frg.php");
        break;

    case 'prod':
        array_push($css, "index.css");
        array_push($css, "produit.css");

        array_push($scripts, "modalbox/modalbox.js");

        if (isset($_GET['idProd'])) {
            $idProd = $_GET['idProd'];
            $p = new AfficheProduit($idProd);
            $contenu = $p->afficher();
        }
        else
            $contenu = "Vous devez désigner un produit - contactez l'administrateur du site";

        include("fragments/produit.frg.php");
        break;

    case 'connexion':
        // TODO : CSS à revoir
        $contenu .= '<h1>Identification</h1>';
        $contenu .= $_SESSION['auth']->afficheFormulaire($action);
        break;

    case 'inscription':
        array_push($css, "index.css");
        array_push($css, "commande.css");
        array_push($css, "inscription.css");

        $contenu = "<h1>Création de votre compte</h1>";

        if (!isset($_POST['valid'])) {
            $personne = new Personne();
            $contenu .= $personne->afficherFormulaire();
        } else {
            // formulaire validé
            $personne = new Personne("", $_POST);
            $contenu .= $personne->enregistrer(false);

            // affichage d'une confirmations sous forme d'une boite de dialogue
            $contenu = "<script type=\"text/javascript\">";
            $contenu .= "window.addEvent('domready', function() {";
            $contenu .= "   var dialog = new SimpleDialog({ title: \"Compte créé\", text: \"Votre compte a bien été créé...\", redirect: \"index.php\" });";
            $contenu .= "});";
            $contenu .= "</script>";
        }

        include("fragments/default_mg.frg.php");
        break;
        
    // pages statiques
    case "presentation":
        array_push($css, "index.css");
        array_push($css, "presentation.css");
        include("fragments/presentation.frg.php");
        break;
    case "boutique":
        array_push($css, "index.css");
        array_push($css, "boutique.css");
        include("fragments/boutique.frg.php");
        break;
    case "commander":
        array_push($css, "index.css");
        array_push($css, "commander.css");
        include("fragments/commander.frg.php");
        break;
    case "cgv":
        array_push($css, "index.css");
        array_push($css, "cgv.css");
        include("fragments/cgv.frg.php");
        break;
    case "contact":
        array_push($css, "index.css");
        array_push($css, "contact.css");
        include("fragments/contact.frg.php");
        break;

    /*
     * Détail et validation de la commande
     */
    case 'commande':
        array_push($css, "index.css");
        array_push($css, "commande.css");
        array_push($css, "cgv.css");

        array_push($scripts, "commande/commande.js");

        include("php/commande/validation.php");

        include("fragments/commande.frg.php");
        break;

    default:
        array_push($css, "index.css");
        include("fragments/index.frg.php");
}

if (isset($_GET['a']) && $_GET['a'] == "print")
    include("squelettes/squelette_facture.php");
else
    include("squelettes/squelette.php");
?>
