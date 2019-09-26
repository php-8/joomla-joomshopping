jQuery(function($) {
	
		

	
	if($( ".attr_select" )){
		var obj = $( ".attr_select" );
		var arr = jQuery.makeArray( obj );
		var array_c = []; 
		arr.forEach(function(div_elements){
			
			// Search input attributes in array div_elements
			if($(div_elements).find('input')){
				var radio_name = $(div_elements).find('input').attr('name');
				var radio_value = $(div_elements).find('input').attr('value');
					if(radio_name && radio_value){
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
		var i = 0;
		var prod_href = "href";
		var half_url = array_c.join("&");
		var cart_url = prod_href + half_url;
	}
	$(".owl-carousel").owlCarousel({
		margin : 5,
		nav    : true,
		smartSpeed :900,
		autoWidth:true,
		dots: false,
		navText : ["<i class='icon-arrow-left'></i>","<i class='icon-arrow-right'></i>"]
	});
	$(".owl-carousel-brands").owlCarousel({
		margin : 5,
		nav    : false,
		smartSpeed :900,
		dots: false,
		autoplay:true,
		loop:true,
		autoplayTimeout:2000,
		autoplayHoverPause:true,
		responsiveClass:true,
		responsive:{
			0:{
				items:3, // In this configuration 1 is enabled from 0px up to 479px screen size 
				margin: 20
			},
			480:{
				items:2, // from 480 to 677 
				nav:false // from 480 to max 
			},
			678:{
				items:4, // from this breakpoint 678 to 959
			},
			960:{
				items:7, // from this breakpoint 960 to 1199
				margin:60, // and so on...
				center:false 
			}
		}
	});
	$(".owl-carousel-mat-last-products").owlCarousel({
		margin : 40,
		items:5,
		dots: false,
		nav    : true,
		responsiveClass:true,
		responsive:{
			0:{
				items:1 // In this configuration 1 is enabled from 0px up to 479px screen size 
			},
			480:{
				items:1, // from 480 to 677 
				nav:false // from 480 to max 
			},
			678:{
				items:4, // from this breakpoint 678 to 959
				center:true // only within 678 and next - 959
			},
			960:{
				items:4, // from this breakpoint 960 to 1199
				margin:30, // and so on...
				center:false 
			}
		},
		navText : ["<i class='icon-arrow-left'></i>","<i class='icon-arrow-right'></i>"]
	});
	
	
	if($('div').is('#list_product_image_middle')){
		
		image_id_text = $('#list_product_image_middle').children('a').attr('id');
		image_id_num = parseInt(image_id_text.replace( /\D+/g, ''));
		
		console.log(image_id_num);
		
		$("#main_image_" + image_id_num).elevateZoom();
		$("#main_image_" + (image_id_num + 1)).elevateZoom();
		$("#main_image_" + (image_id_num + 2)).elevateZoom();
		$("#main_image_" + (image_id_num + 3)).elevateZoom();
		$("#main_image_" + (image_id_num + 4)).elevateZoom();
		$("#main_image_" + (image_id_num + 5)).elevateZoom();
		$("#main_image_" + (image_id_num + 6)).elevateZoom();
		$("#main_image_" + (image_id_num + 7)).elevateZoom();
	}
	
	$('.counter-plus').click(function(event) {
		event.preventDefault();
		var this_input = $(this).parent().children('input');
		var quan_val = parseInt(this_input.attr('value')) + 1;
		$(this).parent().children('input').attr('value', quan_val);
	});
	$('.counter-minus').click(function(event) {
		event.preventDefault();
		var this_input = $(this).parent().children('input');
		var quan_val = parseInt(this_input.attr('value')) - 1;
		if(quan_val > 0){
			$(this).parent().children('input').attr('value', quan_val);
		}
	});

	// Grid List Function
	$('#grid').addClass('grid-list-active');
	$('#list').click(function(event){event.preventDefault(); $('#grid').removeClass('grid-list-active'); $('#list').addClass('grid-list-active'); $('#products .prod-item').addClass('prod-list-group-item');});
    $('#grid').click(function(event){event.preventDefault(); $('#list').removeClass('grid-list-active'); $('#grid').addClass('grid-list-active'); $('#products .prod-item').removeClass('prod-list-group-item');$('#products .prod-item').addClass('grid-group-item');});

	// Nice Select Plugin
	$("select").niceSelect();
	
	//////// AJAX CART OVERRIDE ////////////////
	function MatAjaxCart(){
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
			var product_url = $(this).attr('href');
			var image_src = $(this).closest('.thumbnail').children('.prod-left').children('.image').children('.image_block').children('a').children('img').attr('src');
			var title_name = $(this).parent().parent().parent().parent().children('.prod-center').children('.name').children('a').text();
			var prod_price = $(this).parent().parent().children('.jshop_price').children('.prod-cur-price').text();
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
								var our_input = $(div_elements).find('input');
								var our_input_array = jQuery.makeArray( our_input );
								
								our_input_array.forEach(function(input_elements){
									var radio_name = $(input_elements).attr('name');
									var radio_value = $(input_elements).attr('value');
										if(radio_name && radio_value){
											// Create a Link
											var link_details = radio_name + "=" + radio_value;
											array_c.push(link_details);
										}
								});
							}
							// Search selects attributes in array div_elements
							if($(div_elements).find('select')){	
								var our_select = $(div_elements).find('select');
								var our_input_array = jQuery.makeArray( our_select );
								
								our_input_array.forEach(function(select_elements){
								
									var select_name = $(select_elements).attr('name');
									var select_value = $(select_elements).children("option").attr('value');
								
									if(select_name && select_value){
											var link_detailss = select_name + "=" + select_value;
											array_c.push(link_detailss);
										}
								});
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
			event.stopImmediatePropagation();
		});
	}
	// Call The Cart
	MatAjaxCart();
	//////// END ////////////////
	
	//////////////////  AJax Filter  ///////////////////////
	function MatAjaxFilter(){
		function goAjax(){
			var url = $('form').attr('action');
			$.ajax({
				   type: "POST",
				   url: url,
				   data: $("#mat_js_filter").serialize(), 
				   beforeSend: function(){
					   $('#comjshop_list_product').fadeOut();
					   $(".mat_spinner_block").addClass("mat_spinner");
				   },
				   success: function(data)
				   {
					   var our_prods = $(data).find('#comjshop_list_product');
					   $('#comjshop_list_product').html(our_prods);
					   $('#comjshop_list_product').fadeIn();
					   $(".mat_spinner").removeClass("mat_spinner");
					   $(".mat_spinner_block").removeClass("mat_spinner");
				   }
			});
		}
		
		// Check Buttons
		$(".iCheck-helper").click(function(){
			goAjax();
		});
		
		// Ui-Slider Start
		var handle = $( ".ui-slider-handle" );
		var max_price = $( "#max_price" ).attr("value");
		var price_from = $(".fprice_from").attr("value");
			
		$( ".range_slider" ).slider({
			range: true,
			min: 0,
			max: max_price,
			values: [ price_from, max_price],

			slide: function( event, ui ) {
				$( "#fprice_from" ).attr("placeholder", ui.values[ 0 ]).attr("value", ui.values[ 0 ]);
				$( "#fprice_to" ).attr("placeholder", ui.values[ 1 ]).attr("value", ui.values[ 1 ]);
			},
			stop: function( event, ui ) { 
				goAjax();
				$(".mat_spinner_block").addClass("mat_spinner");
			}
		});
		$( "#fprice_to" ).attr("value", (max_price));
		
		// Reset Button Press
		$('.clear_filter a').click( function() {
			var url = $('form').attr('action');
			var this_form = $('.jshop_filters form[name=jshop_filters]');
			if(this_form.length){
				this_form.find('.fprice_from').val('0');
				$( ".range_slider a" ).first().attr("style" , "left: 0%");
				$( ".range_slider div.ui-corner-all" ).attr("style" , "left: 0%; width: 100%;");
				this_form.find('input[type=checkbox]').prop('checked',false);
			}
			$.ajax({
				type: "POST",
				url: url,
				data: $("#mat_js_filter").serialize(), 
				beforeSend: function(){
				   $('#comjshop_list_product').fadeOut();
				   $('.clear_filter i').addClass('mat-spin');
				   $(".mat_spinner_block").addClass("mat_spinner");
				},
				success: function(data){
					 
				   var our_prods = $(data).find('#comjshop_list_product');
				   $('#comjshop_list_product').html(our_prods);
				   
				   $(".mat_spinner").removeClass("mat_spinner");
				   $('#comjshop_list_product').fadeIn();
					$(document).ready(function(){
						MatAjaxCart();
					});
				}
			});
		});
	}
	MatAjaxFilter();
	//////////////////  End  ///////////////////////
	
		
	// Cart
	$(".icon-cart-top-wrapper .cart_button").on('click', function(){
        $(".cart_top_list").fadeIn(200);
    });

    $(".cart-cloase-button").on('click', function(){
        $(".cart_top_list").fadeOut(200);
    });
	
	///////////////////////////
	$('.sp-mobile-light-logo').addClass('mat_show');
    $(window).scroll(function(){
		var default_logo = $('.sp-default-logo'); 
		var retina_logo = $('.sp-retina-logo'); 
        if($(this).scrollTop() > 200) {
			default_logo.addClass('mat_desctop_hidden');
			retina_logo.addClass('mat_desctop_show');
			$('.sp-retina-logo').show();
			$('.mat_cart_button').removeClass('mat_cart_white');
			$('.mat_cart_button').addClass('mat_cart_black');
			$('.sp-mobile-light-logo').removeClass('mat_show');
			$('.sp-mobile-light-logo').addClass('mat_hidden');
			$('.sp-mobile-dark-logo').addClass('mat_show');
        }
        if($(this).scrollTop() < 200) {
            default_logo.removeClass('mat_desctop_hidden');  
			retina_logo.removeClass('mat_desctop_show');
			retina_logo.hide();
			$('.sp-default-logo').show();
			$('.mat_cart_button').removeClass('mat_cart_black');
			$('.mat_cart_button').addClass('mat_cart_white');
			$('.sp-mobile-light-logo').removeClass('mat_hidden').addClass('mat_show');
			$('.sp-mobile-dark-logo').removeClass('mat_show');
        }
    });
    $('#offcanvas-toggler').on('click', function(event){
        event.preventDefault();
        $('body').addClass('offcanvas');
    });
    $( '<div class="offcanvas-overlay"></div>' ).insertBefore( '.body-innerwrapper > .offcanvas-menu' );

    //$('.offcanvas-menu').append( '<div class="offcanvas-overlay"></div>' );

    $('.close-offcanvas, .offcanvas-overlay').on('click', function(event){
        event.preventDefault();
        $('body').removeClass('offcanvas');
    });

    //Mega Menu
    $('.sp-megamenu-wrapper').parent().parent().css('position','static').parent().css('position', 'relative');
    $('.sp-menu-full').each(function(){
        $(this).parent().addClass('menu-justify');
    });

    //wrap bottom and footer in a div
    // $("section#sp-bottom, footer#sp-footer").wrapAll('<div class="sp-bottom-footer"></div>');
    // has slideshow and sub header
    $(document).ready(function(){
        var spHeader = $("#sp-header");
        if ($('body.com-sppagebuilder #sp-page-builder div.transparent_header').length) {
            $('body').addClass('has-slideshow');
        }
		if ($('body .sp-column  .sp-module.transparent_header').length) {
            $('body').addClass('has-slideshow');
        }
        //has subheader
        if ($('body #sp-page-title .sp-page-title.bg-image').length) {
             $('body').addClass('has-sub-image');
        }
        // class in header
        spHeader.addClass('menu-fixed-out');
    });
	
    //Slideshow height
    var slideHeight = $(window).height();
    $('.sppb-slider-wrapper.sppb-slider-fullwidth-wrapper .sppb-slideshow-fullwidth-item-bg').css('height',slideHeight);
    $('.sppb-addon-animated-headlines .sppb-addon-animated-headlines-bg').css('height',slideHeight);

    // Menu Fixed
    var windowSize = $(window);
    if ($('.sppb-slider-wrapper.sppb-slider-fullwidth-wrapper').length) {
        if(windowSize.scrollTop() + windowSize.height() >= windowSize[0].outerHeight) {
            var stickyNavTop = $('.sppb-slider-wrapper.sppb-slider-fullwidth-wrapper').offset().top;
        }
    }	
	var stickyNavTop = $('#sp-header').offset().top;
    var stickyNav = function(){
        var scrollTop = $(window).scrollTop();
        if (scrollTop > 300) {
            $('#sp-header').removeClass('menu-fixed-out');
			$('#sp-header').addClass('menu-up');
				if (scrollTop > 500) {
					$('#sp-header').addClass('menu-fixed');
					$('#sp-header').addClass('mat_sticky');
				} else{
					if($('#sp-header').hasClass('mat_sticky'))
						{ 
							$('#sp-header').removeClass('mat_sticky');
						}
				}
        }
        else
        {
            if($('#sp-header').hasClass('menu-up'))
            {
				$('#sp-header').removeClass('menu-up mat_sticky');
                $('#sp-header').removeClass('menu-fixed').addClass('menu-fixed-out');
				$('.searchwrapper').hide();
				$(".search-icon").show();
				$("#search_close").hide();
            }
        }
		if (scrollTop <= 500) {
			$(".search-icon").show();
			$("#search_close").hide();
			$('.searchwrapper').hide();
		}
		
    };
    stickyNav();
    $(window).scroll(function() {
        stickyNav();
    });
    //Search
    $(".icon-search.search-icon").on('click', function(){
        $(".searchwrapper").show("fast");
		$(".top-search-box").addClass("mat_show_search");
        $(".remove-search").delay(200).fadeIn(200);
        $(".search-icon").hide();
    });
    $("#search_close").on('click', function(){
        $(".searchwrapper").hide(200);
        $(".remove-search").hide(400);
        $(".search-icon").delay(200).fadeIn(400);
		$(".top-search-box").removeClass("mat_show_search");
    });
    // press esc to hide search
    $(document).keyup(function(e) { 
        if (e.keyCode == 27) { // esc keycode
            $(".searchwrapper").fadeOut(200);
            $(".remove-search").fadeOut(200);
            $(".search-icon").delay(100).fadeIn(200);
			$(".top-search-box").removeClass("mat_show_search");
        }
    });
    if (sp_gotop) {
        // go to top
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut(400);
            }
        });
        $('.scrollup').click(function () {
            $("html, body").animate({
                scrollTop: 0
            }, 600);
            return false;
        });
    }
    //scroll animation
    var lastScrollTop = 0;
    $(window).scroll(function(event){
       var st = $(this).scrollTop();
       if (st > lastScrollTop){
           $('.footer-animation').removeClass('scroll-down');
           $('.footer-animation').addClass('scroll-top');
       } else {
           $('.footer-animation').removeClass('scroll-top');
           $('.footer-animation').addClass('scroll-down');
       }
       lastScrollTop = st;
    });

	//Tooltip
    $('[data-toggle="tooltip"]').tooltip();
    $(document).on('click', '.sp-rating .star', function(event) {
        event.preventDefault();
        var data = {
            'action':'voting',
            'user_rating' : $(this).data('number'),
            'id' : $(this).closest('.post_rating').attr('id')
        };
        var request = {
                'option' : 'com_ajax',
                'plugin' : 'helix3',
                'data'   : data,
                'format' : 'json'
        };
        $.ajax({
            type   : 'POST',
            data   : request,
            beforeSend: function(){
                $('.post_rating .ajax-loader').show();
            },
            success: function (response) {
                var data = $.parseJSON(response.data);
                $('.post_rating .ajax-loader').hide();
                if (data.status == 'invalid') {
                    $('.post_rating .voting-result').text('You have already rated this entry!').fadeIn('fast');
                }else if(data.status == 'false'){
                    $('.post_rating .voting-result').text('Somethings wrong here, try again!').fadeIn('fast');
                }else if(data.status == 'true'){
                    var rate = data.action;
                    $('.voting-symbol').find('.star').each(function(i) {
                        if (i < rate) {
                           $( ".star" ).eq( -(i+1) ).addClass('active');
                        }
                    });
                    $('.post_rating .voting-result').text('Thank You!').fadeIn('fast');
                }
            },
            error: function(){
                $('.post_rating .ajax-loader').hide();
                $('.post_rating .voting-result').text('Failed to rate, try again!').fadeIn('fast');
            }
        });
    });
	
	// Filter	
	var handle = $( ".ui-slider-handle" );
	var max_price = $( "#max_price" ).attr("value");
	var price_from = $(".fprice_from").attr("value");
	$( ".range_slider" ).slider({
		range: true,
		min: 0,
		max: max_price,
		values: [ price_from, max_price],

		slide: function( event, ui ) {
		$( "#fprice_from" ).attr("placeholder", ui.values[ 0 ]).attr("value", ui.values[ 0 ]);
		$( "#fprice_to" ).attr("placeholder", ui.values[ 1 ]).attr("value", ui.values[ 1 ]);
		}
    });
	$( "#fprice_to" ).attr("value", (max_price/2));
		
	// this is the id of the form
	$('.range_slider a, .box_manufacrurer input').click( function() {
		var url = $('form').attr('action');
		$.ajax({
			type: "POST",
			url: url,
			data: $("#mat_js_filter").serialize(),
			
			beforeSend: function(){
				$('#products').fadeOut();
				$('.clear_filter i').addClass('mat-spin');
			},
			
			success: function(data)
			{
				var our_prods = $(data).find('#products');
				$('#products').html(our_prods);
				$('#products').fadeIn();
				//console.log($(data).find('#products')); // show response from the php script.
			}
		});
	});
	
	// Reset Button Press
	$('.clear_filter a').click( function() {
		var url = $('form').attr('action');
		var this_form = $('.jshop_filters form[name=jshop_filters]');
		if(this_form.length){
			this_form.find('input[type=text]').val('');
			this_form.find('input[type=checkbox]').prop('checked',false);
		}
		$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#mat_js_filter").serialize(), 
			   beforeSend: function(){
				   $('#products').fadeOut();
				   $('.clear_filter i').addClass('mat-spin');
			   },
			   success: function(data)
			   {
				   var our_prods = $(data).find('#products');
				   $('#products').html(our_prods);
				   $('#products').fadeIn();
			   }
		});
	});
	
	$(document).ready(function() {
			$('#list_product_image_middle').magnificPopup({
			  delegate: 'a',
			  type: 'image'
			});
		});
});