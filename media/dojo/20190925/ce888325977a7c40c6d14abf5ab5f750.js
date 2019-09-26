
        (function(){
var dojo = odojo;

var dijit = odijit;

var dojox = odojox;

dojo.declare("OfflajnSkin", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.init();
    if(this.hidden.changeSkin){
      this.hidden.changeSkin();
      this.hidden.changeSkin = null;
    }
    if(window[this.name+'delay'] == true){
      window[this.name+'delay'] == false;
      this.hidden.value = this.hidden.options[1].value;
      this.changeSkin();
    }
  },

  init: function() {
    var label = dojo.byId(this.name + '-lbl');
    this.label = label ? label.innerHTML.toLowerCase() : 'preset';
    this.hidden = dojo.byId(this.id);
    //this.span = dojo.create("span", {style: "margin-left: 10px; position: absolute;"}, this.hidden.parentNode.parentNode, "last");
    this.span = dojo.create("span", {style: "margin-left: 10px;"}, this.hidden.parentNode.parentNode, "last");
    this.c = dojo.connect(this.hidden, 'onchange', this, 'changeSkin');
    this.initPreview();
  },

  initPreview: function() {
    var id = this.id,
        root = this.root,
        data = this.data;
    if (window.jQuery) jQuery(function($) {
      $("#offlajnlistcontainer"+id).parent().on("mouseenter", ".listelement", function(e) {
        var $this = $(this),
            i = $this.index()-1,
            j = 0, prop;
        for (prop in data) {
          if (i == j++) {
            if (data[prop].preview) {
              var $img = $('<img src="' + root + data[prop].preview + '">'),
                  off = $this.parent().parent().offset();
              $img.css({
                position: "absolute",
                opacity: 0,
                zIndex: 9999,
                top: off.top,
                left: off.left + $this.parent().parent().outerWidth()
              }).appendTo(document.body)
                .animate({opacity: 1}, 300);
              $this.one("mouseleave", function() {
                $img.animate({opacity: 0}, {
                  duration: 300,
                  complete: function() {$img.remove()}
                });
              });
            }
            break;
          }
        }
      });
    });
  },

  changeSkin: function() {
    if(this.hidden.value != 'custom'){
      this.changeSkinNext();
      this.hidden.value = 'custom';
      OfflajnFireEvent(this.hidden, 'change');
    }
  },

  changeSkinNext: function() {
    var value = this.hidden.value;
    var def = this.data[value];
    for (var k in def) {
      var p = dojo.byId(this.control + k);

      if(!p) {
        var n = this.id.replace(this.name, '');
        p = dojo.byId(n + k);
      }
      if(p) {
        var v = def[k];
        if(v.indexOf("**") >= 0){
            var newv = v.split('|*|');
            var oldv = p.value.split('|*|');
            for(var i = 0; i < oldv.length; i++){
                if(newv[i] != '**'){
                    oldv[i] = newv[i];
                }
            }
            v = oldv.join('|*|');
        }else if(v.length > 0 && v.indexOf("{") == 0){
          var orig = {};
          if(p.value.length > 0 && p.value.indexOf("{") == 0){
            orig = dojo.fromJson(p.value);
          }
          var newValues = dojo.fromJson(v);
          for(var key in newValues){
            if(!orig[key]) orig[key] = {};
            for(var key2 in newValues[key]){
              orig[key][key2] = newValues[key][key2];
            }
          }
          v = dojo.toJson(orig);
        }
        p.value = v;
        OfflajnFireEvent(p, 'change');
      }
    }
    this.span.innerHTML = "The <b>"+value.replace(/^.*?_/,"").replace(/_/g," ")+" "+this.label+"</b> has been set.";

    if(this.dependency){
      window[this.dependency+'delay'] = true;
    }
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

/*
 * jQuery MiniColors: A tiny color picker built on jQuery
 *
 * Copyright Cory LaViska for A Beautiful Site, LLC. (http://www.abeautifulsite.net/)
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 */
if(jQuery) (function($) {

	// Defaults
	$.minicolors = {
		defaults: {
			animationSpeed: 50,
			animationEasing: 'swing',
			change: function() {OfflajnFireEvent(this, "change")},
			changeDelay: 0,
			control: 'hue',
			defaultValue: '',
			hide: null,
			hideSpeed: 100,
			inline: false,
			letterCase: 'lowercase',
			opacity: false,
			position: 'top left',
			show: null,
			showSpeed: 100,
			theme: 'default'
		}
	};

	// Public methods
	$.extend($.fn, {
		minicolors: function(method, data) {

			switch(method) {

				// Destroy the control
				case 'destroy':
					$(this).each( function() {
						destroy($(this));
					});
					return $(this);

				// Hide the color picker
				case 'hide':
					hide();
					return $(this);

				// Get/set opacity
				case 'opacity':
					// Getter
					if( data === undefined ) {
						// Getter
						return $(this).attr('data-opacity');
					} else {
						// Setter
						$(this).each( function() {
							updateFromInput($(this).attr('data-opacity', data));
						});
					}
					return $(this);

				// Get an RGB(A) object based on the current color/opacity
				case 'rgbObject':
					return rgbObject($(this), method === 'rgbaObject');

				// Get an RGB(A) string based on the current color/opacity
				case 'rgbString':
				case 'rgbaString':
					return rgbString($(this), method === 'rgbaString');

				// Get/set settings on the fly
				case 'settings':
					if( data === undefined ) {
						return $(this).data('minicolors-settings');
					} else {
						// Setter
						$(this).each( function() {
							var settings = $(this).data('minicolors-settings') || {};
							destroy($(this));
							$(this).minicolors($.extend(true, settings, data));
						});
					}
					return $(this);

				// Show the color picker
				case 'show':
					show( $(this).eq(0) );
					return $(this);

				// Get/set the hex color value
				case 'value':
					if( data === undefined ) {
						// Getter
						return $(this).val();
					} else {
						// Setter
						$(this).each( function() {
							updateFromInput($(this).val(data));
						});
					}
					return $(this);

				// Initializes the control
				default:
					if( method !== 'create' ) data = method;
					$(this).each( function() {
						init($(this), data);
					});
					return $(this);

			}

		}
	});

	// Initialize input elements
	function init(input, settings) {

		var minicolors = $('<div class="minicolors" />'),
			defaults = $.minicolors.defaults;

		// Do nothing if already initialized
		if( input.data('minicolors-initialized') ) return;

		// Handle settings
		settings = $.extend(true, {}, defaults, settings);

		// The wrapper
		minicolors
			.addClass('minicolors-theme-' + settings.theme)
			.toggleClass('minicolors-with-opacity', settings.opacity);

		// Custom positioning
		if( settings.position !== undefined ) {
			$.each(settings.position.split(' '), function() {
				minicolors.addClass('minicolors-position-' + this);
			});
		}

		// The input
		input
			.addClass('minicolors-input')
			.data('minicolors-initialized', true)
			.data('minicolors-settings', settings)
			.prop('size', 7)
			.wrap(minicolors)
			.after(
				'<div class="minicolors-panel minicolors-slider-' + settings.control + '">' +
					'<div class="minicolors-slider">' +
						'<div class="minicolors-picker"></div>' +
					'</div>' +
					'<div class="minicolors-opacity-slider">' +
						'<div class="minicolors-picker"></div>' +
					'</div>' +
					'<div class="minicolors-grid">' +
						'<div class="minicolors-grid-inner"></div>' +
						'<div class="minicolors-picker"><div></div></div>' +
					'</div>' +
					'<ul class="minicolors-recent-colors"><span></span></ul>' +
				'</div>'
			);

		// The swatch
		if( !settings.inline ) {
			input.after('<span class="minicolors-swatch"><span class="minicolors-swatch-color"></span></span>');
			input.next('.minicolors-swatch').on('click', function(event) {
				event.preventDefault();
				input.focus();
			});
		}

		// Prevent text selection in IE
		input.parent().find('.minicolors-panel').on('selectstart', function() { return false; }).end();

		// Inline controls
		if( settings.inline ) input.parent().addClass('minicolors-inline');

		updateFromInput(input, true);


		// Populate lastChange to prevent change event from firing initially
		input.data('minicolors-lastChange', {
			hex: input.val(),
			opacity: input.attr('data-opacity')
		});

	}

	// Returns the input back to its original state
	function destroy(input) {

		var minicolors = input.parent();

		// Revert the input element
		input
			.removeData('minicolors-initialized')
			.removeData('minicolors-settings')
			.removeProp('size')
			.removeClass('minicolors-input');

		// Remove the wrap and destroy whatever remains
		minicolors.before(input).remove();

	}


	// Test localStorage
	function lsTest(){
		var test = 'lsTest';
		try {
			localStorage.setItem(test, test);
			localStorage.removeItem(test);
			return true;
		} catch(e) {
			return false;
		}
	}

	// Shows the specified dropdown panel
	function show(input) {

		var minicolors = input.parent(),
			panel = minicolors.find('.minicolors-panel'),
			settings = input.data('minicolors-settings');

		// Do nothing if uninitialized, disabled, inline, or already open
		if( !input.data('minicolors-initialized') ||
			input.prop('disabled') ||
			minicolors.hasClass('minicolors-inline') ||
			minicolors.hasClass('minicolors-focus')
		) return;

		hide();

		// Add recent colors
		if( lsTest() ) {

			// Get recent colors
			var items = localStorage.getItem('layerslider.minicolors.recent');
				items = (!items || items == '') ? [] : items.split(';');

			// Add recent colors
			if(items.length > 0) {
				minicolors.find('ul').empty();

				for(var c = 0; c < items.length; c++) {
					minicolors.find('ul').append('<li data-color="'+items[c]+'"><span style="background:'+items[c]+';"></span></li>')
				}
			}
		}

		minicolors.addClass('minicolors-focus');
		panel
			.stop(true, true)
			.fadeIn(settings.showSpeed, function() {
				if( settings.show ) settings.show.call(input.get(0));
			});

	}

	// Hides all dropdown panels
	function hide(savecolor) {

		// Store recent color
		if(typeof savecolor !== "undefined" && savecolor === true) {
			var currInput = $('.minicolors-focus > input')
			if(currInput.length > 0 && currInput.val() !== '' && lsTest()) {

				// Get items
				var items = localStorage.getItem('layerslider.minicolors.recent');
					items = (!items || items == '') ? [] : items.split(';');

				// Add new if it changed
				if(items.length < 1 || items[0] !== currInput.val()) {
					items.unshift(currInput.val());
				}

				// Manage the maximum number of recent colors
				if(items.length > 8) { items.pop(); }

				// Save
				localStorage.setItem('layerslider.minicolors.recent', items.join(';'));
			}
		}

		$('.minicolors-input').each( function() {

			var input = $(this),
				settings = input.data('minicolors-settings'),
				minicolors = input.parent();

			// Don't hide inline controls
			if( settings.inline ) return;

			minicolors.find('.minicolors-panel').fadeOut(settings.hideSpeed, function() {
				if(minicolors.hasClass('minicolors-focus')) {
					// if( settings.hide ) settings.hide.call(input.get(0));
				}
				minicolors.removeClass('minicolors-focus');
			});

		});
	}

	// Moves the selected picker
	function move(target, event, animate) {

		var input = target.parents('.minicolors').find('.minicolors-input'),
			settings = input.data('minicolors-settings'),
			picker = target.find('[class$=-picker]'),
			offsetX = target.offset().left,
			offsetY = target.offset().top,
			x = Math.round(event.pageX - offsetX),
			y = Math.round(event.pageY - offsetY),
			duration = animate ? settings.animationSpeed : 0,
			wx, wy, r, phi;


		// Touch support
		if( event.originalEvent.changedTouches ) {
			x = event.originalEvent.changedTouches[0].pageX - offsetX;
			y = event.originalEvent.changedTouches[0].pageY - offsetY;
		}

		// Constrain picker to its container
		if( x < 0 ) x = 0;
		if( y < 0 ) y = 0;
		if( x > target.width() ) x = target.width();
		if( y > target.height() ) y = target.height();

		// Constrain color wheel values to the wheel
		if( target.parent().is('.minicolors-slider-wheel') && picker.parent().is('.minicolors-grid') ) {
			wx = 75 - x;
			wy = 75 - y;
			r = Math.sqrt(wx * wx + wy * wy);
			phi = Math.atan2(wy, wx);
			if( phi < 0 ) phi += Math.PI * 2;
			if( r > 75 ) {
				r = 75;
				x = 75 - (75 * Math.cos(phi));
				y = 75 - (75 * Math.sin(phi));
			}
			x = Math.round(x);
			y = Math.round(y);
		}

		// Move the picker
		if( target.is('.minicolors-grid') ) {
			picker
				.stop(true)
				.animate({
					top: y + 'px',
					left: x + 'px'
				}, duration, settings.animationEasing, function() {
					updateFromControl(input, target);
				});
		} else {
			picker
				.stop(true)
				.animate({
					top: y + 'px'
				}, duration, settings.animationEasing, function() {
					updateFromControl(input, target);
				});
		}
		input.attr('value', input.val());
	}

	// Sets the input based on the color picker values
	function updateFromControl(input, target) {

		function getCoords(picker, container) {

			var left, top;
			if( !picker.length || !container ) return null;
			left = picker.offset().left;
			top = picker.offset().top;

			return {
				x: left - container.offset().left + (picker.outerWidth() / 2),
				y: top - container.offset().top + (picker.outerHeight() / 2)
			};

		}

		var hue, saturation, brightness, x, y, r, phi,

			hex = input.val(),
			opacity = input.attr('data-opacity'),

			// Helpful references
			minicolors = input.parent(),
			settings = input.data('minicolors-settings'),
			swatch = minicolors.find('.minicolors-swatch'),

			// Panel objects
			grid = minicolors.find('.minicolors-grid'),
			slider = minicolors.find('.minicolors-slider'),
			opacitySlider = minicolors.find('.minicolors-opacity-slider'),

			// Picker objects
			gridPicker = grid.find('[class$=-picker]'),
			sliderPicker = slider.find('[class$=-picker]'),
			opacityPicker = opacitySlider.find('[class$=-picker]'),

			// Picker positions
			gridPos = getCoords(gridPicker, grid),
			sliderPos = getCoords(sliderPicker, slider),
			opacityPos = getCoords(opacityPicker, opacitySlider);

		// Handle colors
		if( target.is('.minicolors-grid, .minicolors-slider, .minicolors-opacity-slider') ) {

			// Determine HSB values
			switch(settings.control) {

				case 'wheel':
					// Calculate hue, saturation, and brightness
					x = (grid.width() / 2) - gridPos.x;
					y = (grid.height() / 2) - gridPos.y;
					r = Math.sqrt(x * x + y * y);
					phi = Math.atan2(y, x);
					if( phi < 0 ) phi += Math.PI * 2;
					if( r > 75 ) {
						r = 75;
						gridPos.x = 69 - (75 * Math.cos(phi));
						gridPos.y = 69 - (75 * Math.sin(phi));
					}
					saturation = keepWithin(r / 0.75, 0, 100);
					hue = keepWithin(phi * 180 / Math.PI, 0, 360);
					brightness = keepWithin(100 - Math.floor(sliderPos.y * (100 / slider.height())), 0, 100);
					hex = hsb2hex({
						h: hue,
						s: saturation,
						b: brightness
					});

					// Update UI
					slider.css('backgroundColor', hsb2hex({ h: hue, s: saturation, b: 100 }));
					break;

				case 'saturation':
					// Calculate hue, saturation, and brightness
					hue = keepWithin(parseInt(gridPos.x * (360 / grid.width()), 10), 0, 360);
					saturation = keepWithin(100 - Math.floor(sliderPos.y * (100 / slider.height())), 0, 100);
					brightness = keepWithin(100 - Math.floor(gridPos.y * (100 / grid.height())), 0, 100);
					hex = hsb2hex({
						h: hue,
						s: saturation,
						b: brightness
					});

					// Update UI
					slider.css('backgroundColor', hsb2hex({ h: hue, s: 100, b: brightness }));
					minicolors.find('.minicolors-grid-inner').css('opacity', saturation / 100);
					break;

				case 'brightness':
					// Calculate hue, saturation, and brightness
					hue = keepWithin(parseInt(gridPos.x * (360 / grid.width()), 10), 0, 360);
					saturation = keepWithin(100 - Math.floor(gridPos.y * (100 / grid.height())), 0, 100);
					brightness = keepWithin(100 - Math.floor(sliderPos.y * (100 / slider.height())), 0, 100);
					hex = hsb2hex({
						h: hue,
						s: saturation,
						b: brightness
					});

					// Update UI
					slider.css('backgroundColor', hsb2hex({ h: hue, s: saturation, b: 100 }));
					minicolors.find('.minicolors-grid-inner').css('opacity', 1 - (brightness / 100));
					break;

				default:
					// Calculate hue, saturation, and brightness
					hue = keepWithin(360 - parseInt(sliderPos.y * (360 / slider.height()), 10), 0, 360);
					saturation = keepWithin(Math.floor(gridPos.x * (100 / grid.width())), 0, 100);
					brightness = keepWithin(100 - Math.floor(gridPos.y * (100 / grid.height())), 0, 100);
					hex = hsb2hex({
						h: hue,
						s: saturation,
						b: brightness
					});

					// Update UI
					grid.css('backgroundColor', hsb2hex({ h: hue, s: 100, b: 100 }));
					break;

			}

			// Adjust case
			var rgb = hex2rgb(hex);
			if(input.minicolors('rgbObject').a < 1 && rgb) {
				input.val('rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + parseFloat(opacity) + ')');
			} else {
				input.val( convertCase(hex, settings.letterCase) );
			}

		}


		// Handle opacity
		if( target.is('.minicolors-opacity-slider')  ) {
			if( settings.opacity ) {
				opacity = parseFloat(1 - (opacityPos.y / opacitySlider.height())).toFixed(2);
			} else {
				opacity = 1;
			}
			if( settings.opacity ) input.attr('data-opacity', opacity);
		}

		// Set swatch color
		swatch.find('SPAN').css({
			backgroundColor: hex,
			opacity: opacity
		});

		// Handle change event
		doChange(input, hex, opacity);

	}

	// Sets the color picker values from the input
	function updateFromInput(input, preserveInputValue) {

		var hex, hexStr,
			hsb,
			rgbaArr,
			opacity, alphaVal,
			x, y, r, phi,

			// Helpful references
			minicolors = input.parent(),
			settings = input.data('minicolors-settings'),
			swatch = minicolors.find('.minicolors-swatch'),

			// Panel objects
			grid = minicolors.find('.minicolors-grid'),
			slider = minicolors.find('.minicolors-slider'),
			opacitySlider = minicolors.find('.minicolors-opacity-slider'),

			// Picker objects
			gridPicker = grid.find('[class$=-picker]'),
			sliderPicker = slider.find('[class$=-picker]'),
			opacityPicker = opacitySlider.find('[class$=-picker]');

		// RGBA value if any
		if(input.val().indexOf('rgb') != -1) {
			rgbaArr = input.val().split("(")[1].split(")")[0].split(",");
			hexStr = '#' + ("0" + parseInt(rgbaArr[0]).toString(16)).slice(-2);
			hexStr += '#' + ("0" + parseInt(rgbaArr[1]).toString(16)).slice(-2);
			hexStr += '#' + ("0" + parseInt(rgbaArr[2]).toString(16)).slice(-2);
			alphaVal = parseFloat(rgbaArr[3]);

		} else {
			if(input.val() == 'transparent') {
				hexStr = '#ffffff';
				alphaVal = 0;
			} else {
				hexStr = input.val();
				alphaVal = 1;
			}
		}

		// Determine hex/HSB values
		hex = convertCase(parseHex(hexStr, true), settings.letterCase);

		if( !hex ){
			hex = convertCase(parseHex(settings.defaultValue, true), settings.letterCase);
		}
		hsb = hex2hsb(hex);

		// Update input value
		if( !preserveInputValue ) input.val(hex);

		// Determine opacity value
		if( settings.opacity ) {
			// Get from data-opacity attribute and keep within 0-1 range
			opacity = alphaVal === '' ? 1 : keepWithin(parseFloat(alphaVal).toFixed(2), 0, 1);
			if( isNaN(opacity) ) opacity = 1;
			input.attr('data-opacity', opacity);
			swatch.find('SPAN').css('opacity', opacity);

			// Set opacity picker position
			y = keepWithin(opacitySlider.height() - (opacitySlider.height() * opacity), 0, opacitySlider.height());
			opacityPicker.css('top', y + 'px');
		}

		// Update swatch
		swatch.find('SPAN').css('backgroundColor', hex);

		// Determine picker locations
		switch(settings.control) {

			case 'wheel':
				// Set grid position
				r = keepWithin(Math.ceil(hsb.s * 0.75), 0, grid.height() / 2);
				phi = hsb.h * Math.PI / 180;
				x = keepWithin(75 - Math.cos(phi) * r, 0, grid.width());
				y = keepWithin(75 - Math.sin(phi) * r, 0, grid.height());
				gridPicker.css({
					top: y + 'px',
					left: x + 'px'
				});

				// Set slider position
				y = 150 - (hsb.b / (100 / grid.height()));
				if( hex === '' ) y = 0;
				sliderPicker.css('top', y + 'px');

				// Update panel color
				slider.css('backgroundColor', hsb2hex({ h: hsb.h, s: hsb.s, b: 100 }));
				break;

			case 'saturation':
				// Set grid position
				x = keepWithin((5 * hsb.h) / 12, 0, 150);
				y = keepWithin(grid.height() - Math.ceil(hsb.b / (100 / grid.height())), 0, grid.height());
				gridPicker.css({
					top: y + 'px',
					left: x + 'px'
				});

				// Set slider position
				y = keepWithin(slider.height() - (hsb.s * (slider.height() / 100)), 0, slider.height());
				sliderPicker.css('top', y + 'px');

				// Update UI
				slider.css('backgroundColor', hsb2hex({ h: hsb.h, s: 100, b: hsb.b }));
				minicolors.find('.minicolors-grid-inner').css('opacity', hsb.s / 100);

				break;

			case 'brightness':
				// Set grid position
				x = keepWithin((5 * hsb.h) / 12, 0, 150);
				y = keepWithin(grid.height() - Math.ceil(hsb.s / (100 / grid.height())), 0, grid.height());
				gridPicker.css({
					top: y + 'px',
					left: x + 'px'
				});

				// Set slider position
				y = keepWithin(slider.height() - (hsb.b * (slider.height() / 100)), 0, slider.height());
				sliderPicker.css('top', y + 'px');

				// Update UI
				slider.css('backgroundColor', hsb2hex({ h: hsb.h, s: hsb.s, b: 100 }));
				minicolors.find('.minicolors-grid-inner').css('opacity', 1 - (hsb.b / 100));
				break;

			default:
				// Set grid position
				x = keepWithin(Math.ceil(hsb.s / (100 / grid.width())), 0, grid.width());
				y = keepWithin(grid.height() - Math.ceil(hsb.b / (100 / grid.height())), 0, grid.height());
				gridPicker.css({
					top: y + 'px',
					left: x + 'px'
				});

				// Set slider position
				y = keepWithin(slider.height() - (hsb.h / (360 / slider.height())), 0, slider.height());
				sliderPicker.css('top', y + 'px');

				// Update panel color
				grid.css('backgroundColor', hsb2hex({ h: hsb.h, s: 100, b: 100 }));
				break;

		}
		input.attr('value', input.val());
	}

	// Runs the change and changeDelay callbacks
	function doChange(input, hex, opacity) {

		var settings = input.data('minicolors-settings'),
			lastChange = input.data('minicolors-lastChange');

		// Only run if it actually changed
		if( lastChange.hex !== hex || lastChange.opacity !== opacity ) {

			// Remember last-changed value
			input.data('minicolors-lastChange', {
				hex: hex,
				opacity: opacity
			});

			// Fire change event
			if( settings.change ) {
				if( settings.changeDelay ) {
					// Call after a delay
					clearTimeout(input.data('minicolors-changeTimeout'));
					input.data('minicolors-changeTimeout', setTimeout( function() {
						settings.change.call(input.get(0), hex, opacity);
					}, settings.changeDelay));
				} else {
					// Call immediately
					settings.change.call(input.get(0), hex, opacity);
				}
			}
			input.trigger('change').trigger('input');
		}

	}

	// Generates an RGB(A) object based on the input's value
	function rgbObject(input) {
		var hex = parseHex($(input).val(), true),
			rgb = hex2rgb(hex),
			opacity = $(input).attr('data-opacity');
		if( !rgb ) return null;
		if( opacity !== undefined ) $.extend(rgb, { a: parseFloat(opacity) });
		return rgb;
	}

	// Genearates an RGB(A) string based on the input's value
	function rgbString(input, alpha) {
		var hex = parseHex($(input).val(), true),
			rgb = hex2rgb(hex),
			opacity = $(input).attr('data-opacity');
		if( !rgb ) return null;
		if( opacity === undefined ) opacity = 1;
		if( alpha ) {
			return 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + parseFloat(opacity) + ')';
		} else {
			return 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
		}
	}

	// Converts to the letter case specified in settings
	function convertCase(string, letterCase) {
		return letterCase === 'uppercase' ? string.toUpperCase() : string.toLowerCase();
	}

	// Parses a string and returns a valid hex string when possible
	function parseHex(string, expand) {
		string = string.replace(/[^A-F0-9]/ig, '');
		if( string.length !== 3 && string.length !== 6 ) return '';
		if( string.length === 3 && expand ) {
			string = string[0] + string[0] + string[1] + string[1] + string[2] + string[2];
		}
		return '#' + string;
	}

	// Keeps value within min and max
	function keepWithin(value, min, max) {
		if( value < min ) value = min;
		if( value > max ) value = max;
		return value;
	}

	// Converts an HSB object to an RGB object
	function hsb2rgb(hsb) {
		var rgb = {};
		var h = Math.round(hsb.h);
		var s = Math.round(hsb.s * 255 / 100);
		var v = Math.round(hsb.b * 255 / 100);
		if(s === 0) {
			rgb.r = rgb.g = rgb.b = v;
		} else {
			var t1 = v;
			var t2 = (255 - s) * v / 255;
			var t3 = (t1 - t2) * (h % 60) / 60;
			if( h === 360 ) h = 0;
			if( h < 60 ) { rgb.r = t1; rgb.b = t2; rgb.g = t2 + t3; }
			else if( h < 120 ) {rgb.g = t1; rgb.b = t2; rgb.r = t1 - t3; }
			else if( h < 180 ) {rgb.g = t1; rgb.r = t2; rgb.b = t2 + t3; }
			else if( h < 240 ) {rgb.b = t1; rgb.r = t2; rgb.g = t1 - t3; }
			else if( h < 300 ) {rgb.b = t1; rgb.g = t2; rgb.r = t2 + t3; }
			else if( h < 360 ) {rgb.r = t1; rgb.g = t2; rgb.b = t1 - t3; }
			else { rgb.r = 0; rgb.g = 0; rgb.b = 0; }
		}
		return {
			r: Math.round(rgb.r),
			g: Math.round(rgb.g),
			b: Math.round(rgb.b)
		};
	}

	// Converts an RGB object to a hex string
	function rgb2hex(rgb) {
		var hex = [
			rgb.r.toString(16),
			rgb.g.toString(16),
			rgb.b.toString(16)
		];
		$.each(hex, function(nr, val) {
			if (val.length === 1) hex[nr] = '0' + val;
		});
		return '#' + hex.join('');
	}

	// Converts an HSB object to a hex string
	function hsb2hex(hsb) {
		return rgb2hex(hsb2rgb(hsb));
	}

	// Converts a hex string to an HSB object
	function hex2hsb(hex) {
		var hsb = rgb2hsb(hex2rgb(hex));
		if( hsb.s === 0 ) hsb.h = 360;
		return hsb;
	}

	// Converts an RGB object to an HSB object
	function rgb2hsb(rgb) {
		var hsb = { h: 0, s: 0, b: 0 };
		var min = Math.min(rgb.r, rgb.g, rgb.b);
		var max = Math.max(rgb.r, rgb.g, rgb.b);
		var delta = max - min;
		hsb.b = max;
		hsb.s = max !== 0 ? 255 * delta / max : 0;
		if( hsb.s !== 0 ) {
			if( rgb.r === max ) {
				hsb.h = (rgb.g - rgb.b) / delta;
			} else if( rgb.g === max ) {
				hsb.h = 2 + (rgb.b - rgb.r) / delta;
			} else {
				hsb.h = 4 + (rgb.r - rgb.g) / delta;
			}
		} else {
			hsb.h = -1;
		}
		hsb.h *= 60;
		if( hsb.h < 0 ) {
			hsb.h += 360;
		}
		hsb.s *= 100/255;
		hsb.b *= 100/255;
		return hsb;
	}

	// Converts a hex string to an RGB object
	function hex2rgb(hex) {
		hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
		return {
			r: hex >> 16,
			g: (hex & 0x00FF00) >> 8,
			b: (hex & 0x0000FF)
		};
	}

	// Handle events
	$(document)
		// Hide on clicks outside of the control
		.on('mousedown.minicolors touchstart.minicolors', function(event) {
			if( !$(event.target).parents().add(event.target).hasClass('minicolors') ) {
				hide(true);
			}
		})
		// Start moving
		.on('mousedown.minicolors touchstart.minicolors', '.minicolors-grid, .minicolors-slider, .minicolors-opacity-slider', function(event) {
			var target = $(this);
			event.preventDefault();
			$(document).data('minicolors-target', target);
			move(target, event, true);
		})
		// Move pickers
		.on('mousemove.minicolors touchmove.minicolors', function(event) {
			var target = $(document).data('minicolors-target');
			if( target ) move(target, event);
		})
		// Stop moving
		.on('mouseup.minicolors touchend.minicolors', function() {
			$(this).removeData('minicolors-target');
		})
		// Show panel when swatch is clicked
		.on('mousedown.minicolors touchstart.minicolors', '.minicolors-swatch', function(event) {
			var input = $(this).parent().find('.minicolors-input');
			event.preventDefault();
			show(input);
		})
		// Show on focus
		.on('focus.minicolors', '.minicolors-input', function() {
			var input = $(this);
			if( !input.data('minicolors-initialized') ) return;
			show(input);
		})
		// Fix hex on blur
		.on('blur.minicolors', '.minicolors-input', function() {
			var input = $(this),
				settings = input.data('minicolors-settings');
			if( !input.data('minicolors-initialized') ) return;

			// Parse Hex
			// input.val(parseHex(input.val(), true));

			// Is it blank?
			if( input.val() === '' ) input.val(parseHex(settings.defaultValue, true));

			// Adjust case
			// input.val( convertCase(input.val(), settings.letterCase) );

		})
		// Handle keypresses
		.on('keydown.minicolors', '.minicolors-input', function(event) {
			var input = $(this);
			if( !input.data('minicolors-initialized') ) return;
			switch(event.keyCode) {
				case 9: // tab
					hide(true);
					break;
				case 13: // enter
				case 27: // esc
					hide(true);
					input.blur();
					break;
			}
		})
		// Update on keyup
		.on('keyup.minicolors', '.minicolors-input', function() {
			var input = $(this);
			if( !input.data('minicolors-initialized') ) return;
			updateFromInput(input, true);
		})
		// Update on paste
		.on('paste.minicolors', '.minicolors-input', function() {
			var input = $(this);
			if( !input.data('minicolors-initialized') ) return;
			setTimeout( function() {
				updateFromInput(input, true);
			}, 1);
		})

		.on('click', '.minicolors-recent-colors li', function() {
			var input = jQuery(this).closest('.minicolors').find('input:first');
			var color = jQuery(this).data('color');
			var settings = input.data('minicolors-settings');
			input.val( color );
			updateFromInput(input, true);
			settings.change.call(input[0], color, false);
		});

})(jQuery);

/*
function OfflajnFireEvent(element,event){
    if ((document.createEventObject && !dojo.isIE) || (document.createEventObject && dojo.isIE && dojo.isIE < 9)){
      var evt = document.createEventObject();
      return element.fireEvent('on'+event,evt);
    }else{
      var evt = document.createEvent("HTMLEvents");
      evt.initEvent(event, true, true );
      return !element.dispatchEvent(evt);
    }
}
*/


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



dojo.declare("OfflajnRadioimg", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
   this.selected = -1;
	 this.init();
  },

  init: function() {
    this.hidden = dojo.byId(this.id);
    this.hidden.radioobj = this;
    dojo.connect(this.hidden, 'change', this, 'reset');
    this.container = dojo.byId('offlajnradioimgcontainer' + this.id);
    this.items = dojo.query('.radioelement', this.container);
    if(this.mode == "image") this.imgitems = dojo.query('.radioelement_img', this.container);
    dojo.forEach(this.items, function(item, i){
      if(this.hidden.value == this.values[i]) this.selected = i;
      dojo.connect(item, 'onclick', dojo.hitch(this, 'selectItem', i));
    }, this);

    this.reset();
  },

  reset: function(){
    var i = this.map[this.hidden.value];
    if(!i) i = 0;
    this.selectItem(i);
  },

  selectItem: function(i) {
    if(this.selected == i) {
      if(this.mode == "image") this.changeImage(i);
     return;
    }
    if(this.selected >= 0) dojo.removeClass(this.items[this.selected], 'selected');
    if(this.mode == "image") this.changeImage(i);
    this.selected = i;
    dojo.addClass(this.items[this.selected], 'selected');
    if(this.hidden.value != this.values[this.selected]){
      this.hidden.value = this.values[this.selected];
      OfflajnFireEvent(this.hidden, 'change');
    }
  },

  changeImage: function(i) {
    dojo.style(this.imgitems[this.selected], 'backgroundPosition', '0px 0px');
    dojo.style(this.imgitems[i], 'backgroundPosition', '0px -8px');
  }
});



dojo.declare("OfflajnImagemanager", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.map = {};
    this.init();
  },


  init: function() {
    this.btn = dojo.byId('offlajnimagemanager'+this.id);
    dojo.connect(this.btn, 'onclick', this, 'showWindow');

    this.selectedImage = "";
    this.hidden = dojo.byId(this.id);
    dojo.connect(this.hidden, 'change', this, 'reset');


    var path = this.hidden.value.split(this.folder);
    if (path[1]) {
      dojo.attr(this.hidden, 'value', this.folder + path[1]);
    }

    this.imgprev = dojo.query('.offlajnimagemanagerimg div', this.btn)[0];
    if(this.hidden.value != "") dojo.style(this.imgprev,'backgroundImage','url("'+this.siteurl+this.hidden.value+'")');
    this.images = new Array();
  },

  reset: function(){
    if(this.hidden.value != this.selectedImage){
      // fix for default value when Joomla is in a subfolder && param is in combine
      if (this.hidden.value.indexOf(this.siteurl) < 0) {
        dojo.attr(this.hidden, 'value', this.siteurl + this.hidden.value);
      }

      this.selectedImage = this.hidden.value;
      if(this.selectedImage == '') this.selectedImage = this.folder;
      this.saveImage();
      OfflajnFireEvent(this.hidden, 'change');
    }
  },

  showOverlay: function(){
    if(!this.overlayBG){
      this.overlayBG = dojo.create('div',{'class': 'blackBg'}, dojo.body());
    }
    dojo.removeClass(this.overlayBG, 'hide');
    dojo.style(this.overlayBG,{
      'opacity': 0.3
    });
  },

  showWindow: function(){
    this.showOverlay();
    if(!this.window){
      this.window = dojo.create('div', {'class': 'OfflajnWindow'}, dojo.body());
      var closeBtn = dojo.create('div', {'class': 'OfflajnWindowClose'}, this.window);
      dojo.connect(closeBtn, 'onclick', this, 'closeWindow');
      var inner = dojo.create('div', {'class': 'OfflajnWindowInner'}, this.window);
      dojo.create('h3', {'innerHTML': 'Image Manager'}, inner);
      dojo.create('div', {'class': 'OfflajnWindowLine'}, inner);
      var imgAreaOuter = dojo.create('div', {'class': 'OfflajnWindowImgAreaOuter'}, inner);
      this.imgArea = dojo.create('div', {'class': 'OfflajnWindowImgArea'}, imgAreaOuter);

      dojo.place(this.createFrame(''), this.imgArea);

      for(var i in this.imgs){
        if(i >=0 )
          dojo.place(this.createFrame(this.imgs[i]), this.imgArea);
      }

      var left = dojo.create('div', {'class': 'OfflajnWindowLeftContainer'}, inner);
      var right = dojo.create('div', {'class': 'OfflajnWindowRightContainer'}, inner);

      dojo.create('h4', {'innerHTML': 'Upload Your Image'}, left);

      this.uploadArea = dojo.create('form', {
        'action': 'index.php?option=offlajnupload&identifier='+this.identifier,
        'enctype': 'multipart/form-data',
        'method': 'post',
        'target': 'uploadiframe',
        'class': 'OfflajnWindowUploadareaForm',
        'innerHTML': 'Drag images here or<br />'
      }, left);
      this.input = dojo.create('input', {'name': 'img', 'type': 'file'}, this.uploadArea);
      dojo.create('button', {'innerHTML': 'Upload', 'type': 'submit'}, this.uploadArea);
      dojo.connect(this.input, 'onchange', this, 'submitUpload');

      dojo.create('h4', {'innerHTML': 'Currently Selected Image'}, right);

      this.selectedframe = dojo.create('div', {'class': 'OfflajnWindowImgFrame'}, right);
      this.selectedframe.img1 = dojo.create('div', {'class': 'OfflajnWindowImgFrameImg'}, this.selectedframe);
      this.selectedframe.img2 = dojo.create('img', {}, this.selectedframe);

      dojo.connect(this.selectedframe, 'onmouseenter', dojo.hitch(this,function(img){dojo.addClass(img, 'show');}, this.selectedframe.img2));
      dojo.connect(this.selectedframe, 'onmouseleave', dojo.hitch(this,function(img){dojo.removeClass(img, 'show');}, this.selectedframe.img2));

      this.desc = dojo.create('div', {'class': 'OfflajnWindowDescription', 'innerHTML': this.description}, right);

      var saveCont = dojo.create('div', {'class': 'OfflajnWindowSaveContainer'}, right);
      var savebtn = dojo.create('div', {'class': 'OfflajnWindowSave', 'innerHTML': 'SAVE'}, saveCont);
      dojo.connect(savebtn, 'onclick', this, 'saveImage');

      this.initUploadArea();

      this.scrollbar = new OfflajnScroller({
        'extraClass': 'multi-select',
        'selectbox': this.imgArea.parentNode,
        'content': this.imgArea,
        'scrollspeed' : 30
      });
    }

    var active = this.hidden.value.match(/[^\/]+\.(jpe?g|png|gif|bmp|svg)$/i);
    this.active = active ? active[0] : '';
    this.select({currentTarget: this.map[this.active]}); // init selected img on first open

    dojo.removeClass(this.window, 'hide');
    this.exit = dojo.connect(document, "onkeypress", this, "keyPressed");
    this.loadSavedImage();
  },

  submitUpload: function() {
    dojo.removeClass(this.uploadArea, 'over');

    if (this.input.files[0]) {
      this.uploadiframe = dojo.create('iframe', {'name': 'uploadiframe', 'style': 'display:none;'}, this.uploadArea);
      dojo.connect(this.uploadiframe, 'onload', this, 'alterUpload');
      this.uploadArea.submit();
    }
  },

  loadSavedImage: function() {
    var val = this.hidden.value;
    if(val == "") val = this.folder;
    val = val.replace(this.siteurl, "");
    if(val == '' || this.images[val] == undefined) return;
    var el = this.images[val];
    el.currentTarget = el.parentNode;
    this.select(el);
  },

  closeWindow: function(){
    dojo.addClass(this.window, 'hide');
    dojo.addClass(this.overlayBG, 'hide');
  },

  createFrame: function(im, folder){
    if(!folder) folder = this.folder;
    if(this.map[im]){
      dojo.place(this.map[im], this.map[im].parentNode, 'last');
      return this.map[im];
    }
    var frame = dojo.create('div', {'class': 'OfflajnWindowImgFrame'});
    dojo.create('div', {'class': 'OfflajnWindowImgFrameImg', 'style': (im != '' ? {
      'backgroundImage': 'url("'+this.root+folder+im+'")'
    }:{}) }, frame);
    if(im != '')
      var img = dojo.create('img', {'src': this.root+folder+im}, frame);

    var caption = im != '' ? im.replace(/^.*[\\\/]/, '') : 'No image';
    dojo.create('div', {'class': 'OfflajnWindowImgFrameCaption', 'innerHTML': "<span>"+caption+"</span>"}, frame);

    frame.selected = dojo.create('div', {'class': 'OfflajnWindowImgFrameSelected'}, frame);
    frame.img = im;

    this.map[im] = frame;
    if(im != ''){
      dojo.connect(frame, 'onmouseenter', dojo.hitch(this,function(img){dojo.addClass(img, 'show');}, img));
      dojo.connect(frame, 'onmouseleave', dojo.hitch(this,function(img){dojo.removeClass(img, 'show');}, img));
      this.images[folder+im] = img;
    }
    dojo.connect(frame, 'onclick', this, 'select');
    return frame;
  },

  select: function(e){
    var el = e.currentTarget;
    jQuery(el).addClass('active').siblings('.active').removeClass('active');
    this.active = el.img;
    dojo.style(this.selectedframe.img1, 'backgroundImage', 'url("'+this.root+this.folder+this.active+'")');
    dojo.attr(this.selectedframe.img2, 'src', this.root+this.folder+this.active);
    if (this.active) {
      if (this.selectedframe.img2.naturalWidth) this.updateDescription();
      else dojo.connect(this.selectedframe.img2, 'onload', this, 'updateDescription');
    } else {
      this.desc.innerHTML = '<h5>No image</h5>';
    }
    this.selectedImage = this.folder+this.active;
    dojo.addClass(this.selectedframe, 'active');
  },

  updateDescription: function() {
    this.desc.innerHTML = '<h5>'+ this.active +'</h5>'+
      'width: ' + this.selectedframe.img2.naturalWidth + 'px<br>' +
      'height: ' + this.selectedframe.img2.naturalHeight + 'px<br>';
  },

  initUploadArea: function(){
    dojo.connect(this.window, "ondragenter", this, function(e){
      jQuery(this.uploadArea).toggleClass('over', jQuery(e.target).closest('.OfflajnWindowUploadareaForm').length > 0);
    });
  },

  changeFrameImg: function(frame, im, folder){
    if(!folder) folder = this.folder;
    dojo.attr(dojo.query("img", frame)[0], 'src', this.root+folder+im+"?"+new Date().getTime());
    dojo.style(dojo.query(".OfflajnWindowImgFrameImg", frame)[0], {
      'backgroundImage': 'url("'+this.root+folder+im+"?"+new Date().getTime()+'")'
    });
  },

  alterUpload: function(){
    var data = jQuery(this.uploadiframe).contents().find('body').html();
    jQuery(this.uploadiframe).remove();
    if (!data) return;
    var r = JSON.parse(data);
    if(r.err){
      alert(r.err);
      return;
    }else if(r.name){
      var frame = this.createFrame(r.name);
      var caption = dojo.query('.OfflajnWindowImgFrameCaption', frame)[0];
      frame.progress = dojo.create('div', {'class':'progress', 'style' : {'width':(dojo.position(caption).w-2)+'px'} }, caption, 'first');
      dojo.place(frame, this.imgArea);
      this.scrollbar.scrollReInit();
      this.scrollbar.goToBottom();
      setTimeout(dojo.hitch(this,function(p){
        dojo.animateProperty({
          node: p,
          duration: 300,
          properties: {
            opacity : 0
          }
        }).play();
      },frame.progress),1000);
    }
  },

  keyPressed: function(e) {
    if(e.keyCode == 27) {
      this.closeWindow();
      dojo.disconnect(this.exit);
    }
  },

  saveImage: function() {
    //dojo.style(this.imgprev,'backgroundImage', 'url("'+this.root+this.selectedImage+'")');
    dojo.style(this.imgprev,'backgroundImage', 'url("'+this.selectedImage+'")');
    if(this.selectedImage != this.hidden.value) {
      this.closeWindow();
      if(this.folder == this.selectedImage) this.selectedImage = "";
      this.hidden.value = this.siteurl + this.selectedImage;
      OfflajnFireEvent(this.hidden, 'change');
    }
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



dojo.declare("OfflajnRadio", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
   this.selected = -1;
	 this.init();
  },

  init: function() {
    this.hidden = dojo.byId(this.id);
    this.hidden.radioobj = this;
    dojo.connect(this.hidden, 'change', this, 'reset');
    this.container = dojo.byId('offlajnradiocontainer' + this.id);
    this.items = dojo.query('.radioelement', this.container);
    if(this.mode == "image") this.imgitems = dojo.query('.radioelement_img', this.container);
    dojo.forEach(this.items, function(item, i){
      if(this.hidden.value == this.values[i]) this.selected = i;
      dojo.connect(item, 'onclick', dojo.hitch(this, 'selectItem', i));
    }, this);

    this.reset();
  },

  reset: function(){
    var i = this.map[this.hidden.value];
    if(!i) i = 0;
    this.selectItem(i);
  },

  selectItem: function(i) {
    if(this.selected == i) {
      if(this.mode == "image") this.changeImage(i);
     return;
    }
    if(this.selected >= 0) dojo.removeClass(this.items[this.selected], 'selected');
    if(this.mode == "image") this.changeImage(i);
    this.selected = i;
    dojo.addClass(this.items[this.selected], 'selected');
    if(this.hidden.value != this.values[this.selected]){
      this.hidden.value = this.values[this.selected];
      OfflajnFireEvent(this.hidden, 'change');
    }
  },

  changeImage: function(i) {
    dojo.style(this.imgitems[this.selected], 'backgroundPosition', '0px 0px');
    dojo.style(this.imgitems[i], 'backgroundPosition', '0px -8px');
  }
});


/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojo.window"]){
dojo._hasResource["dojo.window"]=true;
dojo.provide("dojo.window");
dojo.getObject("window",true,dojo);
dojo.window.getBox=function(){
var _1=(dojo.doc.compatMode=="BackCompat")?dojo.body():dojo.doc.documentElement;
var _2=dojo._docScroll();
return {w:_1.clientWidth,h:_1.clientHeight,l:_2.x,t:_2.y};
};
dojo.window.get=function(_3){
if(dojo.isIE&&window!==document.parentWindow){
_3.parentWindow.execScript("document._parentWindow = window;","Javascript");
var _4=_3._parentWindow;
_3._parentWindow=null;
return _4;
}
return _3.parentWindow||_3.defaultView;
};
dojo.window.scrollIntoView=function(_5,_6){
try{
_5=dojo.byId(_5);
var _7=_5.ownerDocument||dojo.doc,_8=_7.body||dojo.body(),_9=_7.documentElement||_8.parentNode,_a=dojo.isIE,_b=dojo.isWebKit;
if((!(dojo.isMoz||_a||_b||dojo.isOpera)||_5==_8||_5==_9)&&(typeof _5.scrollIntoView!="undefined")){
_5.scrollIntoView(false);
return;
}
var _c=_7.compatMode=="BackCompat",_d=(_a>=9&&_5.ownerDocument.parentWindow.frameElement)?((_9.clientHeight>0&&_9.clientWidth>0&&(_8.clientHeight==0||_8.clientWidth==0||_8.clientHeight>_9.clientHeight||_8.clientWidth>_9.clientWidth))?_9:_8):(_c?_8:_9),_e=_b?_8:_d,_f=_d.clientWidth,_10=_d.clientHeight,rtl=!dojo._isBodyLtr(),_11=_6||dojo.position(_5),el=_5.parentNode,_12=function(el){
return ((_a<=6||(_a&&_c))?false:(dojo.style(el,"position").toLowerCase()=="fixed"));
};
if(_12(_5)){
return;
}
while(el){
if(el==_8){
el=_e;
}
var _13=dojo.position(el),_14=_12(el);
if(el==_e){
_13.w=_f;
_13.h=_10;
if(_e==_9&&_a&&rtl){
_13.x+=_e.offsetWidth-_13.w;
}
if(_13.x<0||!_a){
_13.x=0;
}
if(_13.y<0||!_a){
_13.y=0;
}
}else{
var pb=dojo._getPadBorderExtents(el);
_13.w-=pb.w;
_13.h-=pb.h;
_13.x+=pb.l;
_13.y+=pb.t;
var _15=el.clientWidth,_16=_13.w-_15;
if(_15>0&&_16>0){
_13.w=_15;
_13.x+=(rtl&&(_a||el.clientLeft>pb.l))?_16:0;
}
_15=el.clientHeight;
_16=_13.h-_15;
if(_15>0&&_16>0){
_13.h=_15;
}
}
if(_14){
if(_13.y<0){
_13.h+=_13.y;
_13.y=0;
}
if(_13.x<0){
_13.w+=_13.x;
_13.x=0;
}
if(_13.y+_13.h>_10){
_13.h=_10-_13.y;
}
if(_13.x+_13.w>_f){
_13.w=_f-_13.x;
}
}
var l=_11.x-_13.x,t=_11.y-Math.max(_13.y,0),r=l+_11.w-_13.w,bot=t+_11.h-_13.h;
if(r*l>0){
var s=Math[l<0?"max":"min"](l,r);
if(rtl&&((_a==8&&!_c)||_a>=9)){
s=-s;
}
_11.x+=el.scrollLeft;
el.scrollLeft+=s;
_11.x-=el.scrollLeft;
}
if(bot*t>0){
_11.y+=el.scrollTop;
el.scrollTop+=Math[t<0?"max":"min"](t,bot);
_11.y-=el.scrollTop;
}
el=(el!=_e)&&!_14&&el.parentNode;
}
}
catch(error){
console.error("scrollIntoView: "+error);
_5.scrollIntoView(false);
}
};
}

dojo.require("dojo.window");

dojo.declare("MiniFontConfigurator", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    window.loadedFont = window.loadedFont || {};
    this.init();
  },


  init: function() {
    this.btn = dojo.byId(this.id+'change');
    dojo.connect(this.btn, 'onclick', this, 'showWindow');
    this.settings = dojo.clone(this.origsettings);
    this.hidden = dojo.byId(this.id);
    dojo.connect(this.hidden, 'onchange', this, 'reset');
    this.reset();
  },

  reset: function(){
    if(this.hidden.value == '') this.hidden.value = dojo.toJson(this.settings);
    if(this.hidden.value != dojo.toJson(this.settings)){
      var newsettings = {};
      try{
        newsettings = dojo.fromJson(this.hidden.value.replace(/\\"/g, '"'));
        if(dojo.isArray(newsettings)){
          newsettings = {};
        }
      }catch(e){
        this.hidden.value = dojo.toJson(newsettings);
      }
      for(var s in this.origsettings){
        if(!newsettings[s]){
          newsettings[s] = this.origsettings[s];
        }
      }
      this.settings = this.origsettings = newsettings;
    }
  },

  showOverlay: function(){
    if(!this.overlayBG){
      this.overlayBG = dojo.create('div',{'class': 'blackBg'}, dojo.body());
    }
    dojo.removeClass(this.overlayBG, 'hide');
    dojo.style(this.overlayBG,{
      'opacity': 0.3
    });
  },

  showWindow: function(e){
    dojo.stopEvent(e);
    this.showOverlay();
    if(!this.window){
      this.window = dojo.create('div', {'class': 'OfflajnWindowFont mini'}, dojo.body());
      var closeBtn = dojo.create('div', {'class': 'OfflajnWindowClose'}, this.window);
      dojo.connect(closeBtn, 'onclick', this, 'closeWindow');
      var inner = dojo.create('div', {'class': 'OfflajnWindowInner'}, this.window);
      var h3 = dojo.create('h3', {'innerHTML': 'Font selector'+this.elements.tab['html']}, inner);

      this.reset = dojo.create('div', {'class': 'offlajnfont_reset hasOfflajnTip', 'tooltippos': 'T','title' : 'It will clear the settings on the current tab.', 'innerHTML': '<div class="offlajnfont_reset_img"></div>'}, h3);
      dojo.global.toolTips.connectToolTips(h3);
      dojo.connect(this.reset, 'onclick', this, 'resetValues');

      this.tab = dojo.byId(this.id+'tab');

      dojo.connect(this.tab, 'change', this, 'changeTab');

      dojo.create('div', {'class': 'OfflajnWindowLine'}, inner);
      var fields = dojo.create('div', {'class': 'OfflajnWindowFields'}, inner);


      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Type<br />'+this.elements.type['html']}, fields);
      this.type = dojo.byId(this.elements.type.id);

      this.familyc = dojo.create('div', {'class': 'OfflajnWindowField'}, fields);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Size<br />'+this.elements.size['html']}, fields);
      this.size = dojo.byId(this.elements.size['id']);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Color<br />'+this.elements.color['html']}, fields);
      this.color = dojo.byId(this.elements.color['id']);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Weight<br />'+this.elements.textdecor['html']}, fields);
      this.weight = dojo.byId(this.elements.textdecor.id);

			jQuery(this.weight.parentNode.parentNode)
				.on("mouseenter", ".listelement", jQuery.proxy(function(e) {
					var weight = (jQuery(e.target).index() + 1) * 100;
					this.tester.style.fontWeight = weight;
				}, this))
				.on("mouseleave", ".offlajnlistelements", jQuery.proxy(this, "changeWeight"));

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Decoration<br />'
				+this.elements.italic['html']+this.elements.underline['html']+this.elements.linethrough['html']+this.elements.uppercase['html']}, fields);
      this.italic = dojo.byId(this.elements.italic['id']);
      this.underline = dojo.byId(this.elements.underline['id']);
			this.linethrough = dojo.byId(this.elements.linethrough['id']);
			this.uppercase = dojo.byId(this.elements.uppercase['id']);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Align<br />'+this.elements.align['html']}, fields);
      this.align = dojo.byId(this.elements.align['id']);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Alternative font<br />'+this.elements.afont['html']}, fields);
      this.afont = dojo.byId(this.elements.afont['id']);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Text shadow<br />'+this.elements.tshadow['html']}, fields);
      this.tshadow = dojo.byId(this.elements.tshadow['id']);

      dojo.create('div', {'class': 'OfflajnWindowField', 'innerHTML': 'Line height<br />'+this.elements.lineheight['html']}, fields);
      this.lineheight = dojo.byId(this.elements.lineheight['id']);

      dojo.create('div', {'class': 'OfflajnWindowTester', 'innerHTML': '<span>Grumpy wizards make toxic brew for the evil Queen and Jack.</span>'}, inner);
      this.tester = dojo.query('.OfflajnWindowTester span', inner)[0];
      var saveCont = dojo.create('div', {'class': 'OfflajnWindowSaveContainer'}, inner);
      var savebtn = dojo.create('div', {'class': 'OfflajnWindowSave', 'innerHTML': 'SAVE'}, saveCont);
      dojo.connect(savebtn, 'onclick', this, 'save');

      eval(this.script);


      dojo.connect(this.type, 'change', this, 'changeType');
      dojo.connect(this.size, 'change', dojo.hitch(this, 'changeSet', 'size'));
      dojo.connect(this.size, 'change', this, 'changeSize');
      dojo.connect(this.color, 'change', dojo.hitch(this, 'changeSet', 'color'));
      dojo.connect(this.color, 'change', this, 'changeColor');
      dojo.connect(this.weight, 'change', dojo.hitch(this, 'changeSet', 'textdecor'));
      dojo.connect(this.weight, 'change', this, 'changeWeight');
      dojo.connect(this.italic, 'change', dojo.hitch(this, 'changeSet', 'italic'));
      dojo.connect(this.italic, 'change', this, 'changeItalic');
      dojo.connect(this.underline, 'change', dojo.hitch(this, 'changeSet', 'underline'));
      dojo.connect(this.underline, 'change', this, 'changeUnderline');
      dojo.connect(this.linethrough, 'change', dojo.hitch(this, 'changeSet', 'linethrough'));
      dojo.connect(this.linethrough, 'change', this, 'changeLinethrough');
      dojo.connect(this.uppercase, 'change', dojo.hitch(this, 'changeSet', 'uppercase'));
      dojo.connect(this.uppercase, 'change', this, 'changeUppercase');
      dojo.connect(this.afont, 'change', dojo.hitch(this, 'changeSet', 'afont'));
      dojo.connect(this.afont, 'change', this, 'changeFamily');
      dojo.connect(this.align, 'change', dojo.hitch(this, 'changeSet', 'align'));
      dojo.connect(this.align, 'change', this, 'changeAlign');
      dojo.connect(this.tshadow, 'change', dojo.hitch(this, 'changeSet', 'tshadow'));
      dojo.connect(this.tshadow, 'change', this, 'changeTshadow');
      dojo.connect(this.lineheight, 'change', dojo.hitch(this, 'changeSet', 'lineheight'));
      dojo.connect(this.lineheight, 'change', this, 'changeLineheight');

      dojo.addOnLoad(this, function(){
        this.changeTab();
        this.changeType();
      });
      this.changeType();
      this.refreshFont();
			/*
			//family preview on hover
			jQuery(this.type).parents(".OfflajnWindowField").next()
				.on("mouseenter", ".listelement", jQuery.proxy(function(e) {
					this.changeFamily(e.target.firstChild.nodeValue);
				}, this))
				.on("mouseleave", ".offlajnlistelements", jQuery.proxy(this, "changeFamily"));
			*/
    }else{
      this.settings = dojo.fromJson(this.hidden.value.replace(/\\"/g, '"'));
      this.loadSettings();
    }
    dojo.removeClass(this.window, 'hide');
    this.exit = dojo.connect(document, "onkeypress", this, "keyPressed");
  },

  closeWindow: function(){
    dojo.addClass(this.window, 'hide');
    dojo.addClass(this.overlayBG, 'hide');
  },

  save: function(){
    this.hidden.value = dojo.toJson(this.settings);
    this.closeWindow();
  },

  loadSettings: function(){
    if(this.defaultTab!=this.t){
      this._loadSettings(this.defaultTab, true);
    }
    this._loadSettings(this.t, false);
    this.refreshFont();
  },

  _loadSettings: function(tab, def){
    var set = this.settings[tab];
    for(s in set){
      if(this[s] && (!def || def && !this.settings[this.t][s])){
        this.changeHidden(this[s], set[s]);
      }
    }
  },

  resetValues: function() {
    if(this.t != this.defaultTab) {
      this.settings[this.t] = {};
      this.loadSettings();
    }
  },

  loadFamily: function(e){
    dojo.stopEvent(e);
    var list = this.family.listobj;

    this.maxIteminWindow = parseInt(list.scrollbar.windowHeight/list.lineHeight)+1;
//    this.loadFamilyScroll();
//    list.scrollbar.onScroll = dojo.hitch(this, 'loadFamilyScroll');
  },

  loadFamilyScroll: function(){
    var set = this.settings[this.t];
    var list = this.family.listobj;
    var start = parseInt(list.scrollbar.curr*-1/list.lineHeight);
    for(var i = start; i <= start+this.maxIteminWindow && i < list.list.length; i++){
      var item = list.list[i];
      var option = list.options[i].value;
      this.loadGoogleFont(set.subset, option);
      dojo.style(item, 'fontFamily', "'"+option+"'");
    }
  },

  loadGoogleFont: function(subset, family){
    var hash = subset + family;
    if (!window.loadedFont[hash]) {
      window.loadedFont[hash] = true;
			dojo.create('link', {
				rel: 'stylesheet',
				type: 'text/css',
				href: '//fonts.googleapis.com/css?family='+family+':100,200,300,400,500,600,700,800,900&subset='+subset
			}, dojo.body());
			/*
      setTimeout(function(){
        dojo.create('link', {rel:'stylesheet', type: 'text/css', href: 'http://fonts.googleapis.com/css?family='+family+':100,200,300,400,500,600,700,800,900&subset='+subset}, dojo.body())
      },300);
			*/
    }
  },

  changeType: function(e){
    if(e){
      var obj = e.target.listobj;
      if(obj.map[obj.hidden.value] != obj.hidden.selectedIndex) return;
    }
    var set = this.settings[this.t];
    set.type = this.type.value;
    if(!this.elements.type[set.type]){
      if(!this.family){
        this.familyc.innerHTML = 'Family<br />'+this.elements.type['latin']['html'];
        this.family = dojo.byId(this.elements.type['latin']['id']);
        eval(this.elements.type['latin']['script']);
      }
      dojo.addOnLoad(this, function(){
        dojo.style(this.family.listobj.container,'visibility', 'hidden');
      });
      set.family = '';
      this.changeFamily();
      return;
    }
    this.familyc.innerHTML = 'Family<br />'+this.elements.type[set.type]['html'];
    this.family = dojo.byId(this.elements.type[set.type]['id']);

    dojo.connect(this.family, 'change', dojo.hitch(this, 'changeSet', 'family'));
    dojo.connect(this.family, 'click', this, 'loadFamily');
    dojo.connect(this.family, 'change', this, 'refreshFont');
    eval(this.elements.type[set.type]['script']);
    if(set.family){
      dojo.addOnLoad(this, function(){
        var set = this.settings[this.t];
        this.changeHidden(this.family, set.family);
      });
    }

		this.changeFamily();
		set.subset = this.type.value
  },

  changeSet: function(name, e){
    var set = this.settings[this.t];
    set[name] = e.target.value;
  },

  refreshFont: function(){
    var set = this.settings[this.t];
    if(this.weight) this.changeWeight();
    if(this.italic) this.changeItalic();
    if(this.underline) this.changeUnderline();
		if(this.linethrough) this.changeLinethrough();
		if(this.uppercase) this.changeUppercase();
    this.changeFamily();
    if(this.size) this.changeSize();
    if(this.color) this.changeColor();
    if(this.align) this.changeAlign();
    if(this.tshadow) this.changeTshadow();
    if(this.lineheight) this.changeLineheight();
  },

  changeWeight: function(e){
    dojo.style(this.tester, 'fontWeight', this.weight.value);
  },

  changeItalic: function(){
    dojo.style(this.tester, 'fontStyle', (parseInt(this.italic.value) ? 'italic' : 'normal'));
  },

  changeUnderline: function(){
		if (parseInt(this.linethrough.value) && parseInt(this.underline.value)) {
			jQuery(this.linethrough).prev()[0].click();
		}
    dojo.style(this.tester, 'textDecoration', (parseInt(this.underline.value) ? 'underline' : 'none'));
  },

  changeLinethrough: function(){
		if (parseInt(this.linethrough.value) && parseInt(this.underline.value)) {
			jQuery(this.underline).prev()[0].click();
		}
    dojo.style(this.tester, 'textDecoration', (parseInt(this.linethrough.value) ? 'line-through' : 'none'));
  },

  changeUppercase: function(){
    dojo.style(this.tester, 'textTransform', (parseInt(this.uppercase.value) ? 'uppercase' : 'none'));
  },

	prevFamily: '',

  changeFamily: function(family){
    var set = this.settings[this.t];
    var f = '';
    if(this.family && set.type != '0'){
      if (family === undefined) family = this.family.value;
      f = "'"+family+"'" + this.prevFamily;
      family && this.loadGoogleFont(set.subset, family);
    }

    if(this.afont){
      var afont = this.afont.value.split('||');
      if(afont[0] != '' && parseInt(afont[1])){
        if(f != '') f+=',';
        f+=afont[0];
      }
    }

		this.updateWeight();

    dojo.style(this.tester, 'fontFamily', f);
		if (this.family) this.prevFamily = ",'"+this.family.value+"'";
  },

	updateWeight: function() {
		this.weight.listobj.initSelectBox();
    var index = (this.settings[this.t].textdecor || this.settings[this.defaultTab].textdecor) / 100 - 1;

		if (!this.type.selectedIndex) {
			jQuery(".listelement", this.weight.listobj.selectbox).css('display', '');
			this.weight.listobj.selectbox.h = this.weight.listobj.lineHeight * this.weight.options.length;
		} else if (this.family && this.family.options) {
			var b = this.family.options[this.family.selectedIndex].text.match(/>(.+)</);
			if (b) {
				var $option = jQuery(".listelement", this.weight.listobj.selectbox).css('display', 'none');
				var i, map = this.weight.listobj.map, weight = b[1].split(',');
				for (i = 0; i < weight.length; i++)
					$option[ map[ weight[i] ] ].style.display = '';
				this.weight.listobj.selectbox.h = this.weight.listobj.lineHeight * weight.length;
        if ($option[index].style.display == 'none') index = 3; // set to normal if weight not exists
			}
		}
    this.weight.listobj.setSelected(index);
    this.weight.listobj.selectbox.style.overflow = 'hidden';
	},

  changeSize: function(){
    dojo.style(this.tester, 'fontSize', this.size.value.replace('||', '') );
  },

  changeColor: function(){
    dojo.style(this.tester, 'color', this.color.value);
		var rgb = this.color.value.match(/^#([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})|rgba\((\d+),\s*(\d+),\s*(\d+),\s*\d\.?\d*\)$/i);
		if (!rgb) return;
    var brightness = rgb[1] === undefined ? Math.round((rgb[4] * 299 + rgb[5] * 587 + rgb[6] * 114) / 1000) :
			Math.round((parseInt(rgb[1],16) * 299 + parseInt(rgb[2],16) * 587 + parseInt(rgb[3],16) * 114) / 1000);
    (brightness > 125) ? dojo.style(this.tester.parentNode, 'backgroundColor', '#555555') : dojo.style(this.tester.parentNode, 'backgroundColor', '#fdfdfd');
  },

  changeAlign: function(){
    dojo.style(this.tester.parentNode, 'textAlign', this.align.value );
  },

  changeTshadow: function(){
    var s = this.tshadow.value.replace(/\|\|/g, '').split('|*|');
    var shadow = 'none';
    if(parseInt(s[4])){
      s[4] = '';
      shadow = s.join(' ');
    }
    dojo.style(this.tester, 'textShadow', shadow);
  },

  changeLineheight: function(){
    dojo.style(this.tester, 'lineHeight', this.lineheight.value);
  },

  changeTab: function(){
    var radio = this.tab.radioobj;
    this.t = this.tab.value;
    if(this.t != this.defaultTab){
      dojo.style(this.reset,'display','block');
    }else{
      dojo.style(this.reset,'display','none');
    }
    this.loadSettings();
  },

  changeHidden: function(el, value){
    if(el.value == value) return;
    el.value = value;
    OfflajnFireEvent(el, 'change');
  },

  keyPressed: function(e) {
    if(e.keyCode == 27) {
      this.closeWindow();
      dojo.disconnect(this.exit);
    }
  }
});

OfflajnFont_arabic = [{"value":"Amiri","text":"Amiri<b>400,700<\/b>"},{"value":"Lateef","text":"Lateef<b>400<\/b>"},{"value":"Scheherazade","text":"Scheherazade<b>400,700<\/b>"}];
OfflajnFont_bengali = [{"value":"Hind Siliguri","text":"Hind Siliguri<b>300,400,500,600,700<\/b>"}];
OfflajnFont_cyrillic = [{"value":"Andika","text":"Andika<b>400<\/b>"},{"value":"Anonymous Pro","text":"Anonymous Pro<b>400,700<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Bad Script","text":"Bad Script<b>400<\/b>"},{"value":"Comfortaa","text":"Comfortaa<b>300,400,700<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Cuprum","text":"Cuprum<b>400,700<\/b>"},{"value":"Didact Gothic","text":"Didact Gothic<b>400<\/b>"},{"value":"EB Garamond","text":"EB Garamond<b>400<\/b>"},{"value":"Exo 2","text":"Exo 2<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Fira Mono","text":"Fira Mono<b>400,700<\/b>"},{"value":"Fira Sans","text":"Fira Sans<b>300,400,500,700<\/b>"},{"value":"Forum","text":"Forum<b>400<\/b>"},{"value":"Istok Web","text":"Istok Web<b>400,700<\/b>"},{"value":"Jura","text":"Jura<b>300,400,500,600<\/b>"},{"value":"Kelly Slab","text":"Kelly Slab<b>400<\/b>"},{"value":"Kurale","text":"Kurale<b>400<\/b>"},{"value":"Ledger","text":"Ledger<b>400<\/b>"},{"value":"Lobster","text":"Lobster<b>400<\/b>"},{"value":"Lora","text":"Lora<b>400,700<\/b>"},{"value":"Marck Script","text":"Marck Script<b>400<\/b>"},{"value":"Marmelad","text":"Marmelad<b>400<\/b>"},{"value":"Merriweather","text":"Merriweather<b>300,400,700,900<\/b>"},{"value":"Neucha","text":"Neucha<b>400<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Oranienbaum","text":"Oranienbaum<b>400<\/b>"},{"value":"PT Mono","text":"PT Mono<b>400<\/b>"},{"value":"PT Sans","text":"PT Sans<b>400,700<\/b>"},{"value":"PT Sans Caption","text":"PT Sans Caption<b>400,700<\/b>"},{"value":"PT Sans Narrow","text":"PT Sans Narrow<b>400,700<\/b>"},{"value":"PT Serif","text":"PT Serif<b>400,700<\/b>"},{"value":"PT Serif Caption","text":"PT Serif Caption<b>400<\/b>"},{"value":"Philosopher","text":"Philosopher<b>400,700<\/b>"},{"value":"Play","text":"Play<b>400,700<\/b>"},{"value":"Playfair Display","text":"Playfair Display<b>400,700,900<\/b>"},{"value":"Playfair Display SC","text":"Playfair Display SC<b>400,700,900<\/b>"},{"value":"Poiret One","text":"Poiret One<b>400<\/b>"},{"value":"Press Start 2P","text":"Press Start 2P<b>400<\/b>"},{"value":"Prosto One","text":"Prosto One<b>400<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Rubik","text":"Rubik<b>300,400,500,700,900<\/b>"},{"value":"Rubik Mono One","text":"Rubik Mono One<b>400<\/b>"},{"value":"Rubik One","text":"Rubik One<b>400<\/b>"},{"value":"Ruslan Display","text":"Ruslan Display<b>400<\/b>"},{"value":"Russo One","text":"Russo One<b>400<\/b>"},{"value":"Scada","text":"Scada<b>400,700<\/b>"},{"value":"Seymour One","text":"Seymour One<b>400<\/b>"},{"value":"Stalinist One","text":"Stalinist One<b>400<\/b>"},{"value":"Tenor Sans","text":"Tenor Sans<b>400<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"},{"value":"Ubuntu","text":"Ubuntu<b>300,400,500,700<\/b>"},{"value":"Ubuntu Condensed","text":"Ubuntu Condensed<b>400<\/b>"},{"value":"Ubuntu Mono","text":"Ubuntu Mono<b>400,700<\/b>"},{"value":"Underdog","text":"Underdog<b>400<\/b>"},{"value":"Yeseva One","text":"Yeseva One<b>400<\/b>"}];
OfflajnFont_cyrillic_ext = [{"value":"Andika","text":"Andika<b>400<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Comfortaa","text":"Comfortaa<b>300,400,700<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Didact Gothic","text":"Didact Gothic<b>400<\/b>"},{"value":"EB Garamond","text":"EB Garamond<b>400<\/b>"},{"value":"Fira Mono","text":"Fira Mono<b>400,700<\/b>"},{"value":"Fira Sans","text":"Fira Sans<b>300,400,500,700<\/b>"},{"value":"Forum","text":"Forum<b>400<\/b>"},{"value":"Istok Web","text":"Istok Web<b>400,700<\/b>"},{"value":"Jura","text":"Jura<b>300,400,500,600<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Oranienbaum","text":"Oranienbaum<b>400<\/b>"},{"value":"PT Mono","text":"PT Mono<b>400<\/b>"},{"value":"PT Sans","text":"PT Sans<b>400,700<\/b>"},{"value":"PT Sans Caption","text":"PT Sans Caption<b>400,700<\/b>"},{"value":"PT Sans Narrow","text":"PT Sans Narrow<b>400,700<\/b>"},{"value":"PT Serif","text":"PT Serif<b>400,700<\/b>"},{"value":"PT Serif Caption","text":"PT Serif Caption<b>400<\/b>"},{"value":"Play","text":"Play<b>400,700<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"},{"value":"Ubuntu","text":"Ubuntu<b>300,400,500,700<\/b>"},{"value":"Ubuntu Condensed","text":"Ubuntu Condensed<b>400<\/b>"},{"value":"Ubuntu Mono","text":"Ubuntu Mono<b>400,700<\/b>"}];
OfflajnFont_devanagari = [{"value":"Amita","text":"Amita<b>400,700<\/b>"},{"value":"Arya","text":"Arya<b>400,700<\/b>"},{"value":"Asar","text":"Asar<b>400<\/b>"},{"value":"Biryani","text":"Biryani<b>200,300,400,600,700,800,900<\/b>"},{"value":"Cambay","text":"Cambay<b>400,700<\/b>"},{"value":"Dekko","text":"Dekko<b>400<\/b>"},{"value":"Eczar","text":"Eczar<b>400,500,600,700,800<\/b>"},{"value":"Ek Mukta","text":"Ek Mukta<b>200,300,400,500,600,700,800<\/b>"},{"value":"Glegoo","text":"Glegoo<b>400,700<\/b>"},{"value":"Halant","text":"Halant<b>300,400,500,600,700<\/b>"},{"value":"Hind","text":"Hind<b>300,400,500,600,700<\/b>"},{"value":"Inknut Antiqua","text":"Inknut Antiqua<b>300,400,500,600,700,800,900<\/b>"},{"value":"Jaldi","text":"Jaldi<b>400,700<\/b>"},{"value":"Kadwa","text":"Kadwa<b>400,700<\/b>"},{"value":"Kalam","text":"Kalam<b>300,400,700<\/b>"},{"value":"Karma","text":"Karma<b>300,400,500,600,700<\/b>"},{"value":"Khand","text":"Khand<b>300,400,500,600,700<\/b>"},{"value":"Khula","text":"Khula<b>300,400,600,700,800<\/b>"},{"value":"Kurale","text":"Kurale<b>400<\/b>"},{"value":"Laila","text":"Laila<b>300,400,500,600,700<\/b>"},{"value":"Martel","text":"Martel<b>200,300,400,600,700,800,900<\/b>"},{"value":"Martel Sans","text":"Martel Sans<b>200,300,400,600,700,800,900<\/b>"},{"value":"Modak","text":"Modak<b>400<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Palanquin","text":"Palanquin<b>100,200,300,400,500,600,700<\/b>"},{"value":"Palanquin Dark","text":"Palanquin Dark<b>400,500,600,700<\/b>"},{"value":"Poppins","text":"Poppins<b>300,400,500,600,700<\/b>"},{"value":"Pragati Narrow","text":"Pragati Narrow<b>400,700<\/b>"},{"value":"Rajdhani","text":"Rajdhani<b>300,400,500,600,700<\/b>"},{"value":"Ranga","text":"Ranga<b>400,700<\/b>"},{"value":"Rhodium Libre","text":"Rhodium Libre<b>400<\/b>"},{"value":"Rozha One","text":"Rozha One<b>400<\/b>"},{"value":"Sahitya","text":"Sahitya<b>400,700<\/b>"},{"value":"Sarala","text":"Sarala<b>400,700<\/b>"},{"value":"Sarpanch","text":"Sarpanch<b>400,500,600,700,800,900<\/b>"},{"value":"Sumana","text":"Sumana<b>400,700<\/b>"},{"value":"Sura","text":"Sura<b>400,700<\/b>"},{"value":"Teko","text":"Teko<b>300,400,500,600,700<\/b>"},{"value":"Tillana","text":"Tillana<b>400,500,600,700,800<\/b>"},{"value":"Vesper Libre","text":"Vesper Libre<b>400,500,700,900<\/b>"},{"value":"Yantramanav","text":"Yantramanav<b>100,300,400,500,700,900<\/b>"}];
OfflajnFont_greek = [{"value":"Advent Pro","text":"Advent Pro<b>100,200,300,400,500,600,700<\/b>"},{"value":"Anonymous Pro","text":"Anonymous Pro<b>400,700<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Cardo","text":"Cardo<b>400,700<\/b>"},{"value":"Caudex","text":"Caudex<b>400,700<\/b>"},{"value":"Comfortaa","text":"Comfortaa<b>300,400,700<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Didact Gothic","text":"Didact Gothic<b>400<\/b>"},{"value":"Fira Mono","text":"Fira Mono<b>400,700<\/b>"},{"value":"Fira Sans","text":"Fira Sans<b>300,400,500,700<\/b>"},{"value":"GFS Didot","text":"GFS Didot<b>400<\/b>"},{"value":"GFS Neohellenic","text":"GFS Neohellenic<b>400,700<\/b>"},{"value":"Jura","text":"Jura<b>300,400,500,600<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Nova Mono","text":"Nova Mono<b>400<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Play","text":"Play<b>400,700<\/b>"},{"value":"Press Start 2P","text":"Press Start 2P<b>400<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"},{"value":"Ubuntu","text":"Ubuntu<b>300,400,500,700<\/b>"},{"value":"Ubuntu Condensed","text":"Ubuntu Condensed<b>400<\/b>"},{"value":"Ubuntu Mono","text":"Ubuntu Mono<b>400,700<\/b>"}];
OfflajnFont_greek_ext = [{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Cardo","text":"Cardo<b>400,700<\/b>"},{"value":"Caudex","text":"Caudex<b>400,700<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Didact Gothic","text":"Didact Gothic<b>400<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"},{"value":"Ubuntu","text":"Ubuntu<b>300,400,500,700<\/b>"},{"value":"Ubuntu Condensed","text":"Ubuntu Condensed<b>400<\/b>"},{"value":"Ubuntu Mono","text":"Ubuntu Mono<b>400,700<\/b>"}];
OfflajnFont_gujarati = [{"value":"Hind Vadodara","text":"Hind Vadodara<b>300,400,500,600,700<\/b>"}];
OfflajnFont_hebrew = [{"value":"Alef","text":"Alef<b>400,700<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"}];
OfflajnFont_khmer = [{"value":"Angkor","text":"Angkor<b>400<\/b>"},{"value":"Battambang","text":"Battambang<b>400,700<\/b>"},{"value":"Bayon","text":"Bayon<b>400<\/b>"},{"value":"Bokor","text":"Bokor<b>400<\/b>"},{"value":"Chenla","text":"Chenla<b>400<\/b>"},{"value":"Content","text":"Content<b>400,700<\/b>"},{"value":"Dangrek","text":"Dangrek<b>400<\/b>"},{"value":"Fasthand","text":"Fasthand<b>400<\/b>"},{"value":"Freehand","text":"Freehand<b>400<\/b>"},{"value":"Hanuman","text":"Hanuman<b>400,700<\/b>"},{"value":"Kantumruy","text":"Kantumruy<b>300,400,700<\/b>"},{"value":"Kdam Thmor","text":"Kdam Thmor<b>400<\/b>"},{"value":"Khmer","text":"Khmer<b>400<\/b>"},{"value":"Koulen","text":"Koulen<b>400<\/b>"},{"value":"Metal","text":"Metal<b>400<\/b>"},{"value":"Moul","text":"Moul<b>400<\/b>"},{"value":"Moulpali","text":"Moulpali<b>400<\/b>"},{"value":"Nokora","text":"Nokora<b>400,700<\/b>"},{"value":"Odor Mean Chey","text":"Odor Mean Chey<b>400<\/b>"},{"value":"Preahvihear","text":"Preahvihear<b>400<\/b>"},{"value":"Siemreap","text":"Siemreap<b>400<\/b>"},{"value":"Suwannaphum","text":"Suwannaphum<b>400<\/b>"},{"value":"Taprom","text":"Taprom<b>400<\/b>"}];
OfflajnFont_latin = [{"value":"ABeeZee","text":"ABeeZee<b>400<\/b>"},{"value":"Abel","text":"Abel<b>400<\/b>"},{"value":"Abril Fatface","text":"Abril Fatface<b>400<\/b>"},{"value":"Aclonica","text":"Aclonica<b>400<\/b>"},{"value":"Acme","text":"Acme<b>400<\/b>"},{"value":"Actor","text":"Actor<b>400<\/b>"},{"value":"Adamina","text":"Adamina<b>400<\/b>"},{"value":"Advent Pro","text":"Advent Pro<b>100,200,300,400,500,600,700<\/b>"},{"value":"Aguafina Script","text":"Aguafina Script<b>400<\/b>"},{"value":"Akronim","text":"Akronim<b>400<\/b>"},{"value":"Aladin","text":"Aladin<b>400<\/b>"},{"value":"Aldrich","text":"Aldrich<b>400<\/b>"},{"value":"Alef","text":"Alef<b>400,700<\/b>"},{"value":"Alegreya","text":"Alegreya<b>400,700,900<\/b>"},{"value":"Alegreya SC","text":"Alegreya SC<b>400,700,900<\/b>"},{"value":"Alegreya Sans","text":"Alegreya Sans<b>100,300,400,500,700,800,900<\/b>"},{"value":"Alegreya Sans SC","text":"Alegreya Sans SC<b>100,300,400,500,700,800,900<\/b>"},{"value":"Alex Brush","text":"Alex Brush<b>400<\/b>"},{"value":"Alfa Slab One","text":"Alfa Slab One<b>400<\/b>"},{"value":"Alice","text":"Alice<b>400<\/b>"},{"value":"Alike","text":"Alike<b>400<\/b>"},{"value":"Alike Angular","text":"Alike Angular<b>400<\/b>"},{"value":"Allan","text":"Allan<b>400,700<\/b>"},{"value":"Allerta","text":"Allerta<b>400<\/b>"},{"value":"Allerta Stencil","text":"Allerta Stencil<b>400<\/b>"},{"value":"Allura","text":"Allura<b>400<\/b>"},{"value":"Almendra","text":"Almendra<b>400,700<\/b>"},{"value":"Almendra Display","text":"Almendra Display<b>400<\/b>"},{"value":"Almendra SC","text":"Almendra SC<b>400<\/b>"},{"value":"Amarante","text":"Amarante<b>400<\/b>"},{"value":"Amaranth","text":"Amaranth<b>400,700<\/b>"},{"value":"Amatic SC","text":"Amatic SC<b>400,700<\/b>"},{"value":"Amethysta","text":"Amethysta<b>400<\/b>"},{"value":"Amiri","text":"Amiri<b>400,700<\/b>"},{"value":"Amita","text":"Amita<b>400,700<\/b>"},{"value":"Anaheim","text":"Anaheim<b>400<\/b>"},{"value":"Andada","text":"Andada<b>400<\/b>"},{"value":"Andika","text":"Andika<b>400<\/b>"},{"value":"Annie Use Your Telescope","text":"Annie Use Your Telescope<b>400<\/b>"},{"value":"Anonymous Pro","text":"Anonymous Pro<b>400,700<\/b>"},{"value":"Antic","text":"Antic<b>400<\/b>"},{"value":"Antic Didone","text":"Antic Didone<b>400<\/b>"},{"value":"Antic Slab","text":"Antic Slab<b>400<\/b>"},{"value":"Anton","text":"Anton<b>400<\/b>"},{"value":"Arapey","text":"Arapey<b>400<\/b>"},{"value":"Arbutus","text":"Arbutus<b>400<\/b>"},{"value":"Arbutus Slab","text":"Arbutus Slab<b>400<\/b>"},{"value":"Architects Daughter","text":"Architects Daughter<b>400<\/b>"},{"value":"Archivo Black","text":"Archivo Black<b>400<\/b>"},{"value":"Archivo Narrow","text":"Archivo Narrow<b>400,700<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Arizonia","text":"Arizonia<b>400<\/b>"},{"value":"Armata","text":"Armata<b>400<\/b>"},{"value":"Artifika","text":"Artifika<b>400<\/b>"},{"value":"Arvo","text":"Arvo<b>400,700<\/b>"},{"value":"Arya","text":"Arya<b>400,700<\/b>"},{"value":"Asap","text":"Asap<b>400,700<\/b>"},{"value":"Asar","text":"Asar<b>400<\/b>"},{"value":"Asset","text":"Asset<b>400<\/b>"},{"value":"Astloch","text":"Astloch<b>400,700<\/b>"},{"value":"Asul","text":"Asul<b>400,700<\/b>"},{"value":"Atomic Age","text":"Atomic Age<b>400<\/b>"},{"value":"Aubrey","text":"Aubrey<b>400<\/b>"},{"value":"Audiowide","text":"Audiowide<b>400<\/b>"},{"value":"Autour One","text":"Autour One<b>400<\/b>"},{"value":"Average","text":"Average<b>400<\/b>"},{"value":"Average Sans","text":"Average Sans<b>400<\/b>"},{"value":"Averia Gruesa Libre","text":"Averia Gruesa Libre<b>400<\/b>"},{"value":"Averia Libre","text":"Averia Libre<b>300,400,700<\/b>"},{"value":"Averia Sans Libre","text":"Averia Sans Libre<b>300,400,700<\/b>"},{"value":"Averia Serif Libre","text":"Averia Serif Libre<b>300,400,700<\/b>"},{"value":"Bad Script","text":"Bad Script<b>400<\/b>"},{"value":"Balthazar","text":"Balthazar<b>400<\/b>"},{"value":"Bangers","text":"Bangers<b>400<\/b>"},{"value":"Basic","text":"Basic<b>400<\/b>"},{"value":"Baumans","text":"Baumans<b>400<\/b>"},{"value":"Belgrano","text":"Belgrano<b>400<\/b>"},{"value":"Belleza","text":"Belleza<b>400<\/b>"},{"value":"BenchNine","text":"BenchNine<b>300,400,700<\/b>"},{"value":"Bentham","text":"Bentham<b>400<\/b>"},{"value":"Berkshire Swash","text":"Berkshire Swash<b>400<\/b>"},{"value":"Bevan","text":"Bevan<b>400<\/b>"},{"value":"Bigelow Rules","text":"Bigelow Rules<b>400<\/b>"},{"value":"Bigshot One","text":"Bigshot One<b>400<\/b>"},{"value":"Bilbo","text":"Bilbo<b>400<\/b>"},{"value":"Bilbo Swash Caps","text":"Bilbo Swash Caps<b>400<\/b>"},{"value":"Biryani","text":"Biryani<b>200,300,400,600,700,800,900<\/b>"},{"value":"Bitter","text":"Bitter<b>400,700<\/b>"},{"value":"Black Ops One","text":"Black Ops One<b>400<\/b>"},{"value":"Bonbon","text":"Bonbon<b>400<\/b>"},{"value":"Boogaloo","text":"Boogaloo<b>400<\/b>"},{"value":"Bowlby One","text":"Bowlby One<b>400<\/b>"},{"value":"Bowlby One SC","text":"Bowlby One SC<b>400<\/b>"},{"value":"Brawler","text":"Brawler<b>400<\/b>"},{"value":"Bree Serif","text":"Bree Serif<b>400<\/b>"},{"value":"Bubblegum Sans","text":"Bubblegum Sans<b>400<\/b>"},{"value":"Bubbler One","text":"Bubbler One<b>400<\/b>"},{"value":"Buda","text":"Buda<b>300<\/b>"},{"value":"Buenard","text":"Buenard<b>400,700<\/b>"},{"value":"Butcherman","text":"Butcherman<b>400<\/b>"},{"value":"Butterfly Kids","text":"Butterfly Kids<b>400<\/b>"},{"value":"Cabin","text":"Cabin<b>400,500,600,700<\/b>"},{"value":"Cabin Condensed","text":"Cabin Condensed<b>400,500,600,700<\/b>"},{"value":"Cabin Sketch","text":"Cabin Sketch<b>400,700<\/b>"},{"value":"Caesar Dressing","text":"Caesar Dressing<b>400<\/b>"},{"value":"Cagliostro","text":"Cagliostro<b>400<\/b>"},{"value":"Calligraffitti","text":"Calligraffitti<b>400<\/b>"},{"value":"Cambay","text":"Cambay<b>400,700<\/b>"},{"value":"Cambo","text":"Cambo<b>400<\/b>"},{"value":"Candal","text":"Candal<b>400<\/b>"},{"value":"Cantarell","text":"Cantarell<b>400,700<\/b>"},{"value":"Cantata One","text":"Cantata One<b>400<\/b>"},{"value":"Cantora One","text":"Cantora One<b>400<\/b>"},{"value":"Capriola","text":"Capriola<b>400<\/b>"},{"value":"Cardo","text":"Cardo<b>400,700<\/b>"},{"value":"Carme","text":"Carme<b>400<\/b>"},{"value":"Carrois Gothic","text":"Carrois Gothic<b>400<\/b>"},{"value":"Carrois Gothic SC","text":"Carrois Gothic SC<b>400<\/b>"},{"value":"Carter One","text":"Carter One<b>400<\/b>"},{"value":"Catamaran","text":"Catamaran<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Caudex","text":"Caudex<b>400,700<\/b>"},{"value":"Caveat","text":"Caveat<b>400,700<\/b>"},{"value":"Caveat Brush","text":"Caveat Brush<b>400<\/b>"},{"value":"Cedarville Cursive","text":"Cedarville Cursive<b>400<\/b>"},{"value":"Ceviche One","text":"Ceviche One<b>400<\/b>"},{"value":"Changa One","text":"Changa One<b>400<\/b>"},{"value":"Chango","text":"Chango<b>400<\/b>"},{"value":"Chau Philomene One","text":"Chau Philomene One<b>400<\/b>"},{"value":"Chela One","text":"Chela One<b>400<\/b>"},{"value":"Chelsea Market","text":"Chelsea Market<b>400<\/b>"},{"value":"Cherry Cream Soda","text":"Cherry Cream Soda<b>400<\/b>"},{"value":"Cherry Swash","text":"Cherry Swash<b>400,700<\/b>"},{"value":"Chewy","text":"Chewy<b>400<\/b>"},{"value":"Chicle","text":"Chicle<b>400<\/b>"},{"value":"Chivo","text":"Chivo<b>400,900<\/b>"},{"value":"Chonburi","text":"Chonburi<b>400<\/b>"},{"value":"Cinzel","text":"Cinzel<b>400,700,900<\/b>"},{"value":"Cinzel Decorative","text":"Cinzel Decorative<b>400,700,900<\/b>"},{"value":"Clicker Script","text":"Clicker Script<b>400<\/b>"},{"value":"Coda","text":"Coda<b>400,800<\/b>"},{"value":"Coda Caption","text":"Coda Caption<b>800<\/b>"},{"value":"Codystar","text":"Codystar<b>300,400<\/b>"},{"value":"Combo","text":"Combo<b>400<\/b>"},{"value":"Comfortaa","text":"Comfortaa<b>300,400,700<\/b>"},{"value":"Coming Soon","text":"Coming Soon<b>400<\/b>"},{"value":"Concert One","text":"Concert One<b>400<\/b>"},{"value":"Condiment","text":"Condiment<b>400<\/b>"},{"value":"Contrail One","text":"Contrail One<b>400<\/b>"},{"value":"Convergence","text":"Convergence<b>400<\/b>"},{"value":"Cookie","text":"Cookie<b>400<\/b>"},{"value":"Copse","text":"Copse<b>400<\/b>"},{"value":"Corben","text":"Corben<b>400,700<\/b>"},{"value":"Courgette","text":"Courgette<b>400<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Coustard","text":"Coustard<b>400,900<\/b>"},{"value":"Covered By Your Grace","text":"Covered By Your Grace<b>400<\/b>"},{"value":"Crafty Girls","text":"Crafty Girls<b>400<\/b>"},{"value":"Creepster","text":"Creepster<b>400<\/b>"},{"value":"Crete Round","text":"Crete Round<b>400<\/b>"},{"value":"Crimson Text","text":"Crimson Text<b>400,600,700<\/b>"},{"value":"Croissant One","text":"Croissant One<b>400<\/b>"},{"value":"Crushed","text":"Crushed<b>400<\/b>"},{"value":"Cuprum","text":"Cuprum<b>400,700<\/b>"},{"value":"Cutive","text":"Cutive<b>400<\/b>"},{"value":"Cutive Mono","text":"Cutive Mono<b>400<\/b>"},{"value":"Damion","text":"Damion<b>400<\/b>"},{"value":"Dancing Script","text":"Dancing Script<b>400,700<\/b>"},{"value":"Dawning of a New Day","text":"Dawning of a New Day<b>400<\/b>"},{"value":"Days One","text":"Days One<b>400<\/b>"},{"value":"Dekko","text":"Dekko<b>400<\/b>"},{"value":"Delius","text":"Delius<b>400<\/b>"},{"value":"Delius Swash Caps","text":"Delius Swash Caps<b>400<\/b>"},{"value":"Delius Unicase","text":"Delius Unicase<b>400,700<\/b>"},{"value":"Della Respira","text":"Della Respira<b>400<\/b>"},{"value":"Denk One","text":"Denk One<b>400<\/b>"},{"value":"Devonshire","text":"Devonshire<b>400<\/b>"},{"value":"Dhurjati","text":"Dhurjati<b>400<\/b>"},{"value":"Didact Gothic","text":"Didact Gothic<b>400<\/b>"},{"value":"Diplomata","text":"Diplomata<b>400<\/b>"},{"value":"Diplomata SC","text":"Diplomata SC<b>400<\/b>"},{"value":"Domine","text":"Domine<b>400,700<\/b>"},{"value":"Donegal One","text":"Donegal One<b>400<\/b>"},{"value":"Doppio One","text":"Doppio One<b>400<\/b>"},{"value":"Dorsa","text":"Dorsa<b>400<\/b>"},{"value":"Dosis","text":"Dosis<b>200,300,400,500,600,700,800<\/b>"},{"value":"Dr Sugiyama","text":"Dr Sugiyama<b>400<\/b>"},{"value":"Droid Sans","text":"Droid Sans<b>400,700<\/b>"},{"value":"Droid Sans Mono","text":"Droid Sans Mono<b>400<\/b>"},{"value":"Droid Serif","text":"Droid Serif<b>400,700<\/b>"},{"value":"Duru Sans","text":"Duru Sans<b>400<\/b>"},{"value":"Dynalight","text":"Dynalight<b>400<\/b>"},{"value":"EB Garamond","text":"EB Garamond<b>400<\/b>"},{"value":"Eagle Lake","text":"Eagle Lake<b>400<\/b>"},{"value":"Eater","text":"Eater<b>400<\/b>"},{"value":"Economica","text":"Economica<b>400,700<\/b>"},{"value":"Eczar","text":"Eczar<b>400,500,600,700,800<\/b>"},{"value":"Ek Mukta","text":"Ek Mukta<b>200,300,400,500,600,700,800<\/b>"},{"value":"Electrolize","text":"Electrolize<b>400<\/b>"},{"value":"Elsie","text":"Elsie<b>400,900<\/b>"},{"value":"Elsie Swash Caps","text":"Elsie Swash Caps<b>400,900<\/b>"},{"value":"Emblema One","text":"Emblema One<b>400<\/b>"},{"value":"Emilys Candy","text":"Emilys Candy<b>400<\/b>"},{"value":"Engagement","text":"Engagement<b>400<\/b>"},{"value":"Englebert","text":"Englebert<b>400<\/b>"},{"value":"Enriqueta","text":"Enriqueta<b>400,700<\/b>"},{"value":"Erica One","text":"Erica One<b>400<\/b>"},{"value":"Esteban","text":"Esteban<b>400<\/b>"},{"value":"Euphoria Script","text":"Euphoria Script<b>400<\/b>"},{"value":"Ewert","text":"Ewert<b>400<\/b>"},{"value":"Exo","text":"Exo<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Exo 2","text":"Exo 2<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Expletus Sans","text":"Expletus Sans<b>400,500,600,700<\/b>"},{"value":"Fanwood Text","text":"Fanwood Text<b>400<\/b>"},{"value":"Fascinate","text":"Fascinate<b>400<\/b>"},{"value":"Fascinate Inline","text":"Fascinate Inline<b>400<\/b>"},{"value":"Faster One","text":"Faster One<b>400<\/b>"},{"value":"Fauna One","text":"Fauna One<b>400<\/b>"},{"value":"Federant","text":"Federant<b>400<\/b>"},{"value":"Federo","text":"Federo<b>400<\/b>"},{"value":"Felipa","text":"Felipa<b>400<\/b>"},{"value":"Fenix","text":"Fenix<b>400<\/b>"},{"value":"Finger Paint","text":"Finger Paint<b>400<\/b>"},{"value":"Fira Mono","text":"Fira Mono<b>400,700<\/b>"},{"value":"Fira Sans","text":"Fira Sans<b>300,400,500,700<\/b>"},{"value":"Fjalla One","text":"Fjalla One<b>400<\/b>"},{"value":"Fjord One","text":"Fjord One<b>400<\/b>"},{"value":"Flamenco","text":"Flamenco<b>300,400<\/b>"},{"value":"Flavors","text":"Flavors<b>400<\/b>"},{"value":"Fondamento","text":"Fondamento<b>400<\/b>"},{"value":"Fontdiner Swanky","text":"Fontdiner Swanky<b>400<\/b>"},{"value":"Forum","text":"Forum<b>400<\/b>"},{"value":"Francois One","text":"Francois One<b>400<\/b>"},{"value":"Freckle Face","text":"Freckle Face<b>400<\/b>"},{"value":"Fredericka the Great","text":"Fredericka the Great<b>400<\/b>"},{"value":"Fredoka One","text":"Fredoka One<b>400<\/b>"},{"value":"Fresca","text":"Fresca<b>400<\/b>"},{"value":"Frijole","text":"Frijole<b>400<\/b>"},{"value":"Fruktur","text":"Fruktur<b>400<\/b>"},{"value":"Fugaz One","text":"Fugaz One<b>400<\/b>"},{"value":"Gabriela","text":"Gabriela<b>400<\/b>"},{"value":"Gafata","text":"Gafata<b>400<\/b>"},{"value":"Galdeano","text":"Galdeano<b>400<\/b>"},{"value":"Galindo","text":"Galindo<b>400<\/b>"},{"value":"Gentium Basic","text":"Gentium Basic<b>400,700<\/b>"},{"value":"Gentium Book Basic","text":"Gentium Book Basic<b>400,700<\/b>"},{"value":"Geo","text":"Geo<b>400<\/b>"},{"value":"Geostar","text":"Geostar<b>400<\/b>"},{"value":"Geostar Fill","text":"Geostar Fill<b>400<\/b>"},{"value":"Germania One","text":"Germania One<b>400<\/b>"},{"value":"Gidugu","text":"Gidugu<b>400<\/b>"},{"value":"Gilda Display","text":"Gilda Display<b>400<\/b>"},{"value":"Give You Glory","text":"Give You Glory<b>400<\/b>"},{"value":"Glass Antiqua","text":"Glass Antiqua<b>400<\/b>"},{"value":"Glegoo","text":"Glegoo<b>400,700<\/b>"},{"value":"Gloria Hallelujah","text":"Gloria Hallelujah<b>400<\/b>"},{"value":"Goblin One","text":"Goblin One<b>400<\/b>"},{"value":"Gochi Hand","text":"Gochi Hand<b>400<\/b>"},{"value":"Gorditas","text":"Gorditas<b>400,700<\/b>"},{"value":"Goudy Bookletter 1911","text":"Goudy Bookletter 1911<b>400<\/b>"},{"value":"Graduate","text":"Graduate<b>400<\/b>"},{"value":"Grand Hotel","text":"Grand Hotel<b>400<\/b>"},{"value":"Gravitas One","text":"Gravitas One<b>400<\/b>"},{"value":"Great Vibes","text":"Great Vibes<b>400<\/b>"},{"value":"Griffy","text":"Griffy<b>400<\/b>"},{"value":"Gruppo","text":"Gruppo<b>400<\/b>"},{"value":"Gudea","text":"Gudea<b>400,700<\/b>"},{"value":"Gurajada","text":"Gurajada<b>400<\/b>"},{"value":"Habibi","text":"Habibi<b>400<\/b>"},{"value":"Halant","text":"Halant<b>300,400,500,600,700<\/b>"},{"value":"Hammersmith One","text":"Hammersmith One<b>400<\/b>"},{"value":"Hanalei","text":"Hanalei<b>400<\/b>"},{"value":"Hanalei Fill","text":"Hanalei Fill<b>400<\/b>"},{"value":"Handlee","text":"Handlee<b>400<\/b>"},{"value":"Happy Monkey","text":"Happy Monkey<b>400<\/b>"},{"value":"Headland One","text":"Headland One<b>400<\/b>"},{"value":"Henny Penny","text":"Henny Penny<b>400<\/b>"},{"value":"Herr Von Muellerhoff","text":"Herr Von Muellerhoff<b>400<\/b>"},{"value":"Hind","text":"Hind<b>300,400,500,600,700<\/b>"},{"value":"Hind Siliguri","text":"Hind Siliguri<b>300,400,500,600,700<\/b>"},{"value":"Hind Vadodara","text":"Hind Vadodara<b>300,400,500,600,700<\/b>"},{"value":"Holtwood One SC","text":"Holtwood One SC<b>400<\/b>"},{"value":"Homemade Apple","text":"Homemade Apple<b>400<\/b>"},{"value":"Homenaje","text":"Homenaje<b>400<\/b>"},{"value":"IM Fell DW Pica","text":"IM Fell DW Pica<b>400<\/b>"},{"value":"IM Fell DW Pica SC","text":"IM Fell DW Pica SC<b>400<\/b>"},{"value":"IM Fell Double Pica","text":"IM Fell Double Pica<b>400<\/b>"},{"value":"IM Fell Double Pica SC","text":"IM Fell Double Pica SC<b>400<\/b>"},{"value":"IM Fell English","text":"IM Fell English<b>400<\/b>"},{"value":"IM Fell English SC","text":"IM Fell English SC<b>400<\/b>"},{"value":"IM Fell French Canon","text":"IM Fell French Canon<b>400<\/b>"},{"value":"IM Fell French Canon SC","text":"IM Fell French Canon SC<b>400<\/b>"},{"value":"IM Fell Great Primer","text":"IM Fell Great Primer<b>400<\/b>"},{"value":"IM Fell Great Primer SC","text":"IM Fell Great Primer SC<b>400<\/b>"},{"value":"Iceberg","text":"Iceberg<b>400<\/b>"},{"value":"Iceland","text":"Iceland<b>400<\/b>"},{"value":"Imprima","text":"Imprima<b>400<\/b>"},{"value":"Inconsolata","text":"Inconsolata<b>400,700<\/b>"},{"value":"Inder","text":"Inder<b>400<\/b>"},{"value":"Indie Flower","text":"Indie Flower<b>400<\/b>"},{"value":"Inika","text":"Inika<b>400,700<\/b>"},{"value":"Inknut Antiqua","text":"Inknut Antiqua<b>300,400,500,600,700,800,900<\/b>"},{"value":"Irish Grover","text":"Irish Grover<b>400<\/b>"},{"value":"Istok Web","text":"Istok Web<b>400,700<\/b>"},{"value":"Italiana","text":"Italiana<b>400<\/b>"},{"value":"Italianno","text":"Italianno<b>400<\/b>"},{"value":"Itim","text":"Itim<b>400<\/b>"},{"value":"Jacques Francois","text":"Jacques Francois<b>400<\/b>"},{"value":"Jacques Francois Shadow","text":"Jacques Francois Shadow<b>400<\/b>"},{"value":"Jaldi","text":"Jaldi<b>400,700<\/b>"},{"value":"Jim Nightshade","text":"Jim Nightshade<b>400<\/b>"},{"value":"Jockey One","text":"Jockey One<b>400<\/b>"},{"value":"Jolly Lodger","text":"Jolly Lodger<b>400<\/b>"},{"value":"Josefin Sans","text":"Josefin Sans<b>100,300,400,600,700<\/b>"},{"value":"Josefin Slab","text":"Josefin Slab<b>100,300,400,600,700<\/b>"},{"value":"Joti One","text":"Joti One<b>400<\/b>"},{"value":"Judson","text":"Judson<b>400,700<\/b>"},{"value":"Julee","text":"Julee<b>400<\/b>"},{"value":"Julius Sans One","text":"Julius Sans One<b>400<\/b>"},{"value":"Junge","text":"Junge<b>400<\/b>"},{"value":"Jura","text":"Jura<b>300,400,500,600<\/b>"},{"value":"Just Another Hand","text":"Just Another Hand<b>400<\/b>"},{"value":"Just Me Again Down Here","text":"Just Me Again Down Here<b>400<\/b>"},{"value":"Kadwa","text":"Kadwa<b>400,700<\/b>"},{"value":"Kalam","text":"Kalam<b>300,400,700<\/b>"},{"value":"Kameron","text":"Kameron<b>400,700<\/b>"},{"value":"Kanit","text":"Kanit<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Karla","text":"Karla<b>400,700<\/b>"},{"value":"Karma","text":"Karma<b>300,400,500,600,700<\/b>"},{"value":"Kaushan Script","text":"Kaushan Script<b>400<\/b>"},{"value":"Kavoon","text":"Kavoon<b>400<\/b>"},{"value":"Keania One","text":"Keania One<b>400<\/b>"},{"value":"Kelly Slab","text":"Kelly Slab<b>400<\/b>"},{"value":"Kenia","text":"Kenia<b>400<\/b>"},{"value":"Khand","text":"Khand<b>300,400,500,600,700<\/b>"},{"value":"Khula","text":"Khula<b>300,400,600,700,800<\/b>"},{"value":"Kite One","text":"Kite One<b>400<\/b>"},{"value":"Knewave","text":"Knewave<b>400<\/b>"},{"value":"Kotta One","text":"Kotta One<b>400<\/b>"},{"value":"Kranky","text":"Kranky<b>400<\/b>"},{"value":"Kreon","text":"Kreon<b>300,400,700<\/b>"},{"value":"Kristi","text":"Kristi<b>400<\/b>"},{"value":"Krona One","text":"Krona One<b>400<\/b>"},{"value":"Kurale","text":"Kurale<b>400<\/b>"},{"value":"La Belle Aurore","text":"La Belle Aurore<b>400<\/b>"},{"value":"Laila","text":"Laila<b>300,400,500,600,700<\/b>"},{"value":"Lakki Reddy","text":"Lakki Reddy<b>400<\/b>"},{"value":"Lancelot","text":"Lancelot<b>400<\/b>"},{"value":"Lateef","text":"Lateef<b>400<\/b>"},{"value":"Lato","text":"Lato<b>100,300,400,700,900<\/b>"},{"value":"League Script","text":"League Script<b>400<\/b>"},{"value":"Leckerli One","text":"Leckerli One<b>400<\/b>"},{"value":"Ledger","text":"Ledger<b>400<\/b>"},{"value":"Lekton","text":"Lekton<b>400,700<\/b>"},{"value":"Lemon","text":"Lemon<b>400<\/b>"},{"value":"Libre Baskerville","text":"Libre Baskerville<b>400,700<\/b>"},{"value":"Life Savers","text":"Life Savers<b>400,700<\/b>"},{"value":"Lilita One","text":"Lilita One<b>400<\/b>"},{"value":"Lily Script One","text":"Lily Script One<b>400<\/b>"},{"value":"Limelight","text":"Limelight<b>400<\/b>"},{"value":"Linden Hill","text":"Linden Hill<b>400<\/b>"},{"value":"Lobster","text":"Lobster<b>400<\/b>"},{"value":"Lobster Two","text":"Lobster Two<b>400,700<\/b>"},{"value":"Londrina Outline","text":"Londrina Outline<b>400<\/b>"},{"value":"Londrina Shadow","text":"Londrina Shadow<b>400<\/b>"},{"value":"Londrina Sketch","text":"Londrina Sketch<b>400<\/b>"},{"value":"Londrina Solid","text":"Londrina Solid<b>400<\/b>"},{"value":"Lora","text":"Lora<b>400,700<\/b>"},{"value":"Love Ya Like A Sister","text":"Love Ya Like A Sister<b>400<\/b>"},{"value":"Loved by the King","text":"Loved by the King<b>400<\/b>"},{"value":"Lovers Quarrel","text":"Lovers Quarrel<b>400<\/b>"},{"value":"Luckiest Guy","text":"Luckiest Guy<b>400<\/b>"},{"value":"Lusitana","text":"Lusitana<b>400,700<\/b>"},{"value":"Lustria","text":"Lustria<b>400<\/b>"},{"value":"Macondo","text":"Macondo<b>400<\/b>"},{"value":"Macondo Swash Caps","text":"Macondo Swash Caps<b>400<\/b>"},{"value":"Magra","text":"Magra<b>400,700<\/b>"},{"value":"Maiden Orange","text":"Maiden Orange<b>400<\/b>"},{"value":"Mako","text":"Mako<b>400<\/b>"},{"value":"Mallanna","text":"Mallanna<b>400<\/b>"},{"value":"Mandali","text":"Mandali<b>400<\/b>"},{"value":"Marcellus","text":"Marcellus<b>400<\/b>"},{"value":"Marcellus SC","text":"Marcellus SC<b>400<\/b>"},{"value":"Marck Script","text":"Marck Script<b>400<\/b>"},{"value":"Margarine","text":"Margarine<b>400<\/b>"},{"value":"Marko One","text":"Marko One<b>400<\/b>"},{"value":"Marmelad","text":"Marmelad<b>400<\/b>"},{"value":"Martel","text":"Martel<b>200,300,400,600,700,800,900<\/b>"},{"value":"Martel Sans","text":"Martel Sans<b>200,300,400,600,700,800,900<\/b>"},{"value":"Marvel","text":"Marvel<b>400,700<\/b>"},{"value":"Mate","text":"Mate<b>400<\/b>"},{"value":"Mate SC","text":"Mate SC<b>400<\/b>"},{"value":"Maven Pro","text":"Maven Pro<b>400,500,700,900<\/b>"},{"value":"McLaren","text":"McLaren<b>400<\/b>"},{"value":"Meddon","text":"Meddon<b>400<\/b>"},{"value":"MedievalSharp","text":"MedievalSharp<b>400<\/b>"},{"value":"Medula One","text":"Medula One<b>400<\/b>"},{"value":"Megrim","text":"Megrim<b>400<\/b>"},{"value":"Meie Script","text":"Meie Script<b>400<\/b>"},{"value":"Merienda","text":"Merienda<b>400,700<\/b>"},{"value":"Merienda One","text":"Merienda One<b>400<\/b>"},{"value":"Merriweather","text":"Merriweather<b>300,400,700,900<\/b>"},{"value":"Merriweather Sans","text":"Merriweather Sans<b>300,400,700,800<\/b>"},{"value":"Metal Mania","text":"Metal Mania<b>400<\/b>"},{"value":"Metamorphous","text":"Metamorphous<b>400<\/b>"},{"value":"Metrophobic","text":"Metrophobic<b>400<\/b>"},{"value":"Michroma","text":"Michroma<b>400<\/b>"},{"value":"Milonga","text":"Milonga<b>400<\/b>"},{"value":"Miltonian","text":"Miltonian<b>400<\/b>"},{"value":"Miltonian Tattoo","text":"Miltonian Tattoo<b>400<\/b>"},{"value":"Miniver","text":"Miniver<b>400<\/b>"},{"value":"Miss Fajardose","text":"Miss Fajardose<b>400<\/b>"},{"value":"Modak","text":"Modak<b>400<\/b>"},{"value":"Modern Antiqua","text":"Modern Antiqua<b>400<\/b>"},{"value":"Molengo","text":"Molengo<b>400<\/b>"},{"value":"Molle","text":"Molle<b><\/b>"},{"value":"Monda","text":"Monda<b>400,700<\/b>"},{"value":"Monofett","text":"Monofett<b>400<\/b>"},{"value":"Monoton","text":"Monoton<b>400<\/b>"},{"value":"Monsieur La Doulaise","text":"Monsieur La Doulaise<b>400<\/b>"},{"value":"Montaga","text":"Montaga<b>400<\/b>"},{"value":"Montez","text":"Montez<b>400<\/b>"},{"value":"Montserrat","text":"Montserrat<b>400,700<\/b>"},{"value":"Montserrat Alternates","text":"Montserrat Alternates<b>400,700<\/b>"},{"value":"Montserrat Subrayada","text":"Montserrat Subrayada<b>400,700<\/b>"},{"value":"Mountains of Christmas","text":"Mountains of Christmas<b>400,700<\/b>"},{"value":"Mouse Memoirs","text":"Mouse Memoirs<b>400<\/b>"},{"value":"Mr Bedfort","text":"Mr Bedfort<b>400<\/b>"},{"value":"Mr Dafoe","text":"Mr Dafoe<b>400<\/b>"},{"value":"Mr De Haviland","text":"Mr De Haviland<b>400<\/b>"},{"value":"Mrs Saint Delafield","text":"Mrs Saint Delafield<b>400<\/b>"},{"value":"Mrs Sheppards","text":"Mrs Sheppards<b>400<\/b>"},{"value":"Muli","text":"Muli<b>300,400<\/b>"},{"value":"Mystery Quest","text":"Mystery Quest<b>400<\/b>"},{"value":"NTR","text":"NTR<b>400<\/b>"},{"value":"Neucha","text":"Neucha<b>400<\/b>"},{"value":"Neuton","text":"Neuton<b>200,300,400,700,800<\/b>"},{"value":"New Rocker","text":"New Rocker<b>400<\/b>"},{"value":"News Cycle","text":"News Cycle<b>400,700<\/b>"},{"value":"Niconne","text":"Niconne<b>400<\/b>"},{"value":"Nixie One","text":"Nixie One<b>400<\/b>"},{"value":"Nobile","text":"Nobile<b>400,700<\/b>"},{"value":"Norican","text":"Norican<b>400<\/b>"},{"value":"Nosifer","text":"Nosifer<b>400<\/b>"},{"value":"Nothing You Could Do","text":"Nothing You Could Do<b>400<\/b>"},{"value":"Noticia Text","text":"Noticia Text<b>400,700<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Nova Cut","text":"Nova Cut<b>400<\/b>"},{"value":"Nova Flat","text":"Nova Flat<b>400<\/b>"},{"value":"Nova Mono","text":"Nova Mono<b>400<\/b>"},{"value":"Nova Oval","text":"Nova Oval<b>400<\/b>"},{"value":"Nova Round","text":"Nova Round<b>400<\/b>"},{"value":"Nova Script","text":"Nova Script<b>400<\/b>"},{"value":"Nova Slim","text":"Nova Slim<b>400<\/b>"},{"value":"Nova Square","text":"Nova Square<b>400<\/b>"},{"value":"Numans","text":"Numans<b>400<\/b>"},{"value":"Nunito","text":"Nunito<b>300,400,700<\/b>"},{"value":"Offside","text":"Offside<b>400<\/b>"},{"value":"Old Standard TT","text":"Old Standard TT<b>400,700<\/b>"},{"value":"Oldenburg","text":"Oldenburg<b>400<\/b>"},{"value":"Oleo Script","text":"Oleo Script<b>400,700<\/b>"},{"value":"Oleo Script Swash Caps","text":"Oleo Script Swash Caps<b>400,700<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Oranienbaum","text":"Oranienbaum<b>400<\/b>"},{"value":"Orbitron","text":"Orbitron<b>400,500,700,900<\/b>"},{"value":"Oregano","text":"Oregano<b>400<\/b>"},{"value":"Orienta","text":"Orienta<b>400<\/b>"},{"value":"Original Surfer","text":"Original Surfer<b>400<\/b>"},{"value":"Oswald","text":"Oswald<b>300,400,700<\/b>"},{"value":"Over the Rainbow","text":"Over the Rainbow<b>400<\/b>"},{"value":"Overlock","text":"Overlock<b>400,700,900<\/b>"},{"value":"Overlock SC","text":"Overlock SC<b>400<\/b>"},{"value":"Ovo","text":"Ovo<b>400<\/b>"},{"value":"Oxygen","text":"Oxygen<b>300,400,700<\/b>"},{"value":"Oxygen Mono","text":"Oxygen Mono<b>400<\/b>"},{"value":"PT Mono","text":"PT Mono<b>400<\/b>"},{"value":"PT Sans","text":"PT Sans<b>400,700<\/b>"},{"value":"PT Sans Caption","text":"PT Sans Caption<b>400,700<\/b>"},{"value":"PT Sans Narrow","text":"PT Sans Narrow<b>400,700<\/b>"},{"value":"PT Serif","text":"PT Serif<b>400,700<\/b>"},{"value":"PT Serif Caption","text":"PT Serif Caption<b>400<\/b>"},{"value":"Pacifico","text":"Pacifico<b>400<\/b>"},{"value":"Palanquin","text":"Palanquin<b>100,200,300,400,500,600,700<\/b>"},{"value":"Palanquin Dark","text":"Palanquin Dark<b>400,500,600,700<\/b>"},{"value":"Paprika","text":"Paprika<b>400<\/b>"},{"value":"Parisienne","text":"Parisienne<b>400<\/b>"},{"value":"Passero One","text":"Passero One<b>400<\/b>"},{"value":"Passion One","text":"Passion One<b>400,700,900<\/b>"},{"value":"Pathway Gothic One","text":"Pathway Gothic One<b>400<\/b>"},{"value":"Patrick Hand","text":"Patrick Hand<b>400<\/b>"},{"value":"Patrick Hand SC","text":"Patrick Hand SC<b>400<\/b>"},{"value":"Patua One","text":"Patua One<b>400<\/b>"},{"value":"Paytone One","text":"Paytone One<b>400<\/b>"},{"value":"Peddana","text":"Peddana<b>400<\/b>"},{"value":"Peralta","text":"Peralta<b>400<\/b>"},{"value":"Permanent Marker","text":"Permanent Marker<b>400<\/b>"},{"value":"Petit Formal Script","text":"Petit Formal Script<b>400<\/b>"},{"value":"Petrona","text":"Petrona<b>400<\/b>"},{"value":"Philosopher","text":"Philosopher<b>400,700<\/b>"},{"value":"Piedra","text":"Piedra<b>400<\/b>"},{"value":"Pinyon Script","text":"Pinyon Script<b>400<\/b>"},{"value":"Pirata One","text":"Pirata One<b>400<\/b>"},{"value":"Plaster","text":"Plaster<b>400<\/b>"},{"value":"Play","text":"Play<b>400,700<\/b>"},{"value":"Playball","text":"Playball<b>400<\/b>"},{"value":"Playfair Display","text":"Playfair Display<b>400,700,900<\/b>"},{"value":"Playfair Display SC","text":"Playfair Display SC<b>400,700,900<\/b>"},{"value":"Podkova","text":"Podkova<b>400,700<\/b>"},{"value":"Poiret One","text":"Poiret One<b>400<\/b>"},{"value":"Poller One","text":"Poller One<b>400<\/b>"},{"value":"Poly","text":"Poly<b>400<\/b>"},{"value":"Pompiere","text":"Pompiere<b>400<\/b>"},{"value":"Pontano Sans","text":"Pontano Sans<b>400<\/b>"},{"value":"Poppins","text":"Poppins<b>300,400,500,600,700<\/b>"},{"value":"Port Lligat Sans","text":"Port Lligat Sans<b>400<\/b>"},{"value":"Port Lligat Slab","text":"Port Lligat Slab<b>400<\/b>"},{"value":"Pragati Narrow","text":"Pragati Narrow<b>400,700<\/b>"},{"value":"Prata","text":"Prata<b>400<\/b>"},{"value":"Press Start 2P","text":"Press Start 2P<b>400<\/b>"},{"value":"Princess Sofia","text":"Princess Sofia<b>400<\/b>"},{"value":"Prociono","text":"Prociono<b>400<\/b>"},{"value":"Prosto One","text":"Prosto One<b>400<\/b>"},{"value":"Puritan","text":"Puritan<b>400,700<\/b>"},{"value":"Purple Purse","text":"Purple Purse<b>400<\/b>"},{"value":"Quando","text":"Quando<b>400<\/b>"},{"value":"Quantico","text":"Quantico<b>400,700<\/b>"},{"value":"Quattrocento","text":"Quattrocento<b>400,700<\/b>"},{"value":"Quattrocento Sans","text":"Quattrocento Sans<b>400,700<\/b>"},{"value":"Questrial","text":"Questrial<b>400<\/b>"},{"value":"Quicksand","text":"Quicksand<b>300,400,700<\/b>"},{"value":"Quintessential","text":"Quintessential<b>400<\/b>"},{"value":"Qwigley","text":"Qwigley<b>400<\/b>"},{"value":"Racing Sans One","text":"Racing Sans One<b>400<\/b>"},{"value":"Radley","text":"Radley<b>400<\/b>"},{"value":"Rajdhani","text":"Rajdhani<b>300,400,500,600,700<\/b>"},{"value":"Raleway","text":"Raleway<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Raleway Dots","text":"Raleway Dots<b>400<\/b>"},{"value":"Ramabhadra","text":"Ramabhadra<b>400<\/b>"},{"value":"Ramaraja","text":"Ramaraja<b>400<\/b>"},{"value":"Rambla","text":"Rambla<b>400,700<\/b>"},{"value":"Rammetto One","text":"Rammetto One<b>400<\/b>"},{"value":"Ranchers","text":"Ranchers<b>400<\/b>"},{"value":"Rancho","text":"Rancho<b>400<\/b>"},{"value":"Ranga","text":"Ranga<b>400,700<\/b>"},{"value":"Rationale","text":"Rationale<b>400<\/b>"},{"value":"Ravi Prakash","text":"Ravi Prakash<b>400<\/b>"},{"value":"Redressed","text":"Redressed<b>400<\/b>"},{"value":"Reenie Beanie","text":"Reenie Beanie<b>400<\/b>"},{"value":"Revalia","text":"Revalia<b>400<\/b>"},{"value":"Rhodium Libre","text":"Rhodium Libre<b>400<\/b>"},{"value":"Ribeye","text":"Ribeye<b>400<\/b>"},{"value":"Ribeye Marrow","text":"Ribeye Marrow<b>400<\/b>"},{"value":"Righteous","text":"Righteous<b>400<\/b>"},{"value":"Risque","text":"Risque<b>400<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Rochester","text":"Rochester<b>400<\/b>"},{"value":"Rock Salt","text":"Rock Salt<b>400<\/b>"},{"value":"Rokkitt","text":"Rokkitt<b>400,700<\/b>"},{"value":"Romanesco","text":"Romanesco<b>400<\/b>"},{"value":"Ropa Sans","text":"Ropa Sans<b>400<\/b>"},{"value":"Rosario","text":"Rosario<b>400,700<\/b>"},{"value":"Rosarivo","text":"Rosarivo<b>400<\/b>"},{"value":"Rouge Script","text":"Rouge Script<b>400<\/b>"},{"value":"Rozha One","text":"Rozha One<b>400<\/b>"},{"value":"Rubik","text":"Rubik<b>300,400,500,700,900<\/b>"},{"value":"Rubik Mono One","text":"Rubik Mono One<b>400<\/b>"},{"value":"Rubik One","text":"Rubik One<b>400<\/b>"},{"value":"Ruda","text":"Ruda<b>400,700,900<\/b>"},{"value":"Rufina","text":"Rufina<b>400,700<\/b>"},{"value":"Ruge Boogie","text":"Ruge Boogie<b>400<\/b>"},{"value":"Ruluko","text":"Ruluko<b>400<\/b>"},{"value":"Rum Raisin","text":"Rum Raisin<b>400<\/b>"},{"value":"Ruslan Display","text":"Ruslan Display<b>400<\/b>"},{"value":"Russo One","text":"Russo One<b>400<\/b>"},{"value":"Ruthie","text":"Ruthie<b>400<\/b>"},{"value":"Rye","text":"Rye<b>400<\/b>"},{"value":"Sacramento","text":"Sacramento<b>400<\/b>"},{"value":"Sahitya","text":"Sahitya<b>400,700<\/b>"},{"value":"Sail","text":"Sail<b>400<\/b>"},{"value":"Salsa","text":"Salsa<b>400<\/b>"},{"value":"Sanchez","text":"Sanchez<b>400<\/b>"},{"value":"Sancreek","text":"Sancreek<b>400<\/b>"},{"value":"Sansita One","text":"Sansita One<b>400<\/b>"},{"value":"Sarala","text":"Sarala<b>400,700<\/b>"},{"value":"Sarina","text":"Sarina<b>400<\/b>"},{"value":"Sarpanch","text":"Sarpanch<b>400,500,600,700,800,900<\/b>"},{"value":"Satisfy","text":"Satisfy<b>400<\/b>"},{"value":"Scada","text":"Scada<b>400,700<\/b>"},{"value":"Scheherazade","text":"Scheherazade<b>400,700<\/b>"},{"value":"Schoolbell","text":"Schoolbell<b>400<\/b>"},{"value":"Seaweed Script","text":"Seaweed Script<b>400<\/b>"},{"value":"Sevillana","text":"Sevillana<b>400<\/b>"},{"value":"Seymour One","text":"Seymour One<b>400<\/b>"},{"value":"Shadows Into Light","text":"Shadows Into Light<b>400<\/b>"},{"value":"Shadows Into Light Two","text":"Shadows Into Light Two<b>400<\/b>"},{"value":"Shanti","text":"Shanti<b>400<\/b>"},{"value":"Share","text":"Share<b>400,700<\/b>"},{"value":"Share Tech","text":"Share Tech<b>400<\/b>"},{"value":"Share Tech Mono","text":"Share Tech Mono<b>400<\/b>"},{"value":"Shojumaru","text":"Shojumaru<b>400<\/b>"},{"value":"Short Stack","text":"Short Stack<b>400<\/b>"},{"value":"Sigmar One","text":"Sigmar One<b>400<\/b>"},{"value":"Signika","text":"Signika<b>300,400,600,700<\/b>"},{"value":"Signika Negative","text":"Signika Negative<b>300,400,600,700<\/b>"},{"value":"Simonetta","text":"Simonetta<b>400,900<\/b>"},{"value":"Sintony","text":"Sintony<b>400,700<\/b>"},{"value":"Sirin Stencil","text":"Sirin Stencil<b>400<\/b>"},{"value":"Six Caps","text":"Six Caps<b>400<\/b>"},{"value":"Skranji","text":"Skranji<b>400,700<\/b>"},{"value":"Slabo 13px","text":"Slabo 13px<b>400<\/b>"},{"value":"Slabo 27px","text":"Slabo 27px<b>400<\/b>"},{"value":"Slackey","text":"Slackey<b>400<\/b>"},{"value":"Smokum","text":"Smokum<b>400<\/b>"},{"value":"Smythe","text":"Smythe<b>400<\/b>"},{"value":"Sniglet","text":"Sniglet<b>400,800<\/b>"},{"value":"Snippet","text":"Snippet<b>400<\/b>"},{"value":"Snowburst One","text":"Snowburst One<b>400<\/b>"},{"value":"Sofadi One","text":"Sofadi One<b>400<\/b>"},{"value":"Sofia","text":"Sofia<b>400<\/b>"},{"value":"Sonsie One","text":"Sonsie One<b>400<\/b>"},{"value":"Sorts Mill Goudy","text":"Sorts Mill Goudy<b>400<\/b>"},{"value":"Source Code Pro","text":"Source Code Pro<b>200,300,400,500,600,700,900<\/b>"},{"value":"Source Sans Pro","text":"Source Sans Pro<b>200,300,400,600,700,900<\/b>"},{"value":"Source Serif Pro","text":"Source Serif Pro<b>400,600,700<\/b>"},{"value":"Special Elite","text":"Special Elite<b>400<\/b>"},{"value":"Spicy Rice","text":"Spicy Rice<b>400<\/b>"},{"value":"Spinnaker","text":"Spinnaker<b>400<\/b>"},{"value":"Spirax","text":"Spirax<b>400<\/b>"},{"value":"Squada One","text":"Squada One<b>400<\/b>"},{"value":"Sree Krushnadevaraya","text":"Sree Krushnadevaraya<b>400<\/b>"},{"value":"Stalemate","text":"Stalemate<b>400<\/b>"},{"value":"Stalinist One","text":"Stalinist One<b>400<\/b>"},{"value":"Stardos Stencil","text":"Stardos Stencil<b>400,700<\/b>"},{"value":"Stint Ultra Condensed","text":"Stint Ultra Condensed<b>400<\/b>"},{"value":"Stint Ultra Expanded","text":"Stint Ultra Expanded<b>400<\/b>"},{"value":"Stoke","text":"Stoke<b>300,400<\/b>"},{"value":"Strait","text":"Strait<b>400<\/b>"},{"value":"Sue Ellen Francisco","text":"Sue Ellen Francisco<b>400<\/b>"},{"value":"Sumana","text":"Sumana<b>400,700<\/b>"},{"value":"Sunshiney","text":"Sunshiney<b>400<\/b>"},{"value":"Supermercado One","text":"Supermercado One<b>400<\/b>"},{"value":"Sura","text":"Sura<b>400,700<\/b>"},{"value":"Suranna","text":"Suranna<b>400<\/b>"},{"value":"Suravaram","text":"Suravaram<b>400<\/b>"},{"value":"Swanky and Moo Moo","text":"Swanky and Moo Moo<b>400<\/b>"},{"value":"Syncopate","text":"Syncopate<b>400,700<\/b>"},{"value":"Tangerine","text":"Tangerine<b>400,700<\/b>"},{"value":"Tauri","text":"Tauri<b>400<\/b>"},{"value":"Teko","text":"Teko<b>300,400,500,600,700<\/b>"},{"value":"Telex","text":"Telex<b>400<\/b>"},{"value":"Tenali Ramakrishna","text":"Tenali Ramakrishna<b>400<\/b>"},{"value":"Tenor Sans","text":"Tenor Sans<b>400<\/b>"},{"value":"Text Me One","text":"Text Me One<b>400<\/b>"},{"value":"The Girl Next Door","text":"The Girl Next Door<b>400<\/b>"},{"value":"Tienne","text":"Tienne<b>400,700,900<\/b>"},{"value":"Tillana","text":"Tillana<b>400,500,600,700,800<\/b>"},{"value":"Timmana","text":"Timmana<b>400<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"},{"value":"Titan One","text":"Titan One<b>400<\/b>"},{"value":"Titillium Web","text":"Titillium Web<b>200,300,400,600,700,900<\/b>"},{"value":"Trade Winds","text":"Trade Winds<b>400<\/b>"},{"value":"Trocchi","text":"Trocchi<b>400<\/b>"},{"value":"Trochut","text":"Trochut<b>400,700<\/b>"},{"value":"Trykker","text":"Trykker<b>400<\/b>"},{"value":"Tulpen One","text":"Tulpen One<b>400<\/b>"},{"value":"Ubuntu","text":"Ubuntu<b>300,400,500,700<\/b>"},{"value":"Ubuntu Condensed","text":"Ubuntu Condensed<b>400<\/b>"},{"value":"Ubuntu Mono","text":"Ubuntu Mono<b>400,700<\/b>"},{"value":"Ultra","text":"Ultra<b>400<\/b>"},{"value":"Uncial Antiqua","text":"Uncial Antiqua<b>400<\/b>"},{"value":"Underdog","text":"Underdog<b>400<\/b>"},{"value":"Unica One","text":"Unica One<b>400<\/b>"},{"value":"UnifrakturCook","text":"UnifrakturCook<b>700<\/b>"},{"value":"UnifrakturMaguntia","text":"UnifrakturMaguntia<b>400<\/b>"},{"value":"Unkempt","text":"Unkempt<b>400,700<\/b>"},{"value":"Unlock","text":"Unlock<b>400<\/b>"},{"value":"Unna","text":"Unna<b>400<\/b>"},{"value":"VT323","text":"VT323<b>400<\/b>"},{"value":"Vampiro One","text":"Vampiro One<b>400<\/b>"},{"value":"Varela","text":"Varela<b>400<\/b>"},{"value":"Varela Round","text":"Varela Round<b>400<\/b>"},{"value":"Vast Shadow","text":"Vast Shadow<b>400<\/b>"},{"value":"Vesper Libre","text":"Vesper Libre<b>400,500,700,900<\/b>"},{"value":"Vibur","text":"Vibur<b>400<\/b>"},{"value":"Vidaloka","text":"Vidaloka<b>400<\/b>"},{"value":"Viga","text":"Viga<b>400<\/b>"},{"value":"Voces","text":"Voces<b>400<\/b>"},{"value":"Volkhov","text":"Volkhov<b>400,700<\/b>"},{"value":"Vollkorn","text":"Vollkorn<b>400,700<\/b>"},{"value":"Voltaire","text":"Voltaire<b>400<\/b>"},{"value":"Waiting for the Sunrise","text":"Waiting for the Sunrise<b>400<\/b>"},{"value":"Wallpoet","text":"Wallpoet<b>400<\/b>"},{"value":"Walter Turncoat","text":"Walter Turncoat<b>400<\/b>"},{"value":"Warnes","text":"Warnes<b>400<\/b>"},{"value":"Wellfleet","text":"Wellfleet<b>400<\/b>"},{"value":"Wendy One","text":"Wendy One<b>400<\/b>"},{"value":"Wire One","text":"Wire One<b>400<\/b>"},{"value":"Work Sans","text":"Work Sans<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Yanone Kaffeesatz","text":"Yanone Kaffeesatz<b>200,300,400,700<\/b>"},{"value":"Yantramanav","text":"Yantramanav<b>100,300,400,500,700,900<\/b>"},{"value":"Yellowtail","text":"Yellowtail<b>400<\/b>"},{"value":"Yeseva One","text":"Yeseva One<b>400<\/b>"},{"value":"Yesteryear","text":"Yesteryear<b>400<\/b>"},{"value":"Zeyada","text":"Zeyada<b>400<\/b>"}];
OfflajnFont_latin_ext = [{"value":"Abril Fatface","text":"Abril Fatface<b>400<\/b>"},{"value":"Advent Pro","text":"Advent Pro<b>100,200,300,400,500,600,700<\/b>"},{"value":"Aguafina Script","text":"Aguafina Script<b>400<\/b>"},{"value":"Akronim","text":"Akronim<b>400<\/b>"},{"value":"Aladin","text":"Aladin<b>400<\/b>"},{"value":"Alegreya","text":"Alegreya<b>400,700,900<\/b>"},{"value":"Alegreya SC","text":"Alegreya SC<b>400,700,900<\/b>"},{"value":"Alegreya Sans","text":"Alegreya Sans<b>100,300,400,500,700,800,900<\/b>"},{"value":"Alegreya Sans SC","text":"Alegreya Sans SC<b>100,300,400,500,700,800,900<\/b>"},{"value":"Alex Brush","text":"Alex Brush<b>400<\/b>"},{"value":"Allan","text":"Allan<b>400,700<\/b>"},{"value":"Allura","text":"Allura<b>400<\/b>"},{"value":"Almendra","text":"Almendra<b>400,700<\/b>"},{"value":"Almendra Display","text":"Almendra Display<b>400<\/b>"},{"value":"Amarante","text":"Amarante<b>400<\/b>"},{"value":"Amatic SC","text":"Amatic SC<b>400,700<\/b>"},{"value":"Amita","text":"Amita<b>400,700<\/b>"},{"value":"Anaheim","text":"Anaheim<b>400<\/b>"},{"value":"Andada","text":"Andada<b>400<\/b>"},{"value":"Andika","text":"Andika<b>400<\/b>"},{"value":"Anonymous Pro","text":"Anonymous Pro<b>400,700<\/b>"},{"value":"Anton","text":"Anton<b>400<\/b>"},{"value":"Arbutus","text":"Arbutus<b>400<\/b>"},{"value":"Arbutus Slab","text":"Arbutus Slab<b>400<\/b>"},{"value":"Archivo Black","text":"Archivo Black<b>400<\/b>"},{"value":"Archivo Narrow","text":"Archivo Narrow<b>400,700<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Arizonia","text":"Arizonia<b>400<\/b>"},{"value":"Armata","text":"Armata<b>400<\/b>"},{"value":"Arya","text":"Arya<b>400,700<\/b>"},{"value":"Asap","text":"Asap<b>400,700<\/b>"},{"value":"Asar","text":"Asar<b>400<\/b>"},{"value":"Audiowide","text":"Audiowide<b>400<\/b>"},{"value":"Autour One","text":"Autour One<b>400<\/b>"},{"value":"Average","text":"Average<b>400<\/b>"},{"value":"Average Sans","text":"Average Sans<b>400<\/b>"},{"value":"Averia Gruesa Libre","text":"Averia Gruesa Libre<b>400<\/b>"},{"value":"Basic","text":"Basic<b>400<\/b>"},{"value":"Belleza","text":"Belleza<b>400<\/b>"},{"value":"BenchNine","text":"BenchNine<b>300,400,700<\/b>"},{"value":"Berkshire Swash","text":"Berkshire Swash<b>400<\/b>"},{"value":"Bigelow Rules","text":"Bigelow Rules<b>400<\/b>"},{"value":"Bilbo","text":"Bilbo<b>400<\/b>"},{"value":"Bilbo Swash Caps","text":"Bilbo Swash Caps<b>400<\/b>"},{"value":"Biryani","text":"Biryani<b>200,300,400,600,700,800,900<\/b>"},{"value":"Bitter","text":"Bitter<b>400,700<\/b>"},{"value":"Black Ops One","text":"Black Ops One<b>400<\/b>"},{"value":"Bowlby One SC","text":"Bowlby One SC<b>400<\/b>"},{"value":"Bree Serif","text":"Bree Serif<b>400<\/b>"},{"value":"Bubblegum Sans","text":"Bubblegum Sans<b>400<\/b>"},{"value":"Bubbler One","text":"Bubbler One<b>400<\/b>"},{"value":"Buenard","text":"Buenard<b>400,700<\/b>"},{"value":"Butcherman","text":"Butcherman<b>400<\/b>"},{"value":"Butterfly Kids","text":"Butterfly Kids<b>400<\/b>"},{"value":"Cambay","text":"Cambay<b>400,700<\/b>"},{"value":"Cantata One","text":"Cantata One<b>400<\/b>"},{"value":"Cantora One","text":"Cantora One<b>400<\/b>"},{"value":"Capriola","text":"Capriola<b>400<\/b>"},{"value":"Cardo","text":"Cardo<b>400,700<\/b>"},{"value":"Catamaran","text":"Catamaran<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Caudex","text":"Caudex<b>400,700<\/b>"},{"value":"Caveat","text":"Caveat<b>400,700<\/b>"},{"value":"Caveat Brush","text":"Caveat Brush<b>400<\/b>"},{"value":"Chango","text":"Chango<b>400<\/b>"},{"value":"Chau Philomene One","text":"Chau Philomene One<b>400<\/b>"},{"value":"Chela One","text":"Chela One<b>400<\/b>"},{"value":"Chelsea Market","text":"Chelsea Market<b>400<\/b>"},{"value":"Cherry Swash","text":"Cherry Swash<b>400,700<\/b>"},{"value":"Chicle","text":"Chicle<b>400<\/b>"},{"value":"Chonburi","text":"Chonburi<b>400<\/b>"},{"value":"Clicker Script","text":"Clicker Script<b>400<\/b>"},{"value":"Coda","text":"Coda<b>400,800<\/b>"},{"value":"Coda Caption","text":"Coda Caption<b>800<\/b>"},{"value":"Codystar","text":"Codystar<b>300,400<\/b>"},{"value":"Combo","text":"Combo<b>400<\/b>"},{"value":"Comfortaa","text":"Comfortaa<b>300,400,700<\/b>"},{"value":"Concert One","text":"Concert One<b>400<\/b>"},{"value":"Condiment","text":"Condiment<b>400<\/b>"},{"value":"Corben","text":"Corben<b>400,700<\/b>"},{"value":"Courgette","text":"Courgette<b>400<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"Crete Round","text":"Crete Round<b>400<\/b>"},{"value":"Croissant One","text":"Croissant One<b>400<\/b>"},{"value":"Cuprum","text":"Cuprum<b>400,700<\/b>"},{"value":"Cutive","text":"Cutive<b>400<\/b>"},{"value":"Cutive Mono","text":"Cutive Mono<b>400<\/b>"},{"value":"Dekko","text":"Dekko<b>400<\/b>"},{"value":"Denk One","text":"Denk One<b>400<\/b>"},{"value":"Devonshire","text":"Devonshire<b>400<\/b>"},{"value":"Didact Gothic","text":"Didact Gothic<b>400<\/b>"},{"value":"Diplomata","text":"Diplomata<b>400<\/b>"},{"value":"Diplomata SC","text":"Diplomata SC<b>400<\/b>"},{"value":"Domine","text":"Domine<b>400,700<\/b>"},{"value":"Donegal One","text":"Donegal One<b>400<\/b>"},{"value":"Doppio One","text":"Doppio One<b>400<\/b>"},{"value":"Dosis","text":"Dosis<b>200,300,400,500,600,700,800<\/b>"},{"value":"Dr Sugiyama","text":"Dr Sugiyama<b>400<\/b>"},{"value":"Duru Sans","text":"Duru Sans<b>400<\/b>"},{"value":"Dynalight","text":"Dynalight<b>400<\/b>"},{"value":"EB Garamond","text":"EB Garamond<b>400<\/b>"},{"value":"Eagle Lake","text":"Eagle Lake<b>400<\/b>"},{"value":"Eater","text":"Eater<b>400<\/b>"},{"value":"Economica","text":"Economica<b>400,700<\/b>"},{"value":"Eczar","text":"Eczar<b>400,500,600,700,800<\/b>"},{"value":"Ek Mukta","text":"Ek Mukta<b>200,300,400,500,600,700,800<\/b>"},{"value":"Elsie","text":"Elsie<b>400,900<\/b>"},{"value":"Elsie Swash Caps","text":"Elsie Swash Caps<b>400,900<\/b>"},{"value":"Emblema One","text":"Emblema One<b>400<\/b>"},{"value":"Emilys Candy","text":"Emilys Candy<b>400<\/b>"},{"value":"Englebert","text":"Englebert<b>400<\/b>"},{"value":"Enriqueta","text":"Enriqueta<b>400,700<\/b>"},{"value":"Esteban","text":"Esteban<b>400<\/b>"},{"value":"Euphoria Script","text":"Euphoria Script<b>400<\/b>"},{"value":"Ewert","text":"Ewert<b>400<\/b>"},{"value":"Exo","text":"Exo<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Exo 2","text":"Exo 2<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Fauna One","text":"Fauna One<b>400<\/b>"},{"value":"Felipa","text":"Felipa<b>400<\/b>"},{"value":"Fenix","text":"Fenix<b>400<\/b>"},{"value":"Fira Mono","text":"Fira Mono<b>400,700<\/b>"},{"value":"Fira Sans","text":"Fira Sans<b>300,400,500,700<\/b>"},{"value":"Fjalla One","text":"Fjalla One<b>400<\/b>"},{"value":"Fondamento","text":"Fondamento<b>400<\/b>"},{"value":"Forum","text":"Forum<b>400<\/b>"},{"value":"Francois One","text":"Francois One<b>400<\/b>"},{"value":"Freckle Face","text":"Freckle Face<b>400<\/b>"},{"value":"Fresca","text":"Fresca<b>400<\/b>"},{"value":"Fruktur","text":"Fruktur<b>400<\/b>"},{"value":"Gabriela","text":"Gabriela<b>400<\/b>"},{"value":"Gafata","text":"Gafata<b>400<\/b>"},{"value":"Galindo","text":"Galindo<b>400<\/b>"},{"value":"Gentium Basic","text":"Gentium Basic<b>400,700<\/b>"},{"value":"Gentium Book Basic","text":"Gentium Book Basic<b>400,700<\/b>"},{"value":"Gilda Display","text":"Gilda Display<b>400<\/b>"},{"value":"Glass Antiqua","text":"Glass Antiqua<b>400<\/b>"},{"value":"Glegoo","text":"Glegoo<b>400,700<\/b>"},{"value":"Grand Hotel","text":"Grand Hotel<b>400<\/b>"},{"value":"Great Vibes","text":"Great Vibes<b>400<\/b>"},{"value":"Griffy","text":"Griffy<b>400<\/b>"},{"value":"Gruppo","text":"Gruppo<b>400<\/b>"},{"value":"Gudea","text":"Gudea<b>400,700<\/b>"},{"value":"Habibi","text":"Habibi<b>400<\/b>"},{"value":"Halant","text":"Halant<b>300,400,500,600,700<\/b>"},{"value":"Hammersmith One","text":"Hammersmith One<b>400<\/b>"},{"value":"Hanalei","text":"Hanalei<b>400<\/b>"},{"value":"Hanalei Fill","text":"Hanalei Fill<b>400<\/b>"},{"value":"Happy Monkey","text":"Happy Monkey<b>400<\/b>"},{"value":"Headland One","text":"Headland One<b>400<\/b>"},{"value":"Herr Von Muellerhoff","text":"Herr Von Muellerhoff<b>400<\/b>"},{"value":"Hind","text":"Hind<b>300,400,500,600,700<\/b>"},{"value":"Hind Siliguri","text":"Hind Siliguri<b>300,400,500,600,700<\/b>"},{"value":"Hind Vadodara","text":"Hind Vadodara<b>300,400,500,600,700<\/b>"},{"value":"Homenaje","text":"Homenaje<b>400<\/b>"},{"value":"Imprima","text":"Imprima<b>400<\/b>"},{"value":"Inconsolata","text":"Inconsolata<b>400,700<\/b>"},{"value":"Inder","text":"Inder<b>400<\/b>"},{"value":"Inika","text":"Inika<b>400,700<\/b>"},{"value":"Inknut Antiqua","text":"Inknut Antiqua<b>300,400,500,600,700,800,900<\/b>"},{"value":"Istok Web","text":"Istok Web<b>400,700<\/b>"},{"value":"Italianno","text":"Italianno<b>400<\/b>"},{"value":"Itim","text":"Itim<b>400<\/b>"},{"value":"Jaldi","text":"Jaldi<b>400,700<\/b>"},{"value":"Jim Nightshade","text":"Jim Nightshade<b>400<\/b>"},{"value":"Jockey One","text":"Jockey One<b>400<\/b>"},{"value":"Jolly Lodger","text":"Jolly Lodger<b>400<\/b>"},{"value":"Josefin Sans","text":"Josefin Sans<b>100,300,400,600,700<\/b>"},{"value":"Joti One","text":"Joti One<b>400<\/b>"},{"value":"Judson","text":"Judson<b>400,700<\/b>"},{"value":"Julius Sans One","text":"Julius Sans One<b>400<\/b>"},{"value":"Jura","text":"Jura<b>300,400,500,600<\/b>"},{"value":"Just Me Again Down Here","text":"Just Me Again Down Here<b>400<\/b>"},{"value":"Kalam","text":"Kalam<b>300,400,700<\/b>"},{"value":"Kanit","text":"Kanit<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Karla","text":"Karla<b>400,700<\/b>"},{"value":"Karma","text":"Karma<b>300,400,500,600,700<\/b>"},{"value":"Kaushan Script","text":"Kaushan Script<b>400<\/b>"},{"value":"Kavoon","text":"Kavoon<b>400<\/b>"},{"value":"Keania One","text":"Keania One<b>400<\/b>"},{"value":"Kelly Slab","text":"Kelly Slab<b>400<\/b>"},{"value":"Khand","text":"Khand<b>300,400,500,600,700<\/b>"},{"value":"Khula","text":"Khula<b>300,400,600,700,800<\/b>"},{"value":"Knewave","text":"Knewave<b>400<\/b>"},{"value":"Kotta One","text":"Kotta One<b>400<\/b>"},{"value":"Krona One","text":"Krona One<b>400<\/b>"},{"value":"Kurale","text":"Kurale<b>400<\/b>"},{"value":"Laila","text":"Laila<b>300,400,500,600,700<\/b>"},{"value":"Lancelot","text":"Lancelot<b>400<\/b>"},{"value":"Lato","text":"Lato<b>100,300,400,700,900<\/b>"},{"value":"Ledger","text":"Ledger<b>400<\/b>"},{"value":"Lekton","text":"Lekton<b>400,700<\/b>"},{"value":"Libre Baskerville","text":"Libre Baskerville<b>400,700<\/b>"},{"value":"Life Savers","text":"Life Savers<b>400,700<\/b>"},{"value":"Lilita One","text":"Lilita One<b>400<\/b>"},{"value":"Lily Script One","text":"Lily Script One<b>400<\/b>"},{"value":"Limelight","text":"Limelight<b>400<\/b>"},{"value":"Lobster","text":"Lobster<b>400<\/b>"},{"value":"Lora","text":"Lora<b>400,700<\/b>"},{"value":"Lovers Quarrel","text":"Lovers Quarrel<b>400<\/b>"},{"value":"Magra","text":"Magra<b>400,700<\/b>"},{"value":"Marcellus","text":"Marcellus<b>400<\/b>"},{"value":"Marcellus SC","text":"Marcellus SC<b>400<\/b>"},{"value":"Marck Script","text":"Marck Script<b>400<\/b>"},{"value":"Margarine","text":"Margarine<b>400<\/b>"},{"value":"Marmelad","text":"Marmelad<b>400<\/b>"},{"value":"Martel","text":"Martel<b>200,300,400,600,700,800,900<\/b>"},{"value":"Martel Sans","text":"Martel Sans<b>200,300,400,600,700,800,900<\/b>"},{"value":"McLaren","text":"McLaren<b>400<\/b>"},{"value":"MedievalSharp","text":"MedievalSharp<b>400<\/b>"},{"value":"Meie Script","text":"Meie Script<b>400<\/b>"},{"value":"Merienda","text":"Merienda<b>400,700<\/b>"},{"value":"Merriweather","text":"Merriweather<b>300,400,700,900<\/b>"},{"value":"Merriweather Sans","text":"Merriweather Sans<b>300,400,700,800<\/b>"},{"value":"Metal Mania","text":"Metal Mania<b>400<\/b>"},{"value":"Metamorphous","text":"Metamorphous<b>400<\/b>"},{"value":"Milonga","text":"Milonga<b>400<\/b>"},{"value":"Miss Fajardose","text":"Miss Fajardose<b>400<\/b>"},{"value":"Modak","text":"Modak<b>400<\/b>"},{"value":"Modern Antiqua","text":"Modern Antiqua<b>400<\/b>"},{"value":"Molengo","text":"Molengo<b>400<\/b>"},{"value":"Molle","text":"Molle<b><\/b>"},{"value":"Monda","text":"Monda<b>400,700<\/b>"},{"value":"Monsieur La Doulaise","text":"Monsieur La Doulaise<b>400<\/b>"},{"value":"Mouse Memoirs","text":"Mouse Memoirs<b>400<\/b>"},{"value":"Mr Bedfort","text":"Mr Bedfort<b>400<\/b>"},{"value":"Mr Dafoe","text":"Mr Dafoe<b>400<\/b>"},{"value":"Mr De Haviland","text":"Mr De Haviland<b>400<\/b>"},{"value":"Mrs Saint Delafield","text":"Mrs Saint Delafield<b>400<\/b>"},{"value":"Mrs Sheppards","text":"Mrs Sheppards<b>400<\/b>"},{"value":"Mystery Quest","text":"Mystery Quest<b>400<\/b>"},{"value":"Neuton","text":"Neuton<b>200,300,400,700,800<\/b>"},{"value":"New Rocker","text":"New Rocker<b>400<\/b>"},{"value":"News Cycle","text":"News Cycle<b>400,700<\/b>"},{"value":"Niconne","text":"Niconne<b>400<\/b>"},{"value":"Norican","text":"Norican<b>400<\/b>"},{"value":"Nosifer","text":"Nosifer<b>400<\/b>"},{"value":"Noticia Text","text":"Noticia Text<b>400,700<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Oldenburg","text":"Oldenburg<b>400<\/b>"},{"value":"Oleo Script","text":"Oleo Script<b>400,700<\/b>"},{"value":"Oleo Script Swash Caps","text":"Oleo Script Swash Caps<b>400,700<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Oranienbaum","text":"Oranienbaum<b>400<\/b>"},{"value":"Oregano","text":"Oregano<b>400<\/b>"},{"value":"Orienta","text":"Orienta<b>400<\/b>"},{"value":"Oswald","text":"Oswald<b>300,400,700<\/b>"},{"value":"Overlock","text":"Overlock<b>400,700,900<\/b>"},{"value":"Overlock SC","text":"Overlock SC<b>400<\/b>"},{"value":"Oxygen","text":"Oxygen<b>300,400,700<\/b>"},{"value":"Oxygen Mono","text":"Oxygen Mono<b>400<\/b>"},{"value":"PT Mono","text":"PT Mono<b>400<\/b>"},{"value":"PT Sans","text":"PT Sans<b>400,700<\/b>"},{"value":"PT Sans Caption","text":"PT Sans Caption<b>400,700<\/b>"},{"value":"PT Sans Narrow","text":"PT Sans Narrow<b>400,700<\/b>"},{"value":"PT Serif","text":"PT Serif<b>400,700<\/b>"},{"value":"PT Serif Caption","text":"PT Serif Caption<b>400<\/b>"},{"value":"Palanquin","text":"Palanquin<b>100,200,300,400,500,600,700<\/b>"},{"value":"Palanquin Dark","text":"Palanquin Dark<b>400,500,600,700<\/b>"},{"value":"Parisienne","text":"Parisienne<b>400<\/b>"},{"value":"Passero One","text":"Passero One<b>400<\/b>"},{"value":"Passion One","text":"Passion One<b>400,700,900<\/b>"},{"value":"Pathway Gothic One","text":"Pathway Gothic One<b>400<\/b>"},{"value":"Patrick Hand","text":"Patrick Hand<b>400<\/b>"},{"value":"Patrick Hand SC","text":"Patrick Hand SC<b>400<\/b>"},{"value":"Peralta","text":"Peralta<b>400<\/b>"},{"value":"Petit Formal Script","text":"Petit Formal Script<b>400<\/b>"},{"value":"Piedra","text":"Piedra<b>400<\/b>"},{"value":"Pirata One","text":"Pirata One<b>400<\/b>"},{"value":"Plaster","text":"Plaster<b>400<\/b>"},{"value":"Play","text":"Play<b>400,700<\/b>"},{"value":"Playball","text":"Playball<b>400<\/b>"},{"value":"Playfair Display","text":"Playfair Display<b>400,700,900<\/b>"},{"value":"Playfair Display SC","text":"Playfair Display SC<b>400,700,900<\/b>"},{"value":"Poiret One","text":"Poiret One<b>400<\/b>"},{"value":"Pontano Sans","text":"Pontano Sans<b>400<\/b>"},{"value":"Poppins","text":"Poppins<b>300,400,500,600,700<\/b>"},{"value":"Pragati Narrow","text":"Pragati Narrow<b>400,700<\/b>"},{"value":"Press Start 2P","text":"Press Start 2P<b>400<\/b>"},{"value":"Princess Sofia","text":"Princess Sofia<b>400<\/b>"},{"value":"Prosto One","text":"Prosto One<b>400<\/b>"},{"value":"Purple Purse","text":"Purple Purse<b>400<\/b>"},{"value":"Quando","text":"Quando<b>400<\/b>"},{"value":"Quattrocento","text":"Quattrocento<b>400,700<\/b>"},{"value":"Quattrocento Sans","text":"Quattrocento Sans<b>400,700<\/b>"},{"value":"Quintessential","text":"Quintessential<b>400<\/b>"},{"value":"Qwigley","text":"Qwigley<b>400<\/b>"},{"value":"Racing Sans One","text":"Racing Sans One<b>400<\/b>"},{"value":"Radley","text":"Radley<b>400<\/b>"},{"value":"Rajdhani","text":"Rajdhani<b>300,400,500,600,700<\/b>"},{"value":"Raleway Dots","text":"Raleway Dots<b>400<\/b>"},{"value":"Rambla","text":"Rambla<b>400,700<\/b>"},{"value":"Rammetto One","text":"Rammetto One<b>400<\/b>"},{"value":"Ranchers","text":"Ranchers<b>400<\/b>"},{"value":"Ranga","text":"Ranga<b>400,700<\/b>"},{"value":"Revalia","text":"Revalia<b>400<\/b>"},{"value":"Rhodium Libre","text":"Rhodium Libre<b>400<\/b>"},{"value":"Ribeye","text":"Ribeye<b>400<\/b>"},{"value":"Ribeye Marrow","text":"Ribeye Marrow<b>400<\/b>"},{"value":"Righteous","text":"Righteous<b>400<\/b>"},{"value":"Risque","text":"Risque<b>400<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Romanesco","text":"Romanesco<b>400<\/b>"},{"value":"Ropa Sans","text":"Ropa Sans<b>400<\/b>"},{"value":"Rosarivo","text":"Rosarivo<b>400<\/b>"},{"value":"Rozha One","text":"Rozha One<b>400<\/b>"},{"value":"Rubik","text":"Rubik<b>300,400,500,700,900<\/b>"},{"value":"Rubik Mono One","text":"Rubik Mono One<b>400<\/b>"},{"value":"Rubik One","text":"Rubik One<b>400<\/b>"},{"value":"Ruda","text":"Ruda<b>400,700,900<\/b>"},{"value":"Rufina","text":"Rufina<b>400,700<\/b>"},{"value":"Ruge Boogie","text":"Ruge Boogie<b>400<\/b>"},{"value":"Ruluko","text":"Ruluko<b>400<\/b>"},{"value":"Rum Raisin","text":"Rum Raisin<b>400<\/b>"},{"value":"Ruslan Display","text":"Ruslan Display<b>400<\/b>"},{"value":"Russo One","text":"Russo One<b>400<\/b>"},{"value":"Ruthie","text":"Ruthie<b>400<\/b>"},{"value":"Rye","text":"Rye<b>400<\/b>"},{"value":"Sacramento","text":"Sacramento<b>400<\/b>"},{"value":"Sanchez","text":"Sanchez<b>400<\/b>"},{"value":"Sancreek","text":"Sancreek<b>400<\/b>"},{"value":"Sarala","text":"Sarala<b>400,700<\/b>"},{"value":"Sarina","text":"Sarina<b>400<\/b>"},{"value":"Sarpanch","text":"Sarpanch<b>400,500,600,700,800,900<\/b>"},{"value":"Scada","text":"Scada<b>400,700<\/b>"},{"value":"Seaweed Script","text":"Seaweed Script<b>400<\/b>"},{"value":"Sevillana","text":"Sevillana<b>400<\/b>"},{"value":"Seymour One","text":"Seymour One<b>400<\/b>"},{"value":"Shadows Into Light Two","text":"Shadows Into Light Two<b>400<\/b>"},{"value":"Share","text":"Share<b>400,700<\/b>"},{"value":"Shojumaru","text":"Shojumaru<b>400<\/b>"},{"value":"Signika","text":"Signika<b>300,400,600,700<\/b>"},{"value":"Signika Negative","text":"Signika Negative<b>300,400,600,700<\/b>"},{"value":"Simonetta","text":"Simonetta<b>400,900<\/b>"},{"value":"Sintony","text":"Sintony<b>400,700<\/b>"},{"value":"Skranji","text":"Skranji<b>400,700<\/b>"},{"value":"Slabo 13px","text":"Slabo 13px<b>400<\/b>"},{"value":"Slabo 27px","text":"Slabo 27px<b>400<\/b>"},{"value":"Sniglet","text":"Sniglet<b>400,800<\/b>"},{"value":"Snowburst One","text":"Snowburst One<b>400<\/b>"},{"value":"Sonsie One","text":"Sonsie One<b>400<\/b>"},{"value":"Sorts Mill Goudy","text":"Sorts Mill Goudy<b>400<\/b>"},{"value":"Source Code Pro","text":"Source Code Pro<b>200,300,400,500,600,700,900<\/b>"},{"value":"Source Sans Pro","text":"Source Sans Pro<b>200,300,400,600,700,900<\/b>"},{"value":"Source Serif Pro","text":"Source Serif Pro<b>400,600,700<\/b>"},{"value":"Spinnaker","text":"Spinnaker<b>400<\/b>"},{"value":"Stalemate","text":"Stalemate<b>400<\/b>"},{"value":"Stalinist One","text":"Stalinist One<b>400<\/b>"},{"value":"Stint Ultra Condensed","text":"Stint Ultra Condensed<b>400<\/b>"},{"value":"Stint Ultra Expanded","text":"Stint Ultra Expanded<b>400<\/b>"},{"value":"Stoke","text":"Stoke<b>300,400<\/b>"},{"value":"Sumana","text":"Sumana<b>400,700<\/b>"},{"value":"Sura","text":"Sura<b>400,700<\/b>"},{"value":"Tauri","text":"Tauri<b>400<\/b>"},{"value":"Teko","text":"Teko<b>300,400,500,600,700<\/b>"},{"value":"Tenor Sans","text":"Tenor Sans<b>400<\/b>"},{"value":"Text Me One","text":"Text Me One<b>400<\/b>"},{"value":"Tillana","text":"Tillana<b>400,500,600,700,800<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"},{"value":"Titan One","text":"Titan One<b>400<\/b>"},{"value":"Titillium Web","text":"Titillium Web<b>200,300,400,600,700,900<\/b>"},{"value":"Trocchi","text":"Trocchi<b>400<\/b>"},{"value":"Trykker","text":"Trykker<b>400<\/b>"},{"value":"Ubuntu","text":"Ubuntu<b>300,400,500,700<\/b>"},{"value":"Ubuntu Condensed","text":"Ubuntu Condensed<b>400<\/b>"},{"value":"Ubuntu Mono","text":"Ubuntu Mono<b>400,700<\/b>"},{"value":"Underdog","text":"Underdog<b>400<\/b>"},{"value":"Unica One","text":"Unica One<b>400<\/b>"},{"value":"Vampiro One","text":"Vampiro One<b>400<\/b>"},{"value":"Varela","text":"Varela<b>400<\/b>"},{"value":"Vesper Libre","text":"Vesper Libre<b>400,500,700,900<\/b>"},{"value":"Viga","text":"Viga<b>400<\/b>"},{"value":"Voces","text":"Voces<b>400<\/b>"},{"value":"Warnes","text":"Warnes<b>400<\/b>"},{"value":"Wellfleet","text":"Wellfleet<b>400<\/b>"},{"value":"Wendy One","text":"Wendy One<b>400<\/b>"},{"value":"Work Sans","text":"Work Sans<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Yanone Kaffeesatz","text":"Yanone Kaffeesatz<b>200,300,400,700<\/b>"},{"value":"Yantramanav","text":"Yantramanav<b>100,300,400,500,700,900<\/b>"},{"value":"Yeseva One","text":"Yeseva One<b>400<\/b>"}];
OfflajnFont_tamil = [{"value":"Catamaran","text":"Catamaran<b>100,200,300,400,500,600,700,800,900<\/b>"}];
OfflajnFont_telugu = [{"value":"Dhurjati","text":"Dhurjati<b>400<\/b>"},{"value":"Gidugu","text":"Gidugu<b>400<\/b>"},{"value":"Gurajada","text":"Gurajada<b>400<\/b>"},{"value":"Lakki Reddy","text":"Lakki Reddy<b>400<\/b>"},{"value":"Mallanna","text":"Mallanna<b>400<\/b>"},{"value":"Mandali","text":"Mandali<b>400<\/b>"},{"value":"NTR","text":"NTR<b>400<\/b>"},{"value":"Peddana","text":"Peddana<b>400<\/b>"},{"value":"Ramabhadra","text":"Ramabhadra<b>400<\/b>"},{"value":"Ramaraja","text":"Ramaraja<b>400<\/b>"},{"value":"Ravi Prakash","text":"Ravi Prakash<b>400<\/b>"},{"value":"Sree Krushnadevaraya","text":"Sree Krushnadevaraya<b>400<\/b>"},{"value":"Suranna","text":"Suranna<b>400<\/b>"},{"value":"Suravaram","text":"Suravaram<b>400<\/b>"},{"value":"Tenali Ramakrishna","text":"Tenali Ramakrishna<b>400<\/b>"},{"value":"Timmana","text":"Timmana<b>400<\/b>"}];
OfflajnFont_thai = [{"value":"Chonburi","text":"Chonburi<b>400<\/b>"},{"value":"Itim","text":"Itim<b>400<\/b>"},{"value":"Kanit","text":"Kanit<b>100,200,300,400,500,600,700,800,900<\/b>"}];
OfflajnFont_vietnamese = [{"value":"Alegreya Sans","text":"Alegreya Sans<b>100,300,400,500,700,800,900<\/b>"},{"value":"Alegreya Sans SC","text":"Alegreya Sans SC<b>100,300,400,500,700,800,900<\/b>"},{"value":"Arimo","text":"Arimo<b>400,700<\/b>"},{"value":"Chonburi","text":"Chonburi<b>400<\/b>"},{"value":"Cousine","text":"Cousine<b>400,700<\/b>"},{"value":"EB Garamond","text":"EB Garamond<b>400<\/b>"},{"value":"Itim","text":"Itim<b>400<\/b>"},{"value":"Judson","text":"Judson<b>400,700<\/b>"},{"value":"Kanit","text":"Kanit<b>100,200,300,400,500,600,700,800,900<\/b>"},{"value":"Lobster","text":"Lobster<b>400<\/b>"},{"value":"Noticia Text","text":"Noticia Text<b>400,700<\/b>"},{"value":"Noto Sans","text":"Noto Sans<b>400,700<\/b>"},{"value":"Noto Serif","text":"Noto Serif<b>400,700<\/b>"},{"value":"Open Sans","text":"Open Sans<b>300,400,600,700,800<\/b>"},{"value":"Open Sans Condensed","text":"Open Sans Condensed<b>300,700<\/b>"},{"value":"Patrick Hand","text":"Patrick Hand<b>400<\/b>"},{"value":"Patrick Hand SC","text":"Patrick Hand SC<b>400<\/b>"},{"value":"Roboto","text":"Roboto<b>100,300,400,500,700,900<\/b>"},{"value":"Roboto Condensed","text":"Roboto Condensed<b>300,400,700<\/b>"},{"value":"Roboto Mono","text":"Roboto Mono<b>100,300,400,500,700<\/b>"},{"value":"Roboto Slab","text":"Roboto Slab<b>100,300,400,700<\/b>"},{"value":"Source Sans Pro","text":"Source Sans Pro<b>200,300,400,600,700,900<\/b>"},{"value":"Tinos","text":"Tinos<b>400,700<\/b>"}];

dojo.declare("ThemeLevel", null, {
	constructor: function(args){
    dojo.mixin(this, args);
    for (var k in this.values) {
      if((/^level[0-9]*/).test(k)){
          var formel = document.adminForm[this.control+"["+k+"]"];

          if(formel && formel.length){
            if(formel[0].nodeName == "INPUT"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == this.values[k]){
                  formel[i].checked = true;
                }
              }
            }else if(formel[0].nodeName == "OPTION"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == this.values[k]){
                  formel.selectedIndex = formel[i].index;
                }
              }
            }
          }else{
            try{
              formel.value = this.values[k];
              if(formel.color){
                formel.color.active.val('ahex', formel.value);
              }
              OfflajnFireEvent(formel, 'change');
            }catch(e){
            };
         }
      }
    }

    this.showedRemoveBtn = null;
    this.num = 0;
    this.states = new Array();
    this.loadLevels();
    this.addAddLevelBtn();
    this.addRemoveLevelBtn();
    this.loadStates();
    if(this.version == "15") {
      dojo.removeClass(this.el.parentNode.parentNode, 'blue');
      dojo.addClass(this.el.parentNode.parentNode, 'levelgroup');
    } else {
      dojo.removeClass(this.el.parentNode, 'blue');
      dojo.addClass(this.el.parentNode, 'levelgroup');
    }
  },

  loadLevels: function(){
    this.num = 0;
    this.levels = dojo.query('.legend', this.el);
    dojo.forEach(this.levels, function(el){
      //this.states[this.num] = 0;
      if(el.opener) dojo.disconnect(el.opener);
      var openerEl = dojo.query('h3', el)[0];
      openerEl.animated = dojo.query('.content', el)[0];
      dojo.attr(openerEl.animated, 'id', 'offlajnlevelpanel-' + this.num);
      el.opener = dojo.connect(openerEl, 'onclick', this, 'openClose');
      this.num++;
    }, this);
  },

  openClose: function(e) {
    var opener = e.currentTarget;
        var el = e.currentTarget.animated;
        dojo.style(el, 'overflow', 'hidden');
        var h = parseInt(dojo.position(el).h);
        var id = parseInt(el.id.replace('offlajnlevelpanel-', ''));
        if(h == 0){
          dojo.addClass(opener, 'jpane-toggler-down');
          dojo.addClass(opener, 'pane-toggler-down');
          this.states[id] = 1;
          localStorage['offlajnlevels'] = dojo.toJson(this.states);
          h = parseInt(dojo.position(dojo.query('table', el)[0] || dojo.query('fieldset', el)[0]).h);
        }else{
          dojo.removeClass(opener, 'jpane-toggler-down');
          dojo.removeClass(opener, 'pane-toggler-down');
          this.states[id] = 0;
          localStorage['offlajnlevels'] = dojo.toJson(this.states);
          h=0;
        }
        dojo.animateProperty({
          node: el,
          properties: {
              height: h
          },
          onEnd: function() {if(h) dojo.style(this.node, {height: 'auto', overflow: ''})}
        }).play();
  },

  loadStates: function() {
    var states = dojo.fromJson(localStorage['offlajnlevels'] || '[]');
    dojo.forEach(states, function(el, i){
      if(el) {
          this.states[i] = el;
          var opener = dojo.byId('offlajnlevelpanel-' + i);
          if(opener){
            var h = parseInt(dojo.position(dojo.query('table', opener)[0] || dojo.query('fieldset', opener)[0]).h);
            dojo.style(opener, {height: 'auto', overflow: ''});
          }
        }
    }, this);
  },

  addRemoveLevelBtn: function(){
    this.removeLevelBtn = dojo.create('div', {'class' : 'removeBtn', innerHTML: '<div><div>REMOVE LEVEL</div></div>'}, this.el);
    this.showRemoveLevelBtn();
    dojo.connect(this.removeLevelBtn, 'onclick', this, 'removeLevel');
  },

  showRemoveLevelBtn: function(){
    if(this.levels.length <= 1){
      dojo.removeClass(this.removeLevelBtn, 'removeBtnShow');
      return;
    }
    dojo.addClass(this.removeLevelBtn, 'removeBtnShow');
  },

  addAddLevelBtn: function(){
    this.addLevelBtn = dojo.create('div', {'class' : 'addBtn', innerHTML: '<div><div>ADD LEVEL</div></div>'}, this.el);
    dojo.connect(this.addLevelBtn, 'onclick', this, 'addLevel');
  },

  removeLevel: function(e){
    this.levels[this.levels.length-1].parentNode.removeChild(this.levels[this.levels.length-1]);
    this.loadLevels();
    this.showRemoveLevelBtn();
  },

  addLevel: function(e){
    var lastEl = this.levels[this.levels.length-1];
    var html = this.render.replace(/\[x\]/g,this.levels.length+1);
    dojo.create('div', {'innerHTML' : html}, lastEl, 'after');
    this.loadLevels();
    this.showRemoveLevelBtn();
    eval(this.scripts.replace(/\[x\]/g,this.levels.length));
		if (window.init_conditions) init_conditions();
  }
});

dojo.addOnLoad(function(){
      new OfflajnList({
        name: "jformparamsmoduleparametersTabthemethemeskin",
        options: [{"value":"custom","text":"Custom"},{"value":"clean_blue_blur","text":"Blue blur"},{"value":"clean_brown_blur","text":"Brown blur"},{"value":"clean_eclipse","text":"Eclipse"},{"value":"clean_gaussian_blur","text":"Gaussian blur"},{"value":"clean_green_blur","text":"Green blur"},{"value":"clean_green_triangles","text":"Green triangles"},{"value":"clean_sky_blur","text":"Sky blur"},{"value":"clean_sundown","text":"Sundown"}],
        selectedIndex: 0,
        json: "",
        width: 0,
        height: 0,
        fireshow: 0
      });
    

      window.themeskin = new OfflajnSkin({
        name: "themeskin",
        id: "jformparamsmoduleparametersTabthemethemeskin",
        data: {"clean_blue_blur":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/blueblur.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/blue_blur.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","reseticon":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/reset\/reset-0.png","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1countbg":"rgba(0, 0, 0, 0.22)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_brown_blur":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/brownblur.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/brown_blur.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_eclipse":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/eclipse.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/eclipse.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_gaussian_blur":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/gaussianblur.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/gaussian_blur.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_green_blur":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/greenblur.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/green_blur.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_green_triangles":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/greentriangles.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/green_triangles.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_sky_blur":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/skyblur.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/sky_blur.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"},"clean_sundown":{"preview":"modules\/mod_vertical_menu\/params\/images\/clean_skins\/sundown.png","titlefont":"{\"Text\":{\"color\":\"#ffffff\"}}","level1font":"{\"Text\":{\"color\":\"#ffffff\"}}","level1descfont":"{\"Text\":{\"color\":\"#ffffff\"}}","sitebg":"#444444","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/sundown.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)","margin":"0|*|0|*|10|*|0|*|px","menuitemmargin":"7||px","level1padding":"7|*|12|*|7|*|12|*|px","borderradius":"0|*|0|*|0|*|0|*|px","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff"}},
        root: 'http://printervoronezh.ru/',
        control: "jform[params][moduleparametersTab][theme]",
        dependency: ''
      });
    

      new OfflajnList({
        name: "jformparamsmoduleparametersTabthemefontskin",
        options: [{"value":"custom","text":"Custom"},{"value":"clean_inherit","text":"Inherit"},{"value":"clean_arial","text":"Arial"},{"value":"clean_open_sans","text":"Open sans"},{"value":"clean_roboto","text":"Roboto"},{"value":"clean_lato","text":"Lato"},{"value":"clean_roboto_condensed","text":"Roboto condensed"},{"value":"clean_oswald","text":"Oswald"},{"value":"clean_lora","text":"Lora"},{"value":"clean_source_sans_pro","text":"Source sans pro"},{"value":"clean_pt_sans","text":"Pt sans"},{"value":"clean_open_sans_condensed","text":"Open sans condensed"},{"value":"clean_droid_sans","text":"Droid sans"}],
        selectedIndex: 0,
        json: "",
        width: 0,
        height: 10,
        fireshow: 0
      });
    

      window.fontskin = new OfflajnSkin({
        name: "fontskin",
        id: "jformparamsmoduleparametersTabthemefontskin",
        data: {"clean_inherit":{"titlefont":"{\"Text\":{\"type\":\"0\",\"size\":\"20||px\",\"bold\":\"0\",\"afont\":\"inherit||1\"}}","level1font":"{\"Text\":{\"type\":\"0\",\"size\":\"16||px\",\"bold\":\"0\",\"afont\":\"inherit||1\",\"lineheight\":\"normal\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"0\",\"size\":\"13||px\",\"bold\":\"0\",\"afont\":\"inherit||1\",\"lineheight\":\"normal\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"0\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"inherit||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"textdecor\":\"900\"}}","level1ofont":"{\"Text\":{\"type\":\"0\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"inherit||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"0\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"inherit||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"0\",\"afont\":\"inherit||1\"}}","roundfont":"{\"Text\":{\"type\":\"0\",\"afont\":\"inherit||1\"}}"},"clean_arial":{"titlefont":"{\"Text\":{\"type\":\"0\",\"size\":\"20||px\",\"bold\":\"0\",\"afont\":\"Arial||1\"}}","level1font":"{\"Text\":{\"type\":\"0\",\"size\":\"16||px\",\"bold\":\"0\",\"afont\":\"Arial||1\",\"lineheight\":\"normal\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"0\",\"size\":\"13||px\",\"bold\":\"0\",\"afont\":\"Arial||1\",\"lineheight\":\"normal\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"0\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"textdecor\":\"900\"}}","level1ofont":"{\"Text\":{\"type\":\"0\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"0\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"0\",\"afont\":\"Arial||1\"}}","roundfont":"{\"Text\":{\"type\":\"0\",\"afont\":\"Arial||1\"}}"},"clean_open_sans":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Open Sans\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Open Sans\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Open Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Open sans\",\"subset\":\"latin\",\"textdecor\":\"800\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Open sans\",\"subset\":\"latin\",\"textdecor\":\"800\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Open sans\",\"subset\":\"latin\",\"textdecor\":\"800\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Open sans\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Open sans\",\"subset\":\"latin\"}}"},"clean_roboto":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Roboto\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Roboto\",\"subset\":\"latin\"}}"},"clean_lato":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Lato\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Lato\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Lato\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Lato\",\"subset\":\"latin\",\"textdecor\":\"900\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Lato\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Lato\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Lato\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Lato\",\"subset\":\"latin\"}}"},"clean_roboto_condensed":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"700\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\"}}"},"clean_oswald":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Oswald\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Oswald\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Oswald\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Oswald\",\"subset\":\"latin\",\"textdecor\":\"700\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Oswald\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Oswald\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Oswald\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Oswald\",\"subset\":\"latin\"}}"},"clean_lora":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Lora\",\"subset\":\"latin\",\"textdecor\":\"400\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Lora\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Lora\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Lora\",\"subset\":\"latin\",\"textdecor\":\"700\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Lora\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Lora\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Lora\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Lora\",\"subset\":\"latin\"}}"},"clean_source_sans_pro":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\",\"textdecor\":\"900\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Source Sans Pro\",\"subset\":\"latin\"}}"},"clean_pt_sans":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"PT Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"PT Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"PT Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"PT Sans\",\"subset\":\"latin\",\"textdecor\":\"700\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"PT Sans\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"PT Sans\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"PT Sans\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"PT Sans\",\"subset\":\"latin\"}}"},"clean_open_sans_condensed":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Open Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\",\"textdecor\":\"700\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Open Sans Condensed\",\"subset\":\"latin\"}}"},"clean_droid_sans":{"titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"family\":\"Droid Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"}}","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Droid Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"lineheight\":\"normal\",\"family\":\"Droid Sans\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Droid Sans\",\"subset\":\"latin\",\"textdecor\":\"700\"}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Droid Sans\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Droid Sans\",\"subset\":\"latin\",\"textdecor\":\"700\"},\"Active\":{},\"Hover\":{}}","squarefont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Droid Sans\",\"subset\":\"latin\"}}","roundfont":"{\"Text\":{\"type\":\"latin\",\"afont\":\"sans-serif||1\",\"family\":\"Droid Sans\",\"subset\":\"latin\"}}"}},
        root: 'http://printervoronezh.ru/',
        control: "jform[params][moduleparametersTab][theme]",
        dependency: ''
      });
    
jQuery("#jformparamsmoduleparametersTabthemesitebg").minicolors({opacity: false, position: "bottom left"});

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabthemehideburger",
        mode: "",
        imgs: ""
      }); 
    
jQuery("#jformparamsmoduleparametersTabthemesidebar_icon0").minicolors({opacity: false, position: "bottom left"});
jQuery("#jformparamsmoduleparametersTabthemesidebar_icon1").minicolors({opacity: true, position: "bottom left"});

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemesidebar_icon2",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemesidebar_icon3",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemesidebar_icon4",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemesidebar_icon5",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnList({
        name: "jformparamsmoduleparametersTabthemesidebar_icon6",
        options: [{"value":"0.08em","text":"Normal"},{"value":"0.1em","text":"Bold"},{"value":"3px","text":"Thin"},{"value":"2px","text":"Ultra thin"}],
        selectedIndex: 0,
        json: "",
        width: 0,
        height: 0,
        fireshow: 0
      });
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabthemesidebar_icon7",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemesidebar_icon",
        num: 8,
        switcherid: "",
        hideafter: "6",
        islist: "0"
      }); 
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabthemeburgertitle0",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeburgertitle1",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeburgertitle2",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeburgertitle3",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnRadioimg({
        id: "jformparamsmoduleparametersTabthemeburgertitle4",
        values: ["vertical","horizontal","rotated","small"],
        map: {"vertical":0,"horizontal":1,"rotated":2,"small":3},
        mode: ""
      });
    

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemeburgertitle",
        num: 5,
        switcherid: "",
        hideafter: "4",
        islist: "0"
      }); 
    

        new OfflajnImagemanager({
          id: "jformparamsmoduleparametersTabthemebgimg",
          folder: "/modules/mod_vertical_menu/themes/clean/images/backgrounds/",
          root: "",
          uploadurl: "index.php?option=offlajnupload",
          imgs: ["blue_blur.jpg","brown_blur.jpg","eclipse.jpg","food.jpg","food2.jpg","gaussian_blur.jpg","green.jpg","green_blur.jpg","green_triangles.jpg","sky_blur.jpg","sundown.jpg","tailor.jpg","tailor2.jpg"],
          identifier: "2546be1040dddf0938883dab4b6c9e78",
          description: "",
          siteurl: "http://printervoronezh.ru/"
        });
    
jQuery("#jformparamsmoduleparametersTabthemebg0").minicolors({opacity: true, position: "bottom left"});

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabthemebg1",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemebg2",
        validation: "",
        attachunit: "%",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemebg",
        num: 3,
        switcherid: "jformparamsmoduleparametersTabthemebg1",
        hideafter: "1",
        islist: "0"
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthememargin0",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthememargin1",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthememargin2",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthememargin3",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
dojo.addOnLoad(function(){ 
      new OfflajnSwitcher({
        id: "jformparamsmoduleparametersTabthememargin4",
        units: ["px","em"],
        values: ["px","em"],
        map: {"px":0,"em":1},
        mode: 0,
        url: "http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnswitcher\/images\/"
      }); 
    });

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthememargin",
        num: 5,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeborderradius0",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeborderradius1",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeborderradius2",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemeborderradius3",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
dojo.addOnLoad(function(){ 
      new OfflajnSwitcher({
        id: "jformparamsmoduleparametersTabthemeborderradius4",
        units: ["%","px"],
        values: ["%","px"],
        map: {"%":0,"px":1},
        mode: 0,
        url: "http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnswitcher\/images\/"
      }); 
    });

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemeborderradius",
        num: 5,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    

        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemetitlefont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"20||px","color":"#ffffff","bold":"0","italic":"0","underline":"0","align":"left","afont":"Helvetica, Arial||1","tshadow":"0||px|*|0||px|*|1||px|*|00000033|*|1","lineheight":"50px","family":"Roboto Condensed","subset":"latin","textdecor":"300"}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][titlefont]tab","id":"jformparamsmoduleparametersTabthemetitlefonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemetitlefonttab\"><div class=\"radioelement first last selected\">Text<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemetitlefonttab\" name=\"jform[params][moduleparametersTab][theme][titlefont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][titlefont]type","id":"jformparamsmoduleparametersTabthemetitlefonttype","latin":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][titlefont]family","id":"jformparamsmoduleparametersTabthemetitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]family\" id=\"jformparamsmoduleparametersTabthemetitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]type\" id=\"jformparamsmoduleparametersTabthemetitlefonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][titlefont]size","id":"jformparamsmoduleparametersTabthemetitlefontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemetitlefontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemetitlefontsizeinput\" value=\"20\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemetitlefontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]size[unit]\" id=\"jformparamsmoduleparametersTabthemetitlefontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]size\" id=\"jformparamsmoduleparametersTabthemetitlefontsize\" value=\"20||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][titlefont]color","id":"jformparamsmoduleparametersTabthemetitlefontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][titlefont]color\" id=\"jformparamsmoduleparametersTabthemetitlefontcolor\" value=\"#ffffff\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][titlefont]textdecor","id":"jformparamsmoduleparametersTabthemetitlefonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemetitlefonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">light<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]textdecor\" id=\"jformparamsmoduleparametersTabthemetitlefonttextdecor\" value=\"300\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][titlefont]italic","id":"jformparamsmoduleparametersTabthemetitlefontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemetitlefontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]italic\" id=\"jformparamsmoduleparametersTabthemetitlefontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][titlefont]underline","id":"jformparamsmoduleparametersTabthemetitlefontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemetitlefontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]underline\" id=\"jformparamsmoduleparametersTabthemetitlefontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][titlefont]linethrough","id":"jformparamsmoduleparametersTabthemetitlefontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemetitlefontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]linethrough\" id=\"jformparamsmoduleparametersTabthemetitlefontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][titlefont]uppercase","id":"jformparamsmoduleparametersTabthemetitlefontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemetitlefontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]uppercase\" id=\"jformparamsmoduleparametersTabthemetitlefontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][titlefont]align","id":"jformparamsmoduleparametersTabthemetitlefontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemetitlefontalign\"><div class=\"radioelement first selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement \"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemetitlefontalign\" name=\"jform[params][moduleparametersTab][theme][titlefont]align\" value=\"left\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][titlefont]afont","id":"jformparamsmoduleparametersTabthemetitlefontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemetitlefontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemetitlefontafontinput\" value=\"Helvetica, Arial\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemetitlefontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemetitlefontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]afont\" id=\"jformparamsmoduleparametersTabthemetitlefontafont\" value=\"Helvetica, Arial||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][titlefont]tshadow","id":"jformparamsmoduleparametersTabthemetitlefonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemetitlefonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemetitlefonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]tshadow0\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemetitlefonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow1input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]tshadow1\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow1\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemetitlefonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow2input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]tshadow2\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow2\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][titlefont]tshadow3\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow3\" value=\"rgba(0, 0, 0, 0.20)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemetitlefonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]tshadow4\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow4\" value=\"1\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]tshadow\" id=\"jformparamsmoduleparametersTabthemetitlefonttshadow\" value='0||px|*|0||px|*|1||px|*|00000033|*|1'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][titlefont]lineheight","id":"jformparamsmoduleparametersTabthemetitlefontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemetitlefontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemetitlefontlineheightinput\" value=\"50px\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][titlefont]lineheight\" id=\"jformparamsmoduleparametersTabthemetitlefontlineheight\" value=\"50px\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemetitlefonttab\",\r\n        values: [\"Text\"],\r\n        map: {\"Text\":0},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemetitlefontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemetitlefontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemetitlefontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemetitlefonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 2,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemetitlefontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemetitlefontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemetitlefontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemetitlefontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemetitlefontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemetitlefontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemetitlefontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemetitlefonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemetitlefonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemetitlefonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemetitlefonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemetitlefonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemetitlefonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemetitlefonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemetitlefontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    

        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemeotitlefont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"70||px","color":"#ffffff","italic":"0","underline":"0","align":"center","afont":"Helvetica, Arial||1","tshadow":"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|","lineheight":"90px","family":"Roboto","subset":"latin","textdecor":"900"}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][otitlefont]tab","id":"jformparamsmoduleparametersTabthemeotitlefonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemeotitlefonttab\"><div class=\"radioelement first last selected\">Text<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemeotitlefonttab\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][otitlefont]type","id":"jformparamsmoduleparametersTabthemeotitlefonttype","latin":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][otitlefont]family","id":"jformparamsmoduleparametersTabthemeotitlefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]family\" id=\"jformparamsmoduleparametersTabthemeotitlefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]type\" id=\"jformparamsmoduleparametersTabthemeotitlefonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][otitlefont]size","id":"jformparamsmoduleparametersTabthemeotitlefontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeotitlefontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeotitlefontsizeinput\" value=\"70\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemeotitlefontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]size[unit]\" id=\"jformparamsmoduleparametersTabthemeotitlefontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]size\" id=\"jformparamsmoduleparametersTabthemeotitlefontsize\" value=\"70||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][otitlefont]color","id":"jformparamsmoduleparametersTabthemeotitlefontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][otitlefont]color\" id=\"jformparamsmoduleparametersTabthemeotitlefontcolor\" value=\"#ffffff\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][otitlefont]textdecor","id":"jformparamsmoduleparametersTabthemeotitlefonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeotitlefonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">ultra-bold<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]textdecor\" id=\"jformparamsmoduleparametersTabthemeotitlefonttextdecor\" value=\"900\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][otitlefont]italic","id":"jformparamsmoduleparametersTabthemeotitlefontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeotitlefontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]italic\" id=\"jformparamsmoduleparametersTabthemeotitlefontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][otitlefont]underline","id":"jformparamsmoduleparametersTabthemeotitlefontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeotitlefontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]underline\" id=\"jformparamsmoduleparametersTabthemeotitlefontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][otitlefont]linethrough","id":"jformparamsmoduleparametersTabthemeotitlefontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeotitlefontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]linethrough\" id=\"jformparamsmoduleparametersTabthemeotitlefontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][otitlefont]uppercase","id":"jformparamsmoduleparametersTabthemeotitlefontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeotitlefontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]uppercase\" id=\"jformparamsmoduleparametersTabthemeotitlefontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][otitlefont]align","id":"jformparamsmoduleparametersTabthemeotitlefontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemeotitlefontalign\"><div class=\"radioelement first\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement  selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemeotitlefontalign\" name=\"jform[params][moduleparametersTab][theme][otitlefont]align\" value=\"center\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][otitlefont]afont","id":"jformparamsmoduleparametersTabthemeotitlefontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeotitlefontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeotitlefontafontinput\" value=\"Helvetica, Arial\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemeotitlefontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemeotitlefontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]afont\" id=\"jformparamsmoduleparametersTabthemeotitlefontafont\" value=\"Helvetica, Arial||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][otitlefont]tshadow","id":"jformparamsmoduleparametersTabthemeotitlefonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemeotitlefonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeotitlefonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tshadow0\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeotitlefonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow1input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tshadow1\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow1\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeotitlefonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow2input\" value=\"2\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tshadow2\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow2\" value=\"2||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tshadow3\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow3\" value=\"rgba(0, 0, 0, 0.1)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemeotitlefonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tshadow4\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow4\" value=\"1\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]tshadow\" id=\"jformparamsmoduleparametersTabthemeotitlefonttshadow\" value='0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][otitlefont]lineheight","id":"jformparamsmoduleparametersTabthemeotitlefontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeotitlefontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeotitlefontlineheightinput\" value=\"90px\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][otitlefont]lineheight\" id=\"jformparamsmoduleparametersTabthemeotitlefontlineheight\" value=\"90px\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemeotitlefonttab\",\r\n        values: [\"Text\"],\r\n        map: {\"Text\":0},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemeotitlefontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemeotitlefontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeotitlefonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 8,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemeotitlefontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemeotitlefontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeotitlefonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeotitlefonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeotitlefonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemeotitlefonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemeotitlefonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemeotitlefonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemeotitlefonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeotitlefontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    
jQuery("#jformparamsmoduleparametersTabthemetitlebg").minicolors({opacity: true, position: "bottom left"});
jQuery("#jformparamsmoduleparametersTabthemetitleborder0").minicolors({opacity: true, position: "bottom left"});
jQuery("#jformparamsmoduleparametersTabthemetitleborder1").minicolors({opacity: true, position: "bottom left"});

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemetitleborder",
        num: 2,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    
jQuery("#jformparamsmoduleparametersTabthemefiltercolor0").minicolors({opacity: true, position: "bottom left"});
jQuery("#jformparamsmoduleparametersTabthemefiltercolor1").minicolors({opacity: true, position: "bottom left"});

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemefiltercolor",
        num: 2,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    

        new OfflajnImagemanager({
          id: "jformparamsmoduleparametersTabthemereseticon",
          folder: "/modules/mod_vertical_menu/themes/clean/images/reset/",
          root: "",
          uploadurl: "index.php?option=offlajnupload",
          imgs: ["reset-0.png","reset-1.png","reset-10.png","reset-2.png","reset-3.png","reset-4.png","reset-5.png","reset-6.png","reset-7.png","reset-8.png","reset-9.png"],
          identifier: "83a14d361a5e88def21b371616b90130",
          description: "",
          siteurl: "http://printervoronezh.ru/"
        });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthememenuitemmargin",
        validation: "",
        attachunit: "px",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabthemeopened",
        mode: "",
        imgs: ""
      }); 
    

      new OfflajnOnOff({
        id: "jformparamsmoduleparametersTabthemebadge",
        mode: "",
        imgs: ""
      }); 
    
jQuery("#jformparamsmoduleparametersTabthemesquarebadge0").minicolors({opacity: true, position: "bottom left"});

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemesquarebadge",
        num: 1,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    

        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemesquarefont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"10||px","color":"#ffffff","bold":"0","italic":"0","underline":"0","align":"left","afont":"sans-serif||1","tshadow":"0||px|*|0||px|*|1||px|*|rgba(0, 0, 0, 0.20)|*|0","lineheight":"18px","textdecor":"700","family":"Montserrat","subset":"latin"}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][squarefont]tab","id":"jformparamsmoduleparametersTabthemesquarefonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemesquarefonttab\"><div class=\"radioelement first last selected\">Text<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemesquarefonttab\" name=\"jform[params][moduleparametersTab][theme][squarefont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][squarefont]type","id":"jformparamsmoduleparametersTabthemesquarefonttype","latin":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][squarefont]family","id":"jformparamsmoduleparametersTabthemesquarefontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]family\" id=\"jformparamsmoduleparametersTabthemesquarefontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]type\" id=\"jformparamsmoduleparametersTabthemesquarefonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][squarefont]size","id":"jformparamsmoduleparametersTabthemesquarefontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemesquarefontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemesquarefontsizeinput\" value=\"10\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemesquarefontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]size[unit]\" id=\"jformparamsmoduleparametersTabthemesquarefontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]size\" id=\"jformparamsmoduleparametersTabthemesquarefontsize\" value=\"10||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][squarefont]color","id":"jformparamsmoduleparametersTabthemesquarefontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][squarefont]color\" id=\"jformparamsmoduleparametersTabthemesquarefontcolor\" value=\"#ffffff\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][squarefont]textdecor","id":"jformparamsmoduleparametersTabthemesquarefonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemesquarefonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">bold<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]textdecor\" id=\"jformparamsmoduleparametersTabthemesquarefonttextdecor\" value=\"700\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][squarefont]italic","id":"jformparamsmoduleparametersTabthemesquarefontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemesquarefontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]italic\" id=\"jformparamsmoduleparametersTabthemesquarefontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][squarefont]underline","id":"jformparamsmoduleparametersTabthemesquarefontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemesquarefontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]underline\" id=\"jformparamsmoduleparametersTabthemesquarefontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][squarefont]linethrough","id":"jformparamsmoduleparametersTabthemesquarefontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemesquarefontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]linethrough\" id=\"jformparamsmoduleparametersTabthemesquarefontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][squarefont]uppercase","id":"jformparamsmoduleparametersTabthemesquarefontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemesquarefontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]uppercase\" id=\"jformparamsmoduleparametersTabthemesquarefontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][squarefont]align","id":"jformparamsmoduleparametersTabthemesquarefontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemesquarefontalign\"><div class=\"radioelement first selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement \"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemesquarefontalign\" name=\"jform[params][moduleparametersTab][theme][squarefont]align\" value=\"left\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][squarefont]afont","id":"jformparamsmoduleparametersTabthemesquarefontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemesquarefontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemesquarefontafontinput\" value=\"sans-serif\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemesquarefontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemesquarefontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]afont\" id=\"jformparamsmoduleparametersTabthemesquarefontafont\" value=\"sans-serif||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][squarefont]tshadow","id":"jformparamsmoduleparametersTabthemesquarefonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemesquarefonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemesquarefonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]tshadow0\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemesquarefonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow1input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]tshadow1\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow1\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemesquarefonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow2input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]tshadow2\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow2\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][squarefont]tshadow3\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow3\" value=\"rgba(0, 0, 0, 0.20)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemesquarefonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]tshadow4\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow4\" value=\"0\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]tshadow\" id=\"jformparamsmoduleparametersTabthemesquarefonttshadow\" value='0||px|*|0||px|*|1||px|*|rgba(0, 0, 0, 0.20)|*|0'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][squarefont]lineheight","id":"jformparamsmoduleparametersTabthemesquarefontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemesquarefontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemesquarefontlineheightinput\" value=\"18px\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][squarefont]lineheight\" id=\"jformparamsmoduleparametersTabthemesquarefontlineheight\" value=\"18px\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemesquarefonttab\",\r\n        values: [\"Text\"],\r\n        map: {\"Text\":0},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemesquarefontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemesquarefontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemesquarefontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemesquarefonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 6,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemesquarefontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemesquarefontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemesquarefontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemesquarefontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemesquarefontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemesquarefontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemesquarefontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemesquarefonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemesquarefonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemesquarefonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemesquarefonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemesquarefonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemesquarefonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemesquarefonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemesquarefontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    
jQuery("#jformparamsmoduleparametersTabthemeroundbadge0").minicolors({opacity: true, position: "bottom left"});

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemeroundbadge",
        num: 1,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    

        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemeroundfont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"10||px","color":"#ffffff","bold":"0","italic":"0","underline":"0","align":"left","afont":"sans-serif||1","tshadow":"0||px|*|0||px|*|1||px|*|rgba(0, 0, 0, 0.20)|*|0","lineheight":"18px","textdecor":"700","family":"Montserrat","subset":"latin"}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][roundfont]tab","id":"jformparamsmoduleparametersTabthemeroundfonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemeroundfonttab\"><div class=\"radioelement first last selected\">Text<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemeroundfonttab\" name=\"jform[params][moduleparametersTab][theme][roundfont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][roundfont]type","id":"jformparamsmoduleparametersTabthemeroundfonttype","latin":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][roundfont]family","id":"jformparamsmoduleparametersTabthemeroundfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]family\" id=\"jformparamsmoduleparametersTabthemeroundfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]type\" id=\"jformparamsmoduleparametersTabthemeroundfonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][roundfont]size","id":"jformparamsmoduleparametersTabthemeroundfontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeroundfontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeroundfontsizeinput\" value=\"10\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemeroundfontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]size[unit]\" id=\"jformparamsmoduleparametersTabthemeroundfontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]size\" id=\"jformparamsmoduleparametersTabthemeroundfontsize\" value=\"10||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][roundfont]color","id":"jformparamsmoduleparametersTabthemeroundfontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][roundfont]color\" id=\"jformparamsmoduleparametersTabthemeroundfontcolor\" value=\"#ffffff\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][roundfont]textdecor","id":"jformparamsmoduleparametersTabthemeroundfonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemeroundfonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">bold<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]textdecor\" id=\"jformparamsmoduleparametersTabthemeroundfonttextdecor\" value=\"700\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][roundfont]italic","id":"jformparamsmoduleparametersTabthemeroundfontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeroundfontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]italic\" id=\"jformparamsmoduleparametersTabthemeroundfontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][roundfont]underline","id":"jformparamsmoduleparametersTabthemeroundfontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeroundfontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]underline\" id=\"jformparamsmoduleparametersTabthemeroundfontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][roundfont]linethrough","id":"jformparamsmoduleparametersTabthemeroundfontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeroundfontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]linethrough\" id=\"jformparamsmoduleparametersTabthemeroundfontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][roundfont]uppercase","id":"jformparamsmoduleparametersTabthemeroundfontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemeroundfontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]uppercase\" id=\"jformparamsmoduleparametersTabthemeroundfontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][roundfont]align","id":"jformparamsmoduleparametersTabthemeroundfontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemeroundfontalign\"><div class=\"radioelement first selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement \"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemeroundfontalign\" name=\"jform[params][moduleparametersTab][theme][roundfont]align\" value=\"left\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][roundfont]afont","id":"jformparamsmoduleparametersTabthemeroundfontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeroundfontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeroundfontafontinput\" value=\"sans-serif\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemeroundfontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemeroundfontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]afont\" id=\"jformparamsmoduleparametersTabthemeroundfontafont\" value=\"sans-serif||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][roundfont]tshadow","id":"jformparamsmoduleparametersTabthemeroundfonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemeroundfonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeroundfonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]tshadow0\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeroundfonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow1input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]tshadow1\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow1\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeroundfonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow2input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]tshadow2\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow2\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][roundfont]tshadow3\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow3\" value=\"rgba(0, 0, 0, 0.20)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemeroundfonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]tshadow4\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow4\" value=\"0\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]tshadow\" id=\"jformparamsmoduleparametersTabthemeroundfonttshadow\" value='0||px|*|0||px|*|1||px|*|rgba(0, 0, 0, 0.20)|*|0'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][roundfont]lineheight","id":"jformparamsmoduleparametersTabthemeroundfontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemeroundfontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemeroundfontlineheightinput\" value=\"18px\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][roundfont]lineheight\" id=\"jformparamsmoduleparametersTabthemeroundfontlineheight\" value=\"18px\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemeroundfonttab\",\r\n        values: [\"Text\"],\r\n        map: {\"Text\":0},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemeroundfontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeroundfontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemeroundfontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemeroundfonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 6,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeroundfontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeroundfontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeroundfontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemeroundfontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemeroundfontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemeroundfontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeroundfontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeroundfonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeroundfonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeroundfonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemeroundfonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemeroundfonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemeroundfonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemeroundfonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemeroundfontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemebadgeradius0",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemebadgeradius1",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemebadgeradius2",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    

      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemebadgeradius3",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
dojo.addOnLoad(function(){ 
      new OfflajnSwitcher({
        id: "jformparamsmoduleparametersTabthemebadgeradius4",
        units: ["%","px"],
        values: ["%","px"],
        map: {"%":0,"px":1},
        mode: 0,
        url: "http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnswitcher\/images\/"
      }); 
    });

      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemebadgeradius",
        num: 5,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    

      var themelevel = new ThemeLevel({
        control: "jform[params][moduleparametersTab][theme]",
        id: "jformparamsmoduleparametersTabtheme",
        el: dojo.byId("jform[params][moduleparametersTab][theme]acclevel"),
        render: "<div class=\"legend panel\">\r\n  <h3 class=\"title pane-toggler\"><span>Level [x]<\/span><\/h3>\r\n  <div class=\"pane-slider content pane-down\" style=\"padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0;\">\t\t\r\n    <fieldset class=\"panelform\">\r\n      <ul class=\"adminformlist parsed\"><li class=\"hide\" title=\"\" ><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]]\" id=\"jform[params][moduleparametersTab][theme]level[x]\" value=\"[x]\" class=\"text_area\" \/><\/li><li class=\"blue\" title=\"\" ><label id=\"level[x]bg-lbl\" for=\"level[x]bg\">Menu-item background<\/label><div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]bg\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Hover<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]bg]0\" id=\"jformparamsmoduleparametersTabthemelevel[x]bg0\" value=\"rgba(218, 230, 233, 0.2)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Active<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]bg]1\" id=\"jformparamsmoduleparametersTabthemelevel[x]bg1\" value=\"rgba(0, 0, 0, 0.13)\" class=\"color\" \/><\/div><\/div><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]bg]\" id=\"jformparamsmoduleparametersTabthemelevel[x]bg\" value='rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.13)'><\/li><li class=\"\" title=\"\" ><label id=\"level[x]border-lbl\" for=\"level[x]border\">Menu-item border<\/label><div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]border\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Top<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]border]0\" id=\"jformparamsmoduleparametersTabthemelevel[x]border0\" value=\"rgba(255, 255, 255, 0)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Bottom<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]border]1\" id=\"jformparamsmoduleparametersTabthemelevel[x]border1\" value=\"rgba(0, 0, 0, 0)\" class=\"color\" \/><\/div><\/div><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]border]\" id=\"jformparamsmoduleparametersTabthemelevel[x]border\" value='rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)'><\/li><li class=\"blue\" title=\"\" ><label id=\"level[x]padding-lbl\" for=\"level[x]padding\">Menu-item Padding<\/label><div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]padding\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Top<\/label><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]padding0\"><input  size=\"2\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding0input\" value=\"7\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]padding]0\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding0\" value=\"7\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Right<\/label><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]padding1\"><input  size=\"2\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding1input\" value=\"12\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]padding]1\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding1\" value=\"12\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Bottom<\/label><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]padding2\"><input  size=\"2\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding2input\" value=\"7\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]padding]2\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding2\" value=\"7\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Left<\/label><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]padding3\"><input  size=\"2\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding3input\" value=\"12\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]padding]3\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding3\" value=\"12\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">&nbsp;<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]padding4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]padding]4\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding4\" value=\"px\" \/><\/div><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]padding]\" id=\"jformparamsmoduleparametersTabthemelevel[x]padding\" value='7|*|12|*|7|*|12|*|px'><\/li><li data-if=\"!overlay\" class=\"\" title=\"\" ><label id=\"level[x]font-lbl\" for=\"level[x]font\">Menu-item font<\/label><a style='float: left;' id='jformparamsmoduleparametersTabthemelevel[x]fontchange' href='#' class='font_select'><\/a>&nbsp;&nbsp;<input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]font]\" id=\"jformparamsmoduleparametersTabthemelevel[x]font\" value='{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|#000000|*|0\",\"lineheight\":\"normal\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}' \/><\/li><li data-if=\"overlay\" class=\"blue\" title=\"\" ><label id=\"level[x]ofont-lbl\" for=\"level[x]ofont\">Menu-item font<\/label><a style='float: left;' id='jformparamsmoduleparametersTabthemelevel[x]ofontchange' href='#' class='font_select'><\/a>&nbsp;&nbsp;<input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]ofont]\" id=\"jformparamsmoduleparametersTabthemelevel[x]ofont\" value='{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}' \/><\/li><li data-if=\"desc\" class=\"\" title=\"\" ><label id=\"level[x]descfont-lbl\" for=\"level[x]descfont\">Description font<\/label><a style='float: left;' id='jformparamsmoduleparametersTabthemelevel[x]descfontchange' href='#' class='font_select'><\/a>&nbsp;&nbsp;<input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]descfont]\" id=\"jformparamsmoduleparametersTabthemelevel[x]descfont\" value='{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"color\":\"#90b2b2\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|#000000|*|0\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}' \/><\/li><li data-if=\"overlaydesc\" class=\"blue\" title=\"\" ><label id=\"level[x]odescfont-lbl\" for=\"level[x]odescfont\">Description font<\/label><a style='float: left;' id='jformparamsmoduleparametersTabthemelevel[x]odescfontchange' href='#' class='font_select'><\/a>&nbsp;&nbsp;<input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]odescfont]\" id=\"jformparamsmoduleparametersTabthemelevel[x]odescfont\" value='{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}' \/><\/li><li data-if=\"displaynumprod\" class=\"\" title=\"\" ><label id=\"level[x]countbg-lbl\" for=\"level[x]countbg\">Count of sub-items background<\/label><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]countbg]\" id=\"jformparamsmoduleparametersTabthemelevel[x]countbg\" value=\"rgba(0, 0, 0, 0.22)\" class=\"color\" \/><\/div><\/li><li class=\"blue\" title=\"\" ><label id=\"level[x]plus-lbl\" for=\"level[x]plus\">Arrow<\/label><div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]plus\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Icon<\/label><div class=\"offlajncombinefield\"><div id=\"offlajnimagemanagerjformparamsmoduleparametersTabthemelevel[x]plus0\" class=\"offlajnimagemanager\"><div class=\"offlajnimagemanagerimg\">\r\n                <div><\/div>\r\n              <\/div><div class=\"offlajnimagemanagerbtn\"><\/div><input type=\"hidden\" data-folder=\"\\modules\\mod_vertical_menu\\themes\\clean\\images\\arrows\\\" name=\"jform[params][moduleparametersTab][theme][level[x]plus]0\" id=\"jformparamsmoduleparametersTabthemelevel[x]plus0\" value=\"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png\"\/><\/div><\/div><\/div><div class=\"clear\"><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Position<\/label><div class=\"offlajncombinefield\"><div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]plus1\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Right<br \/>Left<br \/>Right<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]plus]1\" id=\"jformparamsmoduleparametersTabthemelevel[x]plus1\" value=\"right\"\/><\/div><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Color<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]plus]2\" id=\"jformparamsmoduleparametersTabthemelevel[x]plus2\" value=\"#4e7676\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><label style=\"float: left;\">Hover<\/label><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level[x]plus]3\" id=\"jformparamsmoduleparametersTabthemelevel[x]plus3\" value=\"#4e7676\" class=\"color\" \/><\/div><\/div><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level[x]plus]\" id=\"jformparamsmoduleparametersTabthemelevel[x]plus\" value='\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#4e7676|*|#4e7676'><\/li><\/ul>    <\/fieldset>\t\t\t\r\n    <div class=\"clr\"><\/div>\t\r\n  <\/div>\r\n<\/div>",
        scripts: "dojo.addOnLoad(function(){jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]bg0\").minicolors({opacity: true, position: \"bottom left\"});jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]bg1\").minicolors({opacity: true, position: \"bottom left\"});\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel[x]bg\",\r\n        num: 2,\r\n        switcherid: \"\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]border0\").minicolors({opacity: true, position: \"bottom left\"});jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]border1\").minicolors({opacity: true, position: \"bottom left\"});\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel[x]border\",\r\n        num: 2,\r\n        switcherid: \"\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel[x]padding0\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel[x]padding1\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel[x]padding2\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel[x]padding3\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel[x]padding4\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel[x]padding\",\r\n        num: 5,\r\n        switcherid: \"\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \r\n        new MiniFontConfigurator({\r\n          id: \"jformparamsmoduleparametersTabthemelevel[x]font\",\r\n          defaultTab: \"Text\",\r\n          origsettings: {\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|#000000|*|0\",\"lineheight\":\"normal\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}},\r\n          elements: {\"tab\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]tab\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fonttab\",\"html\":\"<div class=\\\"offlajnradiocontainerbutton\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]fonttab\\\"><div class=\\\"radioelement first selected\\\">Text<\\\/div><div class=\\\"radioelement \\\">Active<\\\/div><div class=\\\"radioelement  last\\\">Hover<\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttab\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tab\\\" value=\\\"Text\\\"\\\/>\"},\"type\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]type\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fonttype\",\"latin\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"latin_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"hebrew\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_hebrew\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"vietnamese\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_vietnamese\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"arabic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_arabic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"devanagari\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_devanagari\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"khmer\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_khmer\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"tamil\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_tamil\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"thai\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_thai\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"telugu\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_telugu\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"bengali\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_bengali\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"gujarati\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_gujarati\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fonttype\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">Latin<br \\\/>Alternative fonts<br \\\/>Latin<br \\\/>latin_ext<br \\\/>Greek<br \\\/>greek_ext<br \\\/>hebrew<br \\\/>Vietnamese<br \\\/>arabic<br \\\/>devanagari<br \\\/>Cyrillic<br \\\/>cyrillic_ext<br \\\/>Khmer<br \\\/>tamil<br \\\/>thai<br \\\/>telugu<br \\\/>bengali<br \\\/>gujarati<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]type\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttype\\\" value=\\\"latin\\\"\\\/><\\\/div><\\\/div>\"},\"size\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]size\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontsize\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]fontsize\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontsizeinput\\\" value=\\\"16\\\"><div class=\\\"offlajntext_increment\\\">\\n                <div class=\\\"offlajntext_increment_up arrow\\\"><\\\/div>\\n                <div class=\\\"offlajntext_increment_down arrow\\\"><\\\/div>\\n      <\\\/div><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]fontsizeunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]size[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontsizeunit\\\" value=\\\"px\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]size\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontsize\\\" value=\\\"16||px\\\">\"},\"color\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]color\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontcolor\",\"html\":\"<div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]color\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontcolor\\\" value=\\\"#ffffff\\\" class=\\\"color\\\" \\\/><\\\/div>\"},\"textdecor\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]textdecor\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fonttextdecor\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]fonttextdecor\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">light<br \\\/>thin<br \\\/>extra-light<br \\\/>light<br \\\/>normal<br \\\/>medium<br \\\/>semi-bold<br \\\/>bold<br \\\/>extra-bold<br \\\/>ultra-bold<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]textdecor\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttextdecor\\\" value=\\\"300\\\"\\\/><\\\/div><\\\/div>\"},\"italic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]italic\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontitalic\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]fontitalic\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/italic.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]italic\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontitalic\\\" value=\\\"0\\\" \\\/>\"},\"underline\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]underline\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontunderline\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]fontunderline\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/underline.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]underline\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontunderline\\\" value=\\\"0\\\" \\\/>\"},\"linethrough\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]linethrough\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontlinethrough\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]fontlinethrough\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/linethrough.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]linethrough\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontlinethrough\\\" value=\\\"0\\\" \\\/>\"},\"uppercase\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]uppercase\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontuppercase\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]fontuppercase\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/uppercase.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]uppercase\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontuppercase\\\" value=\\\"0\\\" \\\/>\"},\"align\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]align\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontalign\",\"html\":\"<div class=\\\"offlajnradiocontainerimage\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]fontalign\\\"><div class=\\\"radioelement first selected\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/left_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement \\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/center_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement  last\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/right_align.png);\\\"><\\\/div><\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontalign\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]align\\\" value=\\\"left\\\"\\\/>\"},\"afont\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]afont\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontafont\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]fontafont\\\"><input  size=\\\"10\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontafontinput\\\" value=\\\"Arial, Helvetica\\\"><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]fontafontunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]afont[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontafontunit\\\" value=\\\"1\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]afont\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontafont\\\" value=\\\"Arial, Helvetica||1\\\">\"},\"tshadow\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]tshadow\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow\",\"html\":\"<div id=\\\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]fonttshadow\\\" class=\\\"offlajncombine_outer\\\"><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]fonttshadow0\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow0input\\\" value=\\\"0\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tshadow0\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow0\\\" value=\\\"0||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]fonttshadow1\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow1input\\\" value=\\\"1\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tshadow1\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow1\\\" value=\\\"1||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]fonttshadow2\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow2input\\\" value=\\\"2\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tshadow2\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow2\\\" value=\\\"2||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tshadow3\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow3\\\" value=\\\"#000000\\\" class=\\\"color\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]fonttshadow4\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tshadow4\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow4\\\" value=\\\"0\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombine_hider\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]tshadow\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow\\\" value='0||px|*|1||px|*|2||px|*|#000000|*|0'>\"},\"lineheight\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]font]lineheight\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]fontlineheight\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]fontlineheight\\\"><input  size=\\\"5\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontlineheightinput\\\" value=\\\"normal\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]font]lineheight\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]fontlineheight\\\" value=\\\"normal\\\">\"}},\r\n          script: \"dojo.addOnLoad(function(){\\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttab\\\",\\r\\n        values: [\\\"Text\\\",\\\"Active\\\",\\\"Hover\\\"],\\r\\n        map: {\\\"Text\\\":0,\\\"Active\\\":1,\\\"Hover\\\":2},\\r\\n        mode: \\\"\\\"\\r\\n      });\\r\\n    \\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttype\\\",\\r\\n        options: [{\\\"value\\\":\\\"0\\\",\\\"text\\\":\\\"Alternative fonts\\\"},{\\\"value\\\":\\\"latin\\\",\\\"text\\\":\\\"Latin\\\"},{\\\"value\\\":\\\"latin_ext\\\",\\\"text\\\":\\\"latin_ext\\\"},{\\\"value\\\":\\\"greek\\\",\\\"text\\\":\\\"Greek\\\"},{\\\"value\\\":\\\"greek_ext\\\",\\\"text\\\":\\\"greek_ext\\\"},{\\\"value\\\":\\\"hebrew\\\",\\\"text\\\":\\\"hebrew\\\"},{\\\"value\\\":\\\"vietnamese\\\",\\\"text\\\":\\\"Vietnamese\\\"},{\\\"value\\\":\\\"arabic\\\",\\\"text\\\":\\\"arabic\\\"},{\\\"value\\\":\\\"devanagari\\\",\\\"text\\\":\\\"devanagari\\\"},{\\\"value\\\":\\\"cyrillic\\\",\\\"text\\\":\\\"Cyrillic\\\"},{\\\"value\\\":\\\"cyrillic_ext\\\",\\\"text\\\":\\\"cyrillic_ext\\\"},{\\\"value\\\":\\\"khmer\\\",\\\"text\\\":\\\"Khmer\\\"},{\\\"value\\\":\\\"tamil\\\",\\\"text\\\":\\\"tamil\\\"},{\\\"value\\\":\\\"thai\\\",\\\"text\\\":\\\"thai\\\"},{\\\"value\\\":\\\"telugu\\\",\\\"text\\\":\\\"telugu\\\"},{\\\"value\\\":\\\"bengali\\\",\\\"text\\\":\\\"bengali\\\"},{\\\"value\\\":\\\"gujarati\\\",\\\"text\\\":\\\"gujarati\\\"}],\\r\\n        selectedIndex: 1,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 0\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontsizeunit\\\",\\r\\n        units: [\\\"px\\\",\\\"em\\\"],\\r\\n        values: [\\\"px\\\",\\\"em\\\"],\\r\\n        map: {\\\"px\\\":0,\\\"em\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontsize\\\",\\n        validation: \\\"int\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"increment\\\",\\n        scale: \\\"1\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]fontcolor\\\").minicolors({opacity: false, position: \\\"bottom left\\\"});\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttextdecor\\\",\\r\\n        options: [{\\\"value\\\":\\\"100\\\",\\\"text\\\":\\\"thin\\\"},{\\\"value\\\":\\\"200\\\",\\\"text\\\":\\\"extra-light\\\"},{\\\"value\\\":\\\"300\\\",\\\"text\\\":\\\"light\\\"},{\\\"value\\\":\\\"400\\\",\\\"text\\\":\\\"normal\\\"},{\\\"value\\\":\\\"500\\\",\\\"text\\\":\\\"medium\\\"},{\\\"value\\\":\\\"600\\\",\\\"text\\\":\\\"semi-bold\\\"},{\\\"value\\\":\\\"700\\\",\\\"text\\\":\\\"bold\\\"},{\\\"value\\\":\\\"800\\\",\\\"text\\\":\\\"extra-bold\\\"},{\\\"value\\\":\\\"900\\\",\\\"text\\\":\\\"ultra-bold\\\"}],\\r\\n        selectedIndex: 2,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: 0,\\r\\n        fireshow: 0\\r\\n      });\\r\\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontitalic\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontunderline\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontlinethrough\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontuppercase\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontalign\\\",\\r\\n        values: [\\\"left\\\",\\\"center\\\",\\\"right\\\"],\\r\\n        map: {\\\"left\\\":0,\\\"center\\\":1,\\\"right\\\":2},\\r\\n        mode: \\\"image\\\"\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontafontunit\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontafont\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"1\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow0\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow1\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow2\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]fonttshadow3\\\").minicolors({opacity: true, position: \\\"bottom left\\\"});dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow4\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\r\\n      new OfflajnCombine({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow\\\",\\r\\n        num: 5,\\r\\n        switcherid: \\\"jformparamsmoduleparametersTabthemelevel[x]fonttshadow4\\\",\\r\\n        hideafter: \\\"0\\\",\\r\\n        islist: \\\"0\\\"\\r\\n      }); \\r\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]fontlineheight\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    });\"\r\n        });\r\n    \r\n        new MiniFontConfigurator({\r\n          id: \"jformparamsmoduleparametersTabthemelevel[x]ofont\",\r\n          defaultTab: \"Text\",\r\n          origsettings: {\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}},\r\n          elements: {\"tab\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]tab\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofonttab\",\"html\":\"<div class=\\\"offlajnradiocontainerbutton\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]ofonttab\\\"><div class=\\\"radioelement first selected\\\">Text<\\\/div><div class=\\\"radioelement \\\">Active<\\\/div><div class=\\\"radioelement  last\\\">Hover<\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttab\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tab\\\" value=\\\"Text\\\"\\\/>\"},\"type\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]type\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofonttype\",\"latin\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"latin_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"hebrew\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_hebrew\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"vietnamese\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_vietnamese\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"arabic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_arabic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"devanagari\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_devanagari\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"khmer\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_khmer\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"tamil\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_tamil\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"thai\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_thai\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"telugu\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_telugu\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"bengali\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_bengali\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"gujarati\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_gujarati\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofonttype\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">Latin<br \\\/>Alternative fonts<br \\\/>Latin<br \\\/>latin_ext<br \\\/>Greek<br \\\/>greek_ext<br \\\/>hebrew<br \\\/>Vietnamese<br \\\/>arabic<br \\\/>devanagari<br \\\/>Cyrillic<br \\\/>cyrillic_ext<br \\\/>Khmer<br \\\/>tamil<br \\\/>thai<br \\\/>telugu<br \\\/>bengali<br \\\/>gujarati<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]type\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttype\\\" value=\\\"latin\\\"\\\/><\\\/div><\\\/div>\"},\"size\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]size\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontsize\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]ofontsize\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontsizeinput\\\" value=\\\"50\\\"><div class=\\\"offlajntext_increment\\\">\\n                <div class=\\\"offlajntext_increment_up arrow\\\"><\\\/div>\\n                <div class=\\\"offlajntext_increment_down arrow\\\"><\\\/div>\\n      <\\\/div><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]ofontsizeunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]size[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontsizeunit\\\" value=\\\"px\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]size\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontsize\\\" value=\\\"50||px\\\">\"},\"color\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]color\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontcolor\",\"html\":\"<div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]color\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontcolor\\\" value=\\\"#ffffff\\\" class=\\\"color\\\" \\\/><\\\/div>\"},\"textdecor\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]textdecor\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofonttextdecor\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]ofonttextdecor\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">ultra-bold<br \\\/>thin<br \\\/>extra-light<br \\\/>light<br \\\/>normal<br \\\/>medium<br \\\/>semi-bold<br \\\/>bold<br \\\/>extra-bold<br \\\/>ultra-bold<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]textdecor\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttextdecor\\\" value=\\\"900\\\"\\\/><\\\/div><\\\/div>\"},\"italic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]italic\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontitalic\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]ofontitalic\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/italic.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]italic\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontitalic\\\" value=\\\"0\\\" \\\/>\"},\"underline\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]underline\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontunderline\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]ofontunderline\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/underline.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]underline\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontunderline\\\" value=\\\"0\\\" \\\/>\"},\"linethrough\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]linethrough\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontlinethrough\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]ofontlinethrough\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/linethrough.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]linethrough\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontlinethrough\\\" value=\\\"0\\\" \\\/>\"},\"uppercase\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]uppercase\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontuppercase\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]ofontuppercase\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/uppercase.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]uppercase\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontuppercase\\\" value=\\\"0\\\" \\\/>\"},\"align\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]align\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontalign\",\"html\":\"<div class=\\\"offlajnradiocontainerimage\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]ofontalign\\\"><div class=\\\"radioelement first\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/left_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement  selected\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/center_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement  last\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/right_align.png);\\\"><\\\/div><\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontalign\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]align\\\" value=\\\"center\\\"\\\/>\"},\"afont\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]afont\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontafont\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]ofontafont\\\"><input  size=\\\"10\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontafontinput\\\" value=\\\"Arial, Helvetica\\\"><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]ofontafontunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]afont[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontafontunit\\\" value=\\\"1\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]afont\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontafont\\\" value=\\\"Arial, Helvetica||1\\\">\"},\"tshadow\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow\",\"html\":\"<div id=\\\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]ofonttshadow\\\" class=\\\"offlajncombine_outer\\\"><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]ofonttshadow0\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow0input\\\" value=\\\"0\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow0\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow0\\\" value=\\\"0||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]ofonttshadow1\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow1input\\\" value=\\\"1\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow1\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow1\\\" value=\\\"1||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]ofonttshadow2\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow2input\\\" value=\\\"2\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow2\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow2\\\" value=\\\"2||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow3\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow3\\\" value=\\\"rgba(0, 0, 0, 0.09)\\\" class=\\\"color\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]ofonttshadow4\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow4\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow4\\\" value=\\\"1\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombine_hider\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]tshadow\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow\\\" value='0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|'>\"},\"lineheight\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]ofont]lineheight\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]ofontlineheight\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]ofontlineheight\\\"><input  size=\\\"5\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontlineheightinput\\\" value=\\\"normal\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]ofont]lineheight\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]ofontlineheight\\\" value=\\\"normal\\\">\"}},\r\n          script: \"dojo.addOnLoad(function(){\\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttab\\\",\\r\\n        values: [\\\"Text\\\",\\\"Active\\\",\\\"Hover\\\"],\\r\\n        map: {\\\"Text\\\":0,\\\"Active\\\":1,\\\"Hover\\\":2},\\r\\n        mode: \\\"\\\"\\r\\n      });\\r\\n    \\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttype\\\",\\r\\n        options: [{\\\"value\\\":\\\"0\\\",\\\"text\\\":\\\"Alternative fonts\\\"},{\\\"value\\\":\\\"latin\\\",\\\"text\\\":\\\"Latin\\\"},{\\\"value\\\":\\\"latin_ext\\\",\\\"text\\\":\\\"latin_ext\\\"},{\\\"value\\\":\\\"greek\\\",\\\"text\\\":\\\"Greek\\\"},{\\\"value\\\":\\\"greek_ext\\\",\\\"text\\\":\\\"greek_ext\\\"},{\\\"value\\\":\\\"hebrew\\\",\\\"text\\\":\\\"hebrew\\\"},{\\\"value\\\":\\\"vietnamese\\\",\\\"text\\\":\\\"Vietnamese\\\"},{\\\"value\\\":\\\"arabic\\\",\\\"text\\\":\\\"arabic\\\"},{\\\"value\\\":\\\"devanagari\\\",\\\"text\\\":\\\"devanagari\\\"},{\\\"value\\\":\\\"cyrillic\\\",\\\"text\\\":\\\"Cyrillic\\\"},{\\\"value\\\":\\\"cyrillic_ext\\\",\\\"text\\\":\\\"cyrillic_ext\\\"},{\\\"value\\\":\\\"khmer\\\",\\\"text\\\":\\\"Khmer\\\"},{\\\"value\\\":\\\"tamil\\\",\\\"text\\\":\\\"tamil\\\"},{\\\"value\\\":\\\"thai\\\",\\\"text\\\":\\\"thai\\\"},{\\\"value\\\":\\\"telugu\\\",\\\"text\\\":\\\"telugu\\\"},{\\\"value\\\":\\\"bengali\\\",\\\"text\\\":\\\"bengali\\\"},{\\\"value\\\":\\\"gujarati\\\",\\\"text\\\":\\\"gujarati\\\"}],\\r\\n        selectedIndex: 1,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 0\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontsizeunit\\\",\\r\\n        units: [\\\"px\\\",\\\"em\\\"],\\r\\n        values: [\\\"px\\\",\\\"em\\\"],\\r\\n        map: {\\\"px\\\":0,\\\"em\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontsize\\\",\\n        validation: \\\"int\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"increment\\\",\\n        scale: \\\"1\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]ofontcolor\\\").minicolors({opacity: false, position: \\\"bottom left\\\"});\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttextdecor\\\",\\r\\n        options: [{\\\"value\\\":\\\"100\\\",\\\"text\\\":\\\"thin\\\"},{\\\"value\\\":\\\"200\\\",\\\"text\\\":\\\"extra-light\\\"},{\\\"value\\\":\\\"300\\\",\\\"text\\\":\\\"light\\\"},{\\\"value\\\":\\\"400\\\",\\\"text\\\":\\\"normal\\\"},{\\\"value\\\":\\\"500\\\",\\\"text\\\":\\\"medium\\\"},{\\\"value\\\":\\\"600\\\",\\\"text\\\":\\\"semi-bold\\\"},{\\\"value\\\":\\\"700\\\",\\\"text\\\":\\\"bold\\\"},{\\\"value\\\":\\\"800\\\",\\\"text\\\":\\\"extra-bold\\\"},{\\\"value\\\":\\\"900\\\",\\\"text\\\":\\\"ultra-bold\\\"}],\\r\\n        selectedIndex: 8,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: 0,\\r\\n        fireshow: 0\\r\\n      });\\r\\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontitalic\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontunderline\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontlinethrough\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontuppercase\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontalign\\\",\\r\\n        values: [\\\"left\\\",\\\"center\\\",\\\"right\\\"],\\r\\n        map: {\\\"left\\\":0,\\\"center\\\":1,\\\"right\\\":2},\\r\\n        mode: \\\"image\\\"\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontafontunit\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontafont\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"1\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow0\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow1\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow2\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]ofonttshadow3\\\").minicolors({opacity: true, position: \\\"bottom left\\\"});dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow4\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\r\\n      new OfflajnCombine({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow\\\",\\r\\n        num: 5,\\r\\n        switcherid: \\\"jformparamsmoduleparametersTabthemelevel[x]ofonttshadow4\\\",\\r\\n        hideafter: \\\"0\\\",\\r\\n        islist: \\\"0\\\"\\r\\n      }); \\r\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]ofontlineheight\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    });\"\r\n        });\r\n    \r\n        new MiniFontConfigurator({\r\n          id: \"jformparamsmoduleparametersTabthemelevel[x]descfont\",\r\n          defaultTab: \"Text\",\r\n          origsettings: {\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"color\":\"#90b2b2\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|#000000|*|0\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}},\r\n          elements: {\"tab\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]tab\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfonttab\",\"html\":\"<div class=\\\"offlajnradiocontainerbutton\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]descfonttab\\\"><div class=\\\"radioelement first selected\\\">Text<\\\/div><div class=\\\"radioelement \\\">Active<\\\/div><div class=\\\"radioelement  last\\\">Hover<\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttab\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tab\\\" value=\\\"Text\\\"\\\/>\"},\"type\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]type\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfonttype\",\"latin\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"latin_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"hebrew\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_hebrew\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"vietnamese\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_vietnamese\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"arabic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_arabic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"devanagari\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_devanagari\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"khmer\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_khmer\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"tamil\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_tamil\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"thai\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_thai\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"telugu\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_telugu\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"bengali\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_bengali\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"gujarati\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_gujarati\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfonttype\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">Latin<br \\\/>Alternative fonts<br \\\/>Latin<br \\\/>latin_ext<br \\\/>Greek<br \\\/>greek_ext<br \\\/>hebrew<br \\\/>Vietnamese<br \\\/>arabic<br \\\/>devanagari<br \\\/>Cyrillic<br \\\/>cyrillic_ext<br \\\/>Khmer<br \\\/>tamil<br \\\/>thai<br \\\/>telugu<br \\\/>bengali<br \\\/>gujarati<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]type\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttype\\\" value=\\\"latin\\\"\\\/><\\\/div><\\\/div>\"},\"size\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]size\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontsize\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]descfontsize\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontsizeinput\\\" value=\\\"13\\\"><div class=\\\"offlajntext_increment\\\">\\n                <div class=\\\"offlajntext_increment_up arrow\\\"><\\\/div>\\n                <div class=\\\"offlajntext_increment_down arrow\\\"><\\\/div>\\n      <\\\/div><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]descfontsizeunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]size[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontsizeunit\\\" value=\\\"px\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]size\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontsize\\\" value=\\\"13||px\\\">\"},\"color\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]color\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontcolor\",\"html\":\"<div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]color\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontcolor\\\" value=\\\"#90b2b2\\\" class=\\\"color\\\" \\\/><\\\/div>\"},\"textdecor\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]textdecor\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfonttextdecor\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]descfonttextdecor\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">normal<br \\\/>thin<br \\\/>extra-light<br \\\/>light<br \\\/>normal<br \\\/>medium<br \\\/>semi-bold<br \\\/>bold<br \\\/>extra-bold<br \\\/>ultra-bold<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]textdecor\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttextdecor\\\" value=\\\"400\\\"\\\/><\\\/div><\\\/div>\"},\"italic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]italic\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontitalic\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]descfontitalic\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/italic.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]italic\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontitalic\\\" value=\\\"0\\\" \\\/>\"},\"underline\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]underline\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontunderline\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]descfontunderline\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/underline.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]underline\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontunderline\\\" value=\\\"0\\\" \\\/>\"},\"linethrough\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]linethrough\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontlinethrough\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]descfontlinethrough\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/linethrough.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]linethrough\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontlinethrough\\\" value=\\\"0\\\" \\\/>\"},\"uppercase\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]uppercase\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontuppercase\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]descfontuppercase\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/uppercase.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]uppercase\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontuppercase\\\" value=\\\"0\\\" \\\/>\"},\"align\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]align\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontalign\",\"html\":\"<div class=\\\"offlajnradiocontainerimage\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]descfontalign\\\"><div class=\\\"radioelement first selected\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/left_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement \\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/center_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement  last\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/right_align.png);\\\"><\\\/div><\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontalign\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]align\\\" value=\\\"left\\\"\\\/>\"},\"afont\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]afont\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontafont\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]descfontafont\\\"><input  size=\\\"10\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontafontinput\\\" value=\\\"Arial, Helvetica\\\"><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]descfontafontunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]afont[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontafontunit\\\" value=\\\"1\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]afont\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontafont\\\" value=\\\"Arial, Helvetica||1\\\">\"},\"tshadow\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow\",\"html\":\"<div id=\\\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]descfonttshadow\\\" class=\\\"offlajncombine_outer\\\"><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]descfonttshadow0\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow0input\\\" value=\\\"0\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow0\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow0\\\" value=\\\"0||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]descfonttshadow1\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow1input\\\" value=\\\"1\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow1\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow1\\\" value=\\\"1||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]descfonttshadow2\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow2input\\\" value=\\\"2\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow2\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow2\\\" value=\\\"2||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow3\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow3\\\" value=\\\"#000000\\\" class=\\\"color\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]descfonttshadow4\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow4\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow4\\\" value=\\\"0\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombine_hider\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]tshadow\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow\\\" value='0||px|*|1||px|*|2||px|*|#000000|*|0'>\"},\"lineheight\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]descfont]lineheight\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]descfontlineheight\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]descfontlineheight\\\"><input  size=\\\"5\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontlineheightinput\\\" value=\\\"normal\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]descfont]lineheight\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]descfontlineheight\\\" value=\\\"normal\\\">\"}},\r\n          script: \"dojo.addOnLoad(function(){\\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttab\\\",\\r\\n        values: [\\\"Text\\\",\\\"Active\\\",\\\"Hover\\\"],\\r\\n        map: {\\\"Text\\\":0,\\\"Active\\\":1,\\\"Hover\\\":2},\\r\\n        mode: \\\"\\\"\\r\\n      });\\r\\n    \\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttype\\\",\\r\\n        options: [{\\\"value\\\":\\\"0\\\",\\\"text\\\":\\\"Alternative fonts\\\"},{\\\"value\\\":\\\"latin\\\",\\\"text\\\":\\\"Latin\\\"},{\\\"value\\\":\\\"latin_ext\\\",\\\"text\\\":\\\"latin_ext\\\"},{\\\"value\\\":\\\"greek\\\",\\\"text\\\":\\\"Greek\\\"},{\\\"value\\\":\\\"greek_ext\\\",\\\"text\\\":\\\"greek_ext\\\"},{\\\"value\\\":\\\"hebrew\\\",\\\"text\\\":\\\"hebrew\\\"},{\\\"value\\\":\\\"vietnamese\\\",\\\"text\\\":\\\"Vietnamese\\\"},{\\\"value\\\":\\\"arabic\\\",\\\"text\\\":\\\"arabic\\\"},{\\\"value\\\":\\\"devanagari\\\",\\\"text\\\":\\\"devanagari\\\"},{\\\"value\\\":\\\"cyrillic\\\",\\\"text\\\":\\\"Cyrillic\\\"},{\\\"value\\\":\\\"cyrillic_ext\\\",\\\"text\\\":\\\"cyrillic_ext\\\"},{\\\"value\\\":\\\"khmer\\\",\\\"text\\\":\\\"Khmer\\\"},{\\\"value\\\":\\\"tamil\\\",\\\"text\\\":\\\"tamil\\\"},{\\\"value\\\":\\\"thai\\\",\\\"text\\\":\\\"thai\\\"},{\\\"value\\\":\\\"telugu\\\",\\\"text\\\":\\\"telugu\\\"},{\\\"value\\\":\\\"bengali\\\",\\\"text\\\":\\\"bengali\\\"},{\\\"value\\\":\\\"gujarati\\\",\\\"text\\\":\\\"gujarati\\\"}],\\r\\n        selectedIndex: 1,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 0\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontsizeunit\\\",\\r\\n        units: [\\\"px\\\",\\\"em\\\"],\\r\\n        values: [\\\"px\\\",\\\"em\\\"],\\r\\n        map: {\\\"px\\\":0,\\\"em\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontsize\\\",\\n        validation: \\\"int\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"increment\\\",\\n        scale: \\\"1\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]descfontcolor\\\").minicolors({opacity: false, position: \\\"bottom left\\\"});\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttextdecor\\\",\\r\\n        options: [{\\\"value\\\":\\\"100\\\",\\\"text\\\":\\\"thin\\\"},{\\\"value\\\":\\\"200\\\",\\\"text\\\":\\\"extra-light\\\"},{\\\"value\\\":\\\"300\\\",\\\"text\\\":\\\"light\\\"},{\\\"value\\\":\\\"400\\\",\\\"text\\\":\\\"normal\\\"},{\\\"value\\\":\\\"500\\\",\\\"text\\\":\\\"medium\\\"},{\\\"value\\\":\\\"600\\\",\\\"text\\\":\\\"semi-bold\\\"},{\\\"value\\\":\\\"700\\\",\\\"text\\\":\\\"bold\\\"},{\\\"value\\\":\\\"800\\\",\\\"text\\\":\\\"extra-bold\\\"},{\\\"value\\\":\\\"900\\\",\\\"text\\\":\\\"ultra-bold\\\"}],\\r\\n        selectedIndex: 3,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: 0,\\r\\n        fireshow: 0\\r\\n      });\\r\\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontitalic\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontunderline\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontlinethrough\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontuppercase\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontalign\\\",\\r\\n        values: [\\\"left\\\",\\\"center\\\",\\\"right\\\"],\\r\\n        map: {\\\"left\\\":0,\\\"center\\\":1,\\\"right\\\":2},\\r\\n        mode: \\\"image\\\"\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontafontunit\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontafont\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"1\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow0\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow1\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow2\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]descfonttshadow3\\\").minicolors({opacity: true, position: \\\"bottom left\\\"});dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow4\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\r\\n      new OfflajnCombine({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow\\\",\\r\\n        num: 5,\\r\\n        switcherid: \\\"jformparamsmoduleparametersTabthemelevel[x]descfonttshadow4\\\",\\r\\n        hideafter: \\\"0\\\",\\r\\n        islist: \\\"0\\\"\\r\\n      }); \\r\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]descfontlineheight\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    });\"\r\n        });\r\n    \r\n        new MiniFontConfigurator({\r\n          id: \"jformparamsmoduleparametersTabthemelevel[x]odescfont\",\r\n          defaultTab: \"Text\",\r\n          origsettings: {\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}},\r\n          elements: {\"tab\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]tab\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfonttab\",\"html\":\"<div class=\\\"offlajnradiocontainerbutton\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]odescfonttab\\\"><div class=\\\"radioelement first selected\\\">Text<\\\/div><div class=\\\"radioelement \\\">Active<\\\/div><div class=\\\"radioelement  last\\\">Hover<\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttab\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tab\\\" value=\\\"Text\\\"\\\/>\"},\"type\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]type\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfonttype\",\"latin\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"latin_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_latin_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"greek_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_greek_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"hebrew\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_hebrew\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"vietnamese\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_vietnamese\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"arabic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_arabic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"devanagari\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_devanagari\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"cyrillic_ext\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_cyrillic_ext\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"khmer\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_khmer\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"tamil\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_tamil\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"thai\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_thai\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"telugu\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_telugu\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"bengali\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_bengali\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"gujarati\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\"><br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]family\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\" value=\\\"\\\"\\\/><\\\/div><\\\/div>\",\"script\":\"dojo.addOnLoad(function(){\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontfamily\\\",\\r\\n        options: [],\\r\\n        selectedIndex: 0,\\r\\n        json: \\\"OfflajnFont_gujarati\\\",\\r\\n        width: 164,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 1\\r\\n      });\\r\\n    });\"},\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfonttype\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">Latin<br \\\/>Alternative fonts<br \\\/>Latin<br \\\/>latin_ext<br \\\/>Greek<br \\\/>greek_ext<br \\\/>hebrew<br \\\/>Vietnamese<br \\\/>arabic<br \\\/>devanagari<br \\\/>Cyrillic<br \\\/>cyrillic_ext<br \\\/>Khmer<br \\\/>tamil<br \\\/>thai<br \\\/>telugu<br \\\/>bengali<br \\\/>gujarati<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]type\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttype\\\" value=\\\"latin\\\"\\\/><\\\/div><\\\/div>\"},\"size\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]size\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontsize\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontsize\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontsizeinput\\\" value=\\\"17\\\"><div class=\\\"offlajntext_increment\\\">\\n                <div class=\\\"offlajntext_increment_up arrow\\\"><\\\/div>\\n                <div class=\\\"offlajntext_increment_down arrow\\\"><\\\/div>\\n      <\\\/div><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]odescfontsizeunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]size[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontsizeunit\\\" value=\\\"px\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]size\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontsize\\\" value=\\\"17||px\\\">\"},\"color\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]color\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontcolor\",\"html\":\"<div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]color\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontcolor\\\" value=\\\"#rgba(255,255,255,0.8)\\\" class=\\\"color\\\" \\\/><\\\/div>\"},\"textdecor\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]textdecor\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfonttextdecor\",\"html\":\"<div style='position:relative;'><div id=\\\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel[x]odescfonttextdecor\\\" class=\\\"gk_hack offlajnlistcontainer\\\"><div class=\\\"gk_hack offlajnlist\\\"><span class=\\\"offlajnlistcurrent\\\">ultra-bold<br \\\/>thin<br \\\/>extra-light<br \\\/>light<br \\\/>normal<br \\\/>medium<br \\\/>semi-bold<br \\\/>bold<br \\\/>extra-bold<br \\\/>ultra-bold<br \\\/><\\\/span><div class=\\\"offlajnlistbtn\\\"><span><\\\/span><\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]textdecor\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttextdecor\\\" value=\\\"900\\\"\\\/><\\\/div><\\\/div>\"},\"italic\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]italic\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontitalic\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]odescfontitalic\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/italic.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]italic\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontitalic\\\" value=\\\"0\\\" \\\/>\"},\"underline\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]underline\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontunderline\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]odescfontunderline\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/underline.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]underline\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontunderline\\\" value=\\\"0\\\" \\\/>\"},\"linethrough\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]linethrough\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontlinethrough\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]odescfontlinethrough\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/linethrough.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]linethrough\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontlinethrough\\\" value=\\\"0\\\" \\\/>\"},\"uppercase\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]uppercase\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontuppercase\",\"html\":\"<div id=\\\"offlajnonoffjformparamsmoduleparametersTabthemelevel[x]odescfontuppercase\\\" class=\\\"gk_hack onoffbutton\\\">\\n                <div class=\\\"gk_hack onoffbutton_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnonoff\\\/images\\\/uppercase.png);\\\"><\\\/div>\\n      <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]uppercase\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontuppercase\\\" value=\\\"0\\\" \\\/>\"},\"align\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]align\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontalign\",\"html\":\"<div class=\\\"offlajnradiocontainerimage\\\" id=\\\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel[x]odescfontalign\\\"><div class=\\\"radioelement first\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/left_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement  selected\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/center_align.png);\\\"><\\\/div><\\\/div><div class=\\\"radioelement  last\\\"><div class=\\\"radioelement_img\\\" style=\\\"background-image: url(http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnradio\\\/images\\\/right_align.png);\\\"><\\\/div><\\\/div><div class=\\\"clear\\\"><\\\/div><\\\/div><input type=\\\"hidden\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontalign\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]align\\\" value=\\\"center\\\"\\\/>\"},\"afont\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]afont\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontafont\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontafont\\\"><input  size=\\\"10\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontafontinput\\\" value=\\\"Arial, Helvetica\\\"><\\\/div><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]odescfontafontunit\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]afont[unit]\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontafontunit\\\" value=\\\"1\\\" \\\/><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]afont\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontafont\\\" value=\\\"Arial, Helvetica||1\\\">\"},\"tshadow\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow\",\"html\":\"<div id=\\\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel[x]odescfonttshadow\\\" class=\\\"offlajncombine_outer\\\"><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]odescfonttshadow0\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow0input\\\" value=\\\"0\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow0\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow0\\\" value=\\\"0||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]odescfonttshadow1\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow1input\\\" value=\\\"1\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow1\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow1\\\" value=\\\"1||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]odescfonttshadow2\\\"><input  size=\\\"1\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow2input\\\" value=\\\"2\\\"><div class=\\\"unit\\\">px<\\\/div><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow2\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow2\\\" value=\\\"2||px\\\"><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnminicolor\\\"><input type=\\\"text\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow3\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow3\\\" value=\\\"rgba(0, 0, 0, 0.09)\\\" class=\\\"color\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombinefieldcontainer\\\"><div class=\\\"offlajncombinefield\\\"><div class=\\\"offlajnswitcher\\\">\\r\\n            <div class=\\\"offlajnswitcher_inner\\\" id=\\\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel[x]odescfonttshadow4\\\"><\\\/div>\\r\\n    <\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow4\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow4\\\" value=\\\"1\\\" \\\/><\\\/div><\\\/div><\\\/div><div class=\\\"offlajncombine_hider\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]tshadow\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow\\\" value='0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|'>\"},\"lineheight\":{\"name\":\"jform[params][moduleparametersTab][theme][level[x]odescfont]lineheight\",\"id\":\"jformparamsmoduleparametersTabthemelevel[x]odescfontlineheight\",\"html\":\"<div class=\\\"offlajntextcontainer\\\" id=\\\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel[x]odescfontlineheight\\\"><input  size=\\\"5\\\" class=\\\"offlajntext\\\" type=\\\"text\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontlineheightinput\\\" value=\\\"normal\\\"><\\\/div><input type=\\\"hidden\\\" name=\\\"jform[params][moduleparametersTab][theme][level[x]odescfont]lineheight\\\" id=\\\"jformparamsmoduleparametersTabthemelevel[x]odescfontlineheight\\\" value=\\\"normal\\\">\"}},\r\n          script: \"dojo.addOnLoad(function(){\\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttab\\\",\\r\\n        values: [\\\"Text\\\",\\\"Active\\\",\\\"Hover\\\"],\\r\\n        map: {\\\"Text\\\":0,\\\"Active\\\":1,\\\"Hover\\\":2},\\r\\n        mode: \\\"\\\"\\r\\n      });\\r\\n    \\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttype\\\",\\r\\n        options: [{\\\"value\\\":\\\"0\\\",\\\"text\\\":\\\"Alternative fonts\\\"},{\\\"value\\\":\\\"latin\\\",\\\"text\\\":\\\"Latin\\\"},{\\\"value\\\":\\\"latin_ext\\\",\\\"text\\\":\\\"latin_ext\\\"},{\\\"value\\\":\\\"greek\\\",\\\"text\\\":\\\"Greek\\\"},{\\\"value\\\":\\\"greek_ext\\\",\\\"text\\\":\\\"greek_ext\\\"},{\\\"value\\\":\\\"hebrew\\\",\\\"text\\\":\\\"hebrew\\\"},{\\\"value\\\":\\\"vietnamese\\\",\\\"text\\\":\\\"Vietnamese\\\"},{\\\"value\\\":\\\"arabic\\\",\\\"text\\\":\\\"arabic\\\"},{\\\"value\\\":\\\"devanagari\\\",\\\"text\\\":\\\"devanagari\\\"},{\\\"value\\\":\\\"cyrillic\\\",\\\"text\\\":\\\"Cyrillic\\\"},{\\\"value\\\":\\\"cyrillic_ext\\\",\\\"text\\\":\\\"cyrillic_ext\\\"},{\\\"value\\\":\\\"khmer\\\",\\\"text\\\":\\\"Khmer\\\"},{\\\"value\\\":\\\"tamil\\\",\\\"text\\\":\\\"tamil\\\"},{\\\"value\\\":\\\"thai\\\",\\\"text\\\":\\\"thai\\\"},{\\\"value\\\":\\\"telugu\\\",\\\"text\\\":\\\"telugu\\\"},{\\\"value\\\":\\\"bengali\\\",\\\"text\\\":\\\"bengali\\\"},{\\\"value\\\":\\\"gujarati\\\",\\\"text\\\":\\\"gujarati\\\"}],\\r\\n        selectedIndex: 1,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: \\\"12\\\",\\r\\n        fireshow: 0\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontsizeunit\\\",\\r\\n        units: [\\\"px\\\",\\\"em\\\"],\\r\\n        values: [\\\"px\\\",\\\"em\\\"],\\r\\n        map: {\\\"px\\\":0,\\\"em\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontsize\\\",\\n        validation: \\\"int\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"increment\\\",\\n        scale: \\\"1\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]odescfontcolor\\\").minicolors({opacity: false, position: \\\"bottom left\\\"});\\r\\n      new OfflajnList({\\r\\n        name: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttextdecor\\\",\\r\\n        options: [{\\\"value\\\":\\\"100\\\",\\\"text\\\":\\\"thin\\\"},{\\\"value\\\":\\\"200\\\",\\\"text\\\":\\\"extra-light\\\"},{\\\"value\\\":\\\"300\\\",\\\"text\\\":\\\"light\\\"},{\\\"value\\\":\\\"400\\\",\\\"text\\\":\\\"normal\\\"},{\\\"value\\\":\\\"500\\\",\\\"text\\\":\\\"medium\\\"},{\\\"value\\\":\\\"600\\\",\\\"text\\\":\\\"semi-bold\\\"},{\\\"value\\\":\\\"700\\\",\\\"text\\\":\\\"bold\\\"},{\\\"value\\\":\\\"800\\\",\\\"text\\\":\\\"extra-bold\\\"},{\\\"value\\\":\\\"900\\\",\\\"text\\\":\\\"ultra-bold\\\"}],\\r\\n        selectedIndex: 8,\\r\\n        json: \\\"\\\",\\r\\n        width: 0,\\r\\n        height: 0,\\r\\n        fireshow: 0\\r\\n      });\\r\\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontitalic\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontunderline\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontlinethrough\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\n      new OfflajnOnOff({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontuppercase\\\",\\n        mode: \\\"button\\\",\\n        imgs: \\\"\\\"\\n      }); \\n    \\r\\n      new OfflajnRadio({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontalign\\\",\\r\\n        values: [\\\"left\\\",\\\"center\\\",\\\"right\\\"],\\r\\n        map: {\\\"left\\\":0,\\\"center\\\":1,\\\"right\\\":2},\\r\\n        mode: \\\"image\\\"\\r\\n      });\\r\\n    dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontafontunit\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontafont\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"1\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow0\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow1\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow2\\\",\\n        validation: \\\"float\\\",\\n        attachunit: \\\"px\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    jQuery(\\\"#jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow3\\\").minicolors({opacity: true, position: \\\"bottom left\\\"});dojo.addOnLoad(function(){ \\r\\n      new OfflajnSwitcher({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow4\\\",\\r\\n        units: [\\\"ON\\\",\\\"OFF\\\"],\\r\\n        values: [\\\"1\\\",\\\"0\\\"],\\r\\n        map: {\\\"1\\\":0,\\\"0\\\":1},\\r\\n        mode: 0,\\r\\n        url: \\\"http:\\\\\\\/\\\\\\\/printervoronezh.ru\\\\\\\/administrator\\\\\\\/..\\\\\\\/modules\\\\\\\/mod_vertical_menu\\\\\\\/params\\\\\\\/offlajnswitcher\\\\\\\/images\\\\\\\/\\\"\\r\\n      }); \\r\\n    });\\r\\n      new OfflajnCombine({\\r\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow\\\",\\r\\n        num: 5,\\r\\n        switcherid: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfonttshadow4\\\",\\r\\n        hideafter: \\\"0\\\",\\r\\n        islist: \\\"0\\\"\\r\\n      }); \\r\\n    \\n      new OfflajnText({\\n        id: \\\"jformparamsmoduleparametersTabthemelevel[x]odescfontlineheight\\\",\\n        validation: \\\"\\\",\\n        attachunit: \\\"\\\",\\n        mode: \\\"\\\",\\n        scale: \\\"\\\",\\n        minus: 0,\\n        onoff: \\\"\\\",\\n        placeholder: \\\"\\\"\\n      });\\n    });\"\r\n        });\r\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]countbg\").minicolors({opacity: true, position: \"bottom left\"});\r\n        new OfflajnImagemanager({\r\n          id: \"jformparamsmoduleparametersTabthemelevel[x]plus0\",\r\n          folder: \"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/\",\r\n          root: \"\",\r\n          uploadurl: \"index.php?option=offlajnupload\",\r\n          imgs: [\"arrow_left.png\",\"arrow_right.png\",\"big_left.png\",\"big_right.png\",\"big_tree.png\",\"bold_left.png\",\"bold_right.png\",\"circle_left.png\",\"circle_right.png\",\"default_left.png\",\"default_right.png\",\"default_tree.png\",\"round_left.png\",\"round_right.png\",\"thin_left.png\",\"thin_right.png\",\"triangle_left.png\",\"triangle_right.png\"],\r\n          identifier: \"5751c932107b3dc8442fb61ef596fac5\",\r\n          description: \"\",\r\n          siteurl: \"http:\/\/printervoronezh.ru\/\"\r\n        });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel[x]plus1\",\r\n        options: [{\"value\":\"left\",\"text\":\"Left\"},{\"value\":\"right\",\"text\":\"Right\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]plus2\").minicolors({opacity: true, position: \"bottom left\"});jQuery(\"#jformparamsmoduleparametersTabthemelevel[x]plus3\").minicolors({opacity: true, position: \"bottom left\"});\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel[x]plus\",\r\n        num: 4,\r\n        switcherid: \"\",\r\n        hideafter: \"1\",\r\n        islist: \"0\"\r\n      }); \r\n    });",
        values: {"theme":"clean","themeskin":"custom","fontskin":"custom","sitebg":"#444444","hideburger":"0","sidebar_icon":"#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px|*|0||px|*|0.08em|*|0|*|","burgertitle":"0|*|MENU|*|28||px|*|13||px|*|vertical","bgimg":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/backgrounds\/blue_blur.jpg","bg":"rgba(0, 0, 0, 0.11)|*|1|*|33||%","margin":"0|*|0|*|10|*|0|*|px","borderradius":"0|*|0|*|0|*|0|*|px","titlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"20||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|0||px|*|1||px|*|00000033|*|1\",\"lineheight\":\"50px\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"}}","otitlefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"70||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Helvetica, Arial||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.1)|*|1|*|\",\"lineheight\":\"90px\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"}}","titlebg":"rgba(0, 0, 0, 0.13)","titleborder":"rgba(0, 0, 0, 0.2)|*|rgba(255, 255, 255, 0.2)|*|","filtercolor":"rgba(0, 0, 0, 0.15)|*|rgba(255, 255, 255, 0.13)","reseticon":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/reset\/reset-0.png","menuitemmargin":"7||px","opened":"1","badge":"0","squarebadge":"#EF3D43","squarefont":"{\"Text\":{\"type\":\"latin\",\"size\":\"10||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"sans-serif||1\",\"tshadow\":\"0||px|*|0||px|*|1||px|*|rgba(0, 0, 0, 0.20)|*|0\",\"lineheight\":\"18px\",\"textdecor\":\"700\",\"family\":\"Montserrat\",\"subset\":\"latin\"}}","roundbadge":"#EF3D43","roundfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"10||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"sans-serif||1\",\"tshadow\":\"0||px|*|0||px|*|1||px|*|rgba(0, 0, 0, 0.20)|*|0\",\"lineheight\":\"18px\",\"textdecor\":\"700\",\"family\":\"Montserrat\",\"subset\":\"latin\"}}","badgeradius":"4|*|4|*|4|*|4|*|px","level1":"1","level1bg":"rgba(218, 230, 233, 0.2)|*|rgba(0, 0, 0, 0.07)|*|","level1border":"rgba(255, 255, 255, 0)|*|rgba(0, 0, 0, 0)","level1padding":"7|*|12|*|7|*|12|*|px","level1font":"{\"Text\":{\"type\":\"latin\",\"size\":\"16||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|#000000|*|0\",\"lineheight\":\"normal\",\"family\":\"Roboto Condensed\",\"subset\":\"latin\",\"textdecor\":\"300\"},\"Active\":{},\"Hover\":{}}","level1ofont":"{\"Text\":{\"type\":\"latin\",\"size\":\"50||px\",\"color\":\"#ffffff\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1descfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"13||px\",\"color\":\"#ffffff\",\"bold\":\"0\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"left\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|#000000|*|0\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"400\"},\"Active\":{},\"Hover\":{}}","level1odescfont":"{\"Text\":{\"type\":\"latin\",\"size\":\"17||px\",\"color\":\"rgba(255,255,255,0.8)\",\"italic\":\"0\",\"underline\":\"0\",\"align\":\"center\",\"afont\":\"Arial, Helvetica||1\",\"tshadow\":\"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|\",\"lineheight\":\"normal\",\"family\":\"Roboto\",\"subset\":\"latin\",\"textdecor\":\"900\"},\"Active\":{},\"Hover\":{}}","level1countbg":"rgba(0, 0, 0, 0.22)","level1plus":"\/modules\/mod_vertical_menu\/themes\/clean\/images\/arrows\/default_right.png|*|right|*|#ffffff|*|#ffffff|*|"},
        version: ""
      });
    
dojo.addOnLoad(function(){jQuery("#jformparamsmoduleparametersTabthemelevel1bg0").minicolors({opacity: true, position: "bottom left"});jQuery("#jformparamsmoduleparametersTabthemelevel1bg1").minicolors({opacity: true, position: "bottom left"});
      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemelevel1bg",
        num: 2,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    jQuery("#jformparamsmoduleparametersTabthemelevel1border0").minicolors({opacity: true, position: "bottom left"});jQuery("#jformparamsmoduleparametersTabthemelevel1border1").minicolors({opacity: true, position: "bottom left"});
      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemelevel1border",
        num: 2,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    
      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemelevel1padding0",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemelevel1padding1",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemelevel1padding2",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    
      new OfflajnText({
        id: "jformparamsmoduleparametersTabthemelevel1padding3",
        validation: "",
        attachunit: "",
        mode: "",
        scale: "",
        minus: 0,
        onoff: "",
        placeholder: ""
      });
    dojo.addOnLoad(function(){ 
      new OfflajnSwitcher({
        id: "jformparamsmoduleparametersTabthemelevel1padding4",
        units: ["px","em"],
        values: ["px","em"],
        map: {"px":0,"em":1},
        mode: 0,
        url: "http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnswitcher\/images\/"
      }); 
    });
      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemelevel1padding",
        num: 5,
        switcherid: "",
        hideafter: "0",
        islist: "0"
      }); 
    
        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemelevel1font",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"16||px","color":"#ffffff","bold":"0","italic":"0","underline":"0","align":"left","afont":"Arial, Helvetica||1","tshadow":"0||px|*|1||px|*|2||px|*|#000000|*|0","lineheight":"normal","family":"Roboto Condensed","subset":"latin","textdecor":"300"},"Active":{},"Hover":{}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][level1font]tab","id":"jformparamsmoduleparametersTabthemelevel1fonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1fonttab\"><div class=\"radioelement first selected\">Text<\/div><div class=\"radioelement \">Active<\/div><div class=\"radioelement  last\">Hover<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1fonttab\" name=\"jform[params][moduleparametersTab][theme][level1font]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][level1font]type","id":"jformparamsmoduleparametersTabthemelevel1fonttype","latin":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][level1font]family","id":"jformparamsmoduleparametersTabthemelevel1fontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]family\" id=\"jformparamsmoduleparametersTabthemelevel1fontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]type\" id=\"jformparamsmoduleparametersTabthemelevel1fonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][level1font]size","id":"jformparamsmoduleparametersTabthemelevel1fontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1fontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1fontsizeinput\" value=\"16\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1fontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]size[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1fontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]size\" id=\"jformparamsmoduleparametersTabthemelevel1fontsize\" value=\"16||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][level1font]color","id":"jformparamsmoduleparametersTabthemelevel1fontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1font]color\" id=\"jformparamsmoduleparametersTabthemelevel1fontcolor\" value=\"#ffffff\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][level1font]textdecor","id":"jformparamsmoduleparametersTabthemelevel1fonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1fonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">light<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]textdecor\" id=\"jformparamsmoduleparametersTabthemelevel1fonttextdecor\" value=\"300\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][level1font]italic","id":"jformparamsmoduleparametersTabthemelevel1fontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1fontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]italic\" id=\"jformparamsmoduleparametersTabthemelevel1fontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][level1font]underline","id":"jformparamsmoduleparametersTabthemelevel1fontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1fontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]underline\" id=\"jformparamsmoduleparametersTabthemelevel1fontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][level1font]linethrough","id":"jformparamsmoduleparametersTabthemelevel1fontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1fontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]linethrough\" id=\"jformparamsmoduleparametersTabthemelevel1fontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][level1font]uppercase","id":"jformparamsmoduleparametersTabthemelevel1fontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1fontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]uppercase\" id=\"jformparamsmoduleparametersTabthemelevel1fontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][level1font]align","id":"jformparamsmoduleparametersTabthemelevel1fontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1fontalign\"><div class=\"radioelement first selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement \"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1fontalign\" name=\"jform[params][moduleparametersTab][theme][level1font]align\" value=\"left\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][level1font]afont","id":"jformparamsmoduleparametersTabthemelevel1fontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1fontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1fontafontinput\" value=\"Arial, Helvetica\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1fontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]afont[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1fontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]afont\" id=\"jformparamsmoduleparametersTabthemelevel1fontafont\" value=\"Arial, Helvetica||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][level1font]tshadow","id":"jformparamsmoduleparametersTabthemelevel1fonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel1fonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1fonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]tshadow0\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1fonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow1input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]tshadow1\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow1\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1fonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow2input\" value=\"2\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]tshadow2\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow2\" value=\"2||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1font]tshadow3\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow3\" value=\"#000000\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1fonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]tshadow4\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow4\" value=\"0\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]tshadow\" id=\"jformparamsmoduleparametersTabthemelevel1fonttshadow\" value='0||px|*|1||px|*|2||px|*|#000000|*|0'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][level1font]lineheight","id":"jformparamsmoduleparametersTabthemelevel1fontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1fontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1fontlineheightinput\" value=\"normal\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1font]lineheight\" id=\"jformparamsmoduleparametersTabthemelevel1fontlineheight\" value=\"normal\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1fonttab\",\r\n        values: [\"Text\",\"Active\",\"Hover\"],\r\n        map: {\"Text\":0,\"Active\":1,\"Hover\":2},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1fontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1fontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1fonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 2,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1fontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1fontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1fonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1fonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1fonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1fonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1fonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1fonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemelevel1fonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1fontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    
        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemelevel1ofont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"50||px","color":"#ffffff","italic":"0","underline":"0","align":"center","afont":"Arial, Helvetica||1","tshadow":"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|","lineheight":"normal","family":"Roboto","subset":"latin","textdecor":"900"},"Active":{},"Hover":{}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][level1ofont]tab","id":"jformparamsmoduleparametersTabthemelevel1ofonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1ofonttab\"><div class=\"radioelement first selected\">Text<\/div><div class=\"radioelement \">Active<\/div><div class=\"radioelement  last\">Hover<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttab\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][level1ofont]type","id":"jformparamsmoduleparametersTabthemelevel1ofonttype","latin":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][level1ofont]family","id":"jformparamsmoduleparametersTabthemelevel1ofontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]family\" id=\"jformparamsmoduleparametersTabthemelevel1ofontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]type\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][level1ofont]size","id":"jformparamsmoduleparametersTabthemelevel1ofontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1ofontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1ofontsizeinput\" value=\"50\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1ofontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]size[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1ofontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]size\" id=\"jformparamsmoduleparametersTabthemelevel1ofontsize\" value=\"50||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][level1ofont]color","id":"jformparamsmoduleparametersTabthemelevel1ofontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1ofont]color\" id=\"jformparamsmoduleparametersTabthemelevel1ofontcolor\" value=\"#ffffff\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][level1ofont]textdecor","id":"jformparamsmoduleparametersTabthemelevel1ofonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1ofonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">ultra-bold<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]textdecor\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttextdecor\" value=\"900\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][level1ofont]italic","id":"jformparamsmoduleparametersTabthemelevel1ofontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1ofontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]italic\" id=\"jformparamsmoduleparametersTabthemelevel1ofontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][level1ofont]underline","id":"jformparamsmoduleparametersTabthemelevel1ofontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1ofontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]underline\" id=\"jformparamsmoduleparametersTabthemelevel1ofontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][level1ofont]linethrough","id":"jformparamsmoduleparametersTabthemelevel1ofontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1ofontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]linethrough\" id=\"jformparamsmoduleparametersTabthemelevel1ofontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][level1ofont]uppercase","id":"jformparamsmoduleparametersTabthemelevel1ofontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1ofontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]uppercase\" id=\"jformparamsmoduleparametersTabthemelevel1ofontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][level1ofont]align","id":"jformparamsmoduleparametersTabthemelevel1ofontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1ofontalign\"><div class=\"radioelement first\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement  selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1ofontalign\" name=\"jform[params][moduleparametersTab][theme][level1ofont]align\" value=\"center\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][level1ofont]afont","id":"jformparamsmoduleparametersTabthemelevel1ofontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1ofontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1ofontafontinput\" value=\"Arial, Helvetica\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1ofontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1ofontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]afont\" id=\"jformparamsmoduleparametersTabthemelevel1ofontafont\" value=\"Arial, Helvetica||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][level1ofont]tshadow","id":"jformparamsmoduleparametersTabthemelevel1ofonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel1ofonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1ofonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tshadow0\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1ofonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow1input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tshadow1\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow1\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1ofonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow2input\" value=\"2\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tshadow2\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow2\" value=\"2||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tshadow3\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow3\" value=\"rgba(0, 0, 0, 0.09)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1ofonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tshadow4\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow4\" value=\"1\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]tshadow\" id=\"jformparamsmoduleparametersTabthemelevel1ofonttshadow\" value='0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][level1ofont]lineheight","id":"jformparamsmoduleparametersTabthemelevel1ofontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1ofontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1ofontlineheightinput\" value=\"normal\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1ofont]lineheight\" id=\"jformparamsmoduleparametersTabthemelevel1ofontlineheight\" value=\"normal\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1ofonttab\",\r\n        values: [\"Text\",\"Active\",\"Hover\"],\r\n        map: {\"Text\":0,\"Active\":1,\"Hover\":2},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1ofontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1ofonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 8,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1ofonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1ofonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1ofonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemelevel1ofonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1ofontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    
        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemelevel1descfont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"13||px","color":"#90b2b2","bold":"0","italic":"0","underline":"0","align":"left","afont":"Arial, Helvetica||1","tshadow":"0||px|*|1||px|*|2||px|*|#000000|*|0","lineheight":"normal","family":"Roboto","subset":"latin","textdecor":"400"},"Active":{},"Hover":{}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][level1descfont]tab","id":"jformparamsmoduleparametersTabthemelevel1descfonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1descfonttab\"><div class=\"radioelement first selected\">Text<\/div><div class=\"radioelement \">Active<\/div><div class=\"radioelement  last\">Hover<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttab\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][level1descfont]type","id":"jformparamsmoduleparametersTabthemelevel1descfonttype","latin":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][level1descfont]family","id":"jformparamsmoduleparametersTabthemelevel1descfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1descfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]type\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][level1descfont]size","id":"jformparamsmoduleparametersTabthemelevel1descfontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1descfontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1descfontsizeinput\" value=\"13\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1descfontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]size[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1descfontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]size\" id=\"jformparamsmoduleparametersTabthemelevel1descfontsize\" value=\"13||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][level1descfont]color","id":"jformparamsmoduleparametersTabthemelevel1descfontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1descfont]color\" id=\"jformparamsmoduleparametersTabthemelevel1descfontcolor\" value=\"#90b2b2\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][level1descfont]textdecor","id":"jformparamsmoduleparametersTabthemelevel1descfonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1descfonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">normal<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]textdecor\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttextdecor\" value=\"400\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][level1descfont]italic","id":"jformparamsmoduleparametersTabthemelevel1descfontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1descfontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]italic\" id=\"jformparamsmoduleparametersTabthemelevel1descfontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][level1descfont]underline","id":"jformparamsmoduleparametersTabthemelevel1descfontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1descfontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]underline\" id=\"jformparamsmoduleparametersTabthemelevel1descfontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][level1descfont]linethrough","id":"jformparamsmoduleparametersTabthemelevel1descfontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1descfontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]linethrough\" id=\"jformparamsmoduleparametersTabthemelevel1descfontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][level1descfont]uppercase","id":"jformparamsmoduleparametersTabthemelevel1descfontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1descfontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]uppercase\" id=\"jformparamsmoduleparametersTabthemelevel1descfontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][level1descfont]align","id":"jformparamsmoduleparametersTabthemelevel1descfontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1descfontalign\"><div class=\"radioelement first selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement \"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1descfontalign\" name=\"jform[params][moduleparametersTab][theme][level1descfont]align\" value=\"left\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][level1descfont]afont","id":"jformparamsmoduleparametersTabthemelevel1descfontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1descfontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1descfontafontinput\" value=\"Arial, Helvetica\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1descfontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1descfontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]afont\" id=\"jformparamsmoduleparametersTabthemelevel1descfontafont\" value=\"Arial, Helvetica||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][level1descfont]tshadow","id":"jformparamsmoduleparametersTabthemelevel1descfonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel1descfonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1descfonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tshadow0\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1descfonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow1input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tshadow1\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow1\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1descfonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow2input\" value=\"2\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tshadow2\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow2\" value=\"2||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tshadow3\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow3\" value=\"#000000\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1descfonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tshadow4\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow4\" value=\"0\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]tshadow\" id=\"jformparamsmoduleparametersTabthemelevel1descfonttshadow\" value='0||px|*|1||px|*|2||px|*|#000000|*|0'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][level1descfont]lineheight","id":"jformparamsmoduleparametersTabthemelevel1descfontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1descfontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1descfontlineheightinput\" value=\"normal\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1descfont]lineheight\" id=\"jformparamsmoduleparametersTabthemelevel1descfontlineheight\" value=\"normal\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1descfonttab\",\r\n        values: [\"Text\",\"Active\",\"Hover\"],\r\n        map: {\"Text\":0,\"Active\":1,\"Hover\":2},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1descfontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1descfonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 3,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1descfonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1descfonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1descfonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemelevel1descfonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1descfontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    
        new MiniFontConfigurator({
          id: "jformparamsmoduleparametersTabthemelevel1odescfont",
          defaultTab: "Text",
          origsettings: {"Text":{"type":"latin","size":"17||px","color":"rgba(255,255,255,0.8)","italic":"0","underline":"0","align":"center","afont":"Arial, Helvetica||1","tshadow":"0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|","lineheight":"normal","family":"Roboto","subset":"latin","textdecor":"900"},"Active":{},"Hover":{}},
          elements: {"tab":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]tab","id":"jformparamsmoduleparametersTabthemelevel1odescfonttab","html":"<div class=\"offlajnradiocontainerbutton\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1odescfonttab\"><div class=\"radioelement first selected\">Text<\/div><div class=\"radioelement \">Active<\/div><div class=\"radioelement  last\">Hover<\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttab\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tab\" value=\"Text\"\/>"},"type":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]type","id":"jformparamsmoduleparametersTabthemelevel1odescfonttype","latin":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"latin_ext":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_latin_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"greek_ext":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_greek_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"hebrew":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_hebrew\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"vietnamese":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_vietnamese\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"arabic":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_arabic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"devanagari":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_devanagari\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"cyrillic_ext":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_cyrillic_ext\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"khmer":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_khmer\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"tamil":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_tamil\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"thai":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_thai\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"telugu":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_telugu\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"bengali":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_bengali\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"gujarati":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]family","id":"jformparamsmoduleparametersTabthemelevel1odescfontfamily","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfontfamily\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\"><br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]family\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontfamily\" value=\"\"\/><\/div><\/div>","script":"dojo.addOnLoad(function(){\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfontfamily\",\r\n        options: [],\r\n        selectedIndex: 0,\r\n        json: \"OfflajnFont_gujarati\",\r\n        width: 164,\r\n        height: \"12\",\r\n        fireshow: 1\r\n      });\r\n    });"},"html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfonttype\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">Latin<br \/>Alternative fonts<br \/>Latin<br \/>latin_ext<br \/>Greek<br \/>greek_ext<br \/>hebrew<br \/>Vietnamese<br \/>arabic<br \/>devanagari<br \/>Cyrillic<br \/>cyrillic_ext<br \/>Khmer<br \/>tamil<br \/>thai<br \/>telugu<br \/>bengali<br \/>gujarati<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]type\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttype\" value=\"latin\"\/><\/div><\/div>"},"size":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]size","id":"jformparamsmoduleparametersTabthemelevel1odescfontsize","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1odescfontsize\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontsizeinput\" value=\"17\"><div class=\"offlajntext_increment\">\n                <div class=\"offlajntext_increment_up arrow\"><\/div>\n                <div class=\"offlajntext_increment_down arrow\"><\/div>\n      <\/div><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1odescfontsizeunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]size[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontsizeunit\" value=\"px\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]size\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontsize\" value=\"17||px\">"},"color":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]color","id":"jformparamsmoduleparametersTabthemelevel1odescfontcolor","html":"<div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]color\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontcolor\" value=\"#rgba(255,255,255,0.8)\" class=\"color\" \/><\/div>"},"textdecor":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]textdecor","id":"jformparamsmoduleparametersTabthemelevel1odescfonttextdecor","html":"<div style='position:relative;'><div id=\"offlajnlistcontainerjformparamsmoduleparametersTabthemelevel1odescfonttextdecor\" class=\"gk_hack offlajnlistcontainer\"><div class=\"gk_hack offlajnlist\"><span class=\"offlajnlistcurrent\">ultra-bold<br \/>thin<br \/>extra-light<br \/>light<br \/>normal<br \/>medium<br \/>semi-bold<br \/>bold<br \/>extra-bold<br \/>ultra-bold<br \/><\/span><div class=\"offlajnlistbtn\"><span><\/span><\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]textdecor\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttextdecor\" value=\"900\"\/><\/div><\/div>"},"italic":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]italic","id":"jformparamsmoduleparametersTabthemelevel1odescfontitalic","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1odescfontitalic\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/italic.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]italic\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontitalic\" value=\"0\" \/>"},"underline":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]underline","id":"jformparamsmoduleparametersTabthemelevel1odescfontunderline","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1odescfontunderline\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/underline.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]underline\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontunderline\" value=\"0\" \/>"},"linethrough":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]linethrough","id":"jformparamsmoduleparametersTabthemelevel1odescfontlinethrough","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1odescfontlinethrough\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/linethrough.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]linethrough\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontlinethrough\" value=\"0\" \/>"},"uppercase":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]uppercase","id":"jformparamsmoduleparametersTabthemelevel1odescfontuppercase","html":"<div id=\"offlajnonoffjformparamsmoduleparametersTabthemelevel1odescfontuppercase\" class=\"gk_hack onoffbutton\">\n                <div class=\"gk_hack onoffbutton_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnonoff\/images\/uppercase.png);\"><\/div>\n      <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]uppercase\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontuppercase\" value=\"0\" \/>"},"align":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]align","id":"jformparamsmoduleparametersTabthemelevel1odescfontalign","html":"<div class=\"offlajnradiocontainerimage\" id=\"offlajnradiocontainerjformparamsmoduleparametersTabthemelevel1odescfontalign\"><div class=\"radioelement first\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/left_align.png);\"><\/div><\/div><div class=\"radioelement  selected\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/center_align.png);\"><\/div><\/div><div class=\"radioelement  last\"><div class=\"radioelement_img\" style=\"background-image: url(http:\/\/printervoronezh.ru\/administrator\/..\/modules\/mod_vertical_menu\/params\/offlajnradio\/images\/right_align.png);\"><\/div><\/div><div class=\"clear\"><\/div><\/div><input type=\"hidden\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontalign\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]align\" value=\"center\"\/>"},"afont":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]afont","id":"jformparamsmoduleparametersTabthemelevel1odescfontafont","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1odescfontafont\"><input  size=\"10\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontafontinput\" value=\"Arial, Helvetica\"><\/div><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1odescfontafontunit\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]afont[unit]\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontafontunit\" value=\"1\" \/><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]afont\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontafont\" value=\"Arial, Helvetica||1\">"},"tshadow":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]tshadow","id":"jformparamsmoduleparametersTabthemelevel1odescfonttshadow","html":"<div id=\"offlajncombine_outerjformparamsmoduleparametersTabthemelevel1odescfonttshadow\" class=\"offlajncombine_outer\"><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1odescfonttshadow0\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow0input\" value=\"0\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tshadow0\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow0\" value=\"0||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1odescfonttshadow1\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow1input\" value=\"1\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tshadow1\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow1\" value=\"1||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1odescfonttshadow2\"><input  size=\"1\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow2input\" value=\"2\"><div class=\"unit\">px<\/div><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tshadow2\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow2\" value=\"2||px\"><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnminicolor\"><input type=\"text\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tshadow3\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow3\" value=\"rgba(0, 0, 0, 0.09)\" class=\"color\" \/><\/div><\/div><\/div><div class=\"offlajncombinefieldcontainer\"><div class=\"offlajncombinefield\"><div class=\"offlajnswitcher\">\r\n            <div class=\"offlajnswitcher_inner\" id=\"offlajnswitcher_innerjformparamsmoduleparametersTabthemelevel1odescfonttshadow4\"><\/div>\r\n    <\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tshadow4\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow4\" value=\"1\" \/><\/div><\/div><\/div><div class=\"offlajncombine_hider\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]tshadow\" id=\"jformparamsmoduleparametersTabthemelevel1odescfonttshadow\" value='0||px|*|1||px|*|2||px|*|rgba(0, 0, 0, 0.09)|*|1|*|'>"},"lineheight":{"name":"jform[params][moduleparametersTab][theme][level1odescfont]lineheight","id":"jformparamsmoduleparametersTabthemelevel1odescfontlineheight","html":"<div class=\"offlajntextcontainer\" id=\"offlajntextcontainerjformparamsmoduleparametersTabthemelevel1odescfontlineheight\"><input  size=\"5\" class=\"offlajntext\" type=\"text\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontlineheightinput\" value=\"normal\"><\/div><input type=\"hidden\" name=\"jform[params][moduleparametersTab][theme][level1odescfont]lineheight\" id=\"jformparamsmoduleparametersTabthemelevel1odescfontlineheight\" value=\"normal\">"}},
          script: "dojo.addOnLoad(function(){\r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfonttab\",\r\n        values: [\"Text\",\"Active\",\"Hover\"],\r\n        map: {\"Text\":0,\"Active\":1,\"Hover\":2},\r\n        mode: \"\"\r\n      });\r\n    \r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfonttype\",\r\n        options: [{\"value\":\"0\",\"text\":\"Alternative fonts\"},{\"value\":\"latin\",\"text\":\"Latin\"},{\"value\":\"latin_ext\",\"text\":\"latin_ext\"},{\"value\":\"greek\",\"text\":\"Greek\"},{\"value\":\"greek_ext\",\"text\":\"greek_ext\"},{\"value\":\"hebrew\",\"text\":\"hebrew\"},{\"value\":\"vietnamese\",\"text\":\"Vietnamese\"},{\"value\":\"arabic\",\"text\":\"arabic\"},{\"value\":\"devanagari\",\"text\":\"devanagari\"},{\"value\":\"cyrillic\",\"text\":\"Cyrillic\"},{\"value\":\"cyrillic_ext\",\"text\":\"cyrillic_ext\"},{\"value\":\"khmer\",\"text\":\"Khmer\"},{\"value\":\"tamil\",\"text\":\"tamil\"},{\"value\":\"thai\",\"text\":\"thai\"},{\"value\":\"telugu\",\"text\":\"telugu\"},{\"value\":\"bengali\",\"text\":\"bengali\"},{\"value\":\"gujarati\",\"text\":\"gujarati\"}],\r\n        selectedIndex: 1,\r\n        json: \"\",\r\n        width: 0,\r\n        height: \"12\",\r\n        fireshow: 0\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontsizeunit\",\r\n        units: [\"px\",\"em\"],\r\n        values: [\"px\",\"em\"],\r\n        map: {\"px\":0,\"em\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontsize\",\n        validation: \"int\",\n        attachunit: \"\",\n        mode: \"increment\",\n        scale: \"1\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1odescfontcolor\").minicolors({opacity: false, position: \"bottom left\"});\r\n      new OfflajnList({\r\n        name: \"jformparamsmoduleparametersTabthemelevel1odescfonttextdecor\",\r\n        options: [{\"value\":\"100\",\"text\":\"thin\"},{\"value\":\"200\",\"text\":\"extra-light\"},{\"value\":\"300\",\"text\":\"light\"},{\"value\":\"400\",\"text\":\"normal\"},{\"value\":\"500\",\"text\":\"medium\"},{\"value\":\"600\",\"text\":\"semi-bold\"},{\"value\":\"700\",\"text\":\"bold\"},{\"value\":\"800\",\"text\":\"extra-bold\"},{\"value\":\"900\",\"text\":\"ultra-bold\"}],\r\n        selectedIndex: 8,\r\n        json: \"\",\r\n        width: 0,\r\n        height: 0,\r\n        fireshow: 0\r\n      });\r\n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontitalic\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontunderline\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontlinethrough\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \n      new OfflajnOnOff({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontuppercase\",\n        mode: \"button\",\n        imgs: \"\"\n      }); \n    \r\n      new OfflajnRadio({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontalign\",\r\n        values: [\"left\",\"center\",\"right\"],\r\n        map: {\"left\":0,\"center\":1,\"right\":2},\r\n        mode: \"image\"\r\n      });\r\n    dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontafontunit\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontafont\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"1\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfonttshadow0\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfonttshadow1\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfonttshadow2\",\n        validation: \"float\",\n        attachunit: \"px\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    jQuery(\"#jformparamsmoduleparametersTabthemelevel1odescfonttshadow3\").minicolors({opacity: true, position: \"bottom left\"});dojo.addOnLoad(function(){ \r\n      new OfflajnSwitcher({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfonttshadow4\",\r\n        units: [\"ON\",\"OFF\"],\r\n        values: [\"1\",\"0\"],\r\n        map: {\"1\":0,\"0\":1},\r\n        mode: 0,\r\n        url: \"http:\\\/\\\/printervoronezh.ru\\\/administrator\\\/..\\\/modules\\\/mod_vertical_menu\\\/params\\\/offlajnswitcher\\\/images\\\/\"\r\n      }); \r\n    });\r\n      new OfflajnCombine({\r\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfonttshadow\",\r\n        num: 5,\r\n        switcherid: \"jformparamsmoduleparametersTabthemelevel1odescfonttshadow4\",\r\n        hideafter: \"0\",\r\n        islist: \"0\"\r\n      }); \r\n    \n      new OfflajnText({\n        id: \"jformparamsmoduleparametersTabthemelevel1odescfontlineheight\",\n        validation: \"\",\n        attachunit: \"\",\n        mode: \"\",\n        scale: \"\",\n        minus: 0,\n        onoff: \"\",\n        placeholder: \"\"\n      });\n    });"
        });
    jQuery("#jformparamsmoduleparametersTabthemelevel1countbg").minicolors({opacity: true, position: "bottom left"});
        new OfflajnImagemanager({
          id: "jformparamsmoduleparametersTabthemelevel1plus0",
          folder: "/modules/mod_vertical_menu/themes/clean/images/arrows/",
          root: "",
          uploadurl: "index.php?option=offlajnupload",
          imgs: ["arrow_left.png","arrow_right.png","big_left.png","big_right.png","big_tree.png","bold_left.png","bold_right.png","circle_left.png","circle_right.png","default_left.png","default_right.png","default_tree.png","round_left.png","round_right.png","thin_left.png","thin_right.png","triangle_left.png","triangle_right.png"],
          identifier: "5751c932107b3dc8442fb61ef596fac5",
          description: "",
          siteurl: "http://printervoronezh.ru/"
        });
    
      new OfflajnList({
        name: "jformparamsmoduleparametersTabthemelevel1plus1",
        options: [{"value":"left","text":"Left"},{"value":"right","text":"Right"}],
        selectedIndex: 1,
        json: "",
        width: 0,
        height: 0,
        fireshow: 0
      });
    jQuery("#jformparamsmoduleparametersTabthemelevel1plus2").minicolors({opacity: true, position: "bottom left"});jQuery("#jformparamsmoduleparametersTabthemelevel1plus3").minicolors({opacity: true, position: "bottom left"});
      new OfflajnCombine({
        id: "jformparamsmoduleparametersTabthemelevel1plus",
        num: 4,
        switcherid: "",
        hideafter: "1",
        islist: "0"
      }); 
    });});
      djConfig = {};})();