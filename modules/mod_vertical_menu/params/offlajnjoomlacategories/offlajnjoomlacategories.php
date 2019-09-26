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
defined('_JEXEC') or die('Restricted access');

@JOfflajnParams::load('offlajnmultiselectlist');

class JElementOfflajnJoomlaCategories extends JElementOfflajnMultiSelectList {

  function getItems(&$node) {
    $db =& JFactory::getDBO();

    $query = 'SELECT
                c.id AS id,
                c.title AS name,
                c.title AS title,
                c.parent_id AS parent,
                c.parent_id AS parent_id
            FROM #__categories AS c
            WHERE c.published = 1 AND (c.extension="com_content" OR c.extension = "system")
	    ORDER BY c.lft';
		$db->setQuery( $query );
		$menuItems = $db->loadObjectList();
		$children = array();
		if ( $menuItems ) {
			foreach ($menuItems as $v){
			  $pt 	= $v->parent_id;
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		$list = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0 );
		$n = count( $list );
		$groupedList = array();
  	foreach ($list as $k => $v) {
			$groupedList["joomlacategories"][] = &$list[$k];
		}
  return $groupedList;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnJoomlaCategories extends JElementOfflajnJoomlaCategories {}
}
