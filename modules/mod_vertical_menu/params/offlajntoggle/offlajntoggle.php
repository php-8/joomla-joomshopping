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
# offlajntoggle - Offlajn Toggle Parameter
# ------------------------------------------------------------------------
# author    Jeno Kovacs & Andras Molnar
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

class JElementOfflajnToggle extends JOfflajnFakeElementBase{
  var $_moduleName = '';
  var	$_name = 'offlajntoggle';

  function universalfetchElement($name, $value, &$node){
    $document =& JFactory::getDocument();
    $this->loadFiles();
    $root = JURI::root();
    $attr = $node->attributes();
    $html = '<div class="offlajntoggle" id="offlajntoggle'.$this->id.'"></div>';
    $html .= '<input type="hidden" name="'.$name.'" id="'.$this->id.'" value="'.$value.'" />';

    DojoLoader::addScript('
      new OfflajnToggle({
        id: "'.$this->id.'",
        src: "'.$root.$attr['img'].'"
      });
    ');
    return $html;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnToggle extends JElementOfflajnToggle {}
}