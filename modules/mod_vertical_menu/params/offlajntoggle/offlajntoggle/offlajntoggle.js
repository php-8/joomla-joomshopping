dojo.declare("OfflajnToggle", null, {

	constructor: function(args) {
	 dojo.mixin(this,args);
	 this.init();
  },

  init: function() {
    this.input = dojo.byId(this.id);
    this.toggle = dojo.byId('offlajntoggle'+this.id);
    dojo.connect(this.input, 'onchange', this, 'update');
    dojo.connect(this.toggle, 'onclick', this, 'toggleInput');

    this.img = new Image();
    this.img.onload = dojo.hitch(this, 'onLoadImg');
    this.img.src = this.src;
  },

  onLoadImg: function() {
    this.toggle.style.width = this.img.width +'px';
    this.toggle.style.height = this.img.height / 2 +'px';
    this.toggle.style.backgroundImage = 'url("'+ this.src +'")';
    this.update();
  },

  update: function() {
    dojo[+this.input.value ? 'addClass' : 'removeClass'](this.toggle, 'ot-active');
  },

  toggleInput: function() {
    this.input.value = this.input.value > 0 ? 0 : 1;
    OfflajnFireEvent(this.input, 'change');
    this.update();
  }

});
