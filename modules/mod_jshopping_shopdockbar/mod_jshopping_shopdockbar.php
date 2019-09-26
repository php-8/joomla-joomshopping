<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Dmitry Stashenko
* @website http://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement http://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;

if (!JComponentHelper::isEnabled('com_jshopping')){
    return;
}

require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
require_once JPATH_SITE.'/components/com_jshopping/lib/functions.php';
JSFactory::loadJsFiles();
JSFactory::loadCssFiles();
JSFactory::loadLanguageFile();
$model_path = JPATH_ADMINISTRATOR.'/components/com_jshopping/models';
$session = JFactory::getSession();
$mod_params = $params->toObject();
$layout = $mod_params->layout;
$user = JFactory::getUser();
$jshopConfig = JSFactory::getConfig();

$document = JFactory::getDocument();
$document->addScriptDeclaration('
	var dockbar = dockbar || {};
	dockbar.cart_effect = "'.$mod_params->cart_effect.'";
	dockbar.wishlist_effect = "'.$mod_params->wishlist_effect.'";
	dockbar.compare_effect = "'.$mod_params->compare_effect.'";
	dockbar.product_add_text = "'.JText::_('MOD_JSHOPPING_SHOPDOCKBAR_PRODUCT_ADD').'";
');
$document->addScript(JURI::base().'modules/'.$module->module.'/js/'.substr($layout, $mod_params->layoutdigit).'.js');
$document->addStyleSheet(JURI::base().'modules/'.$module->module.'/css/'.substr($layout, $mod_params->layoutdigit).'.css');
JModelLegacy::addIncludePath($model_path);
$cart = JModelLegacy::getInstance('cart', 'jshop');
$cart->load('cart');
$cart->addLinkToProducts(1);

if ($jshopConfig->enable_wishlist) {
	$wishlist = JModelLegacy::getInstance('cart', 'jshop');
	$wishlist->load('wishlist');
	$wishlist->addLinkToProducts(1,'wishlist');
}

if (isset($jshopConfig->noimage)) {
	$noimage = $jshopConfig->noimage;
} else {
	$noimage = 'noimage.gif';
}

if ($user->id) {
	$adv_user = JTable::getInstance('userShop', 'jshop');
	$adv_user->load($user->id);
} else {
	$document->addScriptDeclaration('jQuery(function($){$("#callback_user_phone,#feedback_user_phone").removeAttr("required");$("#callback_user_token,#feedback_user_token").val("")})');
	$document->addStyleDeclaration('#callback_user_phone,#feedback_user_phone{display:none!important}');
	$adv_user = JSFactory::getUserShopGuest();
}
$uri = JFactory::getURI();
$return = base64_encode($uri->toString(array('path', 'query', 'fragment')));

$shopdockbar_history = $session->get('shopdockbar_history', array());
$shopdockbar_compare = $session->get('shopdockbar_compare', array());

require JModuleHelper::getLayoutPath($module->module, $layout);
?>