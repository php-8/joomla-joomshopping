jQuery(function($) {
	$('.box_manufacrurer input').iCheck({
		checkboxClass: 'icheckbox_polaris',
		//increaseArea: '50%' // optional
	});

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
			   $('#comjshop_list_product').fadeIn();
			   $(".mat_spinner").removeClass("mat_spinner");
			}
		});
	});
});