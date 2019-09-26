
        (function(){
var dojo = odojo;

var dijit = odijit;

var dojox = odojox;

dojo.declare("OfflajnMultiSelectList", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.lineHeight = 20;
    this.init();
  },

  init: function() {
    this.multiselect = dojo.byId('offlajnmultiselect' + this.name);
    if(this.joomla && !this.name.match(/zoo/)) {
      this.type = this.typeSelectorInit();
    } else if(this.name.match(/zoo/)) {
      this.type = this.ZooSelectorInit();
    }
    this.itemscontainer = dojo.create('div', {'class': 'multiselectitems', 'innerHTML': this.data[this.type]}, this.multiselect);
    if(dojo.isIE == 7) dojo.style(this.multiselect, 'width', dojo.position(this.itemscontainer).w + 10 + 'px'); //IE7 width bug
    this.getList();
    this.hidden = dojo.byId(this.name);
    this.hidden.options = this.options;
    this.hidden.listobj = this;

    this.setSize();
    dojo.connect(this.itemscontainer, 'onclick', this, 'selectItem');
    this.setSelected();
    if(this.height) {
      this.scrollbar = new OfflajnScroller({
        'extraClass': 'multi-select',
        'selectbox': this.multiselect,
        'content': this.itemscontainer,
        'scrollspeed' : 30
      });
      if(this.list.length <= this.height - 2) this.scrollbar.hideScrollBtn();
    }
  },

  getList: function() {
    this.list = dojo.query('.multiselectitem', this.itemscontainer);
    if (!this.list.length) {
      this.itemscontainer.innerHTML = '<div class="gk_hack multiselectitem">All items</div>';
      this.list = dojo.query('.multiselectitem', this.itemscontainer);
    }
    this.list.forEach(function(el, i){
      if(this.type != 'simple') {
        el.i = 0;
        if(i) {
          el.i = this.ids[this.type][i-1];
        }
      } else {
          el.i = this.ids[this.type][i];
      }
    },this);
  },

  setSize: function() {
    dojo.style(this.multiselect, {
	   'height': this.height * this.lineHeight +  'px'
	});
  },

  setSelected: function() {
    var arr = this.hidden.value.split('|');
    if ((arr.length < this.list.length-1 && arr.length != 0) || this.type == 'simple') {
      dojo.forEach(arr, function(item, i){
        this.list.forEach(function(el, j){
          if (el.i == item){
            dojo.addClass(this.list[j], 'selected');
            if(this.mode == 2)
              this.hidden.selectedIndex = el.i;
          }
        }, this);
      }, this);
    } else {
        dojo.addClass(this.list[0], 'selected');
    }
  },

  selectItem: function(e) {
    if(this.mode == 1 && e.target.i == 0){
      this.allItemSelection();
    }else{
      if(this.mode == 1 && dojo.hasClass(this.list[0], 'selected')) {
        this.list.forEach(function(el, i){
          dojo.removeClass(el, 'selected');
        },this);
        if(dojo.hasClass(e.target, 'selected')) {
          dojo.removeClass(e.target, 'selected');
        } else {
          dojo.addClass(e.target, 'selected');
        }
      }else if(this.mode == 2){
        this.list.forEach(function(el, i){
          dojo.removeClass(el, 'selected');
        },this);
        dojo.addClass(e.target, 'selected');
        this.hidden.selectedIndex = e.target.i;
      }else{
        if(dojo.hasClass(e.target, 'selected')) {
          dojo.removeClass(e.target, 'selected');
        } else {
          dojo.addClass(e.target, 'selected');
        }
      }
    }
    this.getValues(0);
  },

  allItemSelection: function() {
    this.list.forEach(function(el, i){
      dojo.removeClass(el, 'selected');
    },this);

    dojo.addClass(this.list[0], 'selected');
  },

  getValues: function(mode) {
    var val = 0;
    this.list.forEach(function(el, i){
      if (dojo.hasClass(el, 'selected') || mode == 1) {
        (val) ? val += '|' + el.i : val = el.i;
      }
    },this);
    if(val != this.hidden.value){
      this.hidden.value = val;
      //OfflajnFireEvent(this.hidden, 'change');
    }
     OfflajnFireEvent(this.hidden, 'change');
  },

  /*
  *Menutypeselector
  */

  typeSelectorInit: function() {
    var ts = this.name.replace('joomlamenutype', 'joomlamenu');
    this.typeselector = dojo.byId(ts);
    dojo.connect(this.typeselector, 'onchange', this, 'changeMenuItems');
    return this.typeselector.value;
  },

  changeMenuItems: function() {
    this.type = this.typeselector.value;
    this.itemscontainer.innerHTML = this.data[this.type];
    this.setSize();
    this.getList();
    this.hidden.value = '';
    this.scrollbar.scrollReInit();
    if(this.list.length <= this.height - 2) this.scrollbar.hideScrollBtn();
  },


  /*
  *Zoo Type Selector
  */

  ZooSelectorInit: function() {
    //var ts = this.name.replace('joomlamenutype', 'joomlamenu');
    var ts = "jformparamsmoduleparametersTabmenutypezooapps";
    this.typeselector = dojo.byId(ts);
    dojo.connect(this.typeselector, 'onchange', this, 'changeZooCategories');
    return this.typeselector.value;
  },

  changeZooCategories: function() {
    this.type = this.typeselector.value;
    this.itemscontainer.innerHTML = this.data[this.type];
    this.setSize();
    this.getList();
    this.hidden.value = '';
    this.scrollbar.scrollReInit();
    if(this.list.length <= this.height - 2) this.scrollbar.hideScrollBtn();
  },


});



dojo.declare("OfflajnOnOff", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
   this.w = 26;
	 this.init();
  },


  init: function() {
    this.switcher = dojo.byId('offlajnonoff' + this.id);
    this.input = dojo.byId(this.id);
    this.state = parseInt(this.input.value);
    this.click = dojo.connect(this.switcher, 'onclick', this, 'controller');
    if(this.mode == 'button') {
      this.img = dojo.query('.onoffbutton_img', this.switcher);
      if(dojo.hasClass(this.switcher, 'selected')) dojo.style(this.img[0], 'backgroundPosition', '0px -11px');
    } else {
      dojo.connect(this.switcher, 'onmousedown', this, 'mousedown');
    }
    dojo.connect(this.input, 'onchange', this, 'setValue');
  },

  controller: function() {
    if(!this.mode) {
      if(this.anim) this.anim.stop();
      this.state ? this.setOff() : this.setOn();
    } else if(this.mode == "button") {
      this.state ? this.setBtnOff() : this.setBtnOn();
    }
  },

  setBtnOn: function() {
    dojo.style(this.img[0], 'backgroundPosition', '0px -11px');
    dojo.addClass(this.switcher, 'selected');
    this.changeState(1);
  },

  setBtnOff: function() {
    dojo.style(this.img[0], 'backgroundPosition', '0px 0px');
    dojo.removeClass(this.switcher, 'selected');
    this.changeState(0);
  },

  setValue: function() {
    if(this.state != this.input.value) {
      this.controller();
    }
  },

  changeState: function(state){
    if(this.state != state){
      this.state = state;
      this.stateChanged();
    }
  },

  stateChanged: function(){
    this.input.value = this.state;
    OfflajnFireEvent(this.input, 'change');
  },

  mousedown: function(e){
    this.startState = this.state;
    this.move = dojo.connect(document, 'onmousemove', this, 'mousemove');
    this.up = dojo.connect(document, 'onmouseup', this, 'mouseup');
    this.startX = e.clientX;
  },

  mousemove: function(e){
    var x = e.clientX-this.startX;
    if(!this.startState) x-=this.w;
    if(x > 0){
      x = 0;
      this.changeState(1);
    }
    if(x < -1*this.w){
      x = -1*this.w;
      this.changeState(0);
    }
		var str = x+"px 0px";
    dojo.style(this.switcher,"backgroundPosition",str);
  },

  mouseup: function(e){
    dojo.disconnect(this.move);
    dojo.disconnect(this.up);
  },

  getBgpos: function() {
    var pos = dojo.style(this.switcher, 'backgroundPosition');
    if(dojo.isIE <= 8){
      pos = dojo.style(this.switcher, 'backgroundPositionX')+' '+dojo.style(this.switcher, 'backgroundPositionY');
    }
    var bgp = pos.split(' ');
    bgp[0] = parseInt(bgp[0]);
    return !bgp[0] ? 0 : bgp[0];
  },

  setOn: function() {
    this.changeState(1);

    this.anim = new dojo.Animation({
      curve: new dojo._Line(this.getBgpos(),0),
      node: this.switcher,
      onAnimate: function(){
				var str = Math.floor(arguments[0])+"px 0px";
				dojo.style(this.node,"backgroundPosition",str);
			}
    }).play();
  },


  setOff: function() {
    this.changeState(0);

    this.anim = new dojo.Animation({
      curve: new dojo._Line(this.getBgpos(), -1*this.w),
      node: this.switcher,
      onAnimate: function(){
				var str = Math.floor(arguments[0])+"px 0px";
				dojo.style(this.node,"backgroundPosition",str);
			}
    }).play();
  }

});



dojo.declare("OfflajnList", null, {
	constructor: function(args) {
    this.fireshow = 0;
    this.map = {};
    this.names = new Array();
    this.list = new Array;
	  dojo.mixin(this,args);
    this.showed = 0;
    this.focus = 0;
    this.zindex = 6;
    window.offlajnlistzindex = 10;
    if(this.height) this.height++;
    this.lineHeight = 20;
    this.init();
  },

  init: function() {
    this.hidden = dojo.byId(this.name);
    this.active = this.hidden;

    this.hidden.listobj = this;
    this.hidden.options = this.options;
    this.hidden.selectedIndex = this.selectedIndex;

    dojo.connect(this.hidden, 'onchange', this, 'setValue');
    this.change = 0;

    this.container = dojo.byId('offlajnlistcontainer' + this.name);
    this.offlajnlist = dojo.query('.offlajnlist', this.container)[0];
    this.currentText = dojo.query('.offlajnlistcurrent', this.container)[0];

    if (this.json && window[this.json] && window[this.json].length) {
      this.hidden.options = this.options = this.options.concat(window[this.json]);
      this.hidden.selectedIndex = this.selectedIndex = 0;
      this.currentText.innerHTML = this.options[0].text;
      for (var i = 0; i < this.options.length; i++)
        if (this.options[i].value == this.hidden.value) {
          this.hidden.selectedIndex = this.selectedIndex = i;
          this.currentText.innerHTML = this.options[i].text;
          break;
        }
    }

    if (this.width) {
      dojo.style(this.container, 'minWidth', this.width+'px');
    } else {
      dojo.style(this.container, 'minWidth', Math.ceil(dojo.style(this.container, 'width')+1)+'px');
      if(dojo.isIE == 7) {
        var span = dojo.query('#offlajnlistcontainer' + this.name + ' span');
        dojo.style(this.container, 'width', dojo.style(span[0], 'width')+30+'px');
      }
    }

    dojo.connect(this.container, 'onclick', this, 'controller');
    this.options.forEach(function(o, i){
      this.map[o.value] = i;
      this.names[i] = o.text;
    },this);
    this.keyListener;
  },

  initSelectBox: function(){
    if(this.selectbox) return;

    var i, elements = '<div class="content">';
    for (i = 0; i < this.options.length; i++)
      elements += '<div class="listelement">'+ this.options[i].text +'</div>';
    elements += '</div>';

    this.selectbox = dojo.create('div', {'id': 'offlajnlistelements' + this.name, 'class': 'offlajnlistelements', 'innerHTML': elements}, this.container, "after");
    this.list = dojo.query('.listelement', this.selectbox);

    this.list.connect('onmouseenter', this, 'addActive');

    dojo.style(this.selectbox, {
      opacity: 0,
      display: 'block'
    });

    this.lineHeight = dojo.position(this.list[0]).h;
    dojo.style(this.selectbox, {
      height: (this.height) ? this.height * this.lineHeight + 'px' : 'auto'
    });

    if(this.height) {
      this.content = dojo.query('#offlajnlistelements' + this.name + ' .content')[0];
      dojo.style(this.content, 'height', this.list.length * this.lineHeight + 'px');
      this.scrollbar = new OfflajnScroller({
        'extraClass': 'single-select',
        'selectbox': this.selectbox,
        'content': this.content
      });
    }

    this.maxW = 0;
    this.list.forEach(function(el, i){
      if (this.options[i].value == 'optgroup') dojo.addClass(el, "optgroup");
      el.i = i;
    },this);

    this.list.connect('onclick', this, 'selected');

    this.selectbox.h = dojo.marginBox(this.selectbox).h;
    dojo.style(this.selectbox, {
      height: 0
    });
    dojo.connect(document, 'onclick', this, 'blur');
    dojo.connect(this.selectbox, 'onclick', this, 'focused');

    if(this.fireshow)
      OfflajnFireEvent(this.hidden, 'click');
  },

  controller: function(){
    this.focused();
    this.initSelectBox();
    if(this.showed == 0){
      this.reposition();
      this.showList();
    }else{
      this.hideList();
    }
  },

  reposition: function(){
    var pos = dojo.coords(this.container, true);
    if(this.selectbox){

      dojo.style(this.selectbox, {
        left: pos.l + "px",
        top: pos.t + pos.h  + "px",
        width: pos.w -2 +"px" //-2px because of the side-borders
      });
      if(this.content) {
        dojo.style(this.content,{

         'width': pos.w - 12 + 'px',
         'float': 'left'
         });
      }
    }
  },

  showList: function(){
    this.keyListener = dojo.connect(document, 'keydown', this, 'keySearch');
    if(this.anim) this.anim.stop();
    this.showed = 1;
    dojo.addClass(this.container,'openedlist');
    dojo.addClass(this.selectbox,'openedlist');
    dojo.removeClass(this.active,'active');
    dojo.addClass(this.list[this.hidden.selectedIndex],'selected active');
    if(this.height) {
      var p = this.hidden.selectedIndex * this.lineHeight;
      this.scrollbar.setPosition(p);
    }
    this.active = this.list[this.hidden.selectedIndex];

    dojo.style(this.offlajnlist, 'zIndex', ++window.offlajnlistzindex);
    dojo.style(this.selectbox, {
      display: 'block',
      zIndex: window.offlajnlistzindex-1
    });
    window.offlajnlistzindex++;

    this.anim = dojo.animateProperty({
      node: this.selectbox,
      properties: {
          opacity : 1,
          height: this.selectbox.h
      }
    }).play();
  },

  keySearch: function(e) {
    //console.log(String.fromCharCode(e.keyCode));
    if(e.keyCode == 13) {
      this.hideList();
      OfflajnFireEvent(this.hidden, 'change');
      this.change = 0;
    } else if(e.keyCode == 38) {
      e.preventDefault();
      var index = this.hidden.selectedIndex-1;
        this.setSelected(index);
    } else if(e.keyCode == 40) {
      e.preventDefault();
      var index = this.hidden.selectedIndex+1;
        this.setSelected(index);
    }
    //console.log(this.names);
    var scroll = this.scrollbar;
    for(var i=0;i<this.names.length;i++) {
      if(this.names[i].toLowerCase().indexOf(String.fromCharCode(e.keyCode).toLowerCase()) == 0) {
        this.setSelected(i);
        break;
      }
    }
  },

  hideList: function(){
    dojo.disconnect(this.keyListener);
    if(this.anim) this.anim.stop();
    if(!this.selectbox) return;

    this.showed = 0;

    var h = dojo.marginBox(this.selectbox).h;
    dojo.removeClass(this.container,'openedlist');
    this.anim = dojo.animateProperty({
      node: this.selectbox,
      properties: {
          opacity : 0,
          height: 0
      },
      onEnd: dojo.hitch(this, function(el){
        dojo.style(el, {
          display: 'none',
          height: '0',
          zIndex: this.zindex-1
        });
        dojo.style(this.offlajnlist, 'zIndex', this.zindex);
        dojo.removeClass(this.selectbox,'openedlist');
      })
    }).play();
  },

  selected: function(e){
    if (dojo.hasClass(e.currentTarget, 'optgroup')) return;
    if(this.list[this.hidden.selectedIndex])
      dojo.removeClass(this.list[this.hidden.selectedIndex],'selected active');
    this.hidden.selectedIndex = e.target.i;
    this.hidden.value = this.hidden.options[this.hidden.selectedIndex].value;

    this.currentText.innerHTML = this.hidden.options[this.hidden.selectedIndex].text;
    if(this.list[this.hidden.selectedIndex])
      dojo.addClass(this.list[this.hidden.selectedIndex],'selected active');
    this.hideList();
    OfflajnFireEvent(this.hidden, 'change');
    this.change = 0;
  },

  setSelected: function(val) {
    if(!this.list[val]) return;
    if(this.list[this.hidden.selectedIndex])
      dojo.removeClass(this.list[this.hidden.selectedIndex],'selected active');

    this.hidden.selectedIndex = val;
    this.hidden.value = this.hidden.options[this.hidden.selectedIndex].value;

    this.currentText.innerHTML = this.hidden.options[this.hidden.selectedIndex].text;
    if(this.list[this.hidden.selectedIndex])
      dojo.addClass(this.list[this.hidden.selectedIndex],'selected active');

    if(this.height) {
        var p = this.hidden.selectedIndex * this.lineHeight;
        this.scrollbar.setPosition(p);
    }
  },

  addActive: function(e){
    var el = e.target;
    if(el != this.active){
      dojo.removeClass(this.active,'active');
      dojo.addClass(el,'active');
      this.active = el;
    }
  },

  focused: function(){
    this.focus = 1;
  },

  blur: function(e){
    if(!this.focus){
      this.hideList();
    }
    this.focus = 0;
  },

  setValue: function(e) {
    if(!this.change && this.map[this.hidden.value] != this.hidden.selectedIndex) {
      this.change = 1;
      e.target.i = this.map[this.hidden.value] ? this.map[this.hidden.value] : 0;
      this.selected(e);
    }
  }
});

dojo.declare("OfflajnScroller", null, {
	constructor: function(args) {
   this.scrollspeed = 10;
   this.curr = 0;
	 dojo.mixin(this,args);
	 this.initScrollbar();
  },
  
  initScrollbar: function() {
    (!dojo.isMozilla) ? dojo.connect(this.selectbox, 'onmousewheel', this, 'scrollWheel') : dojo.connect(this.selectbox, 'DOMMouseScroll', this, 'scrollWheel');
    var right = dojo.create('div', {'class': 'gk_hack offlajnscrollerright'}, this.selectbox);
    this.sc = dojo.create('div', {'class': 'gk_hack offlajnscrollerbg'}, right);
    this.scrollbg = dojo.create('div', {'class': 'gk_hack offlajnscrollerscrollbg'}, this.sc);
    this.scrollbtn = dojo.create('div', {'class': 'gk_hack offlajnscrollerscrollbtn'} ,this.sc );
    if(this.extraClass) {
      dojo.addClass(right, this.extraClass);
      dojo.addClass(this.sc, this.extraClass);
      dojo.addClass(this.scrollbg, this.extraClass);
      dojo.addClass(this.scrollbtn, this.extraClass);
    }
    if(this.extraClass == 'multi-select') {
      this.scrollup = dojo.create('div', {'class': 'gk_hack offlajnscrollerarrowup'}, this.sc, 'first');
      this.scrolldown = dojo.create('div', {'class': 'gk_hack offlajnscrollerarrowdown' }, this.sc, 'last');     
      this.scrupc = dojo.connect(this.scrollup, 'onmousedown', this, 'upScroll');
      this.scrdownc = dojo.connect(this.scrolldown, 'onmousedown', this, 'downScroll');   
    }    
    dojo.connect(this.scrollbtn, 'onmousedown', this, 'onscrolldown');
    dojo.connect(this.scrollbg, 'onclick', this, 'scrollTo');
    this.scrbg = dojo.position(this.scrollbg, true);
    this.scrollbtnprop = dojo.position(this.scrollbtn, true);
    
    this.scrollReInit();
  },
  
  scrollReInit: function(){
    dojo.style(this.scrollbtn, 'display', 'block');
    this.maxHeight = parseInt(dojo.position(this.content).h);
    this.windowHeight = parseInt(dojo.style(this.selectbox, 'height'));
    this.scrollRatio = this.maxHeight/this.windowHeight;
    
    this.maxTop = -1 * (this.maxHeight-this.windowHeight);
    if(this.maxTop > 0) this.maxTop = 0;
    var scrollArrowHeight = 0;
    this.scrollHeight = 0;
    var marginVertical = dojo.marginBox(this.scrollbg).h-dojo.position(this.scrollbg).h;
    if(this.extraClass == 'multi-select') {
      scrollArrowHeight = dojo.marginBox(this.scrollup).h;
      this.scrollHeight = (this.windowHeight+(-2*scrollArrowHeight-marginVertical-2));
      this.scrollBtnmaxTop = (this.scrollHeight-this.scrollHeight/this.scrollRatio)-2;
    } else {
      this.scrollHeight = (this.windowHeight-10);
      this.scrollBtnmaxTop = (this.scrollHeight-this.scrollHeight/this.scrollRatio);
    }
    dojo.style(this.scrollbg, 'height', this.scrollHeight+'px');
    var scrollBtn = (this.scrollHeight/this.scrollRatio-2);
    if(scrollBtn<10){
      scrollBtn = 10;
      this.scrollBtnmaxTop = (this.scrollHeight-scrollBtn-2);
    }
    this.scrollBtnH = scrollBtn;
    dojo.style(this.scrollbtn, 'height', scrollBtn+'px');
    if(this.scrollBtnmaxTop < 0) this.scrollBtnmaxTop = 0; 
    if(this.windowHeight > this.maxHeight) this.hideScrollBtn();  
  },
  
  hideScrollBtn: function() {
    dojo.style(this.scrollbtn, 'display', 'none');
  },
  
  goToBottom: function(){
    this.scrolling(-1000,1000);
  },
  
  onscrolldown: function(e) {
    this.scrdown = 1;
    this.currentpos = e.clientY;
    this.scrbtnpos = dojo.style(this.scrollbtn, 'top');
    this.mousemove = dojo.connect(document, 'onmousemove', this, 'onscrollmove');
    this.mouseup = dojo.connect(document, 'onmouseup', this, 'mouseUp');
  },
  
  onscrollmove: function(e) {
    var diff = this.currentpos-e.clientY;
    if(diff == 0) return;
    var lastt = (dojo.style(this.scrollbtn, 'top'));
    var pos = dojo.style(this.content, 'top');
    this.scrolling(diff, 	(((lastt-diff)/this.scrollBtnmaxTop)*this.maxTop-pos)/diff);
    this.currentpos = e.clientY;
  },
  
  scrollTo: function(e) {
    var pos = e.clientY;
    var sc = dojo.position(this.scrollbg);
    var currpos = pos - sc.y;    
    if(currpos < this.maxTop) currpos = maxTop; 
    if(currpos > this.scrollBtnmaxTop) currpos = this.scrollBtnmaxTop;
    dojo.style(this.scrollbtn, 'top', currpos + 'px');
    var scroll = -1*currpos * this.scrollRatio;
    dojo.style(this.content, 'top', scroll + 'px');
  },
  
  setPosition: function(p) {
    var pos = -1*p;
    if(pos < this.maxTop) pos = this.maxTop;
    this.setScrollBtn(pos);
    dojo.style(this.content, 'top', pos + 'px');
  },
  
  onscrollup: function(e) {
    e.stopPropagation();
    this.scrdown = 0;
  },
  
  upScroll: function(e) {
    this.mouseup = dojo.connect(document, 'onmouseup', this, 'mouseUp');
    e.stopPropagation();
    this.btnScroll(1);
  },
  
  downScroll: function(e) {
    this.mouseup = dojo.connect(document, 'onmouseup', this, 'mouseUp');
    e.stopPropagation();
    this.btnScroll(-1);
  },
  
  btnScroll: function(direction){
    this.dscr = 1;
    var fn = dojo.hitch(this, 'scrolling', direction, this.scrollspeed/4);
    fn();
    this.inter = window.setInterval(fn, 50);
  },
    
  scrolling: function(p, ratio) {
    if(ratio == undefined) ratio = this.scrollspeed;
    var pos = dojo.style(this.content, 'top');
    var scr = pos + (p * ratio);

    
    if(scr < this.maxTop) scr = this.maxTop;
    if(scr > 0) scr = 0;
    dojo.style(this.content, 'top', scr + 'px');
   
    this.setScrollBtn(scr);
    this.curr = scr;
    this.onScroll();
  },
  
  onScroll: function(){
  
  },
    
  setScrollBtn: function(val) {
    var top = (this.scrollBtnmaxTop*(val/this.maxTop));
    dojo.style(this.scrollbtn, 'top', top+'px');
  },
  
  mouseUp: function(e) {
    if(this.mousemove)
      dojo.disconnect(this.mousemove);
    if(this.mouseup)
      dojo.disconnect(this.mouseup);
    e.stopPropagation();
    this.inter = window.clearInterval(this.inter);
    if( this.dscr == 1) {
      this.dscr = 0;
    }
  },
  
  scrollWheel: function(e) {
    var pos = 0;
    pos = (e.detail != "") ? e.detail : e.wheelDelta;  
    if(dojo.isMozilla || dojo.isOpera) {  
      if (pos < 0) {
        this.scrolling(1);
      } else {
        this.scrolling(-1);
      }
    } else {
      if (pos < 0) {
        this.scrolling(-1);
      } else {
        this.scrolling(1);
      }
    }
    dojo.stopEvent(e);
  }
  
});
dojo.addOnLoad(function(){new OfflajnMultiSelectList({
      name: "jformparamsmoduleparametersTabmenutypejshoppingcategoryid",
      height: "10",
      type: "jshopping",
      data: {"jshopping":"<div class=\"gk_hack multiselectitem\">All items<\/div><div class=\"gk_hack multiselectitem\">\u0421\u041d\u041f\u0427<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u041d\u041f\u0427 Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u041d\u041f\u0427 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u041d\u041f\u0427 HP<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u041d\u041f\u0427 Brother<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u041d\u041f\u0427 \u041a\u043e\u043d\u0441\u0442\u0440\u0443\u043a\u0442\u043e\u0440<\/div><div class=\"gk_hack multiselectitem\">\u041f\u0417\u041a<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0417\u041a Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0417\u041a Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0417\u041a HP<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0417\u041a Brother<\/div><div class=\"gk_hack multiselectitem\">\u0427\u0438\u043f\u044b<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b HP<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Samsung<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Xerox<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b OKI<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Ricoh<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Pantum<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Kyocera<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0438\u043f\u044b Lexmark<\/div><div class=\"gk_hack multiselectitem\">\u0421\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438 Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438 HP<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438 Brother, Lexmark, Sharp<\/div><div class=\"gk_hack multiselectitem\">\u041b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 HP<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Samsung<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Xerox<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Brother<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0438 Kyocera<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Panasonic<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 OKI<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0440\u0438\u0434\u0436 Toshiba, Sharp, Lexmark, Ricoh<\/div><div class=\"gk_hack multiselectitem\">\u0417\u0418\u041f \u0434\u043b\u044f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 Toshiba<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 HP<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 Samsung<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 Xerox<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 Brother<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 Panasonic<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430 \u0420\u0430\u0437\u043d\u043e\u0435<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0430\u043a\u0435\u0442 \u0434\u043b\u044f \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0430<\/div><div class=\"gk_hack multiselectitem\">\u041f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u041c\u0424\u0423<\/div><div class=\"gk_hack multiselectitem\">--- Oki \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">--- \u0420\u0443\u0447\u043d\u043e\u0439 \u043f\u0440\u0438\u043d\u0442\u0435\u0440<\/div><div class=\"gk_hack multiselectitem\">--- Epson \u0441 \u0421\u041d\u041f\u0427 \u0438 \u041f\u0417\u041a<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0442\u044b\u0439 Samsung<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0442\u044b\u0439 \u041c\u0424\u0423, \u043f\u0440\u0438\u043d\u0442\u0435\u0440 Pantum<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0432 \u0440\u0430\u0437\u0431\u043e\u0440\u0435<\/div><div class=\"gk_hack multiselectitem\">----- Epson \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- HP \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- Canon \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- Panasonic \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- Samsung \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- Brother \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- OKI \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u041c\u0424\u0423<\/div><div class=\"gk_hack multiselectitem\">----- Ricoh \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- Xerox \u0441\u0442\u0440\u0443\u0439\u043d\u044b\u0435 \u0438 \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u043c\u0444\u0443<\/div><div class=\"gk_hack multiselectitem\">----- Sharp \u043b\u0430\u0437\u0435\u0440\u043d\u044b\u0435 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u044b \u0438 \u041c\u0424\u0423<\/div><div class=\"gk_hack multiselectitem\">\u0420\u0435\u0448\u0435\u043d\u0438\u044f \u0434\u043b\u044f \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0432\u043a\u0430 Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0432\u043a\u0438 Samsung<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0432\u043a\u0438 Xerox<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0432\u043a\u0430 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Pantum<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0448\u0438\u0442\u0430\u044f \u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u043e\u043c \u043f\u0430\u043c\u044f\u0442\u044c<\/div><div class=\"gk_hack multiselectitem\">\u0417\u0418\u041f \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f Epson<\/div><div class=\"gk_hack multiselectitem\">----- Print Head ( \u041f\u0435\u0447\u0430\u0442\u0430\u044e\u0449\u0430\u044f \u0433\u043e\u043b\u043e\u0432\u043a\u0430 )<\/div><div class=\"gk_hack multiselectitem\">----- Pump Assy ( \u0423\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0447\u0435\u0440\u043d\u0438\u043b \u0432 \u0441\u0431\u043e\u0440\u0435 )<\/div><div class=\"gk_hack multiselectitem\">----- Board Assy ( \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u0430\u044f \u043f\u043b\u0430\u0442\u0430 )<\/div><div class=\"gk_hack multiselectitem\">----- DAMPER ( \u0414\u0435\u043c\u043f\u0435\u0440 )<\/div><div class=\"gk_hack multiselectitem\">----- Cable Assy ( \u0428\u043b\u0435\u0439\u0444 \u0432 \u0441\u0431\u043e\u0440\u0435 )<\/div><div class=\"gk_hack multiselectitem\">----- Belt, Scale ( \u0440\u0435\u043c\u0435\u043d\u044c \u043a\u0430\u0440\u0435\u0442\u043a\u0438, \u043b\u0435\u043d\u0442\u0430 \u043f\u043e\u0437\u0438\u0446\u0438\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f )<\/div><div class=\"gk_hack multiselectitem\">----- Motor Assy ( \u0434\u0432\u0438\u0433\u0430\u0442\u0435\u043b\u044c )<\/div><div class=\"gk_hack multiselectitem\">----- Paper feed unit ( \u0443\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0431\u0443\u043c\u0430\u0433\u0438 )<\/div><div class=\"gk_hack multiselectitem\">----- Gear ( \u0428\u0435\u0441\u0442\u0435\u0440\u043d\u044f )<\/div><div class=\"gk_hack multiselectitem\">----- Scaner unit ( \u0431\u043b\u043e\u043a \u0441\u043a\u0430\u043d\u0435\u0440\u0430 )<\/div><div class=\"gk_hack multiselectitem\">----- Power Assy ( \u0431\u043b\u043e\u043a \u043f\u0438\u0442\u0430\u043d\u0438\u044f )<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0430\u0437\u043d\u043e\u0435<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u041f\u0435\u0447\u0430\u0442\u0430\u044e\u0449\u0430\u044f \u0433\u043e\u043b\u043e\u0432\u043a\u0430<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u0430\u044f \u043f\u043b\u0430\u0442\u0430 Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u0442\u0435\u0440\u043c\u043e\u0437\u0430\u043a\u0440\u0435\u043f\u043b\u0435\u043d\u0438\u044f Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0431\u0443\u043c\u0430\u0433\u0438 Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043d\u0438, \u043b\u0435\u043d\u0442\u044b \u043f\u043e\u0437\u0438\u0446\u0438\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f, \u0434\u0438\u0441\u043a\u0438 \u044d\u043d\u043a\u043e\u0434\u0435\u0440\u0430 Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u043b\u0435\u0439\u0444\u044b Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0447\u0435\u0440\u043d\u0438\u043b Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u0435\u0441\u0442\u0435\u0440\u0435\u043d\u043a\u0438 Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0411\u043b\u043e\u043a\u0438 \u043f\u0438\u0442\u0430\u043d\u0438\u044f Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u0434\u0432\u0438\u0433\u0430\u0442\u0435\u043b\u0438 \u0438 \u0441\u043e\u043b\u0435\u043d\u043e\u0438\u0434\u044b Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0430\u0442\u0447\u0438\u043a\u0438 Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0430\u0437\u043d\u043e\u0435 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f HP<\/div><div class=\"gk_hack multiselectitem\">----- \u041f\u0435\u0447\u0430\u0442\u0430\u044e\u0449\u0430\u044f \u0433\u043e\u043b\u043e\u0432\u043a\u0430 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u0430\u044f \u043f\u043b\u0430\u0442\u0430 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u0442\u0435\u0440\u043c\u043e\u0437\u0430\u043a\u0440\u0435\u043f\u043b\u0435\u043d\u0438\u044f HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0431\u0443\u043c\u0430\u0433\u0438 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043d\u0438, \u043b\u0435\u043d\u0442\u044b \u043f\u043e\u0437\u0438\u0446\u0438\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f, \u0434\u0438\u0441\u043a\u0438 \u044d\u043d\u043a\u043e\u0434\u0435\u0440\u0430 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u043b\u0435\u0439\u0444\u044b HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0447\u0435\u0440\u043d\u0438\u043b HP<\/div><div class=\"gk_hack multiselectitem\">----- \u041c\u043e\u0434\u0443\u043b\u0438 \u043f\u0430\u043c\u044f\u0442\u0438 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u0435\u0441\u0442\u0435\u0440\u0435\u043d\u043a\u0438 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u041f\u043e\u0434\u0448\u0438\u043f\u043d\u0438\u043a\u0438 (\u0431\u0443\u0448\u0438\u043d\u0433\u0438) HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0411\u043b\u043e\u043a\u0438 \u043f\u0438\u0442\u0430\u043d\u0438\u044f HP<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u0434\u0432\u0438\u0433\u0430\u0442\u0435\u043b\u0438 \u0438 \u0441\u043e\u043b\u0435\u043d\u043e\u0438\u0434\u044b HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0430\u0442\u0447\u0438\u043a\u0438 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0430\u0437\u043d\u043e\u0435 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u0441\u043a\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f HP<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u0430\u044f \u043f\u043b\u0430\u0442\u0430 Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u0442\u0435\u0440\u043c\u043e\u0437\u0430\u043a\u0440\u0435\u043f\u043b\u0435\u043d\u0438\u044f Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u043f\u043e\u0434\u0430\u0447\u0438 \u0431\u0443\u043c\u0430\u0433\u0438 Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u043b\u0435\u0439\u0444\u044b Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u0435\u0441\u0442\u0435\u0440\u0435\u043d\u043a\u0438 Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u041f\u043e\u0434\u0448\u0438\u043f\u043d\u0438\u043a\u0438 (\u0431\u0443\u0448\u0438\u043d\u0433\u0438) Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0411\u043b\u043e\u043a\u0438 \u043f\u0438\u0442\u0430\u043d\u0438\u044f Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u0434\u0432\u0438\u0433\u0430\u0442\u0435\u043b\u0438 \u0438 \u0441\u043e\u043b\u0435\u043d\u043e\u0438\u0434\u044b Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u0441\u043a\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0411\u043b\u043e\u043a \u043b\u0430\u0437\u0435\u0440\u0430 Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0430\u0442\u0447\u0438\u043a\u0438 Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0430\u0437\u043d\u043e\u0435 Samsung. \u0422\u043e\u0432\u0430\u0440\u044b, \u043d\u0435 \u0432\u043e\u0448\u0435\u0434\u0448\u0438\u0435 \u0432 \u043f\u0440\u0435\u0434\u044b\u0434\u0443\u0449\u0438\u0435 \u043a\u0430\u0442\u0435\u0433\u043e\u0440\u0438\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f Xerox<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f Brother<\/div><div class=\"gk_hack multiselectitem\">----- \u041f\u0435\u0447\u0430\u0442\u0430\u044e\u0449\u0430\u044f \u0433\u043e\u043b\u043e\u0432\u043a\u0430 Brother<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0438\u043f Panasonic, Kyocera<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u0430\u044f \u043f\u043b\u0430\u0442\u0430 Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0428\u0435\u0441\u0442\u0435\u0440\u0435\u043d\u043a\u0438 Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u041f\u043e\u0434\u0448\u0438\u043f\u043d\u0438\u043a\u0438 (\u0431\u0443\u0448\u0438\u043d\u0433\u0438) Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0411\u043b\u043e\u043a\u0438 \u043f\u0438\u0442\u0430\u043d\u0438\u044f Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u0434\u0432\u0438\u0433\u0430\u0442\u0435\u043b\u0438 \u0438 \u0441\u043e\u043b\u0435\u043d\u043e\u0438\u0434\u044b Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0423\u0437\u0435\u043b \u0441\u043a\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0411\u043b\u043e\u043a \u043b\u0430\u0437\u0435\u0440\u0430 Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0430\u0442\u0447\u0438\u043a\u0438 Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0430\u0437\u043d\u043e\u0435 Kyocera. \u0422\u043e\u0432\u0430\u0440\u044b, \u043d\u0435 \u0432\u043e\u0448\u0435\u0434\u0448\u0438\u0435 \u0432 \u043f\u0440\u0435\u0434\u044b\u0434\u0443\u0449\u0438\u0435 \u043a\u0430\u0442\u0435\u0433\u043e\u0440\u0438\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0418\u041f \u0422\u0435\u0440\u043c\u043e\u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430<\/div><div class=\"gk_hack multiselectitem\">\u0422\u043e\u043d\u0435\u0440<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 HP<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Samsung<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Xerox<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Brother<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Panasonic<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Kyocera<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Oki<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Ricoh<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Toshiba<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Konica<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u043e\u043d\u0435\u0440 Sharp<\/div><div class=\"gk_hack multiselectitem\">\u0414\u043b\u044f \u043d\u043e\u0443\u0442\u0431\u0443\u043a\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0417\u0430\u0440\u044f\u0434\u043d\u044b\u0435 \u0443\u0441\u0442\u0440\u043e\u0439\u0441\u0442\u0432\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0410\u043a\u043a\u0443\u043c\u0443\u043b\u044f\u0442\u043e\u0440\u044b \u0411\u0430\u0442\u0430\u0440\u0435\u0438<\/div><div class=\"gk_hack multiselectitem\">\u0427\u0435\u0440\u043d\u0438\u043b\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0435\u0440\u043d\u0438\u043b\u0430 Epson<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0435\u0440\u043d\u0438\u043b\u0430 Canon<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0435\u0440\u043d\u0438\u043b\u0430 HP<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0435\u0440\u043d\u0438\u043b\u0430 Brother<\/div><div class=\"gk_hack multiselectitem\">--- \u0427\u0435\u0440\u043d\u0438\u043b\u0430 Epson, Canon, HP \u0432 \u043a\u0430\u043d\u0438\u0441\u0442\u0440\u0435<\/div><div class=\"gk_hack multiselectitem\">\u0424\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0424\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430 \u0433\u043b\u044f\u043d\u0446\u0435\u0432\u0430\u044f<\/div><div class=\"gk_hack multiselectitem\">--- \u0424\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430 \u043c\u0430\u0442\u043e\u0432\u0430\u044f<\/div><div class=\"gk_hack multiselectitem\">--- \u0424\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430 \u0441 \u0442\u0438\u0441\u043d\u0435\u043d\u0438\u0435\u043c<\/div><div class=\"gk_hack multiselectitem\">--- \u0420\u0443\u043b\u043e\u043d\u043d\u0430\u044f \u0444\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0424\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430 \u043c\u0435\u043b\u043e\u0432\u0430\u043d\u043d\u0430\u044f<\/div><div class=\"gk_hack multiselectitem\">--- \u0424\u043e\u0442\u043e\u0431\u0443\u043c\u0430\u0433\u0430 \u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u0430\u044f<\/div><div class=\"gk_hack multiselectitem\">\u0425\u0438\u043c\u0438\u044f \u0434\u043b\u044f \u0442\u0435\u0445\u043d\u0438\u043a\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u043c\u044b\u0432\u043e\u0447\u043d\u0430\u044f \u0436\u0438\u0434\u043a\u043e\u0441\u0442\u044c \u0434\u043b\u044f \u043f\u0435\u0447\u0430\u0442\u0430\u044e\u0449\u0438\u0445 \u0433\u043e\u043b\u043e\u0432\u043e\u043a<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u043c\u0430\u0437\u043a\u0438 \u0438 \u043c\u0430\u0441\u043b\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0412\u043e\u0441\u0441\u0442\u0430\u043d\u0430\u0432\u043b\u0438\u0432\u0430\u044e\u0449\u0438\u0435, \u043e\u0447\u0438\u0449\u0430\u044e\u0449\u0438\u0435 \u0441\u0440\u0435\u0434\u0441\u0442\u0432\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u043c\u044b\u0432\u043e\u0447\u043d\u0430\u044f \u0436\u0438\u0434\u043a\u043e\u0441\u0442\u044c \u0434\u043b\u044f Brother<\/div><div class=\"gk_hack multiselectitem\">\u041f\u0440\u043e\u0433\u0440\u0430\u043c\u043c\u0430\u0442\u043e\u0440\u044b<\/div><div class=\"gk_hack multiselectitem\">--- \u0423\u043d\u0438\u0432\u0435\u0440\u0441\u0430\u043b\u044c\u043d\u044b\u0435 \u0430\u0434\u0430\u043f\u0442\u0435\u0440\u044b ( \u043f\u0430\u043d\u0435\u043b\u044c\u043a\u0438 )<\/div><div class=\"gk_hack multiselectitem\">\u0420\u0430\u0434\u0438\u043e\u0434\u0435\u0442\u0430\u043b\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u0440\u0430\u043d\u0437\u0438\u0441\u0442\u043e\u0440\u044b<\/div><div class=\"gk_hack multiselectitem\">--- \u041c\u0438\u043a\u0440\u043e\u0441\u0445\u0435\u043c\u044b \u043f\u0430\u043c\u044f\u0442\u0438 FLASH<\/div><div class=\"gk_hack multiselectitem\">--- \u041c\u0438\u043a\u0440\u043e\u0441\u0445\u0435\u043c\u044b \u043f\u0430\u043c\u044f\u0442\u0438 EEPROM<\/div><div class=\"gk_hack multiselectitem\">--- \u041c\u0438\u043a\u0440\u043e\u043a\u043e\u043d\u0442\u0440\u043e\u043b\u043b\u0435\u0440\u044b<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u043e\u043d\u0434\u0435\u043d\u0441\u0430\u0442\u043e\u0440\u044b<\/div><div class=\"gk_hack multiselectitem\">--- \u0422\u0435\u0440\u043c\u043e\u044d\u043b\u0435\u043a\u0442\u0440\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u044d\u043b\u0435\u043c\u0435\u043d\u0442 \u041f\u0435\u043b\u044c\u0442\u0435<\/div><div class=\"gk_hack multiselectitem\">\u041c\u0430\u0442\u0435\u0440\u0438\u0430\u043b\u044b \u0434\u043b\u044f \u043d\u0430\u0440\u0443\u0436\u043d\u043e\u0439 \u0440\u0435\u043a\u043b\u0430\u043c\u044b<\/div><div class=\"gk_hack multiselectitem\">\u0421\u0443\u0431\u043b\u0438\u043c\u0430\u0446\u0438\u044f<\/div><div class=\"gk_hack multiselectitem\">3D \u043f\u0435\u0447\u0430\u0442\u044c<\/div><div class=\"gk_hack multiselectitem\">\u041f\u043b\u0430\u043d\u0448\u0435\u0442\u043d\u044b\u0439 \u043f\u0440\u0438\u043d\u0442\u0435\u0440, \u0442\u0435\u043a\u0441\u0442\u0438\u043b\u044c\u043d\u0430\u044f \u043f\u0435\u0447\u0430\u0442\u044c<\/div>"},
      options: [],
      joomla: 0,
      ids: {"jshopping":["1","2","4","3","20","79","5","21","22","23","24","9","25","26","27","28","29","98","100","107","12348","12408","6","30","31","32","33","7","34","35","36","37","38","39","94","95","97","119","10","12366","40","41","42","43","45","105","118","12396","8","12370","12388","46","47","12413","12386","12393","12391","12392","12394","12395","12390","12416","12418","12415","12419","15","49","50","51","12411","93","16","52","89","12350","12351","12352","12353","12354","12355","12356","12357","12358","12359","12360","53","101","12397","12398","12399","12400","12401","12402","12403","12404","12405","12406","12407","54","102","12372","12373","12374","12375","12376","12377","12378","12379","12380","12381","12382","12383","12384","12432","55","12420","12421","12422","12423","12424","12425","12426","12427","12428","12431","12429","12430","56","57","12365","12361","12434","12435","12436","12437","12438","12439","12440","12441","12442","12443","12433","11","60","58","59","61","62","63","64","65","12367","12368","12371","12385","12389","12409","12410","12412","12","66","67","68","69","116","13","70","71","72","74","99","12387","14","75","76","77","78","17","92","18","81","86","87","88","90","91","104","106","117","12364"]},
      mode: 1
    });

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabmenutypeparenthref",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnList({
        name: "jformparamsmoduleparametersTabmenutypedisplaynumprod",
        options: [{"value":"0","text":"No"},{"value":"1","text":"Yes only the real count"},{"value":"2","text":"Yes the aggregated count on each category"}],
        selectedIndex: 0,
        json: "",
        width: 0,
        height: 0,
        fireshow: 0
      });
    

      new OfflajnList({
        name: "jformparamsmoduleparametersTabmenutypeelementorder",
        options: [{"value":"0","text":"Component Default"},{"value":"1","text":"Alphabetical Ascending"},{"value":"2","text":"Alphabetical Descending"}],
        selectedIndex: 0,
        json: "",
        width: 0,
        height: 0,
        fireshow: 0
      });
    });
      djConfig = {};})();