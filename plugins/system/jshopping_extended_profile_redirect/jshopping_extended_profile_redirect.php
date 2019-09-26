<?php

defined('_JEXEC') or die;

jimport( 'joomla.plugin.plugin' );

class plgSystemJshopping_extended_profile_redirect extends JPlugin {

	function onAfterDispatch() {
		$app = JFactory::getApplication();
		if ($app->isSite() && $app->input->getCmd('option') == 'com_jshopping' && $app->input->getCmd('controller') == 'user' && ($app->input->getCmd('task') == 'orders' || $app->input->getCmd('view') == 'orders')) {
			require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
			require_once JPATH_SITE.'/components/com_jshopping/lib/functions.php';
			$app->redirect(SEFLink('index.php?option=com_jshopping&controller=user', 0, 1).'#exac_orders');
		}
	}

}