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

class JElementOfflajnJshoppingCategories extends JElementOfflajnMultiSelectList {

  function getItems(&$node) {

    $db = &JFactory::getDBO();
	$lang = JFactory::getLanguage()->getTag();
		$query = "SELECT m.category_id AS id, `name_$lang` AS title, `name_$lang` AS name, m.category_parent_id AS parent_id, m.category_parent_id as parent
              FROM #__jshopping_categories AS m
              LEFT JOIN #__jshopping_products_to_categories AS f
              ON m.category_id = f.category_id
              WHERE m.category_publish = 1
              ORDER BY ordering";
		$db->setQuery( $query );
		$menuItems = $db->loadObjectList();
		$children = array();
		if ( $menuItems )
		{
			foreach ($menuItems as $v){
			  $pt 	= $v->parent_id;
				
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			@$groupedList["jshopping"][] = &$list[$k];
		}
  return $groupedList;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnJshoppingCategories extends JElementOfflajnJshoppingCategories {}
}
