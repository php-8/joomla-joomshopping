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

class JElementOfflajnZooCategories extends JElementOfflajnMultiSelectList {

function getItems(&$node) {
		$db = &JFactory::getDBO();
    $query = 'SELECT c.*, c.name AS title, c.parent AS parent, c.parent AS parent_id, a.name AS appname, a.id as appid FROM #__zoo_category AS c
                                                                                                                        LEFT JOIN #__zoo_application AS a ON a.id = c.application_id
                                                                                                                         WHERE published = 1 ORDER BY c.parent, c.ordering';
		$db->setQuery( $query );
		$menuItems = $db->loadObjectList();
		$children = array();
    if ( $menuItems ){
			foreach ($menuItems as $v){      
			  $pt 	= $v->parent_id;
        if(!$pt) $v->title .= " (".$v->appname.")"; 	
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}		
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
    
		// assemble into menutype groups
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			$groupedList["zoo"][] = &$list[$k];
		}
  return $groupedList;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnZooCategories extends JElementOfflajnZooCategories {}
}