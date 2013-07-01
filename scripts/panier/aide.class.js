var AidePanier = new Class({
    help: null,
    helpContent: null,
    helpContentClose: null,
    
    initialize: function() {
        this.help = $('panier-aide');
        this.helpContent = $('panier-aide-content');
        this.helpContentClose = $$('#panier-aide-content .close')[0];
        
        this.helpContent.setStyle('display', 'none');
        
        this.help.addEvent('click', this.onClickHelp.bind(this));
        this.helpContentClose.addEvent('click', this.onClose.bind(this));
        
    },
    
    onClickHelp: function() {
        this.helpContent.setStyle('display', 'block');
    },
    
    onClose: function() {
        this.helpContent.setStyle('display', 'none');
    }
});