<?php
	defined('_JEXEC') or die('Restricted access');
	$db = JFactory::getDbo();
	
	$element = "rus_invoices_for_payment";
	
	//delete plygin
	$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'addon_".$element."' AND `folder` = 'jshoppingorder' AND `type` = 'plugin'");
	$db->query();
	
	// delete folder
	jimport('joomla.filesystem.folder');
	foreach(array(
		'components/com_jshopping/addons/'.$element.'/',
		'plugins/jshoppingorder/addon_'.$element.'/',
		'components/com_jshopping/lang/addon_'.$element.'/'	
	) as $folder){JFolder::delete(JPATH_ROOT.'/'.$folder);}
?>
