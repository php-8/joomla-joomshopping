<?php

defined('_JEXEC') or die;

if (!file_exists(JPATH_SITE.'/components/com_jshopping/jshopping.php')){
    return;
}

jimport('joomla.application.component.model');

require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
require_once JPATH_SITE.'/components/com_jshopping/lib/functions.php';

JSFactory::loadCssFiles();

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jshopping/models');

$cart = JModelLegacy::getInstance('cart', 'jshop');
$cart->load('cart');

$layout = $params->get('layout', 'default');
$template = substr($layout, 2);

$document = JFactory::getDocument();
if (file_exists(__DIR__.'/js/'.$template.'.js')) {
	$document->addScript(JURI::base(true).'/modules/'.$module->module.'/js/'.$template.'.js');
}
if (file_exists(__DIR__.'/js/'.$template.'.custom.js')) {
	$document->addScript(JURI::base(true).'/modules/'.$module->module.'/js/'.$template.'.custom.js');
}
if (file_exists(__DIR__.'/css/'.$template.'.css')) {
	$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.$template.'.css');
}
if (file_exists(__DIR__.'/css/'.$template.'.custom.css')) {
	$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.$template.'.custom.css');
}

require JModuleHelper::getLayoutPath($module->module, $layout);
?>