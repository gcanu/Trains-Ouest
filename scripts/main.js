window.addEvent('domready', function() {
    
    // charge le systeme de choix de couleur seulement si on se trouve sur la
    // page des gestion des produits
    var re = /(a=ges_pro&)/g;
    if(re.test(document.location))
        initCouleur();
});

function round (number, precision) {
    var result = Math.round(number * Math.pow(10, precision));
    return result/Math.pow(10, precision);
}
