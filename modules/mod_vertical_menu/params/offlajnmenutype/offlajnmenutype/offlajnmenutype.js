
dojo.declare("TypeConfigurator", null, {
	constructor: function(args) {
	  dojo.mixin(this,args);
	  //var pane = dojo.place(this.typeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'first');
    var pane = null;
    if(!this.joomfish){
     // pane = dojo.place(this.typeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'first');
    }else{
      var hides = dojo.query('.translateparams td .toolbar');
      dojo.forEach(hides,function(el){dojo.style(el, 'display', 'none')});
      var el = null;
      if(this.control == 'orig_params'){
        el = dojo.byId('original_value_params');
      }else if(this.control == 'defaultvalue_params'){
        el = dojo.byId('original_value_params');
      }else if(this.control == 'refField_params'){
        el = dojo.query('.translateparams .translateparams');
        el = el[0];
      }
      pane = dojo.place(this.typeSelector, el, 'first');
      if(this.control == 'defaultvalue_params'){
        dojo.style(pane, 'display', 'none');
      }
    }
    this.tpc = dojo.byId('typeparamcontainer');
    this.typeDetails = dojo.byId(this.control+'-details');
    this.title = dojo.byId(this.control+'-title');

    this.selectType = dojo.byId(this.selectorId);

    dojo.connect(this.selectType, 'onchange', this, 'changeType');
    this.changeType();
  },

  changeType: function(e){
    this.type = this.selectType.value;
    if(this.type == '' || this.type == 'joomla') this.type = 'joomla';

    if(this.typeParams[this.type].length == 32){
      dojo.addClass(this.title, 'offlajnloading');
      this.typeDetails.innerHTML = '';
      (function $ajax(i) {
        jQuery.ajax({
          url: location.href,
          method: "POST",
          data: {
            'offlajnformrenderer': '1',
            'control': this.control,
            'key': this.typeParams[this.type]
          },
          success: dojo.hitch(this, function(data){
            dojo.removeClass(this.title, 'offlajnloading');
            this.typeDetails.innerHTML = data;
            window.head = document.getElementsByTagName('head')[0];
            dojo.query('link',this.typeDetails).forEach(function(el){
              dojo.place(el, head);
            });
            dojo.query('script',this.typeDetails).forEach(function(el){
              var src = dojo.attr(el, 'src');
              if (src) {
                var fileref=document.createElement('script');
                fileref.setAttribute("type", "text/javascript");
                fileref.setAttribute("src", src);
                dojo.place(fileref, head);
              }
            });
            dojo.global.toolTips.connectToolTips(this.typeDetails);
            if (window.init_conditions) init_conditions();
          }),
          error: dojo.hitch(this, function() {
            i && setTimeout($ajax.bind(this, --i), 200);
          })
        });
      }).call(this, 3);
    }else{
      this.typeDetails.innerHTML = this.typeParams[this.type];
    }
  }
});