<?php

/**
 * @version      4.0.1 20.12.2012
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
error_reporting(error_reporting() & ~E_NOTICE);

if (!file_exists(JPATH_SITE . '/components/com_jshopping/jshopping.php')) {
    JError::raiseError(500, "Please install component \"joomshopping\"");
}

jimport('joomla.application.component.model');

require_once (JPATH_SITE . '/components/com_jshopping/lib/factory.php');
require_once (JPATH_SITE . '/components/com_jshopping/lib/functions.php');
JSFactory::loadCssFiles();
JSFactory::loadLanguageFile();
$jshopConfig = JSFactory::getConfig();

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jshopping/models');

$cart = JModelLegacy::getInstance('cart', 'jshop');
$cart->load("cart");

$cartpreview = JSFactory::getModel('cartPreview', 'jshop');
$cartpreview->setCart($cart);
$cartpreview->setCheckoutStep(0);
$cart->tax_list = $cartpreview->getTaxExt();

$show_count = $params->get('show_count', 1);
$show_tax = $params->get('show_tax', 1);
$show_basic_price = $params->get('show_basic_price', 1);
$show_total_tax = $params->get('show_total_tax', 1);

require(JModuleHelper::getLayoutPath('mod_jshopping_cart_ext'));
?>