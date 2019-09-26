;var ajaxCart = ajaxCart || {};
ajaxCart.showModal = function(html) {
	var $=jQuery;
	if ($('#ajaxcart_overlay').size()>0) {
		ajaxCart.hideModal();
	}
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var div=$('<div>').attr('id','ajaxcart_overlay').attr('onclick','ajaxCart.hideModal()');
	div.width(windowWidth+'px');
	div.height(windowHeight+'px');
	$(document.body).append(div);
	div=$('<div>').attr('id','ajaxcart_modal').html(html);
	$(document.body).append(div);
	div = $('#ajaxcart_modal');
	var divWidth = div.width();
	if (divWidth > windowWidth) {
		divWidth = windowWidth;
		div.width(divWidth+'px');
	}
	var divHeight = div.height();
	if (divHeight > windowHeight) {
		divHeight = windowHeight;
		div.height(divHeight+'px');
	}
	div.css({top: (windowHeight - divHeight) / 2+'px', left: (windowWidth - divWidth) / 2+'px'}).fadeIn();
}
ajaxCart.hideModal = function() {
	jQuery('#ajaxcart_overlay, #ajaxcart_modal').remove();
}
ajaxCart.cart_refresh = function(cart){
	var $ = jQuery;
	ajaxCart.showModal(cart['html']);
	$('#jshop_quantity_products').html(cart['format_qty']);
	$('#jshop_summ_product').html(cart['format_price']);
}
jQuery(function($){
    $('body').delegate('.jshop_list_product .buttons a.button_buy', 'click', function(e){
		e.preventDefault();
		var a = $(this);
		$.ajax({
			type: 'POST',
			url: a.attr('href'), 
			data : 'ajax=1',
			cache: false,  
			dataType: 'text',
			success: function(json){
				var cart = $.parseJSON(json);
				if (typeof cart[0] != 'undefined' && typeof cart[0].message != 'undefined') {
					var messages = new Array();
					var redirect_url;
					$.each(cart, function(key, cart_item){
						if(typeof cart_item.message != 'undefined' && cart_item.message.length > 0) {
							messages.push(cart_item.message);
							if (cart_item.code == 101 || cart_item.code == 102) {
								redirect_url = a.attr('href');
								return false;
							}
						}
					});
					if (redirect_url) {
						window.location.href = a.attr('href');
					} else if(messages.length > 0) {
						alert(messages.join(String.fromCharCode(10) + String.fromCharCode(13)));
					}
				} else if (typeof cart['html'] != 'undefined') {
					ajaxCart.cart_refresh(cart);
				}
			}  
		});  
    });
	$('form[name=product]').submit(function(e) {
		if ($('#to').val() != 'cart') {
			return;
		}
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'), 
			data : $(this).serialize()+'&ajax=1',
			cache: false,  
			dataType: 'text',
			success: function(json){
				var cart = $.parseJSON(json);
				if(typeof cart[0] != 'undefined' && typeof cart[0].message != 'undefined') {
					var messages = new Array();
					$.each(cart, function(key, cart_item){
						if(typeof cart_item.message != 'undefined' && cart_item.message.length > 0) {
							if(cart_item.code != 'redirect_url') {
								messages.push(cart_item.message);
							}
						}
					});
					if(messages.length > 0) {
						alert(messages.join(String.fromCharCode(10) + String.fromCharCode(13)));
					}
				} else if (typeof cart['html'] != 'undefined') {
					ajaxCart.cart_refresh(cart);
				}
			}  
		});  
	});
});       	    	  	     	      	          	