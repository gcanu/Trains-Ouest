var Observer = new Class({
    registered: [],

    initialize: function() {},

    addChangeListener: function(object) {
        this.registered.push(object);
    },

    removeChangeListener: function(object) {
        this.registered = this.registered.filter(
            function(element){
                if(object !== element)
                    return element;
            }
        );
    },

    /* ----------------------------------------------------------------------
     * FireChange prend obligatoirement en paramètre un objet de type
     * CalendarEvent défini par le fichier CalendarEvent.class.js, forcément,
     * la méthode onStateChanged aussi
     * ----------------------------------------------------------------------*/
    fireChange: function(ev) {
        this.registered.forEach(
            function(element) {
                element.onStateChanged(ev);
            }
        );
    }
});