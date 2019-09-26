<?php
	defined('_JEXEC') or die('Restricted access');
	
    $db = JFactory::getDbo();
	
    $db->setQuery("DELETE FROM `#__extensions` WHERE element = 'addon_income' AND folder = 'jshoppingmenu' AND `type` = 'plugin'");
	$db->query();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE element = 'addon_income' AND folder = 'jshoppingorder' AND `type` = 'plugin'");
	$db->query();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE element = 'addon_income' AND folder = 'jshoppingcheckout' AND `type` = 'plugin'");
	$db->query();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE element = 'addon_income' AND folder = 'jshoppingadmin' AND `type` = 'plugin'");
	$db->query();
	
//	$db->setQuery('ALTER TABLE `#__jshopping_orders` DROP `buy_price_subtotal`');
//	$db->query();
//    
//    $db->setQuery('ALTER TABLE `#__jshopping_order_item` DROP `product_buy_price`');
//	$db->query();
	
	jimport('joomla.filesystem.folder');
	foreach(array(
		'plugins/jshoppingmenu/addon_income/',
		'plugins/jshoppingorder/addon_income/',
		'plugins/jshoppingcheckout/addon_income/',
		'plugins/jshoppingadmin/addon_income/',
		'administrator/components/com_jshopping/views/addon_income/',
		'administrator/components/com_jshopping/lang/addon_income/',
		'components/com_jshopping/addons/addon_income/'
	) as $folder){JFolder::delete(JPATH_ROOT.'/'.$folder);}
	
	jimport('joomla.filesystem.file');
	foreach(array(
		'administrator/components/com_jshopping/controllers/addon_income.php',
		'administrator/components/com_jshopping/models/addon_income.php'
	) as $file){JFile::delete(JPATH_ROOT.'/'.$file);}