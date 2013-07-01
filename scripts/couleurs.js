function initCouleur() {
    // on initialise les couleurs après le chargement du formulaire des produits
    var form_couleurs = $$('.couleurs');
    var td, div, inputValue, defaultColor;
    for(var x=0; x<form_couleurs.length; x++) {      
        if(x == 0)
            td = form_couleurs[x].getChildren('td')[1];
        else
            td = form_couleurs[x].getFirst();

        div = td.getFirst();
        inputValue = td.getChildren("input")[0].getProperty("value");

        if(inputValue == "")
            defaultColor = [255, 255, 255];
        else
            defaultColor = rgb2array(inputValue);

        div.setStyle("background-color", inputValue);

        this.picker = new MooRainbow(div, {
            'startColor': defaultColor,
            'onComplete': function(color) {
                this.element.setStyle('background-color', color.hex);
                this.element.getNext().setProperty("value", color.hex);
            }
        });
    }
}

function addCouleur() {
    var form_couleurs = $$('.couleurs');
    var form_couleur = form_couleurs[form_couleurs.length-2];

    // on clone la ligne...
    var clone = form_couleur.clone(true, false);

    // ... mais on réinitialise les champs
    var champs_clone = clone.getElements("input");
    for(var x=0; x<champs_clone.length; x++) {
        champs_clone[x].setProperty("value", "");
        
        // ... et surtout on renomme l'attribut name du champ file
        if(champs_clone[x].getProperty("type") == "file")
            champs_clone[x].setProperty("name", "imageCouleur"+(form_couleurs.length-1));
    }

    // on vire l'image copiée (la première uniquement)
    var img = clone.getElements("img");
    if(img.length > 1)
        img[0].dispose();

    // on l'ajoute au code HTML
    clone.inject(form_couleur, "after");

    // on met à jour le rowspan
    var firstTd = form_couleurs[0].getFirst();
    var nbLine = $$('.couleurs').length;
    firstTd.setProperty("rowspan", nbLine-1);
    
    // si on a dupliqué la première ligne, on vire le premier td (à cause du rowspan)
    if(nbLine == 3)
        clone.getFirst().dispose();

    // on rend transparent la div
    var div = clone.getFirst().getFirst();
    div.setStyle("background-color", "transparent");

    // et on attache un objet MooRainbow
    this.picker = new MooRainbow(div, {
        'startColor': [255, 255, 255],
        'onComplete': function(color) {
            this.element.setStyle('background-color', color.hex);
            this.element.getNext().setProperty("value", color.hex);
        }
    });
}

function removeCouleur(object) {

    // on garde le premier td (celui qui contient le rowspan)
    var firstTd = $$('.couleurs')[0].getFirst();

    // on compte le nombre de ligne (utile pour mettre à jour le rowspan)
    var nbLine = $$('.couleurs').length;

    // on prend la ligne concernée par le clic
    var ligne = object.getParent("tr");

    // on fait attention à ce qu'il ne s'agisse pas de la seule ligne de couleur
    // sinon on ne la supprime pas mais on la réinitialise
    if($$('.couleurs').length == 2) {
        $$('.CCcolor')[0].setStyle('background-color', 'transparent');
        $$('.refCouleur')[0].removeProperty('value');
    }
    else {
        // on l'efface en faisant attention de ne pas virer le rowspan du premier élément <td>
        var td = ligne.getFirst();
        var rowspan = td.getProperty("rowspan");
        ligne.dispose();

        if(rowspan == 1)
            firstTd.setProperty("rowspan", nbLine-2);
        else {
            var newFirstTd = $$('.couleurs')[0].getFirst();
            firstTd.inject(newFirstTd, "before");
            firstTd.setProperty("rowspan", nbLine-2);
        }
    }
}

function rgb2array(rgb) {
    var red = parseInt("0x"+rgb.toString().substr(1, 2));
    var green = parseInt("0x"+rgb.toString().substr(3, 2));
    var blue = parseInt("0x"+rgb.toString().substr(5, 2));
    return new Array(red, green, blue);
}