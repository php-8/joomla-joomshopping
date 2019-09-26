<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
* @license agreement https://joom-shopping.com/polzovatelskoe-soglashenie
**/

defined('_JEXEC') or die;
?>
<script type="text/javascript">
//<![CDATA[
var register_field_require = [];
<?php
foreach($this->config_fields as $key=>$val){
    if ($val['require']){
        echo "register_field_require['".$key."']=1;";
    }
}
?>
var oneStepCheckout = oneStepCheckout || {};
oneStepCheckout.showOverlay = function() {
	<?php if ($this->addonParams->overlay) { ?>
	var $=jQuery;
	if ($("#onestepcheckout-overlay").size()>0) {
		return;
	}
	var div=$("<div>").attr("id","onestepcheckout-overlay");
	div.width($(window).width()+"px");
	div.height($(window).height()+"px");
	$(document.body).append(div)
	<?php } ?>
}
oneStepCheckout.hideOverlay = function() {
	<?php if ($this->addonParams->overlay) { ?>
	jQuery("#onestepcheckout-overlay").remove();
	<?php } ?>
}
oneStepCheckout.getJsonMessage = function(json) {
	var $ = jQuery;
	var messages = new Array();
	$.each(json, function(key, value){
		if(typeof value.message != 'undefined' && value.message.length > 0) {
			messages.push(value.message);
		}
	});
	if(messages.length > 0) {
		return messages.join(String.fromCharCode(10) + String.fromCharCode(13));
	}
	return;
}
oneStepCheckout.updateForm = function(step) {
	var $=jQuery;
	step = step || 2;
	if (oneStepCheckout.xhrUpdate) {
		oneStepCheckout.xhrUpdate.abort();
	}
	oneStepCheckout.showOverlay();
	oneStepCheckout.xhrUpdate = $.ajax({
		type: 'POST',
		dataType: 'json',
		url: '<?php echo JURI::root() ?>index.php?option=com_jshopping&controller=checkout&task=step2&osctask=step'+step+'update', 
		data: $('#oneStepCheckoutForm').serialize(),
		cache: false,
		success: function(json){
			for (var key in json){
				$('#'+key).html(json[key]);
			}
			oneStepCheckout.cloneCart();
		},
		error: function(jqXHR, textStatus, errorThrown){
			if (textStatus != 'abort') {
				window.location.reload();
			}
		},
		complete: function(jqXHR, textStatus){
			oneStepCheckout.hideOverlay();
			oneStepCheckout.xhrUpdate = false;
		}
	});
}
oneStepCheckout._refreshForm = function(el) {
	var $=jQuery;
	oneStepCheckout.showOverlay();
	oneStepCheckout.xhrRefresh = $.ajax({
		type: 'POST',
		dataType: 'json',
		url: '<?php echo JURI::root() ?>index.php?option=com_jshopping&controller=cart&task=refresh&ajax=1', 
		data: $('#step5').find('[name^=quantity]').serialize(),
		cache: false,
		success: function(json){
			if (json['count_product']) {
				oneStepCheckout.updateForm(2);
			} else if (typeof json[0] != 'undefined' && typeof json[0].message != 'undefined') {
				if (el) {
					el.value = $(el).data('quantity');
					if ($(el).data('clone') == 1) {
						var el_id = el.id;
						el_id = el_id.replace('topquantity','quantity');
						el_id.replace('topquantity','quantity');
						$('#'+el_id).val(el.value);
					}
				}
				oneStepCheckout.hideOverlay();
				alert(oneStepCheckout.getJsonMessage(json));
			} else {
				window.location.reload();
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if (textStatus != 'abort') {
				window.location.reload();
			}
		}
	});
}
oneStepCheckout.refreshForm = function(el, timeout) {
	var $=jQuery;
	clearTimeout(oneStepCheckout.timeout);
	if (oneStepCheckout.xhrUpdate) {
		return;
	} else if (oneStepCheckout.xhrRefresh) {
		oneStepCheckout.xhrRefresh.abort();
	}
	if (el) {
		<?php if ($this->config->use_decimal_qty) { ?>
		var el_value = parseFloat(el.value);
		<?php } else { ?>
		var el_value = parseInt(el.value);
		<?php } ?>
		if (el_value <= 0 || isNaN(el_value)) {
			if (timeout == 0) {
				el.value = $(el).data('quantity');
			}
			return;
		} else if (el_value != el.value) {
			el.value = el_value;
		}
		if ($(el).data('clone') == 1) {
			var el_id = el.id;
			el_id = el_id.replace('topquantity','quantity');
			console.log(el_id);
			$('#'+el_id).val(el.value);
		}
	}
	if (parseInt(timeout) > 0) {
		oneStepCheckout.timeout = setTimeout(function() {
			oneStepCheckout._refreshForm(el);
		}, timeout);
	} else {
		oneStepCheckout._refreshForm(el);
	}
}		
oneStepCheckout.rabbatForm = function() {
	var $=jQuery;
	if (oneStepCheckout.xhrUpdate) {
		return;
	} else if (oneStepCheckout.xhrRabbat) {
		oneStepCheckout.xhrRabbat.abort();
	}
	oneStepCheckout.showOverlay();
	oneStepCheckout.xhrRabbat = $.ajax({
		type: 'POST',
		dataType: 'json',
		url: '<?php echo JURI::root() ?>index.php?option=com_jshopping&controller=cart&task=discountsave&ajax=1', 
		data: $('#step5 input[name=rabatt]').serialize(),
		cache: false,
		success: function(json){
			if (typeof json[0] != 'undefined' && typeof json[0].message != 'undefined') {
				oneStepCheckout.hideOverlay();
				alert(oneStepCheckout.getJsonMessage(json));
			} else {
				oneStepCheckout.updateForm(2);
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if (textStatus != 'abort') {
				window.location.reload();
			}
		}
	});
}
oneStepCheckout.toggleDeliveryAdress = function() {
	var $ = jQuery;
	if ($('#delivery_adress_2').val() == 1) {
		$('#div_delivery').fadeIn();
	} else {
		$('#div_delivery').fadeOut();
	}
}
oneStepCheckout.toggleRegistration = function() {
	var $ = jQuery;
	if ($('#register').is(':checked')){
		<?php if ($this->config->shop_user_guest == 4 && $this->config_fields['email']['display'] && !$this->config_fields['email']['require']) { ?>
		$('#requiredemail').removeClass('uk-hidden');
		register_field_require['email']=1;
		<?php } ?>
		$('#div_register').fadeIn();
	} else {
		<?php if ($this->config->shop_user_guest == 4 && $this->config_fields['email']['display'] && !$this->config_fields['email']['require']) { ?>
		$('#requiredemail').addClass('uk-hidden');
		register_field_require['email']=0;
		<?php } ?>
		$('#div_register').fadeOut();
	}
}
oneStepCheckout.showPaymentForm = function(paymentMethod, updateForm) {
	<?php if ($this->addonParams->payment_params) { ?>
	showPaymentForm(paymentMethod);
	<?php } else { ?>
	activePaymentMethod=paymentMethod;
	<?php } ?>
	if (updateForm) {
		oneStepCheckout.updateForm(<?php echo $this->config->step_4_3 ? 4 : 3 ?>);	
	}
}
oneStepCheckout.showShippingForm = function(shippingMethod, updateForm) {
	<?php if ($this->addonParams->shipping_params) { ?>
	jQuery("*[id^='tr_shipping_']").hide();
    jQuery('#tr_shipping_'+shippingMethod).show();
	<?php } ?>
	if (updateForm) {
		oneStepCheckout.updateForm(<?php echo $this->config->step_4_3 ? 3 : 4 ?>);	
	}
}
oneStepCheckout.validateForm = function(form) {
	var $ = jQuery;
	form = form || document.oneStepCheckoutForm;

	if (oneStepCheckout.isCheckOut) {
		return false;
	} else {
		var errorMinMax = $.trim($('#error_min_max_price_order').html());
		if (errorMinMax != '') {
			alert(errorMinMax);
			return false;
		}
		oneStepCheckout.isCheckOut = true;
		<?php if (!$this->config->without_payment) { ?>
		oneStepCheckout.validatePaymentForm = false;
		<?php } ?>
	}

	oneStepCheckout.showOverlay();

	if (oneStepCheckout.isCheckOut) {
		oneStepCheckout.isCheckOut = validateCheckoutAdressForm('<?php echo $this->live_path ?>', 'oneStepCheckoutForm');
	}

	<?php if ($this->allowUserRegistration) { ?>
	if (oneStepCheckout.isCheckOut) {
		<?php if ($this->config->shop_user_guest == 4) { ?>
		if ($('#register').is(':checked')){
		<?php } ?>
			var arrayId = new Array();
			var arrayType = new Array();
			var arrayParams = new Array();
			var arrayErrorMessages = new Array();
			var i = 0;
			<?php if ($this->register_fields['u_name']['require']) { ?>
			arrayId[i] = 'u_name';
			arrayType[i] = 'nem';
			arrayParams[i] = '';
			arrayErrorMessages[i] = '';
			i++;    
			<?php } ?>
			<?php if ($this->register_fields['password']['require'] && $this->register_fields['password_2']['require']) { ?>
			arrayId[i] = 'password';
			arrayType[i] = 'eqne';
			arrayParams[i] = 'password_2';
			arrayErrorMessages[i] = '';
			i++;
			<?php } else if ($this->register_fields['password']['require']) { ?>
			arrayId[i] = 'password';
			arrayType[i] = 'nem';
			arrayParams[i] = '';
			arrayErrorMessages[i] = '';
			i++;
			<?php } ?>
			var regForm = new validateForm('oneStepCheckoutForm', arrayId, arrayType, arrayParams, arrayErrorMessages, 2);
			oneStepCheckout.isCheckOut = regForm.validate();
		<?php if ($this->config->shop_user_guest == 4) { ?>
		}
		<?php } ?>
	}
	<?php } ?>

	<?php if (!$this->config->without_payment) { ?>
	if (oneStepCheckout.isCheckOut) {
		<?php if ($this->config->hide_payment_step) { ?>
		oneStepCheckout.isCheckOut = $('form[name="oneStepCheckoutForm"] input[name="payment_method"]').is(':checked');
		<?php } else { ?>
		oneStepCheckout.paymentForm = document.getElementById('payment_form');
		oneStepCheckout.paymentForm.submit = oneStepCheckout.paymentForm.onsubmit = function(){oneStepCheckout.validatePaymentForm=true};
		checkPaymentForm();
		oneStepCheckout.isCheckOut = oneStepCheckout.validatePaymentForm;
		<?php } ?>
	}
	<?php } ?>

	<?php if (!$this->config->without_shipping) { ?>
	if (oneStepCheckout.isCheckOut) {
		oneStepCheckout.isCheckOut = validateShippingMethods();
	}
	<?php } ?>
	
	<?php if ($this->config->display_agb) { ?>
	if (oneStepCheckout.isCheckOut) {
		oneStepCheckout.isCheckOut = checkAGB();
	}
	<?php } ?>
	
	if (!oneStepCheckout.isCheckOut) {
		oneStepCheckout.hideOverlay();
	}

	return oneStepCheckout.isCheckOut;
}
oneStepCheckout.cloneCart = function() {
	var $ = jQuery;
	var cart = $('#step5 table.minicart').clone();
	cart.find('input[id^="quantity"]').each(function (i) {
		$(this).attr('id','top'+this.id).attr('name','top'+this.name).attr('data-clone','1');
	});
	$('#step5top').html(cart);
}
jQuery(function($){
	oneStepCheckout.cloneCart();
	<?php if ($this->addonParams->refresh) { ?>
	oneStepCheckout.updateForm(1);
	<?php } ?>
});
//]]>
</script>