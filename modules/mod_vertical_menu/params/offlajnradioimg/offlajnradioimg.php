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

class JElementOfflajnRadioimg extends JOfflajnFakeElementBase {

  var $_name = 'offlajnradioimg';
  
  function universalfetchElement($name, $value, &$node) {
    $document =& JFactory::getDocument();
    $html = "";
    $el = "";
    $this->values = array();
    $this->mode = "";
    $this->items = array();
    $this->url = JURI::base().'../modules/'.$this->_moduleName.'/params/'.$this->_name.'/images/';
    if(defined('WP_ADMIN'))
      $this->url = smartslider_translate_js_url($this->url);
    $this->loadFiles();
    $attrs = $node->attributes();
    $class = '';
    if(isset($attrs['mode'])) $class = $attrs['mode'];
		$html .= '<div class="offlajnradioimgcontainer'.$class.'" id="offlajnradioimgcontainer'.$this->id.'">';
    $html.= $this->makeItemDivs($node->children(), $value);
    $html .= '</div>';
    
    $html .= '<input type="hidden" id="'.$this->id.'" name="'.$name.'" value="'.$value.'"/>';
    DojoLoader::addScript('
      new OfflajnRadioimg({
        id: "'.$this->id.'",
        values: '.json_encode($this->values).',
        map: '.json_encode(array_flip($this->values)).',
        mode: '.json_encode($this->mode).'
      });
    ');
    
   return $html;
	}
  
  function makeItemDivs($items, $value) {
    $el = "";
    $i = 0;
    foreach ($items as $option) {
      $attrs = $option->attributes();
      $img = (string)@$attrs['img'];
      $val = (string)$attrs['value'];
      $text	= JText::_($option->data());
      if (@$attrs['img']) {
        $text.='<br /><div class="radioimg"><img src="'.JURI::root().((string)$attrs['img']).'" /></div>';
      }
      $class = '';
      if($i == 0) $class='first';
      if($i == count($items)-1) $class.=' last';
      $this->values[] = $val;
      $el .= '<div class="radioelement '.$class.($val == $value ? ' selected' : '').'">'.$text.'</div>';
      $i++;	
    } 
    $el.='<div class="clear"></div>';
    return $el;
  }
  
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnRadioimg extends JElementOfflajnRadioimg {}
}