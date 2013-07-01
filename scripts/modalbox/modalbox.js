/* ----------------------------------------------------------------
 * Cette fonction est chargée d'ouvrir l'image dans un boîte modale
 *
 * @param string chemin de l'image à afficher dans la boîte modale
 * @return null pas de retour
 * ----------------------------------------------------------------*/
function modalbox(imagePath) {
    // on crée la div d'opacification
    var body = $$('body')[0];
    var screenHeight = body.getScrollSize().y;
    var screenWidth = body.getScrollSize().x;
    var visibleHeight = body.getSize().y;
    var visibleWidth = body.getSize().x;

    var bgDiv = new Element("div", {
        id: 'MBOX_bg',
        html: ''
    });

    bgDiv.setStyles({
        position: 'absolute',
        top: '0px',
        left: '0px',
        height: screenHeight,
        width: screenWidth,
        opacity: .75,
        'z-index': 1,
        'background-color': 'black'
    });
	
    bgDiv.inject(body);
	
	var mbx_div = new Element("div", {
		id: 'MBOX'
	});
   
    // on crée la div qui va contenir l'image
    var img = new Element("img" , {
        id: 'MBOX_img',
        src: imagePath
    });

    // styles par défaut avant que l'image ne soit chargée
    var divAttente = new Element('div', {
        html: 'Chargement de l\'image...'
    });
    divAttente.inject(mbx_div);
    divAttente.setStyles({
        position: 'absolute',
        width: '180px',
        height: '20px',
        'background-color': 'white',
        color: 'black',
        top: (body.getSize().y-20)/2,
        left: (body.getSize().x-180)/2,
        'z-index': 1,
        'font-family': 'verdana',
        'font-size': '14px',
        'text-align': 'center',
        padding: '10px'
    });

    img.addEvent('load', function() {
        divAttente.dispose();

        var ratio = .9;
        var controlHeight = 50;
        var imgOriginalHeight = img.getSize().y;
        var imgOriginalWidth = img.getSize().x;
        var imgRatio = imgOriginalWidth/imgOriginalHeight
        var heightOverflow = (imgOriginalHeight+controlHeight)/visibleHeight;
        var widthOverflow = imgOriginalWidth/visibleWidth;
        var imgHeight;
        var imgWidth;

        if(heightOverflow > widthOverflow) {
            imgHeight = visibleHeight*ratio-controlHeight;
            imgWidth = imgHeight*imgRatio;
        }
        else {
            imgWidth = visibleWidth*ratio;
            imgHeight = imgWidth/ratio;
        }

        var marginWidth = Math.floor((visibleWidth-imgWidth)/2);
        var marginHeight = Math.floor((visibleHeight-imgHeight)/2);

        img.setStyles({
            position: 'absolute',
            top: marginHeight+'px',
            left: marginWidth+'px',
            height: Math.floor(imgHeight)+'px',
            width: Math.floor(imgWidth)+'px',
            border: '1px solid white',
            'z-index': 1
        });

        controlDiv.setStyles({
            position: 'absolute',
            top: Math.floor(marginHeight+imgHeight)+'px',
            left: '0px',
            height: controlHeight,
            width: visibleWidth+'px',
            color: 'white',
            'font-family': 'verdana',
            'font-size': '14px',
            'text-align': 'center',
            'z-index': 1
        });
    });

	mbx_div.setStyles({
		position: 'fixed',
		top: 0,
		'z-index': 1
	});
    img.inject(mbx_div);    

    // controle de l'image
    var controlDiv = new Element("div", {
        id: 'MBOX_control',
        html: '<p onClick=\"closeModalBox()\" style=\"cursor: pointer\">fermer</p>'
    });    

    controlDiv.inject(mbx_div);
	mbx_div.inject(body);
}

function closeModalBox() {
    $('MBOX_bg').dispose();
    $('MBOX').dispose();
}
