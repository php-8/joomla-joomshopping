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

if(is_dir(JPATH_ROOT.'/components/com_mijoshop/mijoshop')){
  require_once(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php');
}

class JElementOfflajnMijoshopCategories extends JElementOfflajnMultiSelectList {

  function getItems(&$node) {
    $db =& JFactory::getDBO();
    $lang = '';
    $config = MijoShop::get('opencart')->get('config');
    if (is_object($config)) {
        $lang = ' AND cd.language_id = '.$config->get('config_language_id');
    }

    $query = 'SELECT
                m.category_id AS id,
                cd.name AS name,
                cd.name AS title,
                m.parent_id AS parent,
                m.parent_id as parent_id
            FROM #__mijoshop_category m
            LEFT JOIN #__mijoshop_category_description AS cd ON cd.category_id = m.category_id
            WHERE m.status = 1 '.$lang.'
            ORDER BY m.sort_order';
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
			$groupedList["mijoshop"][] = &$list[$k];
		}
  return $groupedList;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMijoshopCategories extends JElementOfflajnMijoshopCategories {}
}
