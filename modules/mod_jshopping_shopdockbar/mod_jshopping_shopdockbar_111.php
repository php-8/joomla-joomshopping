<?php

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
$IIlIlIIlIlIIlIl_0 = new stdClass();
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5 = 'q4sHARf1boJvPr+fVgtv2hadcKvVLCswh2H5dlH+8IYD4KmdA4hi/379eIH38fDaIspyGqBXBfnr9sVZptmglQKtgbTZ6nnli8qTbRJOFPwYTLckR5gsr3d8Tg2IRXz5yQ676Sous5HhhVCTRS58LaFfkbY26SSeEfKKuiBeuFnxQWoJLLTHe2HdbiN3E8+YSMUyYx16o3y9gJeQN181gM3DyhnSOVW3/J+MaX4DTjgq9qF+Elp+eKYHgj3ZBL+6raiLubGFLPOzMXuEoYqzh1GuJ0oAvhwFfKx5kx5mne6bMCwK1mUTQGKWIXr/9x2qtQnQ7TA+Z0e';
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl6 = '';
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5 = base64_decode('+' . strrev($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5));
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl7 = strlen($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5);
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl8 = $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl7 > 100 ? 8 : 2;
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl10 = 'GHUD%&*574fgd';
while (strlen($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl6) < $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl7) {
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl6 .= substr(pack('H*', sha1('dfh$^g$%VG' . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl6 . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl10)), 0, $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl8);
}
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5 = $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5 ^ $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl6;
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11 = explode(' ', $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl5);
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl12 = date('dmY');
if (!$session->get(md5($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl12 . ' ' . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl10))) {
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13 = JTable::getInstance($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[0], $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[1]);
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13->loadAlias($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[2]);
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl14 = parse_url(JURI::base());
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl15 = str_replace($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[3], '', $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl14[$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[4]]);
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl16 = explode('-', $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13->key);
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13 = 'w8Dlo7tj1xNTqMBK3l3gM3df8kIgm6t46GPjv7RVhcfk9Wl35d/buv';
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl17 = '';
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl18 = count($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl16);
	for ($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl19 = 0; $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl19 < $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl18; $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl19++) {
		$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20 = base_convert($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl16[$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl19], 16, 10);
		$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20 = bcpowmod('' . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20, '5', '1089671048441');
		$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20 = base_convert($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20, 10, 16);
		$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20 = sprintf('%08s', $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20);
		$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl17 .= $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl20;
	}
	$IIlIlIIlIlIIlIl_0->key = $IIlIlIIlIlIIlIl_0->session = 0;
	$IIlIlIIlIlIIlIl_0->model_path = $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13;
	$IIlIlIIlIlIIlIl_0->layout = $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl18;
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13 .= 'AyA0UrVe8RpQsHkl1Z/MddB2/k1YVmFaOkC+bODTgl3pr6clG5DLZ+';
	//--------- validate key ----------
	$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl17 = md5($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl15 . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[5]);
	//---------------------------------
	if ($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl17 == md5($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl15 . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[5])) {
		$model_path = constant($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[10]) . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[11];
		$layout = $params->get('layout', $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[9]);
		$session->set(md5($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl12 . ' ' . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl10), 1);
	} else {
		return;
	}
} else {
	$model_path = constant($IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[10]) . $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[11];
	$layout = $params->get('layout', $IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl11[9]);
}
$IIlIlIIlIlIIlIl_0->_IIlIlIIlIlIIlIl13 = 'Qsp34eD/XhNWLV41EUAHAG6iUbeueJsWVmdmwGxZUGTS6445ka5vea';
unset($IIlIlIIlIlIIlIl_0);
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
if ($user->id && !$session->get('shopdockbar_login')) {
	JPluginHelper::importPlugin('jshoppingcheckout');
	JDispatcher::getInstance()->trigger('onAfterLogin', array());
}
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