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

@JOfflajnParams::load('offlajnmultiselectlist');

global $mosConfig_absolute_path;
if( !isset( $mosConfig_absolute_path ) ) {
 $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
}

class JElementOfflajnHikashopBrands extends JElementOfflajnMultiSelectList {
  function getItems(&$node) {
   
		$db =JFactory::getDBO();

		$query = 'SELECT m.category_id AS id, m.category_name AS name, m.category_name as title, m.category_parent_id AS parent,  m.category_parent_id as parent_id
              FROM #__hikashop_category m
              WHERE m.category_type="manufacturer" AND m.category_published = 1
              ORDER BY m.category_id'; 
 
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$children = array();
		if ( $mitems )
		{
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
    $k = array_keys($children);
		$list = JHTML::_('menu.treerecurse', $k[0], '', array(), $children, 9999, 0, 0 );
		
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			@$groupedList["hikashop"][] =$list[$k];
		}
  return $groupedList;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnHikashopBrands extends JElementOfflajnHikashopBrands {}
}
