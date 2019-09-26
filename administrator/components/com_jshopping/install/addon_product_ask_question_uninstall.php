<?php

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$db->setQuery("DELETE FROM `#__extensions` WHERE element='product_ask_question' AND folder='jshoppingproducts'");
$db->query();

$db->setQuery("SELECT template FROM `#__jshopping_config` WHERE 1");
$template = $db->loadResult();
if ($template && $template!='default') {
	JFile::delete(JPATH_COMPONENT_SITE.'/templates/'.$template.'/product/ask_question.php');
}
JFile::delete(JPATH_COMPONENT_SITE.'/templates/default/product/ask_question.php');
JFile::delete(JPATH_COMPONENT_SITE.'/controllers/product_ask_question.php');
JFolder::delete(JPATH_COMPONENT_SITE.'/lang/product_ask_question');
JFolder::delete(JPATH_ROOT.'/plugins/jshoppingproducts/product_ask_question');
JFile::delete(JPATH_COMPONENT_ADMINISTRATOR.'/install/addon_product_ask_question_uninstall.php');
?>