<?php
	defined('_JEXEC') or die('Restricted access');
	$db = JFactory::getDbo();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'inform_availability_product' AND `folder` = 'jshoppingproducts' AND `type` = 'plugin'");
	$db->query();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'inform_availability_product' AND `folder` = 'jshoppingadmin' AND `type` = 'plugin'");
	$db->query();
	
	$db->setQuery("DROP TABLE `#__jshopping_requests_availability_product`");
	$db->query();
	
	jimport('joomla.filesystem.folder');
	foreach(array(
		'components/com_jshopping/addons/inform_availability/',
		'components/com_jshopping/lang/addon_inform_availability_product/',
		'plugins/jshoppingadmin/inform_availability_product/',
		'plugins/jshoppingproducts/inform_availability_product/',
		'plugins/jshoppingmenu/requests_availability/',
		'administrator/components/com_jshopping/views/requests_availability'
	) as $folder){JFolder::delete(JPATH_ROOT.'/'.$folder);}
	   
	jimport('joomla.filesystem.file');
	foreach(array(
		'administrator/components/com_jshopping/language/en-GB/en-GB.addon_inform_availability_product.ini',
		'administrator/components/com_jshopping/language/ru-RU/ru-RU.addon_inform_availability_product.ini',
		'administrator/components/com_jshopping/controllers/requests_availability.php',
		'administrator/components/com_jshopping/models/requests_availability.php',
		'components/com_jshopping/controllers/inform_availability_product.php',
		'components/com_jshopping/css/inform_availability_product.css'
	) as $file){JFile::delete(JPATH_ROOT.'/'.$file);}