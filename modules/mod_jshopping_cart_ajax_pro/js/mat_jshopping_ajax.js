/**
* @version      1.0.1 2.6.2017
* @author       MatBlunic
* @package      JoomShopping
* @copyright    Copyright (C) 2017 matblunic.com. All rights reserved.
*/
function MatAjaxInitx() {
	jQuery(function($) {
	// Cart
	$(".icon-cart-top-wrapper .mat_cart_button").on('click', function(){
		$(".mat_cart_top_list").fadeIn(200);
	});
	$(".mat_cart-cloase-button").on('click', function(){
		$(".mat_cart_top_list").fadeOut(200);
	});
	//Ajax Cart
	$('.button_buy').click(function(event) {
		event.preventDefault();
		var product_url = $(this).closest('.product').children('.name').children('a').attr('href');
		var image_src = $(this).closest('.product').children('.image').children('.image_block').children('a').children('img').attr('src');
		var title_name = $(this).closest('.product').children('.name').children('a').text();
		var prod_price = $(this).closest('.product').children('.oiproduct').children('.jshop_price').text();
		var substr_price = Math.floor(prod_price.replace(/[^0-9]/gi, ''));
		var total_price = $("#jshop_summ_product").text();
		var currency = $('.mat_curr').attr('value');
		var substr_total_price = Math.floor(total_price.replace(/[^0-9]/gi, ''));
		
		//Html For Cart
		var html_con = $('<div/>', {
			'id':'myDiv',
			'class':'mat_cart_prod col-xs-12',
			'style':'',
			'html':'<div class="mat_cart_image_block col-md-4"><img src="'+ image_src +'"/></div>' + 
			'<div class="mat_prod-cart-info col-md-8">'+
			'<div class="mat_prod-cat-name">'+ title_name +'</div>' +
			'<div class="mat_prod-cat-quan">'+'1 x'+'</div>' +
			'<div class="mat_prod-cat-sum">'+ prod_price + '</div>'
			+'<div>' }
		).prependTo('.mat_cart_info_box');
		
		// Html For Pop Up
		var html_popup_con = $('<div/>', {
			'class':'mat_cart_prod col-xs-12',
			'style':'',
			'html':'<div class="mat_cart_image_block col-md-4"><img src="'+ image_src +'"/></div>' + 
			'<div class="mat_prod-cart-info col-md-8">'+
			'<div class="mat_prod-cat-name">'+ title_name +'</div>' +
			'<div class="mat_prod-cat-quan">'+'1 x'+'</div>' +
			'<div class="mat_prod-cat-sum">'+ prod_price + '</div>'
			+'<div>'}
		).prependTo('.mat_popup_product');
		var this_href = this.href;
		var this_string = this_href.toString();
		var this_part_of_url = this_string.slice(-6);
		var this_id = this_part_of_url.replace( /\D+/g, '');
		var curent_prod = $(this);
		var num = parseInt($.trim($('.mat_cart_counter').html()));
		
		// AJAX Start
		$.ajax(product_url, {
			// Before Send
			beforeSend: function() {    
				curent_prod.html('<i class = "icon-clock mat_no_click" ></i>')
				curent_prod.children('i').removeClass('icon-basket').addClass('mat_no_click icon-clock');
				$('.mat_popup_load').fadeIn();
			},
			// Success
			success: function(data) {
				
				// Retrive Pop Up
				$('.mat_infinity_loader').fadeOut();
				$('.mat_popup_load_box').fadeIn();
				
				$(".mat_prod-cloase-button").on('click', function(){
					$(".mat_popup_load").fadeOut(200);
				});
				html_popup_con;
				
				// Make Url For Attributes if attr have.
				if($(data).find( ".attributes_title")){
					// Search in product page .jshop_prod_attributes
					var obj = $(data).find('.jshop_prod_attributes');
					// Make Array
					var arr = jQuery.makeArray( obj );
					// Create a free array
					var array_c = []; 
					// Loop Array forEach
					arr.forEach(function(div_elements){
						// Search input attributes in array div_elements
						if($(div_elements).find('input')){
							console.log("input find");
							var radio_name = $(div_elements).find('input').attr('name');
							var radio_value = $(div_elements).find('input').attr('value');
								if(radio_name && radio_value){
									// Create a Link
									var link_details = radio_name + "=" + radio_value;
									array_c.push(link_details);
								}
						}
						// Search selects attributes in array div_elements
						if($(div_elements).find('select')){
							var select_name = $(div_elements).find('select').attr('name');
							var select_value = $(div_elements).find('select').children('option').attr('value');
							if(select_name && select_value){
									var link_detailss = select_name + "=" + select_value;
									array_c.push(link_detailss);
								}
						}
					});
					var half_url = array_c.join("&");
					console.log(half_url);
				}
				// Inner Ajax Start Send Product to Cart
				$.ajax(this_href + "&" + half_url, {
				   success: function(data) {
				   }
				});
				 curent_prod.children('i').removeClass('icon-clock').addClass('icon-check'); 
				 $('.mat_cart_counter');
				 $('.mat_cart_counter').html(++num);
				 curent_prod.removeAttr('href');
				 // Call html
				 html_con;
				 curent_prod.removeClass('button_buy').addClass('mat_no_click');
				 $('.mat_prod-cat-empty').remove();
				 var sum_total = Number(substr_price) + Number(substr_total_price);
				 $("#jshop_summ_product").text(currency + sum_total);
		  },
		  error: function() {
			 $('.price_extra_info').text('An error occurred');
		  }
	   });
	});});
}

jQuery(function($) {
	MatAjaxInitx();
});