<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgJshoppingMenuRequests_availability extends JPlugin {
	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
		//JSFactory::loadExtAdminLanguageFile('addon_inform_availability_product');
	}
	
	function onBeforeAdminOptionPanelMenuDisplay(&$menu) {
		$menu['requests_availability'] = array(_JSHOP_REQUESTS_AVAILABILITY, 'index.php?option=com_jshopping&controller=requests_availability', 'jshop_country_list_b.png', 1);
	}
	
	function onBeforeAdminOptionPanelIcoDisplay(&$menu) {
		$menu['requests_availability'] = array(_JSHOP_REQUESTS_AVAILABILITY, 'index.php?option=com_jshopping&controller=requests_availability', 'jshop_country_list_b.png', 1);
	}
}
?>