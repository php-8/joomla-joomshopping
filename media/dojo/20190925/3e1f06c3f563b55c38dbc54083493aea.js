
        (function(){
var dojo = odojo;

var dijit = odijit;

var dojox = odojox;


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



dojo.declare("JoomlaType", null, {
  constructor: function(args){
    dojo.mixin(this, args);
    this.list = dojo.byId(this.selectorId);
    if(!this.joomfish){
      this.select = dojo.byId("paramsjoomlamenu") ? dojo.byId("paramsjoomlamenu") : dojo.byId('jformparamsmenutypejoomlamenu');
    }else{
      this.select = dojo.byId(this.control+"joomlamenu");
    }
    dojo.destroy(this.select.options[0]);
    dojo.connect(this.select, 'onchange', this, "changeList");
    this.defaultMenu = this.select.options[this.select.selectedIndex].value;
    this.changeList();
  },
  
  changeList: function(e) {
      var type = this.select.options[this.select.selectedIndex].value;
      var node = dojo.create("div");
      node.innerHTML = this.data[type].replace(/option/g,'div');
      dojo.forEach(this.list.childNodes, function(el){
        if(el)
          el.parentNode.removeChild(el);
      });
      
      dojo.forEach(node.childNodes, function(el){
        if(el.nodeName == 'DIV'){
          var opt = document.createElement('OPTION');
          opt.text = el.innerHTML;
          opt.value = dojo.attr(el,'value');
          opt.selected = dojo.attr(el,'selected');
          this.list.options.add(opt);
        }
      }, this);
      if (type!=this.defaultMenu && e && e.currentTarget == this.select) this.list.selectedIndex = 0;   
      //if(e == undefined) this.list.selectedIndex = 0;
  }
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



dojo.declare("OfflajnCombine", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.fields = new Array();
    this.init();
  },


  init: function() {
    this.hidden = dojo.byId(this.id);
    //console.log(this.hidden.value);
    dojo.connect(this.hidden, 'onchange', this, 'reset');
    for(var i = 0;i < this.num; i++){
      this.fields[i] = dojo.byId(this.id+i);
      this.fields[i].combineobj = this;
      if(this.fields[i].loaded) this.fields[i].loaded();
      dojo.connect(this.fields[i], 'change', this, 'change');
    }
    this.reset();

    this.outer = dojo.byId('offlajncombine_outer' + this.id);
    this.items = dojo.query('.offlajncombinefieldcontainer', this.outer);
    if(this.switcherid) {
      this.switcher = dojo.byId(this.switcherid);
      dojo.connect(this.switcher, 'onchange', this, 'hider');
      this.hider();
    }
  },

  reset: function(){
    this.value = this.hidden.value;
    //console.log(this.hidden);
    var values = this.value.split('|*|');
    for(var i = 0;i < this.num; i++){
      if(this.fields[i].value != values[i]){
        this.fields[i].value = values[i] === undefined ? '' : values[i];
        OfflajnFireEvent(this.fields[i], 'change');
      }
    }
  },

  change: function(){
    var value = '';
    for(var i = 0;i < this.num; i++){
      value+= this.fields[i].value+'|*|';
    }
    this.hidden.value = value;
    OfflajnFireEvent(this.hidden, 'change');
  },

  hider: function() {
    var w = dojo.position(this.outer).w;
    if(!this.hiderdiv) {
      //this.hiderdiv = dojo.query('.offlajncombine_hider', this.switcher.parentNode.parentNode.parentNode)[0];
      this.hiderdiv = dojo.query('.offlajncombine_hider', this.switcher.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode)[0];

      dojo.style(this.hiderdiv, 'width',  w - 38 + 'px');
    }

    var switcherVal = this.switcher.value;



    if(this.islist == 1){
      if(switcherVal > 0) {
        switcherVal=0;
      } else {
        switcherVal=1;
      }
    }

    if(switcherVal == 0) {
      this.items.forEach(function(item, i){
        if(i >= this.hideafter && item != this.switcher.parentNode.parentNode) {
          item.style.opacity = 0.5;
          item.style.pointerEvents = 'none';
        }
      }, this);
      if(this.hideafter == 0)
        dojo.style(this.hiderdiv, 'display', 'block');
    } else {
      this.items.forEach(function(item, i){
        if(item != this.switcher.parentNode.parentNode) {
          item.style.opacity = '';
          item.style.pointerEvents = '';
        }
      }, this);
      if(this.hideafter == 0)
        dojo.style(this.hiderdiv, 'display', 'none');
    }
  }
});


dojo.declare("OfflajnText", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.init();
  },


  init: function() {
    this.hidden = dojo.byId(this.id);
    dojo.connect(this.hidden, 'change', this, 'reset');

    this.input = dojo.byId(this.id+'input');
    this.switcher = dojo.byId(this.id+'unit');

    this.placeholder && dojo.attr(this.input, 'placeholder', this.placeholder.replace(/:$/, ''));

    if(this.validation == 'int'){
      dojo.connect(this.input, 'keyup', this, 'validateInt');
      this.validateInt();
    }else if(this.validation == 'float'){
      dojo.connect(this.input, 'keyup', this, 'validateFloat');
      this.validateFloat();
    }
    dojo.connect(this.input, 'onblur', this, 'change');
    if(this.switcher){
      dojo.connect(this.switcher, 'change', this, 'change');
    }else{
      if(this.attachunit != '')
        this.switcher = {'value': this.attachunit, 'noelement':true};

    }
    this.container = dojo.byId('offlajntextcontainer' + this.id);
    if(this.mode == 'increment') {
      this.arrows = dojo.query('.arrow', this.container);
      dojo.connect(this.arrows[0], 'onmousedown', dojo.hitch(this, 'mouseDown', 1));
      dojo.connect(this.arrows[1], 'onmousedown', dojo.hitch(this, 'mouseDown', -1));
    }
    dojo.connect(this.input, 'onfocus', this, dojo.hitch(this, 'setFocus', 1));
    dojo.connect(this.input, 'onblur', this, dojo.hitch(this, 'setFocus', 0));
  },

  reset: function(e){
    if(this.hidden.value != this.input.value+(this.switcher? '||'+this.switcher.value : '')){
      var v = this.hidden.value.split('||');
      this.input.value = v[0];
      if(this.switcher && this.switcher.noelement != true){
        this.switcher.value = v[1];
        OfflajnFireEvent(this.switcher, 'change');
      }
      if(e) dojo.stopEvent(e);
      OfflajnFireEvent(this.input, 'change');
    }
  },

  change: function(){
    this.hidden.value = this.input.value+(this.switcher? '||'+this.switcher.value : '');
    OfflajnFireEvent(this.hidden, 'change');
    if(this.onoff) this.hider();
  },

  setFocus: function(mode) {
    if(mode){
      dojo.addClass(this.input.parentNode, 'focus');
    } else {
      dojo.removeClass(this.input.parentNode, 'focus');
    }
  },

  hider: function() {
    if(!this.hiderdiv) {
      this.hiderdiv = dojo.create('div', {'class': 'offlajntext_hider'}, this.container);
      dojo.style(this.hiderdiv, 'width', dojo.position(this.container).w + 'px');
    }
    if(parseInt(this.switcher.value)) {
      dojo.style(this.container, 'opacity', '1');
      dojo.style(this.hiderdiv, 'display', 'none');
    } else {
      dojo.style(this.container, 'opacity', '0.5');
      dojo.style(this.hiderdiv, 'display', 'block');
    }
  },

  validateInt: function(){
    var val = parseInt(this.input.value, 10);
    if(!val) val = 0;
    this.input.value = val;
  },

  validateFloat: function(){
    var val = parseFloat(this.input.value);
    if(!val) val = 0;
    this.input.value = val;
  },

  mouseDown: function(m){
    dojo.connect(document, 'onmouseup', this, 'mouseUp');
    var f = dojo.hitch(this, 'modifyValue', m);
    f();
    this.interval = setInterval(f, 200);
  },

  mouseUp: function(){
    clearInterval(this.interval);
  },

  modifyValue: function(m) {
    var val = 0;
    if(this.validation == 'int') {
      val = parseInt(this.input.value);
    } else if(this.validation == 'float') {
      val = parseFloat(this.input.value);
    }
    val = val + m*this.scale;
    if(val < 0 && this.minus == 0) val = 0;
    this.input.value = val;
    this.change();
    OfflajnFireEvent(this.input, 'change');
  }
});



dojo.declare("OfflajnSwitcher", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
   this.w = 11;
	 this.init();
  },


  init: function() {
    this.switcher = dojo.byId('offlajnswitcher_inner' + this.id);
    this.input = dojo.byId(this.id);
    this.state = this.map[this.input.value];
    this.click = dojo.connect(this.switcher, 'onclick', this, 'controller');
    dojo.connect(this.input, 'onchange', this, 'setValue');
    this.elements = new Array();
    this.getUnits();
    this.setSwitcher();
  },

  getUnits: function() {
    var units = dojo.create('div', {'class': 'offlajnswitcher_units' }, this.switcher.parentNode, "after");
    dojo.forEach(this.units, function(item, i){
      this.elements[i] = dojo.create('span', {'class': 'offlajnswitcher_unit', 'innerHTML': item }, units);
      if(this.mode) {
        this.elements[i].innerHTML = '';
        this.elements[i] = dojo.create('img', {'src': this.url + item }, this.elements[i]);
      }
      this.elements[i].i = i;
      dojo.connect(this.elements[i], 'onclick', this, 'selectUnit');
    }, this);
  },

  getBgpos: function() {
    var pos = dojo.style(this.switcher, 'backgroundPosition');
    if(dojo.isIE <= 8){
      pos = dojo.style(this.switcher, 'backgroundPositionX')+' '+dojo.style(this.switcher, 'backgroundPositionY');
    }
    var bgp = pos.split(' ');
    bgp[1] = parseInt(bgp[1]);
    return !bgp[1] ? 0 : bgp[1];
  },

  selectUnit: function(e) {
    this.state = (e.target.i) ? 0 : 1;
    this.controller();
  },

  setSelected: function() {
    var s = (this.state) ? 0 : 1;
    dojo.removeClass(this.elements[s], 'selected');
    dojo.addClass(this.elements[this.state], 'selected');
  },

  controller: function() {
    if(this.anim) this.anim.stop();
    this.state ? this.setSecond() : this.setFirst();
  },


  setValue: function() {
    if(this.values[this.state] != this.input.value) {
      this.controller();
    }
  },

  setSwitcher: function() {
    (this.state) ? this.setFirst() : this.setSecond();
  },

  changeState: function(state){
    if(this.state != state){
      this.state = state;
      this.stateChanged();
    }
    this.setSelected();
  },

  stateChanged: function(){
    this.input.value = this.values[this.state];
    OfflajnFireEvent(this.input, 'change');
  },

  setFirst: function() {
    this.changeState(1);
    var bgp = this.getBgpos();
    this.anim = new dojo.Animation({
      curve: new dojo._Line(bgp, 0),
      node: this.switcher,
      duration: 200,
      onAnimate: function(){
				var str = "center " + Math.floor(arguments[0])+"px";
				dojo.style(this.node,"backgroundPosition",str);
			}
    }).play();
  },


  setSecond: function() {
    this.changeState(0);
    var bgp = this.getBgpos();
    this.anim = new dojo.Animation({
      curve: new dojo._Line(bgp, -1*this.w),
      node: this.switcher,
      duration: 200,
      onAnimate: function(){
				var str =  "center " + Math.floor(arguments[0])+"px";
				dojo.style(this.node,"backgroundPosition",str);
			}
    }).play();
  }

});

dojo.addOnLoad(function(){
      new OfflajnList({
        name: "jformparamsmoduleparametersTabmenutypejoomlamenu",
        options: [{"value":"glavnoe-menyu","text":"Glavnoe-menyu"},{"value":"magazin","text":"Magazin"},{"value":"poleznoe","text":"Poleznoe"},{"value":"skrytoe-menyu","text":"Skrytoe-menyu"},{"value":"menyu","text":"Menyu"}],
        selectedIndex: 0,
        json: "",
        width: 0,
        height: 0,
        fireshow: 0
      });
    
new OfflajnMultiSelectList({
      name: "jformparamsmoduleparametersTabmenutypejoomlamenutype",
      height: "10",
      type: "skrytoe-menyu",
      data: {"":"<div class=\"gk_hack multiselectitem\">All items<\/div><div class=\"gk_hack multiselectitem\">Menu_Item_Root<\/div>","glavnoe-menyu":"<div class=\"gk_hack multiselectitem\">All items<\/div><div class=\"gk_hack multiselectitem\">--- \u00a0<\/div><div class=\"gk_hack multiselectitem\">--- \u041e \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u041e\u043f\u043b\u0430\u0442\u0430 \u0438 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0423\u0441\u043b\u0443\u0433\u0438<\/div><div class=\"gk_hack multiselectitem\">----- \u0417\u0430\u043f\u0440\u0430\u0432\u043a\u0430 \u043a\u0430\u0440\u0442\u0440\u0438\u0434\u0436\u0435\u0439 \u0432 \u041c\u043e\u0441\u043a\u0432\u0435<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Epson<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Kyocera<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Lexmark<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Brother<\/div><div class=\"gk_hack multiselectitem\">----- \u0420\u0435\u043c\u043e\u043d\u0442 \u043f\u0440\u0438\u043d\u0442\u0435\u0440\u0430 Ricoh<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u043e\u043d\u0442\u0430\u043a\u0442\u044b <\/div><div class=\"gk_hack multiselectitem\">--- \u0412\u0445\u043e\u0434\u00a0 <\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u0430\u0440\u0442\u0430 \u0441\u0430\u0439\u0442\u0430<\/div>","main":"<div class=\"gk_hack multiselectitem\">All items<\/div><div class=\"gk_hack multiselectitem\">--- com_banners<\/div><div class=\"gk_hack multiselectitem\">----- com_banners<\/div><div class=\"gk_hack multiselectitem\">----- com_banners_categories<\/div><div class=\"gk_hack multiselectitem\">----- com_banners_clients<\/div><div class=\"gk_hack multiselectitem\">----- com_banners_tracks<\/div><div class=\"gk_hack multiselectitem\">--- com_contact<\/div><div class=\"gk_hack multiselectitem\">----- com_contact_contacts<\/div><div class=\"gk_hack multiselectitem\">----- com_contact_categories<\/div><div class=\"gk_hack multiselectitem\">--- com_messages<\/div><div class=\"gk_hack multiselectitem\">----- com_messages_add<\/div><div class=\"gk_hack multiselectitem\">--- com_newsfeeds<\/div><div class=\"gk_hack multiselectitem\">----- com_newsfeeds_feeds<\/div><div class=\"gk_hack multiselectitem\">----- com_newsfeeds_categories<\/div><div class=\"gk_hack multiselectitem\">--- com_redirect<\/div><div class=\"gk_hack multiselectitem\">--- com_search<\/div><div class=\"gk_hack multiselectitem\">--- com_finder<\/div><div class=\"gk_hack multiselectitem\">--- com_joomlaupdate<\/div><div class=\"gk_hack multiselectitem\">--- com_tags<\/div><div class=\"gk_hack multiselectitem\">--- com_postinstall<\/div><div class=\"gk_hack multiselectitem\">--- com_associations<\/div><div class=\"gk_hack multiselectitem\">--- JoomShopping<\/div><div class=\"gk_hack multiselectitem\">----- categories<\/div><div class=\"gk_hack multiselectitem\">----- products<\/div><div class=\"gk_hack multiselectitem\">----- orders<\/div><div class=\"gk_hack multiselectitem\">----- clients<\/div><div class=\"gk_hack multiselectitem\">----- options<\/div><div class=\"gk_hack multiselectitem\">----- configuration<\/div><div class=\"gk_hack multiselectitem\">----- install-and-update<\/div><div class=\"gk_hack multiselectitem\">----- about-as<\/div><div class=\"gk_hack multiselectitem\">--- JMAP<\/div><div class=\"gk_hack multiselectitem\">--- COM_JDOWNLOADS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_CONTROL_PANEL<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_CATEGORIES<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_DOWNLOADS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_FILES<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_LICENSES<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_LAYOUTS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_LOGS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_USER_GROUPS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_CONFIGURATION<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_TOOLS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JDOWNLOADS_TERMS_OF_USE<\/div><div class=\"gk_hack multiselectitem\">--- COM_JCOMMENTS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_COMMENTS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_SETTINGS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_SMILIES<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_SUBSCRIPTIONS<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_CUSTOM_BBCODE<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_BLACKLIST<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_MAILQ<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_IMPORT<\/div><div class=\"gk_hack multiselectitem\">----- COM_JCOMMENTS_ABOUT<\/div><div class=\"gk_hack multiselectitem\">--- COM_PHOCADOWNLOAD<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_CONTROLPANEL<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_FILES<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_CATEGORIES<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_LICENSES<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_STATISTICS<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_DOWNLOADS<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_UPLOADS<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_FILE_RATING<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_TAGS<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_LAYOUT<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_STYLES<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_LOGGING<\/div><div class=\"gk_hack multiselectitem\">----- COM_PHOCADOWNLOAD_INFO<\/div><div class=\"gk_hack multiselectitem\">--- COM_SPSIMPLEPORTFOLIO<\/div><div class=\"gk_hack multiselectitem\">--- IMPORTJS<\/div><div class=\"gk_hack multiselectitem\">----- IMPORT<\/div><div class=\"gk_hack multiselectitem\">----- EXPORT<\/div><div class=\"gk_hack multiselectitem\">----- CONFIGURATIONS<\/div><div class=\"gk_hack multiselectitem\">----- RECOVER<\/div><div class=\"gk_hack multiselectitem\">----- YML<\/div><div class=\"gk_hack multiselectitem\">----- VK<\/div><div class=\"gk_hack multiselectitem\">----- SUPPORT<\/div>","poleznoe":"<div class=\"gk_hack multiselectitem\">All items<\/div><div class=\"gk_hack multiselectitem\">--- \u041d\u043e\u0432\u043e\u0441\u0442\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u0420\u0430\u0431\u043e\u0442\u0430\u0435\u043c \u0441 \u0440\u0435\u0433\u0438\u043e\u043d\u0430\u043c\u0438<\/div><div class=\"gk_hack multiselectitem\">--- \u0421\u043a\u0430\u0447\u0430\u0442\u044c \u0434\u0440\u0430\u0439\u0432\u0435\u0440\u0430<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440\u044b HP<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440\u044b Brother<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440\u044b Samsung<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440\u044b Epson<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440\u044b Canon<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440 \u0434\u043b\u044f Xerox<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440 \u0434\u043b\u044f Lexmark<\/div><div class=\"gk_hack multiselectitem\">----- \u0414\u0440\u0430\u0439\u0432\u0435\u0440 \u0434\u043b\u044f Kyocera<\/div>","skrytoe-menyu":"<div class=\"gk_hack multiselectitem\">All items<\/div><div class=\"gk_hack multiselectitem\">--- \u041a\u043e\u0440\u0437\u0438\u043d\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u041e\u0444\u043e\u0440\u043c\u043b\u0435\u043d\u0438\u0435 \u0437\u0430\u043a\u0430\u0437\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u041f\u0440\u043e\u0444\u0438\u043b\u044c<\/div><div class=\"gk_hack multiselectitem\">--- \u0420\u0435\u0433\u0438\u0441\u0442\u0440\u0430\u0446\u0438\u044f<\/div><div class=\"gk_hack multiselectitem\">--- \u0414\u043e\u0433\u043e\u0432\u043e\u0440-\u043e\u0444\u0435\u0440\u0442\u0430<\/div><div class=\"gk_hack multiselectitem\">--- \u0412\u043e\u0441\u0441\u0442\u0430\u043d\u043e\u0432\u043b\u0435\u043d\u0438\u0435 \u043f\u0430\u0440\u043e\u043b\u044f<\/div>"},
      options: [],
      joomla: 1,
      ids: {"":["1"],"glavnoe-menyu":["101","134","136","170","171","172","173","174","175","176","177","178","138","188","137"],"main":["2","3","4","5","6","7","8","9","10","11","13","14","15","16","17","18","19","20","21","22","102","103","104","105","106","107","108","109","110","111","112","113","114","115","116","117","118","119","120","121","122","123","124","125","126","127","128","129","130","131","132","133","146","147","148","149","150","151","152","153","154","155","156","157","158","159","179","180","181","182","183","184","185","186","187"],"poleznoe":["135","139","140","162","163","164","160","161","165","166","167"],"skrytoe-menyu":["141","142","143","145","168","169"]},
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
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabmenutypesubheader0",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutypesubheader1",
        validation: "int",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabmenutypesubheader",
        num: 2,
        switcherid: "jformparamsmoduleparametersTabmenutypesubheader0",
        hideafter: "0",
        islist: "0"
      }); 
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabmenutypemenu_images",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabmenutyperesizeicon0",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutyperesizeicon1",
        validation: "int",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutyperesizeicon2",
        validation: "int",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutyperesizeicon3",
        validation: "int",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
dojo.addOnLoad(function(){ 
      new OfflajnSwitcher({
        id: "jformparamsmoduleparametersTabmenutyperesizeicon4",
        units: ["Scale","Crop"],
        values: ["1","0"],
        map: {"1":0,"0":1},
        mode: 0,
        url: "http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnswitcher\/images\/"
      }); 
    });

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabmenutyperesizeicon",
        num: 5,
        switcherid: "jformparamsmoduleparametersTabmenutyperesizeicon0",
        hideafter: "2",
        islist: "0"
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutypeiconborderradius0",
        validation: "int",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutypeiconborderradius1",
        validation: "int",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutypeiconborderradius2",
        validation: "int",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabmenutypeiconborderradius3",
        validation: "int",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
dojo.addOnLoad(function(){ 
      new OfflajnSwitcher({
        id: "jformparamsmoduleparametersTabmenutypeiconborderradius4",
        units: ["%","px"],
        values: ["%","px"],
        map: {"%":0,"px":1},
        mode: 0,
        url: "http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnswitcher\/images\/"
      }); 
    });

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabmenutypeiconborderradius",
        num: 5,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    });
      djConfig = {};})();