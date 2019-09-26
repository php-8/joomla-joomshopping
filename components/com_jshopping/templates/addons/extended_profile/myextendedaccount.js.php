<?php

defined('_JEXEC') or die;
?>
<script type="text/javascript">
(function($) {
	$(function() {
		$('ul.eac_tabs').delegate('li:not(.current)', 'click', function() {
			$(this).addClass('current').siblings().removeClass('current')
			.parents('div.tabs_section').find('div.eac_box').hide().eq($(this).index()).show();
			window.location.hash = $(this).attr('id').replace('_tab','');
		});
		var windows_hash = window.location.hash.substr(1);
		if (windows_hash != '') {
			var tab_id = windows_hash+'_tab';
			var div_id = windows_hash+'_data';
			$('div.tabs_section').find('div.eac_box').hide();
			$('li.current').removeClass('current');
			$('#'+div_id).show();
			$('#'+tab_id).addClass('current');
		}
	});
})(jQuery)
function viewStatusTooltip(elem, mode) {
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
</script>