<?php
/**
* @version      1.0.1 2.6.2017
* @author       MatBlunic
* @package      JoomShopping
* @copyright    Copyright (C) 2017 matblunic.com. All rights reserved.
*/
defined('_JEXEC') or die('Restricted access');
error_reporting(error_reporting() & ~E_NOTICE); 
if (!file_exists(JPATH_SITE.'/components/com_jshopping/jshopping.php')){
    JError::raiseError(500,"Please install component \"joomshopping\"");
}
jimport('joomla.application.component.model');
require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php'); 
require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');        
JSFactory::loadCssFiles();
JSFactory::loadLanguageFile();
$JoomConf = JSFactory::getConfig();
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jshopping/models');
$cart_ins = JModelLegacy::getInstance('cart', 'jshop');
$cart_ins->load("cart");
$position = $params->get('position',1);
$display_cont = $params->get('display_cont',1);
$cart_style = $params->get('cart_style',1);
$cart_style_box = $params->get('cart_style_box',1);
$currency = $params->get('currency');
$cart_button = $params->get('cart_button',1);
$load_jquery = $params->get('load_jquery',1);
$pop_up = $params->get('pop_up',1);

require(JModuleHelper::getLayoutPath('mod_jshopping_cart_ajax_pro')); 
?>