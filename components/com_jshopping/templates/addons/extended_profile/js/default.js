;var extendedProfile = extendedProfile || {};
extendedProfile.init = function() {
	var windows_hash = window.location.hash.substr(1);
	if (windows_hash != '') {
		this.changeTab(document.getElementById(windows_hash+'_tab'));
	}
	this.load = true;
}
extendedProfile.changeTab = function(el) {
	var $ = jQuery;
	var tab = $(el);
	tab.addClass('current').siblings().removeClass('current')
		.parents('div.nvg-eac-tabs-section').find('div.eac_box').removeClass('visible').eq(tab.index()).addClass('visible');
	if (this.load) {
		window.location.hash = tab.attr('id').replace('_tab','');
	}
}
extendedProfile.viewStatusTooltip = function(elem, mode) {
	if (mode=='show') {
		jQuery("#eac_tooltip").remove();
		jQuery("body").append("<div id='eac_tooltip'>"+jQuery(elem).prev().html()+"</div> ");
		jQuery('#eac_tooltip')
			.css('top',(jQuery(elem).offset().top + 30) + 'px')
			.css('left',(jQuery(elem).offset().left) + 'px')
			.fadeIn("fast");
    } else {
		jQuery('#eac_tooltip').remove();
    }
}
extendedProfile.toggleDeliveryAdress = function() {
	var $ = jQuery;
	// alert($('#delivery_adress_1').attr('checked'));
	if ($('#delivery_adress_1').attr('checked')) {
		$('#d_adress_fields').fadeOut();
	} else {
		$('#d_adress_fields').fadeIn();
	}
}
extendedProfile.resetAdressFields = function() {
	var $ = jQuery;
	$('#eacAdressForm').removeClass('active').trigger('reset');
	this.toggleDeliveryAdress();
	$('#eacAdressForm .fielderror').removeClass('fielderror');
}
extendedProfile.saveAdressFields = function() {
	var $ = jQuery;
	$('#eacAdressForm').submit();
}
jQuery(function($) {
	extendedProfile.init();
	$('#eacAdressForm :input').click(function(){
		$('#eacAdressForm').addClass('active');
	});
});     	     	     		    	          	