var BgChanger = new Class({
     initialize: function() {
         console.log("OK");
         var select = $("bg_changer");
         var bg = $("bg");

         select.addEvent('change', function () {
             var option = this.options[this.selectedIndex];
             bg.setStyles({
                 'background-image': 'url(../train/images/fonds/fond_'+option.value+'.jpg)',
                 'width': '1280px',
                 'height': '1024px'
             });
         });
     }
});
