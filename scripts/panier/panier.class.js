var panier = new Class({
    produits: [],
    element: null,

    initialize: function(element) {
        this.element = element;
        this.getCookie();
        this.simpleDisplay();
        this.displayTotal();
    },

    addProduitById: function(id, qty) {
        var request = new Request({
            url: 'scripts/panier/ajax_request.php',
            method: 'get',
            data: "type=1&id="+id,
            async: false,
            onSuccess: function(responseText, responseXML) {
                var infos = responseText.split(";");
                this.addProduit(new produit(infos[0], infos[1], infos[2], qty, 0.196));                
            }.bind(this)
        });
        request.send();        
    },

    addProduit: function(produit) {
        produit.addChangeListener(this);

        this.produits.push(produit);
        this.setCookie();
        this.simpleDisplay();
        this.displayTotal();
    },

    removeProduit: function(produit) {
        produit.removeChangeListener(this);

        this.produits = this.produits.filter(
            function(element) {
                if(produit !== element)
                    return element;
                return null;
            }
            );

        this.setCookie();
        this.simpleDisplay();
        this.displayTotal();        
    },

    getProduit: function(n) {
        return this.produits[n];
    },

    getNbProduit: function() {
        return this.produits.length;
    },

    getLast: function() {
        return this.produits[this.produits.length-1];
    },

    getTotalHT: function() {
        var totalHT = 0;
        for(var x=0; x<this.produits.length; x++)
            totalHT += this.produits[x].getPHT();

        return totalHT;
    },

    getTotalTVA: function() {
        var totalTVA = 0;
        for(var x=0; x<this.produits.length; x++)
            totalTVA += this.produits[x].getTVA();
        
        return totalTVA;
    },

    getTotalTTC: function() {
        var totalTTC = 0;
        for(var x=0; x<this.produits.length; x++)
            totalTTC += this.produits[x].getPTTC();
        
        // le round n'a pour objectif que d'éviter un bug d'affichage
        // l'addition des nombres retourne parfois par exemple 4,0000000002
        return round(totalTTC, 2);
    },

    simpleDisplay: function() {
        var html = "";

        this.element.empty();
        
        if(this.produits.length == 0) {            
            this.displayValidationCommande(false);
            this.element.set('html', "<span>aucun article</span>");
        }
        else {
            this.displayValidationCommande(true);

            for(var x=0; x<this.produits.length; x++) {
                
                // on ajoute pour chaque élément une petite croix qui servira
                // à supprimer le produit
                var close_button = new Element('img', {
                    'id': 'p_remove_'+x,
                    'class': 'p_remove',
                    'src': 'images/fermer_petit.png'
                });
                var produit = this.produits[x];
                close_button.addEvent('click', function(ev) {
                    this.removeProduit(this.getProduit(ev.target.id.substr(9,1)));
                }.bind(this));
                close_button.inject(this.element);

                var span_des = new Element('span', {
                    'class': 'p_designation'                 
                });
                
                var designation = this.produits[x].getDesignation();
                var longueur_max = 19;
                if(designation.length > longueur_max)
                    designation = designation.substr(0,longueur_max)+"...";
                span_des.set('text', designation);
                span_des.inject(this.element);
                
                var span_qte = new Element('span', {
                    'class': 'p_qte'
                });
                span_qte.set('text', this.produits[x].getQuantite());
                span_qte.inject(this.element);

                var span_pttc = new Element('span', {
                    'class': 'p_pttc'
                });
                span_pttc.set('text', round(this.produits[x].getPTTC(), 2)+" \u20ac");
                span_pttc.inject(this.element);

                (new Element('br')).inject(this.element);
            }
        }
    },

    displayValidationCommande: function(display) {
        if(display) {
            $('panier').setStyle('background-image', 'url(images/fond_panier2.png)');
            if(!$('validerCommande')) {
                // on crée la div validerCommande si elle n'existe pas
                var div = new Element('div', {
                    id: 'validerCommande'
                });
                div.inject($('panier'));
                div.setStyles({
                    'background-image': 'url("images/valider_commande.png")',
                    height: '15px',
                    left: '155px',
                    position: 'absolute',
                    top: '29px',
                    width: '141px'
                });
                div.addEvent('mouseover', function() {
                    div.setStyle('background-image', 'url("images/valider_commande_over.png")');
                });
                div.addEvent('mouseout', function() {
                    div.setStyle('background-image', 'url("images/valider_commande.png")');
                });
                div.addEvent('click', function() {
                    document.location = 'index.php?a=commande';
                });
            }
            else {
                $('validerCommande').setStyle('display', 'block');
            }
        }
        else {
            $('panier').setStyle('background-image', 'url(images/fond_panier.png)');
            if($('validerCommande'))
                $('validerCommande').setStyle('display', 'none');
        }
    },

    display: function() {},

    displayTotal: function() {
        var total = this.getTotalTTC();
        var text_total = "" + total;

        if(total == Math.floor(total))
            text_total += ",00";
        else
            text_total = text_total.replace(/\./, ",");
        $('p_total').set('html', text_total+" &euro;");
    },

    displayQtyChooser: function(id) {
        // on opacifie le fond de l'écran
        var body = $$('body')[0];

        var bgDiv = new Element("div", {
            id: 'QTY_bg',
            html: ''
        });

        bgDiv.setStyles({
            height: body.getScrollSize().y,
            width: body.getScrollSize().x
        });

        bgDiv.inject(body);

        // on ajoute la boîte de dialogue
        var dialog = new Element("div", {
            id: 'QTY_dialog_bg',
            html: ''
        });
        dialog.inject(body);

        dialog.setStyles({
            top: Math.floor((body.getSize().y-dialog.getSize().y)/2),
            left: Math.floor((body.getSize().x-dialog.getSize().x)/2)
        });

        dialog.set('html', ''+
            '<p class=\"QTY_help\">Spécifiez la quantité</p>'+
            '<div><span id=\"QTY_qte\">quantité </span></div>');

        var input = new Element('input', {
            id: 'QTY_input',
            type: 'text',
            value: ''
        });
        input.inject($('QTY_qte'), "after");
        input.addEvent('keyup', this.isSubmitable.bind(this));

        // on ajoute les boutons
        var valider = new Element('input', {
            type: 'button',
            id: 'QTY_validate',
            'class': 'QTY_desactive'
        });
        valider.inject(dialog);

        var annuler = new Element('input', {
            type: 'button',
            id: 'QTY_cancel'
        });
        annuler.inject(dialog);
        annuler.addEvent('click', this.cancel);

        var hidden = new Element('input', {
            type: 'hidden',
            id: 'QTY_hidden',
            value: id
        });
        hidden.inject(dialog);
    },

    cancel: function(event) {
        $('QTY_bg').dispose();
        $('QTY_dialog_bg').dispose();
    },

    colorSelect: function(event) {
        var divColor = event.target;
        
        var colors = $$('#QTY_palette > div');
        for(var x=0; x<colors.length; x++) {
            if(colors.length > 1) {
				if(colors[x] == divColor)
					divColor.toggleClass('QTY_color_selected');
				else
					colors[x].set('class', 'QTY_color');
			}
        }

        this.isSubmitable(event);
    },

    isSubmitable: function(event) {
        var inputValue = $('QTY_input').getProperty('value');
        var bouton = $('QTY_validate');

        if(inputValue.test("[0-9]+")) {
            bouton.setProperty('class', 'QTY_active');
            bouton.addEvent('click', this.validateQtyForm.bind(this));
        }
        else {
            $('QTY_validate').setProperty('class', 'QTY_desactive');
            bouton.removeEvents();
        }
    },

    validateQtyForm: function(event) {
        event.stopPropagation();
		event.preventDefault();
		
		 // pour prévenir le bug d'IE7 et 8 qui ne connait pas le preventDefault, 
		 // on met un if($('QTY_hidden')) car le deuxième appel ne reconnait pas QTY_hidden,
		 // la fenêtre ayant disparu (this.cancel).
		if($('QTY_hidden')) {
			var id = $('QTY_hidden').getProperty('value');
			var qty = $('QTY_input').getProperty('value');

			this.cancel();

			this.addProduitById(id, qty);
		}
    },

    setCookie: function() {
        var datas = "";

        for(var x=0; x<this.produits.length; x++) {
            if(datas != "")
                datas += "~";
            datas += this.produits[x].getId()+";"+
            this.produits[x].getQuantite();
        }        

        var request = new Request({
            url: 'scripts/panier/cookie.php',
            method: 'post',
            data: "action=set&data="+encodeURI(datas),
            async: false,
            onSuccess: function(responseText, responseXML) {
            }
        });
        request.send();
    },

    getCookie: function() {
        var request = new Request({
            url: 'scripts/panier/cookie.php',
            method: 'post',
            data: "action=get",
            async: false,
            onSuccess: function(responseText, responseXML) {
                var result = decodeURI(responseText);

                if(result != false) {
                    var infos = result.split("~");
                    var commande;
                    for(var x=0; x<infos.length; x++) {
                        commande = infos[x].split(";");
                        this.addProduitById(commande[0], commande[1]);
                    }
                }
            }.bind(this)
        });
        request.send();
    },

    resetCookie: function() {
        var request = new Request({
            url: 'scripts/panier/cookie.php',
            method: 'post',
            data: "action=reset",
            async: false,
            onSuccess: function(responseText, responseXML) {
            }
        });
        request.send();
    },
    
    resetPanier: function() {
        // remove the cookie
        var request = new Request({
            url: 'scripts/panier/cookie.php',
            method: 'post',
            data: "action=remove",
            async: false,
            onSuccess: function(responseText, responseXML) {
            }
        });
        request.send();
        
        // remove all products
        for(var i=0, ii=this.produits.length; i<ii; i++) {
            this.removeProduit(this.getProduit(0));
        }
    },

    onStateChanged: function(ev) {
        this.simpleDisplay();
        this.displayTotal();
    }
});
