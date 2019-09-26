<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgJshoppingMenuAddon_income extends JPlugin 
{
	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
		JSFactory::loadExtAdminLanguageFile('addon_income');
	}
	
	function onBeforeAdminOptionPanelMenuDisplay(&$menu) {
		$menu['addon_income'] = array(_INCOME, 'index.php?option=com_jshopping&controller=addon_income', 'jshop_order_status_b.png', 1);
	}
	
	function onBeforeAdminOptionPanelIcoDisplay(&$menu) {
		$menu['addon_income'] = array(_INCOME, 'index.php?option=com_jshopping&controller=addon_income', 'jshop_order_status_b.png', 1);
	}
}
?>