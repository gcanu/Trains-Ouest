function CDE_delete(object) {
    var li = object.getParent("li").getNext();

    var input;
    for(var i=0; i<4; i++) {
        input = li.getElements("input");
        input[0].setProperty("value", "");
        li = li.getNext();
    }
}

(function (){
    window.addEvent('domready', function() {
        var comments = $$(".comments .title");
        var options = [];

        for(var i in comments) {
            if(i >= 2) {
                var comment = comments[i].getParent();
                
                // sauvegarde des hauteurs des éléments
                options.push({
                    id: comment.id,
                    height : comment.getStyle("height")
                });
                
                comment.addEvent("click", onClick);
                
                // repli par défaut
                hide(comment);
            }
        }
        
        var cgvInputs = $$("#cgv-acceptance input");        
        var validate = $("validation-button");
		if(validate) {
			validate.setStyle("display", "none");
        
			if(cgvInputs.length > 0) {
				var cgvInput = cgvInputs[0];
				cgvInput.addEvent("click", function() {
					if(cgvInput.checked)
						validate.setStyle("display", "block");
					else
						validate.setStyle("display", "none");                
				});
			}
		}
        
        function onClick(event) {
            var o = event.target.getParent();
            for(var i in options) {
                if(o.id == options[i].id) {
                    if(o.getStyle("height") == options[i].height)
                        hide(o);
                    else
                        show(o);
                    
                    break;
                }
            }
        }
        
        function hide(obj) {
            obj.setStyle("height", "1.5em");
            var span = obj.getElement('span');
            span.innerHTML = "Cliquez pour entrer une adresse";
            span.removeProperty("onClick");
            span.setStyles({
                "font-weight": "normal",
                "font-style": "italic"
            });
        }
        
        function show(obj) {
            obj.setStyle("height", options[i].height);
            var span = obj.getElement('span');
            span.innerHTML = "[Effacer le formulaire]";
            span.setProperty("onclick", "CDE_delete(this)")
            span.setStyles({
                "font-weight": "bold",
                "font-style": "normal"
            });
        }
    });
})();