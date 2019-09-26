;(function($, window, document, undefined) {
  var pluginName = "accordion";
  var defaults = {
    speed: 200,
    showDelay: 0,
    hideDelay: 0,
    singleOpen: true,
    clickEffect: true,
    indicator: 'submenu-indicator-minus',
    subMenu: 'submenu',
    event: 'click touchstart'
  };
  function Plugin(element, options) {
    this.element = element;
    this.settings = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }
  $.extend(Plugin.prototype, {
    init: function() {
      this.openSubmenu();
      this.submenuIndicators();
      if (defaults.clickEffect) {
        this.addClickEffect();
      }
    },
    openSubmenu: function() {
		console.log($(this.element).find(".submenu-indicator"));
      $(this.element).find(".mat_but_hide").bind(defaults.event, function(e) {
        
		e.stopPropagation();
        e.preventDefault();

        var $subMenus = $(this).parent().children("." + defaults.subMenu);
		console.log($subMenus);
        var $allSubMenus = $(this).parent().find("." + defaults.subMenu);
        if ($subMenus.length > 0) {
          if ($subMenus.css("display") == "none") {
            $subMenus.slideDown(defaults.speed).parent("li").addClass(defaults.indicator);
            if (defaults.singleOpen) {
              $(this).parent().siblings().find("." + defaults.subMenu).slideUp(defaults.speed)
                .end().find("li").removeClass(defaults.indicator);
            }
            return false;
          } else {
            $(this).parent().find("." + defaults.subMenu).delay(defaults.hideDelay).slideUp(defaults.speed);
          }
          if ($allSubMenus.parent("li").hasClass(defaults.indicator)) {
            $allSubMenus.parent("li").removeClass(defaults.indicator);
          }
        }
      });
    },
    submenuIndicators: function() {
      if ($(this.element).find("." + defaults.subMenu).length > 0) {
		  console.log($(this.element).find("." + defaults.subMenu).length);
        $(this.element).find("." + defaults.subMenu).parent("li").find(".mat_but_hide").prepend("<span class='submenu-indicator'>+</span>");
		
		
		
      }
    },
    addClickEffect: function() {
      var ink, d, x, y;
      $(this.element).find("a").bind("click touchstart", function(e) {
		  
		  
        $(".ink").remove();
        if ($(this).children(".ink").length === 0) {
          $(this).prepend("<span class='ink'></span>");
        }
        ink = $(this).find(".ink");
        ink.removeClass("animate-ink");
        if (!ink.height() && !ink.width()) {
          d = Math.max($(this).outerWidth(), $(this).outerHeight());
          ink.css({
            height: d,
            width: d
          });
        }
        x = e.pageX - $(this).offset().left - ink.width() / 2;
        y = e.pageY - $(this).offset().top - ink.height() / 2;
        ink.css({
          top: y + 'px',
          left: x + 'px'
        }).addClass("animate-ink");
      });
    }
  });
  $.fn[pluginName] = function(options) {
    this.each(function() {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, "plugin_" + pluginName, new Plugin(this, options));
      }
    });
    return this;
  };
})(jQuery, window, document);

jQuery(function($) {
	$("#mat_categories_menu").accordion();
	$("#mat_categories_menu a").click(function(){
		location.href = $(this).attr("href");
	});
	$("ul li .active").parent().parent("ul").show();
	$("ul li .active").parent().parent().parent("ul").show();
	$("ul li .active").parent().parent().parent().parent("ul").show();
	$("ul li .active").parent().parent().parent().parent().parent().parent("ul").show();
	$("ul li .active").parent().parent().parent("li").addClass("submenu-indicator-minus");
});