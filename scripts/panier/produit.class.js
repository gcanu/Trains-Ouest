/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var produit = new Class({
    Extends: Observer,

    id: 0,
    designation: "",
    PU: 0,
    quantite: 0,
    PHT: 0,    
    TVA: 0,
    PTVA : 0,
    PTTC: 0,

    initialize: function(id, designation, pu, quantite, tva) {
        this.id = id;
        this.designation = designation;
        this.PU = pu;
        this.quantite = quantite;
        this.TVA = tva;
        
        this.update();
    },

    update: function() {
        this.PHT = this.PU * this.quantite;
        this.PTVA = round(this.TVA * this.PHT, 2);
        this.PTTC = this.PHT /*+ this.PTVA*/;
    },

    getId: function() {
        return this.id;
    },

    getDesignation: function() {
        return this.designation;
    },

    getPU: function() {
        return this.PU;
    },

    getQuantite: function() {
        return this.quantite;
    },

    setQuantite: function(i) {
        this.quantite = i;
        this.update();
        this.fireChange(null);
    },

    getTVA: function() {
        return this.TVA;
    },

    getPTVA: function() {
        return this.PTVA;
    },

    getPTTC: function() {
        return this.PTTC;
    }
});
