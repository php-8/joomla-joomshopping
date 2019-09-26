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

@JOfflajnParams::load('offlajnlist');

class JElementOfflajnMenuItem extends JElementOfflajnList {

  function universalfetchElement($name, $value, &$node) {
    if(version_compare(JVERSION,'1.6.0','ge')) {
	    require_once( JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php' );
      $menus	= MenusHelper::getMenuLinks();
      $element = empty($node->_attributes['element']) ? '' : $node->_attributes['element'];
  		foreach ($menus as $menu) {
        if(!$element){
          $node->addChild('option',array('value' => 'optgroup'))->setData($menu->title);
        }
        foreach ($menu->links as $link) {
          $option = $link->element;
          if($element && $element == $option || !$element){ 
            $node->addChild('option',array('value' => $link->value))->setData('&nbsp;&nbsp;&nbsp;'.$link->text);
          }
        }
      }
    } else {
      require_once( JPATH_ADMINISTRATOR.'/components/com_menus/helpers/helper.php' );
      $db =& JFactory::getDBO();
  		$query = 'SELECT id, parent, name, menutype, type' .
  				' FROM #__menu WHERE published = 1' .
  				' ORDER BY menutype, parent, ordering';
  		$db->setQuery($query);
  		$menuItems = $db->loadObjectList();

  		$children = array();
  		if ($menuItems)
  		{
  			// first pass - collect children
  			foreach ($menuItems as $v)
  			{
  				$pt 	= $v->parent;
  				$list 	= @$children[$pt] ? $children[$pt] : array();
  				array_push( $list, $v );
  				$children[$pt] = $list;
  			}
  		}
      $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
  		// assemble into menutype groups
  		$n = count( $list );
  		$menus = array();
  		foreach ($list as $k => $v) {
  			$menus[$v->menutype][] = &$list[$k];
  		}
      foreach ($menus as $k => $menu) {
        $node->addChild('option',array('value' => 'optgroup'))->setData($k);
        foreach ($menu as $menuitem) {
          $node->addChild('option',array('value' => $menuitem->id))->setData('&nbsp;&nbsp;&nbsp;'.$menuitem->name);
        }
      }
    }
    return parent::universalfetchElement($name, $value, $node);
  }

}
if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMenuItem extends JElementOfflajnMenuItem {}
}