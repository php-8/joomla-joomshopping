/**       		  	        		          	
* @package Joomla
* @subpackage JoomShopping
* @author joom-shopping.com
* @website https://joom-shopping.com/
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
**/

var quickOrder = quickOrder || {};
quickOrder.openForm = function(elem,cid,pid) {
	var $ = jQuery;
	var form = $(elem).closest('form');
	quickOrder.productInputs = form.length ? form.serializeArray() : [];
	$('#quickorderpopup input[name=category_id]').val(cid);
	$('#quickorderpopup input[name=product_id]').val(pid);
	$('#quickorderpopup').css('display','table');
}
quickOrder.closeForm = function(elem) {
	jQuery('#quickorderpopup').css('display','');
}
quickOrder.submitForm = function() {
	var $ = jQuery;
	var error = false;
	$('#quickorderpopup :input[data-required]').removeClass('fielderror').each(function(){
		var $el = $(this);
		if ($el.val().trim() === '') {
			$el.addClass('fielderror');
			error = true;
		}
	});
	if (error) {
		return false;
	}
	var inputs = [];
	$('#quickorderpopup :input[name]').each(function(){
		inputs.push(this.name);
	});
	$.each(quickOrder.productInputs, function(){
		if(inputs.indexOf(this.name)<0) {
			if (this.name == 'quantity' && this.value<=0) {
				this.value = 1;
			}
			$('#quickorderpopup form').append('<input type="hidden" name="'+this.name+'" value="'+this.value+'" />');
		}
	});
	$('#quickorderpopup form input[name="to"]').val('quickorder');
	return true;
}       	         			        	          	