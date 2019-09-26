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
# author    Kristof Molnar
# copyright Copyright (C) 2018 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

@JOfflajnParams::load('offlajnmultiselectlist');

global $mosConfig_absolute_path;
if( !isset( $mosConfig_absolute_path ) ) {
 $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
}

class JElementOfflajnEasyBlog extends JElementOfflajnMultiSelectList {
  function getItems(&$node) {

		$db = JFactory::getDBO();
    $query = 'SELECT c.id AS id, c.title AS name, c.title AS title, c.parent_id AS parent, c.parent_id AS parent_id
			        FROM #__easyblog_category AS c
			        WHERE c.published = 1
              ORDER BY parent_id, ordering';
		 
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$children = array();
		if ( $mitems )
		{
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent_id;
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
			@$groupedList["easyblog"][] = &$list[$k];
		}

  return $groupedList;
  }
}



