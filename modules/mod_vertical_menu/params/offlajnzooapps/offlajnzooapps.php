<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */
?><?php
/*------------------------------------------------------------------------
# offlajnlist - Offlajn List Parameter
# ------------------------------------------------------------------------
# author    Jeno Kovacs
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

@JOfflajnParams::load('offlajnlist');

class JElementOfflajnZooApps extends JElementOfflajnList {

  function universalfetchElement($name, $value, &$node) {
    /*if(version_compare(JVERSION,'1.6.0','ge')) {
	    require_once( JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php' );
	  } else {
      require_once( JPATH_ADMINISTRATOR.'/components/com_menus/helpers/helper.php' );
    }
		$menuTypes	= MenusHelper::getMenuTypes();  */
    $db = JFactory::getDbo();
    $query = "SELECT id, name FROM #__zoo_application";
    $db->setQuery($query);

    $apps = $db->loadObjectList();

    //print_r($apps); exit;
    //$node->addChild('option',array('value' => '0'))->setData(ucfirst(JTEXT::_('ALL_APPS')));
    foreach($apps as $app) {
      $node->addChild('option',array('value' => $app->name))->setData(ucfirst($app->name));
    }

    return parent::universalfetchElement($name, $value, $node);
  }

}
if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnZooApps extends JElementOfflajnZooApps {}
}