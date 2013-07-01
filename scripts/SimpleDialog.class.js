/**
 * SIMPLE DIALOG BOX
 * 
 * Author : Guillaume Canu
 * 
 * Design for MooTools 1.2.4, do not forget MooTools-more for Fx ans other 
 * functions. Not tested on earlier versions, use at your own risks...
 */

var SimpleDialog = new Class({
    options: null,
    
    body: null,
    
    background: null,
    widget: {
        element: null,
        components: []
    },
    
    initialize: function(options) {
        this.options = options;
        
        // body initialize
        this.body = $$("body")[0];
        
        // construct the dialog Box
        this.createBox();
        
        // compute the ideal dimensions for window
        this.pack();
        
        // Appear with effect
        var fx = new Fx.Tween(this.widget.element, {
            duration: 'short'
        });
        fx.start('opacity', 0, 1);
    },

    createBox: function() {
        // create semi opaque background
        this.background = new Element('div', {
            'class': 'bg',
            styles: {
                height: this.body.scrollHeight+'px'
            }
        });
        
        this.background.inject(this.body);
        
        /*
         * ajout des composants du widget
         */ 
        
        var window = new Element('div', {
            'class': 'window'
        });
        
        window.inject(this.body);
        this.widget.element = window;
        
        var header = new Element('div', {
            'class': 'hd',
            html: this.options.title,
            events: {
                click: this.closeWindow.bind(this)
            }
        });
        
        header.inject(window);
        this.widget.components.push(header);
        
        var content = new Element('div', {
            'class': 'content',
            html: this.options.text
        });
        
        content.inject(window);
        this.widget.components.push(content);
        
        var actions = new Element('div', {
            'class': 'actions'
        });
        
        actions.inject(window);
        this.widget.components.push(actions);
        
        var okButton = new Element('button', {
            text: "Ok",
            events: {
                click: this.closeWindow.bind(this)
            }
        });
        
        okButton.inject(actions);
    },
    
    closeWindow: function() {
        this.background.destroy();
        this.widget.element.destroy();
        
        if(this.options.redirect)
            document.location.href = this.options.redirect;
    },
    
    pack: function() {
        var size = this.widget.element.getSize();
        var bodySize = this.body.getSize();
        
        // calculate components widths
        var height = 0;
        
        for(var i=0, ii=this.widget.components.length; i<ii; i++) {
            var component = this.widget.components[i];
            component.setStyle('width', (size.x - this.getPadding(component, "left") - this.getPadding(component, "right")) + 'px');
            height += component.getSize().y;
        }
        
        // calculate window position & dimensions
        this.widget.element.setStyles({
            top: this.body.scrollTop + (bodySize.y - height)/2,
            left: this.body.scrollLeft + (bodySize.x - size.x)/2,
            width: size.x+'px',
            height: height+'px'
        });
    },
    
    getPadding: function(element, type) {
        if(!type || !element)
            return null;
        
        var value = null;
        switch(type) {
            case "top":
                value = element.getStyle("padding-top");
                break;
            case "left":
                value = element.getStyle("padding-left");
                break;
            case "right":
                value = element.getStyle("padding-right");
                break;
            case "bottom":
                value = element.getStyle("padding-bottom");
                break;
        }
        
        if(value) {
            var m = value.match("^([0-9]+)px$")
            if(m && m.length > 1)
                return parseInt(m[1], 10);
        }
        
        return value;
    }
});